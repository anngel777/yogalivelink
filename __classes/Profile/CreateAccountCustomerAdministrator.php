<?php

/* ========================================================================== 
    FUNCTION :: Profile_CreateAccountCustomerAdministrator
    
    Create "Customer || Administrator" accounts
    
    Used by administrators to create accounts in the website. This
    class will create all account information in all required tables.
    
# ========================================================================== */

class Profile_CreateAccountCustomerAdministrator extends BaseClass
{
    public $Registration_Email_Template_Id          = 3;
    public $Registration_Admin_Email_Template_Id    = 5;
    
    public $User_Type_Customer      = false;
    public $Force_Email_Unique      = false;
    public $Force_Email_Not_Unique  = false;
    public $Allow_Email_Not_Unique  = false;
    public $URL_Success_Redirect    = '';
    
    public $wh_id                   = 0;
    public $num_free_credits        = 5;
    public $num_paid_credits        = 5;
    
    public $Admin_Module_Role               = 0;
    public $Admin_Class_Role                = 0;
    
    
    public $customer_module_roles       = 36;
    public $customer_class_roles        = 0;
    public $administrator_module_roles  = 0;
    public $administrator_class_roles   = 0;
    
    public $ShowArray               = false;
    public $ShowQuery               = false;
    
    public $record_total_success    = true;
    
    
    
    public $session_cancel_within_time_hrs    = 1;
    
    public $table_credits                   = 'credits';
    public $credits                         = array();
    public $credit_count_free               = 0;
    public $credit_count_purchase           = 0;
    public $credit_count_other              = 0;
    public $credits_refund_conf_number      = '';
    
    
    public $table_sessions                  = 'sessions';
    public $table_sessions_checklists       = 'session_checklists';
    public $sessions                        = array();
    public $sessions_booked_count           = 0;
    
    public $Table_Timezones                 = 'time_zones';
    public $Table_Contacts                  = 'contacts';
    
    
    
    public function  __construct()
    {
        $this->SetSQL();
        
        $this->Close_On_Success = false;
        
        $created_by = (isset($_SESSION['USER_LOGIN']['USER_NAME'])) ? $_SESSION['USER_LOGIN']['USER_NAME'] : 'SYSTEM';
        
        $this->Default_Values = array(
            'language_code'                 => 4,
            'time_zones_id'                 => 5,
            'created_by'                    => $created_by,
            'contact_discounts_id'          => 1,
            
            'AddContacts'                   => 1,
            'AddContactsAccount'            => 1,
            'AddContactsBillingStorage'     => 1,
            'AddCreditsFree'                => 0,
            'AddCreditsPurchased'           => 0,
            
            'card_type'                     => 'VISA',
            'card_number_reference'         => '12-XAS-34',
            'card_month'                    => 11,
            'card_year'                     => 2666,
            'card_ccv'                      => 666,
            
            'last_name'                     => 'Testerson',
            'email_address'                 => '##.Testerson@mailwh.com',
            'created'                       => date("Y-m-d H:i:s"),
        );
        
        //$this->Unique_Fields        = 'email_address';
        
        # SETUP EMAIL SUBSCRIPTIONS
        # ===================================================================================
        $email_list = '';
        foreach ($GLOBALS['EMAIL_SUBSCRIPTIONS_INSTRUCTOR'] as $type => $description) {
            $email_list .= "$type,";
        }
        $email_list = substr($email_list, 0, -1);
        $this->Default_Values['email_subscriptions'] = $email_list;
    }
    
    public function SetSQL()
    {
        if (empty($this->SQL)) {
            $this->SQL = Lib_Singleton::GetInstance('Lib_Pdo');
        }
    }
    
    public function Execute()
    {
        $this->AddRecord();   
    }
    
    public function ExecuteUserSignup()
    {
        $this->User_Type_Customer = true;
        $this->Default_Values = array(
            'AddContacts'                   => 0,
            'AddContactsAccount'            => 0,
            'AddContactsBillingStorage'     => 0,
            'AddCreditsFree'                => 0,
            'AddCreditsPurchased'           => 0,
            'created_by'                    => 'Website Registration',
            'created'                       => date("Y-m-d H:i:s"),
        );
        return $this->AddRecordText();
    }
    
