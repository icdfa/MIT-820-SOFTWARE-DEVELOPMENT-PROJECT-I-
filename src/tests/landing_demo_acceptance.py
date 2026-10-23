#!/usr/bin/env python3
"""Release 1.0.3: isolated HTTP checks for the landing page and opt-in role dataset."""
import argparse,importlib.util,json,os,re,shutil,socket,sqlite3,subprocess,tempfile,time
from pathlib import Path

def main():
 p=argparse.ArgumentParser();p.add_argument('--php',default='php');a=p.parse_args();source=Path(__file__).resolve().parents[1];php=str(Path(a.php).resolve()) if '/' in a.php else shutil.which(a.php)
 spec=importlib.util.spec_from_file_location('http_tests',source/'tests/http_acceptance.py');m=importlib.util.module_from_spec(spec);spec.loader.exec_module(m)
 checks=[]
 def check(name,ok,detail=''):
  checks.append({'check':name,'passed':bool(ok)})
  if not ok:raise AssertionError(name+' '+str(detail)[:1200])
 with tempfile.TemporaryDirectory(prefix='civicflow-landing-demo-') as td:
  root=Path(td)/'app';shutil.copytree(source,root)
  for name in ['config/local.php','config/installed.lock','config/install.mutex','storage/civicflow.sqlite']:
   (root/name).unlink(missing_ok=True)
  setup='TEST-'+os.urandom(20).hex();(root/'config/setup.key').write_text(setup)
  sock=socket.socket();sock.bind(('127.0.0.1',0));port=sock.getsockname()[1];sock.close();base='http://127.0.0.1:'+str(port)
  def q(query,params=()):
   with sqlite3.connect(root/'storage/civicflow.sqlite') as db:
    db.row_factory=sqlite3.Row;c=db.execute(query,params);return [dict(x) for x in c.fetchall()] if c.description else []
  def one(query,params=()):return q(query,params)[0]
  def count(table):return one('SELECT COUNT(*) n FROM '+table)['n']
  with (Path(td)/'server.log').open('w') as log:
   server=subprocess.Popen([php,'-S','127.0.0.1:'+str(port),'router.php'],cwd=root,stdout=log,stderr=log)
   try:
    admin=m.Client(base)
    for _ in range(60):
     try:r=admin.get('/install');break
     except OSError:time.sleep(.07)
    password='DemonstrationPass2026';adminpass='ExistingAdminPass2026'
    r=admin.post('/install',{'setup_key':setup,'organisation_name':'Presentation Service Office','support_email':'support@example.test','base_url':base,'db_driver':'sqlite','name':'Portal Administrator','email':'admin@example.test','phone':'08011112222','password':adminpass,'password_confirm':adminpass})
    check('Fresh live-mode installation remains supported',r[0]==303,r)
    check('Upload or installation does not automatically create presentation records',count('users')==1 and count('requests')==0)
    public=m.Client(base);home=public.get('/');html=home[2].decode()
    check('Landing page renders without presentation records',home[0]==200 and 'Government services, closer to you.' in html and 'Services will appear here' in html)
    check('Carousel has 3 slides and accessible controls',html.count('data-slide role=')==3 and 'data-pause' in html and 'aria-roledescription="carousel"' in html and 'data-carousel-controls' in html)
    check('Carousel uses local images and no inline handlers',html.count('assets/img/landing/')==3 and 'onclick=' not in html and 'onload=' not in html)
    check('Landing service search and private-code tracking forms are wired',f'action="{base}/services"' in html and f'action="{base}/track"' in html and 'name="tracking_code"' in html and 'name="_token"' in html)
    for image in ['citizen-services','community-works','business-permits']:
     r=public.get('/assets/img/landing/'+image+'.webp');check('Local slider image loads: '+image,r[0]==200 and len(r[2])>20000 and r[2][:4]==b'RIFF')
    for asset in ['/assets/css/landing.css','/assets/js/landing.js']:
     check('Landing asset loads: '+asset,public.get(asset)[0]==200)
    check('Live landing page does not claim demo activity',b'Demonstration portal' not in home[2] and b'fictional case records' not in home[2])
    check('Dataset and source cannot be fetched from the web',public.get('/app/demo_dataset.json')[0]==403 and public.get('/app/demo_data.php')[0]==403)
    check('Anonymous data-loader access requires sign-in',public.get('/admin/demo-data')[0]==303)
    check('Admin loader GET is read-only',admin.get('/admin/demo-data')[0]==200 and count('users')==1)
    check('Data loader requires CSRF',admin.request('/admin/demo-data',{'confirm_demo':'1','demo_password':password,'demo_password_confirm':password})[0]==419 and count('users')==1)
    admin.post('/admin/demo-data',{'confirm_demo':'1','demo_password':'short','demo_password_confirm':'short'})
    check('Invalid demonstration password creates no data',count('users')==1)
    admin.post('/admin/demo-data',{'demo_password':password,'demo_password_confirm':password,'confirm_demo':'0'})
    check('Explicit demonstration confirmation is required',count('users')==1 and count('requests')==0)
    # Add a pre-existing real-mode request and upload in this isolated test installation.
    service=one('SELECT * FROM services WHERE id=1');admin.post('/admin/services',dict(service,active='1',fee='750'))
    preserved=m.Client(base);r=preserved.post('/register',{'name':'Existing Citizen','email':'existing@example.test','phone':'08011113333','password':adminpass,'password_confirm':adminpass,'consent':'1'})
    check('Existing citizen account can be created before a demo load',r[0]==303)
    pdf=b'%PDF-1.4\n1 0 obj<</Type/Catalog>>endobj\n%%EOF'
    r=preserved.post('/requests/new',{'service_id':'1','subject':'Preserve this original request','description':'This original request and its evidence must survive the optional dataset load.','location':'Existing location','state_name':'Nasarawa','priority':'normal','confirmation':'1','intent':'submit'},files=[('existing-evidence.pdf',pdf,'application/pdf')])
    check('Existing request and uploaded evidence are prepared',r[0]==303 and count('requests')==1,r)
    original=one('SELECT * FROM requests');original_account=one('SELECT * FROM users WHERE email=?',['admin@example.test']);config=(root/'config/local.php').read_bytes();attachment=one('SELECT * FROM attachments');original_file=(root/'storage/uploads'/attachment['stored_name']).read_bytes();outbox_count=count('outbox')
    # A catalogue conflict occurs after earlier service activations, and must roll back all of them.
    q("UPDATE services SET kind='permit' WHERE slug='planning-enquiry'")
    r=admin.post('/admin/demo-data',{'confirm_demo':'1','demo_password':password,'demo_password_confirm':password})
    check('Catalogue conflicts fail without overwriting operator data',r[0]==303 and count('users')==2 and count('requests')==1)
    check('Catalogue conflict rolls back earlier publications',one('SELECT COUNT(*) n FROM services WHERE active=1')['n']==1)
    q("UPDATE services SET kind='complaint' WHERE slug='planning-enquiry'")
    r=admin.post('/admin/demo-data',{'confirm_demo':'1','demo_password':password,'demo_password_confirm':password})
    check('Administrator loads complete dataset',r[0]==303 and count('users')==28 and count('requests')==37,r)
    manifest=json.loads(one("SELECT setting_value FROM settings WHERE setting_key='demo_dataset_v103'")['setting_value']);ids=manifest['request_ids'];uid=manifest['user_ids'];slots=','.join('?'*len(ids));user_slots=','.join('?'*len(uid))
    check('All 26 account records and 36 cases are tracked',len(uid)==26 and len(ids)==36)
    check('Six roles covered without adding another administrator',one("SELECT COUNT(*) n FROM users WHERE role='admin'")['n']==1 and {x['role']:x['n'] for x in q('SELECT role,COUNT(*) n FROM users WHERE id IN ('+user_slots+') GROUP BY role',uid)}=={'citizen':12,'officer':8,'department_head':4,'auditor':1,'communications':1})
    check('All eight request stages are present',set(x['status'] for x in q('SELECT DISTINCT status FROM requests WHERE id IN ('+slots+')',ids))=={'draft','submitted','in_review','needs_information','approved','resolved','rejected','withdrawn'})
    check('Starter catalogue is published; existing fee retained',one('SELECT COUNT(*) n FROM services WHERE active=1')['n']==9 and one('SELECT fee FROM services WHERE id=1')['fee']==750)
    check('Existing administrator password and config are preserved',one('SELECT password_hash FROM users WHERE id=?',[original_account['id']])['password_hash']==original_account['password_hash'] and (root/'config/local.php').read_bytes()==config)
    check('Existing request fields and private upload are unchanged',one('SELECT * FROM requests WHERE id=?',[original['id']])==original and (root/'storage/uploads'/attachment['stored_name']).read_bytes()==original_file)
    check('Demonstration contacts are reserved addresses with no invented phones',all(x['email'].endswith('@civicflow.example.test') and x['phone']=='' and x['notify_email']==0 and x['notify_sms']==0 for x in q('SELECT * FROM users WHERE id IN ('+user_slots+')',uid)))
    outbox=q('SELECT * FROM outbox WHERE request_id IN ('+slots+')',ids)
    check('34 notification examples are cancelled without provider attempts',len(outbox)==34 and all(x['state']=='cancelled' and x['attempts']==0 and not x['sent_at'] and not x['provider_id'] for x in outbox))
    check('Existing delivery configuration is retained',one("SELECT setting_value FROM settings WHERE setting_key='email_enabled'")['setting_value']=='0' and count('outbox')==outbox_count+34)
    check('Owner notifications, staff assignments and feedback populated',count('notifications')>=60 and count('feedback')==9)
    check('No orphan records',not q('PRAGMA foreign_key_check'))
    cases=q('SELECT * FROM requests WHERE id IN ('+slots+')',ids)
    check('Cases have realistic descriptions and private random tracking codes',all(len(x['description'])>100 and len(x['access_token'])==16 and x['reference'].startswith('GOV-') for x in cases))
    check('Drafts are unassigned and closed cases have completion dates',all((not x['due_at'] and not x['assigned_to']) if x['status']=='draft' else (bool(x['closed_at']) if x['status'] in ['approved','resolved','rejected','withdrawn'] else not x['closed_at']) for x in cases))
    check('Every submitted permit has private supporting evidence',all(one('SELECT COUNT(*) n FROM attachments WHERE request_id=? AND visibility=?',[x['id'],'public'])['n']>0 for x in cases if x['kind']=='permit' and x['status']!='draft'))
    fixture_attachments=q('SELECT * FROM attachments WHERE request_id IN ('+slots+')',ids)
    check('Fixture PDFs have valid headers and tracked sizes',all((root/'storage/uploads'/x['stored_name']).read_bytes().startswith(b'%PDF-1.4') and (root/'storage/uploads'/x['stored_name']).stat().st_size==x['size_bytes'] for x in fixture_attachments))
    from pypdf import PdfReader
    check('Generated evidence is a readable one-page PDF with demonstration label',all(len(PdfReader(root/'storage/uploads'/x['stored_name']).pages)==1 and 'CIVICFLOW DEMONSTRATION DOCUMENT' in PdfReader(root/'storage/uploads'/x['stored_name']).pages[0].extract_text() for x in fixture_attachments))
    # Repeated submissions must not change records, account hashes or selected demo password.
    hashes=q('SELECT id,password_hash FROM users ORDER BY id');counts={t:count(t) for t in ['users','requests','events','attachments','outbox','notifications','audit_logs']}
    r=admin.post('/admin/demo-data',{'confirm_demo':'1','demo_password':'DifferentPassword2027','demo_password_confirm':'DifferentPassword2027'})
    check('Repeated load is idempotent',r[0]==303 and counts=={t:count(t) for t in counts} and hashes==q('SELECT id,password_hash FROM users ORDER BY id'))
    directory=admin.get('/admin/demo-data');check('Admin receives role guide and named account directory',directory[0]==200 and b'Role walkthrough' in directory[2] and b'hauwa.saleh@civicflow.example.test' in directory[2])
    q('UPDATE users SET name=? WHERE id=?',["=HYPERLINK(\"https://example.test\")",uid[0]])
    csv=admin.get('/admin/demo-data?export=accounts');check('Account CSV escapes spreadsheet formula prefixes',b"'=HYPERLINK" in csv[2])
    q('UPDATE users SET name=? WHERE id=?',['Amina Bello',uid[0]])
    check('Account directory export contains all demo users without passwords',csv[0]==200 and csv[2].count(b'@civicflow.example.test')==26 and password.encode() not in csv[2] and b'$2y$' not in csv[2])
    html=public.get('/')[2].decode();check('Loaded data appears in public services, department cards and notices',html.count('class="service-card"')==6 and '4 published services' not in html and 'Connected departments' in html and 'Keep your request reference' in html)
    check('Home and workspace omit demonstration banners while data provenance remains in About', 'Demonstration portal' not in html and 'fictional case records' not in html and 'Demonstration mode ·' not in admin.get('/dashboard')[2].decode() and b'for evaluation only' in public.get('/about')[2])
    # All roles use normal sign-in and normal authorization, with no demo bypass.
    sessions={};accounts=q('SELECT * FROM users WHERE id IN ('+user_slots+') ORDER BY id',uid)
    for account in accounts:
     c=m.Client(base);r=c.login(account['email'],password);check('Normal login works: '+account['email'],r[0]==303 and '/dashboard' in r[1].get('Location',''),r)
     check('Populated workspace renders: '+account['email'],c.get('/dashboard')[0]==200)
     if account['role'] not in sessions:sessions[account['role']]=(c,account)
    for role,(c,account) in sessions.items():
     check('Data loader denied to '+role,c.get('/admin/demo-data')[0]==403 and c.post('/admin/demo-data',{'confirm_demo':'1','demo_password':password,'demo_password_confirm':password},source='/profile')[0]==403)
    citizen,person=sessions['citizen'];own=next(x for x in cases if x['user_id']==person['id'] and x['status']!='draft');other=next(x for x in cases if x['user_id']!=person['id'] and x['status']!='draft')
    check('Citizen can open own record and cannot open another citizen record',citizen.get('/requests/'+str(own['id']))[0]==200 and citizen.get('/requests/'+str(other['id']))[0]==404)
    for role in ['officer','department_head']:
     c,staff=sessions[role];same=next(x for x in cases if x['department_id']==staff['department_id'] and x['status']!='draft');outside=next(x for x in cases if x['department_id']!=staff['department_id'] and x['status']!='draft');check(role+' remains department-scoped',c.get('/requests/'+str(same['id']))[0]==200 and c.get('/requests/'+str(outside['id']))[0]==404)
    internal=next(x for x in fixture_attachments if x['visibility']=='internal');internal_case=one('SELECT * FROM requests WHERE id=?',[internal['request_id']]);owner_email=one('SELECT email FROM users WHERE id=?',[internal_case['user_id']])['email']
    # Reuse existing sessions without consuming additional login attempts.
    public_attachment=next(x for x in fixture_attachments if x['visibility']=='public' and x['request_id']==own['id'])
    check('Own evidence downloads and another citizen evidence is blocked',citizen.get('/documents/'+str(public_attachment['id']))[0]==200 and citizen.get('/documents/'+str(internal['id']))[0]==404)
    auditor,_=sessions['auditor'];check('Auditor sees cases and audit but cannot download internal documents',auditor.get('/requests/'+str(other['id']))[0]==200 and auditor.get('/admin/audit')[0]==200 and auditor.get('/documents/'+str(internal['id']))[0] in [403,404])
    comms,_=sessions['communications'];check('Communications can view outbox and templates without case access',comms.get('/admin/outbox')[0]==200 and comms.get('/admin/templates')[0]==200 and comms.get('/requests/'+str(own['id']))[0]==404)
    check('Anonymous attachments require authentication',public.get('/documents/'+str(public_attachment['id']))[0]==303)
    r=public.post('/track',{'reference':own['reference'],'tracking_code':own['access_token']});tracking=public.get('/track')[2]
    check('Landing tracking uses the existing private-code flow',r[0]==303 and own['reference'].encode() in tracking and own['description'].encode() not in tracking and person['email'].encode() not in tracking)
    approved=next(x for x in cases if x['status']=='approved');verification=public.get('/verify/'+approved['reference']);check('Sample approval verification is labelled and excludes applicant identity',verification[0]==200 and b'no legal effect' in verification[2] and b'@civicflow.example.test' not in verification[2])
    q("INSERT INTO user_roles(user_id,role_key) VALUES(?,?)",[person['id'],'admin']);check('Additional role cannot bypass primary-admin data-loader check',citizen.get('/admin/demo-data')[0]==403)
    warning=(root/'storage/logs/application.log').read_text() if (root/'storage/logs/application.log').exists() else ''
    check('No unexpected PHP warnings or application exceptions',not any(x in warning for x in ['PHP Warning','PHP Fatal','Uncaught','Exception']),warning)
    report={'release':'1.0.4','suite':'landing and demonstration data','database':'sqlite','total':len(checks),'passed':sum(x['passed'] for x in checks),'demo_accounts':26,'demo_requests':36,'attachment_fixtures':len(fixture_attachments),'time_utc':time.strftime('%Y-%m-%dT%H:%M:%SZ',time.gmtime()),'checks':checks}
    (source/'tests/acceptance-landing-demo.json').write_text(json.dumps(report,indent=2)+'\n');print(json.dumps({k:v for k,v in report.items() if k!='checks'},indent=2))
   except Exception:
    print('Failed after',len(checks),'checks. Last result:',checks[-1] if checks else None)
    print((root/'storage/logs/application.log').read_text()[-3000:] if (root/'storage/logs/application.log').exists() else '')
    raise
   finally:server.terminate();server.wait(timeout=10)
if __name__=='__main__':main()
