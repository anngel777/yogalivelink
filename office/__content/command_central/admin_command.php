<?php

    $Ins = new General_ModuleInstructions;
    $Ins->AddInstructions('dev_richard/admin_command');

$Obj = new Profile_AdminCommand();

if ($AJAX) {
    $Obj->AjaxHandle();
} else {
    $Obj->Execute();
}