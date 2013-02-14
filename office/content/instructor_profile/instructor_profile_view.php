<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: instructor_profile_view.php
    Description: CLASS :: InstructorProfile_View
==================================================================================== */

$OBJ    = new InstructorProfile_View();

$WH_ID = (Get('wh_id')) ?  Get('wh_id') : 666;

$mod_type = Get('mod');

if ($AJAX) {
    $OBJ->ProcessAjax();
} else {
    if ($mod_type) {
        $OBJ->InitializeProfileWindowAJAXEDITING($WH_ID);
    } else {
        $Ins = new General_ModuleInstructions;
        $Ins->AddInstructions('instructor_profile/instructor_profile_view');
        $OBJ->InitializeProfileWindow($WH_ID);
    }
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