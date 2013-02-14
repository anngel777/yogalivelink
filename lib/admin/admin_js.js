// -------------- ADMIN JAVASCRIPT ROUTINES -------------

function setTab(num){
  hideGroupExcept('mainpage',num);
  setClassGroup('tablink',num,'tablink','tabselect');
}

function getkey(e){
  if (window.event) return window.event.keyCode;
  else if (e) return e.which;
  else return null;
}

function changeText(myItem,newText){
  document.getElementById(myItem).innerHTML=newText;
}


function updatePreview(){
  if(needEditor){ var outText=document.getElementById('CTEXT').value; }
  else {var outText=getContentModified('CTEXT');}
  document.getElementById('PREVIEW').innerHTML=outText;
}


function showInfo(){
  toggleDisplay('infobox');
}


//--------EDIT MENU ITEMS-------
function removeExtraSpace(mytext){
  mytext = mytext.replace( /( ){2,}/g," ");
  return mytext;
}

function Trim(sString){
  while (sString.substring(0,1) == ' ') sString = sString.substring(1, sString.length);
  while (sString.substring(sString.length-1, sString.length) == ' ') sString = sString.substring(0,sString.length-1);
  return sString;
}

function removeTrailingSpaces() {
   var input = document.getElementById('CTEXT');
   input.value = input.value.replace( /( )+\n/g,"\n");
}

function compressSpaces()
{
  var input = document.getElementById('CTEXT');
  if (input.setSelectionRange) {
    var selectionStart = input.selectionStart;
    var selectionEnd = input.selectionEnd;
    var myText = input.value.substring(selectionStart,selectionEnd);
    var replaceString = Trim(myText.replace( /( ){2,}/g," "));
    replaceString = replaceString.replace( /\n /g,"\n");
    input.value = input.value.substring(0, selectionStart)+ replaceString+ input.value.substring(selectionEnd);
    input.setSelectionRange(selectionStart,selectionStart + replaceString.length);
    showId('contentmodifed');
  }
}

function createList(list)
{
    compressSpaces();
    replaceWithinSelection('^CR^CR','^CR');
    replaceWithinSelection('^CR','[/li]^CR[li]');
    if (list == 'UL') tagSurround('[ul]^CR[li]','[/li]^CR[/ul]','CTEXT');
    else if(list == 'OL') tagSurround('[ol]^CR[li]','[/li]^CR[/ol]','CTEXT');
    else tagSurround('[li]','[/li]','CTEXT');
}

function createParagraphs()
{
    compressSpaces();
    replaceWithinSelection('^CR^CR','^CR');
    replaceWithinSelection('^CR','[/p]^CR^CR[p]');
    tagSurround('[p]','[/p]','CTEXT');
}

function clearBlock(){
  var input = document.getElementById('CTEXT');
 if (input.setSelectionRange) {
    var selectionStart = input.selectionStart;
    var selectionEnd   = input.selectionEnd;
    var replaceString  = input.value.substring(selectionStart,selectionEnd);
    replaceString = replaceString.replace(/<\/?(h[1-6]|p|div)[^>]*(>|$)/ig,'');
    input.value = input.value.substring(0, selectionStart)+ replaceString+ input.value.substring(selectionEnd);
    input.setSelectionRange(selectionStart,selectionStart + replaceString.length);
    showId('contentmodifed');
  }
}

function stripTags(tag){
  var input = document.getElementById('CTEXT');
  if (input.setSelectionRange) {
    var selectionStart = input.selectionStart;
    var selectionEnd   = input.selectionEnd;
    var replaceString  = input.value.substring(selectionStart,selectionEnd);
    if (tag == 'span') {
        replaceString = replaceString.replace(/<\/?span[^>]*(>|$)/g, '');
    } else {
        replaceString = replaceString.replace(/<\/?[^>]+(>|$)/g, '');
    }
    input.value = input.value.substring(0, selectionStart)+ replaceString+ input.value.substring(selectionEnd);
    input.setSelectionRange(selectionStart,selectionStart + replaceString.length);
    showId('contentmodifed');
  }
}

function replaceContentText(oldtext, newtext)
{
   var input = document.getElementById('CTEXT');
   input.value = input.value.replace( new RegExp( oldtext, 'g' ), newtext );
   showId('contentmodifed');
}

function replaceText()
{
    var findText = document.getElementById('find').value;
    var replaceText = document.getElementById('replace').value;

    findText = findText.replace(/@/g,'"');
    findText = findText.replace(/\[/g,'<');
    findText = findText.replace(/\]/g,'>');
    findText = findText.replace(/\^CR/g,"\n");
    findText = findText.replace(/\^T/g,"\t");
    findText = findText.replace(/_/g,' ');

    replaceText = replaceText.replace(/@/g,'"');
    replaceText = replaceText.replace(/\[/g,'<');
    replaceText = replaceText.replace(/\]/g,'>');
    replaceText = replaceText.replace(/\^CR/g,"\n");
    replaceText = replaceText.replace(/\^T/g,"\t");
    replaceText = replaceText.replace(/_/g,' ');

    replaceContentText(findText, replaceText);
}

