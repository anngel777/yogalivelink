<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: store
    Description: Content page for store
==================================================================================== */

AddStylesheet('/css/store_mod.css');

$cell_width = 150;
$_GET['banners'] = true;


$webcam     = MakeProductTable(array(
        'picture'       => '/images/store/webcam.jpg',
        'title'         => 'Logitech HD Pro Webcam C910',
        'description'   => '',
        'price'         => '$77.99',
        'link'          => 'http://www.amazon.com/Logitech-1080p-Webcam-Pro-C910/dp/B003M2YT96/ref=sr_1_1?ie=UTF8&qid=1304899495&sr=8-1',
        'image_width'   => $cell_width,
        'cell_width'    => $cell_width,
    ));

$webcam_eyeball = MakeProductTable(array(
        'picture'       => '/images/store/webcam_eyeball.jpg',
        'title'         => 'Blue Microphones Eyeball',
        'description'   => '',
        'price'         => '$46',
        'link'          => 'http://www.google.com/products/catalog?q=Eyeball+2.0&um=1&ie=UTF-8&tbm=shop&cid=3481693776246281614&sa=X&ei=fCgqTqzfMtTRiALK-PGvAg&ved=0CEIQ8gIwAQ',
        'image_width'   => $cell_width,
        'cell_width'    => $cell_width,
    ));
    
$webcam_ICECAM2 = MakeProductTable(array(
        'picture'       => '/images/store/webcam_ICECAM2.jpg',
        'title'         => 'Macally ICECAM2 USB 2.0',
        'description'   => '',
        'price'         => '$21.95',
        'link'          => 'http://www.amazon.com/Macally-ICECAM2-Camera-Built-Microphone/dp/B001AD0QPA',
        'image_width'   => $cell_width,
        'cell_width'    => $cell_width,
    ));   

$headset    = MakeProductTable(array(
        'picture'       => '/images/store/headset.jpg',
        'title'         => 'Samson AirLine Micro-Wireless',
        'description'   => "",
        'price'         => '$249.00<br />(Use Code: G7DBWT10TS8 to save $55)',
        'link'          => 'http://www.dvestore.com/products/Samson-AirLine-Micro-%252d-Wireless-Earset-System.html',
        'image_width'   => $cell_width,
        'cell_width'    => $cell_width,
    ));
    
$dvi        = MakeProductTable(array(
        'picture'       => '/images/store/dvi_cable.jpg',
        'title'         => '6ft HDMI to DVI-D Cable',
        'description'   => 'Connect your webcamera to a DVI-enabled TV set.',
        'price'         => '$6.20',
        'link'          => 'http://www.amazon.com/Cables-Unlimited-PCM-2296-06-HDMI-Cable/dp/B0007MWE1E/ref=sr_1_1?ie=UTF8&s=electronics&qid=1304899159&sr=1-1',
        'image_width'   => $cell_width,
        'cell_width'    => $cell_width,
    ));
    
$ethernet   = MakeProductTable(array(
        'picture'       => '/images/store/ethernet_cable.jpg',
        'title'         => '50ft Ethernet Cable',
        'description'   => 'Connect your computer directly to your internet for faster speeds and higher-quality web video.',
        'price'         => '$10.95',
        'link'          => 'http://www.amazon.com/AmazonBasics-Cat5e-Ethernet-Patch-Meters/dp/B001TH7GVO/ref=sr_1_1?ie=UTF8&s=electronics&qid=1304899044&sr=8-1',
        'image_width'   => $cell_width,
        'cell_width'    => $cell_width,
    ));
    
$usb        = MakeProductTable(array(
        'picture'       => '/images/store/usb_cable.jpg',
        'title'         => '12ft USB 2.0 Extension Cable',
        'description'   => 'Connect your computer and webcam for the optimal yoga session.',
        'price'         => '$17.95',
        'link'          => 'http://www.amazon.com/Tripp-Lite-U026-016-Certified-Extension/dp/B0002D6QJO/ref=sr_1_1?ie=UTF8&s=electronics&qid=1304899410&sr=8-1',
        'image_width'   => $cell_width,
        'cell_width'    => $cell_width,
    ));

