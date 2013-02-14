<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: seo.php
    Description: CLASS :: Website_SEO
==================================================================================== */

$Obj = new Website_SEO();

if ($AJAX) {
    $Obj->AjaxHandle();
} else {
    $Obj->Execute();
}