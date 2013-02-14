<?php
//======================SITEMAP==========================

//$domain = str_replace('www.', '', $HTTP_HOST);
$domain = $HTTP_HOST;
if (!$SITEMAP) $SITEMAP = "sitemap_" . strTo($domain, '.') . '.xml';

printqn("<form method=`post` action=`$ADMIN_FILE`>");
?>

<table align="center" style="background-color:#eee; margin-top:10px;">
<tr><td>
  <div style="float:left; width:150px; text-align:right; font-weight:bold;">Site Map File:</div>
  <div style="margin-left:160px;">
    <input class="formitem" type="text" name="SITEMAP" value="<?php echo $SITEMAP; ?>" size="60" />
  </div>


<?php
print '<div style="margin-left:160px;">
  <input class="messagesubmit" name="GENERATESITEMAP" type="Submit" value="Generate" />';

  if ($GENERATESITEMAP) {
    print '&nbsp;<input class="messagesubmit" name="WRITESITEMAP" type="Submit" value="Write Sitemap" />';
}

print '</div>';

print '
</td></tr>
</table>
</form>';


//======================GENERATE FILE==========================


if ($GENERATESITEMAP or $WRITESITEMAP) {
    $filename = "$DOCUMENT_ROOT/$SITEMAP";

    $text = '';
    $files = GetDirectory(ADMIN_CONTENT_DIR,ADMIN_CONTENT_STR);
    $files = SubTextBetweenArray('',ADMIN_CONTENT_STR,$files);
    foreach ($files as $f) {
      $titlename = ADMIN_CONTENT_DIR . "/$f" . ADMIN_TITLE_STR;
      $titletext = file_get_contents($titlename);
      $name = TextBetween('<name>', '</name>', $titletext);
      if ($name) {
        $text .= "  <url>\n    <loc>http://$domain/$f</loc>\n  </url>\n";
      }
    }
    $text = qq("<?xml version=`1.0` encoding=`UTF-8`?>
<urlset xmlns=`http://www.sitemaps.org/schemas/sitemap/0.9`>
$text</urlset>
");

//======================SAVE FILE==========================
if ($WRITESITEMAP) {
    AdminWriteFile($filename, $text);
    printqn("<p style=`text-align:center`><a style=`width:10em;` class=`mainbutton` target=`_blank` href=`/$SITEMAP`>View XML File</a></p>");
}

//======================OUTPUT FILE==========================

$otext = htmlentities($text);
printqn("
<div style=`background-color:#fff; border:1px dashed #888; margin:10px auto; padding:1em; width:80%;`>
<h2>Sitemap File</h2>
<pre>
$otext
<pre>
</div>
");

}
