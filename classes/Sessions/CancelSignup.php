<?php
class Sessions_CancelSignup extends BaseClass
{
    public $Show_Query                      = false;    // TRUE = output the database queries ocurring on this page
    public $Refund_Credits_On_Cancellation  = true;     // TRUE = give user credits back upon cancellation
    public $Cancel_Reason                   = 'UNKNOWN CANCELLATION REASON';
    
    // ---------- EMAIL SETTINGS ----------
    public $Send_Email_Instructor           = true;     // TRUE = send notification email to instructor when cancelled
    public $Send_Email_Customer             = true;     // TRUE = send notification email to customer when cancelled
    public $Send_Email_BCC_Administrator    = false;    // TRUE = send copy of email to system adminsitrator
    public $instructor_email_template_id    = 12;
    public $customer_email_template_id      = 11;
    
    public $table_credits                   = 'credits';
    public $table_sessions                  = 'sessions';
    public $table_sessions_checklists       = 'session_checklists';
    
    
    // ---------- NON-MODIFIABLE VARIABLES ----------
    public $Email_Administrator_Email       = '';
    public $Email_Instructor_Name           = '';
    public $Email_Instructor_Email          = '';
    public $Instructor_WH_ID                = 0;
    public $Email_Customer_Name             = '';
    public $Email_Customer_Email            = '';
    public $Customer_WH_ID                  = 0;
    public $WH_ID                           = 0;
    public $Sessions_Id                     = 0;        // The Sessions_Id from "sessions" table that we are going to cancel
    public $Approved_To_Delete              = false;    // If passed in via URL vars - will assume its ok to delete w/o further checking
    public $Session_Cancel_Within_Time_Hrs  = 2;        // How many hrs prior to event happening can it be cancelled
    public $Session_Record                  = null;     // Can assign a record already so won't call database to check record - push in array()
    public $User_Type                       = '';       // What type of user is this - decides on delete path
    public $Customer_Record                 = null;     // Needed details about the customer - for sending emails
    public $Instructor_Record               = null;     // Needed details about the instructor - for sending emails
    public $Page_Link_Query                 = '';
    public $Script_Location                 = '';
    public $Is_Overlay                      = false;
    public $OBJ_STEP                        = null;
    public $OBJ_TIMEZONE                    = null;
    public $Force_Not_Cancellable           = false;    // (if gets set) TRUE = won' allow sessionto be cancellable
    
    public $Step_Width = 500;
    public $Step_Array = array(
        1 => 'Cancel Summary',
        2 => 'Confirmation',
    );
    public $Step_Array_Error = array(
        1 => 'Session Information',
        2 => 'Confirmation',
        3 => 'ERROR',
    );
    
    public function  __construct()
    {
        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Description' => 'Checks if a user can cancel a session - and then cancels it',
        );
        
