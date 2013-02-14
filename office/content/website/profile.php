<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: profile
    Description: CLASS :: Profile_ProfileOverview
==================================================================================== */

$Obj            = new Profile_ProfileOverview();
$Obj->WH_ID     = $_SESSION['USER_LOGIN']['LOGIN_RECORD']['wh_id'];
$Obj->AddScript();


if ($AJAX) {
    $Obj->AjaxHandle();
} else {
    $content_right  = $Obj->Execute();
    $content_left   = "<div id='left_column_content'>" . $Obj->TodaysSessions() . "</div>";
}

AddSwap('@@CONTENT_LEFT@@',$content_left);
AddSwap('@@CONTENT_RIGHT@@',$content_right);
AddSwap('@@PAGE_HEADER_TITLE@@','my profile: welcome to YogaLiveLink.com');