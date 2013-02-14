<?php
$Obj = new Website_WebsiteCommand();

if ($AJAX) {
    $Obj->AjaxHandle();
} else {
    $Obj->Execute();
}