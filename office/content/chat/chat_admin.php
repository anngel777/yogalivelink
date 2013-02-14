<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: chat_admin.php
    Description: Launch a chat window for an adminsitrator/instructor
==================================================================================== */

$OBJ    = new Chat_Chat();
$OBJ->DIALOGID = Get('DIALOGID');

$code       = Get('code');
$id         = Get('id');

if ($AJAX) {
    $OBJ->ProcessAjax();
} else {
    #$Ins = new General_ModuleInstructions;
    #$Ins->AddInstructions('chat/chat_admin');
    
    $OBJ->InitializeChatWindowAdmin($code);
}

#AddDialogClassInfo($OBJ->ClassInfo); # WILL OUTPUT CLASS INFORMATION TO BOTTOM OF DIALOG SCREEN





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