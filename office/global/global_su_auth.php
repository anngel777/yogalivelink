<?php
if (!session_id()) {
    if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-42000, '/');
    }
    session_start();
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/mvptools.php';

//================= AUTHENTICATION BLOCK =================

$ADMIN_SUPER_USER = Session('ADMIN_LOGIN_SUPER_USER');

include_once "$ROOT/office/config/db_info.php";
include_once "$LIB/dbi_helper.php";
include_once "$LIB/form_helper.php";


if (!$ADMIN_SUPER_USER) {
    include "$ROOT/office/helper/auth_helper.php";
}

if (!$ADMIN_SUPER_USER) exit;

//include_once "$ROOT/classes/class.BaseClass.php";

$_SESSION['AdminLevel']    = 9;
$_SESSION['AdminUsername'] = Session('ADMIN_LOGIN_NAME');
$_SESSION['AdminLoginOK'] = 'ok';