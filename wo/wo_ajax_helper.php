<?php
if (!session_id()) session_start();

if (empty($AJAX_HELPER_PROCESSED)) {

    require $_SERVER['DOCUMENT_ROOT'] . '/lib/mvptools.php';

// -------- authentication check --------

    require "$ROOT/wo/site_office_config.php";
    //require "$LIB/pdo_helper.php";
    require "$LIB/form_helper.php";
    require "$ROOT/classes/autoload.php";

    require ClassFileFromName('Lib_BaseClass');
    require ClassFileFromName('Lib_Authentication');
    $USER = new Authentication(true);
}

$DATA = array();
if(Post('data')) {
    $var_pairs = explode('&', $_POST['data']);

    foreach ($var_pairs as $field) {
        list($key,$value) = explode('=', $field);
        $key              = urldecode($key);
        $value            = urldecode($value);
        $DATA[$key]       = $value;
    }
}

function GetData($key)
{
    global $DATA;
    return (isset($DATA[$key]))? $DATA[$key] : '';
}

function EchoData()
{
    global $DATA;
    if ($DATA) {
        foreach ($DATA as $key => $value) {
            echo "$key|$value\n";
        }
    }
}