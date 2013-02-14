<!-- =========================== CONTENT EDITING MENU ======================= -->
<?php
$alinks = '<select id="filelinks">';
$count = 0;
foreach ($files as $file) {
  $count++;
  $mfile = str_replace('_','~',$file);
  $alinks .= qq("\n<option value=`$mfile{$SITECONFIG['extension']}`>$count. $file{$SITECONFIG['extension']}</option>");
}
$alinks .= '</select>';

StoreTime('Content Menu Files');

$ADMIN_IMAGE_LINK_DIR = ADMIN_IMAGE_LINK_DIR;

//$ifiles = GetDirectory(ADMIN_IMAGE_DIR, ADMIN_IMAGE_TYPES);

if ($FILE_SET or !AdminSession('IMAGE_FILES', SESSION_FILES)) {
    $ifiles = GetDirectory(ADMIN_IMAGE_DIR, ADMIN_IMAGE_TYPES);
    //$_SESSION['SITE_ADMIN'][SESSION_FILES]['IMAGE_FILES'] = $ifiles;
    $image_sizes = array();
    foreach($ifiles as $file) {
        $filename = ADMIN_IMAGE_DIR . "/$file";
        $image_sizes[$file] = getimagesize($filename);
    }
    //$_SESSION['SITE_ADMIN'][SESSION_FILES]['IMAGE_FILE_SIZES'] = $image_sizes;
} else {
    $ifiles      = AdminSession('IMAGE_FILES', SESSION_FILES);
    $image_sizes = AdminSession('IMAGE_FILE_SIZES', SESSION_FILES);
}


if ($ifiles) {
    $ilinks = '<select id="ifilelinks">';
    $count = 0;
    foreach ($ifiles as $file) {
        $count++;
        list($width, $height, $type, $attr) = $image_sizes[$file];
        $mfile = str_replace('_','~',$file);
        $title = str_replace(' ','_',NameToTitle(strTo($file,'.')));
        $mfile = "[img_src=@$ADMIN_IMAGE_LINK_DIR/$mfile@_alt=@$title@_width=@$width@_height=@$height@_border=@0@_/]";
        $ilinks .= qq("\n<option value=`$mfile`>$count. $file</option>");
    }
    $ilinks .= '</select>';
} else {
    $ilinks = '';
}
StoreTime('Content Menu Image Files');

$date = date('Y-m-d');
$date_time = date('Y-m-d H:i:s');
print <<<CONTENTMENULABEL
<div id="editmenu" style="background-color:#ccc; height:2em;">
<ul class="menu">

<li><a style="width:4em;" href="#" onclick="return false;">Text</a>
  <ul>
<li><a href="#" onclick="tagSurround('[b]','[/b]','CTEXT'); return false;">&lt;b&gt;</a></li>
<li><a href="#" onclick="tagSurround('[i]','[/i]','CTEXT'); return false;">&lt;i&gt;</a></li>
<li><a href="#" onclick="tagSurround('[strong]','[/strong]','CTEXT'); return false;">&lt;strong&gt;</a></li>
<li><a href="#" onclick="tagSurround('[em]','[/em]','CTEXT'); return false;">&lt;em&gt;</a></li>
<li><a href="#" onclick="tagSurround('[u]','[/u]','CTEXT'); return false;">&lt;u&gt;</a></li>
<li><a href="#" onclick="tagSurround('[sub]','[/sub]','CTEXT'); return false;">&lt;sub&gt;</a></li>
<li><a href="#" onclick="tagSurround('[sup]','[/sup]','CTEXT'); return false;">&lt;sup&gt;</a></li>
<li><a href="#" onclick="tagSurround('[br_/]','','CTEXT'); return false;">&lt;br /&gt;</a></li>
<li><a href="#" onclick="tagSurround('&amp;amp;','','CTEXT'); return false;">&amp;amp;</a></li>
<li><a href="#" onclick="tagSurround('&amp;nbsp;','','CTEXT'); return false;">nbsp</a></li>
<li><a href="#" onclick="tagSurround('&amp;ldquo;','&amp;rdquo;','CTEXT'); return false;">&ldquo;&nbsp;&rdquo;</a></li>
<li><a href="#" onclick="tagSurround('&amp;lsquo;','&amp;rsquo;','CTEXT'); return false;">&lsquo;&nbsp;&rsquo;</a></li>
<li><a href="#" onclick="changeCase('U'); return false;">Uppercase</a></li>
<li><a href="#" onclick="changeCase('L'); return false;">Lowercase</a></li>
<li><a href="#" onclick="changeCase('T'); return false;">Titlecase</a></li>
<li><a href="#" onclick="changeCase('V'); return false;">Variable(_)</a></li>
  </ul>
