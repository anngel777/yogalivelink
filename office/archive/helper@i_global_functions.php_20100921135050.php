<?php	

//====================================================================================================
//This function locks out data boxes from being edited if the user doesn't have adequate access level
//====================================================================================================
define('DATA_LOCKED', 		" class=\"dataLocked\" readonly=\"readonly\" ");
define('DATA_UNLOCKED', 	" class=\"dataUnlocked\" ");
define('DATA_LOCKED_NB', 	" class=\"dataLocked_noBorder\" readonly=\"readonly\" ");
define('DATA_UNLOCKED_NB', 	" class=\"dataUnlocked_noBorder\" ");

function AccessRequired($level, $type, $object, $bypass_admin=false)
{
	global $Access_Level;

if (isset($_SESSION['S_O_access_level']) && $_SESSION['S_O_access_level'] <= 2 && $bypass_admin == false)
{ //THIS IS AN ADMINISTRATOR OR DIRECTOR - SO THEY HAVE FULL ACCESS REGARDLESS OF ANY OTHER SETTINGS
	$passed = true;
} else {
	if (isset($Access_Level) && $type != 'mnu')
	{
		if ($Access_Level <= $level)
		{
			$passed = true;
		} else {
			$passed = false;
		}
	} else {
		if (isset($_SESSION['S_O_access_level']) && $_SESSION['S_O_access_level'] <= $level	) 
		{ 
			$passed = true;
		} else {
			$passed = false;
		}
	}
}
	
	if ($passed == true)
	{
		switch ($type) {
			case "btn":
				echo DATA_UNLOCKED;
			    break;
			case "img":
				echo DATA_UNLOCKED_NB;
			    break;
			case "sel":
				echo DATA_UNLOCKED;
			    break;
			case "chk":
				echo DATA_UNLOCKED;
			    break;
			case "span":
				echo DATA_UNLOCKED_NB;
			    break;
			case "mnu":
				echo DATA_UNLOCKED_NB;
			    break;
			case "input":
				echo DATA_UNLOCKED;
			    break;
			default:
				echo DATA_UNLOCKED;
				break;
		}
	} else { 
		switch ($type) {
			case "btn":
				echo DATA_LOCKED;
			    echo ' disabled="disabled" ';
			    break;
			case "img":
				echo DATA_LOCKED;
				echo ' style="visibility:hidden" ';
			    break;
			case "sel":
				echo DATA_LOCKED;
			    echo ' disabled="disabled" ';
			    break;
			case "chk":
				echo DATA_LOCKED;
			    echo ' onclick="reverseCheck(\''.$object.'\')" ';
			    break;
			case "span":
				echo DATA_LOCKED_NB;
			    echo ' style="visibility:hidden; display:none" ';
			    break;
			case "mnu":
				echo DATA_LOCKED_NB;
			    echo ' style="display:none" ';
			    break;
			case "input":
				echo ' disabled="disabled" ';
			    break;
			default:
				echo DATA_LOCKED;
				break;
		}	
	}
}
//====================================================================================================



//====================================================================================================
//These functions handle the ref_back_array
//====================================================================================================
function RefDeclare()
{
	$REF_BACK_ARRAY = array();

	//Store array in session variables
	$_SESSION['REF_BACK_ARRAY'] = $REF_BACK_ARRAY;
}

function RefAddItem($REFID, $REFVAR)
{
	$returnedRow = RefSearchItem($REFID);
	
	if ($returnedRow != NULL)
	{
		//WIPE OUT EVERYTHING ABOVE THIS ROW AND THE ROW ITSELF
		RefDeleteAbove ($returnedRow-1);
	}
	
	//ADD ITEM TO ARRAY
	$next = count($_SESSION['REF_BACK_ARRAY']);
	$_SESSION['REF_BACK_ARRAY'][$next][0] = $REFID;
	$_SESSION['REF_BACK_ARRAY'][$next][1] = $REFVAR;
	
	RefDump();
}

function RefGetItem()
{
	$refId = count($_SESSION['REF_BACK_ARRAY']);
	
	$Rurl = $_SESSION['REF_BACK_ARRAY'][$refId-2][0];
	$Rvar = $_SESSION['REF_BACK_ARRAY'][$refId-2][1];
	
	return $Rurl . "?" . $Rvar;
}

