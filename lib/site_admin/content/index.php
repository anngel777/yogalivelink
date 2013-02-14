<?php
// --- HOME PAGE - DESKTOP ---
if ($AJAX and Get('FL')) {
    echo $ADMIN->GetFileList();
    exit;
}

function IndexPageVariables()
{
    global $PAGE_DIR;
    $USER_NAME = ArrayValue(Session('SITE_ADMIN'), 'AdminName');

    $QUERY = Get('QUERY');
    if ($QUERY == 'ON') {
        $_SESSION['WANT_DB_QUERIES'] = 1;
    } elseif($QUERY == 'OFF') {
        unset($_SESSION['WANT_DB_QUERIES']);
    }
    if ($QUERY) exit;  // exit, because using AJAX to set the Session

    $setvalue = Session('WANT_DB_QUERIES')? 'OFF' : 'ON';

    $script = "var dbQuerySetValue = '$setvalue';";

    AddScript($script);
    $querybutton = '<a id="DB_QUERY_BUTTON" href="#" onclick="setDbQuery(); return false;">Set Query '.$setvalue.'</a>';
    AddSwap('[[QUERY]]', $querybutton);
    $UserInfo = "<div>Welcome <b>{$USER_NAME}</b></div><a href=\"LOGOUT\">Logout</a>";
    AddSwap('[[USER]]',  $UserInfo);
}

IndexPageVariables();

AddSwap('[[TITLE]]', $ADMIN->Site_Config['sitename']);

AddSwap('[[ADMINMENU]]', $ADMIN->GetAdminMenu());
