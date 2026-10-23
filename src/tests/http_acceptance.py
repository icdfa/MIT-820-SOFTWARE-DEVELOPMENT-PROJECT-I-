#!/usr/bin/env python3
"""End-to-end checks on an isolated temporary copy. Never point this at a live site.
Run: python3 tests/http_acceptance.py --php /path/to/php
The PHP CLI needs pdo_sqlite, mbstring, fileinfo, openssl, curl and DOM.
"""
import argparse, base64, hashlib, hmac, http.cookiejar, json, os, re, shutil, socket, struct, subprocess, tempfile, time
from html.parser import HTMLParser
from pathlib import Path
from urllib.error import HTTPError
from urllib.parse import urlencode, urlparse, parse_qs
from urllib.request import Request, build_opener, HTTPCookieProcessor, HTTPRedirectHandler

class NoRedirect(HTTPRedirectHandler):
    def redirect_request(self, req, fp, code, msg, headers, newurl): return None

class Inputs(HTMLParser):
    def __init__(self, html):
        super().__init__(); self.values={}; self.feed(html)
    def handle_starttag(self, tag, attrs):
        a=dict(attrs)
        if tag=='input' and a.get('name') and a.get('type')=='hidden': self.values[a['name']]=a.get('value','')
        if tag=='meta' and a.get('name')=='csrf-token': self.values['_token']=a.get('content','')

class Client:
    def __init__(self, base):
        self.base=base; self.jar=http.cookiejar.CookieJar(); self.opener=build_opener(HTTPCookieProcessor(self.jar),NoRedirect())
    def request(self,path, data=None, files=None):
        body=None; headers={}
        if data is not None:
            if files:
                boundary='CivicFlowBoundary123456789'; parts=[]
                for k,v in data.items():
                    for item in (v if isinstance(v,list) else [v]): parts.append(f'--{boundary}\r\nContent-Disposition: form-data; name="{k}"\r\n\r\n{item}\r\n'.encode())
                for name,raw,mime in files: parts.append(f'--{boundary}\r\nContent-Disposition: form-data; name="documents[]"; filename="{name}"\r\nContent-Type: {mime}\r\n\r\n'.encode()+raw+b'\r\n')
                body=b''.join(parts)+f'--{boundary}--\r\n'.encode(); headers['Content-Type']='multipart/form-data; boundary='+boundary
            else: body=urlencode(data,doseq=True).encode(); headers['Content-Type']='application/x-www-form-urlencoded'
        req=Request(self.base+path, data=body, headers=headers)
        try: response=self.opener.open(req,timeout=15)
        except HTTPError as ex: response=ex
        return response.code,dict(response.headers),response.read()
    def get(self,path='/dashboard'): return self.request(path)
    def post(self,path,data=None,source=None,files=None):
        status,h,html=self.get(source or path); fields=Inputs(html.decode(errors='replace')).values
        fields.update(data or {}); return self.request(path,fields,files)
    def login(self,email,password): return self.post('/login',{'email':email,'password':password})


