<?PHP
/*-------------------PLACE CHANGE NOTES HERE--------------------

2009-05-03: MVP-> Updated Code,
                  added SetLanguage in Construct,
                  renamed variables: Default_Language, Word_Array_Language
2009-07-26: MVP-> Added check for Translation, if empty gives missing message.

-------------------------------------------------------*/

class Translation_Translations extends BaseClass
{
    public $Table                       = 'translations';
    public $Default_Language            = 'english';
    public $LANGUAGE                    = '';
    public $Language_Code               = 'en-us';
    protected $Word_Array               = '';
    protected $Word_Array_Language      = '';

    public function  __construct($table = 'translations')
    {
        //parent::__construct();
        global $FORM_VAR, $FORM_MONTHS;
        
        $this->Table = $table;

/*        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Description' => 'Create and manage language translations',
            'Created'     => '2008-12-26',
            'Updated'     => '2008-12-26'
        );
*/


/*
        $FORM_VAR['default_country']            = 'US';
        $FORM_VAR['new_select_text']            = '-- [T~FRM_OTHER_LC] --';
        $FORM_VAR['start_select']               = '-- [T~TXT_REGISTRATION_0029] --';
        $FORM_VAR['new_select_text']            = '-- [T~FRM_OTHER_LC] --';
        $FORM_VAR['submit_click_text']          = '[T~FRM_PROCESSING]';
        $FORM_VAR['referrer_error']             = '[T~FRM_INVALID_REFERRER]';
        $FORM_VAR['illegal_characters']         = '[T~FRM_ILLEGAL_CHARACTERS]';
        $FORM_VAR['is_missing']                 = '[T~TXT_REGISTRATION_0007]';
        $FORM_VAR['is_not_valid']               = '[T~FRM_IS_NOT_VALID]';
        $FORM_VAR['has_passed']                 = '[T~FRM_HAS_PASSED]';
        $FORM_VAR['year']                       = '[T~FRM_YEAR]';
        $FORM_VAR['month']                      = '[T~FRM_MONTH]';
        $FORM_VAR['day']                        = '[T~FRM_DAY]';
        $FORM_VAR['hour']                       = '[T~FRM_HOUR]';
        $FORM_VAR['minute']                     = '[T~FRM_MINUTE]';
        $FORM_VAR['international_entry']        = '[T~FRM_REQUIRES_NON_US_ENTRY]';
        $FORM_VAR['us_state']                   = '[T~FRM_US_STATE]';
        $FORM_VAR['non_us']                     = '[T~FRM_NON_US]';
        $FORM_VAR['has_incorrect_number_count'] = '[T~FRM_HAS_INCORRECT_NUMBER_COUNT]';
        $FORM_VAR['is_not_a_valid_number']      = '[T~FRM_IS_NOT_A_VALID_NUMBER]';
        $FORM_VAR['is_missing_numbers']         = '[T~FRM_IS_MISSING_NUMBERS]';
        $FORM_VAR['show_password']              = '[T~FRM_SHOW_PASSWORD]';

        $FORM_MONTHS = array('[T~MONTH_JANUARY]','[T~MONTH_FEBRUARY]','[T~MONTH_MARCH]',
        '[T~MONTH_APRIL]','[T~MONTH_MAY]','[T~MONTH_JUNE]','[T~MONTH_JULY]','[T~MONTH_AUGUST]',
        '[T~MONTH_SEPTEMBER]','[T~MONTH_OCTOBER]','[T~MONTH_NOVEMBER]','[T~MONTH_DECEMBER]');
*/
        $this->SetLanguage();

    } #---------------------END CONSTRUCT---------------------




#==================================================================================================
#FUNCTION DESCRIPTIONS
#==================================================================================================
/*
translations_db_CreateTable ();                                         #create the database table
translations_db_AddLanguageColumn ($LANGUAGE);                          #adds a new column to the db table which is the new translation language
translations_db_EditLanguageColumn ($LANGUAGE, $LANGUAGE_NEW_NAME);     #rename the translation column
translations_db_DeleteLanguageColumn ($LANGUAGE);                       #delete the translation column

translations_AddWordsRoot ($WORD_ARRAY);                                #add a new word in the root language (must add root before translation word - although word can be blank as long as this adds an identifier)
translations_EditWordsRoot ($WORD_ARRAY);                               #edit the root word (this will mark all the other translations as out-of-date)
translations_DeleteWordsRoot ($WORD_ARRAY);                             #delete the root word (this will also delete all the translations for this word)

translations_DisplayTranslationsList ($LANGUAGE);                       #displays translations for editing - shows english and the language passed
translations_DisplayLanguages ();                                       #display all the current languages in the database (basis for add / edit / delete)
translations_DisplayLanguagesList ();                                   #returns pipe seperated list of all the current languages in the database

translations_ProcessWords ($TRANS_WORDS, $LANGUAGE);                    #process all translations being added or edited - will call EditWords or AddWords
translations_ProcessLanguages ($TRANS_WORDS);                           #process all language titles being added or edited - will call EditLanguages or AddLanguages

translations_EditWordsTranslation ($WORD_ARRAY);
translations_DeleteWordsTranslation ($WORD_ARRAY);

translations_EditLanguages ($WORD_ARRAY);
translations_AddLanguages ($WORD_ARRAY);
translations_DeleteLanguages ($WORD_ARRAY);

translations_DisplayActions ();                                         #displays a list of language translation actions (menu)
translations_DisplayKeyTable ();                                        #displays a key table for tranlsations (explains color coding)

LoadTranslations ($LANGUAGE);                              #load all the translations for a specific language
translations_DisplayTranslation ($LANGUAGE, $WORD);                     #display a single translation for a specific language

*/


#==================================================================================================
#START OF FUNCTIONS
#==================================================================================================
    public function AddWordsToTranlateArrayFake ($WORDS) 
    {
        foreach ($WORDS AS $identifier => $translation) {
            $this->Word_Array[$identifier] = $translation;
        }
        
        $this->Word_Array_Language = $this->Default_Language;
    }
    
    

