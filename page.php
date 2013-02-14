<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: July 16, 2012
Last Updated By: Richard Witherspoon

       Filename: /page.php
    Description: Controls what page content and templates are output to the screen

  Upgrade Notes:
                04-12-2012  -> Added include file lib_sanitize.php
                07-16-2012  -> Re-ordered where some functions located at foot of page
==================================================================================== */


# ------------- INITIAL CONFIGURATION -------------
ini_set('display_errors','1');
require $_SERVER['DOCUMENT_ROOT']. '/lib/page_helper.php';
$ROOT       = dirname(__FILE__);
$LIB        = $ROOT . '/lib';
$SITE_ROOT  = $ROOT;


# ------------- MVP SETUP -------------
require "$LIB/custom_error.php";
require "$ROOT/wo/site_office_config.php";
require "$ROOT/classes/autoload.php";
require "$LIB/form_helper.php";
require "$LIB/yoga_helper.php";
require "$LIB/lib_sanitize.php";

$ENABLE_BYPASS                  = false;
$ENABLE_PDO                     = true;
$ENABLE_SEO                     = true;
$ENABLE_POPUP                   = true;
$ENABLE_SANITIZE                = true;

$_GET['beta']                   = true;


/* -----------------------------------------------------------
----- Notes -----

;noswap=true        Will turn OFF swapping out unused content swaps



----------------------------------------------------------- */

 
    
/*
echo "<br />SESSION ARRAYS<br />";
echo ArrayToStr($_SESSION);
echo "<br />========================<br />";
*/

//-----------------------------------------------------------







//============ INITIATE TRANSLATION CLASS ============
$TRANSLATION = new Translation_Translations();


//============ CREATE PAGE NAMES ============
GetPageName();



# ========== SANITIZE ALL $_GET and $_POST ==========
if ($ENABLE_SANITIZE) {
    SanitizePost();
    SanitizeGet();
    SanitizeServer();
    SanitizePage();
}



//============ SPECIAL PAGE NAMING WHERE A NEW PAGE IS ACTING AS CONTROLLER (i.e. Blogs) ============
if (substr($PAGE['pagename'], 0, 6) == 'store/' || $PAGE['pagename'] == 'store') {
    
    list($PAGENAME, $CID, $PRODUCT_ID) = explode('/', $PAGE['pagename'] . '//');
    $CID = intOnly($CID);
    $PRODUCT_ID = intOnly($PRODUCT_ID);
    Page_SetPageName('store');
    
} elseif (substr($PAGE['pagename'], 0, 5) == 'blog/' || $PAGE['pagename'] == 'blog') {
    
    list($PAGENAME, $BID, $TITLE) = explode('/', $PAGE['pagename'] . '//');
    $BID = intOnly($BID);
    Page_SetPageName('blog');
    
}


if (strpos($PAGE['pagename'], 'gimage/') !== false) {
    include $LIB . '/image_helper.php';
}



//============WRITE LOG============//
WriteTrackingLog();
//BlockedIPCheck();



//============ GET PAGE PARTS ============
$page_parts             = explode('/', $PAGE['pagename']);
$parts_count            = count($page_parts);
$pagename_original      = $PAGE['pagename'];
$THISPAGE               = $PAGE['pagename'];



//========== HANDLE PAGE PARTS FOR SEO URLS ===========
switch($page_parts[0]) {
    case 'instructors':
        $PAGE['pagename']   = $page_parts[0];
        $PAGE['pagelink']   = '/' . $page_parts[0];
        $name               = (isset($page_parts[1])) ? str_replace('_', ' ', $page_parts[1]) : '';
        $_GET['eq']         = (isset($page_parts[2])) ? $page_parts[2] : '';
    break;
    
    case 'article':
        $PAGE['pagename']   = $page_parts[0];       // Set the actual page name removing custom url stuff from stream
        $PAGE['pagelink']   = '/' . $page_parts[0];
        $name               = (isset($page_parts[1])) ? str_replace('_', ' ', $page_parts[1]) : '';
        $_GET['eq']         = (isset($page_parts[2])) ? $page_parts[2] : '';
    break;
    
    case 'help':
    case 'help_seo':
    case 'instructor_help':
        $PAGE['pagename']   = $page_parts[0];       // Set the actual page name removing custom url stuff from stream
        $PAGE['pagelink']   = '/' . $page_parts[0];
        $name               = (isset($page_parts[1])) ? str_replace('_', ' ', $page_parts[1]) : '';
        $_GET['eq']         = (isset($page_parts[2])) ? $page_parts[2] : '';
    break;
} //end switch



