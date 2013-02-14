<script type="text/javascript" src="swfobject.js"></script>
<script type="text/javascript" src="swffit.js"></script>
<script type="text/javascript">
    var flashvars = {};
    var params = { allowfullscreen:true };
    swfobject.embedSWF("faces.swf", "flashContent", "100", "100", "10.0.0", "expressInstall.swf", flashvars, params);
    swffit.fit("flashContent", 100, 100);
</script>

<?php
$width  = '700'; //750
$height = '300'; //550
$roomid = $_GET['roomid']; //'test123';
#$roomid = 'test123';
$swf    = 'faces.swf';

$OUTPUT = <<<OUTPUT
<div id="flashContent">
    <h1>Please update your flash plugin.</h1>
    <p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a></p>
</div>
OUTPUT;
echo $OUTPUT;
?>
