function getId(id){return document.getElementById(id);}
function toggleDisplay(myItem){if(getId(myItem)) {var style2 = getId(myItem).style;  style2.display = style2.display? '':'none';}}
function showId(id) {if(getId(id)) getId(id).style.display =''; }
function hideId(id) {if(getId(id)) getId(id).style.display ='none';}
function changeText(myItem,newText){getId(myItem).innerHTML=newText;}
function hideGroup(group){
  var i=1;
  while (getId(group+i)){ getId(group+i).style.display='none';  i++; }
}
function hideGroupExcept(group,except){
  hideGroup(group);
  if (getId(group+except)){getId(group+except).style.display='';}
}



window.onload = function () {
    var flash = getId('flash');
    if (flash) {
        setTimeout("$('#flash').fadeOut('slow')",4000);
    }
}




// ------------ TABS --------------

function setClassGroup(group,except,c1,c2){
  var i=1;
  while (getId(group+i)){
    getId(group+i).className = c1;
    i++;
  }
  if (getId(group+except)){getId(group+except).className = c2;}
}


function hideGroupExcept(group,except){
  hideGroup(group);
  if (getId(group+except)){getId(group+except).style.display='';}
}

function setTab(num, group, tablink, tabselect)
{
  if (group == undefined) { var group = 'tab'; }
  var linkname = group + 'link';

  if (tablink ==  undefined) { var tablink = 'tablink'; } //CLASS OF LINK
  if (tabselect ==  undefined) { var tabselect = 'tabselect'; } //CLASS OF SELECT

  hideGroupExcept(group, num);
  setClassGroup(linkname, num, tablink, tabselect);
}




