<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: instructor_profile_edit.php
    Description: CLASS :: InstructorProfile_Edit
==================================================================================== */

$WH_ID = 666;





$OBJ    = new InstructorProfile_Edit();
$OBJ->wh_id = $WH_ID;



if ($AJAX) {
    $OBJ->ProcessAjax();
} else {
    $Ins = new General_ModuleInstructions;
    $Ins->AddInstructions('instructor_profile/instructor_profile_edit');
    $OBJ->ListTable();
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