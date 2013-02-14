//Effects by Michael V. Petrovich, GT Multimedia, LLC, REV 20070417

var std_Slide_SlideInc = 5;              //% change in effect
var std_Slide_SlideSpeed = 20;           //time between interval


function getId(id){if(!id) return false; else return document.getElementById(id);}
function showId(id) {if(getId(id)) getId(id).style.display = '';}
function hideId(id) {if(getId(id)) getId(id).style.display = 'none';}
function toggleDisplay(myItem){if(getId(myItem)) {var style2 = getId(myItem).style;  style2.display = style2.display? '':'none';}}
function showElem(myElem) {if(myElem) myElem.style.display = ''; }
function hideElem(myElem) {if(myElem) myElem.style.display = 'none';}
function insertAfter(parent, node, referenceNode) {parent.insertBefore(node, referenceNode.nextSibling);}
function markTime(){var t = new Date(); return t.valueOf();}

function toggleSlide(id){
  var elem = getId(id);
  if (elem){
    if (elem.style.display == 'none') slideDown(id);
      else closeUp(id);
  }
}


var overlayId = 'overlay';

function createOverlay(overlayContentId){
  var newIdName = overlayId;
  var newdiv = getId(newIdName);
  if(!newdiv){
     newdiv = document.createElement('div');
     newdiv.setAttribute('id',newIdName);
     document.body.insertBefore(newdiv,document.body.childNodes[0]);
  }
  var arrayPageSize = getPageSize();
  newdiv.style.width = '100%'; //arrayPageSize[0] +'px';
  newdiv.style.height =  arrayPageSize[1] +'px';
  newdiv.style.position = 'absolute';
  newdiv.style.display  = 'none';
  newdiv.innerHTML = getId(overlayContentId).innerHTML;
  newdiv.style.left  = '0px';
  newdiv.style.top   = '0px';
  window.scrollTo(0,0);
}

function closeOverlay(){
  hideId(overlayId);
  getId(overlayId).innterHTML = '';
}

function changeText(myItem,newText){getId(myItem).innerHTML=newText;}

function hideGroup(group){
  var i=1;
  while (getId(group+i)){ getId(group+i).style.display='none';  i++; }
}

function hideGroupExcept(group,except){
  hideGroup(group);
  if (getId(group+except)){getId(group+except).style.display='';}
}


function colorGroup(group,except,c1,c2){
  var i=1;
  while (getId(group+i)){
    getId(group+i).style.backgroundColor = c1; 
    i++;
  }
  if (getId(group+except)){getId(group+except).style.backgroundColor = c2;}
}

function toggleUpDown(id){
  var elem = getId(id);
  if (elem){
    if (elem.style.display == 'none') slideDown(id);
      else closeUp(id);
  }
}

function setClassGroup(group,except,c1,c2){
  var i=1;
  while (getId(group+i)){
    getId(group+i).className = c1; 
    i++;
  }
  if (getId(group+except)){getId(group+except).className = c2;}
}


function changeStyle(id,styleclass,newstyle){
  elem = getId(id);
  if (elem){elem.style[styleclass] = newstyle;}
}


//================ get Element Position ===================
//credit: Peter-Paul Koch and Alex Tingle
function findPosX(obj){
    var curleft = 0;
    if(obj.offsetParent)
        while(1) 
        {
          curleft += obj.offsetLeft;
          if(!obj.offsetParent)
            break;
          obj = obj.offsetParent;
        }
    else if(obj.x)
        curleft += obj.x;
    return curleft;
  }

  function findPosY(obj){
    var curtop = 0;
    if(obj.offsetParent)
        while(1)
        {
          curtop += obj.offsetTop;
          if(!obj.offsetParent)
            break;
          obj = obj.offsetParent;
        }
    else if(obj.y)
        curtop += obj.y;
    return curtop;
  }

//================ getPageSize ===================
// Returns array with page width, height and window width, height
// Core code from - quirksmode.org, Edit for Firefox by pHaez

var pageHeight = 0;
var pageWidth  = 0;

