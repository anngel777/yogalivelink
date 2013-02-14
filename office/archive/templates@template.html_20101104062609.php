<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>YOGA &mdash; @@TITLE@@</title>

<link rel="stylesheet" type="text/css" media="screen" href="/css/site.css" />


<!-- IE 6 hacks -->
<!-- [if lt IE 7] -->
<!-- link type='text/css' href='/css/i_css_contact_ie.css' rel='stylesheet' media='screen' / -->
<!-- [endif] -->

<!-- @@SCRIPTINCLUDE@@ -->
<!-- @@STYLE@@ -->
<!-- @@SCRIPT@@ -->

<script type="text/javascript" src="/jslib/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="/js/site.js"></script>
</head>
<body>
@@PHPERROR@@@@QUERY@@@@MESSAGES@@@@ERROR@@ 
<br /><br /><br />


<div id="container">

    
    
    <table cellpadding="0" cellspacing="0" border="0">
    
    <!-- ================================ MENU ===================================== -->
    <tr>
        <td colspan="2">
            
            <div id="menu_holder">
                <div id="nav">
                    <ul>
                        <li><a href="index">[T~MNU_HOME]</a></li>
                        <li><a href="store">[T~MNU_STORE]</a></li>
                        <li><a href="article">[T~MNU_ARTICLES]</a></li>
                        <li><a href="login">[T~MNU_LOGIN]</a></li>
                    </ul>
                </div>
                
                <div style="float:right;">
                    <a href=";lang=english"><img src="/images/template/flag_english.jpg" alt="[T~MNU_VIEW_ENGLISH]" border="0" /></a>
                    <a href=";lang=swedish"><img src="/images/template/flag_swedish.jpg" alt="[T~MNU_VIEW_SWEDISH]" border="0" /></a>
                    <a href=";lang=french"><img src="/images/template/flag_french.jpg" alt="[T~MNU_VIEW_FRENCH]" border="0" /></a>
                </div>
            </div>
            
        </td>
    </tr>
    
    
    
    <!-- ================================ HEADER ===================================== -->
    <tr>
        <td>@@HEADER_LEFT@@</td>
        <td>@@HEADER_RIGHT@@</td>
    </tr>
    
    <!-- ================================ CONTENT ===================================== -->
    <tr>
        <td colspan="2">
            <div class="container_gray">
                @@CONTENT@@
            </div>
            <div class="container_dark">
                <br /><br /><br />
            
            <div class="container_light">
                <div id="footertext">
                    &copy; 2010 Yoga Online
                </div>
            </div>
            
            </div>
            
        </td>
    </tr>
    
    </table>
   
</div>    

@@DBMESSAGES@@
</body>
</html>