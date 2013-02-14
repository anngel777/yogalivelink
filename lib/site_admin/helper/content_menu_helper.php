<?php
$alinks = '';
$ifiles = '';
$ilinks = '';

$date = date('Y-m-d');
$date_time = date('Y-m-d H:i:s');
print <<<CONTENTMENULABEL
\n<!-- =========================== CONTENT EDITING MENU ======================= -->
<div id="editmenu">
<ul class="menu">

<li><a style="width:4em;" href="#" onclick="return false;">Text</a>
  <ul>
<li><a href="#" onclick="return tagSurround('[b]','[/b]','CTEXT');">&lt;b&gt;</a></li>
<li><a href="#" onclick="return tagSurround('[i]','[/i]','CTEXT');">&lt;i&gt;</a></li>
<li><a href="#" onclick="return tagSurround('[strong]','[/strong]','CTEXT');">&lt;strong&gt;</a></li>
<li><a href="#" onclick="return tagSurround('[em]','[/em]','CTEXT');">&lt;em&gt;</a></li>
<li><a href="#" onclick="return tagSurround('[u]','[/u]','CTEXT');">&lt;u&gt;</a></li>
<li><a href="#" onclick="return tagSurround('[sub]','[/sub]','CTEXT');">&lt;sub&gt;</a></li>
<li><a href="#" onclick="return tagSurround('[sup]','[/sup]','CTEXT');">&lt;sup&gt;</a></li>
<li><a href="#" onclick="return tagSurround('[br_/]','','CTEXT');">&lt;br /&gt;</a></li>
<li><a href="#" onclick="return tagSurround('&amp;amp;','','CTEXT');">&amp;amp;</a></li>
<li><a href="#" onclick="return tagSurround('&amp;nbsp;','','CTEXT');">nbsp</a></li>
<li><a href="#" onclick="return tagSurround('&amp;ldquo;','&amp;rdquo;','CTEXT');">&ldquo;&nbsp;&rdquo;</a></li>
<li><a href="#" onclick="return tagSurround('&amp;lsquo;','&amp;rsquo;','CTEXT');">&lsquo;&nbsp;&rsquo;</a></li>
<li><a href="#" onclick="changeCase('U'); return false;">Uppercase</a></li>
<li><a href="#" onclick="changeCase('L'); return false;">Lowercase</a></li>
<li><a href="#" onclick="changeCase('T'); return false;">Titlecase</a></li>
<li><a href="#" onclick="changeCase('V'); return false;">Variable(_)</a></li>
  </ul>
</li>

<li><a style="width:4em;" href="#" onclick="return false;">Attribute</a>
  <ul>
<li><a href="#" onclick="return tagSurround('_id=@@','','CTEXT');">[id]</a></li>
<li><a href="#" onclick="return tagSurround('_class=@@','','CTEXT');">[class]</a></li>
<li><a href="#" onclick="return tagSurround('_style=@@','','CTEXT');">[style]</a></li>
<li><a href="#" onclick="return tagSurround('_name=@@','','CTEXT');">[name]</a></li>
<li><a href="#" onclick="return tagSurround('_onclick=@@','','CTEXT');">[onclick]</a></li>
<li><a href="#" onclick="return tagSurround('_alt=@@','','CTEXT');">[alt]</a></li>
  </ul>
</li>

<li><a style="width:4em;" href="#" onclick="return false;">Heading</a>
  <ul>
  <li><a href="#"
     onclick="
       clearBlock();
       return tagSurround('[h1]','[/h1]','CTEXT');"
     >&lt;h1&gt;</a></li>
  <li><a href="#" onclick="
       clearBlock();
       return tagSurround('[h2]','[/h2]','CTEXT');">&lt;h2&gt;</a></li>
  <li><a href="#" onclick="
       clearBlock();
       return tagSurround('[h3]','[/h3]','CTEXT');">&lt;h3&gt;</a></li>
  <li><a href="#" onclick="
       clearBlock();
       return tagSurround('[h4]','[/h4]','CTEXT');">&lt;h4&gt;</a></li>
  <li><a href="#" onclick="
       clearBlock();
       return tagSurround('[h5]','[/h5]','CTEXT');">&lt;h5&gt;</a></li>
  <li><a href="#" onclick="
       clearBlock();
       return tagSurround('[h6]','[/h6]','CTEXT');">&lt;h6&gt;</a></li>
  </ul>
</li>

