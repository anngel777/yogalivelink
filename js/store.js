/* ========= Store Javascript, by MVP ========= */

//---------------------- SHOPPING --------------------------
function closeShoppingCart()
{
   $('#shopping_cart').slideUp('normal', function() {
       $('#shopping_cart_form').fadeOut('normal', function() {
           $('#cart_overlay').remove();
           $('#header_flash_obj').show();
       });
   });
   return false;
}

function viewShoppingCart()
{
    shoppingCartAddItem(0);
    return false;
}

function shoppingCartAddItem(pn)
{
    var content = '<div id="cart_overlay"><div id="shopping_cart_form"><a title="Close" id="shopping_cart_close" href="#" onclick="return closeShoppingCart();">X<\/a><div id="shopping_cart"><img src="/wo/images/upload.gif" border="0" width="32" height="32" alt="Loading..." /><\/div><\/div>';
    $('body').prepend(content);
    $('#cart_overlay').height($('body').height() + 30);
    $('#shopping_cart_form').css('margin-top', $(window).scrollTop()+10);

    if (pn == 0) {
       var link = '/AJAX/store_order';
    } else {
       var formdata = $('#STORE_ITEM_FORM' + pn).serialize();
       var test = formdata;
       formdata = formdata.replace(/FORM_OPTION_[0-9]+_/g, '~').replace(/\.[0-9]+/g, '').replace(/&/g, '').replace(/=/g, '.');
       var link = '/AJAX/store_order;PN=' + pn + formdata;
       //$('#store_item_' + pn).addClass('store_item_in_cart');
       $('#button_view_cart').show();
    }

    $('#shopping_cart').load(link, function () {
       $('#ordercontent').slideDown();
    });

    return false;
}


function updateShoppingCart()
{
    var formdata = $('#ORDERFORM').serialize() + '&UPDATECART=1';
    $('#cart_heading').append('&nbsp;<img style="position:absolute;" id="loading_gif" src="/wo/images/upload.gif" alt="loading" border"0" \/>');
    $('#ordercontent').load('/AJAX/store_order', {ajaxdata : formdata});
    return false;
}

function clearShoppingCart()
{
    $('#ordercontent').html('<img id="loading_gif" src="/wo/images/upload.gif" alt="loading" border"0" \/>');
    $('#ordercontent').load('/AJAX/store_order', {ajaxdata : 'CLEARALL=1'});
    $('.store_item_outter_wrapper').removeClass('store_item_in_cart');
    $('#button_view_cart').hide();
    return false;
}
