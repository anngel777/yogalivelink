<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
 <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
 <title>IE PNG Alpha Fix Demonstration</title>

 <!-- Addon for background tiling support -->
 <script type="text/javascript" src="iepngfix_tilebg.js"></script>

 <style type="text/css">

 /*
  USAGE:
  Copy and paste this one line into your site's CSS stylesheet.
  Add comma-separated CSS selectors / element names that have transparent PNGs.
  Remember that the path is RELATIVE TO THIS HTML FILE, not the CSS file.
  See below for another method of activating the script without adding CSS here.
 */

 img, div, input { behavior: url("iepngfix.htc") }


 /*
  Here's an example you might use in practice:
  img, div.menu, .pngfix, input { behavior: url("/css/iepngfix.htc") }
 */

 </style>

 <!--
  Consider wrapping the <style> element above in IE conditional comments.
  This will prevent the .HTC being downloaded by IE7+ at all.
  This might not work in IE6 installed alongside IE7 on the same computer.
  -->

 <!--[if lte IE 6]><style>/* ... your rules ... */</style><![endif]-->


 <script type="text/javascript">
 //<![CDATA[

 // If you don't want to put nonstandard properties in your stylesheet, here's yet
 // another means of activating the script. This assumes that you have at least one
 // stylesheet included already in the document above this script.
 // To activate, delete the CSS rules above and uncomment below (remove /* and */ ).

 /*
 if (document.all && /MSIE (5\.5|6)/.test(navigator.userAgent) &&
  document.styleSheets && document.styleSheets[0] && document.styleSheets[0].addRule)
 {
  document.styleSheets[0].addRule('*', 'behavior: url(iepngfix.htc)');
  // Feel free to add rules for specific elements only, as above.
  // You have to call this once for each selector, like so:
  //document.styleSheets[0].addRule('img', 'behavior: url(iepngfix.htc)');
  //document.styleSheets[0].addRule('div', 'behavior: url(iepngfix.htc)');
 }
 */


 // Here's another script that disables all PNGs in IE when the page is printed.
 /*
 if (window.attachEvent && /MSIE (5\.5|6)/.test(navigator.userAgent))
 {
  function printPNGFix(disable)
  {
   for (var  i = 0; i < document.all.length; i++)
   {
    var e = document.all[i];
    if (e.filters['DXImageTransform.Microsoft.AlphaImageLoader'] || e._png_print)
    {
     if (disable)
     {
      e._png_print = e.style.filter;
      e.style.filter = '';
     }
     else
     {
      e.style.filter = e._png_print;
      e._png_print = '';
     }
    }
   }
  };
  window.attachEvent('onbeforeprint',  function() { printPNGFix(1) });
  window.attachEvent('onafterprint',  function() { printPNGFix(0) });
 }
 */

 //]]>
 </script>

 <style type="text/css">
  /* Some styles for the default demonstration */
  body {
   font: 10pt/14pt sans-serif;
   background-color: #FFFFFF;
  }
  #header {
   text-align: center;
   margin-top: 10pt;
  }
  h1 {
   font-size: 20pt;
  }
  h3 {
   margin-top: 2em;
  }
  dt {
   font-weight: bold;
   margin-top: 0.5em;
  }
  code {
   color: #663300;
  }
  li {
   margin-top: 0.5em;
  }

  #demoWrap {
   background-image: url(checkerboard.gif);
   border: 2px solid #999;
   padding: 20px;
   margin-left: 10px;
   float: right;
  }

  #demoDiv {
   height: 300px;
   width: 200px;
   background: url(opacity.png);
   text-align: center;
   border: 1px solid #999;
  }

  #demoLink {
   background: url(opacity2.png) no-repeat;
   cursor: pointer;
   /*
    Here we apply the fix to just one element.
    You can do the same with CLASS selectors...
   */
   behavior: url("iepngfix.htc");
  }

  #demoClass {
   height: 220px;
   width: 200px;
   background-repeat: no-repeat;
  }
  .opacity1 {
   background-position: 0px 0px;
   background-image: url(opacity.png);
  }
  .opacity2 {
   /* Try CSS sprites :) */
   background-position: 0px 20px;
   background-image: url(opacity2.png);
  }

  .shadowImg {
   padding: 9px 11px 11px 9px;
   background-image: url(shadow.png);
  }

 </style>

