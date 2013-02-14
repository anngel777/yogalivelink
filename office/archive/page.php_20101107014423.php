<?php
ini_set('display_errors','1');

require $_SERVER['DOCUMENT_ROOT']. '/lib/page_helper.php';
$ROOT = dirname(__FILE__);
$LIB = $ROOT . '/lib';
$SITE_ROOT = $ROOT;

//------------- MVP SETUP -----------
require "$LIB/custom_error.php";
require "$ROOT/wo/site_office_config.php";
require "$ROOT/classes/autoload.php";


#echo "TESTING...<br />" . $PATH . '<br />' . $ROOT. '<br />' . $LIB; exit;

$ENABLE_BYPASS                  = false;
$ENABLE_PDO                     = true;
//$_SESSION['WANT_DB_QUERIES']    = false;




//-----------------------------------------------------------


/*
if ($HTTPS != "on") {
    #echo "<br />NOT SSL";
    //require_once $_SERVER['DOCUMENT_ROOT'].'/lib/page_helper.php';
    //require_once './lib/page_helper.php';

    require_once "$LIB/form_helper.php";
    require_once "$ROOT/classes/autoload.php";
    require_once "$ROOT/classes/class.BaseClass.php";
    if ($ENABLE_PDO) {
        require_once "$ROOT/config/db_info.php";
        require_once "$LIB/pdo_helper.php";
        require_once "$ROOT/classes/Lib/Pdo.php";
    }
} else {
    #echo "<br />SSL'd";
    #$SSL_ROOT = "https://secure.bluehost.com/~afterho2/afterhoursutilities";
    #require_once $_SERVER['DOCUMENT_ROOT'].'/lib/page_helper.php';
    //require_once $SSL_ROOT.'/lib/page_helper.php';
    //require_once './lib/page_helper.php';


    require_once "./lib/form_helper.php";
    require_once "./classes/autoload.php";
    require_once "./classes/class.BaseClass.php";
    if ($ENABLE_PDO) {
        require_once "./config/db_info.php";
        require_once "./lib/pdo_helper.php";
        require_once "./classes/Lib/Pdo.php";
    }

    #echo "<br />_SERVER['DOCUMENT_ROOT'] ===> {$_SERVER['DOCUMENT_ROOT']}";
    #echo "<br />LIB ===> {$LIB}";
    #echo "<br />ROOT ===> {$ROOT}";
}
*/



//$ROOT = $ROOT_ORIG;
$SITE_DIR = $SITECONFIG['sitedir'];
if (AdminRunning()) {
   //echo ArrayToStr($GLOBALS);
   //echo ArrayToStr($SITECONFIG);
   //echo "ROOT=$ROOT";
}

#require_once('helper/i_global_functions.php');
#require_once('helper/i_global.php');


function TrackErrorOnPage($ACTION, $FLAG){
   /*    global $PAGE;
   $page = $PAGE['pagename'];    $action = $ACTION;
   $flag = $FLAG;
   $keys = "`page`, `action`, `flag`";
   $values = "'$page', '$action', '$flag'";
   $insert = db_AddRecord('error_count_tracking', $keys, $values);
   */
}


function SetHeaderImage($IMG) {
    # SET THE HEADER IMAGE - IF IT EXISTS
    # ==========================================================================================
    global $ROOT, $THISPAGE, $SITE_PATH;
    $filename = "{$ROOT}/images/page_unique/{$IMG}";
    if (file_exists($filename)) {
        $HEADER_IMAGE = $IMG;
    } else {
        $HEADER_IMAGE = "master_header.jpg";
    }
    return $HEADER_IMAGE;
}


function SetContentHeader($IMG) {
    # SET THE CONTENT HEADER IMAGE - IF IT EXISTS
    # ==========================================================================================
    global $ROOT, $THISPAGE, $SITE_PATH;
    $filename = "{$ROOT}/images/page_unique/{$IMG}";
    if (file_exists($filename)) {
        $CONTENT_HEADER_IMAGE = $IMG;
    } else {
        $CONTENT_HEADER_IMAGE = "master_content_header.jpg";
    }
    return $CONTENT_HEADER_IMAGE;
}


function SetContentFooter($IMG) {
    # SET THE CONTENT FOOTER IMAGE - IF IT EXISTS
    # ==========================================================================================
    global $ROOT, $THISPAGE, $SITE_PATH;
    $filename = "{$ROOT}/images/page_unique/{$IMG}";
    if (file_exists($filename)) {
        $CONTENT_FOOTER_IMAGE = $IMG;
    } else {
        $CONTENT_FOOTER_IMAGE = "master_content_footer.jpg";
    }
    return $CONTENT_FOOTER_IMAGE;
}


//============CREATE PAGE NAMES============
GetPageName();

if (strpos($PAGE['pagename'], 'gimage/') !== false) {
    include $LIB . '/image_helper.php';
}

//............GET EVENT PAGE DETAILS............
$page_parts             = explode('/', $PAGE['pagename']);
$parts_count            = count($page_parts);
$HEADER_IMAGE           = '';
$CONTENT_HEADER_IMAGE   = '';
$CONTENT_FOOTER_IMAGE   = '';
$BOX_HEADER_IMAGE       = '';
$BOX_BODY_IMAGE         = '';
$pagename_original      = $PAGE['pagename'];
$THISPAGE               = $PAGE['pagename'];
$parts_count            = 4;
$HEADER_IMAGE           = SetHeaderImage("{$THISPAGE}_header.jpg");
$CONTENT_HEADER_IMAGE   = SetContentHeader("{$THISPAGE}_content_header.jpg");
$CONTENT_FOOTER_IMAGE   = SetContentFooter("{$THISPAGE}_content_footer.jpg");

