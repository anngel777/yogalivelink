<?php
//------MySQL Helper Routines - Michael V. Petrovich

$DBQUERY               = '';
$DbLastQuery           = '';
$DbQueryTableOptions   = 'align="center" style="background-color:#888; border:1px solid #000; margin-top:10px;"';
$DbQueryTableOptionsTH = 'align="center" style="background-color:#aaa; color:#fff;"';
$DbQueryTableOptionsTD = 'align="left" style="background-color:#ff7; color:#000; font-size:0.8em; padding:1em 1em 0px 0px;"';
$FieldValues           = array();

if (!empty($DB_INFO)) {
    SetUpDbConnection($DB_INFO);
}

//------ call this function at start-up
function SetUpDbConnection($DB_INFO)
{
    @mysql_connect($DB_INFO['HOST'],$DB_INFO['USER'],$DB_INFO['PASS']) or Mtext('Error','Could not connect to Database!');
    mysql_select_db($DB_INFO['NAME']);
}

function RowSelect($row_count,$rows,$start_row,$STARTROW,$ROWS,$HOME,$PREVIOUSPAGE,$NEXTPAGE,$END,$SHOW)
{
    if ($row_count > $rows) {
        $RESULT = qqn("
        <div class=`rowselect`><p>Found: ".number_format($row_count)." records</p>
        <p>Start Row: <input type=`text` size=`4` maxlength=`10` name=`$STARTROW` value=`$start_row` />&nbsp;&nbsp;
        Rows per Page: <input type=`text` size=`4` maxlength=`4` name=`$ROWS` value=`$rows` />&nbsp;&nbsp;
        <input type=`submit` value=`Show` name=`$SHOW` />&nbsp;");
        if ($start_row > 0) {
            $RESULT .= qqn("<input type=`submit` value=`&lt;&lt;` name=`$HOME` />&nbsp;");
        }
        if ($start_row > 0) {
            $RESULT .= qqn("<input type=`submit` value=`<&mdash;` name=`$PREVIOUSPAGE` />&nbsp;");
        }
        if ($row_count >= $start_row+$rows) {
            $RESULT .= qqn("<input type=`submit` value=`&mdash;&gt;` name=`$NEXTPAGE` />");
        }
        if ($row_count >= $start_row+$rows) {
            $RESULT .= qqn("<input type=`submit` value=`&gt;&gt;` name=`$END` />");
        }
        $RESULT .= '</p></div>';
    } else {
        $RESULT = '';
    }
    return $RESULT;
}

// ------------------ Database Query Tracking --------------
function SetDbQuery($function,$query)
{
    global $DBQUERY, $DbLastQuery;
    $query = htmlentities($query);
    $DbLastQuery = $query;
    $span1 = '<span style="color:#f00; font-weight:bold;">';
    $span2 = '</span>';
    $keywords = array('UPDATE','INSERT','INTO','SET','LEFT','JOIN','COUNT(',')',' AS ','CONCAT(',
        'WHERE','LIMIT','SELECT','AND','OR ','FROM','MAX','DELETE','VALUES(','GROUP',' BY ',' ORDER ');
    foreach ($keywords as $word) {
        $query = str_ireplace($word,"$span1$word$span2",$query);
    }
    $error = mysql_error();
    if (!empty($error)) {
        $error = "<li style=\"background-color:#f00;color:#fff;padding:3px;\">$error</li>";
    }
    $DBQUERY .= "$error<li style=\"padding:5px;\"><b>$function:</b> $query</li>\n";
}

function WriteDbQuery()
{
    echo WriteDbQueryText();
}

function WriteDbQueryText()
{
    global $DBQUERY, $DbQueryTableOptions, $DbQueryTableOptionsTH, $DbQueryTableOptionsTD;
    $RESULT = '';
    if (!empty($DBQUERY)) {
        $RESULT .= "<table $DbQueryTableOptions>";
        $RESULT .= "<tr><th $DbQueryTableOptionsTH>DB Queries</th></tr>";
        $RESULT .= "<tr><td $DbQueryTableOptionsTD><ul>$DBQUERY</ul></td></tr>";
        $RESULT .='</table>';
    }
    return $RESULT;
}

function db_KeyValues($array)
{
    $RESULT = '';
    foreach ($array as $key => $value) {
        $RESULT .= "$key='$value',";
    }
    return substr($RESULT,0,-1);
}

function db_Keys($array)
{
    $RESULT = '';
    foreach ($array as $key => $value) {
        $RESULT .= "$key,";
    }
    return substr($RESULT,0,-1);
}

function db_Values($array)
{
    $RESULT = '';
    foreach ($array as $key => $value) {
        $RESULT .= "'$value',";
    }
    return substr($RESULT,0,-1);
}

function ArrayToPost($arraymap,$array)
{
    foreach ($arraymap as $varname => $postname) {
        $_POST[$postname] = empty($array[$varname])? '' : $array[$varname];
    }
}

function db_output_table($SearchArray,$UserInfo,$TableSetup,$EditTitle,$EditLinks,$id)
{
    global $FieldValues;
    if (count($SearchArray) == 0) return '';
    $RESULT = "<table $TableSetup>\n<tr><th>No.</th>";
    $wantedit = (!empty($id) and !empty($EditLinks));
    if ($wantedit) $RESULT .= "<th>$EditTitle</th>";
    if (empty($UserInfo)) {
        $UserInfo = array();
        foreach ($SearchArray[0] as $key=>$value) {
            $UserInfo[$key] = NameToTitle($key);
        }
    }
    foreach ($UserInfo as $key => $value) { $RESULT .= "<th>$value</th>"; }
    $RESULT .= "</tr>\n";
    $evenodd = 2;
    $count = 0;
    foreach ($SearchArray as $row) {
        $count++;
        $evenodd = 3 - $evenodd;
        $class = ($evenodd == 1) ? 'odd' : 'even';
        $RESULT .= "<tr class=\"$class\"><td align=\"right\">$count.</td>";
        if ($wantedit) $RESULT .= '<td>'. str_replace('@@ID@@',$row[$id],$EditLinks) .'</td>';
        foreach ($UserInfo as $key => $value) {
            $field = (empty($row[$key]) and ($row[$key] != '0'))? '' : $row[$key];
            $outvalue = (empty($FieldValues[$key][$field]))? $field : $FieldValues[$key][$field];
            $RESULT .= "<td>$outvalue</td>";
        }
        $RESULT .= "</tr>\n";
    }
    $RESULT .= "</table>\n";
    return $RESULT;
}

function db_IsUnique($db_table,$key,$value,$exclude)
{
    if (empty($key) or empty($value)) {
        return false;
    }
    $excludestr = (!empty($exclude))? " AND $exclude" :'';
    $query = "SELECT $key FROM `$db_table` WHERE $key='$value'$excludestr LIMIT 1";
    $db_query = mysql_query($query);
    SetDbQuery('db_IsUnique',$query);
    if ($db_query) {
        $row = mysql_fetch_assoc($db_query);
    }
    return empty($row[$key]);
}

function db_GetUniqueID($db_table,$field) {
    $UID     = md5(uniqid(rand(), true));
    while (!db_IsUnique($db_table,$field,$UID,'')) {
        $UID = md5(uniqid(rand(),true));
    }
    return $UID;
}


function db_GetNextValue($db_table,$key)
{
    if (empty($key)){
        return '';
    }
    $query    = "SELECT MAX($key) AS maxkey FROM `$db_table`";
    $db_query = mysql_query($query);
    SetDbQuery('db_GetNextValue',$query);
    $row      = mysql_fetch_assoc($db_query);
    $RESULT   = $row['maxkey'] + 1;
    return $RESULT;
}

function db_GetNextDate($db_table,$key,$format,$inc)
{
    if (empty($key)) {
        return '';
    }
    $query    = "SELECT MAX($key) AS maxkey FROM `$db_table`";
    $db_query = mysql_query($query);
    SetDbQuery('db_GetNextDate',$query);
    $row      = mysql_fetch_assoc($db_query);
    $maxdate  = DateToDashes($row['maxkey']);
    if (strlen($maxdate)<8) {
        $maxdate .= '-01';
    }
    return date($format,strtotime("$maxdate +1 $inc"));
}


function db_GetFieldValues()
{
    // $db_table,  $key, [where]
    $numargs  = func_num_args();
    $db_table = func_get_arg(0);
    $key      = func_get_arg(1);
    $where = ($numargs > 2)? ' WHERE '.func_get_arg(2) : '';
    $RESULT = array();
    if (empty($key)) {
        return $RESULT;
    }
    $query    = "SELECT $key FROM `$db_table`$where GROUP BY $key ORDER BY $key";
    $db_query = mysql_query($query);
    SetDbQuery('db_GetFieldValues',$query);
    if (!$db_query) {
        return $RESULT;
    }
    while ($row = mysql_fetch_assoc($db_query)) {
        foreach ($row as $key => $value) {
            $RESULT[] = $value;
        }
    }

    return $RESULT;
}

function db_GetFieldValuesLC($db_table,$key)
{
    $RESULT = array();
    if (empty($key)) {
        return $RESULT;
    }

    $query = "SELECT $key FROM `$db_table` GROUP BY $key ORDER BY $key";
    $db_query = mysql_query($query);
    SetDbQuery('db_GetFieldValuesLC',$query);

    if (!$db_query) {
        return $RESULT;
    }

    while ($row = mysql_fetch_assoc($db_query)) {
        foreach ($row as $key => $value) {
            $RESULT[] = strtolower($value);
        }
    }
    return $RESULT;
}

function db_ViewRecord($db_table,$keymap,$TableSetup,$keys,$where)
{
    global $FieldValues;
    if (empty($keys) or empty($where)) return '';
    $query = "SELECT $keys FROM `$db_table` WHERE $where LIMIT 1";
    $db_query = mysql_query($query);
    SetDbQuery('db_ViewRecord',$query);
    if (!$db_query) return '';
    $row =mysql_fetch_assoc($db_query);
    if (count($row)>0) {
        $RESULT = "<table $TableSetup>\n";
        foreach ($row as $key => $value) {
            $outkey = (empty($keymap[$key]))? $key : $keymap[$key];
            $outvalue = (empty($FieldValues[$key][$value]))? $value : $FieldValues[$key][$value];
            $RESULT .= "<tr><th align=\"right\">$outkey</th><td>$outvalue</td></tr>\n";
        }
        $RESULT .= "</table>\n";
    } else {
        $RESULT = '';
    }
    return $RESULT;
}

function db_ViewRecordArray($Record,$Titles,$TableSetup)
{
    global $FieldValues;
    if (count($Record)>0) {
        $RESULT = "<table $TableSetup>\n";
        foreach ($Record as $key => $value) {
            $outkey = (empty($Titles[$key]))? $key : $Titles[$key];
            $outvalue = (empty($FieldValues[$key][$value]))? $value : $FieldValues[$key][$value];
            $RESULT .= "<tr><th align=\"right\">$outkey</th><td>$outvalue</td></tr>\n";
        }
        $RESULT .= "</table>\n";
    } else {
        $RESULT = '';
    }
    return $RESULT;
}


function db_GetRecord($db_table,$keys,$where)
{
    if (empty($keys) or empty($where)) {
        return '';
    }

    $query = "SELECT $keys FROM `$db_table` WHERE $where LIMIT 1";
    $db_query = mysql_query($query);
    if ($db_query) {
        SetDbQuery('db_GetRecord',$query);
        return mysql_fetch_assoc($db_query);
    } else {
        return 0;
    }
}

function db_UpdateRecord($db_table,$key_values,$where)
{
    if (empty($key_values) or empty($where)) {
        return false;
    }

    $query = "UPDATE `$db_table` SET $key_values WHERE $where";
    $RESULT = mysql_query($query);
    SetDbQuery('db_UpdateRecord',$query);
    return $RESULT;
}

function db_DeleteRecord($db_table,$where)
{
  if (empty($where)) {
    return false;
  }

  $query = "DELETE FROM `$db_table` WHERE $where";
  $RESULT = mysql_query($query);
  SetDbQuery('db_DeleteRecord',$query);
  return $RESULT;
}

function db_AddRecord($db_table,$keys,$values)
{
    if (empty($keys) or empty($values)) {
        return false;
    }
    $query = "INSERT INTO `$db_table` ($keys) VALUES ($values)";
    $RESULT = mysql_query($query);
    SetDbQuery('db_AddRecord',$query);
    return $RESULT;
}


function db_GetSearchArray($db_table, $keys, $conditions, $order, $startlist, $listsize, &$num_rows, &$query)
{
  //deprecated - do not need &$query can get from $DbLastQuery  -- use db_GetArray()
    if ($keys == '') return '';

    if (empty($order) or ($order == 'none')) {
        $ORDER = '';
    } else {
        $order = "ORDER BY $order";
    }

    if ((empty($startlist)) and (empty($listsize))) {
        $LIMIT = '';
    } else {
        $LIMIT = "LIMIT $startlist, $listsize";
    }

    if (!empty($conditions)) {
        $conditions = "WHERE $conditions";
    }

    $firstkey = strTo($keys,',');
    $query = "SELECT $keys FROM `$db_table` $conditions $order $LIMIT";
    $query_num = "SELECT $firstkey FROM `$db_table` $conditions";

    $db_query = mysql_query($query_num);
    if ($db_query) {
        $num_rows = mysql_num_rows($db_query);
    } else {
        $num_rows = 0;
    }
    $db_query = mysql_query($query);
    SetDbQuery('db_GetSearchArray',$query);

    $RESULT = array();
    if ($db_query) {
        while ($row = mysql_fetch_assoc($db_query)) {
            $RESULT[] = $row;
        }
    }

    return $RESULT;
}

function db_GetArray($db_table, $keys, $conditions, $order, $startlist, $listsize, &$num_rows)
{
    if ($keys == '') {
        return '';
    }

    if (empty($order) or ($order == 'none')) {
        $ORDER = '';
    } else {
        $order = "ORDER BY $order";
    }

    if ((empty($startlist)) and (empty($listsize))) {
        $LIMIT = '';
    } else {
        $LIMIT = "LIMIT $startlist, $listsize";
    }

    if (!empty($conditions)) {
        $conditions = "WHERE $conditions";
    }

    $firstkey  = strTo($keys,',');
    $query     = "SELECT $keys FROM `$db_table` $conditions $order $LIMIT";
    $query_num = "SELECT $firstkey FROM `$db_table` $conditions";

    $db_query = mysql_query($query_num);
    $num_rows = mysql_num_rows($db_query);
    $db_query = mysql_query($query);
    SetDbQuery('db_GetSearchArray',$query);

    $RESULT = array();
    if ($db_query) {
        while ($row = mysql_fetch_assoc($db_query)) {
            $RESULT[] = $row;
        }
    }

    return $RESULT;
}


function db_GetArrayAll($db_table, $keys, $conditions, $order)
{
    if ($keys == '') {
        return '';
    }

    $order = (empty($order) or ($order == 'none'))? '' : "ORDER BY $order";
    if (!empty($conditions)) {
        $conditions = "WHERE $conditions";
    }

    $query = "SELECT $keys FROM `$db_table` $conditions $order";
    $db_query = mysql_query($query);
    SetDbQuery('db_GetArrayAll',$query);
    $RESULT = array();
    if ($db_query) {
        while ($row = mysql_fetch_assoc($db_query)) {
            $RESULT[] = $row;
        }
    }
    return $RESULT;
}

// -------------------- function to return an Associative Array from two fields in a Table ---------------------------
function db_GetAssocArray($db_table, $key, $value , $conditions = '')
{
    $RESULT = array();

    if (($key == '') or ($value == '')) {
        return $RESULT;
    }

    if (!empty($conditions)) {
        $conditions = "WHERE $conditions";
    }

    $query = "SELECT $key,$value FROM `$db_table` $conditions";
    $db_query = mysql_query($query);
    SetDbQuery('db_GetAssocArray',$query);
    if ($db_query) {
        while ($row = mysql_fetch_assoc($db_query)) {
            $RESULT[$row[$key]] = $row[$value];
        }
    }
    return $RESULT;
}


function db_GetFreq($db_table, $key, $order, $orderdir, $conditions)
{
    if ($key == '') {
        return '';
    }

    $order = ($order == 1)? 'ORDER BY 1 ' : 'ORDER BY 2 ';
    $order .= $orderdir;
    if (!empty($conditions)) {
        $conditions = "WHERE $conditions";
    }

    $query = "SELECT $key, COUNT(*) FROM `$db_table` $conditions GROUP BY $key $order";
    $db_query = mysql_query($query);
    SetDbQuery('db_GetFreq',$query);
    $RESULT = array();
    if (!$db_query) {
        return $RESULT;
    }

    while ($row = mysql_fetch_row($db_query)) {
        $RESULT[$row[0]] = $row[1];
    }

    return $RESULT;
}

function db_GetMax($db_table, $maxkey, $indexkey, $conditions)
{
    if (!empty($conditions)) {
        $conditions = "WHERE $conditions";
    }
    $query = "SELECT $indexkey, MAX($maxkey) FROM `$db_table` $conditions GROUP BY $indexkey";
    $db_query = mysql_query($query);
    SetDbQuery('db_GetMax',$query);
    $RESULT = array();

    if (!$db_query) {
        return $RESULT;
    }

    while ($row = mysql_fetch_row($db_query)) {
        $RESULT[$row[0]] = $row[1];
    }

    return $RESULT;
}


function db_FieldArray($db_table, $key, $conditions, $order)
{
    if ($key == '') {
        return '';
    }

    $order = (empty($order) or ($order == 'none'))? $ORDER = '' : "ORDER BY $order";
    if (!empty($conditions)) {
        $conditions = "WHERE $conditions";
    }

    $query = "SELECT $key FROM `$db_table` $conditions $order";
    $db_query = mysql_query($query);
    SetDbQuery('db_FieldArray',$query);
    $RESULT = array();
    if (!$db_query) {
        return $RESULT;
    }
    while ($row = mysql_fetch_assoc($db_query)) {
        $RESULT[] = $row[$key];
    }
    return $RESULT;
}

function db_GetValue($db_table, $key, $conditions)
{
    if (empty($key) or empty($conditions)) {
        return '';
    }

    $query = "SELECT $key FROM `$db_table` WHERE $conditions LIMIT 1";
    $db_query = mysql_query($query);
    SetDbQuery('db_GetValue',$query);
    if (!$db_query) {
        return '';
    }

    $RESULT = ($row = mysql_fetch_assoc($db_query))? $row[$key] : '';
    return $RESULT;
}


function db_IncValue($db_table, $key, $conditions)
{
    if (empty($key) or empty($conditions)) {
        return '';
    }
    $query = "SELECT $key FROM `$db_table` WHERE $conditions LIMIT 1";
    $db_query = mysql_query($query);
    if (!$db_query) {
        return '';
    }
    $RESULT = ($row = mysql_fetch_assoc($db_query))? $row[$key] : '';
    if ($RESULT != '') {
        $RESULT++;
        $query = "UPDATE `$db_table` SET $key=$RESULT WHERE $conditions LIMIT 1";
        mysql_query($query);
        SetDbQuery('db_IncValue',$query);
        return $RESULT;
    }
    return '';
}

function db_GetCount($db_table,$conditions)
{
    if (!empty($conditions)) {
        $conditions = "WHERE $conditions";
    }

    $query = "SELECT count(*) as `TOTALFOUND` FROM `$db_table` $conditions";
    $db_query = mysql_query($query);
    SetDbQuery('db_GetCount',$query);
    if ($db_query) {
        $row = mysql_fetch_assoc($db_query);
        $RESULT = $row['TOTALFOUND'];
    } else $RESULT = 0;
    return $RESULT;
}

function db_GetTables()
{
    $query = "SHOW TABLES";
    $db_query = mysql_query($query);
    $RESULT = array();
    if ($db_query) {
        while ($row = mysql_fetch_row($db_query)) {
            $RESULT[] = $row[0];
        }
    }
    return $RESULT;
}

function db_TableExists($db_table)
{
    $query = "SHOW TABLES LIKE `$db_table`";
    $db_query = mysql_query($query);
    if ($db_query) {
        $row = mysql_fetch_row($db_query);
        $table = $row[0];
    } else {
        $table = '';
    }

    return $table==$db_table;
}


function db_TableFieldInfo($db_table)
{
    $query = "SHOW COLUMNS FROM `$db_table`";
    $db_query = mysql_query($query);
    $RESULT = array();
    if ($db_query) {
        while ($row = mysql_fetch_assoc($db_query)) {
            $type = $row['Type'];
            $typeonly = strTo($type,'(');
            $size = TextBetween('(',')',$type);
            $row['Kind'] = $typeonly;
            $row['Size'] = $size;
            $RESULT[] = $row;
        }
    }
    return $RESULT;
}

function db_TableFieldNames($db_table)
{
    $RESULT = array();
    $tableinfo = db_TableFieldInfo($db_table);
    if (count($tableinfo)>0) {
        foreach ($tableinfo as $row) {
            $RESULT[]=$row['Field'];
        }
    }
    return $RESULT;
}

function db_TableFieldTitleNames($db_table)
{
    $RESULT = array();
    $tableinfo = db_TableFieldInfo($db_table);
    if (count($tableinfo)>0) {
        foreach ($tableinfo as $row) {
            $value = NameToTitle($row['Field']);
            $RESULT[$row['Field']]= $value;
        }
    }
    return $RESULT;
}


?>