<?php
//===================UPDATE LOG FILE======================
function LogUpdate($user, $item, $file)
{
    $line     = date("Y-m-d:H:i").'|'.$user.'|'.$item.'|'.$file."\n";
    //$filename = ADMIN_FILES_DIR.'/logfile.dat';
    $filename = 'logfile.dat';
    append_file($filename, $line);
    return;
}

//===================VIEW LOG FILE======================
function ViewAdminLogFile()
{
    //$filename = ADMIN_FILES_DIR.'/logfile.dat';
    $filename = 'logfile.dat';
    $lines    = file($filename);
    rsort($lines);
    echo '<div id="viewlogdiv">';
    foreach ($lines as $line) {
        echo "$line<br />";
    }
    echo '</div>';
}
