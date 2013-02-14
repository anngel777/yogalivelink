<?php

//-----------read the directory----------

$files = GetDirectory("$ROOT/{$SITECONFIG['contentdir']}",$SITECONFIG['titlestr']);
$files = SubTextBetweenArray('',$SITECONFIG['titlestr'],$files);

//---------------Output the Map-------

$dir='';
$count=0;
foreach ($files as $fi){
    $titlename = "$ROOT/{$SITECONFIG['contentdir']}/$fi{$SITECONFIG['titlestr']}";
    $titletext = file_get_contents($titlename);
    $name = TextBetween('<name>','</name>',$titletext);
    if ($name) {
        $count++;
        if(!empty($flatfile)) {
            $newdir=strTo($fi,'/');
            if ($newdir != $dir) {
                $dir = $newdir;
                echo '<h3>'.NameToTitle($dir)."</h3>\n";
            }
        }
        if ($fi=='index') {
            $fi="{$SITECONFIG['pagedir']}/";
        } else {
            $fi="{$SITECONFIG['pagedir']}/$fi{$SITECONFIG['extension']}";
        }
        $summary=TextBetween('<summary>','</summary>',$titletext);
        printqn("<div style=`clear:right; width:3em; float:left; text-align:right;`>$count.</div>");
        printqn("<div style=`margin-left:3.5em; margin-bottom:1em;`><a href=`$fi`>$name</a>: $summary<br /></div>");
    }
}
