<?php
    $SEARCHSTR = Post('SEARCHSTR');
?>

<form method="post" action="<?php echo $THIS_PAGE ?>">
<div class="formtitle">Find:</div>
<div class="forminfo"><input class="formitem" type="text" name="SEARCHSTR" value="<? echo htmlentities($SEARCHSTR) ?>" size="40" />
<a class="stdbutton" style="padding-left:20px; display:inline;
  background-image:url(/images/help.gif); background-repeat:no-repeat; background-position: 2px 2px;"
  href="#" onclick="toggleDisplay('helptext'); return false;">Help</a>
</div>
<div class="forminfo" id="helptext" style="display:none; background-color:#FFFF7F; border:1px dashed #888; padding:0px 1em;">
<p style="font-size:1.2em; font-weight:bold;">Search Help</p>
<p>Enter search string in box above. The entire string will be matched, unless:</p>
<ul>
  <li>&ldquo; <b>AND</b> &rdquo; (in uppercase) is used to separate search groups, which finds items containing all groups delimited by the &ldquo; AND &rdquo;, or</li>
  <li>&ldquo; <b>OR</b> &rdquo; (in uppercase) is used to separate search groups, which finds items containing either of the groups delimited by the &ldquo; OR &rdquo;. </li>
  <li>If both AND <i>and</i> OR are used, AND groups will be found within OR groups</li>
  </ul>

</div>
<div class="forminfo"><input class="messagesubmit" type="submit" value="Search" /></div>
</form>


<?php

$SEARCH_thisfile = $PAGE['pagename'].$SITECONFIG['contentstr'];

//======================Search Files==========================

if ($SEARCHSTR) {

    $SEARCH_files = GetDirectory($ROOT.$SITECONFIG['contentdir'], $SITECONFIG['contentstr']);
    printqn("<div class=`search`>
          <h2>[".htmlentities($SEARCHSTR)."] Found in Files . . .</h2>
    <ol>");

    $count=0;
    foreach ($SEARCH_files as $SEARCH_fi) {
        $SEARCH_titlefile = str_replace($SITECONFIG['contentstr'], $SITECONFIG['titlestr'], $SEARCH_fi);
        $SEARCH_title = TextBetween( '<name>', '</name>', file_get_contents("$ROOT{$SITECONFIG['contentdir']}/$SEARCH_titlefile") );

        if (($SEARCH_fi != $SEARCH_thisfile ) and !empty($SEARCH_title)) {
            $SEARCH_filename="$ROOT{$SITECONFIG['contentdir']}/$SEARCH_fi";
            ob_start();
            include $SEARCH_filename;
            $SEARCH_filetext = ob_get_contents();
            ob_end_clean();
            $SEARCH_filetext = strtolower(strip_tags($SEARCH_filetext));

            $SEARCH_OrTerms  = explode(' OR ',$SEARCHSTR);
            $SEARCH_FOUND = 0;
            foreach ($SEARCH_OrTerms as $SEARCH_terms) {
                if ($SEARCH_FOUND==0) {
                    $SEARCH_AndTerms = explode(' AND ',$SEARCH_terms);
                    foreach ($SEARCH_AndTerms as $SEARCH_aterms){
                    $SEARCH_searchstr = trim(strtolower($SEARCH_aterms));
                    if (strpos($SEARCH_filetext, $SEARCH_searchstr)!==false) $SEARCH_FOUND++;
                        if ($SEARCH_FOUND==count($SEARCH_AndTerms)) {
                            $count++;
                            $SEARCH_link = strTo("{$SITECONFIG['pagedir']}/$SEARCH_fi",'.').$SITECONFIG['extension'];
                            if($SEARCH_title) printqn("<li><a href=`$SEARCH_link`>$SEARCH_title</a></li>");
                        }
                    }
                }
            }
        }
    }

    print '</ol>';
    if ($count==0) echo "<h3>&hellip;Not Found!</h3>";
    print '</div>';

}
