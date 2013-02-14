<?php
$Obj = new Website_SEOArticles();

if ($AJAX) {
    $Obj->AjaxHandle();
} else {
    $Obj->Execute();
}