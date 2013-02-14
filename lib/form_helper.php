<?php
// ------------- FORM HELPER UNIT ----------------
// ------------- Michael V. Petrovich ------------------
/*
    2009-12-28 : Added: 'has_too_few_characters'
    2010-06-25 : Added: Form_SetFormPrefix, Form_GetFormPrefix, Form_GetStateCodeFromName
    2010-06-30 : Added: Form_PostValue
    2010-07-12 : Autocomplete - set so that if value shown is empty, result is empty
    2010-07-16 : Added Website, Form_CheckDomainName
    2010-11-29 : Added 'dollarz' kind
    2010-12-08 : Added nbsp to radioh before text

*/
if (empty($LIB)) {
    include dirname(__FILE__).'/mvptools.php';
}

$FormPrefix           = 'FORM_'; // -------- !! this is prepended to each input tagname and id
$E                    = chr(27); // use as line delimiter  ---- recommend using |$E to end lines when using text field for arrays

//vars set from old variable names for backward compatiblity
$FORM_SHOW_POSTED     = true;   // -------- flag to display posted date-time at the end of output table, used mainly for email
$FORM_BLOCK_REFERRER  = true;   // -------- block referrer requires form to be posted from same domain
$FORM_SHOW_MISSING    = false;  // -------- show missing in output table
$FORM_STRIP_QUOTES    = false;  // -------- this removes all the quotes from inputs
$FORM_TIME_SHIFT      = 0; // set for server time differences hours -- used to return the posted date-time shifted from the server time
$FORM_CREDITCARD_LENGTH = 16;


$FORM_VAR = array(
    'line_delimit'       => "|$E",
    'id_prefix'          => '',
    'required_text'      => '<span class="formrequired">&bull;</span>',      // -------- When flagged as required, prepends to title
    'title_template'     => '<br class="formtitlebreak" /><div class="formtitle">@:</div>' ."\n",    // -------- Template for the title
    'info_template'      => '<div class="forminfo">@</div>'."\n\n",    // -------- templete for the input field
    'error_template'     => '<div class="error">@</div>'."\n",       // -------- template for the input field with error
    'posted_cell_style'  => 'white-space:nowrap; text-align:center; font-size:0.76em;',  // ------- how to display the posted date-time

    'default_country'    => 'US',               // -------- the 2-digit default country

    'start_select'       => '-- select --',     // -------- initial default for a SELECT input

    'start_select_value' => 'START_SELECT_VALUE',  // no need change this

    'new_select_text'    => '-- new --',        // -------- used with a combination SELECT and TEXT input
    'new_select_text_value'  => 'NEW_SELECT_TEXT_VALUE',  // no need change this


    'new'                => 'New',
    'submit_click_text'  => 'Processing. . .',  // -------- submit button onclick  changes to this text

    // text used in output of errors or additional input text

    'referrer_error'     => 'Invalid Referrer - Blocked for Security!',
    'illegal_characters' => 'has illegal characters',
    'insecure_password'  => 'is insecure: include upper and lower case letters, numbers, and symbols (!@#$%^*()-_+={}[]|/:;,.?~|)',
    'has_too_few_characters' => 'has too few characters (@ needed)',
    'has_too_many_characters' => 'has too many characters (@ maximum)',
    'is_missing'         => 'is missing',
    'year'               => 'Year',
    'month'              => 'Month',
    'day'                => 'Day',
    'hour'               => 'Hour',
    'minute'             => 'Minute',
    'international_entry' => 'International requires Non-US entry',
    'us_state'           => 'U.S. State',
    'state'              => 'State',
    'canada_province'    => 'Province/Territory',
    'state_province'     => 'State/Province',
    'non_us'             => 'Non-U.S.',

    'has_passed'                    => 'has passed', // for credit card
    'has_incorrect_number_count'    => 'has incorrect number count', // for credit card
    'is_not_a_valid_number'         => 'is not a valid number',      // for credit card
    'is_not_valid'                  =>  'is not valid',  // for email
    'is_missing_numbers'            => 'is missing numbers',  // for phone
    'show_password'                 => 'Show Password',
    'phone_delimiter'    => '-',                // -------- used to process phone numbers (xxx-xxx-xxxx)
    'form_date_code'     => 'l, M j, Y, g:ia',  // -------- this is the format for the posted date-time
    'accept_charset'     => 'accept-charset="utf-8"'
);

//$SubmitClickText = 'Processing. . .';  // -------- submit button onclick  changes to this text
//$StartSelect = '-- select --';         // -------- initial default for a SELECT input
//$NewSelectText     = '-- new --';      // -------- used with a combination SELECT and TEXT input
//$FormPhoneDelimiter = '-';             // -------- used to process phone numbers (xxx-xxx-xxxx)
//$DefaultCountry = 'US';                // -------- the 2-digit default country
//$RequiredText  = '<span class="formrequired">&bull;</span>';      // -------- When flagged as required, prepends to title
//$TitleTemplate = '<br class="formtitlebreak" /><div class="formtitle">@@VAR@@:</div>' ."\n";    // -------- Template for the title
//$InfoTemplate  = '<div class="forminfo">@@VAR@@</div>'."\n\n";    // -------- templete for the input field
//$ErrorTemplate = '<div class="error">@@ERROR@@</div>'."\n";       // -------- template for the input field with error
//$form_date_code = 'l, M j, Y, g:ia';                              // -------- this is the format for the posted date-time
//$posted_cell_style = 'white-space:nowrap; text-align:center; font-size:0.76em;';  // ------- how to display the posted date-time



//masks for edit
$Mask_Integer    = '^[0-9]+$';                   // -------- only integer values
$Mask_Name       = '^[a-zA-Z0-9 \'\-]+$';        // -------- used for names
$Mask_UserName   = '^[a-zA-Z0-9]+$';             // -------- used for usernames (no spaces)
$Mask_Password   = '^[a-zA-Z0-9:\.\-\!\@\#\$\%\^\&\*_]+$';   // -------- used for passwords
$Mask_Email      = '^([0-9a-z]+)([0-9a-z\.-]+)@([0-9a-z\._-]+)\.([a-z]{2,6})';  // -------- email mask (better to use email type)
$Mask_Real       = '^[0-9.\-]+$';                // -------- real numbers
$Mask_RealC      = '^[0-9\,.\-]+$';              // -------- real numbers, with commas
$Mask_ZIP        = '^[0-9\-]+$';                 // -------- ZIP codes
$Mask_4int       = '^[0-9]{4}$';                 // -------- 4 digit integer
$Mask_2int       = '^[0-9]{2}$';                 // -------- 2 digit integer
$Mask_Char       = '^[a-zA-Z]+$';                // -------- letters-only
$Mask_2chr       = '^[a-zA-Z]{2}$';              // -------- 2 letters only
$Mask_General_Line = '^[a-zA-Z0-9_ \!-\?[:punct:]]+$';              // -------- general text line
$Mask_General    = '^[a-zA-Z0-9_ '."\r\n\t".'\!-\?[:punct:]]+$';    // -------- general text + CR and tabs
$Mask_Words      = '^[[:alnum:][:space:][:punct:]]+$';              // -------- words only
$Mask_Dir        = '^[a-zA-Z0-9\/_\.\-]+$';      // -------- directory listings


$FORM_MONTHS = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

$FORM_COUNTRY_CODES = '';  // loaded with Form_LoadCountryCodes() in file: country_codes.dat

$FORM_STATE_CHAR_CODES = array('INT','AL','AK','AZ','AR','CA','CO','CT','DE','DC','FL','GA','HI','IA','ID','IL','IN','KS','KY','LA','MA','MD','ME','MN','MI','MO','MS','MT','NC','ND','NE','NH','NJ','NM','NV','NY','OK','OR','OH','PA','RI','SC','SD','TN','TX','UT','VA','VT','WA','WI','WV','WY');
$FORM_STATE_CODES = array(
  'International','AL - Alabama','AK - Alaska','AZ - Arizona','AR - Arkansas',
  'CA - California','CO - Colorado','CT - Connecticut','DE - Delaware','DC - Washington D.C.',
  'FL - Florida','GA - Georgia','HI - Hawaii','IA - Iowa','ID - Idaho','IL - Illinois','IN - Indiana',
  'KS - Kansas','KY - Kentucky','LA - Louisiana',
  'MA - Massachusetts','MD - Maryland','ME - Maine','MN - Minnesota','MI - Michigan','MO - Missouri',
  'MS - Mississippi','MT - Montana','NC - North Carolina','ND - North Dakota','NE - Nebraska','NH - New Hampshire',
  'NJ - New Jersey','NM - New Mexico','NV - Nevada','NY - New York','OH - Ohio','OK - Oklahoma','OR - Oregon',
  'PA - Pennsylvania','RI - Rhode Island','SC - South Carolina','SD - South Dakota','TN - Tennessee','TX - Texas',
  'UT - Utah','VA - Virgina','VT - Vermont','WA - Washington','WI - Wisconsin','WV - West Virginia','WY - Wyoming'
);

$FORM_CANADA_PROVINCES = array(
    'Alberta', 'British Columbia', 'Manitoba', 'New Brunswick',
    'Newfoundland and Labrador', 'Northwest Territories', 'Nova Scotia',
    'Nunavut', 'Ontario', 'Prince Edward Island', 'Quebec', 'Saskatchewan', 'Yukon'
);

// ============================================== FUNCTIONS ==============================================

function Form_SetFormPrefix($prefix)
{
    global $FormPrefix;
    $FormPrefix = $prefix;
}

function Form_GetFormPrefix()
{
    global $FormPrefix;
    return $FormPrefix;
}

function Form_SetShowPosted($value)
{
    global $FORM_SHOW_POSTED;
    $FORM_SHOW_POSTED = $value;
}

function Form_GetStateCodeFromName($name)
{
    global $FORM_STATE_CODES;
    foreach ($FORM_STATE_CODES as $row) {
        if (substr($row, 5) == $name ) {
            return substr($row, 0, 2);
        }
    }
    return $name;
}

function Form_CheckDomainName($domain)
{
    $domain = strTo($domain, '/');
    if (strlen($domain > 255)) return false;
    if (!preg_match('/([0-9a-z\.-]+)\.([a-z]{2,6})$/', $domain)) return false;
    $domain_labels = explode('.', $domain);
    foreach ($domain_labels as $label) {
        if (empty($label) or (strlen($label) > 63)) return false;
    }
    return true;
}


function GetCountryNameOrCode($cc)
{
    global $FORM_COUNTRY_CODES;

    Form_LoadCountryCodes();

    if (empty($cc)) return '';

    if (!empty($FORM_COUNTRY_CODES[$cc])) {
        $RESULT = $FORM_COUNTRY_CODES[$cc];
    } else {
        $RESULT = array_search($cc, $FORM_COUNTRY_CODES);
        if (!$RESULT) $RESULT = '';
    }
    return $RESULT;
}


function HaveSubmit($submitname)
{
    GLOBAL $FORM_VAR;
    return Post($submitname) == $FORM_VAR['submit_click_text'];
}

function WriteErrorText($error)
{
    global $FORM_VAR;
    $RESULT = '';
    if(!empty($error)) {
        return str_replace('@', $error, $FORM_VAR['error_template']);
    }
    return $RESULT;
}

function WriteError($error)
{
    echo WriteErrorText($error);
}


function GetPostItem($str)
{
    global $FormPrefix;
    $value = Post($FormPrefix.$str);
    if ($value == intOnly($value)) {
        return $value;
    } elseif (!empty($value)) {
        $value = htmlspecialchars_decode(Form_StripQuotes($value));
        return trim(htmlspecialchars($value, ENT_COMPAT, 'UTF-8'));
    } else {
        return '';
    }
}

function WriteFormStart($action, $method, $name='')
{
    if ($name) $name = " name=\"$name\" id=\"$name\"";
    echo "\n<form action=\"$action\" method=\"$method\"$name>\n";
}

function WriteFormEnd()
{
    echo "</form>\n";
}


function Form_KeyValuesToVar($FormArray)
{
    foreach($FormArray as $key => $value) $GLOBALS[$key] = $value;
    return '';
}

function Form_PostVars($titlearray, $valuearray)
{
    global $FormPrefix;
    foreach ($titlearray as $key => $value) {
        $_POST[$FormPrefix . $key] = empty($valuearray[$key])? '' : $valuearray[$key];
    }
}

function Form_PostArray($array)
{
    global $FormPrefix;
    if ($array) {
        foreach ($array as $key => $value) {
            $_POST[$FormPrefix . $key] = $value;
        }
    }
}

function Form_PostValue($key, $value)
{
    global $FormPrefix;
    $_POST[$FormPrefix . $key] = $value;
}

function Form_AjaxToPost($var)
{
    if(Post($var)) {
        $var_pairs = explode('&', $_POST[$var]);

        foreach ($var_pairs as $field) {
            list($key,$value) = explode('=', $field);
            $key              = urldecode($key);
            $value            = urldecode($value);
            $_POST[$key]      = $value;
        }
    }
}


function Form_AssocArrayToList($array)
{
    // returns kkk=vvv|kkk=vvv
    $RESULT = '';
    if ($array) {
        foreach ($array as $key=>$value) {
            $RESULT .= "|$key=$value";
        }
        $RESULT = substr($RESULT, 1);
    }
    return $RESULT;
}

function Form_ArrayToList($array)
{
    // returns vvv|vvv
    $RESULT = '';
    if ($array) {
        $RESULT = implode('|', $array);
    }
    return $RESULT;
}


function Form_GetIdFromVar($var)
{
    global $FORM_VAR, $FormPrefix;

    $RESULT = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $var);
    $RESULT = $FormPrefix . $FORM_VAR['id_prefix'] . $RESULT;
    if (!preg_match('/^[a-zA-Z]/', $RESULT)) {
        $RESULT = 'id_' . $RESULT;
    }
    return $RESULT;
}

// =======================================================================
// private functions -- not expected to call these outside form processing
// =======================================================================


function Form_LoadCountryCodes()
{
    global $FORM_COUNTRY_CODES, $LIB;
    if (empty($FORM_COUNTRY_CODES)) {
        $lines = file("$LIB/country_codes.dat");
        foreach ($lines as $line) {
            list($co, $name) = explode("\t", trim($line));
            if ($co) {
                $FORM_COUNTRY_CODES[$co] = $name;
            }
        }
    }
}


function Form_CheckCreditCardLength($num)
{
    $len = strlen($num);
    $c1 = substr($num,0,1);
    $c2 = substr($num,0,2);
    $c3 = substr($num,0,3);
    $c4 = substr($num,0,4);
    $c5 = substr($num,0,5);
    $c6 = substr($num,0,6);
    if (($c2 == '34') or ($c2 == '37')) return $len == 15;  // American Express

    if (($c3 == '622') or (($c6 >= '622126') and ($c6 <= '622925'))) return (($len > 15) and ($len < 20)); //China UnionPay

    if (($c3 >= '300') and ($c3 <= '305')) return $len == 14; // Diners Club Carte Blanche

    if ($c2 == '36') return $len == 14; // Diners Club International

    if ($c2 == '55') return $len == 16; // Diners Club US & Canada (or visa)

    if (($c4 == '6011') or (($c5 >= '60112') and ($c5 <= '60114')) or ($c6 == '601174')
        or (($c6 >= '601177') and ($c6 <= '601179'))
        or (($c6 >= '601186') and ($c6 <= '601199'))
        or (($c6 >= '622126') and ($c6 <= '622925'))
        or (($c3 >= '644') and ($c3 <= '644'))
        or ($c2 == '65'))  return $len == 16; //  Discover Card

    if (($c4 >= '3528') and ($c4 <= '3589')) return $len == 16; //JCB

    if (($c4 == '5018') or ($c4 == '5020') or ($c4 == '5038') or ($c4 == '6304')
        or ($c4 == '6759') or ($c4 == '6761'))  return (($len > 11) and ($len < 20)); //Maestro (debit card)

    if (($c2 >= '51') and ($c2 <= '55')) return $len == 16; //    MasterCard

    if (($c4 == '6334') or ($c4 == '6767')) return (($len == 16) or ($len == 18) or ($len == 19)); //Solo (debit card)

    if (($c4 == '4903') or ($c4 == '4905') or ($c4 == '4911') or ($c4 == '4936')
        or ($c6 == '564182') or ($c6 == '633110') or ($c4 == '6333')
        or ($c4 == '6759')) return (($len == 16) or ($len == 18) or ($len == 19)); // Switch (debit card)

    if (($c6 == '417500') or ($c4 == '4917') or ($c4 == '4508') or ($c4 == '4844'))  return $len == 16;  // Visa Electron

    if ($c1 == '4') return $len == 16;  // Visa

    return true;  //since could not check
}

