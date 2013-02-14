<?php
printqn("<div class=`search`>
      <h2>Links Found in Files . . .</h2>
      <ol>");


$linkfiles = GetDirectory(ADMIN_CONTENT_DIR, ADMIN_CONTENT_STR);
  
if (!empty($DocLinkDirs)) {
    foreach ($DocLinkDirs as $dir) {
        $docfiles = GetDirectory("$ROOT$dir",'');
        for ($i=0; $i< count($docfiles); $i++) {
            $docfiles[$i]=substr($dir,1).'/'.StrTo($docfiles[$i],'.');
        }
        $files = array_merge($files,$docfiles);
    }
}

$count=0;
foreach ($linkfiles as $fi) {
    $filename=ADMIN_CONTENT_DIR."/$fi";
    $text=file_get_contents($filename); 
    $links = TextBetweenArray('href="','"',$text);
    if (count($links) > 0) {
        $pagelink = strTo("$fi",'.');
        printqn("<li><a href=`$ADMIN_FILE?F=$pagelink`>$fi</a>");
        print '<ul style="text-align:left; margin-left:3em;">';
        foreach ($links as $link) {
            if ((strpos($link,'#')===false) and ($link!='/')) {
                if (strpos($link,'http')===false) {
                    $testlink = strTo($link,'.');
                    $testlink = preg_replace('/(;|\?|:).+$/', '', $testlink);
                    $testlink = preg_replace('/@+.*@/', '', $testlink); // remove @ swap tags
                    //$testlink = strTo($testlink,':');
                    $testlink = strFrom($testlink, $SITECONFIG['pagedir']);
                    if (substr($testlink,0,1)=='/') $testlink = substr($testlink,1);
                    $flag  = in_array($testlink, $files) ? '' : " - not found";
                    $style = ($flag)? ' style="background-color:#f00;"' : '';
                    printqn("<li><span$style>$link$flag</span></li>");
                } else {
                    $style = ' style="background-color:#ff7;"';
                    printqn("<li><span$style><a target=`_blank` href=`$link`>$link</a></span></li>");
                }
            }
        }
      printqn('</ul></li>');
    }
  }

print '</ol>';
print '</div>';
