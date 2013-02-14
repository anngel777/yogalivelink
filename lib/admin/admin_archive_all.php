<div id="archive">
<h1>Archive Files</h1>
<?php

print <<<ALBL
<a class="mainbutton" href="$ADMIN_FILE?ARCHIVE=VIEW">View Files to Archive</a>
<a class="mainbutton" href="$ADMIN_FILE?ARCHIVE=ALL">Archive Files</a>
<a class="mainbutton" href="$ADMIN_FILE?ARCHIVE=LIST">View Archive</a>
ALBL;

//----------ARCHIVE ALL FILES (/content and /common)-------------

$rootdir = (ADMIN_SITE_LINK_DIR == '')? '/' : ADMIN_SITE_LINK_DIR;

if (file_exists(ARCHIVE_LIST_FILE)) {
    $archive_folders = file(ARCHIVE_LIST_FILE);
    TrimArray($archive_folders);
    $archive_folders = array_unique($archive_folders);
} else {
    //create a list and save it

    $archive_folders = array(
        PathFromRoot(ADMIN_CONTENT_DIR),
        PathFromRoot("$SITE_ROOT/common"),
        PathFromRoot("$SITE_ROOT/config"),
        PathFromRoot(ADMIN_TEMPLATE_DIR),
        PathFromRoot(ADMIN_CLASS_DIR),
        PathFromRoot("$SITE_ROOT/js"),
        PathFromRoot(ADMIN_CSS_DIR),
        PathFromRoot("$SITE_ROOT/lists"),
        PathFromRoot("$SITE_ROOT/helper"),
        PathFromRoot(ADMIN_IMAGE_DIR),
        $rootdir
    );
    $archive_folders = array_unique($archive_folders);
    AdminWriteFile(ARCHIVE_LIST_FILE, implode("\n", $archive_folders));
}
//-----------------------------------------------------------------------
$EXCLUDE_LIST  = 'Desktop.ini,Thumbs,.log,.tmp';
$IMAGELIST     = explode(',', ADMIN_IMAGE_TYPES);

$INCLUDE      = Get('INCLUDE');

//-----------------------------------------------------------------------

function StripRoot($str)
{
    global $ROOT;
    $str = str_replace('\\', '/', $str);  // needed for Windows
    $str = str_replace($ROOT,'',$str);
    return $str;
}

//-----------------------------------------------------------------------
if ($ARCHIVE == 'FILE') {
    $viewfile = urldecode(get('viewfile'));
    echo "<h2>FILE: $viewfile</h2>";
    if(ArrayItemsWithinStr($IMAGELIST, $viewfile)) {
        echo "<img src=\"/$viewfile\" alt=\"IMAGE: $viewfile\" />";
    } else {
        $file = ADMIN_ARCHIVE_DIR . "/$viewfile";
        $content = htmlentities(file_get_contents($file));
        echo "<textarea rows=\"25\" style=\"width:100%\">$content</textarea>";
    }
}

if ($ARCHIVE == 'LIST') {
    echo '<h2>Archive List. . .</h2>';

    $ArchiveFiles = GetDirectory(ADMIN_ARCHIVE_DIR, $INCLUDE);

    if ($ArchiveFiles) {
        printqn("<ol id=`dirlist`>");

        foreach ($ArchiveFiles as $file) {
            $link = urlencode("$file");
            echo "<li><a href=\"$ADMIN_FILE?ARCHIVE=FILE{$SV}viewfile=$link\">$file</a></li>\n";
        }
        echo '</ol>';
    }
}


if ($ARCHIVE == 'ALL' or $ARCHIVE == 'VIEW') {

    if($ARCHIVE == 'ALL') echo '<h2>Archiving Files. . .</h2>';
    else  echo '<h2>Viewing Files to Archive. . .</h2>';


    foreach ($archive_folders as $folder) {

        //----------ARCHIVE ALL FILES (/content and /common)-------------


        echo "<h3>$folder</h3>";


        if (($folder == "{$SITECONFIG['sitedir']}") or ($folder == '/')) {
            if ($folder == '/') $folder = '';
            $ContentFiles = GetDirectory("$ROOT$folder", '', $EXCLUDE_LIST, false);
        } else {
            $ContentFiles = GetDirectory("$ROOT$folder", '', $EXCLUDE_LIST);
        }

        $ARCHIVE_FOLDER = "$ROOT$folder/archive";
        $count = 0;

        echo '<ol style="text-align:left; font-size:1.2em;">';

        //------------ARCHIVE CONTENT FILES--------------
        foreach ($ContentFiles as $AF) {

            $filename = "$ROOT$folder/$AF";
            $filedate = date(ARCHIVE_DATE_FORMAT, filemtime($filename));
            $AF       = str_replace($SITECONFIG['sitedir'], '', "$folder/$AF");
            $AF       = str_replace('/','@', substr($AF, 1));

            if (ArrayItemsWithinStr($IMAGELIST,$filename)) {
                $ext = '.'. strFromLast($filename, '.');
            } else {
                $ext = '.php';
            }

            $filename2 = ADMIN_ARCHIVE_DIR . "/{$AF}_$filedate$ext";

            if (!file_exists($filename2)) {
                if($ARCHIVE == 'ALL') {
                    copy($filename, $filename2);
                    chmod($filename2, 0666);
                    $out ="<li>$filename --> {$AF}_$filedate$ext</li>\n";
                } else {
                    $out ="<li>$filename <span style=\"font-size:0.7em;\">[$filedate]</span></li>\n";
                }
                echo StripRoot($out);  // remove $ROOT from output
                $count++;
            }
        }
        if ($count==0) {
            echo '<li>All files Archived!</li>';
        }
        echo '</ol>';

    }
    if (($ARCHIVE == 'ALL') and ($count>0)) {
        LogUpdate(ADMIN_USERNAME,'Archive All','');
    }
}

?>
</div>