function RefGetCurrentItemVars()
{
	$refId = count($_SESSION['REF_BACK_ARRAY']);
	$Rvar = $_SESSION['REF_BACK_ARRAY'][$refId-1][1];
	
	return $Rvar;
}

function RefGetCurrentItemUrl()
{
	$refId = count($_SESSION['REF_BACK_ARRAY']);
	$Rurl = $_SESSION['REF_BACK_ARRAY'][$refId-1][0];
	return $Rurl;
}


function RefGetCount()
{
	$count = count($_SESSION['REF_BACK_ARRAY']);
	return $count;
}


function RefSearchItem($REFID)
{
	$RefIdFoundRow = NULL;
	$checkId = $REFID;
	
	$LastRow = count($_SESSION['REF_BACK_ARRAY']) - 1;
	for ($t=$LastRow; $t>=0; $t--)
	{
		$searchId = $_SESSION['REF_BACK_ARRAY'][$t][0];
		if ($checkId == $searchId)
		{
			$RefIdFoundRow = $t;
			$t = 0;
		}
	}
	
	return $RefIdFoundRow;
}


function RefDeleteAbove($ROW)
{
	$LastRow = count($_SESSION['REF_BACK_ARRAY']);
	for ($t=$LastRow; $t>$ROW+1; $t--)
	{
		array_pop ($_SESSION['REF_BACK_ARRAY']);
	}
}

function RefDump($OVERRIDE=0)
{
	if (SHOW_REFBACK == "true" || $OVERRIDE == 1)
	{
		echo "<BR>=================================================<BR>";
		$count = count($_SESSION['REF_BACK_ARRAY']);
		for ($t=0; $t <= $count; $t++)
		{
			echo "[$t][0] " . $_SESSION['REF_BACK_ARRAY'][$t][0] . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			echo "[$t][1] " . $_SESSION['REF_BACK_ARRAY'][$t][1] . "<BR>";
		}
		echo "=================================================<BR>";
	}
}


function ShowEcho ($string)
{
	//====================================================================================================
	//FUNCTION DISPLAYS ERROR MESSAGES EMBEDDED IN CODE AND DATABASE CALLS
	//====================================================================================================

	if (SHOW_ERROR == "true")
	{
		echo "<font color='red'>";
		echo "<br />" . $string;
		echo "</font>";
	}
}
//====================================================================================================



//====================================================================================================
//Other functions
//====================================================================================================



function createRandomPassword() 
{
	$chars = "abcdefghijkmnopqrstuvwxyz023456789";
    srand((double)microtime()*1000000);
	$i = 0;
	$pass = '' ;
	while ($i <= 7) {
		$num = rand() % 33;
		$tmp = substr($chars, $num, 1);
		$pass = $pass . $tmp;
		$i++;
	}
	return $pass;
}

function ArrayToPost($arraymap)
{
	if ($arraymap) {
	    foreach ($arraymap as $varname => $varvalue) {
	        $_POST[$varname] = $varvalue;
	    }
	}
}


function CreateDropDownSelect($FIELDNAME,$SELECTNAME,$CLASS,$VALUE,$UseNoSelect=true, $ADDITIONALFUNCTION=null)
{
	$noSelect = "-- SELECT --";
    $RESULT = "<select id=\"$SELECTNAME\" class=\"$CLASS\" name=\"$SELECTNAME\" $ADDITIONALFUNCTION>\n";
    if ($UseNoSelect) $RESULT .= '<option value="">'.$noSelect."</option>\n";

	$QUERY = selectAllTable('dropdownvalues', 'fieldName=\''.$FIELDNAME.'\' AND active=1', 'fieldName,sortOrder ASC');
	//WriteDbQuery();
	
	while ($ROW = mysql_fetch_array($QUERY, MYSQL_ASSOC)) 
	{
    	$optionName 	= $ROW['optionName'];
    	$optionValue 	= $ROW['optionValue'];

        $select = ($optionValue == $VALUE)? ' selected="selected"' : '';
		$RESULT .= "    <option value=\"$optionValue\"$select>$optionName</option>\n";
    
	}

	$RESULT .= "</select>\n";
	RETURN $RESULT;
}

?>