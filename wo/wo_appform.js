/*
    Web Office Main Page Javascript
    by Michael V. Petrovich 2009
*/

var appformZindex = 1000;
var appformIdCounter = 0;
var appformProcessing = false;
var taskbarId = 'appform_taskbar';
var drag = new Array;

//var haveTaskBar = false;

$(function(){
    taskBarElem = document.getElementById(taskbarId);
});



function appformCreateWithContent(title, content, containerId, width, height)
{
    if (appformProcessing) return;
    appformProcessing = true;
    appformIdCounter++;
    appformZindex++;
    var id = 'appform' + appformIdCounter;

    var controls = '<div class="appform_controls">' +
     '<a href="#" title="Minimize" onclick="appformMinimize(\'' + id + '\',\''+ title + '\'); return false;">_</a>' +
     '<a href="#" title="Close" onclick="appformClose(\'' + id + '\'); return false;">X</a></div>';
    var titlebar = '<div id="titlebar_' + id + '" class="appform_titlebar"><h1>' + title + '</h1></div>';
    content = '<div class="appform" id="' + id + '">' + controls + titlebar +
            '<div class="appformcontent"><div style="overflow:scroll; width:' + width + 'px; height:' + height + 'px;">' +
            content +'</div></div></div>';
    $('#'+ containerId).prepend(content);

    $('#' + id).draggable({
        handle : '#titlebar_'+id,
        stop : function () {
            if ( !$.browser.msie ) {
                var dpos = $(this).position();
                if ($(this).position().top < 0) {
                    $(this).css('top', 0);
                }
            }
        }
    });

    //$('#' + id).resizable({handles : 'all'});

    $('#' + id).css('z-index',appformZindex);
    $('#' + id).width(width);
    $('#' + id).height(height);
    $('#' + id).click(function () {appformActivate(this.id);});
    $('#' + id).attr('maximized', false);
    appformAddToTaskbar(id, title);
    appformProcessing = false;
}



function appformCreate(title, file, containerId) {
    if (appformProcessing) return;
    appformProcessing = true;
    appformIdCounter++;
    appformZindex++;
    var id = 'appform' + appformIdCounter;
    var iframeId = 'appformIframe' + appformIdCounter;

    var dialogFile =  file + ';DIALOGID=' + appformIdCounter;

    var minimizeLink = (taskBarElem)? '<a href="#" title="Minimize" onclick="appformMinimize(\'' + id + '\',\''+ title + '\'); return false;">_</a>' : '';
    var externalLink = (file == 'admin')? '' : '<a href="' + file + '" target="_blank" title="New Window">&#9674;</a>';

    var controls = '<div class="appform_controls">' +
     minimizeLink +
     '<a href="#" title="Maximize" onclick="appformMaximize(\'' + id + '\'); return false;">^</a>' +
     externalLink +
     '<a href="#" title="Close" onclick="appformClose(\'' + id + '\'); return false;">X</a></div>';
    var titlebar = '<div id="titlebar_' + id + '" class="appform_titlebar"><h1>' + title + '</h1></div>';
    var content = '<div class="appform" id="' + id + '">' + controls + titlebar + '<iframe id="'+ iframeId +'" class="appiframe" src="' + dialogFile +'"></iframe></div>';

    $('#'+ containerId).prepend(content);
    $('#' + id).attr('maximized', '0');
    
    $('#titlebar_' + id).dblclick(function () {appformMaximize(id)});
    
    $('#' + id).draggable({
        handle : '#titlebar_'+id,
        start : function () {
            $('#' + iframeId).hide();
            $(this).css('opacity', 0.8);
        },
        stop : function () {
            $('#' + iframeId).show();
            $(this).css('opacity', 1);
            if ( !$.browser.msie ) {
                var dpos = $(this).position();
                if ($(this).position().top < 0) {
                    $(this).css('top', 0);
                }
            }
        }
    });

    //$('#' + id).resizable({handles : 'all'});
    $('#' + id).css('z-index',appformZindex);
    $('#' + id).mousedown(function () {appformActivate(this.id);});
    appformAddToTaskbar(id, title);
    appformProcessing = false;
    window.scrollTo(0,0);
}

