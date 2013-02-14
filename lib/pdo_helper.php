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

//$DBQUERY               = '';
//if (!isset($DB_WANT_QUERY)) $DB_WANT_QUERY = true;
//$DB_MAX_DBQUERY_LENGTH = 1000000; //strlength;
//$DB_START_TIME         = microtime(true);
//$DbQueryTableOptions   = 'align="center" style="background-color:#888; border:1px solid #000; margin-top:10px;"';
//$DbQueryTableOptionsTH = 'style="text-aling:center; background-color:#aaa; color:#fff;"';
//$DbQueryTableOptionsTD = 'style="text-align:left; background-color:#ff7; color:#000; font-size:0.8em; padding:1em 1em 0px 0px;"';
//$FieldValues           = array();
//$DB_LAST_INSERT_ID     = 0;


$DB_HELPER_NAME = 'pdo_helper';
$DB_SHOW_TRACE  = false;
$DB_WANT_QUERY  = true;
$DbLastQuery    = '';
$DB_NUMBER_ROWS = 0;

/* ====== global variables used in older helpers =======
    $DB_WANT_QUERY, $DbLastQuery, $DB_SHOW_TRACE;
    For $DbLastQuery use: db_GetLastQuery()
    For $DB_WANT_QUERY, use db_SetWantQuery($value)
    For $DB_SHOW_TRACE, use db_SetTrace($value)
===================================================== */



if (!isset($SQL)) {
    include_once "$ROOT/classes/Lib/Pdo.php";
    include_once "$ROOT/classes/Lib/Singleton.php";
    $SQL = Lib_Singleton::GetInstance('Lib_Pdo');
}


if (!empty($DB_INFO) and !isset($SQL->Pdo)) {
    SetUpDbConnection($DB_INFO);
}

