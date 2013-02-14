<?php
//------MySQL Helper Routines - Michael V. Petrovich

require_once dirname(__FILE__).'/mvptools.php';

/*  ============================== defined functions =============================
ArrayToPost($arraymap, $array)
RowSelect($row_count, $rows, $start_row, $STARTROW, $ROWS, $HOME, $PREVIOUSPAGE, $NEXTPAGE, $END, $SHOW)
SetDbQuery($function, $query)
SetUpDbConnection($DB_INFO)
WriteDbQuery()
WriteDbQueryText()
db_AddRecord($db_table, $keys, $values)
db_Close()
db_CopyRow($db_table, $exclude_list, $change_list, $where)
db_Count($db_table, $WHERE='', $joins='')
db_DeleteRecord($db_table, $where)
db_FetchAssoc($db_query)
db_FetchRow($db_query)
db_FieldArray($db_table, $key, $conditions='', $order='')
db_GetArray($db_table, $keys, $conditions, $order, $startlist, $listsize, &$num_rows, $joins='', $get_count=true)
db_GetArrayAll($db_table, $keys='*', $conditions='', $order='', $joins='')
db_GetArrayAssoc($db_table, $referencekey, $keys='*', $conditions='', $order='', $joins='', $limit='')
db_GetAssocArray($db_table, $key, $value , $conditions='', $keycase='', $joins='', $order='', $limit='')
db_GetCount($db_table, $conditions='', $joins='')
db_GetCustomRecord($QUERY)
db_GetEnumArrays($db_table)
db_GetFieldValues($db_table, $key, $where='', $case='', $joins='')
db_GetFieldValuesLC($db_table, $key, $where='', $joins='')
db_GetFreq($db_table, $key, $order=2, $orderdir='', $conditions='', $joins='', $limit='')
db_GetMax($db_table, $maxkey, $indexkey, $conditions='')
db_GetNextDate($db_table, $key, $format, $inc)
db_GetNextValue($db_table, $key)
db_GetRecord($db_table, $keys, $where, $joins='')
db_GetTables()
db_GetUniqueID($db_table, $field)
db_GetValue($db_table, $key, $conditions, $joins='')
db_IncValue($db_table, $key, $conditions)
db_IsUnique($db_table, $key, $value, $exclude='')
db_KeyValues($array)
db_Keys($array, $assoc=true)
db_MaxValue($db_table, $key, $conditions='')
db_Query($QUERY)
db_QueryToArray($QUERY)
db_QuoteKey($key)
db_QuoteTables($db_tables)
db_QuoteValue($value)
db_QuoteValueC($value)
db_Rollback()
db_StartTransaction()
db_TableExists($db_table)
db_TableFieldInfo($db_table)
db_TableFieldNames($db_table)
db_TableFieldTitleNames($db_table)
db_TransactionCommit()
db_UpdateRecord($db_table, $key_values, $where)
db_Values($array)
db_ViewRecord($db_table, $keymap, $TableSetup, $keys, $where, $joins='')
db_ViewRecordArray($Record, $Titles, $TableSetup)
db_output_table($SearchArray, $UserInfo='', $TableSetup='', $EditTitle='', $EditLinks='', $id='')
*/

$DBQUERY               = '';
$DB_MAX_DBQUERY_LENGTH = 1000000; //strlength;
$DB_START_TIME         = microtime_float();
if (!isset($DB_WANT_QUERY)) $DB_WANT_QUERY = true;
$DbLastQuery           = '';
$DbQueryTableOptions   = 'align="center" style="background-color:#888; border:1px solid #000; margin-top:10px;"';
$DbQueryTableOptionsTH = 'align="center" style="background-color:#aaa; color:#fff;"';
$DbQueryTableOptionsTD = 'align="left" style="background-color:#ff7; color:#000; font-size:0.8em; padding:1em 1em 0px 0px;"';
$FieldValues           = array();
$DB_LAST_INSERT_ID     = 0;
$DB_NUMBER_ROWS        = 0;

if (!empty($DB_INFO)) {
    SetUpDbConnection($DB_INFO);
}

//------ call this function at start-up
function SetUpDbConnection($DB_INFO)
{
    @mysql_connect($DB_INFO['HOST'], $DB_INFO['USER'], $DB_INFO['PASS']) or Mtext('Error','Could not connect to database!');
    mysql_select_db($DB_INFO['NAME']);
    mysql_query('SET CHARACTER SET utf8');
    mysql_query("SET SESSION collation_connection='utf8_general_ci'");
}

