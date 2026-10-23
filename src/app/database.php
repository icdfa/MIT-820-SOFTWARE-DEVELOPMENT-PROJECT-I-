<?php
declare(strict_types=1);
function db(): PDO {
 static $pdo=null;if($pdo)return $pdo;
 $c=cfg('database',[]);
 if(($c['driver']??'mysql')==='sqlite'){$pdo=new PDO('sqlite:'.ROOT.'/storage/civicflow.sqlite');$pdo->exec('PRAGMA foreign_keys=ON');$pdo->exec('PRAGMA busy_timeout=5000');}
 else{$pdo=new PDO('mysql:host='.($c['host']??'localhost').';port='.($c['port']??3306).';dbname='.($c['name']??'').';charset=utf8mb4',$c['user']??'',$c['password']??'');}
 $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);return $pdo;
}
function sql(string $s,array $p=[]): PDOStatement {$q=db()->prepare($s);$q->execute($p);return $q;}
function row(string $s,array $p=[]): ?array {return sql($s,$p)->fetch()?:null;}
function rows(string $s,array $p=[]): array {return sql($s,$p)->fetchAll();}
function value(string $s,array $p=[]): mixed {return sql($s,$p)->fetchColumn();}
function insert(string $table,array $data): int {$keys=array_keys($data);sql('INSERT INTO '.$table.' ('.implode(',',$keys).') VALUES ('.implode(',',array_fill(0,count($keys),'?')).')',array_values($data));return (int)db()->lastInsertId();}
function setting(string $key,string $default=''): string {if(!cfg('installed',false))return $default;static $cache=[];if(!array_key_exists($key,$cache)){$v=value('SELECT setting_value FROM settings WHERE setting_key=?',[$key]);$cache[$key]=$v===false?$default:(string)$v;}return $cache[$key];}
function set_setting(string $key,string $val): void {if(row('SELECT setting_key FROM settings WHERE setting_key=?',[$key]))sql('UPDATE settings SET setting_value=? WHERE setting_key=?',[$val,$key]);else insert('settings',['setting_key'=>$key,'setting_value'=>$val]);}
function encrypt_secret(string $plain): string {if($plain==='')return ''; $iv=random_bytes(12);$tag='';$data=openssl_encrypt($plain,'aes-256-gcm',hash('sha256',(string)cfg('app_key'),true),OPENSSL_RAW_DATA,$iv,$tag);if($data===false)throw new RuntimeException('Encryption failed');return base64_encode($iv.$tag.$data);}
function secret(string $key): string {$raw=base64_decode(setting($key),true);if(!$raw||strlen($raw)<29)return '';return openssl_decrypt(substr($raw,28),'aes-256-gcm',hash('sha256',(string)cfg('app_key'),true),OPENSSL_RAW_DATA,substr($raw,0,12),substr($raw,12,16))?:'';}
function schema_statements(string $driver): array {
 $id=$driver==='mysql'?'INTEGER PRIMARY KEY AUTO_INCREMENT':'INTEGER PRIMARY KEY AUTOINCREMENT';
 $tail=$driver==='mysql'?' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci':'';
 $tables=[
 'departments'=>"id $id, name VARCHAR(140) NOT NULL, code VARCHAR(20) NOT NULL UNIQUE, description TEXT NOT NULL",
 'services'=>"id $id, department_id INTEGER NOT NULL, title VARCHAR(180) NOT NULL, slug VARCHAR(100) NOT NULL UNIQUE, kind VARCHAR(20) NOT NULL, description TEXT NOT NULL, requirements TEXT NOT NULL, fee DECIMAL(12,2) NOT NULL DEFAULT 0, sla_hours INTEGER NOT NULL DEFAULT 72, active INTEGER NOT NULL DEFAULT 1, FOREIGN KEY(department_id) REFERENCES departments(id)",
 'roles'=>"role_key VARCHAR(50) PRIMARY KEY, role_name VARCHAR(100) NOT NULL, description TEXT NOT NULL, is_system INTEGER NOT NULL DEFAULT 0, active INTEGER NOT NULL DEFAULT 1",
 'role_permissions'=>"role_key VARCHAR(50) NOT NULL, permission_key VARCHAR(100) NOT NULL, PRIMARY KEY(role_key,permission_key), FOREIGN KEY(role_key) REFERENCES roles(role_key)",
 'users'=>"id $id, name VARCHAR(140) NOT NULL, email VARCHAR(190) NOT NULL UNIQUE, phone VARCHAR(25) NOT NULL DEFAULT '', password_hash VARCHAR(255) NOT NULL, role VARCHAR(50) NOT NULL DEFAULT 'citizen', department_id INTEGER NULL, status VARCHAR(20) NOT NULL DEFAULT 'active', email_verified_at DATETIME NULL, notify_email INTEGER NOT NULL DEFAULT 1, notify_sms INTEGER NOT NULL DEFAULT 0, auth_version INTEGER NOT NULL DEFAULT 1, totp_secret TEXT NULL, totp_confirmed INTEGER NOT NULL DEFAULT 0, created_at DATETIME NOT NULL, FOREIGN KEY(department_id) REFERENCES departments(id)",
 'user_roles'=>"user_id INTEGER NOT NULL, role_key VARCHAR(50) NOT NULL, PRIMARY KEY(user_id,role_key), FOREIGN KEY(user_id) REFERENCES users(id), FOREIGN KEY(role_key) REFERENCES roles(role_key)",
 'email_templates'=>"id $id, template_key VARCHAR(60) NOT NULL UNIQUE, name VARCHAR(140) NOT NULL, subject VARCHAR(180) NOT NULL, html_body TEXT NOT NULL, plain_body TEXT NOT NULL, active INTEGER NOT NULL DEFAULT 1, updated_at DATETIME NOT NULL",
 'device_sessions'=>"id $id, user_id INTEGER NOT NULL, token_hash VARCHAR(64) NOT NULL UNIQUE, ip_address VARCHAR(64) NOT NULL, user_agent VARCHAR(250) NOT NULL, created_at DATETIME NOT NULL, last_seen DATETIME NOT NULL, revoked_at DATETIME NULL, FOREIGN KEY(user_id) REFERENCES users(id)",
 'recovery_codes'=>"id $id, user_id INTEGER NOT NULL, code_hash VARCHAR(64) NOT NULL UNIQUE, used_at DATETIME NULL, FOREIGN KEY(user_id) REFERENCES users(id)",
 'requests'=>"id $id, reference VARCHAR(50) NOT NULL UNIQUE, access_token VARCHAR(64) NOT NULL, user_id INTEGER NOT NULL, service_id INTEGER NOT NULL, department_id INTEGER NOT NULL, assigned_to INTEGER NULL, kind VARCHAR(20) NOT NULL, subject VARCHAR(180) NOT NULL, description TEXT NOT NULL, location VARCHAR(200) NOT NULL DEFAULT '', state_name VARCHAR(60) NOT NULL DEFAULT '', organisation VARCHAR(180) NOT NULL DEFAULT '', priority VARCHAR(20) NOT NULL DEFAULT 'normal', status VARCHAR(30) NOT NULL, due_at DATETIME NULL, escalated_at DATETIME NULL, closed_at DATETIME NULL, version INTEGER NOT NULL DEFAULT 1, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, FOREIGN KEY(user_id) REFERENCES users(id), FOREIGN KEY(service_id) REFERENCES services(id), FOREIGN KEY(department_id) REFERENCES departments(id), FOREIGN KEY(assigned_to) REFERENCES users(id)",
 'events'=>"id $id, request_id INTEGER NOT NULL, actor_id INTEGER NULL, from_status VARCHAR(30) NOT NULL DEFAULT '', to_status VARCHAR(30) NOT NULL DEFAULT '', message TEXT NOT NULL, visibility VARCHAR(20) NOT NULL DEFAULT 'public', created_at DATETIME NOT NULL, FOREIGN KEY(request_id) REFERENCES requests(id), FOREIGN KEY(actor_id) REFERENCES users(id)",
 'attachments'=>"id $id, request_id INTEGER NOT NULL, user_id INTEGER NOT NULL, original_name VARCHAR(200) NOT NULL, stored_name VARCHAR(100) NOT NULL UNIQUE, mime VARCHAR(100) NOT NULL, size_bytes INTEGER NOT NULL, sha256 VARCHAR(64) NOT NULL, visibility VARCHAR(20) NOT NULL DEFAULT 'public', created_at DATETIME NOT NULL, FOREIGN KEY(request_id) REFERENCES requests(id), FOREIGN KEY(user_id) REFERENCES users(id)",
 'notifications'=>"id $id, user_id INTEGER NOT NULL, request_id INTEGER NULL, title VARCHAR(180) NOT NULL, body TEXT NOT NULL, link VARCHAR(250) NOT NULL, read_at DATETIME NULL, created_at DATETIME NOT NULL, FOREIGN KEY(user_id) REFERENCES users(id)",
 'outbox'=>"id $id, user_id INTEGER NOT NULL, request_id INTEGER NULL, channel VARCHAR(15) NOT NULL, recipient VARCHAR(190) NOT NULL, subject VARCHAR(180) NOT NULL, body TEXT NOT NULL, html_body TEXT NULL, template_key VARCHAR(60) NULL, state VARCHAR(30) NOT NULL DEFAULT 'pending', attempts INTEGER NOT NULL DEFAULT 0, next_attempt_at DATETIME NULL, sent_at DATETIME NULL, last_error VARCHAR(250) NOT NULL DEFAULT '', provider_id VARCHAR(150) NOT NULL DEFAULT '', created_at DATETIME NOT NULL, FOREIGN KEY(user_id) REFERENCES users(id)",
 'settings'=>"setting_key VARCHAR(100) PRIMARY KEY, setting_value TEXT NOT NULL",
 'password_resets'=>"id $id, user_id INTEGER NOT NULL, token_hash VARCHAR(64) NOT NULL UNIQUE, expires_at DATETIME NOT NULL, used_at DATETIME NULL, FOREIGN KEY(user_id) REFERENCES users(id)",
 'email_verifications'=>"id $id, user_id INTEGER NOT NULL, token_hash VARCHAR(64) NOT NULL UNIQUE, expires_at DATETIME NOT NULL, used_at DATETIME NULL, FOREIGN KEY(user_id) REFERENCES users(id)",
 'audit_logs'=>"id $id, actor_id INTEGER NULL, action VARCHAR(100) NOT NULL, entity_id INTEGER NULL, metadata TEXT NOT NULL, ip_address VARCHAR(64) NOT NULL, created_at DATETIME NOT NULL",
 'feedback'=>"id $id, request_id INTEGER NOT NULL UNIQUE, user_id INTEGER NOT NULL, rating INTEGER NOT NULL, comment TEXT NOT NULL, created_at DATETIME NOT NULL, FOREIGN KEY(request_id) REFERENCES requests(id), FOREIGN KEY(user_id) REFERENCES users(id)",
 'faqs'=>"id $id, question VARCHAR(220) NOT NULL, answer TEXT NOT NULL, keywords VARCHAR(250) NOT NULL, active INTEGER NOT NULL DEFAULT 1",
 'announcements'=>"id $id, title VARCHAR(180) NOT NULL, body TEXT NOT NULL, active INTEGER NOT NULL DEFAULT 1, created_at DATETIME NOT NULL"
 ];$out=[];foreach($tables as $name=>$cols)$out[]='CREATE TABLE IF NOT EXISTS '.$name.' ('.$cols.')'.$tail;
 foreach(['requests_user'=>'requests(user_id)','requests_dept_status'=>'requests(department_id,status)','requests_due'=>'requests(status,due_at)','events_request'=>'events(request_id)','notifications_user'=>'notifications(user_id,read_at)','outbox_state'=>'outbox(state,next_attempt_at)','audit_date'=>'audit_logs(created_at)']as $name=>$target)$out[]='CREATE INDEX idx_'.$name.' ON '.$target;return $out;
}
