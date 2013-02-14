<?php

    #$Ins = new General_ModuleInstructions;
    #$Ins->AddInstructions('dev_richard/instructor_profile');

$Obj = new Profile_InstructorProfile();
$Obj->Default_Tab = (Get('tab')) ? Get('tab') : 0;

if ($AJAX) {
    $Obj->AjaxHandle();    
} else {
    $Obj->Execute();
}