//==========GET SITE CONFIGURATION===========
$CONFIG = new Website_Configuration();
$CONFIG->GetWebsiteConfiguration();
$CONFIG->GetSiteConfigSwaps();



//========== GET CONTENT FILE NAMES ===========
GetPageFileNames('/', '404.php');
if ($AJAX) {
    include $PAGE['contentfilename'];
    exit;
}



//========== GET PAGE VARIABLES ===========
GetTitleVariables();
if (empty($PAGE['template'])) {
    $stdtemplete = 'template.html';
    $PAGE['template'] = ($PAGE['print'])? 'ptemplate.html' : $stdtemplete;
}

# override the page title - if that's been setup
$PAGE['title'] = (isset($override_page_title)) ? $override_page_title : $PAGE['title'];



//========== GET PAGE CONTENT ===========
$Under_Development = false;
if ($ENABLE_BYPASS) {

    $bypass = false;
    $bypass = (Get('bypass')==true || Session('bypass')==true) ? true : $bypass;
    $bypass = (Get('bypass')=='off') ? false : $bypass;

    if ($bypass) {
        $_SESSION['bypass']     = true;
    } else {
        $Under_Development      = true;
        $_SESSION['bypass']     = false;
    }
} else {
    $_SESSION['bypass']         = true;
}

$is_superuser           = (isset($_SESSION['USER_LOGIN']['LOGIN_RECORD']['super_user'])) ? $_SESSION['USER_LOGIN']['LOGIN_RECORD']['super_user'] : false;
$is_administrator       = (isset($_SESSION['USER_LOGIN']['LOGIN_RECORD']['type_administrator'])) ? $_SESSION['USER_LOGIN']['LOGIN_RECORD']['type_administrator'] : false;

if ($is_superuser || $is_administrator) {
    $instructor_bypass = false;
}



# ========================================================================
# if instructor is in limited mode - redirect to limited pages
$instructor_account_limited_allowed_pages = array('instructor_help', 'terms_and_conditions', 'liability_waiver', 'privacy_policy', 'how_yll_works');
if (Session('instructor_account_limited') && !$is_superuser && (!in_array($PAGE['pagename'], $instructor_account_limited_allowed_pages))) {
    header('Location: http://www.yogalivelink.com/office/website/instructor_no_access;bypass');
}
# ========================================================================
# ======================= TEMP INSTRUCTOR BYPASS =====================
$i_bypass = false;
$i_bypass = (Get('IN45KL')==true || Session('IN45KL')==true) ? true : $i_bypass;
$i_bypass = (Get('IN45KL')=='off') ? false : $i_bypass;
    
if ($i_bypass) {
    $instructor_bypass      = true;
    $Under_Development      = false;
    $_SESSION['IN45KL']     = true;
} else {
    $instructor_bypass      = false;
    $Under_Development      = $Under_Development;
    $_SESSION['IN45KL']     = false;
}
# ========================================================================
# ========================================================================



if ($Under_Development) {
    #echo arraytostr($PAGE);
    #echo "<br /><br /><center>UNDER DEVELOPMENT</center>";
    #ob_end_clean();
    
    $PAGE['template']           = "splash.html";
    $PAGE['titlefilename']      = "/mnt/stor3-wc2-dfw1/482052/525307/www.yogalivelink.com/web/content/content/splash.def";
    $PAGE['contentfilename']    = "/mnt/stor3-wc2-dfw1/482052/525307/www.yogalivelink.com/web/content/content/splash.php";

    
} else if ($instructor_bypass) {

    # only allow instructor to see instructor signup page
    # ========================================================================================
    $PAGE['template']           = 'instructor.html';
    $PAGE['titlefilename']      = "/mnt/stor3-wc2-dfw1/482052/525307/www.yogalivelink.com/web/content/content/signup_instructor.def";
    $PAGE['contentfilename']    = "/mnt/stor3-wc2-dfw1/482052/525307/www.yogalivelink.com/web/content/content/signup_instructor.php";
    
}



if (Get('therapy')) {
    $PAGE['template']           = 'instructor.html';
    $PAGE['titlefilename']      = "/mnt/stor3-wc2-dfw1/482052/525307/www.yogalivelink.com/web/content/content/therapy.def";
    $PAGE['contentfilename']    = "/mnt/stor3-wc2-dfw1/482052/525307/www.yogalivelink.com/web/content/content/therapy.php";
}

