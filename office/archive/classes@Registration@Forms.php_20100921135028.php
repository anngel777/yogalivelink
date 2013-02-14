<?php

// FILE: class.Contacts.php

class Registration_Forms extends BaseClass
{    
    private $Company_Concat         = '';    private $Form_Process_Step      = 0;
    public $Lookup_Lists            = array();
    private $Contact_Form           = false;
    private $Ajax                   = false;
    private $Wh_Cid                 = '';    private $Wh_Id                  = '';
    public $Form_Arrays_Step        = array();
    private $Language               = '';
    private $Translation            = '';
    public  $Add_Company_Info       = false;
    public  $Contact_Form_Javascript_Function = 'processContactUpdate();';

    public $Current_Step            = 0;
    public $PassedStep              = false;

    public $Encrypted_Code          = "";
    public $Encrypted_CC            = "";
    
    public $ExternalWHID            = 0;
    public $ExternalWHCID           = 0;

    public $CreateWHID              = false;    #Force a WHID to be created for the user after they're inserted in database
    public $CreateWHCID             = false;    #Force a WHCID to be created for the company after they're inserted in database
    public $CreateWhProfile         = false;    #Create session variable containing user record data
    public $CreateLogin             = false;    #Creates a login for this user - trigger after insert of a user
    public $RequireWhProfile        = false;    #Requires a session with user record data to exist before allowing continuation - WILL !NOT! CREATE ONE
    public $ProcessBilling          = false;    #If TRUE - we are processing the billing and we need to NOT write the CC info to database
    
    public $ContactRecord           = array();
    public $ClassID                 = 0;
    public $Success_Goto_Url;
    public $Log                     = '';

    public $HaveError               = false;
    public $HaveInfo                = false;
    public $HaveLog                 = false;
    public $MessageError            = '';
    public $MessageInfo             = '';
    public $MessageLog              = '';

    public $Profile_Keys            = '';
    public $Profile_Table           = '';

    public $TriggerCreateWHID_Table     = '';   #When creating a WHID - this is the table to make the update in
    public $TriggerCreateWHID_Id        = '';   #When creating a WHID - this is the index of the table to make the update in
    public $TriggerCreateWHCID_Table    = '';   #When creating a WHCID - this is the table to make the update in
    public $TriggerCreateWHCID_Id       = '';   #When creating a WHCID - this is the index of the table to make the update in

    private $TempFormArray              = '';
    public $No_Buttons                  = false;    #When set to true - form buttons will not be shown

    public function  __construct()
    {
        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => 'RAW',
           'Description' => 'Generic Registration Class',
           'Created'     => '2010-04-10',
           'Updated'     => '2010-04-11'
       );
        $this->ClassID = rand(1, 999);
        $this->Add_Submit_Name      = 'CONTACTS_SUBMIT_ADD';
       $this->Edit_Submit_Name     = 'CONTACTS_SUBMIT_EDIT';
       #$this->Table                = 'contacts';
       #$this->Index_Name           = 'contacts_id';
       #$this->Flash_Field          = 'contacts_id';
       #$this->Default_Sort         = 'last_name';  // field for default table sort
       $this->Default_Values       = array();


       #$this->GetLookupLists();

