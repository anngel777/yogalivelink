<?php
# ==================================================================================
# CLASS ::  Used to show details about a session. Generally for instructors to see 
#           who booked a session and what tasks they can do.
# ==================================================================================

class Sessions_Details extends BaseClass
{
    public $show_query                      = false;
    public $show_array                      = false;
    
    public $session_record                  = array();
    public $customer_record                 = array();
    public $instructor_record               = array();
    

    
    public $Page_Link_Query                 = '';
    public $Is_Overlay                      = false;

    
    public $WH_ID                           = 0;
    public $Server_Timezone                 = '';

    public $Picture_Dir                     = '/office/';
    public $Image_Dir_Instructor            = 'images/instructors/';
    public $Image_No_Pic                    = 'thumbnail_no_picture.jpg';

    public $sessions_id                     = 0;
    
    public $customer_wh_id                  = 0;


    # note ---> might want to do thiswith ajax calls like the user starting a session
    public $OBJ_TIMEZONE                = null;


    public function  __construct()
    {
        parent::__construct();
        
        
        $this->OBJ_TIMEZONE         = $GLOBALS['TIMEZONE'];
        $this->WH_ID                = (isset($_SESSION['USER_LOGIN']['LOGIN_RECORD']['wh_id'])) ? $_SESSION['USER_LOGIN']['LOGIN_RECORD']['wh_id'] : 0;
        $this->Server_Timezone      = date_default_timezone_get();
        $this->Page_Link_Query      = preg_replace('/;step=[a-zA-Z0-9_\-]*;/', ';', Server('REQUEST_URI'));  // removes step
        
        
        $this->SetParameters(func_get_args());
        $this->sessions_id          = ($this->GetParameter(0)) ? $this->GetParameter(0) : 0;
        
        
        
        # INITIALIZE OVERLAY
        # ===================================
        $this->Is_Overlay = (Get('template')=='overlay') ? true : false;
        if ($this->Is_Overlay) {
            $this->Page_Link_Query .= ';template=overlay';
            $this->script_location .= ';template=overlay';
        }
        
        
        # INITIALIZE PAGE LINKS
        # ===================================
        global $PAGE;
        $this->Page_Link_Query                 = "/office/{$PAGE['pagename']};{$PAGE['query']}";
        $this->Script_Location                 = "/office/{$PAGE['pagename']};{$PAGE['query']}";
        
    } // -------------- END __construct --------------

    
    public function Execute()
    {
        $this->sessions_id  = (Get('sid')) ? Get('sid') : $this->sessions_id;
        
        # GET THE SESSION INFORMATION
        $this->GetSessionRecord();
        $this->GetInstructorRecord();
        $this->GetCustomerRecord();
        
        # FORMAT THE OUTPUT
        $session    = $this->ShowSessionInformation();
        $checklist  = $this->ShowSessionChecklistInformation();
        $instructor = $this->ShowInstructorInformation();
        $customer   = $this->ShowCustomerInformation();
        $status     = $this->ShowSessionStatusInformation();
        $actions    = $this->ShowActionInformation();
        
        # CREATE THE FINAL OUTPUT
        $output = "
        <div style='width:700px;'>
        
            <div class='col' style='width:47%;'>
                <div style='border:1px solid #990000;'>{$session}</div>
                <br /><br />
                <div style='border:1px solid #990000;'>{$actions}</div>
                <br /><br />
                <div style='border:1px solid #990000;'>{$instructor}</div>
                <br /><br />
                <div style='border:1px solid #990000;'>{$customer}</div>
            </div>
            <div class='col'>
                &nbsp;&nbsp;&nbsp;&nbsp;
            </div>
            
            <div class='col' style='width:47%;'>
                <div style='border:1px solid #990000;'>{$status}</div>
                <br /><br />
                <div style='border:1px solid #990000;'>{$checklist}</div>
            </div>
            <div class='col'>
                &nbsp;&nbsp;&nbsp;&nbsp;
            </div>
            
            
            
            <div class='clear'></div>
            <br /><br />
            
        </div>
        ";
        
        echo $output;
    }
    


