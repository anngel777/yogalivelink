<?php
$OBJ    = new Sessions_InstructorScheduling();


if ($AJAX) {
    $OBJ->AjaxHandle();
} else {
    $content_right = $OBJ->Execute();
    $content_left = $OBJ->Instructions();
}


AddSwap('@@CONTENT_LEFT@@',$content_left);
AddSwap('@@CONTENT_RIGHT@@',$content_right);
AddSwap('@@PAGE_HEADER_TITLE@@','scheduling: create your yoga session calendar');