<?php
//======================Search Files==========================

printqn("<form method=`post` action=`$ADMIN_FILE`>");

?>
<table align="center" style="background-color:#eee;">
<tr><td>
<div style="float:left; width:50px; text-align:right; font-weight:bold;">Find:</div>
<div style="margin-left:60px;"><input class="formitem" type="text" name="SEARCHSTR" value="<? echo htmlentities($SEARCHSTR,ENT_COMPAT) ?>" size="40" />
<a class="mainbutton" style="width:6em; display:inline;" href="#" onclick="toggleDisplay('helptext'); return false;">Help</a>
</div>
<div id="helptext" style="margin-left:60px; width:290px;
    display:none; background-color:#FFFF7F;
    border:1px dashed #888; padding:0px 1em;">
<p style="font-size:1.2em; font-weight:bold;">Search Help</p>
<p>Enter search string in box above. The entire string will be matched, unless:</p>
<ul style="text-align:left; margin-left:4em;">
  <li>&ldquo; <b>AND</b> &rdquo; (in uppercase) is used to separate search groups, which finds items containing all groups delimited by the &ldquo; AND &rdquo;, or</li>
  <li>&ldquo; <b>OR</b> &rdquo; (in uppercase) is used to separate search groups, which finds items containing either of the groups delimited by the &ldquo; OR &rdquo;. </li>
  <li>If both AND <i>and</i> OR are used, AND groups will be found within OR groups</li>
</ul>
</div>
<div style="margin-left:60px;"><input class="messagesubmit" name="PROCESSFIND" type="submit" value="Search" /></div>
</td></tr></table>
</form>

<?php

//======================Search Files==========================

if ($SEARCHSTR) {
    $files1 = GetDirectory(ADMIN_CONTENT_DIR);
    $files2 = GetDirectory("$SITE_ROOT/common");
    $files3 = GetDirectory("$SITE_ROOT/helper");
    $files = array();
    foreach ($files1 as $f) $files[] = ADMIN_CONTENT_DIR."/$f";
    foreach ($files2 as $f) $files[] = "$SITE_ROOT/common/$f";
    foreach ($files3 as $f) $files[] = "$SITE_ROOT/helper/$f";
    $files[] = "$SITE_ROOT/page.php";

    printqn("<div class=`search`>
          <h2>[$SEARCHSTR] Found in Files . . .</h2>
          <ol>");

    $count=0;
    $rootcount = strlen($ROOT);
    $subcontentdir = strFrom(ADMIN_CONTENT_DIR.'/',$ROOT);
    foreach ($files as $filename) {
        $text=file_get_contents($filename);
        $fi = substr($filename,$rootcount);
        $OrTerms  = explode(' OR ',$SEARCHSTR);
        $FOUND = 0;
        foreach ($OrTerms as $terms) {
            if ($FOUND==0) {
                $AndTerms = explode(' AND ',$terms);
                foreach ($AndTerms as $aterms) {
                    $searchstr = trim($aterms);
                    if (stripos($text,$searchstr)!==false) {
                        $FOUND++;
                    }

                    if ($FOUND==count($AndTerms)) {
                        $viewtext = htmlentities($text);
                        $allterms = array_merge($OrTerms,$AndTerms);
                        for ($i=0; $i<count($allterms); $i++) {
                            $f = htmlentities($allterms[$i]);
                            $r = "<span style=\"background-color:#f00;\">$f</span>";
            		        $viewtext = str_ireplace($f,$r,$viewtext);
                        }
                        //---------------
                        $count++;
                        if (strpos($fi,'common/')!==false) {
                            $link = "..$fi{$SV}OPT=C{$SV}SP=1";
                        }
                            else $link=str_replace($subcontentdir,'',strTo("$fi",'.'));
                          printqn("<li>
            		      <a style=`font-size:1.1em; font-weight:bold; border-bottom:2px solid #036;` href=`#` onclick=`toggleDisplay('page_view$count'); return false;`>$fi</a>
            		      <a target=`_blank` class=`editbutton` href=`$ADMIN_FILE?F=$link`>Edit</a>
            			  <div id=`page_view$count` style=`font-size:0.8em; margin:1em; display:none; padding:1em; border:1px dashed #888;`>
            			  <pre>$viewtext</pre>
            			  </div>
            			  </li>");
                    }
                }
            }
        }
    }
    print '</ol>';
    if ($count==0) printqn("<h3 style=`margin-left:4em;`>[$SEARCHSTR] Not Found!</h3>");
    print '</div>';
}


?>