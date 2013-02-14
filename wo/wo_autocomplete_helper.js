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
    $(nameTarget).find("..+/input").val(data[0]);     //output the name
    last_ac_field = data[0];
    $(valueTarget).val(data[1]); //output the value
    if (data[1] > 0 ) {
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
