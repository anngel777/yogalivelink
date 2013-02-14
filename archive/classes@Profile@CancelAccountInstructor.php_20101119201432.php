<?php

/* ========================================================================== 
    FUNCTION :: Profile_CancelAccountInstructor
    
    Used by users and administrators to cancel accounts in the website. This
    class will de-activate all account information in all required tables.
    
    This function can be called from either an admin panel or a user panel.
    
    If this is a user - it will ask some questions
    
    If this is an admin - it will override all checks and just cancel the account
    
    
# ========================================================================== */


class Profile_CancelAccountInstructor extends BaseClass
{
    public $wh_id       = 0;
    public $ShowArray   = false;
    public $ShowQuery   = true;
    
    
    public $session_cancel_within_time_hrs      = 1; // how close to session happening is a cancellation allowed
    public $force_cancel_booked_sessions        = true; // TRUE - will just cancel all booked sessions with out concern for moving or notifying customers
    public $force_no_notification_to_customer   = true; // TRUE - will not notify customer that a session has been cancelled
    public $force_no_notification_to_instructor = true; // TRUE - will not notify instructor that a session has been cancelled
    public $force_no_refund_to_customer         = true; // TRUE - don't refund credits to customer upon cancellation
    
    
    public $user_is_customer                    = 0; // the person calling this class is a customer
    public $user_is_administrator               = 0; // the person calling this class is an administrator
    
    
    public $cancel_account_link_location    = "/office/dev_richard/class_execute;class=Profile_CancelAccountInstructor;";
    
    
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
    public $sessions_booked_list            = array();
    
    public function  __construct()
    {
        $this->SetSQL();
        
        $this->SetParameters(func_get_args());
        $this->wh_id = ($this->GetParameter(0)) ? $this->GetParameter(0) : 0;
    }
    
    public function SetSQL()
    {
        if (empty($this->SQL)) {
            $this->SQL = Lib_Singleton::GetInstance('Lib_Pdo');
        }
    }
    
    public function Execute()
    {
        $this->user_is_customer         = 0;
        $this->user_is_administrator    = 1;
        
    
        # CHECK TO SEE IF THIS HAS BEEN CALLED WITH A WH_ID PASSED IN
        # ====================================================================================
        # <> YES - just execute the cancel on that wh_id
        # <> NO - display form asking admin to identify person
        # ====================================================================================
        if ($this->wh_id == 0) {
            $this->AddRecord();
        } else {
            
            
            # CHECK IF THE INSTRUCTOR HAS ANY BOOKED SESSIONS
            # ========================================================
            $this->GetSessions();
            
            
            # DE-ACTIVATE ANY BOOKED SESSIONS
            # ========================================================
            if ($this->sessions_booked_count > 0) {
                # <> IF USER
                # if this is the user - ask if they want to do sessions before cancelling
                
                # <> IF ADMIN
                # De-activate all sessions
                $result = $this->DeactivateSessions();
                
                ### !!!!!!!!!!!!!
                ### NEED TO REBOOK OR SEND AN EMAIL TO CUSTOMERS
                ### !!!!!!!!!!!!!
            }
            
            
            
            
            
            
            # DEACTIVATE THE ACCOUNT
            # ========================================================================
            $this->DeactivateAccountInstructor();
            
            
            # ASK SURVEY QUESTIONS
            # ========================================================================
            if ($this->user_is_customer) {
                
            }
            
            # SHOW CONFIRMATION OF DELETION TO USER / ADMIN
            # ========================================================================
            echo "<br /><br />User deletion process is now complete.";
            
            
            # SEND CONFIRMATION EMAIL TO USER
            # ========================================================================
            $this->EmailConfirmationCancel();
            
            
        } //end the wh_id check
    }
    
    
    
    
    public function SetFormArrays()
    {
        $client_list        = $this->CreateListingByAlphabet($this->cancel_account_link_location, 'instructors');
        
        $account_types      = "Customer|Instructor|Administrator";
        $card_types         = "MasterCard|VISA|AmEx";
        $style_fieldset     = "style='color:#990000; font-size:14px; font-weight:bold;'";
        
        
        //$display_value      = "CONCAT(first_name, ' ', last_name, ' - ', city, ', ', state, ' (', email_address, ') (#', wh_id, ')')";
        //$display_value      = "CONCAT(first_name, ' ', last_name, ' ', email_address, ' ', wh_id)";
        $display_value      = "CONCAT(first_name, ' ', last_name, ' ', email_address)";
        $eq_wh_id           = EncryptQuery("ac_table=contacts&ac_key=wh_id&ac_field={$display_value}");
        
        
        $base_array = array(
            "form|$this->Action_Link|post|db_edit_form",
            "fieldset|Customer Information|options_fieldset|$style_fieldset",
                "autocomplete|Customer (wh id)|wh_id|N|60|80||addAutoCompleteFunctionality|$this->Auto_Complete_Helper?eq=$eq_wh_id",
            "code|<div style='height:300px; padding:10px; border:1px solid #990000; overflow:scroll;'>{$client_list}</div>",
            "endfieldset",
        );

        
        $base_array[] = "submit|Add Record|$this->Add_Submit_Name";
        $base_array[] = 'endform';
        $this->Form_Data_Array_Add = $base_array;
        
        
        
    }
    
    
    public function PostProcessFormValues($FormArray)
    {
        # NOW CALL THIS CLASS AGAIN BUT PASS IN THE WH_ID
        $wh_id      = $FormArray['wh_id'];
        $link       = "{$this->cancel_account_link_location};classVars={$wh_id}";
        header("Location: $link");
    }
    
    

    
    
    
   