    public function GetSessionRecord()
    {
        $instructor_rating_keys = "
        session_ratings_instructor.overall_session              AS INS_overall_session, 
        session_ratings_instructor.technical_quality_video      AS INS_technical_quality_video, 
        session_ratings_instructor.technical_quality_audio      AS INS_technical_quality_audio
        ";
        
        /*
        $records = $this->SQL->GetArrayAll(array(
            'table' => "$this->table_session_checklists",
            'keys'  => "$this->table_session_checklists.*, $this->table_sessions.*, $this->table_sessions.sessions_id AS SID, $this->table_instructor_profile.*, $this->table_session_checklists.wh_id AS user_wh_id, session_ratings_user.*, $instructor_rating_keys",
            //'where' => "`wh_id`=$this->WH_ID AND $this->table_chats.active=1",
            'where' => "$this->table_session_checklists.active=1",
            'joins' => "
                LEFT JOIN $this->table_sessions ON $this->table_sessions.sessions_id = $this->table_session_checklists.sessions_id 
                LEFT JOIN $this->table_instructor_profile ON $this->table_instructor_profile.wh_id = $this->table_sessions.instructor_id 
                LEFT JOIN session_ratings_user ON session_ratings_user.sessions_id = $this->table_session_checklists.sessions_id 
                LEFT JOIN session_ratings_instructor ON session_ratings_instructor.sessions_id = $this->table_session_checklists.sessions_id 
                ",
        ));
        */
        
        $record = $this->SQL->GetRecord(array(
            'table' => $GLOBALS['TABLE_sessions'],
            'keys'  => "{$GLOBALS['TABLE_sessions']}.*, {$GLOBALS['TABLE_session_checklists']}.*, session_ratings_user.*, $instructor_rating_keys",
            'where' => "`{$GLOBALS['TABLE_sessions']}`.`sessions_id`='{$this->sessions_id}' AND {$GLOBALS['TABLE_sessions']}.active=1",
            'joins' => "LEFT JOIN {$GLOBALS['TABLE_session_checklists']} ON {$GLOBALS['TABLE_session_checklists']}.sessions_id = {$GLOBALS['TABLE_sessions']}.sessions_id
                        LEFT JOIN session_ratings_user ON session_ratings_user.sessions_id = {$GLOBALS['TABLE_sessions']}.sessions_id 
                        LEFT JOIN session_ratings_instructor ON session_ratings_instructor.sessions_id = {$GLOBALS['TABLE_sessions']}.sessions_id 
                        ",
        ));
        if ($this->show_query) echo "<br /><b>LAST QUERY =</b> " . $this->SQL->Db_Last_Query;
        if ($this->show_array) echo "<br /><b>LAST QUERY ARRAY =</b> " . ArrayToStr($record);
        
        $this->session_record = $record;
    }
    
    public function GetInstructorRecord()
    {
        if ($this->session_record) {
            $record = $this->SQL->GetRecord(array(
                'table' => $GLOBALS['TABLE_contacts'],
                'keys'  => "{$GLOBALS['TABLE_contacts']}.*, {$GLOBALS['TABLE_instructor_profile']}.*, {$GLOBALS['TABLE_timezones']}.*",
                'where' => "{$GLOBALS['TABLE_contacts']}.`wh_id`='{$this->session_record['instructor_id']}' AND {$GLOBALS['TABLE_contacts']}.active=1",
                'joins' => "LEFT JOIN {$GLOBALS['TABLE_instructor_profile']} ON {$GLOBALS['TABLE_instructor_profile']}.wh_id = {$GLOBALS['TABLE_contacts']}.wh_id
                            LEFT JOIN {$GLOBALS['TABLE_timezones']} ON {$GLOBALS['TABLE_timezones']}.time_zones_id = {$GLOBALS['TABLE_contacts']}.time_zones_id",
            ));
            if ($this->show_query) echo "<br /><b>LAST QUERY =</b> " . $this->SQL->Db_Last_Query;
            if ($this->show_array) echo "<br /><b>LAST QUERY ARRAY =</b> " . ArrayToStr($record);
            
            $this->instructor_record = $record;
        }
    }
    