if (Get('standard')) {
    $PAGE['template']           = 'instructor.html';
    $PAGE['titlefilename']      = "/mnt/stor3-wc2-dfw1/482052/525307/www.yogalivelink.com/web/content/content/standard.def";
    $PAGE['contentfilename']    = "/mnt/stor3-wc2-dfw1/482052/525307/www.yogalivelink.com/web/content/content/standard.php";
}



if (!Get('template')) {
    // only run this if were not passing in a specific template
    if ($PAGE['pagename'] == 'index') {
        $_GET['template'] = 'new';
    } else if ($PAGE['pagename'] == 'index_new') {
        $_GET['template'] = 'new';
    } else if ($PAGE['pagename'] == 'signup') {
        $_GET['template'] = 'new_inner_1col';
    } else if ($PAGE['pagename'] == 'signup_instructor') {
        $_GET['template'] = 'new_inner_1col';
    } else if ($PAGE['pagename'] == 'blog') {
        $_GET['template'] = 'new_inner_1col';
    //} else if ($PAGE['pagename'] == 'class_execute') {
    //    $_GET['template'] = 'new_inner_1col';
    } else {
        $_GET['template'] = 'new_inner';
    }
}

# HANDLE OTHER PAGE TEMPLATES
# =========================================

//echo Get('template');

$PAGE['template']   = ((Get('template') == 'launch'))           ? 'launch_template.html' : $PAGE['template'];
$PAGE['template']   = ((Get('template') == 'overlay'))          ? 'overlay_template.html' : $PAGE['template'];
$PAGE['template']   = ((Get('template') == 'blank'))            ? 'blank.html' : $PAGE['template'];
$PAGE['template']   = ((Get('template') == 'none'))             ? 'none.html' : $PAGE['template'];
$PAGE['template']   = ((Get('template') == '2'))                ? 'template2.html' : $PAGE['template'];

$PAGE['template']   = ((Get('template') == 'new'))              ? 'template.html' : $PAGE['template'];
$PAGE['template']   = ((Get('template') == 'new_inner'))        ? 'template_inner.html' : $PAGE['template'];
$PAGE['template']   = ((Get('template') == 'new_inner_1col'))   ? 'template_inner_1col.html' : $PAGE['template'];

/*
$PAGE['template']   = ((Get('template') == 'new'))              ? 'template_new.html' : $PAGE['template'];
$PAGE['template']   = ((Get('template') == 'new_inner'))        ? 'template_new_inner.html' : $PAGE['template'];
$PAGE['template']   = ((Get('template') == 'new_inner_1col'))   ? 'template_new_inner_1col.html' : $PAGE['template'];
*/










# OUTPUT THE CONTENT STREAM
# =========================================
$PAGE_STREAM                = file_get_contents("$SITE_ROOT/templates/{$PAGE['template']}");
$PAGE_CONTENTS              = $PAGE['contentfilename'];
ob_start();
include $PAGE_CONTENTS;
$PAGE_CONTENT = ob_get_contents();
ob_end_clean();



# GET CONTENT FROM DATABASE
# ===========================================================
$OBJ_CONTENT = new Website_PageContents();
$OBJ_CONTENT->Show_Query                = Get('query');
$OBJ_CONTENT->Show_Array                = Get('array');
$OBJ_CONTENT->Show_Identifier           = Get('identifier');
$OBJ_CONTENT->Show_Identifier_Only      = Get('identifieronly');
$OBJ_CONTENT->Page_Name                 = $PAGE['pagename'];
$OBJ_CONTENT->GetContents();


# SWAP CONTENT
# ===========================================================
$dbmessages         = Session('WANT_DB_QUERIES')? $SQL->WriteDbQueryText() : '';
$TRANSLATION_AREA   = '';
$LANGUAGE           = 'english';
$DBSWAP             = '';



addSwapCustom('@@TRANSLATION_AREA@@',$TRANSLATION_AREA);
addSwapCustom('@@TRANSLATION_SELECT@@','');
addSwapCustom('@@DBMESSAGES@@', $dbmessages);
addSwapCustom('@@PAGENAME@@',$PAGE['pagename']);
addSwap('@@LANGUAGE@@',strtoupper($LANGUAGE));
addSwapCustom('@@DBMESSAGES@@', $dbmessages);
addSwapCustom('@@MESSAGES@@', '');











