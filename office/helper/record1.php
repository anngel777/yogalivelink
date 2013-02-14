<?php
ini_set('display_errors','1');
include $_SERVER['DOCUMENT_ROOT'] . '/lib/mvptools.php';
include "$ROOT/wo/site_office_config.php";
include "$LIB/dbi_helper.php";
include "$LIB/form_helper.php";
include "$LIB/custom_error.php";
include "$ROOT/classes/autoload.php";
include "$ROOT/classes/Lib/BaseClass.php";

function AddFlash(){}

$OBJ = new Lib_AdminUsers;
$OBJ->EditRecord(1);