        $this->Field_Titles = array(
            'contacts_id'           => 'ID',
            'contacts.wh_id'        => 'WH-ID',
            'wh_password'           => 'WH Password',
            'contact_salutations.salutation'  => 'Salutation',
           'contacts.first_name'   => 'First Name',            
           'contacts.last_name'    => 'Last Name',            
           'contacts.middle_name'  => 'Middle Name',
           'contacts.name_badge'    => 'Name Badge Name',
           'companies.company_name'=> 'Company',
           'contacts.wh_cid'       => 'WH-CID',
           'contacts.phone_number' => 'Phone',
           'contacts.cell_number'  => 'Cell Number',
           'contacts.fax_number'   => 'Fax Number',
           'contacts.email_address'=> 'Email Address',
            'contacts.address_1'    => 'Address 1',
            'contacts.address_2'    => 'Address 2',
            'contacts.address_3'    => 'Address 3',
            'contacts.city'         => 'City',
            'contacts.state'        => 'State',
            'countries.country_name'        => 'Country',
            'contacts.postal_code'  => 'Postal Code',

            'languages.language_name'                          => 'Language',
            'test_account_flag'                                => 'Test Account',

            'contact_job_functions.job_function_name'          => 'Primary Job Function',
            'contacts.job_title'                               => 'Job Title',
            'contacts.primary_contact'                         => 'Primary Contact',

            'contacts.contact_comments'                        => 'Contact Comments',
            'contacts.email_subscriptions'                     => 'Email Subscriptions',
            'contacts.comments'                                => 'Comments',
            'contacts.changed_date'                            => 'Changed Date',
            'contacts.active'                                  => 'Active',
            'contacts.updated'                                 => 'Updated',
            'contacts.created'                                 => 'Created'
        );

        /*
        $this->Join_Array = array(
            'contact_salutations' => 'LEFT JOIN `contact_salutations`
                ON `contact_salutations`.`contact_salutations_id`=`contacts`.`contact_salutations_id`',
            'countries'  => 'LEFT JOIN `countries`
                ON `countries`.`country_code`=`contacts`.`country_code`',
            'companies'  => 'LEFT JOIN `companies`
                ON `companies`.`wh_cid`=`contacts`.`wh_cid`',
            'languages' => 'LEFT JOIN `languages`
                ON `languages`.`language_code`=`contacts`.`language_code`',
            'contact_job_functions' => 'LEFT JOIN `contact_job_functions`
                ON `contact_job_functions`.`contact_job_functions_id`=`contacts`.`contact_job_functions_id`',
            'contacts_billing' => 'LEFT JOIN `contacts_billing`
                ON `contacts_billing`.`wh_id`=`contacts`.`wh_id`',
        );
        */


        $this->Company_Concat = "CONCAT(company_name, ' - ', city, ', ', state, ' ', country_code)";
        $this->Default_Fields = 'contacts.wh_id,contacts.first_name,contacts.last_name,contacts.city,contacts.state,countries.name,companies.company_name';
        $this->Field_Values['test_account_flag'] = array(0=> 'No', 1 => 'Yes');
        $this->Default_Values['wh_password'] = Lib_Password::MakePassword();

        $this->Unique_Fields = '';

        $this->Autocomplete_Fields = array(
            'wh_cid'            => "companies|wh_cid|{$this->Company_Concat}",
            'language_code'     => "languages|language_code|CONCAT(language_code, ' - ', language_name)"
        );  // associative array: field => table|field|variable


        $this->Edit_Links  = qqn("
            <td align=`center` valign=`middle`><a target=`_blank` class=`stdbuttoni`
              href=`$this->Channel_Events_Site/contact_registration;contacts_id=@VALUE@`
              onclick=`\$('#TABLE_ROW_ID@IDX@_@VALUE@ td').css('background-color','#afa');`>Register</a></td>") . $this->Edit_Links;

        $this->Edit_Links_Count++;

    } // -------------- END __construct --------------
//==========================================================================================================================================

