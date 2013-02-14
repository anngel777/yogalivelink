<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
   $title = basename($F);
   $title = ($F) ? " [$title] - " : '';
   printqn("<title>$title{$SITECONFIG['sitename']} - Website Administration</title>"); 

   echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$LIB_DIRECTORY/admin/admin_style.css\" />";
   
   echo ($BROWSER != 'IE')? '' : '
<!-- Code below necessary for Internet Explorer -->
<!--[if lt IE 8]>
   <style type="text/css">
        ul.menu li, ul.mainmenu li {behavior: url(/lib/IEmen.htc);}
        ul.menu ul, ul.mainmenu ul {display: none; position: absolute; top:1.5em; left:-2px;}
        ul.mainmenu ul li {width:10em; text-align:left;}
    </style>
<![endif]-->
';  



include "$admin_inc/admin_tinymce.php"; 

echo "<script type=\"text/javascript\" src=\"$LIB_DIRECTORY/mvpeffects.js\"></script>";
echo "<script type=\"text/javascript\" src=\"$LIB_DIRECTORY/admin/admin_js.js\"></script>";

if (!empty($SITECONFIG['wanteditarea'])) print <<<EDITAREA
<script language="javascript" type="text/javascript" src="/jslib/edit_area/edit_area_compressor.php?plugins"></script>
<script type="text/javascript">
//function editTitle(){
  editAreaLoader.init({
  id: "TTEXT" 
  ,start_highlight: true
  ,allow_resize: "y"
  ,allow_toggle: true
  ,language: "en"
  ,syntax: "html"
  ,display: "later"
  ,toolbar: "search, go_to_line, |, undo, redo, |, select_font, |, change_smooth_selection, highlight, reset_highlight, |, help"
  ,gecko_spellcheck: true
  });
//}
//function editContent(){
  editAreaLoader.init({
  id: "CTEXT" 
  ,start_highlight: true
  ,allow_resize: "y"
  ,allow_toggle: true
  ,language: "en"
  ,syntax: "php"
  ,display: "later"
  ,toolbar: "search, go_to_line, |, undo, redo, |, select_font, |, change_smooth_selection, highlight, reset_highlight, |, help"
  ,gecko_spellcheck: true
  });
//}
</script>
EDITAREA;

$startpage = 1; //($need_preview) ? '1' : '2';

printqn("</head>\n<body class=`admin` onload=`adminOnload();`>");
