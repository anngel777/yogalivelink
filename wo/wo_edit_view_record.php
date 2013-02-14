<?php
// - requires $ACTION = 'VIEW', 'EDIT', or 'ADD'
$QDATA = GetEncryptQuery('eq');

if (!$QDATA) {
    echo "<h1>No Data to Display!</h1>";
    return;
}

$CLASS_NAME  = ArrayValue($QDATA, 'class');
$ID          = ArrayValue($QDATA, 'id');
$PARAMETERS  = ArrayValue($QDATA, 'parameters');

if (empty($CLASS_NAME) or (empty($ID) and ($ACTION != 'ADD'))) {
    echo "<h1>Cannot find Table Class information!</h1>";
    return;
}

$Obj = new $CLASS_NAME($PARAMETERS);

$TITLE = $Obj->GetTableTitle();


//--------------------------------------------

$dummy_div = '<div style="width:400px;"></div>';

switch($ACTION) {

    case 'EDIT' :
        if (!$AJAX) {
            echo "<h2 class=\"pagehead\">Edit Record &mdash; $TITLE ($ID)</h2>";
            echo $dummy_div;
        }

        $Obj->EditRecord($ID);
    break;
    
    case 'ADD' :
        if (!$AJAX) {
            echo "<h2 class=\"pagehead\">Add Record &mdash; $TITLE</h2>";
            echo $dummy_div;
        }
        $Obj->AddRecord();
    
    break;
    
    case 'COPY' :
        if (!$AJAX) {
            echo "<h2 class=\"pagehead\">Copy Record ($ID) &mdash; $TITLE</h2>";
            echo $dummy_div;
        }
        if (empty($_POST)) {
            $Obj->PrePopulateFormValues($ID);
        }
        $Obj->Action_Copy = true;
        $Obj->AddRecord();
    
    break;
    
    case 'VIEW' :
        echo "<h2 class=\"pagehead\">View Record &mdash; $TITLE ($ID)</h2>";

        $Obj->ViewRecord($ID);

        if (empty($NO_UPDATES)) {  // set $NO_UPDATES = 1 to remove this link
            $LINK = str_replace(array(';VIEW_UPDATES=1'), '', $THIS_PAGE_QUERY);
            printqn("<p><a class=`stdbuttoni` href=`$LINK;VIEW_UPDATES=1`>View Updates</a></p>");

            if (Get('VIEW_UPDATES')) {
                $Obj->ViewUpdates($ID);
            }   
        }
    break;
}