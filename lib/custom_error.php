<?php
// error handler function
ini_set('display_errors','1');
error_reporting(E_ALL);

$CUSTOM_ERROR_STRING = '';

$CUSTOM_ERROR_PUBLIC_TEMPLATE =<<<ERRORLBL2
<table id="PUBLIC_ERROR_NOTICE" align="center" style="background-color:#000; margin:10px auto;">
    <tr>
        <th style="background-color:#f66; color:#fff; text-align:center;">PAGE ERROR</th>
    </tr>
    <tr>
        <td style="background-color:#ff7; text-align:left; color:#000; padding:1em">
        An Error has occurred on this page.  Site Administration has been notified.
        </td>
    </tr>
</table>
ERRORLBL2;

$CUSTOM_ERROR_TEMPLATE =<<<ERRORLBL1
<table id="PHPERROR" align="center" style="background-color:#000; margin:10px auto;">
    <tr>
        <th style="background-color:#f66; color:#fff; text-align:center;">
        
        <a style="display:block; margin-left:3px; color:#000; text-decoration:none; width:5em; font-size:0.8em; float:right; background-color:#ccc; padding:1px 3px;" href="#" onclick="document.getElementById('PHPERROR').style.display='none'; return false;">Hide</a>        
        
        <a style="display:block; color:#000; text-decoration:none; width:5em; font-size:0.8em; float:right; background-color:#ccc; padding:1px 3px;" href="#" onclick="
        var i=1;
        var ErrorTraceState = document.getElementById('error_trace_1').style.display;
        var tDisplay = (ErrorTraceState == 'none')? 'block' : 'none';
        while (document.getElementById('error_trace_'+i)) {
            document.getElementById('error_trace_'+i).style.display=tDisplay;  i++;
        }
        return false;">Trace</a>PHP ERROR</th>
    </tr>
    <tr>
        <td style="background-color:#ff7; text-align:left; color:#000; padding:1em">
        @@PHPERR@@
        </td>
    </tr>
</table>
ERRORLBL1;

function HavePhpError()
{
    global $CUSTOM_ERROR_STRING;
    return !empty($CUSTOM_ERROR_STRING);
}


function CustomErrorServer($name) 
{
    return (isset($_SERVER[$name]))? $_SERVER[$name] : '';
}

function CustomErrorAdminRunning() 
{
    if (isset($_SESSION['SITE_ADMIN']['AdminLoginOK'])) {
        return ($_SESSION['SITE_ADMIN']['AdminLoginOK'] == 'ok');
    } else {
        return false;
    }
}

function CustomErrorReport()
{
    global $CUSTOM_ERROR_STRING;
    if (empty($CUSTOM_ERROR_STRING)) {
        return '';
    }
    $DATE_TIME   = date('l, F j, Y g:ia');

    $SCRIPT_NAME     = CustomErrorServer('SCRIPT_NAME');
    $HTTP_HOST       = CustomErrorServer('HTTP_HOST');
    $REQUEST_URI     = CustomErrorServer('REQUEST_URI');
    if (empty($REQUEST_URI)) {
        $REQUEST_URI = "$SCRIPT_NAME?" . CustomErrorServer('QUERY_STRING');
    }
    $HTTPS           = CustomErrorServer('HTTPS');
    $SCRIPT_URI      = empty($HTTPS)? 'http://'.$HTTP_HOST.$REQUEST_URI : 'https://'.$HTTP_HOST.$REQUEST_URI;

    $ADDR        = CustomErrorServer('REMOTE_ADDR');
    $HTTP_REFERER= CustomErrorServer('HTTP_REFERER');
    $USER_AGENT  = CustomErrorServer('HTTP_USER_AGENT');
    $ERROR_INFO  = str_replace(' style="display:none;"', '', $CUSTOM_ERROR_STRING);

    $left_style  = 'text-align:right; font-weight:bold; background-color:#ccc';
    $right_style = 'background-color:#fff;';

    $RESULT =<<<ERRORLBL2
<table align="center" style="background-color:#000;" cellspacing="1" cellpadding="2">
    <tr>
        <th colspan="2" style="background-color:#f66; color:#fff; text-align:center;">PHP ERROR</th>
    </tr>
    <tr><td style="$left_style">TIME:</td><td style="$right_style">$DATE_TIME</td></tr>
    <tr><td style="$left_style">URI:</td><td style="$right_style">$SCRIPT_URI</td></tr>
    <tr><td style="$left_style">REMOTE ADDR:</td><td style="$right_style">$ADDR</td></tr>
    <tr><td style="$left_style">REFERRER:</td><td style="$right_style">$HTTP_REFERER</td></tr>
    <tr><td style="$left_style">USER AGENT:</td><td style="$right_style">$USER_AGENT</td></tr>
    <tr>
        <td colspan="2" align="left" style="background-color:#ff7; text-align:left; color:#000; padding:1em">
        $ERROR_INFO
        </td>
    </tr>
</table>
ERRORLBL2;
    return $RESULT;
}