</head>

<body>

<div id="header">
 <h1>IE PNG Alpha Fix v2.0 Alpha 4</h1>
 by Angus Turnbull - <a href="http://www.twinhelix.com">http://www.twinhelix.com</a>.
 Updated: 2 October 2009.
 <hr />
</div>

<div id="demoWrap">

 <h3>Demo:</h3>

 <!-- As you can see, regular image/background swapping works automatically, too! -->
 <!-- No special coding need here. -->

 <h3>REGULAR IMAGE TAG</h3>
 <img id="demoImg" src="opacity.png" alt="Opacity demo"
  onmouseover="this.src='opacity2.png'" onmouseout="this.src='opacity.png'" />

 <h3>BACKGROUND IMAGE</h3>
 <div id="demoDiv">
  <a href="#" onclick="document.getElementById('demoDiv').style.background='url(helix.gif)'; return false">GIF</a>
  |
  <a href="#" onclick="document.getElementById('demoDiv').style.background='url(opacity.png)'; return false">PNG 1</a>
  |
  <a href="#" onclick="document.getElementById('demoDiv').style.background='url(opacity2.png)'; return false">PNG 2</a>
  |
  <a href="#" onclick="document.getElementById('demoDiv').style.background='none'; return false">None</a>
  <br />
  <a href="#" onclick="document.getElementById('demoDiv').style.backgroundPosition=(Math.random()*200)+'px '+(Math.random()*200)+'px'; return false">Background Position</a>
  |
  <a href="#" onclick="document.getElementById('demoDiv').style.height=(100 + Math.random()*300)+'px'; return false">Height</a>
 </div>

 <h3>IMAGE DROPSHADOW</h3>
 <p>(PNG background behind GIF/JPEG)</p>
 <img class="shadowImg" src="helix.gif" alt="Helix" />

 <h3>INLINE LINK</h3>
 <a id="demoLink" href="#">
  Here's some inline link text.
 </a>

 <h3>INPUT TYPE=IMAGE</h3>
 <form action="javascript:void(0)">
  <div>
   <input type="image" src="opacity.png" />
  </div>
 </form>

 <h3>CLASSNAME CHANGE</h3>
 <div id="demoClass" class="opacity1"
  onmouseover="this.className = 'opacity2'"
  onmouseout="this.className = 'opacity1'"
  onclick="this.className = ''"></div>

 <h3>Self-test debugging:</h3>
 <a href="#" onclick="selfTest(); return false">Click here to test.</a>
 <script type="text/javascript">//<![CDATA[
    function selfTest() {
        if (!/MSIE (5\.5|6)/.test(navigator.userAgent)) {
            return alert('Please try this in IE6 :)');
        }
        var failed = 'MISSING! Check your .HTC pathname is correct ' +
            'and that the server is returning the correct MIME type.';
        var demoImg = document.getElementById('demoImg');
        alert(
            'IEPNGFix Core Behavior: ' +
            (IEPNGFix.process ? 'OK' : failed) +
            '\n\nBackground repeat/position extension: ' +
            (IEPNGFix.tileBG ? 'OK' : 'MISSING! Check your SCRIPT SRC for the extension') +
            '\n\nFilter activation: ' +
            (demoImg.filters['DXImageTransform.Microsoft.AlphaImageLoader'] ? 'OK' : failed) +
            '\n\nBLANK image pathname: ' + demoImg.src
        );
        if (location.protocol.indexOf('http') == -1) {
            return alert('Please run this from your server for MIME testing.');
        }
        alert('The script will now request "iepngfix.htc" in the current folder...');
        var xmlhttp;
        try {
            xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            try {
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) { }
        }
        // Change this to test if your pathname is right.
        xmlhttp.open('GET', 'iepngfix.htc', true);
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4) {
                alert(
                    'Server response: File ' +
                    (xmlhttp.status == 404 ? 'NOT FOUND!' : 'found.') +
                    '\n\nMIME type (should be "text/x-component"): ' +
                    xmlhttp.getResponseHeader('Content-Type')
                );
            }
        };
        xmlhttp.send(null);
    };
 //]]></script>

</div>


