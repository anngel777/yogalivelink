// appform dialog Javascript
// must set var dialogNumber = '@@DIALOGID@@'

var haveDialogTemplate = 1;

var defaultAppId = 'apps';
// if (!document.getElementById('apps')) {
    // if (top.location == self.location) {
        // defaultAppId='dialogcontent';
    // }
// }

function ResizeIframe() {

    if (dialogNumber == '') return;

    var dialogId = 'appform' + dialogNumber;
    var iframeId = 'appformIframe' + dialogNumber;

    if (!parent.document.getElementById(iframeId)) return;
    
    var widthOffset        = ( $.browser.msie )? 20 : 20;
    var heightOffset       = ( $.browser.msie )? 11 : 10;
    var dialogHeightOffset = ( $.browser.msie )? 22 : 22;
  
    var dtop      = $('#' + dialogId, top.document).position().top;
    var dleft     = $('#' + dialogId, top.document).position().left;

    var winHeight =  top.parent.getScreenHight();
    var winWidth  =  top.parent.getScreenWidth();
    var titlebarHeight = $('#titlebar_' + dialogId, top.document).height();
    var taskbarHeight  = 0;
    if (top.document.getElementById('appform_taskbar')) {
        taskbarHeight  = $('#appform_taskbar', top.document).height(); 
    }        
    var maxHeight      = winHeight - dtop - heightOffset - titlebarHeight - taskbarHeight - 20;
    var maxWidth       = winWidth - dleft - 20;
    
    // -------- set height ---------
    var contentHeight = $('#dialogcontainer').height() + heightOffset;

    var newHeightDialog = Math.min(contentHeight + dialogHeightOffset, maxHeight + dialogHeightOffset + 5);
    var newHeightIframe = Math.min(contentHeight, maxHeight);    
   
   
    $('#' + dialogId, top.document).height(newHeightDialog);
    $('#' + iframeId, top.document).height(newHeightIframe);
   
    // -------- set width ---------    
    
    
    var contentWidth  = $('#dialogcontainer').width() + widthOffset;
    var dialogWidth   = Math.min(contentWidth, maxWidth)  + 20;
    //dialogWidth = $(document).width();
    $('#' + dialogId, top.document).width(dialogWidth);
    $('#' + iframeId, top.document).width(dialogWidth);
    //var diff = $('#' + iframeId, top.document).width() - $('#dialogcontainer').width();
    //alert(diff);
    //if (diff > 10) $('#dialogcontainer').width($('#' + iframeId, top.document).width() -10);
    //alert($('#dialogcontainer').width()  + ', '+ $(document).width() + ', '+ dialogWidth);
    //$(document).width($('#dialogcontainer').width()+10);


}


window.onload = function () {
    var flash = getId('flash');   
    if (flash) {
        var dialogcontainer = document.getElementById('dialogcontainer');
        dialogcontainer.style.width  = '600px';
        dialogcontainer.style.height = '200px';
        setTimeout("$('#flash').fadeOut('slow')",4000);
    }
    
    ResizeIframe();    
    //setTimeout('ResizeIframe()', 1000);   
    top.window.scrollTo(0,0);
}