function Form_ValidCreditCardNumber($cardNumber)
{
    $cardNumber = preg_replace('/[^0-9]/', '', $cardNumber);

    if ( empty($cardNumber) ) {
        return false;
    }

    $validFormat = preg_match("/^5[1-5][0-9]{14}|"  // mastercard
        . "^4[0-9]{12}([0-9]{3})?|" // visa
        . "^3[47][0-9]{13}|" // american express
        . "^3(0[0-5]|[68][0-9])[0-9]{11}|" //discover
        . "^6011[0-9]{12}|" //diners
        . "^(3[0-9]{4}|2131|1800)[0-9]{11}$/", $cardNumber); //JC

    if (!$validFormat) {
        return false;
    }

    // Is the number valid?
    $revNumber = strrev($cardNumber);
    $numSum = 0;

    for ($i = 0; $i < strlen($revNumber); $i++) {

        $currentNum = substr($revNumber, $i, 1);

        // Double every second digit
        if ($i % 2 == 1) {
            $currentNum *= 2;
        }

        // Add digits of 2-digit numbers together
        if ($currentNum > 9) {
            $firstNum = $currentNum % 10;
            $secondNum = ($currentNum - $firstNum) / 10;
            $currentNum = $firstNum + $secondNum;
        }

        $numSum += $currentNum;
    }

    // If the total has no remainder it's OK
    $passCheck = ($numSum % 10 == 0);
    return $passCheck;
}


function Form_PhoneConvert($phone)
{
    global $FORM_VAR;
    $RESULT = $phone;
    if ($RESULT == intOnly($RESULT)) {
        if (strlen($RESULT)>4) $RESULT = substr($RESULT,0,-4).$FORM_VAR['phone_delimiter'].substr($RESULT,-4);
        if (strlen($RESULT)>8) $RESULT = substr($RESULT,0,-8).$FORM_VAR['phone_delimiter'].substr($RESULT,-8);
        if (strlen($RESULT)>12) $RESULT = substr($RESULT,0,-12).$FORM_VAR['phone_delimiter'].substr($RESULT,-12);
    } else {

        $RESULT = preg_replace('/[\(\) ]/', $FORM_VAR['phone_delimiter'], $RESULT);
        $RESULT = preg_replace('/[^0-9\-]/', '', $RESULT);
        $RESULT = preg_replace('/\-+/', $FORM_VAR['phone_delimiter'], $RESULT);
        $RESULT = preg_replace('/(^\-+)|(\-+$)/', '', $RESULT);
    }
    return $RESULT;
}


function Form_StripQuotes($str)
{
    global $FORM_STRIP_QUOTES;
    if ($FORM_STRIP_QUOTES) return str_replace(array("'", '"', "`"), '', $str);
    else return $str;
}


function Form_GetPostItemQuotes($str)
{
    global $FormPrefix;
    $value = Post($FormPrefix.$str);
    if ($value == '0') return '0';
    return (empty($value))? '' : addslashes(trim(htmlspecialchars($value, ENT_COMPAT, 'UTF-8')));
}


function Form_GetPostHTML($str)
{
    global $FormPrefix;
    if (!empty($_POST[$FormPrefix.$str])) {
        //return htmlspecialchars_decode(trim($_POST[$FormPrefix.$str]));
        return trim($_POST[$FormPrefix.$str]);
    }
    else return '';
}


function Form_OkRow($value)
{
    global $FORM_SHOW_MISSING;
    return ($FORM_SHOW_MISSING or ($value!=''));
}


function Form_RequiredCheck($required, $process)
{
    // determine if required
    $required = trim($required);

    if (empty($required) or ($required == 'N')) {
        return false;
    } elseif ($required == 'Y') {
        return true;
    } elseif (strpos($required, '=') !== false) {

    /// ------------- note may be better to preprocess FormArray - checkings states and creating a required variable

        if (!$process) return true;

        $checks = explode('::', $required);

        // checks are OR checks for all conditions, any true return true
        foreach ($checks as $rc) {
            $var = strTo($rc, '=');
            $var_value = strFrom($rc, '=');
            $not = (substr($var, -1) == '!');
            if ($not) {
                $var = substr($var, 0, -1);
            }
            $var_value_check = GetPostItem($var);

            if ($not) {
                if ($var_value_check != $var_value) return true;
            } else {
                if ($var_value_check == $var_value) return true;
            }
        }
    }
    return false;
}

function Form_CheckMissing($required, $value, $empty, $field_name, &$error)
{
    global $FORM_VAR;

    if (Form_RequiredCheck($required, 1) and ($value == $empty)) {
        $error .= "$field_name {$FORM_VAR['is_missing']}<br />\n";
    }
}


function Form_GetMax($max_field)
{
    $min_max = explode(',', str_replace(' ', '', $max_field));
    return (count($min_max)>1)? $min_max[1] : $max = $min_max[0];
}

function Form_CheckMinMax($value, $field1, $max_field, &$error)
{
    global $FORM_VAR;
    $min_max = explode(',', str_replace(' ', '', $max_field));

    $RESULT = true;
    if ($value) {
        if (count($min_max)>1) {
            if (strlen($value) < $min_max[0]) {
                $text = str_replace('@', $min_max[0], $FORM_VAR['has_too_few_characters']);
                $error .= "$field1 $text<br />\n";
                $RESULT = false;
            }
            $max = $min_max[1];
        } else {
            $max = $min_max[0];
        }
        if (strlen($value) > $max) {
            $text = str_replace('@', $max, $FORM_VAR['has_too_many_characters']);
            $error .= "$field1 $text<br />\n";
            $RESULT = false;
        }
    }
    return $RESULT;
}


function Form_CheckListValue($value, $field, $start_index)
{
    $RESULT = false;
    if ($value) {
        $field_count = count($field);
        for($i = $start_index; $i < $field_count; $i++) {
            $option = strTo($field[$i], '=');
            $option = strTo($option, '::');
            if (($option != '') and ($option == $value) ) {
                return true;
            }
        }
    }
    return $RESULT;
}

//===============================================================================
//                               PROCESS FORM
//===============================================================================


function ProcessFormNT($formdata, &$error)
{
    // process form without a table returned
    return ProcessForm($formdata, $table, '', '', '', $error, false);
}

