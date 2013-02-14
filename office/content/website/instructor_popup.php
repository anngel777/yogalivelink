<?php
/* ====================================================================================
        Created: March 27, 2012
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: instructor_popup.php
    Description: Content for generic instructor website pop-up
==================================================================================== */



$CONTENT        = ($PAGE['template'] == 'template_inner_1col.html') ? "<br /><br />" : '';
$back_link      = '<a href="/office/website/instructor_profile;z" class="link_arrow red" style="font-size:20px;">BACK TO PROFILE</a>';
 
$ID             = intOnly(Get('id'));
$OBJ_INS_POPUP  = new Website_InstructorPopups();
$CONTENT       .= "<div style='font-size:14px;'>" . $OBJ_INS_POPUP->GetContentFromId($ID) . "</div>";

$content_left = "<br /><br />{$back_link}<br /><br />";


if (Get('type') == 'popup') {
    echo $CONTENT;
} else {
    $CONTENT .= "<br /><br />" . $back_link;
    
    AddSwap('@@CONTENT_LEFT@@', $content_left);
    AddSwap('@@CONTENT_BOTTOM@@', '');
    AddSwap('@@CONTENT_RIGHT@@', $CONTENT);
    AddSwap('@@PAGE_HEADER_TITLE@@','YogaLiveLink.com Instructor Notice');
}
?>