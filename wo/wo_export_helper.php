<?php
//ini_set('display_errors','1');
if (empty($AJAX_HELPER_PROCESSED)) {
    include 'wo_ajax_helper.php';
}

if (!$USER->Login_Ok) {
    MText('Login Required', 'Login Required');
}

$idx = Get('idx');
if (Post("EXPORT_EQ$idx")) {
    $t = Post("EXPORT_TYPE$idx");
    $DATA = GetEncryptQuery(Post("EXPORT_EQ$idx"), false); 
    $QUERY      = $DATA['query'];
    $CLASS_NAME = $DATA['class'];
} else {
    $q = DecryptStringHex(Get('q'), 'dbquery');
    $t = Get('t');
    $QUERY = Session($q);    
    $parts = explode('__', $q);
    $CLASS_NAME = ArrayValue($parts, 1);    
}

if (empty($QUERY)) {
    echo "<h1>NO QUERY DEFINED!</h1>";
    return;
}


include ClassFileFromName($CLASS_NAME);

$TableObj = new $CLASS_NAME;
$TableObj->ExportCsv($QUERY, '', $t=='xml');