function getPageSize(){
    var xScroll, yScroll, windowWidth, windowHeight;
    if (window.innerHeight && window.scrollMaxY) {    
        xScroll = document.body.scrollWidth;
        yScroll = window.innerHeight + window.scrollMaxY;
    } else if (document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac
        xScroll = document.body.scrollWidth;
        yScroll = document.body.scrollHeight;
    } else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
        xScroll = document.body.offsetWidth;
        yScroll = document.body.offsetHeight;
    }
    
    var windowWidth, windowHeight;
    if (self.innerHeight) {    // all except Explorer
        windowWidth = self.innerWidth;
        windowHeight = self.innerHeight;
    } else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
        windowWidth = document.documentElement.clientWidth;
        windowHeight = document.documentElement.clientHeight;
    } else if (document.body) { // other Explorers
        windowWidth = document.body.clientWidth;
        windowHeight = document.body.clientHeight;
    }    
    // for small pages with total height less then height of the viewport
    if(yScroll < windowHeight){
        pageHeight = windowHeight;
    } else { 
        pageHeight = yScroll;
    }

    // for small pages with total width less then width of the viewport
    if(xScroll < windowWidth){    
        pageWidth = windowWidth;
    } else {
        pageWidth = xScroll;
    }
    var arrayPageSize = new Array(pageWidth,pageHeight,windowWidth,windowHeight) 
    return arrayPageSize;
}

//================ OPACITY ROUTINES===================
//Opacity Routines Modified from JavaScript - http://www.brainerror.net/scripts_js_blendtrans.php

function fadeEffect(id, opacStart, opacEnd, millisec) {
    var speed = Math.round(millisec / 100);
    var timer = 0;
    //determine the direction for the blending, if start and end are the same nothing happens
    if(opacStart > opacEnd) {
        for(i = opacStart; i >= opacEnd; i--) {
            setTimeout("changeOpac(" + i + ",'" + id + "')",(timer * speed));
            timer++;
        }
    } else if(opacStart < opacEnd) {
        for(i = opacStart; i <= opacEnd; i++)    {
            setTimeout("changeOpac(" + i + ",'" + id + "')",(timer * speed));
            timer++;
        }
    }
}

function changeOpac(opacity, id) {
    var object = getId(id).style; 
    object.opacity = (opacity / 100);
    //object.MozOpacity = (opacity / 100);
    object.KhtmlOpacity = (opacity / 100);
    object.filter = "alpha(opacity=" + opacity + ")";
}

function toggleFade(id, millisec) {
  if(getId(id)){
    if(getId(id).style.opacity == 0) {fadeEffect(id, 0, 100, millisec);}
    else {fadeEffect(id, 100, 0, millisec);}
  }
}

function blendimage(divid, imageid, imagefile, millisec) {
    var speed = Math.round(millisec / 100);
    var timer = 0;
    getId(divid).style.backgroundImage = "url(" + getId(imageid).src + ")";
    changeOpac(0, imageid);
    getId(imageid).src = imagefile;
    for(i = 0; i <= 100; i++) {
        setTimeout("changeOpac(" + i + ",'" + imageid + "')",(timer * speed));
        timer++;
    }
}

function currentOpac(id, opacEnd, millisec) {
    var currentOpac = 100;
    if(document.getElementById(id).style.opacity < 100) {currentOpac = getId(id).style.opacity * 100;}
    fadeEffect(id, currentOpac, opacEnd, millisec);
}

//======= EFFECT HELPER ROUTINES =========

function checkEffectVisibility(id,state){
  var effectElem  = getId(id);
  if (!effectElem.Slide_SlideTimerCount) effectElem.Slide_SlideTimerCount = 0;
  if (((state) && (effectElem.Slide_SlideTimerCount == 0) && (effectElem.style.display  == '')) ||
     ((!state) && (effectElem.Slide_SlideTimerCount == 0) && (effectElem.style.display  == 'none'))){
       effectElem.run = false;
       return true;
     }
  return false;
}

