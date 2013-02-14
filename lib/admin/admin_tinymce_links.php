<?php
// create Javascript Array of Image links for TinyMCE

if ($TINYMCE_IMAGE_LINKS) {

    $RESULT = '';

    $files = GetDirectory(ADMIN_IMAGE_DIR, ADMIN_IMAGE_TYPES);

    $RESULT .= 'var tinyMCEImageList = new Array(';
    
    $count=0;
    foreach ($files as $fi) {
        if ($count) {
            $RESULT .= ',';
        }
        $count++;
        $RESULT .= "['$fi', '".ADMIN_IMAGE_LINK_DIR."/$fi']";
    }
    $RESULT .= ');';
    
    echo $RESULT;
}

if ($TINYMCE_FILE_LINKS) {

    $RESULT = '';
    
    $files = GetDirectory(ADMIN_CONTENT_DIR, ADMIN_CONTENT_STR);

    $RESULT .= 'var tinyMCELinkList = new Array(';
    
    $count=0;
    foreach ($files as $fi) {
        $fi = strTo($fi, ADMIN_CONTENT_STR);
        if ($count) {
            $RESULT .= ',';
        }
        $count++;
        $RESULT .= "['$fi', '".ADMIN_PAGE_DIR."/$fi".ADMIN_PAGE_EXTENSION."']";
      }
    $RESULT .= ');';

    echo $RESULT;
}

?>