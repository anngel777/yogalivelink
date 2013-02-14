<?php
//======================Search Within Files==========================

SetPost('SEARCHSTR FOLDER INCLUDE EXCLUDE PROCESSFIND PHP IGNORE_CASE', 'T');

$htmlarray = explode(' ', 'a td tr table th ol ul li i b p h1 h2 h3 h4 h5 h6 div br sup sub u span img');

function FormatFile($FILE)
{
    global $htmlarray;

    $linecount = substr_count($FILE,"\n") + 1;
    $linenum = '<code class="search_num">' . implode(range(1, $linecount) , '<br />') . '</code>';
    $filecontent = highlight_string($FILE,true);
    foreach ($htmlarray as $code) {
        $codearray = array_unique(TextBetweenArray("&lt;$code",'&gt;',$filecontent));
        foreach ($codearray as $c) {
            $filecontent = str_ireplace("&lt;$code$c&gt;","<span class=\"html\">&lt;$code$c&gt;</span>",$filecontent);
        }
        $filecontent = str_ireplace("&lt;$code&gt;","<span class=\"html\">&lt;$code&gt;</span>",$filecontent);
        $filecontent = str_ireplace("&lt;/$code&gt;","<span class=\"html\">&lt;/$code&gt;</span>",$filecontent);
    }

    return "$linenum\n<div class=\"search_filecontent\">$filecontent</div>";
}


if (empty($PROCESSFIND)) {
    $INCLUDE = '.php,.htm,.css,.js';
    $EXCLUDE = 'archive' . DIRECTORY_SEPARATOR;
}


if (!$FOLDER) {
    $FOLDER =  $SITE_ROOT;
}


?>
<div id="seach_header">
<form method="post" action="<?php echo $ADMIN_FILE; ?>">
<div class="formtitle">Find:</div>
<div class="forminfo"><input class="formitem" type="text" name="SEARCHSTR" value="<?php echo htmlentities($SEARCHSTR); ?>" size="60" />
<a class="editbutton" href="#" onclick="toggleDisplay('seach_helptext'); return false;">Help <span style="border: 1px solid #888; background-color:#ff0; color:#f00;">&nbsp;?&nbsp;</span></a>
</div>
<div id="seach_helptext" style="display:none;">
<p style="font-size:1.2em; font-weight:bold;">Search Help</p>
<p>Enter search string in box above. The entire string will be matched, unless:</p>
<ul style="text-align:left; margin-left:4em;">
  <li>&ldquo; <b>AND</b> &rdquo; (in uppercase) is used to separate search groups, which finds items containing all groups delimited by the &ldquo; AND &rdquo;, or</li>
  <li>&ldquo; <b>OR</b> &rdquo; (in uppercase) is used to separate search groups, which finds items containing either of the groups delimited by the &ldquo; OR &rdquo;. </li>
  <li>If both AND <i>and</i> OR are used, AND groups will be found within OR groups</li>
  <li>Include and Exclude are comma-separated lists used in file selection</li>
</ul>
</div>

<div class="formtitle2">Ignore Case:</div>
<div class="forminfo2"><input class="formitem" name="IGNORE_CASE" type="checkbox" value="1" <?php echo ($IGNORE_CASE)? 'checked="checked"' : ''; ?> /></div>


<div class="formtitle2">File Include Strings:</div>
<div class="forminfo2"><input class="formitem" name="INCLUDE" size="40" type="text" value="<?php echo $INCLUDE; ?>" /></div>

<div class="formtitle2">File Exclude Strings:</div>
<div class="forminfo2"><input class="formitem" name="EXCLUDE" size="40" type="text" value="<?php echo $EXCLUDE; ?>" /></div>

<div class="formtitle2">PHP Highlight:</div>
<div class="forminfo2"><input class="formitem" name="PHP" size="40" type="checkbox" value="1" "<?php echo (empty($PHP))?'':'checked="checked"'; ?>" /></div>


<div class="formtitle">Search Directory:</div>
<div class="forminfo"><input class="formitem" name="FOLDER" size="60" type="text" value="<?php echo $FOLDER; ?>" /></div>

