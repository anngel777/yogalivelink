<?php
$type           = (Get('type')) ? Get('type') : 'all';
$product_id     = (Get('product_id')) ? Get('product_id') : '0';

$Obj = new Store_ShoppingProduct();
$Obj->page_location         = 'shopping_product';
$Obj->product_detail_link   = 'shopping_product';
$Obj->category              = Get('category') ? Get('category') : '';
$Obj->store_categories_id   = Get('cid') ? Get('cid') : '';
$Obj->where                 = Get('where') ? Get('where') : '';
$Obj->Execute($type, $product_id);

echo "<br /><br />Load Time: @@TIME@@";



# RESIZE THE CURRENT FRAME TO FIT CONTENTS
# ================================================
$script = <<<SCRIPT
    var dialogNumber = '';
    if (window.frameElement) {
        if (window.frameElement.id.substring(0, 13) == 'appformIframe') {
            dialogNumber = window.frameElement.id.replace('appformIframe', '');
        }
    }
    ResizeIframe();
SCRIPT;
AddScript($script);