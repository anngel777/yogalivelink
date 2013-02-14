<?php
/* ========== UPDATE LOG ==========
03-08-2012 - RAW - Function GetPageFileNames() - added 404 header to redirect and error redirect function
03-12-2012 - RAW - Function GetPageFileNames() - added storing of original page extension - for error redirecting - added function CheckExtension404()
07-16-2012 - RAW - Function RemoveUnusedSwaps() - remove unused swaps for page stream
*/



//------- page helper file ---------
if (!session_id()) {
    ini_set('url_rewriter.tags', '');
    ini_set('session.use_trans_sid', false);
    session_start();
}
if (empty($LIB)) {
    include 'mvptools.php';
}

$PAGE   = array(
    'ERROR'               => '',
    'MESSAGE'             => '',
    'FLASH'               => '',
    'START_TIME'          => microtime(true),
    'DATETIME'            => date('YmdHis'),
    'MENU_ID_TEMPLATE'    => 'id="menu_@"',
    'MENU_SELECT_TEMPLATE'=> 'id="pageselected"',
    'SCRIPTINC'           => '',
    'SCRIPT'              => '',
    'SCRIPT_ONREADY'      => '',
    'SCRIPT_ONLOAD'       => '',
    'STYLE'               => '',
    'STYLE_SHEETS'        => '',
    'SCRIPT_ONREADY_TEMPLATE' => "\n\$(function(){\n@\n});\n",
    'SCRIPT_ONLOAD_TEMPLATE'  => "\nwindow.onload = function(){\n@\n}\n",
    'original_name'       => '',
    'PAGE_CONTENT_ONLY'   => 0
);

if (empty($SITECONFIG)) {
    $ConfigPath = empty($ConfigPath)? '.' : $ConfigPath;
    include "$ConfigPath/config/siteconfig.php";
}

$SITE_DIR     = $SITECONFIG['sitedir'];
$PAGE_DIR     = $SITECONFIG['pagedir'];
$SITE_ROOT    = $ROOT.$SITE_DIR;
$PAGEDEFAULT  = 'index';

if (empty($SITECONFIG['templatedir'])) $SITECONFIG['templatedir'] = $SITE_DIR . '/common';
if (empty($SITECONFIG['cssdir'])) $SITECONFIG['cssdir'] = $SITE_DIR . '/common';

$PAGE_SWAP_VARIABLES = array();  // assoc array of additional items to swap

$BLOCK_LIST_TERMS  = array('awstats','xmlrpc','chat/','phpmyadmin','phpMyAdmin-','/main','azenv','thisdoesnotexistahaha');
$QUERYVAR          = array();
$AJAX              = 0;

function AdminRunning()
{
    return ArrayValue(ArrayValue($_SESSION, 'SITE_ADMIN'), 'AdminLoginOK') == 'ok';
}

function addSwap($key, $value)
{
    global $PAGE_SWAP_VARIABLES;
    $PAGE_SWAP_VARIABLES[$key] = $value;
}


function Page_Helper_Add_Msg($field, $MSG, $template)
{
    global $PAGE;

    if ($MSG != '') {
        $PAGE[$field] .= str_replace('@@', $MSG, $template);
    }
}

function addError($MSG, $template='<p>@@</p>')
{
    Page_Helper_Add_Msg('ERROR', $MSG, $template);
}

function addMessage($MSG, $template='<p>@@</p>')
{
    Page_Helper_Add_Msg('MESSAGE', $MSG, $template);
}

function addFlash($MSG, $template='<p>@@</p>')
{
    Page_Helper_Add_Msg('FLASH', $MSG, $template);
}

function addScript($MSG)
{
    Page_Helper_Add_Msg('SCRIPT', $MSG, "@@\n\n");
}

function addScriptOnload($MSG)
{
    Page_Helper_Add_Msg('SCRIPT_ONLOAD', $MSG, "@@\n\n");
}

function addScriptOnReady($MSG)
{
    Page_Helper_Add_Msg('SCRIPT_ONREADY', $MSG, "@@\n\n");
}

