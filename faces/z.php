<?php

$room_id    = (isset($_GET['rid'])) ? $_GET['rid'] : 0;
$faces_loc  = 'faces.swf';

if ($room_id) {
    $OBJECT = <<<OBJECT
        <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="750" height="550">
            <param name="movie" value="{$faces_loc}">
            <param name="quality" value="high">
            <param name='flashvars' value='roomid={$room_id}'>
        <param name="allowFullScreen" value="true"></param>
        <param name="allowscriptaccess" value="always"></param>
            <embed src="{$faces_loc}" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="750" height="550" flashvars='roomid={$room_id}'></embed>
        </object>
OBJECT;
    echo $OBJECT;
} else {
    $output = "<h1>ERROR :: Unable to load room :: No room id provided</h1>";
    echo $output;
}
?>