</li>

<li><a style="width:4em;" href="#" onclick="return false;">Attribute</a>
  <ul>
<li><a href="#" onclick="tagSurround('_id=@@','','CTEXT'); return false;">[id]</a></li>
<li><a href="#" onclick="tagSurround('_class=@@','','CTEXT'); return false;">[class]</a></li>
<li><a href="#" onclick="tagSurround('_style=@@','','CTEXT'); return false;">[style]</a></li>
<li><a href="#" onclick="tagSurround('_name=@@','','CTEXT'); return false;">[name]</a></li>
<li><a href="#" onclick="tagSurround('_onclick=@@','','CTEXT'); return false;">[onclick]</a></li>
<li><a href="#" onclick="tagSurround('_alt=@@','','CTEXT'); return false;">[alt]</a></li>
  </ul>
</li>

<li><a style="width:4em;" href="#" onclick="return false;">Heading</a>
  <ul>
  <li><a href="#"
     onclick="
       clearBlock();
       tagSurround('[h1]','[/h1]','CTEXT'); return false;"
     >&lt;h1&gt;</a></li>
  <li><a href="#" onclick="
       clearBlock();
       tagSurround('[h2]','[/h2]','CTEXT'); return false;">&lt;h2&gt;</a></li>
  <li><a href="#" onclick="
       clearBlock();
       tagSurround('[h3]','[/h3]','CTEXT'); return false;">&lt;h3&gt;</a></li>
  <li><a href="#" onclick="
       clearBlock();
       tagSurround('[h4]','[/h4]','CTEXT'); return false;">&lt;h4&gt;</a></li>
  <li><a href="#" onclick="
       clearBlock();
       tagSurround('[h5]','[/h5]','CTEXT'); return false;">&lt;h5&gt;</a></li>
  <li><a href="#" onclick="
       clearBlock();
       tagSurround('[h6]','[/h6]','CTEXT'); return false;">&lt;h6&gt;</a></li>
  </ul>
</li>

<li><a style="width:4em;" href="#" onclick="return false;">Block</a>
  <ul>
  <li><a href="#" onclick="tagSurround('[div]','[/div]','CTEXT'); return false;">&lt;div&gt;</a></li>
  <li><a href="#" onclick="clearBlock(); tagSurround('[p]','[/p]','CTEXT'); return false;">&lt;p&gt;</a></li>
  <li><a href="#" onclick="tagSurround('[span_style=@@]','[/span]','CTEXT'); return false;">&lt;span&gt;(style)</a></li>
  <li><a href="#" onclick="tagSurround('[span_class=@@]','[/span]','CTEXT'); return false;">&lt;span&gt;(class)</a></li>
  <li><a href="#" onclick="clearBlock(); tagSurround('[pre]','[/pre]','CTEXT'); return false;">&lt;pre&gt;</a></li>
  <li><a href="#" onclick="createParagraphs(); return false;">Create Paragraphs</a></li>
  <li><a href="#" onclick="tagSurround('[fieldset]','[/fieldset]','CTEXT'); return false;">&lt;fieldset&gt;</a></li>
  <li><a href="#" onclick="clearBlock(); tagSurround('[legend]','[/legend]','CTEXT'); return false;">&lt;legend&gt;</a></li>
  </ul>
</li>

<li><a style="width:4em;" href="#" onclick="return false;">&lt;a&gt;&nbsp;Tags</a>
  <ul>
    <li><a href="#" onclick="tagSurround('[a_href=@@]','[/a]','CTEXT'); return false;">&lt;a&gt;</a></li>
    <li><a href="#" onclick="tagSurround('[a_class=@stdbutton@_href=@@]','[/a]','CTEXT'); return false;">&lt;a&gt; stdbutton</a></li>
    <li><a href="#" onclick="tagSurround('[a_name=@@]','[/a]','CTEXT'); return false;">#Anchor</a></li>
  </ul>
</li>

<li><a href="#" onclick="tagSurround('[img_src=@$ADMIN_IMAGE_LINK_DIR/@_alt=@@_border=@0@_/]','','CTEXT'); return false;">&lt;img&gt;</a></li>

<li><a style="width:2em;" href="#">List</a>
  <ul>
    <li><a href="#" onclick="tagSurround('[li]','[/li]','CTEXT'); return false;">&lt;li&gt;</a></li>
    <li><a href="#" onclick="tagSurround('[ul]^CR','^CR[/ul]','CTEXT'); return false;">&lt;ul&gt;</a></li>
    <li><a href="#" onclick="tagSurround('[ol]^CR','^CR[/ol]','CTEXT'); return false;">&lt;ol&gt;</a></li>
    <li><a href="#" onclick="
           createList();
           return false;">Create List</a>
    </li>
    <li><a href="#" onclick="
           createList('UL');
           return false;">Create Unordered List</a>
    </li>
    <li><a href="#" onclick="
           createList('OL');
           return false;">Create Ordered List</a>
    </li>
  </ul>