    public function SetTable ($table='') {
        if ($table) {
            $this->Table = $table;
        }
    }

    public function GetLanguageCode($language='')
    {
    /*
        if (empty($language)) {
            $language = $this->LANGUAGE;
        }

###$code_array = db_GetAssocArray('languages', 'language_name', 'language_code_2',  "active=1");

        $language = ucfirst($language);

        $RESULT = ArrayValue($code_array, $language);

        $this->Language_Code = $RESULT;
        return $RESULT;
    */
    }




    public function TranslateArray($ARRAY, $language='', $TRANSID_FLAG='')
    {
        if (empty($language)) {
            $language = $this->LANGUAGE;
        }
        $count = count($ARRAY);
        for ($i=0; $i<$count; $i++) {
            $ARRAY[$i] = $this->TranslateText($ARRAY[$i], $language, $TRANSID_FLAG);
        }
        return $ARRAY;
    }


    public function TranslateText($TEXT, $language='', $TRANSID_FLAG='', $table='')
    {
        if (empty($language)) {
            $language = $this->LANGUAGE;
        }
        
        if (empty($table)) {
            $table = $this->Table;
        }

        if (!$TRANSID_FLAG || strtoupper($TRANSID_FLAG) == 'ALL') {

            $TRANS_ARRAY = TextBetweenArray('[T~', ']', $TEXT);

            if ($this->Word_Array_Language != $language) {
                $this->Word_Array = $this->LoadTranslations($language, $table);
                $this->Word_Array_Language = $language;
            }


            $TransArray = array();
            foreach ($TRANS_ARRAY as $identifier) {
                $FROM = "[T~$identifier]";

                if (strtoupper($TRANSID_FLAG) == 'ALL') {
                    $TO = '<span style="color:red; font-size:10px;">' . $FROM .
                          ' => </span>' . $this->Word_Array[$identifier];
                } else {
                    $TO = ArrayValue($this->Word_Array, $identifier);
                    if (empty($TO)) {
                        $TO = "<span style=\"color:#f00; background-color:#ff7;\">[MISSING TRANSLATION: $identifier]</span>";
                    }
                }

                if (strpos($TO, '[NO TRANSLATION FOR') !== false) {
                    $this->WriteTranslationMissingLog($language, $identifier);
                }

                $TransArray[$FROM] = $TO;
            }

            $TEXT = $this->mbstr_replace($TransArray, $TEXT);
        }
        return $TEXT;
    }


    public function mbstr_replace($array, $str)
    {
        foreach ($array as $key=>$value) {
            $str = mb_str_replace($key, $value, $str);  // function from mvptools
        }

        return $str;
    }



    public function SetLanguage()
    {
        global $FORM_VAR;

        #SET DEFAULT LANGUAGE
        $language = $this->Default_Language;

        #CHECK TO SEE IF A LANGUAGE HAS BEEN SET IN THE SESSION VARS
        $language = (Session('language')) ? Session('language') : $language;

        #CHECK IF TRYING TO VERRIDE LANGUAGE IN THE GET VARS
        $language = (Get('lang')) ? Get('lang') : $language;

        #SET THE LANGAUGE INTO THE SESSION VAR
        $_SESSION['language'] = $language;

        $this->LANGUAGE = $language;

        // set up form processing submit click text
        $FORM_VAR['submit_click_text'] = $this->TranslateText($FORM_VAR['submit_click_text'], $language);

        $this->GetLanguageCode();

        return $language;
    }


