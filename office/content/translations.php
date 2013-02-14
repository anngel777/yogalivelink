<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: translations.php
    Description: Perform translation swaps
==================================================================================== */

#DECLARE CLASSES AND INSTANTIATE THEM
include_once "$ROOT/global/general_helper.php";
include "$ROOT/classes/class.Translations.php";
include "$ROOT/classes/class.Tabs.php";
$Tab = new Tabs('menu');
$Translations = new Translation_Translations;

#================================================
#SETUP THE LANGUAGE VARS
#================================================
# GET THE LANGUAGE
$Translations->LANGUAGE = (Get('lang')) ? Get('lang') : $Translations->translations_DEFAULT_LANGUAGE;

# RESET LANGAUGE ARRAY (if requested)
if (Get('reset') == 'true') { unset($_SESSION['LANGUAGE']); };

#LOAD LANGUAGE INTO SESSION ARRAY
if (!isset($_SESSION['LANGUAGE'][$Translations->LANGUAGE]))
{
	$_SESSION['LANGUAGE'][$Translations->LANGUAGE] = $Translations->translations_LoadTranslations($Translations->LANGUAGE);
}
#================================================

/*
foreach ($_POST as $var => $value)
	{
		$TRANS_WORDS[$var] = $value;
		echo "<br/>$var -> $value";
	}
	//exit();
*/



if (HaveSubmit('TRANSLATION_LANGUAGE_SUBMIT_ADD'))
{
	#===EDITING/ADDING/DELETING A LANGUAGE NAME===#

	#MAKE HIGH-ACCESS CONNECTION TO DATABASE
	mysql_close();
	include "$ROOT/global/z_intelserver_db_info.php";
	SetUpDbConnection($DB_INFO);

	#LOOP THROUGH FORM VARIABLES AND STORE IN NEW ARRAY	
	foreach ($_POST as $var => $value)
	{
		$TRANS_WORDS[$var] = $value;
		unset($_POST[$var]); #UNSET THE FORM VARIABLES
	}

	$errors = $Translations->translations_ProcessLanguages($TRANS_WORDS);
}




if (HaveSubmit('TRANSLATION_WORD_SUBMIT_ADD'))
{
	#===EDITING/ADDING/DELETING A WORD===#

	#MAKE HIGH-ACCESS CONNECTION TO DATABASE
	mysql_close();
	include "$ROOT/global/z_intelserver_db_info.php";
	SetUpDbConnection($DB_INFO);

	#GET THE LANGUAGE BEING SUBMITTED
	$SUB_LANG = $_POST['FORM_StoreLanguage'];
	unset($_POST['FORM_StoreLanguage']);
	
	#LOOP THROUGH FORM VARIABLES AND STORE IN NEW ARRAY	
	foreach ($_POST as $var => $value)
	{
		$TRANS_WORDS[$var] = $value;
		unset($_POST[$var]); #UNSET THE FORM VARIABLES
	}

	$errors = $Translations->translations_ProcessWords ($TRANS_WORDS, $SUB_LANG);
}

//if (HaveSubmit('TRANSLATION_LANG_SELECT_SUBMIT_ADD'))
if (Post('tempLanguageSelect'))
{
	//echo 'here';
	$SUB_TEMP_LANG = $_POST['tempLanguageSelect'];
}




$TEMP_LANG = (isset($SUB_TEMP_LANG)) ? $SUB_TEMP_LANG : '';
$OUTPUT = $Translations->translations_DisplayTranslationsList($TEMP_LANG);
$Tab->AddTab('Translations', $OUTPUT);

$OUTPUT2 = $Translations->translations_DisplayLanguages();
$Tab->AddTab('Languages', $OUTPUT2);

$OUTPUT3 = $Translations->translations_DisplayKeyTable();
$Tab->AddTab('KEY', $OUTPUT3);


$Tab->OutputTabs();



//WriteDBQuery();
?>