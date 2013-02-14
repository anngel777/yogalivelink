<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: sessions
    Description: CLASS :: Sessions_Sessions
==================================================================================== */

    $Ins = new General_ModuleInstructions;
    $Ins->AddInstructions('sessions/sessions');
    
$Obj = new Sessions_Sessions();
$Obj->ListTable();