function cleanWord()
{
    var input = document.getElementById('CTEXT');
    var swapCodes   = new Array(8211, 8212, 8216, 8217, 8220, 8221, 8226, 8230); // dec codes from char at
    var swapStrings = new Array('&ndash;', "&mdash;", "&lsquo;",  "&rsquo;",  '&ldquo;',  '&rdquo;',  "&bull;",  "&hellip;");

    for (i = 0; i < swapCodes.length; i++) {
        input.value = input.value.replace(new RegExp(String.fromCharCode(swapCodes[i]), 'g'), swapStrings[i]);
    }
    showId('contentmodifed');
}

function replaceWithinSelection(findText,replaceText){
  var input = document.getElementById('CTEXT');
  findText = findText.replace(/@/g,'"');
  findText = findText.replace(/\[/g,'<');
  findText = findText.replace(/\]/g,'>');
  findText = findText.replace(/\^CR/g,"\n");
  findText = findText.replace(/\^T/g,"\t");
  findText = findText.replace(/_/g,' ');

  replaceText = replaceText.replace(/@/g,'"');
  replaceText = replaceText.replace(/\[/g,'<');
  replaceText = replaceText.replace(/\]/g,'>');
  replaceText = replaceText.replace(/\^CR/g,"\n");
  replaceText = replaceText.replace(/\^T/g,"\t");
  replaceText = replaceText.replace(/_/g,' ');

  if (input.setSelectionRange) {
    var selectionStart = input.selectionStart;
    var selectionEnd = input.selectionEnd;
    var myText = input.value.substring(selectionStart,selectionEnd);
    var replaceString = myText.replace( new RegExp( findText, "g" ), replaceText );
    input.value = input.value.substring(0, selectionStart)+ replaceString+ input.value.substring(selectionEnd);
    input.setSelectionRange(selectionStart,selectionStart + replaceString.length);
  }
}


String.prototype.titleCase = function () {
    var str = "";
    var words = this.toLowerCase().split(' ');
    for (i in words) {
        str += ' ' + words[i].substr(0,1).toUpperCase()+words[i].substr(1);
    }
    return str.substr(1);
}




function changeCase(dir)
{
  var input = document.getElementById('CTEXT');
  if (input.setSelectionRange) {
    var selectionStart = input.selectionStart;
    var selectionEnd = input.selectionEnd;
    var myText = input.value.substring(selectionStart,selectionEnd);
    if (dir == 'U') {
        var replaceString = myText.toUpperCase();
    } else if (dir == 'L') {
        var replaceString = myText.toLowerCase();
    } else if (dir == 'T') {
        var replaceString = myText.titleCase();
    } else if (dir == 'V') {
        var replaceString = myText.toLowerCase().replace(/[^a-zA-Z0-9]/g,'_').replace(/_+/g,'_');
    }
    input.value = input.value.substring(0, selectionStart)+ replaceString+ input.value.substring(selectionEnd);
  }
}





function tagSurround(tag1,tag2,inputID){
var input = document.getElementById(inputID);

  tag1 = tag1.replace(/@/g,'"');
  tag1 = tag1.replace(/\[/g,'<');
  tag1 = tag1.replace(/\]/g,'>');
  tag1 = tag1.replace(/\^CR/g,"\n");
  tag1 = tag1.replace(/_/g,' ');
  tag1 = tag1.replace(/~/g,'_');


  tag2 = tag2.replace(/@/g,'"');
  tag2 = tag2.replace(/\[/g,'<');
  tag2 = tag2.replace(/\]/g,'>');
  tag2 = tag2.replace(/\^CR/g,"\n");
  tag2 = tag2.replace(/_/g,' ');
  tag2 = tag2.replace(/~/g,'_');


  if (input.setSelectionRange) {
    var selectionStart = input.selectionStart;
    var selectionEnd = input.selectionEnd;

    var myText = input.value.substring(selectionStart,selectionEnd);
    if (tag2=='') myText ='';
    var replaceString = tag1 + myText + tag2;
    input.value = input.value.substring(0, selectionStart)+ replaceString+ input.value.substring(selectionEnd);
    input.setSelectionRange(selectionStart + replaceString.length,selectionStart + replaceString.length);
    showId('contentmodifed');
  }
  else if (document.selection) {
    var range = document.selection.createRange();
    if (range.parentElement() == input) {
      var isCollapsed = range.text == '';

      var myText = range.text;
      if (tag2=='') myText ='';
      var replaceString = tag1 + myText + tag2;
      range.text = replaceString;
      if (!isCollapsed)  {
        range.moveStart('character', -replaceString.length);
        range.select();
      }
    }
  }
}

