<?php
/*
$output = <<<OUTPUT
<div id="form_processing_display" style="display:none; z-index:100; position:absolute; left:0px; top:0px; width:100%; height:100%;">
    <img src="images/loading.gif" alt="loading" border="0" />
</div>
OUTPUT;
echo $output;
*/

    $Ins = new General_ModuleInstructions;
    $Ins->AddInstructions('contact_form');

$Obj            = new Touchpoint_ContactForm();
$Obj->WH_ID     = $_SESSION['USER_LOGIN']['LOGIN_RECORD']['wh_id'];

if ($AJAX) {
    $Obj->AJAXHandle();    
} else {
    $Obj->AddRecord();
}

$dialog_id = Get('DIALOGID');
$script = <<<SCRIPT
    var dialogNumber = {$dialog_id};
    ResizeIframe();
SCRIPT;
AddScript($script);