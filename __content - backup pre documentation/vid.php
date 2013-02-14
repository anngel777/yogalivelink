
<div>
This will allow you to test your system.
</div>
<br /><br />

<?php
$test_sessions_id   = (Get('r')) ? Get('r') : '123-V';
$eq_TestSession     = EncryptQuery("class=Sessions_Launch;v1={$test_sessions_id};v2=666;v3=;v4=testing");
echo "<div><a href='#' class='link_arrow' onclick=\"LaunchSessionNewWindow('{$eq_TestSession}');\">CLICK HERE TO LAUNCH VIDEO WINDOW</a></div>";
echo "<br />Room Id: $test_sessions_id";

$script = "
    var classExecuteLinkAjax = '/AJAX/class_execute';
    var classExecuteLink = '/class_execute';
    
    function getClassExecuteLink(eq)
    {
        return classExecuteLinkAjax + '?eq=' + eq;
    }

    function getClassExecuteLinkNoAjax(eq)
    {
        return classExecuteLink + '?eq=' + eq;
    }
    
    function LaunchSessionNewWindow(eq) {
        //top.parent.window.location = getClassExecuteLinkNoAjax(eq);
        var link = getClassExecuteLinkNoAjax(eq) + ';template=launch;pagetitle=Yoga Video Session';
        var width = 880;
        var height = 570;
        window.open(link,'blank','toolbar=no,width='+width+',height='+height+',location=no');
    }
";
AddScript($script);
?>    

