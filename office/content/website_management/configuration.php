<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: website_command.php
    Description: CLASS :: Website_Configuration
==================================================================================== */

$Obj = new Website_Configuration();

if ($AJAX) {
    $Obj->AjaxHandle();
} else {
    //$Obj->Execute();
    $Obj->ListTable();
}