        if (!isset($_SESSION['USER_LOGIN']['LOGIN_RECORD']['wh_id'])) {
            echo "<h2>ERROR :: UNABLE TO VERIFY USER ACCOUNT</h2>";
            exit();
        }
        
        
        # INITIALIZE VARIABLES
        # ===================================
        $this->SetSQL();
        $this->WH_ID                = $_SESSION['USER_LOGIN']['LOGIN_RECORD']['wh_id'];
        
        
        # INITIALIZE CLASS PARAMETERS
        # ===================================
        $this->SetParameters(func_get_args());
        $this->Sessions_Id          = ($this->GetParameter(0)) ? $this->GetParameter(0) : 0;
        $this->Approved_To_Delete   = ($this->GetParameter(1)) ? $this->GetParameter(1) : false;
        $this->User_Type            = ($this->GetParameter(2)) ? $this->GetParameter(2) : '';
        
        
        # INITIALIZE OBJECTS
        # ===================================
        $this->OBJ_STEP             = new General_Steps();
        $this->OBJ_TIMEZONE         = new General_TimezoneConversion();
        
        
        # INITIALIZE OVERLAY
        # ===================================
        $this->Is_Overlay = (Get('template')=='overlay') ? true : false;
        if ($this->Is_Overlay) {
            $this->Page_Link_Query .= ';template=overlay';
            $this->Script_Location .= ';template=overlay';
        }
        
        
        # INITIALIZE PAGE LINKS
        # ===================================
        global $PAGE;
        $this->Page_Link_Query                 = "/office/{$PAGE['pagename']};{$PAGE['query']}";
        $this->Script_Location                 = "/office/{$PAGE['pagename']};{$PAGE['query']}";
        
        
        # SETUP EMAIL ITEMS
        # ===================================
        $this->Email_Administrator_Email = ($this->Send_Email_BCC_Administrator) ? $GLOBALS['EMAIL_ADMIN_EMAIL'] : '';
        
    } // -------------- END __construct --------------
    
    
    public function SetSQL()
    {
        if (empty($this->SQL)) {
            $this->SQL = Lib_Singleton::GetInstance('Lib_Pdo');
        }
    }
    
    public function HandleStep($step)
    {
        $output = '';
        
        switch ($step) {
        
            case 'start':
                # Step :: Shows the summary of what is about to happen
                # ====================================================================
                $link_continue      = $this->MakeStepLink('process_cancellation');
                $cancellable        = $this->CheckIfSessionCanBeCancelled();
                
                $output .= "<h3>You are about to cancel a session you signed up for.</h3><br />";
                
                switch ($this->User_Type) {
                    case 'customer':
                        if (!$cancellable || $this->Force_Not_Cancellable) {
                            $output .= '<div class="error"><h3>You are too close to the sesion time. If you continue to cancel this session you will not receive a refund.</h3></div><br />';
                        } else {
                            $output .= '<div><h3>We will credit your account with one session that you can use in the future. If you have any questions, contact Support@YogaLiveLink.com</h3></div><br /><br />';
                        }
                    break;
                    
                    case 'instructor':
                        if (!$cancellable) {
                            $link = $this->MakeStepLink('notice_contact_administrator');
                            header("Location: {$link}");
                        }
                    break;
                }
                
                # Create show form asking why they want to cancel
                # ====================================================================
                $base_array = array(
                    "form|$link_continue|post|OPTIONS_YOGA_TYPES",
                    "textarea|Reason For Canceling Session|cancel_reason|Y|4|5",
                );
                $base_array     = BaseArraySpecialButtons($base_array, 'OPTIONS_YOGA_TYPES_ADD', 'CONTINUE WITH SESSION CANCEL', '', true, false);
                $base_array[]   = 'endform';
                $options_form   = OutputForm($base_array);
                
                
                $output     .= $options_form;
                $output     .= "<br /><center>" . MakeButton('negative', 'CLOSE WINDOW', '', '', '', "parent.CloseOverlay();") . "</center>";
                $step_output = $this->OBJ_STEP->GetSteps($this->Step_Array, 1, $output, $this->Step_Width);
            break;
            
            
            case 'process_cancellation':
                # ====================================================================
                # Step :: Actually does the cancel and refund
                # ====================================================================
                $this->Cancel_Reason = (Post('FORM_cancel_reason')) ? Post('FORM_cancel_reason') : $this->Cancel_Reason;
                
                # GET RECORD FIRST - Once deactivated we can't get this information
                # ====================================================================
                $this->GetSessionRecord();
                $this->GetCustomerRecord();
                $this->GetInstructorRecord();
                
                # DEACTIVATE THE SESSION
                # ====================================================================
                $result = $this->DeactivateSingleSession($this->Sessions_Id);
                if (!$result) {
                    $link = $this->MakeStepLink('error_deactivate');
                    header("Location: {$link}");
                }
                
                echo "<br />Customer_Record";
                echo ArrayToStr($this->Customer_Record);
                
                echo "<br />Instructor_Record";
                echo ArrayToStr($this->Instructor_Record);
                
                
                # SEND CONFIRMATION EMAILS
                # ====================================================================
                $this->SendCancelEmailToInstructor();
                $this->SendCancelEmailToCustomer();
                
                # GO TO OMPLETION STEP
                # ====================================================================
                $link = $this->MakeStepLink('complete');
                header("Location: {$link}");
            break;
            
            
            //case 'notice_credits_lost':
            //    $step_output = $this->OBJ_STEP->GetSteps($this->Step_Array, 1, $output, $this->Step_Width);
            //break;
            
            //case 'notice_unable_cancel_account':
            //    $step_output = $this->OBJ_STEP->GetSteps($this->Step_Array_Error, 1, $output, $this->Step_Width);
            //break;
            
            case 'notice_contact_administrator':
                $output .= "<div class='error'>You cannot cancel this session because you are within the non-cancel time window. If you need to cancel this session - contact a system administrtor immediately.</div>";
                $output .= MakeButton('negative', 'CLOSE WINDOW', '', '', '', "parent.CloseOverlay();");
                $step_output = $this->OBJ_STEP->GetSteps($this->Step_Array_Error, 3, $output, $this->Step_Width);
            break;
            
            case 'complete':
                $output .= '<h1>SESSION CANCELLATION COMPLETED SUCCESSFULLY</h1>';
                $output .= MakeButton('negative', 'CLOSE WINDOW', '', '', '', "parent.RefreshWindow();");
                $step_output = $this->OBJ_STEP->GetSteps($this->Step_Array, 2, $output, $this->Step_Width);
            break;
            
            case 'error_deactivate':
                $output .= "<div class='error'>ERROR :: Unable to cancel this session. Please contact support@yogalivelink.com.</div>";
                $step_output = $this->OBJ_STEP->GetSteps($this->Step_Array_Error, 3, $output, $this->Step_Width);
            break;
        
        }
        
        $this->AddScript();

        AddStyle("
            .stepwrapper {
                background-color:#9E9D41;
            }
            .steps {
                background-color:#EAE6CD;
            }
        ");
        
        return $step_output;
    }

    public function Execute()
    {
        $step               = (Get('step')) ? Get('step') : 'start';
        $output             = $this->HandleStep($step);
        
        echo $output;
    }
    
    public function GetSessionRecord()
    {
        # GET THE SESSION RECORD
        # ======================================================================
        if (!$this->Session_Record) {
            $record = $this->SQL->GetRecord(array(
                'table'     => $GLOBALS['TABLE_sessions'],
                'keys'      => "sessions_id, utc_start_datetime, utc_end_datetime, instructor_id AS INSTRUCTOR_WH_ID, booked_wh_id AS CUSTOMER_WH_ID",
                'where'     => "`sessions_id`=$this->Sessions_Id AND `active`=1",
            ));
            if ($this->Show_Query) echo '<br /><br />QUERY => ' . $this->SQL->Db_Last_Query;
            $this->Session_Record = $record;
        }
    }
    
    public function GetCustomerRecord()
    {
        # GET THE CUSTOMER RECORD - Based on session record
        # ======================================================================
        if (!$this->Session_Record) {
            $this->GetSessionRecord();
        }
        
        if ($this->Session_Record && !$this->Customer_Record) {
            $record = $this->SQL->GetRecord(array(
                'table'     => $GLOBALS['TABLE_contacts'],
                'keys'      => "first_name, last_name, wh_id, email_address, tz_name AS TIMEZONE",
                'where'     => "`wh_id`='{$this->Session_Record['CUSTOMER_WH_ID']}' AND {$GLOBALS['TABLE_contacts']}.`active`=1",
                'joins'     => "LEFT JOIN {$GLOBALS['TABLE_timezones']} ON {$GLOBALS['TABLE_timezones']}.time_zones_id = {$GLOBALS['TABLE_contacts']}.time_zones_id",
            ));
            if ($this->Show_Query) echo '<br /><br />QUERY => ' . $this->SQL->Db_Last_Query;
            
            $this->Customer_Record          = $record;
            $this->Email_Customer_Name      = "{$this->Customer_Record['first_name']} {$this->Customer_Record['last_name']}";
            $this->Email_Customer_Email     = "{$this->Customer_Record['email_address']}";
            $this->Customer_WH_ID           = "{$this->Customer_Record['wh_id']}";
        }
    }
    
    public function GetInstructorRecord()
    {
        # GET THE INSTRUCTOR RECORD - Based on session record
        # ======================================================================
        if (!$this->Session_Record) {
            $this->GetSessionRecord();
        }
        
        if ($this->Session_Record && !$this->Instructor_Record) {
            $record = $this->SQL->GetRecord(array(
                'table'     => $GLOBALS['TABLE_contacts'],
                'keys'      => "first_name, last_name, wh_id, email_address, tz_name AS TIMEZONE",
                'where'     => "`wh_id`='{$this->Session_Record['INSTRUCTOR_WH_ID']}' AND {$GLOBALS['TABLE_contacts']}.`active`=1",
                'joins'     => "LEFT JOIN {$GLOBALS['TABLE_timezones']} ON {$GLOBALS['TABLE_timezones']}.time_zones_id = {$GLOBALS['TABLE_contacts']}.time_zones_id",
            ));
            if ($this->Show_Query) echo '<br /><br />QUERY => ' . $this->SQL->Db_Last_Query;
            
            $this->Instructor_Record        = $record;
            $this->Email_Instructor_Name    = "{$this->Instructor_Record['first_name']} {$this->Instructor_Record['last_name']}";
            $this->Email_Instructor_Email   = "{$this->Instructor_Record['email_address']}";
            $this->Instructor_WH_ID         = "{$this->Instructor_Record['wh_id']}";
        }
    }
    
        
    
    
    public function CheckIfSessionCanBeCancelled()
    {
        # ================================================================================
        # FUNCTION :: Checks to see if this session can be cancelled - because 
        #             its within cancel time window.
        # ================================================================================
    
        if (!$this->Session_Record) {
            $this->GetSessionRecord();
        }
        
        if (!$this->Session_Record) {
            echo "<br /><br />NO ACTIVE RECORD FOUND";
            $cancellable = false;
        } else { 
        
            # CHECK IF SESSION CAN BE DE-ACTIVATED - WITHIN TIME WINDOW
            # ======================================================================
            /*
            
            ISSUE - time is being based on local server time - NOT 
            the time on the user's computer - is that a problem???
            
            */
            
            $now_date       = date('Y-m-d H:i:00');
            $session_date   = $this->Session_Record['utc_start_datetime'];
            $diff_in_hours  = ((abs(strtotime($session_date) - strtotime($now_date)) / 60) / 60);
            
            #echo "<br />now_date ===> $now_date";
            #echo "<br />session_date ===> $session_date";
            #echo "<br />$diff_in_hours > $this->Session_Cancel_Within_Time_Hrs";
            
            $cancellable = false;
            if (strtotime($session_date) > strtotime($now_date)) {
                if ($diff_in_hours > $this->Session_Cancel_Within_Time_Hrs) {
                    # session CAN be cancelled
                    $cancellable = true;
                } else {
                    # session is occurring too close - cannot cancel without additional authorization
                    $cancellable = false;
                }
            } else {
                # SESSION HAS ALREADY PASSED /// - but for some reason is still not marked completed - DEACTIVATE
                $cancellable = false;
            }
        }
        
        return $cancellable;
    }
    
    public function DeactivateSingleSession($Sessions_Id)
    {
        # 1. deactivate single session via checklist
        # ==============================================================
        $key_values = $this->FormatDataForUpdate(array(
            'cancelled'             => 1,
            'cacelled_reason'       => $this->Cancel_Reason,
            'active'                => 0,
            'cancelled_by_wh_id'    => $this->WH_ID,
        ));
        
        $result = $this->SQL->UpdateRecord(array(
            'table'         => $this->table_sessions_checklists,
            'key_values'    => $key_values,
            'where'         => "`sessions_id`=$Sessions_Id", // AND `active`=1
        ));
        if ($this->Show_Query) echo '<br /><br />QUERY => ' . $this->SQL->Db_Last_Query;
        $echo = ($result) ? "SUCCESS -> Session checklist deactivated." : "FAILED to deactivate session checklist.";
        echo "<br /><br />$echo";
        
        
        # 2. update the session so it can be booked again
        # ==============================================================
        $key_values = $this->FormatDataForUpdate(array(
            'booked'                => 0,
            'booked_wh_id'          => 0,
            'locked'                => 0,
            'locked_wh_id'          => 0,
            'locked_start_datetime' => 0,
        ));
        
        $result = $this->SQL->UpdateRecord(array(
            'table'         => $this->table_sessions,
            'key_values'    => $key_values,
            'where'         => "`sessions_id`=$Sessions_Id", //AND `active`=1
        ));
        if ($this->Show_Query) echo '<br /><br />QUERY => ' . $this->SQL->Db_Last_Query;
        $echo = ($result) ? "SUCCESS -> Session available for purchase again." : "FAILED to make session available for purchase again.";
        echo "<br /><br />$echo";
        
        
        # 3. refund credits
        # ==============================================================
        if ($this->Refund_Credits_On_Cancellation) {
            $date = date('now');
            $note = "\n\n [Date:$date] Credit Refunded from session cancellation (session_id=$Sessions_Id).";
            $result = $this->SQL->AppendValue(array(
                'table'       => $this->table_credits,
                'key'         => 'notes',
                'value'       => "'$note'",
                'where'       => "`sessions_id`=$Sessions_Id AND `active`=1",
            ));
            if ($this->Show_Query) echo '<br /><br />QUERY => ' . $this->SQL->Db_Last_Query;
            $echo = ($result) ? "SUCCESS -> Notes about credit refund UPDATED." : "FAILED to update notes about credit refund.";
            echo "<br /><br />$echo";
            
            
            $FormArray = array(
                'used'          => 0,
                //'sessions_id'   => 0,
            );
            $key_values = $this->FormatDataForUpdate($FormArray);
            
            $result = $this->SQL->UpdateRecord(array(
                'table'         => $this->table_credits,
                'key_values'    => $key_values,
                'where'         => "`sessions_id`=$Sessions_Id AND `active`=1",
            ));
            if ($this->Show_Query) echo '<br /><br />QUERY => ' . $this->SQL->Db_Last_Query;
            $echo = ($result) ? "SUCCESS -> Credits refunded." : "FAILED to refund credits.";
            echo "<br /><br />$echo";
        }
        
        return $result;
    }

    public function AddScript()
    {
        global $JS_CLOSE_WINDOW_SCRIPT;
        AddScript($JS_CLOSE_WINDOW_SCRIPT);
    }
    
    public function MakeStepLink($STEP)
    {
        return "{$this->Script_Location};step={$STEP}";
        #;sid={$this->Sessions_Id}
    }
    
    
    
    # EMAIL SENDING FUNCTIONS
    # ====================================================================
    
    private function SendCancelEmailToCustomer()
    {
        if ($this->Send_Email_Customer) {
            # INITIALIZE THE EMAIL CLASS
            # =====================================================
            global $ROOT;
            require_once "$ROOT/phplib/swift4/swift_required.php";
            $MAIL = new Email_MailWh;
            
            
            # MAKE SESSION CONTENT
            # =====================================================
            $Email_Content_Session_Info     = $this->MakeContentForEmail_SessionInfo($this->Customer_Record['TIMEZONE']);
            $Email_Content_Customer_Info    = $this->MakeContentForEmail_CustomerInfo();
            $Email_Content_Instructor_Info  = $this->MakeContentForEmail_InstructorInfo();
            
            
            # PREP THE SWAP ARRAY
            # =====================================================
            $swap_array = array (
                '@@login_url@@'         => $GLOBALS['URL_SITE_LOGIN'],
                '@@session_info@@'      => $Email_Content_Session_Info,
                '@@customer_info@@'     => $Email_Content_Customer_Info,
                '@@instructor_info@@'   => $Email_Content_Instructor_Info,
                '@@cancel_reason@@'     => $this->Cancel_Reason,
            );
            
            
            # PREP THE MESSAGE ARRAY
            # =====================================================
            $bcc = ($this->email_bcc_to_admin) ? $GLOBALS['EMAIL_ADMIN_EMAIL'] : '';
            $MAIL->PrepareMailToSend(array(
                'email_template_id'     => $this->customer_email_template_id,
                'swap_array'            => $swap_array,
                'to_name'               => $this->Email_Customer_Name,
                'to_email'              => $this->Email_Customer_Email,
                'bcc'                   => $this->Email_Administrator_Email,
                'wh_id'                 => $this->Customer_WH_ID,
            ));
            
            
            # SEND THE PREPARED MESSAGE
            # =====================================================
            if ($MAIL->MailPrepared()) {
                echo "<h1>Message send to CUSTOMER.</h1>";
                $key_values = $this->FormatDataForUpdate(array(
                    'email_cancelled_user_sent'         => 1,
                ));
            } else {
                echo "<h1>Unable to send message to CUSTOMER.</h1>";
                $key_values = $this->FormatDataForUpdate(array(
                    'email_cancelled_user_sent'         => 9,
                ));
            }
            
            
            # UPDATE THE CHECKLIST
            # ==============================================================
            $result = $this->SQL->UpdateRecord(array(
                'table'         => $this->table_sessions_checklists,
                'key_values'    => $key_values,
                'where'         => "`sessions_id`={$this->Sessions_Id}",
            ));
            if ($this->Show_Query) echo '<br /><br />QUERY => ' . $this->SQL->Db_Last_Query;
        }
    }
    
    private function SendCancelEmailToInstructor()
    {
        if ($this->Send_Email_Instructor) {
            # INITIALIZE THE EMAIL CLASS
            # =====================================================
            global $ROOT;
            require_once "$ROOT/phplib/swift4/swift_required.php";
            $MAIL = new Email_MailWh;
            
            
            # MAKE SESSION CONTENT
            # =====================================================
            $Email_Content_Session_Info     = $this->MakeContentForEmail_SessionInfo($this->Instructor_Record['TIMEZONE']);
            $Email_Content_Customer_Info    = $this->MakeContentForEmail_CustomerInfo();
            $Email_Content_Instructor_Info  = $this->MakeContentForEmail_InstructorInfo();
            
            
            # PREP THE SWAP ARRAY
            # =====================================================
            $swap_array = array (
                '@@login_url@@'         => $GLOBALS['URL_SITE_LOGIN'],
                '@@session_info@@'      => $Email_Content_Session_Info,
                '@@customer_info@@'     => $Email_Content_Customer_Info,
                '@@instructor_info@@'   => $Email_Content_Instructor_Info,
                '@@cancel_reason@@'     => $this->Cancel_Reason,
            );
            
            
            # PREP THE MESSAGE ARRAY
            # =====================================================
            $MAIL->PrepareMailToSend(array(
                'email_template_id'     => $this->instructor_email_template_id,
                'swap_array'            => $swap_array,
                'to_name'               => $this->Email_Instructor_Name,
                'to_email'              => $this->Email_Instructor_Email,
                'bcc'                   => $this->Email_Administrator_Email,
                'wh_id'                 => $this->Instructor_WH_ID,
            ));
            
            
            # SEND THE PREPARED MESSAGE
            # =====================================================
            if ($MAIL->MailPrepared()) {
                echo "<h1>Message send to INSTRUCTOR.</h1>";
                $key_values = $this->FormatDataForUpdate(array(
                    'email_cancelled_instructor_sent'         => 1,
                ));
            } else {
                echo "<h1>Unable to send message to INSTRUCTOR.</h1>";
                $key_values = $this->FormatDataForUpdate(array(
                    'email_cancelled_instructor_sent'         => 9,
                ));
            }
            
            
            # UPDATE THE CHECKLIST
            # ==============================================================
            $result = $this->SQL->UpdateRecord(array(
                'table'         => $this->table_sessions_checklists,
                'key_values'    => $key_values,
                'where'         => "`sessions_id`={$this->Sessions_Id}",
            ));
            if ($this->Show_Query) echo '<br /><br />QUERY => ' . $this->SQL->Db_Last_Query;
        }
    }
    
    private function MakeContentForEmail_SessionInfo($TIMEZONE)
    {
        # ========================================================================
        # FUNCTION :: Create the output for how the session will be displayed
        # ========================================================================
        
        $USER_LOCAL_TIMEZONE = $TIMEZONE;
        
        global $USER_DISPLAY_DATE, $USER_DISPLAY_TIME, $USER_DISPLAY_DATE_CALC, $USER_LOCAL_TIMEZONE_DISPLAY;

        $input_date_time        = $this->Session_Record['utc_start_datetime'];
        $input_timezone         = 'UTC';
        $output_timezone        = $USER_LOCAL_TIMEZONE;
        $output_format          = "$USER_DISPLAY_DATE|$USER_DISPLAY_TIME";
        $user_start_datetime    = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
        $parts                  = explode('|', $user_start_datetime);
        $user_start_date        = $parts[0];
        $user_start_time        = $parts[1];

        $input_date_time        = $this->Session_Record['utc_end_datetime'];
        $input_timezone         = 'UTC';
        $output_timezone        = $USER_LOCAL_TIMEZONE;
        $output_format          = "$USER_DISPLAY_DATE_CALC|$USER_DISPLAY_TIME";
        $user_end_datetime      = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
        $parts                  = explode('|', $user_end_datetime);
        $user_end_date          = $parts[0];
        $user_end_time          = $parts[1];
        
        $data = array(
            "DATE:|{$user_start_date}",
            "TIME:|{$user_start_time} - {$user_end_time}",
            "|($USER_LOCAL_TIMEZONE_DISPLAY)",
        );
        $box_content    = MakeTable($data, 'font-size:13px;');
        $output         = AddBox_Type1('SESSION INFORMATION', $box_content);
        
        return $output;
    }
    
    private function MakeContentForEmail_CustomerInfo()
    {
        $name = ucwords(strtolower("{$this->Customer_Record['first_name']} {$this->Customer_Record['last_name']}"));
        $data = array(
            "NAME:|{$name}",
        );
        $box_content    = MakeTable($data, 'font-size:13px;');
        $output         = AddBox_Type1('CUSTOMER INFORMATION', $box_content);
        
        return $output;
    }
    
    private function MakeContentForEmail_InstructorInfo()
    {
        $name = ucwords(strtolower("{$this->Instructor_Record['first_name']} {$this->Instructor_Record['last_name']}"));
        $data = array(
            "NAME:|{$name}",
        );
        $box_content    = MakeTable($data, 'font-size:13px;');
        $output         = AddBox_Type1('INSTRUCTOR INFORMATION', $box_content);
        
        return $output;
    }
    
    
}  // -------------- END CLASS --------------