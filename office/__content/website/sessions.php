<?php
$Obj            = new Profile_CustomerProfileSessions();
$Obj->WH_ID     = $_SESSION['USER_LOGIN']['LOGIN_RECORD']['wh_id'];
$Obj->AddScript();


if ($AJAX) {
    $Obj->AjaxHandle();
} else {
    $content_right  = $Obj->Execute();
    $content_left   = $Obj->TodaysSessions();
}


AddSwap('@@CONTENT_LEFT@@',$content_left);
AddSwap('@@CONTENT_RIGHT@@',$content_right);
AddSwap('@@PAGE_HEADER_TITLE@@','my sessions: booked sessions with instructors');