<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'].'/lib/mvptools.php';

if (ArrayValue(ArrayValue($_SESSION, 'SITE_ADMIN'), 'AdminLoginOK') != 'ok') {
    exit;
}
 
$SERVER = 'https://mvpprograms.com/mvp_framework_update/update.php';
 
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <title>Get Site Library Updates</title>
  <style type="text/css">
     body{background-color:#eee; font-family:Arial,Helvetica,Geneva,Sans-serif;}
     #content{margin:2em auto; background-color:#fff; padding:1em; border:1px dashed #888;}
     h1{color:#036; border-bottom:2px solid #036;}
     td{padding:2px 10px; }
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


print <<<LBLS
<form method="post" action="$THIS_PAGE">
<div style="float:right; margin-left:3px;"> 
  <a class="stdbutton" href="$THIS_PAGE">Refresh</a>
  <input class="submit" type="submit" value="Update" />
</div>
<h1>Get Site Library Updates</h1>
<br style="clear:both;" />

LBLS;

//===============================================================================


//=========== get posted variable files =============
$permissions = '';
foreach ($_POST as $key => $value){  
    list($file,$date) = explode('|',str_replace('@','.',$key));
    $NewFileContent = file_get_contents("$SERVER?F=$file");
    $filename = "$LIB/$file"; 
    $filepointer = fopen($filename,"w");
    fwrite($filepointer,$NewFileContent);
    chmod($filename,0666);
    touch($filename,$date);
    fclose($filepointer);  
}


//========== get local file list ==========
$LocalFiles = GetDirectory($LIB,'', 'htaccess');
$LocalFileList = array();
foreach ($LocalFiles as $file) {
    $date = filemtime("$LIB/$file");
    $LocalFileList[$file] = $date;
}


//========== get server file list ==========
$ServerFileList = TextBetweenArray('<file>','</file>',file_get_contents($SERVER));
rsort($ServerFileList);

echo '<table align="center">';
echo '<tr><th>File</th><th>Server Date</th><th>Local Date</th><th>Update</th></tr>';
$count = 0;
foreach ($ServerFileList as $file){
  $count++;
  $name = TextBetween('<name>','</name>',$file);
  $date = TextBetween('<date>','</date>',$file);
  echo '<tr>';
  $outdate = date("Y-m-d H:i", $date); 
  echo "<td>$name</td><td>$outdate</td>";
  $localdate = (!empty($LocalFileList[$name]))? $LocalFileList[$name] : '';
  $outlocaldate = ($localdate) ? date("Y-m-d H:i", $localdate) : '';
  $cellstyle = ($date <= $localdate) ? 'background-color:#fff' : 'background-color:#0f6';
  $checked = (($date <= $localdate) or (!$localdate)) ? '' : 'checked';
  if(!$localdate){$localdate = 'Not Found';}
  printqn("<td style=`$cellstyle`>$outlocaldate</td>");
  $value = str_replace('.','@',$name)."|$date";
  printqn("<td align=`center`><input type=`checkbox` name=`$value` $checked /></td>");
  echo "</tr>\n";
}
echo '</table>';

//===============================================================================
?>
</form>
</td></tr></table>
</body></html>