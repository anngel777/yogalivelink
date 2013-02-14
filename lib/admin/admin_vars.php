<?PHP
//  WEBSITE ADMINISTRATION PROGRAM

//============INITIALIZE VARIABLES=========
$SCRIPT_NAME  = $_SERVER['SCRIPT_NAME'];
$PHP_SELF     = $_SERVER['PHP_SELF'];

$TTEXT        = Post('TTEXT');
$CTEXT        = Post('CTEXT');

SetPost(
'NEWFILE NEWFILENAME PUBLISH SAVEDRAFT SAVETFILE SAVECFILE NEWNAME RENAMEFILE OLDNAME'
.' DISPLAYREPLACE REPLACEALL IMAGEUPLOAD IMAGEDIR'
.' NEWNAME_IMAGE RENAMEFILE_IMAGE OLDNAME_IMAGE DOCUMENTDIR DOCUMENTUPLOAD RENAMEFILE_DOCUMENT'
.' NEWNAME_DOCUMENT OLDNAME_DOCUMENT TITLEDATE CONTENTDATE'
.' SITEMAP WRITESITEMAP GENERATESITEMAP NEWFILETYPE'
.' COPYFILE'
);

$SEARCHSTR = Post('SEARCHSTR'); $FINDSTR = Post('FINDSTR'); $REPLACESTR = Post('REPLACESTR');

SetGet(
'NEW DELETE DELETE_IMAGE PHP F SP SAT SAC VC VG OPT FM VD VL AL RENAME FIND RENAME_IMAGE'
.' REPLACE DELETE_DOCUMENT RENAME_DOCUMENT GOOGLE'
.' ART ARC ARCHIVE ARCHIVEPAGE IU DU PRINT SITELOG COPY CONFIG ADMININFO'
.' TINYMCE_IMAGE_LINKS TINYMCE_FILE_LINKS'
);


define('ADMIN_FILES_DIR', dirname("$ROOT$PHP_SELF"));
//define('ADMIN_FILES_DIR', dirname(Server('SCRIPT_FILENAME')));

if (empty($LIB_LINK)) {
    // lib link used when a ROOT does not yield a valid path;
    $LIB_LINK = '';
}

$LIB_DIRECTORY = $LIB_LINK . strFrom($LIB, $ROOT);
if (empty($LIB_DIRECTORY)) {
    $LIB_DIRECTORY = '/lib';
}

define('SESSION_FILES', 'FILES' . $SITECONFIG['sitedir']);

$ADMIN_PATH = dirname($PHP_SELF);
$ADMIN_FILE = basename($PHP_SELF);

if (empty($SITECONFIG['templatedir'])) $SITECONFIG['templatedir'] = $SITECONFIG['sitedir'].'/common';
if (empty($SITECONFIG['cssdir'])) $SITECONFIG['cssdir'] = $SITECONFIG['sitedir'].'/common';
if (empty($SITECONFIG['classdir'])) $SITECONFIG['classdir'] = $SITECONFIG['sitedir'].'/classes';

define('ADMIN_IMAGE_DIR', $ROOT.$SITECONFIG['imagedir']);
define('ADMIN_IMAGE_LINK_DIR', $SITECONFIG['imagedir']);
define('ADMIN_SITE_LINK_DIR', $SITECONFIG['sitedir']);
define('ADMIN_CONTENT_DIR', $ROOT.$SITECONFIG['contentdir']);
define('ADMIN_TEMPLATE_DIR', $ROOT.$SITECONFIG['templatedir']);
define('ADMIN_ARCHIVE_DIR', $ROOT.$SITECONFIG['archivedir']);
define('ADMIN_CLASS_DIR', $ROOT.$SITECONFIG['classdir']);
define('ADMIN_CSS_DIR', $ROOT.$SITECONFIG['cssdir']);
define('ADMIN_CONTENT_STR', $SITECONFIG['contentstr']);
define('ADMIN_TITLE_STR', $SITECONFIG['titlestr']);
define('ADMIN_CSS_PATH', $SITECONFIG['csspath']);
define('ADMIN_CONTENT_WIDTH', $SITECONFIG['contentwidth']);
define('ADMIN_PAGE_DIR', $SITECONFIG['pagedir']);
define('ADMIN_PAGE_EXTENSION', $SITECONFIG['extension']);
define('ADMIN_IMAGE_TYPES','.jpg,.png,.gif,.bmp,.swf');
define('ADMIN_TINYMCE', $SITECONFIG['tinymcepath']);
define('ARCHIVE_DATE_FORMAT', 'YmdHis');
define('ARCHIVE_LIST_FILE', ADMIN_FILES_DIR . '/archive_list.dat');



$DocLinkDirs = $SITECONFIG['docdirs'];
$SITE_ROOT   = $ROOT.$SITECONFIG['sitedir'];
$ADMIN_PAGE_QUERY_LINK = htmlentities($THIS_PAGE_QUERY);



$ERROR_MSG   = '';
$ADMIN_FLASH = '';

function AddError($msg)
{
    global $ERROR_MSG;
    $ERROR_MSG .= "<p>Error: $msg</p>";
}

function AddFlash($msg)
{
    global $ADMIN_FLASH;
    $ADMIN_FLASH .= "<p>$msg</p>";
}