function addScriptInclude($SCRIPTS)
{
    global $PAGE;

    if (!empty($SCRIPTS)) {
        $scripts_list   = explode(',', $SCRIPTS);
        foreach ($scripts_list as $script) {
            $script = trim($script);
            if ($script) {
                $PAGE['SCRIPTINC'] .= "<script type=\"text/javascript\" src=\"$script\"></script>\n";
            }
        }
    }
}

function addStyle($MSG)
{
    Page_Helper_Add_Msg('STYLE', $MSG, "@@\n\n");
}

function addStyleSheet($style_sheets)
{
    global $PAGE;

    if (!empty($style_sheets)) {
        $sheets_array = explode(',', $style_sheets);
        foreach ($sheets_array as $sheet) {
            $sheet = trim($sheet);
            if ($sheet) {
                $PAGE['STYLE_SHEETS'] .= "@import \"$sheet\";\n";
            }
        }
    }
}


function IsBot($user_agent = '')
{
    global $LIB;
    static $bot_array;

    if (isset($_SESSION['IS_BOT'])) {
        return $_SESSION['IS_BOT'];
    }

    if (empty($user_agent)) {
        $user_agent = Server('HTTP_USER_AGENT');
        $set_session = true;
    } else {
        $set_session = false;
    }

    if (empty($bot_array)) {

        $user_agent_data = file("$LIB/user_agent_list.dat");

        foreach ($user_agent_data as $line) {
            $line = trim($line);
            if ($line) {
                list($agent_text, $title) = explode('|', $line);
                if (strpos($title, ' BOT') !== false) {
                    $bot_array[] = $agent_text;
                }
            }
        }
    }
    $RESULT = ArrayItemsWithinStr($bot_array, $user_agent);
    if ($set_session) {
        $_SESSION['IS_BOT'] = $RESULT;
    }
    return $RESULT;
}

function GetQuery($name)
{
    global $QUERYVAR;
    return (isset($QUERYVAR[$name]))? $QUERYVAR[$name] : '';
}

function page_OutputGroup()  // group process (js/css)
{
    global $REQUEST_URI, $ROOT, $SITECONFIG;

    $list = strFrom($REQUEST_URI, 'GROUP/');
    $list = strTo($list, '?');
    $update = strFrom($REQUEST_URI, '?');
    $files = explode(';', $list);
    // check extensions -- must be all alike and js or css
    $extension = '';

    foreach ($files as $file) {
        $ext = strFromLast($file, '.');
        if ($ext == 'js' || $ext =='css') {
            if ($extension == '') {
                $extension = $ext;
            } elseif ($extension != $ext) {
                echo 'Extension Mismatch';
                exit;
            }
        } else {
            echo 'Extension Error';
            exit;
        }
    }
    if (!$extension) {
        echo 'Invalid File Group';
        exit;
    }

    $cache_path = $ROOT . $SITECONFIG['cachedir']; // Cache path, this is where the .gz files will be stored
    $expires_offset = 3600 * 24 * 3650; // Cache for 10 years in browser cache
    $content = '';
    $encodings = array();
    $supports_gzip = false;
    $enc = '';
    $cache_key = '';

    // Headers
    if ($extension == 'js') {
        header('Content-type: text/javascript');
    } else {
        header('Content-type: text/css');
    }
    header('Vary: Accept-Encoding');  // Handle proxies
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires_offset) . ' GMT');

    // Setup cache info
    $cache_key = md5($list);

    $cache_file = $cache_path . '/' . $cache_key . '_' . $extension . '.gz';

    // Check if it supports gzip
    if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
        $encodings = explode(',', strtolower(preg_replace("/\s+/", "", $_SERVER['HTTP_ACCEPT_ENCODING'])));
    }

    if ((in_array('gzip', $encodings) || in_array('x-gzip', $encodings) ||
        isset($_SERVER['---------------'])) && function_exists('ob_gzhandler') && !ini_get('zlib.output_compression')) {
        $enc = in_array('x-gzip', $encodings) ? "x-gzip" : "gzip";
        $supports_gzip = true;
    }

    // Use cached file disk cache
    $get_cache = true;
    if (!empty($update) && file_exists($cache_file)) {
        $cache_date = date('YmdHi', filemtime($cache_file));
        foreach ($files as $file) {
            $file_date = date('YmdHi', filemtime($ROOT . '/' . $file));
            if ($file_date > $cache_date) {
                $get_cache = false;
                break;
            }
        }
    }

    if ($get_cache && $supports_gzip && file_exists($cache_file)) {
        //$cache_date = date('YmdHi', filemtime($cache_file));
        header("Content-Encoding: " . $enc);
        echo file_get_contents($cache_file);
        exit;
    }

    // Add content
    foreach ($files as $file) {
        $file_content = file_get_contents($ROOT . '/' . $file);
        if (empty($file_content)) {
            $file_content = '// -------> ERROR: File Contents Not Found';
        }
        $content .= "/*
=========================================
  FILE: $file
=========================================
*/\n" . $file_content . "\n\n";
    }

    // Generate GZIP'd content
    if ($supports_gzip) {
        header('Content-Encoding: ' . $enc);
        $cache_data = gzencode($content, 9, FORCE_GZIP);
        write_file($cache_file, $cache_data);  // Write gz file
        echo $cache_data; // Stream to client

    } else {
        echo $content; // Stream uncompressed content
    }
    exit;
}

