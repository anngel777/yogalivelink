<?php

$WH_ID  = $_SESSION['USER_LOGIN']['LOGIN_RECORD']['wh_id'];
$Obj    = new Sessions_Analysis($WH_ID);

$Obj->Is_Instructor = true;
//$Obj->AddScript();

if ($AJAX) {
    $Obj->AjaxHandle();
} else {
    $content_right  = "<div id='SESSION_CHART_AREA'>{$GLOBALS['LOADER_FULL_IMG']}</div>";
    $content_left   = $Obj->InstructorAnalysis_MenuLeft();
}


AddSwap('@@CONTENT_LEFT@@',$content_left);
AddSwap('@@CONTENT_RIGHT@@',$content_right);
AddSwap('@@PAGE_HEADER_TITLE@@','my sessions: content goes here');