//------ call this function at start-up
function SetUpDbConnection($DB_INFO)
{
    global $SQL;
    $SQL->ConnectMySql($DB_INFO);
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

function db_GetLastQuery()
{
    global $SQL;
    return $SQL->GetLastQuery();
}

function db_SetWantQuery($value)
{
    global $SQL;
    return $SQL->SetWantQuery($value);
}

function db_SetTrace($value)
{
    global $SQL;
    return $SQL->SetTrace($value);
}

function db_GetAffectedRows()
{
    global $SQL;
    return $SQL->Affected_Rows;
} 

// ------------------ Database Query Tracking --------------
function SetDbQuery($function, $query)
{
    global $SQL;
    $SQL->SetDbQuery($function, $query);
}

function WriteDbQuery()
{
    global $SQL;
    $SQL->WriteDbQuery();
}

function WriteDbQueryText()
{
    global $SQL;
    return $SQL->WriteDbQueryText();
}

function db_QuoteValue($value)
{
    global $SQL;
    return $SQL->QuoteValue($value);
}

function db_QuoteValueC($value)
{
    global $SQL;
    return $SQL->QuoteValueC($value);
}

function db_QuoteKey($key)
{
    global $SQL;
    return $SQL->QuoteKey($key);
}

function db_QuoteTables($tables)
{
    global $SQL;
    return $SQL->QuoteTables($tables);
}


function db_KeyValues($array)
{
    global $SQL;
    return $SQL->KeyValues($array);
}

// takes keys from an array or associative array and returns a key list.
function db_Keys($array, $assoc=true)
{
    global $SQL;
    return $SQL->Keys($array, $assoc);
}

function db_Values($array)
{   // this function works on standard and associative arrays
    global $SQL;
    return $SQL->Values($array);
}

function ArrayToPost($arraymap, $array)
{
    foreach ($arraymap as $varname => $postname) {
        $_POST[$postname] = empty($array[$varname])? '' : $array[$varname];
    }
}

function db_output_table($SearchArray, $UserInfo='', $table_setup='', $EditTitle='', $EditLinks='', $id='')
{
    global $SQL;
    return $SQL->OutputTable($SearchArray, $UserInfo, $table_setup, $EditTitle, $EditLinks, $id);
}

function db_GetLastInsertId()
{
    global $SQL;
    return $SQL->GetLastInsertId();
}

function db_GetLastNumberRows()
{
    global $SQL, $DB_NUMBER_ROWS;
    $DB_NUMBER_ROWS = $SQL->GetLastNumberRows();
    return $DB_NUMBER_ROWS;
}

function db_IsUnique($table, $key='', $value='', $exclude='')
{
    global $SQL;
    return $SQL->IsUnique($table, $key, $value, $exclude);
}

function db_GetUniqueID($table, $field) {
    global $SQL;
    return $SQL->GetUniqueID($table, $field);
}


function db_GetNextValue($table, $key)
{
    global $SQL;
    return $SQL->GetNextValue($table, $key);
}

function db_GetNextDate($table, $key, $format, $inc)
{
    global $SQL;
    return $SQL->GetNextDate($table, $key, $format, $inc);
}


function db_GetFieldValues($table, $key='', $where='', $case='', $joins='')
{
    global $SQL;
    return $SQL->GetFieldValues($table, $key, $where, $case, $joins);
}

function db_GetFieldValuesLC($table, $key='', $where='', $joins='')
{
    global $SQL;
    return $SQL->GetFieldValues($table, $key, $where, 'L', $joins);
}

function db_ViewRecord($table, $title_array='', $table_setup='', $keys='', $where='', $joins='')
{
    global $SQL;
    return $SQL->ViewRecord($table, $title_array, $table_setup, $keys, $where, $joins);
}

function db_ViewRecordArray($record, $title_array, $table_setup='')
{
    global $SQL;
    return $SQL->ViewRecordArray($record, $title_array, $table_setup);
}


function db_GetRecord($table, $keys='', $where='', $joins='')
{
    global $SQL;
    return $SQL->GetRecord($table, $keys, $where, $joins);
}

function db_UpdateRecord($table, $key_values='', $where='')
{
    global $SQL;
    return $SQL->UpdateRecord($table, $key_values, $where);
}

function db_DeleteRecord($table, $where='')
{
    global $SQL;
    return $SQL->DeleteRecord($table, $where);
}

function db_AddRecord($table, $keys='', $values='')
{
    global $SQL;
    return $SQL->AddRecord($table, $keys, $values);
}


function db_GetArray($table, $keys='', $where='', $order='', $start_list='', $list_size='', $joins='', $get_count=true)
{
    global $SQL, $DB_NUMBER_ROWS;
    $RESULT = $SQL->GetArray($table, $keys, $where, $order, $start_list, $list_size, $joins, $get_count);
    $DB_NUMBER_ROWS = $SQL->GetLastNumberRows();
    return $RESULT;
}



function db_GetArrayAll($table, $keys='*', $where='', $order='', $joins='')
{
    global $SQL;
    return $SQL->GetArrayAll($table, $keys, $where, $order, $joins);
}

function db_GetArrayAssoc($table, $reference_key='', $keys='*', $where='', $order='', $joins='', $limit='')
{
    global $SQL;
    return $SQL->GetArrayAssoc($table, $reference_key, $keys, $where, $order, $joins, $limit);
}

// -------------------- function to return an Associative Array from two fields in a Table ---------------------------
function db_GetAssocArray($table, $key='', $value='', $where='', $key_case='', $joins='', $order='', $limit='')
{
    global $SQL;
    return $SQL->GetAssocArray($table, $key, $value, $where, $key_case, $joins, $order, $limit);
}


function db_Count($table, $where='', $joins='')
{
    global $SQL;
    return $SQL->Count($table, $where, $joins);
}

function db_GetFreq($table, $key='', $order=2, $order_direction='', $where='', $joins='', $limit='')
{
    global $SQL;
    return $SQL->GetFreq($table, $key, $order, $order_direction, $where, $joins, $limit);
}

function db_GetMax($table, $key='', $group='', $where='')
{
    global $SQL;
    return $SQL->GetMax($table, $key, $group, $where);
}

function db_MaxValue($table, $key='', $where='')
{
    global $SQL;
    return $SQL->MaxValue($table, $key, $where);
}


function db_FieldArray($table, $key='', $where='', $order='')
{
    global $SQL;
    return $SQL->FieldArray($table, $key, $where, $order);
}

function db_GetValue($table, $key='', $where='', $joins='')
{
    global $SQL;
    return $SQL->GetValue($table, $key, $where, $joins);
}


function db_IncValue($table, $key='', $where='')
{
    global $SQL;
    return $SQL->IncValue($table, $key, $where);
}


function db_GetTables()
{
    global $SQL;
    return $SQL->GetTables();
}

function db_TableExists($table)
{
    global $SQL;
    return $SQL->TableExists($table);
}


function db_TableFieldInfo($table)
{
    global $SQL;
    return $SQL->TableFieldInfo($table);
}

function db_TableFieldNames($table)
{
    global $SQL;
    return $SQL->TableFieldNames($table);
}

function db_TableFieldTitleNames($table)
{
    global $SQL;
    return $SQL->TableFieldTitleNames($table);
}

function db_GetEnumArrays($table)
{
    global $SQL;
    return $SQL->GetEnumArrays($table);
}

function db_CopyRow($table, $exclude_list='', $change_list='', $where='')
{
    global $SQL;
    return $SQL->CopyRow($table, $exclude_list, $change_list, $where);
}


function db_Query($QUERY)
{
    global $SQL;
    return $SQL->Pdo->query($QUERY);
}

function db_Exec($QUERY)
{
    global $SQL;
    return $SQL->Pdo->exec($QUERY);
}

function db_FetchAssoc($db_query)
{
    return $db_query->fetch(PDO::FETCH_ASSOC);
}

function db_FetchRow($db_query)
{
        return $db_query->fetch(PDO::FETCH_NUM);
}

function db_QueryToArray($QUERY)
{
    global $SQL;
    return $SQL->QueryToArray($QUERY);
}


function db_GetCustomRecord($QUERY)
{
    global $SQL;
    return $SQL->GetCustomRecord($QUERY);
}

function db_StartTransaction()
{
    global $SQL;
    return $SQL->StartTransaction();
}

function db_TransactionCommit()
{
    global $SQL;
    return $SQL->TransactionCommit();
}

function db_Rollback()
{
    global $SQL;
    return $SQL->Rollback();
}

function db_Error()
{
    global $SQL;
    return $SQL->Error();
}

function db_Close($connection='')
{
    global $SQL;
    return $SQL->Close();
}