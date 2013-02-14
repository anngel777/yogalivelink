<?php
$image   = strFrom($QUERY_STRING, 'GIMAGE/');
$info    = strTo($image, '/');
$filename= strFrom($image, '/');

$ERROR_IMAGE      = "$SITE_ROOT/images/error2.gif"; // width="20" height="20" 
$image_class_file = "$ROOT/classes/class.Image.php";

$ERROR = 0;
if (file_exists($image_class_file)) {
    include "$ROOT/classes/class.Image.php";
    list($width, $height) = explode ('x', $info);
    $width   = intOnly($width);
    $height  = intOnly($height);
    $filename= "$ROOT/$filename";
    if (!file_exists($filename) or is_dir($filename)) {
        $ERROR = 1;
    }    
} else {
    $ERROR = 1;
}

if ($ERROR) {
    $filename = $ERROR_IMAGE;
    $width = 20;
    $height = 20;
}

if (file_exists($filename) and !is_dir($filename)) {
    $type  = strToUpper(strFromLast($file, '.'));
    $image = new Image($filename);

    switch ($type) {
        case 'GIF' : $image->type = IMAGETYPE_GIF;
            break;
        case 'JPG' : $image->type = IMAGETYPE_JPEG;
            break;
        case 'PNG' : $image->type = IMAGETYPE_PNG;
    }

    $image->scale($width, $height); // scales the image but maintains the aspect ratio
    $image->output(); // output the image to the browser (also sets the http header)

}
exit;