    public function ShowVariables()
    {
        $info = '';
        $info .= "<br />ShowVariables<hr>";
        $info .= "<ul>";
        $info .= "<li>ClassID => " . $this->ClassID . "</li>";
        $info .= "<li>Company_Concat => " . $this->Company_Concat . "</li>";
        $info .= "<li>Form_Process_Step => " . $this->Form_Process_Step . "</li>";
        $info .= "<li>Lookup_Lists => " . $this->Lookup_Lists . "</li>";
        $info .= "<li>Contact_Form => " . $this->Contact_Form . "</li>";
        $info .= "<li>Ajax => " . $this->Ajax . "</li>";
        $info .= "<li>Wh_Cid => " . $this->Wh_Cid . "</li>";
        $info .= "<li>Wh_Id => " . $this->Wh_Id . "</li>";
        $info .= "<li>Form_Arrays_Step => " . $this->Form_Arrays_Step . "</li>";
        $info .= "<li>Language => " . $this->Language . "</li>";
        $info .= "<li>Translation => " . $this->Translation . "</li>";
        $info .= "<li>Add_Company_Info => " . $this->Add_Company_Info . "</li>";
        $info .= "<li>Contact_Form_Javascript_Function => " . $this->Contact_Form_Javascript_Function . "</li>";
        $info .= "<li>Current_Step => " . $this->Current_Step . "</li>";
        $info .= "<li>PassedStep => " . $this->PassedStep . "</li>";
        $info .= "<li>CreateWHID => " . $this->CreateWHID . "</li>";
        $info .= "<li>CreateWhProfile => " . $this->CreateWhProfile . "</li>";
        $info .= "<li>UpdateWhProfile => " . $this->UpdateWhProfile . "</li>";
        $info .= "<li>RequireWhProfile => " . $this->RequireWhProfile . "</li>";
        $info .= "<li>ContactRecord => " . $this->ContactRecord . "</li>";
        $info .= "<li>Success_Goto_Url => " . $this->Success_Goto_Url . "</li>";

        $info .= "</ul>";
        $info .= "<br /><br />";

        #$info .= "Form_Arrays_Step => " . ArrayToStr($this->Form_Arrays_Step);

        return $this->AddMessage('info', $info);
    }


    protected function TriggerCreateWHID($db_last_insert_id)
    {
#echo "TriggerCreateWHID()";
        if ($this->CreateWHID) {
            # create a WH_ID
            $base   = 10000000000;
            $WH_ID  = $base + $db_last_insert_id;
            $this->SQL->UpdateRecord(array(
                'table' => $this->TriggerCreateWHID_Table,
                'key_values' => "wh_id = $WH_ID",
                'where' => "`{$this->TriggerCreateWHID_Id}`=$db_last_insert_id"
            ));
            $this->Wh_Id        = $WH_ID;
            $_SESSION['WH_ID']  = $this->Wh_Id;
        }
    }

    protected function TriggerCreateWHCID($db_last_insert_id)
    {
#echo "TriggerCreateWHCID()";
        if ($this->CreateWHCID) {
            # create a WH_ID
            $base   = 1000000;
            $WH_CID  = $base + $db_last_insert_id;
            $this->SQL->UpdateRecord(array(
                'table' => $this->TriggerCreateWHCID_Table,
                'key_values' => "wh_cid = $WH_CID",
                'where' => "`{$this->TriggerCreateWHCID_Id}`=$db_last_insert_id"
            ));
            $this->Wh_Cid        = $WH_CID;
            $_SESSION['WH_CID']  = $this->Wh_Cid;
        }
    }