function ProcessForm($formdata,&$table, $tableoptions, $thoptions, $tdoptions,&$error, $TBL=true) {

    global $FORM_VAR, $FormPrefix,
    $FORM_MONTHS, $FORM_STATE_CODES, $FORM_CANADA_PROVINCES, $FORM_STATE_CHAR_CODES,
    $FORM_BLOCK_REFERRER,
    $FORM_SHOW_POSTED, $FORM_SHOW_MISSING, $FORM_TIME_SHIFT,
    $Mask_Integer,
    $Mask_2int, $Mask_2chr, $Mask_General, $Mask_General_Line,
    $ENCRYPT_QUERY_KEY;

    if (empty($formdata)) {
        $error = 'NO FORM DATA';
        return '';
    }

    if (!is_array($formdata)) {
        //then we have text only, not an array
        $formdata = explode($FORM_VAR['line_delimit'], $formdata);
    }

    $n = "\n";
    $brk = "<br />\n";

    $RESULT = array();

    if (!FromThisDomain() and $FORM_BLOCK_REFERRER) {
        $error = $FORM_VAR['referrer_error'];
        $table = '';
        return $RESULT;
    }

    if ($TBL) $table = "<table $tableoptions>\n<tbody>\n";

    $CountryDivOption = '';

    foreach($formdata as $item) {

        $field = explode('|',trim($item));
        foreach($field as $id => $value) {
            $field[$id] = trim ($value);
        }

        $kind = strtolower($field[0]);

        if (substr($kind,0,1) == '@') $kind = substr($kind,1);

        $field1 = (count($field) > 1) ? $field[1] : '';
        $field2 = (count($field) > 2) ? $field[2] : '';
        $field3 = (count($field) > 3) ? $field[3] : '';
        $field4 = (count($field) > 4) ? $field[4] : '';
        $field5 = (count($field) > 5) ? $field[5] : '';
        $field6 = (count($field) > 6) ? $field[6] : '';
        $field7 = (count($field) > 7) ? $field[7] : '';
        $field8 = (count($field) > 8) ? $field[8] : '';
        $field9 = (count($field) > 9) ? $field[9] : '';
        $field10= (count($field) > 10) ? $field[10] : '';
        $field11= (count($field) > 11) ? $field[11] : '';

        switch ($kind) {

        case 'h1':
        case 'h2':
        case 'h3':
        case 'h4':
        case 'h5':
        case 'h6':
        case 'hh1':
        case 'hh2':
        case 'hh3':
        case 'hh4':
        case 'hh5':
        case 'hh6':
        case 'fieldset': //"fieldset|title|options_fieldset|options_legend|h-type|h-options"
            if ($TBL) {
                //"hx|text|options"
                if ($kind == 'fieldset') {
                    $kind = empty($field4)? 'h3' : $field4;
                    $field2 = empty($field5)? $field3 : $field5;
                }
                if (substr($kind,0,2)=='hh') $kind = substr($kind,1);
                $option = (empty($field2))? 'style="margin:0px 3px;"' : $field2;
                $table .= "<tr><td colspan=\"2\" $tdoptions><$kind $option>$field1</$kind>$n</td></tr>$n";
            }
            break;

        case 'cell':
            //"cell|content"
            if ($TBL) $table .= "<tr><td colspan=\"2\" $tdoptions>$field1$n</td></tr>$n";
            break;

        case 'info':
            //"info|title|info|hide (=H)"
            if ($TBL and ($field3 != 'H')) $table .= "<tr><th $thoptions>$field1</th>$n<td $tdoptions>$field2</td></tr>$n";
            break;

        case 'title':
        case 'title:':
            //"title|title"
            if ($TBL) $table .= "<tr><th $thoptions>$field1</th>";
            break;

        case 'infostart':
            //"infostart"
            if ($TBL) $table .= "<td $tdoptions>";
            break;

        case 'infoend':
            //"infostart"
            if ($TBL) $table .= "</td></tr>$n";
            break;


        case 'ehiddeninfo':
        case 'ehidden':
            //"ehidden|VARNAME|value|title"
            $value = GetPostItem($field1);

            if ($value == EncryptString(@DecryptString($value, $ENCRYPT_QUERY_KEY), $ENCRYPT_QUERY_KEY)) {
                $value = DecryptString($value, $ENCRYPT_QUERY_KEY);
            }

            if (!empty($field3)) {
                $tvalue = nl2br($value);
                if (Form_OkRow($value) and $TBL) $table .= "<tr><th $thoptions>$field3</th>$n<td $tdoptions>$tvalue</td></tr>$n";
            }
            $RESULT[$field1] = $value;
            break;

        case 'hiddeninfo':
        case 'hidden':
          //"hidden|VARNAME|value|mask|title"
            $value = GetPostItem($field1);
            if (!empty($field3) and $value) if (!preg_match("/$field3/", $value)) {
                if (!empty($field4)) $error .= "$field4 {$FORM_VAR['illegal_characters']}$brk";
                else $error .= "$field1 {$FORM_VAR['illegal_characters']}$brk";
            }
            if (!empty($field4)) {
                $tvalue = nl2br($value);
                if (Form_OkRow($value) and $TBL) $table .= "<tr><th $thoptions>$field4</th>$n<td $tdoptions>$tvalue</td></tr>$n";
            }
            $RESULT[$field1] = $value;
            break;

        case 'text':
        case 'lctext':
        case 'password':
        case 'textquote':
        case 'qtext':
            //"text|title|VARNAME|required|size|min,maxlength|options|mask|hide in table(H)|aftertext",
            $value = (($kind == 'textquote') or ($kind == 'qtext')) ? Form_GetPostItemQuotes($field2) : GetPostItem($field2);
            if ($kind == 'lctext') $value=strtolower($value);

            if ($value and ($kind == 'password') and (strpos($field6, 'SECURE') !== false)) {
                if (!preg_match('/[A-Z]/', $value) or
                    !preg_match('/[a-z]/', $value) or
                    !preg_match('/[0-9]/', $value) or
                    !preg_match('/[\@\#\$\%\^\*\(\)_\+\=\{\}\[\]|\/\:\;\,\.\?~|\-]/', $value)
                ) {
                    $error .= "$field1 {$FORM_VAR['insecure_password']}$brk";
                }
            }

            Form_CheckMissing($field3, $value, '', $field1, $error);
            Form_CheckMinMax($value, $field1, $field5, $error);

            if (!empty($field7) and $value) if (!preg_match("/$field7/", $value)) $error .= "$field1 {$FORM_VAR['illegal_characters']}$brk";
            if (Form_OkRow($value) and ($field8 != 'H') and $TBL) $table .= "<tr><th $thoptions>$field1</th>$n<td $tdoptions>$value</td></tr>$n";
            $RESULT[$field2] = $value;
            break;

        case 'integer':
        case 'integerc':
            //"integer|title|VARNAME|required|size|min,max, or max|options|hide in table(H)|aftertext",  // no mask needed
            $value = intOnly(Post($FormPrefix.$field2));  // only take integer values

            Form_CheckMinMax($value, $field1, $field5, $error);
            Form_CheckMissing($field3, $value, '', $field1, $error);

            $RESULT[$field2] = $value;
            if ($kind == 'integerc') {
                $value = number_format($value, 0);
            }
            if (Form_OkRow($value) and ($field7 != 'H') and $TBL) $table .= "<tr><th $thoptions>$field1</th>$n<td $tdoptions>$value</td></tr>$n";
            break;

        case 'dollar':
        case 'dollarz':
            //"dollar|title|VARNAME|required|size|maxlength|options|aftertext",
            $value = GetPostItem($field2);

            if ($value != '') {
                $value = preg_replace('/[^0-9\.]/', '', $value);
                $decimal = (strpos($value, '.') !== false)? 2 : 0;
                $value_format = number_format($value, $decimal);
            }

            Form_CheckMissing($field3, $value, '', $field1, $error);
            if ($kind == 'dollar') {
                Form_CheckMissing($field3, $value, '0', $field1, $error);
            }
            Form_CheckMinMax($value, $field1, $field5, $error);

            if (Form_OkRow($value) and $TBL) $table .= "<tr><th $thoptions>$field1</th>$n<td $tdoptions>\$$value_format</td></tr>$n";
            $RESULT[$field2] = $value;
            break;



        case 'autocomplete':
            //"autocomplete|title|VARNAME|N|40|80|Options|functionname|url|params|Mask",
            $value = GetPostItem($field2);
            $value_shown = TransformContent(Post("AC_$FormPrefix$field2"), 'TS');
            if (empty($value_shown)) {
                $value = '';
            }
            Form_CheckMissing($field3, $value, '', $field1, $error);
            if (!empty($field10) and $value) if (!preg_match("/$field10/", $value)) $error .= "$field1 value {$FORM_VAR['illegal_characters']}$brk";
            if (Form_OkRow($value) and $TBL) $table .= "<tr><th $thoptions>$field1</th>$n<td $tdoptions>$value_shown</td></tr>$n";
            $RESULT[$field2] = $value;
            break;

        case 'creditcard':
            //"text|title|VARNAME|required|options"  //no mask needed
            $value = intOnly(GetPostItem($field2));
            Form_CheckMissing($field3, $value, '', $field1, $error);
            if ($value) {
                if (!Form_ValidCreditCardNumber($value)) $error .= "$field1 {$FORM_VAR['is_not_a_valid_number']} $brk";
                if (!Form_CheckCreditCardLength($value)) $error .= "$field1 {$FORM_VAR['has_incorrect_number_count']}$brk";

                $outputvalue = "xxxx-xxxx-xxxx-".substr($value,12,4);
            } else $outputvalue = '';
            if (Form_OkRow($value) and $TBL) $table .= "<tr><th $thoptions>$field1</th>$n<td $tdoptions>$outputvalue</td></tr>$n";
            $RESULT[$field2] = $value;
            break;

        case 'website' :
            //website|title|VARNAME|required|size|maxlength|options|aftertext
            $value   = GetPostItem($field2);
            $domain  = preg_replace('/^(http|https):\/\//', '', $value);
            $prefix  = GetPostItem($field2 . '_WEB_PREFIX');

            $validcheck = Form_CheckDomainName($domain);
            if($value and !$validcheck) $error .= "$field1 {$FORM_VAR['is_not_valid']}$brk";

            $full_domain = ($domain)? $prefix . $domain : '';
            Form_CheckMissing($field3, $value, '', $field1, $error);
            if (Form_OkRow($value) and $TBL and $validcheck) $table .= "<tr><th $thoptions>$field1</th>$n<td $tdoptions>$full_domain</td></tr>$n";
            $_POST[$FormPrefix.$field2] = $full_domain;  // update post with phone number transform
            $RESULT[$field2] = $full_domain;

            break;

        case 'phone':
            //"phone|title|VARNAME|required|options",  (mask not needed)
            $value = Form_PhoneConvert(GetPostItem($field2));
            if($value and strlen($value)< 12) $error .= "$field1 {$FORM_VAR['is_missing_numbers']}$brk";
            Form_CheckMissing($field3, $value, '', $field1, $error);
            if (Form_OkRow($value) and $TBL) $table .= "<tr><th $thoptions>$field1</th>$n<td $tdoptions>$value</td></tr>$n";
            $_POST[$FormPrefix.$field2] = $value;  // update post with phone number transform
            $RESULT[$field2] = $value;
            break;

        case 'email':
            //"email|title|VARNAME|required|size|maxlength|options"
            $value = GetPostItem($field2);
            Form_CheckMissing($field3, $value, '', $field1, $error);
            if ($value) {
                if (!CheckEmail($value)) {$error .= "$field1 {$FORM_VAR['is_not_valid']}$brk";}
            }
            if (Form_OkRow($value) and $TBL) $table .= "<tr><th $thoptions>$field1</th>$n<td $tdoptions>$value</td></tr>$n";
            $RESULT[$field2] = $value;
            break;

        case 'textarea':
        case 'qtextarea':
            //"textarea|title|VARNAME|required|cols|rows|options|mask"
            $value = ($kind == 'qtextarea') ? Form_GetPostItemQuotes($field2) : GetPostItem($field2);
            $value = GetPostItem($field2);
            Form_CheckMissing($field3, $value, '', $field1, $error);
            if (!empty($field7) and $value) if (!preg_match("/$field7/", $value)) $error .= "$field1 {$FORM_VAR['illegal_characters']}$brk";
            $tvalue = nl2br($value);
            if (Form_OkRow($value) and $TBL) $table .= "<tr><th $thoptions>$field1</th>$n<td $tdoptions>$tvalue</td></tr>$n";
            $RESULT[$field2] = $value;
            break;

        case 'html':
            //"html|title|VARNAME|required|cols|rows|mask"
            $value = Form_GetPostHTML($field2);
            Form_CheckMissing($field3, $value, '', $field1, $error);
            if (!empty($field7) and $value) if (!preg_match("/$field7/", $value)) $error .= "$field1 {$FORM_VAR['illegal_characters']}$brk";
            $tvalue = nl2br($value);
            if (Form_OkRow($value) and $TBL) $table .= "<tr><th $thoptions>$field1</th>$n<td $tdoptions>$tvalue</td></tr>$n";
            $RESULT[$field2] = $value;
            break;


        case 'select':
            //"select|title|VAR|required|options|value1=text|value2=text"
            $value = GetPostItem($field2);
            Form_CheckMissing($field3, $value, $FORM_VAR['start_select_value'], $field1, $error);
            if ($value==$FORM_VAR['start_select_value']) $value = '';

            $start_index = ($field5 == 'N')? 6 : 5;
            if (!Form_CheckListValue($value, $field, $start_index)) $value = '';

            if (Form_OkRow($value) and $TBL) $table .= "<tr><th $thoptions>$field1</th>$n<td $tdoptions>$value</td></tr>$n";
            $RESULT[$field2] = $value;
            break;


        case 'selecttext':
            //"selecttext|title|VAR|required|size|min,max or max|mask|value1|value2 . . ."
            $value = GetPostItem($field2);
            $value2 = GetPostItem("new_$field2");

            if (($value != $FORM_VAR['start_select_value']) and ($value != $FORM_VAR['new_select_text_value'])
                and !Form_CheckListValue($value, $field, 7)) {
                    $value = '';
            }

            Form_CheckMissing($field3, $value, $FORM_VAR['start_select_value'], $field1, $error);
            if ($value==$FORM_VAR['start_select_value']) {
                $value = '';
            }

            if (($field3 == 'Y') and ($value==$FORM_VAR['new_select_text_value']) and ($value2 == '')) $error .= "$field1 {$FORM_VAR['is_missing']}$brk";

            if ($value == $FORM_VAR['new_select_text_value']) {
                $value = $value2;

                Form_CheckMinMax($value, $field1, $field5, $error);
            }

            if (Form_OkRow($value) and $TBL) $table .= "<tr><th $thoptions>$field1</th>$n<td $tdoptions>$value</td></tr>$n";
            $RESULT[$field2] = $value;
            break;

        case 'selectcount':
            //"selectcount|title|VAR|required|start|end"
            $value = GetPostItem($field2);
            Form_CheckMissing($field3, $value, $FORM_VAR['start_select_value'], $field1, $error);
            if ($value==$FORM_VAR['start_select_value']) $value = '';
            $value = intOnly($value);
            if (Form_OkRow($value) and $TBL) $table .= "<tr><th $thoptions>$field1</th>$n<td $tdoptions>$value</td></tr>$n";
            $RESULT[$field2] = $value;
            break;

        case 'datecc':
            //"datecc|title|varname|required|hide=H"
            if (empty($field3)) $field3 = 'Y';
            $yearvar  = "{$field2}_YEAR";
            $monthvar = "{$field2}_MONTH";

            $value = intOnly(GetPostItem($yearvar));
            Form_CheckMissing($field3, $value, '00',"$field1 {$FORM_VAR['year']}", $error);
            $yearvar_result = $value;

            $value = intOnly(GetPostItem($monthvar));
            Form_CheckMissing($field3, $value, '00',"$field1 {$FORM_VAR['month']}", $error);

            $monthvar_result = $value;
            if (($yearvar_result==date('y')) and (intval($monthvar_result)< date('n'))) $error .= "$field1 {$FORM_VAR['has_passed']}$brk";

            if (!empty($yearvar_result) and !empty($monthvar_result)) {
                $value = "$monthvar_result$yearvar_result";
                $_POST[$FormPrefix.$field2] = $value;
            } else $value ='';
            if ($field4 != 'H') {
                if (Form_OkRow($value) and $TBL) $table .= "<tr><th $thoptions>$field1</th>$n<td $tdoptions>$value</td></tr>$n";
            }
            $RESULT[$field2] = $value;
            break;


        case 'datepick':
            //"datepick|title|varname|required|startyear|NOW|function|options|aftertext"
            $value = IntOnly(Post($FormPrefix.$field2));  // only take integer values
            Form_CheckMissing($field3, $value, '', $field1, $error);

            if ($value) {
                $value = substr($value, 0, 4) . '-' . substr($value, 4, 2) . '-' . substr($value, 6, 2);
            }

            $RESULT[$field2] = $value;

            if (Form_OkRow($value) and $TBL) $table .= "<tr><th $thoptions>$field1</th>$n<td $tdoptions>$value</td></tr>$n";
            break;

        case 'dateymd':
        case 'dateym':
            //"dateymd|title|varname|format|required|startyear|NOW"

            $field3 = strtolower($field3);
            $yearvar  = "{$field2}_YEAR";
            $monthvar = "{$field2}_MONTH";
            $dayvar   = "{$field2}_DAY";

            $yearvar_result = intOnly(GetPostItem($yearvar));
            Form_CheckMissing($field4, $yearvar_result, '', "$field1 {$FORM_VAR['year']}", $error);
            $monthvar_result = intOnly(GetPostItem($monthvar));
            Form_CheckMissing($field4, $monthvar_result, '', "$field1 {$FORM_VAR['month']}", $error);

            if ($kind == 'dateymd') {
                $dayvar_result = intOnly(GetPostItem($dayvar));
                Form_CheckMissing($field4, $dayvar_result, '', "$field1 {$FORM_VAR['day']}", $error);
            }

            if ((($kind == 'dateymd') and !empty($yearvar_result) and !empty($monthvar_result) and !empty($dayvar_result))
              or  (($kind == 'dateym') and !empty($yearvar_result) and !empty($monthvar_result))) {
                $value = str_replace('y', $yearvar_result, $field3);
                $value = str_replace('m', $monthvar_result, $value);
                if ($kind == 'dateymd') $value = str_replace('d', $dayvar_result, $value);
            } else $value ='';

            if (Form_OkRow($value) and $TBL) $table .= "<tr><th $thoptions>$field1</th>$n<td $tdoptions>$value</td></tr>$n";
            $RESULT[$field2] = $value;
            break;

        case 'time':
            //"time|title|varname|format|required|options"
            // NOTE : format not used

            $field3 = strtolower($field3);
            $hourvar  = "{$field2}_HOUR";
            $minutevar = "{$field2}_MINUTE";

            $value = intOnly(GetPostItem($hourvar));
            Form_CheckMissing($field4, $value, $FORM_VAR['start_select_value'],"$field1 {$FORM_VAR['hour']}", $error);

            if ($value==$FORM_VAR['start_select_value']) $value = '';
            $hour_result = $value;

            $value = intOnly(GetPostItem($minutevar));
            Form_CheckMissing($field4, $value, $FORM_VAR['start_select_value'],"$field1 {$FORM_VAR['minute']}", $error);

            if ($value==$FORM_VAR['start_select_value']) $value = '';
            $minute_result = $value;

            if (($hour_result != '') and ($minute_result !='')) {
                $value = "$hour_result:$minute_result";
            } else $value ='';

            if (Form_OkRow($value) and $TBL) $table .= "<tr><th $thoptions>$field1</th>$n<td $tdoptions>$value</td></tr>$n";
            $RESULT[$field2] = $value;
            break;

        case 'datetime':
            //"datetime|title|varname|required|startyear|NOW"

            $yearvar  = "{$field2}_YEAR";
            $monthvar = "{$field2}_MONTH";
            $dayvar   = "{$field2}_DAY";
            $hourvar   = "{$field2}_HOUR";
            $minutevar = "{$field2}_MINUTE";

            $yearvar_result = intOnly(GetPostItem($yearvar));
            Form_CheckMissing($field3, $yearvar_result, '', "$field1 {$FORM_VAR['year']}", $error);

            $monthvar_result = intOnly(GetPostItem($monthvar));
            Form_CheckMissing($field3, $monthvar_result, '', "$field1 {$FORM_VAR['month']}", $error);

            $dayvar_result = intOnly(GetPostItem($dayvar));
            Form_CheckMissing($field3, $dayvar_result, '', "$field1 {$FORM_VAR['day']}", $error);

            $hourvar_result = intOnly(GetPostItem($hourvar));
            Form_CheckMissing($field3, $hourvar_result,
                $FORM_VAR['start_select_value'], "$field1 {$FORM_VAR['hour']}", $error);

            $minutevar_result = intOnly(GetPostItem($minutevar));
            Form_CheckMissing($field3, $minutevar_result,
                $FORM_VAR['start_select_value'],"$field1 {$FORM_VAR['minute']}", $error);


            if (!empty($yearvar_result) and !empty($monthvar_result) and !empty($dayvar_result)
                and !empty($hourvar_result) and !empty($minutevar_result)) {
                $value = "$yearvar_result-$monthvar_result-$dayvar_result $hourvar_result:$minutevar_result:00";
            } else {
                $value ='';
            }

            if (Form_OkRow($value) and $TBL) $table .= "<tr><th $thoptions>$field1</th>$n<td $tdoptions>$value</td></tr>$n";
            $RESULT[$field2] = $value;
            break;


        case 'country':
            //"country|title|VAR|required"
            $value = GetPostItem($field2);
            Form_CheckMissing($field3, $value, $FORM_VAR['start_select_value'], $field1, $error);
            if ($value==$FORM_VAR['start_select_value']) $value = '';
            $cname = GetCountryNameOrCode($value);
            if (empty($cname)) {
                $value = '';
            }
            if (Form_OkRow($value) and $TBL) $table .= "<tr><th $thoptions>$field1</th>$n<td $tdoptions>$cname</td></tr>$n";
            $RESULT[$field2] = $value;
            break;

        case 'countrystate':
            //"countrystate|title|VARcountry:VARstate|required|options"
            list($country_var, $state_var) = explode(':', $field2);
            $country_value = GetPostItem($country_var);
            $value = GetPostItem($state_var);
            if (empty($value)) {
                if ($country_value == 'US') {
                    $value = GetPostItem('US_STATES_' . $state_var);
                } elseif ($country_value == 'CA') {
                    $value = GetPostItem('CANADA_PROVINCES_' . $state_var);
                } elseif ($country_value != $FORM_VAR['start_select_value']) {
                    $value = GetPostItem('OTHER_STATES_' . $state_var);
                }
            }
            if ($value==$FORM_VAR['start_select_value']) {
                $value = '';
            }
            if ($country_value == 'US') {
                //---- process US state ----
                Form_CheckMissing($field3, $value, $FORM_VAR['start_select_value'], $FORM_VAR['state'], $error);
                if (!in_array($value, $FORM_STATE_CHAR_CODES)) {
                    $value = '';
                }
                if (Form_OkRow($value) and $TBL) $table .= "<tr><th $thoptions>{$FORM_VAR['state']}</th>$n<td $tdoptions>$value</td></tr>$n";
                $RESULT[$state_var] = $value;

            } elseif ($country_value == 'CA') {
                //---- process Canada Province ----
                Form_CheckMissing($field3, $value, $FORM_VAR['start_select_value'], $FORM_VAR['canada_province'], $error);

                if (!in_array($value, $FORM_CANADA_PROVINCES)) {
                    $value = '';
                }

                if (Form_OkRow($value) and $TBL) $table .= "<tr><th $thoptions>{$FORM_VAR['canada_province']}</th>$n<td $tdoptions>$value</td></tr>$n";
                $RESULT[$state_var] = $value;

            } elseif($country_value != $FORM_VAR['start_select_value']) {
                //---- process Other State/Province ----
                Form_CheckMissing($field3, $value, '', $FORM_VAR['state_province'], $error);
                if (!empty($value) and (!preg_match("/$Mask_General_Line/", $value))) $error .= "{$FORM_VAR['state_province']} {$FORM_VAR['illegal_characters']}$brk";
                if (Form_OkRow($value) and $TBL) $table .= "<tr><th $thoptions>{$FORM_VAR['state_province']}</th>$n<td $tdoptions>$value</td></tr>$n";
                $RESULT[$state_var] = $value;
            }

            //---- process country ----
            $value = $country_value;
            Form_CheckMissing($field3, $value, $FORM_VAR['start_select_value'], $field1, $error);
            if ($value==$FORM_VAR['start_select_value']) $value = '';
            $cname = GetCountryNameOrCode($value);
            if (empty($cname)) {
                $value = '';
            }
            if (Form_OkRow($value) and $TBL) $table .= "<tr><th $thoptions>$field1</th>$n<td $tdoptions>$cname</td></tr>$n";
            $RESULT[$country_var] = $value;
            break;

            break;

        case 'state':
            //"state|title|VAR|required|US"
            $value = GetPostItem($field2);
            Form_CheckMissing($field3, $value, $FORM_VAR['start_select_value'], $field1, $error);

            if (!in_array($value, $FORM_STATE_CHAR_CODES)) {
                $value = '';
            }
            if (Form_OkRow($value) and $TBL) $table .= "<tr><th $thoptions>$field1</th>$n<td $tdoptions>$value</td></tr>$n";
            $RESULT[$field2] = $value;
            break;


        case 'intstate':
            //"intstate|title|VAR|required|options|countryid"
            $value = GetPostItem($field2);

            if (!in_array($value, $FORM_STATE_CHAR_CODES)) {
                $value = '';
            }

            $intvalue = GetPostItem("INT_$field2");

            if (($value=='INT') and ($intvalue == '')) {
                Form_CheckMissing($field3, $value, 'INT', "$field1 {$FORM_VAR['international_entry']}", $error);
            }
            if ($value == 'INT') $value = $intvalue;
            if (Form_OkRow($value) and $TBL) $table .= "<tr><th $thoptions>$field1</th>$n<td $tdoptions>$value</td></tr>$n";
            $RESULT[$field2] = $value;
            break;


        case 'radio':
        case 'radioh':
            //"radio|title|VAR|required|options|value1=text|value2=text"
            $value = GetPostItem($field2);
            for($i=5; $i<count($field); $i++) {
                $itemvalue = trim(strTo($field[$i], '='));
                $itemtext  = trim(strFrom($field[$i], '='));
                if (empty($itemtext)) {
                    $itemtext = $itemvalue;
                }

                if ($itemvalue == $value) $avalue = $itemtext;
            }
            if (empty($avalue)) {
                $value = '';
            }
            Form_CheckMissing($field3, $value, '', $field1, $error);

            if (Form_OkRow($value) and $TBL) $table .= "<tr><th $thoptions>$field1</th>$n<td $tdoptions>$avalue</td></tr>$n";
            $RESULT[$field2] = $value;
            break;


        case 'checkboxlist':
            //"checkboxlist|title|options|value1=text|value2=text"
            for($i=3; $i<count($field); $i++) {

                $itemvalue = trim(strTo($field[$i], '='));
                $itemtext  = trim(strFrom($field[$i], '='));
                if (empty($itemtext)) {
                    $itemtext = $itemvalue;
                }

                $itemname = preg_replace('/[^a-zA-Z0-9]/', '_', $itemtext);
                $value = GetPostItem($itemname);
                if (!empty($value) and ($value != $itemvalue)) $error .= "$field1 {$FORM_VAR['illegal_characters']}$brk";
                $checked = ($value)? 'Checked' : '';
                if (Form_OkRow($value) and $TBL) $table .= "<tr><th $thoptions>$itemtext</th>$n<td $tdoptions>$checked</td></tr>$n";
                $RESULT[$itemname] = $value;
            }
            break;

        case 'checkboxlistbar':
        case 'checkboxlistbarh':
            //"checkboxlistbar|title|var|Y|options|value1=text|value2=text"
            $item_list = '';
            $have_checked_item = 0;
            for($i=5; $i<count($field); $i++) {
                $itemvalue = trim(strTo($field[$i], '='));
                $itemtext  = trim(strFrom($field[$i], '='));
                if (empty($itemtext)) {
                    $itemtext = $itemvalue;
                }
                $count = $i-5;
                $itemname = "$field2$count";
                $value = GetPostItem($itemname);
                if (!empty($value) and ($value != $itemvalue)) $error .= "$field1 {$FORM_VAR['illegal_characters']}$brk";
                $item_result = ($value)? $itemvalue : '';
                if ($item_result) $have_checked_item = 1;
                if (Form_OkRow($value) and $TBL) $table .= "<tr><th $thoptions>$itemtext</th>$n<td $tdoptions>$item_result</td></tr>$n";
                $item_list .= "$item_result|";
            }
            Form_CheckMissing($field3, $item_result, 0, $field1, $error);

            $RESULT[$field2] = substr($item_list,0,-1);
            break;


        case 'checkboxlistset':
        case 'checkboxlistseth':
            //"checkboxlistset|title|var|Y|options|value1=text|value2=text"
            $item_list = '';
            $item_array = array();
            $have_checked_item = 0;
            for($i=5; $i<count($field); $i++) {
                $itemvalue = trim(strTo($field[$i], '='));
                $itemtext  = trim(strFrom($field[$i], '='));
                if (empty($itemtext)) {
                    $itemtext = $itemvalue;
                }
                $count = $i-5;
                $itemname = "$field2$count";
                $value = GetPostItem($itemname);
                if (!empty($value) and ($value != $itemvalue)) $error .= "$field1 {$FORM_VAR['illegal_characters']}$brk";
                $item_result = ($value)? $itemvalue : '';
                if ($item_result) {
                    $have_checked_item = 1;
                    $item_array[] = $item_result;
                }
            }
            $item_list = implode(',', $item_array);

            if (Form_OkRow($value) and $TBL and $have_checked_item) {
                $table .= "<tr><th $thoptions>$field1</th>$n<td $tdoptions>$item_list</td></tr>$n";
            }

            Form_CheckMissing($field3, $have_checked_item, 0, $field1, $error);

            $RESULT[$field2] = $item_list;
            break;

        case 'checkbox':
            //"checkbox|title|varname|options|value|value-null"
            $value = GetPostItem($field2);
            if (!empty($value) and ($value != $field4)) $error .= "$field1 {$FORM_VAR['illegal_characters']}$brk";
            if ($value != $field4) $value = $field5;
            if (Form_OkRow($value) and $TBL) $table .= "<tr><th $thoptions>$field1</th>$n<td $tdoptions>$value</td></tr>$n";
            $RESULT[$field2] = $value;
            break;
        }
    }
    if ($FORM_SHOW_POSTED) {
        if ($TBL) {
            $date = date($FORM_VAR['form_date_code'], time()+($FORM_TIME_SHIFT * 3600));
            $table .= "<tr><th colspan=\"2\" $thoptions><div style=\"{$FORM_VAR['posted_cell_style']}\">Posted: $date</div></th></tr>\n";
        }
    }
    if($TBL) $table .= "</tbody>\n</table>\n";
    return $RESULT;
}

