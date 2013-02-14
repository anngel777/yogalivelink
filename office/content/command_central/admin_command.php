<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: admin_command
    Description: CLASS :: Profile_AdminCommand
==================================================================================== */

    $Ins = new General_ModuleInstructions;
    $Ins->AddInstructions('dev_richard/admin_command');

$Obj = new Profile_AdminCommand();

if ($AJAX) {
    $Obj->AjaxHandle();
} else {
    $Obj->Execute();
}