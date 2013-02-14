<?php

/* ========================================================================== 
    FUNCTION :: Profile_CancelAccountCustomer
    
    Used by users and administrators to cancel accounts in the website. This
    class will de-activate all account information in all required tables.
    
    This function can be called from either an admin panel or a user panel.
    
    If this is a user - it will ask some questions
    
    If this is an admin - it will override all checks and just cancel the account
    
    
# ========================================================================== */


class Profile_CancelAccountCustomer extends BaseClass
{
    public $wh_id       = 0;
    public $ShowArray   = false;
    public $Show_Query  = false;
    
    public $session_cancel_within_time_hrs      = 1; // how close to session happening is a cancellation allowed
    public $user_is_customer                    = 0; // the person calling this class is a customer
    public $user_is_administrator               = 0; // the person calling this class is an administrator
    
    
    #public $cancel_account_link_location    = "/office/dev_richard/class_execute;class=Profile_CancelAccountCustomer;";
    public $cancel_account_link_location    = "Profile_CancelAccountCustomer";
    
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
    
    
    public $record_total_success            = true;
    public $email_send_to_admin             = true;
    public $email_template_id               = 8;
    
    
    
    public function  __construct()
    {
        $this->SetSQL();
        
        $this->SetParameters(func_get_args());
        $this->wh_id = ($this->GetParameter(0)) ? $this->GetParameter(0) : 0;
        #echo "<br />wh_id ===> " . $this->wh_id;
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
        
        
        if (Get('action') == 'approve') {
            $this->CancelAccount();
        } else {
            global $PAGE;
            
            $link_approve   = "{$PAGE['pagelinkquery']};action=approve";
            $btn_approve    = MakeButton('positive', 'Cancel Account', $link_approve);
            $btn_deny       = MakeButton('negative', 'Close Window', '', '', '', 'parent.CloseOverlay();');
            
            
            # determine if record is inactive
            # =================================================================================
            $OBJ_CONTACTS   = new Profile_CustomerProfileContacts();
            $record         = $OBJ_CONTACTS->ListRecordSpecial($this->wh_id, true);
            $notice         = (!$record) ? '<h3>NOTE: This record has already been de-activated. If you run the cancel record - it will de-activate any remaining active information. Regardless, running the "cancel account" will send an email to the user.</h3><br /><br />' : '';
            
            
            echo "
            <div style='width:300px;'>
            {$notice}
            {$btn_approve}<br /><br />
            {$btn_deny}<br /><br />
            </div>";
        }
    }

