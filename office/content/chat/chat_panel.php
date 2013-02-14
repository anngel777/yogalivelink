<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: chat_panel.php
    Description: Adminsitrator/Instructor asmin panel - view current chats and launch them
==================================================================================== */

//$OBJ = new Chat_Chat();
$OBJ = new Chat_TouchpointChatAdminPanel();

$OBJ->reset_settings = false;
#$OBJ = Lib_Singleton::GetInstance('Chat_TouchpointChatAdminPanel');

if ($AJAX) {
    $OBJ->ProcessAjax();
} else {

    #$Ins = new General_ModuleInstructions;
    #$Ins->AddInstructions('chat/chat_panel');

    $OBJ->InitializeChatPanel();
}