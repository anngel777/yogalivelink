<?php
$Obj = new DevRichard_ToDo;

SetGet('ADD LIST');
$LINK = str_replace(array(';ADD=1', ';LIST=1'), '', $THIS_PAGE_QUERY);
echo '<p>';
printqn("<a class=`stdbuttoni` href=`$LINK;ADD=1`>Add To Do Item</a>");
printqn("<a class=`stdbuttoni` href=`$LINK;LIST=1`>List To Dos</a>");
echo '</p>';


if ($ADD) {
    if(!HaveSubmit($Obj->Add_Submit_Name)) {
        $_POST[$FormPrefix.'created_by'] = $ADMIN_NAME;
    }
    $Obj->AddRecord();
}

if ($LIST or !$ADD) {
    $Obj->ListTable();
}
