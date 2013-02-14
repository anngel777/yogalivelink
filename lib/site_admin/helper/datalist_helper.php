<?php
// $CLASS_PATH must be defined;
if (empty($CLASS_PATH)) {
    Mtext('ERROR', '$CLASS_PATH must be defined');
}
if (!isset($AUTOLOAD_RECORD_ARRAY)) {
    include "$ROOT/classes/autoload.php";
}
if (empty($FormPrefix)) {
    include "$LIB/form_helper.php";
}

AddScriptInclude('/jslib/jquery.tablednd_0_5.js');

include RootPath($CLASS_PATH);

$class = str_replace(array('class.', '.php', '/'), array('', '', '_'), strFrom($CLASS_PATH, 'classes/'));

$DL = new $class;
$DL->ProcessPage();