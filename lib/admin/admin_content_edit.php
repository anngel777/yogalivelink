<?php
//==========================================================
//                       CONTENT EDIT
//==========================================================

$RevContentText = '';
if (!$CTEXT) {
    if ($ARC) {
        $RevContentText = '<span class="revnotice">REVISION</span>';
        if ($SP) {
            $fname = str_replace('../','',$F);
            if (strpos($fname, $ROOT) !== false) {
                $fname = strFrom($fname, "$ROOT/");
            }
            $AF = str_replace('/','@',$fname);
            $CTEXT = file_get_contents("$SITE_ROOT{$SITECONFIG['archivedir']}/$AF".'_'.$ARC.'.php');
        } else {
            $archive_path = str_replace($SITE_ROOT, '', ADMIN_CONTENT_DIR);
            $AF = str_replace('/','@', $F);
            $CTEXT = file_get_contents(ADMIN_ARCHIVE_DIR . "/$archive_path@$AF".ADMIN_CONTENT_STR.'_'.$ARC.'.php');
        }
    } else $CTEXT = file_get_contents($Cfilename);
}


StoreTime('Content Loaded');
$WantHTML = $SITECONFIG['wanthtml'];

if ((stripos($CTEXT,'<?php')!==false) or (stripos($CTEXT,'<form')!==false)) {
    $WantHTML = 0;
}

//$CTEXT = mb_convert_encoding($CTEXT, 'UTF-8');
$CTEXT = htmlspecialchars($CTEXT, ENT_COMPAT, 'UTF-8');

if (!empty($WantHTML) and (($SP==0) or (($SP==1) and !(strpos($OPT,'H')===false)))) {
    $HTMLbutton = '<a id="HTMLcontentButton" class="contentbutton" href="#" onclick="SetEditor(); return false;">Edit&nbsp;Content&nbsp;(HTML)</a>';
} else {
    $HTMLbutton = '';
}

$pagecount++;

print <<<EC1

<!-- =========================== CONTENT EDITING ======================= -->
<div id="mainpage$pagecount" class="contenttab">
   <table align="center" cellpadding="3">
     <tr>
       <td>$HTMLbutton</td>
       <td><select class="box2" name="ATFILES" onchange="window.location=this.options[this.selectedIndex].value">
           <option value="$ADMIN_FILE?$QS1">Current Content: $Cfd</option>
EC1;

foreach ($ACfiles as $fi) {
    $rev = DateToStd($fi);
    $QS  = $QS1 . $SV . "ARC=$fi";
    $selected = ($ARC==$fi) ? ' selected="selected"' : '';
    printqn("           <option class=`special2` value=`$ADMIN_FILE?$QS`$selected>REV: $rev</option>");
}

StoreTime('Content Archive Loaded');

print '           </select>
       </td>
       <td>';

if (($SP!=1) and (!empty($SITECONFIG['wantdraft']))) {
    print'<input type="submit" class="contentsubmit" name="SAVEDRAFT" value="Save Page Draft" />&nbsp;';
}

print '<input type="submit" class="contentsubmit" name="PUBLISH" value="Publish Page" />'.$RevContentText;

print '</td></tr></table>';

include "$admin_inc/admin_content_menu.php";

StoreTime('Content Menu Loaded');

print <<<EC4

<div id="contentedit">
<!-- =========================== Edit Content ======================= -->
<textarea id="CTEXT" name="CTEXT" rows="25" cols="80"
   onkeypress="showId('contentmodifed'); setAutoTextAreaHeight('CTEXT');">$CTEXT</textarea>
</div>

</div>
<script type="text/javascript">
   setTimeout("setAutoTextAreaHeight('CTEXT')", 100);
</script>
<!-- ================================================================= -->

EC4;

//---------- clean variables from this file----------
unset($CTEXT);  // done with this
unset($AF);
unset($QS);
unset($QS1);
unset($rev);
unset($selected);
unset($HTMLbutton);
unset($ACfiles);
