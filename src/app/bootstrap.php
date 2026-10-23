<?php
declare(strict_types=1);
define('ROOT', dirname(__DIR__));
date_default_timezone_set('Africa/Lagos');
ini_set('display_errors','0');
ini_set('log_errors','1');
ini_set('error_log', ROOT.'/storage/logs/application.log');
$GLOBALS['config']=is_file(ROOT.'/config/local.php') ? require ROOT.'/config/local.php' : [];
function cfg(string $key, mixed $default=null): mixed {
 $value=$GLOBALS['config'][$key]??$default;
 if($key==='demo_mode'&&!$value&&function_exists('setting')&&installed())return setting('demo_dataset_mode','0')==='1';
 return $value;
}
function installed(): bool { return (bool)cfg('installed',false) && is_file(ROOT.'/config/installed.lock'); }
function base_path(): string { return rtrim((string)(parse_url((string)cfg('base_url',''),PHP_URL_PATH) ?: dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php')), '/.'); }
function url(string $path=''): string { return (cfg('base_url') ?: base_path()).'/'.ltrim($path,'/'); }
function route_path(): string { $p=rawurldecode((string)parse_url($_SERVER['REQUEST_URI']??'/',PHP_URL_PATH)); $base=base_path(); if($base!==''&&($p===$base||str_starts_with($p,$base.'/'))) $p=substr($p,strlen($base)); return '/'.trim($p,'/'); }
function e(mixed $v): string { return htmlspecialchars((string)($v??''),ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8'); }
function now(): string { return date('Y-m-d H:i:s'); }
function dates(?string $v, bool $time=false): string { return $v ? date($time?'d M Y, g:i a':'d M Y',strtotime($v)).($time?' WAT':'') : '—'; }
function label(string $s): string { return ucwords(str_replace('_',' ',$s)); }
function go(string $path=''): never { header('Location: '.url($path),true,303); exit; }
function json_out(array $data,int $code=200): never { http_response_code($code); header('Content-Type: application/json'); echo json_encode($data,JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE); exit; }
function flash(string $text,string $kind='success'): void { $_SESSION['flash']=['text'=>$text,'kind'=>$kind]; }
function abort_page(int $code,string $message): never { http_response_code($code); if(function_exists('page_start')){page_start((string)$code,''); echo '<div class="panel panel-body"><h2>'.e($message).'</h2><p>Please return to a page you have access to.</p><a class="btn btn-primary" href="'.e(url('dashboard')).'">Dashboard</a></div>'; page_end();}else echo e($message); exit; }
if(PHP_SAPI!=='cli'){
 header('X-Content-Type-Options: nosniff'); header('X-Frame-Options: DENY'); header('Referrer-Policy: same-origin'); header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
 header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; connect-src 'self'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'; object-src 'none'");
 header('Cache-Control: no-store, private');
 if(($_SERVER['HTTPS']??'')==='on')header('Strict-Transport-Security: max-age=31536000');
}
function prepare_private_storage(bool $probeWrites=false): array {
 $failed=[];
 foreach(['storage','storage/uploads','storage/cache','storage/logs','storage/sessions'] as $relative){
  $path=ROOT.'/'.$relative;
  if(!is_dir($path)&&!@mkdir($path,0700,true)&&!is_dir($path)){$failed[]=$relative;continue;}
  if(!is_writable($path)){$failed[]=$relative;continue;}
  $guard=$path.'/.htaccess';
  if(!is_file($guard)&&@file_put_contents($guard,"Require all denied\n",LOCK_EX)===false){$failed[]=$relative;continue;}
  if(!$probeWrites)continue;
  $probe=$path.'/.writecheck-'.bin2hex(random_bytes(6));
  if(@file_put_contents($probe,'ok',LOCK_EX)===false){$failed[]=$relative;continue;}
  if(!@unlink($probe))$failed[]=$relative;
 }
 return array_values(array_unique($failed));
}
$GLOBALS['storage_errors']=prepare_private_storage(!installed());
if(in_array('storage/sessions',$GLOBALS['storage_errors'],true)){
 http_response_code(503);header('Content-Type: text/html; charset=utf-8');
 echo '<!doctype html><html lang="en"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Server setup required</title><h1>Server setup required</h1><p>The portal cannot write to storage/sessions. In cPanel File Manager, confirm that storage and its subfolders belong to this hosting account and allow PHP to write to them. Use 0755 for account-owned directories; ask the host to correct ownership if writing is still blocked. Do not use 0777.</p><p>After correcting the folder, reload this page. No database records were changed.</p></html>';exit;
}
session_name('civicflow_session');
session_save_path(ROOT.'/storage/sessions');
ini_set('session.use_strict_mode','1');
session_set_cookie_params(['lifetime'=>0,'path'=>base_path()?:'/','secure'=>(($_SERVER['HTTPS']??'')==='on'),'httponly'=>true,'samesite'=>'Lax']);
session_start();
require ROOT.'/app/database.php';
require ROOT.'/app/security.php';
require ROOT.'/app/permissions.php';
require ROOT.'/app/account_security.php';
require ROOT.'/app/email_templates.php';
require ROOT.'/app/domain.php';
require ROOT.'/app/integrations.php';
require ROOT.'/app/layout.php';
set_exception_handler(function(Throwable $ex){$id=bin2hex(random_bytes(5));error_log('['.$id.'] '.$ex);if(PHP_SAPI==='cli'){fwrite(STDERR,"Error $id: ".$ex->getMessage()."\n");exit(1);}if(str_starts_with(route_path(),'/api/'))json_out(['error'=>'The request could not be completed. Reference '.$id],500);http_response_code(500);header('Content-Type: text/html; charset=utf-8');echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Service temporarily unavailable</title></head><body><h1>Service temporarily unavailable</h1><p>Please try again shortly. If this continues, contact the portal administrator and quote reference '.e($id).'.</p><a href="'.e(url()).'">Return to the portal</a></body></html>';exit;});
if(installed() && cfg('force_https',true) && PHP_SAPI!=='cli' && (($_SERVER['HTTPS']??'')!=='on')){header('Location: '.url(ltrim(route_path(),'/')).(!empty($_SERVER['QUERY_STRING'])?'?'.$_SERVER['QUERY_STRING']:''),true,308);exit;}