</li>

<li><a href="#" onclick="tagSurround('[?php^CR^CR','?]','CTEXT'); return false;">php</a></li>

<li><a style="width:5em;" href="#">Comment</a>
  <ul>
   <li><a href="#" onclick="tagSurround('[!--_','_-->','CTEXT'); return false;">&lt;!-- --&gt;</a></li>
   <li><a href="#" onclick="tagSurround('[!--_====================_','_====================_-->','CTEXT'); return false;">&lt;!-- ==== ==== --&gt;</a></li>
   <li><a href="#" onclick="tagSurround('/*_','_*/','CTEXT'); return false;">/* */</a></li>
   <li><a href="#" onclick="tagSurround('$date', '', 'CTEXT'); return false;">Insert Date</a></li>
   <li><a href="#" onclick="tagSurround('$date_time', '', 'CTEXT'); return false;">Insert Date-Time</a></li>
  </ul>
</li>

<li><a style="width:3em;" href="#">Table</a>
  <ul>
   <li><a href="#" onclick="tagSurround('[table_width=@100%@_cellspacing=@0@_cellpadding=@0@_align=@center@]^CR[tbody]','^CR[/tbody]^CR[/table]','CTEXT'); return false;">&lt;table&gt;</a></li>
   <li><a href="#" onclick="tagSurround('[tr]','[/tr]','CTEXT'); return false;">&lt;tr&gt;</a></li>
   <li><a href="#" onclick="tagSurround('[th]','[/th]','CTEXT'); return false;">&lt;th&gt;</a></li>
   <li><a href="#" onclick="tagSurround('[td]','[/td]','CTEXT'); return false;">&lt;td&gt;</a></li>
   <li><a href="#" onclick="tagSurround('_align=@center@','','CTEXT'); return false;">[align="center"]</a></li>
   <li><a href="#" onclick="tagSurround('_align=@left@','','CTEXT'); return false;">[align="left"]</a></li>
   <li><a href="#" onclick="tagSurround('_align=@right@','','CTEXT'); return false;">[align="right"]</a></li>
   <li><a href="#" onclick="tagSurround('_colspan=@@','','CTEXT'); return false;">[colspan=""]</a></li>
   <li><a href="#" onclick="
           compressSpaces();
           replaceWithinSelection('[td','[th');
           replaceWithinSelection('[/td','[/th');
           return false;">&lt;td&gt; to &lt;th&gt;</a></li>

   <li><a href="#" onclick="
           compressSpaces();
           replaceWithinSelection('^T','[/td][td]');
           replaceWithinSelection('^CR','[/td][/tr]^CR[tr][td]');
           tagSurround('[table_width=@100%@_cellspacing=@0@_cellpadding=@0@_align=@center@]^CR[tbody]^CR[tr][td]','[/td][/tr]^CR[/tbody]^CR[/table]','CTEXT');
           return false;">Create Table</a></li>
  </ul>
</li>

<li><a style="width:6em;" href="#" onclick="return false;">Clean Up</a>
  <ul>
  <li><a href="#" onclick="compressSpaces(); return false;">Compress Space</a></li>
  <li><a href="#" onclick="clearBlock(); return false;">Remove Blocks</a></li>
  <li><a href="#" onclick="stripTags(); return false;">Remove Tags</a></li>
  <li><a href="#" onclick="stripTags('span'); return false;">Remove Spans</a></li>
  <li><a href="#" onclick="cleanWord(); return false;">Replace MS-Word Characters</a></li>
  <li><a href="#" onclick="replaceContentText('\t','    '); return false;">Tabs to Spaces</a></li>
  <li><a href="#" onclick="removeTrailingSpaces(); return false;">Remove Trailing Spaces</a></li>
  <li><a href="#" onclick="replaceContentText(' &amp; ',' &amp;amp; '); return false;">&amp; to &amp;amp;</a></li>
  <li><a href="#" onclick="replaceContentText('<br>','<br />'); return false;">&lt;br&gt; to &lt;br /&gt;</a></li>
  <li><a href="#" onclick="
       replaceWithinSelection('^CR','^CR__');
       tagSurround('__','_','CTEXT');
       return false;">Indent 2</a>
  </li>
  <li><a href="#" onclick="
       replaceWithinSelection('^CR','^CR____');
       tagSurround('____','_','CTEXT');
       return false;">Indent 4</a>
  </li>
  </ul>