//============WRITE LOG============//
WriteTrackingLog();
//BlockedIPCheck();

//==========GET CONTENT FILE NAMES===========
###echo "<br />contentfilename ===> {$PAGE['contentfilename']}";
###exit();
$REDIRECT = "index";
GetPageFileNames($REDIRECT);
if ($AJAX) {
    include $PAGE['contentfilename'];
    exit;
}

//==========GET PAGE VARIABLES===========
GetTitleVariables();
if (empty($PAGE['template'])) {
    $stdtemplete = 'template.html';
    $PAGE['template'] = ($PAGE['print'])? 'ptemplate.html' : $stdtemplete;
}

//==========GET CONTENT===========
$Under_Development = false;
if ($ENABLE_BYPASS) {
    if (Get('bypass')==true || Session('bypass')==true) {
        $PAGE_STREAM            = file_get_contents("$SITE_ROOT/templates/{$PAGE['template']}");
        $PAGE_CONTENTS          = $PAGE['contentfilename'];
        $_SESSION['bypass']     = true;
    } else {
        $Under_Development      = true;
        $_SESSION['bypass']     = false;
    }
} else {
    $PAGE_STREAM                = file_get_contents("$SITE_ROOT/templates/{$PAGE['template']}");
    $PAGE_CONTENTS              = $PAGE['contentfilename'];
    $_SESSION['bypass']         = true;
}



if ($Under_Development) {
    echo "<br /><br /><center>UNDER DEVELOPMENT</center>";
    ob_end_clean();
} else {
    ob_start();
    include $PAGE_CONTENTS;
    $PAGE_CONTENT = ob_get_contents();
    ob_end_clean();
}


# SWAP CONTENT
# ===========================================================
//$URL                = "$EVENTS_ID/@@PAGENAME@@";
$dbmessages         = Session('WANT_DB_QUERIES')? $SQL->WriteDbQueryText() : '';
$TRANSLATION_AREA   = '';
$LANGUAGE           = 'english';
$DBSWAP             = '';
$HEADER_LEFT        = (isset($HEADER_LEFT)) ? $HEADER_LEFT : '<img src="images/template/header_camera.jpg" width="314" alt="" border="0" />';
$HEADER_RIGHT       = (isset($HEADER_RIGHT)) ? $HEADER_RIGHT : '<img src="images/template/header_picture.jpg" width="666" alt="" border="0" />';

addSwap('@@TRANSLATION_AREA@@',$TRANSLATION_AREA);
addSwap('@@TRANSLATION_SELECT@@','');
addSwap('@@DBMESSAGES@@', $dbmessages);
addSwap('@@PAGENAME@@',$PAGE['pagename']);
//addSwap('@@EVENTPATH@@',$EVENT_PATH);
//addSwap('@@PAGEPATH@@',$PAGE_PATH);
addSwap('@@HEADER_IMAGE@@',$HEADER_IMAGE);
addSwap('@@CONTENT_HEADER_IMAGE@@',$CONTENT_HEADER_IMAGE);
addSwap('@@CONTENT_FOOTER_IMAGE@@',$CONTENT_FOOTER_IMAGE);
addSwap('@@BOX_HEADER_IMAGE@@',$BOX_HEADER_IMAGE);
addSwap('@@BOX_BODY_IMAGE@@',$BOX_BODY_IMAGE);
//addSwap('@@MENU_LINKS@@',$MENU_LINKS);
addSwap('@@LANGUAGE@@',strtoupper($LANGUAGE));
//addSwap('@@FOOTER@@', $footer);
addSwap('@@DBMESSAGES@@', $dbmessages);
addSwap('@@MESSAGES@@', '');
addSwap('@@HEADER_LEFT@@', $HEADER_LEFT);
addSwap('@@HEADER_RIGHT@@', $HEADER_RIGHT);

SwapStdMarkUp();


# FUNCTIONS FOR PERFORMATING TRANSLATION SWAPS
# ===========================================================
# NOTE: use custom variable ($LANG_VAR) for language tracking or it will conflict with other sites.
#
$TRANSLATION            = new Translation_Translations;
#$DBSWAP                = new Translation_DatabaseSwap;
$LANG_VAR               = 'yoga_lang';
$LANGUAGE               = 'English';                                                    # default language
$LANGUAGE               = (Session($LANG_VAR)) ? Session($LANG_VAR) : $LANGUAGE;        # set to SESSION language if present
$LANGUAGE               = Get('lang')? Get('lang') : $LANGUAGE;                         # set to GET language if present
$_SESSION[$LANG_VAR]    = $LANGUAGE;                                                    # set language in session

if (Get('lang_reload')) $TRANSLATION->Reload_Word_Array = true;

#$LANGUAGE_LIST         = (isset($LANGUAGE_LIST)) ? $LANGUAGE_LIST : 'English';
$PAGE_STREAM            = $TRANSLATION->TranslateText($PAGE_STREAM, $LANGUAGE, Get('transid'));
#$PAGE_STREAM           = $DBSWAP->SwapDatabaseText($PAGE_STREAM, $DBSWAP->SwapFromArray, Get('dbid'));


# OUTPUT CONTENT
# ===========================================================
echo $PAGE_STREAM;