<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: instructor_profile.php
    Description: CLASS :: Profile_InstructorProfile
==================================================================================== */

$Obj = new Profile_InstructorProfile();
$Obj->Default_Tab = (Get('tab')) ? Get('tab') : 0;

if ($AJAX) {
    $Obj->AjaxHandle();    
} else {
    $Obj->Execute();
}