# SEO SWAPS
# ===========================================================
if ($ENABLE_SEO) {
    $OBJ_SEO                    = new Website_SEO();
    $OBJ_SEO->Page_Name         = $PAGE['pagename'];
    $OBJ_SEO->GetMetaAndSwapContents();
}







//================= GOOGLE ANALYTICS ========================
AddScript("
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-23675217-1']);
    _gaq.push(['_trackPageview']);
    
    (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
");






SwapStdMarkUp();

# ---------- BETA SWAP ----------
if (Get('beta')) {
    #$find       = '<img src="/office/images/yogalivelinks_header.jpg" alt="" height="50px" width="200px" border="0" />';
    #$replace    = '<img src="/office/images/yogalivelinks_header-beta.jpg" alt="" border="0" height="50px" width="200px">';
    #addSwap($find, $replace);
    
    addSwap('@@BETA@@', '<div id="beta_left"></div><div id="beta_right" style="display:none;"></div>');
    SwapStdMarkUp();
    
} else {
    addSwap('@@BETA@@', '');
    SwapStdMarkUp();
}

# FUNCTIONS FOR PERFORMATING TRANSLATION SWAPS
# ===========================================================
# NOTE: use custom variable ($LANG_VAR) for language tracking or it will conflict with other sites.
#
#$TRANSLATION            = new Translation_Translations; // DECLARED ABOVE FOR SOME REASON
#$DBSWAP                = new Translation_DatabaseSwap;
$LANG_VAR               = 'yoga_lang_main';
$LANGUAGE               = 'english';                                                    # default language
$LANGUAGE               = (Session($LANG_VAR)) ? Session($LANG_VAR) : $LANGUAGE;        # set to SESSION language if present
$LANGUAGE               = Get('lang')? Get('lang') : $LANGUAGE;                         # set to GET language if present
$_SESSION[$LANG_VAR]    = $LANGUAGE;                                                    # set language in session

if (Get('lang_reload')) $TRANSLATION->Reload_Word_Array = true;

require "$LIB/yoga_translations.php"; //<------- TEMPORARY LIST OF ALL TRANSLATIONS FOR SITE - eventually move to database

if (Get('z')) {

}

#$LANGUAGE_LIST         = (isset($LANGUAGE_LIST)) ? $LANGUAGE_LIST : 'English';
$PAGE_STREAM            = $TRANSLATION->TranslateText($PAGE_STREAM, $LANGUAGE, Get('transid'));
#$PAGE_STREAM           = $DBSWAP->SwapDatabaseText($PAGE_STREAM, $DBSWAP->SwapFromArray, Get('dbid'));



# REMOVE UNUSED SWAPS
# ===========================================================
if (!Get('noswap')) {
    $skip = array(
        'META_DESCRIPTION',
        'META_KEYWORDS',
        'META_AUTHOR',
        'META_HIDDEN',
    );
    RemoveUnusedSwaps($skip);
}


# OUTPUT CONTENT
# ===========================================================
echo $PAGE_STREAM;












function addSwapCustom($identifier, $content) 
{
    $content = (Get('identifier')) ? "<span class='idetifier'>{$identifier} ==> </span>{$content}" : $content;
    $content = (Get('identifieronly')) ? "<span class='idetifier'>{$identifier}</span>" : $content;
    addSwap($identifier, $content);
}

function TrackErrorOnPage($ACTION, $FLAG)
{
}

function SetSessionSwitchVariable($varname, $default, $switch_on, $switch_off) 
{
    # =======================================================================================================
    # FUNCTION :: Used to set session and get variables on - things like a bypass switch from the URL
    #   $varname    = name of the variable to be stored in session - also the name of variable in GET
    #   $default    = TRUE || FALSE -> by default what the variable should be set to if no switch_on given
    #   $switch_on  = text string in GET that will turn SESSION ON - also the value stored in that session
    #   $switch_off = text string in GET that will turn SESSION OFF (unset)
    # =======================================================================================================
    $var_on = (Get($varname)==$switch_on || Session($varname)==$switch_on) ? true : $default;
    $var_on = (Get($varname)==$switch_off) ? false : $var_on;
    if ($var_on) {
        $_SESSION[$varname] = $switch_on;
    } else {
        unset($_SESSION[$varname]);
    }
}