    public function ShowLanguageSelect($langs='')
    {
        global $THIS_PAGE_QUERY;

        if (empty($langs)) {
            #GATHER ALL THE LANGUAGE COLUMNS
###$columns    = $this->db_GetColumns ($this->Table); #get all table columns
            $columns    = explode('|', $columns);
            $IGNORELIST = 'id|identifier|'; #each item must end in | character
        } else {
            $columns    = explode('|', $langs);
            $IGNORELIST = ''; #each item must end in | character
        }

        #GET ALL THE LANGUAGE ROWS & THEIR TRANSLATIONS
        $referencekey = 'identifier';
        $conditions   = "`identifier` LIKE 'LANG_%'";
###$lang_trans   = db_GetArrayAssoc('translations', $referencekey, '*', $conditions, 'id ASC');

        $lang = Get('lang');
        $link = str_replace(array(";lang=$lang", "?lang=$lang", "&lang=$lang"), '', $THIS_PAGE_QUERY);
        $result = '
        <select name="lang" onchange="window.location=this.options[this.selectedIndex].value">';
            foreach ($columns as $KEY => $VALUE) {
                if (strpos($IGNORELIST, $VALUE.'|') === false) {
                    $curLanguage = $_SESSION['language'];
                    $langIdentifier = strtoupper("LANG_$VALUE");

                    if (!empty($lang_trans[$langIdentifier])) {
                        $SELECTED = (strtoupper($VALUE) == strtoupper($curLanguage)) ? ' selected="selected"': '';
                        $DISPLAY_VALUE = $lang_trans[$langIdentifier][strtolower($VALUE)] . ' ([T~LANG_'.strtoupper($VALUE).'])';
                        $result .= '<option value="'.$link.';lang='.$VALUE.'" '.$SELECTED.'>'.$DISPLAY_VALUE."</option>\n";
                    }
                }
            }
        $result .= '
        </select>';

        return $result;
    }




    function WriteTranslationMissingLog($LANGUAGE, $IDENTIFIER)
    {
        global $PAGE,$SITECONFIG,$ROOT,$SITE_DIR;
        
        $TRACKING = Session(str_replace('/', '_', "SITE_TRACKING$SITE_DIR"));
        $start_time = ArrayValue($TRACKING, 'START_TIME');
        if (!$start_time) {
            $start_time = time();
            $_SESSION[$SITE_TRACKING]['START_TIME'] = $start_time;
        }
        $tid = $start_time . substr(session_id(),-4);
        $logfile = $ROOT.$SITECONFIG['logdir'].'/translationloglog-'.date('Y-m').'.dat';
        $line="$tid|{$PAGE['pagename']}|$LANGUAGE|$IDENTIFIER\n";
        append_file($logfile,$line);
    }

    function translations_DisplayTranslation($LANGUAGE, $WORD)
    {
        return $this->DisplayTranslation($LANGUAGE, $WORD);
    }

    function DisplayTranslation($LANGUAGE, $WORD)
    {
        $WORD = (isset($_SESSION['LANGUAGE'][$LANGUAGE][$WORD])) ?
            $_SESSION['LANGUAGE'][$LANGUAGE][$WORD] : '{{NO TRANSLATION IN SYSTEM}}';
        return $WORD;
    }

    public function LoadTranslations($LANGUAGE, $table='')
    {
    /*
        $WORDS = '';
$order      = "id ASC";
        if (empty($table)) {
            $table = $this->Table;
            $order      = "id DESC";
        }
        
        //SELECT ALL FROM DATABASE
        $conditions = "id != 'x'";
        //$order      = "id ASC";
        $keys       = "`id`, `identifier`, `$LANGUAGE`";
###$languages_array = $this->SQL->GetArrayAll($table, $keys, $conditions, $order);

        if ($languages_array) {
            foreach ($languages_array as $ROW) {
                $WORD = ($ROW[$LANGUAGE] == '|*| ') ? '' : $ROW[$LANGUAGE];
                $WORD = str_replace('|*| ','',$WORD);

                if ($WORD != '') {
                    $VALUE = $ROW[$LANGUAGE];
                } else {
                    $VALUE = "[NO TRANSLATION FOR - {$ROW['identifier']}]";
                }

                $WORDS[$ROW['identifier']] = $VALUE;
            }
        }

        return $WORDS;
        */
    }


    function db_GetColumns($TABLE)
    {
###$fields = db_TableFieldNames($TABLE);
        return implode('|', $fields);
    }


#==================================================================================
#>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
#==================================================================================

    function translations_DisplayLanguages()
    {
        // ----- deprecated function ----
        return $this->DisplayLanguages();
    }

