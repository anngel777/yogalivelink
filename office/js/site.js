/* ------- SITE JavaScript --------- */


var classExecuteLinkAjax = '/office/AJAX/class_execute';
var classExecuteLink = '/office/class_execute';


function getClassExecuteLink(eq)
{
    return classExecuteLinkAjax + '?eq=' + eq;
}

function getClassExecuteLinkNoAjax(eq)
{
    return classExecuteLink + '?eq=' + eq;
}

function overlayContent(content, link)
{
    var content = '<div id="page_overlay"><div id="page_overlay_form"><a title="Close" id="page_overlay_close" href="#" onclick="return closePageOverlay();">X<\/a><div id="page_overlay_content"><img src="/wo/images/upload.gif" border="0" width="32" height="32" alt="Loading..." /><\/div><\/div>';
    $('body').prepend(content);
    //$('#page_overlay').height($('body').height() + 30);
    $('#page_overlay').height($(window).height() + 30);
    $('#page_overlay_form').css('margin-top', $('body').scrollTop()+10);
    
    $('#page_overlay_content').load(link, function () {
       //$('#ordercontent').slideDown();
    });
    
    return false;
}

function closePageOverlay()
{
   $('#page_overlay_content').slideUp('normal', function() {
       $('#page_overlay_form').fadeOut('normal', function() {
           $('#page_overlay').remove();
       });
   });
   return false;
}
            
            
document.onclick = function(e){
    e = e || window.event;
    if(typeof(dialogNumber) == 'undefined') return;
    if (dialogNumber == '') return;
    var t = e.target || e.srcElement;
    if(t.nodeName.toLowerCase() != 'a') {
        top.parent.appformActivate('appform' + dialogNumber);
    }
}

$(function(){
    if ($('.date_entry').size() > 0) {
        $('.date_entry').datepicker({ 
            altFormat: 'yy-mm-dd',         
            createButton: false,
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd',
            yearRange: '+0:+5'
        }).each(function(){
            if (this.value == '0000-00-00') this.value = '';
        }).keydown(function(event) {
            event.keyCode = null;
            return false;
        }    
        );
    }
});

function addslashes(str) {
    str=str.replace(/\\/g,'\\\\');
    str=str.replace(/\'/g,'\\\'');
    str=str.replace(/\"/g,'\\"');
    str=str.replace(/\0/g,'\\0');
    return str;
}

function stripslashes(str) {
    str=str.replace(/\\'/g,'\'');
    str=str.replace(/\\"/g,'"');
    str=str.replace(/\\0/g,'\0');
    str=str.replace(/\\\\/g,'\\');
    return str;
}