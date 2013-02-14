<?php
$OBJ = new Website_Instructors();

$instructor_menu    = $OBJ->GetInstructorMenu();
$instructor         = $OBJ->HandleInstructor(Get('instructor_id'), Get('eq'));

AddSwap('@@CONTENT_LEFT@@',$instructor_menu);
AddSwap('@@CONTENT_RIGHT@@',$instructor);

AddSwap('@@PAGE_HEADER_TITLE@@','instructors: discover what our professionals have to offer');