<h3>What is this?</h3>
<p>This is a IE5.5+ "behavior" that automatically adds near-native PNG support to
MSIE 5.5 and 6.0 without any changes to the HTML document itself. Supported features
include:</p>
<ul>
 <li>Automatic activation of transparency for PNGs in the page.</li>
 <li>Support for &lt;IMG SRC=""&gt; elements.</li>
 <li>Support for background PNG images (unlike many other scripts!)</li>
 <li>Support for CSS1 background repeat and position (via optional add-on)</li>
 <li>Background images can be defined inline or in external stylesheets.</li>
 <li>Automatically handles changed SRC/background via normal JavaScript
  (e.g. mouseover rollovers) -- no special coding needed.</li>
 <li>Change support includes CSS 'className' changes on elements.</li>
 <li>Incorporates automatic workaround for &lt;a href=""&gt; elements within
  PNG-background elements.</li>
 <li>Tiny script (for fast downloads).</li>
 <li>Licensed under a Free Software license.</li>
</ul>


<h3>How To Use</h3>
<p>Follow these simple steps to add this to your page:</p>
<ol>
 <li>Copy and paste <code>iepngfix.htc</code> and <code>blank.gif</code> into
  your website folder.</li>
 <li>Copy and paste this into your website's CSS or HTML:
  <blockquote>
   <div>
    <code>
     &lt;style type="text/css"&gt;<br />
     img, div { behavior: url(iepngfix.htc) }<br />
     &lt;/style&gt;
    </code>
   </div>
  </blockquote>
  That CSS selector must include the tags/elements on which you want PNG
  support -- basically, give it a comma-separated list of tags you use.
  It must also include the correct path to the .HTC <em>relative to the HTML
  document location</em> (not relative to the CSS document!). For instance,
  yours may look like this:
  <blockquote>
   <div>
    <code>
     &lt;style type="text/css"&gt;<br />
     img, div, a, input { behavior: url(/css/resources/iepngfix.htc) }<br />
     &lt;/style&gt;
    </code>
   </div>
  </blockquote>
 </li>
 <li>If your site uses subfolders, open the .HTC file in a text editor like
  Windows Notepad and change the <code>blankImg</code> variable to include
  a correct path to blank.gif like so:
  <blockquote>
   <div>
    <code>
     IEPNGFix.blankImg = '/images/blank.gif';
    </code>
   </div>
  </blockquote>
  Again the path is relative to the HTML file.
  Otherwise, you will see a "broken image" graphic!</li>
 <li>If you want support for CSS1 background-repeat and background-position,
  make sure you include the add-on .JS file in your <code>&lt;head&gt;</code>:
  <blockquote>
   <div>
    <code>
     &lt;script type="text/javascript" src="iepngfix_tilebg.js"&gt;&lt;/script&gt;
    </code>
   </div>
  </blockquote>
  Otherwise, background images will work but won't repeat or position.
 </li>
 <li>Sit back and enjoy! Perhaps consider making a
  <a href="http://www.twinhelix.com/donate/">donation</a> to support this script's
  development if you like what you see, as I have spent hundreds of hours
  developing, testing and supporting it :). Alternatively, I would certainly
  appreciate a crediting link on your site back to mine!</li>
</ol>

<p>If you are interested in more details or an alternative activation method
for the script that maintains CSS validation compatibility, see the source
code to this demonstration file.</p>

