<?php
$width      = '670'; //670   //700  //750
$height     = '500'; //300  //550
$roomid     = $_GET['roomid']; //'test123';
$swf        = 'faces.swf';

$OUTPUT     = <<<OUTPUT
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td align="left">
            <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="https://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,0,0,0" width="{$width}" height="{$height}">
            <param name="movie" value="{$swf}">
            <param name="quality" value="high">
            <param name="allowfullscreen" value="true">
            <param name='flashvars' value='roomid={$roomid}'>
            <embed src="{$swf}" allowfullscreen="true" quality="high" pluginspage="https://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="{$width}" height="{$height}" flashvars='roomid={$roomid}'></embed>
        </object>
        </td>
    </tr>
</table>
<div style='display:none;'>
    /office/faces/dynamic_1/faces.php
    roomid ===> $roomid
</div>
OUTPUT;

echo $OUTPUT;
?>

<style type="text/css">
body {
    padding:0px;
    margin:0px;
    background-color:#F6F0E1;
}
</style>