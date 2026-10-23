<?php
// The browser installer creates local.php. Do not put credentials in public files.
return ['installed'=>false,'base_url'=>'https://your-domain.example','force_https'=>true,'app_key'=>'GENERATED_DURING_INSTALL','database'=>['driver'=>'mysql','host'=>'localhost','port'=>3306,'name'=>'CPANEL_DATABASE','user'=>'CPANEL_USER','password'=>'YOUR_DATABASE_PASSWORD']];
