<?php
//================ Input New Page ================
require "$LIB/form_helper.php";

echo '<table class="upload" align="center"><tbody><tr><td align="left">';

$combined_files = explode("\n", $SITECONFIG['combined_files']);

$list = '';
$combined_file_array = array();
foreach ($combined_files as $line) {
    $item = strTo($line, '|');
    $combined_file_array[$item] = strFrom($line, '|');
    $list = $item . '|';
}

$form_array = array(
    "form|$THIS_PAGE_QUERY|post",
    'code|<p>',
    '@select|File Group&nbsp;|file_group|||' . $list,
    '@submit|Create/Update|COMBINE_SUBMIT|class="contentsubmit"',
    'code|</p>',
    'endform'
);

echo OutputForm($form_array, 1);

if (HaveSubmit('COMBINE_SUBMIT')) {
    $file_group  = GetPostItem('file_group');
    $group_files = explode('|', $combined_file_array[$file_group]);
    echo '<ol><li>' . implode('</li><li>', $group_files) . '</li></ol>';

    $ERROR = 0;
    foreach ($group_files as $file) {
        $file = trim($file);
        $filepath = $ROOT . $file;
        if (!file_exists($filepath)) {
            echo "<h3 style=\"background-color:#f88; color:#fff;\">$file Not Found</h3>";
            $ERROR = 1;
        }
    }

    if (empty($ERROR)) {
        $RESULT = '';
        foreach ($group_files as $file) {
            $file = trim($file);
            $contents = file_get_contents($ROOT . $file);
            // ==================== CLEAN FILE =====================
            $contents = preg_replace('/(<\?php|\?>|\/\/ .*|\/\/-.*)/', '', $contents); //remove php + //comments
            //$contents = preg_replace('/\/\*(^(\*\/).|\n)+\*\//', '', $contents);  // remove * comments
            while($replace = TextBetween('/*', '*/', $contents)) {
                $contents = str_replace("/*$replace*/", '', $contents);
            }

            $contents = preg_replace('/  +/', ' ', $contents); // extra spaces
            $contents = preg_replace("/(\n | \n)/", "\n", $contents); //trim lines
            $contents = preg_replace("/((\n|\r\n)(\n|\r\n)+)/", "\n", $contents);  // remove double lines
            $contents = trim($contents);

            $RESULT .= "//-----$file-----\n$contents\n";

        }
        $RESULT = "<?php\n$RESULT";
        $outfile = htmlentities($RESULT);
        $update_created = file_exists($ROOT . $file_group)? 'Updated' : 'Created';
        file_put_contents("$ROOT$file_group", $RESULT);
        chmod("$ROOT$file_group", 0666);
        echo "<h2>$file_group $update_created</h2>";
        echo "<pre style=\"background-color:#fff; color:#000;\">$outfile</pre>";
    }
}


echo '</td></tr></tbody></table>';
