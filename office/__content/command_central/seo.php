<?php
$Obj = new Website_SEO();

if ($AJAX) {
    $Obj->AjaxHandle();
} else {
    $Obj->Execute();
}