$headset_2  = MakeProductTable(array(
        'picture'       => '/images/store/headset_jabra_extreme.jpg',
        'title'         => 'Jabra Extreme (Bluetooth Headset)',
        'description'   => '',
        'price'         => '$62.15',
        'link'          => 'http://www.amazon.com/gp/product/B0049SKHYS/ref=s9_simh_gw_p107_d0_i1?pf_rd_m=ATVPDKIKX0DER&pf_rd_s=center-2&pf_rd_r=13YJS1EEBBT2X1YEHCCC&pf_rd_t=101&pf_rd_p=470938631&pf_rd_i=507846',
        'image_width'   => $cell_width,
        'cell_width'    => $cell_width,
    ));
    
$headset_3  = MakeProductTable(array(
        'picture'       => '/images/store/headset_jabra_stone.jpg',
        'title'         => 'Jabra STONE2 (Bluetooth Headset)',
        'description'   => '',
        'price'         => '$89.84',
        'link'          => 'http://www.amazon.com/gp/product/B004KPLA9Q/ref=s9_simh_gw_p107_d0_i3?pf_rd_m=ATVPDKIKX0DER&pf_rd_s=center-2&pf_rd_r=13YJS1EEBBT2X1YEHCCC&pf_rd_t=101&pf_rd_p=470938631&pf_rd_i=507846',
        'image_width'   => $cell_width,
        'cell_width'    => $cell_width,
    ));
    
    
    $title_style = 'style="font-size:16px; padding:5px; font-weight:bold; color:#fff;" bgcolor="#9B9A41"';
    

    
    
$banner_1  = MakeProductTable(array(
        'picture'       => '/images/store/banner_vert_yogabody.jpg',
        'title'         => 'all-natural yoga practice aids',
        'description'   => '',
        'price'         => '',
        'link'          => 'http://www.yogabodynaturals.com/cmd.php?af=1383925',
        'image_width'   => $cell_width-20,
        'cell_width'    => $cell_width-20,
    ));

$banner_2  = MakeProductTable(array(
        'picture'       => '/images/store/banner_vert_culturalelement.jpg',
        'title'         => 'home decor and cultural gifts',
        'description'   => '',
        'price'         => '',
        'link'          => 'http://www.shareasale.com/r.cfm?B=175390&U=542694&M=22149&urllink=',
        'image_width'   => $cell_width-20,
        'cell_width'    => $cell_width-20,
    ));

$banner_3  = MakeProductTable(array(
        'picture'       => '/images/store/banner_vert_barefootyoga.jpg',
        'title'         => 'clothing, mats, and accessories',
        'description'   => '',
        'price'         => '',
        'link'          => 'http://www.shareasale.com/r.cfm?B=193307&U=542694&M=24185&urllink',
        'image_width'   => $cell_width-20,
        'cell_width'    => $cell_width-20,
    ));

$banner_4  = MakeProductTable(array(
        'picture'       => '/images/store/banner_vert_asanagreen.jpg',
        'title'         => 'eco-friendly clothing and accessories',
        'description'   => '',
        'price'         => '',
        'link'          => 'http://www.asanagreen.com/?agp=65',
        'image_width'   => $cell_width-20,
        'cell_width'    => $cell_width-20,
    ));    
    
$banners = <<<BANNERS
    <table width="100%" align="center" cellspacing="10">
    <tr>
        <td align="left" valign="top" colspan="4" $title_style>PRODUCTS OFFERED BY OUR PARTNERS</td>
    </tr>
    <tr>
        <td align="center" valign="top">$banner_1</td>
        <td align="center" valign="top">$banner_2</td>
        <td align="center" valign="top">$banner_3</td>
        <td align="center" valign="top">$banner_4</td>
    </tr>
    </table>
BANNERS;

