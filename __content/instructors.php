<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: instructors
    Description: Show all of the instructors and their profiles
==================================================================================== */

// ---------- CALL CLASS FOR PROCESSING ----------
$OBJ                = new Website_Instructors();
$instructor_menu    = $OBJ->GetInstructorMenu();
$instructor         = $OBJ->HandleInstructor(Get('instructor_id'), Get('eq'));

// ---------- GET CONTENT FROM DATABASE AND SWAP INTO PAGE ----------
AddSwap('@@CONTENT_LEFT@@',$instructor_menu);
AddSwap('@@CONTENT_RIGHT@@',$instructor);
AddSwap('@@PAGE_HEADER_TITLE@@','instructors: discover what our professionals have to offer');