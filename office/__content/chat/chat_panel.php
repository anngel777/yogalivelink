<?php
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