function appformActivate(id)
{
    appformRestoreIcons();
    if ( !$('#' + id) ) {
       alert ('element ' + id + ' not found');
       return;
    }
    if ($('#' + id).css('z-index') < appformZindex) {
        appformZindex++;
        $('#' + id).css('z-index', appformZindex);
    }
}

function appformClose(id)
{
    $('#'+id).fadeOut('normal', function () {
        $(this).draggable('destroy');
        setTimeout("$('#" + id + "').empty()",100);  // delay prevents hanging in Firefox
        $('#taskbar_'+ id).remove();
    });
}


function tempShowDialog(id, state)
{
return;
    if (state == true) {
        $('#' + id).attr('holdz', $('#' + id).css('z-index'));
        $('#' + id).attr('holdvis', $('#' + id).css('display'));
        $('#' + id).css('z-index', 1999999999);
        $('#' + id).css('display', '');
    } else {
        $('#' + id).css('z-index', $('#' + id).attr('holdz'));
        $('#' + id).css('display', $('#' + id).attr('holdvis'));
    }
}

function appformAddToTaskbar(id, title)
{
    appformRestoreIcons();
    var content = '<a class="taskbar_visible" id="taskbar_'+ id +'" href="#"' +
             'title="' + title + '"' +
             ' onclick="appformActivate(\'' + id + '\'); return false;"' +
             ' onmouseover="tempShowDialog(\''+id+'\', true); return false;"' +
             ' onmouseout="tempShowDialog(\''+id+'\', false); return false;"' +
             '>' + title +'</a>';

    $('#' + taskbarId).append(content).fadeIn();
}


function appformMinimize(id, title)
{
    $('#'+id).slideUp('normal', function () {
        $('#taskbar_'+ id).attr('class', 'taskbar_minimized');
        $('#taskbar_'+ id).click( function() {appformRestore(id); return false;} );
    });
}


function appformRestore(id, title)
{
    appformActivate(id);
    $('#'+id).slideDown('normal', function () {
        $('#taskbar_'+ id).attr('class', 'taskbar_visible');
    });
}

function getScreenHight()
{
    return $(window).height();
}

function getScreenWidth()
{
    return $(window).width();
}

function appformMaximize(id)
{
    var screenWidth  = $(window).width()-20;
    var screenHeight = $(window).height()-25;

    if (taskBarElem) {
        screenHeight = screenHeight - $('#' + taskbarId).height();
    }

    var appformIframeId = 'appformIframe'+ id.substr(7,99);

    var currentWidth = $('#' + appformIframeId).width();
    if ($('#'+id).attr('maximized') == '1') {
        $('#'+id).attr('maximized', '0');
        document.getElementById(appformIframeId).contentWindow.ResizeIframe();
    } else {
        $('#'+id).attr('maximized', '1');
        var leftpos = $('#'+id).parent().position().left;
        $('#'+id).css('top', 0).css('left', -leftpos).width(screenWidth).height(screenHeight);
        $('#' + appformIframeId).css('width', screenWidth);
        $('#' + appformIframeId).css('height', screenHeight-40);
    }
}


function appformShowIcons()
{
    $('#menu_icons').toggleClass('menu_icons_on_top');
}

function appformRestoreIcons()
{
    $('#menu_icons').removeClass('menu_icons_on_top');
}

function removeFlash()
{
    $('#flash').fadeOut('slow', function(){
        $(this).remove();
    });
}

function setTopFlash(content)
{
    content = '<div id="flash">' + content + '<\/div>';
    $('body', top.document).append(content);
    setTimeout('removeFlash()', 3000);
}