function GetPageName($pagename = '')
{
    global $PAGE, $QUERY_STRING, $REQUEST_URI, $HTTP_HOST, $HTTPS, $SITECONFIG, $PAGEDEFAULT, $QUERYVAR,
           $PAGE_ALIAS, $AJAX, $PHP_SELF;


    if (strpos($REQUEST_URI, 'GROUP/') !== false) {
        page_OutputGroup();  // does not return;
    }

    //---- process psudo query string ----

    $querystr = preg_replace('/(^.+(\?|:|;|$))/U', '', $REQUEST_URI);

    if (!empty($querystr)) {

        $qlist = explode('&', preg_replace('/;|\?/', '&', $querystr));

        foreach ($qlist as $var) {
            $varinfo            = explode('=',$var);
            $key                = $varinfo[0];
            $value              = (count($varinfo)>1)? $varinfo[1] : 1;
            $value              = urldecode($value);
            $QUERYVAR[$key]     = $value;
            $_GET[$key]         = $value;
        }
    }

    if (empty($pagename)) {
        //set pagename up to query itmes
        $pagename = preg_replace('/(;|\?|:).+$/', '', $QUERY_STRING);
    }

    // remove beginning slashes
    $pagename = preg_replace('/^\/+/', '', $pagename);

    if (substr($pagename, 0, 5) == 'AJAX/') {
        $AJAX = 1;
        $pagename = substr($pagename, 5);
    }

    if (substr($pagename, 0, 12) == 'PAGECONTENT/') {
        $PAGE['PAGE_CONTENT_ONLY'] = 1;
        $pagename = substr($pagename, 12);
    }

    if (substr($pagename, 0, 2)=='P-') {
        $print = 1;
        $pagename = substr($pagename, 2);
    } else {
        $print = 0;
    }

    $PAGE['extension'] = pathinfo($pagename, PATHINFO_EXTENSION);
    $pagename = RemoveExtension($pagename);

    if ($pagename == '') {
        $pagename = $PAGEDEFAULT;
    }


    $PAGE['dirpage'] = dirname($pagename);

    // if ending slash add default
    if (substr($pagename, -1) == '/') {
        $pagename .= $PAGEDEFAULT;
    }

    $PAGE['original_name'] = $pagename;
    if (!empty($PAGE_ALIAS)) {
        if (array_key_exists($pagename,$PAGE_ALIAS)) {
            $pagename = $PAGE_ALIAS[$pagename];
        }
    }

    $pagename_with_query = ($querystr)? "$pagename?$querystr" : $pagename;

    $basename = str_replace('//', '/', $HTTP_HOST . dirname($PHP_SELF) . '/');
    $basename = empty($HTTPS)? 'http://'.$basename : 'https://'. $basename;

    $PAGE['pagename']         = $pagename;
    $PAGE['basename']         = $basename;
    $PAGE['print']            = $print;
    $PAGE['query']            = $querystr;
    $PAGE['printversionlink'] = "{$SITECONFIG['pagedir']}/P-$pagename_with_query";
    $PAGE['ajaxlink']         = "{$SITECONFIG['pagedir']}/AJAX/$pagename_with_query";
    $PAGE['pagelink']         = "{$SITECONFIG['pagedir']}/$pagename";
    $PAGE['pagelinkquery']    = "{$SITECONFIG['pagedir']}/$pagename_with_query";
    $PAGE['url']              = urlencode($HTTP_HOST)."{$SITECONFIG['pagedir']}/$pagename";
    $PAGE['id']               = str_replace('/','_',$pagename);
    $PAGE['menuid']           = str_replace('@', $PAGE['id'], $PAGE['MENU_ID_TEMPLATE']);
}

