<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: chat_system.php
    Description: Open the chat panel in a new window - initialize it so windows open correctly - like chat_panel.php
==================================================================================== */

$OBJ = new Chat_TouchpointChatAdminPanel();
$OBJ->reset_settings = false;
$OBJ->Window_Type = 'newWindow';


if ($AJAX) {
    $OBJ->ProcessAjax();
} else {
    $OBJ->InitializeChatPanel();
}