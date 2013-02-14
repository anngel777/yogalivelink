<?php
//======================REPLACE IN FILES==========================

//printqn("<form method=`post` action=`$ADMIN_FILE`>");

SetPost('FINDSTR REPLACESTR FOLDER INCLUDE EXCLUDE IGNORE_CASE DISPLAYREPLACE REPLACEALL', 'T');

if (!Post('FOLDER')) {
    $_POST['FOLDER'] =  $SITE_ROOT;
}

if (!$DISPLAYREPLACE and !$REPLACEALL) {
    $_POST['INCLUDE'] = '.php,.def,.htm,.css,.js';
    $_POST['EXCLUDE'] = 'archive' . DIRECTORY_SEPARATOR;
}

require_once "$LIB/form_helper.php";
$FormPrefix = '';

$replace_all = ($DISPLAYREPLACE)? "submit|Replace All|REPLACEALL" : '';

$search_form = "

    form|$ADMIN_FILE|post|$E
    text|Find|FINDSTR||60|255|$E
    text|Replace With|REPLACESTR||60|255|$E
    code|<div style=\"margin-left:100px; font-size:0.9em;\">|$E
    text|Include|INCLUDE||40|255|$E
    text|Exclude|EXCLUDE||40|255|$E
    code|</div>|$E
    text|Search Directory|FOLDER||60|255|$E
    submit|Display|DISPLAYREPLACE|$E
    $replace_all|$E
    endform|$E
";

$ERROR = '';
echo '<div id="seach_header">';
echo OutputForm($search_form, 1);
echo '</div>';


/*
// <table align="center" style="background-color:#eee;">
// <tr><td>
// <div style="float:left; width:150px; text-align:right; font-weight:bold;">Find:</div>
// <div style="margin-left:160px;">
// <input class="formitem" type="text" name="FINDSTR" value="<?php echo htmlentities($FINDSTR) ?>" size="60" /></div>

// <div style="float:left; width:150px; text-align:right; font-weight:bold;">Replace&nbsp;With:</div>
// <div style="margin-left:160px;">
// <input class="formitem" type="text" name="REPLACESTR" value="<?php echo htmlentities($REPLACESTR) ?>" size="60" /></div>

// <div class="formtitle">Search Directory:</div>
// <div class="forminfo"><input class="formitem" name="FOLDER" size="60" type="text" value="<?php echo $FOLDER; ?>" /></div>


// print '<div style="margin-left:160px;">
  // <input class="messagesubmit" name="DISPLAYREPLACE" type="Submit" value="Display" />';

  // if ($DISPLAYREPLACE) {
    // print '&nbsp;<input class="messagesubmit" name="REPLACEALL" type="Submit" value="Replace All" />';
// }

//print '</div>';
*/

//======================Search Files==========================


if ($FINDSTR) {
    $files = GetDirectory($FOLDER, $INCLUDE, $EXCLUDE);

    $FINDSTR_OUT = htmlspecialchars($FINDSTR);
    printqn("<div class=`search` style=`padding:0.5em;`>
          <h2>[$FINDSTR_OUT] Found in Files . . .</h2>
          <ol>");

    $OLDtext = array('^T','^CR');
    $NEWtext = array("\t","\n");

    $FINDSTR    = str_replace($OLDtext,$NEWtext,$FINDSTR);
    $REPLACESTR = str_replace($OLDtext,$NEWtext,$REPLACESTR);

    $count=0;
    $OrTerms = explode('|',$FINDSTR);
    $ReplaceTerms = explode('|',$REPLACESTR);

    foreach ($files as $fi) {
        $filename = $FOLDER . "/$fi";
        $text=(file_get_contents($filename));

        $newtext  = str_replace($OrTerms,$ReplaceTerms,$text);
        $viewtext = htmlentities($text);
        for ($i=0; $i<count($OrTerms); $i++) {
            $f = htmlentities($OrTerms[$i]);
            $r = "<span style=\"background-color:#f00;\">$ReplaceTerms[$i]</span>";
            $viewtext = str_replace($f,$r,$viewtext);
        }

        if ($newtext != $text) {
            $count++;
            $link=strtok("$fi",'.');
            printqn("<li>
         <a style=`font-size:1.1em; font-weight:bold; border-bottom:2px solid #036;` href=`#` onclick=`toggleDisplay('replace_page_view$count'); return false;`>$fi</a>
         <a target=`_blank` class=`editbutton` href=`$ADMIN_FILE?F=$link`>Edit</a>
            <div id=`replace_page_view$count` style=`font-size:0.8em; margin:1em; display:none; padding:1em; border:1px dashed #888;`>
            <pre>$viewtext</pre>
            </div>
            </li>");

            if ($REPLACEALL) {
                AdminWriteFile($filename, $newtext);
            }
        }
    }
    print '</ol>';
    if ($count==0) {
        printqn("<h3 style=`margin-left:4em;`>[$FINDSTR_OUT] Not Found!</h3>");
    }
}
print '</div>';
print '</td></tr></table></form>';
