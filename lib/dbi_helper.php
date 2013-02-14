<?php
//------MySQL Helper Routines - Michael V. Petrovich
// version to use MySQLi functions --- REQUIRES PHP 5+

//require_once dirname(__FILE__).'/mvptools.php';

/*  ============================== defined functions =============================
example db_Function(array
    'table' => '',
    'keys'  => '',
    'values'=> '',
    'where' => '',
    'order' => '',
    'joins' => ''
));


PARAMETERS
case
change_list
exclude_list
get_count
group
joins
key
key_values
key_case
keys
limit
list_size
order
order_direction
reference_key
start_list
table
table_setup
title_array
values
where

FUNCTIONS

ArrayToPost($arraymap, $array)
RowSelect($row_count, $rows, $start_row, $STARTROW, $ROWS, $HOME, $PREVIOUSPAGE, $NEXTPAGE, $END, $SHOW)
SetDbQuery($function, $query)
SetUpDbConnection($DB_INFO)
WriteDbQuery()
WriteDbQueryText()
db_AddRecord($table, $keys='', $values='')
db_Close($connection='')
db_CopyRow($table, $exclude_list='', $change_list='', $where='')
db_Count($table, $where='', $joins='')
db_DeleteRecord($table, $where='')
db_Error()
db_FetchAssoc($db_query)
db_FetchRow($db_query)
db_FieldArray($table, $key='', $where='', $order='')
db_GetArray($table, $keys='', $where='', $order='', $start_list='', $list_size='', $joins='', $get_count=true)
db_GetArrayAll($table, $keys='*', $where='', $order='', $joins='')
db_GetArrayAssoc($table, $reference_key='', $keys='*', $where='', $order='', $joins='', $limit='')
db_GetAssocArray($table, $key='', $value='' , $where='', $key_case='', $joins='', $order='', $limit='')
db_GetCount($table, $where='', $joins='')
db_GetCustomRecord($QUERY)
db_GetEnumArrays($table)
db_GetFieldValues($table, $key='', $where='', $case='', $joins='')
db_GetFieldValuesLC($table, $key='', $where='', $joins='')
db_GetFreq($table, $key='', $order=2, $order_direction='', $where='', $joins='', $limit='')
db_GetLastInsertId()
db_GetLastNumberRows()
db_GetMax($table, $key='', $group='', $where='')
db_GetNextDate($table, $key, $format, $inc)
db_GetNextValue($table, $key)
db_GetRecord($table, $keys='', $where='', $joins='')
db_GetTables()
db_GetUniqueID($table, $field)
db_GetValue($table, $key='', $where='', $joins='')
db_IncValue($table, $key='', $where='')
db_IsUnique($table, $key, $value, $exclude='')
db_KeyValues($array)
db_Keys($array, $assoc=true)
db_MaxValue($table, $key='', $where='')
db_Query($QUERY)
db_QueryToArray($QUERY)
db_QuoteKey($key)
db_QuoteTables($tables)
db_QuoteValue($value)
db_QuoteValueC($value)
db_Rollback()
db_StartTransaction()
db_TableExists($table)
db_TableFieldInfo($table)
db_TableFieldNames($table)
db_TableFieldTitleNames($table)
db_TransactionCommit()
db_UpdateRecord($table, $key_values='', $where='')
db_Values($array)
db_ViewRecord($table, $title_array='', $table_setup='', $keys='', $where='', $joins='')
db_ViewRecordArray($record, $title_array, $table_setup)
db_output_table($SearchArray, $UserInfo='', $table_setup='', $EditTitle='', $EditLinks='', $id='')
*/

$DB_HELPER_NAME        = 'dbi_helper';
$DBQUERY               = '';
$DB_MAX_DBQUERY_LENGTH = 1000000; //strlength;
$DB_START_TIME         = microtime(true);
$DB_SHOW_TRACE         = false;
if (!isset($DB_WANT_QUERY)) $DB_WANT_QUERY = true;
$DbLastQuery           = '';
$DbQueryTableOptions   = 'align="center" style="background-color:#888; border:1px solid #000; margin-top:10px;"';
$DbQueryTableOptionsTH = 'style="text-aling:center; background-color:#aaa; color:#fff;"';
$DbQueryTableOptionsTD = 'style="text-align:left; background-color:#ff7; color:#000; font-size:0.8em; padding:1em 1em 0px 0px;"';
$FieldValues           = array();

