<?php
declare(strict_types=1);
require __DIR__.'/app/bootstrap.php';
$path=route_path();
foreach($_GET as $v)if(is_array($v))abort_page(400,'Invalid query parameter.');
foreach($_POST as $k=>$v)if(is_array($v)&&!in_array($k,['permissions','additional_roles'],true))abort_page(400,'Invalid form parameter.');
if(!installed()){
 if($_SERVER['REQUEST_METHOD']==='POST'&&$path!=='/install')go('install');
 require ROOT.'/app/install.php';install_page();exit;
}
if($path==='/install')go('login');
require ROOT.'/app/pages_public.php';require ROOT.'/app/pages_workspace.php';require ROOT.'/app/pages_admin.php';
if($path==='/two-factor'){two_factor_page();exit;}
if($_SERVER['REQUEST_METHOD']==='POST'){
 require_once ROOT.'/app/actions.php';
 try{handle_post($path);}catch(InvalidArgumentException $ex){if(str_starts_with($path,'/api/'))json_out(['error'=>$ex->getMessage()],422);flash($ex->getMessage(),'error');$return=$path;
 if(preg_match('~^(/requests/\d+)/(status|reply|assign|withdraw|reopen|feedback)$~',$path,$m))$return=$m[1];
 if(in_array($path,['/profile/password','/profile/verify-email'],true))$return='/profile';if(str_starts_with($path,'/profile/security/')||$path==='/profile/sessions/revoke')$return='/profile/security';if(str_starts_with($path,'/admin/outbox/')||$path==='/admin/jobs')$return='/admin/outbox';if($path==='/verify-email')$return='/verify-email?token='.rawurlencode((string)($_POST['token']??''));if($path==='/reset-password')$return='/reset-password?token='.rawurlencode((string)($_POST['token']??''));
 go(ltrim($return,'/'));}
}
if($_SERVER['REQUEST_METHOD']!=='GET'&&$_SERVER['REQUEST_METHOD']!=='HEAD')abort_page(405,'Method not allowed.');
if($path==='/'||$path==='/index'){home_page();exit;}
if(in_array($path,['/login','/register','/forgot-password','/reset-password'],true)){auth_page($path);exit;}
if($path==='/verify-email'){verify_email_page();exit;}
if($path==='/services'){services_page();exit;}
if(preg_match('~^/services/(\d+)$~',$path,$m)){service_page((int)$m[1]);exit;}
if($path==='/track'){track_page();exit;}
if($path==='/assistant'){assistant_page();exit;}
if($path==='/help'){help_page();exit;}
if($path==='/privacy'){privacy_page();exit;}
if($path==='/about'){about_page();exit;}
if(preg_match('~^/verify/(GOV-[A-Z0-9-]+)$~',$path,$m)){verify_page($m[1]);exit;}
if($path==='/dashboard'){dashboard_page();exit;}
if($path==='/requests'){requests_page();exit;}
if($path==='/requests/new'){request_form_page();exit;}
if(preg_match('~^/requests/(\d+)/edit$~',$path,$m)){request_form_page((int)$m[1]);exit;}
if(preg_match('~^/requests/(\d+)/(receipt|permit)$~',$path,$m)){document_page((int)$m[1],$m[2]);exit;}
if(preg_match('~^/requests/(\d+)$~',$path,$m)){request_detail_page((int)$m[1]);exit;}
if(preg_match('~^/documents/(\d+)$~',$path,$m)){download_document((int)$m[1]);exit;}
if($path==='/notifications'){notifications_page();exit;}
if($path==='/profile/security'){account_security_page();exit;}
if($path==='/profile'){profile_page();exit;}
if($path==='/reports'){reports_page();exit;}
if($path==='/reports/export'){export_requests();exit;}
if(str_starts_with($path,'/admin/')){admin_page($path);exit;}
abort_page(404,'This page could not be found.');