    public function GetCustomerRecord()
    {
        if ($this->session_record) {
            $record = $this->SQL->GetRecord(array(
                'table' => $GLOBALS['TABLE_contacts'],
                'keys'  => "{$GLOBALS['TABLE_contacts']}.*, {$GLOBALS['TABLE_timezones']}.*",
                'where' => "`wh_id`='{$this->session_record['booked_wh_id']}' AND {$GLOBALS['TABLE_contacts']}.active=1",
                'joins' => "LEFT JOIN {$GLOBALS['TABLE_timezones']} ON {$GLOBALS['TABLE_timezones']}.time_zones_id = {$GLOBALS['TABLE_contacts']}.time_zones_id",
            ));
            if ($this->show_query) echo "<br /><b>LAST QUERY =</b> " . $this->SQL->Db_Last_Query;
            if ($this->show_array) echo "<br /><b>LAST QUERY ARRAY =</b> " . ArrayToStr($record);
            
            $this->customer_record = $record;
        }
    }







    public function ShowSessionInformation()
    {
        # ========================================================================
        # FUNCTION :: Create the output for how the session will be displayed
        # ========================================================================
        
        global $USER_LOCAL_TIMEZONE, $USER_DISPLAY_DATE, $USER_DISPLAY_TIME, $USER_DISPLAY_DATE_CALC, $USER_LOCAL_TIMEZONE_DISPLAY;

        $input_date_time        = $this->session_record['utc_start_datetime'];
        $input_timezone         = 'UTC';
        $output_timezone        = $USER_LOCAL_TIMEZONE;
        $output_format          = "$USER_DISPLAY_DATE|$USER_DISPLAY_TIME";
        $user_start_datetime    = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
        $parts                  = explode('|', $user_start_datetime);
        $user_start_date        = $parts[0];
        $user_start_time        = $parts[1];

        $input_date_time        = $this->session_record['utc_end_datetime'];
        $input_timezone         = 'UTC';
        $output_timezone        = $USER_LOCAL_TIMEZONE;
        $output_format          = "$USER_DISPLAY_DATE_CALC|$USER_DISPLAY_TIME";
        $user_end_datetime      = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
        $parts                  = explode('|', $user_end_datetime);
        $user_end_date          = $parts[0];
        $user_end_time          = $parts[1];
        
        
        $session_type_description   = '';
        $session_type_description  .= ($this->session_record['type_standard']) ? ' [Standard] ' : '';
        $session_type_description  .= ($this->session_record['type_therapy']) ? ' [Therapy] ' : '';
        
        # FORMAT THE SESSION INFO
        # =================================================================
        
        $data = array(
            "DATE:|{$user_start_date}",
            "TIME:|{$user_start_time} - {$user_end_time}",
            "|($USER_LOCAL_TIMEZONE_DISPLAY)",
            "TYPE:|{$session_type_description}",
        );
        $session_box_content        = MakeTable($data, 'font-size:13px;');
        $output                     = AddBox_Type1('SESSION INFORMATION', $session_box_content);
        
        return $output;
    }

