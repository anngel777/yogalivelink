<?php
/*
Helper is used within framework to generate a scaled or cropped image
include "$LIB/image_helper.php" within the desired page, such as "gimage"

Resizes images, intelligently sharpens, crops based on width-height ratios, color fills
transparent GIFs and PNGs, and caches variations for optimal performance

color  (optional) background hex color for filling transparent PNGs (e.g. 900 or 16a942)
cropratio (optional) ratio of width to height to crop final image (e.g. 1-1 or 3-2)
nocache  (optional) does not read image from the cache
quality  (optional, 0-100, default: 90) quality of output image

EXAMPLES
          /gimage/200x100/images/image.png    resize to 200w by 100h
          /gimage/200/images/image.png        resize to 200w by 200h
          /gimage/200x200c1-1                 resize to 200w by 200h crop 1:1 ratio
          /gimage/200x200b990000/             background on a PNG with #990000
          /gimage/200x200q100/                quality 100 (0-100 scale)
*/

define('MEMORY_TO_ALLOCATE', '100M');
define('DEFAULT_QUALITY', 90);
$CACHE_DIR = empty($SITECONFIG['cachedir'])? '' : $ROOT . $SITECONFIG['cachedir'];

$QUERY_STRING = urldecode(substr($QUERY_STRING, 1)); // from 1, in case starts with '/'
$image_info = TextBetween('/', '/', $QUERY_STRING); 

// --------- sizes --------
$size_info  = preg_replace('/[cbq](.)+/', '', $image_info);

list($WIDTH, $HEIGHT) = explode ('x', $size_info . 'x');
if (empty($HEIGHT)) {
    $HEIGHT = $WIDTH;
}
$MAX_WIDTH   = intOnly($WIDTH);
$MAX_HEIGHT  = intOnly($HEIGHT);

// ----------quality ----------
if (strpos($image_info, 'q') !== false) {
    $QUALITY = preg_replace('/.*q|b.*|c.*/', '', $image_info);
} else {
    $QUALITY = '';
}

// ---------- background ----------
if (strpos($image_info, 'b') !== false) {
    $BACKGROUND = preg_replace('/.*b|q.*|c.*/', '', $image_info);
    $BACKGROUND  = preg_replace('/[^0-9a-fA-F]/', '', $BACKGROUND);
} else {
    $BACKGROUND = '';
}
// ---------- crop ----------
if (strpos($image_info, 'c') !== false) {
    $CROP = preg_replace('/.*c|q.*|b.*/', '', $image_info);
} else {
    $CROP = '';
}


// ---------- file ----------
$filename = $ROOT . strFrom($QUERY_STRING, $image_info);

//Mtext('IMAGE INFO', "FILE: $filename<br />WIDTH: $WIDTH<br />HEIGHT: $HEIGHT<br />CROP: $CROP<br />COLOR: $COLOR<br />BACKGROUND: $BACKGROUND");

$ERROR_IMAGE = "$LIB/site_admin/images/error2.gif"; // width="20" height="20"
$ERROR = 0;


if (!file_exists($filename) or is_dir($filename)) {
    $filename = $ERROR_IMAGE;
}

// Get the size and MIME type of the requested image
$size = GetImageSize($filename);
$width     = $size[0];
$height    = $size[1];
$mime      = $size['mime'];

// Make sure that the requested file is actually an image
if (substr($mime, 0, 6) != 'image/') {
    $filename = $ERROR_IMAGE;
    $size = GetImageSize($filename);
    $mime = $size['mime'];
}

// If we don't have a max width or max height, OR the image is smaller than both
// we do not want to resize it, so we simply output the original image and exit
if (!$BACKGROUND && $MAX_WIDTH >= $width && $MAX_HEIGHT >= $height) {
    $data = file_get_contents($filename);
    $lastModifiedString = gmdate('D, d M Y H:i:s', filemtime($filename)) . ' GMT';
    $etag    = md5($data);

    doConditionalGet($etag, $lastModifiedString);

    header("Content-type: $mime");
    header('Content-Length: ' . strlen($data));
    echo $data;
    exit();
}