<li><a style="width:4em;" href="#" onclick="return false;">Block</a>
  <ul>
  <li><a href="#" onclick="return tagSurround('[div]','[/div]','CTEXT');">&lt;div&gt;</a></li>
  <li><a href="#" onclick="clearBlock(); return tagSurround('[p]','[/p]','CTEXT');">&lt;p&gt;</a></li>
  <li><a href="#" onclick="return tagSurround('[span]','[/span]','CTEXT');">&lt;span&gt;</a></li>
  <li><a href="#" onclick="return tagSurround('[span_style=@@]','[/span]','CTEXT');">&lt;span&gt;(style)</a></li>
  <li><a href="#" onclick="return tagSurround('[span_class=@@]','[/span]','CTEXT');">&lt;span&gt;(class)</a></li>
  <li><a href="#" onclick="clearBlock(); return tagSurround('[pre]','[/pre]','CTEXT');">&lt;pre&gt;</a></li>
  <li><a href="#" onclick="createParagraphs(); return false;">Create Paragraphs</a></li>
  <li><a href="#" onclick="return tagSurround('[fieldset]','[/fieldset]','CTEXT');">&lt;fieldset&gt;</a></li>
  <li><a href="#" onclick="clearBlock(); return tagSurround('[legend]','[/legend]','CTEXT');">&lt;legend&gt;</a></li>
  </ul>
</li>

<li><a style="width:4em;" href="#" onclick="return false;">&lt;a&gt;&nbsp;Tags</a>
  <ul>
    <li><a href="#" onclick="return tagSurround('[a_href=@@]','[/a]','CTEXT');">&lt;a&gt;</a></li>
    <li><a href="#" onclick="return tagSurround('[a_class=@stdbutton@_href=@@]','[/a]','CTEXT');">&lt;a&gt; stdbutton</a></li>
    <li><a href="#" onclick="return tagSurround('[a_name=@@]','[/a]','CTEXT');">#Anchor</a></li>
    <li><a href="#" onclick="return tagSurround('[button_type=@@_class=@@_onclick=@@]','[/button]','CTEXT');">&lt;button&gt;</a></li>
  </ul>
</li>

<li><a href="#" onclick="return tagSurround('[img_src=@$this->Admin_Image_Link_Dir/@_alt=@@_border=@0@_/]','','CTEXT');">&lt;img&gt;</a></li>

<li><a style="width:2em;" href="#">List</a>
  <ul>
    <li><a href="#" onclick="return tagSurround('[li]','[/li]','CTEXT');">&lt;li&gt;</a></li>
    <li><a href="#" onclick="return tagSurround('[ul]^CR','^CR[/ul]','CTEXT');">&lt;ul&gt;</a></li>
    <li><a href="#" onclick="return tagSurround('[ol]^CR','^CR[/ol]','CTEXT');">&lt;ol&gt;</a></li>
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

<li><a href="#" onclick="return tagSurround('[?php^CR^CR','?]','CTEXT');">php</a></li>

<li><a style="width:5em;" href="#">Comment</a>
  <ul>
   <li><a href="#" onclick="return tagSurround('[!--_','_-->','CTEXT');">&lt;!-- --&gt;</a></li>
   <li><a href="#" onclick="return tagSurround('[!--_====================_','_====================_-->','CTEXT');">&lt;!-- ==== ==== --&gt;</a></li>
   <li><a href="#" onclick="return tagSurround('/*_','_*/','CTEXT');">/* */</a></li>
   <li><a href="#" onclick="return tagSurround('$date', '', 'CTEXT');">Insert Date</a></li>
   <li><a href="#" onclick="return tagSurround('$date_time', '', 'CTEXT');">Insert Date-Time</a></li>
  </ul>
</li>

<li><a style="width:3em;" href="#">Table</a>
  <ul>
   <li><a href="#" onclick="return tagSurround('[table_width=@100%@_cellspacing=@0@_cellpadding=@0@_align=@center@]^CR[tbody]','^CR[/tbody]^CR[/table]','CTEXT');">&lt;table&gt;</a></li>
   <li><a href="#" onclick="return tagSurround('[tr]','[/tr]','CTEXT');">&lt;tr&gt;</a></li>
   <li><a href="#" onclick="return tagSurround('[th]','[/th]','CTEXT');">&lt;th&gt;</a></li>
   <li><a href="#" onclick="return tagSurround('[td]','[/td]','CTEXT');">&lt;td&gt;</a></li>
   <li><a href="#" onclick="return tagSurround('_align=@center@','','CTEXT');">[align="center"]</a></li>
   <li><a href="#" onclick="return tagSurround('_align=@left@','','CTEXT');">[align="left"]</a></li>
   <li><a href="#" onclick="return tagSurround('_align=@right@','','CTEXT');">[align="right"]</a></li>
   <li><a href="#" onclick="return tagSurround('_colspan=@@','','CTEXT');">[colspan=""]</a></li>
   <li><a href="#" onclick="
           compressSpaces();
           replaceWithinSelection('[td','[th');
           replaceWithinSelection('[/td','[/th');
           return false;">&lt;td&gt; to &lt;th&gt;</a></li>

   <li><a href="#" onclick="
           compressSpaces();
           replaceWithinSelection('^T','[/td][td]');
           replaceWithinSelection('^CR','[/td][/tr]^CR[tr][td]');
           return tagSurround('[table_width=@100%@_cellspacing=@0@_cellpadding=@0@_align=@center@]^CR[tbody]^CR[tr][td]','[/td][/tr]^CR[/tbody]^CR[/table]','CTEXT');
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

