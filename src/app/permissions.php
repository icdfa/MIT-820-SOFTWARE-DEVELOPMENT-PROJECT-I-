<?php
declare(strict_types=1);
function permission_catalogue(): array {return [
 'requests.create'=>['Citizen services','Create own requests'],
 'requests.scope_own'=>['Request access','View own requests'],
 'requests.scope_department'=>['Request access','View department requests'],
 'requests.scope_all'=>['Request access','View requests across all departments'],
 'requests.review'=>['Request processing','Start review and request information'],
 'requests.decide'=>['Request processing','Approve, resolve and reject requests'],
 'requests.assign'=>['Request processing','Assign officers within authorised department'],
 'requests.reroute'=>['Request processing','Route requests between departments'],
 'requests.message'=>['Request processing','Send citizen-visible messages'],
 'requests.internal_notes'=>['Request processing','Read and write internal staff notes'],
 'requests.withdraw'=>['Citizen services','Withdraw own eligible requests'],
 'requests.reopen'=>['Citizen services','Reopen own resolved complaints'],
 'requests.feedback'=>['Citizen services','Rate own completed requests'],
 'documents.download'=>['Documents','Download authorised attachments'],
 'documents.print'=>['Documents','Print authorised receipts and approvals'],
 'reports.view'=>['Oversight','View operational reports'],
 'reports.export'=>['Oversight','Export scoped request reports'],
 'audit.view'=>['Oversight','View the audit trail'],
 'services.manage'=>['Administration','Manage service catalogue'],
 'departments.manage'=>['Administration','Manage departments'],
 'users.manage'=>['Administration','Manage accounts and role assignments'],
 'roles.manage'=>['Administration','Manage roles and permissions'],
 'knowledge.manage'=>['Communications','Manage FAQs and announcements'],
 'outbox.view'=>['Communications','View notification delivery records'],
 'outbox.manage'=>['Communications','Retry and cancel queued notifications'],
 'outbox.compose'=>['Communications','Compose messages to registered users'],
 'templates.manage'=>['Communications','Edit and preview email templates'],
 'workflow.run'=>['Administration','Run escalation and notification workflows'],
 'settings.manage'=>['Administration','Configure integrations and workspace']
 ];}
function seed_permissions(): void {
 $citizen=['requests.create','requests.scope_own','requests.message','requests.withdraw','requests.reopen','requests.feedback','documents.download','documents.print'];
 $officer=['requests.scope_department','requests.review','requests.decide','requests.assign','requests.message','requests.internal_notes','documents.download','documents.print','reports.view'];
 $definitions=[
 'admin'=>['Administrator','Full workspace administration and permission management.',array_keys(permission_catalogue())],
 'citizen'=>['Citizen','Own requests, documents and service feedback.',$citizen],
 'officer'=>['Service officer','Department-scoped review, decisions and citizen communication.',$officer],
 'department_head'=>['Department head','Department operations, reporting and staff oversight.',array_merge($officer,['reports.export'])],
 'auditor'=>['Oversight auditor','Read-only access across departments with reports and audit trail.',['requests.scope_all','documents.print','reports.view','reports.export','audit.view']],
 'communications'=>['Communications officer','Notification outbox, message templates, FAQs and announcements.',['knowledge.manage','outbox.view','outbox.manage','outbox.compose','templates.manage']]
 ];
 foreach($definitions as $key=>$r){insert('roles',['role_key'=>$key,'role_name'=>$r[0],'description'=>$r[1],'is_system'=>1,'active'=>1]);foreach(array_unique($r[2])as $permission)insert('role_permissions',['role_key'=>$key,'permission_key'=>$permission]);}
}
function user_permissions(?array $u=null): array {
 $u=$u??current_user();if(!$u)return [];if($u['role']==='admin')return array_keys(permission_catalogue());static $cache=[];$cacheKey=$u['id'].':'.$u['auth_version'];if(isset($cache[$cacheKey]))return $cache[$cacheKey];
 return $cache[$cacheKey]=array_column(rows('SELECT DISTINCT rp.permission_key FROM role_permissions rp JOIN roles r ON r.role_key=rp.role_key WHERE r.active=1 AND (r.role_key=? OR r.role_key IN (SELECT role_key FROM user_roles WHERE user_id=?))',[$u['role'],$u['id']]),'permission_key');
}
function can(string $permission,?array $u=null): bool {return in_array($permission,user_permissions($u),true);}
function require_permission(string $permission): array {$u=require_user();if(!can($permission,$u)){audit('access.permission_denied',null,['permission'=>$permission]);abort_page(403,'You do not have permission to perform this action.');}return $u;}
function admin_permission(string $path): string {return match($path){'/admin/services'=>'services.manage','/admin/departments'=>'departments.manage','/admin/users'=>'users.manage','/admin/roles'=>'roles.manage','/admin/knowledge'=>'knowledge.manage','/admin/outbox','/admin/outbox/detail'=>'outbox.view','/admin/outbox/retry','/admin/outbox/cancel'=>'outbox.manage','/admin/compose'=>'outbox.compose','/admin/templates'=>'templates.manage','/admin/audit'=>'audit.view','/admin/settings'=>'settings.manage','/admin/jobs'=>'workflow.run','/admin/setup-complete'=>'settings.manage',default=>'settings.manage'};}

function role_has_permission(string $role,string $permission): bool {return $role==='admin'||(bool)row('SELECT permission_key FROM role_permissions rp JOIN roles r ON r.role_key=rp.role_key WHERE rp.role_key=? AND rp.permission_key=? AND r.active=1',[$role,$permission]);}
