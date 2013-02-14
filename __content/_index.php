
<div>


<table>
    <tr>
        <td colspan="3" valign="top">
            <div class="index_header">@@INDEX_HEADER@@</div>
            <div class="index_header_sub">@@INDEX_HEADER_SUB@@</div>
            <br />
            <div class="index_content">@@INDEX_CONTENT@@</div>
            
            <center>
            <div>
                
                <a href="http://www.yogalivelink.com/signup">
                <img alt="" src="/images/pricing_current_special.png" border="0" width="70%"/>
                </a>
                <br /><br />
                
                <table width="100%" border="0">
                <tr>
                <td align="center"><a href="/how_yll_works" class="index_button_simple">@@INDEX_BTN_1@@</a></td>
                <td align="center"><a href="/pricing" class="index_button_simple">@@INDEX_BTN_3@@</a></td>
                <td align="center"><a href="/signup" class="index_button_simple">@@INDEX_BTN_2@@</a></td>
                </tr>
                </table>
            
            </div>
            </center>
            
        </td>
        <td align="right" valign="top">
            
            <!-- The Video Thumbnail -->
            <a class="fancyYouTube" id="vid1" href="http://infxapps.influxis.com/apps/jhoy0ijutn4f8jc5vz10/InfluxisPlayer_20110506170520/InfluxisPlayer.html">
                <img alt="View Demo Video" src="/images/template/video_play.gif" border="0" />
            </a>
            
        </td>
    </tr>
    <tr>
        <td colspan="4">
            
            <br /><br />
                
            
                <div class="slideshow">
                    <img src="/images/slider/1.jpg" alt="" border="0" />
                    <img src="/images/slider/2.jpg" alt="" border="0" />
                    <img src="/images/slider/3.jpg" alt="" border="0" />
                    <img src="/images/slider/4.jpg" alt="" border="0" />
                </div> <!-- END of slider -->
            
            
            
            <br /><br />
        </td>
    </tr>
    <tr>
        <td colspan="4">
            @@BOXES@@
        </td>
    </tr>
</table>


</div>



<br /><br />


@@PAGE_CONTENT@@



<?php
$OBJ = new Website_IndexBoxes();
$boxes = $OBJ->GetIndexBoxesAsTable();
addSwapCustom('@@BOXES@@',$boxes);



AddStylesheet("/css/jquery.fancybox-1.3.4.css");
AddScriptInclude("/jslib/jquery.easing-1.3.pack.js");
AddScriptInclude("/jslib/jquery.fancybox-1.3.4.pack.js");


$script = <<<SCRIPT
    $(".fancyYouTube").fancybox({
        'autoScale'     : false,
        'title'         : '',
        'transitionIn'  : 'elastic',
        'transitionOut' : 'elastic',
        'width'         : 680,
        'height'        : 510,
        'type'          : 'iframe'
    });
        
    if (location.hash) {
        $(location.hash).click();
    }
SCRIPT;
AddScriptOnReady($script);







AddScriptInclude("/jslib/jquery.cycle.lite.min.js");

$script = <<<SCRIPT
$('.slideshow').cycle({
    fx: 'fade'
});
SCRIPT;
addScriptOnReady($script);




$style = <<<STYLE
.slideshow { height: 270px; width: 930px; margin: auto }
.slideshow img { padding: 0px; border: 1px solid #C0C455; background-color: #eee; }
STYLE;

AddStyle($style);