    function DisplayLanguages()
    {
        $TABLE  = $this->Table;

        #GATHER ALL THE LANGUAGE COLUMNS
###$columns     = $this->db_GetColumns ($TABLE); #get all table columns
        $columns    = explode('|', $columns);
        $IGNORELIST = 'id|identifier|'; #each item must end in | character

        $result = '
            <table style="border: 1px solid blue; width:300px" class="translation_language_table" cellpadding="5" cellspacing="0">
            <tr>
            <th>IDENTIFIER</th><th>NAME</th>
            </tr>
            ';

        foreach ($columns as $KEY => $VALUE) {
            if (strpos($IGNORELIST, $VALUE.'|') === false) {
                $CLASS            = 'translation_saved';
                $LANGUAGE        = 'LANGUAGETITLE';
                $STATUS            = 'SAVED';
                $ID             = $LANGUAGE .'::'. $VALUE .'::'. $STATUS;
                $result .= '
                <tr>
                <td class="row_identifier">'.$VALUE.'</td>
                <td><input type="text" onkeypress="changeId (this.id, \'EDITED\')" name="'.$ID.'" id="'.$ID.'" size="20" class="'.$CLASS.'" value="'.$VALUE.'" /><img src="images/delete.gif" alt="Delete Language" onclick="changeId (\''.$ID.'\', \'DELETE\')" /></td>
                </tr>';
            }
        }

        $result .= '<tr><td></td><td valign="top"><input onkeyup="changeId(this.id, \'NEW\');" id="LANGUAGE_TEMP" type="text" size="20" /></td></tr>';
        $result .= '</table>';

        $this->Form_Data_Array_Add = array(
        "form|$this->Action_Link|post|db_edit_form_1",
        "submit|Save|TRANSLATION_LANGUAGE_SUBMIT_ADD",
        "code|".$result,
        "submit|Save|TRANSLATION_LANGUAGE_SUBMIT_ADD",
        "endform"
        );

        //$this->Add_Submit_Name  = 'TRANSLATION_LANGUAGE_SUBMIT_ADD';

        return $this->AddRecordText();
    }


    #==================================================================================
    #<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
    #==================================================================================


    function translations_DisplayLanguagesList()
    {
        // ----- deprecated function ----
        return $this->DisplayLanguagesList();
    }

    function DisplayLanguagesList()
    {
        $TABLE = $this->Table;

        #GATHER ALL THE LANGUAGE COLUMNS
###$columns     = $this->db_GetColumns ($TABLE); #get all table columns
        $columns    = explode('|', $columns);
        $IGNORELIST = 'id|identifier|'; #each item must end in | character

        $result = '';
        foreach ($columns as $KEY => $VALUE) {
            if (strpos($IGNORELIST, $VALUE.'|') === false) {
                $result .= "$VALUE|";
            }
        }

        return $result;
    }


    function translations_ProcessWords($TRANS_WORDS, $LANGUAGE)
    {
        // ----- deprecated function ----
        $this->ProcessWords($TRANS_WORDS, $LANGUAGE);
    }

    function ProcessWords($TRANS_WORDS, $LANGUAGE)
    {
        $ADD_LIST = '';
        $UPDATE_LIST = '';
        $DELETE_LIST = '';

        #run through words and create 2 lists
        foreach ($TRANS_WORDS as $key => $value) {
            if (strpos($key, '::') != false) {
                #a valid language tranlsation will have a :: character in its key

                $PARTS = explode('::', $key);

                switch ($PARTS[2]) {
                    case "NEW":
                        $ADD_LIST[$key] = $value;
                        break;
                    case "DELETE":
                        $DELETE_LIST[$key] = $value;
                        break;
                    case "EDITED":
                        $UPDATE_LIST[$key] = $value;
                        break;
                    default:
                        break;
                }
            }
        }

        if ($LANGUAGE == $this->Default_Language) {
            #ADD ALL THE WORDS
            if ($ADD_LIST) $this->AddWordsRoot ($ADD_LIST);
            #EDIT ALL THE WORDS
            if ($UPDATE_LIST) $this->EditWordsRoot ($UPDATE_LIST);
            #DELETE ALL THE WORDS
            if ($DELETE_LIST) $this->DeleteWordsRoot ($DELETE_LIST);

        } else {
    //        #ADD ALL THE WORDS
    //        if ($ADD_LIST) $this->translations_AddWordsTranslation ($ADD_LIST);
            #EDIT ALL THE WORDS
            if ($UPDATE_LIST) $this->EditWordsTranslation ($UPDATE_LIST);
            #DELETE ALL THE WORDS
            if ($DELETE_LIST) $this->DeleteWordsTranslation ($DELETE_LIST);
        }
    }

    function translations_ProcessLanguages($TRANS_WORDS)
    {
        // ----- deprecated function ----
        $this->ProcessLanguages($TRANS_WORDS);
    }


    function ProcessLanguages($TRANS_WORDS)
    {
        #DECLARE ARRAYS
        $ADD_LIST = '';
        $DELETE_LIST = '';
        $UPDATE_LIST = '';

        #run through languages and create 2 lists
        foreach ($TRANS_WORDS as $key => $value) {
            if (strpos($key, '::') != false) {
            #a valid language will have a :: character in its key
                $PARTS = explode('::', $key);

                switch ($PARTS[2]) {
                    case "NEW":
                        $ADD_LIST[$key] = $value;
                        break;
                    case "DELETE":
                        $DELETE_LIST[$key] = $value;
                        break;
                    case "EDITED":
                        $UPDATE_LIST[$key] = $value;
                        break;
                    default:
                        break;
                }
            }
        }

        #ADD ALL THE WORDS
        if ($ADD_LIST) $this->AddLanguages($ADD_LIST);

        #EDIT ALL THE WORDS
        if ($UPDATE_LIST) $this->EditLanguages($UPDATE_LIST);

        #DELETE ALL THE WORDS
        if ($DELETE_LIST) $this->DeleteLanguages($DELETE_LIST);
    }