function setCurrent(id,state){
  var effectElem  = getId(id);
  if (!effectElem) return false;  
  if (checkEffectVisibility(id,state)) return false;
  if (!effectElem.run) {
      effectElem.Start_Time = markTime();
      effectElem.Run_Time = 0;
      effectElem.run = true;
      effectElem.Slide_SlideTimer = 0;
      effectElem.Slide_SlideTimerCount = 0;
      if (arguments[2]) effectElem.Slide_SlideInc = arguments[2];
        else effectElem.Slide_SlideInc = std_Slide_SlideInc;
      if (arguments[3]) effectElem.Slide_SlideSpeed = arguments[3];
        else effectElem.Slide_SlideSpeed = std_Slide_SlideSpeed;
      effectElem.Total_Time = Math.round(effectElem.Slide_SlideSpeed * (100/effectElem.Slide_SlideInc));
      effectElem.fillerId     = '';
      effectElem.effectFiller = '';
      createFiller(id);
      effectElem.HoldPosition = effectElem.style.position;
      effectElem.style.position = 'absolute';
      return true;
  } else return false;
}

function processEffect(id){
  var effectElem  = getId(id);  
  effectElem.Run_Time = markTime() - effectElem.Start_Time;
  var t = Math.round(100*effectElem.Run_Time/effectElem.Total_Time);
  if ((effectElem.Slide_SlideTimerCount < 100) && ( t > 100)) effectElem.Slide_SlideTimerCount = 100;
    else effectElem.Slide_SlideTimerCount = t;
  
  if(effectElem.Slide_SlideTimerCount>100){
      clearTimeout(effectElem.Slide_SlideTimer);
      effectElem.Slide_SlideTimerCount=0;
      effectElem.style.position = effectElem.HoldPosition;
      if(getId(effectElem.fillerId)) {
        setTimeout("removeFiller('"+id+"')", 10);  //timeout prevents blinking
      }
      effectElem.run = false;
      return false;
  }
  return true;
}


function setFillerHeight(id,Height){
  var effectElem  = getId(id);
  var effectFillerElem = getId(effectElem.fillerId);
  if(effectFillerElem) effectFillerElem.style.height = Height+'px';
}


function createFiller(id){
  var effectElem  = getId(id);
  if (effectElem.noFiller) return;
  if (effectElem.style.position != 'absolute'){
    effectElem.fillerId = 'filler_'+ id;
    var checkdiv = getId(effectElem.fillerId);
    if (checkdiv) return;
    var effectFillerElem = document.createElement('div');
    effectFillerElem.setAttribute('id',effectElem.fillerId);
    insertAfter(effectElem.parentNode, effectFillerElem, effectElem);
    effectFillerElem.style.width = '1px';
  }
}

function removeFiller(id){
 var effectElem  = getId(id);
 var filler = getId(effectElem.fillerId);
 if (filler){
   effectElem.parentNode.removeChild(filler);
   effectElem.fillerId = '';
 }
}


//============ slideDown ============
function slideDown(id){
  if (setCurrent(id,true,arguments[1],arguments[2])) slideDownProcess(id);
}
function slideDownProcess(id){
  if(processEffect(id)){
      var effectElem  = getId(id);
      var Height = effectElem.offsetHeight*effectElem.Slide_SlideTimerCount/100;
      setFillerHeight(id,Height);
      effectElem.style.clip = 'rect(auto auto ' + Height + 'px auto)';
      showElem(effectElem);
      effectElem.Slide_SlideTimer = setTimeout("slideDownProcess('"+id+"')",effectElem.Slide_SlideSpeed);
  }
}

//============ slideUp ============
function slideUp(id){
  if (setCurrent(id,true,arguments[1],arguments[2])) slideUpProcess(id);
}
function slideUpProcess(id){  
  if(processEffect(id)){
     var effectElem  = getId(id);
     setFillerHeight(id,effectElem.offsetHeight);
	 var Height = effectElem.offsetHeight*(1-effectElem.Slide_SlideTimerCount/100);
     effectElem.style.clip = 'rect(' + Height + 'px auto auto auto)';
     showElem(effectElem);
     effectElem.Slide_SlideTimer = setTimeout("slideUpProcess('"+id+"')",effectElem.Slide_SlideSpeed);
  }
}


