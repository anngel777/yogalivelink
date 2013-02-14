<?php
/* ===============================================

Must set the following: 

    $RemoteFolder = '';
    $LocalFolder  = '';
    $include_list = '';
    $exclude_list = 'htaccess,Thumbs.db';
    $title = 'title';


================================================== */
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/mvptools.php';
if (empty($RemoteFolder) or empty($LocalFolder)) {
    MText('ERROR', 'Folder Update Not Setup');
}
echo $DOCTYPE_XHTML;
?>
<head>
  <title><?php echo $title ?></title>
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
  <script type="text/javascript">
     function getId(id){return document.getElementById(id);}
     function selectAll(){
         var i=1;
         while (getId('checkbox'+i)){ getId('checkbox'+i).checked='true';  i++; }
     }
  </script>
  </head>
<body>

<table id="content" align="center">
<tr><td>
<form method="post" action="<?php echo $THIS_PAGE  ?>">
<div style="float:right; margin-left:3px;"> 
  <a class="stdbutton" href="<?php echo $THIS_PAGE  ?>">Refresh</a>
  <input class="submit" type="submit" value="Update" />
</div>
<h1><?php echo $title ?></h1>

<div style="float:right; margin-left:3px;"> 
  <a class="stdbutton" href="#" onclick="selectAll(); return false;">Select All</a>
</div>

<br style="clear:both;" />


<?php
//===============================================================================

$ServerFiles    = GetDirectory($RemoteFolder, $include_list, $exclude_list);
$ServerFileList = array();
foreach($ServerFiles as $file){
  $date = filemtime("$RemoteFolder/$file");
  $ServerFileList[] = "<file><date>$date</date><name>$file</name></file>";
}
rsort($ServerFileList);


//=========== get posted variable files =============
foreach ($_POST as $key => $value){  
  list($file,$date) = explode('|',str_replace('@','.',$key));
  copy("$RemoteFolder/$file", "$LocalFolder/$file");
}


//========== get local file list ==========

$LocalFiles = GetDirectory($LocalFolder,'', 'htaccess');
$LocalFileList = array();
foreach ($LocalFiles as $file) {
    $date = filemtime("$LocalFolder/$file");
    $LocalFileList[$file] = $date;
}


//========== get server file list ==========



echo '<table align="center">';
echo '<tr><th>File</th><th>Server Date</th><th>Local Date</th><th>Update</th></tr>';
$count = 0;
foreach ($ServerFileList as $file){
  $count++;
  $outname = $name = TextBetween('<name>','</name>',$file);
  $date = TextBetween('<date>','</date>',$file);
  echo '<tr>';
  $outdate = date("Y-m-d-H:i", $date); 
  
  if (strpos($name, DIRECTORY_SEPARATOR) !== false) {
        $basename = basename($name);
        $outname = '<span style="color:#f00">' . strTo($name, $basename) . '</span>' . $basename;
  }
  
  echo "<td>$outname</td><td>$outdate</td>";
  $localdate = array_key_exists($name,$LocalFileList)? $LocalFileList[$name] : '';
  $outlocaldate = ($localdate) ? date("Y-m-d-H:i", $localdate) : '';
  $cellstyle = ($date <= $localdate) ? 'background-color:#fff' : 'background-color:#0f6';
  $checked = (($date <= $localdate) or (!$localdate)) ? '' : 'checked';
  if(!$localdate){$localdate = 'Not Found';}
  printqn("<td style=`$cellstyle`>$outlocaldate</td>");
  $value = str_replace('.','@',$name)."|$date";
  printqn("<td align=`center`><input type=`checkbox` id=`checkbox$count` name=`$value` $checked /></td>");
  echo "</tr>\n";
}
echo '</table>';

//===============================================================================
?>
</form>
</td></tr></table>
</body></html>