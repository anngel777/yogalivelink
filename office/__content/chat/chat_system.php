<?php

# OPEN THE CHAT PANEL IN A NEW WINDOW
# INITIALIZE IT SO WINDOWS OPEN CORRECTLY

$OBJ = new Chat_TouchpointChatAdminPanel();
$OBJ->reset_settings = false;
$OBJ->Window_Type = 'newWindow';


if ($AJAX) {
    $OBJ->ProcessAjax();
} else {
    $OBJ->InitializeChatPanel();
}



/*
$script = "
    function LaunchSessionNewWindow(eq) {
        //top.parent.window.location = getClassExecuteLinkNoAjax(eq);
        var link = getClassExecuteLinkNoAjax(eq) + ';template=launch;pagetitle=Yoga Video Session';
        var width = 880;
        var height = 570;
        window.open(link,'blank','toolbar=no,width='+width+',height='+height+',location=no');
    }
    ";
AddScript($script);


$eq_CancelSession           = EncryptQuery("class=Sessions_CancelSignup;v1={$record['sessions_id']};v3={$user_type}");


<div><a href='#' class='link_arrow' onclick=\"LaunchSessionNewWindow('{$eq_LaunchSession}');\">BEGIN SESSION</a></div>
<div><a href='#' class='link_arrow' onclick=\"top.parent.appformCreateOverlay('Upload Session Video', getClassExecuteLinkNoAjax('{$eq_UploadVideo}'), 'apps'); return false;\">UPLOAD SESSION VIDEO</a></div>
*/