$DB_CONNECTION         = '';
$DB_LAST_INSERT_ID     = 0;
$DB_NUMBER_ROWS        = 0;

if (!empty($DB_INFO)) {
    SetUpDbConnection($DB_INFO);
}

function db_GetLastQuery()
{
    global $DbLastQuery ;
    return $DbLastQuery;
}

function db_SetWantQuery($value)
{
    global $DB_WANT_QUERY;
    $DB_WANT_QUERY = $value;
}

function db_SetTrace($value)
{
    global $DB_SHOW_TRACE;
    $DB_SHOW_TRACE = $value;
}

function db_GetAffectedRows()
{
    global $DB_CONNECTION;
    return mysqli_affected_rows($DB_CONNECTION);
}  


//------ call this function at start-up
function SetUpDbConnection($DB_INFO)
{
    global $DB_CONNECTION;
    $DB_CONNECTION = @mysqli_connect($DB_INFO['HOST'], $DB_INFO['USER'], $DB_INFO['PASS'])
            or Mtext('Error','Could not connect to database!');
    mysqli_select_db($DB_CONNECTION, $DB_INFO['NAME']);
    mysqli_set_charset($DB_CONNECTION, 'utf8');
    mysqli_query($DB_CONNECTION, "SET SESSION collation_connection='utf8_general_ci'");
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

    $query = preg_replace('/\b([a-zA-Z]+\()/', "$span1\$1$span2", $query);
    $query = preg_replace('/\b(UPDATE|INSERT|INTO|SET|LEFT|RIGHT|JOIN|AS|WHERE|LIMIT|SELECT|AND|OR|ON|FROM|DELETE|GROUP BY|ORDER BY|START TRANSACTION|COMMIT|ROLLBACK)\b/i', "$span1\$1$span2", $query);
    $query = preg_replace('/\b(SET|LEFT|RIGHT|WHERE|LIMIT|ON|FROM|VALUES|GROUP|ORDER)\b/i', '<br />$1', $query);
    $query = str_replace(')', "$span1)$span2", $query);

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


        $error = mysqli_error($DB_CONNECTION);
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
    //SetDbQuery('','');  // update in case last query had an error -- no longer needed

    $RESULT = '';
    $id='db_query_results'.date('His').rand(100,999);
    if (!empty($DBQUERY)) {
        $RESULT .= "\n\n<table id=\"$id\" $DbQueryTableOptions>\n";
        $RESULT .= '<tr><th ' . $DbQueryTableOptionsTH . '>DB Queries (<a href="#" onclick="getElementById(\''.$id.'\').style.display=\'none\'; return false;">Hide</a>)</th></tr>';
        $RESULT .= "\n<tr><td $DbQueryTableOptionsTD>\n<ol style=\"margin:0px 2em;\">$DBQUERY</ol>\n</td></tr>\n";
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
    $RESULT = preg_replace('/`+/', '`', $RESULT);
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

function db_output_table($SearchArray, $UserInfo='', $table_setup='', $EditTitle='', $EditLinks='', $id='')
{
    global $FieldValues;
    if (count($SearchArray) == 0) return '';
    $RESULT = "<table $table_setup>\n";
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
                $outvalue = (empty($FieldValues[$key][$field]))? $field : $FieldValues[$key][$field];
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

function db_GetLastNumberRows()
{
    global $DB_NUMBER_ROWS;
    return $DB_NUMBER_ROWS;
}

function db_IsUnique($table, $key='', $value='', $exclude='')
{
    global $DB_CONNECTION;

    if (is_array($table)) extract($table, EXTR_OVERWRITE);

    if (empty($key) or empty($value)) {
        return false;
    }
    $excludestr = (!empty($exclude))? " AND $exclude" :'';

    $table = db_QuoteTables($table);
    $key = db_QuoteKey($key);
    $value = db_QuoteValue($value);

    // cannot compare number to string 1000='1000a' so use lower
    $query = "SELECT $key FROM $table WHERE LOWER($key)=LOWER($value)$excludestr LIMIT 1";
    $db_query = mysqli_query($DB_CONNECTION, $query);
    SetDbQuery('db_IsUnique', $query);
    if ($db_query) {
        $row = mysqli_fetch_row($db_query);
        mysqli_free_result($db_query);
    }
    return empty($row[0]);
}

function db_GetUniqueID($table, $field) {
    $UID = md5(uniqid(rand(), true));
    while (!db_IsUnique($table, $field, $UID)) {
        $UID = md5(uniqid(rand(), true));
    }
    return $UID;
}


function db_GetNextValue($table, $key)
{
    global $DB_CONNECTION;
    if (empty($key)){
        return '';
    }
    $table = db_QuoteTables($table);
    $query    = "SELECT MAX($key) AS maxkey FROM $table";
    $db_query = mysqli_query($DB_CONNECTION, $query);
    SetDbQuery('db_GetNextValue', $query);
    if ($db_query) {
        $row      = mysqli_fetch_assoc($db_query);
        mysqli_free_result($db_query);
        $RESULT   = $row['maxkey'] + 1;
    } else {
        $RESULT = 0;
    }
    return $RESULT;
}

function db_GetNextDate($table, $key, $format, $inc)
{
    global $DB_CONNECTION;
    if (empty($key)) {
        return '';
    }

    $table = db_QuoteTables($table);

    $query    = "SELECT MAX($key) AS maxkey FROM $table";
    $db_query = mysqli_query($DB_CONNECTION, $query);
    SetDbQuery('db_GetNextDate', $query);
    if ($db_query) {
        $row      = mysqli_fetch_assoc($db_query);
        mysqli_free_result($db_query);
        $maxdate  = DateToDashes($row['maxkey']);

        if (strlen($maxdate)<8) {
            $maxdate .= '-01';
        }
        return date($format, strtotime("$maxdate +1 $inc"));
    } else return '';
}


function db_GetFieldValues($table, $key='', $where='', $case='', $joins='')
{
    global $DB_CONNECTION;

    if (is_array($table)) extract($table, EXTR_OVERWRITE);

    $RESULT = array();
    $case = strtoupper($case);

    if (!empty($where)) $where = "WHERE $where";

    if (empty($key)) {
        return $RESULT;
    }

    $table = db_QuoteTables($table);

    $query    = "SELECT $key FROM $table $joins $where GROUP BY $key ORDER BY $key";
    $db_query = mysqli_query($DB_CONNECTION, $query);

    SetDbQuery('db_GetFieldValues', $query);

    if (!$db_query) {
        return $RESULT;
    }

    while ($row = mysqli_fetch_array($db_query, MYSQLI_NUM)) {
        $value = $row[0];
        if (!empty($value)) {
            if ($case == 'U') $value = strtoupper($value);
            elseif ($case == 'L') $value = strtolower($value);
            $RESULT[] = $value;
        }
    }
    mysqli_free_result($db_query);
    return $RESULT;
}

function db_GetFieldValuesLC($table, $key='', $where='', $joins='')
{
    if (is_array($table)) extract($table, EXTR_OVERWRITE);
    return db_GetFieldValues($table, $key, $where, 'L', $joins);
}

function db_ViewRecord($table, $title_array='', $table_setup='', $keys='', $where='', $joins='')
{
    global $FieldValues, $DB_CONNECTION;

    if (is_array($table)) extract($table, EXTR_OVERWRITE);

    if (empty($keys) or empty($where)) return '';

    $table = db_QuoteTables($table);

    $query = "SELECT $keys FROM $table $joins WHERE $where LIMIT 1";
    $db_query = mysqli_query($DB_CONNECTION, $query);
    SetDbQuery('db_ViewRecord', $query);

    if (!$db_query) {
        return '';
    }

    $row = mysqli_fetch_assoc($db_query);
    mysqli_free_result($db_query);

    if (count($row)>0) {
        $RESULT = "<table $table_setup>\n";

        if (!empty($title_array)) {
            foreach ($title_array as $key => $value) {
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

function db_ViewRecordArray($record, $title_array, $table_setup='')
{
    global $FieldValues;

    if (!empty($record)) {
        $RESULT = "<table $table_setup>\n";
        foreach ($record as $key => $value) {
            $outkey = (empty($title_array[$key]))? $key : $title_array[$key];
            $outvalue = (empty($FieldValues[$key][$value]))? $value : $FieldValues[$key][$value];
            $RESULT .= "<tr><th align=\"right\">$outkey</th><td>$outvalue</td></tr>\n";
        }
        $RESULT .= "</table>\n";
    } else {
        $RESULT = '';
    }
    return $RESULT;
}


function db_GetRecord($table, $keys='', $where='', $joins='')
{
    global $DB_CONNECTION;

    if (is_array($table)) extract($table, EXTR_OVERWRITE);

    if (empty($keys) or empty($where)) {
        return '';
    }

    $table = db_QuoteTables($table);

    $query = "SELECT $keys FROM $table $joins WHERE $where LIMIT 1";
    $db_query = mysqli_query($DB_CONNECTION, $query);
    SetDbQuery('db_GetRecord', $query);

    if ($db_query) {
        $RESULT = mysqli_fetch_assoc($db_query);
        mysqli_free_result($db_query);
    } else {
        $RESULT = 0;
    }
    return $RESULT;
}

function db_UpdateRecord($table, $key_values='', $where='')
{
    global $DB_CONNECTION;

    if (is_array($table)) extract($table, EXTR_OVERWRITE);

    if (empty($key_values) or empty($where)) {
        return false;
    }
    $table = db_QuoteTables($table);
    $query = "UPDATE $table SET $key_values WHERE $where";
    $db_query = mysqli_query($DB_CONNECTION, $query);
    SetDbQuery('db_UpdateRecord', $query);
    if ($db_query) {
        $DB_LAST_INSERT_ID = mysqli_insert_id($DB_CONNECTION);
        return 1;
    }
    $DB_LAST_INSERT_ID = 0;
    return 0;
}

function db_DeleteRecord($table, $where='')
{
    global $DB_CONNECTION;

    if (is_array($table)) extract($table, EXTR_OVERWRITE);

    if (empty($where)) {
        return false;
    }

    $table = db_QuoteTables($table);
    $query = "DELETE FROM $table WHERE $where";
    $db_query = mysqli_query($DB_CONNECTION, $query);
    SetDbQuery('db_DeleteRecord', $query);
    if ($db_query) {
        return mysqli_affected_rows($DB_CONNECTION);
    }
    return 0;
}

function db_AddRecord($table, $keys='', $values='')
{
    global $DB_CONNECTION, $DB_LAST_INSERT_ID;

    if (is_array($table)) extract($table, EXTR_OVERWRITE);

    if (empty($keys) or empty($values)) {
        return false;
    }

    $table = db_QuoteTables($table);
    $query = "INSERT INTO $table ($keys) VALUES ($values)";
    $db_query = mysqli_query($DB_CONNECTION, $query);
    SetDbQuery('db_AddRecord', $query);

    if ($db_query) {
        $DB_LAST_INSERT_ID = mysqli_insert_id($DB_CONNECTION);
        return mysqli_affected_rows($DB_CONNECTION);
    } else {
        $DB_LAST_INSERT_ID = 0;
    }
    return 0;

}


/*deprecated*/ function db_GetSearchArray($table, $keys='', $where='', $order='', $start_list='', $list_size='', &$num_rows='', &$query='')
{
  global $DbLastQuery, $DB_NUMBER_ROWS;
  //deprecated - do not need &$query can get from $DbLastQuery  -- use db_GetArray()

  $RESULT   = db_GetArray($table, $keys, $where, $order, $start_list, $list_size);
  $num_rows = $DB_NUMBER_ROWS;
  $query    = $DbLastQuery;
  return $RESULT;
}

// ======================= MAJOR CHANGE REMOVED NUM ROWS: USE $DB_NUMBER_ROWS OR db_GetLastNumberRows()
//function db_GetArray($table, $keys='', $where='', $order='', $start_list='', $list_size='', &$num_rows, $joins='', $get_count=true)
function db_GetArray($table, $keys='', $where='', $order='', $start_list='', $list_size='', $joins='', $get_count=true)
{
    global $DB_CONNECTION, $DB_NUMBER_ROWS;

    if (is_array($table)) extract($table, EXTR_OVERWRITE);

    // joins need to be provide in full SQL
    if ($keys == '') {
        return '';
    }

    $start_list = intOnly($start_list);
    $list_size  = intOnly($list_size);

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
        $db_query = mysqli_query($DB_CONNECTION, $query);
        if ($db_query) {
            $row = mysqli_fetch_row($db_query);
            $DB_NUMBER_ROWS = $row[0];
            mysqli_free_result($db_query);
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
    $db_query = mysqli_query($DB_CONNECTION, $query);

    SetDbQuery('db_GetArray', $query);

    $RESULT = array();
    if ($db_query) {
        while ($row = mysqli_fetch_assoc($db_query)) {
            $RESULT[] = $row;
        }
        mysqli_free_result($db_query);
    }
    return $RESULT;
}


function db_GetArrayAll($table, $keys='*', $where='', $order='', $joins='')
{
    global $DB_CONNECTION;

    if (is_array($table)) extract($table, EXTR_OVERWRITE);

    if ($keys == '') {
        return '';
    }

    $order = (empty($order) or ($order == 'none'))? '' : "ORDER BY $order";

    if (!empty($where)) {
        $where = "WHERE $where";
    }

    $table = db_QuoteTables($table);

    $query = "SELECT $keys FROM $table $joins $where $order";
    $db_query = mysqli_query($DB_CONNECTION, $query);
    SetDbQuery('db_GetArrayAll', $query);
    $RESULT = array();
    if ($db_query) {
        while ($row = mysqli_fetch_assoc($db_query)) {
            $RESULT[] = $row;
        }
        mysqli_free_result($db_query);
    }
    return $RESULT;
}

function db_GetArrayAssoc($table, $reference_key='', $keys='*', $where='', $order='', $joins='', $limit='')
{
    global $DB_CONNECTION;

    if (is_array($table)) extract($table, EXTR_OVERWRITE);

    if(!empty($order)) $order = "ORDER BY $order";

    if (!empty($where)) {
        $where = "WHERE $where";
    }

    if (!empty($limit)) {
        $limit = " LIMIT $limit";
    }

    $table = db_QuoteTables($table);

    $quote_ref = db_QuoteKey($reference_key);

    $query = "SELECT $keys, $quote_ref AS 'THE_REFERENCE_KEY' FROM $table $joins $where $order $limit";
    $db_query = mysqli_query($DB_CONNECTION, $query);
    SetDbQuery("db_GetArrayAssoc ($reference_key)", $query);
    $RESULT = array();
    if ($db_query) {
        while ($row = mysqli_fetch_assoc($db_query)) {
            if (!empty($row['THE_REFERENCE_KEY']) or ($row['THE_REFERENCE_KEY'] == 0)) {
                $RESULT[$row['THE_REFERENCE_KEY']] = $row;
            }
        }
        mysqli_free_result($db_query);
    }
    return $RESULT;
}

// -------------------- function to return an Associative Array from two fields in a Table ---------------------------
function db_GetAssocArray($table, $key='', $value='', $where='', $key_case='', $joins='', $order='', $limit='')
{
    global $DB_CONNECTION;

    if (is_array($table)) extract($table, EXTR_OVERWRITE);

    $RESULT = array();
    $key_case = strtoupper($key_case);

    if (($key == '') or ($value == '')) {
        return $RESULT;
    }

    if (!empty($where)) {
        $where = "WHERE $where";
    }

    if (!empty($limit)) {
        $limit = " LIMIT $limit";
    }

    $key = db_QuoteKey($key);
    $value = db_QuoteKey($value);
    if(empty($order)) $order = $key;

    $table = db_QuoteTables($table);
    $query = "SELECT DISTINCT($key), $value FROM $table $joins $where ORDER BY $order$limit";
    $db_query = mysqli_query($DB_CONNECTION, $query);
    SetDbQuery('db_GetAssocArray', $query);
    if ($db_query) {
        while ($row = mysqli_fetch_array($db_query, MYSQLI_NUM)) {
            $idx = $row[0];
            if (!empty($idx) or ($idx == 0)) {
                if ($key_case == 'U') $idx = strtoupper($idx);
                elseif ($key_case == 'L') $idx = strtolower($idx);
                $RESULT[$idx] = $row[1];
            }
        }
        mysqli_free_result($db_query);
    }
    return $RESULT;
}


function db_Count($table, $where='', $joins='')
{
    global $DB_CONNECTION;

    if (is_array($table)) extract($table, EXTR_OVERWRITE);

    if (!empty($where)) $where = " WHERE $where";

    $table = db_QuoteTables($table);

    $query = "SELECT count(1) FROM $table $joins $where";

    $db_query = mysqli_query($DB_CONNECTION, $query);
    SetDbQuery('db_Count', $query);
    if ($db_query) {
        $row = mysqli_fetch_row($db_query);
        mysqli_free_result($db_query);
        $RESULT = $row[0];
    } else {
        $RESULT = 0;
    }
    return $RESULT;
}

function db_GetCount($table, $where='', $joins='')
{
    // depreciated: use db_Count
    if (is_array($table)) extract($table, EXTR_OVERWRITE);
    return db_Count($table, $where, $joins);
}

function db_GetFreq($table, $key='', $order=2, $order_direction='', $where='', $joins='', $limit='')
{
    global $DB_CONNECTION;

    if (is_array($table)) extract($table, EXTR_OVERWRITE);

    if ($key == '') {
        return '';
    }

    if (!empty($limit)) {
        $limit = " LIMIT $limit";
    }


    $order = ($order == 1)? 'ORDER BY 1 ' : 'ORDER BY 2 ';
    $order .= $order_direction;
    if (!empty($where)) {
        $where = "WHERE $where";
    }

    $table = db_QuoteTables($table);
    $key   = db_QuoteKey($key);

    $query = "SELECT $key, COUNT(1) FROM $table $joins $where GROUP BY 1 $order$limit";
    $db_query = mysqli_query($DB_CONNECTION, $query);
    SetDbQuery('db_GetFreq', $query);
    $RESULT = array();
    if (!$db_query) {
        return $RESULT;
    }

    while ($row = mysqli_fetch_row($db_query)) {
        $RESULT[$row[0]] = $row[1];
    }

    mysqli_free_result($db_query);
    return $RESULT;
}

function db_GetMax($table, $key='', $group='', $where='')
{
    global $DB_CONNECTION;

    if (is_array($table)) extract($table, EXTR_OVERWRITE);

    if ($group == '') {
        return '';
    }

    if (!empty($where)) {
        $where = "WHERE $where";
    }

    $table    = db_QuoteTables($table);
    $group    = db_QuoteKey($group);
    $key      = db_QuoteKey($key);

    $query = "SELECT $group, MAX($key) FROM $table $where GROUP BY $group";
    $db_query = mysqli_query($DB_CONNECTION, $query);
    SetDbQuery('db_GetMax', $query);
    $RESULT = array();

    if (!$db_query) {
        return $RESULT;
    }

    while ($row = mysqli_fetch_row($db_query)) {
        $RESULT[$row[0]] = $row[1];
    }

    mysqli_free_result($db_query);
    return $RESULT;
}

function db_MaxValue($table, $key='', $where='')
{
    global $DB_CONNECTION;

    if (is_array($table)) extract($table, EXTR_OVERWRITE);

    if (!empty($where)) {
        $where = "WHERE $where";
    }

    $table = db_QuoteTables($table);

    $key = db_QuoteKey($key);
    $query = "SELECT MAX($key) FROM $table $where";
    $db_query = mysqli_query($DB_CONNECTION, $query);
    SetDbQuery('db_MaxValue', $query);

    if (!$db_query) {
        return '';
    }

    $row = mysqli_fetch_row($db_query);
    mysqli_free_result($db_query);
    return $row[0];
}


function db_FieldArray($table, $key='', $where='', $order='')
{
    global $DB_CONNECTION;

    if (is_array($table)) extract($table, EXTR_OVERWRITE);

    if ($key == '') {
        return '';
    }

    if (!empty($order)) $order = "ORDER BY $order";
    if (!empty($where)) $where = "WHERE $where";

    $table = db_QuoteTables($table);

    $qkey   = db_QuoteKey($key);
    $query = "SELECT $qkey FROM $table $where $order";
    $db_query = mysqli_query($DB_CONNECTION, $query);
    SetDbQuery('db_FieldArray', $query);
    $RESULT = array();
    if (!$db_query) {
        return $RESULT;
    }
    while ($row = mysqli_fetch_row($db_query)) {
        $RESULT[] = $row[0];
    }
    mysqli_free_result($db_query);
    return $RESULT;
}

function db_GetValue($table, $key='', $where='', $joins='')
{
    global $DB_CONNECTION;

    if (is_array($table)) extract($table, EXTR_OVERWRITE);

    if (empty($key) or empty($where)) {
        return '';
    }

    $table = db_QuoteTables($table);
    $key = db_QuoteKey($key);

    $query = "SELECT $key FROM $table $joins WHERE $where LIMIT 1";
    $db_query = mysqli_query($DB_CONNECTION, $query);
    SetDbQuery('db_GetValue', $query);
    if (!$db_query) {
        return '';
    }

    $RESULT = ($row = mysqli_fetch_row($db_query))? $row[0] : '';
    mysqli_free_result($db_query);
    return $RESULT;
}


function db_IncValue($table, $key='', $where='')
{
    global $DB_CONNECTION;

    if (is_array($table)) extract($table, EXTR_OVERWRITE);

    if (empty($key) or empty($where)) {
        return '';
    }

    $table = db_QuoteTables($table);
    $key = db_QuoteKey($key);
    $query = "UPDATE $table SET $key=$key+1 WHERE $where LIMIT 1";
    $RESULT = mysqli_query($DB_CONNECTION, $query);
    SetDbQuery('db_IncValue', $query);
    return $RESULT;
}



function db_GetTables()
{
    global $DB_CONNECTION;
    $query = "SHOW TABLES";
    $db_query = mysqli_query($DB_CONNECTION, $query);
    SetDbQuery('db_GetTables', $query);
    $RESULT = array();
    if ($db_query) {
        while ($row = mysqli_fetch_row($db_query)) {
            $RESULT[] = $row[0];
        }
        mysqli_free_result($db_query);
    }
    NatCaseSort($RESULT);
    return $RESULT;
}

function db_TableExists($table)
{
    global $DB_CONNECTION;
    $query = "SHOW TABLES LIKE '$table'";
    $db_query = mysqli_query($DB_CONNECTION, $query);
    SetDbQuery('db_TableExists', $query);
    if ($db_query) {
        $row = mysqli_fetch_row($db_query);
        $table = $row[0];
        mysqli_free_result($db_query);
    } else {
        $table = '';
    }

    return $table==$table;
}


function db_TableFieldInfo($table)
{
    global $DB_CONNECTION;
    $table = db_QuoteTables($table);
    $query = "SHOW COLUMNS FROM $table";
    $db_query = mysqli_query($DB_CONNECTION, $query);
    SetDbQuery('db_TableFieldInfo', $query);
    $RESULT = array();
    if ($db_query) {
        while ($row = mysqli_fetch_assoc($db_query)) {
            $type = $row['Type'];
            $typeonly = strTo($type,'(');
            $size = TextBetween('(',')', $type);
            $row['Kind'] = $typeonly;
            $row['Size'] = $size;
            $RESULT[] = $row;
        }
        mysqli_free_result($db_query);
    }
    return $RESULT;
}

function db_TableFieldNames($table)
{
    $RESULT = array();
    $tableinfo = db_TableFieldInfo($table);
    if (count($tableinfo)>0) {
        foreach ($tableinfo as $row) {
            $RESULT[]=$row['Field'];
        }
    }
    return $RESULT;
}

function db_TableFieldTitleNames($table)
{
    $RESULT = array();
    $tableinfo = db_TableFieldInfo($table);
    if (count($tableinfo)>0) {
        foreach ($tableinfo as $row) {
            $value = NameToTitle($row['Field']);
            $RESULT[$row['Field']]= $value;
        }
    }
    return $RESULT;
}

function db_GetEnumArrays($table) {
    $RESULT = array();
    $table_info = db_TableFieldInfo($table);
    foreach ($table_info as $row) {
        if ($row['Kind'] == 'enum') {
            $size = $row['Size'];
            $RESULT[$row['Field']] = TextBetweenArray("'","'", $row['Size']);
        }
    }
    return $RESULT;
}

function db_CopyRow($table, $exclude_list='', $change_list='', $where='')
{
    // $exculude_list is a comma delimited list
    // $change_list is a '|' delimited list  >> "var1=My Value|var2=My Other Value"  --- do not use quotes for values
    // $change_list can assign values from another field using '=='  >> "var1==myId"

    global $DB_CONNECTION, $DB_LAST_INSERT_ID;

    if (is_array($table)) extract($table, EXTR_OVERWRITE);

    $row = db_GetRecord($table, '*', $where);
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

        db_AddRecord($table, db_Keys($row), db_Values($row));
        $DB_LAST_INSERT_ID = mysqli_insert_id($DB_CONNECTION);
        return $DB_LAST_INSERT_ID;
    } else return false;
}


function db_Query($QUERY)
{
    global $DB_CONNECTION;
    $RESULT = @mysqli_query($DB_CONNECTION, $QUERY);
    SetDbQuery('db_Query', $QUERY);
    return $RESULT;
}

function db_FetchAssoc($db_query)
{
    return mysqli_fetch_assoc($db_query);
}

function db_FetchRow($db_query)
{
    return mysqli_fetch_row($db_query);
}

function db_QueryToArray($QUERY)
{
    global $DB_CONNECTION;
    $RESULT = array();
    $db_query = mysqli_query($DB_CONNECTION, $QUERY);
    SetDbQuery('db_QueryToArray', $QUERY);
    if ($db_query) {
        while ($row = mysqli_fetch_assoc($db_query)) {
            $RESULT[] = $row;
        }
        mysqli_free_result($db_query);
    }
    return $RESULT;
}


function db_GetCustomRecord($QUERY)
{
    global $DB_CONNECTION;
    SetDbQuery('db_GetCustomRecord', $QUERY);
    $db_query = mysqli_query($DB_CONNECTION, $QUERY);
    if ($db_query) {
        return mysqli_fetch_assoc($db_query);
    } else {
        return 0;
    }
}

function db_StartTransaction()
{
    global $DB_CONNECTION;
    SetDbQuery('db_StartTransaction', "-------- START TRANSACTION --------");
    return mysqli_autocommit($DB_CONNECTION, FALSE);
}

function db_TransactionCommit()
{
    global $DB_CONNECTION;
    SetDbQuery('db_TransactionCommit', "-------- COMMIT --------");
    $RESULT = mysqli_commit($DB_CONNECTION);
    mysqli_autocommit($DB_CONNECTION, TRUE);
    return $RESULT;
}

function db_Rollback()
{
    global $DB_CONNECTION;
    SetDbQuery('db_Rollback', "-------- ROLLBACK --------");

    $RESULT = mysqli_rollback($DB_CONNECTION);
    mysqli_autocommit($DB_CONNECTION, TRUE);
    return $RESULT;
}

function db_Error()
{
    global $DB_CONNECTION;
    return mysqli_error($DB_CONNECTION);
}

function db_Close($connection='')
{
    global $DB_CONNECTION;
    if ($connection) {
        mysqli_close($connection);
    } else {
        mysqli_close($DB_CONNECTION);
    }
}