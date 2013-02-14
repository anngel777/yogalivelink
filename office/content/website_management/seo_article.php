<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: seo_article.php
    Description: CLASS :: Website_SEOArticles
==================================================================================== */

$Obj = new Website_SEOArticles();

if ($AJAX) {
    $Obj->AjaxHandle();
} else {
    $Obj->Execute();
}