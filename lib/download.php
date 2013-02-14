<?php
include 'mvptools.php';
if (Session('QUERY_KEY')) {
    $ENCRYPT_QUERY_KEY = Session('QUERY_KEY');
}
$array = GetEncryptQuery($QUERY_STRING, false);

$file = ArrayValue($array, 'f');
$ext = strtolower(strFromLast($file, '.'));

// security : allow only specific files
$allowed_extensions = array('mp3', 'pdf', 'doc');

function SetError()
{
    MText('Download Error', 'Error: File could not be downloaded!');    
}

if (!in_array($ext, $allowed_extensions)) {
    SetError();
}


if (substr($file, 0, 1) != '/') {
    $file = '/' . $file;
}


$filename = $ROOT . $file;

if ($file and file_exists($filename)) {

    $filesize = filesize($filename);
    
    $basename = basename($file);

    Header('Content-Type: application/x-octet-stream');
    Header('Content-Transfer-Encoding: binary');
    Header('Content-Length: ' . $filesize);
    Header('Cache-Control: no-cache, must-revalidate'); //HTTP 1.1
    Header('Cache-Control: post-check=0, pre-check=0', false); //HTTP 1.1
    Header('Pragma: no-cache'); //HTTP 1.0
    Header('Content-Description: File');

    Header('Content-Disposition: attachment; filename="' . $basename . '"');
    Header('Title: ' . $basename);

    $rfile = fopen($filename, 'r');
    if (!$rfile) {
        SetError();
    }
    while (!feof($rfile)) {
        echo fread($rfile, 4096);
    }
    fclose($rfile);

} else {
    SetError();
}
