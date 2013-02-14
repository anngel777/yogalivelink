<?php		
//require_once ('../../mysql_connect_afterhoursindustrial.php');
$path = dirname(dirname(__FILE__));
require_once ($path.'../../mysql_connect_afterhoursutilities.php');

// ------------------ variables for showing Database Queries --------------
$DBQUERY               = "on";
$DbLastQuery           = '';

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
        //$query = str_ireplace($word,"$span1$word$span2",$query);
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
    global $DBQUERY;
    $DbQueryTableOptions   = 'id="db_queries" align="center" style="background-color:#888; border:1px solid #000; margin:10px auto;"';
    $DbQueryTableOptionsTH = 'align="center" style="background-color:#aaa; color:#fff;"';
    $DbQueryTableOptionsTD = 'align="left" style="background-color:#ff7; color:#000; font-size:0.8em; padding:1em 1em 0px 0px;"';

    $RESULT = '';
    if (!empty($DBQUERY)) {
        $RESULT .= "\n\n<table $DbQueryTableOptions>\n";
        $RESULT .= '<tr><th '.$DbQueryTableOptionsTH.'>DB Queries (<a href="#" onclick="getElementById(\'db_queries\').style.display=\'none\'; return false;">Hide</a>)</th></tr>';
        $RESULT .= "\n<tr><td $DbQueryTableOptionsTD>\n<ul style=\"margin:0px 2em;\">$DBQUERY</ul>\n</td></tr>\n";
        $RESULT .= "</table>\n";
    }
    return $RESULT;
}

//-------------------------------------------------------------------------------


function selectAllTable($TABLE, $WHERE, $ORDER)
{
	$queryAllTable = "SELECT * FROM " . $TABLE . " WHERE " . $WHERE . " ORDER BY " . $ORDER;
    SetDbQuery('selectAllTable',$queryAllTable);

	$resultAllTable = @mysql_query ($queryAllTable);
	return $resultAllTable;
}

function selectAllRow($TABLE, $WHERE)
{
	$queryAllRow = "SELECT * FROM " . $TABLE . " WHERE " . $WHERE;
    SetDbQuery('selectAllRow',$queryAllRow);

	$resultAllRow = @mysql_query ($queryAllRow);
	$resultOneRow = mysql_fetch_assoc ($resultAllRow);
	return $resultOneRow;
}

function updateRow($TABLE, $SET, $WHERE)
{
	$queryAllRow = "UPDATE " . $TABLE . " SET " . $SET . " WHERE " . $WHERE;
    SetDbQuery('updateRow',$queryAllRow);

	$resultAllRow = @mysql_query ($queryAllRow);
	return $resultAllRow;
}

function selectAll($v1,$v2,$v3,$v4,$v5,$v6,$v7,$v8,$v9,$v10,$v11,$v12,$v13,$v14,$v15,$v16,$v17,$v18,$v19,$v20)
{
	$query_leads = "SELECT * FROM " . DATA_TABLE . " WHERE vendor='$vendor[0]' AND location='$CURRENT_LOCATION'";
	//echo $fname . " Refsnes" . $punctuation . "<br />";
}

function insertRow($COLUMNS, $VALUES, $TABLE)
{
	$queryInsertRow = "
		INSERT INTO `" . $TABLE . "` (" . $COLUMNS . " )
		VALUES (" . $VALUES . ");
		";
    SetDbQuery('insertRow',$queryInsertRow);

	$resultInsertRow = @mysql_query ($queryInsertRow);
	return $resultInsertRow;

	//INSERT INTO `" . $TABLE . "` ( `id` " . $COLUMNS . " )
}

