<?PHP
//  WEBSITE ADMINISTRATION PROGRAM
//  Developed by Michael V. Petrovich, 2006-2009
ini_set('display_errors','1');
if (!session_id()) session_start();
$START_TIME = microtime(true);

$TIMES = array();
function StoreTime($title)
{
    global $START_TIME, $TIMES;
    static $LAST_TIME;
    if (empty($LAST_TIME)) {
        $LAST_TIME=$START_TIME;
    }
    $now  = microtime(true);
    $elapased_time = number_format($now - $START_TIME, 4);
    $inc_time = number_format($now - $LAST_TIME, 4);
    $LAST_TIME = $now;
    $TIMES[] = "$title|$elapased_time|$inc_time";
}

function AdminSession($var, $level2='')
{
    if (!isset($_SESSION['SITE_ADMIN'])) {
        $_SESSION['SITE_ADMIN'] = array();
    }
    if (!isset($_SESSION['SITE_ADMIN'][$level2])) {
        $_SESSION['SITE_ADMIN'][$level2] = array();
    }
    if ($level2) {
        return ArrayValue($_SESSION['SITE_ADMIN'][$level2], $var);
    } else {
        return ArrayValue($_SESSION['SITE_ADMIN'], $var);
    }
}

function ReadConfigFile() {
    global $SITECONFIG,$USER_ARRAY;
    require_once '../config/siteconfig.php';
}


$LIB = dirname(dirname(__FILE__));

//============INITIALIZE VARIABLES=========

require_once $LIB.'/mvptools.php';

$VIEWPAGE = Get('VIEWPAGE');
if ($VIEWPAGE) {
    include "admin_view_page.php";
    exit;
}

ReadConfigFile();

StoreTime('Configfile Read');

if (!empty($SITECONFIG['usehttps']) and empty($HTTPS)) {
   header("Location:$HTTPS_URI");
}

$admin_inc = "$LIB/admin";
//include "$admin_inc/admin_updates.php";
include "$admin_inc/admin_log.php";
include "$admin_inc/admin_auth.php";
include "$admin_inc/admin_vars.php";


// if (empty($QUERY_STRING) and (count($_POST) == 0) and (count($_GET) == 0)) {
    // $FILE_SET = 1;
    // $_SESSION['SITE_ADMIN'][SESSION_FILES] = '';
// } else {
    // $FILE_SET = 0;
// }

$FILE_SET = 1;

StoreTime('Auth and Vars');

if ($TINYMCE_IMAGE_LINKS or $TINYMCE_FILE_LINKS) {
    include "$admin_inc/admin_tinymce_links.php";
    exit;
}

StoreTime('TinyMCE');

if ($PHP=='1') {phpinfo(); exit;}

if ($SITELOG == 1) {
    include "$admin_inc/admin_viewlogs.php"; exit;
} elseif ($SITELOG == 2) {
    include "$admin_inc/admin_viewlogs_old.php"; exit;
} elseif ($SITELOG == 3) {
    include "$admin_inc/admin_viewlogs_db.php"; exit;
}


if (ADMIN_LEVEL == 9) {
    if (Get('CREATE_CLASS')) {
        include "$admin_inc/admin_class_creation.php";
        exit;
    }

  // --------- diagnostics routines --------
    if ($ADMININFO == '1') {
        $_SESSION['SITE_ADMIN']['ADMININFO'] = 1;
        AddFlash('Admin Info Set');
    }
    if ($ADMININFO == '0') {
        $_SESSION['SITE_ADMIN']['ADMININFO'] = 0;
        AddFlash('Admin Info Removed');
    }
    if (Get('TIME') == '1') {
        $_SESSION['SITE_ADMIN']['TIME'] = 1;
        AddFlash('Time Display Set');
    }
    if (Get('TIME') == '0') {
        $_SESSION['SITE_ADMIN']['TIME'] = 0;
        AddFlash('Time Display Removed');
    }
}


include "$admin_inc/admin_process_file.php";

StoreTime('Process File');

//=====================START PAGE BODY=====================
if (!empty($PRINT)) {include "$admin_inc/admin_print_page.php"; exit;}

$need_title   = (($F) and (($SP==0) or (($SP==1) and !(strpos($OPT,'L')===false))));
$need_content = ((($F) and ($SP=='')) or
                 (($SP==1) and (strpos($OPT,'P')!==false)) or
                 (($SP==1) and (strpos($OPT,'C')!==false)));
$need_preview = ( (($F) and ($SP=='')) or
                 (($SP==1) and (strpos($OPT,'T')!==false)) or
                 (($SP==1) and (strpos($OPT,'P')!==false))  );

