<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: cron_email_session_reminder
    Description: Checks to see if any admins showing as logged into the chat 
                 tool but haven't had a live update in timeframe meaning they 
                 have closed a browser window without logging out.
==================================================================================== */

$OBJ = new Chat_Chat();
$OBJ->ClearAbandonedAdmin();
?>