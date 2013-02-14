<?php
$FILE = Get('F');
if (!$FILE) {
    AddError('No File!');
    return;
}
if (!Get('OPT')) { 
    $FILE = $ADMIN->Root_Content_Dir . "/$FILE" . $ADMIN->Admin_Content_Str;
}

if (!(file_exists($FILE))) {
    AddError("FILE NOT FOUND - $FILE");
    return;
} else {

    $html='a td tr table th ol ul li i b p h1 h2 h3 h4 h5 h6 div br sup sub u span img';
    $htmlarray = explode(' ',$html);

    $linenum = '<code class="num">' . implode(range(1, count(file($FILE))) , '<br />') . '</code>';
    $filecontent = highlight_file($FILE,true);
    foreach ($htmlarray as $code) {
        $codearray = array_unique(TextBetweenArray("&lt;$code",'&gt;',$filecontent));
        foreach ($codearray as $c) $filecontent = str_ireplace("&lt;$code$c&gt;","<span class=\"html\">&lt;$code$c&gt;</span>",$filecontent);
        $filecontent = str_ireplace("&lt;$code&gt;","<span class=\"html\">&lt;$code&gt;</span>",$filecontent);
        $filecontent = str_ireplace("&lt;/$code&gt;","<span class=\"html\">&lt;/$code&gt;</span>",$filecontent);
    }
  
    $CONTENT = "$linenum\n<div id=\"filecontent\">$filecontent</div>";
    $TITLE = "FILE: $FILE";
}

print <<<PRINTLBL
$DOCTYPE_XHTML
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<!-- ================TITLE================ -->   
<title>$FILE</title>
<!-- ================STYLE================ -->   
<style type="text/css">
body{
    padding:3px 1em;
    background-color : #fff;
}

#filecontent{
    white-space:nowrap;
}

.html {
    color:#77f;
}

.num {
    float: left;
    color: gray;
    text-align: right;
    margin-right: 6pt;
    padding-right: 6pt;
    border-right: 1px solid gray;
}

#heading{
    border-bottom:2px solid gray;  
}

#printbutton {
    font-family: Arial, Helvetica, sans-serif;
    font-size:80%;
    text-decoration:none;
    display:inline; 
    border:1px solid #888;
    background-color:#ccc;
    color:#000;
    margin:0.25em 0em; 
    padding:0.25em;
}

a.printbutton:active {
    border-color:#345 #cde #def #678;
}

a.printbutton:hover {
    background-color:#eee; color:#000;
}
</style>

<!-- ================SCRIPT================ -->
<script type="text/javascript">
 function printpage() {
   document.getElementById('return').style.display = 'none';
   window.print();
   setTimeout("document.getElementById('return').style.display = ''", 1000);
 }
</script>

</head>
<!-- ================BODY================ -->
<body>
<p id="return"><a id="printbutton" href="#" onclick="printpage(); return false;">Print</a></p>

<!-- ================CONTENT================ -->
<div id="pcontent">

<div id="heading">
  <h2>File: $FILE</h2>
</div>
$CONTENT
</div> <!--End #content-->

<!-- ================footer================ -->
<div class="printfooter">
</div> <!--End footer-->
</body>
</html>
PRINTLBL;

exit;
