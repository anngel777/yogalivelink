<?php
// =================== SPONSOR AUTOCOMPELTE FUNCTIONS ==================
#include_once $_SERVER['DOCUMENT_ROOT'] . '/lib/mvptools.php';
#require_once "$ROOT/office/config/db_info.php";
#require_once "$ROOT/classes/Lib/Pdo.php";


#include "$LIB/form_helper.php";
#include "$LIB/custom_error.php";
#include "$ROOT/classes/autoload.php";

include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Allwrittens/Study.php';

$STUDY = new Allwrittens_Study;
$STUDY->SingleQuestion();  //--------- MVP ADDED
?>