    protected function TriggerAfterInsert($db_last_insert_id) // extended from parent
    {
#echo "TriggerAfterInsert()";
        # HANDLE CREATION OF WHID
        # =======================================================
        if ($this->CreateWHID) {
            $this->TriggerCreateWHID($db_last_insert_id);
        }
        if (!$this->CreateWHID && $this->ExternalWHID) {
            $this->Wh_Id        = $this->ExternalWHID;
            $_SESSION['WH_ID']  = $this->Wh_Id;
        }

        # HANDLE CREATION OF WHCID
        # =======================================================
        if ($this->CreateWHCID) {
            $this->TriggerCreateWHCID($db_last_insert_id);
        }
        if (!$this->CreateWHCID && $this->ExternalWHCID) {
            $this->Wh_Cid        = $this->ExternalWHCID;
            $_SESSION['WH_CID']  = $this->Wh_Cid;
        }
        
        # HANDLE CREATION OF USER ACCOUNT
        # =======================================================
        if ($this->CreateLogin) {

            $FormArray = $this->TempFormArray;

            $tmp_array = array(
                'email_address'     => $FormArray['email_address'],
                'first_name'        => $FormArray['first_name'],
                'last_name'         => $FormArray['last_name'],
                'created_by'        => 'Registration System',
                'module_roles'      => $FormArray['module_roles'],
                'wh_id'             => $this->Wh_Id,
                'time_zones_id'     => '5',
                'active'            => '1'
            );

            $keys       = '';
            $values     = '';
            foreach ($tmp_array AS $key => $val)
            {
                $val        = addslashes($val);
                $keys      .= "`$key`, ";
                $values    .= "'$val', ";
            }


            # 1. Add the user login account
            # ========================================
            $keys       = substr($keys, 0, -2);
            $values     = substr($values, 0, -2);
            $result     = $this->SQL->AddRecord('admin_users', $keys, $values);

            # 2. Update the ah_contacts record
            # ========================================
            $admin_users_id = $this->SQL->Last_Insert_Id;
            $this->SQL->UpdateRecord(array(
                'table' => $this->TriggerCreateWHID_Table,
                'key_values' => "admin_users_id = $admin_users_id",
                'where' => "`{$this->TriggerCreateWHID_Id}`={$this->Wh_Id}"
            ));




            $QUERY = $this->SQL->Db_Last_Query . '<br /><br />'. ArrayToStr($result);
            #echo $QUERY;
            #exit();
            if ($result) {
                #$_SESSION['ADMIN_USERS_ID'] = $Last_Insert_Id;
            } else {
                echo "UNABLE TO CREATE LOGIN ID";
            }
        }


        # HANDLE PAYMENT PROCESSING
        # =======================================================
        if ($this->ProcessBilling) {
        
            # Get all the fields for billing
            $FormArray = $this->TempFormArray;

            # Create the combo of information
            $COMBO = "
            {$FormArray['CARDTYPE']}|
            {$FormArray['CARDNUMBER']}|
            {$FormArray['CARDNAME']}|
            {$FormArray['CARDEXPM']}|
            {$FormArray['CARDSC']}|
            {$FormArray['bill_address_1']}|
            {$FormArray['bill_address_2']}|
            {$FormArray['bill_address_3']}|
            {$FormArray['bill_city']}|
            {$FormArray['bill_state']}|
            {$FormArray['bill_country_code']}|
            {$FormArray['bill_postal_code']}";
            
            # Create theencrypted string and set to SESSION VAR
            $PE                         = new Payment_PaymentEncrypt();
            $cstring                    = $PE->EncryptStringHex($COMBO, $this->Encrypted_Code);
            $this->Encrypted_CC         = $cstring;
            $_SESSION['Encrypted_CC']   = $this->Encrypted_CC;
            
            # Remove the card info from the database
            $new_card = 'xxxx-xxxx-xxxx-' . substr($FormArray['CARDNUMBER'], -4);
            $admin_users_id = $this->SQL->Last_Insert_Id;
            $this->SQL->UpdateRecord(array(
                'table'         => "ah_billing",
                'key_values'    => "CARDNUMBER = '{$new_card}'",
                'where'         => "`billing_id`={$db_last_insert_id}"
            ));
            
            #echo "<br />Encrypted_CC ==> " . $this->Encrypted_CC;
            #echo "<br />Last_Insert_Id ==> " . $this->SQL->Last_Insert_Id;
            #echo "<br />db_last_insert_id ==> " . $db_last_insert_id;
            #echo "<br />Last Query ==> " . $this->SQL->Db_Last_Query;
            #exit();
        }

        ###echo "<br />this->Wh_Id =====> " . $this->Wh_Id;
        $this->MarkPassedStep('set', 'set');
        $this->SuccessRedirect();
    }


    protected function TriggerAfterUpdate($id, $id_field='', $tables='', $span_where='', $joins='')
    {
        /*
        // extend this function to add DB calls after a record has been added
        if (empty($id_field)) {
            $id_field = $this->Index_Name;
        }

        $this->SQL->UpdateRecord(array(
            'table' => 'contacts,companies',
            'key_values' => 'contacts.contact_company_name=companies.company_name',
            'where' => "`$id_field`=$id
                AND `contacts`.`wh_cid` != 0
                AND `contacts`.`wh_cid` = `companies`.`wh_cid`
                AND `contacts`.`contact_company_name` != `companies`.`company_name`"
        ));
        */

        $this->MarkPassedStep('set', 'set');
        $this->SuccessRedirect();
    }