//===============================================================================
//                               OUTPUT FORM
//===============================================================================

function Form_GetTitleCode($title, $title_template) {
    if (empty($title_template)) {
        return $title;
    } else {
        if (empty($title)) {
            return str_replace(array('@:', '@'), '', $title_template);
        } else {
            return str_replace('@', $title, $title_template);
        }
    }
}

function Form_GetInfoCode($info, $info_template) {
    if (empty($info_template)) {
        return $info;
    } else {
        return str_replace('@', $info, $info_template);
    }
}


function OutputForm($formdata, $process=0)
{
    global $FormPrefix, $FORM_VAR, $FORM_MONTHS, $FORM_COUNTRY_CODES, $FORM_STATE_CODES, $FORM_STATE_CHAR_CODES,
           $FORM_CANADA_PROVINCES, $FORM_CREDITCARD_LENGTH, $ENCRYPT_QUERY_KEY;

    $n = "\n";

    $JavaScript = '';
    $javascript_functions = array();

    $newdatecodes = array('yyyy', 'mm', 'dd');
    $olddatecodes = array('y', 'm', 'd');

    $SELECT_START = "<option value=\"{$FORM_VAR['start_select_value']}\">{$FORM_VAR['start_select']}</option>$n";

    $TitleTemplateStd = $FORM_VAR['title_template'];
    $InfoTemplateStd  = $FORM_VAR['info_template'];

    if (empty($formdata)) {
        $error = 'NO FORM DATA';
        return '';
    }

    if (!is_array($formdata)) {
        //then we have text only, not an array
        $formdata = explode($FORM_VAR['line_delimit'], $formdata);
    }

    $RESULT = '';
    foreach($formdata as $item) {
        $field = explode('|', trim($item));
        $kind = strtolower(trim($field[0]));

        if (substr($kind,0,1) == '!') {
            $kind = substr($kind,1);
        }

        if (substr($kind,0,1) == '@') {
            $kind = substr($kind,1);
            $TitleTemplate = '';
            $InfoTemplate  = '';
        } else {
            $TitleTemplate = $TitleTemplateStd;
            $InfoTemplate  = $InfoTemplateStd;
        }

        $field1 = (count($field) > 1) ? trim($field[1]) : '';
        $field2 = (count($field) > 2) ? trim($field[2]) : '';
        $field3 = (count($field) > 3) ? trim($field[3]) : '';
        $field4 = (count($field) > 4) ? trim($field[4]) : '';
        $field5 = (count($field) > 5) ? trim($field[5]) : '';
        $field6 = (count($field) > 6) ? trim($field[6]) : '';
        $field7 = (count($field) > 7) ? trim($field[7]) : '';
        $field8 = (count($field) > 8) ? trim($field[8]) : '';
        $field9 = (count($field) > 9) ? trim($field[9]) : '';
        $field10= (count($field) > 10)? trim($field[10]) : '';
        $field11= (count($field) > 11)? trim($field[11]) : '';

        $alt = strip_tags($field1);

        switch ($kind) {
/*<formitem>
<def>titletemplate|template-code</def>
<test><code>titletemplate|<div>@</div></code></test>
<test><code>titletemplate|STD</code></test>
</formitem>*/
        case 'titletemplate':
            if ($field1 == 'STD') {
                $TitleTemplateStd = $FORM_VAR['title_template'];
            } else {
                if (strpos($field1, '@') === false) {
                    echo WriteErrorText('<h3>FROM TITLE TEMPLATE ERROR: NO @</h3>');
                    return;
                }
                $TitleTemplateStd = $field1;
            }
            break;
/*<formitem>
<def>infotemplate|template-code</def>
<test><code>infotemplate|<div>@</div></code></test>
<test><code>infotemplate|STD</code></test>
</formitem>*/

        case 'infotemplate':
            if ($field1 == 'STD') {
                $InfoTemplateStd = $FORM_VAR['info_template'];
            } else {
                if (strpos($field1, '@') === false) {
                    echo WriteErrorText('<h3>FROM INFO TEMPLATE ERROR: NO @</h3>');
                    return;
                }
                $InfoTemplateStd = $field1;
            }
            break;

        case 'code':
        case 'cell':
            //"code|text"
            $RESULT .= "$field1$n";
            break;

        case 'js':
            //"js|code"
            $JavaScript .= "$field1$n";
            break;


/*<formitem>
<def>form|action|method|name</def>
<test><code>form|@@PAGELINK@@|post|formname</code></test>
</formitem>*/
        case 'form':
            //"form|action|method|name"
            $name = ($field3)? " name=\"$field3\"" : '';
            $id   = ($field3)? " id=\"$field3\"" : '';
            $RESULT .= "<form action=\"$field1\" method=\"$field2\" {$FORM_VAR['accept_charset']}$id$name>$n";
            break;


        case 'info':
            //"info|title|info"
            if (!$field2) {
                $field2 = '&nbsp;';
            }
            $RESULT .= Form_GetTitleCode($field1, $TitleTemplate);
            $RESULT .= Form_GetInfoCode($field2, $InfoTemplate);
            break;

        case 'title':    // these are experimental
            //"title|title"
            $RESULT .= Form_GetTitleCode($field1, $TitleTemplate);
            break;

        case 'infostart':
            //"infostart"
            $RESULT .= strTo($InfoTemplate, '@@VAR@@');
            break;

        case 'infoend':
            //"infostart"
            $RESULT .= strFrom($InfoTemplate, '@@VAR@@');
            break;
/*<formitem>
<def>fieldset|title|options</def>
<test>
    <code>fieldset|title|style="background-color:#f77"</code>
</test>
<test>
    <code>endfieldset</code>
</test>
</formitem>*/


        case 'fieldset':
            //"fieldset|title|options_fieldset|options_legend"
            $option1 = ($field2)? " $field2" : '';
            $option2 = ($field3)? " $field3" : '';
            $RESULT .= "<fieldset$option1>$n<legend$option2>$field1</legend>$n";
            break;
        case 'endfieldset':
            //"endfieldset"
            $RESULT .= "</fieldset>$n";
            break;

/*<formitem>
<def>hx|text|options</def>
<test>
    <code>h2|heading-2|style="background-color:#000; color:#fff;"</code>
</test>
</formitem>*/

        case 'h1':
        case 'h2':
        case 'h3':
        case 'h4':
        case 'h5':
        case 'h6':
            //"hx|text|options"
            $options = ($field2)? " $field2" : '';
            $RESULT .= "<$kind$options>$field1</$kind>$n";
            break;




        case 'button':
            //"button|title|onclick|options"
            $formitem = "<input type=\"button\" class=\"formsubmit\" value=\"$field1\" onclick=\"$field2\" $field3 />$n";
            $RESULT .= Form_GetInfoCode($formitem, $InfoTemplate);
            break;

/*<formitem>
<def>ehidden|VARNAME|value|title</def>
<test>
    <code>ehidden|eHidden|12.00|Hidden Test Initial Value</code>
    <testvalue></testvalue><testvar></testvar>
</test>
<test>
    <code>ehidden|eHidden2||Hidden Test No Initial Value</code>
    <testvalue>25.00</testvalue><testvar>eHidden2</testvar>
</test>
</formitem>*/

        case 'hidden':
        case 'hiddeninfo':
        case 'ehidden':
        case 'ehiddeninfo':
            //"hidden|VARNAME|value|mask|title"
            //"ehidden|VARNAME|value|title"
            if (!$field2) {
                $field2 = GetPostItem($field1);
            }

            if ($kind == 'ehidden' || $kind == 'ehiddeninfo') {
                // need to determine if already encrypted
                if ($field2 != EncryptString(@DecryptString($field2, $ENCRYPT_QUERY_KEY), $ENCRYPT_QUERY_KEY)) {
                    $field2 = EncryptString($field2, $ENCRYPT_QUERY_KEY);
                }
            }
            if ($kind == 'hiddeninfo' || $kind == 'ehiddeninfo') {
                $title = ($kind == 'hiddeninfo')? $field4 : $field3;
                $RESULT .= Form_GetTitleCode($title, $TitleTemplate);
                $RESULT .= Form_GetInfoCode($field2, $InfoTemplate);
            }

            $id = Form_GetIdFromVar($field1);
            $RESULT .= "<input type=\"hidden\" id=\"$id\" name=\"$FormPrefix$field1\" value=\"$field2\" />$n$n";
            break;

/*<formitem>
<def>text|title|VARNAME|required|size|maxlength|options|mask|hide in table(H)|aftertext</def>
<test>
    <code>text|text|text1|N|20|10,30|style="background-color:#080; color:#fff;"|||aftertext</code>
    <testvalue>This is a test of long text to see what will happen</testvalue><testvar>test1</testvar>
</test>
</formitem>*/

        case 'text':
        case 'lctext':
        case 'textquote':
        case 'qtext':
            //"text|title|VARNAME|required|size|maxlength|options|mask|hide in table(H)|aftertext",
            $field1 = Form_RequiredCheck($field3, $process)? "{$FORM_VAR['required_text']}$field1" : $field1;
            $RESULT .= Form_GetTitleCode($field1, $TitleTemplate);
            $value = (($kind == 'textquote') or ($kind == 'qtext')) ? Form_GetPostItemQuotes($field2) : GetPostItem($field2);

            $error = ($process and ((Form_RequiredCheck($field3, $process) and !$value) or
                   (!empty($field7) and $value and !preg_match("/$field7/", $value))));

            $class =  ($error)? 'formitemerror' : 'formitem';
            $class2 = TextBetween('class="', '"', $field6);
            if ($class2) {
                $field6 = trim(str_replace('class="' . $class2 . '"', '', $field6));
                $class2 = " $class2";
            }
            $options = ($field6)? " $field6" : '';

            $maxlen = ($field5)? ' maxlength="' . Form_GetMax($field5) . '"' : '';
            $id = Form_GetIdFromVar($field2);
            $formitem = "<input type=\"text\" alt=\"$alt\" id=\"$id\" class=\"$class$class2\" name=\"$FormPrefix$field2\" size=\"$field4\"$maxlen value=\"$value\"$options />$field9$n";
            $RESULT .= Form_GetInfoCode($formitem, $InfoTemplate);
            break;

        case 'integer':
        case 'integerc':
            //"integer|title|VARNAME|required|size|maxlength|options|hide in table(H)|aftertext",  // no mask needed

            $field1 = Form_RequiredCheck($field3, $process)? "{$FORM_VAR['required_text']}$field1" : $field1;
            $RESULT .= Form_GetTitleCode($field1, $TitleTemplate);
            $options = ($field6)? " $field6" : '';
            $value = intOnly(Post($FormPrefix.$field2));  // only take integer values
            if ($kind == 'integerc') {
                $value = number_format($value, 0);
                $javascript_functions['formatIntegerObj'] = "
                function formatIntegerObj(obj) {
                    var num = obj.value;
                    num = num.replace(/[^0-9]/g, '');
                    var objRegExp  = new RegExp('(-?[0-9]+)([0-9]{3})');
                    while(objRegExp.test(num)) {
                       num = num.replace(objRegExp, '$1,$2');
                    }
                    obj.value = num;
                }";
                $options = trim($options . ' onkeyup="formatIntegerObj(this);"');
            }
            $error = ($process and (Form_RequiredCheck($field3, $process) and !$value));
            $class =  ($error)? 'formitemerror' : 'formitem';

            $maxlen = ($field5)? ' maxlength="' . Form_GetMax($field5) . '"' : '';

            $id = Form_GetIdFromVar($field2);
            $formitem = "<input type=\"text\" alt=\"$alt\" id=\"$id\" class=\"$class\" name=\"$FormPrefix$field2\" size=\"$field4\"$maxlen value=\"$value\"$options />$field8$n";
            $RESULT .= Form_GetInfoCode($formitem, $InfoTemplate);
            break;
/*<formitem>
<def>dollar|title|VARNAME|required|size|maxlength|options|aftertext</def>
<test>
    <code>dollar|Dollar|dollar1|N|10|10||aftertext</code>
    <testvalue>12,000</testvalue><testvar>dollar1</testvar>
</test>
<test>
    <code>dollar|Dollar|dollar2|N|10|10||aftertext</code>
    <testvalue>12000</testvalue><testvar>dollar2</testvar>
</test>
</formitem>*/
        case 'dollar':
        case 'dollarz':  // dollar z allows zeros
            //"dollar|title|VARNAME|required|size|maxlength|options|aftertext",
            $field1 = Form_RequiredCheck($field3, $process)? "{$FORM_VAR['required_text']}$field1" : $field1;
            $RESULT .= Form_GetTitleCode($field1, $TitleTemplate);
            $options = ($field6)? " $field6" : '';
            $value = GetPostItem($field2);
            if ($value != '') {
                $decimal = (strpos($value, '.') !== false)? 2 : 0;
                $value = number_format( preg_replace('/[^0-9\.]/', '', $value), $decimal);
            }

            $javascript_functions['formatRealObj'] = "
function formatRealObj(obj) {
    var num = obj.value;
    if (num == '.') num = '0.';
    num = num.replace(/[^0-9\.-]/g, '');
    if (parseFloat(num) != num) {
        num = num.substring(0, num.length-1);
    }
    var parts = num.split('.');
    var base = parts[0];
    var rem  = (parts[1] != null)? '.' + parts[1] : '';
    var objRegExp  = new RegExp('(-?[0-9]+)([0-9]{3})');
    while(objRegExp.test(base)) {
       base = base.replace(objRegExp, '$1,$2');
    }
    obj.value = base + rem;
}";
            $options = trim($options . ' onkeyup="formatRealObj(this);"');

            $error = ($process and (Form_RequiredCheck($field3, $process) and !$value));

            $class =  ($error)? 'formitemerror' : 'formitem';
            $id = Form_GetIdFromVar($field2);
            $formitem = "$&nbsp;<input type=\"text\" alt=\"$alt\" id=\"$id\" class=\"$class\" name=\"$FormPrefix$field2\" size=\"$field4\" maxlength=\"$field5\" value=\"$value\"$options />$field7$n";
            $RESULT .= Form_GetInfoCode($formitem, $InfoTemplate);
            break;



        case 'autocomplete':
            //"autocomplete|title|VARNAME|N|40|80|Options|functionname|url|completionfunction|Mask|aftertext",
            $field1 = Form_RequiredCheck($field3, $process)? "{$FORM_VAR['required_text']}$field1" : $field1;
            $RESULT .= Form_GetTitleCode($field1, $TitleTemplate);
            $options = ($field6)? " $field6" : '';
            $value  = GetPostItem($field2);
            $value2 = TransformContent(Post("AC_$FormPrefix$field2"), 'TS');
            if ($value and !$value2) {
                $value2 = FORM_GetAutocompleteValue($field8, $value);
            }

            $error = ($process and ((Form_RequiredCheck($field3, $process) and !$value) or
                   (!empty($field10) and $value and !preg_match("/$field10/", $value))));

            $class =  ($error)? 'formitemerror' : 'formitem';
            $id = Form_GetIdFromVar($field2);
            $formitem  = "<input type=\"text\" alt=\"$alt\" id=\"AC_$id\" class=\"$class ac_input\" name=\"AC_$FormPrefix$field2\" size=\"$field4\" maxlength=\"$field5\" value=\"$value2\"$options />$field11$n";
            $formitem .= "$n<input type=\"hidden\" id=\"$id\" name=\"$FormPrefix$field2\" value=\"$value\" />$n";
            $RESULT .= Form_GetInfoCode($formitem, $InfoTemplate);
            $complete_function = ($field9)? $field9 : "''";
            $JavaScript .= "$field7('AC_$id','$id','$field8',$complete_function);$n";

            break;



        case 'creditcard':
            //"creditcard|title|VARNAME|required|options|aftertext"  //no mask needed
            $field1 = Form_RequiredCheck($field3, $process)? "{$FORM_VAR['required_text']}$field1" : $field1;
            $RESULT .= Form_GetTitleCode($field1, $TitleTemplate);
            $options = ($field)? " $field4" : '';
            $value = intOnly(GetPostItem($field2));
            $validcheck = Form_ValidCreditCardNumber($value);
            $class =  (Form_RequiredCheck($field3, $process) and ($process) and (!$value or !$validcheck))? 'formitemerror' : 'formitem';
            $id = Form_GetIdFromVar($field2);
            $formitem = "<input type=\"text\" alt=\"$alt\" id=\"$id\" class=\"$class\" name=\"$FormPrefix$field2\" size=\"$FORM_CREDITCARD_LENGTH\" maxlength=\"$FORM_CREDITCARD_LENGTH\" value=\"$value\"$options />$field5$n";
            $RESULT .= Form_GetInfoCode($formitem, $InfoTemplate);
            break;

/*<formitem>
<def>phone|title|VARNAME|required|options|aftertext</def>
<test>
    <code>phone|phone1|phone1|N||aftertext</code>
    <testvalue>123-333-1234</testvalue><testvar>phone1</testvar>
</test>
</formitem>*/

        case 'website' :
            //website|title|VARNAME|required|size|maxlength|options|aftertext
            $field1  = Form_RequiredCheck($field3, $process)? "{$FORM_VAR['required_text']}$field1" : $field1;
            $RESULT .= Form_GetTitleCode($field1, $TitleTemplate);
            $options = ($field6)? " $field6" : '';
            $value   = GetPostItem($field2);
            if (substr($value, 0, 4) == 'http') {
                $prefix  = strTo($value, '://') . '://';
            } else {
                $prefix  = GetPostItem($field2 . '_WEB_PREFIX');
            }
            $domain  = preg_replace('/^(http|https):\/\//', '', $value);

            $validcheck = Form_CheckDomainName($domain); //<<<<<<<<<<---------- FIX ----------<<<<<<<<<<
            $class =  (Form_RequiredCheck($field3, $process) and ($process) and (!$value or !$validcheck))? 'formitemerror' : 'formitem';
            $s1 = (($prefix == 'http://') or empty($prefix))? ' selected="selected"' : '';
            $s2 = ($prefix == 'https://')? ' selected="selected"' : '';
            $id = Form_GetIdFromVar($field2);
            $formitem = '<select class="' . $class .'" name="' . $FormPrefix . $field2 . '_WEB_PREFIX' .
                '"><option' . $s1 .'>http://</option><option' . $s2 .'>https://</option></select>';
            $formitem .= "&nbsp;<input type=\"text\" alt=\"$alt\" id=\"$id\" class=\"$class\" name=\"$FormPrefix$field2\" size=\"$field4\" maxlength=\"$field5\" value=\"$domain\"$options />$field7$n";
            $RESULT .= Form_GetInfoCode($formitem, $InfoTemplate);
        break;

/*<formitem>
<def>website|title|VARNAME|required|size|maxlength|options|aftertext</def>
<test>
    <code>website|Website|website1|N|40|255||</code>
    <testvalue>https://mvpprograms.com</testvalue><testvar>website1</testvar>
</test>
</formitem>*/

        case 'phone':
            //"phone|title|VARNAME|required|options|aftertext",  (mask not needed)
            $field1 = Form_RequiredCheck($field3, $process)? "{$FORM_VAR['required_text']}$field1" : $field1;
            $RESULT .= Form_GetTitleCode($field1, $TitleTemplate);
            $options = ($field4)? " $field4" : '';
            $value = Form_PhoneConvert(GetPostItem($field2));
            $class =  (Form_RequiredCheck($field3, $process) and ($process) and ((!$value) or ($value and strlen($value)< 12)))? 'formitemerror' : 'formitem';
            $id = Form_GetIdFromVar($field2);
            $formitem = "<input type=\"text\" alt=\"$alt\" id=\"$id\" class=\"$class\" name=\"$FormPrefix$field2\" size=\"12\" maxlength=\"20\" value=\"$value\"$options />$field5$n";
            $RESULT .= Form_GetInfoCode($formitem, $InfoTemplate);
            break;


        case 'email':
        case 'lcemail':
            //"email|title|VARNAME|required|size|maxlength|options|aftertext"
            $field1 = Form_RequiredCheck($field3, $process)? "{$FORM_VAR['required_text']}$field1" : $field1;
            $options = ($field6)? " $field6" : '';
            $RESULT .= Form_GetTitleCode($field1, $TitleTemplate);
            $value = GetPostItem($field2);
            if ($field3 == 'Y') {
                $BadEmail = !CheckEmail($value);
            }
            $class =  (Form_RequiredCheck($field3, $process) and ($process) and ($BadEmail))? 'formitemerror' : 'formitem';
            $id = Form_GetIdFromVar($field2);
            $formitem = "<input type=\"text\" alt=\"$alt\" id=\"$id\" class=\"$class\" name=\"$FormPrefix$field2\" size=\"$field4\" maxlength=\"$field5\" value=\"$value\"$options />$field7$n";
            $RESULT .= Form_GetInfoCode($formitem, $InfoTemplate);
            break;

        case 'textarea':
        case 'qtextarea':
            //"textarea|title|VARNAME|required|cols|rows|options",
            $field1 = Form_RequiredCheck($field3, $process)? "{$FORM_VAR['required_text']}$field1" : $field1;
            $options = ($field6)? " $field6" : '';
            $RESULT .= Form_GetTitleCode($field1, $TitleTemplate);
            $value = ($kind == 'qtextarea') ? Form_GetPostItemQuotes($field2) : GetPostItem($field2);
            $class =  (Form_RequiredCheck($field3, $process) and ($process) and (!$value))? 'formitemerror' : 'formitem';
            $id = Form_GetIdFromVar($field2);
            $formitem = "<textarea class=\"$class\" id=\"$id\" name=\"$FormPrefix$field2\" cols=\"$field4\" rows=\"$field5\"$options>$value</textarea>$n";
            $RESULT .= Form_GetInfoCode($formitem, $InfoTemplate);
            break;
/*<formitem>
<def>html|title|VARNAME|required|cols|rows|options</def>
<test>
    <code>html|html|html|N|40|4|onchange="this.style.backgroundColor='#f77';"</code>
    <testvalue><h1>"test &amp; test"</h1></testvalue><testvar>html</testvar>
</test>
</formitem>*/
        case 'html':
            //"html|title|VARNAME|required|cols|rows|options",
            $field1 = Form_RequiredCheck($field3, $process)? "{$FORM_VAR['required_text']}$field1" : $field1;
            $options = ($field6)? " $field6" : '';
            $RESULT .= Form_GetTitleCode($field1, $TitleTemplate);
            $value = '';
            if (!empty($_POST[$FormPrefix.$field2])) {
                //$value = Form_GetPostHTML($field2);
                $value = trim($_POST[$FormPrefix.$field2]);
                //$value = htmlspecialchars_decode(trim($_POST[$FormPrefix.$field2]));
            }
            $outtext = htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
            $class =  (Form_RequiredCheck($field3, $process) and ($process) and (!$value))? 'formitemerror' : 'formitem';
            $id = Form_GetIdFromVar($field2);
            $formitem = "<textarea class=\"$class\" id=\"$id\" name=\"$FormPrefix$field2\" cols=\"$field4\" rows=\"$field5\"$options>$outtext</textarea>$n";
            $RESULT .= Form_GetInfoCode($formitem, $InfoTemplate);
            break;


        case 'datecc':
            //"datecc|title|varname|required|hide"
            if (empty($field3)) $field3='Y';
            $field1 = Form_RequiredCheck($field3, $process)? "{$FORM_VAR['required_text']}$field1" : $field1;
            $RESULT .= Form_GetTitleCode($field1, $TitleTemplate);

            $yearvar  = "{$field2}_YEAR";
            $monthvar = "{$field2}_MONTH";
            $value = GetPostItem($field2);

            if ($value) {
                $yearvalue  = substr($value,2);
                $monthvalue = substr($value,0,2);
            }

            //----month----
            $monthvalue = ($value)? $monthvalue : GetPostItem($monthvar);
            $yearvalue = ($value)? $yearvalue : GetPostItem($yearvar);

            $class =  ($process and ($monthvalue=='00') and Form_RequiredCheck($field3, $process))? 'formitemerror' : 'formitem';
            if (($yearvalue==date('y')) and (intval($monthvalue)< date('n'))) $class = 'formitemerror';
            $id = Form_GetIdFromVar($monthvar);
            $formitem =  "{$FORM_VAR['month']} <select id=\"$id\" class=\"$class\" name=\"$FormPrefix$monthvar\">$n";
            $formitem .= "<option value=\"00\">{$FORM_VAR['start_select']}</option>$n";
            for($i=1; $i<=12; $i++) {
                $selected = ($monthvalue == $i)? ' selected="selected"' : '';
                $optvalue = sprintf('%02d', $i);
                $formitem .=  "<option value=\"$optvalue\"$selected>$optvalue - {$FORM_MONTHS[$i-1]}</option>$n";
            }
            $formitem .=  "</select>$n";

            //----year----
            $startyear = date('Y');
            $endyear   = date('Y', strtotime('+9 year'));

            $class =  (($process) and ($yearvalue=='00') and Form_RequiredCheck($field3, $process))? 'formitemerror' : 'formitem';
            if (($yearvalue==date('y')) and (intval($monthvalue)< date('n'))) $class = 'formitemerror';

            $id = Form_GetIdFromVar($yearvar);
            $formitem .= "{$FORM_VAR['year']} <select id=\"$id\" class=\"$class\" name=\"$FormPrefix$yearvar\"$options>$n";
            $formitem .= "<option value=\"00\">{$FORM_VAR['start_select']}</option>$n";
            for($i=$startyear; $i<=$endyear; $i++) {
                $optvalue = substr($i,2);
                $selected = ($yearvalue == $optvalue)? ' selected="selected"' : '';
                $formitem .= "<option value=\"$optvalue\"$selected>$i</option>$n";
            }
            $formitem .=  "</select>$n";

            $RESULT .= Form_GetInfoCode($formitem, $InfoTemplate);
            break;

        case 'datepick':
            //"datepick|title|varname|required|startyear|NOW|function|options|aftertext"

            $field1 = Form_RequiredCheck($field3, $process)? "{$FORM_VAR['required_text']}$field1" : $field1;
            $RESULT .= Form_GetTitleCode($field1, $TitleTemplate);
            $value = GetPostItem($field2);

            $now = date('Y');
            $startyear = ($field4 == 'NOW')? $now : $field4;
            $endyear   = ($field5 == 'NOW')? $now : $field5 + $startyear;
            $startyear = $startyear - $now;
            $endyear   = $endyear - $now;

            $error = ($process and (Form_RequiredCheck($field3, $process) and !$value));
            $class =  ($error)? 'formitemerror datepick' : 'formitem datepick';
            $options = ($field7)? " $field7" : '';
            $id = Form_GetIdFromVar($field2);
            $formitem = "<input type=\"text\"  id=\"$id\" class=\"$class\" name=\"$FormPrefix$field2\" size=\"12\" maxlength=\"12\" value=\"$value\"$options />$field8$n";
            $RESULT .= Form_GetInfoCode($formitem, $InfoTemplate);
            $JavaScript .= "$field6('$FormPrefix$field2', $startyear, $endyear);$n";
            break;

        case 'dateymd':
        case 'dateym':
        //"dateYMD|title|varname|format|required|startyear|NOW|options|aftertext"
        //"dateYD|title|varname|format|required|startyear|NOW|options|aftertext"
/*<formitem>
<def>dateYMD|title|varname|format|required|startyear|NOW|options|aftertext</def>
<test>
    <code>dateYMD|dateYMD|dateymd|Y-M-D|N|NOW|5|onchange="this.style.backgroundColor='#f77';"|aftertext</code>
    <testvalue>2009-04-01</testvalue><testvar>dateymd</testvar>
</test>
</formitem>*/

/*<formitem>
<def>dateYM|title|varname|format|required|startyear|NOW|options|aftertext</def>
<test>
    <code>dateYM|dateYM|dateym|Y-M|N|NOW|5|onchange="this.style.backgroundColor='#f77'"|aftertext</code>
    <testvalue>2009-04</testvalue><testvar>dateym</testvar>
</test>
</formitem>*/
            $field1 = Form_RequiredCheck($field4, $process)? "{$FORM_VAR['required_text']}$field1" : $field1;
            $options = ($field7)? " $field7" : '';
            $RESULT .= Form_GetTitleCode($field1, $TitleTemplate);

            $field3 = strtolower($field3);
            $yearvar  = "{$field2}_YEAR";
            $monthvar = "{$field2}_MONTH";
            $dayvar   = "{$field2}_DAY";

            $value = GetPostItem($field2);

            if ($value) {
                $format = str_replace($olddatecodes, $newdatecodes, $field3);
                $yearvalue  = ($value)? substr($value,strpos($format, 'yyyy'),4):'';
                $monthvalue = ($value)? substr($value,strpos($format, 'mm'),2):'';
                if ($kind == 'dateymd') {
                    $dayvalue   = ($value)? substr($value,strpos($format, 'dd'),2):'';
                }
            }

            //----year----
            $startyear = ($field5 == 'NOW')? date('Y') : $field5;
            $endyear   = ($field6 == 'NOW')? date('Y') : $field6 + $startyear;

            $id = Form_GetIdFromVar($yearvar);
            if ($endyear == $startyear) {
                $formitem = "<input type=\"hidden\" id=\"$id\" name=\"$FormPrefix$yearvar\" value=\"$startyear\" />$n";
                $formitem .= $startyear;
            } else {
                $yearvalue = ($value)? $yearvalue : intOnly(GetPostItem($yearvar));
                $class =  (Form_RequiredCheck($field4, $process) and ($process) and ($yearvalue==$FORM_VAR['start_select_value']))? 'formitemerror' : 'formitem';

                $formitem = "{$FORM_VAR['year']}&nbsp;<select id=\"$id\" class=\"$class\" name=\"$FormPrefix$yearvar\"$options>$n";
                $formitem .= $SELECT_START;
                for($i=$startyear; $i<=$endyear; $i++) {
                    $selected = ($yearvalue == $i)? ' selected="selected"' : '';
                    $formitem .= "<option value=\"$i\"$selected>$i</option>$n";
                }
                $formitem .=  "</select>";
            }

            //----month----
            $id = Form_GetIdFromVar($monthvar);
            $monthvalue = ($value)? $monthvalue : intOnly(GetPostItem($monthvar));
            $class =  (Form_RequiredCheck($field4, $process) and ($process) and ($monthvalue==$FORM_VAR['start_select_value']))? 'formitemerror' : 'formitem';
            $formitem .=  "&nbsp;{$FORM_VAR['month']}&nbsp;<select id=\"$id\" class=\"$class\" name=\"$FormPrefix$monthvar\"$options>$n";
            $formitem .= $SELECT_START;
            for($i=1; $i<=12; $i++) {
                $selected = ($monthvalue == $i)? ' selected="selected"' : '';
                $optvalue = sprintf('%02d', $i);
                $formitem .=  "<option value=\"$optvalue\"$selected>{$FORM_MONTHS[$i-1]}</option>$n";
            }
            $formitem .=  "</select>";

            //----day----
            if ($kind == 'dateymd') {
                $dayvalue = ($value)? $dayvalue : intOnly(GetPostItem($dayvar));
                $value = GetPostItem($dayvar);
                $class =  (Form_RequiredCheck($field4, $process) and ($process) and ($dayvalue==$FORM_VAR['start_select_value']))? 'formitemerror' : 'formitem';
                $id = Form_GetIdFromVar($dayvar);
                $formitem .=  "&nbsp;{$FORM_VAR['day']}&nbsp;<select id=\"$id\" class=\"$class\" name=\"$FormPrefix$dayvar\"$options>$n";
                $formitem .= $SELECT_START;
                for($i=1; $i<=31; $i++) {
                    $selected = ($dayvalue == $i)? ' selected="selected"' : '';
                    $optvalue = ($i<10)? "0$i" : $i;
                    $formitem .=  "<option value=\"$optvalue\"$selected>$optvalue</option>$n";
                }
                $formitem .=  "</select>";
            }
            $formitem .= "$field8$n";
            $RESULT .= Form_GetInfoCode($formitem, $InfoTemplate);
            break;

        case 'time':
            //"time|title|varname|format|required|options"
            $field1 = Form_RequiredCheck($field4, $process)? "{$FORM_VAR['required_text']}$field1" : $field1;
            $options = ($field5)? " $field5" : '';
            $RESULT .= Form_GetTitleCode($field1, $TitleTemplate);

            $field3 = strtolower($field3);
            $hourvar  = "{$field2}_HOUR";
            $minutevar = "{$field2}_MINUTE";

            $value = GetPostItem($field2);

            if ($value) {
                $time_pieces  = explode(':', $value);
                $hourvalue    = trim($time_pieces[0]);
                $minutevalue  = trim($time_pieces[1]);  // note could also have seconds
            } else {
                $hourvalue    = intOnly(GetPostItem($hourvar));
                $minutevalue  = intOnly(GetPostItem($minutevar));
            }

            //----hour----

            $class =  (Form_RequiredCheck($field4, $process) and ($process) and ($hourvalue==$FORM_VAR['start_select_value']))? 'formitemerror' : 'formitem';
            $id = Form_GetIdFromVar($hourvar);
            $formitem  =  "{$FORM_VAR['hour']} <select id=\"$id\" class=\"$class\" name=\"$FormPrefix$hourvar\"$options>$n";
            $formitem .= $SELECT_START;
            for($i=0; $i<=23; $i++) {
                $optvalue = ($i<10)? "0$i" : $i;
                $selected = ($hourvalue == $optvalue)? ' selected="selected"' : '';
                if ($i == 0)      $opttitle = '12am';
                elseif ($i < 12)  $opttitle = $i.'am';
                elseif ($i == 12) $opttitle = '12pm';
                else              $opttitle = ($i-12).'pm';
                $formitem .=  "<option value=\"$optvalue\"$selected>$opttitle</option>";
            }
            $formitem .=  "</select>$n";

            //----minute----
            $class =  (Form_RequiredCheck($field4, $process) and ($process) and ($minutevalue==$FORM_VAR['start_select_value']))? 'formitemerror' : 'formitem';
            $id = Form_GetIdFromVar($minutevar);
            $formitem .=  " {$FORM_VAR['minute']}&nbsp;<select id=\"$id\" class=\"$class\" name=\"$FormPrefix$minutevar\"$options>$n";
            $formitem .= $SELECT_START;
            for($i=0; $i<=59; $i++) {
                $optvalue = ($i<10)? "0$i" : $i;
                $selected = ($minutevalue == $optvalue)? ' selected="selected"' : '';
                $opttitle = $optvalue;
                $style = (!($i % 15))? ' style="background-color:#888; color:#fff;"' : '';
                $formitem .=  "<option value=\"$optvalue\"$selected$style>$opttitle</option>";
            }
            $formitem .=  "</select>$n";


            $RESULT .= Form_GetInfoCode($formitem, $InfoTemplate);
            break;

/*<formitem>
<def>datetime|title|varname|required|startyear|NOW|options</def>
<test>
    <code>datetime|datetime|datetime|N|2001|NOW|onchange="this.style.backgroundColor='#f77'"|aftertext</code>
    <testvalue>2009-05-28 18:01</testvalue><testvar>datetime</testvar>
</test>
</formitem>*/
        case 'datetime':
            //"datetime|title|varname|required|startyear|NOW|options"
            $field1 = Form_RequiredCheck($field3, $process)? "{$FORM_VAR['required_text']}$field1" : $field1;
            $options = ($field6)? " $field6" : '';
            $RESULT .= Form_GetTitleCode($field1, $TitleTemplate);

            $yearvar   = "{$field2}_YEAR";
            $monthvar  = "{$field2}_MONTH";
            $dayvar    = "{$field2}_DAY";
            $hourvar   = "{$field2}_HOUR";
            $minutevar = "{$field2}_MINUTE";

            $value = GetPostItem($field2);

            $yearvalue   = ($value)? substr($value, 0, 4) :'';
            $monthvalue  = ($value)? substr($value, 5, 2) :'';
            $dayvalue    = ($value)? substr($value, 8, 2) :'';
            $hourvalue   = ($value)? substr($value, 11, 2) :'';
            $minutevalue = ($value)? substr($value, 14, 2) :'';

            //----year----
            $startyear = ($field4 == 'NOW')? date('Y') : $field4;
            $endyear   = ($field5 == 'NOW')? date('Y') : $field5 + $startyear;

            $id = Form_GetIdFromVar($yearvar);
            if ($endyear == $startyear) {
                $formitem = "<input type=\"hidden\" id=\"$id\" name=\"$FormPrefix$yearvar\" value=\"$startyear\" />$n";
                $formitem .= $startyear;
            } else {
                $yearvalue = ($value)? $yearvalue : intOnly(GetPostItem($yearvar));
                $class =  (Form_RequiredCheck($field4, $process) and ($process) and
                    ($yearvalue==$FORM_VAR['start_select_value']))? 'formitemerror' : 'formitem';

                $formitem = "{$FORM_VAR['year']}&nbsp;<select id=\"$id\" class=\"$class\" name=\"$FormPrefix$yearvar\"$options>$n";
                $formitem .= $SELECT_START;
                for($i=$startyear; $i<=$endyear; $i++) {
                    $selected = ($yearvalue == $i)? ' selected="selected"' : '';
                    $formitem .= "<option value=\"$i\"$selected>$i</option>$n";
                }
                $formitem .=  "</select>";
            }

            //----month----
            $monthvalue = ($value)? $monthvalue : intOnly(GetPostItem($monthvar));
            $class =  (Form_RequiredCheck($field3, $process) and ($process) and
                ($monthvalue==$FORM_VAR['start_select_value']))? 'formitemerror' : 'formitem';
            $id = Form_GetIdFromVar($monthvar);
            $formitem .=
            "&nbsp;{$FORM_VAR['month']}&nbsp;<select id=\"$id\" class=\"$class\" name=\"$FormPrefix$monthvar\"$options>$n";
            $formitem .= $SELECT_START;
            for($i=1; $i<=12; $i++) {
                $selected = ($monthvalue == $i)? ' selected="selected"' : '';
                $optvalue = sprintf('%02d', $i);
                $formitem .=  "<option value=\"$optvalue\"$selected>{$FORM_MONTHS[$i-1]}</option>$n";
            }
            $formitem .=  "</select>";

            //----day----

            $dayvalue = ($value)? $dayvalue : intOnly(GetPostItem($dayvar));
            $class =  (Form_RequiredCheck($field3, $process) and ($process) and
                ($dayvalue==$FORM_VAR['start_select_value']))? 'formitemerror' : 'formitem';
            $id = Form_GetIdFromVar($dayvar);
            $formitem .=
                "&nbsp;{$FORM_VAR['day']}&nbsp;<select id=\"$id\" class=\"$class\" name=\"$FormPrefix$dayvar\"$options>$n";
            $formitem .= $SELECT_START;
            for($i=1; $i<=31; $i++) {
                $selected = ($dayvalue == $i)? ' selected="selected"' : '';
                $optvalue = ($i<10)? "0$i" : $i;
                $formitem .=  "<option value=\"$optvalue\"$selected>$optvalue</option>$n";
            }
            $formitem .=  "</select>";

            $RESULT .= Form_GetInfoCode($formitem, $InfoTemplate);
            $formitem = '';
            //$RESULT .= Form_GetInfoCode($formitem, $InfoTemplate);
            //$RESULT .= '<br /><br />';

            // ---------- end date, start time ---------

            //----hour----

            $hourvalue = ($value)? $hourvalue : intOnly(GetPostItem($hourvar));
            $class =  (Form_RequiredCheck($field3, $process) and ($process) and ($hourvalue==$FORM_VAR['start_select_value']))? 'formitemerror' : 'formitem';
            $id = Form_GetIdFromVar($hourvar);
            $formitem .=  "{$FORM_VAR['hour']} <select id=\"$id\" class=\"$class\" name=\"$FormPrefix$hourvar\"$options>$n";
            $formitem .= $SELECT_START;
            for($i=0; $i<=23; $i++) {
                $optvalue = ($i<10)? "0$i" : $i;
                $selected = ($hourvalue == $optvalue)? ' selected="selected"' : '';
                if ($i == 0)      $opttitle = '12am';
                elseif ($i < 12)  $opttitle = $i.'am';
                elseif ($i == 12) $opttitle = '12pm';
                else              $opttitle = ($i-12).'pm';
                $formitem .=  "<option value=\"$optvalue\"$selected>$opttitle</option>";
            }
            $formitem .=  "</select>$n";

            //----minute----
            $minutevalue = ($value)? $minutevalue : intOnly(GetPostItem($minutevar));

            $class =  (Form_RequiredCheck($field3, $process) and ($process) and ($minutevalue==$FORM_VAR['start_select_value']))? 'formitemerror' : 'formitem';
            $id = Form_GetIdFromVar($minutevar);
            $formitem .=  " {$FORM_VAR['minute']}&nbsp;<select id=\"$id\" class=\"$class\" name=\"$FormPrefix$minutevar\"$options>$n";
            $formitem .= $SELECT_START;
            for($i=0; $i<=59; $i++) {
                $optvalue = ($i<10)? "0$i" : $i;
                $selected = ($minutevalue == $optvalue)? ' selected="selected"' : '';
                $opttitle = $optvalue;
                $style = (!($i % 15))? ' style="background-color:#888; color:#fff;"' : '';
                $formitem .=  "<option value=\"$optvalue\"$selected$style>$opttitle</option>";
            }
            $formitem .=  "</select>$n";

            $RESULT .= Form_GetInfoCode($formitem, $InfoTemplate);
            break;

/*<formitem>
<def>countrystate|title|VARcountry:VARstate|required|options</def>
<test>
    <code>countrystate|countrystate|VARcountry:VARstate|required|options</code>
    <testvalue>US</testvalue><testvar>VARcountry</testvar>
    <testvalue>WA</testvalue><testvar>VARstate</testvar>
</test>
</formitem>*/
        case 'countrystate':
            //"countrystate|title|VARcountry:VARstate|required|options"

            $base = $FormPrefix . $FORM_VAR['id_prefix'];
            $javascript_functions['formCountryState'] = "
            function formCountryState(countryVar, stateVar) {
                var countryElem = document.getElementById('$base' + countryVar);
                var country_index = countryElem.selectedIndex;
                var country = '';
                if (country_index >= 0) {
                    country = countryElem.options[country_index].value;
                }
                var usState     = 'none';
                var canadaState = 'none';
                var otherState  = 'none';
                if (country == 'US') usState = '';
                else if(country == 'CA') canadaState = '';
                else otherState = '';
                document.getElementById('{$base}US_STATES_DIV_' + stateVar).style.display        = usState;
                document.getElementById('{$base}CANADA_PROVINCES_DIV_' + stateVar).style.display = canadaState;
                document.getElementById('{$base}OTHER_STATES_DIV_' + stateVar).style.display     = otherState;
            }";

            //------- country --------
            $field1 = Form_RequiredCheck($field3, $process)? "{$FORM_VAR['required_text']}$field1" : $field1;
            $options = ($field4)? " $field4" : '';

            $RESULT .= Form_GetTitleCode($field1, $TitleTemplate);

            list($country_var, $state_var) = explode(':', $field2);
            $country_var_id = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $country_var);
            $state_var_id = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $state_var);

            $raw_state_value = GetPostItem($state_var);

            $JavaScript .= "formCountryState('$country_var_id', '$state_var_id');$n";

            $value = GetPostItem($country_var);
            $ic = ($value)? $value : $FORM_VAR['default_country'];
            $class =  (Form_RequiredCheck($field3, $process) and ($process) and ($value==$FORM_VAR['start_select_value']))? 'formitemerror' : 'formitem';
            $id = Form_GetIdFromVar($country_var);
            $formitem  =  "<select id=\"$id\" class=\"$class\" name=\"$FormPrefix$country_var\" onchange=\"formCountryState('$country_var_id', '$state_var_id')\">$n";
            $formitem .= $SELECT_START;
            Form_LoadCountryCodes();
            foreach ($FORM_COUNTRY_CODES as $code => $name) {
                $selected =(($ic == $code) or ($ic == $name))? ' selected="selected"' : '';
                $formitem .= "<option value=\"$code\"$selected>$name</option>$n";
            }
            $formitem .=  "</select>$n";
            $RESULT .= Form_GetInfoCode($formitem, $InfoTemplate);

            //------- us state --------

            $id = Form_GetIdFromVar('US_STATES_DIV_' . $state_var);
            $RESULT .= '<div id="' . $id . '" style="display:block;">' . $n;
            $field1 = Form_RequiredCheck($field3, $process)? $FORM_VAR['required_text'] . $FORM_VAR['state'] : $FORM_VAR['state'];

            $FORM_STATE_CODES[0] = $FORM_VAR['start_select'];

            $RESULT .= Form_GetTitleCode($field1, $TitleTemplate);

            $value = ($raw_state_value)? $raw_state_value : GetPostItem('US_STATES_' . $state_var);
            $class =  (Form_RequiredCheck($field3, $process) and ($process) and ($value==$FORM_VAR['start_select_value']))? 'formitemerror' : 'formitem';
            $id = Form_GetIdFromVar('US_STATES_' . $state_var);
            $formitem  =  "\n<select id=\"$id\" class=\"$class\" name=\"{$FormPrefix}US_STATES_$state_var\">$n";

            foreach ($FORM_STATE_CODES as $code) {
                $statecode = ($code == $FORM_VAR['start_select']) ? $FORM_VAR['start_select_value'] : substr($code,0,2);
                $text = ($statecode == $value)? ' selected="selected"': '';
                $formitem .= "<option$text value=\"$statecode\">$code</option>$n";
            }
            $formitem .=  "</select>$n";
            $RESULT .= Form_GetInfoCode($formitem, $InfoTemplate);
            $RESULT .= "</div>$n";

            //------- Canada Province/Territory --------

            $id = Form_GetIdFromVar('CANADA_PROVINCES_DIV_' . $state_var);
            $RESULT .= '<div id="' . $id . '" style="display:none;">' . $n;
            $field1 = Form_RequiredCheck($field3, $process)? $FORM_VAR['required_text'] . $FORM_VAR['canada_province'] : $FORM_VAR['canada_province'];

            $RESULT .= Form_GetTitleCode($field1, $TitleTemplate);

            $value = ($raw_state_value)? $raw_state_value : GetPostItem('CANADA_PROVINCES_' . $state_var);

            $class =  (Form_RequiredCheck($field3, $process) and ($process) and ($value==$FORM_VAR['start_select_value']))? 'formitemerror' : 'formitem';

            $id = Form_GetIdFromVar('CANADA_PROVINCES_' . $state_var);
            $formitem  =  "\n<select id=\"$id\" class=\"$class\" name=\"{$FormPrefix}CANADA_PROVINCES_$state_var\">$n";
            $formitem .= $SELECT_START;
            foreach ($FORM_CANADA_PROVINCES as $province) {
                $text = ($province == $value)? ' selected="selected"': '';
                $formitem .= "<option$text value=\"$province\">$province</option>$n";
            }
            $formitem .=  "</select>$n";
            $RESULT .= Form_GetInfoCode($formitem, $InfoTemplate);
            $RESULT .= "</div>$n";

             //------- Other State/Province --------

            $id = Form_GetIdFromVar('OTHER_STATES_DIV_' . $state_var);
            $RESULT .= '<div id="' . $id . '" style="display:none;">' . $n;
            $field1 = Form_RequiredCheck($field3, $process)? $FORM_VAR['required_text'] . $FORM_VAR['state_province'] : $FORM_VAR['state_province'];
            $RESULT .= Form_GetTitleCode($field1, $TitleTemplate);
            $value = ($raw_state_value)? $raw_state_value : GetPostItem('OTHER_STATES_' . $state_var);
            $error = ($process and (Form_RequiredCheck($field3, $process) and !$value));
            $class =  ($error)? 'formitemerror' : 'formitem';
            $id = Form_GetIdFromVar('OTHER_STATES_' . $state_var);
            $formitem = "<input type=\"text\" alt=\"$state_var\" id=\"$id\" class=\"$class\" name=\"{$FormPrefix}OTHER_STATES_$state_var\" size=\"20\" maxlength=\"60\" value=\"$value\" />$n";
            $RESULT .= Form_GetInfoCode($formitem, $InfoTemplate);
            $RESULT .= "</div>$n";

            break;

        case 'country':
            //"country|title|VAR|required|options"
            $field1 = Form_RequiredCheck($field3, $process)? "{$FORM_VAR['required_text']}$field1" : $field1;
            $options = ($field4)? " $field4" : '';
            if (empty($CountryDivOption)) $CountryDivOption = '';
            $id = Form_GetIdFromVar("countrydiv_$field2");
            $RESULT .= "<div id=\"$id\"$CountryDivOption>";
            $RESULT .= Form_GetTitleCode($field1, $TitleTemplate);
            $value = GetPostItem($field2);
            $ic = ($value)? $value : $FORM_VAR['default_country'];
            $class =  (Form_RequiredCheck($field3, $process) and ($process) and ($value==$FORM_VAR['start_select_value']))? 'formitemerror' : 'formitem';
            $id = Form_GetIdFromVar($field2);
            $formitem  =  "<select id=\"$id\" class=\"$class\" name=\"$FormPrefix$field2\"$options>$n";
            $formitem .= $SELECT_START;
            Form_LoadCountryCodes();
            foreach ($FORM_COUNTRY_CODES as $code => $name) {
                $selected =(($ic == $code) or ($ic == $name))? ' selected="selected"' : '';
                $formitem .= "<option value=\"$code\"$selected>$name</option>$n";
            }
            $formitem .=  "</select>$n";
            $RESULT .= Form_GetInfoCode($formitem, $InfoTemplate);
            $RESULT .= '</div>';
            break;

        case 'state':
            //"state|title|VAR|required|options|US"
            $field1 = Form_RequiredCheck($field3, $process)? "{$FORM_VAR['required_text']}$field1" : $field1;
            $options = ($field4)? " $field4" : '';
            $US = ($field5 == 'US');
            if ($US) $FORM_STATE_CODES[0] = $FORM_VAR['start_select'];
            $RESULT .= Form_GetTitleCode($field1, $TitleTemplate);
            $value = GetPostItem($field2);
            $defaultvalue = ($US)? $FORM_VAR['start_select_value'] : 'INT';
            $ic = ($value)? $value : $defaultvalue;
            $class =  (Form_RequiredCheck($field3, $process) and ($process) and ($value==$FORM_VAR['start_select_value']))? 'formitemerror' : 'formitem';
            $id = Form_GetIdFromVar($field2);
            $formitem  =  "\n<select id=\"$id\" class=\"$class\" name=\"$FormPrefix$field2\"$options>$n";
            foreach ($FORM_STATE_CODES as $code) {
                $statecode = (
                  ($code == 'International') or
                  ($code == $FORM_VAR['start_select'])
                ) ? $defaultvalue : substr($code,0,2);
                $text = ($statecode == $value)? ' selected="selected"': '';
                $formitem .= "<option$text value=\"$statecode\">$code</option>$n";
            }
            $formitem .=  "</select>$n";
            $RESULT .= Form_GetInfoCode($formitem, $InfoTemplate);
            break;



/*<formitem>
<def>intstate|title|VAR|required|options|countryid</def>
<test>
    <code>intstate|intstate|intstate|N|style="background-color:#fcc"|country</code>
    <testvalue>WA</testvalue><testvar>intstate</testvar>
</test>
<test>
    <code>intstate|intstate-int|intstate_int|N|style="background-color:#cfc"|country_int</code>
    <testvalue>British Columbia</testvalue><testvar>intstate_int</testvar>
</test>
</formitem>*/

        case 'intstate':
            //"intstate|title|VAR|required|options|countryid"

            $onchange = ($field5 == '')? '' : " onchange=\"stateElem = document.getElementById('{$field2}_span');
         if (stateElem) stateElem.style.display = (this.value=='INT')? '' : 'none';
         countrydivElem = document.getElementById('countrydiv_$field5');
         if (countrydivElem) countrydivElem.style.display = (this.value=='INT')? '' : 'none';
         if (this.value != 'INT') {
             countryElem = document.getElementById('{$FormPrefix}$field5');
             if (countryElem) {
                countryElem.selectedIndex=1;
                countryElem.value='US';
              }
             intState = document.getElementById('INT_$field2');
             if (intState) intState.value='';
           }
         \"";

            $field1 = Form_RequiredCheck($field3, $process)? "{$FORM_VAR['required_text']}$field1" : $field1;
            $options = ($field4)? " $field4" : '';
            $RESULT .= Form_GetTitleCode($field1, $TitleTemplate);
            $value = GetPostItem($field2);
            $intvalue = GetPostItem("INT_$field2");
            $ic = ($value)? $value : 'INT';
            $hideInt = ($value and ($value != 'INT') and in_array($value, $FORM_STATE_CHAR_CODES));
            $CountryDivOption = ($hideInt)? ' style="display:none;"' : '';
            $IntStyle = $CountryDivOption;
            $class =  (Form_RequiredCheck($field3, $process) and ($process) and ($value=='INT') and ($intvalue ==''))? 'formitemerror' : 'formitem';
            $id = Form_GetIdFromVar($field2);
            $formitem  =  "<div class=\"withinformtext\" style=\"float:left; width:5.5em;\">{$FORM_VAR['us_state']}:</div><select id=\"$id\" class=\"$class\" name=\"$FormPrefix$field2\"$options$onchange>$n";

            $have_state = false;
            foreach ($FORM_STATE_CODES as $code) {
                $statecode = (($code == 'International') or ($code == $FORM_VAR['start_select_value'])) ? 'INT' : substr($code,0,2);
                $text = ($statecode == $value)? ' selected="selected"' : '';
                if($text and ($statecode != 'INT')) {
                    $have_state = true;
                }
                $formitem .= "<option$text value=\"$statecode\">$code</option>$n";
            }
            $formitem .=  "</select>$n";

            if (empty($intvalue) and !$have_state) {
                $intvalue = ($value != 'INT')? $value : '';
            }
            $id = Form_GetIdFromVar($field2);
            $id2 = Form_GetIdFromVar('INT_' .$field2);
            $formitem .=  "<div id=\"{$id}_span\"$IntStyle><span class=\"withinformtext\">or<br /></span>\n<div class=\"withinformtext\" style=\"float:left; width:5.5em;\">{$FORM_VAR['non_us']}:</div>\n<input type=\"text\" id=\"$id2\" class=\"$class\" name=\"{$FormPrefix}INT_$field2\" size=\"20\" maxlength=\"80\" value=\"$intvalue\"$options /></div>$n";
            $RESULT .= Form_GetInfoCode($formitem, $InfoTemplate);
            break;

/*<formitem>
<def>radio|title|VAR|required|options|value1=text|value2=text</def>
<test>
    <code>radio|radio|radio|N|onclick="alert('click');"|value1=Item 1|value2=Item 2</code>
    <testvalue>value1</testvalue><testvar>radio</testvar>
</test>
</formitem>*/

/*<formitem>
<def>radioh|title|VAR|required|options|value1=text|value2=text</def>
<test>
    <code>radioh|radioh|radioh|N|onclick="alert('click');"|value1=Item 1|value2=Item 2</code>
    <testvalue>value2</testvalue><testvar>radioh</testvar>
</test>
</formitem>*/

        case 'radio':
        case 'radioh':
            //"radio|title|VAR|required|options|value1=text|value2=text"
            $field1 = Form_RequiredCheck($field3, $process)? "{$FORM_VAR['required_text']}$field1" : $field1;
            $options = ($field4)? " $field4" : '';
            $RESULT .= Form_GetTitleCode($field1, $TitleTemplate);
            $value = GetPostItem($field2);
            $formitem = '';

            $radio_error =  (Form_RequiredCheck($field3, $process) and ($process) and ($value==''));
            $start_span = ($radio_error)? '<span class="formitemerror" style="border:none;">' : '';
            $end_span = ($radio_error)? '</span>' : '';

            for($i=5; $i<count($field); $i++) {
                $itemvalue = trim(strTo($field[$i], '='));
                $itemtext  = trim(strFrom($field[$i], '='));
                if (empty($itemtext)) {
                    $itemtext = $itemvalue;
                }

                $checked =($itemvalue == $value)? ' checked="checked"' : '';
                if ($kind == 'radio') {
                    $BRK = ($i<count($field)-1)? '<br />' : '';
                } else {
                    $start_span .= '<span style="white-space:nowrap;">';
                    $end_span   .= '</span>';
                    $BRK = ($i<count($field)-1)? '&nbsp; ' : '';
                }
                $idsuffix = '_'.($i-4);

                $formitem .= "$start_span<input type=\"radio\" id=\"$field2$idsuffix\" class=\"radio_formitem\" name=\"$FormPrefix$field2\" value=\"$itemvalue\"$checked$options />&nbsp;$itemtext$end_span$BRK$n";
            }
            $RESULT .= Form_GetInfoCode($formitem, $InfoTemplate);
            break;
/*<formitem>
<def>select|title|VAR|required|options|value1=text|value2=text</def>
<test>
    <code>select|Select|select|N||1=item1|2=item2|3=item3</code>
    <testvalue>3</testvalue><testvar>select</testvar>
</test>
</formitem>*/

        case 'select':
            //"select|title|VAR|required|options|value1=text|value2=text"
            $field1 = Form_RequiredCheck($field3, $process)? "{$FORM_VAR['required_text']}$field1" : $field1;
            $options = ($field4)? " $field4" : '';
            $RESULT .= Form_GetTitleCode($field1, $TitleTemplate);

            $value = GetPostItem($field2);
            $class =  (Form_RequiredCheck($field3, $process) and ($process) and ($value==$FORM_VAR['start_select_value']))? 'formitemerror' : 'formitem';
            $id = Form_GetIdFromVar($field2);
            $formitem  =  "<select id=\"$id\" class=\"$class\" name=\"$FormPrefix$field2\"$options>$n";

            $optionstart = 5;
            if ($field5 == 'N')  $optionstart = 6;
            else  $formitem .= $SELECT_START;

            for($i=$optionstart; $i<count($field); $i++) {
                $option = strTo($field[$i], '::');
                if ($option !='') {
                    $option_options = strFrom($field[$i], '::');
                    if ($option_options) {
                        $option_options = " $option_options";
                    }

                    $itemvalue = trim(strTo($option, '='));
                    $itemtext  = trim(strFrom($option, '='));
                    if (empty($itemtext)) {
                        $itemtext = $itemvalue;
                    }

                    $selected =($itemvalue == $value)? ' selected="selected"' : '';
                    $formitem .= "<option value=\"$itemvalue\"$selected$option_options>$itemtext</option>$n";
                }
            }
            $formitem .=  "</select>$n";
            $RESULT .= Form_GetInfoCode($formitem, $InfoTemplate);
            break;


/*<formitem>
<def>selectcount|title|VAR|required|start|end|options|mask</def>
<test>
    <code>selectcount|selectcount|selectcount|N|1|10|onchange="this.style.backgroundColor='#f77'"</code>
    <testvalue>5</testvalue><testvar>selectcount</testvar>
</test>
</formitem>*/

        case 'selectcount':
            //"selectcount|title|VAR|required|start|end|options|mask"
            $field1 = Form_RequiredCheck($field3, $process)? "{$FORM_VAR['required_text']}$field1" : $field1;
            $options = ($field6)? " $field6" : '';
            $RESULT .= Form_GetTitleCode($field1, $TitleTemplate);
            $value = GetPostItem($field2);
            $class =  (Form_RequiredCheck($field3, $process) and ($process) and ($value==$FORM_VAR['start_select_value']))? 'formitemerror' : 'formitem';
            $id = Form_GetIdFromVar($field2);
            $formitem  =  "<select id=\"$id\" class=\"$class\" name=\"$FormPrefix$field2\"$options>$n";


            if (substr($field4,0,1) == 'N') $field4=substr($field4,1);
            else  $formitem .= $SELECT_START;

            for($i=$field4; $i<=$field5; $i++) {
                $selected = ($value == $i)? ' selected="selected"' : '';
                $formitem .=  "<option value=\"$i\"$selected>$i</option>$n";
            }
            $formitem .=  "</select>$n";
            $RESULT .= Form_GetInfoCode($formitem, $InfoTemplate);
            break;


/*<formitem>
<def>selecttext|title|VAR|required|size|maxlength|mask|value1|value2 . . .</def>
<test>
    <code>selecttext|selecttext|selecttext1|Y|50|50||value1|value2</code>
    <testvalue></testvalue><testvar>selecttext1</testvar>
</test>
<test>
    <code>selecttext|selecttext2|selecttext2|N|50|50||value1|value2</code>
    <testvalue>value3</testvalue><testvar>selecttext2</testvar>
</test>
</formitem>*/
        case 'selecttext':
            //"selecttext|title|VAR|required|size|maxlength|mask|value1|value2 . . ."
            $field1a = $field1;
            $field1 = Form_RequiredCheck($field3, $process)? "{$FORM_VAR['required_text']}$field1" : $field1;
            $RESULT .= Form_GetTitleCode($field1, $TitleTemplate);

            $value = GetPostItem($field2);
            $value2 = GetPostItem('new_' . $field2);
            $id2 = Form_GetIdFromVar('new_' . $field2);
            $options = "onchange=\"if (this.value == '{$FORM_VAR['new_select_text_value']}') {
              document.getElementById('$id2').style.display = '';
              } else document.getElementById('$id2').style.display = 'none';\"";

            $class =  (
                Form_RequiredCheck($field3, $process) and ($process) and
                (($value==$FORM_VAR['start_select_value']) or
                 (($FORM_VAR['new_select_text_value'] == $value) and (empty($value2))))
                )? 'formitemerror' : 'formitem';
            $id = Form_GetIdFromVar($field2);
            $formitem  =  "<select id=\"$id\" class=\"$class\" name=\"$FormPrefix$field2\" $options>$n";

            if ($value==$FORM_VAR['start_select_value']) {
                $value = '';
            }

            $optionstart = 7;
            if ($field6 == 'N')  $optionstart = 8;
            else  $formitem .= $SELECT_START;

            $have_option = false;
            for($i=$optionstart; $i<count($field); $i++) {
                $itemtext = trim($field[$i]);
                if ($itemtext == $value) {
                    $selected = ' selected="selected"';
                    $have_option = true;
                } else {
                    $selected = '';
                }
                $selected =($itemtext == $value)? ' selected="selected"' : '';
                if (!empty($itemtext)) $formitem .= "<option value=\"$itemtext\"$selected>$itemtext</option>$n";
            }

            if ($value2 and !$have_option) {
                $selected = ' selected="selected"';
            } else {
                if (!$have_option and $value) {
                    // must be posted value
                    $selected = ' selected="selected"';
                    $value2 = ($FORM_VAR['new_select_text_value'] != $value)? $value : '';
                } else {
                    $selected = ($FORM_VAR['new_select_text_value'] == $value)? ' selected="selected"' : '';
                }
            }

            $style  = ($selected)? 'inline' : 'none';
            $formitem .=  "<option value=\"{$FORM_VAR['new_select_text_value']}\" $selected>{$FORM_VAR['new_select_text']}</option>$n</select>$n";

            $maxlen = ($field5)? ' maxlength="' . Form_GetMax($field5) . '"' : '';

            $id = Form_GetIdFromVar('new_' . $field2);
            $formitem .=  "<span id=\"$id\" style=\"display:$style;\">&nbsp;{$FORM_VAR['new']} ($field1a): <input class=\"$class\" name=\"{$FormPrefix}new_$field2\" value=\"$value2\" type=\"text\" size=\"$field4\"$maxlen /></span>$n";
            $RESULT .= Form_GetInfoCode($formitem, $InfoTemplate);
            break;

