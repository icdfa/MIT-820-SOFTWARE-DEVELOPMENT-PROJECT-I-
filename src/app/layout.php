<?php
declare(strict_types=1);
function icon(string $name): string {
 $paths=[
 'grid'=>'<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
 'file'=>'<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M8 13h8m-8 4h6"/>',
 'building'=>'<path d="m3 9 9-6 9 6M4 10h16M6 10v9m6-9v9m6-9v9M3 21h18"/>',
 'search'=>'<circle cx="10.5" cy="10.5" r="7.5"/><path d="m16 16 5 5"/>',
 'chat'=>'<path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.4 8.4 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8v.5z"/><path d="M8 11h8m-8 4h5"/>',
 'users'=>'<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2m20 0v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/><circle cx="9" cy="7" r="4"/>',
 'bell'=>'<path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9m-11 13h4"/>',
 'settings'=>'<path d="M12 3v3m0 12v3M3 12h3m12 0h3M5.6 5.6l2.1 2.1m8.6 8.6 2.1 2.1M5.6 18.4l2.1-2.1m8.6-8.6 2.1-2.1"/><circle cx="12" cy="12" r="5"/>',
 'check'=>'<path d="m5 12 4 4L20 5"/>',
 'clock'=>'<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
 'arrow'=>'<path d="M4 12h16m-6-6 6 6-6 6"/>',
 'plus'=>'<path d="M12 5v14M5 12h14"/>',
 'shield'=>'<path d="M12 3 3 7v6c0 5 9 9 9 9s9-4 9-9V7z"/><path d="m8 12 3 3 5-6"/>',
 'chart'=>'<path d="M3 3v18h18M7 15l4-5 4 3 6-8"/>',
 'logout'=>'<path d="M9 3H4v18h5m6-14 5 5-5 5M9 12h11"/>',
 'menu'=>'<path d="M3 6h18M3 12h18M3 18h18"/>',
 'download'=>'<path d="M12 3v12m-5-5 5 5 5-5M4 16v5h16v-5"/>',
 'help'=>'<circle cx="12" cy="12" r="9"/><path d="M9.1 9a3 3 0 0 1 5.8 1c0 2-3 3-3 3m.1 4h.01"/>',
 ];return '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.($paths[$name]??$paths['file']).'</svg>';
}
function badge(string $s): string {return '<span class="badge status-'.e($s).'">'.e(label($s)).'</span>';}
function page_start(string $title,string $subtitle='',string $active=''): void {
 $u=current_user();$name=installed()?setting('app_name','CivicFlow'):'CivicFlow';
 $unread=$u?(int)value('SELECT COUNT(*) FROM notifications WHERE user_id=? AND read_at IS NULL',[$u['id']]):0;
 $nav=[];
 if($u){
  $nav[]=['dashboard','Dashboard','grid','Workspace'];
  if(can('requests.scope_own',$u)||is_staff($u))$nav[]=['requests',$u['role']==='citizen'?'My requests':'Request register','file',''];
  $nav[]=['services','Service directory','building','Services'];
  $nav[]=['track','Track a request','search',''];$nav[]=['assistant','Service assistance','chat',''];
  if(can('reports.view',$u))$nav[]=['reports','Reports','chart','Operations'];
  $adminNav=[['admin/services','Manage services','building','services.manage'],['admin/departments','Departments','grid','departments.manage'],['admin/users','People and roles','users','users.manage'],['admin/roles','Permissions','shield','roles.manage'],['admin/knowledge','Notices and FAQs','help','knowledge.manage'],['admin/outbox','Notification outbox','bell','outbox.view'],['admin/compose','Compose message','chat','outbox.compose'],['admin/templates','Email templates','file','templates.manage'],['admin/audit','Audit trail','shield','audit.view'],['admin/settings','Configuration','settings','settings.manage']];
  $first=true;foreach($adminNav as $x)if(can($x[3],$u)){$nav[]=[$x[0],$x[1],$x[2],$first?'Administration':''];$first=false;}
  if($u['role']==='admin')$nav[]=['admin/demo-data','Demonstration data','users',''];
  $nav[]=['notifications','Notifications','bell','Account'];$nav[]=['profile','My profile','users',''];$nav[]=['profile/security','Account security','shield',''];$nav[]=['help','Help centre','help',''];
 }
 $public=[['','Home'],['services','Services'],['track','Track a request'],['assistant','Service assistance'],['help','Help centre']];
 $GLOBALS['page_shell_public']=!$u;
 ?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="theme-color" content="#1b365d"><meta name="csrf-token" content="<?=e(csrf_token())?>"><meta name="description" content="Submit complaints, apply for permits and track requests through the CivicFlow service portal."><title><?=e($title)?> | <?=e($name)?></title><link rel="icon" href="<?=e(url('assets/img/logo.svg'))?>" type="image/svg+xml"><link rel="stylesheet" href="<?=e(url('assets/css/portal-template.css?v=1.0.4'))?>"><link rel="stylesheet" href="<?=e(url('assets/css/app.css?v=1.0.4'))?>"><link rel="stylesheet" href="<?=e(url('assets/css/landing.css?v=1.0.4'))?>"></head><body class="<?=$u?'workspace-page':('public-page'.(in_array(route_path(),['/','/index'],true)?' landing-page':''))?>"><a class="skip-link" href="#mainContent">Skip to content</a>
 <?php if(!$u):?><header class="public-header"><div class="public-masthead"><a class="public-brand" href="<?=e(url())?>"><img src="<?=e(url('assets/img/logo.svg'))?>" alt=""><span><strong><?=e($name)?></strong><small>Government Service Delivery Portal</small></span></a><div class="public-account"><a href="<?=e(url('login'))?>">Sign in</a><a class="btn btn-primary" href="<?=e(url('register'))?>">Create account</a></div></div><nav class="public-nav" aria-label="Primary navigation"><div><?php foreach($public as $n):?><a href="<?=e(url($n[0]))?>" class="<?=$active===$n[0]&&($n[0]!==''||route_path()==='/')?'active':''?>"><?=e($n[1])?></a><?php endforeach;?></div></nav></header><div class="campus-shell public-shell"><main class="campus-main" id="mainContent" tabindex="-1">
 <?php else:?><div class="campus-shell"><button class="sidebar-overlay" id="sidebarOverlay" aria-label="Close navigation" type="button"></button><aside class="campus-sidebar" id="campusSidebar"><a class="campus-brand" href="<?=e(url('dashboard'))?>"><img src="<?=e(url('assets/img/logo.svg'))?>" alt=""><span><strong><?=e($name)?></strong><small>Service Delivery Portal</small></span></a><div class="sidebar-user"><div class="avatar"><?=e(mb_strtoupper(mb_substr($u['name'],0,1)))?></div><div><strong><?=e($u['name'])?></strong><small><?=e(label($u['role']))?></small></div></div><nav class="sidebar-nav" aria-label="Primary navigation"><?php foreach($nav as $n):if($n[3]):?><span class="nav-caption"><?=e($n[3])?></span><?php endif;?><a href="<?=e(url($n[0]))?>" class="<?=$active===$n[0]?'active':''?>" <?=$active===$n[0]?'aria-current="page"':''?>><?=icon($n[2])?><span><?=e($n[1])?></span></a><?php endforeach;?></nav><div class="sidebar-footer"><form method="post" action="<?=e(url('logout'))?>"><?=csrf_field()?><button class="nav-signout" type="submit"><?=icon('logout')?> Sign out</button></form><a href="<?=e(url('privacy'))?>">Privacy notice</a></div></aside><main class="campus-main" id="mainContent" tabindex="-1"><div class="workspace-toolbar"><button class="sidebar-toggle" id="sidebarToggle" aria-label="Open navigation" aria-controls="campusSidebar" aria-expanded="false" type="button"><?=icon('menu')?></button><span><?=e($u['role']==='citizen'?'Citizen portal':'Staff portal')?></span><div class="topbar-actions"><a href="<?=e(url('notifications'))?>" class="toolbar-link"><?=icon('bell')?> Notifications<?php if($unread):?><span class="unread-count"><?=$unread?></span><?php endif;?></a><a href="<?=e(url('profile'))?>" class="toolbar-link"><?=e($u['name'])?></a></div></div><?php endif;?>
 <header class="campus-topbar"><div class="header-left"><div><span class="breadcrumb-label"><a href="<?=e(url($u?'dashboard':''))?>"><?=$u?'Dashboard':'Home'?></a> / <?=e($title)?></span><h1><?=e($title)?></h1><?php if($subtitle):?><p><?=e($subtitle)?></p><?php endif;?></div></div></header><div class="campus-content"><?php $f=$_SESSION['flash']??null;unset($_SESSION['flash']);if($f):?><div class="alert alert-<?=e($f['kind'])?>" role="alert"><?=e($f['text'])?></div><?php endif;
}
function page_end(): void { ?></div><footer class="campus-footer"><span>© <?=date('Y')?> <?=e(setting('organisation_name','CivicFlow'))?> · Service Delivery Portal</span><span><a href="<?=e(url('help'))?>">Help</a> · <a href="<?=e(url('privacy'))?>">Privacy</a> · West Africa Time</span></footer></main></div><script src="<?=e(url('assets/js/app.js?v=1.0.4'))?>" defer></script><script src="<?=e(url('assets/js/landing.js?v=1.0.4'))?>" defer></script></body></html><?php }
function panel_start(string $title,string $extra=''): void {echo '<section class="panel"><div class="panel-header"><h2>'.e($title).'</h2>'.$extra.'</div><div class="panel-body">';}
function panel_end(): void {echo '</div></section>';}
function empty_state(string $title,string $text,string $href='',string $cta=''): void {echo '<div class="empty-state">'.icon('file').'<h3>'.e($title).'</h3><p>'.e($text).'</p>'.($href?'<a class="btn btn-primary" href="'.e(url($href)).'">'.e($cta).'</a>':'').'</div>';}
function form_control_id(string $name): string {static $used=[];$id=preg_replace('/[^a-zA-Z0-9_-]/','_',$name);$n=$used[$id]=($used[$id]??0)+1;return $n===1?$id:$id.'_'.$n;}
function field(string $name,string $labelText,string $value='',string $type='text',string $extra=''): void {$id=form_control_id($name);echo '<div class="field"><label for="'.e($id).'">'.e($labelText).'</label><input id="'.e($id).'" name="'.e($name).'" type="'.e($type).'" value="'.e($value).'" '.$extra.'></div>';}
function textarea_field(string $name,string $labelText,string $value='',int $max=5000,bool $required=true): void {$id=form_control_id($name);echo '<div class="field"><label for="'.e($id).'">'.e($labelText).'</label><textarea id="'.e($id).'" name="'.e($name).'" rows="5" maxlength="'.$max.'" '.($required?'required':'').'>'.e($value).'</textarea></div>';}
function select_field(string $name,string $labelText,array $options,string $selected='',string $extra=''): void {$id=form_control_id($name);echo '<div class="field"><label for="'.e($id).'">'.e($labelText).'</label><select id="'.e($id).'" name="'.e($name).'" '.$extra.'>';foreach($options as $val=>$text)echo '<option value="'.e($val).'" '.((string)$val===$selected?'selected':'').'>'.e($text).'</option>';echo '</select></div>';}
function file_field(): void {$id=form_control_id('documents');echo '<div class="field upload-box"><label for="'.e($id).'">Supporting documents</label><input id="'.e($id).'" name="documents[]" type="file" accept=".pdf,.jpg,.jpeg,.png" multiple><small>PDF, JPG or PNG · maximum 5 MB per file · up to three files per upload. Files stay private.</small></div>';}
function request_table(array $list,bool $staff=false): void {
 if(!$list){empty_state('No requests to show','Try adjusting your filters or start a new service request.');return;}
 echo '<div class="table-responsive"><table class="table"><thead><tr><th>Reference / request</th>'.($staff?'<th>Citizen</th>':'').'<th>Department</th><th>Status</th><th>Service target</th><th></th></tr></thead><tbody>';
 foreach($list as $r){$late=!in_array($r['status'],FINAL_STATUSES,true)&&$r['status']!=='draft'&&$r['due_at']&&$r['due_at']<now();echo '<tr><td><a class="table-title" href="'.e(url('requests/'.$r['id'])).'">'.e($r['subject']).'</a><small class="block mono">'.e($r['reference']).'</small></td>'.($staff?'<td>'.e($r['citizen_name']??'').'</td>':'').'<td>'.e($r['department_name']).'</td><td>'.badge($r['status']).($r['escalated_at']?'<small class="block danger-text">Escalated</small>':'').'</td><td class="'.($late?'danger-text':'').'">'.e(dates($r['due_at'])).($late?'<small class="block">Overdue</small>':'').'</td><td><a class="icon-button" aria-label="Open '.e($r['reference']).'" href="'.e(url('requests/'.$r['id'])).'">'.icon('arrow').'</a></td></tr>';}
 echo '</tbody></table></div>';
}
