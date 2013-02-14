/*
    Web Office Javascript
    by Michael V. Petrovich 2009
*/
// =========================  SETUP =============================

// must set in header : var dialogNumber = '@@DIALOGID@@'
var tableAjaxHelperFile = '/wo/wo_table_ajax_helper.php';
var appformZindex = 1000;
var appformIdCounter = 0;
var appformProcessing = false;
var taskbarId = 'appform_taskbar';
var haveDialogTemplate = 1;
var defaultAppId = 'apps';

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



// =========================  TABLE FUNCTIONS =============================



function tableDeleteClick(idx, value, eq)
{
    var idbase = 'TABLE_ROW_ID' + idx + '_';

    var rowNumber = $('#' + idbase + value + ' td:first-child').html().replace('.', '');

    $('#' + idbase + value +' td').css('background-color','#ff7');

    if (confirm('Are you sure you want to delete (inactivate) row (' + rowNumber + ')?')) {

        $.get( tableAjaxHelperFile + '?delete=1&eq=' + eq, '', function(data){
            if (data == 1) {
                $('#' + idbase + value +' td').fadeOut();
            } else if (data == 2 ) {
                if (confirm('Are you sure you want to permanently remove row (' + rowNumber + ')?')) {
                    $.get( tableAjaxHelperFile + '?delete=2&eq=' + eq, '', function(data){
                        if (data == 1) {
                            $('#' + idbase + value +' td').fadeOut();
                        } else {
                            alert('Error: Could not delete record!');
                        }
                    });
                }
            } else {
                alert('Error: Could not delete record!');
            }
        });

    }
    $('#' + idbase + value +' td').css('background-color','');
    return false;
}


function tableViewClick(idx, value, eq, title)
{
    var idbase = 'TABLE_ROW_ID' + idx + '_';

    $('#' + idbase + value +' td').css('background-color','#ff7');

    if (top.parent.appformCreate) {
        top.parent.appformCreate('View Record - ' + title, 'view_record;eq=' + eq, defaultAppId);
    } else {
        alert('Function Not Available to View');
    }
    return false;
}


function tableEditClick(idx, value, eq, title)
{
    var idbase = 'TABLE_ROW_ID' + idx + '_';

    $('#' + idbase + value +' td').css('background-color','#ff7');

    top.parent.appformCreate('Edit Record - ' + title, 'edit_record;eq=' + eq, defaultAppId);
    return false;
}

function tableAddClick(eq, title)
{
    top.parent.appformCreate('Add Record-' + title, 'add_record;eq=' + eq, defaultAppId);
    return false;
}

function tableCopyClick(idx, value, eq, title)
{
    var idbase = 'TABLE_ROW_ID' + idx + '_';

    $('#' + idbase + value +' td').css('background-color','#ff7');

    if (top.parent.appformCreate) {
        top.parent.appformCreate('Copy Record - ' + title, 'copy_record;eq=' + eq, defaultAppId);
    } else {
        alert('Function Not Available to Copy');
    }
    return false;
}

function changeTableSearchFilterBox(selection, idx, field)
{
    var id = '#TABLE_SEARCH_VALUE' + idx + '_' + field;
    if (selection=='All') {
        $(id).removeClass('SEARCH_FILTER_VALUE_SELECTED').addClass('SEARCH_FILTER_VALUE');
    } else {
        $(id).removeClass('SEARCH_FILTER_VALUE').addClass('SEARCH_FILTER_VALUE_SELECTED');
    }
}

function changeSearchSelectRow(checkboxId)
{
    var checked = getId(checkboxId).checked;
    if (checked) {
        $('#' + checkboxId).parent().parent().removeClass('SEARCH_SELECT_ROW').addClass('SEARCH_SELECT_ROW_SELECTED');
    } else {
        $('#' + checkboxId).parent().parent().removeClass('SEARCH_SELECT_ROW_SELECTED').addClass('SEARCH_SELECT_ROW');
    }
}

function setSearchSelectRows()
{
    $('.SEARCH_DISPLAY').each(function() {
        changeSearchSelectRow($(this).attr('id'));
    });
}


function searchSelectAll()
{
    $('.SEARCH_DISPLAY').attr('checked', 'checked');
    setSearchSelectRows();
}
function searchClearAll()
{
    $('.SEARCH_DISPLAY').attr('checked', '');
    $('[id^=TABLE_SEARCH_VALUE]').val('');
    $('[id^=TABLE_SEARCH_VALUE]').removeClass('SEARCH_FILTER_VALUE_SELECTED').addClass('SEARCH_FILTER_VALUE');
    //$('.table_search_display_operators').val(0);
    $('.table_search_display_operators').each(function(){
        $(this).selectedIndex = 0
    });

    setSearchSelectRows();
}

function searchClearDisplay()
{
    $('.SEARCH_DISPLAY').attr('checked', '');
    setSearchSelectRows();
}

function tableSearchDisplayToggle()
{
    $('#TABLE_SEARCH_TAB').slideToggle();
}

