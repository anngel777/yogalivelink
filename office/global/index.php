<?php
ini_set('display_errors','1');
//================= AUTHENTICATION BLOCK =================
require $_SERVER['DOCUMENT_ROOT'] .'/global/global_su_auth.php';

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-us">
<head>
<title>Global Files</title>
<style type="text/css">
  .viewbutton {
    background-color : #fdf;
    padding : 0px 3px;
    text-decoration : none;
    border : 1px solid #888;
  }
  .viewbutton:hover {
    background-color : #888;
    color : #fff;s
    padding : 0px 3px;
  }
  
  .loadbutton {
    background-color : #ccf;
    color : #000;
    padding : 0px 3px;
    text-decoration : none;
    border : 1px solid #888;
  }
  .loadbutton:hover {
    background-color : #888;
    color : #fff;s
    padding : 0px 3px;
  }
  
  li {
    padding : 3px;
  }
  
  pre {
  }
  
  table {
    margin : 3px;
    border : 1px dotted #888;
    background-color : #eef;
  }
  h1 {
    color:#fff; background-color:#f00; text-align:center;
  }
  body {
    background-color:#eee;
    font-family : Verdana, Arial, Helvetica, sans-serif;
  }
  #container {
    width:50%; border:1px solid #888; background-color:#fff; padding:1em; margin:2em auto;
  }
</style>
<script type="text/javascript" src="/lib/mvp.js"></script>
</head>
<body>
<div id="container">
<h1>Global Files</h1>
<ul>
<?php
$files = GetDirectory(dirname(__FILE__), '.php', 'archive/,db_info');
$count = 0;
foreach ($files as $file) {
    $count++;
    $file_contents = htmlentities(file_get_contents($file));
    
    $content = "<div id=\"file$count\" style=\"display:none;\"><table><tr><td><pre>$file_contents</pre></div></td></tr></table>";
    
    echo "<li><a class=\"loadbutton\" href=\"$file\">$file</a>&nbsp;<a class=\"viewbutton\" href=\"#\" onclick=\"toggleDisplay('file$count'); return false;\">View</a>
    $content
    </li>";
}


?>
</ul>

</div>
</body>

</html>