// Ratio cropping
$offsetX = 0;
$offsetY = 0;

if ($CROP) {
    $cropRatio  = explode('-', $CROP);
    if (count($cropRatio) == 2) {
        $ratioComputed  = $width / $height;
        $cropRatioComputed = (float) $cropRatio[0] / (float) $cropRatio[1];

        if ($ratioComputed < $cropRatioComputed) {
            // Image is too tall so we will crop the top and bottom
            $origHeight = $height;
            $height  = $width / $cropRatioComputed;
            $offsetY = ($origHeight - $height) / 2;
        } else if ($ratioComputed > $cropRatioComputed) {
            // Image is too wide so we will crop off the left and right sides
            $origWidth = $width;
            $width  = $height * $cropRatioComputed;
            $offsetX = ($origWidth - $width) / 2;
        }
    }
}

// Setting up the ratios needed for resizing. We will compare these below to determine how to
// resize the image (based on height or based on width)
$xRatio  = $MAX_WIDTH / $width;
$yRatio  = $MAX_HEIGHT / $height;

if ($xRatio * $height < $MAX_HEIGHT) {
    // Resize the image based on width
    $tnHeight = ceil($xRatio * $height);
    $tnWidth = $MAX_WIDTH;
} else {
    // Resize the image based on height
    $tnWidth = ceil($yRatio * $width);
    $tnHeight = $MAX_HEIGHT;
}

// Determine the quality of the output image
$quality = ($QUALITY)? (int) $QUALITY : DEFAULT_QUALITY;

// We store our cached image filenames as a hash of the dimensions and the original filename
$resizedImageSource  = $tnWidth . 'x' . $tnHeight . 'x' . $quality;

if ($BACKGROUND) {
    $resizedImageSource .= 'x' . $BACKGROUND;
}

if (isset($_GET['cropratio'])) {
    $resizedImageSource .= 'x' . (string) $_GET['cropratio'];
}

$resizedImageSource  .= '-' . $filename;

$resizedImage = md5($resizedImageSource);

$resized  = $CACHE_DIR . '/' . $resizedImage;

// Check the modified times of the cached file and the original file.
// If the original file is older than the cached file, then we simply serve up the cached file

if ($CACHE_DIR && file_exists($resized)) {

    $imageModified = filemtime($filename);
    $thumbModified = filemtime($resized);

    if($imageModified < $thumbModified) {
        $data = file_get_contents($resized);

        $lastModifiedString = gmdate('D, d M Y H:i:s', $thumbModified) . ' GMT';
        $etag = md5($data);

        doConditionalGet($etag, $lastModifiedString);

        header("Content-type: $mime");
        header('Content-Length: ' . strlen($data));
        echo $data;
        exit();
    }
}

// We don't want to run out of memory
ini_set('memory_limit', MEMORY_TO_ALLOCATE);

// Set up a blank canvas for our resized image (destination)
$dst = imagecreatetruecolor($tnWidth, $tnHeight);

// Set up the appropriate image handling functions based on the original image's mime type
switch ($size['mime']) {

    case 'image/gif':
        // We will be converting GIFs to PNGs to avoid transparency issues when resizing GIFs
        // This is maybe not the ideal solution, but IE6 can suck it
        $creationFunction = 'ImageCreateFromGif';
        $outputFunction  = 'ImagePng';
        $mime    = 'image/png'; // We need to convert GIFs to PNGs
        $doSharpen   = FALSE;
        $quality   = round(10 - ($quality / 10)); // We are converting the GIF to a PNG and PNG needs a compression level of 0 (no compression) through 9
    break;

    case 'image/x-png':
    case 'image/png':
        $creationFunction = 'ImageCreateFromPng';
        $outputFunction  = 'ImagePng';
        $doSharpen   = FALSE;
        $quality   = round(10 - ($quality / 10)); // PNG needs a compression level of 0 (no compression) through 9
    break;

    default:
        $creationFunction = 'ImageCreateFromJpeg';
        $outputFunction   = 'ImageJpeg';
        $doSharpen   = TRUE;
    break;
}