    public function PostProcessFormValues($FormArray)
    {
        #$FormArray['changed_date'] = 'NOW()';
        $this->TempFormArray = $FormArray;

#echo "<br />FormArray ===> " . ArrayToStr($FormArray);

        return $FormArray;
    }


    public function ContactAddRecord($ajax=false, $wh_cid='', $text=false)
    {
        $this->Contact_Form             = true;
        $this->Ajax                     = $ajax;
        $this->Wh_Cid                   = $wh_cid;
        $this->User_Name                = 'Contact';
        $_SESSION['ContactAddRecord']   = 'ContactAddRecord';
        if ($text) {
            return $this->AddRecordText();
        } else {
            echo $this->AddRecordText();
        }
    }
    
# ==================================================================================================
# ==================================================================================================
# ==================================================================================================
    public function BillingAddRecord($ajax=false, $wh_cid='', $text=false)
    {
        $this->Contact_Form             = true;
        $this->Ajax                     = $ajax;
        $this->Wh_Cid                   = $wh_cid;
        $this->User_Name                = 'Contact';
        $_SESSION['ContactAddRecord']   = 'ContactAddRecord';
        if ($text) {
            return $this->AddRecordText();
        } else {
            echo $this->AddRecordText();
        }
    }
# ==================================================================================================
# ==================================================================================================
# ==================================================================================================
    
    
    
    
    
    
    
    public function CompanyAddRecord($ajax=false, $wh_cid='', $text=false)
    {
        $this->ContactAddRecord($ajax, $wh_cid, $text);
        #$this->ContactAddRecord($ajax, $wh_cid, 'true');
    }
    /*
    public function BillingAddRecord($ajax=false, $wh_cid='', $text=false)
    {
        $this->ContactAddRecord($ajax, $wh_cid, $text);
    }
    */

    public function ContactEditRecord($wh_id, $ajax=false, $wh_cid='')
    {
        if (!$wh_id) {
            return;
        }
        #$this->Contact_Form     = true;
        $this->Ajax             = $ajax;
        $this->Wh_Id            = $wh_id;
        #$this->Wh_Cid           = $wh_cid;
        #$this->User_Name        = 'Contact';
        return $this->EditRecordText($wh_id, 'wh_id');
    }

    public function CompanyEditRecord($wh_cid, $ajax=false)
    {
        if (!$wh_cid) {
            return;
        }
        #$this->Contact_Form     = true;
        $this->Ajax             = $ajax;
        $this->Wh_Cid           = $wh_cid;
        #$this->User_Name        = 'Contact';
        return $this->EditRecordText($wh_cid, 'wh_cid');
    }

    public function PrePopulateFormValues($id, $field='')
    {
        parent::PrePopulateFormValues($id, $field);
        if ($this->Contact_Form) {
            $this->SetFormArrays();
            $FormArray = ProcessFormNT($this->Form_Data_Array_Edit, $this->Error);
            if ($this->Error) {
                $_POST[$this->Edit_Submit_Name] = 1;  // force form to show missing elements
            }
        }
    }


