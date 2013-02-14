<?php
//==========================================================
//                    VIEW ALL CONTENT
//==========================================================
if ($VC>=1) {
    $ContentWidth = ADMIN_CONTENT_WIDTH;
    print "\n".'<!-- =====================VIEW ALL CONTENT================== -->'."\n";
    printqn("<table cellpadding=`5` bgcolor=`black` align=`center`  width=`{$SITECONFIG['contentwidth']}`>");
    print '<tr><td class="header" align="center"><span class="subheader">Content Files</span></td></tr>';

    $count=0;

    foreach ($files as $fi) {
        //---------------output the info-------
        $filename    = ADMIN_CONTENT_DIR."/$fi".ADMIN_CONTENT_STR;
        $titlename   = ADMIN_CONTENT_DIR."/$fi".ADMIN_TITLE_STR;
        $titletext   = file_get_contents($titlename);
        $title       = TextBetween('<title>','</title>',$titletext);
        $name        = TextBetween('<name>','</name>',$titletext);
        $summary     = TextBetween('<summary>','</summary>',$titletext);
        $description = TextBetween('<description>','</description>',$titletext);
        $keywords    = TextBetween('<keywords>','</keywords>',$titletext);

        $t=date("m\/d\/Y",filemtime($filename));
        $count++;
        $link="$ADMIN_FILE?F=$fi";

        $pagelink = "http://$HTTP_HOST{$SITECONFIG['pagedir']}/$fi{$SITECONFIG['extension']}";
  
        print <<<FILELABLE
  <tr>
  <!-- ===================FILE: $fi ===================== -->

  <td class="header2">

  <span class="fileheader">$count. </span><a class="fileheader" href="$link">$fi</a>

  &nbsp;&nbsp;&nbsp;
    <span style="font-size: 8pt">Version: $t
  &nbsp;&nbsp;&nbsp;
    (Validation:&nbsp;&nbsp;
    <a target="_blank" href="http://validator.w3.org/check?uri=$pagelink">XHTML</a>
  &nbsp;&nbsp;
    <a target="_blank" href="http://validator.w3.org/checklink?uri=$pagelink&amp;summary=on&amp;hide_redirects=on&amp;hide_type=all&amp;depth=&amp;check=Check">Links</a>
  &nbsp;&nbsp;
    <a target="_blank" href="http://www.contentquality.com/mynewtester/cynthia.exe?rptmode=-1&amp;url1=$pagelink">508</a>
  &nbsp;&nbsp;
    <a target="_blank" href="http://www.contentquality.com/mynewtester/cynthia.exe?rptmode=2&amp;url1=$pagelink">WAI</a>)</span>

  </td>
  </tr>

FILELABLE;

        if ($name) {
            printqn("<tr><td class=`header2`><b>NAME</b>: $name</td></tr>");
        }
        
        if ($summary) {
            printqn("<tr><td class=`header2`><b>SUMMARY</b>: $summary</td></tr>");
        }
        
        if ($title) {
            printqn("<tr><td class=`header2`><b>TITLE</b>: $title</td></tr>");
        }
        
        if ($description) {
            printqn("<tr><td class=`header2`><b>DESCRIPTION</b>: $description</td></tr>");
        }
        
        if ($keywords) {
            printqn("<tr><td class=`header2`><b>KEYWORDS</b>: $keywords</td></tr>");
        }

        if ($VC==1) {

            print <<<FILELABLE2
  <tr>
  <td class="page">

  <iframe id="ViewContent$count" src="admin.php?VIEWPAGE=$fi:ViewContent$count" width="$ContentWidth" height="500"></iframe>

  </td>
  </tr>
FILELABLE2;
        } else {
            echo '<tr><td style="background-color:#fff;"></td></tr>';
        }
    }
    
    print "</table>\n";
    
    //---------- clean variables from this file----------
    unset($ContentWidth);    
    
}
?>