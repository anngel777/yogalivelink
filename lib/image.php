<?php
ini_set('display_errors','1');
// file to output an image using encrypted information


include 'mvptools.php';
include "$ROOT/classes/class.Image.php";
$file  = Get('f');
$width = intOnly(Get('w'));
$height= intOnly(Get('h'));

$filename = "$ROOT/$file";

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

    // $filename = $image->write('thumb_image'); // write the image to the specified file, using the default extension
    // $filename = $image->write('thumb_image.jpg', array('extension' => false)); // write the image to the specified file, but don't use the default extension

    $image->output(); // output the image to the browser (also sets the http header)

}