    public function GetSessions() 
    {
        # GET ALL THE SESSIONS
        # =========================================================================
        $sessions = $this->SQL->GetArrayAll(array(
            'table'     => $this->table_sessions,
            'keys'      => "sessions_id",
            'where'     => "`instructor_id`=$this->wh_id AND `active`=1",
        ));
        if ($this->ShowQuery) echo '<br /><br />' . $this->SQL->Db_Last_Query;
        
        
        # LOOP THROUGH SESSIONS AND THEN GET ALL CHECKLISTS - BOOKED SESSIONS
        # =========================================================================
        $sessions_list = '';
        foreach ($sessions as $session) {
            $sessions_list .= "'{$session['sessions_id']}',";
        }
        $sessions_list = substr($sessions_list, 0, -1);
        
        
        # GET THE CHECKLISTS - for NON-COMPLETED sessions
        # =========================================================================
        $sessions_booked = $this->SQL->GetArrayAll(array(
            'table'     => $this->table_sessions_checklists,
            'keys'      => "session_checklists_id, sessions_id",
            'where'     => "`active`=1 AND `session_completed`=0 AND `sessions_id` IN ($sessions_list)",
        ));
        if ($this->ShowQuery) echo '<br /><br />' . $this->SQL->Db_Last_Query;
        
        $sessions_booked_list = array();
        foreach ($sessions_booked as $session) {
            $sessions_booked_list[] = $session['sessions_id'];
        }
        
        
        if ($sessions) {
            $this->sessions = $sessions;
            $this->sessions_booked = $sessions_booked;
            $this->sessions_booked_count = count($sessions_booked);
            $this->sessions_booked_list = $sessions_booked_list;
            
            echo "<br /><br />sessions_booked_count ===> $this->sessions_booked_count";
        }
    }
    
    
    public function DeactivateAccountInstructor()
    {
        # DEACTIVATE contacts
        # ================================================
        $result = $this->SQL->UpdateRecord(array(
            'table'         => 'contacts',
            'key_values'    => "`active`=0",
            'where'         => "`wh_id`=$this->wh_id AND `active`=1",
        ));
        if ($this->ShowQuery) echo '<br /><br />' . $this->SQL->Db_Last_Query;
        
        
        # DEACTIVATE contacts_account
        # ================================================
        $result = $this->SQL->UpdateRecord(array(
            'table'         => 'contacts_account',
            'key_values'    => "`active`=0",
            'where'         => "`wh_id`=$this->wh_id AND `active`=1",
        ));
        if ($this->ShowQuery) echo '<br /><br />' . $this->SQL->Db_Last_Query;
        
        
        # DEACTIVATE instructor_profile
        # ================================================
        $result = $this->SQL->UpdateRecord(array(
            'table'         => 'instructor_profile',
            'key_values'    => "`active`=0",
            'where'         => "`wh_id`=$this->wh_id AND `active`=1",
        ));
        if ($this->ShowQuery) echo '<br /><br />' . $this->SQL->Db_Last_Query;
        
        
        # DEACTIVATE instructor_profile_pending
        # ================================================
        $result = $this->SQL->UpdateRecord(array(
            'table'         => 'instructor_profile_pending',
            'key_values'    => "`active`=0",
            'where'         => "`wh_id`=$this->wh_id AND `active`=1",
        ));
        if ($this->ShowQuery) echo '<br /><br />' . $this->SQL->Db_Last_Query;
        
        
        # DEACTIVATE instructor_checklist
        # ================================================
        $result = $this->SQL->UpdateRecord(array(
            'table'         => 'instructor_checklist',
            'key_values'    => "`active`=0",
            'where'         => "`wh_id`=$this->wh_id AND `active`=1",
        ));
        if ($this->ShowQuery) echo '<br /><br />' . $this->SQL->Db_Last_Query;
        
        
        return $result;
        
    }

