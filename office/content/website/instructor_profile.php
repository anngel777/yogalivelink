<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: March 27, 2012
Last Updated By: Richard Witherspoon

       Filename: instructor_profile.php
    Description: CLASS :: Profile_ProfileOverview

    UPDATES:
    March 27, 2012 - changed content to allow instructor popups to happen
==================================================================================== */

$Obj                    = new Profile_ProfileOverview();
$Obj->WH_ID             = $_SESSION['USER_LOGIN']['LOGIN_RECORD']['wh_id'];
$Obj->Is_Instructor     = true;
$Obj->AddScript();


if ($AJAX) {
    $Obj->AjaxHandle();
} else {
    $content_left       = $Obj->TodaysSessions();
    $content_right      = '';
    
    if (Get('z')) {
        // output the main listing of special offers
        $OBJ_INS_POPUP = new Website_InstructorPopups();
        $content_right .= $OBJ_INS_POPUP->GetContents();
    }
    
    $content_right     .= $Obj->Execute();
}


AddSwap('@@CONTENT_LEFT@@',$content_left);
AddSwap('@@CONTENT_RIGHT@@',$content_right);
AddSwap('@@PAGE_HEADER_TITLE@@','my profile: all about me');