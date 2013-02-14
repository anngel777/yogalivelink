<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: image_upload_scaling.php
    Description: CLASS :: Images_UploadingScaling
==================================================================================== */

$OBJ                        = new Images_UploadingScaling();
$OBJ->upload_dir            = (Get('upload_dir')) ? (Get('upload_dir')) : $OBJ->upload_dir;
$OBJ->image_handling_file   = "/office/AJAX/image_upload_crop?upload_dir=$OBJ->upload_dir";
$OBJ->return_window_id      = Get('ret_diag');
$OBJ->return_object_id      = Get('ret_field');
$OBJ->SetPath();


if ($AJAX) {
    $OBJ->AjaxHandle();
} else {
    $OBJ->OutputUploadForm();
}


# RESIZE THE CURRENT FRAME TO FIT CONTENTS
# ================================================
$script = <<<SCRIPT
    var dialogNumber = '';
    if (window.frameElement) {
        if (window.frameElement.id.substring(0, 13) == 'appformIframe') {
            dialogNumber = window.frameElement.id.replace('appformIframe', '');
        }
    }
    ResizeIframe();
SCRIPT;
AddScript($script);