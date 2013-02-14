<?php

# HEADER SWAP
# ========================================================================================================
$HEADER_LEFT    = '<img src="images/template/[T~IMG_HEADER_STORE_LEFT]" width="314" alt="" border="0" />';
$HEADER_RIGHT   = '<img src="images/template/[T~IMG_HEADER_STORE_RIGHT]" width="666" alt="" border="0" />';


$items = array();

$items[0]['image']       = '/images/store/socks.png'; #gloves
$items[0]['title']       = '[T~TXT_STORE_0001]';
$items[0]['description'] = '[T~TXT_STORE_0002]';
$items[0]['price']       = '$7.95';
$items[0]['code']        = '#98732';

$items[1]['image']       = '/images/store/camera.png';
$items[1]['title']       = '[T~TXT_STORE_0003]';
$items[1]['description'] = '[T~TXT_STORE_0004]';
$items[1]['price']       = '$25.00';
$items[1]['code']        = '#512453';

$items[2]['image']       = '/images/store/mat.png';
$items[2]['title']       = '[T~TXT_STORE_0005]';
$items[2]['description'] = '[T~TXT_STORE_0006]';
$items[2]['price']       = '$27.55';
$items[2]['code']        = '#19237834';

$items[3]['image']       = '/images/store/wash.png';
$items[3]['title']       = '[T~TXT_STORE_0007]';
$items[3]['description'] = '[T~TXT_STORE_0008]';
$items[3]['price']       = '$12.95';
$items[3]['code']        = '#93122';

for ($i=0; $i<count($items); $i++) {
    $item = $items[$i];
    
    $item = <<<ITEM
    <div class="col">
    <div class="item_outter_wrapper">
    <div class="item_inner_wrapper">
        <div class="item_picture"><img src="{$item['image']}" border="0" alt="{$item['title']}" /></div>
        <div class="item_content_wrapper">
            <div class="item_title">{$item['title']}</div>
            <div class="item_code">{$item['code']}</div>
            <div class="item_description">{$item['description']}</div>
            <br />
            <div class="item_price">{$item['price']}</div>
        </div>
    </div>
    </div>
    </div> <!-- END col -->
    
    <div class="col" style="width:20px;">&nbsp;</div>
ITEM;

echo $item;
}
echo "<div class='clear'></div>";



$style = <<<STYLE

.item_outter_wrapper {
    padding:5px;
    width:200px;
    border:1px solid #999;
    /*background-color:#fff;*/
}
.item_inner_wrapper {
    width:200px;
    height:350px;
    border:1px soli red;
    background-color:#fff;
}
.item_picture {
    width:200px;
    height:210px;
}
.item_content_wrapper {
    padding:10px;
}
.item_title {
    font-weight:bold;
    font-size:14px;
    color:#44636E;
    padding-bottom:0px;
}
.item_code {
    font-weight:normal;
    font-size:8px;
    color:#44636E;
    font-style:italic;
    padding-bottom:5px;
}
.item_description {
    font-weight:normal;
    font-size:12px;
    color:#999;
}
.item_price {
    font-weight:bold;
    font-size:16px;
    color:#44636E;
}

STYLE;
AddStyle($style);
?>