/*<formitem>
<def>checkboxlist|title|options|value1=text|value2=text</def>
<test>
    <code>checkboxlist|checkboxlist|onclick="alert('click');"|value1=Item 1|value2=Item 2</code>
    <testvalue>value2</testvalue><testvar>checkboxlist</testvar>
</test>
</formitem>*/
        case 'checkboxlist':
          //"checkboxlist|title|options|value1=text|value2=text"
            $RESULT .= Form_GetTitleCode($field1, $TitleTemplate);
            $options = ($field2)? " $field2" : '';
            $formitem = '';
            for($i=3; $i<count($field); $i++) {

                $itemvalue = trim(strTo($field[$i], '='));
                $itemtext  = trim(strFrom($field[$i], '='));
                if (empty($itemtext)) {
                    $itemtext = $itemvalue;
                }

                $itemname = preg_replace('/[^a-zA-Z0-9]/', '_', $itemtext);
                $value = GetPostItem($itemname);
                $checked =($value)? ' checked="checked"' : '';
                $BRK = ($i<count($field)-1)? '<br />' : '';
                $id = Form_GetIdFromVar($itemname);
                $formitem .= "<input type=\"checkbox\" id=\"$id\" class=\"formitem_checkbox\" name=\"$FormPrefix$itemname\" value=\"$itemvalue\"$checked$options /> $itemtext$BRK$n";
            }
            $RESULT .= Form_GetInfoCode($formitem, $InfoTemplate);
            break;