function Page_SetPageName($pagename)
{
    global $PAGE, $SITECONFIG, $HTTP_HOST;
    $pagename_with_query = ($PAGE['query'])? $pagename . '?' . $PAGE['query'] : $pagename;
    $PAGE['pagename']         = $pagename;
    $PAGE['printversionlink'] = "{$SITECONFIG['pagedir']}/P-$pagename_with_query";
    $PAGE['ajaxlink']         = "{$SITECONFIG['pagedir']}/AJAX/$pagename_with_query";
    $PAGE['pagelink']         = "{$SITECONFIG['pagedir']}/$pagename";
    $PAGE['pagelinkquery']    = "{$SITECONFIG['pagedir']}/$pagename_with_query";
    $PAGE['url']              = urlencode($HTTP_HOST)."{$SITECONFIG['pagedir']}/$pagename";
    $PAGE['id']               = str_replace('/','_',$pagename);
    $PAGE['menuid']           = str_replace('@', $PAGE['id'], $PAGE['MENU_ID_TEMPLATE']);
}


function GetPageFileNames($redirect='/', $errorpage='/')
{
    global $PAGE, $SITECONFIG, $ROOT, $QUERY_STRING, $AJAX;

    // ----- perform a 404 check on the original file extension if its a blank page
    if ($PAGE['pagename'] == 'index') {
        CheckExtension404($PAGE['extension'], $errorpage);
    }
    
    $PAGE['titlefilename']   = $ROOT.$SITECONFIG['contentdir']."/{$PAGE['pagename']}{$SITECONFIG['titlestr']}";
    $PAGE['contentfilename'] = $ROOT.$SITECONFIG['contentdir']."/{$PAGE['pagename']}{$SITECONFIG['contentstr']}";

    if (!file_exists($PAGE['titlefilename']) and !$AJAX) {
        // ----- add to missing log -----
        $missingfile    = $ROOT.$SITECONFIG['logdir'].'/missingpage-'.date('Y-m').'.dat';
        $date           = date('Y-m-d:H:i:s');
        $ADDR           = Server('REMOTE_ADDR');
        $HTTP_REFERER   = Server('HTTP_REFERER');
        $USER_AGENT     = Server('HTTP_USER_AGENT');
        $line           = "$date|$QUERY_STRING|$HTTP_REFERER|$ADDR|$USER_AGENT\n";
        append_file($missingfile,$line);
        
        
        if (isset($errorpage)) {
            Error404Page($errorpage);
        } else {
            // ----- redirect user -----
            header("Location: $redirect");
            exit;
        }
        
    }

    $t                  = filemtime($PAGE['titlefilename']);
    $c                  = filemtime($PAGE['contentfilename']);
    $PAGE['updated']    = date("m\/d\/Y", max($t,$c));
    $PAGE['modified']   = gmdate('D, d M Y H:i:s', max($t,$c)) . ' GMT';
}