    public function SetFormArrays() // overrides parent
    {
        global $FORM_VAR, $FormPrefix, $Mask_Integer;
        $E = chr(27);

#echo "<h1>{$this->Current_Step}</h1>";
#echo $this->Form_Arrays_Step[$this->Current_Step];


        $base_array = "form|$this->Action_Link|post|contact_edit_form|$E";
        $base_array .= $this->Form_Arrays_Step[$this->Current_Step];
        $base_array .= "code|<br /><br />|$E";

        if ($this->No_Buttons) {
            $button = "";
        } else {
            $button = ($this->Ajax)? "
               button|[T~CE_S_0071]|$this->Contact_Form_Javascript_Function|$E
               code|<input type=\"hidden\" name=\"$this->Edit_Submit_Name\" value=\"{$FORM_VAR['submit_click_text']}\" />|$E"
            : "submit|[T~BTN_SUBMIT]|$this->Edit_Submit_Name|$E";
            $add_button = "submit|[T~BTN_SUBMIT]|$this->Add_Submit_Name|$E";
        }



        $this->Form_Data_Array_Add = "
            $base_array
            $add_button
            endform|$E
        ";

        $this->Form_Data_Array_Edit = "
            $base_array
            $button
            endform|$E
        ";

        $this->Translation = Lib_Singleton::GetInstance('Translations');
        if (empty($this->Translation->LANGUAGE)) {
            $this->Language = $this->Translation->SetLanguage();
        } else {
            $this->Language = $this->Translation->LANGUAGE;
        }

        $this->Form_Data_Array_Add  = $this->Translation->TranslateText($this->Form_Data_Array_Add, $this->Language);
        $this->Form_Data_Array_Edit = $this->Translation->TranslateText($this->Form_Data_Array_Edit, $this->Language);

#echo "<br /><br /><br />";
#echo $this->Form_Data_Array_Add;
#echo $this->Form_Data_Array_Edit;
    }


    public function GetAllContactDetails($wh_id)
    {
        $this->AddLog("GetAllContactDetails($wh_id)");

        $wh_id      = intOnly($wh_id);
        $keys       = $this->Profile_Keys;
        $table      = $this->Profile_Table;
        $joins      = implode("\n", array_values($this->Join_Array));

        $RESULT     = db_GetRecord($table, $keys, "`$table`.`wh_id`=$wh_id", $joins);


        if ($RESULT) {
            unset($RESULT['contact_company_name']);
            return $RESULT;
        } else {
            $INFO = $this->SQL->Db_Last_Query . '<br /><br />'. ArrayToStr($RESULT);
            $this->AddMessage('error', $INFO, 'GetAllContactDetails() QUERY');
            $this->ClassCrash();
        }

    }

    public function SetWhProfileFromWhid($WHID)
    {
        $this->AddLog("SetWhProfileFromWhid($WHID)");

        $this->LogoutWhProfile();                       #CLEAR ANY EXISTING PROFILE

        $WHID = intOnly($WHID);
        if (!$WHID) {
            return;
        }

        $record = $this->GetAllContactDetails($WHID);   #GET THE CONTACT RECORD
        if ($record) {
            $this->AddLog("RECORD FOUND");
            $_SESSION['WH_ID'] = $WHID;
            $_SESSION['WH_PROFILE'] = $record;
        } else {
            $this->AddLog("RECORD NOT FOUND");
            echo "UNABLE TO CREATE SESSION FROM WHID INFORMATION :: WHID => $WHID";
        }
    }

    public function CheckWhProfileFromWhid($WHID)
    {
        $this->AddLog("CheckWhProfileFromWhid($WHID)");

        # 1. check to see if session exists
        # 2. check if session matches whid
        $WHID       = intOnly($WHID);
        $record     = Session('WH_PROFILE');
        $result     = (Session('WH_PROFILE') && $record['wh_id']==$WHID) ? true : false;

        return $result;
    }

    public function LogoutWhProfile()
    {
        $this->AddLog("LogoutWhProfile()");

        if (isset($_SESSION['WHPROFILE'])) unset($_SESSION['WHPROFILE']);
        if (isset($_SESSION['WH_PROFILE'])) unset($_SESSION['WH_PROFILE']);
        if (isset($_SESSION['WHID'])) unset($_SESSION['WHID']);
        if (isset($_SESSION['WH_ID'])) unset($_SESSION['WH_ID']);
        if (isset($_SESSION['WH_CONTACT_RECORD'])) unset($_SESSION['WH_CONTACT_RECORD']);
    }

    public function FN_CreateWhProfile($WHID)
    {
        $this->AddLog("FN_CreateWhProfile($WHID)");

        $WHID   = intOnly($WHID);
        if ($this->CreateWhProfile) {
            $this->SetWhProfileFromWhid($WHID);
        }
    }

