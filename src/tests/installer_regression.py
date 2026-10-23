#!/usr/bin/env python3
"""Regression checks for release 1.0.2. Uses isolated temporary copies only."""
import argparse,importlib.util,json,os,shutil,socket,subprocess,tempfile,time
from pathlib import Path

def main():
 p=argparse.ArgumentParser();p.add_argument('--php',default='php');p.add_argument('--source');p.add_argument('--old-source');a=p.parse_args()
 source=Path(a.source).resolve() if a.source else Path(__file__).resolve().parents[1]
 php=str(Path(a.php).resolve()) if '/' in a.php else shutil.which(a.php)
 spec=importlib.util.spec_from_file_location('acceptance',source/'tests/http_acceptance.py');m=importlib.util.module_from_spec(spec);spec.loader.exec_module(m)
 results=[]
 def scenario(src,case,old=False):
  with tempfile.TemporaryDirectory(prefix='civicflow-installer-') as td:
   root=Path(td)/'app';shutil.copytree(src,root)
   for n in ['config/local.php','config/installed.lock','config/install.mutex','storage/civicflow.sqlite']:
    f=root/n
    if f.is_file():f.unlink()
   key='QA-'+os.urandom(20).hex();(root/'config/setup.key').write_text(key)
   if case=='missing_folders':
    for n in ['cache','logs','uploads','sessions']:shutil.rmtree(root/'storage'/n,ignore_errors=True)
   if case=='blocked_cache':
    shutil.rmtree(root/'storage/cache',ignore_errors=True);(root/'storage/cache').write_text('not a directory')
   if case=='blocked_mutex':(root/'config/install.mutex').mkdir()
   if case=='blocked_sessions':
    shutil.rmtree(root/'storage/sessions',ignore_errors=True);(root/'storage/sessions').write_text('not a directory')
   sock=socket.socket();sock.bind(('127.0.0.1',0));port=sock.getsockname()[1];sock.close();base='http://127.0.0.1:'+str(port)
   with (Path(td)/'server.log').open('w') as log:
    server=subprocess.Popen([php,'-S','127.0.0.1:'+str(port),'router.php'],cwd=root,stdout=log,stderr=log)
    try:
     c=m.Client(base)
     for _ in range(50):
      try:r=c.get('/install');break
      except OSError:time.sleep(.06)
     else:raise AssertionError('Local PHP server unavailable')
     if case=='blocked_sessions':
      assert r[0]==503 and b'Server setup required' in r[2] and b'storage/sessions' in r[2];return {'case':case,'passed':True,'http':503}
     assert r[0]==200
     data={'setup_key':key,'organisation_name':'QA Service Authority','support_email':'support@example.test','base_url':base,'db_driver':'sqlite','name':'QA Administrator','email':'admin@example.test','phone':'08012345678','password':'QaSyntheticPassword42','password_confirm':'QaSyntheticPassword42'}
     if case=='database_unreachable':data.update(db_driver='mysql',db_host='127.0.0.1',db_port='1',db_name='qa_empty',db_user='qa_user',db_password='not-a-real-password')
     response=c.post('/install',data,source='/install');body=response[2]
     if old:
      assert response[0]==500 and b'Service temporarily unavailable' in body
      return {'case':case,'reproduced':True,'http':500}
     if case=='missing_folders':
      assert response[0]==303 and (root/'config/installed.lock').exists()
      for n in ['cache','logs','uploads','sessions']:assert (root/'storage'/n/'.htaccess').is_file()
      dashboard=c.get('/dashboard');assert dashboard[0]==200
     elif case=='blocked_cache':
      assert response[0]==200 and b'Server setup is incomplete' in body and b'storage/cache' in body
      assert not (root/'storage/civicflow.sqlite').exists()
     elif case=='blocked_mutex':
      assert response[0]==200 and b'cannot write config/install.mutex' in body
      assert not (root/'storage/civicflow.sqlite').exists()
     elif case=='database_unreachable':
      assert response[0]==200 and b'database server could not be reached' in body
      assert b'not-a-real-password' not in body and b'value="qa_empty"' in body and b'value="qa_user"' in body
     assert b'Service temporarily unavailable' not in body
     return {'case':case,'passed':True,'http':response[0]}
    finally:server.terminate();server.wait(timeout=10)
 if a.old_source:
  results += [scenario(Path(a.old_source).resolve(),c,True) for c in ['missing_folders','blocked_cache','blocked_mutex']]
 checks=[scenario(source,c) for c in ['missing_folders','blocked_cache','blocked_mutex','blocked_sessions','database_unreachable']]
 report={'release':'1.0.2','suite':'installer regression','total':len(checks),'passed':sum(x['passed'] for x in checks),'time_utc':time.strftime('%Y-%m-%dT%H:%M:%SZ',time.gmtime()),'checks':checks,'prior_release_reproduction':results}
 (source/'tests/acceptance-installer.json').write_text(json.dumps(report,indent=2)+'\n');print(json.dumps(report,indent=2))
if __name__=='__main__':main()