function CheckExtension404($extension, $errorpage)
{
    # FUNCTION :: Check if the original page extension was an allowed one - otherwise 404 the page
    $allowed_extensions = array('php', '', 'html', 'htm', 'asp');
    
    if (!in_array($extension, $allowed_extensions)) {
        Error404Page($errorpage);
    }
}

function Error404Page($pagename='404.php')
{
    # FUNCTION :: Output headers and a 404 error page. Can't do via redirect or headers return 200/Ok
    
    header('HTTP/1.0 404 Not Found');   // set the error header
    echo readfile($pagename);           // load in the custom error page
    exit();                             // exit so no more code can execute
}

function GetTitleVariables() {
    global $PAGE, $PAGE_TITLE_CONTENT, $ROOT, $SITECONFIG;

    $PAGE_TITLE_CONTENT    = file_get_contents($PAGE['titlefilename']);
    $PAGE['name']          = TextBetween('<name>','</name>', $PAGE_TITLE_CONTENT);
    $PAGE['title']         = TextBetween('<title>','</title>', $PAGE_TITLE_CONTENT);
    $PAGE['description']   = TextBetween('<description>','</description>', $PAGE_TITLE_CONTENT);
    $PAGE['keywords']      = TextBetween('<keywords>','</keywords>', $PAGE_TITLE_CONTENT);
    $PAGE['banner']        = TextBetween('<banner>','</banner>', $PAGE_TITLE_CONTENT);
    $style                 = TextBetween('<style>','</style>', $PAGE_TITLE_CONTENT);
    $style_sheets          = TextBetween('<stylesheet>','</stylesheet>', $PAGE_TITLE_CONTENT);
    $script                = TextBetween('<script>','</script>', $PAGE_TITLE_CONTENT);
    $include_script        = TextBetween('<scriptinclude>','</scriptinclude>', $PAGE_TITLE_CONTENT);
    $php                   = TextBetween('<php>','</php>', $PAGE_TITLE_CONTENT);
    if ($php) {
        eval($php);
    }
    AddStyle($style);
    AddStyleSheet($style_sheets);
    AddScript($script);
    AddScriptInclude($include_script);

    $body                  = TextBetween('<body>','</body>', $PAGE_TITLE_CONTENT);
    if ($body) {
        $body  = ' ' . $body;
    }

    $PAGE['body']          = $body;

    $PAGE['template']      = TextBetween('<template>','</template>', $PAGE_TITLE_CONTENT);

    $robots                = TextBetween('<robots>','</robots>', $PAGE_TITLE_CONTENT);

    if (!empty($robots)) {
        $robots  = "<meta name=\"robots\" content=\"$robots\" />";
    }

    $PAGE['robots']        = $robots;

}


function BlockedIPCheck()
{
    global $BLOCK_LIST_TERMS, $PAGE, $SITECONFIG, $ROOT, $SITE_DIR;

    if (AdminRunning()) {
        return;
    }

    $SITE_TRACKING = str_replace('/', '_', "SITE_TRACKING$SITE_DIR");

    if (!isset($_SESSION[$SITE_TRACKING])) {
        $_SESSION[$SITE_TRACKING] = array();
    }

    $pageok = !ArrayItemsWithinStr($BLOCK_LIST_TERMS, $PAGE['pagename']);
    $tracking_array = $_SESSION[$SITE_TRACKING];

    if ($pageok and ArrayValue($tracking_array, 'BLOCK_CHECK') == 'ok') {
        return;
    }

    $blockfile = $ROOT . $SITECONFIG['logdir'] . '/block-' . date('Y-m-d') . '.dat';

    $block = false;
    $ADDR  = Server('REMOTE_ADDR');

    if (!$pageok or ArrayValue($tracking_array, 'BLOCK_CHECK') == 'block') {
        $block = true;
    } else {
        $blocklist = file_exists($blockfile)? file_get_contents($blockfile) : '';
        if (strpos($blocklist, $ADDR) !== false) {
           $block = true;
        }
    }

    if ($block) {
        $line = "$ADDR|{$_SERVER['QUERY_STRING']}|" . date('H:i:s') . "\n";
        append_file($blockfile, $line);
        $_SESSION[$SITE_TRACKING]['BLOCK_CHECK'] = 'block';
        header("Location: $ADDR/{$PAGE['pagename']}");
    } else {
        $_SESSION[$SITE_TRACKING]['BLOCK_CHECK'] = 'ok';
    }
}

