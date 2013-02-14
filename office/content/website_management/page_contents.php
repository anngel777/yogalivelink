<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: website_command.php
    Description: CLASS :: Website_PageContents
==================================================================================== */

$Obj = new Website_PageContents();

if ($AJAX) {
    $Obj->AjaxHandle();
} else {
    //$Obj->Execute();
    $Obj->ListTable();
}