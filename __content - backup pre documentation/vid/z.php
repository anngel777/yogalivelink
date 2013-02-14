<?php

$room_id = 'test1234';

$faces_loc = '/faces/faces.swf';
$faces_loc = "http://www.yogalivelink.com/faces/faces.swf?roomid={$room_id}";



$OBJECT = <<<OBJECT
    <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="750" height="550">
        <param name="movie" value="{$faces_loc}">
        <param name="quality" value="high">
        <param name='flashvars' value='roomid={$room_id}'>
        <embed src="{$faces_loc}" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="750" height="550" flashvars='roomid={$room_id}'></embed>
    </object>
OBJECT;

//echo $OBJECT;


$OBJECT = <<<OBJECT
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center"><object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="750" height="550">
      <param name="movie" value="http://www.yogalivelink.com/faces/faces.swf">
      <param name="quality" value="high">
	 <param name='flashvars' value='roomid=test123'>
      <embed src="http://www.yogalivelink.com/faces/faces.swf" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="750" height="550" flashvars='roomid=test123'></embed>
    </object></td>
  </tr>
</table>
OBJECT;

echo $OBJECT;
?>