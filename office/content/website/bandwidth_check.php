<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: bandwidth_check.php
    Description: DEV - Check users bandwidth
==================================================================================== */

AddSwap('@@CONTENT_LEFT@@', '');
AddSwap('@@CONTENT_RIGHT@@', '');
AddSwap('@@PAGE_HEADER_TITLE@@', 'BANDWIDTH CHECK');


$obj_link = "/office/bandwidthchecker/bwcheck_download.swf";
#$obj_link = "../bandwidthchecker/bwcheck_download.swf";


$obj_width  = 800; //1030
$obj_height = 600; //816
$account_id = 'yznmbqr9';
$OBJECT     = <<<OBJECT
<object type="application/x-shockwave-flash" data="{$obj_link}" width="{$obj_width}" height="{$obj_height}">
<param name="movie" value="{$obj_link}"></param>
<param name="allowFullScreen" value="true"></param>
<param name="allowscriptaccess" value="always"></param>

</object>
OBJECT;

//echo $OBJECT;


$OBJECT2 = <<<OBJECT2


    <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="350" height="300">
    <param name="movie" value="{$obj_link}">
    <param name=quality value=high>
    <embed src="{$obj_link}" quality=high pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" width="350" height="300"></embed>
    </object>


    <object width="550" height="400">
    <param name="movie" value="{$obj_link}">
    <embed src="{$obj_link}" width="550" height="400">
    </embed>
    </object>
OBJECT2;
echo $OBJECT2;


/*
<script type="text/javascript">
//AC_FL_RunContent( 'codebase','http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0','width','350','height','300','src','{$obj_link}','quality','high','pluginspage','http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash','movie','{$obj_link}' ); //end AC code
</script>
<noscript>
</noscript>
*/
?>



