<?php
if ($F) {
    $dateinfo = qqn("<form method=`post` action=`$ADMIN_PAGE_QUERY_LINK`>
  <input type=`hidden` name=`TITLEDATE` value=`$TitleDate` />
  <input type=`hidden` name=`CONTENTDATE` value=`$ContentDate` />");
} else {
    $dateinfo = '';
}


$errorout = ($ERROR_MSG)? qqn("<div id=`errormsg`>$ERROR_MSG</div>") : '';

$dropdown = qqn("<option value=`$ADMIN_FILE`>---- Choose File ----</option>");

$count=0;
$space = '';//'            ';

//-----------Process Standard Files----------
foreach ($files as $fi) {
    $count++;
    $QS="F=$fi";
    $selected = ($F==$fi)? ' selected="selected"' : '';
    $m=explode('/',$fi);
    if (count($m)>1) {
        $fi1='';
        for($i=0;$i<count($m)-1;$i++) {$fi1.=$m[$i].':';}
        $fi=$fi1.'&nbsp;&nbsp;&nbsp;'.$m[count($m)-1];
    }
    if (substr($fi,0,1)=='_') {
        $dropdown .= qqn("$space<option class=`draft` value=`$ADMIN_FILE?$QS`$selected>$count. Draft: $fi</option>");
    } else {
        $dropdown .= qqn("$space<option class=`filedropdown` value=`$ADMIN_FILE?$QS`$selected>$count. $fi</option>");
    }
}

StoreTime('Process Standard Files');

//-----------Process Special Files----------

if (ADMIN_LEVEL==9) {

    $special_folders_array = array();

    $special_folders_array[ADMIN_CLASS_DIR] = 'classfile|CLASS';

    $special_folders_array["$SITE_ROOT/common"] = 'commonfile|COMMON';

    $special_folders_array["$SITE_ROOT/helper"] = 'helperfile|HELPER';

    if (!array_key_exists(ADMIN_TEMPLATE_DIR, $special_folders_array)) {
        $special_folders_array[ADMIN_TEMPLATE_DIR] = 'templatefile|TEMPLATES';
    }

    if (!array_key_exists(ADMIN_CSS_DIR, $special_folders_array)) {
        $special_folders_array[ADMIN_CSS_DIR] = 'cssfile|CSS';
    }

    if (!array_key_exists("$SITE_ROOT/js", $special_folders_array)) {
        $special_folders_array["$SITE_ROOT/js"] = 'jsfile|JAVASCRIPT';
    }

    $special_folders_array["$SITE_ROOT/lists"] = 'listfile|LIST';
    
    if (ArrayValue($SITECONFIG,'special_dirs')) {
        foreach ($SITECONFIG['special_dirs'] as $dir) {
            $special_folders_array["$ROOT$dir"] = "special|[$dir]";
        }
    }

 
    foreach ($special_folders_array as $folder => $info) {

        if ($FILE_SET or !AdminSession("SPECIAL_FILES:$folder", SESSION_FILES)) {
            $SpecialFiles = GetDirectory($folder, '', 'archive/');
            //$_SESSION['SITE_ADMIN'][SESSION_FILES]["SPECIAL_FILES:$folder"] = $SpecialFiles;
        } else {
            $SpecialFiles = AdminSession("SPECIAL_FILES:$folder", SESSION_FILES);
        }

        list($css_class, $select_name) = explode('|', $info);

        foreach ($SpecialFiles as $fi) {
            $count++;
            $OPTtype   = (strpos($fi,'.htm')!==false)? 'CT' : 'C';
            $QS        = "F=$folder/$fi$SV" . "OPT=$OPTtype$SV"."SP=1";
            $selected  = ($F == TextBetween('F=', $SV, $QS))? ' selected="selected"' : '';
            $dropdown .= qqn("$space<option class=`$css_class` value=`$ADMIN_FILE?$QS`$selected>$count. $select_name: $fi</option>");
        }
    }

    $special = file(ADMIN_FILES_DIR.'/special.dat');
    foreach ($special as $i) {
        $i=trim($i);
        if ($i) {
            $m=explode('|',$i);
            $content=$m[1];
            $count++;
            $QS       = "F=$content$SV"."OPT=$m[2]$SV"."SP=1";
            $selected = ($F==$m[1])? ' selected="selected"' : '';

            if (strPos($m[1],'./common/')!==false) $class = 'commonfile';
            elseif (strPos($m[1],'./page.php')!==false) $class = 'controllerfile';
            elseif (strPos($m[1],'/siteconfig.php')!==false) $class = 'configfile';
            else $class = 'special';
            $dropdown  .= qqn("$space<option class=`$class` value=`$ADMIN_FILE?$QS`$selected>$count. SPECIAL: $m[0]</option>");
        }
    }
    $dropdown = substr($dropdown,0,-1);
}

StoreTime('Process Special Files');

//================ END File Dropdown Selection ================

// -------------- TOP MENU ---------
ob_start(); include "$admin_inc/admin_top_menu.php"; $TOP_MENU = ob_get_contents(); ob_end_clean();

StoreTime('Process Top Menu');

$MenuWidth = ($SpecialMenuCount>0) ? '20em' : '15em';

//=================================== PRINT HEADER=======================================
echo $dateinfo . $errorout . '<div id="mainheader">
<table border="0" width="100%">
  <tr>
    <td id="sitename">' . $SITECONFIG['sitename'] . '<br /><span style="font-size:0.8em;">Website Administration</span>
    </td>
    <td style="width:' . $MenuWidth . ';">' .
    $TOP_MENU . '
    </td>
    <td>
      <!-- ====================== FILE SELECTION ======================= -->
      <span class="dropdown">
          <select class="box" name="FILES" onchange="window.location=this.options[this.selectedIndex].value">
' . $dropdown . '
          </select>
      </span>
    </td>
  </tr>
</table>
</div> <!-- end main header -->
';

StoreTime('Print Menu');

if ($F) {
print '
<!-- =========================== TABS ======================= -->
';

$pagecount = 0;
if ($need_preview or $need_title or $need_content) {
  print '<div id="tabdiv">
';

if ($need_preview) {
  $pagecount++;
  print '
    <a id="tablink1" class="tabselect" href="#" onclick="setTab(1); return false;">Preview</a>
';
}
if ($need_title) {
  $pagecount++;
  print '
    <a id="tablink'.$pagecount.'" class="tablink" href="#" onclick="setTab('.$pagecount.'); return false;">Title
    <span class="modified" id="titlemodifed" style="display:none;">- Modified</span></a>
';
}
if ($need_content) {
  $pagecount++;
  print '
    <a id="tablink'.$pagecount.'" class="tablink" href="#" onclick="setTab('.$pagecount.'); return false;">Content
    <span class="modified" id="contentmodifed" style="display:none;">- Modified</span></a>
';
}
  print '
<div class="tabspacer">&nbsp;</div>
</div>
<div id="pagewrapper">
';
}
$pagecount = 0;
}

//---------- clean variables from this file----------
unset($dropdown);
unset($SpecialFiles);
unset($TOP_MENU);
unset($special);
unset($MenuWidth);

