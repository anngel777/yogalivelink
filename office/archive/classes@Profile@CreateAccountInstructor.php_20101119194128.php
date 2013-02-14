<?php

/* ========================================================================== 
    FUNCTION :: Profile_CreateAccountInstructor

    Create "Instructor" accounts
    
    Used by administrators to create accounts in the website. This
    class will create all account information in all required tables.
    
# ========================================================================== */


class Profile_CreateAccountInstructor extends BaseClass
{
    public $wh_id                   = 0;
    public $num_sessions            = 2;
    public $num_booked_sessions     = 2;
    public $num_free_credits        = 5;
    
    public $ShowArray               = false;
    public $ShowQuery               = false;
    
    public $record_total_success    = true;
    
    
    
    
    
    
    public function  __construct()
    {
        $this->SetSQL();
        
        $this->Default_Values = array(
            'language_code'     => 4,
            'time_zones_id'     => 5,
            'created_by'        => $_SESSION['USER_LOGIN']['USER_NAME'],
            
            'AddContacts'                   => 1,
            'AddContactsAccount'            => 1,
            'AddInstructorChecklist'        => 1,
            'AddInstructorProfile'          => 1,
            
            'last_name'                     => 'Instructerson',
            'email_address'                 => '##.Instructerson@mailwh.com',
            
            'profile'                       => 'PROFILE NOT PROVIDED',
            
            'type_instructor'               => 1,
            'type_customer'                 => 0,
            'type_administrator'            => 0,
            
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
        $account_types      = "Instructor";
        $card_types         = "MasterCard|VISA|AmEx";
        $gender_types       = "M=Male|F=Female";
        $style_fieldset     = "style='color:#990000; font-size:14px; font-weight:bold;'";
        
        
        $base_array = array(
            "form|$this->Action_Link|post|db_edit_form",

            "fieldset|User Information|options_fieldset|$style_fieldset",
                "select|Gender|gender|Y||$gender_types",
                'text|First Name|first_name|Y|60|100',
                'text|Last Name|last_name|Y|60|100',
                'text|City|city|N|60|100',
                'text|State|state|N|60|100',
                'text|Email Address|email_address|Y|60|100',
            "endfieldset",
            
            
            "fieldset|Instructor Profile|options_fieldset|$style_fieldset",
                'textarea|Profile|profile|N|60|4',
            "endfieldset",
            
            
            "fieldset|Additional Information|options_fieldset|$style_fieldset",
                'checkbox|Add Contacts|AddContacts||1|0',
                'checkbox|Add Contacts Account|AddContactsAccount||1|0',
                'checkbox|Add Instructor Checklist|AddInstructorChecklist||1|0',
                'checkbox|Add Instructor Profile|AddInstructorProfile||1|0',
                "checkbox|Add {$this->num_sessions} Sessions|AddSessions||1|0",
                "checkbox|Add {$this->num_booked_sessions} Booked Sessions|AddSessionsBooked||1|0",
                'code|<br />',
                
                'textarea|Comments|comments|N|60|4',
                'text|Skype Username|skype_username|N|45|45',
                'text|Skype Phone Number|skype_phone_number|N|45|45',
            "endfieldset",
            
            'code|<div style="display:none;">',
                'checkbox|Super User|super_user||1|0',
                #'text|Module Roles|module_roles|N|60|255',
                #'text|Class Roles|class_roles|N|60|255',
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
            
            
            # ADD INSTRUCTOR CHECKLIST - instructor_checklist
            # ========================================================================
            if ($FormArray['AddInstructorChecklist']) {
                echo "<br /><br />Adding [instructor_checklist] record";
                $this->AddInstructorChecklist($FormArray);
            }
            
            
            # ADD INSTRUCTOR PROFILE - instructor_profile
            # ========================================================================
            if ($FormArray['AddInstructorProfile']) {
                echo "<br /><br />Adding [instructor_profile] record";
                $this->AddInstructorProfile($FormArray);
            }
            
            
            # ADD INSTRUCTOR SESSION - sessions
            # ========================================================================
            if ($FormArray['AddSessions']) {
                echo "<br /><br />Adding [instructor_profile] record";
                $this->AddInstructorSessions();
            }
            
            
            # ADD INSTRUCTOR SESSIONS AND BOOK THEM - sessions && session_checklists
            # ========================================================================
            if ($FormArray['AddSessionsBooked']) {
                echo "<br /><br />Adding [instructor_profile] record";
                $this->AddInstructorSessions(true);
            }
            

        # DETERMINE IF RECORD TOTALLY ADDED
        # ==========================================================
        $success_fail_message = ($this->record_total_success) ? "<span style='color:blue;'>RECORD ADDED SUCCESSFULLY</span>" : "<span style='color:#990000;'>RECORD FAILED TO ADDED SUCCESSFULLY</span>";
        echo "<h2>$success_fail_message</h2>";
        echo "<h2>Created WHID: $this->wh_id</h2>";
        
        # UNSET FORM VALUES
        # ==========================================================
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
       
    public function AddInstructorChecklist($FormArray)
    {
        $db_record = array(            
            'wh_id'                 => $this->wh_id,
            'account_created'       => 1,
            'login_created'         => 1,
        );
        $this->AddRecordLoc('instructor_checklist', $db_record);
    }
    
    public function AddInstructorProfile($FormArray)
    {
        $db_record = array(            
            'wh_id'             => $this->wh_id,
            'gender'            => $FormArray['gender'],
            'first_name'        => $FormArray['first_name'],
            'last_name'         => $FormArray['last_name'],
            'profile'           => $FormArray['profile'],
        );
        $this->AddRecordLoc('instructor_profile', $db_record);
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
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    public function AddInstructorSessions($book=false)
    {
        for ($i=0; $i<$this->num_sessions; $i++) {
            $instructor_wh_id  = $this->wh_id;
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
                'ci_cancel_before_time' => 'Created By: Profile_CreateAccountInstructor',
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
            
            
            
            
            if ($book) {
                $date = date('now');
                $db_record = array(            
                    'wh_id'                 => '666',
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
                $credit_ids = $added_credits_ids;
                
                
                
                # LOCK & BOOK THE SESSION - THIS WILL LOCK CREDITS
                # =================================================================
                $SIGNUP = new Sessions_Signup();
                $SIGNUP->sessions_id                    = $sessions_id;
                $SIGNUP->customer_wh_id                 = '666';
                $SIGNUP->booking_session_credits_list   = $credit_ids;
                $SIGNUP->LockSession();
                $SIGNUP->BookSession();
            }
            
        }
        
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