// Read in the original image
$src = $creationFunction($filename);

if (in_array($size['mime'], array('image/gif', 'image/png'))) {

    if (!$BACKGROUND) {
        // If this is a GIF or a PNG, we need to set up transparency
        imagealphablending($dst, false);
        imagesavealpha($dst, true);

    } else {
        // Fill the background with the specified color for matting purposes
        if ($BACKGROUND[0] == '#')
            $BACKGROUND = substr($BACKGROUND, 1);

        $background = FALSE;

        if (strlen($BACKGROUND) == 6)
            $background = imagecolorallocate($dst, hexdec($BACKGROUND[0].$BACKGROUND[1]), hexdec($BACKGROUND[2].$BACKGROUND[3]), hexdec($BACKGROUND[4].$BACKGROUND[5]));
        else if (strlen($BACKGROUND) == 3)
            $background = imagecolorallocate($dst, hexdec($BACKGROUND[0].$BACKGROUND[0]), hexdec($BACKGROUND[1].$BACKGROUND[1]), hexdec($BACKGROUND[2].$BACKGROUND[2]));
        if ($background)
            imagefill($dst, 0, 0, $background);
    }
}

// Resample the original image into the resized canvas we set up earlier
ImageCopyResampled($dst, $src, 0, 0, $offsetX, $offsetY, $tnWidth, $tnHeight, $width, $height);

if ($doSharpen) {
    // Sharpen the image based on two things:
    // (1) the difference between the original size and the final size
    // (2) the final size
    $sharpness = findSharp($width, $tnWidth);

    $sharpenMatrix = array(
        array(-1, -2, -1),
        array(-2, $sharpness + 12, -2),
        array(-1, -2, -1)
    );
    $divisor  = $sharpness;
    $offset   = 0;
    imageconvolution($dst, $sharpenMatrix, $divisor, $offset);
}


// Write the resized image to the cache
$outputFunction($dst, $resized, $quality);

// Put the data of the resized image into a variable
ob_start();
    $outputFunction($dst, null, $quality);
    $data = ob_get_contents();
ob_end_clean();

// Clean up the memory
ImageDestroy($src);
ImageDestroy($dst);

// See if the browser already has the image
$lastModifiedString = gmdate('D, d M Y H:i:s', filemtime($resized)) . ' GMT';
$etag    = md5($data);

doConditionalGet($etag, $lastModifiedString);

// Send the image to the browser with some delicious headers
header("Content-type: $mime");
header('Content-Length: ' . strlen($data));
echo $data;

//------------------ FUNCTIONS --------------------

function findSharp($orig, $final) // function from Ryan Rud (http://adryrun.com)
{
    $final = $final * (750.0 / $orig);
    $a  = 52;
    $b  = -0.27810650887573124;
    $c  = .00047337278106508946;

    $result = $a + $b * $final + $c * $final * $final;

    return max(round($result), 0);
} // findSharp()

function doConditionalGet($etag, $lastModified)
{
    header("Last-Modified: $lastModified");
    header("ETag: \"{$etag}\"");

    $if_none_match = isset($_SERVER['HTTP_IF_NONE_MATCH']) ?
        stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) :
        false;

    $if_modified_since = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ?
        stripslashes($_SERVER['HTTP_IF_MODIFIED_SINCE']) :
        false;

    if (!$if_modified_since && !$if_none_match)
        return;

    if ($if_none_match && $if_none_match != $etag && $if_none_match != '"' . $etag . '"')
        return; // etag is there but doesn't match

    if ($if_modified_since && $if_modified_since != $lastModified)
        return; // if-modified-since is there but doesn't match

    // Nothing has changed since their last request - serve a 304 and exit
    header('HTTP/1.1 304 Not Modified');
    exit();
} // doConditionalGet()
