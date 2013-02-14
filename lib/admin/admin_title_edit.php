<?php
//==========================================================
//                       TITLE EDIT
//==========================================================

$RevTitleText = '';
if (!$TTEXT) {
    if ($ART) {
        $RevTitleText = '<span class="revnotice">REVISION</span>';
        if ($SP) {
            $fname = str_replace('../','',$FS);
            $AF = str_replace('/','@',$fname);
            $TTEXT=file_get_contents("$SITE_ROOT{$SITECONFIG['archivedir']}/$AF".'_'.$ART.'.php');
        } else {
            $archive_path = str_replace($SITE_ROOT, '', ADMIN_CONTENT_DIR);            
            $AF = str_replace('/','@',$F);
            $TTEXT = file_get_contents(ADMIN_ARCHIVE_DIR.  "/$archive_path@$AF".ADMIN_TITLE_STR.'_'.$ART.'.php');
        }
    }
    else {
        if (file_exists($Tfilename)) {
            $TTEXT = file_get_contents($Tfilename);
        } else {
            $TTEXT = file_get_contents('blanktitle.dat');
            $Tfd = '(NEW)';
        }
    }
}

$TTEXT = htmlentities($TTEXT);

$archives = '';

foreach ($ATfiles as $fi) {
    $rev = DateToStd($fi);
    $QS  = $QS1 . $SV . "ART=$fi";
    $selected  = ($ART==$fi) ? ' selected="selected"' : '';
    $archives .= qq("           <option class=`special2` value=`$ADMIN_FILE?$QS`$selected>REV: $rev</option>\n");
}


$pagecount++;
print <<<ET1

<!-- =========================== TITLE EDITING ======================= -->
<div id="mainpage$pagecount" class="titleedit">
    <table align="center" cellpadding="3">
      <tr>
       <td>
         <select class="box2" name="ATFILES" onchange="window.location=this.options[this.selectedIndex].value">
         <option value="$ADMIN_FILE?$QS1">Current Title: $Tfd</option>
$archives
         </select>
       </td>
       <td>
         <input type="submit" class="titlesubmit" name="PUBLISH" value="Publish Page" />
         $RevTitleText
       </td>
      </tr>
    </table>

<textarea id="TTEXT" name="TTEXT" rows="25" cols="80"
  onkeypress="showId('titlemodifed'); setAutoTextAreaHeight('TTEXT');">$TTEXT</textarea>
</div>

<!-- ================================================================= -->

ET1;
//---------- clean variables from this file----------
unset($TTEXT);  // done with this
unset($fname);
unset($archives);
unset($selected);
unset($AF);
unset($rev);
unset($QS);
unset($ATfiles);