    public function UserSignupSpecialSubmitButton() 
    {
        global $FORM_VAR;
        
	    $onclick 	= "this.value='{$FORM_VAR['submit_click_text']}';";
		$id 		= Form_GetIdFromVar($this->Add_Submit_Name);
		$name 		= $this->Add_Submit_Name;
		$btn_temp 	= MakeButton('positive', 'NEXT', '', '', $id, $onclick, 'submit', $name);
        
        return $btn_temp;
    }
    
    public function SetFormArrays()
    {
        $account_types      = "Customer|Administrator";
        $card_types         = "MasterCard|VISA|AmEx";
        $country_types      = "USA|Canada";
        $agree_types        = "1=I Agree";
        
        $timezone_list      = $this->SQL->GetAssocArray($this->Table_Timezones, 'time_zones_id', 'tz_display', '`active`=1');
        $timezone_types     = Form_AssocArrayToList($timezone_list);
        
        $agree_link_terms       = "<div style='font-size:10px;'><a href='http://www.yogalivelink.com/terms_and_conditions' target='_new'>Click to View Terms and Conditions (opens in new window)</a></div>";
        $agree_link_liability   = "<div style='font-size:10px;'><a href='http://www.yogalivelink.com/liability_waiver' target='_new'>Click to View Liability Waiver (opens in new window)</a></div>";
        $agree_link_privacy     = "<div style='font-size:10px;'><a href='http://www.yogalivelink.com/privacy_policy' target='_new'>Click to View Privacy Policy (opens in new window)</a></div>";
        /*
        $agree_link         = "
        <a href='#' target="" onclick=\"parent.appformCreateOverlay('Terms and Conditions', 'instructor_profile/instructor_profile_view','apps'); return false;\">
                            View Instructor Bio
                        </a>";
        */
        
        $style_fieldset     = "style='color:#990000; font-size:14px; font-weight:bold;'";
        
        if ($this->User_Type_Customer) {
            $base_array = array(
                "form|$this->Action_Link|post|db_edit_form",
                
                "hidden|account_type|Customer",
                "hidden|super_user|0",
                #"hidden|module_roles|{$this->Admin_Module_Role}",
                #"hidden|class_roles|{$this->Admin_Class_Role}",
                "hidden|language_code|4", //Language = English
                "hidden|contact_discounts_id|1",
                
                
                'hidden|AddContacts|1',
                'hidden|AddContactsAccount|1',
                'hidden|AddContactsBillingStorage|0',
                "hidden|AddCreditsFree|0",
                "hidden|AddCreditsPurchased|0",
                "hidden|SendEmail|1",
                
                "hidden|type_customer|1",
                "hidden|type_administrator|0",
                "hidden|type_instructor|0",
                "hidden|type_instructor_standard|0",
                "hidden|type_instructor_therapy|0",
                "hidden|instructor_account_limited|0",
                
                "hidden|created|0",
                //'text|Created|created|Y|60|100',
                
                #"fieldset|User Information|options_fieldset|$style_fieldset",
                    'text|First Name|first_name|Y|60|100',
                    'text|Last Name|last_name|Y|60|100',
                    'text|Email Address|email_address|Y|60|100',
                    
                    'code|<br />',
                    
                    'text|Address 1|address_1|N|60|100',
                    'text|Address 2|address_2|N|60|100',
                    'text|Address 3|address_3|N|60|100',
                    'text|City|city|N|60|100',
                    'text|State|state|N|60|100',
                    'text|Postal Code|postal_code|N|50|50',
                    "select|Country|country_code|Y||$country_types",
                    'phone|Phone Number|phone_home|Y|',
                    
                    "code|<br />",
                    
                    "select|Time Zone|time_zones_id|Y||$timezone_types",
                    
                    "code|<br />",
                    
                    
                    "select|Terms &amp; Conditions|terms_conditions_agree|Y||$agree_types",
                    "info||$agree_link_terms",
                    "select|Liability Waiver|liability_waiver_agree|Y||$agree_types",
                    "info||$agree_link_liability",
                    #"select|Privacy Policy|privacy_policy_agree|Y||$agree_types",
                    #"info||$agree_link_privacy",
                    
                    
                    
                    "code|<br />",
                    "code|<br />",
                    
                    #'text|Skype Username|skype_username|N|45|45',
                    #'text|Skype Phone Number|skype_phone_number|N|45|45',
                #"endfieldset",
                
                'code|<div style="display:none;">',
                    'textarea|Comments|comments|N|60|4',
                    'text|Contact Salutations Id|contact_salutations_id|N|3|3',
                    'text|Phone Work|phone_work|N|50|50',
                    'text|Phone Cell|phone_cell|N|50|50',
                    'text|Middle Name|middle_name|N|60|100',
                    
                    'text|Password|password|N|60|80',
                    'text|Created By|created_by|N|60|64',
                    'text|Language Code|language_code|N|3|3',
                    #'checkboxlistset|Email Subscriptions|email_subscriptions|N||ICC|IRD|ISS|TST|ICAS|WEBINAR|OTHER',
                    'text|Email Subscriptions|email_subscriptions|N|60|100',

                    
                'code|</div>',
                
            );        
        } else {
            $base_array = array(
                "form|$this->Action_Link|post|db_edit_form",

                "fieldset|User Information|options_fieldset|$style_fieldset",
                    'text|First Name|first_name|Y|60|100',
                    'text|Last Name|last_name|Y|60|100',
                    
                    'text|Address 1|address_1|N|60|100',
                    'text|Address 2|address_2|N|60|100',
                    'text|Address 3|address_3|N|60|100',
                    'text|City|city|N|60|100',
                    'text|State|state|N|60|100',
                    'text|Postal Code|postal_code|N|50|50',
                    'text|Country Code|country_code|N|5|5',
                    
                    'text|Email Address|email_address|Y|60|100',
                "endfieldset",
                
                
                "fieldset|Billing Information|options_fieldset|$style_fieldset",
                    "select|Card Type|card_type|Y||$card_types",
                    'text|Card Reference Number|card_number_reference|N|60|100',
                    'text|Card Month|card_month|N|60|10',
                    'text|Card Year|card_year|N|60|10',
                    'text|Card CCV|card_ccv|N|60|10',
                "endfieldset",
                
                
                "fieldset|Account Information|options_fieldset|$style_fieldset",
                    "select|Account Type|account_type|Y||$account_types",
                    'checkbox|Super User|super_user||1|0',
                    #'text|Module Roles|module_roles|N|60|255',
                    #'text|Class Roles|class_roles|N|60|255',
                "endfieldset",
                
                "fieldset|Additional Information|options_fieldset|$style_fieldset",
                    'checkbox|Send Registration Email|SendEmail||1|0',
                    'checkbox|Add Contacts|AddContacts||1|0',
                    'checkbox|Add Contacts Account|AddContactsAccount||1|0',
                    'checkbox|Add Contacts Billing Storage|AddContactsBillingStorage||1|0',
                    "checkbox|Add {$this->num_free_credits} Credits Free|AddCreditsFree||1|0",
                    "checkbox|Add {$this->num_paid_credits} Credits Purchased|AddCreditsPurchased||1|0",
                    
                    'code|<br />',
                    
                    'textarea|Comments|comments|N|60|4',
                    #'text|Skype Username|skype_username|N|45|45',
                    #'text|Skype Phone Number|skype_phone_number|N|45|45',
                "endfieldset",
                
                'code|<div style="display:none;">',
                    'text|Contact Salutations Id|contact_salutations_id|N|3|3',
                    'text|Phone Home|phone_home|N|50|50',
                    'text|Phone Work|phone_work|N|50|50',
                    'text|Phone Cell|phone_cell|N|50|50',
                    'text|Time Zones Id|time_zones_id|N|11|11',
                    'text|Middle Name|middle_name|N|60|100',
                    'text|Password|password|N|60|80',
                    'text|Created By|created_by|N|60|64',
                    'text|Language Code|language_code|N|3|3',
                    'text|Discounts ID|contact_discounts_id|N|3|3',
                    
                    #'checkboxlistset|Email Subscriptions|email_subscriptions|N||ICC|IRD|ISS|TST|ICAS|WEBINAR|OTHER',
                    'text|Email Subscriptions|email_subscriptions|N|60|100',
                    'checkbox|Type Customer|type_customer||1|0',
                    'checkbox|Type Instructor|type_instructor||1|0',
                    'checkbox|Type Administrator|type_administrator||1|0',
                'code|</div>',
                
            );
        }
        
        
        
        if ($this->Action == 'ADD') {
            if (!$this->User_Type_Customer) { 
                $base_array[] = "submit|Add Record|$this->Add_Submit_Name"; 
            } else {
                $btn = $this->UserSignupSpecialSubmitButton();
                $base_array[] = "info||$btn";
            }
            
            
            
            $base_array[] = 'endform';
            $this->Form_Data_Array_Add = $base_array;
        } else {
            if (!$this->User_Type_Customer) { $base_array[] = "submit|Update Record|$this->Edit_Submit_Name"; }
            $base_array[] = 'endform';
            $this->Form_Data_Array_Edit = $base_array;
        }
    }
    
    
    