    public function ShowSessionStatusInformation()
    {
        if ($this->session_record['paid'] == 1) {
            $content[] = "Status:|BOOKED";
            $content[] = "Payment ID:|{$this->session_record['payment_id']}";
            $content[] = "Booked By WHID:|{$this->session_record['booked_wh_id']}";
        } else {
            $content[] = "Status:|AVAILABLE";
        }
        
        if ($this->session_record['locked'] == 1) {
            $content[] = "|&nbsp;";
            $content[] = "Status:|LOCKED";
            $content[] = "Locked By WHID:|{$this->session_record['locked_wh_id']}";
            $content[] = "Locked Start:|{$this->session_record['locked_start_datetime']}";
        }
        
        if ($this->session_record['cancelled'] == 1) {
            $content[] = "|&nbsp;";
            $content[] = "Status:|CANCELLED";
            $content[] = "Cancelled Reason:|{$this->session_record['cacelled_reason']}";
            $content[] = "Cancelled By WHID:|{$this->session_record['cacelled_reason']}";
        }
        
        if ($this->session_record['session_started'] == 1) {
            $content[] = "|&nbsp;";
            $content[] = "Status:|SESSION STARTED";
            $content[] = "Start Time|{$this->session_record['session_started_datetime']}";
            $content[] = "Customer Login|{$this->session_record['login_user_datetime']}";
            $content[] = "Instructor Login|{$this->session_record['login_instructor_datetime']}";
        }
        
        if ($this->session_record['session_completed'] == 1) {
            $content[] = "|&nbsp;";
            $content[] = "Status:|SESSION COMPLETED";
            $content[] = "Completed Time|{$this->session_record['session_completed_datetime']}";
        }
        
        $content = MakeTable($content);
        
        //$content        = "\n<div id='customer_profile_info'>\n" . $this->GetCustomerProfileContent() . "\n</div>\n";
        $edit_link      = ''; //"<a href='#' onclick=\"top.parent.appformCreateOverlay('INSTRUCTOR PROFILE', getClassExecuteLinkNoAjax('{$eq_CustomerProfileContacts}'), 'apps'); return false;\">[T~CPO_006]</a>";
        $output         = AddBox('SESSION STATUS', $content, $edit_link) . '<br /><br />';
        
        return $output;
    }
    
    public function ShowSessionChecklistInformation()
    {
        $ico_yes                = "<img src='{$GLOBALS['ICO_YES']}' alt='' />";
        $ico_no                 = "<img src='{$GLOBALS['ICO_NO']}' alt='' />";
        
        $content[] = ($this->session_record['paid'] == 1)                               ? "$ico_yes|Paid"                                       : "$ico_no|Paid";
        $content[] = ($this->session_record['email_booked_user_sent'] == 1)             ? "$ico_yes|Customer - Session Booked Email Sent"       : "$ico_no|Customer - Session Booked Email Sent";
        $content[] = ($this->session_record['email_booked_instructor_sent'] == 1)       ? "$ico_yes|Instructor - Session Booked Email Sent"     : "$ico_no|Instructor - Session Booked Email Sent";
        $content[] = ($this->session_record['email_reminder_1_user_sent'] == 1)         ? "$ico_yes|Customer - Reminder 1 Email Sent"           : "$ico_no|Customer - Reminder 1 Email Sent";
        $content[] = ($this->session_record['email_reminder_1_instructor_sent'] == 1)   ? "$ico_yes|Instructor - Reminder 1 Email Sent"         : "$ico_no|Instructor - Reminder 1 Email Sent";
        $content[] = ($this->session_record['email_reminder_2_user_sent'] == 1)         ? "$ico_yes|Customer - Reminder 2 Email Sent"           : "$ico_no|Customer - Reminder 2 Email Sent";
        $content[] = ($this->session_record['email_reminder_2_instructor_sent'] == 1)   ? "$ico_yes|Instructor - Reminder 2 Email Sent"         : "$ico_no|Instructor - Reminder 2 Email Sent";
        
        $content[] = "|&nbsp;";
        $content[] = ($this->session_record['session_started'] == 1)                    ? "$ico_yes|Session Started"                            : "$ico_no|Session Started";    
        $content[] = ($this->session_record['login_user'] == 1)                         ? "$ico_yes|Customer - Logged Into Session"             : "$ico_no|Customer - Logged Into Session";
        $content[] = ($this->session_record['login_instructor'] == 1)                   ? "$ico_yes|Instructor - Logged Into Session"           : "$ico_no|Instructor - Logged Into Session";
        
        $content[] = "|&nbsp;";
        $content[] = ($this->session_record['session_completed'] == 1)                  ? "$ico_yes|Session Completed"                          : "$ico_no|Session Completed";    
        $content[] = ($this->session_record['rating_user'] == 1)                        ? "$ico_yes|Customer - Rated Session"                   : "$ico_no|Customer - Rated Session";
        $content[] = ($this->session_record['rating_instructor'] == 1)                  ? "$ico_yes|Instructor - Rated Session"                 : "$ico_no|Instructor - Rated Session";
        $content[] = ($this->session_record['instructor_video_uploaded'] == 1)          ? "$ico_yes|Instructor - Uploaded Video"                : "$ico_no|Instructor - Uploaded Video";
        
        $content[] = "|&nbsp;";
        $content[] = ($this->session_record['cancelled'] == 1)                          ? "$ico_yes|Session Cancelled"                          : "$ico_no|Session Cancelled";
        $content[] = ($this->session_record['email_cancelled_user_sent'] == 1)          ? "$ico_yes|Customer - Cancelled Email Sent"            : "$ico_no|Customer - Cancelled Email Sent";
        $content[] = ($this->session_record['email_cancelled_instructor_sent'] == 1)    ? "$ico_yes|Instructor - Cancelled Email Sent"          : "$ico_no|Instructor - Cancelled Email Sent";
        
        $content = MakeTable($content);
        
        //$content        = "\n<div id='customer_profile_info'>\n" . $this->GetCustomerProfileContent() . "\n</div>\n";
        $edit_link      = ''; //"<a href='#' onclick=\"top.parent.appformCreateOverlay('INSTRUCTOR PROFILE', getClassExecuteLinkNoAjax('{$eq_CustomerProfileContacts}'), 'apps'); return false;\">[T~CPO_006]</a>";
        $output         = AddBox('SESSION CHECKLIST', $content, $edit_link) . '<br /><br />';
        
        return $output;
    }
    
