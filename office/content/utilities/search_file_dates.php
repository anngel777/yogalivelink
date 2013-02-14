<h1>Search File Dates</h1>
<div style="width:1px; height:200px; float:left;"></div>
<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: search_file_dates.php
    Description: Search files based on date edited
==================================================================================== */

$form = "
  form|@@PAGELINKQUERY@@|post|$E
  code|<p class=\"center\" style=\"white-space:nowrap\">|$E
  @text|<b>Search Date</b>&nbsp;|search_date|N|10|10|class=\"date_entry\"|$E
  @submit|Submit|SUBMIT|$E
  code|</p>|$E
  endform|$E
";

echo OutputForm($form, Post('SUBMIT'));

if (haveSubmit('SUBMIT')) {
    $search_date = GetPostItem('search_date');

    $files = GetDirectory("$ROOT", '', 'archive,.dat,.log,Thumbs.db');

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