    function translations_AddLanguages($WORD_ARRAY)
    {
        // ----- deprecated function ----
        $this->translations_AddLanguages($WORD_ARRAY);
    }

    function AddLanguages($WORD_ARRAY)
    {
        #CREATE LIST OF VARIABLES TO UPDATE
        $ERRORLIST = '';
        foreach ($WORD_ARRAY as $key => $value) {
            if (strpos($key, '::') != false) {
            #a valid language tranlsation will have a :: character in its key
                $PARTS = explode('::', $key);
                $IDENTIFIER = $PARTS[1];
                $LANG_NAME = $value;
###$this->translations_db_AddLanguageColumn ($LANG_NAME);
            }
        }
    }

    function translations_EditLanguages($WORD_ARRAY)
    {
        $this->EditLanguages($WORD_ARRAY);
    }

    function EditLanguages($WORD_ARRAY)
    {
        #CREATE LIST OF VARIABLES TO UPDATE
        foreach ($WORD_ARRAY as $key => $value) {
            if (strpos($key, '::') != false) #a valid language tranlsation will have a :: character in its key
            {
                $PARTS = explode('::', $key);
                $IDENTIFIER = $PARTS[1];
                $IDENTIFIER = str_replace('_',' ',$IDENTIFIER);
                $NEW_LANG_NAME = $value;
###$this->db_EditLanguageColumn ($IDENTIFIER, $NEW_LANG_NAME);
            }
        }
    }

    function translations_DeleteLanguages($WORD_ARRAY)
    {
        // ----- deprecated function ----
        $this->DeleteLanguages($WORD_ARRAY);
    }

    function DeleteLanguages($WORD_ARRAY)
    {
        #CREATE LIST OF VARIABLES TO UPDATE
        foreach ($WORD_ARRAY as $key => $value) {
            if (strpos($key, '::') != false) {
            #a valid language tranlsation will have a :: character in its key
                $PARTS = explode('::', $key);
                $IDENTIFIER = $PARTS[1];
                $IDENTIFIER = str_replace('_',' ',$IDENTIFIER);
                $NEW_LANG_NAME = $value;
                echo "<br />$key => $value";
###$this->db_DeleteLanguageColumn ($IDENTIFIER);
            }
        }
    }

    function translations_AddWordsRoot($WORD_ARRAY)
    {
        // ----- deprecated function ----
        $this->AddWordsRoot($WORD_ARRAY);
    }

    function AddWordsRoot($WORD_ARRAY)
    {
        foreach ($WORD_ARRAY as $key => $value)
        {
            if (strpos($key, '::') != false) {
            #a valid language tranlsation will have a :: character in its key
                $COLLIST     = '';
                $VALLIST     = '';

                $PARTS = explode('::', $key);
                $IDENTIFIER = $PARTS[1];
                $WORD_DEFAULT = $value;

                addVariableToQueryInsertList ($COLLIST, $VALLIST, $this->Default_Language, $WORD_DEFAULT);
                addVariableToQueryInsertList ($COLLIST, $VALLIST, 'identifier', $IDENTIFIER);

                #add flags to all the other langauges
                #============================================================================================================
                #edit the english word (this will mark all the other translations as out-of-date)
                $TABLE        = $this->Table;
                $WHERE        = "`identifier` = '$IDENTIFIER'";

                #GATHER ALL THE LANGUAGE COLUMNS
###$columns     = $this->db_GetColumns ($TABLE); #get all table columns
                $columns    = explode('|', $columns);
                $IGNORELIST = 'id|identifier|'.$this->Default_Language.'|'; #each item must end in | character

                #GET THEIR VALUES
###$row         = db_GetRecord($TABLE, '*', $WHERE);

                #PUT '|*| ' IN EACH OTHER LANGAUGE FIELD
                $count_n     = count($columns);
                $a             = 0;
                while ($a < $count_n-1) {
                    $COLNAME     = $columns[$a];
                    $VAL_ORIG     = $row["$COLNAME"];

                    if ($VAL_ORIG != '|*| ') {
                        $VAL_NEW     = '|*| ' . $VAL_ORIG;
                        addVariableToQueryInsertList ($COLLIST, $VALLIST, $COLNAME, '|*| ', $IGNORELIST);
                    }
                    $a++;
                }
                #============================================================================================================

                $TABLE        = $this->Table;
                $result     = insertRow($COLLIST, $VALLIST, $TABLE);
            }
        }

    //    return $result;
    }


    function translations_EditWordsRoot($WORD_ARRAY)
    {
        // ----- deprecated function ----
        $this->EditWordsRoot($WORD_ARRAY);
    }

