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


var editProfileFlag = true;
function xxeditMyProfile(eq)
{
    if (editProfileFlag) {
        $('.contact_heading').append('<h3>To update your profile, contact Customer at support@goeventreg.com</h3>');
    }
    editProfileFlag = false;
}

function editMyProfile(eq)
{
    $('body').append('<div id="overlay"><\/div>');
    $('#overlay').height($('body').height() + 20);

    $('#EDIT_CONTACT_PROFILE').load('/home/edit_contact_profile_ajax', '', function() {  
       $(this).slideDown();
      });    
}

function closeContactEdit()
{
    $('#EDIT_CONTACT_PROFILE').slideUp('normal', function() {
        $('#overlay').fadeOut('normal', function(){
            $('#overlay').remove();}
        ); 
    });
    

}

function processContactUpdate()
{
    $.post(
        '/home/edit_contact_profile_ajax;AJAX=1;POST=1',
        {data : $('#EDIT_CONTACT_PROFILE input, #EDIT_CONTACT_PROFILE select, #EDIT_CONTACT_PROFILE textarea').serialize()},
        function(data) {
            if (data == 'ok') {
                $('#contact_record tbody').load('/home/edit_contact_profile_ajax?update_profile=1', '', function (){
                    closeContactEdit();
                });
            } else {
                $('#EDIT_CONTACT_PROFILE').html(data);
            }
        } ); 
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
