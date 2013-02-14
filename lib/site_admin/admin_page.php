<?php
//============================================
//          ADMIN PAGE CONTROLLER
//============================================
ini_set('display_errors','1');
require $_SERVER['DOCUMENT_ROOT'] . '/lib/site_admin/config/siteconfig.php';
require $_SERVER['DOCUMENT_ROOT'] . '/lib/page_helper.php';

require "$LIB/custom_error.php";
require "$LIB/site_admin/classes/class.AdminHelper.php";

$PAGE_ALIAS = array(
    'admin'              => 'index',
    'upload_image'       => 'image_files',
    'image_manager'      => 'image_files',
    'upload_document'    => 'documents',
    'document_manager'   => 'documents',
    'archive_all'        => 'archive_files',
    'archive_file'       => 'archive_files',
    'archive_list'       => 'archive_files',
    'view_site_logs_db'  => 'view_site_logs',
    'view_all_headers'   => 'view_all_content',
    'view_file_content'  => 'view_all_content',
);

//============CREATE PAGE NAMES============
GetPageName();
if ($PAGE['pagename'] == 'HOLDSESSION') {
    exit;
}

$ADMIN = new AdminHelper();

if (strpos($PAGE['pagename'], 'GIMAGE/') !== false) {
    $SITECONFIG['cachedir'] = $ADMIN->Site_Config['cachedir'];
    include $LIB . '/image_helper.php';
}


if (!empty($ADMIN->Site_Config['usehttps']) and empty($HTTPS)) {   
   $HTTPS_URI = 'https://'.$HTTP_HOST . $ADMIN->Admin_File_Query;
   header("Location:$HTTPS_URI");
}

$ADMIN->Authentication();

//==========GET CONTENT FILE NAMES===========

$folder = (dirname($PAGE['pagename']) != 'custom')? $LIB . '/site_admin/content' : $ADMIN->Admin_Files_Dir;

$PAGE['contentfilename'] = $folder . '/' . $PAGE['pagename'] . '.php';
if ($AJAX) { include $PAGE['contentfilename']; exit; }

$PAGE['titlefilename']   = $folder . '/' . $PAGE['pagename'] . '.def';
$PAGE['updated']  = '';
$PAGE['modified'] = '';

if (!file_exists($PAGE['titlefilename'])) {
    MText('Problem', 'Title File Not Found:' . $PAGE['titlefilename']);
}


//==========GET PAGE VARIABLES===========
GetTitleVariables();

if(empty($PAGE['template'])){
    $PAGE['template'] = ($PAGE['pagename'] == 'index')? 'template.html' : 'dialog_template.html';
}


//==========GET CONTENT===========
$PAGE_STREAM = file_get_contents("$ROOT{$SITECONFIG['templatedir']}/{$PAGE['template']}");

ob_start(); include $PAGE['contentfilename']; $PAGE_CONTENT = ob_get_contents(); ob_end_clean();

$ADMIN->AdminSwapMarkUp();

echo $PAGE_STREAM;