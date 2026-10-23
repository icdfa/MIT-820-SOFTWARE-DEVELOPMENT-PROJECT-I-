'use strict';
const qs=(s,r=document)=>r.querySelector(s);
const sidebar=qs('#campusSidebar'), overlay=qs('#sidebarOverlay'), toggle=qs('#sidebarToggle');
function closeMenu(){sidebar?.classList.remove('open');overlay?.classList.remove('show');toggle?.setAttribute('aria-expanded','false');}
toggle?.addEventListener('click',()=>{const on=sidebar.classList.toggle('open');overlay.classList.toggle('show',on);toggle.setAttribute('aria-expanded',String(on));});
overlay?.addEventListener('click',closeMenu);document.addEventListener('keydown',e=>{if(e.key==='Escape')closeMenu();});
qs('#printButton')?.addEventListener('click',()=>window.print());
document.querySelectorAll('[data-confirm]').forEach(f=>f.addEventListener('submit',e=>{if(!confirm(f.dataset.confirm))e.preventDefault();}));
document.querySelectorAll('form[data-once]').forEach(f=>f.addEventListener('submit',()=>{if(f.checkValidity())setTimeout(()=>f.querySelectorAll('button[type="submit"]').forEach(b=>b.disabled=true),50);}));
const filter=qs('#serviceKind');filter?.addEventListener('change',()=>filter.form.submit());
const serviceSelect=qs('#service_id');function updateService(){const o=serviceSelect?.selectedOptions[0];const info=qs('#serviceRequirements');if(info&&o)info.textContent=o.dataset.requirements||'Select a service to see its requirements.';}serviceSelect?.addEventListener('change',updateService);updateService();
const roles=qs('#staffRole');function deptVisibility(){const d=qs('#staffDepartment');if(d){d.required=roles.selectedOptions[0]?.dataset.needsDept==='1';d.disabled=false;}}roles?.addEventListener('change',deptVisibility);if(roles)deptVisibility();
const chatForm=qs('#assistantForm');
chatForm?.addEventListener('submit',async e=>{e.preventDefault();const field=qs('[name="question"]',chatForm), val=field.value.trim();if(!val)return;const log=qs('#chatLog'), button=qs('button[type="submit"]',chatForm);append('You',val,'user');field.value='';button.disabled=true;qs('#chatStatus').textContent='Finding service guidance…';
 function append(who,text,kind){const el=document.createElement('div');el.className='chat-message '+kind;const label=document.createElement('strong');label.textContent=who;const body=document.createElement('p');body.textContent=text;el.append(label,body);log.append(el);log.scrollTop=log.scrollHeight;return el;}
 try{const r=await fetch(chatForm.action,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded','X-CSRF-Token':qs('meta[name="csrf-token"]').content},body:new URLSearchParams({question:val,use_ai:qs('[name="use_ai"]',chatForm)?.checked?'1':'0'}),signal:AbortSignal.timeout(45000)});const data=await r.json();if(!r.ok)throw new Error(data.error||'Please try again.');const el=append(data.mode,data.answer,'assistant');if(data.sources?.length){const links=document.createElement('div');links.className='chat-sources';for(const s of data.sources){const a=document.createElement('a');a.href=s.url;a.textContent=s.title;links.append(a);}el.append(links);}qs('#chatStatus').textContent='Ready';}
 catch(err){append('Service assistant',err.message==='The operation was aborted due to timeout'?'The service took too long. Please try again.':err.message,'assistant');qs('#chatStatus').textContent='Please try again';}
 finally{button.disabled=false;field.focus();}
});
document.querySelectorAll('[data-question]').forEach(b=>b.addEventListener('click',()=>{const f=qs('#assistantForm [name="question"]');if(f){f.value=b.dataset.question;f.focus();}}));
const emailEditor=qs('#emailEditor'),emailSource=qs('#emailSource');
emailEditor?.addEventListener('input',()=>emailSource.value=emailEditor.innerHTML);
emailSource?.addEventListener('input',()=>{emailEditor.textContent='HTML source changed. Save the template to see the sanitised preview.';});
document.querySelectorAll('[data-format]').forEach(button=>button.addEventListener('click',()=>{emailEditor.focus();document.execCommand(button.dataset.format,false,null);emailSource.value=emailEditor.innerHTML;}));

emailEditor?.addEventListener('paste',e=>{e.preventDefault();document.execCommand('insertText',false,e.clipboardData.getData('text/plain'));});