<div class="forminfo"><input class="messagesubmit" name="PROCESSFIND" type="Submit" value="Search" /></div>
</form>
</div>
<?php

//======================Search Files==========================

if ($SEARCHSTR) {
    $files = GetDirectory($FOLDER, $INCLUDE, $EXCLUDE);

    printqn("<div class=`search`>
          <h2>[".htmlentities($SEARCHSTR)."] Found in Files . . .</h2>
          <ol>");

    $count = 0;
    $replace_diff = strlen('<span class="found"></span>');
    
    foreach ($files as $fi) {
        $filename  = "$FOLDER/$fi";
        $filetext  = file_get_contents($filename);

        $or_terms  = explode(' OR ', $SEARCHSTR);

        $searchterms  = array();
        $replaceterms = array();
        foreach ($or_terms as $terms) {
            $and_terms = explode(' AND ', $terms);

            foreach ($and_terms as $and_term) {
                if ($PHP) {
                    $term = highlight_string(trim($and_term), true);
                } else {
                    $term = htmlentities(trim($and_term));
                }
                $searchterms[]  = $term;
                $replaceterms[] = "<span class=\"found\">$term</span>";
            }
        }

        $FOUND = 0;
        foreach ($or_terms as $terms) {
            if ($FOUND==0) {
                $and_terms = explode(' AND ',$terms);
                foreach ($and_terms as $and_term) {

                    $searchstr = trim($and_term);

                    if ($IGNORE_CASE) {
                        if (stripos($filetext, $searchstr) !== false) {
                            $FOUND++;
                        }
                    } else {
                        if (strpos($filetext, $searchstr) !== false) {
                            $FOUND++;
                        }
                    }

                    if ($FOUND == count($and_terms)) {
                        $count++;

                        printqn("<li><a class=`stdbutton` href=`#` onclick=`toggleDisplay('textfield$count'); return false;`>$fi</a>");
                        $f = $FOLDER . DIRECTORY_SEPARATOR . $fi;
                        $f = preg_replace('/\\' . DIRECTORY_SEPARATOR . '+/', '/', $f);
                        if (strpos($f, ADMIN_CONTENT_DIR) !== false) {
                            $link = strFrom(RemoveExtension($f), ADMIN_CONTENT_DIR . '/');                            
                        } else {
                            $link = "$f{$SV}OPT=C{$SV}SP=1";
                        }
                        printqn("&nbsp;<a target=`_blank` class=`editbutton` style=`padding:0 0.4em;` href=`$ADMIN_FILE?F=$link`>Edit</a>");
                        
                        if ($PHP) {
                            $text = FormatFile($filetext);
                        } else {
                            $text = htmlentities($filetext);
                        }

                        if ($IGNORE_CASE) {
                            foreach($searchterms as $search_term) {
                                $offset = 0;
                                $length = strlen($search_term);
                                do {
                                    $pos = stripos ($text, $search_term, $offset);
                                    if ($pos !== false) {
                                        $found_term = substr($text, $pos, $length);
                                        $text1 = substr($text, 0, $pos);
                                        $text2 = '<span class="found">' . $found_term . '</span>';
                                        $text3 = substr($text, $pos + $length);
                                        $text = $text1 . $text2 . $text3;
                                        $offset = $pos + $length + $replace_diff;
                                    }

                                } while ($pos !== false);
                            }
                        } else {
                            $text = str_replace($searchterms, $replaceterms, $text);
                        }

                        if ($PHP) {
                            printqn("
                        <div style=`display:none;` class=`fileview` id=`textfield$count`>
                            <div class=`php`>$text</div>
                        </div>");
                        } else {
                            printqn("<div style=`display:none;` class=`fileview` id=`textfield$count`><pre>$text</pre></div>");
                        }

                        print '</li>';
                    }
                }
            }
        }
    }
    print '</ol>';
    if ($count==0) {
        printqn("<h3 style=`margin-left:4em;`>[$SEARCHSTR] Not Found!</h3>");
    }
    print '</div>';
}
