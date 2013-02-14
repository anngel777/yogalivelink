<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: April 19, 2012
Last Updated By: Richard Witherspoon

       Filename: instructor_help
    Description: Help center just for instructors
    
    Update Notes:
        April 19, 2012 --> Changed class call from Profile_CustomerProfileHelpCenter() to Profile_HelpCenter()
==================================================================================== */

// ---------- CALL CLASS FOR PROCESSING ----------
$OBJ = new Profile_HelpCenter();
$OBJ->Is_Instructor = true;
$OBJ->AddScript();
$content_right = $OBJ->Execute();
$content_left = $OBJ->ColumnLeft();

// ---------- GET CONTENT FROM DATABASE AND SWAP INTO PAGE ----------
AddSwap('@@CONTENT_LEFT@@',$content_left);
AddSwap('@@CONTENT_RIGHT@@',$content_right);
AddSwap('@@PAGE_HEADER_TITLE@@','help center: get answers to your questions');