function WriteTrackingLogDb()
{
    global $ROOT, $LIB, $PAGE, $SITECONFIG, $SITE_DIR;

    $titlefilename = $ROOT . $SITECONFIG['contentdir'] . "/{$PAGE['pagename']}{$SITECONFIG['titlestr']}";
    $SITE_TRACKING = str_replace('/', '_', "SITE_TRACKING$SITE_DIR");

    if (!AdminRunning() and file_exists($titlefilename) and empty($_SESSION[$SITE_TRACKING]['PAGE'][$PAGE['pagename']])) {
        include "$LIB/class.SiteLogs.php";
        $SL = new SiteLogs;
        $SL->AddToSiteLog();
    }
}

function WriteTrackingLog()
{
    global $PAGE, $SITECONFIG, $ROOT, $SITE_DIR;

    if (AdminRunning()) {
        return;
    }

    $SITE_TRACKING = str_replace('/', '_', "SITE_TRACKING$SITE_DIR");

    if (!isset($_SESSION[$SITE_TRACKING])) {
        $_SESSION[$SITE_TRACKING] = array();
    }

    $pagename = $PAGE['pagename'];

    if (empty($_SESSION[$SITE_TRACKING]['PAGE'][$pagename])) {
        $_SESSION[$SITE_TRACKING]['PAGE'][$pagename] = 1;
        if (empty($_SESSION[$SITE_TRACKING]['START_TIME'])) {
            $_SESSION[$SITE_TRACKING]['START_TIME'] = time();
        } else {
            $elapsedtime = time() - $_SESSION[$SITE_TRACKING]['START_TIME'];
        }

        $tid = $_SESSION[$SITE_TRACKING]['START_TIME'] . substr(session_id(),-4);
        $logfile = $ROOT . $SITECONFIG['logdir'] . '/log-' . date('Y-m').'.dat';

        if (empty($_SESSION[$SITE_TRACKING]['REFERRER'])) {
            $_SESSION[$SITE_TRACKING]['REFERRER'] = 1;
            $ADDR = Server('REMOTE_ADDR');
            $HTTP_REFERER = Server('HTTP_REFERER');
            $USER_AGENT = Server('HTTP_USER_AGENT');
            $line="$tid|REF|{$HTTP_REFERER}[$ADDR][$USER_AGENT]\n$tid|0|{$PAGE['pagename']}\n";
        } else {
            $line="$tid|$elapsedtime|$pagename\n";
        }
        append_file($logfile, $line);
    }
}