<h3>How to fix common problems</h3>
<dl>
 <dt>I've pasted in the CSS but my PNGs aren't transparent!</dt>
 <dd>Make sure you remember that the path to the HTC file is relative to the HTML
  file, not the CSS file (like CSS background images are). If you want to test the
  path, insert this: <code>alert('This works!');</code> into the .HTC file.</dd>
 <dt>It works offline but not online.</dt>
  <dd>First try unzipping this default demonstration and uploading to your web
   server as-is. If it doesn't work, you may have a MIME type problem.
   You must ensure your server is sending the correct MIME type of "text/x-component"
   for .HTC files. Try one of these two easy fixes:
   <ol>
    <li>Upload the ".htaccess" file from within this script's download ZIP to your
     webserver, which will make Apache send the correct MIME type.</li>
    <li>Instead of calling "IEPNGFIX.HTC" from your CSS, upload IEPNGFIX.PHP to
     the same folder and call that instead, which also sends the right MIME type.</li>
   </ol>
 </dd>
 <dt>My PNGs are transparent but have a funny border or red "X" icon.</dt>
 <dd>Check that the <code>blankImg</code> variable is set correctly in the .HTC
  file, again this should be relative to the HTML document containing the PNGs.</dd>
 <dt>Images are distorted, or this script breaks my page layout.</dt>
 <dd>When applied to images without set dimensions, this script will try and
  "guess" the correct image size and apply that. If it gets it wrong, give
  your images a definite <code>width</code> and <code>height</code>.</dd>
 <dt>Links or form elements within a PNG'd element aren't clickable.</dt>
 <dd>Due to an IE bug, if you are putting links within an element with a transparent
  background, the element must <em>not</em> have a CSS relative/absolute position.
  Otherwise the links will likely be un-clickable. The script will warn you with
  a popup alert dialog if this occurs. There is an excellent article on
  <a href="http://www.satzansatz.de/cssd/tmp/alphatransparency.html">PNG filters
  and links</a> you might want to read if you are a CSS expert that contains
  more info and workarounds.</dd>
 <dt>It works, but breaks another application like Google Maps on my page.</dt>
 <dd>You'll need to stop applying this behavior to the third-party element that
  presumably contains its own PNG fix. Try making your CSS selector more specific,
  or put a manual override for the element in your CSS like so:
  <code>* html div#map img { behavior: none; }</code></dd>
 <dt>I have IE6 installed "alongside" IE7+, and this script fails.</dt>
 <dd>Either try on a computer with IE6 installed system-wide, or make sure
  that you copy the required DLLs into your IE6 folder. You will need
  <code>dxtrans.dll</code> and <code>dxtmsft.dll</code> for filter support,
  try Googling for them or finding them on your Windows install CD.
  They must also be the "version 6" DLLs, not v7+, to work with IE6.
  Note that I can't support your setup here, sorry!</dd>
 <dt>I have lots of images and page loading is slow.</dt>
 <dd>With a lot of images, it can certainly slow down your page! Make sure
  that you apply the script as narrowly as possible. Consider applying only to
  elements of a particular CLASS perhaps, rather than all tags. Also, make
  sure that you are not trying to tile a 1x1px PNG background over a large
  element, as this will bring the browser to its kness -- make your images a
  little larger if you run into this :).</dd>
 <dt>The browser is making hundreds of extra HTTP requests.</dt>
 <dd> See <a href="http://support.microsoft.com/default.aspx?scid=kb;EN-US;Q319176">this
  MSDN HTC bug report</a> for the details and a workaround. The same technique
  might prove handy with the BLANK.GIF file too if that's your problem.</dd>
 <dt>The MIME type and path are right, but the core script won't load online.</dt>
 <dd>Check that your server isn't sending the HTC file GZIP-compressed. This
  can break IE6, it seems.</dd>
 <dt>It still won't go.</dt>
 <dd>Try running the self-test at the bottom of the list of demo images.
  If that throws any errors, you'll know where to start fixing!</dd>
</dl>


<h3>Limitations and known isses with the script</h3>
<ul>
 <li>Padding and borders don't indent the PNG image and can sometimes contribute
  to the distortion problem. An easy fix is to use 'margin' instead.</li>
 <li>A:HOVER transparent images are not supported out of the box. If you want
  this functionality, I recommend you download the excellent
  <a href="http://www.xs4all.nl/~peterned/csshover.html">Whatever:hover</a>
  script. This script will then enable :hover PNG background changes on all page
  elements when both are applied to the page.</li>
 <li>IE 4.0/5.0 are not supported. MSIE/Mac has native support for IMG SRC but
  no background PNG support. The scripts does nothing in MSIE7 as it supports
  PNGs natively.</li>
 <li>Users can't right-click-save processed PNG images, they'll save the blank GIF
  file if they try that. In some cases this might be a feature, not a bug...</li>
 <li>The script detects the ".png" extension in image URLs. So if you have a CGI script that
  generates a PNG image, you may have to rewrite parts of the script, or just cache them as PNG
  files somewhere.</li>
 <li>There may be about a short time as soon as the image loads when images are not
  transparent, before the IE filter kicks in.</li>
</ul>


