<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: index2
    Description: DEVELOPMENT PAGE - Show the website homepage
==================================================================================== */

if (!Get('bypass')) {
    exit();
}

$tag = 'LIVE <span style="font-weight: bold; text-decoration: underline;">PRIVATE</span> ONLINE YOGA SESSIONS';
$tag = (Get('tag') == 1) ? 'LIVE <span style="font-weight: bold; text-decoration: underline;">PERSONAL</span> ONLINE YOGA SESSIONS' : $tag;
$tag = (Get('tag') == 2) ? 'live, private, online yoga sessions ... 1-on-1' : $tag;
$tag = (Get('tag') == 3) ? '<b>1-on-1</b> live, private, online yoga sessions' : $tag;
$tag = (Get('tag') == 4) ? '<b>1-on-1</b> live personal yoga sessions' : $tag;
$tag = (Get('tag') == 5) ? '<b>1-on-1</b> live yoga sessions - just you and your instructor' : $tag;
$tag = (Get('tag') == 6) ? 'Your own personal <b>1-on-1</b> yoga instructor  - LIVE' : $tag;

$tag = (Get('tag') == 7) ? '<b>1-on-1</b> live yoga sessions - Just you and your instructor online' : $tag;
?>



<div>


<table width="100%" border="0">
    <tr>
        
        <td colspan="2" valign="top">
            <div class="index_header"><?php echo $tag; ?></div>
            <div class="index_header_sub">Stretch your boundaries anywhere, anytime</div>
            <div style="display:none;" class="index_header_sub">Stretch your boundaries anywhere, anytime with live yoga online</div>
            <br>
            <div class="index_content" style="display: none;"><b>Live private yoga instruction</b> from certified professionals in the comfort of your own home, at the office -- even when you are on the road.<br><br><b>Create the body and life you want  -</b> Work with an expert to tailor a yoga practice that fits your goals and lifestyle.</div>

        </td>
        
        
    </tr>
    

    
    
    
    <tr>
        <td rowspan="2">
            <div id="slideshow_wrapper">
                <div style="position: relative;" class="slideshow">
                    <img style="position: absolute; top: 0px; left: 0px; z-index: 6; opacity: 0;" class="image" src="/images/slider/slider_lisa.jpg" alt="" border="0">
                    
                    <img style="position: absolute; top: 0px; left: 0px; z-index: 5; opacity: 1;" class="image" src="/images/slider/slider_young_woman.jpg" alt="" border="0">
                    <img style="position: absolute; top: 0px; left: 0px; z-index: 4; opacity: 0;" class="image" src="/images/slider/slider_man.jpg" alt="" border="0">

                    <img style="position: absolute; top: 0px; left: 0px; z-index: 3; opacity: 0;" class="image" src="/images/slider/slider_woman_baby.jpg" alt="" border="0">
                    
                    <img style="position: absolute; top: 0px; left: 0px; z-index: 2; opacity: 0;" class="image" src="/images/slider/slider_old_woman.jpg" alt="" border="0">
                    <img style="position: absolute; top: 0px; left: 0px; z-index: 1; opacity: 0;" class="image" src="/images/slider/slider_african_woman.jpg" alt="" border="0">
                </div> <!-- END of slider -->
            </div>
            
            
                <br>
                <div align="center">
                <a href="http://www.yogalivelink.com/signup">

                <img class="image" src="/images/yoga---below-real-estate.jpg" alt="" border="0"> 
                </a>
                </div>
                            
            
        </td>
        <td align="center" valign="top">
            <a class="fancyYouTube" id="vid1" href="http://infxapps.influxis.com/apps/jhoy0ijutn4f8jc5vz10/InfluxisPlayer_20110506170520/InfluxisPlayer.html">
            <img alt="View Demo Video" src="/images/template/video_play_box.gif" border="0" width="200">
            
            
                <div class="index_header" style="font-size: 12px;">
                Watch our introduction video
                </div>

                            </a>
            
            
            
                <br>
                <a href="http://getfitnow.cascadiaaudio.com/getfitnow209.mp3" target="_radioshow">
                <img class="image" src="/images/yoga---radio-show.jpg" alt="" border="0" width="200"> 
                <div class="index_header" style="font-size: 12px;">
                Radio interview with YogaLiveLink.com
                </div>
                </a>
                            
            
        </td>
    </tr>

    
    <tr>
        <td align="center" valign="bottom">
            <div style="padding-bottom: 10px;"><a href="/how_yll_works" class="index_button_simple">How it Works</a></div>
            <div style="padding-bottom: 10px;"><a href="/pricing" class="index_button_simple">Pricing</a></div>
            <div style="padding-bottom: 0px;"><a href="/signup" class="index_button_simple_red">Get Started!</a></div>
        </td>
    </tr>

    
    
    
    
    <tr>
        <td colspan="2">
            <br /><br />
            @@BOXES@@
        </td>
    </tr>
</table>


<a id="logo_overlay_trigger" href="#logo_overlay"></a>
<div style="display:none;"><div id="logo_overlay">
    <center>
    <img alt="YogaLiveLink.com" src="/images/yoga_animated_logo.gif" border="0" />
    </center>
</div></div>

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






# SLIDESHOW FOR IMAGES
# ==================================================================
AddScriptInclude("/jslib/jquery.cycle.lite.min.js");

$script = <<<SCRIPT
    $('.slideshow').cycle({
        fx: 'fade'
    });
SCRIPT;
addScriptOnReady($script);

$style = <<<STYLE
    #slideshow_wrapper { padding:10px; border:1px solid #C0C455; width: 620px; }
    .slideshow { height: 290px; width: 620px; margin: auto }
    .slideshow .image { height: 290px; width: 620px; margin: auto }
    .slideshow img { padding: 0px; border: 1px solid #C0C455; background-color: #eee; }
STYLE;
AddStyle($style);


/*
# COOKIE.js -> For Opening logo on first visit to website
# ==================================================================
AddScriptInclude("/jslib/jquery.cookie.js");
$script = <<<SCRIPT
    if(!$.cookie('visits')){
        $.cookie('visits',1, {expires: 30});
    }
    $.cookie('visits', (parseInt($.cookie('visits')) + 1), {expires: 7}); //will expire after 7 days
    var overlayContact = function(){
        if(parseInt($.cookie('visits')) < 3000){
            //$('#logo_overlay').show();
            $("#logo_overlay_trigger").trigger('click');

            
            
        }
    };
    setTimeout(overlayContact, 50); //run the script immediately
    
    $("#logo_overlay_trigger").fancybox({
        'autoScale'         : false,
        'autoDimensions'    : false,
        'centerOnScroll'    : true,
        'title'             : '',
        'width'             : 550,
        'height'            : 270
    });
SCRIPT;
addScriptOnReady($script);
*/