function RowSelect($row_count, $rows, $start_row, $STARTROW, $ROWS, $HOME, $PREVIOUSPAGE, $NEXTPAGE, $END, $SHOW)
{
    if ($row_count > $rows) {
        $RESULT = '
        <div class=rowselect"><p>Found: "' . number_format($row_count) . '" records</p>
        <p>Start Row: <input type="text" size="4" maxlength="10" name="' . $STARTROW . '" value="' .$start_row . '" />&nbsp;&nbsp;
        Rows per Page: <input type="text" size="4" maxlength="4" name="' . $ROWS . '" value="' . $rows . '" />&nbsp;&nbsp;
        <input type="submit" value="Show" name="'.$SHOW.'" />&nbsp;';
        if ($start_row > 1) {
            $RESULT .= '<input type="submit" value="&lt;&lt;" name="' . $HOME . '" />&nbsp;';
        }
        if ($start_row > 1) {
            $RESULT .= '<input type="submit" value="<&mdash;" name="' . $PREVIOUSPAGE. '" />&nbsp;';
        }
        if ($row_count >= $start_row+$rows) {
            $RESULT .= '<input type="submit" value="&mdash;&gt;" name="' . $NEXTPAGE . '" />';
        }
        if ($row_count >= $start_row+$rows) {
            $RESULT .= '<input type="submit" value="&gt;&gt;" name="' . $END . '" />';
        }
        $RESULT .= '</p></div>';
    } else {
        $RESULT = '';
    }
    return $RESULT;
}

// ------------------ Database Query Tracking --------------
function SetDbQuery($function, $query)
{
    global $DBQUERY, $DbLastQuery, $DB_WANT_QUERY, $DB_START_TIME, $DB_CONNECTION, $DB_SHOW_TRACE, $DB_MAX_DBQUERY_LENGTH;

    static $max_reached = false;
    
    if (!$DB_WANT_QUERY or $max_reached) return;
    
    $DbLastQuery = $query;
    $query = str_replace(',', ', ', $query);

    $query = htmlspecialchars(trim($query), ENT_COMPAT, 'UTF-8');
    
    $span1 = '<span style="color:#f00; font-weight:bold;">';
    $span2 = '</span>';

    $keywords = array('UPDATE ','INSERT ','INTO ','SET ','LEFT ',' RIGHT ','JOIN ','NOW()','COUNT(',')',' AS ','CONCAT(',
        'WHERE ','LIMIT ','SELECT ',' AND ',' OR ',' ON ', ' FROM ',' MAX ','DELETE','VALUES(','GROUP BY ',' ORDER BY',
        'START TRANSACTION', 'COMMIT', 'ROLLBACK', 'MAX(');

    $line_breaks = array('SET ','LEFT ','RIGHT ','WHERE ','LIMIT ',' ON ', ' FROM ', 'VALUES(', 'GROUP BY ', ' ORDER');

    foreach ($keywords as $word) {
        if (in_array($word, $line_breaks)) {
            $query = str_ireplace($word, "<br />$span1$word$span2", $query);
        } else {
            $query = str_ireplace($word, "$span1$word$span2", $query);
        }
    }

    if (!empty($query)) {
        $query = "<b>$function:</b> $query";

        //-------------- tracing -------------
        if ($DB_SHOW_TRACE) {
            $trace = debug_backtrace();
            $trace_output = '<ul>';
            foreach($trace as $entry){
                if ($entry['function'] != 'SetDbQuery') {
                    $trace_output .= "<li><b>File:</b> {$entry['file']} (Line: {$entry['line']})<br />\n";
                    $trace_output .= (!empty($entry['class']))? "<b>Class:</b> {$entry['class']}<br />" : '';
                    $trace_output .= "<b>Function:</b> {$entry['function']}<br />\n";
                    $args = ArrayToStr($entry['args']);
                    if ($args) {
                        $trace_output .= "<b>Args:</b> $args\n";
                    }
                    $trace_output .= "</li>\n";
                }
            }
            $trace_output .= '</ul>';
        }


        $error = mysql_error();
        if (!empty($error)) {
            $error = "<br /><span style=\"background-color:#f00;color:#fff;padding:0px 3px;\">$error</span>";
            if (function_exists('AddError')) {
                AddError('Database Error - ' . $function);
            }
        }

        $RESULT  = '';
        if("$error$query" != '') $RESULT .= "<li style=\"padding:5px;\">$query$error\n";
        $RESULT .= '<ul>';
        if ($DB_SHOW_TRACE) {
            $RESULT .= '<li style="padding:5px;"><b>TRACE</b>' . $trace_output . "</li>\n";
        }
        $RESULT .= '<li style="padding:5px;"><b>Elapsed Time:</b> ' . number_format(microtime(true) - $DB_START_TIME, 3) . "</li>\n";
        $RESULT .= "</ul></li>\n";
        if (strlen($DBQUERY) + strlen($RESULT) > $DB_MAX_DBQUERY_LENGTH) {
            $max_reached = true;            
            $amount = number_format($DB_MAX_DBQUERY_LENGTH);
            $DBQUERY .= '<h3 style="color:#f00; text-align:center;">. . . Maximum Length of Query Text Reached (' . $amount . ')</h3>';
            
        } else {
            $DBQUERY .= $RESULT;
        } 
    }
}

