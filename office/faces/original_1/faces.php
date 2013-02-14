
<?php
$width  = '700'; //750
$height = '300'; //550
$roomid = $_GET['roomid']; //'test123';
#$roomid = 'test123';
$swf    = 'faces.swf';

$OUTPUT = <<<OUTPUT
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td align="center">
            <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="{$width}" height="{$height}">
            <param name="movie" value="{$swf}">
            <param name="quality" value="high">
            <param name='flashvars' value='roomid={$roomid}'>
            <embed src="{$swf}" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="{$width}" height="{$height}" flashvars='roomid={$roomid}'></embed>
        </object>
        </td>
    </tr>
</table>
OUTPUT;
echo $OUTPUT;
?>