$banners = (Get('banners')) ? $banners : '';

    
$TABLE = <<<TABLE
    <div style="width:100%;">



    <table width="100%" align="center" cellspacing="20">
    <tr>
        <td align="left" valign="top" colspan="3" $title_style>PC COMPATIBLE WEBCAMS</td>
    </tr>
    <tr>
        <td align="center" valign="top">$webcam</td>
        <td align="center" valign="top">$webcam_ICECAM2</td>
        <td align="center" valign="top"></td>
    </tr>
    
    
    
    <tr>
        <td align="left" valign="top" colspan="3" $title_style>MAC COMPATIBLE WEBCAMS</td>
    </tr>
    <tr>
        <td align="center" valign="top">$webcam_eyeball</td>
        <td align="center" valign="top">$webcam_ICECAM2</td>
        <td align="center" valign="top"></td>
    </tr>
    
    
    
    <tr>
        <td align="left" valign="top" colspan="3" $title_style>PC/MAC COMPATIBLE HEADSETS</td>
    </tr>
    <tr>
        <td align="center" valign="top">$headset</td>
        <td align="center" valign="top">$headset_2</td>
        <td align="center" valign="top">$headset_3</td>
    </tr>
    
    
    
    <tr>
        <td align="left" valign="top" colspan="3" $title_style>ADDITIONAL ACCESSORIES</td>
    </tr>
    <tr>
        <td align="center" valign="top">$usb</td>
        <td align="center" valign="top">$ethernet</td>
        <td align="center" valign="top">$dvi</td>
    </tr>
    
    
    </table>
    
    </div>
TABLE;





AddStyle("
.store_banners {
    border:1px solid #9B9A41;
}
");


$content = <<<CONTENT
<br />
<div class="article_all_content">


<b>Get the most out of your one-on-one yoga sessions</b> with the right gear tested and approved by YogaLiveLink!


<b>You'll need: Webcam, Headset, Connector Cables</b>
<br /><br />

<b>Webcam</b><br />
A webcam built into your computer will work great. We highly recommend an external webcam for better image quality and placement versatility. 
<br /><br />

<b>Headset</b><br />
Optional. We highly recommend a headset for ease in talking with your instructor.
<br /><br />

<b>Connector Cables</b><br />
Use connector cables to connect your gear to your computer
<br /><br />

<div style="display:none;">
<b>Have all your gear? Try it out in our <a href="/test_computer;bypass" style="font-weight:bold; font-size:14px; color:#A91148;">Testing Area here</a>. </b>
</div>

<br />
<a href="/test_computer">
<img src="/images/test_equipment.jpg">
</a>


</div>
<br /><br />
CONTENT;


// ---------- FINALIZE CONTENT ----------
$content_left   = "{$content}";
$content_right  = " {$banners} {$TABLE}";


// ---------- SWAP CONTENT INTO PAGE ----------
AddSwap('@@CONTENT_LEFT@@', $content_left);
AddSwap('@@CONTENT_RIGHT@@', $content_right);
AddSwap('@@PAGE_HEADER_TITLE@@','store: items to improve your yoga experience');


// ---------- FUNCTION TO CREATE THE LOOK OF A STORE ITEM ----------
function MakeProductTable($item_array) 
{
    $output = <<<ITEM
    <!-- =============== STORE ITEM =============== -->
    
    <div class="store_item_outter_wrapper" style="width:{$item_array['cell_width']}px;">
    <div class="store_item_inner_wrappe___r" style="background-color:#fff;">

    <a class="store_catalog_product" href="{$item_array['link']}" target="_blank">
        <img class="store_catalog_picture" src="{$item_array['picture']}" border="0" alt="{$item_array['title']}" width="{$item_array['image_width']}"  />
        <span class="store_item_content_wrapper">
            <span class="store_item_title">{$item_array['title']}</span>
            <span class="store_item_price" style="font-size:12px;">{$item_array['price']}</span>
            <span class="store_item_description" style="text-align:left; padding:5px;">{$item_array['description']}</span>
        </span>
    </a>

    </div>
    </div>
    
    <!-- =============== END STORE ITEM =============== -->
ITEM;

    return $output;
}