function WriteDbQuery()
{
    echo WriteDbQueryText();
}

function WriteDbQueryText()
{
    global $DBQUERY, $DbQueryTableOptions, $DbQueryTableOptionsTH, $DbQueryTableOptionsTD;
    SetDbQuery('','');  // update in case last query had an error

    $RESULT = '';
    $id='db_query_results'.date('His').rand(100,999);
    if (!empty($DBQUERY)) {
        $RESULT .= "\n\n<table id=\"$id\" $DbQueryTableOptions>\n";
        $RESULT .= '<tr><th '.$DbQueryTableOptionsTH.'>DB Queries (<a href="#" onclick="getElementById(\''.$id.'\').style.display=\'none\'; return false;">Hide</a>)</th></tr>';
        $RESULT .= "\n<tr><td $DbQueryTableOptionsTD>\n<ul style=\"margin:0px 2em;\">$DBQUERY</ul>\n</td></tr>\n";
        $RESULT .= "</table>\n";
    }
    return $RESULT;
}

function db_QuoteValue($value)
{
    global $DB_CONNECTION;
    $value = trim($value);
    if (($value == 'NOW()') or ($value == '0') or (is_numeric($value) and (strlen($value)<12) and (substr($value, 0, 1)!='0')))  return $value;
    elseif ((strpos($value, "'")!==false) and (strpos($value, '"')===false)) return "\"$value\"";
    elseif (strpos($value, "'")===false)  return "'$value'";
    else return "'" . mysqli_real_escape_string($DB_CONNECTION, $value) . "'";
}

function db_QuoteValueC($value)
{
    return db_QuoteValue($value) . ',';
}

function db_QuoteKey($key)
{
    $key = str_replace('.', '`.`', $key);
    $key = str_ireplace(' AS ', '` AS `', $key);
    $key = str_replace('``','`', $key);
    $key = str_replace(')`', ')', $key);
    $key = "`$key`";
    $key = str_replace('``','`', $key);
    $key = preg_replace('/`([a-zA-Z\_]+)\(/', '\1(', $key);
    $key = str_replace(')`',')', $key);
    $key = str_replace('`(','(', $key);
    return $key;
}

function db_QuoteTables($tables)
{
    $RESULT = str_replace(array(' ', '`'), '', $tables);
    $RESULT = '`' . str_replace(',', '`,`', $RESULT) . '`';
    $RESULT = preg_replace('/`+', '`/', $RESULT);
    return $RESULT;
}


function db_KeyValues($array)
{
    $RESULT = '';
    if (!empty($array)) {
        foreach ($array as $key => $value) {
            $key = db_QuoteKey($key);
            $RESULT .= "$key=". db_QuoteValueC($value);
        }
        $RESULT = substr($RESULT,0,-1);
    }
    return $RESULT;
}

// takes keys from an array or associative array and returns a key list.
function db_Keys($array, $assoc=true)
{
    $RESULT = '';
    if (!empty($array)) {
        if ($assoc) {
            $keys = array_keys($array);
        } else {
            $keys = $array;
        }
        foreach ($keys as $key) {
            $key = db_QuoteKey($key) . ',';
            $RESULT .= $key;
        }
        $RESULT = substr($RESULT,0,-1);
    }
    return $RESULT;
}

function db_Values($array)
{   // this function works on standard and associative arrays
    $RESULT = '';
    if (!empty($array)) {
        foreach ($array as $key => $value) {
            $RESULT .= db_QuoteValueC($value);
        }
        $RESULT = substr($RESULT,0,-1);
    }
    return $RESULT;
}

function ArrayToPost($arraymap, $array)
{
    foreach ($arraymap as $varname => $postname) {
        $_POST[$postname] = empty($array[$varname])? '' : $array[$varname];
    }
}