function tableSearch(action, eq, idx)
{
    var formdata = $('#TABLE_SEARCH_SELECTION' + idx +', #TABLE_STARTROW' + idx + ', #TABLE_ROWS' + idx + ', #NUMBER_ROWS' + idx).serialize();
    $('#TABLE_DISPLAY' + idx +' tbody').empty().append('<tr><td style="text-align:center; padding:1em;">Processing . . .<br /><br /><img src="/wo/images/upload.gif" /></td></tr>');
    $.post( tableAjaxHelperFile + '?table_search=1&eq=' + eq + '&idx=' + idx + '&action=' + action,
        {data : formdata},
        function(data) {
            $('#TABLE_DISPLAY' + idx +' tbody').empty().append(data);
            if (haveDialogTemplate) {
                ResizeIframe();
            }
        });
}

function rowUpdate(eq, idx, id)
{
    var formdata = $('#TABLE_SEARCH_SELECTION' + idx).serialize();
    var firstCell = $('#TABLE_ROW_ID' + idx +'_'+ id + ' td:first-child').html();
    $('#TABLE_ROW_ID' + idx +'_'+ id + ' td:first-child').html('<img src="/wo/images/indicator.gif" alt="loading" border"0" \/>');

    $.post( tableAjaxHelperFile + '?table_search=1&eq=' + eq + '&idx=' + idx + '&action=update_row&row_id=' + id,
        {data : formdata},
        function(data) {
            $('#TABLE_ROW_ID' + idx +'_'+ id).empty().append(data);
            $('#TABLE_ROW_ID' + idx +'_'+ id + ' td:first-child').html(firstCell);
            $('#TABLE_ROW_ID' + idx +'_'+ id +' td').css({ backgroundColor:'#8f8'});
        });
}


function tableCustomSearchSave(eq, idx)
{
    var searchName = $('#CUSTOM_SEARCH_NAME' + idx).val();
    if (searchName == '') {
        alert('Search Name Must be Entered!');
        return;
    }

    var searchNameHex = hexEncodeString(searchName);

    var formdata = $('#TABLE_SEARCH_SELECTION' + idx +', #CUSTOM_SEARCH_NAME' + idx).serialize();

    $('#CUSTOM_SEARCH_NAME' + idx).css({color:'#fff', backgroundColor:'#7b7'});

    $.post( tableAjaxHelperFile + '?custom_search=save&eq=' + eq + '&idx=' + idx,
        {data : formdata},
        function(data) {
            if (data.substring(0,6) == "ERROR:") {
                alert(data);
            } else {
                var searchNameId = 'CUSTOM_SEARCH'+ idx + '_' + searchNameHex;
                $('#' + searchNameId).remove();
                $('#CUSTOM_SEARCHES' + idx).append(data);
                $('#CUSTOM_SEARCH_NAME' + idx).css({color:'#000', backgroundColor:'#fff'});
            }
        });
}

function tableCustomSearchDelete(eq, idx)
{
    var searchName = $('#CUSTOM_SEARCH_NAME' + idx).val();
    if (searchName == '') {
        alert('Search Name Must be Entered!');
        return;
    }
    var searchNameHex = hexEncodeString(searchName);

    $('#CUSTOM_SEARCH_NAME' + idx).css({color:'#fff', backgroundColor:'#7b7'});

    var formdata = $('#CUSTOM_SEARCH_NAME' + idx).serialize();

    $.post( tableAjaxHelperFile + '?custom_search=delete&eq=' + eq + '&idx=' + idx,
        {data : formdata},
        function(data) {
            if (data.substring(0,6) == "ERROR:") {
                alert(data);
            } else {
                var searchNameId = 'CUSTOM_SEARCH'+ idx + '_' + searchNameHex;
                $('#' + searchNameId).fadeOut('normal', function(){$(this).remove();});

                $('#CUSTOM_SEARCH_NAME' + idx).css({color:'#000', backgroundColor:'#fff'});
                $('#CUSTOM_SEARCH_NAME' + idx).val('');
            }
        });
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

function tableCustomSearchSelect(idx, qObj)
{
    searchClearAll();

    var searchName = qObj['name'];
    searchName = hexDecodeString(searchName);
    $('#CUSTOM_SEARCH_NAME' + idx).val(searchName).css({color:'#000', backgroundColor:'#fff'});

    var orderList = qObj['order'].replace(' ', '_') + ',,';
    var orderArray = orderList.split(',');

    $('#TABLE_ORDER' + idx + '_' + orderArray[0]).attr('checked', 'checked');
    if (orderArray[1] != '') {
        $('#TABLE_2ORDER' + idx + '_' + orderArray[1]).attr('checked', 'checked');
        if (orderArray[2] != '') {
            $('#TABLE_3ORDER' + idx + '_' + orderArray[2]).attr('checked', 'checked');
        }
    }

    var selections = qObj['selections'];

    for (i=0; i<selections.length; i++) {
        var row      = selections[i];
        var field    = row['field'];
        var selector = row['selector'];
        var filter   = row['filter'];
        var view     = row['view'];

        $('#TABLE_SEARCH_OPERATOR' + idx + '_' + field).val(selector);
        changeTableSearchFilterBox(selector, idx, field);
        $('#TABLE_SEARCH_VALUE' + idx + '_' + field).val(filter);
        var check = (view==1)? 'checked' : '';
        $('#TABLE_SEARCH_DISPLAY' + idx + '_' + field).attr('checked', check);

    }
    setSearchSelectRows();

}


function setColumnSort(idx, eq, field)
{
    var down = $('#TABLE_ORDER' + idx + '_' + field).attr('checked');
    if (down) {
        $('#TABLE_ORDER' + idx + '_' + field + '_DESC').attr('checked', 'checked');
    } else {
        $('#TABLE_ORDER' + idx + '_' + field).attr('checked', 'checked');
    }
    tableSearch('SHOW', eq, idx);
}

function setTabSearchTable(num, group, eq, idx)
{
    var linkname = group + 'link';
    var num2 = 3 - num;

    $('#' + group + num2).hide();
    $('#' + group + num).show();


    $('#' + linkname + num2).removeClass('tabselect').addClass('tablink');
    $('#' + linkname + num).removeClass('tablink').addClass('tabselect');

    if (num == 2) {
        tableSearch('SHOW', eq, idx);

    } else {
        if (haveDialogTemplate) {
            ResizeIframe();
        }
    }
}

function runFilter()
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

// =========================  APPFORM =============================


var drag = new Array;

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
    return false;
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
    return false;
}



