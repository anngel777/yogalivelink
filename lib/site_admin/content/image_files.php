<?php
if ($PAGE['original_name'] == 'upload_image') {
    $ADMIN->ImageUpload();
} else {
    $ADMIN->ImageManager();
}