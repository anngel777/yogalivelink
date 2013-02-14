<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: checklists.php
    Description: CLASS :: Sessions_Checklists
==================================================================================== */

    $Ins = new General_ModuleInstructions;
    $Ins->AddInstructions('sessions/checklists');
    
$Obj = new Sessions_Checklists();
$Obj->ListTable();