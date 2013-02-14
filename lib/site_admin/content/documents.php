<?php
if ($PAGE['original_name'] == 'upload_document') {
    $ADMIN->UploadDocument();
} else {
    $ADMIN->DocumentManager();
}