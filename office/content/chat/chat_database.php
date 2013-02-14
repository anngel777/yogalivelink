<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: chat_database.php
    Description: Modify various settings and data for chats - Back-Office module
==================================================================================== */

$dialog_id = Get('DIALOGID');

$menu = "
<h2>DATABASE MODIFICATIONS</h2><br />
<a href='chat_database;menu=1;DIALOGID=$dialog_id'>Chats</a><br />
<a href='chat_database;menu=2;DIALOGID=$dialog_id'>Common Responses</a><br />
<a href='chat_database;menu=3;DIALOGID=$dialog_id'>Settings</a><br />
<hr><br />";
echo $menu;

$menu = Get('menu');
switch($menu) {
    case 1:
        $OBJ = new Chat_TouchpointChats();
        $OBJ->ListTable();
    break;
    case 2:
        $OBJ = new Chat_TouchpointChatCommonResponses();
        $OBJ->ListTable();
    break;
    case 3:
        $OBJ = new Chat_TouchpointChatSettings();
        $OBJ->ListTable();
    break;
}







# RESIZE THE CURRENT FRAME TO FIT CONTENTS
# ================================================
$dialog_id = Get('DIALOGID');
$script = <<<SCRIPT
    var dialogNumber = {$dialog_id};
    ResizeIframe();
SCRIPT;
AddScript($script);