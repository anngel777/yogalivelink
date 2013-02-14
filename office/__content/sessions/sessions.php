<?php
    $Ins = new General_ModuleInstructions;
    $Ins->AddInstructions('sessions/sessions');
    
$Obj = new Sessions_Sessions;
$Obj->ListTable();