function db_output_table($SearchArray, $UserInfo='', $TableSetup='', $EditTitle='', $EditLinks='', $id='')
{
    global $FieldValues;
    if (count($SearchArray) == 0) return '';
    $RESULT = "<table $TableSetup>\n";
    $RESULT .= "<tbody>\n<tr><th>No.</th>";
    $wantedit = (!empty($id) and !empty($EditLinks));
    if ($wantedit) $RESULT .= "<th>$EditTitle</th>";
    if (empty($UserInfo)) {
        $UserInfo = array();
        foreach ($SearchArray[0] as $key=>$value) {
            $UserInfo[$key] = NameToTitle($key);
        }
    }

    foreach ($UserInfo as $key => $value) {
        if (array_key_exists($key, $SearchArray[0])) {
            $RESULT .= "<th>{$value}</th>";
        }
    }
    
    $RESULT .= "</tr>\n";
    $evenodd = 2;
    $count = 0;
    foreach ($SearchArray as $row) {
        $count++;
        $evenodd = 3 - $evenodd;
        $class = ($evenodd == 1) ? 'odd' : 'even';
        $RESULT .= "<tr class=\"$class\"><td align=\"right\">$count.</td>";
        if ($wantedit) $RESULT .= '<td>'. str_replace('@@ID@@', $row[$id], $EditLinks) .'</td>';
        foreach ($UserInfo as $key => $value) {
            if (isset($row[$key])) {
                $field = $row[$key];
                $outvalue = (empty($FieldValues[$key][$field]))?
                    $field :
                    $FieldValues[$key][$field];
                $RESULT .= "<td>$outvalue</td>";
            } else {
                $RESULT .= "<td></td>";  // null value
            }
        }
        $RESULT .= "</tr>\n";
    }
    $RESULT .= "</tbody></table>\n";
    return $RESULT;
}

function db_GetLastInsertId()
{
    global $DB_LAST_INSERT_ID;
    return $DB_LAST_INSERT_ID;
}

function db_IsUnique($db_table, $key, $value, $exclude='')
{
    if (empty($key) or empty($value)) {
        return false;
    }
    $excludestr = (!empty($exclude))? " AND $exclude" :'';

    $db_table = db_QuoteTables($db_table);
    $key = db_QuoteKey($key);
    $value = db_QuoteValue($value);

    $query = "SELECT $key FROM $db_table WHERE $key=$value $excludestr LIMIT 1";
    $db_query = mysql_query($query);
    SetDbQuery('db_IsUnique', $query);
    if ($db_query) {
        $row = mysql_fetch_row($db_query);
        mysql_free_result($db_query);
    }
    return empty($row[0]);
}

function db_GetUniqueID($db_table, $field) {
    $UID = md5(uniqid(rand(), true));
    while (!db_IsUnique($db_table, $field, $UID)) {
        $UID = md5(uniqid(rand(), true));
    }
    return $UID;
}


function db_GetNextValue($db_table, $key)
{
    if (empty($key)){
        return '';
    }
    $db_table = db_QuoteTables($db_table);
    $query    = "SELECT MAX($key) AS maxkey FROM $db_table";
    $db_query = mysql_query($query);
    SetDbQuery('db_GetNextValue', $query);
    if ($db_query) {
        $row      = mysql_fetch_assoc($db_query);
        mysql_free_result($db_query);
        $RESULT   = $row['maxkey'] + 1;
    } else {
        $RESULT = 0;
    }
    return $RESULT;
}

function db_GetNextDate($db_table, $key, $format, $inc)
{
    if (empty($key)) {
        return '';
    }

    $db_table = db_QuoteTables($db_table);

    $query    = "SELECT MAX($key) AS maxkey FROM $db_table";
    $db_query = mysql_query($query);
    SetDbQuery('db_GetNextDate', $query);
    if ($db_query) {
        $row      = mysql_fetch_assoc($db_query);
        mysql_free_result($db_query);
        $maxdate  = DateToDashes($row['maxkey']);

        if (strlen($maxdate)<8) {
            $maxdate .= '-01';
        }
        return date($format,strtotime("$maxdate +1 $inc"));
    } else return '';
}


function db_GetFieldValues($db_table, $key, $where='', $case='', $joins='')
{
    $RESULT = array();
    $case = strtoupper($case);

    if (!empty($where)) $where = "WHERE $where";

    if (empty($key)) {
        return $RESULT;
    }

    $db_table = db_QuoteTables($db_table);

    $query    = "SELECT $key FROM $db_table $joins $where GROUP BY $key ORDER BY $key";
    $db_query = mysql_query($query);

    SetDbQuery('db_GetFieldValues', $query);

    if (!$db_query) {
        return $RESULT;
    }

    while ($row = mysql_fetch_array($db_query, MYSQL_NUM)) {
        $value = $row[0];
        if (!empty($value)) {
            if ($case == 'U') $value = strtoupper($value);
            elseif ($case == 'L') $value = strtolower($value);
            $RESULT[] = $value;
        }
    }
    mysql_free_result($db_query);
    return $RESULT;
}

function db_GetFieldValuesLC($db_table, $key, $where='', $joins='')
{
    return db_GetFieldValues($db_table, $key, $where, 'L', $joins);
}