//============ slideRight ============
function slideRight(id){
  if (setCurrent(id,true,arguments[1],arguments[2])) slideRightProcess(id);
}
function slideRightProcess(id){
  if( processEffect(id)){
     var effectElem  = getId(id);
     setFillerHeight(id,effectElem.offsetHeight);
     var Width = effectElem.offsetWidth*effectElem.Slide_SlideTimerCount/100;
     //if(effectFillerElem) effectFillerElem.style.width = Width+'px';
     effectElem.style.clip = 'rect(auto ' + Width + 'px auto auto)';
     showElem(effectElem);
     effectElem.Slide_SlideTimer = setTimeout("slideRightProcess('"+id+"')",effectElem.Slide_SlideSpeed);
  }
}

//============ slideLeft ============
function slideLeft(id){
  if (setCurrent(id,true,arguments[1],arguments[2])) slideLeftProcess(id);
}
function slideLeftProcess(id){
  if( processEffect(id)){  
     var effectElem  = getId(id); 
     setFillerHeight(id,effectElem.offsetHeight);
     var Width = effectElem.offsetWidth*(1- effectElem.Slide_SlideTimerCount/100);
     //if(effectFillerElem) effectFillerElem.style.width = Width+'px';
     effectElem.style.clip = 'rect(auto auto auto '+ Width + 'px)';
     showElem(effectElem);
     effectElem.Slide_SlideTimer = setTimeout("slideLeftProcess('"+id+"')",effectElem.Slide_SlideSpeed);
  }
}

//============ slideUpDown ============
function slideUpDown(id){
   if (setCurrent(id,true,arguments[1],arguments[2])) slideUpDownProcess(id);
}
function slideUpDownProcess(id){
  if(processEffect(id)){
     var effectElem  = getId(id);
     setFillerHeight(id,effectElem.offsetHeight);
     var Height1 = effectElem.offsetHeight*(0.5 - effectElem.Slide_SlideTimerCount/200);
     var Height2 = effectElem.offsetHeight*(0.5 + effectElem.Slide_SlideTimerCount/200);
     effectElem.style.clip = 'rect('+ Height1 + 'px auto '+ Height2 + 'px auto)';
     showElem(effectElem);
     effectElem.Slide_SlideTimer = setTimeout("slideUpDownProcess('"+id+"')",effectElem.Slide_SlideSpeed);
  }
}


//============ slideLeftRight ============
function slideLeftRight(id){
  if (setCurrent(id,true,arguments[1],arguments[2])) slideLeftRightProcess(id);
}
function slideLeftRightProcess(id){
  if(processEffect(id)){
     var effectElem  = getId(id);
     setFillerHeight(id,effectElem.offsetHeight);
     var Width1 = effectElem.offsetWidth*(0.5 - effectElem.Slide_SlideTimerCount/200);
     var Width2 = effectElem.offsetWidth*(0.5 + effectElem.Slide_SlideTimerCount/200);
     effectElem.style.clip = 'rect(auto '+ Width2 + 'px auto '+ Width1 + 'px)';
     showElem(effectElem);
     effectElem.Slide_SlideTimer = setTimeout("slideLeftRightProcess('"+id+"')",effectElem.Slide_SlideSpeed);
  }
}


//============ slideCenter ============
function slideCenter(id){
if (setCurrent(id,true,arguments[1],arguments[2])) slideCenterProcess(id);
}
function slideCenterProcess(id){
  if(processEffect(id)){
     var effectElem  = getId(id);
     setFillerHeight(id,effectElem.offsetHeight);
     var Height1 = effectElem.offsetHeight*(0.5 - effectElem.Slide_SlideTimerCount/200);
     var Height2 = effectElem.offsetHeight*(0.5 + effectElem.Slide_SlideTimerCount/200);
     var Width1 = effectElem.offsetWidth*(0.5 - effectElem.Slide_SlideTimerCount/200);
     var Width2 = effectElem.offsetWidth*(0.5 + effectElem.Slide_SlideTimerCount/200);
     effectElem.style.clip = 'rect('+ Height1 + 'px '+ Width2 + 'px '+ Height2 + 'px '+ Width1 + 'px)';
     showElem(effectElem);
     effectElem.Slide_SlideTimer = setTimeout("slideCenterProcess('"+id+"')",effectElem.Slide_SlideSpeed);
  }
}