    public function PostProcessFormValues($FormArray)
    {
        # RUNS AS A TRANSACTION SO EVERYTHING MUST PASS OR IT WILL NOT COMPLETE
        # ================================================================================
        # ================================================================================
        #echo ArrayToStr($FormArray);
        #exit();
        $show_msg = ($this->User_Type_Customer) ? false : true;
        $show_msg = true;
        
        
        
        # DON'T CONTINUE IF THERE ARE EXISTING FORM ERRORS
        # ==========================================================
        if ($this->Error) {
            return false;
        }
        
        
        # VERIFY IF EMAIL ADDRESS IS UNIQUE
        # ==========================================================
        $email              = $FormArray['email_address'];
        
        if (!$this->Allow_Email_Not_Unique) {
            $email_is_unique    = $this->SQL->IsUnique($this->Table_Contacts, 'email_address', $email);
            $email_is_unique    = ($this->Force_Email_Unique) ? true : $email_is_unique;
            $email_is_unique    = ($this->Force_Email_Not_Unique) ? false : $email_is_unique;
            
            if (!$email_is_unique) {
                $this->Error = "[T~EMAIL_NOT_UNIQUE]";
                return false;
            }
        }
        
        
        
        # FIGURE OUT THE ACCOUNT TYPE
        # ==========================================================
        switch($FormArray['account_type']) {
            case 'Customer':
                $FormArray['type_customer']         = 1;
                $FormArray['type_instructor']       = 0;
                $FormArray['type_administrator']    = 0;
                $FormArray['module_roles']          = $this->customer_module_roles;
                $FormArray['class_roles']           = $this->customer_class_roles;
            break;
            case 'Administrator':
                $FormArray['type_customer']         = 0;
                $FormArray['type_instructor']       = 0;
                $FormArray['type_administrator']    = 1;
                $FormArray['module_roles']          = $this->administrator_module_roles;
                $FormArray['class_roles']           = $this->administrator_class_roles;
            break;
        }
        
        
        
        # START TRANSACTION
        # ============================================================
        $this->SQL->StartTransaction();
        
        
        
        
        # NOW ADD THE ACCOUNT STUFF
        # ==========================================================
            
            # ADD AN ACCOUNT - contacts
            # ========================================================================
            if ($FormArray['AddContacts']) {
                if ($show_msg) echo "<br /><br />Adding [contacts] record";
                $this->AddContacts($FormArray);
            }
            
            
            # ADD ACCOUNT SETTINGS - contacts_account
            # ========================================================================
            if ($FormArray['AddContactsAccount']) {
                if ($show_msg) echo "<br /><br />Adding [contacts_account] record";
                $this->AddContactsAccount($FormArray);
            }
            
            
            # ADD BILLING INFORMATION - contacts_billing_storage
            # ========================================================================
            if ($FormArray['AddContactsBillingStorage']) {
                if ($show_msg) echo "<br /><br />Adding [contacts_billing_storage] record";
                $this->AddContactsBillingStorage($FormArray);
            }
            
            
            # ADD FREE CREDITS - credits
            # ========================================================================
            if ($FormArray['AddCreditsFree']) {
                if ($show_msg) echo "<br /><br />Adding [credits] record - FREE CREDITS";
                $this->AddCreditsFree($FormArray);
            }
            
            
            # ADD PURCHASED CREDITS - credits
            # ========================================================================
            if ($FormArray['AddCreditsPurchased']) {
                if ($show_msg) echo "<br /><br />Adding [credits] record - PURCHASED CREDITS";
                $this->AddCreditsPurchased($FormArray);
            }
            
            
            
        # DETERMINE IF RECORD TOTALLY ADDED
        # COMPLETE TRANSACTION
        # ==========================================================
        if ($this->record_total_success) {
            # COMMIT TRANSACTION
            # =======================
            $this->SQL->TransactionCommit();
            
            # SEND CONFIRMATION EMAIL
            # ========================================================================
            if ($FormArray['SendEmail']) {
                if ($show_msg) echo "<br /><br />Sending Confirmation Email";
                $this->EmailConfirmationRegistration($FormArray);
                $this->EmailConfirmationRegistrationAdministrator($FormArray);
            }            
            
            # DIRECT TO SUCCESSS MESSAGE
            # =======================
            if ($this->URL_Success_Redirect) {
                header("Location: $this->URL_Success_Redirect");
            }
            
        } else {
            # ROLL-BACK TRANSACTION
            # =======================
            if ($show_msg) echo "<br /><br /><div class='error_message'>ROLLING BACK TRANSACTION</div>";
            $this->Error = "[T~UNKNOWN_REGISTRATION_ERROR]";
            $this->SQL->Rollback();
        }
        
        
        
        if ($show_msg) {
            $success_fail_message = ($this->record_total_success) ? "<span style='color:blue;'>RECORD ADDED SUCCESSFULLY</span>" : "<span style='color:#990000;'>RECORD FAILED TO ADDED SUCCESSFULLY</span>";
            echo "<h2>$success_fail_message</h2>";
            echo "<h2>Created WHID: $this->wh_id</h2>";
        }
        
        
        
        
        # UNSET FORM VALUES
        # ==========================================================
        unset($FormArray);
        
        $this->Error = "Added Successfully";
        
    }
    
    
    
    
    
