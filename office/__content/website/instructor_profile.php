<?php
$Obj                    = new Profile_CustomerProfileOverview();
$Obj->WH_ID             = $_SESSION['USER_LOGIN']['LOGIN_RECORD']['wh_id'];
$Obj->Is_Instructor     = true;
$Obj->AddScript();


if ($AJAX) {
    $Obj->AjaxHandle();
} else {
    $content_left       = $Obj->TodaysSessions();
    $content_right      = '';
    $content_right     .= '
        <div class="yogabox_box_footer">
            <center>
            <div class="red left_header lowercase">SPECIAL NOTICES FROM YOGALIVELINK.COM</div>
            </center>
            
            <div style="padding:10px; display:none;">
            <div style="padding:10px; background-color:#FBF8EE;">
                <div style="font-size:16px; color:#000;">
                <b>October Sharing Challenge</b>
                </div>
                <br />
                <div style="font-size:14px; color:#000;">
                We will award $60 to the first 3 Instructors who have 2 customers acquired as a result of a social networking post. Three Instructors Will Win! 
                </div>
                <br />
                <a href="/office/website/instructor_october_challenge" class="link_arrow">VIEW DETAILS</a>
            </div>    
            </div>
            
            <div style="padding:10px;">
            <div style="padding:10px; background-color:#FBF8EE;">
                <div style="font-size:16px; color:#000;">
                <b>Social Media Challenge</b>
                </div>
                <br />
                <div style="font-size:14px; color:#000;">
                The first three instructors to complete the challenge will win the $60 and incentives. The winning customers will be charged only $25 for their session. All customers in will be entered into a drawing to win a free session. The session must be used within 2 months of the drawing.
                </div>
                <br />
                <a href="/office/website/instructor_social_media_challenge" class="link_arrow">VIEW DETAILS</a>
            </div>    
            </div>
            
        </div>
        <br /><br /><br />
    ';
    $content_right     .= $Obj->Execute();
    
    
    
    
    $content_right .= '
    <a id="logo_overlay_trigger" class="iframe" href="https://www.yogalivelink.com/office/website/instructor_social_media_challenge;template=blank">This goes to iframe</a>
    ';
    

    /*
    
    <a id="logo_overlay_trigger" href="#logo_overlay"></a>
    <div style="display:none;"><div id="logo_overlay">
        <center>
        <img alt="YogaLiveLink.com" src="/images/yoga_animated_logo.gif" border="0" />
        </center>
    </div></div>
    
    */
    
}


AddSwap('@@CONTENT_LEFT@@',$content_left);
AddSwap('@@CONTENT_RIGHT@@',$content_right);
AddSwap('@@PAGE_HEADER_TITLE@@','my profile: all about me');



//$forward_page   = (isset($_SESSION['LOGIN_RETURN_URL'])) ? $_SESSION['LOGIN_RETURN_URL'] : 'none provided';
//echo "<br />forward_page ===> $forward_page";



# COOKIE.js -> For Opening logo on first visit to website
# ==================================================================

if (!Session('HIDE_INFO_POPUP')) {
    
    $ENABLE_POPUP = true;
    $_SESSION['HIDE_INFO_POPUP'] = true;
    
    if ($ENABLE_POPUP) {
        
        AddStylesheet("/css/jquery.fancybox-1.3.4.css");
        AddScriptInclude("/jslib/jquery.easing-1.3.pack.js");
        AddScriptInclude("/jslib/jquery.fancybox-1.3.4.pack.js");
        
        $script = <<<SCRIPT
            var overlayContact = function(){
                $("#logo_overlay_trigger").trigger('click');
            };
            setTimeout(overlayContact, 50); //run the script immediately
            
            $("#logo_overlay_trigger").fancybox({
                'autoScale'         : true,
                'autoDimensions'    : false,
                'centerOnScroll'    : true,
                'title'             : '',
                'width'             : 650,
                'height'            : 500
            });
SCRIPT;
        addScriptOnReady($script);
    }
}