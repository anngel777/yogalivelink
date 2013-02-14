<?php
    $Ins = new General_ModuleInstructions;
    $Ins->AddInstructions('sessions/checklists');
    
$Obj = new Sessions_Checklists;
$Obj->ListTable();