/*<formitem>
<def>checkboxlistbar|title|var|Y|options|value1=text|value2=text</def>
<test>
    <code>checkboxlistbar|checkboxlistbar|checkboxlistbar|N|onclick="alert('click');"|value1=Item 1|value2=Item 2</code>
    <testvalue>value2</testvalue><testvar>checkboxlistbar</testvar>
</test>
<test>
    <code>checkboxlistbarh|checkboxlistbarh|checkboxlistbarh|N|onclick="alert('click');"|value1=Item 1|value2=Item 2</code>
    <testvalue>value2</testvalue><testvar>checkboxlistbarh</testvar>
</test>
</formitem>*/
        case 'checkboxlistbar':
        case 'checkboxlistbarh':
            //"checkboxlistbar|title|var|Y|options|value1=text|value2=text"
            $RESULT .= Form_GetTitleCode($field1, $TitleTemplate);
            $options = ($field4)? " $field4" : '';
            $formitem = '';
            $value = GetPostItem($field2);
            $stripped_value = preg_replace('/|/', '', $value);
            $missing_error =  (Form_RequiredCheck($field3, $process) and ($process) and ($stripped_value==''));
            $start_span = ($missing_error)? '<span class="formitemerror" style="border:none;">' : '';
            $end_span = ($missing_error)? '</span>' : '';

            if (!empty($value)) {
                $values = explode('|', $value);
            } else {
                // else get values from post
                $values = array();
                for($i=5; $i<count($field); $i++) {
                    $count = $i-5;
                    $itemname = "$field2$count";
                    $values[$count] = GetPostItem($itemname);
                }
            }
            for($i=5; $i<count($field); $i++) {

                $itemvalue = trim(strTo($field[$i], '='));
                $itemtext  = trim(strFrom($field[$i], '='));
                if (empty($itemtext)) {
                    $itemtext = $itemvalue;
                }

                $count = $i-5;
                $itemname = "$field2$count";
                $checked = !empty($values[$count])? ' checked="checked"' : '';
                $break_type = ($kind == 'checkboxlistbar')? '<br />' : '&nbsp;&nbsp;';
                $BRK = ($i<count($field)-1)? $break_type : '';
                $id = Form_GetIdFromVar($itemname);
                $formitem .= "<input type=\"checkbox\" id=\"$id\" class=\"formitem_checkbox\" name=\"$FormPrefix$itemname\" value=\"$itemvalue\"$checked$options />&nbsp;$itemtext$BRK$n";
            }
            $RESULT .= Form_GetInfoCode($formitem, $InfoTemplate);
            break;