function db_ViewRecord($db_table, $keymap, $TableSetup, $keys, $where, $joins='')
{
    global $FieldValues;
    if (empty($keys) or empty($where)) return '';

    $db_table = db_QuoteTables($db_table);

    $query = "SELECT $keys FROM $db_table $joins WHERE $where LIMIT 1";
    $db_query = mysql_query($query);
    SetDbQuery('db_ViewRecord', $query);

    if (!$db_query) {
        return '';
    }

    $row = mysql_fetch_assoc($db_query);
    mysql_free_result($db_query);

    if (count($row)>0) {
        $RESULT = "<table $TableSetup>\n";

        if (!empty($keymap)) {
            foreach ($keymap as $key => $value) {
                $newkey = strFrom($key,'AS ');
                if (empty($newkey)) $newkey = strFrom($key,'.');
                if (empty($newkey)) $newkey = $key;
                if (isset($row[$newkey])) {
                    $outvalue = (empty($FieldValues[$key][$value]))? $row[$newkey] : $FieldValues[$key][$row[$newkey]];
                    $RESULT .= "<tr><th align=\"right\">$value</th><td>$outvalue</td></tr>\n";
                }
            }
        } else {
            foreach ($row as $key => $value) {
                $outvalue = (empty($FieldValues[$key][$value]))? $value : $FieldValues[$key][$value];
                $RESULT .= "<tr><th align=\"right\">$key</th><td>$outvalue</td></tr>\n";
            }
        }

        $RESULT .= "</table>\n";
    } else {
        $RESULT = '';
    }
    return $RESULT;
}

function db_ViewRecordArray($Record, $Titles, $TableSetup)
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


function db_GetRecord($db_table, $keys, $where, $joins='')
{
    if (empty($keys) or empty($where)) {
        return '';
    }

    $db_table = db_QuoteTables($db_table);

    $query = "SELECT $keys FROM $db_table $joins WHERE $where LIMIT 1";
    $db_query = mysql_query($query);
    SetDbQuery('db_GetRecord', $query);

    if ($db_query) {
        $RESULT = mysql_fetch_assoc($db_query);
        mysql_free_result($db_query);
    } else {
        $RESULT = 0;
    }
    return $RESULT;
}

function db_UpdateRecord($db_table, $key_values, $where)
{
    global $DB_LAST_INSERT_ID;
    if (empty($key_values) or empty($where)) {
        return false;
    }
    $db_table = db_QuoteTables($db_table);
    $query = "UPDATE $db_table SET $key_values WHERE $where";
    $db_query = mysql_query($query);
    SetDbQuery('db_UpdateRecord', $query);
    if ($db_query) {
        $DB_LAST_INSERT_ID = mysql_insert_id();
        return 1;
    }
    $DB_LAST_INSERT_ID = 0;
    return 0;
}

function db_DeleteRecord($db_table, $where)
{
    if (empty($where)) {
        return false;
    }

    $db_table = db_QuoteTables($db_table);
    $query = "DELETE FROM $db_table WHERE $where";
    $db_query = mysql_query($query);
    SetDbQuery('db_DeleteRecord', $query);
    if ($db_query) {
        return mysql_affected_rows();
    }
    return 0;
}

function db_AddRecord($db_table, $keys, $values)
{
    global $DB_LAST_INSERT_ID;
    if (empty($keys) or empty($values)) {
        return false;
    }

    $db_table = db_QuoteTables($db_table);
    $query = "INSERT INTO $db_table ($keys) VALUES ($values)";
    $db_query = mysql_query($query);
    SetDbQuery('db_AddRecord', $query);

    if ($db_query) {
        $DB_LAST_INSERT_ID = mysql_insert_id();
        return mysql_affected_rows();
    }
    $DB_LAST_INSERT_ID = 0;
    return 0;

}


// function db_GetSearchArray($db_table, $keys, $conditions, $order, $startlist, $listsize, &$num_rows, &$query)
// {
  // global $DbLastQuery;
  // //deprecated - do not need &$query can get from $DbLastQuery  -- use db_GetArray()

  // $RESULT = db_GetArray($db_table, $keys, $conditions, $order, $startlist, $listsize, $num_rows);
  // $query  = $DbLastQuery;
  // return $RESULT;
// }

//function db_GetArray($table, $keys='', $where='', $order='', $start_list='', $list_size='', &$num_rows, $joins='', $get_count=true)