    function EditWordsRoot($WORD_ARRAY)
    {
        #edit the root word (this will mark all the other translations as out-of-date)

        foreach ($WORD_ARRAY as $key => $value) {
            if (strpos($key, '::') != false) {
            #a valid language tranlsation will have a :: character in its key
                $SETLIST     = '';

                $PARTS = explode('::', $key);
                $IDENTIFIER = $PARTS[1];
                $WORD_DEFAULT = $value;

                addVariableToQueryUpdateList($SETLIST, $this->Default_Language, $WORD_DEFAULT);


                #NOW UPDATE THE OTHER LANGUAGE COLUMNS
                #================================================================================================================
                $TABLE        = $this->Table;
                $WHERE        = "`identifier` = '$IDENTIFIER'";

                #GATHER ALL THE LANGUAGE COLUMNS
###$columns     = $this->db_GetColumns ($TABLE); #get all table columns
                $columns    = explode('|', $columns);
                $IGNORELIST = 'id|identifier|'.$this->Default_Language.'|'; #each item must end in | character

                #GET THEIR VALUES
                //$row         = selectAllRow ($TABLE, $WHERE); #returns associative array of values
###$row = db_GetRecord($TABLE, '*', $WHERE);

                #PRE-PEND '|*| ' (if not already there) TO THE FRONT TO FLAG AS THE ENGLISH HAVING BEEN UPDATED
                $count_n     = count($columns);
                $a             = 0;
                while ($a < $count_n-1) {
                    $COLNAME     = $columns[$a];
                    $VAL_ORIG     = $row["$COLNAME"];
                    $VAL_CHECK     = substr($row["$COLNAME"],0,4);

                    if ($VAL_CHECK != '|*| ') {
                        $VAL_NEW     = '|*| ' . $VAL_ORIG;
                        addVariableToQueryUpdateList($SETLIST, $COLNAME, $VAL_NEW, $IGNORELIST);
                    }
                    $a++;
                }


                #UPDATE THE DATABASE
                $TABLE      = $TABLE;
                $SET        = $SETLIST;
                $WHERE      = $WHERE;
                $result     = updateRow($TABLE, $SET, $WHERE);

            }
        }
    }

    function translations_DeleteWordsRoot($WORD_ARRAY)
    {
        // ----- deprecated function ----
        $this->DeleteWordsRoot($WORD_ARRAY);
    }

    function DeleteWordsRoot($WORD_ARRAY)
    {
        #delete the english word (this will also delete all the translations for this word)

        foreach ($WORD_ARRAY as $key => $value)
        {
            if (strpos($key, '::') != false) #a valid language tranlsation will have a :: character in its key
            {
                $COLLIST     = '';
                $VALLIST     = '';

                $PARTS = explode('::', $key);
                $IDENTIFIER = $PARTS[1];
                $WORD_DEFAULT = $value;

                $TABLE        = $this->Table;
                $WHERE         = "`identifier` = '$IDENTIFIER'";
###$result     = db_DeleteRecord($TABLE, $WHERE);
            }
        }
    }




    function translations_EditWordsTranslation($WORD_ARRAY)
    {
        // ----- deprecated function ----
        $this->EditWordsTranslation($WORD_ARRAY);
    }

    function EditWordsTranslation($WORD_ARRAY)
    {
        #CREATE LIST OF VARIABLES TO UPDATE
        $ERRORLIST = '';
        foreach ($WORD_ARRAY as $key => $value)
        {
            if (strpos($key, '::') != false) {
            #a valid language tranlsation will have a | character in its key
                $PARTS         = explode('::', $key);
                $LANGUAGE     = $PARTS[0];
                $IDENTIFIER = $PARTS[1];
                $SAVESTATUS = $PARTS[2];

                $STATADD     = ($SAVESTATUS == 'UNSAVED') ? '|*| ' : '';
                $NEWVALUE     = $STATADD . $value;

                $SETLIST     = '';
                $IGNORELIST = '';
                addVariableToQueryUpdateList ($SETLIST, $LANGUAGE, $NEWVALUE, $IGNORELIST);

                $TABLE        = $this->Table;
                $SET        = $SETLIST;
                $WHERE        = "`identifier` = '$IDENTIFIER'";
                $result     = updateRow($TABLE, $SET, $WHERE);

                if (!$result) {
                    $ERRORLIST .= "<br />ERROR: $LANGUAGE -> $IDENTIFIER";
                }
            }
        }

        return $ERRORLIST;
    }

    function translations_DeleteWordsTranslation ($WORD_ARRAY)
    {
        // ----- deprecated function ----
        $this->DeleteWordsTranslation($WORD_ARRAY);
    }