/*<formitem>
<def>checkboxlistset|title|var|Y|options|value1=text|value2=text</def>
<test>
    <code>checkboxlistset|checkboxlistset|checkboxlistset|N|onclick="alert('click');"|value1=Item 1|value2=Item 2|value3=Item 3</code>
    <testvalue>value1,value2,value3</testvalue><testvar>checkboxlistset</testvar>
</test>
<test>
    <code>checkboxlistseth|checkboxlistseth|checkboxlistseth|N|onclick="alert('click');"|value1=Item 1|value2=Item 2</code>
    <testvalue>value2</testvalue><testvar>checkboxlistseth</testvar>
</test>
</formitem>*/

        case 'checkboxlistset':
        case 'checkboxlistseth':
            //"checkboxlistset|title|var|Y|options|value1=text|value2=text"
            $RESULT .= Form_GetTitleCode($field1, $TitleTemplate);
            $options = ($field4)? " $field4" : '';
            $formitem = '';
            $value = GetPostItem($field2);

            // $value = preg_replace('/,\s+/', ',', $value); // remove space after commas
            // $value = preg_replace('/,+/', ',', $value);   // remove double commas
            // $value = preg_replace('[(^,)|(, $)]', '', $value);  // remove beginning and end commas

            $values_in = explode(',', $value);
            TrimArray($values_in);
            $missing_error = (Form_RequiredCheck($field3, $process) and ($process) and ($value==''));
            $start_span = ($missing_error)? '<span class="formitemerror" style="border:none;">' : '';
            $end_span   = ($missing_error)? '</span>' : '';

            // else get values from post
            $values = array();
            for($i=5, $c=count($field); $i < $c; $i++) {
                $count = $i-5;
                $itemname = "$field2$count";
                if ($value) {
                    $var = trim(strTo($field[$i], '='));
                    $values[$count]  = in_array($var, $values_in);
                } else {
                    $values[$count] = GetPostItem($itemname);
                }
            }

            for($i=5, $c=count($field); $i < $c; $i++) {

                $itemvalue = trim(strTo($field[$i], '='));
                $itemtext  = trim(strFrom($field[$i], '='));
                if (empty($itemtext)) {
                    $itemtext = $itemvalue;
                }

                $count = $i-5;
                $itemname = "$field2$count";
                $checked = !empty($values[$count])? ' checked="checked"' : '';
                $break_type = ($kind == 'checkboxlistset')? '<br />' : '&nbsp;&nbsp;';
                $BRK = ($i<count($field)-1)? $break_type : '';
                $id = Form_GetIdFromVar($itemname);
                $formitem .= "<input type=\"checkbox\" id=\"$id\" class=\"formitem_checkbox\" name=\"$FormPrefix$itemname\" value=\"$itemvalue\"$checked$options />&nbsp;$itemtext$BRK$n";
            }
            $RESULT .= Form_GetInfoCode($formitem, $InfoTemplate);
            break;


        case 'checkbox':
            //"checkbox|title|varname|options|value|value-null|aftertext"
            $RESULT .= Form_GetTitleCode($field1, $TitleTemplate);
            $options = ($field3)? " $field3" : '';
            $formitem = '';
            $value = GetPostItem($field2);
            $checked = ($value==$field4)? ' checked="checked"' : '';
            $id = Form_GetIdFromVar($field2);
            $formitem .= "<input type=\"checkbox\" id=\"$id\" class=\"formitem_checkbox\" name=\"$FormPrefix$field2\" value=\"$field4\"$checked$options />$field6$n";
            $RESULT .= Form_GetInfoCode($formitem, $InfoTemplate);
            break;

        case 'password':
            //"password|title|VARNAME|required|size|maxlength|options|mask|show_text",
            $field1 = Form_RequiredCheck($field3, $process)? "{$FORM_VAR['required_text']}$field1" : $field1;
            $RESULT .= Form_GetTitleCode($field1, $TitleTemplate);
            if (strpos($field6, 'SECURE') !== false) {
                $field6 = trim(str_replace('SECURE', '', $field6));
            }
            $options = ($field6)? " $field6" : '';
            $value  = GetPostItem($field2);
            $show_text = ($field8)? $field8 : $FORM_VAR['show_password'];
            $class  =  (Form_RequiredCheck($field3, $process) and ($process) and (!$value))? 'formitemerror' : 'formitem';
            $maxlen = ($field5)? ' maxlength="' . Form_GetMax($field5) . '"' : '';

            $id = Form_GetIdFromVar($field2);
            $JavaScript .= "var My$field2 = '<input type=\"@@TYPE@@\" id=\"$id\" class=\"$class\" name=\"$FormPrefix$field2\" size=\"$field4\" $maxlen value=\"@@VALUE@@\"$options />';$n";

            $formitem = "<span id=\"span_$id\">
    <input type=\"password\" alt=\"$alt\" id=\"$id\" class=\"$class\" name=\"$FormPrefix$field2\" size=\"$field4\"$maxlen value=\"$value\"$options /></span>
        <br /><input type=\"checkbox\" value=\"1\" onclick=\"myElem  = document.getElementById('span_$id');
        myValue = document.getElementById('$id').value;
        var myInput = (this.value== '1')? My$field2.replace('@@TYPE@@', 'text'): My$field2.replace('@@TYPE@@', 'password');
        this.value  = (this.value== '1')? 0 : 1;
        myElem.innerHTML= myInput.replace('@@VALUE@@',myValue);\"/>
        <span class=\"withinformtext\">&nbsp;$show_text</span>$n";
            $RESULT .= Form_GetInfoCode($formitem, $InfoTemplate);
            break;


