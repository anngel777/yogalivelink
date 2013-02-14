/* ========= Javascript ========= */

/*
    Admin Javascript
    by Michael V. Petrovich SEP 2010
*/

// =========================  SETUP =============================

// must set in header : var dialogNumber = '@@DIALOGID@@'

// var dialogNumber = '';
// if (window.frameElement) {
    // dialogNumber = window.frameElement.id.replace('appformIframe', '');
// }

var tableAjaxHelperFile = '/wo/wo_table_ajax_helper.php';
var appformZindex = 1000;
var appformIdCounter = 0;
var appformProcessing = false;
var taskbarId = 'appform_taskbar';
var haveDialogTemplate = 1;
var defaultAppId = 'apps';
var SV = ';';

$(function(){
  taskBarElem = document.getElementById(taskbarId);

    $('.sectionbutton').each(function() {
        var id = $(this).attr('id');
        $('#div_' + id).hide();
        $(this).append('<span class="updown">&nbsp;<\/span>');
        $(this).click(function (){
            $('#div_' + id).slideToggle('normal', function (){
                $('#' + id + ' span').toggleClass('updown_down');
            });
            return false;
        });
    });

    $('.dragme').draggable({
        handle : '.dragbar'
    });
});

document.onclick = function(e){
    e = e || window.event;
    var t = e.target || e.srcElement;
    if (t.id == 'sitetitle' || t.id == 'appform_taskbar' || t.id == 'admin_menu') {
        adminMenuClick();
    }
    if(typeof(dialogNumber) == 'undefined') return;
    if (dialogNumber == '') return;
    if(t.nodeName.toLowerCase() != 'a') {
        if (t.nodeName.parentNode && t.nodeName.parentNode.toLowerCase() == 'a') {
            return;
        }
        top.parent.appformActivate('appform' + dialogNumber);
    }
}

function mainOnload()
{
    var flash = getId('flash');
    if (flash) {
        var dialogcontainer = document.getElementById('dialogcontainer');
        dialogcontainer.style.width  = '600px';
        dialogcontainer.style.height = '200px';
        setTimeout("$('#flash').fadeOut('slow')",4000);
    }

    ResizeIframe();
    top.window.scrollTo(0,0);
}

window.onload = function () {
    mainOnload();
}

function adminMenuClick()
{
    if (top.parent.getId('sitetitle').style.zIndex == 0) {
        adminMenuOn();
    } else {
        adminMenuOff();
    }
}

function setAdminMenuZindex(z)
{
    //top.parent.getId('sitetitle').style.zIndex = z;
    //top.parent.getId('admin_menu').style.zIndex = z;
    $('#sitetitle', top.document).css({zIndex : z});
    $('#admin_menu', top.document).css({zIndex : z});

}

function adminMenuOn()
{
    setAdminMenuZindex(1999999999);
}

function adminMenuOff()
{
    setAdminMenuZindex(0);
}


