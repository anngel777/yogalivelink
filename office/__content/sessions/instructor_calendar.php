<?php
$Obj            = new Sessions_Search();

$Obj->Is_Instructor             = true;
$Obj->Show_Booked_Sessions      = true;
$Obj->Show_Locked_Sessions      = true;
$Obj->Show_Sessions_Before_Today = true;

$Obj->AddScript();
$Obj->script_location = "/office/AJAX/website/sessions_schedule";


if ($AJAX) {
    $Obj->AjaxHandle();
} else {
    //$content_right  = $Obj->SearchByInstructor();

    #$Obj->WH_ID     = $_SESSION['USER_LOGIN']['LOGIN_RECORD']['wh_id'];
    $WH_ID          = $_SESSION['USER_LOGIN']['LOGIN_RECORD']['wh_id'];
    
    $content_right  = $Obj->LoadCalendarScheduleForInstructor_Instructor($WH_ID);
    $content_left   = $Obj->SearchByInstructor_MenuLeft_Instructor();
}


AddSwap('@@CONTENT_LEFT@@',$content_left);
AddSwap('@@CONTENT_RIGHT@@',$content_right);
AddSwap('@@PAGE_HEADER_TITLE@@','my calendar: my schedule and booked sessions');