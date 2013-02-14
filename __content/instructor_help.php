<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: instructor_help
    Description: Help center just for instructors
==================================================================================== */

// ---------- CALL CLASS FOR PROCESSING ----------
$OBJ = new Profile_CustomerProfileHelpCenter();
$OBJ->Is_Instructor = true;
$OBJ->AddScript();
$content_right = $OBJ->Execute();
$content_left = $OBJ->ColumnLeft();

// ---------- GET CONTENT FROM DATABASE AND SWAP INTO PAGE ----------
AddSwap('@@CONTENT_LEFT@@',$content_left);
AddSwap('@@CONTENT_RIGHT@@',$content_right);
AddSwap('@@PAGE_HEADER_TITLE@@','help center: get answers to your questions');