    public function AddContacts(&$FormArray)
    {
        # 1. DO THIS INITIAL RECORD CREATION
        # ================================================
        $db_record = array(
                'first_name'            => $FormArray['first_name'],
                'last_name'             => $FormArray['last_name'],
                'email_address'         => $FormArray['email_address'],
                'super_user'            => $FormArray['super_user'],
                'created_by'            => $FormArray['created_by'],
                'type_customer'         => $FormArray['type_customer'],
                
                'type_administrator'    => $FormArray['type_administrator'],
                
                'type_instructor'               => $FormArray['type_instructor'],
                'type_instructor_standard'      => $FormArray['type_instructor_standard'],
                'type_instructor_therapy'       => $FormArray['type_instructor_therapy'],
                'instructor_account_limited'    => $FormArray['instructor_account_limited'],
                
                'contact_discounts_id'  => $FormArray['contact_discounts_id'],
                
                'module_roles'          => $FormArray['module_roles'],
                'class_roles'           => $FormArray['class_roles'],
                'email_subscriptions'   => $FormArray['email_subscriptions'],
                'terms_conditions_agree'        => $FormArray['terms_conditions_agree'],
                'liability_waiver_agree'        => $FormArray['liability_waiver_agree'],
                'privacy_policy_agree'          => $FormArray['privacy_policy_agree'],
                'address_1'                     => $FormArray['address_1'],
                'address_2'                     => $FormArray['address_2'],
                'address_3'                     => $FormArray['address_3'],
                'city'                          => $FormArray['city'],
                'state'                         => $FormArray['state'],
                'postal_code'                   => $FormArray['postal_code'],
                'time_zones_id'                 => $FormArray['time_zones_id'],
                'phone_home'                    => $FormArray['phone_home'],
                'phone_work'                    => $FormArray['phone_work'],
                'phone_cell'                    => $FormArray['phone_cell'],
                'created'                       => $FormArray['created'],
            );
        $this->AddRecordLoc('contacts', $db_record);
        $db_last_insert_id = $this->SQL->Last_Insert_Id;
        $this->wh_id = $db_last_insert_id + 1000000;
        
        
        # 2. CREATE PASSWORD FOR USER
        # ================================================
        $Password_Syllables     = 2;
        $Password_Characters    = 'CN';
        $new_password           = Lib_Password::MakePassword($Password_Syllables, $Password_Characters);
        $update_password        = Lib_Password::GetPasswordHash($new_password);
        
        
        # 3. UPDATE USER RECORD
        # ================================================
        $db_record = array(            
                'wh_id'         => $this->wh_id,
                'password'      => $update_password,
            );
        $where = "`contacts_id`=$db_last_insert_id";
        $this->UpdateRecordLoc('contacts', $db_record, $where);
        
        $FormArray['wh_id'] = $this->wh_id;
        $FormArray['password'] = $new_password;
    }
    