</li>

<li><a href="#" onclick="showId('FandR'); return false;">Replace</a></li>

<li><a style="width:3em;" href="#">Links</a>
  <ul>
    <li><a href="#" onclick="showId('LINKS_PAGES'); return false;">Page Links</a></li>
    <li><a href="#" onclick="showId('LINKS_IMAGES'); return false;">Image Links</a></li>
  </ul>
</li>  

<li id="maximize"><a href="#" onclick="
          var editmenu = getId('editmenu');
          editmenu.style.position = 'fixed';
          editmenu.style.top = '0';
          editmenu.style.width = '100%';
          getId('pagewrapper').style.borderWidth = '0px';
          hideId('maximize');
          hideId('tabdiv');
          showId('minimize');
          showId('publish_button2');
          showId('publish_button3');
          hideId('mainheader');
          return false;">Maximize</a></li>

<li id="minimize" style="display:none;"><a href="#" onclick="
          var editmenu = getId('editmenu');
          editmenu.style.position = 'relative';
          getId('pagewrapper').style.borderWidth = '5px';
          editmenu.style.width = '95%';
          showId('tabdiv');
          hideId('minimize');
          hideId('publish_button2');
          hideId('publish_button3');
          showId('maximize');
          getId('CTEXT').rows = 25;
          showId('mainheader');
          showId('PREVIEW');
          return false;">Minimize</a></li>
<li><a target="_blank" href="$ADMIN_FILE?PRINT=$F$SPLINK">Print/View Content</a></li>

<li id="publish_button3" style="display:none; padding:0px;"><input type="submit" class="contentsubmit" name="SAVEDRAFT" value="Save Page Draft" /></li>
<li id="publish_button2" style="display:none; padding:0px;"><input type="submit" class="contentsubmit" name="PUBLISH" value="Publish Page" /></li>

</ul>
<div id="FandR" style="display:none;">
<div style="border-bottom:1px solid #888; background-color:#bfb; height:20px; cursor: pointer;"
   onmousedown="doDrag = true;"
   onmouseup="doDrag = false;"></div>
<p style="text-align:right">Find: <input type="text" size="20" id="find" /></p>
<p style="text-align:right">Replace: <input type="text" size="20" id="replace" /></p>
<p style="text-align:center;">
<a class="contentbutton" style="width:6em; margin-left:2em;" href="#" onclick="replaceText(); return false;">Replace All</a>
<a class="contentbutton" style="width:6em;" href="#" onclick="hideId('FandR'); return false;">Close</a>
</p>
</div>

<div id="LINKS_PAGES" style="display:none;" class="dragme">
<div style="border-bottom:1px solid #888; background-color:#bfb; height:20px; cursor: pointer;"
   onmousedown="doDrag = true;"
   onmouseup="doDrag = false;"></div>
<table border="0" cellpadding="2" cellspacing="2" style="padding:0.5em;">
  <tr>
  <td align="right">Page Link:</td>
  <td>$alinks <a class="contentbutton" href="#" onclick="
      var elem = getId('filelinks');
      var link = elem.value;
      tagSurround('[a_href=@{$SITECONFIG['pagedir']}/'+link+'@]','[/a]','CTEXT');
      return false;">Insert Link</a>
  </td>
  </tr>
  <tr>
  <td colspan="2" align="center">
  <a class="contentbutton" style="width:6em;" href="#" onclick="hideId('LINKS_PAGES'); return false;">Close</a>
  </td>
  </tr>
</table>
</div>

<div id="LINKS_IMAGES" style="display:none;" class="dragme">
<div style="border-bottom:1px solid #888; background-color:#bfb; height:20px; cursor: pointer;"
   onmousedown="doDrag = true;"
   onmouseup="doDrag = false;"></div>
<table border="0" cellpadding="2" cellspacing="2" style="padding:0.5em;">
  <tr>
  <td align="right">Image Link:</td>
  <td>$ilinks <a class="contentbutton" href="#" onclick="
      var elem = getId('ifilelinks');
      var link = elem.value;
      tagSurround(link,'','CTEXT');
      return false;">Insert Link</a>
  </td>
  </tr>
  <tr>
  <td colspan="2" align="center">
  <a class="contentbutton" style="width:6em;" href="#" onclick="hideId('LINKS_IMAGES'); return false;">Close</a>
  </td>
  </tr>
</table>
</div>

</div>

CONTENTMENULABEL;

//---------- clean variables from this file----------
unset($ilinks);  // done with this
unset($alinks);
unset($ifiles);