function insertRowArray ($ARRAY, $TABLE, $ITERATIONS=1)
{
	for ($p=1; $p<=$ITERATIONS; $p++)
	{
		$count 		= count($ARRAY);
		$COLUMNS 	= "";
		$VALUES 	= "";
		$firstVal	= true;

		for ($i=0; $i<$count; $i++)
		{
			if ($ARRAY[$i][0] == $TABLE && $ARRAY[$i][3] == $p)
			{
				if ($firstVal == true)
				{
					$COLUMNS 	.= "`" . $ARRAY[$i][1] . "`";
					$VALUES 	.= "'" . $ARRAY[$i][2] . "'";
					$firstVal	= false;
				} else {
					$COLUMNS 	.= ", `" . $ARRAY[$i][1] . "`";
					$VALUES 	.= ", '" . $ARRAY[$i][2] . "'";
				}
			}
		}

		if ($COLUMNS != NULL && $VALUES != NULL)
		{
			$queryInsertRow = "INSERT INTO `" . $TABLE . "` ( " . $COLUMNS . " ) VALUES (" . $VALUES . ");";
            SetDbQuery('insertRowArray',$queryInsertRow);
			$resultInsertRow = mysql_query ($queryInsertRow);
			//return $resultInsertRow;
		}

	}
	return $resultInsertRow;
}

function selectCustomQuery($QUERY)
{
	$queryCustom = $QUERY;
    SetDbQuery('selectCustomQuery',$queryCustom);
	$resultCustom = @mysql_query($queryCustom);
	return $resultCustom;
}

function DeleteRow($TABLE, $WHERE)
{
	$queryDeleteRow = "DELETE FROM " . $TABLE . " WHERE " . $WHERE;
    SetDbQuery('DeleteRow',$queryDeleteRow);
	$resultDeleteRow = @mysql_query($queryDeleteRow);
	return $resultDeleteRow;
}

function CountRows ($TABLE, $WHERE)
{
	$query = "SELECT * FROM " . $TABLE . " WHERE " . $WHERE;
    SetDbQuery('CountRows',$query);
	$result = @mysql_query ($query);
	$num_rows = mysql_num_rows($result);
	return $num_rows;
}

function transactionStart()
{
	$query = "SET AUTOCOMMIT=0;";
	selectCustomQuery($query);

   	$query = "BEGIN;";
	selectCustomQuery($query);
}

function transactionCommit()
{
	$query = "COMMIT";
	selectCustomQuery($query);
}

function transactionAbort()
{

}

// ================================== MVP Added Functions ======================================

function queryToTable($QUERY)
{
    $RESULT = array();
    if ($QUERY) {
        while ($row = mysql_fetch_array($QUERY, MYSQL_ASSOC)) $RESULT[] = $row;
    }
    return $RESULT;
}

function db_GetFieldValues()
{
    // returns array of unique field values
    // Inputs:  $db_table,  $key, [where]

    $numargs  = func_num_args();
    $db_table = func_get_arg(0);
    $key      = func_get_arg(1);
    $where = ($numargs > 2)? ' WHERE '.func_get_arg(2) : '';

    $RESULT = array();

    if (empty($key)) {
        return $RESULT;
    }

    $query    = "SELECT $key FROM `$db_table`$where GROUP BY $key ORDER BY $key";
    SetDbQuery('db_GetFieldValues',$query);

    $db_query = mysql_query($query);

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

// ----------------------- get a record into an array -----------------------
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

//---------------- function to return a single value from a field -------------------
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

function addVariableToQueryInsertList (&$COLLIST, &$VALLIST, $VARNAME, $VARVALUE, $IGNORELIST='')
{
	$haystack 	= "|$IGNORELIST|";
	$needle 	= "|$VARNAME|";
	if (strpos($haystack, $needle) === false)
	{
		if(!empty($COLLIST))
		{
			$COLLIST .= ', ';
			$VALLIST .= ', ';
		}
		
		$COLLIST .= "`" . $VARNAME . "`";
		$VALLIST .= "'" . $VARVALUE . "'";
	}
}

function addVariableToQueryUpdateList (&$SETLIST, $VARNAME, $VARVALUE, $IGNORELIST='')
{
	$haystack 	= "|$IGNORELIST|";
	$needle 	= "|$VARNAME|";
	if (strpos($haystack, $needle) === false)
	{
		if(!empty($SETLIST)) $SETLIST .= ', ';
		$SETLIST .= $VARNAME . '="' . $VARVALUE . '"';
	}
}
?>