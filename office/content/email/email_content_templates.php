<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: email_content_templates.php
    Description: CLASS :: Email_EmailContentTemplates
==================================================================================== */

$OBJ = new Email_EmailContentTemplates();

if ($AJAX) {
    $OBJ->ProcessAjax();
} else {
    $Ins = new General_ModuleInstructions;
    $Ins->AddInstructions("{$PAGE['pagename']}");

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