    public function AddContactsAccount($FormArray)
    {
        $db_record = array(            
                'wh_id'         => $this->wh_id,
                'setting'       => 'enabled',
                'value'         => 'true',                
            );
        $this->AddRecordLoc('contacts_account', $db_record);
    }
    
    public function AddContactsBillingStorage($FormArray)
    {
        $db_record = array(            
                'wh_id'                 => $this->wh_id,
                'payment_option'        => '',
                'card_type'             => $FormArray['card_type'],
                'card_name'             => "{$FormArray['first_name']} {$FormArray['last_name']}",
                'card_number_reference' => $FormArray['card_number_reference'],
                'card_month'            => $FormArray['card_month'],
                'card_year'             => $FormArray['card_year'],
                'card_ccv'              => $FormArray['card_ccv'],
                'card_address'          => '',
                'card_postal_code'      => '',
                'bill_address_1'        => '',
                'bill_address_2'        => '',
                'bill_address_3'        => '',
                'bill_city'             => '',
                'bill_state'            => '',
                'bill_country_code'     => '',
                'bill_postal_code'      => '',
                'bill_contact'          => '',
                'bill_contact_phone'    => '',
            );
        $this->AddRecordLoc('contacts_billing_storage', $db_record);
    }
    
    public function AddCreditsFree($FormArray)
    {
        $date = date('now');
        $db_record = array(            
            'wh_id'                 => $this->wh_id,
            'credits_code'          => 'TEST01',
            'order_id'              => 1,
            'payment_conf_number'   => '',
            'refund_conf_number'    => '',
            'type'                  => 'free',
            'notes'                 => "[Date:$date] Created via Class::Profile_CustomerProfileCreateAccount.",
        );
        $added_credits_ids = '';
        for ($z=0; $z<$this->num_free_credits; $z++) {
            $this->AddRecordLoc('credits', $db_record);
            $added_credits_ids .= $this->SQL->Last_Insert_Id . '|';
        }
        $added_credits_ids = substr($added_credits_ids, 0, -1);
        $this->added_credits_ids = $added_credits_ids;
    }
    
