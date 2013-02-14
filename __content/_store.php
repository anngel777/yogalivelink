<?php
/*

$webcam     = MakeProductTable(array(
        'picture'       => '/images/store/webcam.jpg',
        'title'         => 'Logitech HD Pro Webcam C910',
        'description'   => 'Based on customer reviews, this high-definition webcam has the best picture and sound quality for your yoga sessions. This webcam plugs it into your computer via a long cable, and can be placed in your yoga space where it provides the optimal angle and view during your yoga sessions. 
                            <br />
                            <ul style="padding-left:10px;">
                                <li>Enjoy widescreen HD 720p video calling</li>
                                <li>Breathtaking HD 1080p video recording</li>
                                <li>High quality 10MP photos</li>
                                <li>Built-in stereo speakers</li>
                            </ul>
                            <br />
                            <b>Recommended for PC users.</b>
                            ',
        'price'         => '$77.99',
        'link'          => 'http://www.amazon.com/Logitech-1080p-Webcam-Pro-C910/dp/B003M2YT96/ref=sr_1_1?ie=UTF8&qid=1304899495&sr=8-1',
        'width'         => '256', 
    ));


$headset    = MakeProductTable(array(
        'picture'       => '/images/store/headset.jpg',
        'title'         => 'Samson AirLine Micro-Wireless Earset System',
        'description'   => "The AirLine Micro-Wireless Earset System uses the latest in lithium ion battery technology and small, high-quality audio and RF components to bring performance, freedom and convenience to a new level. It is Samson's smallest wireless system, and considered to be their most comfortable, reliable and versatile system yet. 
                            <br /><br />
                            When you use this lightweight, versatile headset you'll hear your instructor's cues with great sound quality, making your yoga sessions even more enjoyable.
                            <br /><br />
                            <b>Recommended for instructors.</b>
                            ",
        'price'         => '$249.00<br />(Use Code: G7DBWT10TS8 to save $55)',
        'link'          => 'http://www.dvestore.com/products/Samson-AirLine-Micro-%252d-Wireless-Earset-System.html',
        'width'         => '256', 
    ));
*/


AddStylesheet('/css/store_mod.css');

$webcam     = MakeProductTable(array(
        'picture'       => '/images/store/webcam.jpg',
        'title'         => 'Logitech HD Pro Webcam C910',
        'description'   => '',
        'price'         => '$77.99',
        'link'          => 'http://www.amazon.com/Logitech-1080p-Webcam-Pro-C910/dp/B003M2YT96/ref=sr_1_1?ie=UTF8&qid=1304899495&sr=8-1',
        'width'         => '256', /*338*/
    ));

$webcam_eyeball = MakeProductTable(array(
        'picture'       => '/images/store/webcam_eyeball.jpg',
        'title'         => 'Blue Microphones Eyeball',
        'description'   => '',
        'price'         => '$46',
        'link'          => 'http://www.google.com/products/catalog?q=Eyeball+2.0&um=1&ie=UTF-8&tbm=shop&cid=3481693776246281614&sa=X&ei=fCgqTqzfMtTRiALK-PGvAg&ved=0CEIQ8gIwAQ',
        'width'         => '256', /*338*/
    ));
    
$webcam_ICECAM2 = MakeProductTable(array(
        'picture'       => '/images/store/webcam_ICECAM2.jpg',
        'title'         => 'Macally ICECAM2 USB 2.0',
        'description'   => '',
        'price'         => '$21.95',
        'link'          => 'http://www.amazon.com/Macally-ICECAM2-Camera-Built-Microphone/dp/B001AD0QPA',
        'width'         => '256', /*338*/
    ));   
    
    
    


    
    
    
    
$headset    = MakeProductTable(array(
        'picture'       => '/images/store/headset.jpg',
        'title'         => 'Samson AirLine Micro-Wireless',
        'description'   => "",
        'price'         => '$249.00<br />(Use Code: G7DBWT10TS8 to save $55)',
        'link'          => 'http://www.dvestore.com/products/Samson-AirLine-Micro-%252d-Wireless-Earset-System.html',
        'width'         => '256', /*338*/
    ));
    
