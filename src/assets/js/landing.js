'use strict';
(() => {
 const carousel=document.querySelector('[data-carousel]');
 if(!carousel)return;
 const slides=[...carousel.querySelectorAll('[data-slide]')],dots=[...carousel.querySelectorAll('[data-dot]')];
 const controls=carousel.querySelector('[data-carousel-controls]'),pause=carousel.querySelector('[data-pause]'),status=carousel.querySelector('[data-slide-status]');
 if(slides.length<2||!controls||!pause)return;
 const reduced=window.matchMedia('(prefers-reduced-motion: reduce)');
 let current=0,paused=reduced.matches,hover=false,focused=false,timer=null;
 controls.hidden=false;
 function draw(index,announce=false){
  current=(index+slides.length)%slides.length;
  slides.forEach((slide,i)=>slide.hidden=i!==current);
  dots.forEach((dot,i)=>dot.setAttribute('aria-current',String(i===current)));
  if(announce&&status)status.textContent='Slide '+(current+1)+' of '+slides.length+': '+slides[current].querySelector('h1').textContent;
 }
 function schedule(){
  window.clearInterval(timer);timer=null;
  pause.textContent=paused?'Play':'Pause';pause.setAttribute('aria-label',paused?'Start automatic slideshow':'Pause automatic slideshow');
  if(!paused&&!hover&&!focused&&!document.hidden)timer=window.setInterval(()=>draw(current+1),6500);
 }
 function manual(index){paused=true;draw(index,true);schedule();}
 carousel.querySelector('[data-prev]').addEventListener('click',()=>manual(current-1));
 carousel.querySelector('[data-next]').addEventListener('click',()=>manual(current+1));
 dots.forEach((dot,i)=>dot.addEventListener('click',()=>manual(i)));
 pause.addEventListener('click',()=>{paused=!paused;schedule();});
 carousel.addEventListener('mouseenter',()=>{hover=true;schedule();});
 carousel.addEventListener('mouseleave',()=>{hover=false;schedule();});
 carousel.addEventListener('focusin',()=>{focused=true;schedule();});
 carousel.addEventListener('focusout',event=>{focused=carousel.contains(event.relatedTarget);schedule();});
 carousel.addEventListener('keydown',event=>{if(event.key==='ArrowLeft'||event.key==='ArrowRight'){event.preventDefault();manual(current+(event.key==='ArrowLeft'?-1:1));}});
 document.addEventListener('visibilitychange',schedule);
 reduced.addEventListener('change',event=>{if(event.matches){paused=true;schedule();}});
 draw(0);schedule();
})();