    public function AddCreditsPurchased($FormArray)
    {
        $date = date('now');
        $db_record = array(            
                'wh_id'                 => $this->wh_id,
                'credits_code'          => 'TEST02',
                'order_id'              => '1001-5',
                'payment_conf_number'   => 'TST-01-X',
                'refund_conf_number'    => '',
                'type'                  => 'Purchase',
                'notes'                 => "[Date:$date] Created via Class::Profile_CustomerProfileCreateAccount.",
            );
        for ($z=0; $z<$this->num_paid_credits; $z++) {
            $this->AddRecordLoc('credits', $db_record);
        }
    }
    
    
    
    
    
    private function AddRecordLoc($table, $db_record) 
    {
        $keys   = '';
        $values = '';            
        foreach ($db_record as $var => $val) {
            $val = addslashes($val);
            
            $keys   .= "`$var`, ";
            $values .= "'$val', ";
        }
        $keys   = substr($keys, 0, -2);
        $values = substr($values, 0, -2);
        
        $result = $this->SQL->AddRecord(array(
            'table'     => $table,
            'keys'      => $keys,
            'values'    => $values,
        ));
        
        if ($result) {
            if ($this->ShowQuery) echo "<br /><br />LAST QUERY = " . $this->SQL->Db_Last_Query;
            echo "<br /><br />RECORD ADDED";
        } else {
            echo "<br /><br /><span style='color:#990000;'>RECORD FAILED TO ADD</span>";
            echo "<br /><br />LAST QUERY = " . $this->SQL->Db_Last_Query;
            $this->record_total_success = false;
        }
    }
    