require_once "$admin_inc/admin_head.php";
require_once "$admin_inc/admin_header.php";

StoreTime('Header');

if ($F) {
    $QS1="F=$F";
    if ($OPT) {
        $QS1.=$SV."OPT=$OPT";
    }
    if ($SP) {
        $QS1.=$SV."SP=1";
    }
}

if ($need_preview and $F) include "$admin_inc/admin_preview.php";
StoreTime('Preview');
if ($need_title and $F)   include "$admin_inc/admin_title_edit.php";
StoreTime('Title');
if ($need_content and !empty($F)) include "$admin_inc/admin_content_edit.php";
StoreTime('Content');

if (Get('SET_FTP')) {
    include "$admin_inc/admin_set_ftp.php";
}

if ($IU or $IMAGEUPLOAD or $DU or $DOCUMENTUPLOAD) include "$admin_inc/admin_upload.php";
if ($NEW) include "$admin_inc/admin_new_page.php";
if ($FIND or $SEARCHSTR) include "$admin_inc/admin_search.php";
if (Get('SESSIONS')) include "$admin_inc/admin_session_manager.php";
if ($FINDSTR or $REPLACE) include "$admin_inc/admin_find_and_replace.php";
if ($GOOGLE or $SITEMAP or $WRITESITEMAP or $GENERATESITEMAP) include "$admin_inc/admin_google_sitemap.php";
if (Get('COMBINE')==1) include "$admin_inc/admin_combine_files.php";

if ($DELETE_IMAGE or $RENAMEFILE_IMAGE or $RENAME_IMAGE or $VG
    or Get('RESIZE_IMAGE') or Post('RESIZE_IMAGE_SUBMIT') ) include "$admin_inc/admin_view_all_images.php";
if ($VD or $RENAMEFILE_DOCUMENT or $DELETE_DOCUMENT or $RENAME_DOCUMENT) include "$admin_inc/admin_view_documents.php";
if ($VC>=1) include "$admin_inc/admin_view_all_content.php";
if ($VL==1) include "$admin_inc/admin_view_links.php";
if ($RENAME or $COPY or ($FM=='1')) include "$admin_inc/admin_file_manager.php";
if ($ARCHIVE) include "$admin_inc/admin_archive_all.php";
if ($AL==1) ViewAdminLogFile();
if (Get('PHPFUNC') == 1) {
    include "$admin_inc/admin_php_functions.php";
}
if ($CONFIG) include "$admin_inc/admin_edit_config.php";

if (!empty($pagecount)) echo '</div>';
if ($F) {print'</form>';}

if ((ADMIN_LEVEL == 9) and (AdminSession('ADMININFO') == 1)) include "$admin_inc/admin_info.php";

if ((ADMIN_LEVEL == 9) and Get('CLEAR_CACHE')) {
    $dir = $SITECONFIG['cachedir'];
    if ($dir) {
        $cache_files = GetDirectory($ROOT . $SITECONFIG['cachedir']);
        if ($cache_files) {
            echo '<table class="upload" align="center"><tbody><tr><td align="left"><ol>';
            $count = 0;
            foreach ($cache_files as $cache_file) {
                if (unlink($ROOT . $SITECONFIG['cachedir'] . '/' . $cache_file)) {
                    $count++;
                    echo "<li>$cache_file &mdash; REMOVED</li>";
                }
            }
            echo '</ol></td></tr></tbody></table>';
        }
    }
}

if(!empty($ADMIN_FLASH)){
    print <<<FLASH
  <div id="flash">$ADMIN_FLASH</div>
  <script type="text/javascript">
      setTimeout("closeCenter('flash')",3000);
  </script>
FLASH;
}

if (AdminSession('TIME')) {
    StoreTime('End');
    $total_time = microtime(true) - $START_TIME;
    echo '<table id="time_table" align="center" border="0" cellspacing="1"  cellpadding="0"><tbody>';
    echo "<tr><th>Title</th><th>Elapased<br />Time</th><th>Incremental<br />Time</th><th></th></tr>";
    foreach ($TIMES as $row) {
        list($title, $elapased_time, $inc_time) = explode('|', $row);
        $width = round(200 * floatval($inc_time)/ $total_time);
        printqn("<tr><td>$title</td><td align=`right`>$elapased_time</td><td align=`right`>$inc_time</td>
        <td><div style=`height:0.8em; width:{$width}px; background-color:#0f0; border:1px solid #000;`></div></td></tr>");
        
    }
    echo '</tbody></table>';
}

?>
</body>
</html>