<li><a href="#" onclick="showId('F_AND_R'); return false;">Replace</a></li>

<li><a style="width:3em;" href="#">Links</a>
  <ul>
    <li><a href="#" onclick="$('#filelinkselections').load('AJAX/edit?LINKS=F'); showId('LINKS_PAGES'); return false;">Page Links</a></li>
    <li><a href="#" onclick="
        showId('LINKS_IMAGES');
        $('#imagelinkselections').load('AJAX/edit?LINKS=I');
        return false;">Image Links</a></li>
    <li><a href="#" onclick="$('#doclinkselections').load('AJAX/edit?LINKS=D'); showId('LINKS_DOCS'); return false;">Document Links</a></li>
  </ul>
</li>

<li><a style="width:3em;" href="#">View</a>
  <ul>
    <li><a href="$this->Admin_File_Query">Reload</a></li>
    <li><a target="_blank" href="print_view?F=$this->F">Print/View</a></li>
  </ul>
</li>

<li id="publish_button3" style="display:none; padding:0px;"><input type="submit" class="contentsubmit" name="SAVEDRAFT" value="Save Page Draft" /></li>
<li id="publish_button2" style="display:none; padding:0px;"><input type="submit" class="contentsubmit" name="PUBLISH" value="Publish Page" /></li>

</ul>
<div id="F_AND_R" style="display:none;" class="dragme">
<div class="dragbar"></div>
<p style="text-align:right; margin-right:1em;">Find: <input type="text" size="25" id="find" /><br />
Replace: <input type="text" size="25" id="replace" /></p>
<p class="center">
<a class="stdbuttoni" href="#" onclick="replaceText(); return false;">Replace All</a>
<a class="stdbuttoni" href="#" onclick="hideId('F_AND_R'); return false;">Close</a>
</p>
</div>

<div id="LINKS_PAGES" style="display:none;" class="dragme">
<div class="dragbar"></div>
<table border="0" cellpadding="2" cellspacing="2" style="padding:0.5em;">
  <tr>
  <td align="right">Page Link:</td>
  <td><span id="filelinkselections"><img src="/lib/site_admin/images/indicator.gif" alt="Loading" width="20" height="20" border="0" /></span>
      <a class="stdbuttoni" href="#" onclick="
      var elem = getId('filelinks');
      var link = elem.value;
      tagSurround('[a_href=@{$this->Site_Config['pagedir']}/'+link+'@]','[/a]','CTEXT');
      return false;">Insert Link</a>
  </td>
  </tr>
  <tr>
  <td colspan="2" align="center">
  <a class="stdbuttoni" href="#" onclick="hideId('LINKS_PAGES'); return false;">Close</a>
  </td>
  </tr>
</table>
</div>

<div id="LINKS_IMAGES" style="display:none;" class="dragme">
<div class="dragbar"></div>
  <p><span id="imagelinkselections"><img src="/lib/site_admin/images/indicator.gif" alt="Loading" width="20" height="20" border="0" /></span></p>
  <p class="center"><a class="stdbuttoni" style="width:6em;" href="#" onclick="hideId('LINKS_IMAGES'); return false;">Close</a></p>
</div>

<div id="LINKS_DOCS" style="display:none;" class="dragme">
<div class="dragbar"></div>
<table border="0" cellpadding="2" cellspacing="2" style="padding:0.5em;">
  <tr>
  <td align="right">Document Link:</td>
  <td><span id="doclinkselections"><img src="/lib/site_admin/images/indicator.gif" alt="Loading" width="20" height="20" border="0" /></span>
      <a class="stdbuttoni" href="#" onclick="
      var elem = getId('doclinks');
      var link = elem.value;
      tagSurround('[a_href=@'+link+'@]','[/a]','CTEXT');
      return false;">Insert Link</a>
  </td>
  </tr>
  <tr>
  <td colspan="2" align="center">
  <a class="stdbuttoni" href="#" onclick="hideId('LINKS_DOCS'); return false;">Close</a>
  </td>
  </tr>
</table>
</div>

</div>

CONTENTMENULABEL;

