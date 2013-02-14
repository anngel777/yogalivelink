<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: class_execute.php
    Description: Allow execution of any class within the site
==================================================================================== */

/* =============================================================================================
    Created by MVP, based on code by RAW
============================================================================================= */

$QDATA = GetEncryptQuery('eq');

if (!$QDATA) {
    echo "<h1>No Data to Display!</h1>";
    return;
}

$CLASS_NAME     = ArrayValue($QDATA, 'class');
$PARAMETERS     = ArrayValue($QDATA, 'parameters');

$class_vars_1   = ArrayValue($QDATA, 'v1');
$class_vars_2   = ArrayValue($QDATA, 'v2');
$class_vars_3   = ArrayValue($QDATA, 'v3');
$class_vars_4   = ArrayValue($QDATA, 'v4');
$class_vars_5   = ArrayValue($QDATA, 'v5');

$ID             = ArrayValue($QDATA, 'id');


if (Get('pagetitle')) $PAGE['title'] = Get('pagetitle');


if (Get('dialogWidth')) { $DIALOG_CONTENT_WIDTH = Get('dialogWidth') . 'px'; }

if ($CLASS_NAME) {
    if ($PARAMETERS) {
        $Obj = new $CLASS_NAME($PARAMETERS);
    } else {
        $Obj = new $CLASS_NAME($class_vars_1, $class_vars_2, $class_vars_3, $class_vars_4, $class_vars_5);
    }

    if ($AJAX) {
        $Obj->ExecuteAjax();
    } else {
        $Obj->Execute();
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