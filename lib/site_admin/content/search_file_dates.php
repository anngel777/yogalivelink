<div id="searchdiv">
<?php
include "$LIB/form_helper.php";

AddStyleSheet('/GROUP/jslib/themes/base/ui.core.css;jslib/themes/base/ui.theme.css;jslib/themes/base/ui.datepicker.css');
AddStyle('
    #searchdiv {min-height:220px; background-color : #fff;}
    #dialogcontent {background-color : #fff;}
    .TABLE_DISPLAY td {white-space:nowrap;}
    #dateinput {width:300px; text-align:center; white-space:nowrap;}
');

$form = "
  form|[[PAGELINKQUERY]]|post|$E
  code|<p id=\"dateinput\">|$E
  @datepick|<b>Search Date</b>&nbsp;|search_date|N|NOW|NOW|addDatePick|$E
  @submit|Submit|SUBMIT|$E
  code|</p>|$E
  endform|$E
";

echo OutputForm($form, Post('SUBMIT'));

if (haveSubmit('SUBMIT')) {
    $search_date = GetPostItem('search_date');

    $files = GetDirectory("$ROOT", '', 'archive/,cache/,.dat,.log,Thumbs.db');

    $new_files = array();
    foreach ($files as $file) {
        $date = filemtime("$ROOT/$file");
        if (date('Y-m-d H:i:s', $date) > $search_date) {
            $new_files[] = "$date|$file";
        }
    }
    rsort($new_files);
    echo '<table cellspacing="1" class="TABLE_DISPLAY" cellpadding="0"><tbody>' . "\n";

    $count = 0;
    $even = false;
    $day = '';
    foreach ($new_files as $line) {
        list($date, $file) = explode('|', $line);
        $tdate = date('Y-m-d', $date);
        $date = date('Y-m-d H:i:s', $date);
        if (!empty($day)) {
            if ($tdate != $day) {
                echo '<tr><th colspan="3"></th></tr>';
            }
        }
        $day = $tdate;
        $count++;
        $class = $even? 'even' : 'odd';
        $even = !$even;
        echo "<tr class=\"$class\"><th>$count.</th><td>$file</td><td>$date</td></tr>\n";
    }
    echo '</tbody></table>';
}
echo '
</div>';