<?php
$archive_current_page = ($F) ? qq("<li><a href=`$ADMIN_FILE?F=$F{$SV}ARCHIVEPAGE=1`
          onclick=`return confirm('Do you want to Archive this page?')`>Archive Current Page</a></li>") : '';
$SPLINK = (!empty($SP))? $SV.'SP=1' : '';


$special = '';
$lines = file(ADMIN_FILES_DIR.'/adminlinks.dat');
$SpecialMenuCount = count($lines);
if ($SpecialMenuCount>0) {
    $special .= '<li><a href="#">Additional</a><ul>'."\n";
    foreach ($lines as $i) {
        $i = trim($i);
        if ($i) {
            $m = explode('|',$i);
            $m[1] = rtrim($m[1]);
            $special .= '<li><a target="_blank" href="'.$m[1].'">'.$m[0].'</a></li>'."\n";
        }
    }
    $special .= "</ul>\n</li>\n";
}

$sitelink = $SITECONFIG['pagedir'];


if (ADMIN_LEVEL==9) {  //restrict menu items;

    $admininfoitem = (AdminSession('ADMININFO')==1)?
        '<li><a href="#" onclick="showInfo(); return false;">Show Admin Info</a></li> ' : '';

    $level9menu1 = <<<menu1
        <li><a href="$ADMIN_FILE?SET_FTP=1">Set FTP Write</a></li>
        <li><a target="_blank" href="/lib/getupdates.php">Update Library</a></li>
        <li><a target="_blank" href="/lib/get_file_updates_ftp.php">Update Common Files (FTP)</a></li>
        <li><a target="_blank" href="$ADMIN_FILE?PHP=1">PHP Info</a></li>
        <li><a target="_blank" href="$ADMIN_FILE?PHPFUNC=1">PHP Functions</a></li>
        <li><a href="$ADMIN_FILE?SESSIONS=1">Session Manager</a></li>
        <li><a href="$ADMIN_FILE?GOOGLE=1">Google Sitemap</a></li>
        <li><a href="$ADMIN_FILE?CONFIG=1">Site Configuration</a></li>
        <li><a href="$ADMIN_FILE?COMBINE=1">Create/Update Combined Files</a></li>
        <li><a href="$ADMIN_FILE?CLEAR_CACHE=1" onclick="return confirm('Are you sure you want to clear the cache?');">Clear Site Cache</a></li>
        $admininfoitem
menu1;

    if (file_exists("$ROOT/wo/site_office_config.php")) {
        $level9menu1 .= <<<menu2
        <li><a target="_blank" href="$ADMIN_FILE?CREATE_CLASS=1">Class Creation</a></li>
menu2;
    }

    unset($admininfoitem);

} else {
    $level9menu1 = '';
}

print <<<TOPMENU1
<ul class="mainmenu">
   <li><a href="#">Files</a>
      <ul>
        <li><a href="$ADMIN_FILE?NEW=1">New Page</a></li>
        <li><a href="$ADMIN_FILE?IU=1">Upload Image</a></li>
        <li><a href="$ADMIN_FILE?DU=1">Upload Document</a></li>
        <li><a href="$ADMIN_FILE?FM=1">File Manager</a></li>
        $archive_current_page
        <li><a href="$ADMIN_FILE?ARCHIVE=VIEW">Archive Files</a></li>
      </ul>
   </li>
   <li><a href="#">View</a>
      <ul>
        <li><a target="_blank" href="$ADMIN_FILE?VC=1">View All Content</a></li>
        <li><a target="_blank" href="$ADMIN_FILE?VC=2">View All Header Info</a></li>
        <li><a target="_blank" href="$ADMIN_FILE?VG=1">View All Images</a></li>
        <li><a target="_blank" href="$ADMIN_FILE?VD=1">View Document Files</a></li>
        <li><a target="_blank" href="$ADMIN_FILE?VL=1">View Links</a></li>
        <li><a href="$ADMIN_FILE?FIND=1">Search</a></li>
        <li><a href="$ADMIN_FILE?REPLACE=1">Replace</a></li>
        <li><a target="_blank" href="$sitelink/">Site</a></li>
      </ul>
   </li>

   <li><a href="#">Admin</a>
      <ul>
        <li><a href="$ADMIN_PAGE_QUERY_LINK">Refresh</a></li>
        <li><a target="_blank" href="$ADMIN_FILE">New Admin</a></li>
        <li><a target="_blank" href="$ADMIN_FILE?SITELOG=1">View Site Logs</a></li>
        <li><a target="_blank" href="$ADMIN_FILE?AL=1">View Admin Logs</a></li>
$level9menu1
        <li><a href="$ADMIN_FILE">Reset</a></li>
        <li><a href="$ADMIN_FILE?LOGOUT=1">Log Out</a></li>
      </ul>
   </li>
$special
</ul>
TOPMENU1;

//---------- clean variables from this file----------
unset($sitelink);
unset($level9menu1);
unset($special);
unset($lines);
