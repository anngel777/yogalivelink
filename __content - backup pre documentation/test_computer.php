<?php
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
        var width = 200; //1124
        var height = 200; //570
        window.open(link,'blank','toolbar=no,width='+width+',height='+height+',location=no');
    }
";
AddScript($script);


$test_sessions_id   = time() . '-T';
$eq_TestSession     = EncryptQuery("class=Sessions_Launch;v1={$test_sessions_id};v2=666;v3=;v4=testing");

$output = <<<OUTPUT
<center>
<br /><br />
<div style="height: 10px; background-color: #F29358">&nbsp;</div>
<br /><br /><br />
<div style="font-size:18px; text-align:left; width:600px;">
Test your computer setup before your first session with our testing tool. By testing your setup you'll be ready to go for your first session and get the most out of your time with your instructor. 
<br /><br /><br />
<div><a href='#' class='link_arrow' onclick="LaunchSessionNewWindow('{$eq_TestSession}');">CLICK HERE TO LAUNCH TESTING WINDOW</a></div>
<br /><br /><br />
</div>
</center>
OUTPUT;
echo $output;
?>    