function appformCreateOverlay(title, file) {
    if (appformProcessing) return;
    appformProcessing = true;
    appformIdCounter++;
    appformZindex++;
    var id = 'appform' + appformIdCounter;
    var iframeId = 'appformIframe' + appformIdCounter;

    var dialogFile 	    =  file + ';template=overlay;DIALOGID=' + appformIdCounter;
    var controls 	    = '<span class="appform_overlay_controls"><a id="page_overlay_close" href="#" title="Close" onclick="appformCloseOverlay(\'' + id + '\'); return false;"><img src="/office/images/closelabel.gif" id="page_overlay_close_img" border="0" alt="close"/></a></span>';
    var titlebar 	    = '<div id="overlay_titlebar_' + id + '" class="appform_overlay_titlebar">' + title + ''+ controls +'</div>';
    var closeFunction   = '<script type="text/javascript">function CloseOverlay() { appformCloseOverlay(\'' + id + '\'); return false; }</script>';
    var content 	    = '<div id="page_dialog_overlay"><div class="appform_overlay" id="' + id + '">' + closeFunction + titlebar + '<iframe id="'+ iframeId +'" class="appiframe overlay_loading" src="' + dialogFile +'"></iframe></div></div>';
    

    $('body').prepend(content);
	//$(nameTarget).addClass('formitem_loading');
    $('#page_dialog_overlay').height($(window).height() + 30);

	var width = 300;
	$('#' + id).width(width);
    $('#' + id).css('z-index', appformZindex);
    
    //$('#' + id).css('max-height', '400');
    //$('#' + id).css('border', '2px solid red');
    
    //$('#' + iframeId).css('max-height', '400');
    //$('#' + iframeId).css('border', '3px solid blue');
    
	$('#page_dialog_overlay').css('z-index', appformZindex-1);
    appformProcessing = false;
    window.scrollTo(0,0);
	//$('#' + iframeId).removeClass('overlay_loading');
    return false;
}

function appformCloseOverlay(id)
{
   $('#'+id).fadeOut('normal', function () {
       $('#page_dialog_overlay').remove();
   });
   return false;
}

function appformActivate(id)
{
    if (appformProcessing) return;
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





// =========================  APPFORM DIALOG =============================



function ResizeIframe() {
//alert('ResizeIframe');
    if(typeof(dialogNumber) == 'undefined') return;
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
    
    var customHeightNeg = 100;
    
    var maxHeight      = winHeight - dtop - heightOffset - titlebarHeight - taskbarHeight - 20 - customHeightNeg;
    var maxWidth       = winWidth - (dleft/2) - 20;

//alert(' height: '+winHeight+ ' width: '+winWidth+' dtop: '+dtop+ ' dleft: '+dleft+' maxHeight: '+maxHeight+ ' maxWidth: '+maxWidth);
    
    // -------- set height ---------
    var contentHeight = $('#dialogcontainer').height() + heightOffset;

    var newHeightDialog = Math.min(contentHeight + dialogHeightOffset, maxHeight + dialogHeightOffset + 5);
    var newHeightIframe = Math.min(contentHeight, maxHeight);

//alert(' contentHeight: '+contentHeight);

    $('#' + dialogId, top.document).height(newHeightDialog);
    $('#' + iframeId, top.document).height(newHeightIframe);

    // -------- set width ---------

    var contentWidth  = $('#dialogcontainer').width() + widthOffset;
    var dialogWidth   = Math.min(contentWidth, maxWidth)  + 20;

//alert(' contentWidth: '+contentWidth+ ' dialogWidth: '+dialogWidth);

    $('#' + dialogId, top.document).width(dialogWidth);
    $('#' + iframeId, top.document).width(dialogWidth);
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
    top.window.scrollTo(0,0);
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

