<?php
/*
echo "<h1>CRON :: web\content</h1>";
echo "<br /><br />";
echo "";
echo 'DOCUMENT_ROOT ==> ' . $_SERVER['DOCUMENT_ROOT'];
echo "<br /><br />";
echo "";
$dir = $_SERVER['DOCUMENT_ROOT']. '/lib/page_helper.php';
echo 'dir ==> ' . $dir;
echo "<br /><br />";
echo "";
*/


ini_set('display_errors','1');


#require $_SERVER['DOCUMENT_ROOT']. '/lib/page_helper.php';
$ROOT       = '/mnt/stor3-wc2-dfw1/482052/525307/www.yogalivelink.com/web/content';
$LIB        = $ROOT . '/lib';
$SITE_ROOT  = $ROOT;

//------------- MVP SETUP -----------
require_once "$LIB/page_helper_cron.php";
require_once "$LIB/custom_error.php";


require_once "$LIB/mvptools.php";
require_once "$ROOT/classes/autoload.php";
require_once "$LIB/form_helper.php";
require_once "$LIB/yoga_helper.php";


# DATABASE INITIATION
# =====================================================
require_once $ROOT . '/office/config/db_info.php';
require_once $ROOT . '/classes/Lib/Singleton.php';
require_once $ROOT . '/classes/Lib/Pdo.php';

$SQL = Lib_Singleton::GetInstance('Lib_Pdo');
$SQL->ConnectMySql($DB_INFO);




//echo "running";
$OBJ = new Cron_SessionUnlock();
$OBJ->Execute();
?>