    public function FN_RequireWhProfile($WHID)
    {
        $this->AddLog("FN_RequireWhProfile($WHID)");

        if ($WHID == 99999999999) $this->RequireWhProfile = false;
        if ($this->RequireWhProfile) {
            $WHID       = intOnly($WHID);
            $record     = Session('WH_PROFILE');
            $result     = (Session('WH_PROFILE') && $record['wh_id']==$WHID) ? true : false;
        } else {
            $result = true;
        }
        return $result;
    }


    private function ClassCrash()
    {
        $this->OutputLogs();
        exit();
    }

    public function SuccessRedirect()
    {
        $this->AddLog("SuccessRedirect()");

        if ($this->Success_Goto_Url && $this->PassedStep) {
            # 1. Create a s[WH_PROFILE] and s[WHID] record
            $this->FN_CreateWhProfile($this->Wh_Id);

            # 2. Unset passed step
            $this->MarkPassedStep('unset', 'unset');

            # 3. Ouput error logs
            if ($this->HaveError) {
                $this->ClassCrash();
            }

            # 4. redirect to next step
            header("Location: {$this->Success_Goto_Url}");
        } else {
            $error = $this->AddMessage('error', "
            <b>Class:</b> class.Contacts<br />
            <b>Function:</b> SuccessRedirect()<br />
            <b>Error:</b> No Variable 'Success_Goto_Url'");
            echo $error;
        }
    }


    public function OutputLogs()
    {
        echo $this->MessageError . '<br />';
        echo $this->MessageLog . '<br />';
        echo $this->MessageInfo;
    }


    public function AddMessage($TYPE='', $INFO, $TITLE='', $SKIPADD=false)
    {
        switch ($TYPE) {
            case 'info':
                $color = '#5BA0C7'; #blue
                $title = ($TITLE != '') ? "INFORMATION :: {$TITLE}" : 'INFORMATION';
                $this->HaveInfo = true;
                break;
            case 'error':
                $color = '#990000'; #red
                $title = ($TITLE != '') ? "ERROR :: {$TITLE}" : 'ERROR';
                $this->HaveError = true;
                break;
            case 'log':
                $color = '#5EC05E'; #green
                $title = ($TITLE != '') ? "LOG :: {$TITLE}" : 'LOG';
                $this->HaveLog = true;
                break;
        }

        $output = "
            <div style='padding:20px;'>
                <div style='color:#fff; font-weight:bold; font-size:14px; background-color:{$color}; padding:5px; border:1px solid {$color};'>{$title}</div>
                <div style='color:#000; font-size:12px; padding:10px; border:1px solid {$color};'>{$INFO}</div>
            </div>";

        switch ($TYPE) {
            case 'info':
                $this->MessageInfo .= $output;
                break;
            case 'error':
                $this->MessageError .= $output;
                break;
            case 'log':
                $this->MessageLog .= $output;
                break;
        }

        return $output;
    }

    public function AddLog($INFO)
    {
        $this->Log .= "{$INFO}<br />";
    }

    public function OutputLog()
    {
        $INFO = $this->Log;
        $this->AddMessage('log', $INFO, 'LOG');
    }

    public function MarkPassedStep($CLASS='', $SESSION='')
    {
        $this->AddLog("MarkPassedStep($CLASS, $SESSION)");

        switch ($CLASS) {
            case 'set':
                $this->PassedStep = true;
                break;
            case 'unset':
                $this->PassedStep = false;
                break;
            default:
                break;
        }

        switch ($SESSION) {
            case 'set':
                $_SESSION['PASSED_STEP'] = true;
                break;
            case 'unset':
                if (isset($_SESSION['PASSED_STEP'])) unset($_SESSION['PASSED_STEP']);
                break;
            default:
                break;
        }
    }

    public function OutputQuery() {
        echo '<br />Last Query ===> ' . $this->SQL->Db_Last_Query;
    }
}  // -------------- END CLASS --------------