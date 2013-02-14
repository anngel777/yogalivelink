<?php
include $_SERVER['DOCUMENT_ROOT'] . '/lib/mvptools.php';
$dir =  PathFromRoot(dirname(str_replace('\\', '/', __FILE__)));

$QUERY_STRING = strFrom($REQUEST_URI, $dir);


//================= WO AUTHENTICATION BLOCK =================

ini_set('url_rewriter.tags', '');
ini_set('session.use_trans_sid', false);
session_start();

include "$ROOT/wo/site_office_config.php";
include "$ROOT/classes/autoload.php";


$USER = new Authentication;
$ADMIN_SUPER_USER = $USER->Super_User;
$ADMIN_NAME       = $USER->User_Name;
unset($USER);

if (!$ADMIN_SUPER_USER) exit;

$_SESSION['SITE_ADMIN']['AdminLevel']    = 9;
$_SESSION['SITE_ADMIN']['AdminUsername'] = $ADMIN_NAME;
$_SESSION['SITE_ADMIN']['AdminLoginOK'] = 'ok';

//================= END WO AUTHENTICATION BLOCK =================



include $LIB . '/site_admin/admin_page.php';