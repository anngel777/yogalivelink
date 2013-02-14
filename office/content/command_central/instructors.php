<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: instructors
    Description: CLASS :: Profile_CustomerProfile --> View a list of instructors
==================================================================================== */

$Obj = new Profile_CustomerProfile();

if ($AJAX) {
    $Obj->AjaxHandle();
} else {
    
    $class      = 'Profile_InstructorProfile';
    $win_type   = 'blank'; //'window'
    $list       = $Obj->CreateListingByAlphabet($class, 'instructors', $win_type);
    
    echo $list;
}