    private function UpdateRecordLoc($table, $db_record, $where) 
    {
        $key_values = '';
        foreach ($db_record as $var => $val) {
            $val = addslashes($val);
            $key_values .= "`$var`='$val', ";
        }
        $key_values = substr($key_values, 0, -2);
        
        $result = $this->SQL->UpdateRecord(array(
            'table'         => $table,
            'key_values'    => $key_values,
            'where'         => "{$where} AND active=1",
        ));
        
        if ($result) {
            if ($this->ShowQuery) echo "<br /><br />LAST QUERY = " . $this->SQL->Db_Last_Query;
            echo "<br /><br />RECORD UPDATED";
        } else {
            echo "<br /><br /><span style='color:#990000;'>RECORD FAILED TO UPDATED</span>";
            echo "<br /><br />LAST QUERY = " . $this->SQL->Db_Last_Query;
            $this->record_total_success = false;
        }
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    public function AddSessionsTEMP()
    {
        $instructor_wh_id  = 666;
        $date   = date('Y-m-d');
        
        $time_start     = date('H00') + 201; //put 2 hours into the future
        $time_end       = $time_start + 101;
        
        $time_start     = str_pad($time_start, 4, "0", STR_PAD_LEFT);
        $time_end       = str_pad($time_end, 4, "0", STR_PAD_LEFT);
        
        # ACTUALLY USE THE INSTRUCTOR SCHEDULING CLASS TO ADD A RECORD
        # =================================================================
        $session_record = array(
            'instructor_id'         => $instructor_wh_id,
            'date'                  => $date,
            'ci_time_start'         => $time_start,
            'ci_time_end'           => $time_end,
            'ci_cancel_before_time' => 'Created By: Profile_CustomerProfileCancelAccount',
            'ci_id'                 => 0,
            'ci_status'             => 'adding',
        );
        $session_records[0] = $session_record;
        $SESSION = new Sessions_InstructorScheduling();
        $SESSION->processing_records           = $session_records;
        $SESSION->processing_records_wh_id     = $instructor_wh_id;
        $SESSION->processing_records_date      = $date;
        $SESSION->ProcessRecords();
        
        $sessions_id = $SESSION->Last_Sessions_Id;
        
        
        
        # ADD CREDITS NEEDED FOR PURCHASING THIS SESSION
        # =================================================================
        $PROFILE = new Profile_CreateAccountCustomerAdministrator();
        $PROFILE->AddCreditsFree();
        
        $credit_ids = $PROFILE->added_credits_ids;
        
        
        
        # LOCK & BOOK THE SESSION - THIS WILL LOCK CREDITS
        # =================================================================
        $SIGNUP = new Sessions_Signup();
        $SIGNUP->sessions_id                    = $sessions_id;
        $SIGNUP->customer_wh_id                 = $this->wh_id;
        $SIGNUP->booking_session_credits_list   = $credit_ids;
        $SIGNUP->LockSession();
        $SIGNUP->BookSession();
        
        
        
        
    }
    
    public function GetSessionsTEMP() 
    {
        # FUNCTION :: get all sessions this user has booked but haven't completed
        
        $sessions = $this->SQL->GetArrayAll(array(
            'table' => $this->table_sessions_checklists,
            'keys'  => "$this->table_sessions_checklists.*, $this->table_sessions.*",
            'where' => "`session_completed`=0 AND $this->table_sessions.`active`=1 AND $this->table_sessions_checklists.`wh_id`=$this->wh_id",
            'joins' => "LEFT JOIN $this->table_sessions ON $this->table_sessions.sessions_id = $this->table_sessions_checklists.sessions_id",
        ));
        echo '<br />' . $this->SQL->Db_Last_Query;
        
        if ($sessions) {
            $this->sessions = $sessions;
            $this->sessions_booked_count = count($sessions);
            
            echo "<br /><br />FOUND [{$this->sessions_booked_count}] SESSIONS FOR THIS USER";
        }
    }
    
    
    
    
    

    public function EmailConfirmationRegistration($FormArray)
    {
        global $URL_SITE_LOGIN;
        
        $swap_array = array(
            '@@first_name@@'        => $FormArray['first_name'],
            '@@last_name@@'         => $FormArray['last_name'],
            '@@login_url@@'         => $URL_SITE_LOGIN,
            '@@login_username@@'    => $FormArray['email_address'],
            '@@login_password@@'    => $FormArray['password'],
        );
        
        
        global $ROOT;
        require_once "$ROOT/phplib/swift4/swift_required.php";
        $MAIL = new Email_MailWh;
        
        
        $wh_id                  = $FormArray['wh_id'];
        
        $msg_array = array(
            'email_template_id'     => $this->Registration_Email_Template_Id,
            'swap_array'            => $swap_array,
        );
        $MAIL->PrepareMailToSend($msg_array);
        
        $to_name        = "{$FormArray['first_name']} {$FormArray['last_name']}";
        $to_email       = $FormArray['email_address'];
        $cc             = '';
        $bcc            = '';
        $from_name      = $MAIL->prepared_message_from_name;
        $from_email     = $MAIL->prepared_message_from_email;
        $subject        = $MAIL->prepared_message_subject;
        $message_html   = $MAIL->prepared_message_html;
        $message_text   = $MAIL->prepared_message_text;
        
        if ($MAIL->Mail($subject, $message_html, $message_text, $to_name, $to_email, $from_name, $from_email, $cc, $bcc, $wh_id)) {
            AddFlash("Email Sent to: $to_name <$to_email>");
            //AddMessage($MAIL->GetMessageDetails());
        } else {
            AddError("Message Failed: $MAIL->Error");
        }
    }   


    public function EmailConfirmationRegistrationAdministrator($FormArray)
    {
        $swap_array = array(
            '@@email_body_content@@'    => "
                A customer has registered on the YogaLiveLink.com website:<br />
                <b>Name:</b> {$FormArray['first_name']} {$FormArray['last_name']}<br />
                <b>Email:</b> {$FormArray['email_address']}
            ",
        );
        
        
        global $ROOT;
        require_once "$ROOT/phplib/swift4/swift_required.php";
        $MAIL = new Email_MailWh;
        
        
        $wh_id                  = 0; //$FormArray['wh_id'];
        
        $msg_array = array(
            'email_template_id'     => $this->Registration_Admin_Email_Template_Id,
            'swap_array'            => $swap_array,
            'subject'               => 'Customer Registration',
        );
        $MAIL->PrepareMailToSend($msg_array);
        
        $to_name        = 'support';
        $to_email       = 'support@yogalivelink.com';
        $cc             = '';
        $bcc            = '';
        $from_name      = $MAIL->prepared_message_from_name;
        $from_email     = $MAIL->prepared_message_from_email;
        $subject        = $MAIL->prepared_message_subject;
        $message_html   = $MAIL->prepared_message_html;
        $message_text   = $MAIL->prepared_message_text;
        
        if ($MAIL->Mail($subject, $message_html, $message_text, $to_name, $to_email, $from_name, $from_email, $cc, $bcc, $wh_id)) {
            AddFlash("Email Sent to: $to_name <$to_email>");
            //AddMessage($MAIL->GetMessageDetails());
        } else {
            AddError("Message Failed: $MAIL->Error");
        }
    }   
    
    
}