    public function DeactivateSessions()
    {
        
        foreach ($this->sessions as $session) {
            
            # ===============================================
            # CHECK IF SESSION IS BOOKED
            # <> No - deactivate it
            # <> Yes - run the check to attempt to deactivate it
            # ===============================================
            $booked = in_array($session['sessions_id'], $this->sessions_booked_list);
            
            
            if (!$booked) {
                echo "<br /><br />SESSION IS NOT BOOKED";
                $result = $this->DeactivateSingleSessionUnbooked($session['sessions_id']);
                
            } else {
                echo "<br /><br />SESSION IS BOOKED";
                
                if ($this->force_cancel_booked_sessions) {
                    # DEACTIVATE session_checklists
                    # ================================================
                    echo "<br /><br />FORCING DEACTIVATION OF BOOKED SESSION";
                    $result = $this->DeactivateSingleSessionBooked($session['sessions_id']);
                    
                } else {
                    # CHECK IF SESSION CAN BE DE-ACTIVATED - WITHIN TIME WINDOW
                    # ================================================
                    echo "<br /><br /> >>> CHECKING A SESSION";
                    
                    $session_cancel_within_time_hrs = $this->session_cancel_within_time_hrs; // how close to scheduled start of session can it be cancelled
                    
                    $curr_date      = date('Y-m-d');
                    $curr_time      = date('Hi');
                    $now_date       = "{$curr_date} {$curr_time}";
                    $session_date   = "{$session['date']} {$session['start_datetime']}";
                    $diff_in_hours  = ((abs(strtotime($session_date) - strtotime($now_date)) / 60) / 60);
                    
                    if (strtotime($session_date) > strtotime($now_date)) {
                        # session happens in the future
                        echo "<br /><br />[$diff_in_hours > $session_cancel_within_time_hrs]";
                        
                        if ($diff_in_hours > $session_cancel_within_time_hrs) {
                            # session CAN be cancelled
                            echo "<br /><br />ABOUT TO DEACTIVATE A SESSION";
                            $result = $this->DeactivateSingleSessionBooked($session['sessions_id']);
                        } else {
                            # session is occurring too close - cannot cancel without additional authorization
                            echo "<br /><br /><span style='color:#990000;'>SESSION CANNOT BE CANCELED BECAUSE THE COURSE HAPPENS TOO CLOSE IN THE FUTURE [$diff_in_hours > $session_cancel_within_time_hrs]</span>";
                        }
                    } else {
                        echo "<br /><br />SESSION HAS ALREADY PASSED";
                        # SESSION HAS ALREADY PASSED - but for some reason is still not marked completed - DEACTIVATE
                        $result = $this->DeactivateSingleSessionBooked($session['sessions_id']);
                    } // end timing check
                    
                } // end force cancellation check
                
            } // end booked check
            
        } // end session loop
        
    }
    
    
    public function DeactivateSingleSessionUnbooked($sessions_id)
    {
        # deactivate the session
        # ==============================================================
        $result = $this->SQL->UpdateRecord(array(
            'table'         => $this->table_sessions,
            'key_values'    => "`active`=0",
            'where'         => "`sessions_id`=$sessions_id AND `active`=1",
        ));
        if ($this->ShowQuery) echo '<br /><br />' . $this->SQL->Db_Last_Query;
        
        return $result;
    }
    
    
    public function DeactivateSingleSessionBooked($sessions_id)
    {
        # 1. deactivate single session via checklist
        # ==============================================================
        $FormArray = array(
            'cancelled'         => 1,
            'cacelled_reason'   => 'Instructor account being deleted',
            'active'            => 0,
        );
        $key_values = $this->FormatDataForUpdate($FormArray);
        
        $result = $this->SQL->UpdateRecord(array(
            'table'         => $this->table_sessions_checklists,
            'key_values'    => $key_values,
            'where'         => "`sessions_id`=$sessions_id AND `active`=1",
        ));
        if ($this->ShowQuery) echo '<br /><br />' . $this->SQL->Db_Last_Query;
        
        
        
        # 2. deactivate the session
        # ==============================================================
        $result = $this->SQL->UpdateRecord(array(
            'table'         => $this->table_sessions,
            'key_values'    => "`active`=0",
            'where'         => "`sessions_id`=$sessions_id AND `active`=1",
        ));
        if ($this->ShowQuery) echo '<br /><br />' . $this->SQL->Db_Last_Query;
        
        
        if (!$this->force_no_refund_to_customer) {
            # 3. refund credits TO THE USER
            # ==============================================================
            $date = date('now');
            $note = "\n\n [Date:$date] Credit Refunded from session cancellation (session_id=$sessions_id).";
            $result = $this->SQL->AppendValue(array(
                'table'       => $this->table_credits,
                'key'         => 'notes',
                'value'       => "'$note'",
                'where'       => "`sessions_id`=$sessions_id AND `active`=1",
            ));
            
            $FormArray = array(
                'used'          => 0,
                //'sessions_id'   => 0,
            );
            $key_values = $this->FormatDataForUpdate($FormArray);
            
            $result = $this->SQL->UpdateRecord(array(
                'table'         => $this->table_credits,
                'key_values'    => $key_values,
                'where'         => "`sessions_id`=$sessions_id AND `active`=1",
            ));
            if ($this->ShowQuery) echo '<br /><br />' . $this->SQL->Db_Last_Query;
        }
        
        
        # 4. send email to instructor
        # ==============================================================
        if (!$this->force_no_notification_to_instructor) {
            echo "<br /><br />SEND CONFIRMATION EMAIL TO INSTRUCTOR";
        }
        
        
        # 5. send email to customer
        # ==============================================================
        if (!$this->force_no_notification_to_customer) {
            echo "<br /><br />SEND CONFIRMATION EMAIL TO CUSTOMER";
        }
        
        return $result;
    }
    
    
    
    public function EmailConfirmationCancel()
    {
        echo "<br /><br />SEND EMAIL TO INSTRUCTOR";
    }
    
    
    
}