$dvi        = MakeProductTable(array(
        'picture'       => '/images/store/dvi_cable.jpg',
        'title'         => '6ft HDMI to DVI-D Cable',
        'description'   => 'Connect your webcamera to a DVI-enabled TV set.<br />
                            <ul style="padding-left:10px;">
                                <li>Compatible with all HDTV formats including 720p and 1080i</li>
                                <li>Gold-plated connectors create precise contact for low loss of data</li>
                                <li>Connects components with HDMI and DVI interfaces to each other</li>
                                <li>Shielded for maximum protection from RFI and EMI interference</li>
                            </ul>',
        'price'         => '$6.20',
        'link'          => 'http://www.amazon.com/Cables-Unlimited-PCM-2296-06-HDMI-Cable/dp/B0007MWE1E/ref=sr_1_1?ie=UTF8&s=electronics&qid=1304899159&sr=1-1',
        'width'         => '256',
    ));
    
$ethernet   = MakeProductTable(array(
        'picture'       => '/images/store/ethernet_cable.jpg',
        'title'         => '50ft Ethernet Cable',
        'description'   => 'Connect your computer directly to your internet for faster speeds and higher-quality web video.<br />
                            <ul style="padding-left:10px;">
                                <li>One 50-foot-long RJ45 Cat5e Ethernet patch cable </li>
                                <li>Connects computers and peripherals such as printers to your Local Area Network (LAN)</li>
                                <li>Constructed with four UTP (Unshielded Twisted Pair) cable to minimize noise and interference, and durable outer PVC jacket</li>
                            </ul>',
        'price'         => '$10.95',
        'link'          => 'http://www.amazon.com/AmazonBasics-Cat5e-Ethernet-Patch-Meters/dp/B001TH7GVO/ref=sr_1_1?ie=UTF8&s=electronics&qid=1304899044&sr=8-1',
        'width'         => '256',
    ));
    
$usb        = MakeProductTable(array(
        'picture'       => '/images/store/usb_cable.jpg',
        'title'         => '12ft USB 2.0 Extension Cable',
        'description'   => 'Connect your computer and webcam for the optimal yoga session.<br />
                            <ul style="padding-left:10px;">
                                <li>16-foot-long USB 2.0 Active Extension Cable extends a USB signal</li>
                                <li>Supports data transfer rates up to 480 Mbps</li>
                                <li>Premium double shielding minimizes signal interference</li>
                                <li>Gold-plated connectors with gold-plated copper contacts assure positive connectivity</li>
                                <li>Lifetime product warranty</li>
                            </ul>',
        'price'         => '$17.95',
        'link'          => 'http://www.amazon.com/Tripp-Lite-U026-016-Certified-Extension/dp/B0002D6QJO/ref=sr_1_1?ie=UTF8&s=electronics&qid=1304899410&sr=8-1',
        'width'         => '256',
    ));
/*
$headset_1  = MakeProductTable(array(
        'picture'       => '/images/store/headset_itech_oval.jpg',
        'title'         => 'iTech - i.OVAL 303 (Bluetooth Headset)',
        'description'   => '',
        'price'         => '$59.95',
        'link'          => 'http://www.amazon.com/iTech-i-OVAL-303-Bluetooth-Headset/dp/B00193KDTA/ref=sr_1_1?ie=UTF8&qid=1305055364&sr=8-1',
        'width'         => '256',
    ));
*/
$headset_2  = MakeProductTable(array(
        'picture'       => '/images/store/headset_jabra_extreme.jpg',
        'title'         => 'Jabra Extreme (Bluetooth Headset)',
        'description'   => '',
        'price'         => '$62.15',
        'link'          => 'http://www.amazon.com/gp/product/B0049SKHYS/ref=s9_simh_gw_p107_d0_i1?pf_rd_m=ATVPDKIKX0DER&pf_rd_s=center-2&pf_rd_r=13YJS1EEBBT2X1YEHCCC&pf_rd_t=101&pf_rd_p=470938631&pf_rd_i=507846',
        'width'         => '256',
    ));
    
