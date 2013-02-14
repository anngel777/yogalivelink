<?php
AddScriptInclude("/office/video/swfobject.js");
AddScriptInclude("/office/video/swffit.js");
AddScript("
    var flashvars = {};
    var params = { allowfullscreen:true };
    swfobject.embedSWF('/office/video/faces.swf', 'flashContent', '750', '550', '10.0.0', '/office/video/expressInstall.swf', flashvars, params);
    swffit.fit('flashContent', 750, 550);
");

AddSwap('@@CONTENT_LEFT@@', '');
AddSwap('@@CONTENT_RIGHT@@', '');
AddSwap('@@PAGE_HEADER_TITLE@@', 'TESTING FLASH VIDEO');


$obj_width  = 515; //1030
$obj_height = 408; //816
$account_id = 'yznmbqr9';
$OBJECT     = <<<OBJECT
<object type="application/x-shockwave-flash" data="http://infxapps.influxis.com/apps/jhoy0ijutn4f8jc5vz10/faces/faces.swf" width="{$obj_width}" height="{$obj_height}">
<param name="movie" value="http://infxapps.influxis.com/apps/jhoy0ijutn4f8jc5vz10/faces/faces.swf"></param>
<param name="allowFullScreen" value="true"></param>
<param name="allowscriptaccess" value="always"></param>
<param name="flashvars" value="rtmp=rtmp://{$account_id}.rtmphost.com/faces"></param>
</object>
OBJECT;

echo $OBJECT;
?>