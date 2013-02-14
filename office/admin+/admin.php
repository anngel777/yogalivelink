<?php

if (!session_id()) {
    session_start();
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/mvptools.php';

//================= AUTHENTICATION BLOCK =================

require $ROOT . '/config/db_info.php';
require $ROOT . '/classes/Lib/Singleton.php';
require $ROOT . '/classes/Lib/Pdo.php';

$SQL = Lib_Singleton::GetInstance('Lib_Pdo');
$SQL->ConnectMySql($DB_INFO);

include_once "$ROOT/classes/autoload.php";

$USER = new Authentication;
$ADMIN_SUPER_USER = $USER->Super_User;
$ADMIN_NAME       = $USER->User_Name;

if (!$ADMIN_SUPER_USER) exit;

$_SESSION['SITE_ADMIN']['AdminLevel']    = 9;
$_SESSION['SITE_ADMIN']['AdminUsername'] = $ADMIN_NAME;
$_SESSION['SITE_ADMIN']['AdminLoginOK'] = 'ok';
require_once $_SERVER['DOCUMENT_ROOT'].  '/lib/admin/admin_main.php';
