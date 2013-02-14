<?php

/* ========================================================================== 
    FUNCTION :: Profile_CreateAccountCustomerAdministrator
    
    Create "Customer || Administrator" accounts
    
    Used by administrators to create accounts in the website. This
    class will create all account information in all required tables.
    
# ========================================================================== */


class Profile_CreateAccountCustomerAdministrator extends BaseClass
{
    public $wh_id                   = 0;
    public $num_free_credits        = 5;
    public $num_paid_credits        = 5;
    
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
    
    
    public function  __construct()
    {
        $this->SetSQL();
        
        $this->Default_Values = array(
            'language_code'     => 4,
            'time_zones_id'     => 5,
            'created_by'        => $_SESSION['USER_LOGIN']['USER_NAME'],
            
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
        );
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
    
    public function SetFormArrays()
    {
        $account_types      = "Customer|Administrator";
        $card_types         = "MasterCard|VISA|AmEx";
        $style_fieldset     = "style='color:#990000; font-size:14px; font-weight:bold;'";
        
        
        $base_array = array(
            "form|$this->Action_Link|post|db_edit_form",

            "fieldset|User Information|options_fieldset|$style_fieldset",
                'text|First Name|first_name|Y|60|100',
                'text|Last Name|last_name|Y|60|100',
                'text|City|city|N|60|100',
                'text|State|state|N|60|100',
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
                'checkbox|Add Contacts|AddContacts||1|0',
                'checkbox|Add Contacts Account|AddContactsAccount||1|0',
                'checkbox|Add Contacts Billing Storage|AddContactsBillingStorage||1|0',
                "checkbox|Add {$this->num_free_credits} Credits Free|AddCreditsFree||1|0",
                "checkbox|Add {$this->num_paid_credits} Credits Purchased|AddCreditsPurchased||1|0",
                
                'code|<br />',
                
                'textarea|Comments|comments|N|60|4',
                'text|Skype Username|skype_username|N|45|45',
                'text|Skype Phone Number|skype_phone_number|N|45|45',
            "endfieldset",
            
            'code|<div style="display:none;">',
                'text|Contact Salutations Id|contact_salutations_id|N|3|3',
                'text|Phone Home|phone_home|N|50|50',
                'text|Phone Work|phone_work|N|50|50',
                'text|Phone Cell|phone_cell|N|50|50',
                'text|Time Zones Id|time_zones_id|N|11|11',
                'text|Middle Name|middle_name|N|60|100',
                'text|Address 1|address_1|N|60|100',
                'text|Address 2|address_2|N|60|100',
                'text|Address 3|address_3|N|60|100',
                'text|Country Code|country_code|N|5|5',
                'text|Postal Code|postal_code|N|50|50',
                'text|Password|password|N|60|80',
                'text|Created By|created_by|N|60|64',
                'text|Language Code|language_code|N|3|3',
                'checkboxlistset|Email Subscriptions|email_subscriptions|N||ICC|IRD|ISS|TST|ICAS|WEBINAR|OTHER',
                'checkbox|Type Customer|type_customer||1|0',
                'checkbox|Type Instructor|type_instructor||1|0',
                'checkbox|Type Administrator|type_administrator||1|0',
            'code|</div>',
            
        );

        if ($this->Action == 'ADD') {
            $base_array[] = "submit|Add Record|$this->Add_Submit_Name";
            $base_array[] = 'endform';
            $this->Form_Data_Array_Add = $base_array;
        } else {
            $base_array[] = 'checkbox|Active|active||1|0';
            $base_array[] = "submit|Update Record|$this->Edit_Submit_Name";
            $base_array[] = 'endform';
            $this->Form_Data_Array_Edit = $base_array;
        }
    }
    
    
    
    public function PostProcessFormValues($FormArray)
    {
        # FIGURE OUT THE ACCOUNT TYPE
        # ==========================================================
        switch($FormArray['account_type']) {
            case 'Customer':
                $FormArray['type_customer']         = 1;
                $FormArray['type_instructor']       = 0;
                $FormArray['type_administrator']    = 0;
            break;
            case 'Administrator':
                $FormArray['type_customer']         = 0;
                $FormArray['type_instructor']       = 0;
                $FormArray['type_administrator']    = 1;
            break;
        }
        
        
        
        # NOW ADD THE ACCOUNT STUFF
        # ==========================================================
            
            # ADD AN ACCOUNT - contacts
            # ========================================================================
            if ($FormArray['AddContacts']) {
                echo "<br /><br />Adding [contacts] record";
                $this->AddContacts($FormArray);
            }
            
            
            # ADD ACCOUNT SETTINGS - contacts_account
            # ========================================================================
            if ($FormArray['AddContactsAccount']) {
                echo "<br /><br />Adding [contacts_account] record";
                $this->AddContactsAccount($FormArray);
            }
            
            
            # ADD BILLING INFORMATION - contacts_billing_storage
            # ========================================================================
            if ($FormArray['AddContactsBillingStorage']) {
                echo "<br /><br />Adding [contacts_billing_storage] record";
                $this->AddContactsBillingStorage($FormArray);
            }
            
            
            # ADD FREE CREDITS - credits
            # ========================================================================
            if ($FormArray['AddCreditsFree']) {
                echo "<br /><br />Adding [credits] record - FREE CREDITS";
                $this->AddCreditsFree($FormArray);
            }
            
            
            # ADD PURCHASED CREDITS - credits
            # ========================================================================
            if ($FormArray['AddCreditsPurchased']) {
                echo "<br /><br />Adding [credits] record - PURCHASED CREDITS";
                $this->AddCreditsPurchased($FormArray);
            }

        # DETERMINE IF RECORD TOTALLY ADDED
        # ==========================================================
        $success_fail_message = ($this->record_total_success) ? "<span style='color:blue;'>RECORD ADDED SUCCESSFULLY</span>" : "<span style='color:#990000;'>RECORD FAILED TO ADDED SUCCESSFULLY</span>";
        echo "<h2>$success_fail_message</h2>";
        echo "<h2>Created WHID: $this->wh_id</h2>";

        # UNSET FORM VALUES
        # ==========================================================
        unset($FormArray['account_type']);
        unset($FormArray['AddContacts']);
        unset($FormArray['AddContactsAccount']);
        unset($FormArray['AddContactsBillingStorage']);
        unset($FormArray['AddCreditsFree']);
        unset($FormArray['AddCreditsPurchased']);
        
        unset($FormArray);
        
        exit();
    }
    
    
    
    
    
    public function AddContacts($FormArray)
    {
        $db_record = array(
                'first_name'            => $FormArray['first_name'],
                'last_name'             => $FormArray['last_name'],
                'email_address'         => $FormArray['email_address'],
                'super_user'            => $FormArray['super_user'],
                'created_by'            => $FormArray['created_by'],
                'type_customer'         => $FormArray['type_customer'],
                'type_instructor'       => $FormArray['type_instructor'],
                'type_administrator'    => $FormArray['type_administrator'],
            );
        $this->AddRecordLoc('contacts', $db_record);
        $db_last_insert_id = $this->SQL->Last_Insert_Id;
        $this->wh_id = $db_last_insert_id + 1000000;
        
        $db_record = array(            
                'wh_id'         => $this->wh_id,
            );
        $where = "`contacts_id`=$db_last_insert_id";
        $this->UpdateRecordLoc('contacts', $db_record, $where);
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
    
    
    
    
    

    
    
    public function EmailConfirmationRefund()
    {
        echo "<br /><br />Email Confirmation Send --> REFUND";
    }

    
}