function db_GetArray($table, $keys='*', $where='', $order='', $start_list='', $list_size='', $joins='', $get_count=true)
{
    global $DB_NUMBER_ROWS;
    if (is_array($table)) extract($table, EXTR_OVERWRITE);
    
    // joins need to be provide in full SQL
    if ($keys == '') {
        return '';
    }

    if (empty($order) or ($order == 'none')) {
        $order = '';
    } else {
        $order = "ORDER BY $order";
    }

    if (!empty($where)) {
        $where = "WHERE $where";
    }

    $table = db_QuoteTables($table);

    // --------- get row count ---------
    if ($get_count) {
        $query = "SELECT count(1) FROM $table $joins $where";
        $db_query = mysql_query($query);
        if ($db_query) {
            $row = mysql_fetch_row($db_query);
            $DB_NUMBER_ROWS = $row[0];
            mysql_free_result($db_query);
            if ($start_list < 0) $start_list = $DB_NUMBER_ROWS + $start_list;  // gets last rows (-100, would get last 100 rows)
        } else {
            $DB_NUMBER_ROWS = 0;
        }
    } else {
        $DB_NUMBER_ROWS = 0;
    }


    if ((empty($start_list)) and (empty($list_size))) {
        $LIMIT = '';
    } else {
        $LIMIT = "LIMIT $start_list, $list_size";
    }

    $query    = "SELECT $keys FROM $table $joins $where $order $LIMIT";
    $db_query = mysql_query($query);

    SetDbQuery('db_GetArray', $query);

    $RESULT = array();
    if ($db_query) {
        while ($row = mysql_fetch_assoc($db_query)) {
            $RESULT[] = $row;
        }
        mysql_free_result($db_query);
    }
    return $RESULT;
}


function db_GetArrayAll($db_table, $keys='*', $conditions='', $order='', $joins='')
{
    if ($keys == '') {
        return '';
    }

    $order = (empty($order) or ($order == 'none'))? '' : "ORDER BY $order";

    if (!empty($conditions)) {
        $conditions = "WHERE $conditions";
    }

    $db_table = db_QuoteTables($db_table);

    $query = "SELECT $keys FROM $db_table $joins $conditions $order";
    $db_query = mysql_query($query);
    SetDbQuery('db_GetArrayAll', $query);
    $RESULT = array();
    if ($db_query) {
        while ($row = mysql_fetch_assoc($db_query)) {
            $RESULT[] = $row;
        }
        mysql_free_result($db_query);
    }
    return $RESULT;
}

function db_GetArrayAssoc($db_table, $referencekey, $keys='*', $conditions='', $order='', $joins='', $limit='')
{
    if(!empty($order)) $order = "ORDER BY $order";

    if (!empty($conditions)) {
        $conditions = "WHERE $conditions";
    }

    if (!empty($limit)) {
        $limit = " LIMIT $limit";
    }

    $db_table = db_QuoteTables($db_table);
    
    $quote_ref = db_QuoteKey($referencekey);

    $query = "SELECT $keys, $quote_ref AS 'THE_REFERENCE_KEY' FROM $db_table $joins $conditions $order $limit";

    $db_query = mysql_query($query);
    SetDbQuery("db_GetArrayAssoc ($referencekey)", $query);
    $RESULT = array();
    if ($db_query) {
        while ($row = mysql_fetch_assoc($db_query)) {
            if (!empty($row['THE_REFERENCE_KEY']) or ($row['THE_REFERENCE_KEY'] == 0)) {
                $RESULT[$row['THE_REFERENCE_KEY']] = $row;
            }
        }
        mysql_free_result($db_query);
    }
    return $RESULT;
}

// -------------------- function to return an Associative Array from two fields in a Table ---------------------------
function db_GetAssocArray($db_table, $key, $value , $conditions='', $keycase='', $joins='', $order='', $limit='')
{
    $RESULT = array();
    $keycase = strtoupper($keycase);

    if (($key == '') or ($value == '')) {
        return $RESULT;
    }

    if (!empty($conditions)) {
        $conditions = "WHERE $conditions";
    }

    if (!empty($limit)) {
        $limit = " LIMIT $limit";
    }

    $key = db_QuoteKey($key);
    $value = db_QuoteKey($value);
    if(empty($order)) $order = $key;

    $db_table = db_QuoteTables($db_table);
    
    
    $query = "SELECT $key, $value FROM $db_table $joins $conditions ORDER BY $order$limit";
    $db_query = mysql_query($query);
    SetDbQuery('db_GetAssocArray', $query);
    if ($db_query) {
        while ($row = mysql_fetch_array($db_query, MYSQL_NUM)) {
            $idx = $row[0];
            if (!empty($idx) or ($idx == 0)) {
                if ($keycase == 'U') $idx = strtoupper($idx);
                elseif ($keycase == 'L') $idx = strtolower($idx);
                $RESULT[$idx] = $row[1];
            }
        }
        mysql_free_result($db_query);
    }
    return $RESULT;
}


function db_Count($db_table, $WHERE='', $joins='')
{
    if (!empty($WHERE)) $WHERE = " WHERE $WHERE";

    $db_table = db_QuoteTables($db_table);

    $query = "SELECT count(1) FROM $db_table $joins $WHERE";
    $db_query = mysql_query($query);
    SetDbQuery('db_Count', $query);
    if ($db_query) {
        $row = mysql_fetch_row($db_query);
        mysql_free_result($db_query);
        $RESULT = $row[0];
    } else {
        $RESULT = 0;
    }
    return $RESULT;
}

function db_GetCount($db_table, $conditions='', $joins='')
{
   // depreciated: use db_Count
    return db_Count($db_table, $conditions, $joins);
}