<h3>License:</h3>
<blockquote>
 <p>IE5.5+ PNG Alpha Fix</p>
 <p>(c) 2004-2009 Angus Turnbull http://www.twinhelix.com</p>
 <p>This is licensed under the <a href="http://creativecommons.org/licenses/LGPL/2.1/">GNU LGPL,
  version 2.1 or later</a>.</p>
</blockquote>
<p>If you want to link my <a href="http://www.twinhelix.com">site</a>
or make a <a href="http://www.twinhelix.com/donate/">donation</a>, you're more than welcome :).</p>


<h3>Tech Specs</h3>
<p>IE5.5+ includes support for a DirectX
<a href="http://msdn2.microsoft.com/en-us/library/ms532969.aspx">AlphaImageLoader</a>
filter, that can take an element on the page and insert a correctly-rendered PNG
image inbetween the element's content and background. This script automatically
scans all IMG SRCs and element backgroundImages for .PNG files, and if it finds
them, it removes the regular image and substitutes this filter in place. See the
source if you want, but I've coded it quite compactly to keep download time down
to a minimum (at the expense of comprehensibility, sorry!).</p>


<h3>Changelog</h3>
<ul>
 <li>v2.0 Alpha 4<
  <ul>
   <li>Made blankImg pathname relative to the HTC by default.</li>
   <li>Added Macromedia rollover script compatibility.</li>
   <li>Fix for tiled backgrounds in elements with padding.</li>
   <li>Initially "display: none" PNG images now show once visible.</li>
   <li>Documentation updates.</li>
  </ul>
 </li>
 <li>v2.0 Alpha 3
  <ul>
   <li>Changed activation method so inline LI elements etc. work OK.</li>
   <li>Script now handles dynamic element width/height changes.</li>
   <li>Added .update() method to the background tiler that will update all
    elements on the page (called automatically on window resize).</li>
   <li>Restored "scale" sizingMethod for IMG SRC elements.</li>
  </ul>
 </li>
 <li>v2.0 Alpha 2
  <ul>
   <li>Fixed issue with positioning non-repeated backgrounds.</li>
   <li>Fixed typo in child-link-fix function.</li>
   <li>Fixed background-position changing via className (so 'CSS sprites'
    should now work).</li>
  </ul>
 </li>
 <li>v2.0 Alpha 1
  <ul>
   <li>Complete rewrite into an object-based pattern.</li>
   <li>Support for CSS1 background repeat and position.</li>
   <li>Performance improvements via toggling of the 'onpropertychange' hook.</li>
   <li>Activation of the script via 'oncontentready'.</li>
   <li>Slightly enhanced self-test mode.</li>
   <li>Numerous other minor tweaks.</li>
  </ul>
 </li>
  <li>v1.0 Final
  <ul>
   <li>Loosened IMG SRC matching regex slightly.</li>
   <li>Script exits gracefully when IE filter isn't installed (e.g. IE on Linux).</li>
   <li>Unclickable child fix now checks for pre-existing absolute/relative position.</li>
   <li>Added className switch demo and self-test debug mode.</li>
   <li>Documentation changes.</li>
  </ul>
 </li>
 <li>v1.0 RC5
  <ul>
   <li>Added support for CSS className changing of background images.</li>
   <li>The script now detects element's background-repeat and sets the PNG
    sizingMethod to 'crop' or 'scale' automatically.</li>
   <li>Enhanced link fixer to cover many clickable elements, and added popup
    warning dialog when the fix cannot be made.</li>
   <li>Support for PNG backgrounds behind GIF/JPEG images (e.g. dropshadows).</li>
   <li>Script sets display:inline-block automatically on inline elements.</li>
   <li>Loosened the URL matching rules, now any URLs with a .PNG in the path are
    activated, so /cgi-app/foo.png?date=123 will now work without modifications.</li>
   <li>Now works with the 'Whatever:hover' behavior to support :HOVER changing of
    background images on page elements!</li>
   <li>Simplified and reorganised portions of the script, especially the
    background image changer.</li>
   <li>Included demo .HTACCESS and .PHP files for sending the correct MIME type
    for servers where this is an issue.</li>
   <li>Rewrote the documentation, now it's understandable by human beings.</li>
  </ul>
 </li>
 <li>v1.0 RC4 and earlier
  <ul>
   <li>Various tweaks ;).</li>
  </ul>
 </li>
</ul>

</body>
</html>