    public function ShowInstructorInformation()
    {
        $record = $this->instructor_record;
        
        $address                = FormatAddress("<div>{$record['address_1']}\n{$record['address_2']}\n{$record['address_3']}\n{$record['city']}, {$record['state']}\n{$record['postal_code']}</div>");
        $instructor_picture     = ($this->instructor_record['primary_pictures_id']) ? "{$this->Picture_Dir}{$this->instructor_record['primary_pictures_id']}" : "{$this->Picture_Dir}{$this->Image_Dir_Instructor}{$this->Image_No_Pic}";
        $instructor_picture     = "<img src='{$instructor_picture}' border='0' height='50' alt='' />";
        
        $content = MakeTable(array(
            "ID:|{$record['wh_id']}",
            "Name:|{$record['first_name']} {$record['middle_name']} {$record['last_name']}",
            "|<br />",
            "Address:|{$address}",
            "|<br />",
            "Email:|{$record['email_address']}",
            "Phone:|{$record['phone_home']}",
            "Timezone: |{$record['tz_name']}",
            "Picture: |{$instructor_picture}",
        ));
        
        //$content        = "\n<div id='customer_profile_info'>\n" . $this->GetCustomerProfileContent() . "\n</div>\n";
        $edit_link      = ''; //"<a href='#' onclick=\"top.parent.appformCreateOverlay('INSTRUCTOR PROFILE', getClassExecuteLinkNoAjax('{$eq_CustomerProfileContacts}'), 'apps'); return false;\">[T~CPO_006]</a>";
        $output         = AddBox('INSTRUCTOR INFORMATION', $content, $edit_link) . '<br /><br />';
        
        return $output;
    }
    
    public function ShowCustomerInformation()
    {
        $record = $this->customer_record;
        
        if ($record) {
        $address = FormatAddress("<div>{$record['address_1']}\n{$record['address_2']}\n{$record['address_3']}\n{$record['city']}, {$record['state']}\n{$record['postal_code']}</div>");
        
        $content = MakeTable(array(
            "ID:|{$record['wh_id']}",
            "Name:|{$record['first_name']} {$record['middle_name']} {$record['last_name']}",
            "|<br />",
            "Address:|{$address}",
            "|<br />",
            "Email:|{$record['email_address']}",
            "Phone:|{$record['phone_home']}",
            "Timezone: |{$record['tz_name']}",
        ));
        } else {
            $content = 'NO CUSTOMER REGISTERED';
        }
        
        //$content        = "\n<div id='customer_profile_info'>\n" . $this->GetCustomerProfileContent() . "\n</div>\n";
        $edit_link      = ''; //"<a href='#' onclick=\"top.parent.appformCreateOverlay('INSTRUCTOR PROFILE', getClassExecuteLinkNoAjax('{$eq_CustomerProfileContacts}'), 'apps'); return false;\">[T~CPO_006]</a>";
        $output         = AddBox('CUSTOMER INFORMATION', $content, $edit_link) . '<br /><br />';
        
        return $output;
    }
    
