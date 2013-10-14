<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: July 16, 2012
Last Updated By: Richard Witherspoon

       Filename: /office/page.php
    Description: Controls what page content and teamplates are output to the screen

  Upgrade Notes:
                11-01-2010  -> Added include file yoga_helper.php
                04-12-2012  -> Added include file lib_sanitize.php
                07-16-2012  -> Cleaned up error for super-admin logging in
                            -> Re-ordered where some functions located at foot of page
==================================================================================== */


ini_set('display_errors','1');
setlocale(LC_MONETARY, 'en_US');


# for this one you must use: &ssl=off
$requireSSL = false;//(isset($_GET['ssl']) && $_GET['ssl']=='off') ? false : true;

// redirect to the SSL version of the page
if($requireSSL && $_SERVER["HTTPS"] != "on") {
   header("HTTP/1.1 301 Moved Permanently");
   header("Location: https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
   exit();
}



# ============================= WEBSITE SETUP =============================
$SITE               = "/office";        // set the sub-directory this office install is in
$hold_session_open  = true;             // TRUE = don't ever log users out of system
$ENABLE_SANITIZE    = true;             // TRUE = clean the GET and POST variables
$default_template   = 'template.html';  // what template to show if not defined elsewhere

// what pages can a limited instructor access
$instructor_account_limited_allowed_pages = array('website/instructor_profile', 'website/instructor_help', 'website/video2', 'class_execute', 'image_upload_crop', 'website/z');



# ============================= LOAD FILES =============================
require $_SERVER['DOCUMENT_ROOT'] . '/lib/page_helper.php';
require "$ROOT/wo/site_office_config.php";
require "$LIB/form_helper.php";
require "$LIB/custom_error.php";
require "$LIB/yoga_helper.php";
require "$LIB/lib_sanitize.php";
require "$ROOT/classes/autoload.php";


if (false) {
    echo "<hr>" . ArrayToStr($_SESSION) . "<hr>";
}


# ============= VARIABLE SETUP ==================
$dialog_win_classinfo = '';
#$_SESSION['WANT_CLASS_INFO'] = true;


# ============= CREATE PAGE NAMES =============
GetPageName();
//$USER->CheckModulePermission($PAGE['pagename']);


# ========== SANITIZE ALL $_GET and $_POST VARIABLES ==========
#SanitizePost();
#SanitizeGet();
#SanitizeServer(false);

$ENABLE_SANITIZE = (Get('sanitize')=='off') ? false : $ENABLE_SANITIZE;
if ($ENABLE_SANITIZE) {
    SanitizeArray($_POST, false);
    SanitizeArray($_GET, false);
    SanitizeArray($_SERVER, false);
    SanitizePage(false);
}


SetSessionSwitchVariable('session_booking_online', false, 'on', 'off');



# ============= USER AUTHENTICATION & LOGOUT=============
if ($QUERY_STRING == 'LOGOUT') {
    $_GET['LOGOUT'] = 1;
}

$USER               = new Authentication;
$ADMIN_SUPER_USER   = $USER->Super_User;



if (false) {
    echo "<br />SESSION ARRAYS<br />";
    echo ArrayToStr($_SESSION);
    echo "<br />========================<br />";
    exit();
}


# ============= INITIALIZE GLOBAL OBJECTS =============
$TRANSLATION    = new Translation_Translations();
$TIMEZONE       = new General_TimezoneConversion();


# ============= SPECIALIZED CSS STYLE HERE =============
$OBJ_CSS = new General_CSSStyle();
$OBJ_CSS->AddStyleBlog();


# ============= WRITE LOG =============
WriteTrackingLog();
//BlockedIPCheck();


# ============= GET SITE CONFIGURATION =============
$CONFIG = new Website_Configuration();
$CONFIG->GetWebsiteConfiguration();
$CONFIG->GetSiteConfigSwaps();


# ============= GET CONTENT FILE NAMES =============
GetPageFileNames();


# ============= LOAD PAGE AS AJAX =============
if ($AJAX) { 
    ob_start(); include $PAGE['contentfilename']; $PAGE_STREAM = ob_get_contents(); ob_end_clean();
    $PAGE_STREAM = TranslatePageStream($PAGE_STREAM);
    echo $PAGE_STREAM;
    exit;
}


# ============= HANDLE IMAGE PROCESSING =============
if (strpos($PAGE['pagename'], 'gimage/') !== false) {
    include $LIB . '/image_helper.php';
}


# ============= GET PAGE VARIABLES =============
GetTitleVariables();


# ============= SET ADMIN LOG =============
$ADMIN_LOG = Lib_Singleton::GetInstance('Lib_AdminLog');
if (!empty($PAGE['name'])) {
    $ADMIN_LOG->AddModuleRecord($PAGE['pagename']);
}

if (false) {
    echo "<br />pagename ===> {$PAGE['pagename']}";
    exit();
}


# ============= CONFIGURE THE USR ROLE FOR TEMPLATES =============
$run_this               = true;
$is_superuser           = (isset($_SESSION['USER_LOGIN']['LOGIN_RECORD']['super_user'])) ? $_SESSION['USER_LOGIN']['LOGIN_RECORD']['super_user'] : false;
$is_administrator       = (isset($_SESSION['USER_LOGIN']['LOGIN_RECORD']['type_administrator'])) ? $_SESSION['USER_LOGIN']['LOGIN_RECORD']['type_administrator'] : false;

$is_superuser           = ($is_administrator) ? true : $is_superuser;

$IS_SUPERUSER           = $is_superuser;
$IS_ADMINISTRATOR       = $is_administrator;

if (!$is_superuser && $run_this) {
    # =================== FORCE THE USER INTO CUSTOMER TEMPLATE ===================
    # NOTES: If you have the $customer_role in the list of roles - it will force the change-over unless a super-admin
    # This means that if you were to assign an instructor role AND a customer role to same person - it would force to customer first
    # =====================================================================================================================================
    $customer_role          = 36;
    $instructor_role        = 37;
    $default_page           = 'website/profile';
    
    //echo ArrayToStr($PAGE);
    //echo ArrayToStr($_SESSION);
    
    
    # CHECK IF THIS IS A CUSTOMER
    # ==============================================================
    $module_roles           = (isset($_SESSION['USER_LOGIN']['LOGIN_RECORD']['module_roles'])) ? $_SESSION['USER_LOGIN']['LOGIN_RECORD']['module_roles'] : '';
    $module_roles           = explode(',', $module_roles);
    $is_customer            = (in_array($customer_role, $module_roles)) ? true : false;
    if ($is_customer) {
        #echo "CUSTOMER<hr><br /><br />";
        #exit();
        $default_page           = 'website/profile';
        $user_type              = 'customer';
        $_SESSION['USER_TYPE']  = 'customer';
        $_SESSION['customer']   = true;
        $_SESSION['instructor'] = false;
        $_SESSION['BYPASS']     = true;
    }
    
    
    # CHECK IF THIS IS AN INSTRUCTOR
    # ==============================================================
    $module_roles           = (isset($_SESSION['USER_LOGIN']['LOGIN_RECORD']['module_roles'])) ? $_SESSION['USER_LOGIN']['LOGIN_RECORD']['module_roles'] : '';
    $module_roles           = explode(',', $module_roles);
    $is_instructor          = (in_array($instructor_role, $module_roles)) ? true : false;
    if ($is_instructor) {
        #echo "INSTRUCTOR<hr><br /><br />";
        #exit();
        $default_page           = 'website/instructor_profile';
        $user_type              = 'instructor';
        $_SESSION['USER_TYPE']  = 'instructor';
        $_SESSION['customer']   = false;
        $_SESSION['instructor'] = true;
        $_SESSION['BYPASS']     = true;
    }
    
    
    # DONT ALLOW CUSTOMERS TO ACCESS NORMAL OFFICE SITE
    # ==============================================================
    if ($PAGE['pagename'] == 'index') {
        
        # CHECK TO SEE IF GOING TO ANOTHER URL
        $forward_page   = (isset($_SESSION['LOGIN_RETURN_URL'])) ? $_SESSION['LOGIN_RETURN_URL'] : $default_page;
        unset($_SESSION['LOGIN_RETURN_URL']);
        
        //$forward_page   = $default_page;
        
        //echo "forward_page ===> " . $forward_page;
        //exit();
        
        //header("Location: /office/{$forward_page}");
        header("Location: {$forward_page}");
    }
    
    
    $customer_template      = 'website/profile';
    $user_module_roles      = (isset($_SESSION['USER_LOGIN']['LOGIN_RECORD']['module_roles'])) ? $_SESSION['USER_LOGIN']['LOGIN_RECORD']['module_roles'] : '';
    $user_module_roles      = explode(',', $user_module_roles);
    $is_customer            = (in_array($customer_role, $user_module_roles)) ? true : false;
    $is_wrong_template      = ($PAGE['pagename']!=$customer_template) ? true : false;

} // end superuser check








/*
#------------------------------------------------------------------------------------------
# TEMPORARILY USED FOR DEV SO I DON'T HAVE TO ALWAYS SET THE TEMPLATE
#------------------------------------------------------------------------------------------
    
    
    SetSessionSwitchVariable('newtemplate', false, 'on', 'off');
    SetSessionSwitchVariable('bypass', false, 'on', 'off');
    
    $_GET['newtemplate']    = (Session('newtemplate'))      ? true : false;
    $_GET['bypass']         = (Session('bypass'))           ? true : false;

    
    #$newtemplate = false;
    #$newtemplate = (Get('newtemplate')==true || Session('newtemplate')==true) ? true : $newtemplate;
    #$newtemplate = (Get('newtemplate')=='off') ? false : $newtemplate;
    #$_SESSION['newtemplate'] = $newtemplate;

    if (    
    Get('template') == 'launch' ||
    Get('template') == 'overlay' || 
    Get('template') == 'blank' ||
    Get('template') == 'none'
    ) {
        // don't modify template
    } else {    
        if (Session('newtemplate')) {
            if ($PAGE['pagename'] == 'index') {
                $_GET['template'] = 'new';
            } else if ($PAGE['pagename'] == 'index_new') {
                $_GET['template'] = 'new';
            } else if ($PAGE['pagename'] == 'signup') {
                $_GET['template'] = 'new_inner_1col';
            } else if ($PAGE['pagename'] == 'blog') {
                $_GET['template'] = 'new_inner_1col';
            } else {
                $_GET['template'] = 'new_inner';
            }
        }
    }
    
#------------------------------------------------------------------------------------------
*/





# =================== SETUP THE CONTENT TEMPLATE ===================
if (Get('customer') == 'on') $_SESSION['customer'] = true;
if (Get('customer') == 'off') unset($_SESSION['customer']);
if (Get('instructor') == 'on') $_SESSION['instructor'] = true;
if (Get('instructor') == 'off') unset($_SESSION['instructor']);


if ($PAGE['pagename']!='index' && $PAGE['pagename']!='index2') {



    $PAGE['template']   = 'dialog_template.html';
    
    $PAGE['template']   = (Session('customer'))                     ? 'customer_template.html' : $PAGE['template'];
    $PAGE['template']   = (Session('instructor'))                   ? 'instructor_template.html' : $PAGE['template'];
    $PAGE['template']   = ($PAGE['pagename']=='index_customer')     ? 'customer_template.html' : $PAGE['template'];
    $PAGE['template']   = ($PAGE['pagename']=='index_instructor')   ? 'instructor_template.html' : $PAGE['template'];
    
    /*
    $PAGE['template']   = (Session('customer'))                     ? 'template_new.html' : $PAGE['template'];
    $PAGE['template']   = (Session('instructor'))                   ? 'template_new.html' : $PAGE['template'];
    $PAGE['template']   = ($PAGE['pagename']=='index_customer')     ? 'template_new.html' : $PAGE['template'];
    $PAGE['template']   = ($PAGE['pagename']=='index_instructor')   ? 'template_new.html' : $PAGE['template'];
    */
    
    
    $PAGE['template']   = ($PAGE['pagename']=='website/video')      ? 'blank.html' : $PAGE['template'];
    $PAGE['template']   = ((Get('template') == 'launch'))           ? 'launch_template.html' : $PAGE['template'];
    $PAGE['template']   = ((Get('template') == 'overlay'))          ? 'overlay_template.html' : $PAGE['template'];
    $PAGE['template']   = ((Get('template') == 'blank'))            ? 'blank.html' : $PAGE['template'];
    $PAGE['template']   = ((Get('template') == 'chat'))             ? 'chat_template.html' : $PAGE['template'];
    $PAGE['template']   = ((Get('template') == 'new'))              ? 'template_new.html' : $PAGE['template'];
    $PAGE['template']   = ((Get('template') == 'new_inner'))        ? 'template_new_inner.html' : $PAGE['template'];


    if ($PAGE['pagename'] == 'website/instructor_october_challenge') {
        $PAGE['template']   = 'template_inner_1col.html';
        $PAGE['template']   = ((Get('template') == 'blank'))            ? 'blank.html' : $PAGE['template'];
    }
    
} elseif(empty($PAGE['template']) && !$is_superuser) {
    $PAGE['template'] = $default_template;
}
//echo '======>' . $PAGE['pagename'];
#echo ArrayToStr($_SESSION['USER_LOGIN']);





# ===================== SEE IF THIS IS A LIMITED INSTRUCTOR =====================

$instructor_account_limited = (isset($_SESSION['USER_LOGIN']['LOGIN_RECORD']['instructor_account_limited']) && !isset($_SESSION['USER_LOGIN']['LOGIN_RECORD']['type_customer'])) ? $_SESSION['USER_LOGIN']['LOGIN_RECORD']['instructor_account_limited'] : false;
if ($instructor_account_limited) {
    $_SESSION['instructor_account_limited'] = true;
} else {
    unset($_SESSION['instructor_account_limited']);
}

//echo "<br />instructor_account_limited ===> {$_SESSION['instructor_account_limited']}";


if (Get('instructor')) {
    $instructor_account_limited = false;
    $_SESSION['USER_LOGIN']['LOGIN_RECORD']['instructor_account_limited'] = 0;
    $_SESSION['instructor_account_limited'] = false;
}




# ============================ TEMPLATE ============================
# ======================== SUPER USER CHECK ========================
if ($is_superuser) {
    if ($PAGE['pagename']!='index' && $PAGE['pagename']!='index2') {
        $PAGE['template'] = 'dialog_template.html';
    } elseif(empty($PAGE['template'])) {
        $PAGE['template'] = 'template_backoffice.html';
    }
}


# ============= GET CONTENT =============
$DIALOG_CONTENT_WIDTH = '900px';
$PAGE_STREAM = file_get_contents("$SITE_ROOT/templates/{$PAGE['template']}");
ob_start(); 


# ============= IF LIMITED INSTRUCTOR - OUTPUT MESSAGE IF NEEDED =============
if (!$is_superuser && $instructor_account_limited && (!in_array($PAGE['pagename'], $instructor_account_limited_allowed_pages))) {
    $temp_content = "<div style='font-size:20px;'>Your account has limited access at this time. Review the checklist on your profile page and complete the steps to become a YogaLiveLink Instructor with full access.<br /><br /><a class='link_arrow' href='instructor_profile'>Click here to return to your profile page.</a></div>";
    AddSwap('@@PAGE_HEADER_TITLE@@', 'LIMITED ACCOUNT ACCESS');
    AddSwap('@@CONTENT_LEFT@@', '');
    AddSwap('@@CONTENT_RIGHT@@', $temp_content);
} else {
    include $PAGE['contentfilename'];
}


# ============= POPULATE THE $PAGE_CONTENT VARIABLE =============
$PAGE_CONTENT = ob_get_contents(); ob_end_clean();






// ========================= HOLD SESSIONS OPEN =======================

if ($hold_session_open) {
    AddScript("
    var holdSessionInterval = 300000;  // 5-minutes
    var haveHoldSession     = false;
    function holdSessionOpenAction()
    {
        $.get('HOLDSESSION');
    }

    function holdSessionOpen()
    {
        if (haveHoldSession == false) {
            setInterval('holdSessionOpenAction()', holdSessionInterval);
            //var content = '<span id=\"SESSION_HOLD\">SESSION HOLD</span>';
            //$('#' + taskbarId).prepend(content);
        }
        //setTopFlash('Session now held open until window closed or reset');
        haveHoldSession = true;
        return false;
    }
    ");
    
    AddScriptOnReady("holdSessionOpen();");
}

#echo ArrayToStr($PAGE);

//-------------------------- custom -------------------------
$dbmessages             = Session('YOGA_WANT_DB_QUERIES')? $SQL->WriteDbQueryText() : '';
$dialog_win_classinfo   = Session('WANT_CLASS_INFO') ? $dialog_win_classinfo : '';


AddSwap('@@DBMESSAGES@@', $dbmessages);
AddSwap('@@DIALOGNUMBER@@', Get('DIALOGID'));
AddSwap('@@IS_FOLDER@@', Get('isFolder'));  // --- MVP ADDED --- ???????
AddSwap('@@CLASSINFO@@', $dialog_win_classinfo);    // --- RAW ADDED 10-09-10
AddSwap('@@DIALOG_CONTENT_WIDTH@@', $DIALOG_CONTENT_WIDTH);
addSwap('@@BETA@@', '');
addSwap('@@CONTENT_BOTTOM@@', '');



//$TESTVAR .= Session('YOGA_WANT_DB_QUERIES')? 'WANT_DB_QUERIES=1' : 'WANT_DB_QUERIES=0';






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

$PAGE_STREAM = TranslatePageStream($PAGE_STREAM);


# OUTPUT CONTENT
# ===========================================================
echo $PAGE_STREAM;
















function AddDialogClassInfo($info_array) 
{
    global $dialog_win_classinfo;
    $output = "<br /><hr>";
    foreach ($info_array AS $TITLE => $VAL) {
        $output .= "{$TITLE}: {$VAL} <br />";
    }
    $dialog_win_classinfo .= $output;
}


function TranslatePageStream($PAGE_STREAM) 
{
    global $TRANSLATION, $LIB;
    
    # FUNCTIONS FOR PERFORMATING TRANSLATION SWAPS
    # ===========================================================
    # NOTE: use custom variable ($LANG_VAR) for language tracking or it will conflict with other sites.
    #
    #$TRANSLATION            = new Translation_Translations; // DECLARED ABOVE FOR SOME REASON
    #$DBSWAP                = new Translation_DatabaseSwap;
    $LANG_VAR               = 'yoga_lang';
    $LANGUAGE               = 'english';                                                    # default language
    $LANGUAGE               = (Session($LANG_VAR)) ? Session($LANG_VAR) : $LANGUAGE;        # set to SESSION language if present
    $LANGUAGE               = Get('lang')? Get('lang') : $LANGUAGE;                         # set to GET language if present
    $_SESSION[$LANG_VAR]    = $LANGUAGE;                                                    # set language in session

    if (Get('lang_reload')) $TRANSLATION->Reload_Word_Array = true;

    require "$LIB/yoga_translations.php"; //<------- TEMPORARY LIST OF ALL TRANSLATIONS FOR SITE - eventually move to database

    #$LANGUAGE_LIST         = (isset($LANGUAGE_LIST)) ? $LANGUAGE_LIST : 'English';
    $PAGE_STREAM            = $TRANSLATION->TranslateText($PAGE_STREAM, $LANGUAGE, Get('transid'));
    #$PAGE_STREAM           = $DBSWAP->SwapDatabaseText($PAGE_STREAM, $DBSWAP->SwapFromArray, Get('dbid'));
    
    return $PAGE_STREAM;
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