/*<formitem>
<def>submit|text|VAR|options</def>
<test>
    <code>submit|Submit|SUBMIT|onmousedown="this.style.backgroundColor='#f77'"</code>
</test>
</formitem>*/
        case 'submit':
            //"submit|text|VAR|options"
            $class   = strIn($field3, 'class=')? '' : 'class="formsubmit"';
            $onclick = strIn($field3, 'onclick=')? '' : "onclick=\"this.value='{$FORM_VAR['submit_click_text']}';\"";
            $id = Form_GetIdFromVar($field2);
            $formitem = "<input type=\"submit\" $class id=\"$id\"  name=\"$field2\" value=\"$field1\" $field3 $onclick />$n";
            $RESULT .= Form_GetInfoCode($formitem, $InfoTemplate);
            break;

/*<formitem>
<def>endform</def>
<test><code>endform</code></test>
</formitem>*/
        case 'endform':
            //"endform"
            $RESULT .= "</form>$n";
            break;
        }

    }
    $js_functions = '';
    foreach ($javascript_functions as $key => $value) {
        $js_functions .= "$value\n";
    }
    $JavaScript = $js_functions . $JavaScript;
    if ($JavaScript) $RESULT .= JavaScriptString($JavaScript);
    return $RESULT;
}

function FORM_GetAutocompleteValue($url, $value)
{
    global $ROOT;
    $evalue = urlencode($value);
    $url = (strpos($url, '?') !== false)? "$url&v=$evalue" : "$url?v=$evalue";
    $url_file = $ROOT . strTo($url, '?');
    $RESULT = '';
    if (file_exists($url_file)) {
        $FORM_AUTOCOMPLETE_PROCESSING = 1;
        $FORM_AUTOCOMPLETE_PARAMETERS = strFrom($url, '?');
        $HOLD_GET = $_GET;
        include ($url_file);
        $_GET = $HOLD_GET;
        $RESULT = $FORM_AUTOCOMPLETE_RESULT;
    }
    return $RESULT;
}
