<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: session_rating_instructor
    Description: CLASS :: Sessions_RatingsInstructor
==================================================================================== */

    $Ins = new General_ModuleInstructions;
    $Ins->AddInstructions('sessions/session_rating_instructor');
    
$OBJ = new Sessions_RatingsInstructor();
$OBJ->sessions_id = Get('sessions_id');

if ($AJAX) {
    $OBJ->AjaxHandle();
} else {
    $OBJ->AddRecord();
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