$headset_3  = MakeProductTable(array(
        'picture'       => '/images/store/headset_jabra_stone.jpg',
        'title'         => 'Jabra STONE2 (Bluetooth Headset)',
        'description'   => '',
        'price'         => '$89.84',
        'link'          => 'http://www.amazon.com/gp/product/B004KPLA9Q/ref=s9_simh_gw_p107_d0_i3?pf_rd_m=ATVPDKIKX0DER&pf_rd_s=center-2&pf_rd_r=13YJS1EEBBT2X1YEHCCC&pf_rd_t=101&pf_rd_p=470938631&pf_rd_i=507846',
        'width'         => '256',
    ));
    
    
    $title_style = 'style="font-size:16px; padding:5px; font-weight:bold; color:#fff;" bgcolor="#9B9A41"';
    
    
$banners = <<<BANNERS
    <tr>
        <td align="left" valign="top" colspan="3" $title_style>PRODUCTS OFFERED BY OUR PARTNERS</td>
    </tr>
    <tr>
        <td colspan="3" align="center" valign="top">
            <br />
            <a href="http://www.yogabodynaturals.com/cmd.php?af=1383925" target="_blank_1"><img src="/images/store/store_banner_yogabody.jpg" alt="Yoga Body" border="0" class="store_banners" /></a>
            <br /><br />
            <a href="http://www.asanagreen.com/?agp=65" target="_blank_2"><img src="/images/store/store_banner_asanagreen.jpg" alt="Asana Green" border="0" class="store_banners" /></a>
            <br /><br />
            <a href="http://www.shareasale.com/r.cfm?B=193307&U=542694&M=24185&urllink=" target="_blank_3"><img src="/images/store/store_banner_barefootyoga.jpg" alt="Barefoot Yoga" border="0" class="store_banners" /></a>
            <br /><br />
            <a href="http://www.shareasale.com/r.cfm?B=175390&U=542694&M=22149&urllink=" target="_blank_4"><img src="/images/store/store_banner_culturalelements.jpg" alt="Cultural Elements" border="0" class="store_banners" /></a>
        </td>
    </tr>
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
    
    $banners
    
    
    </table>
    </div>
TABLE;





AddStyle("
.store_banners {
    border:1px solid #9B9A41;
}
");


$content = <<<CONTENT
<br /><br />


<div style="height: 10px; background-color: #F29358">&nbsp;</div>
<br /><br />

<div style="padding: 0px 50px 0px 50px; font-size:16px;">
<b>Get the most out of your one-on-one yoga sessions</b> with the right gear tested and approved by YogaLiveLink. With a versatile webcam, a quality headset for communicating with your Instructor, and the proper connector cables you'll have a complete setup and be ready to go.
<br /><br />
The gear you'll need: A webcam and long connector cables for placing your webcam and computer for the best viewing of your instructor while you take your private yoga session. We also highly recommend a headset for ease of talking with your instructor.
<br /><br />
<b>Already have your own gear?</b> Great! Test your setup in our testing area by <a href="/test_computer;bypass">clicking here</a>. 
</div>

<br /><br />
<div style="height: 10px; background-color: #F29358">&nbsp;</div>

<br />
CONTENT;
echo $content;

echo $TABLE;



function MakeProductTable($item_array) 
{
    $output = <<<ITEM
    <!-- =============== STORE ITEM =============== -->
    
    <div class="store_item_outter_wrapper" style="width:{$item_array['width']}px;">
    <div class="store_item_inner_wrappe___r" style="background-color:#fff;">

    <a class="store_catalog_product" href="{$item_array['link']}" target="_blank">
        <img class="store_catalog_picture" src="{$item_array['picture']}" border="0" alt="{$item_array['title']}" width="246" height="150" />
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