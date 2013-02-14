<?php
echo "<h1>CRON :: cron directory</h1>\n\n";
echo "";
echo "";
echo 'DOCUMENT_ROOT ==> ' . $_SERVER['DOCUMENT_ROOT'];
echo "";
echo "";
$dir = '../' . $_SERVER['DOCUMENT_ROOT']. '/web/content/lib/page_helper.php';
echo 'dir ==> ' . $dir;
echo "";
echo "";

ini_set('display_errors','1');

require '../' . $_SERVER['DOCUMENT_ROOT']. '/web/content/lib/page_helper.php';
$ROOT = dirname(__FILE__) . '../web/content';
$LIB = $ROOT . '/lib';
$SITE_ROOT = $ROOT;

//------------- MVP SETUP -----------
require "$LIB/custom_error.php";
#require "$ROOT/wo/site_office_config.php";
require "$ROOT/classes/autoload.php";
#require "$LIB/form_helper.php";
require "$LIB/yoga_helper.php";


$OBJ = new Cron_SessionUnlock();
$OBJ->Execute();


$OBJ = new Cron_SessionEmailReminder();
$OBJ->Execute();
?>