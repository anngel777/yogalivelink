<?php
/* ====================================================================================
        Created: March 27, 2012
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: instructor_popup_content.php
    Description: CLASS :: Website_InstructorPopups
==================================================================================== */

$Obj = new Website_InstructorPopups();

if ($AJAX) {
    $Obj->AjaxHandle();
} else {
    $Obj->ListTable();
}