    function DeleteWordsTranslation($WORD_ARRAY)
    {
        #CREATE LIST OF VARIABLES TO UPDATE
        foreach ($WORD_ARRAY as $key => $value)
        {
            if (strpos($key, '::') != false) {
            #a valid language tranlsation will have a :: character in its key
                $PARTS         = explode('::', $key);
                $LANGUAGE     = $PARTS[0];
                $IDENTIFIER = $PARTS[1];
                $SAVESTATUS = $PARTS[2];

                $NEWVALUE     = '';

                $SETLIST     = '';
                $IGNORELIST = '';
                addVariableToQueryUpdateList ($SETLIST, $LANGUAGE, $NEWVALUE, $IGNORELIST);

                $TABLE        = $this->Table;
                $SET        = $SETLIST;
                $WHERE        = "`identifier` = '$IDENTIFIER'";
                $result     = updateRow($TABLE, $SET, $WHERE);

                if (!$result) {
                    $ERRORLIST .= "<br />ERROR: $LANGUAGE -> $IDENTIFIER";
                }
            }
        }
    }



    function translations_DisplayTranslationsList($LANGUAGE)
    {
        // ----- deprecated function ----
        $this->DisplayTranslationsList($LANGUAGE);
    }

    function DisplayTranslationsList($LANGUAGE)
    {
        $result = '';

        if ($LANGUAGE == '')
        {
            #LANGUAGE HEADERS ======================================================================

            $TABLE        = $this->Table;

            #GATHER ALL THE LANGUAGE COLUMNS
###$columns     = $this->db_GetColumns ($TABLE); #get all table columns
            $columns    = explode('|', $columns);
            $IGNORELIST = 'id|identifier|'; #each item must end in | character

            $result .= '
            <div style="width:98%; border:1px dashed #cccccc; background-color: #f3f3f3; padding: 5px;">
            LANGUAGES: <select name="tempLanguageSelect">';
                foreach ($columns as $KEY => $VALUE)
                {
                    if (strpos($IGNORELIST, $VALUE.'|') === false)
                    {
                        $EXTRA         = ($VALUE == $this->Default_Language) ? ' (r)': '';
                        $SELECTED     = ($VALUE == Get('lang')) ? ' selected': '';
                        $result .= '<option value="'.$VALUE.'" '.$SELECTED.'>'.strtoupper($VALUE) . $EXTRA;
                    }
                }
            $result .= '
            </select>
            </div>
            ';


            $this->Form_Data_Array_Add = array(
            "form|$this->Action_Link|post|db_edit_form_2",
            "code|".$result,
            "submit|Load|TRANSLATION_LANG_SELECT_SUBMIT_ADD",
            "endform"
            );

            //$this->Add_Submit_Name  = 'TRANSLATION_LANG_SELECT_SUBMIT_ADD';
            return $this->AddRecordText();

            #=======================================================================================


        } else {

            //SELECT ALL FROM DATABASE
            $db_table       = $this->Table;
            $conditions     = "id != 'x'";
            $order          = "id ASC";
            $keys           = '*';
###$RESULTS        = db_GetArrayAll($db_table, $keys, $conditions, $order);

            $result .= '
            <table style="border: 1px solid blue;" class="translation_table" cellpadding="5" cellspacing="0">
            <tr>
            <th>Identifier</th><th>Root Language ('.$this->Default_Language.')</th><th>'.ucwords($LANGUAGE).' Translation</th><th></th>
            </tr>
            ';

            foreach ($RESULTS as $ROW) {

                if (strpos($ROW[$LANGUAGE], '|*| ') === false) {
                # note the === that is required
                    #THIS IS NOT MARKED AS A TRANSLATION NEEDING UPDATE
                    $STATUS     = 'SAVED';
                    $TEXT         = $ROW[$LANGUAGE];
                    $CLASS        = 'translation_saved';
                } else {
                    #THIS IS MARKED AS A TRANSLATION NEEDING UPDATE
                    $PARTS         = explode('|*| ', $ROW[$LANGUAGE]);
                    $STATUS     = substr($ROW[$LANGUAGE], 0, 3);
                    $STATUS     = 'UNSAVED';
                    $TEXT         = $PARTS[1];
                    $CLASS        = 'translation_unsaved';
                }

                $ID = $LANGUAGE .'::'. $ROW['identifier'] .'::'. $STATUS;

                $ACCEPT_IMAGE = ($STATUS =='UNSAVED') ?
                '<img src="images/check_mark.gif" alt="Accept Translation" id="'.$ID.'_checkmark" onclick="changeId(\''.$ID.'\', \'EDITED\')" />' : '';

                $DELETE_IMAGE = ($LANGUAGE == $this->Default_Language) ? '<img src="images/delete.gif" alt="Delete" id="'.$ID.'_deletemark" onclick="changeClassToDeletedRow (\''.$ID.'\', \'DELETE\')" />' : '<img src="images/delete.gif" alt="Delete" id="'.$ID.'_deletemark" onclick="changeClassToDeleted (\''.$ID.'\', \'DELETE\')" />';

                $result .= '<tr id="tr_'.$ID.'"><td class="row_identifier">'.$ROW['identifier'].'</td><td>' . $ROW[$this->Default_Language] . '</td><td><textarea onkeypress="changeClassToEdited(this.id)" name="'.$ID.'" id="'.$ID.'" rows="5" cols="30" class="'.$CLASS.'">' . $TEXT . '</textarea></td><td>'.$ACCEPT_IMAGE . $DELETE_IMAGE.'</td></tr>';
                //$result .= '<tr><td colspan="4" class="row_spacer"></td></tr>';
            }

            if ($LANGUAGE == $this->Default_Language) {
                $CLASS = ($CLASS) ? $CLASS : 'd';
                $CLASS = 'translation_saved';
                $result .= '
                <tr class="row_new">
                <td valign="top"><input onkeyup="changeClassToNew(this.id, \''.$LANGUAGE.'\');" id="IDENTIFIER_TEMP" type="text" size="25" /><br />NEW IDENTIFIER NAME</td><td></td>
                <td><textarea id="CONTENT_TEMP" name="CONTENT_TEMP" rows="8" cols="30" class="'.$CLASS.'"></textarea></td>
                <td></td>
                </tr>';
            }

            $result .= '</table>';

            $this->Form_Data_Array_Add = array(
            "form|$this->Action_Link|post|db_edit_form_3",
            "submit|Save|TRANSLATION_WORD_SUBMIT_ADD",
            "hidden|StoreLanguage|".$LANGUAGE,
            "code|".$result,
            "submit|Save|TRANSLATION_WORD_SUBMIT_ADD",
            "endform"
            );

            //$this->Add_Submit_Name  = 'TRANSLATION_WORD_SUBMIT_ADD';

            return $this->AddRecordText();

        }
    }


