<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: index_customer.php
    Description: Layout of back-office --> customer login
==================================================================================== */

// --- HOME PAGE - DESKTOP ---

function IndexPageVariables()
{
    global $USER, $PAGE_DIR;

    if($USER->Super_User) {
        $QUERY = Get('QUERY');
        if ($QUERY == 'ON') {
            $_SESSION['WANT_DB_QUERIES'] = 1;
        } elseif($QUERY == 'OFF') {
            unset($_SESSION['WANT_DB_QUERIES']);
        }
        if ($QUERY) exit;  // exit, because using AJAX to set the Session

        $setvalue = Session('WANT_DB_QUERIES')? 'OFF' : 'ON';

        $script = <<<SULBL1
var dbQuerySetValue = '$setvalue';
function setDbQuery() {
    var lastValue = dbQuerySetValue;
    dbQuerySetValue = (dbQuerySetValue=='ON')? 'OFF' : 'ON';
    $.get('index;QUERY=' + lastValue, '', function() {
          $('#DB_QUERY_BUTTON').empty().append('Set Query ' + dbQuerySetValue);
    } );

}
SULBL1;

        AddScript($script);
        $querybutton = '<a id="DB_QUERY_BUTTON" href="#" onclick="setDbQuery(); return false;">Set Query '.$setvalue.'</a>';
    } else {
        $querybutton = '';
    }
    AddSwap('@@QUERY@@', $querybutton);
    
    $UserInfo = "<div>Welcome <b>{$USER->User_Name}</b></div><a href=\"$PAGE_DIR/LOGOUT\">Logout</a>";
    AddSwap('@@USER@@',  $UserInfo);
}

#IndexPageVariables();
#require $ROOT . '/wo/wo_menu_helper.php';
include_once('command_central/customer_profile.php');