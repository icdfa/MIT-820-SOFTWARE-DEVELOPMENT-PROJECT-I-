<?php
// Local PHP development server only: php -S 127.0.0.1:8080 router.php
$path=rawurldecode(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH)?:'/');
if(preg_match('~^/(app|config|storage|database|cron|docs|tests|vendor)(/|$)|(^|/)\.|\.(sql|log|zip|md|bak)$|^/START_HERE~i',$path)){http_response_code(403);exit('Access denied');}
$real=realpath(__DIR__.$path);if(str_starts_with($path,'/assets/')&&$real&&str_starts_with($real,__DIR__.'/assets/')&&is_file($real))return false;
if(preg_match('~^/(.*)\.php$~',$path,$m)){header('Location: /'.($m[1]==='index'?'':$m[1]),true,308);exit;}
$_SERVER['SCRIPT_NAME']='/index.php';require __DIR__.'/index.php';