function db_GetFreq($db_table, $key, $order=2, $orderdir='', $conditions='', $joins='', $limit='')
{
    if ($key == '') {
        return '';
    }

    if (!empty($limit)) {
        $limit = " LIMIT $limit";
    }


    $order = ($order == 1)? 'ORDER BY 1 ' : 'ORDER BY 2 ';
    $order .= $orderdir;
    if (!empty($conditions)) {
        $conditions = "WHERE $conditions";
    }

    $db_table = db_QuoteTables($db_table);
    $key      = db_QuoteKey($key);
    
    $query = "SELECT $key, COUNT(1) FROM $db_table $joins $conditions GROUP BY 1 $order$limit";
    $db_query = mysql_query($query);
    SetDbQuery('db_GetFreq', $query);
    $RESULT = array();
    if (!$db_query) {
        return $RESULT;
    }

    while ($row = mysql_fetch_row($db_query)) {
        $RESULT[$row[0]] = $row[1];
    }

    mysql_free_result($db_query);
    return $RESULT;
}

function db_GetMax($db_table, $maxkey, $indexkey, $conditions='')
{
    if (!empty($conditions)) {
        $conditions = "WHERE $conditions";
    }

    $db_table = db_QuoteTables($db_table);

    $indexkey = db_QuoteKey($indexkey);
    $maxkey   = db_QuoteKey($maxkey);
    $query = "SELECT $indexkey, MAX($maxkey) FROM $db_table $conditions GROUP BY $indexkey";
    $db_query = mysql_query($query);
    SetDbQuery('db_GetMax', $query);
    $RESULT = array();

    if (!$db_query) {
        return $RESULT;
    }

    while ($row = mysql_fetch_row($db_query)) {
        $RESULT[$row[0]] = $row[1];
    }

    mysql_free_result($db_query);
    return $RESULT;
}

function db_MaxValue($db_table, $key, $conditions='')
{
    if (!empty($conditions)) {
        $conditions = "WHERE $conditions";
    }

    $db_table = db_QuoteTables($db_table);

    $key = db_QuoteKey($key);
    $query = "SELECT MAX($key) FROM $db_table $conditions";
    $db_query = mysql_query($query);
    SetDbQuery('db_MaxValue', $query);

    if (!$db_query) {
        return '';
    }

    $row = mysql_fetch_row($db_query);
    mysql_free_result($db_query);
    return $row[0];
}


function db_FieldArray($db_table, $key, $conditions='', $order='')
{
    if ($key == '') {
        return '';
    }

    if (!empty($order)) $order = "ORDER BY $order";
    if (!empty($conditions)) $conditions = "WHERE $conditions";

    $db_table = db_QuoteTables($db_table);
    $key      = db_QuoteKey($key);
    
    $query = "SELECT $key FROM $db_table $conditions $order";
    $db_query = mysql_query($query);
    SetDbQuery('db_FieldArray', $query);
    $RESULT = array();
    if (!$db_query) {
        return $RESULT;
    }
    while ($row = mysql_fetch_row($db_query)) {
        $RESULT[] = $row[0];
    }
    mysql_free_result($db_query);
    return $RESULT;
}

function db_GetValue($db_table, $key, $conditions, $joins='')
{
    if (empty($key) or empty($conditions)) {
        return '';
    }

    $db_table = db_QuoteTables($db_table);
    $key = db_QuoteKey($key);

    $query = "SELECT $key FROM $db_table $joins WHERE $conditions LIMIT 1";
    $db_query = mysql_query($query);
    SetDbQuery('db_GetValue', $query);
    if (!$db_query) {
        return '';
    }

    $RESULT = ($row = mysql_fetch_row($db_query))? $row[0] : '';
    mysql_free_result($db_query);
    return $RESULT;
}


function db_IncValue($db_table, $key, $conditions)
{
    global $DB_LAST_INSERT_ID;
    if (empty($key) or empty($conditions)) {
        return '';
    }

    // could use: UPDATE MyTable SET MyColumn=MyColumn+1 Where MyID=123

    $db_table = db_QuoteTables($db_table);

    $query = "SELECT $key FROM $db_table WHERE $conditions LIMIT 1";
    $db_query = mysql_query($query);
    if (!$db_query) {
        return '';
    }
    $RESULT = ($row = mysql_fetch_assoc($db_query))? $row[$key] : '';
    if ($RESULT != '') {
        $RESULT++;
        $query = "UPDATE $db_table SET $key=$RESULT WHERE $conditions LIMIT 1";
        mysql_query($query);
        SetDbQuery('db_IncValue', $query);
        $DB_LAST_INSERT_ID = mysql_insert_id();
        mysql_free_result($db_query);
        return $RESULT;
    }
    return '';
}



