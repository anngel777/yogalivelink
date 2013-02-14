<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: 
    Description: CLASS :: DatabaseManagement_TableEdit && CLASS :: DatabaseManagement_TableSelect
==================================================================================== */

$table = Get('table');
if ($table) {
    #$SQL = Lib_Singleton::GetInstance('Lib_Pdo');
    #$table_info = $SQL->TableFieldInfo($table);
    
    echo "<h1>TABLE ==> $table</h1>";
    $href= "db_table_select;DIALOGID=" . Get('DIALOGID');
    echo "<br /><a href='$href'>[BACK TO TABLE SELECTION]</a><br /><br />";
    
    $Obj = new DatabaseManagement_TableEdit($table);
    $Obj->ListTable();
} else {
    $Obj = new DatabaseManagement_TableSelect();
    $Obj->dialog_id = Get('DIALOGID');
    $Obj->ShowAllTables();
}













# RESIZE THE CURRENT FRAME TO FIT CONTENTS
# ================================================
$dialog_id = Get('DIALOGID');
$dialog_id = ($dialog_id) ? $dialog_id : 0;
$script = <<<SCRIPT
    var dialogNumber = {$dialog_id};
    ResizeIframe();
SCRIPT;
AddScript($script);