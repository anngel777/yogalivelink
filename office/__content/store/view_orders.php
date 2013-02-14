<?php
$type           = (Get('type')) ? Get('type') : 'all';
$order_number   = (Get('order_number')) ? Get('order_number') : '0';

$Obj = new Store_ViewOrders();
$Obj->page_location = 'view_orders';
$content = $Obj->Execute($type, $order_number);
echo $content;









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