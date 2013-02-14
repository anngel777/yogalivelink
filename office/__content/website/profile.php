<?php
$Obj            = new Profile_CustomerProfileOverview();
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



//$forward_page   = (isset($_SESSION['LOGIN_RETURN_URL'])) ? $_SESSION['LOGIN_RETURN_URL'] : 'none provided';
//echo "<br />forward_page ===> $forward_page";