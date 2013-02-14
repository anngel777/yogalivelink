<?php
//needs $linkfile name (no path)
//-----------read the directory----------

if(empty($linkfile)) $linkfile = 'links.dat';

$linkdata = file_get_contents("$ROOT{$SITECONFIG['listdir']}/$linkfile");
$categories=TextBetweenArray('<category>','</category>',$linkdata);
$count=0;

foreach ($categories as $category){
$heading=TextBetween('<heading>','</heading>',$category);
if($heading) echo "<h2>$heading</h2>";
$links=TextBetweenArray('<link>','</link>',$category);
foreach ($links as $link){
  $name=TextBetween('<name>','</name>',$link);
  $url=TextBetween('<url>','</url>',$link);
  $desc=TextBetween('<description>','</description>',$link);
  $img=TextBetween('<img>','</img>',$link);

  if($name){
    $count++;
    echo "<div style=\"margin-left:2em; float:left; \">$count.&nbsp;</div>";
    echo "<p style=\"margin-left:4em; \"><a href=\"$url\"><b>$name</b></a><br />$desc</p>\n";
    }
  }
}