    function translations_db_CreateTable()
    {
        // ----- deprecated function ----
###$this->db_CreateTable();
    }

    function db_CreateTable()
    {
        $query = '
            CREATE TABLE `translations` (
              `id` int(11) NOT NULL auto_increment,
              `identifier` varchar(255) NOT NULL,
              `'.$this->Default_Language.'` text NOT NULL,
              PRIMARY KEY  (`id`),
              UNIQUE KEY `identifier` (`identifier`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0;';
###db_Query($query);
    }

    function translations_db_AddLanguageColumn($LANGUAGE)
    {
        // ----- deprecated function ----
###$this->db_AddLanguageColumn($LANGUAGE);
    }

    function db_AddLanguageColumn($LANGUAGE)
    {
        $query = "ALTER TABLE `translations` ADD `$LANGUAGE` TEXT NOT NULL ;";
###db_Query($query);
    }

    function translations_db_DeleteLanguageColumn($LANGUAGE)
    {
        // ----- deprecated function ----
###$this->db_DeleteLanguageColumn($LANGUAGE);
    }
    function db_DeleteLanguageColumn($LANGUAGE)
    {
        $query = "ALTER TABLE `translations` DROP `$LANGUAGE` ;";
###db_Query($query);
    }

    function translations_db_EditLanguageColumn($LANGUAGE, $LANGUAGE_NEW_NAME)
    {
        // ----- deprecated function ----
###$this->db_EditLanguageColumn($LANGUAGE, $LANGUAGE_NEW_NAME);
    }

    function db_EditLanguageColumn($LANGUAGE, $LANGUAGE_NEW_NAME)
    {
        $query = "ALTER TABLE `translations` CHANGE `$LANGUAGE` `$LANGUAGE_NEW_NAME` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL";
###db_Query($query);
    }


    function translations_DisplayKeyTable()
    {
        // ----- deprecated function ----
        return $this->DisplayKeyTable();
    }

    function DisplayKeyTable()
    {
        $table ='
        <table style="border: 1px solid blue" class="translation_language_table" cellpadding="5" cellspacing="0">
          <tr>
            <th>KEY</th>
            <th>DESCRIPTION</th>
          </tr>
          <tr>
            <td class="translation_unsaved" style="width:20px;"></td>
            <td>ROOT TRANSLATION HAS CHANGED</td>
          </tr>
          <tr>
            <td class="translation_edited" style="width:20px;"></td>
            <td>UNSAVED DATA</td>
          </tr>
          <tr>
            <td style="width:20px;"><img src="images/check_mark.gif" alt="" /></td>
            <td>ACCEPT TRANSLATION (root translation changed)</td>
          </tr>
          <tr>
            <td style="width:20px;"><img src="images/delete.gif" alt="" /></td>
            <td>DELETE TRANSLATION TEXT</td>
          </tr>
        </table>
        <br />
        ';

        return $table;
    }

    function translations_DisplayActions()
    {
        // ----- deprecated function ----
        return $this->DisplayActions();
    }
    function DisplayActions()
    {
        $table = '
        <a href="'.$_SERVER['PHP_SELF'].'?action=word">EDIT TRANSLATIONS</a> &nbsp;&nbsp;
        <a href="'.$_SERVER['PHP_SELF'].'?action=language">EDIT LANGUAGES</a>
        <br /><br />
        ';
        return $table;
    }



} #END CLASS