//============ closeUp ============
function closeUp(id){
  if (setCurrent(id,false,arguments[1],arguments[2])) closeUpProcess(id);
}
function closeUpProcess(id){
  var effectElem  = getId(id);
  if(processEffect(id) ){
      var Height = effectElem.offsetHeight*(1 - effectElem.Slide_SlideTimerCount/100);
      setFillerHeight(id,Height);
      effectElem.style.clip = 'rect(auto auto ' + Height + 'px auto)';
      showElem(effectElem);
      effectElem.Slide_SlideTimer = setTimeout("closeUpProcess('"+id+"')",effectElem.Slide_SlideSpeed);
  }
  else{
      hideElem(effectElem); 
      effectElem.style.clip = 'rect(auto auto auto auto)';
  }
}


//============ closeDown ============
function closeDown(id){
  if (setCurrent(id,false,arguments[1],arguments[2])) closeDownProcess(id);
}
function closeDownProcess(id){
  var effectElem  = getId(id);
  if(processEffect(id) ){
    setFillerHeight(id,effectElem.offsetHeight);
    var Height = effectElem.offsetHeight*(effectElem.Slide_SlideTimerCount/100);
    effectElem.style.clip = 'rect(' + Height + 'px auto auto auto)';
    showElem(effectElem);
    effectElem.Slide_SlideTimer = setTimeout("closeDownProcess('"+id+"')",effectElem.Slide_SlideSpeed);
  }
  else{
    hideElem(effectElem); 
    effectElem.style.clip = 'rect(auto auto auto auto)';
  }
}


//============ closeLeft ============
function closeLeft(id){
  if (setCurrent(id,false,arguments[1],arguments[2])) closeLeftProcess(id);
}
function closeLeftProcess(id){
  var effectElem  = getId(id);
  if(processEffect(id) ){
    setFillerHeight(id,effectElem.offsetHeight);
    var Width = effectElem.offsetWidth*(1- effectElem.Slide_SlideTimerCount/100);
    effectElem.style.clip = 'rect(auto '+ Width + 'px auto auto)';
    showElem(effectElem);
    Slide_SlideTimer = setTimeout("closeLeftProcess('"+id+"')",effectElem.Slide_SlideSpeed);
  }
  else{
    hideElem(effectElem); 
    effectElem.style.clip = 'rect(auto auto auto auto)';
  }
}

//============ closeRight ============
function closeRight(id){
  if (setCurrent(id,false,arguments[1],arguments[2])) closeRightProcess(id);
}
function closeRightProcess(id){
  var effectElem  = getId(id);
  if(processEffect(id)){ 
    setFillerHeight(id,effectElem.offsetHeight);  
    var Width = effectElem.offsetWidth*(effectElem.Slide_SlideTimerCount/100);
    effectElem.style.clip = 'rect(auto auto auto '+ Width + 'px)';
    showElem(effectElem);
    effectElem.Slide_SlideTimer = setTimeout("closeRightProcess('"+id+"')",effectElem.Slide_SlideSpeed);
  }
  else{
    hideElem(effectElem); 
    effectElem.style.clip = 'rect(auto auto auto auto)';
  }
}

