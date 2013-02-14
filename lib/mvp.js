//Michael V. Petrovich - Javascript Functions  REV: 200601207

function getId(id){return document.getElementById(id);}

function toggleDisplay(myItem){if(getId(myItem)) {var style2 = getId(myItem).style;  style2.display = style2.display? '':'none';}}
function showId(id) {if(getId(id)) getId(id).style.display =''; }
function hideId(id) {if(getId(id)) getId(id).style.display ='none';}
function showElem(myElem) {if(myElem) myElem.style.display = ''; }
function hideElem(myElem) {if(myElem) myElem.style.display = 'none';}
function insertAfter(parent, node, referenceNode) {parent.insertBefore(node, referenceNode.nextSibling);}


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


function getElementsByClass(searchClass,node,tag) {
    var classElements = new Array();
    if ( node == null )
        node = document;
    if ( tag == null )
        tag = '*';
    var els = node.getElementsByTagName(tag);
    var elsLen = els.length;
    var pattern = new RegExp('(^|\\s)'+searchClass+'(\\s|$)');
    var i = 0;
    var j = 0;
    for (i = 0; i < elsLen; i++) {
        if ( pattern.test(els[i].className) ) {
            classElements[j] = els[i];
            j++;
        }
    }
    return classElements;
}


function leftTrim(sString){
   while (sString.substring(0,1) == ' ') sString = sString.substring(1, sString.length);
   return sString;
}

function rightTrim(sString){
   while (sString.substring(sString.length-1, sString.length) == ' ') sString = sString.substring(0,sString.length-1);
   return sString;
}

function Trim(sString){
  while (sString.substring(0,1) == ' ') sString = sString.substring(1, sString.length);
  while (sString.substring(sString.length-1, sString.length) == ' ') sString = sString.substring(0,sString.length-1);
  return sString;
}