function menuFileFilter()
{
    var filter = $('#menufilefilter').val();
    filter = Trim(filter.toLowerCase());
    if (filter == '') {
        $('#filelist a').show();
    } else {
        var check = false;
        var file = '';

        $('#filelist a').each(function(){
            file = $(this).html();
            check = file.toLowerCase().indexOf(filter);
            if (check > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }
}

// ========================= Hold Session Open =======================

var holdSessionInterval = 300000;  // 5-minutes
var haveHoldSession     = false;
function holdSessionOpenAction()
{
    $.get('HOLDSESSION');
}

function holdSessionOpen()
{
    if (haveHoldSession == false) {
        setInterval('holdSessionOpenAction()', holdSessionInterval);
        var content = '<span id="SESSION_HOLD">SESSION HOLD</span>';
        $('#' + taskbarId).prepend(content);
    }
    setTopFlash('Session now held open until window closed or reset');
    haveHoldSession = true;
    return false;
}

// ========================= Update DbQuery =======================

var dbQuerySetValue = 'ON';
function setDbQuery() {
    var lastValue = dbQuerySetValue;
    dbQuerySetValue = (dbQuerySetValue=='ON')? 'OFF' : 'ON';
    $.get('index;QUERY=' + lastValue, '', function() {
          $('#DB_QUERY_BUTTON').empty().append('Set Query ' + dbQuerySetValue);
    });

}


// ========================= Update File List =======================

function updateFileList()
{
    $('#filelist', top.document).load('AJAX/index?FL=1', '', function(){
        top.parent.menuFileFilter();
    });
}

// =========================  EDIT FILE =============================

function editFile(filename, fileOptions)
{
    var options = (fileOptions != '')? ';OPT=' + fileOptions : '';
    var file = (fileOptions == '')? filename : filename  + options;
    appformCreate('EDIT &mdash; ' + filename, 'edit?F=' + file);
    return false;
}

function deleteFile(filename, row, fileOptions)
{
    var id = 'TABLE_ROW_ID' + row;
    $('#' + id + ' td').css('background-color','#ff7');
    var options = (fileOptions != '')? ';OPT=' + fileOptions : '';

    if (confirm('Are you sure you want to delete [' + filename + ']?')) {

        $.get( 'AJAX/modify_file?A=DELETE;F=' + filename + options, '', function(data){
            if (data == 1) {
                $('#' + id ).fadeOut();
                updateFileList();
                ResizeIframe();
            } else {
                alert('Error: Could not delete file [' + filename + ']!');
            }
        });

    }
    $('#' + id + ' td').css('background-color','');
    return false;
}

function copyFile(filename, row, fmId, fileOptions)
{
    var options = (fileOptions != '')? ';OPT=' + fileOptions : '';
    $('#TABLE_ROW_ID' + row + ' td').css('background-color','#ff7');
    top.parent.appformCreate('COPY &mdash; ' + filename, 'modify_file?A=COPY;F=' + filename + options + ';D=' + fmId);
    return false;
}

function renameFile(filename, row, fmId, fileOptions)
{
    var options = (fileOptions != '')? ';OPT=' + fileOptions : '';
    $('#TABLE_ROW_ID' + row + ' td').css('background-color','#ff7');
    top.parent.appformCreate('RENAME &mdash; ' + filename, 'modify_file?A=RENAME;F=' + filename + options  + ';D=' + fmId);
    return false;
}

function resizeImage(filename, row, fmId)
{
    $('#TABLE_ROW_ID' + row + ' td').css('background-color','#ff7');
    top.parent.appformCreate('RESIZE &mdash; ' + filename, 'modify_file?A=RESIZE;F=' + filename + ';D=' + fmId);
    return false;
}




// =========================  GENERAL FUNCTIONS =============================

function getId(id){return document.getElementById(id);}
function showId(id) {if(getId(id)) getId(id).style.display =''; }
function hideId(id) {if(getId(id)) getId(id).style.display ='none';}

function Trim(sString){
  while (sString.substring(0,1) == ' ') sString = sString.substring(1, sString.length);
  while (sString.substring(sString.length-1, sString.length) == ' ') sString = sString.substring(0,sString.length-1);
  return sString;
}

function setClassGroup(group,except,c1,c2){
  var i=1;
  while (getId(group+i)){
    getId(group+i).className = c1;
    i++;
  }
  if (getId(group+except)){getId(group+except).className = c2;}
}

function hideGroup(group){
  var i=1;
  while (getId(group+i)){ getId(group+i).style.display='none';  i++; }
}

function hideGroupExcept(group,except){
  hideGroup(group);
  if (getId(group+except)){getId(group+except).style.display='';}
}

function setTab(num, group, tablink, tabselect)
{
  //FIX FOR PRE-EXISTING TABS ALREADY BEING USED
  if (group == undefined) {group = 'tab';}
  var linkname = group + 'link';

  if (tablink ==  undefined) {tablink = 'tablink';} //CLASS OF LINK
  if (tabselect ==  undefined) {tabselect = 'tabselect';} //CLASS OF SELECT

  hideGroupExcept(group, num);
  setClassGroup(linkname, num, tablink, tabselect);

  if (haveDialogTemplate) ResizeIframe();
}

function setEditTab(num)
{
  hideGroupExcept('mainpage', num);
  setClassGroup('tablink', num, 'tablink', 'tabselect');
  window.scrollTo(0,0);
  $('#content_edit_head').css({top : null});
  if (haveDialogTemplate) ResizeIframe();
}




function hexEncodeString(str)
{
    var keys = '0123456789abcdef';
    var result = '';
    var len = str.length;
    var c1;
    var c2;
    var c;
    var i;
    for (i=0; i < len; i++) {
        c = str.charCodeAt(i);
        c1 = Math.floor(c / 16);
        c2 = (c % 16);
        result += keys.substr(c1,1);
        result += keys.substr(c2,1);
    }
    return result;
}

function hexDecodeString(str)
{
    var result = '';
    var len = str.length;
    var c1;
    var c2;
    var c;
    var i;
    for (i=0; i < len; i+= 2) {
        c1 = str.charCodeAt(i);
        if (c1 < 58) c1 = c1-48;
        if (c1 > 96) c1 = c1-87;
        c2 = str.charCodeAt(i+1);
        if (c2 < 58) c2 = c2-48;
        if (c2 > 96) c2 = c2-87;
        c = 16*c1 + c2;
        result += String.fromCharCode(c);
    }
    return result;
}


// =========================  APPFORM =============================

function appformViewImage(image, width, height)
{
    var content = '<img src="' + image + '" border="0" width="' + width + '" height="' + height + '" alt="' + image + '" \/>';
    top.parent.appformCreateWithContent('IMAGE &mdash; ' + image, content, width, height, false);
    return false;
}


var drag = new Array;

function appformCreateWithContent(title, content, width, height, scroll)
{
    if (appformProcessing) return;
    appformProcessing = true;
    appformIdCounter++;
    appformZindex++;
    var id = 'appform' + appformIdCounter;

    var screenWidth  = getScreenWidth();
    var screenHeight = getScreenHight();

    if (width > screenWidth -40) {
        width = screenWidth -40;
        scroll = true;
    }

    if (height > screenHeight -40) {
        height = screenHeight -40;
        scroll = true;
    }

    var scrollStyle = (scroll)?  'overflow:auto; "' : '';

    var controls = '<div class="appform_controls">' +
     '<a href="#" title="Minimize" onclick="appformMinimize(\'' + id + '\',\''+ title + '\'); return false;">_</a>' +
     '<a href="#" title="Close" onclick="appformClose(\'' + id + '\'); return false;">X</a></div>';
    var titlebar = '<div id="titlebar_' + id + '" class="appform_titlebar"><h1>' + title + '</h1></div>';

    content = '<div class="appform" id="' + id + '">' + controls + titlebar +
            '<div class="appformcontent"><div style="'+ scrollStyle +'width:' + width + 'px; height:' + height + 'px;">' +
            content +'</div></div></div>';

    $('#'+ defaultAppId).prepend(content);

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


    $('#' + id).css('z-index',appformZindex);
    $('#' + id).width(width+10);
    $('#' + id).height(height + 30);
    $('#' + id).click(function () {appformActivate(this.id);});
    $('#' + id).attr('maximized', false);
    appformAddToTaskbar(id, title);
    appformProcessing = false;
    return false;
}



function appformCreate(title, file) {
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

    $('#'+ defaultAppId).prepend(content);
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
    return false;
}

function appformActivate(id)
{
    //appformRestoreIcons();
    adminMenuOff();
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
    //appformRestoreIcons();
    adminMenuOff();

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





// =========================  APPFORM DIALOG =============================



function ResizeIframe() {

    if(typeof(dialogNumber) == 'undefined') return;
    if (dialogNumber == '') return;

    var dialogId = 'appform' + dialogNumber;
    var iframeId = 'appformIframe' + dialogNumber;

    if (!parent.document.getElementById(iframeId)) return;

    //var widthOffset        = ( $.browser.msie )? 20 : 20;
    var widthOffset        = ( $.browser.msie )? 7 : 7;
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

    $('#' + dialogId, top.document).width(dialogWidth);
    $('#' + iframeId, top.document).width(dialogWidth);
}







// =========================  AUTOCOMPLETE =============================

function formatItem(row) {
    return row[0];
}

function formatResult(row) {
    return row[0];
}

//url : ac_url +'&Param='+ac_param+'&table='+ac_table+'&search='+ac_search+'&search_string='+ac_search_string,

function addAutoCompleteSetup(nameTarget, url)
{
    $(nameTarget).addClass('autocomplete');
    $(nameTarget).attr('url', url);
    $(nameTarget).autocomplete(url, {
        delay: 150,
        width: 260,
        antiCache: new Date().getTime(),
        formatItem: formatItem,
        formatResult: formatResult,
        selectFirst: true,
        cacheLength: 1,
        matchSubset: false,
        max: 1000
    });

    $(nameTarget).change( function() {
        $(nameTarget).css({color:'#fff', backgroundColor:'#f00'});
    } );
}

var last_ac_field;
var last_ac_value;

function setAutoCompleteValue(nameTargetId, valueTargetId, value)
{
    // set the autocomplete from the value
    var nameTarget = '#' + nameTargetId;
    $('#' + valueTargetId).val(value);
    $(nameTarget).addClass('formitem_loading');
    $.get($(nameTarget).attr('url'), {v : value}, function(data) {
        if (data != '' ) {
            $(nameTarget).val(data);
        }
        $(nameTarget).removeClass('formitem_loading');
    });
}

function setFormAutoCompleteValue(value, form_id)
{
    setAutoCompleteValue('AC_FORM_' + form_id, 'FORM_' + form_id, value);
}

function addAutoCompleteResult(nameTarget, valueTarget, data, completeFunction)
{
    $(nameTarget).find('..+/input').val(data[0]);     //output the name
    last_ac_field = data[0];
    $(valueTarget).val(data[1]); //output the value
    //if (data[1] > 0 ) {
    if (data[1] != '') {
        last_ac_value = data[1];
        $(nameTarget).css({color:'#fff', backgroundColor:'#7b7'});
        $(valueTarget).val(data[1]);
        if(typeof completeFunction == 'function') {
            completeFunction(valueTarget,data[1]);
        }
    }
}

function addAutoCompleteFunctionality(nameTargetId, valueTargetId, url, completeFunction)
{
    $('#' + nameTargetId).addClass('autocomplete');
    addAutoCompleteSetup('#' + nameTargetId, url);
    $('#' + nameTargetId).result(function(event, data, formatted) {
        addAutoCompleteResult('#' + nameTargetId, '#' + valueTargetId, data, completeFunction);

    });
}

function addDatePick(id, startyear, endyear)
{
    var year = ':';
    year = (startyear >=0)? '+' + startyear + year : startyear + year;
    year = (endyear >=0)? year + '+' + endyear : year + startyear;
    $('#' + id).datepicker({
        altFormat: 'yy-mm-dd',
        createButton: false,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
        yearRange: year
    }).keydown(function(event) {
        event.keyCode = null;
        return false;
    });
    if ($('#' + id).val() == '0000-00-00') {
        $('#' + id).val('');
    }
}


//=====================================EDIT MENU ITEMS=======================================
function removeExtraSpace(mytext){
  mytext = mytext.replace( /( ){2,}/g," ");
  return mytext;
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


function imageLinkPreview(ilink)
{
    //example ilink="[img_src=@/images/myimage.gif@_alt=@myimage@_width=@20@_height=@20@_border=@0@_/]">
    ilink = ilink.replace(/img_src=@/,'img_src=@GIMAGE/100x100');
    var width  = parseInt(ilink.replace(/(.)*width=@|@_height(.)+/, ''));
    var height = parseInt(ilink.replace(/(.)*height=@|@_border(.)+/, ''));
    if (width > 100 || height > 100) {
        var ratio = width/height;
        if (ratio > 1) {
            width = 100;
            height = Math.round(100 / ratio);
        } else {
            height = 100;
            width = Math.round(100 * ratio);
        }
    }
    ilink = ilink.replace(/width=@[0-9]+@/, 'width="' + width + '"');
    ilink = ilink.replace(/height=@[0-9]+@/, 'height="' + height + '"');
    ilink = ilink.replace(/@/g,'"');
    ilink = ilink.replace(/\[/g,'<');
    ilink = ilink.replace(/\]/g,'>');
    ilink = ilink.replace(/_/g,' ');
    ilink = ilink.replace(/~/g,'_');
    return ilink;
}


function tagSurround(tag1,tag2,inputID)
{
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
    } else if (document.selection) {
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
    return false;
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
        $('#HTMLcontentButton').html('Edit&nbsp;Text');

    } else {
        HTMLedit = !HTMLedit;
        tinyMCE.execCommand('mceRemoveControl', false, 'CTEXT');
        if(HTMLedit){
            $('#HTMLcontentButton').html('Edit&nbsp;Text');
            hideId('editmenu');
        } else {
            $('#HTMLcontentButton').html('Edit&nbsp;Content&nbsp;(HTML)');
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

//=========================== DATA LIST =================================

/* ========= Javascript ========= */

function tableDataListAddClick(link, dataFile)
{
    top.parent.appformCreate('ADD &mdash; ' + dataFile, link + ';A=ADD;F=' + dataFile + ';D=' + dialogNumber, 'apps');
    return false;
}

function tableDataListEditClick(idx, link, dataFile, rowId)
{
    var idbase = 'TABLE_ROW_ID' + idx + '_';
    $('#' + idbase + rowId +' td').css('background-color', '#ff7');
    top.parent.appformCreate('EDIT &mdash; ' + dataFile, link + ';A=EDIT;F=' + dataFile + ';ID=' + rowId + ';D=' + dialogNumber, 'apps');
    return false;
}


function tableDataListViewClick(idx, link, dataFile, rowId)
{
    var idbase = 'TABLE_ROW_ID' + idx + '_';
    $('#' + idbase + rowId +' td').css('background-color', '#ff7');
    top.parent.appformCreate('VIEW &mdash; ' + dataFile, link + ';A=VIEW;F=' + dataFile + ';ID=' + rowId, 'apps');
    return false;
}

function tableDataListDeleteClick(idx, link, rowId)
{
    var idbase = 'TABLE_ROW_ID' + idx + '_';

    var rowNumber = $('#' + idbase + rowId + ' td:first-child').html().replace('.', '');

    $('#' + idbase + rowId +' td').css('background-color','#ff7');

    if (confirm('Are you sure you want to delete row (' + rowNumber + ')?')) {

        $.get(link + ';A=DELETE;ID=' + rowId, '', function(data){
            if (data == 1) {
                $('#' + idbase + rowId +' td').fadeOut();
            } else {
                alert('Error: Could not delete record!' + data);
            }
        });

    }
    $('#' + idbase + rowId +' td').css('background-color','');
    return false;
}

function setDataListTableDrag(idx, filePath)
{
    $('#TABLE_DISPLAY' + idx).tableDnD({
        dragHandle: 'dragHandle',
        onDrop: function(table, row) {
            reloadDataListTable(row.id, idx, filePath);
        }
    });
}

function reloadDataListTable(row_id, idx, filePath) {
    var id_list = idx + ':';
    var id = '';
    $('[id^=TABLE_ROW_ID' + idx + ']').each(function(){
        id = $(this).attr('id');
        id = id.replace('TABLE_ROW_ID' + idx + '_', '');
        id_list += id + ',';
    });
    id_list = id_list.replace(/\,$/, '');

    $('#TABLE_DISPLAY' + idx + ' tbody').empty().append('<tr><td style="text-align:center; padding:1em;">Processing . . .<br /><br /><img src="/lib/site_admin/images/upload.gif" /><\/td><\/tr>');


    if (row_id == null){
        row_id = '';
    } else {
        row_id = row_id.replace('TABLE_ROW_ID' + idx + '_', '');
    }
    $.post(filePath,
        {data : id_list, row_id : row_id },
        function(data) {
            if (data) {
                $('#TABLE_DISPLAY' + idx + ' tbody').empty().append(data);
                setDataListTableDrag(idx, filePath);
            }
        }
    );
}

function runDataListFilter()
{
    var filter = $('#TABLE_FILTER').val();
    filter = filter.toLowerCase();
    var row = '';
    var check = false;
    var odd = 2;

    $('[id^=TABLE_ROW_ID]').each(function(){
        row = $('#' + this.id).html();
        row = row.replace(/<\/?[^>]+(>|$)/g, '|').toLowerCase();  // strips html tags and add bars to separate
        row = row.replace(/\|+/g, '|');  // strips html tags
        check = row.indexOf(filter);
        if (check > -1) {
            odd = 3 - odd;
            if (odd == 1) {
                $('#' + this.id + '.even').removeClass('even').addClass('odd');
            } else {
                $('#' + this.id + '.odd').removeClass('odd').addClass('even');
            }
            $('#' + this.id).show();
        } else {
            $('#' + this.id).hide();
        }
    });


}



