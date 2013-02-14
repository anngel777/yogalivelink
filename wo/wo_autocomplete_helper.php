<?php
if (empty($FORM_AUTOCOMPLETE_PROCESSING)) {
    if (empty($AJAX_HELPER_PROCESSED)) {
        include 'wo_ajax_helper.php';
    }

    if (!$USER->Login_Ok) {
        echo "LOGIN REQUIRED!|\n";
        return;
    }

    $SUPER_USER = $_SESSION[$USER->Session_Name]['SUPER_USER'];

    $TEST = Get('TEST');
    if (!$USER->Super_User) {
        $TEST = 0;
    }
} else {
    if ($FORM_AUTOCOMPLETE_PARAMETERS) {
        $SQL = ArrayValue($GLOBALS, 'SQL');
        parse_str($FORM_AUTOCOMPLETE_PARAMETERS, $_GET);
    }
}


$v = Get('v');
$q = strtolower(Get('q'));

if (($q == '#query') and $USER->Super_User) {
    echo str_replace('q=%23query', 'q=%20', $SCRIPT_URI . '&TEST=1|');
    exit;
}

if (!$q and !$v) {
    return;  //<<<<<<<<<<---------- stops and exits the autocomplete program ----------<<<<<<<<<<
}

$q = trim($q);
$EQ = GetEncryptQuery('eq');

$ac_table = $EQ['ac_table'];
$ac_field = $EQ['ac_field'];
$ac_key   = $EQ['ac_key'];

$ac_where = ArrayValue($EQ, 'ac_where');
$ac_joins = ArrayValue($EQ, 'joins');  //<<< this is a deprecated way of specifiying joins, use 'ac_joins'

if (!$ac_joins) {
    $ac_joins = ArrayValue($EQ, 'ac_joins');
}

$like_field = strTo($ac_field, ' AS ');

if ($v) {
    $vq = $SQL->QuoteValue($v);
    $LIKE  = " AND $ac_key=$vq";
} else {
    $qq = $SQL->QuoteValue("%$q%");
    $LIKE  = ($q)? " AND $like_field LIKE $qq" : '';
}

$EXTRA_WHERE = (empty($ac_where)) ? "" : "AND $ac_where ";
$LIMIT = ($v)? '1' : '0,50';

$result = $SQL->GetAssocArray(array(
    'table' => $ac_table,
    'key'   => $ac_key,
    'value' => $ac_field,
    'where' => "`$ac_table`.`active`=1 $EXTRA_WHERE $LIKE",
    'joins' => $ac_joins,
    'order' => 2,
    'limit' => $LIMIT
));

if ($v) {
    if (!empty($FORM_AUTOCOMPLETE_PROCESSING)) {
        $FORM_AUTOCOMPLETE_RESULT = ArrayValue($result, $v);
    } else {
        echo ArrayValue($result, $v);
    }
    return;
} else {
    asort($result);

    if (!empty($result)) {
        foreach ($result as $key=>$display) {
            if ($TEST == 1) {
                echo "$display|$key<br />\n";
            } else {
                echo "$display|$key\n";
            }
        }
    } else {
        echo "(No Suggestions Found)|\n";
    }
}

if ($TEST) {
    if ($TEST == 1) {
        $SQL->writedbquery();
        echo ArrayToStr($EQ);
    } else {
        echo trim(strip_tags(strFrom(writedbquerytext(), '</th>'))) . "|0\n";
        foreach ($EQ as $key => $value) {
            echo "$key =&gt; $value|0\n";
        }
    }
}
