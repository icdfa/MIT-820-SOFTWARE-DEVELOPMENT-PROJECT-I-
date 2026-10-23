<?php
declare(strict_types=1);
const FINAL_STATUSES=['approved','resolved','rejected','withdrawn'];
const STATUSES=['draft','submitted','in_review','needs_information','approved','resolved','rejected','withdrawn'];
function service_list(bool $all=false): array {return rows('SELECT s.*,d.name department_name FROM services s JOIN departments d ON d.id=s.department_id'.($all?'':' WHERE s.active=1').' ORDER BY s.kind,s.title');}
function best_officer(int $dept): ?int {$list=rows("SELECT u.*,(SELECT COUNT(*) FROM requests r WHERE r.assigned_to=u.id AND r.status IN ('submitted','in_review','needs_information')) work_count FROM users u WHERE u.status='active' AND u.department_id=? ORDER BY work_count,u.id",[$dept]);foreach($list as $u)if(can('requests.review',$u))return (int)$u['id'];return null;}
function event(int $id,?int $actor,string $from,string $to,string $message,string $visibility='public'): void {insert('events',['request_id'=>$id,'actor_id'=>$actor,'from_status'=>$from,'to_status'=>$to,'message'=>$message,'visibility'=>$visibility,'created_at'=>now()]);}
function notify(int $uid,string $title,string $body,string $link,?int $rid=null,bool $channels=true): void {
 insert('notifications',['user_id'=>$uid,'request_id'=>$rid,'title'=>$title,'body'=>$body,'link'=>$link,'read_at'=>null,'created_at'=>now()]);
 if(!$channels)return;$u=row('SELECT * FROM users WHERE id=?',[$uid]);if(!$u)return;
 foreach(['email'=>'email','sms'=>'phone']as $ch=>$field){if(!$u['notify_'.$ch]||empty($u[$field]))continue;
 $key=$rid?'service_update':(str_starts_with($title,'Welcome')?'welcome':'general_notice');$template=row('SELECT * FROM email_templates WHERE template_key=? AND active=1',[$key]);$rr=$rid?row('SELECT r.reference,r.status,s.title service FROM requests r JOIN services s ON s.id=r.service_id WHERE r.id=?',[$rid]):[];$message=$template?template_render($template,['name'=>$u['name'],'title'=>$title,'message'=>$body,'reference'=>$rr['reference']??'','status'=>label($rr['status']??''),'service'=>$rr['service']??'','link'=>url($link),'app_name'=>setting('app_name','CivicFlow')]):['subject'=>$title,'body'=>$body."\n\nOpen: ".url($link),'html_body'=>'<p>'.nl2br(e($body)).'</p>'];
 insert('outbox',['user_id'=>$uid,'request_id'=>$rid,'channel'=>$ch,'recipient'=>$u[$field],'subject'=>$ch==='email'?$message['subject']:$title,'body'=>$ch==='email'?$message['body']:$body,'html_body'=>$ch==='email'?$message['html_body']:null,'template_key'=>$template?$key:null,'state'=>setting($ch.'_enabled','0')==='1'?'pending':'configuration_required','attempts'=>0,'created_at'=>now()]);}
}
function request_notice(array $r,string $title,string $body): void {notify((int)$r['user_id'],$title,$r['reference'].': '.$body,'requests/'.$r['id'],(int)$r['id']);}
function validated_uploads(): array {
 $f=$_FILES['documents']??null;if(!$f)return [];
 $out=[];foreach(['name','error','tmp_name','size']as $key)if(!isset($f[$key])||!is_array($f[$key]))throw new InvalidArgumentException('The upload form is invalid. Please attach your documents again.');$count=count($f['name']);$allowed=['application/pdf'=>'pdf','image/jpeg'=>'jpg','image/png'=>'png'];
 for($i=0;$i<$count;$i++){
  if(($f['error'][$i]??UPLOAD_ERR_NO_FILE)===UPLOAD_ERR_NO_FILE)continue;
  if(count($out)>=3)throw new InvalidArgumentException('Attach up to three files at a time.');
  if($f['error'][$i]!==UPLOAD_ERR_OK)throw new InvalidArgumentException('An upload failed. Check the server upload limits and try again.');
  if($f['size'][$i]>5*1024*1024||$f['size'][$i]<1)throw new InvalidArgumentException('Each file must be between 1 byte and 5 MB.');
  $mime=(new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name'][$i]);if(!isset($allowed[$mime]))throw new InvalidArgumentException('Only PDF, JPG and PNG documents are accepted.');
  if(!is_uploaded_file($f['tmp_name'][$i]))throw new InvalidArgumentException('Invalid file upload.');
  $name=basename(str_replace('\\','/',$f['name'][$i]));$name=preg_replace('/[^\pL\pN ._()-]/u','_',$name)?:'document.'.$allowed[$mime];
  $out[]=['temp'=>$f['tmp_name'][$i],'original_name'=>mb_substr($name,0,180),'stored_name'=>bin2hex(random_bytes(24)).'.'.$allowed[$mime],'mime'=>$mime,'size_bytes'=>(int)$f['size'][$i],'sha256'=>hash_file('sha256',$f['tmp_name'][$i])];
 }
 return $out;
}
function attach_files(array $files,int $rid,int $uid,array &$moved,bool $internal=false): void {
 if(!$files)return;
 $count=(int)value('SELECT COUNT(*) FROM attachments WHERE request_id=?',[$rid]);$bytes=(int)value('SELECT COALESCE(SUM(size_bytes),0) FROM attachments WHERE user_id=?',[$uid]);
 if($count+count($files)>20)throw new InvalidArgumentException('A request can hold up to 20 supporting documents.');if($bytes+array_sum(array_column($files,'size_bytes'))>100*1024*1024)throw new InvalidArgumentException('Your upload allowance is 100 MB. Contact the portal administrator if you need more space.');
 foreach($files as $f){$dest=ROOT.'/storage/uploads/'.$f['stored_name'];if(!move_uploaded_file($f['temp'],$dest))throw new RuntimeException('Cannot save uploaded file');@chmod($dest,0600);$moved[]=$dest;unset($f['temp']);insert('attachments',array_merge($f,['request_id'=>$rid,'user_id'=>$uid,'visibility'=>$internal?'internal':'public','created_at'=>now()]));}
}
function submit_request(array $u,array $data,array $files,?int $draftId=null,bool $draft=false): int {
 $s=row('SELECT * FROM services WHERE id=? AND active=1',[$data['service_id']]);if(!$s)throw new InvalidArgumentException('Choose an available service.');
 $existing=$draftId?scoped_request($draftId,$u):null;if($existing&&($existing['status']!=='draft'||(int)$existing['user_id']!==(int)$u['id']))throw new InvalidArgumentException('Only your draft can be edited.');
 $existingFiles=$draftId?(int)value('SELECT COUNT(*) FROM attachments WHERE request_id=?',[$draftId]):0;
 if(!$draft&&$s['kind']==='permit'&&!empty($s['requirements'])&&count($files)+$existingFiles===0)throw new InvalidArgumentException('Attach the supporting documents listed for this permit.');
 $status=$draft?'draft':'submitted';$due=$draft?null:date('Y-m-d H:i:s',time()+(int)$s['sla_hours']*3600);$assignee=$draft?null:best_officer((int)$s['department_id']);$moved=[];
 db()->beginTransaction();try{
 $values=['service_id'=>(int)$s['id'],'department_id'=>(int)$s['department_id'],'assigned_to'=>$assignee,'kind'=>$s['kind'],'subject'=>$data['subject'],'description'=>$data['description'],'location'=>$data['location'],'state_name'=>$data['state_name'],'organisation'=>$data['organisation'],'priority'=>$data['priority'],'status'=>$status,'due_at'=>$due,'updated_at'=>now()];
 if($existing){$id=$draftId;$sets=implode(',',array_map(fn($k)=>$k.'=?',array_keys($values)));$p=array_values($values);$p[]=$id;$p[]=$data['version'];$q=sql('UPDATE requests SET '.$sets.',version=version+1 WHERE id=? AND status=\'draft\' AND version=?',$p);if(!$q->rowCount())throw new InvalidArgumentException('This draft changed. Reload it before saving.');}
 else{$id=insert('requests',array_merge($values,['reference'=>'GOV-'.($s['kind']==='permit'?'PER':'CMP').'-'.date('Y').'-'.strtoupper(bin2hex(random_bytes(5))),'access_token'=>strtoupper(bin2hex(random_bytes(8))),'user_id'=>(int)$u['id'],'created_at'=>now(),'version'=>1]));}
 attach_files($files,$id,(int)$u['id'],$moved);event($id,(int)$u['id'],$existing?'draft':'',$status,$draft?'Draft saved.':'Submitted and routed to the responsible department.');
 $r=row('SELECT * FROM requests WHERE id=?',[$id]);if(!$draft){request_notice($r,'Request received','Your request has been submitted. You can follow every update in your account.');if($assignee)notify($assignee,'New request assigned',$r['reference'].' is ready for review.','requests/'.$id,$id,false);}
 audit($draft?'request.draft_saved':'request.submitted',$id,['reference'=>$r['reference']]);db()->commit();return $id;
 }catch(Throwable $ex){if(db()->inTransaction())db()->rollBack();foreach($moved as $file)@unlink($file);throw $ex;}
}
function change_status(array $r,array $u,string $to,string $message,int $version): void {
 $allowed=['submitted'=>['in_review'],'in_review'=>['needs_information','rejected',$r['kind']==='permit'?'approved':'resolved'],'needs_information'=>['in_review']];
 if(!can(in_array($to,['approved','resolved','rejected'],true)?'requests.decide':'requests.review',$u)||!can_view($r,$u)||!in_array($to,$allowed[$r['status']]??[],true))throw new InvalidArgumentException('That status change is not allowed. Start review before making a decision.');
 if(strlen(trim($message))<5)throw new InvalidArgumentException('Explain the update or decision in at least five characters.');
 db()->beginTransaction();try{
 $q=sql('UPDATE requests SET status=?,closed_at=?,updated_at=?,version=version+1 WHERE id=? AND version=? AND status=?',[$to,in_array($to,FINAL_STATUSES,true)?now():null,now(),$r['id'],$version,$r['status']]);if(!$q->rowCount())throw new InvalidArgumentException('Another update was made. Reload this request.');
 event((int)$r['id'],(int)$u['id'],$r['status'],$to,$message);request_notice($r,'Request '.label($to),$message);audit('request.status_changed',(int)$r['id'],['from'=>$r['status'],'to'=>$to]);db()->commit();
 }catch(Throwable $ex){if(db()->inTransaction())db()->rollBack();throw $ex;}
}
function add_reply(array $r,array $u,string $message,array $files,bool $internal=false): void {
 if(in_array($r['status'],FINAL_STATUSES,true)||$r['status']==='draft')throw new InvalidArgumentException('Messages cannot be added to a draft or closed request.');
 if($internal&&!can('requests.internal_notes',$u))throw new InvalidArgumentException('Internal notes are for staff only.');
 $to=(!$internal&&$u['role']==='citizen'&&$r['status']==='needs_information')?'submitted':$r['status'];$moved=[];
 db()->beginTransaction();try{
 $q=sql('UPDATE requests SET status=?,updated_at=?,version=version+1 WHERE id=? AND version=?',[ $to,now(),$r['id'],$r['version']]);if(!$q->rowCount())throw new InvalidArgumentException('The request has changed. Reload before replying.');
 attach_files($files,(int)$r['id'],(int)$u['id'],$moved,$internal);event((int)$r['id'],(int)$u['id'],$r['status'],$to,$message,$internal?'internal':'public');
 if(!$internal){if($u['role']==='citizen'&&$r['assigned_to'])notify((int)$r['assigned_to'],'Citizen response received',$r['reference'].' has a new response.','requests/'.$r['id'],(int)$r['id'],false);elseif(is_staff($u))request_notice($r,'New message on your request',$message);}
 audit($internal?'request.internal_note':'request.message',(int)$r['id']);db()->commit();
 }catch(Throwable $ex){if(db()->inTransaction())db()->rollBack();foreach($moved as $f)@unlink($f);throw $ex;}
}
function escalation_job(): int {
 $list=rows("SELECT * FROM requests WHERE status IN ('submitted','in_review','needs_information') AND due_at<? AND escalated_at IS NULL",[now()]);$count=0;
 foreach($list as $r){db()->beginTransaction();try{$q=sql('UPDATE requests SET escalated_at=?,version=version+1 WHERE id=? AND escalated_at IS NULL',[now(),$r['id']]);if(!$q->rowCount()){db()->rollBack();continue;}
 event((int)$r['id'],null,$r['status'],$r['status'],'Service target exceeded. Escalated for departmental follow-up.');
 $staff=rows("SELECT * FROM users WHERE status='active' AND (role='admin' OR department_id=?)",[$r['department_id']]);foreach($staff as $s)if(can('requests.review',$s))notify((int)$s['id'],'Overdue request escalated',$r['reference'].' has passed its service target.','requests/'.$r['id'],(int)$r['id'],false);
 request_notice($r,'Service request escalated','The service target has passed. Your request has been escalated for follow-up.');audit('request.escalated',(int)$r['id']);db()->commit();$count++;}catch(Throwable $ex){if(db()->inTransaction())db()->rollBack();throw $ex;}}
 return $count;
}
function seed_catalogue(): void {
 seed_permissions();seed_email_templates();
 $depts=[['Public Works','WORKS','Roads, drainage, public infrastructure and street lighting.'],['Commerce and Industry','TRADE','Business premises, trade permits and commercial services.'],['Environment and Sanitation','ENV','Waste collection, environmental complaints and sanitation services.'],['Planning and Development','PLAN','Development permits, building applications and planning concerns.']];
 foreach($depts as $d)insert('departments',['name'=>$d[0],'code'=>$d[1],'description'=>$d[2]]);
 $services=[
 [1,'Report damaged roads','road-maintenance','complaint','Report a pothole, damaged road or blocked public drain.','Location or landmark; photographs where available.',0,72],
 [1,'Street lighting complaint','street-lighting','complaint','Report faulty streetlights and lighting hazards.','Pole location or nearest landmark.',0,48],
 [2,'Business premises permit','business-premises','permit','Apply for a business premises operating permit.','Business registration document; proof of premises or tenancy; proprietor identification.',5000,120],
 [2,'Trade licence','trade-licence','permit','Apply for a licence for a small trading activity.','Applicant identification; business address evidence; description of trading activity.',2500,72],
 [3,'Waste collection complaint','waste-collection','complaint','Report missed waste collection or illegal dumping.','Location and description; photographs where available.',0,48],
 [3,'Environmental nuisance','environmental-nuisance','complaint','Report persistent noise, pollution or other environmental nuisance.','Location and description of the nuisance.',0,72],
 [3,'Event sanitation permit','event-sanitation','permit','Request a sanitation review for an event.','Event plan; venue evidence; sanitation and waste management plan.',3000,96],
 [4,'Building development permit','building-permit','permit','Submit a development application for departmental review.','Site or ownership evidence; drawings; applicant identification.',15000,168],
 [4,'Planning enquiry or complaint','planning-enquiry','complaint','Raise a planning concern or request clarification.','Location and clear explanation of the issue.',0,96]
 ];
 foreach($services as $s)insert('services',['department_id'=>$s[0],'title'=>$s[1],'slug'=>$s[2],'kind'=>$s[3],'description'=>$s[4],'requirements'=>$s[5],'fee'=>cfg('demo_mode',false)?$s[6]:0,'sla_hours'=>$s[7],'active'=>cfg('demo_mode',false)?1:0]);
 $faqs=[
 ['How do I make a complaint?','Create an account, open Services, choose a complaint service and select Start request. Describe the issue and location. Add evidence if available, then submit. The request is routed to the responsible department.','complaint report issue submit'],
 ['How do I apply for a permit?','Open Services, filter to permits and read the requirements. Sign in, complete the application and upload the supporting documents as PDF, JPG or PNG. The officer reviews the application and records a decision.','permit licence apply documents business building'],
 ['How do I track my request?','Sign in and open My requests to see the full status history. You can also use Track request with the reference and private tracking code shown on your request receipt. Never share the code publicly.','track status reference code progress'],
 ['Which documents can I upload?','PDF, JPG and PNG files are supported. Each file must be at most 5 MB, with up to three files per upload. You can add more documents when replying to a request for information. Do not upload passwords or unnecessary sensitive information.','upload file pdf png jpeg size evidence'],
 ['What does Needs information mean?','The reviewing officer needs more detail. Open the request, read the officer message, and use Reply to provide the explanation and supporting documents. The request returns to the submitted queue for review.','needs information reply missing documents'],
 ['How long does a request take?','Each service lists a target in calendar hours. Your submitted request shows its exact target date. Open requests that pass the target are escalated when the scheduled workflow job runs. Targets are service commitments configured by the service authority.','time deadline long delay overdue escalate sla'],
 ['Are permit fees real?','The catalogue shows the charges published by the service authority. This platform does not collect payments. Follow the department’s instructions for any required payment; never send payment credentials in a request message.','fee payment money cost price'],
 ['Can I reopen a resolved complaint?','Yes. Open your resolved complaint and select Reopen complaint, giving a clear reason. A fresh service target is set and the department is notified.','reopen resolved complaint dissatisfied'],
 ['How is my information protected?','Citizens can access their own requests. Officers see their department requests, and administrators manage operations. Uploads are stored privately and downloaded only through permission checks. Read Privacy for details.','privacy secure data access'],
 ['Is this an official government portal?','CivicFlow is service delivery software operated by the organisation named on this portal. For service authority details, use the published support contact. CivicFlow itself does not imply government affiliation.','official government academic project demo']
 ];foreach($faqs as $f)insert('faqs',['question'=>$f[0],'answer'=>$f[1],'keywords'=>$f[2],'active'=>1]);
 insert('announcements',['title'=>'Welcome to CivicFlow','body'=>'Submit a request online, follow its progress and keep a clear record of every update.','active'=>1,'created_at'=>now()]);
 foreach(['app_name'=>'CivicFlow','app_tagline'=>'Public services. Clear progress.','email_enabled'=>'0','sms_enabled'=>'0','ai_enabled'=>'0','smtp_port'=>'587','smtp_security'=>'tls','sms_base_url'=>'','sms_sender'=>'CivicFlow','sms_channel'=>'generic','ai_model'=>'','project_version'=>'1.0.4']as $k=>$v)set_setting($k,$v);
}
function seed_demo(string $password): array {
 $hash=password_hash($password,PASSWORD_DEFAULT);$demo=[];
 $citizen=insert('users',['name'=>'Amina Bello','email'=>'citizen@example.test','phone'=>'08000000001','password_hash'=>$hash,'role'=>'citizen','status'=>'active','created_at'=>now()]);
 foreach(rows('SELECT * FROM departments')as $d){$email=strtolower($d['code']).'.officer@example.test';$id=insert('users',['name'=>$d['name'].' Officer','email'=>$email,'phone'=>'08000000002','password_hash'=>$hash,'role'=>'officer','department_id'=>$d['id'],'status'=>'active','created_at'=>now()]);$demo[]=['email'=>$email,'role'=>'Officer — '.$d['name']];}
 $demo[]=['email'=>'citizen@example.test','role'=>'Citizen'];
 $samples=[['Damaged road near the community centre',1,'submitted',-72],['Waste collection missed this week',5,'in_review',12],['Business premises application',3,'needs_information',48],['Community lighting report',2,'resolved',48],['Sample trade licence',4,'approved',24],['Planning concern at a public site',9,'submitted',72]];
 foreach($samples as $i=>$x){$svc=row('SELECT * FROM services WHERE id=?',[$x[1]]);$created=date('Y-m-d H:i:s',time()-86400*($i+1));$id=insert('requests',['reference'=>'GOV-'.($svc['kind']==='permit'?'PER':'CMP').'-'.date('Y').'-DEMO'.str_pad((string)($i+1),6,'0',STR_PAD_LEFT),'access_token'=>strtoupper(bin2hex(random_bytes(8))),'user_id'=>$citizen,'service_id'=>$svc['id'],'department_id'=>$svc['department_id'],'assigned_to'=>best_officer((int)$svc['department_id']),'kind'=>$svc['kind'],'subject'=>$x[0],'description'=>'Synthetic demonstration request. This record is sample data for the project presentation.','location'=>'Sample community, Lafia','state_name'=>'Nasarawa','organisation'=>'Sample Enterprise','priority'=>$i===0?'high':'normal','status'=>$x[2],'due_at'=>date('Y-m-d H:i:s',time()+$x[3]*3600),'closed_at'=>in_array($x[2],FINAL_STATUSES,true)?now():null,'version'=>1,'created_at'=>$created,'updated_at'=>now()]);event($id,$citizen,'','submitted','Sample request submitted.');if($x[2]!=='submitted')event($id,null,'submitted',$x[2],'Sample workflow update for demonstration.');}
 return $demo;
}

function queue_email_verification(array $u): void {
 if($u['email_verified_at'])return;
 sql('UPDATE email_verifications SET used_at=? WHERE user_id=? AND used_at IS NULL',[now(),$u['id']]);$token=bin2hex(random_bytes(32));insert('email_verifications',['user_id'=>$u['id'],'token_hash'=>hash('sha256',$token),'expires_at'=>date('Y-m-d H:i:s',time()+86400),'used_at'=>null]);
 insert('outbox',['user_id'=>$u['id'],'channel'=>'email','recipient'=>$u['email'],'subject'=>'Verify your '.setting('app_name','CivicFlow').' email address','body'=>'Confirm your email address within 24 hours: '.url('verify-email?token='.$token).' . If you did not create this account, ignore this message.','template_key'=>'auth_verification','state'=>setting('email_enabled','0')==='1'?'pending':'configuration_required','created_at'=>now()]);
}
