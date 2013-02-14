<?php
ini_set('display_errors','1');
if (empty($AJAX_HELPER_PROCESSED)) {
    include 'wo_ajax_helper.php';
}

SetGet('view edit delete table_search row_id action idx custom_search','SQT');

$QDATA = GetEncryptQuery('eq');

// echo '<tr><td><h2>GET</h2>' . ArrayToStr($_GET);
// echo "<h2>QDATA</h2>" . ArrayToStr($QDATA);
// echo '</td></tr>';


if ($QDATA) {
    $CLASS_NAME = $QDATA['class'];
    $PARAMETERS = ArrayValue($QDATA, 'parameters');
}

if (!$USER->Login_Ok) {
    if ($table_search) {
        echo '<tr><td><h2>Login Required!</h2></td></tr>';
    } else {
        echo '<h2>Login Required!</h2>';
    }
    exit;
}

if (empty($CLASS_NAME)) {
    echo 'Cannot find Table Class';
    exit;
}

require ClassFileFromName($CLASS_NAME);

$TableObj = new $CLASS_NAME($PARAMETERS);

if ($custom_search) {
    $TableObj->AjaxCustomSearch($DATA, $custom_search, $idx);
    exit;
}


if ($view) {
    $TableObj->ViewRecord($view);
    exit;
}



if ($delete) {

    $id = $QDATA['id'];

    if ($delete == 1) {
        $result = $TableObj->Inactivate($id);
    } elseif ($delete == 2 ) {
        $result = $TableObj->DeleteRecord($id);
    }

    if ($result == 0) {
        echo $TableObj->Error;
    } else {
        echo $result;
    }

    // if (empty($TableObj->Error)) {
        // echo 'ok';
    // } else {
        // echo $TableObj->Error;
    // }
    exit;
}

if ($edit) {

    $id = $edit;
    if ($id) {
        $TableObj->EditRecord($id);
    }
    exit;
}


if ($table_search) {

    $TableObj->AjaxTableDisplay($DATA, $action, $idx, $row_id);

    if (Session('WANT_DB_QUERIES')) {
        $TableObj->DisplayQueryFromAjaxTable();
    }
    exit;
}