function db_GetTables()
{
    $query = "SHOW TABLES";
    $db_query = mysql_query($query);
    SetDbQuery('db_GetTables', $query);
    if ($db_query) {
        while ($row = mysql_fetch_row($db_query)) {
            $RESULT[] = $row[0];
        }
        mysql_free_result($db_query);
    }
    NatCaseSort($RESULT);
    return $RESULT;
}

function db_TableExists($db_table)
{
    $query = "SHOW TABLES LIKE '$db_table'";
    $db_query = mysql_query($query);
    SetDbQuery('db_TableExists', $query);
    if ($db_query) {
        $row = mysql_fetch_row($db_query);
        $table = $row[0];
        mysql_free_result($db_query);
    } else {
        $table = '';
    }

    return $table==$db_table;
}


function db_TableFieldInfo($db_table)
{
    $db_table = db_QuoteTables($db_table);
    $query = "SHOW COLUMNS FROM $db_table";
    $db_query = mysql_query($query);
    SetDbQuery('db_TableFieldInfo', $query);
    $RESULT = array();
    if ($db_query) {
        while ($row = mysql_fetch_assoc($db_query)) {
            $type = $row['Type'];
            $typeonly = strTo($type,'(');
            $size = TextBetween('(',')', $type);
            $row['Kind'] = $typeonly;
            $row['Size'] = $size;
            $RESULT[] = $row;
        }
        mysql_free_result($db_query);
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

function db_GetEnumArrays($db_table) {
    $RESULT = array();
    $table_info = db_TableFieldInfo($db_table);
    foreach ($table_info as $row) {
        if ($row['Kind'] == 'enum') {
            $size = $row['Size'];
            $RESULT[$row['Field']] = TextBetweenArray("'","'", $row['Size']);
        }
    }
    return $RESULT;
}

function db_CopyRow($db_table, $exclude_list, $change_list, $where)
{
    // $exculude_list is a comma delimited list
    // $change_list is a '|' delimited list  >> "var1=My Value|var2=My Other Value"  --- do not use quotes for values
    // $change_list can assign values from another field using '=='  >> "var1==myId"

    global $DB_LAST_INSERT_ID;

    $row = db_GetRecord($db_table, '*', $where);
    if ($row) {
        $changes  = explode('|', $change_list);
        TrimArray($changes);
        foreach ($changes as $change) {
            $var   = trim(strTo($change,'='));
            $value = trim(strFrom($change, '='));
            if (substr($value, 0, 1) == '=') {
                $field = trim(strFrom($value,'='));
                $value = $row[$field];
            }
            $row[$var] = $value;
        }

        $excludes = explode(',', $exclude_list);
        TrimArray($excludes);
        foreach ($excludes as $field) unset($row[$field]);

        db_AddRecord($db_table, db_Keys($row), db_Values($row));

        $DB_LAST_INSERT_ID = mysql_insert_id();
        return $DB_LAST_INSERT_ID;
    } else return false;
}


function db_Query($QUERY)
{
	$RESULT = @mysql_query($QUERY);
    SetDbQuery('db_Query', $QUERY);
	return $RESULT;
}

function db_FetchAssoc($db_query)
{
    return mysql_fetch_assoc($db_query);
}

function db_FetchRow($db_query)
{
    return mysql_fetch_row($db_query);
}


function db_QueryToArray($QUERY)
{
    $RESULT = array();
    $db_query = mysql_query($QUERY);
    SetDbQuery('db_QueryToArray', $QUERY);
    if ($db_query) {
        while ($row = mysql_fetch_assoc($db_query)) {
            $RESULT[] = $row;
        }
        mysql_free_result($db_query);
    }
    return $RESULT;
}


function db_GetCustomRecord($QUERY)
{
    SetDbQuery('db_GetCustomRecord', $QUERY);
    $db_query = mysql_query($QUERY);
    if ($db_query) {
        return mysql_fetch_assoc($db_query);
    } else {
        return 0;
    }
}

function db_StartTransaction()
{
    $QUERY = 'START TRANSACTION';
    SetDbQuery('db_StartTransaction', "-------- $QUERY --------");
    $db_query = mysql_query($QUERY);
    if ($db_query) {
        return true;
    } else {
        return false;
    }
}

function db_TransactionCommit()
{
    $QUERY = 'COMMIT';
    SetDbQuery('db_TransactionCommit', "-------- $QUERY --------");
    return mysql_query($QUERY);
}

function db_Rollback()
{
    $QUERY = 'ROLLBACK';
    SetDbQuery('db_Rollback', "-------- $QUERY --------");
    $db_query = mysql_query($QUERY);
    if ($db_query) {
        return true;
    } else {
        return false;
    }
}

function db_Error()
{
    global $DB_CONNECTION;
    return mysqli_error($DB_CONNECTION);
}

function db_Close()
{
    mysql_close($DB_CONNECTION);
}