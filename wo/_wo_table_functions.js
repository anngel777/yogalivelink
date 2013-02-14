/* Web Office standard table functions */

/* =========================  TABLE SEARCH FUNCTIONS ============================= */

var tableAjaxHelperFile = '/wo/wo_table_ajax_helper.php';

function tableDeleteClick(idx, value, eq)
{
    var idbase = 'TABLE_ROW_ID' + idx + '_';
    
    var rowNumber = $('#' + idbase + value + ' td:first-child').html().replace('.', '');

    $('#' + idbase + value +' td').css('background-color','#ff7');

    if (confirm('Are you sure you want to delete row (' + rowNumber + ')?')) {

        $.get( tableAjaxHelperFile + '?delete=1&eq=' + eq, '', function(data){
            if (data == 'ok') {
                $('#' + idbase + value +' td').fadeOut();
            } else {
                alert('Error: Could not delete record!');
            }
        });

    }
    $('#' + idbase + value +' td').css('background-color','');
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
}


function tableEditClick(idx, value, eq, title)
{
    var idbase = 'TABLE_ROW_ID' + idx + '_';

    $('#' + idbase + value +' td').css('background-color','#ff7');

    top.parent.appformCreate('Edit Record - ' + title, 'edit_record;eq=' + eq, defaultAppId);
}

function tableAddClick(eq, title)
{
    top.parent.appformCreate('Add Record-' + title, 'add_record;eq=' + eq, defaultAppId);
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
    $('.table_search_display_operators').val(0);
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