    public function ShowActionInformation()
    {
    
        $OBJ_SESS   = new Profile_CustomerProfileSessions();
        $OBJ_SESS->Is_Instructor = true;
        $OBJ_SESS->show_session_details = false;
        $content    = $OBJ_SESS->CalculateActions($this->session_record);
        
        //$content        = "\n<div id='customer_profile_info'>\n" . $this->GetCustomerProfileContent() . "\n</div>\n";
        $edit_link      = ''; //"<a href='#' onclick=\"top.parent.appformCreateOverlay('INSTRUCTOR PROFILE', getClassExecuteLinkNoAjax('{$eq_CustomerProfileContacts}'), 'apps'); return false;\">[T~CPO_006]</a>";
        $output         = AddBox('SESSION ACTIONS', $content, $edit_link) . '<br /><br />';
        
        return $output;
    }

    public function AddScript()
    {
        AddScriptInclude('/jslib/countdown/jquery.countdown.pack.js');

        $script = <<<SCRIPT
            $('#dialog').dialog({
                autoOpen: false,
                width: 400,
                modal: true,
                resizable: false,
                buttons: {
                    "Use Credits": function() {
                        document.testconfirmJQ.submit();
                    },
                    "Cancel": function() {
                        $(this).dialog("close");
                    }
                }
            });


            // BUY DIALOG POPUP
            // ====================================================
            $('form#testconfirmJQ').submit(function(){
                $('#dialog').dialog('open');
                return false;
            });

            $('#btn_purchase_continue').click(function(){
                $('#dialog').dialog('open');
                return false;
            });

            $('#btn_purchase_use_credits').click(function(){
                $('#dialog').dialog('open');
                return false;
            });

            $('input#TBcancel').click(function(){
                tb_remove();
            });

            $('input#TBsubmit').click(function(){
                document.testconfirmTB.submit();
            });


            // COUNTDOWN TIMER - PURCHASING
            // ====================================================
            var expireTime = new Date({$this->time_release_javascript_user});
            //alert('expireTime ===> '+ expireTime);
            
            $('#shortly').countdown({
                until: expireTime,
                onExpiry: liftOff,
                onTick: watchCountdown,
                format: 'MS',
                expiryUrl: '{$this->script_location};step=timeexpire;sid={$this->sessions_id}'
                });

            function liftOff() {
                alert('You have not completed session purchase in time!');
            }

            function watchCountdown(periods) {
                if (periods[6] < 10) {
                    var seconds = '0' + periods[6];
                } else {
                    var seconds = periods[6];
                }

                $('#time_remaining_monitor').text(periods[5] + ':' + seconds);

                //if ($.countdown.periodsToSeconds(periods) == 5) {
                //    $(this).addClass('highlight');
                //}
            }


            // COUNTDOWN TIMER - RESTART PURCHASE
            // ====================================================
            $('#restart_process_countdown').countdown({
                until: +{$this->time_restart_duration},
                format: 'MS',
                onTick: restartProcessCountdown,
                onExpiry: allowRestartProcess,
            });

            function allowRestartProcess() {
                $('#restart_process_link').show();
            }

            function restartProcessCountdown(periods) {
                if (periods[6] < 10) {
                    var seconds = '0' + periods[6];
                } else {
                    var seconds = periods[6];
                }

                $('#restart_process_countdown_monitor').text(periods[5] + ':' + seconds);
            }
SCRIPT;
        AddScriptOnReady($script);
        
        global $JS_CLOSE_WINDOW_SCRIPT;
        AddScript($JS_CLOSE_WINDOW_SCRIPT);
    }


}  // -------------- END CLASS --------------