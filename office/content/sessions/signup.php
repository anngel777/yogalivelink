<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: signup.php
    Description: CLASS :: Sessions_Signup
==================================================================================== */

$Obj = new Sessions_Signup();
$Obj->sessions_id = (Get('sid')) ? Get('sid') : $Obj->sessions_id;


$step = (Get('step')) ? Get('step') : 'start';
echo $Obj->HandleStep($step);

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