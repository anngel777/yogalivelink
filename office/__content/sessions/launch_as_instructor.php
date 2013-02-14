<h1>Launch Session as Instructor</h1>
<?php

$form = "
    form|@@PAGELINKQUERY@@|post|$E
    text|Room ID|room_id|Y|20|11|$E
    text|Instructor ID|instructor_id|Y|20|11|$E
    submit|Submit|SUBMIT|$E
    endform|$E
";


echo OutputForm($form, Post('SUBMIT'));

if (Post('SUBMIT')) {
    $array      = ProcessFormNT($form, $ERROR);
    $eq         = EncryptQuery("class=Sessions_Launch;v1={$array['room_id']};v2={$array['instructor_id']};v3=instructor");
    
    $script = "
        function LaunchSessionNewWindow(eq) {
            var link    = getClassExecuteLinkNoAjax(eq) + ';template=launch;pagetitle=Yoga Video Session';
            var width   = 880;
            var height  = 570;
            window.open(link,'blank','toolbar=no,width='+width+',height='+height+',location=no');
        }
        
        LaunchSessionNewWindow('{$eq}');
    ";
    
    EchoScript($script);
    
    /*
    $query_array = GetEncryptQuery($eq, false);
    AddError($ERROR);
    if (!$ERROR) {
        echo "<h3>Result</h3>\n" . ArrayToStr($query_array);
        if (!empty($query_array['parameters'])) {
            $BC = new BaseClass;
            $BC->SetParameters(array($query_array['parameters']));
            echo "<h3>Parameters</h3>\n" . ArrayToStr($BC->Parameters);
        }
    }
    */
    
}
