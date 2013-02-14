<?php

$content            = Get('content');
$source_dialog_id   = Get('source_dialog_id');
$iframe_id          = '#appformIframe'.$source_dialog_id;



echo "<h2 class=\"pagehead\">Page Content Preview &mdash; <span id=\"subject\"></span></h2>";
echo "<div id=\"page_content\" style='width:700px;'></div>";

$SCRIPT = <<<SCRIPT
    $(function() {
        var content             = $('$iframe_id', top.document).contents().find('#$content').val();
        var strSingleLineText   = content.replace(new RegExp( "\\n", "g" ), "<br />");
        
        //content = nl2br(content);
        $('#page_content').html(strSingleLineText);    
    });
SCRIPT;
AddScript($SCRIPT);