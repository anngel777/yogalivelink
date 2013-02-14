<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: z.php
    Description: UNKNOWN DEV FILE
==================================================================================== */

$PAGE['template'] = 'blank.html';

$faces_loc = '/office/faces/faces.swf';
$room_id = 'test1234';

$OBJECT = <<<OBJECT
    <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="750" height="550">
        <param name="movie" value="{$faces_loc}">
        <param name="quality" value="high">
        <param name='flashvars' value='roomid={$room_id}'>
        <embed src="{$faces_loc}" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="750" height="550" flashvars='roomid={$room_id}'></embed>
    </object>
OBJECT;

echo $OBJECT;
?>

