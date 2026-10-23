<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require dirname(__DIR__).'/app/bootstrap.php';
if(!installed()){fwrite(STDERR,"CivicFlow is not installed.\n");exit(1);}
$f=fopen(ROOT.'/storage/cache/workflow.lock','c+');if(!$f||!flock($f,LOCK_EX|LOCK_NB)){echo "Workflow already running.\n";exit;}
try{$escalated=escalation_job();$delivery=notification_job(20);set_setting('workflow_last_run',now());echo json_encode(['time'=>now(),'escalated'=>$escalated]+$delivery,JSON_UNESCAPED_SLASHES)."\n";}finally{flock($f,LOCK_UN);fclose($f);}
