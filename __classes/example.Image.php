<?php
require('class.Image.php');

$image = new Image('image.jpg');
// $image->type = IMAGETYPE_JPEG; // change the output type of the image, using PHP's constants
// $image->width = 400; // aspect resizes based on width
// $image->x = 400; // aspect resizes based on width
// $image->height = 300; // aspect resizes based on height
// $image->y = 300; // aspect resizes based on height
$image->scale(400, 300); // scales the image but maintains the aspect ratio
// $image->scale(400, 300, array('force' => true)); // scales the image and forces the size, can lose the aspect ratio
// $image->cutout(600, 200); // cuts out a section of the image resizes to the minimum possible size unless options are specified
// $image->cutout(600, 200, array('scaleX' => 800, 'scaleY' => 600, 'offsetX' => 0, 'offsetY' => 0)); // cuts out the top left quarter of the image
// $image->whitespace(400, 300); // resizes the image and adds whitespace using the default colour
// $image->whitespace(400, 300, array('color' => '#f00')); // resizes the image and adds whitespace using a custom colour
// $image->whitespace(400, 300, array('image' => 'bg.jpg')); // resizes the image and adds whitespace using an image as the base
// $image->watermark('sample.png'); // watermarks the image using the specified watermark image
// $image->watermark('sample.png', array('repeatX' => false, 'repeatY' => false)); // watermarks the image using the specified watermark image, repeating the image vertically only
// $image->watermark('sample.png', array('repeatX' => false, 'repeatY' => true)); // watermarks the image using the specified watermark image, repeating the image vertically only
// $image->watermark('sample.png', array('repeatX' => true, 'repeatY' => false)); // watermarks the image using the specified watermark image, repeating the image horizontally only
// $image->watermark('sample.png', array('repeatX' => true, 'repeatY' => true)); // watermarks the image using the specified watermark image, repeating the watermark horizontally and vertically
// $image->watermark = 'sample.png'; // watermarks the image using the specified watermark image, using default options
// $filename = $image->write('thumb_image'); // write the image to the specified file, using the default extension
// $filename = $image->write('thumb_image.jpg', array('extension' => false)); // write the image to the specified file, but don't use the default extension
// $image->drawLine(array(0, 0), array($image->currentX, $image->currentY), array('size' => 4, 'color' => '#fff'));
// $image->drawBox(array(0, 230), array(400, 300), array('size' => 4, 'color' => '#000', 'transparency' => 60));
// $image->addText('Testing raw text input!', 30, ($image->currentY - 30), array('color' => '#fff', 'font' => 'ArialBlack', 'transparency' => 30));
$image->output(); // output the image to the browser (also sets the http header)