    public function CancelAccount() 
    {
        
        
        # CHECK TO SEE IF THIS HAS BEEN CALLED WITH A WH_ID PASSED IN
        # ====================================================================================
        # <> YES - just execute the cancel on that wh_id
        # <> NO - display form asking admin to identify person
        # ====================================================================================
        if ($this->wh_id == 0) {
            $this->AddRecord();
        } else {
            
            
            
            # RUNS AS A TRANSACTION SO EVERYTHING MUST PASS OR IT WILL NOT COMPLETE
            # ================================================================================
            # ================================================================================
            //$show_msg = ($this->User_Type_Customer) ? false : true;
            $show_msg = true;
            
            
            # START TRANSACTION
            # ============================================================
            $this->SQL->StartTransaction();
            
            
            
            
            
            
            
            
            # CHECK IF THE USER HAS ANY CREDITS
            # ========================================================
            $this->GetCredits();
            
            
            # CHECK IF THE USER HAS ANY BOOKED SESSIONS
            # ========================================================
            $this->GetSessions();
            
            
            # DE-ACTIVATE ANY BOOKED SESSIONS
            # ========================================================
            if ($this->record_total_success) {
                if ($this->sessions_booked_count > 0) {
                    # <> IF USER
                    # if this is the user - ask if they want to do sessions before cancelling
                    
                    # <> IF ADMIN
                    # De-activate all sessions
                    
                    if ($show_msg) echo "<br /><br />Deactivating Booked Sessions";
                    $result = $this->DeactivateSessions();
                    $this->record_total_success = $result;
                }
            }
            
            if ($this->record_total_success) {
                if ($this->credit_count_free > 0) {
                    echo "<br /><br />USER HAS FREE CREDITS";
                    # <> IF USER
                    # if this is the user - ask if they want to use credits before cancelling
                    
                    # <> IF ADMIN
                    # De-activate all free credits
                    if ($show_msg) echo "<br /><br />Deactivating FREE Credits";
                    $result = $this->DeactivateFreeCredits();
                    $this->record_total_success = $result;
                }
            }
            
            if ($this->record_total_success) {
                if ($this->credit_count_purchase > 0) {
                    echo "<br /><br />USER HAS PURCHASED CREDITS";
                    
                    # <> IF USER
                    # SHOW REQUESTION FORM FOR GETTING A REFUND
                    # SEND EMAIL NOTICE TO ADMINISTRATION
                    
                    
                    # <> IF ADMIN
                    # ADMIN DO REFUND PROCESS - WITH CREDIT CARD COMPANY
                    
                    # ENTER REFUND CONFIRMATION #
                    $this->credits_refund_conf_number = 'FAKE-001-20x';
                    
                    # ZERO CREDITS IN USER ACCOUNT
                    if ($show_msg) echo "<br /><br />Deactivating PURCHASED Credits";
                    $result = $this->DeactivatePurchasedCredits();
                    
                    # SEND CONFIRMATION EMAIL TO USER
                    if ($show_msg) echo "<br /><br />Sending Email for PURCHASED Credits";
                    $this->EmailConfirmationRefund();
                }
            }
            
            
            # DEACTIVATE THE ACCOUNT
            # ========================================================================
            if ($this->record_total_success) {
                if ($show_msg) echo "<br /><br />Deactivating Customer Account";
                $result = $this->DeactivateCustomerAccount();
                $this->record_total_success = $result;
            }
            
            # ASK SURVEY QUESTIONS
            # ========================================================================
            if ($this->record_total_success) {
                if ($this->user_is_customer) {
                    
                }
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
                ###if ($FormArray['SendEmail']) {
                    if ($show_msg) echo "<br /><br />Sending Confirmation Email";
                    $this->EmailConfirmationAccountCancel();
                ###}            
                
                ### DIRECT TO SUCCESSS MESSAGE
                ### =======================
                ##if ($this->URL_Success_Redirect) {
                ##    header("Location: $this->URL_Success_Redirect");
                ##}
                
            } else {
                # ROLL-BACK TRANSACTION
                # =======================
                if ($show_msg) echo "<br /><br /><div class='error_message'>ROLLING BACK TRANSACTION</div>";
                $this->Error = "[T~UNKNOWN_REGISTRATION_ERROR]";
                $this->SQL->Rollback();
            }
            
            
            
            if ($show_msg) {
                $success_fail_message = ($this->record_total_success) ? "<span style='color:blue;'>RECORD DELETED SUCCESSFULLY</span>" : "<span style='color:#990000;'>RECORD FAILED TO DELETE SUCCESSFULLY</span>";
                echo "<h2>$success_fail_message</h2>";
                echo "<h2>Created WHID: $this->wh_id</h2>";
            }
            
            
            
        } //end the wh_id check
    }
    
    
    
    
    public function SetFormArrays()
    {
        $client_list        = $this->CreateListingByAlphabet($this->cancel_account_link_location, 'customers');
    
        $account_types      = "Customer|Instructor|Administrator";
        $card_types         = "MasterCard|VISA|AmEx";
        $style_fieldset     = "style='color:#990000; font-size:14px; font-weight:bold;'";
        
        
        //$display_value      = "CONCAT(first_name, ' ', last_name, ' - ', city, ', ', state, ' (', email_address, ') (#', wh_id, ')')";
        //$display_value      = "CONCAT(first_name, ' ', last_name, ' ', email_address, ' ', wh_id)";
        $display_value      = "CONCAT(first_name, ' ', last_name, ' ', email_address)";
        $eq_wh_id           = EncryptQuery("ac_table=contacts&ac_key=wh_id&ac_field={$display_value}");
        
        
        $base_array = array(
            "form|$this->Action_Link|post|db_edit_form",
            "info||<span style='color:#990000; font-size:14px;'>The account will be deleted IMMEDIATELY upon clicking the name.</span>",
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
    
    
    
    
    
    
    
    
    public function GetCredits() 
    {
        $credits = $this->SQL->GetArrayAll(array(
            'table' => $this->table_credits,
            'keys'  => '*',
            'where' => "`wh_id`=$this->wh_id",
        ));
        if ($this->Show_Query) echo '<br /><br />' . $this->SQL->Db_Last_Query;
        
        foreach ($credits as $credit) {
            $type = strtolower($credit['type']);
            switch($type) {
                case 'free':
                    $this->credit_count_free++;
                break;
                case 'purchase':
                    $this->credit_count_purchase++;
                break;
                default:
                    $this->credit_count_other++;
                break;
            }
        }
        
        $this->credits = $credits;
    }
    
    public function GetSessions() 
    {
        # FUNCTION :: get all sessions this user has booked but haven't completed
        
        $sessions = $this->SQL->GetArrayAll(array(
            'table' => $this->table_sessions_checklists,
            'keys'  => "$this->table_sessions_checklists.*, $this->table_sessions.*",
            'where' => "`wh_id`=$this->wh_id AND `session_completed`=0 AND $this->table_sessions.`active`=1",
            'joins' => "LEFT JOIN $this->table_sessions ON $this->table_sessions.sessions_id = $this->table_sessions_checklists.sessions_id",
        ));
        if ($this->Show_Query) echo '<br /><br />' . $this->SQL->Db_Last_Query;
        
        if ($sessions) {
            $this->sessions = $sessions;
            $this->sessions_booked_count = count($sessions);
        }
    }
    
    public function DeactivateFreeCredits() 
    {
        $result = $this->SQL->UpdateRecord(array(
            'table'         => $this->table_credits,
            'key_values'    => "`active`=0",
            'where'         => "`wh_id`=$this->wh_id AND `type` LIKE '%free%' AND `active`=1",
        ));
        if ($this->Show_Query) echo '<br /><br />' . $this->SQL->Db_Last_Query;
        return $result;
    }

    public function DeactivatePurchasedCredits() 
    {
        $result = $this->SQL->UpdateRecord(array(
            'table'         => $this->table_credits,
            'key_values'    => "`active`=0, `refund_conf_number`='{$this->credits_refund_conf_number}'",
            'where'         => "`wh_id`=$this->wh_id AND `type` LIKE '%Purchase%' AND `active`=1",
        ));
        if ($this->Show_Query) echo '<br /><br />' . $this->SQL->Db_Last_Query;
        return $result;
    }
    
    public function DeactivateCustomerAccount()
    {
        # DEACTIVATE contacts
        # ================================================
        $result = $this->SQL->UpdateRecord(array(
            'table'         => 'contacts',
            'key_values'    => "`active`=0",
            'where'         => "`wh_id`=$this->wh_id AND `active`=1",
        ));
        if ($this->Show_Query) echo '<br /><br />' . $this->SQL->Db_Last_Query;
        $this->record_total_success = $result;
        
        
        # DEACTIVATE contacts_account
        # ================================================
        $result = $this->SQL->UpdateRecord(array(
            'table'         => 'contacts_account',
            'key_values'    => "`active`=0",
            'where'         => "`wh_id`=$this->wh_id AND `active`=1",
        ));
        if ($this->Show_Query) echo '<br /><br />' . $this->SQL->Db_Last_Query;
        $this->record_total_success = $result;
        
        
        # DEACTIVATE contacts_billing_storage
        # ================================================
        $result = $this->SQL->UpdateRecord(array(
            'table'         => 'contacts_billing_storage',
            'key_values'    => "`active`=0",
            'where'         => "`wh_id`=$this->wh_id AND `active`=1",
        ));
        if ($this->Show_Query) echo '<br /><br />' . $this->SQL->Db_Last_Query;
        $this->record_total_success = $result;
        
        
        # DEACTIVATE customer_profile
        # ================================================
        $result = $this->SQL->UpdateRecord(array(
            'table'         => 'customer_profile',
            'key_values'    => "`active`=0",
            'where'         => "`wh_id`=$this->wh_id AND `active`=1",
        ));
        if ($this->Show_Query) echo '<br /><br />' . $this->SQL->Db_Last_Query;
        $this->record_total_success = $result;
        
        
        return $result;
        
    }
    
    
    public function DeactivateSessions()
    {
        $SESSION_CANCEL = new Sessions_CancelSignup();
        $SESSION_CANCEL->cancel_reason = 'User account being deleted';
        
        foreach ($this->sessions as $session) {
            $SESSION_CANCEL->session_record = $session;
            $sessions_id = $session['sessions_id'];
            $result = $SESSION_CANCEL->DeactivateSingleSession($sessions_id);
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
        $PROFILE = new Profile_CustomerProfileAddAccount();
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
        if ($this->Show_Query) echo '<br /><br />' . $this->SQL->Db_Last_Query;
        
        if ($sessions) {
            $this->sessions = $sessions;
            $this->sessions_booked_count = count($sessions);
            
            echo "<br /><br />FOUND [{$this->sessions_booked_count}] SESSIONS FOR THIS USER";
        }
    }
    
    
    
    
    

    
    
    public function EmailConfirmationRefund()
    {
        echo "<br /><br />Email Confirmation Send --> REFUND";
        
        /*
        # SETUP THE EMAIL ADDRESSES
        # ==================================
        global $EMAIL_ADMIN_EMAIL;
        $bcc = ($this->email_send_to_admin) ? $EMAIL_ADMIN_EMAIL : '';
        
        # PREP THE MESSAGE ARRAY
        # ==================================
        $msg_array = array(
            'email_template_id'     => $this->email_template_id,
            'cc'                    => '',
            'bcc'                   => $bcc,
            'wh_id'                 => $this->wh_id,
        );
        
        $MAIL->PrepareMailToSend($msg_array);
        
        
        # SEND THE PREPARED MESSAGE
        # ==================================
        if ($MAIL->MailPreparedWhId($this->wh_id)) {
            echo "<h1>Message send to USER.</h1>";
        } else {
            echo "<h1>Unable to send message to USER.</h1>";
        }
        */
        
    }

    public function EmailConfirmationAccountCancel()
    {
        global $ROOT;
        require_once "$ROOT/phplib/swift4/swift_required.php";
        $MAIL = new Email_MailWh;
    
        # SETUP THE EMAIL ADDRESSES
        # ==================================
        global $EMAIL_ADMIN_EMAIL;
        $bcc = ($this->email_send_to_admin) ? $EMAIL_ADMIN_EMAIL : '';
        
        # PREP THE MESSAGE ARRAY
        # ==================================
        $msg_array = array(
            'email_template_id'     => $this->email_template_id,
            'cc'                    => '',
            'bcc'                   => $bcc,
            'wh_id'                 => $this->wh_id,
        );
        
        $MAIL->PrepareMailToSend($msg_array);
        
        
        # SEND THE PREPARED MESSAGE
        # ==================================
        if ($MAIL->MailPreparedWhId($this->wh_id)) {
            echo "<h1>Message send to USER.</h1>";
        } else {
            echo "<h1>Unable to send message to USER.</h1>";
        }
        
    }
    
    
    
} //End Class