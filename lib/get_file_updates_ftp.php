<?php
ini_set('display_errors','1');
require $_SERVER['DOCUMENT_ROOT'].'/lib/mvptools.php';
session_start();
if (empty($_SESSION['SITE_ADMIN']['AdminLoginOK'])) {
    Mtext('Error', 'Not Logged In!');
}

$SERVER = 'https://mvpprograms.com/mvp_framework_update/update.php';


?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <title>Get Site File Updates</title>
  <style type="text/css">
     body{background-color:#eee; font-family:Arial,Helvetica,Geneva,Sans-serif;}
     #content{margin:2em auto; background-color:#fff; padding:1em; border:1px dashed #888;}
     h1{color:#036; border-bottom:2px solid #036;}
     td{padding:2px 10px; white-space:nowrap;}
     th{padding:2px 10px; background-color:#888; color:#fff; font-weight:bold;}
     a.stdbutton, .submit{
        font-family:Arial,Helvetica,Geneva,Sans-serif;
        font-size:0.9em; text-align:center; text-decoration:none; padding:2px 0.4em;
        background-color:#eee; color:#000; border:1px solid;
        border-color:#ddd #666 #555 #ccc;
     }
     .submit{padding:1px 0.4em;}
     a.stdbutton:active{border-color:#555 #ccc #ddd #666;}
     a.stdbutton:hover, .submit:hover{background-color: #888; color:#fff; cursor:pointer;}

  </style>
  </head>
<body>

<table id="content" align="center">
<tr><td>

<?php
if (!empty($_SESSION['SITE_ADMIN']['FTP'])) {
    extract($_SESSION['SITE_ADMIN']['FTP'], EXTR_OVERWRITE);
} else {
    $FTP_SERVER_ADDR = Post('FTP_SERVER_ADDR')? Post('FTP_SERVER_ADDR') : $_SERVER['SERVER_ADDR'];
    $FTP_ROOT = Post('FTP_ROOT')? Post('FTP_ROOT') : $ROOT;
    $FTP_USER = Post('FTP_USER');
    $FTP_PASS = Post('FTP_PASS');
}

$have_wo       = file_exists("$ROOT/wo");
$have_classes  = file_exists("$ROOT/classes/Lib");
$have_tiny_mce = file_exists("$ROOT/jslib/tiny_mce");

if ($have_wo or $have_classes or $have_tiny_mce) {
    $DIR      = Post('DIR')? Post('DIR') : 'lib';
    $s1 = $s2 = $s3 = $s4 = '';
    $selected = ' selected="selected"';
    switch ($DIR) {
        case 'lib'            : $s1 = $selected; break;
        case 'wo'             : $s2 = $selected; break;
        case 'classes/Lib'    : $s3 = $selected; break;
        case 'jslib/tiny_mce' : $s4 = $selected; break;
    }

    $option2 = ($have_wo)? "<option$s2>wo</option>\n" : '';
    $option3 = ($have_classes)? "<option$s3>classes/Lib</option>\n" : '';
    $option4 = ($have_tiny_mce)? "<option$s4>jslib/tiny_mce</option>\n" : '';


    $select = "Folder:
    <select name=\"DIR\" onchange=\"document.update_form.submit();\">
        <option$s1>lib</option>
        $option2$option3$option4
    </select>";

} else {
    $DIR = 'lib';
    $select = '';
}
print <<<LBLS
<h1>Get Site File Updates ($DIR)</h1>
<form name="update_form" method="post" action="$THIS_PAGE">
<div style="float:right; margin-left:3px;">
  <input class="submit" name="REFRESH" type="submit" value="Refresh" />
  <input class="submit" name="SUBMIT"  type="submit" value="Update" /><br /><br />
  $select
</div>
  <table border="0" cellspacing="1" cellpadding="0" style="margin-right:200px;"><tbody>
  <tr><td align="right">FTP Server Address:</td><td><input type="text" name="FTP_SERVER_ADDR" value="$FTP_SERVER_ADDR" size="30" maxlength="255" /></td></tr>  
  <tr><td align="right">FTP Root:</td><td><input type="text" name="FTP_ROOT" value="$FTP_ROOT" size="30" maxlength="255" /></td></tr>
  <tr><td align="right">FTP User:</td><td><input type="password" name="FTP_USER" value="$FTP_USER" size="20" maxlength="255" /></td></tr>
  <tr><td align="right">FTP Pass:</td><td><input type="password" name="FTP_PASS" value="$FTP_PASS" size="20" maxlength="255" /></td></tr>
  </tbody></table>
<br style="clear:both;" />

LBLS;

$conn_id = ftp_connect($FTP_SERVER_ADDR);
if (!$conn_id) {
    echo '<h2>FTP Connection Failure!</h2>';
}
$ftp_result = (!empty($FTP_USER) and !empty($FTP_PASS))? ftp_login($conn_id, $FTP_USER, $FTP_PASS) : false;

// check path
if ($ftp_result) {
    $hostname = "ftp://$FTP_USER:$FTP_PASS@" . $FTP_SERVER_ADDR . $FTP_ROOT . '/lib/mvptools.php';
    $context  = stream_context_create(array('ftp' => array('overwrite' => false)));
    $result   = @file_get_contents($hostname, 0, $context, 0, 10);
    if (!$result) {
        echo '<h2>FTP Path Error</h2>';
        exit;
    }
}

if (!$ftp_result) {
    if (empty($FTP_USER) or empty($FTP_PASS)) {
        echo '<h2>FTP Information Missing</h2>';
    } else {
        echo '<h2>FTP Information Not Valid</h2>';
    }
    exit;

} else {

    //===============================================================================


    //=========== get posted variable files =============
    $permissions = '';
    if (Post('SUBMIT')) {
        foreach ($_POST as $key => $value){
            if (strpos($key, 'FILE|') !== false) {
                list($dummy, $file, $date) = explode('|',str_replace('@','.',$key));
                $new_file_content = gzuncompress(file_get_contents("$SERVER?DIR=$DIR&C=1&F=$file"));
                $ftp_file_path = "$FTP_ROOT/$DIR/$file";
                $file_dir = dirname("$ROOT/$DIR/$file");
                if (!file_exists($file_dir)) {
                    ftp_mkdir($conn_id, dirname($ftp_file_path));
                }

                if ($new_file_content) {
                    WriteFileFtp($FTP_SERVER_ADDR, $FTP_USER, $FTP_PASS, $ftp_file_path, $new_file_content);
                } else {
                    echo "<p>Error in file: $file</p>";
                }
            }
        }
    }


    //========== get local file list ==========
    $path = $ROOT . '/' . $DIR;
    $LocalFiles = GetDirectory($path, '', 'htaccess');
    $LocalFileList = array();
    foreach ($LocalFiles as $file) {
        $date = filemtime("$path/$file");
        $LocalFileList[$file] = $date;
    }


    //========== get server file list ==========
    $ServerFileList = TextBetweenArray('<file>','</file>',file_get_contents("$SERVER?DIR=$DIR"));
    rsort($ServerFileList);

    echo '<table align="center">';
    echo '<tr><th>File</th><th>Server Date</th><th>Local Date</th><th>Update</th></tr>';
    $count = 0;
    foreach ($ServerFileList as $file){
        $count++;
        $name = TextBetween('<name>','</name>',$file);
        $date = TextBetween('<date>','</date>',$file);
        echo '<tr>';
        $outdate = date("Y-m-d-H:i", $date);
        echo "<td>$name</td><td>$outdate</td>";
        $localdate = ArrayValue($LocalFileList, $name);
        $outlocaldate = ($localdate) ? date("Y-m-d-H:i", $localdate) : '';
        $cellstyle = ($date <= $localdate) ? 'background-color:#fff' : 'background-color:#0f6';
        $checked = (($date <= $localdate) or (!$localdate)) ? '' : 'checked';
        if(!$localdate){
            $localdate = 'Not Found';
        }
        printqn("<td style=`$cellstyle`>$outlocaldate</td>");
        $value = 'FILE|' . str_replace('.','@',$name)."|$date";
        printqn("<td align=`center`><input type=`checkbox` name=`$value` $checked /></td>");
        echo "</tr>\n";
    }
    echo '</table>';

    //===============================================================================
}
?>
</form>
</td></tr></table>
</body></html>