def run():
    parser=argparse.ArgumentParser();parser.add_argument('--php',default='php');parser.add_argument('--keep',action='store_true');parser.add_argument('--mysql',action='store_true');parser.add_argument('--live',action='store_true');parser.add_argument('--db-name',default='civicflow_qa');args=parser.parse_args()
    php=str(Path(args.php).resolve()) if '/' in args.php else shutil.which(args.php)
    if not php: raise SystemExit('PHP CLI not found')
    original=Path(__file__).resolve().parents[1]; temp=Path(tempfile.mkdtemp(prefix='civicflow-qa-'));app=temp/'app';shutil.copytree(original,app)
    for p in [app/'config/local.php',app/'config/installed.lock',app/'config/install.mutex',app/'storage/civicflow.sqlite']:
        if p.exists():p.unlink()
    for folder in ['uploads','logs','cache','sessions']:
        for p in (app/'storage'/folder).iterdir():
            if p.is_file() and not p.name.startswith('.'):p.unlink()
    setup='QA-'+os.urandom(20).hex();(app/'config/setup.key').write_text(setup)
    sock=socket.socket();sock.bind(('127.0.0.1',0));port=sock.getsockname()[1];sock.close();base=f'http://127.0.0.1:{port}'
    log=open(temp/'server.log','w');server=subprocess.Popen([php,'-d','upload_max_filesize=6M','-d','post_max_size=20M','-S',f'127.0.0.1:{port}','router.php'],cwd=app,stdout=log,stderr=log)
    results=[]
    def check(name, condition, detail=''):
        results.append({'check':name,'passed':bool(condition)})
        if not condition: raise AssertionError(name+' '+str(detail)[:1000])
    def q(sql,params=()):
        if not args.mysql:
            import sqlite3
            con=sqlite3.connect(app/'storage/civicflow.sqlite');con.row_factory=sqlite3.Row
            try:
                cur=con.execute(sql,params);result=[dict(x) for x in cur.fetchall()] if cur.description else [];con.commit();return result
            finally:con.close()
        payload=base64.b64encode(json.dumps([sql,list(params)]).encode()).decode()
        code="require 'app/bootstrap.php';[$s,$p]=json_decode(base64_decode($argv[1]),true);$q=sql($s,$p);echo json_encode($q->columnCount()?$q->fetchAll():[]);"
        return json.loads(subprocess.check_output([php,'-r',code,payload],cwd=app))
    def one(sql,params=()): return q(sql,params)[0]
    def state(rid): return one('SELECT * FROM requests WHERE id=?',[rid])
    admin=Client(base); password='QaStrongPassword2026'; pdf=b'%PDF-1.4\n1 0 obj<</Type/Catalog>>endobj\n%%EOF'
    try:
        for _ in range(60):
            if server.poll() is not None: raise RuntimeError('Web server failed: '+(temp/'server.log').read_text())
            try:
                r=admin.get('/install')
                if r[0]:break
            except OSError:time.sleep(.1)
        check('Installer renders before database setup',r[0]==200,r[2])
        data={'setup_key':setup,'organisation_name':'CivicFlow QA Authority','support_email':'support@example.test','base_url':base,'db_driver':'mysql' if args.mysql else 'sqlite','db_host':'127.0.0.1','db_name':args.db_name,'db_user':'root','db_password':'','name':'QA Administrator','email':'admin@example.test','phone':'08011111111','password':password,'password_confirm':password,'seed_demo':'1','demo_password':password}
        if args.live:data.pop('seed_demo');data.pop('demo_password')
        r=admin.post('/install',dict(data,setup_key='wrong'));check('Incorrect setup key refused',r[0]==200 and b'setup key is incorrect' in r[2])
        (app/'config/setup.key').unlink();r=admin.post('/install',data);check('Missing setup key fails closed',r[0]==200 and b'setup key file is missing' in r[2]);(app/'config/setup.key').write_text(setup)
        r=admin.post('/install',data);check('Installer creates database and locks setup',r[0]==303 and '/admin/setup-complete' in r[1].get('Location',''),r)
        check('Installer locked after setup',admin.get('/install')[0]==303)
        if args.live:
            check('Live mode contains only administrator account',one('SELECT COUNT(*) n FROM users')['n']==1)
            check('Live services unpublished and zero fee',one('SELECT COUNT(*) n FROM services WHERE active=1 OR fee<>0')['n']==0)
            check('Live mode has no sample requests',one('SELECT COUNT(*) n FROM requests')['n']==0)
            check('Live public home uses organisation identity','CivicFlow QA Authority' in Client(base).get('/')[2].decode())
            svc=one('SELECT * FROM services WHERE id=1'); admin.post('/admin/services',dict(svc,active='1'))
            check('Administrator publishes service',one('SELECT active FROM services WHERE id=1')['active']==1 and Client(base).get('/services/1')[0]==200)
            viewer=Client(base);r=viewer.post('/register',{'name':'Live Test Citizen','email':'live@example.test','phone':'08011112222','password':password,'password_confirm':password,'consent':'1'})
            check('Live citizen registration works',r[0]==303 and '/dashboard' in r[1].get('Location',''))
            livehtml=viewer.get('/dashboard')[2].decode();check('Live workspace has no academic branding',all(x not in livehtml.lower() for x in ['academic project','demonstration mode','icdfa','group 1']))
            r=viewer.post('/requests/new',{'service_id':'1','subject':'Live mode service request','description':'A controlled operational validation request.','location':'Lafia','state_name':'Nasarawa','priority':'normal','confirmation':'1','intent':'submit'})
            check('Live request can be submitted',r[0]==303 and '/requests/' in r[1].get('Location',''))
            report={'database':'sqlite-live','total':len(results),'passed':sum(x['passed'] for x in results),'checks':results,'time_utc':time.strftime('%Y-%m-%dT%H:%M:%SZ',time.gmtime())}
            (original/'tests/acceptance-live.json').write_text(json.dumps(report,indent=2)+'\n');print(json.dumps({k:v for k,v in report.items() if k!='checks'}));return
        for path in ['/admin/setup-complete','/dashboard','/services','/services/3','/requests','/reports','/admin/users','/admin/roles','/admin/templates','/admin/compose','/admin/outbox','/admin/audit','/admin/settings','/profile','/profile/security','/assistant','/help','/privacy','/about']:
            r=admin.get(path);check('Page renders '+path,r[0]==200,r[2])
        for path in ['/config/setup.key','/config/local.php','/storage/civicflow.sqlite','/storage/uploads/test.pdf','/app/security.php','/vendor/phpmailer/SMTP.php','/docs/test.md','/.env','/START_HERE.html']:
            check('Private URL denied '+path,admin.get(path)[0]==403)
        r=admin.get('/login.php');check('PHP address redirects to clean URL',r[0]==308 and r[1].get('Location')=='/login')
        r=admin.get('/index.php');check('Index address canonicalised',r[0]==308 and r[1].get('Location')=='/')
        r=admin.get('/assets/css/app.css');check('Local assets available',r[0]==200 and len(r[2])>1000)
        r=admin.request('/profile',{'name':'CSRF attack'});check('POST without CSRF token rejected',r[0]==419)
        invalid=Client(base);badpassword='A1'+'x'*71;invalid.post('/register',{'name':'Invalid Length','email':'length@example.test','phone':'08011112222','password':badpassword,'password_confirm':badpassword,'consent':'1'});check('Passwords beyond bcrypt byte limit rejected',not q('SELECT id FROM users WHERE email=?',['length@example.test']))
        citizen=Client(base); r=citizen.post('/register',{'name':'Case Owner','email':'owner@example.test','phone':'08022222222','password':password,'password_confirm':password,'consent':'1','role':'admin'})
        check('Citizen registration succeeds',r[0]==303 and '/dashboard' in r[1].get('Location',''),r)
        owner=one('SELECT * FROM users WHERE email=?',['owner@example.test']);check('Registration cannot elevate role',owner['role']=='citizen')
        mail=one("SELECT * FROM outbox WHERE user_id=? AND template_key='auth_verification' ORDER BY id DESC LIMIT 1",[owner['id']]);vtoken=re.search(r'token=([a-f0-9]+)',mail['body']).group(1)
        check('Email verification token hidden from operations',vtoken not in admin.get('/admin/outbox/detail?id='+str(mail['id']))[2].decode())
        verify=Client(base);verify.post('/verify-email',{'token':vtoken},source='/verify-email?token='+vtoken);check('Email verification succeeds',one('SELECT email_verified_at FROM users WHERE id=?',[owner['id']])['email_verified_at'] is not None)
        verify.post('/verify-email',{'token':vtoken},source='/verify-email?token='+vtoken);check('Email verification token single-use','invalid or expired' in verify.get('/verify-email?token='+vtoken)[2].decode())
        check('Malformed query array rejected',verify.get('/services?q[]=test')[0]==400)
        for path in ['/admin/users','/admin/settings','/admin/outbox','/admin/roles','/reports/export']:
            check('Citizen direct access denied '+path,citizen.get(path)[0]==403)
        r=citizen.post('/admin/users',{'name':'Attack','role':'admin'},source='/dashboard');check('Citizen admin POST denied',r[0]==403)
        form={'service_id':'1','subject':'=Potential spreadsheet formula','description':'Road is damaged near the school <script>alert(1)</script>.','location':'Lafia civic centre','state_name':'Nasarawa','priority':'high','confirmation':'1','intent':'submit','organisation':''}
        r=citizen.post('/requests/new',form);check('Complaint submission succeeds',r[0]==303 and re.search(r'/requests/\d+$',r[1].get('Location','')) is not None,r)
        rid=int(r[1]['Location'].rsplit('/',1)[1]); req=state(rid);check('Routing, reference and SLA set',req['assigned_to'] and req['due_at'] and req['reference'].startswith('GOV-CMP-'))
        html=citizen.get('/requests/'+str(rid))[2].decode();check('User content escaped', '&lt;script&gt;' in html and '<script>alert(1)</script>' not in html);ids=re.findall(r'\bid="([^"]+)"',html);check('Form labels use unique HTML IDs',len(ids)==len(set(ids)))
        check('Receipt available to owner',citizen.get(f'/requests/{rid}/receipt')[0]==200)
        other=Client(base);other.login('citizen@example.test',password);check('Another citizen cannot read request',other.get(f'/requests/{rid}')[0]==404)
        works=Client(base);works.login('works.officer@example.test',password);env=Client(base);env.login('env.officer@example.test',password)
        check('Officer can read own department request',works.get(f'/requests/{rid}')[0]==200)
        check('Officer cannot read other department request',env.get(f'/requests/{rid}')[0]==404)
        r=citizen.post(f'/requests/{rid}/status',{'status':'resolved','message':'Attempted self decision','version':req['version']},source=f'/requests/{rid}');check('Citizen cannot decide own request',r[0]==403 and state(rid)['status']=='submitted')
        r=works.post(f'/requests/{rid}/status',{'status':'resolved','message':'Attempted invalid jump','version':req['version']},source=f'/requests/{rid}');check('Invalid workflow transition refused',state(rid)['status']=='submitted')
        r=works.post(f'/requests/{rid}/status',{'status':'in_review','message':'Review started with evidence.','version':req['version']},source=f'/requests/{rid}');check('Officer starts review',state(rid)['status']=='in_review')
        r=works.post(f'/requests/{rid}/status',{'status':'rejected','message':'Stale decision attempt','version':req['version']},source=f'/requests/{rid}');check('Concurrent stale update refused',state(rid)['status']=='in_review')
        r=works.post(f'/requests/{rid}/reply',{'message':'CONFIDENTIAL STAFF NOTE','internal':'1'},source=f'/requests/{rid}',files=[('internal.pdf',pdf,'application/pdf')]);check('Internal note and attachment saved',r[0]==303 and one("SELECT COUNT(*) n FROM events WHERE request_id=? AND visibility='internal'",[rid])['n']==1,r)
        internal=one("SELECT * FROM attachments WHERE request_id=? AND visibility='internal'",[rid]);check('Internal note hidden from citizen','CONFIDENTIAL STAFF NOTE' not in citizen.get(f'/requests/{rid}')[2].decode())
        check('Internal attachment hidden from citizen','internal.pdf' not in citizen.get(f'/requests/{rid}')[2].decode() and citizen.get('/documents/'+str(internal['id']))[0]==404)
        check('Authorised staff can download internal attachment',works.get('/documents/'+str(internal['id']))[2]==pdf)
        r=works.post(f'/requests/{rid}/status',{'status':'needs_information','message':'Please provide a closer photo.','version':state(rid)['version']},source=f'/requests/{rid}');check('Information requested',state(rid)['status']=='needs_information')
        r=citizen.post(f'/requests/{rid}/reply',{'message':'Additional location evidence supplied.'},source=f'/requests/{rid}',files=[('evidence.pdf',pdf,'application/pdf')]);check('Citizen reply resubmits request',r[0]==303 and state(rid)['status']=='submitted',r)
        public=one("SELECT * FROM attachments WHERE request_id=? AND visibility='public'",[rid]);check('Owner document download works',citizen.get('/documents/'+str(public['id']))[2]==pdf)
        check('Other citizen document download denied',other.get('/documents/'+str(public['id']))[0]==404)
        check('Other department document download denied',env.get('/documents/'+str(public['id']))[0]==404)
        for to in ['in_review','resolved']:
            works.post(f'/requests/{rid}/status',{'status':to,'message':'Work completed and evidence recorded.','version':state(rid)['version']},source=f'/requests/{rid}')
        check('Complaint resolved',state(rid)['status']=='resolved')
        citizen.post(f'/requests/{rid}/feedback',{'rating':'4','comment':'Clear updates.'},source=f'/requests/{rid}');check('Citizen feedback recorded',one('SELECT rating FROM feedback WHERE request_id=?',[rid])['rating']==4)
        citizen.post(f'/requests/{rid}/reopen',{'message':'Issue persists at the same location.','version':state(rid)['version']},source=f'/requests/{rid}');check('Resolved complaint reopened with fresh target',state(rid)['status']=='submitted' and state(rid)['closed_at'] is None)
        check('Reopen clears prior feedback',not q('SELECT id FROM feedback WHERE request_id=?',[rid]))
        anon=Client(base);req=state(rid);r=anon.post('/track',{'reference':req['reference'],'tracking_code':req['access_token']});check('Private-code public tracking works',r[0]==303 and req['reference'] in anon.get('/track')[2].decode())
        check('Public tracker does not expose applicant or documents',all(x not in anon.get('/track')[2].decode() for x in ['Case Owner','evidence.pdf','CONFIDENTIAL']))
        bad=Client(base);bad.post('/track',{'reference':req['reference'],'tracking_code':'INCORRECT'});check('Incorrect tracking code refused','No matching request' in bad.get('/track')[2].decode())
        permit=dict(form,service_id='3',subject='Permit application QA')
        before=one('SELECT COUNT(*) n FROM requests')['n'];citizen.post('/requests/new',permit);check('Permit requires supporting documents',one('SELECT COUNT(*) n FROM requests')['n']==before)
        r=citizen.post('/requests/new',permit,files=[('fake.pdf',b'<?php echo "attack"; ?>','application/pdf')]);check('MIME spoofed PHP upload refused',one('SELECT COUNT(*) n FROM requests')['n']==before)
        r=citizen.post('/requests/new',permit,files=[('business.pdf',pdf,'application/pdf')]);check('Permit with valid PDF submitted',r[0]==303 and re.search(r'/requests/\d+$',r[1].get('Location','')) is not None,r);pid=int(r[1]['Location'].rsplit('/',1)[1]);trade=Client(base);trade.login('trade.officer@example.test',password)
        for to in ['in_review','approved']:trade.post(f'/requests/{pid}/status',{'status':to,'message':'Documents checked and decision recorded.','version':state(pid)['version']},source=f'/requests/{pid}')
        check('Permit approval completed',state(pid)['status']=='approved' and citizen.get(f'/requests/{pid}/permit')[0]==200)
        verify=anon.get('/verify/'+state(pid)['reference']);check('Public approval verification excludes applicant',verify[0]==200 and 'Case Owner' not in verify[2].decode())
        r=citizen.post('/requests/new',dict(form,intent='draft',subject='Saved complaint draft'));did=int(r[1]['Location'].rsplit('/',1)[1]);check('Draft saved without SLA or assignment',state(did)['status']=='draft' and not state(did)['due_at'] and not state(did)['assigned_to']);check('Unsubmitted draft private from staff',works.get(f'/requests/{did}')[0]==404 and admin.get(f'/requests/{did}')[0]==404)
        edit=dict(form,version=state(did)['version']);r=citizen.post(f'/requests/{did}/edit',edit);check('Draft can be submitted once',state(did)['status']=='submitted' and state(did)['due_at'])
        citizen.post(f'/requests/{did}/withdraw',{'message':'No longer required.','version':state(did)['version']},source=f'/requests/{did}');check('Eligible request withdrawn',state(did)['status']=='withdrawn')
        r=admin.get('/reports/export');check('Report CSV prevents formula execution',r[0]==200 and "'=Potential spreadsheet formula" in r[2].decode())
        q("UPDATE requests SET due_at='2020-01-01 00:00:00' WHERE id=?",[rid]);admin.post('/admin/jobs',{},source='/admin/outbox');check('Overdue workflow escalates once',state(rid)['escalated_at'] is not None)
        n=one("SELECT COUNT(*) n FROM events WHERE request_id=? AND message LIKE 'Service target exceeded%'",[rid])['n'];admin.post('/admin/jobs',{},source='/admin/outbox');check('Repeated workflow does not duplicate escalation',one("SELECT COUNT(*) n FROM events WHERE request_id=? AND message LIKE 'Service target exceeded%'",[rid])['n']==n)
        check('External delivery held without credentials',one("SELECT COUNT(*) n FROM outbox WHERE state='configuration_required'")['n']>0 and one("SELECT COUNT(*) n FROM outbox WHERE state='sent'")['n']==0)
        held=one('SELECT id FROM outbox ORDER BY id DESC LIMIT 1')['id'];admin.post('/admin/outbox/retry',{'id':held},source='/admin/outbox');check('Cannot release unconfigured channel',one('SELECT state FROM outbox WHERE id=?',[held])['state']=='configuration_required')
        admin.post('/admin/outbox/cancel',{'id':held},source='/admin/outbox');check('Outbox cancellation works',one('SELECT state FROM outbox WHERE id=?',[held])['state']=='cancelled')
        r=anon.post('/api/assistant',{'question':'How do I apply for a permit?'},source='/assistant');guide=json.loads(r[2]);check('Offline assistant identifies service-guide response',r[0]==200 and guide.get('mode')!='ai' and 'permit' in guide.get('answer','').lower(),guide)
        payload={'role_key':'intake_reviewer','role_name':'Intake reviewer','description':'Department review without decisions','active':'1','permissions[]':['requests.scope_department','requests.review','requests.message','documents.download']}
        admin.post('/admin/roles',payload);check('Custom role saved',one("SELECT COUNT(*) n FROM role_permissions WHERE role_key='intake_reviewer'")['n']==4)
        admin.post('/admin/users',{'name':'Restricted Reviewer','email':'restricted@example.test','phone':'08033333333','role':'intake_reviewer','department_id':'1','status':'active','password':password})
        restricted=Client(base);restricted.login('restricted@example.test',password);check('Custom role can access department request',restricted.get(f'/requests/{rid}')[0]==200)
        r=restricted.post(f'/requests/{rid}/status',{'status':'rejected','message':'Not permitted to decide','version':state(rid)['version']},source=f'/requests/{rid}');check('Separate decision permission enforced',r[0]==403 and state(rid)['status']=='submitted')
        r=restricted.post(f'/requests/{rid}/assign',{'assigned_to':'','department_id':'1','version':state(rid)['version'],'message':'Attempt assignment'},source=f'/requests/{rid}');check('Separate assignment permission enforced',r[0]==403)
        admin.post('/admin/users',{'name':'Audit Viewer','email':'audit@example.test','phone':'08044444444','role':'auditor','status':'active','password':password});auditor=Client(base);auditor.login('audit@example.test',password)
        check('Auditor reads reports and audit trail',auditor.get('/reports')[0]==200 and auditor.get('/admin/audit')[0]==200)
        check('Auditor cannot edit service or download documents',auditor.get('/admin/services')[0]==403 and auditor.get('/documents/'+str(public['id']))[0]==403)
        admin.post('/admin/users',{'name':'Communications User','email':'comms@example.test','phone':'08055555555','role':'communications','status':'active','password':password});comms=Client(base);comms.login('comms@example.test',password)
        check('Communications dashboard uses operational tools',comms.get('/dashboard')[0]==200 and 'Your citizen workspace' not in comms.get('/dashboard')[2].decode())
        check('Communications role isolated from cases',comms.get('/admin/outbox')[0]==200 and comms.get(f'/requests/{rid}')[0]==404 and comms.get('/admin/settings')[0]==403)
        malicious='<h2 onclick="alert(1)">Safe heading</h2><script>alert(1)</script><img src=x onerror=alert(1)><p>Hello {{name}}</p><a href="javascript:alert(1)">Unsafe link</a><p>{{message}}</p>'
        comms.post('/admin/templates',{'template_key':'qa_notice','name':'QA Notice','subject':'{{title}}','html_body':malicious,'plain_body':'{{message}}','active':'1'})
        saved=one("SELECT * FROM email_templates WHERE template_key='qa_notice'");check('HTML email template strips unsafe markup',all(x not in saved['html_body'].lower() for x in ['<script','onclick','<img','javascript:']) and '<h2>' in saved['html_body'])
        comms.post('/admin/compose',{'recipient_id':owner['id'],'channel':'email','template_key':'qa_notice','title':'QA controlled notice','message':'<script>not executable</script>'})
        notice=one("SELECT * FROM outbox WHERE subject='QA controlled notice'");check('Composed notice queues escaped HTML',notice['state']=='configuration_required' and '&lt;script&gt;' in notice['html_body'])
        # Controlled SMTP connection refusal exercises the worker without sending mail.
        settings={'organisation_name':'CivicFlow QA Authority','support_email':'support@example.test','app_name':'CivicFlow','app_tagline':'Public services. Clear progress.','smtp_host':'127.0.0.1','smtp_port':'9','smtp_security':'ssl','smtp_username':'qa','smtp_password':'QA_ENCRYPTION_SECRET_2026','smtp_from':'noreply@example.test','sms_channel':'generic','sms_sender':'CivicFlow','email_enabled':'1'}
        admin.post('/admin/settings',settings)
        check('Integration credential encrypted at rest',one("SELECT setting_value FROM settings WHERE setting_key='smtp_password'")['setting_value']!='QA_ENCRYPTION_SECRET_2026' and 'QA_ENCRYPTION_SECRET_2026' not in admin.get('/admin/settings')[2].decode())
        comms.post('/admin/compose',{'recipient_id':owner['id'],'channel':'email','template_key':'qa_notice','title':'QA connection refusal','message':'Controlled loopback-only delivery test.'})
        mid=one("SELECT id FROM outbox WHERE subject='QA connection refusal'")['id']
        admin.post('/admin/jobs',{},source='/admin/outbox')
        delivery=one('SELECT * FROM outbox WHERE id=?',[mid]);check('SMTP failure schedules retry without exposing credentials',delivery['state']=='retry' and delivery['attempts']==1 and delivery['next_attempt_at'] and 'QA_ENCRYPTION_SECRET_2026' not in delivery['last_error'])
        for _ in range(4):
            q("UPDATE outbox SET next_attempt_at='2020-01-01 00:00:00' WHERE id=?",[mid]);admin.post('/admin/jobs',{},source='/admin/outbox')
        delivery=one('SELECT * FROM outbox WHERE id=?',[mid]);check('Delivery stops after five failed attempts',delivery['state']=='failed' and delivery['attempts']==5)
        q("UPDATE settings SET setting_value='0' WHERE setting_key='email_enabled'")
        # A role edit revokes current sessions of affected users.
        payload['permissions[]'].append('reports.view');admin.post('/admin/roles',payload);check('Permission changes revoke affected sessions',restricted.get('/dashboard')[0]==303)
        # Password-reset tokens are single-use and invalidate older sessions.
        old=Client(base);old.login('owner@example.test',password);resetter=Client(base);resetter.post('/forgot-password',{'email':'owner@example.test'})
        resetmail=one("SELECT * FROM outbox WHERE user_id=? AND subject LIKE 'Reset your%' ORDER BY id DESC LIMIT 1",[owner['id']]);token=re.search(r'token=([a-f0-9]+)',resetmail['body']).group(1)
        check('Reset token hidden from outbox UI',token not in admin.get('/admin/outbox/detail?id='+str(resetmail['id']))[2].decode())
        newpass='ChangedStrongPassword2026';r=resetter.post('/reset-password',{'token':token,'password':newpass,'password_confirm':newpass},source='/reset-password?token='+token);check('Password reset succeeds',r[0]==303 and '/login' in r[1].get('Location',''))
        check('Reset revokes existing session',old.get('/dashboard')[0]==303)
        r=resetter.post('/reset-password',{'token':token,'password':password,'password_confirm':password},source='/reset-password?token='+token);check('Reset token cannot be reused','/reset-password' in r[1].get('Location',''))
        citizen=Client(base);citizen.login('owner@example.test',newpass);security=citizen.get('/profile/security')[2].decode();secret=re.search(r'class="tracking-code mono">([A-Z2-7]{32})',security).group(1)
        def otp(secret):
            digest=hmac.new(base64.b32decode(secret),struct.pack('>Q',int(time.time())//30),hashlib.sha1).digest();offset=digest[-1]&15;return f'{(struct.unpack(">I",digest[offset:offset+4])[0]&0x7fffffff)%1000000:06d}'
        r=citizen.post('/profile/security/setup',{'current_password':newpass,'code':otp(secret)},source='/profile/security');check('Authenticator MFA enabled',r[0]==303 and one('SELECT totp_confirmed FROM users WHERE id=?',[owner['id']])['totp_confirmed']==1)
        page=citizen.get('/profile/security')[2].decode();codes=re.findall(r'\b[A-F0-9]{10}\b',page);check('Eight recovery codes shown once',len(set(codes))==8 and codes[0] not in citizen.get('/profile/security')[2].decode())
        mfa=Client(base);r=mfa.login('owner@example.test',newpass);check('MFA required after correct password','/two-factor' in r[1].get('Location',''))
        check('Pending MFA cannot access dashboard',mfa.get('/dashboard')[0]==303)
        r=mfa.post('/two-factor',{'code':codes[0]});check('Recovery code completes sign-in',r[0]==303 and '/dashboard' in r[1].get('Location',''))
        replay=Client(base);replay.login('owner@example.test',newpass);r=replay.post('/two-factor',{'code':codes[0]});check('Recovery code cannot be replayed','/two-factor' in r[1].get('Location',''))
        r=replay.post('/two-factor',{'code':otp(secret)});check('Authenticator code completes sign-in','/dashboard' in r[1].get('Location',''))
        device=one('SELECT id FROM device_sessions WHERE user_id=? AND revoked_at IS NULL ORDER BY id DESC LIMIT 1',[owner['id']]);citizen.post('/profile/sessions/revoke',{'id':device['id']},source='/profile/security');check('Remote device revocation enforced',replay.get('/dashboard')[0]==303)
        check('MFA secret encrypted at rest',secret not in one('SELECT totp_secret FROM users WHERE id=?',[owner['id']])['totp_secret'])
        check('Passwords hashed at rest',one('SELECT password_hash FROM users WHERE id=?',[owner['id']])['password_hash'].startswith('$2y$'))
        check('Audit trail populated',one('SELECT COUNT(*) n FROM audit_logs')['n']>20)
        errors=(app/'storage/logs/application.log').read_text() if (app/'storage/logs/application.log').exists() else ''
        check('No application warnings or exceptions',not errors,errors[-2000:])
        report={'database':'mysql' if args.mysql else 'sqlite','total':len(results),'passed':sum(x['passed'] for x in results),'checks':results,'time_utc':time.strftime('%Y-%m-%dT%H:%M:%SZ',time.gmtime()),'runtime':subprocess.check_output([php,'-v'],text=True).splitlines()[0]}
        (original/'tests'/('acceptance-mysql.json' if args.mysql else 'acceptance-sqlite.json')).write_text(json.dumps(report,indent=2)+'\n')
        print(json.dumps({k:v for k,v in report.items() if k!='checks'},indent=2))
    except Exception:
        print('Failed after',len(results),'checks; test copy:',temp)
        if (app/'storage/logs/application.log').exists(): print((app/'storage/logs/application.log').read_text()[-2500:])
        print((temp/'server.log').read_text()[-1500:]);raise
    finally:
        server.terminate();server.wait(timeout=5);log.close()
        if not args.keep and all(x['passed'] for x in results):shutil.rmtree(temp)

if __name__=='__main__':run()
