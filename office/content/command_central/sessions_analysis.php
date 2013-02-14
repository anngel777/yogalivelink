<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: sessions_analysis
    Description: CLASS :: Sessions_Analysis
==================================================================================== */

$Obj = new Sessions_Analysis();

if ($AJAX) {
    $Obj->AjaxHandle();
} else {
    $Obj->Execute();
}