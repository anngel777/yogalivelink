<?php
//==========================================================
//                        PREVIEW
//==========================================================

$pagecount = 1;

$ContentWidth = ADMIN_CONTENT_WIDTH;

if (strpos($OPT,'T')!==false) {
    $display_file = str_replace($ROOT, '', $F);
    $iframe = qqn("<iframe id=`ViewPage` src=`$display_file?$ContentDate` width=`$ContentWidth` height=`500`></iframe>");
} else {
    //$iframe = qqn("<iframe id=`ViewPage` src=`$ADMIN_FILE?VIEWPAGE=$F` width=`$ContentWidth` height=`500`></iframe>");
    //$iframe = qqn("<iframe id=`ViewPage` src=`{$SITECONFIG['pagedir']}/ADMINVIEW-$F` width=`$ContentWidth` height=`500`></iframe>");
    $iframe = qqn("<iframe id=`ViewPage` src=`{$SITECONFIG['pagedir']}/$F` width=`$ContentWidth` height=`500`></iframe>");
}

$BASE = 'http://' . Server('HTTP_HOST');
if ($SP==1) {
    if (strpos($F, './common/') !==false) {
        $Fnew = str_replace($ROOT, '', $F);
        $PageLink = qq("<a class=`headfilename` target=`_blank` href=`$BASE{$SITECONFIG['sitedir']}/$Fnew`>{$SITECONFIG['sitedir']}/$Fnew</a>");
    } else {
        $PageLink = str_replace($ROOT, '', $F);
    }
} elseif ($F) {
    $PageLink = qq("$DraftNotice
    <a class=`headfilename` href=`#` onclick=`getId('ViewPage').contentWindow.location.reload(true); return false;`>Reload</a>
    <a class=`headfilename` target=`_blank` href=`$BASE{$SITECONFIG['pagedir']}/$F{$SITECONFIG['extension']}`>{$SITECONFIG['pagedir']}/$F{$SITECONFIG['extension']}</a> 
");
} else {
    $PageLink = '';
}

print <<<PREVIEW
<!-- =========================== PREVIEW ======================= -->
<div id="mainpage1" class="previewfolder">
<h1 id="previewtitle">$PageLink</h1>
  <div id="PREVIEW" style="width:{$ContentWidth}px;">
  $iframe
  </div>
</div>
PREVIEW;

//---------- clean variables from this file----------
unset($ContentWidth);
unset($PageLink);
unset($Fnew);
unset($iframe);

?>
