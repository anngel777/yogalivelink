<?php

/* ========================================================================== 
    FUNCTION :: Profile_ReactivateAccountCustomer
    
    Used by administrators to re-activate accounts in the website. This
    class will re-activate all user information but will not activate credits.
    
    This function can be called from either an admin panel or a user panel.
    
    If this is a user - it will ask some questions
    
    If this is an admin - it will override all checks and just activate the account
    
    
# ========================================================================== */


class Profile_ReactivateAccountCustomer extends BaseClass
{
    public $wh_id       = 0;
    public $ShowArray   = false;
    public $Show_Query  = false;
    
    public $session_cancel_within_time_hrs      = 1; // how close to session happening is a cancellation allowed
    public $user_is_customer                    = 0; // the person calling this class is a customer
    public $user_is_administrator               = 0; // the person calling this class is an administrator
    
    
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
    
    public $email_template_id               = 10;
    public $email_send_to_admin             = false;
    
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
            
            /*
            
            # CHECK IF THE USER HAS ANY CREDITS
            # ========================================================
            $this->GetCredits();
            
            
            # CHECK IF THE USER HAS ANY BOOKED SESSIONS
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
            }
            
            if ($this->credit_count_free > 0) {
                echo "<br /><br />USER HAS FREE CREDITS";
                # <> IF USER
                # if this is the user - ask if they want to use credits before cancelling
                
                # <> IF ADMIN
                # De-activate all free credits
                $result = $this->DeactivateFreeCredits();
            }
            
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
                $result = $this->DeactivatePurchasedCredits();
                
                # SEND CONFIRMATION EMAIL TO USER
                $this->EmailConfirmationRefund();
            }
            
            */
            
            # DEACTIVATE THE ACCOUNT
            # ========================================================================
            $result = $this->ReactivateCustomerAccount();
            if ($result) {
                $this->EmailConfirmationAccountReactivate();
            }
            
            /*
            # ASK SURVEY QUESTIONS
            # ========================================================================
            if ($this->user_is_customer) {
                
            }
            */
            
            # SHOW CONFIRMATION OF DELETION TO USER / ADMIN
            # ========================================================================
            echo "<br /><br />User re-activation process is now complete.";
            
            /*
            # SEND CONFIRMATION EMAIL TO USER
            # ========================================================================
            $this->EmailConfirmationRefund();
            */
            
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
    
    
    
    
    
    public function CheckIfCustomerAccountActive()
    {
        $record = $this->SQL->GetRecord(array(
            'table'     => 'contacts',
            'keys'      => 'active',
            'where'     => "`wh_id`=$this->wh_id AND `active`=0",
        ));
        if ($this->Show_Query) echo '<br /><br />' . $this->SQL->Db_Last_Query;
        
        $result = ($record) ? true : false;
        
        return $result;
    }
    

    public function ReactivateCustomerAccount()
    {
        # CHECK IF RECORD IS DEACTIVATED
        # ================================================
        $inactive = $this->CheckIfCustomerAccountActive();
        
        
        if ($inactive) {
            
            # DEACTIVATE contacts
            # ================================================
            $result = $this->SQL->UpdateRecord(array(
                'table'         => 'contacts',
                'key_values'    => "`active`=1",
                'where'         => "`wh_id`=$this->wh_id AND `active`=0",
            ));
            if ($this->Show_Query) echo '<br /><br />' . $this->SQL->Db_Last_Query;
            
            
            # DEACTIVATE contacts_account
            # ================================================
            $result = $this->SQL->UpdateRecord(array(
                'table'         => 'contacts_account',
                'key_values'    => "`active`=1",
                'where'         => "`wh_id`=$this->wh_id AND `active`=0",
            ));
            if ($this->Show_Query) echo '<br /><br />' . $this->SQL->Db_Last_Query;
            
            
            # DEACTIVATE contacts_billing_storage
            # ================================================
            $result = $this->SQL->UpdateRecord(array(
                'table'         => 'contacts_billing_storage',
                'key_values'    => "`active`=1",
                'where'         => "`wh_id`=$this->wh_id AND `active`=0",
            ));
            if ($this->Show_Query) echo '<br /><br />' . $this->SQL->Db_Last_Query;
            
            
            # DEACTIVATE customer_profile
            # ================================================
            $result = $this->SQL->UpdateRecord(array(
                'table'         => 'customer_profile',
                'key_values'    => "`active`=1",
                'where'         => "`wh_id`=$this->wh_id AND `active`=0",
            ));
            if ($this->Show_Query) echo '<br /><br />' . $this->SQL->Db_Last_Query;
            
        } else {
            echo "<br /><div style='width:400px;'><h1 style='color:#990000'>USER IS CURRENTLY ACTIVE - UNABLE TO DE-ACTIVATE</h1></div>";
            $result = false;
        }
        
        return $result;
    }
    
    
    public function EmailConfirmationAccountReactivate()
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
    
    
    
    
} // END CLASS