<div class="formdiv">
<?php

SetPost('FOLDER1 FOLDER2 SUBMIT UPDATE');

$FOLDER1 = preg_replace('/^\/+/', '', $FOLDER1);
$FOLDER2 = preg_replace('/^\/+/', '', $FOLDER2);

$DATE_FORMAT  = 'Y-m-d H:i';  //'Y-m-d-H:i:s' has problems with seconds truncated on some files

$EXCLUDE_LIST = '.htaccess,archive/,logs/,tmp/,logs/,.log,.dat,.csv';

if ($SUBMIT) {
    if (empty($FOLDER1)) {
        echo "<h3>Error: FOLDER 1 missing</h3>";
    }
    if (empty($FOLDER2)) {
        echo "<h3>Error: FOLDER 2 missing</h3>";
    }
}

if ($UPDATE) {
    ProcessUpdates($FOLDER1, $FOLDER2);
}


print <<<LBLS
<form method="post" action="[[PAGELINKQUERY]]">
<div>
  <div style="text-align:right;"><a class="stdbuttoni" href="[[PAGELINKQUERY]]">Refresh</a></div>
  <p><b>Folder 1:</b>&nbsp;<input class="FOLDER1" type="text" name="FOLDER1" value="$FOLDER1" size="40" maxlength="255" /><br />
  <b>Folder 2:</b>&nbsp;<input class="FOLDER2" type="text" name="FOLDER2" value="$FOLDER2" size="40" maxlength="255" /></p>
  <p class="center"><input class="contentsubmit s15" name="SUBMIT" type="submit" value="Compare" /></p>
</div>

LBLS;

if (empty($FOLDER1) or empty($FOLDER2)) {
    echo '</form></div>';
    return;
}


//===============================================================================


//========== get server file list ==========
$folder1_filenames = GetDirectory("$ROOT/$FOLDER1", '', $EXCLUDE_LIST);
$folder2_filenames = GetDirectory("$ROOT/$FOLDER2", '', $EXCLUDE_LIST);
$folder1_files = array();
$folder2_files = array();

$folder1_file_sizes = array();
$folder2_file_sizes = array();

foreach ($folder1_filenames as $file) {
    $file_path = "$ROOT/$FOLDER1/$file";
    $folder1_files[$file] = date($DATE_FORMAT, filemtime($file_path));
    $folder1_file_sizes[$file] = number_format(filesize($file_path));
}

foreach ($folder2_filenames as $file) {
    $file_path = "$ROOT/$FOLDER2/$file";
    $folder2_files[$file] = date($DATE_FORMAT, filemtime($file_path));
    $folder2_file_sizes[$file] = number_format(filesize($file_path));
}

$file_list = array_unique(array_merge(array_keys($folder1_files), array_keys($folder2_files)));
natcasesort($file_list);

$count = 0;
echo '<table id="file_list" cellspacing="1" border="0" cellpadding="0"><tbody>';
echo '<tr><th>No.</th><th>File</th><th>' . $FOLDER1 .'</th><th></th><th>' . $FOLDER2 . '</th></tr>';
foreach ($file_list as $file) {
    $folder1_date  = ArrayValue($folder1_files, $file);
    $folder2_date  = ArrayValue($folder2_files, $file);

    $folder1_size  = ArrayValue($folder1_file_sizes, $file);
    $folder2_size  = ArrayValue($folder2_file_sizes, $file);

    if ($folder1_size) {
        $folder1_size = " [$folder1_size]";
    }

    if ($folder2_size) {
        $folder2_size = " [$folder2_size]";
    }

    if ($folder1_date != $folder2_date) {
        $count++;
        if (!$folder1_date) {
            $folder1_class = 'missing_file';
        } elseif(!$folder1_date) {
            $folder1_class = 'only_file';
        } elseif($folder1_date > $folder2_date) {
            $folder1_class = 'newer_file';
        } else {
            $folder1_class = 'older_file';
        }

        if (!$folder2_date) {
            $folder2_class = 'missing_file';
        } elseif(!$folder2_date) {
            $folder2_class = 'only_file';
        } elseif($folder2_date > $folder1_date) {
            $folder2_class = 'newer_file';
        } else {
            $folder2_class = 'older_file';
        }

        $name = HexEncodeString($file);
        $align = 'center';
        $radio = '';
        if ($folder2_date) {
            $radio  .= '<input name="f_' . $name . '"
                onclick="if(this.checked){$(\'#td_' . $name . '\').css(\'background-color\', \'#ff7\');}"
                type="radio" value="-1" />&larr;&nbsp;';
        } else {
            $align = 'right';
        }
        $radio .= '<input name="f_' . $name . '"
                onclick="if(this.checked){$(\'#td_' . $name . '\').css(\'background-color\', \'#fff\');}"
                type="radio" value="0" checked="checked" />';
        if ($folder1_date) {
            $radio .= '&nbsp;&rarr;<input  name="f_' . $name . '"
                onclick="if(this.checked){$(\'#td_' . $name . '\').css(\'background-color\', \'#ff0\');}"
                type="radio" value="1" />';
        } else {
            $align = 'left';
        }


        echo "
        <tr>
            <td>$count.</td>
            <td>$file</td>
            <td class=\"$folder1_class\">$folder1_date $folder1_size</td>
            <td align=\"$align\" style=\"white-space:nowrap;\" id=\"td_$name\">$radio</td>
            <td class=\"$folder2_class\">$folder2_date $folder2_size</td>
        </tr>\n";
    }

}
echo '<tr><th colspan="3"></th><th align="center"><input class="contentsubmit" type="submit" name="UPDATE" value="Update" /></th><th></th></tr>
</tbody></table>
</form>
</div>';

function ProcessUpdates($FOLDER1, $FOLDER2)
{
    global $ROOT, $DATE_FORMAT;
    echo '<h3>Updates</h3>';
    foreach ($_POST as $key => $value) {
        if (($value != 0) and (substr($key, 0, 2) == 'f_')) {
            $file = HexDecodeString(substr($key, 2));

            if ($value == -1) {
                $source_file = "$FOLDER2/$file";
                $dest_file   = "$FOLDER1/$file";

            } else {
                $source_file = "$FOLDER1/$file";
                $dest_file   = "$FOLDER2/$file";
            }
            $source = "$ROOT/$source_file";
            $destination = "$ROOT/$dest_file";
            $file_date = filemtime($source);
            $file_date_out = date($DATE_FORMAT, $file_date);
            echo "&bull; $source_file &rarr;<br />&nbsp;&nbsp;&nbsp;&nbsp;$dest_file [$file_date_out]<br />";

            if (file_exists($destination)) {
                unlink($destination);
            }

            $dir = dirname($destination);
            if (!file_exists($dir)) {
                if (mkdir($dir)) {
                    chmod($dir, 0777);
                } else {
                    echo '<span class="error_text">Could not create directory [' . $dir . ']</span><br />';
                }
            }

            //Need to use exec because copy does not maintain the file dates
            //$result = exec("cp -p '$source' '$destination'");
            $result = exec("cp -p '$source' '$destination'");
            echo "$result<br />";
        }
    }
}