function CustomErrorText()
{
    global $CUSTOM_ERROR_STRING, $CUSTOM_ERROR_TEMPLATE, $CUSTOM_ERROR_PUBLIC_TEMPLATE;

    if ($CUSTOM_ERROR_STRING) {
        if (CustomErrorAdminRunning()) {
            return str_replace('@@PHPERR@@', $CUSTOM_ERROR_STRING, $CUSTOM_ERROR_TEMPLATE);
        } else {
            return $CUSTOM_ERROR_PUBLIC_TEMPLATE;
        }
    } else {
    return '';
    }
}

function WriteCustomError()
{
    echo CustomErrorText();
}

function CustomErrorHandler($errno, $errstr, $errfile, $errline)
{
    global $CUSTOM_ERROR_STRING;
    static $trace_count = 0;
    
    if (strlen($CUSTOM_ERROR_STRING) > 1000000) {
        return true;
    }

    $DRL = strlen($_SERVER['DOCUMENT_ROOT']);
    $errfile = substr($errfile,$DRL);

/*
E_ERROR
E_WARNING
E_PARSE
E_NOTICE
E_CORE_ERROR
E_CORE_WARNING
E_COMPILE_ERROR
E_COMPILE_WARNING
*/

    $trace_count++;
    $trace = debug_backtrace();
    $trace_output = '<ul id="error_trace_' . $trace_count . '" style="display:none; text-align:left;">';
    foreach($trace as $entry){    
        if ($entry['function'] != 'CustomErrorHandler') {
            if (!empty($entry['file'])) {
                $trace_output .= "<li><b>File:</b> {$entry['file']} (Line: {$entry['line']})<br />\n";
                $trace_output .= (!empty($entry['class']))? "<b>Class:</b> {$entry['class']}<br />" : '';
                $trace_output .= "<b>Function:</b> {$entry['function']}<br />\n";
                if (!empty($entry['args'])) {
                    $args = ArrayToStr($entry['args']);
                    if ($args) {
                        $trace_output .= "<b>Args:</b> $args\n";
                    }
                }
                $trace_output .= "</li>\n";
            }
        }
    }
    $trace_output .= '</ul>';


    $errstr = strip_tags($errstr);
    $LINE2 = " [$errno] $errstr<br />\nin line <b>$errline</b> in file <b>$errfile</b><br /><br />\n";
    $LINE2 .= $trace_output;

    switch ($errno) {

    case E_USER_ERROR:
        $CUSTOM_ERROR_STRING .= "<b>ERROR:</b> [$errno] $errstr<br />\n";
        $CUSTOM_ERROR_STRING .= "  Fatal error on line $errline in file <b>$errfile</b>";
        $CUSTOM_ERROR_STRING .= ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
        $CUSTOM_ERROR_STRING .= "Aborting...\n";
        $CUSTOM_ERROR_STRING .= $trace_output;
        WriteCustomError();
        exit(1);
        break;

    case E_USER_WARNING:
        $CUSTOM_ERROR_STRING .= "<b>WARNING:</b>$LINE2";
        break;

    case E_USER_NOTICE:
        $CUSTOM_ERROR_STRING .= "<b>NOTICE:</b>$LINE2";
        $CUSTOM_ERROR_STRING .= $trace_output;
        break;

    default:
        $CUSTOM_ERROR_STRING .= "<b>ERROR:</b>$LINE2";
        break;
    }

    /* Don't execute PHP internal error handler */
    return true;
}

$old_error_handler = set_error_handler('CustomErrorHandler');
