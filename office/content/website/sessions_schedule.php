<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: sessions_schedule.php
    Description: CLASS :: Sessions_Search
==================================================================================== */

$Obj                    = new Sessions_Search();
$Obj->AddScript();
$Obj->script_location   = "/office/AJAX/website/sessions_schedule";
$Obj->Return_Page       = (Get('retpage')) ? "http://www.yogalivelink.com/" . Get('retpage') : '';

if ($AJAX) {
    $Obj->AjaxHandle();
} else {
    $type       = Get('type');
    switch ($type) {
        case 'instructor':
            $content_right  = $Obj->SearchByInstructor();
            $content_left   = $Obj->SearchByInstructor_MenuLeft();
        break;
        
        case 'date':
        default:
            $content_right  = $Obj->SearchByDate();
            $content_left   = $Obj->SearchByDate_MenuLeft();
        break;
    }
}

AddSwap('@@CONTENT_LEFT@@',$content_left);
AddSwap('@@CONTENT_RIGHT@@',$content_right);
AddSwap('@@PAGE_HEADER_TITLE@@','search for a session');