function SwapStdMarkUp()
{
    global $PAGE_STREAM, $PAGE, $SITECONFIG, $PAGE_CONTENT, $TESTVAR, $PAGE_SWAP_VARIABLES;

    $ERROR   = (empty($PAGE['ERROR']))?   '' : "<div id=\"error\">{$PAGE['ERROR']}</div>";
    $MESSAGE = (empty($PAGE['MESSAGE']))? '' : "<div id=\"message\">{$PAGE['MESSAGE']}</div>";
    $FLASH   = (empty($PAGE['FLASH']))?   '' : "<div id=\"flash\">{$PAGE['FLASH']}</div>";

    if ($PAGE['PAGE_CONTENT_ONLY']) {
        $PAGE_STREAM = '@@ERROR@@@@MESSAGE@@@@FLASH@@@@CONTENT@@';
    }

    if ($PAGE['SCRIPT_ONREADY']) {
        $PAGE['SCRIPT'] .= str_replace('@', rtrim($PAGE['SCRIPT_ONREADY']), $PAGE['SCRIPT_ONREADY_TEMPLATE']);
    }
    if ($PAGE['SCRIPT_ONLOAD']) {
        $PAGE['SCRIPT'] .= str_replace('@', rtrim($PAGE['SCRIPT_ONLOAD']), $PAGE['SCRIPT_ONLOAD_TEMPLATE']);
    }
    $PAGE['SCRIPT'] = JavaScriptString($PAGE['SCRIPT']);
    $PAGE['STYLE']  = StyleString($PAGE['STYLE_SHEETS'] . $PAGE['STYLE']);

    $new_end_body = isset($TESTVAR)? "$TESTVAR\n</body>" : '</body>';

    if (function_exists('customerrortext')) {
        AddSwap('@@PHPERROR@@', CustomErrorText());
    }

    $swap_array = array(
        '@@TITLE@@'                 => $PAGE['title'],
        '@@DESCRIPTION@@'           => $PAGE['description'],
        '@@KEYWORDS@@'              => $PAGE['keywords'],
        '<!-- @@STYLE@@ -->'        => $PAGE['STYLE'],
        '<!-- @@SCRIPT@@ -->'       => $PAGE['SCRIPT'],
        '<!-- @@SCRIPTINCLUDE@@ -->'=> $PAGE['SCRIPTINC'],
        ' title="@@BODY@@"'         => $PAGE['body'],
        '<!-- @@ROBOTS@@ -->'       => $PAGE['robots'],
        '@@CONTENT@@'               => $PAGE_CONTENT,
        '@@ERROR@@'                 => $ERROR,
        '@@MESSAGE@@'               => $MESSAGE,
        '@@FLASH@@'                 => $FLASH,
        '@@BASENAME@@'              => $PAGE['basename'],
        '@@UPDATED@@'               => $PAGE['updated'],
        '@@PRINTVERSIONLINK@@'      => $PAGE['printversionlink'],
        '@@DIR@@'                   => $PAGE['dirpage'],
        '@@PAGEURL@@'               => $PAGE['url'],
        '@@PAGEID@@'                => $PAGE['id'],
        '--PAGEID--'                => $PAGE['id'],
        '@@COMPANYNAME@@'           => $SITECONFIG['companyname'],
        '@@BANNER@@'                => $PAGE['banner'],
        '</body>'                   => $new_end_body
    );


    $last_swap = array (
        $PAGE['menuid']             => $PAGE['MENU_SELECT_TEMPLATE'],
        '@@SITEDIR@@'               => $SITECONFIG['sitedir'],
        '@@PAGEDIR@@'               => $SITECONFIG['pagedir'],
        '@@PAGELINK@@'              => $PAGE['pagelink'],
        '@@AJAXLINK@@'              => $PAGE['ajaxlink'],
        '@@PAGELINKQUERY@@'         => $PAGE['pagelinkquery'],
        '@@PAGENAME@@'              => $PAGE['pagename'],
        '@@DATETIME@@'              => $PAGE['DATETIME'],
        '@@TIME@@'                  => number_format(microtime(true) - $PAGE['START_TIME'], 3)
    );

    $swap_array = array_merge($swap_array, $PAGE_SWAP_VARIABLES, $last_swap);

    $PAGE_STREAM = astr_replace($swap_array, $PAGE_STREAM);
}


function RemoveUnusedSwaps($skipArray)
{
    // FUNCTION :: Removes unused page swaps from the PAGE_STREAM variable
    
    global $PAGE_STREAM;
    $swap_array = array();
    
    // Get all the unused swaps
    $tmpArray = TextBetweenArray('@@', '@@', $PAGE_STREAM);
    
    // Loop through and handle
    foreach ($tmpArray as $tmpSwap) {
        // Check if they're in the skip list
        if (!in_array($tmpSwap, $skipArray)) {
            $swap_array["@@{$tmpSwap}@@"] = ''; // add blank content to clear it
        }
    }
    
    // Perform the actual swap out of content
    $PAGE_STREAM = astr_replace($swap_array, $PAGE_STREAM);
    
    return true;
}