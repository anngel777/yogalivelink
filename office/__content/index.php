<?php
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

        $QUERY_CLASSINFO = Get('QUERY_CLASSINFO');
        if ($QUERY_CLASSINFO == 'ON') {
            $_SESSION['WANT_CLASS_INFO'] = 1;
        } elseif($QUERY_CLASSINFO == 'OFF') {
            unset($_SESSION['WANT_CLASS_INFO']);
        }
        if ($QUERY_CLASSINFO) exit;  // exit, because using AJAX to set the Session
        
        
        $setvalue = Session('WANT_DB_QUERIES')? 'OFF' : 'ON';
        $setvalue_classinfo = Session('WANT_CLASS_INFO')? 'OFF' : 'ON';

        $script = <<<SULBL1
var dbQuerySetValue = '$setvalue';
function setDbQuery() {
    var lastValue = dbQuerySetValue;
    dbQuerySetValue = (dbQuerySetValue=='ON')? 'OFF' : 'ON';
    $.get('index;QUERY=' + lastValue, '', function() {
          $('#DB_QUERY_BUTTON').empty().append('Set Query ' + dbQuerySetValue);
    } );

}

var classInfoSetValue = '$setvalue_classinfo';
function setClassInfo() {
    var lastValue = classInfoSetValue;
    classInfoSetValue = (classInfoSetValue=='ON')? 'OFF' : 'ON';
    $.get('index;QUERY_CLASSINFO=' + lastValue, '', function() {
          $('#CLASS_BUTTON').empty().append('Set Class Info ' + classInfoSetValue);
    } );

}
SULBL1;

        AddScript($script);
        $querybutton = '<a id="DB_QUERY_BUTTON" href="#" onclick="setDbQuery(); return false;">Set Query '.$setvalue.'</a>';
        $classbutton = '<a id="CLASS_BUTTON" href="#" onclick="setClassInfo(); return false;">Set Class Info '.$setvalue_classinfo.'</a>';
    } else {
        $querybutton = '';
        $classbutton = '[[]]';
    }
    AddSwap('@@QUERY@@', $querybutton);
    AddSwap('@@CLASSINFO_BTN@@', $classbutton);    
    
    $UserInfo = "<div>Welcome <b>{$USER->User_Name}</b></div><a href=\"$PAGE_DIR/LOGOUT\">Logout</a>";
    AddSwap('@@USER@@',  $UserInfo);
}

IndexPageVariables();

require $ROOT . '/wo/wo_menu_helper.php';
wo_GenerateGroupMenu();
