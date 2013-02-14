<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: session_process_user
    Description: CLASS :: Sessions_ProcessUser
==================================================================================== */

    #$Ins = new General_ModuleInstructions;
    #$Ins->AddInstructions('sessions/session_process_user');
    
$OBJ = new Sessions_ProcessUser();
$OBJ->sessions_id = Get('sessions_id');
$OBJ->ModifyScriptLocation();

if ($AJAX) {
    $OBJ->AjaxHandle();
} else {

    echo "<br />Current Session: {$OBJ->sessions_id}<br /><br />";

    $OBJ->ShowFormSessionStart();
}


# RESIZE THE CURRENT FRAME TO FIT CONTENTS
# ================================================
$script = <<<SCRIPT
    var dialogNumber = '';
    if (window.frameElement) {
        if (window.frameElement.id.substring(0, 13) == 'appformIframe') {
            dialogNumber = window.frameElement.id.replace('appformIframe', '');
        }
    }
    ResizeIframe();
SCRIPT;
AddScript($script);