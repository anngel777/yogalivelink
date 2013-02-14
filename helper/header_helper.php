<?php
function GetHeaderBlock() {
   global $ROOT;

   AddScript("
$(function() {
    $('#headers').cycle({ 
        fx:    'fade', 
        pause:  4 
    });
});");

    $RESULT = '<div id="headers">';
    $images = GetDirectory("$ROOT/channelevents/images/event_unique", '_header.jpg', 'content,box,master');

    $sizes = array();  // use file size to check if unique
    foreach ($images as $image) {
        $size = filesize("$ROOT/channelevents/images/event_unique/$image");
        if (!in_array($size, $sizes)) {
            $sizes[] = $size;
            $RESULT .= qqn("<img src=`/channelevents/images/event_unique/$image` width=`808` height=`165` alt=`Intel` />");
        }
    }
    $RESULT .= "</div>\n";
    return $RESULT;
}
