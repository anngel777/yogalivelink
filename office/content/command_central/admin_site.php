<h1>SET ADMIN LOGIN</h1><br />
This will set a variables as though you had logged into the /admin+ site
<br /><br />
<ul>
<li><a href="admin_site;status=true">LOGIN</a></li>
<li><a href="admin_site;status=false">LOGOUT</a></li>
</ul>
<?php
$status = Get('status');
$show_message = true;
$Obj = new DevRichard_AdminLogin();
$Obj->SetAdmin($status, $show_message);