//============ closeLeftRight ============
function closeLeftRight(id){
  if (setCurrent(id,false,arguments[1],arguments[2])) closeLeftRightProcess(id);
}
function closeLeftRightProcess(id){
  var effectElem  = getId(id);
  if(processEffect(id) ){
      setFillerHeight(id,effectElem.offsetHeight);
      var Width1 = effectElem.offsetWidth*(effectElem.Slide_SlideTimerCount/200);
      var Width2 = effectElem.offsetWidth*(1 - effectElem.Slide_SlideTimerCount/200);
      effectElem.style.clip = 'rect(auto '+ Width2 + 'px auto '+ Width1 + 'px)';
      showElem(effectElem);
      effectElem.Slide_SlideTimer = setTimeout("closeLeftRightProcess('"+id+"')",effectElem.Slide_SlideSpeed);
  }
  else{
      hideElem(effectElem); 
      effectElem.style.clip = 'rect(auto auto auto auto)';
  }
}

//============ closeUpDown ============
function closeUpDown(id){
  if (setCurrent(id,false,arguments[1],arguments[2])) closeUpDownProcess(id);
}
function closeUpDownProcess(id){
  var effectElem  = getId(id);
  if(processEffect(id) ){
      setFillerHeight(id,effectElem.offsetHeight);
      var Height1 = effectElem.offsetHeight*(1 - effectElem.Slide_SlideTimerCount/200);
      var Height2 = effectElem.offsetHeight*(effectElem.Slide_SlideTimerCount/200);
      effectElem.style.clip = 'rect('+ Height2 + 'px auto '+ Height1 + 'px auto)';
      showElem(effectElem);
      effectElem.Slide_SlideTimer = setTimeout("closeUpDownProcess('"+id+"')",effectElem.Slide_SlideSpeed);
  }
  else{
      hideElem(effectElem); 
      effectElem.style.clip = 'rect(auto auto auto auto)';
  }
}

//============ closeCenter ============
function closeCenter(id){
  if (setCurrent(id,false,arguments[1],arguments[2])) closeCenterProcess(id);
}
function closeCenterProcess(id){
  var effectElem  = getId(id);
  if(processEffect(id) ){
      setFillerHeight(id,effectElem.offsetHeight);
      var Width1 = effectElem.offsetWidth*(effectElem.Slide_SlideTimerCount/200);
      var Width2 = effectElem.offsetWidth*(1 - effectElem.Slide_SlideTimerCount/200);
      var Height1 = effectElem.offsetHeight*(effectElem.Slide_SlideTimerCount/200);
      var Height2 = effectElem.offsetHeight*(1 - effectElem.Slide_SlideTimerCount/200);
      effectElem.style.clip = 'rect('+ Height1 + 'px '+ Width2 + 'px '+ Height2 + 'px '+ Width1 + 'px)';
      showElem(effectElem);
      effectElem.Slide_SlideTimer = setTimeout("closeCenterProcess('"+id+"')",effectElem.Slide_SlideSpeed);
  }
  else{
      hideElem(effectElem); 
      effectElem.style.clip = 'rect(auto auto auto auto)';
  }
}


//============ fadeIn ============
function fadeIn(id){
  if (setCurrent(id,true,arguments[1],arguments[2])) fadeInProcess(id);
}
function fadeInProcess(id){
  var effectElem  = getId(id);
  if(processEffect(id)){
    setFillerHeight(id,effectElem.offsetHeight);
    var effectElem  = getId(id);
    changeOpac(effectElem.Slide_SlideTimerCount,id);
    showElem(effectElem);
    effectElem.Slide_SlideTimer = setTimeout("fadeInProcess('"+id+"')",effectElem.Slide_SlideSpeed);
  } 
}

//============ fadeOut ============
function fadeOut(id){
  if (setCurrent(id,false,arguments[1],arguments[2])) fadeOutProcess(id);
}
function fadeOutProcess(id){
  var effectElem  = getId(id);
  if(processEffect(id) ){
    setFillerHeight(id,effectElem.offsetHeight);
    changeOpac(100 - effectElem.Slide_SlideTimerCount, id)
    showElem(effectElem);
    effectElem.Slide_SlideTimer = setTimeout("fadeOutProcess('"+id+"')",effectElem.Slide_SlideSpeed);
  }
  else { hideElem(effectElem); changeOpac(100,id); }
}
