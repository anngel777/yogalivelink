<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: website_command.php
    Description: CLASS :: Website_HelpcenterFAQs
==================================================================================== */

$Obj = new Website_HelpcenterFAQs();

if ($AJAX) {
    $Obj->AjaxHandle();
} else {
    //$Obj->Execute();
    $Obj->ListTable();
}