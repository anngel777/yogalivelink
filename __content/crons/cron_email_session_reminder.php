<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: cron_email_session_reminder
    Description: Send emails to customers and instructors reminding of upcoming sesions
==================================================================================== */

$OBJ = new Cron_SessionEmailReminder();
$OBJ->Execute();
?>