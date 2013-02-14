<?PHP
//============variable setup=============
$ConfigPath = '..';

$QUERY_STRING = $VIEWPAGE;

require_once $LIB.'/page_helper.php';

//============create page name============

$frameid = strFrom($QUERY_STRING,':');

if ($frameid == '') {
    $frameid = 'ViewPage';
} else {
    $QUERY_STRING = strTo($QUERY_STRING,':');
}

GetPageName();
GetPageFileNames();

$PAGE_STREAM = strTo(file_get_contents("$ROOT{$SITECONFIG['templatedir']}/template.html"), '</head>');

GetTitleVariables();

$PAGE['basename'] = strTo($PAGE['basename'], '/admin') . '/';

SwapStdMarkUp();


echo $PAGE_STREAM;

?>

<script type="text/javascript">
function resizeIframe(iframeID) {

/* framePage is the ID of the framed page's BODY tag. The added 10 pixels prevent an unnecessary scrollbar. */
/* "iframeID" is the ID of the inline frame in the parent page. */

  if(self==parent) return false;
  var myPage = document.getElementById('framePage');
  var FramePageHeight = myPage.scrollHeight +70;
  parent.document.getElementById(iframeID).style.height=FramePageHeight + 'px';
}
</script>

<style type="text/css">
html {
  /* unforce scrollbars */
  height: 100%;
  margin-bottom: 0px;
}
body {background-image : none;}
</style>
<?php
if (file_exists('pageview.css')) {
    $css = dirname($PHP_SELF) . '/pageview.css';
    echo '<link rel="stylesheet" type="text/css" href="'.$css.'" />';
}    
?>
</head>
<body id="framePage"  onload="resizeIframe('<?php echo $frameid; ?>')">
<!-- ================CONTENT================ -->
<div id="framecontent">

<?php
if (file_exists($PAGE['contentfilename'])) {
    include $PAGE['contentfilename'];
} else {
    echo "<h1>Content File Not Available!</h1>";
}

?>
</div> <!--End #content-->

</body>
</html>