var needEditor = true;
var HTMLedit   = false;

function getContentModified(editor_id) {
    if (typeof(editor_id) != "undefined")
        tinyMCE.selectedInstance = tinyMCE.getInstanceById(editor_id);
    if (tinyMCE.selectedInstance) {
        var html = tinyMCE._cleanupHTML(tinyMCE.selectedInstance, tinyMCE.selectedInstance.getDoc(), tinyMCE.settings, tinyMCE.selectedInstance.getBody(), false, true);
        return html;
    }
    return null;
}


function SetEditor()
{
    if (!tinyMCE.getInstanceById('CTEXT')) {
        tinyMCE.execCommand('mceAddControl', false, 'CTEXT');
        tinyMCEmode = true;
        hideId('editmenu');
        HTMLedit = true;
        changeText('HTMLcontentButton','Edit&nbsp;Text');
    } else {
        HTMLedit = !HTMLedit;
        tinyMCE.execCommand('mceRemoveControl', false, 'CTEXT');
        if(HTMLedit){
            changeText('HTMLcontentButton','Edit&nbsp;Text');
            hideId('editmenu');
        } else {
            changeText('HTMLcontentButton','Edit&nbsp;Content&nbsp;(HTML)');
            showId('editmenu');
        }
    }
}

function setAutoTextAreaHeight(id){
  var myelem = getId(id);
  if(myelem){
    if (myelem.scrollHeight > myelem.offsetHeight) myelem.style.height = myelem.scrollHeight + 50 + 'px';
  }
}

//------------------------drag item--------------------------
//  http://www.webtoolkit.info/

var doDrag = false;

var DragHandler = {
    // private property.
    _oElem : null,


    // public method. Attach drag handler to an element.
    attach : function(oElem) {
        oElem.onmousedown = DragHandler._dragBegin;

        // callbacks
        oElem.dragBegin = new Function();
        oElem.drag = new Function();
        oElem.dragEnd = new Function();

        return oElem;
    },


    // private method. Begin drag process.
    _dragBegin : function(e) {
        if (doDrag == false) return;
        var oElem = DragHandler._oElem = this;

        if (isNaN(parseInt(oElem.style.left))) { oElem.style.left = '0px'; }
        if (isNaN(parseInt(oElem.style.top))) { oElem.style.top = '0px'; }

        var x = parseInt(oElem.style.left);
        var y = parseInt(oElem.style.top);

        e = e ? e : window.event;
        oElem.mouseX = e.clientX;
        oElem.mouseY = e.clientY;

        oElem.dragBegin(oElem, x, y);

        document.onmousemove = DragHandler._drag;
        document.onmouseup = DragHandler._dragEnd;
        return;
    },


    // private method. Drag (move) element.
    _drag : function(e) {
        if (doDrag == false) return;
        var oElem = DragHandler._oElem;

        var x = parseInt(oElem.style.left);
        var y = parseInt(oElem.style.top);

        e = e ? e : window.event;
        oElem.style.left = x + (e.clientX - oElem.mouseX) + 'px';
        oElem.style.top = y + (e.clientY - oElem.mouseY) + 'px';

        oElem.mouseX = e.clientX;
        oElem.mouseY = e.clientY;

        oElem.drag(oElem, x, y);

        return;
    },


    // private method. Stop drag process.
    _dragEnd : function() {
        var oElem = DragHandler._oElem;

        var x = parseInt(oElem.style.left);
        var y = parseInt(oElem.style.top);

        oElem.dragEnd(oElem, x, y);

        document.onmousemove = null;
        document.onmouseup = null;
        DragHandler._oElem = null;
    }

};

function adminOnload(){
    if(getId('TTEXT')) setAutoTextAreaHeight('TTEXT');
    if(getId('CTEXT')) {
        setAutoTextAreaHeight('CTEXT');
        var drag1 = DragHandler.attach(getId('LINKS_PAGES'));
        var drag2 = DragHandler.attach(getId('LINKS_IMAGES'));
        var drag3 = DragHandler.attach(getId('FandR'));
    }
    var viewPage = getId('ViewPage');
    var pageSizeArray = getPageSize();  //pageWidth,pageHeight,windowWidth,windowHeight
    if (viewPage) {
        var viewPageTop = findPosY(viewPage);
        var newHeight = pageSizeArray[3] - viewPageTop - 20;
        viewPage.style.height = newHeight + 'px';
    }
    setTab(1);
}
