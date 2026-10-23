/* Interaction tests in a minimal DOM fixture; this is not a browser rendering test. */
'use strict';
const fs=require('fs'),path=require('path'),vm=require('vm'),assert=require('assert');
const source=fs.readFileSync(path.join(__dirname,'../assets/js/landing.js'),'utf8'),checks=[];
class Element{
 constructor(text=''){this.textContent=text;this.hidden=false;this.attrs={};this.listeners={};this.children={};}
 querySelector(s){return this.children[s]||null;}
 querySelectorAll(s){return this.children[s]||[];}
 setAttribute(k,v){this.attrs[k]=v;}
 addEventListener(k,f){(this.listeners[k]??=[]).push(f);}
 emit(k,e={}){for(const f of this.listeners[k]||[])f(e);}
 contains(target){return Object.values(this.children).flat().includes(target);}
}
function setup(reducedMotion=false){
 const carousel=new Element(),document=new Element(),controls=new Element(),pause=new Element(),status=new Element(),previous=new Element(),next=new Element(),media=new Element();
 const slides=[0,1,2].map(i=>{const e=new Element();e.hidden=i!==0;e.children.h1=new Element('Heading '+(i+1));return e;}),dots=[0,1,2].map(()=>new Element());let timer=null;
 carousel.children={'[data-slide]':slides,'[data-dot]':dots,'[data-carousel-controls]':controls,'[data-pause]':pause,'[data-slide-status]':status,'[data-prev]':previous,'[data-next]':next};document.children['[data-carousel]']=carousel;document.hidden=false;controls.hidden=true;media.matches=reducedMotion;
 vm.runInNewContext(source,{document,window:{matchMedia:()=>media,setInterval:f=>(timer=f,1),clearInterval:()=>timer=null}});
 return {carousel,document,controls,pause,status,previous,next,slides,dots,media,tick:()=>timer?.(),active:()=>slides.findIndex(x=>!x.hidden),running:()=>!!timer};
}
function check(name,f){f();checks.push({check:name,passed:true});}
const s=setup();
check('First slide and accessible controls are ready',()=>{assert.equal(s.active(),0);assert.equal(s.controls.hidden,false);assert.equal(s.dots[0].attrs['aria-current'],'true');assert(s.running());});
check('Automatic rotation advances and wraps',()=>{s.tick();assert.equal(s.active(),1);s.tick();s.tick();assert.equal(s.active(),0);});
check('Manual next pauses rotation and announces the slide',()=>{s.next.emit('click');assert.equal(s.active(),1);assert(!s.running());assert.equal(s.pause.textContent,'Play');assert(s.status.textContent.includes('Slide 2 of 3'));});
check('Previous control wraps from first to last',()=>{s.previous.emit('click');s.previous.emit('click');assert.equal(s.active(),2);});
check('Slide selector updates visible content and current state',()=>{s.dots[1].emit('click');assert.equal(s.active(),1);assert.equal(s.dots[1].attrs['aria-current'],'true');assert.equal(s.slides.filter(x=>!x.hidden).length,1);});
check('Arrow keys navigate from the controls',()=>{let prevented=false;s.carousel.emit('keydown',{key:'ArrowRight',preventDefault:()=>prevented=true});assert.equal(s.active(),2);assert(prevented);});
check('Play resumes when the carousel is not focused',()=>{s.pause.emit('click');assert(s.running());});
check('Hover pauses and leaving resumes',()=>{s.carousel.emit('mouseenter');assert(!s.running());s.carousel.emit('mouseleave');assert(s.running());});
check('Keyboard focus pauses automatic rotation',()=>{s.carousel.emit('focusin');assert(!s.running());s.carousel.emit('focusout',{relatedTarget:null});assert(s.running());});
check('Background tab pauses and visible tab resumes',()=>{s.document.hidden=true;s.document.emit('visibilitychange');assert(!s.running());s.document.hidden=false;s.document.emit('visibilitychange');assert(s.running());});
check('Reduced-motion preference stops automatic rotation',()=>{s.media.emit('change',{matches:true});assert(!s.running());const reduced=setup(true);assert(!reduced.running());assert.equal(reduced.active(),0);});
check('Non-landing pages require no carousel elements',()=>{vm.runInNewContext(source,{document:{querySelector:()=>null}});});
const report={release:'1.0.4',suite:'carousel interaction fixture',total:checks.length,passed:checks.length,checks,browser_rendering_test:false};fs.writeFileSync(path.join(__dirname,'acceptance-carousel.json'),JSON.stringify(report,null,2)+'\n');console.log(JSON.stringify({suite:report.suite,total:checks.length,passed:checks.length}));
