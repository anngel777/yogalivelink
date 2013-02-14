<?php
class Profile_CustomerProfileSessions extends BaseClass
{
    public $Show_Query                          = false;    // TRUE = output the database queries ocurring on this page
    public $Show_Timezone_Conversion            = false;    // TRUE = output info on timezone conversions - DEV ONLY
    public $show_admin_datetime                 = false;    // TRUE = show the datetime info - DEV ONLY
    public $Echo_Records_Array                  = false;    // TRUE = show all the records - DEV ONLY
    public $Create_Testing_Session              = false;    // TRUE = will output a fake session in the "Today's Sessions" area - DEV ONLY
    
    public $session_cancel_within_time_hrs      = 2;        // Dont' allow cancelling if within X hrs of current time
    public $Time_Show_Launch_Button             = 7200;     // How soon before session should launch button be shown --> 120 mins = 7200 seconds
    
    public $show_sessions_today                 = true;     // TRUE = show sessions happening today
    public $show_sessions_future                = true;     // TRUE = show sessions hapenning in the future
    public $show_sessions_past                  = true;     // TRUE = show sessions that already happened
    public $show_session_id                     = false;    // TRUE = show the session id in the output list
    public $show_launch_session                 = true;     // TRUE = show option to launch session
    public $show_test_session                   = true;     // TRUE = show option to test session
    public $show_ical                           = true;     // TRUE = show option to get iCal file
    public $show_session_details                = true;     // TRUE = show option to view session details
    public $show_user_cancel_session            = true;     // TRUE = show option for user to cancel booked session
    public $show_instructor_rate_session        = true;     // TRUE = show option for user to rate past session
    public $show_instructor_upload_video        = true;     // TRUE = show option for user to upload a video of session
    public $show_intake_form                    = true;     // TRUE = show option for user to fill in intake form
    public $show_instructor_profile			    = true;     // TRUE = show option to view instructor profile
    public $show_user_rate_session              = true;     // TRUE = show if customer has rated the session
    public $Verify_Instructor_Video_Exists      = true;     // TRUE = check if instructor has uploaded video file for this session
    public $Format_Session_Customer_Not_Rated   = false;    // TRUE = Output session as a table
    
    public $Is_Instructor                       = false;    // TRUE - user is an instructor
    
    public $Btn_ScheduleSession_Class           = "buttonImg";                                              // button class
    public $Btn_ScheduleSession_Text            = '<img src="/images/buttons/btn_schedule_off.png">';       // button image
    
    
    public $picture_dir                         = '/office/';
    public $image_dir_instructor                = 'images/instructors/';
    public $image_no_pic                        = 'thumbnail_no_picture.jpg';
    
    public $table_sessions                      = 'sessions';
    public $table_session_checklists            = 'session_checklists';
    public $table_instructor_profile            = 'instructor_profile';
    
    
    // ---------- NON-MODIFIABLE VARIABLES ----------
    public $WH_ID                               = 0;
    public $OBJ_SESSION_CANCEL                  = null;
    public $OBJ_TIMEZONE                        = null;
    private $Random_Form_IDs                    = array();
    public $Have_Session_Today                  = false;    // TRUE = (if set) there is a session happening today
    public $Have_Session_Customer_Not_Rated     = false;    // TRUE = (if set) customer has not rated some sessions
    private $User_Rated_Current_Session         = false;    // TRUE = (if set) user has rated this session
    private $Instructor_Rated_Current_Session   = false;    // TRUE = (if set) instructor has rated this session
    public $sessions_array                      = array();
    public $sessions_today                      = '';
    public $sessions_future                     = '';
    public $sessions_past                       = '';
    public $sessions_next                       = '';
    public $sessions_testing                    = '';
    public $sessions_today_temp                 = array();
    public $sessions_future_temp                = array();
    public $Force_Show_Launch_Session           = false;    // ";launch" to force ON
    public $link_session_window                 = '';
    
    
    public function  __construct()
    {
        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Description' => 'Show a list of all the customer sessions - booked future and past',
        );
        
        $this->OBJ_SESSION_CANCEL   = new Sessions_CancelSignup();
        $this->OBJ_TIMEZONE         = new General_TimezoneConversion();
        
        
        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2010-10-27',
            'Updated By'  => 'Richard Witherspoon',
            'Updated'     => '2012-06-20',
            'Version'     => '1.1',
            'Description' => 'Create and manage session_checklists',
        );
        
        /* UPDATE LOG ======================================================================================
        
            2012-06-20  -> Modified TodaysSessions() for new button
        
        ====================================================================================== */
        
        
        /*
        $this->SetParameters(func_get_args());
        $this->Store_Products_Id = $this->GetParameter(0);
        if ($this->Store_Products_Id) {
            $this->AddDefaultWhere("`$this->Table`.`store_products_id`=$this->Store_Products_Id");
        }
        */

        $this->Table                = 'session_checklists';
        $this->Add_Submit_Name      = 'SESSION_CHECKLISTS_SUBMIT_ADD';
        $this->Edit_Submit_Name     = 'SESSION_CHECKLISTS_SUBMIT_EDIT';
        $this->Index_Name           = 'session_checklists_id';
        $this->Flash_Field          = 'session_checklists_id';
        $this->Default_Where        = '';  // additional search conditions
        $this->Default_Sort         = 'session_checklists_id';  // field for default table sort
        $this->Default_Fields       = '';
        $this->Unique_Fields        = '';
        $this->Joins = 
            "LEFT JOIN sessions ON sessions.sessions_id = session_checklists.sessions_id 
            LEFT JOIN instructor_profile ON instructor_profile.wh_id = sessions.instructor_id";
        

    
    
        $this->Field_Titles = array(
            
            'instructor_profile.gender'         => 'Instructor Gender',
            'instructor_profile.first_name'     => 'Instructor First Name',
            'instructor_profile.last_name'      => 'Instructor Last Name',
            'instructor_profile.wh_id'          => 'Instructor WHID',
            
            'sessions.date'                     => 'Session Date',
            'sessions.start_datetime'           => 'Session Start Time',
            'sessions.end_datetime'             => 'Session End Time',
            
            #'session_checklists.session_checklists_id'      => 'Session Checklists Id',
            #'session_checklists.sessions_id'                => 'Sessions Id',
            #'session_checklists.wh_id'                      => 'Wh Id',
            'session_checklists.paid'                       => 'Paid',
            'session_checklists.payment_id'                 => 'Payment Id',
            'session_checklists.email_booked_user_sent'             => 'Email Booked User Sent',
            #'session_checklists.email_booked_instructor_sent'       => 'Email Booked Instructor Sent',
            'session_checklists.email_reminder_1_user_sent'         => 'Email Reminder 1 User Sent',
            #'session_checklists.email_reminder_1_instructor_sent'   => 'Email Reminder 1 Instructor Sent',
            'session_checklists.email_reminder_2_user_sent'         => 'Email Reminder 2 User Sent',
            #'session_checklists.email_reminder_2_instructor_sent'   => 'Email Reminder 2 Instructor Sent',
            'session_checklists.cancelled'                          => 'Cancelled',
            'session_checklists.cacelled_reason'                    => 'Cacelled Reason',
            'session_checklists.cancelled_by_wh_id'                 => 'Cancelled By Wh Id',
            'session_checklists.email_cancelled_user_sent'          => 'Email Cancelled User Sent',
            #'session_checklists.email_cancelled_instructor_sent'    => 'Email Cancelled Instructor Sent',
            #'session_checklists.login_user'                         => 'Login User',
            #'session_checklists.login_user_datetime'                => 'Login User Datetime',
            #'session_checklists.login_instructor'                   => 'Login Instructor',
            #'session_checklists.login_instructor_datetime'          => 'Login Instructor Datetime',
            'session_checklists.session_started'                    => 'Session Started',
            'session_checklists.session_started_datetime'           => 'Session Started Datetime',
            'session_checklists.session_completed'                  => 'Session Completed',
            'session_checklists.session_completed_datetime'         => 'Session Completed Datetime',
            'session_checklists.rating_user'                        => 'Rating User',
            'session_checklists.rating_instructor'                  => 'Rating Instructor',
            #'session_checklists.instructor_video_uploaded'          => 'Instructor Video Uploaded',
            #'session_checklists.active'                             => 'Active',
            #'session_checklists.updated'                            => 'Updated',
            #'session_checklists.created'                            => 'Created'
        );


        if (Get('launch')) {
            $this->Force_Show_Launch_Session = true;
        }
        
    } // -------------- END __construct --------------


    public function SetFormArrays()
    {
        $base_array = array(
            "form|$this->Action_Link|post|db_edit_form",
            'text|Sessions Id|sessions_id|N|11|11',
            'text|Wh Id|wh_id|N|11|11',
            'checkbox|Paid|paid||1|0',
            'text|Payment Id|payment_id|N|11|11',
            'checkbox|Email Booked User Sent|email_booked_user_sent||1|0',
            'checkbox|Email Booked Instructor Sent|email_booked_instructor_sent||1|0',
            'checkbox|Email Reminder 1 User Sent|email_reminder_1_user_sent||1|0',
            'checkbox|Email Reminder 1 Instructor Sent|email_reminder_1_instructor_sent||1|0',
            'checkbox|Email Reminder 2 User Sent|email_reminder_2_user_sent||1|0',
            'checkbox|Email Reminder 2 Instructor Sent|email_reminder_2_instructor_sent||1|0',
            'checkbox|Cancelled|cancelled||1|0',
            'textarea|Cacelled Reason|cacelled_reason|N|60|4',
            'text|Cancelled By Wh Id|cancelled_by_wh_id|N|20|20',
            'checkbox|Email Cancelled User Sent|email_cancelled_user_sent||1|0',
            'checkbox|Email Cancelled Instructor Sent|email_cancelled_instructor_sent||1|0',
            'checkbox|Login User|login_user||1|0',
            'text|Login User Datetime|login_user_datetime|N||',
            'checkbox|Login Instructor|login_instructor||1|0',
            'text|Login Instructor Datetime|login_instructor_datetime|N||',
            'checkbox|Session Started|session_started||1|0',
            'text|Session Started Datetime|session_started_datetime|N||',
            'checkbox|Session Completed|session_completed||1|0',
            'text|Session Completed Datetime|session_completed_datetime|N||',
            'checkbox|Rating User|rating_user||1|0',
            'checkbox|Rating Instructor|rating_instructor||1|0',
            'checkbox|Instructor Video Uploaded|instructor_video_uploaded||1|0',
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

    public function AddScript() 
    {
    //$eq_TestChat  = EncryptQuery("class=Sessions_iCal;v1={$record['sessions_id']}");
    
        $script = "
            //function LaunchSession(eq) {
            //    //top.parent.appformCreate('Window', getClassExecuteLink(eq), 'apps'); return false;
            //    
            //    alert('code to LAUNCH session would go here');
            //}
            
            function LaunchSessionNewWindow(eq) {
                //top.parent.window.location = getClassExecuteLinkNoAjax(eq);
                var link = getClassExecuteLinkNoAjax(eq) + ';template=launch;pagetitle=Yoga Video Session';
                var width = 200; //880
                var height = 200; //570
                window.open(link,'blank','toolbar=no,width='+width+',height='+height+',location=no');
            }
            
            function LaunchSessionNewWindowHTTP(eq) {
                //top.parent.window.location = getClassExecuteLinkNoAjax(eq);
                var link = 'http://www.yogalivelink.com' + getClassExecuteLinkNoAjax(eq) + ';template=launch;pagetitle=Yoga Video Session';
                alert(link);
                var width = 200; //880
                var height = 200; //570
                window.open(link,'blank','toolbar=no,width='+width+',height='+height+',location=no');
            }
            
            //function CancelSession(eq) {
            //    top.parent.appformCreate('Window', getClassExecuteLink(eq), 'apps'); return false;
            //}
            
            function TestSession(eq) {
                alert('code to TEST LAUNCH session would go here');
            }
            
            
    function TestChat(eq) {
        //alert('code to TEST LAUNCH session would go here');
        top.parent.appformCreateOverlay('TESTING CHAT', '/office/chat/chat_user', 'apps'); return false;
    }
    
            function RateSession(eq) {
                //var eq = eq + ';dialogWidth=420';
                //top.parent.appformCreate('Rate Session', getClassExecuteLinkNoAjax(eq), 'apps'); return false;
                
                // <--- RAW - I've added this new function in wo/wo_all.js to start playing with overlays.
                top.parent.appformCreateOverlay('Rate Session', getClassExecuteLinkNoAjax(eq), 'apps'); return false;
                
                //overlayContent('Rate Session', getClassExecuteLink(eq));
                //return false;
        
            }
            function GetOutlookCalendar(eq) {
                top.parent.appformCreate('Window', getClassExecuteLink(eq), 'apps'); return false;
            }
            function ViewInstructorProfile(eq) {
				top.parent.appformCreateOverlay('Instructor Profile', getClassExecuteLinkNoAjax(eq), 'apps'); return false;
            }
            
            function RefreshWindow() {
                location.reload(true);
            }
            
            

            ";
        AddScript($script);
        
        
        
        # SCRIPT INCLUDES
        # ======================================================================
        //AddScriptInclude('/jslib/jquery-ui-1.8.9.custom.min.js');
        AddScriptInclude('/jslib/jquery.ui.stars.min.js');
    
    }

    public function GetAllSessions($WH_ID='')
    {
        if ($WH_ID) {
            $this->WH_ID = $WH_ID;
        }
        
        
        # GET ALL THE SESSIONS & CREATE OUTPUTS
        # ============================================
        $this->sessions_array = $this->GetSessions();
        
        //$this->RandomlyDateSessions();
        
        
        # SORT THE SESSIONS BY DATE
        # ============================================
        $array                  = $this->sessions_array;
        $on                     = 'utc_start_datetime'; //'date';
        $order                  = 'SORT_DESC';
        $this->sessions_array   = array_sort($array, $on, $order);
        
        
        foreach ($this->sessions_array as $session) {
            $data = $this->FormatSession($session);
        }
        
        
        
        // re-sort today's sessions and future sessions so they list dated from soonest to oldest
        # ============================================
        $this->sessions_today_temp = array_reverse($this->sessions_today_temp);
        foreach ($this->sessions_today_temp as $session) {
            $this->sessions_today .= $session;
        }
        
        $this->sessions_future_temp = array_reverse($this->sessions_future_temp);
        foreach ($this->sessions_future_temp as $session) {
            $this->sessions_future .= $session;
        }
        
        
        
        # OUTPUT ALL THE SESSIONS
        # ============================================
        $sessions_today     = ''; //($this->show_sessions_today && $this->sessions_today) ? "{$this->sessions_today}<br /><br />" : '';
        $sessions_future    = ($this->show_sessions_future && $this->sessions_future) ? "{$this->sessions_future}<br /><br />" : '<div class="red">YOU HAVE NO SCHEDULED SESSIONS</div>';
        $sessions_past      = ($this->show_sessions_past && $this->sessions_past) ? "{$this->sessions_past}<br /><br />" : '<div class="red">YOU HAVE NO PAST SESSIONS</div>';
        
        $output = "
            <div>
                <div class='col' style='width:50px;'>
                    &nbsp;
                </div>
                
                <div class='col' style='width:250px;'>
                    <div class='dark_green_text left_header'>scheduled sessions</div>
                    <br />
                    <div class='scheduled_sessions_list_wrapper'>
                        {$sessions_today}
                        {$sessions_future}
                    </div>
                </div>

                <div class='col' style='width:50px;'>
                    &nbsp;
                </div>
                
                <div class='col' style='width:250px;'>
                    <div class='dark_green_text left_header'>past sessions</div>
                    <br />
                    <div class='scheduled_sessions_list_wrapper'>
                        {$sessions_past}
                    </div>
                </div>
                
                <div class='clear'></div>
            </div>
            <br /><br />
            ";
        
        //$output .= '<br />==============<br />' . ArrayToStr($this->sessions_future);
        return $output;
    }
    
    
    
    
    public function GetAllSessionsNotRatedByCustomer($WH_ID='')
    {
        if ($WH_ID) {
            $this->WH_ID = $WH_ID;
        }
        
        
        # GET ALL THE SESSIONS & CREATE OUTPUTS
        # ============================================
        $where = "`session_ratings_user`.`session_ratings_user_id` IS NULL";
        $this->sessions_array = $this->GetSessions($where);
        
        //$this->RandomlyDateSessions();
        
        
        # SORT THE SESSIONS BY DATE
        # ============================================
        $array                  = $this->sessions_array;
        $on                     = 'utc_start_datetime'; //'date';
        $order                  = 'SORT_ASC';
        $this->sessions_array   = array_sort($array, $on, $order);
        
        if ($this->sessions_array) {
            $this->Have_Session_Customer_Not_Rated = true;
        }
        
        foreach ($this->sessions_array as $session) {
            $data = $this->FormatSession($session);
        }
        
        
        # OUTPUT ALL THE SESSIONS
        # ============================================
        $sessions_past      = ($this->show_sessions_past && $this->sessions_past) ? "{$this->sessions_past}<br /><br />" : '<div class="red">YOU HAVE NO PAST SESSIONS</div>';
        
        $output = "
            <div>

                <div class='col' style='width:500px;'>
                    <div class='scheduled_sessions_list_wrapper'>
                        {$sessions_past}
                    </div>
                </div>
                
                <div class='clear'></div>
            </div>
            <br /><br />
            ";
        
        //$output .= '<br />==============<br />' . ArrayToStr($this->sessions_future);
        return $output;
    }
    
    
    
    
    public function GetSessions($WHERE='')
    {
        $instructor_rating_keys = "
        session_ratings_instructor.overall_session              AS INS_overall_session, 
        session_ratings_instructor.technical_quality_video      AS INS_technical_quality_video, 
        session_ratings_instructor.technical_quality_audio      AS INS_technical_quality_audio
        ";
        
        $where_WHID = ($this->Is_Instructor) ? "$this->table_sessions.instructor_id=$this->WH_ID" : "$this->table_sessions.booked_wh_id=$this->WH_ID";
        $where_in   = ($WHERE) ? " AND $WHERE" : '';
    
        $records = $this->SQL->GetArrayAll(array(
            'table' => "$this->table_session_checklists",
            'keys'  => "$this->table_session_checklists.*, $this->table_sessions.*, $this->table_sessions.sessions_id AS SID, $this->table_instructor_profile.*, $this->table_session_checklists.wh_id AS customer_wh_id, session_ratings_user.*, $instructor_rating_keys",
            'where' => "$this->table_session_checklists.active=1 AND $where_WHID $where_in",
            'joins' => "
                LEFT JOIN $this->table_sessions ON $this->table_sessions.sessions_id = $this->table_session_checklists.sessions_id 
                LEFT JOIN $this->table_instructor_profile ON $this->table_instructor_profile.wh_id = $this->table_sessions.instructor_id 
                LEFT JOIN session_ratings_user ON session_ratings_user.sessions_id = $this->table_session_checklists.sessions_id 
                LEFT JOIN session_ratings_instructor ON session_ratings_instructor.sessions_id = $this->table_session_checklists.sessions_id 
                ",
            //'order' => "$this->table_sessions.utc_start_datetime ASC",
        ));
        if ($this->Show_Query) echo '<br />' . $this->SQL->Db_Last_Query;
        if ($this->Echo_Records_Array) echo '<br />' . ArrayToStr($records);
        
        #echo "<h1>GETTING ALL SESSIONS</h1>";
        
        return $records;
    }
    
    
    public function RandomlyDateSessions()
    {
        global $USER_LOCAL_TIMEZONE;
    
        $done_random_time = false;
        
        for ($i=0; $i<count($this->sessions_array); $i++) {
            $num = rand(1,3);
            switch ($num) {
                case 1:
                    # today
                    $date = date('Y-m-d');
                    
                    # PUT ONE EVENT INTO THE FUTURE - To TEST CANCEL ACTION
                    if (!$done_random_time) {
                        $time_base  = date('H00');
                        $time_start = $time_base + (($i)*100);
                        $time_end   = $time_base + (($i+1)*100);
                        
                        $time_start = str_pad($time_start, 4, "0", STR_PAD_LEFT);
                        $time_end   = str_pad($time_end, 4, "0", STR_PAD_LEFT);
                        
                        
                        $input_date_time        = "{$date} {$time_start}";
                        $input_timezone         = $USER_LOCAL_TIMEZONE;
                        $output_timezone        = 'UTC';
                        $output_format          = 'Y-m-d g:i a';
                        $time_start_utc         = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
                        
                        $input_date_time        = "{$date} {$time_end}";
                        $input_timezone         = $USER_LOCAL_TIMEZONE;
                        $output_timezone        = 'UTC';
                        $output_format          = 'Y-m-d g:i a';
                        $time_end_utc           = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
                        
                        $date_start = $time_start_utc;
                        $date_end = $time_end_utc;
                        #$this->sessions_array[$i]['utc_start_datetime']     = $time_start_utc;
                        #$this->sessions_array[$i]['utc_end_datetime']       = $time_end_utc;
                        $done_random_time                                   = true;
                        
                        #echo "<br />time_base ==> $time_base";
                        #echo "<br />time_start ==> $time_start";
                        #echo "<br />time_end ==> $time_end";
                    }
                break;
                case 2:
                    # past
                    #$date   = "2010-05-11";
                    $date_start = $this->sessions_array[$i]['utc_start_datetime'];
                    $date_end = $this->sessions_array[$i]['utc_end_datetime'];
                break;
                case 3:
                    # future
                    $date_start   = "2010-12-30 09:00:00";
                    $date_end   = "2010-12-30 10:00:00";
                break;
            }
            $this->sessions_array[$i]['utc_start_datetime'] = $date_start;
            $this->sessions_array[$i]['utc_end_datetime'] = $date_end;
        }
        #$i--;
        ## MAKE THE LAST ONE GO INTO THE PAST
        #$this->sessions_array[$i]['date'] = $this->sessions_array[$i]['date'];
    }
    
    public function CalculateCustomerRating($record)
    {
            # CREATE A TRULY RANDOM ID
            # =====================================
            do {
                $rnd        = rand(1,9999);
                $form_id    = "star_form_{$record['sessions_id']}_{$rnd}_customer";
            } while (in_array($form_id, $this->Random_Form_IDs));
            $this->Random_Form_IDs[] = $form_id;
            
            
            # CREATE THE SCORE
            # =====================================
            $max_val    = 5;
            $total      = 0;
            $count      = 0;
            
            if ($record['instructor_overall'] > 0)          { $total += $record['instructor_overall']; $count++; }
            if ($record['instructor_expectations'] > 0)     { $total += $record['instructor_expectations']; $count++; }
            //if ($record['system_ease'] > 0)                 { $total += $record['system_ease']; $count++; }
            if ($record['technical_quality_video'] > 0)     { $total += $record['technical_quality_video']; $count++; }
            if ($record['technical_quality_audio'] > 0)     { $total += $record['technical_quality_audio']; $count++; }
            
            $score      = ($count > 0) ? round (($total / ($max_val * $count)) * $max_val) : 0;
            $user_rated = ($score > 0) ? true : false;
            
            /*
            echo "<br />instructor_skill {$record['instructor_skill']}";
            echo "<br />instructor_knowledge {$record['instructor_knowledge']}";
            echo "<br />technical_ease {$record['technical_ease']}";
            echo "<br />technical_quality_video {$record['technical_quality_video']}";
            echo "<br />technical_quality_audio {$record['technical_quality_audio']}";
            echo "<br /><b>rating ====> {$score}</b>";
            echo "<br /><br />";
            */
            
            
            # CREATE THE STAR RATING FORM
            # =====================================
            $output = "rated session [<b>{$score}</b> of <b>{$max_val}</b>] <div id='{$form_id}'>";
            for ($i=1; $i<6; $i++) {
                $checked = ($score == $i) ? "checked='checked'" : '';
                $output .= "<input type='radio' star='star' name='rate_avg_{$form_id}' value='{$i}' title='{$i}' disabled='disabled' {$checked} />";
            }
            $output .= "</div><br />";
            
            $script = "$(\"#{$form_id}\").stars();";
            AddScriptOnReady($script);
            
            return $output;
    }
    
    public function CalculateInstructorRating($record)
    {
            # CREATE A TRULY RANDOM ID
            # =====================================
            do {
                $rnd        = rand(1,9999);
                $form_id    = "star_form_{$record['sessions_id']}_{$rnd}_instructor";
            } while (in_array($form_id, $this->Random_Form_IDs));
            $this->Random_Form_IDs[] = $form_id;
            
            # CREATE THE SCORE
            # =====================================
            $max_val    = 5;
            $total      = 0;
            $count      = 0;
            
            if ($record['INS_overall_session'] > 0)         { $total += $record['INS_overall_session']; $count++; }
            if ($record['INS_technical_quality_video'] > 0) { $total += $record['INS_technical_quality_video']; $count++; }
            if ($record['INS_technical_quality_audio'] > 0) { $total += $record['INS_technical_quality_audio']; $count++; }
            
            $score      = ($count > 0) ? round (($total / ($max_val * $count)) * $max_val) : 0;
            $user_rated = ($score > 0) ? true : false;
            
            /*
            echo "<br />user_skill {$record['user_skill']}";
            echo "<br />user_knowledge {$record['user_knowledge']}";
            echo "<br />technical_ease {$record['technical_ease']}";
            echo "<br />technical_quality_video {$record['technical_quality_video']}";
            echo "<br />technical_quality_audio {$record['technical_quality_audio']}";
            echo "<br /><b>rating ====> {$score}</b>";
            echo "<br /><br />";
            */
            
            
            # CREATE THE STAR RATING FORM
            # =====================================
            $output = "rated session [<b>{$score}</b> of <b>{$max_val}</b>] <div id='{$form_id}'>";
            for ($i=1; $i<6; $i++) {
                $checked = ($score == $i) ? "checked='checked'" : '';
                $output .= "<input type='radio' star='star' name='rate_avg_{$form_id}' value='{$i}' title='{$i}' disabled='disabled' {$checked} />";
            }
            $output .= "</div><br />";
            
            $script = "$(\"#{$form_id}\").stars();";
            AddScriptOnReady($script);
            
            return $output;
    }
    
    public function CalculateActions($record)
    {
    
        $record['customer_wh_id'] = (isset($record['customer_wh_id'])) ? $record['customer_wh_id'] : 0;
    
        $today_date     = date('Y-m-d');
        $user_type      = $_SESSION['USER_TYPE']; //'customer';
        
        
        
        # FORMAT DATE & TIME - to local for user
        # ============================
        $input_date_time        = $record['utc_start_datetime'];
        $input_timezone         = 'UTC';
        $output_timezone        = $GLOBALS['USER_LOCAL_TIMEZONE'];
        $output_format          = 'Y-m-d g:i a';
        $converted_datetime     = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
        $parts                  = explode(' ', $converted_datetime);
        $date_start_local       = $parts[0];
        $time_start_local       = $parts[1];
        $date_time_start_local  = $converted_datetime;
        
        $input_date_time        = $record['utc_end_datetime'];
        $input_timezone         = 'UTC';
        $output_timezone        = $GLOBALS['USER_LOCAL_TIMEZONE'];
        $output_format          = 'Y-m-d g:i a';
        $converted_datetime     = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
        $parts                  = explode(' ', $converted_datetime);
        $date_end_local         = $parts[0];
        $time_end_local         = $parts[1];
        $date_time_end_local    = $converted_datetime;
        
        ###$session_past = ($date_time_end_local < date('Y-m-d g:i a')) ? true : false;
        
        
        
        # determine if session passed
        # =======================================================
        date_default_timezone_set($GLOBALS['USER_LOCAL_TIMEZONE']);
        $now_unix           = strtotime(date('Y-m-d g:i a'));
        $session_end_unix   = strtotime($date_time_end_local);
        $session_past       = ($session_end_unix < $now_unix) ? true : false;
        
        
        # see if session can be cancelled
        # =======================================================
        $this->OBJ_SESSION_CANCEL->Session_Record = $record;
        $cancellable = ($date_start_local < $today_date) ? false : $this->OBJ_SESSION_CANCEL->CheckIfSessionCanBeCancelled();
        
        
        # see if user has rated this session already
        # =======================================================
        $user_rated         = ($record['rating_user'] == 0 || $record['rating_user'] == '0' || $record['rating_user'] == '' || $record['rating_user'] == null) ? false : true;
        $instructor_rated   = ($record['rating_instructor'] == 0 || $record['rating_instructor'] == '0' || $record['rating_instructor'] == '' || $record['rating_instructor'] == null) ? false : true;
        
        $this->User_Rated_Current_Session           = $user_rated;
        $this->Instructor_Rated_Current_Session     = $instructor_rated;
        
        $session_rating_instructor_val  = ($this->Is_Instructor) ? $this->CalculateInstructorRating($record) : 0;
        $session_rating_customer_val    = $this->CalculateCustomerRating($record);
        
        
        # setup the encrypted queries
        # =======================================================
        #$eq_TestSession             = EncryptQuery("class=Sessions_TestLogin;v1={$record['sessions_id']}");
        if ($this->Is_Instructor) {
            $eq_LaunchSession           = EncryptQuery("class=Sessions_Launch;v1={$record['sessions_id']};v2={$record['instructor_id']};v3=instructor");
            
            $test_sessions_id           = time() . '-T';
            $eq_TestSession             = EncryptQuery("class=Sessions_Launch;v1={$test_sessions_id};v2={$record['instructor_id']};v3=instructor;v4=testing");
        } else {
            $eq_LaunchSession           = EncryptQuery("class=Sessions_Launch;v1={$record['sessions_id']};v2={$record['customer_wh_id']};v3=;");
            
            $test_sessions_id           = time() . '-T';
            $eq_TestSession             = EncryptQuery("class=Sessions_Launch;v1={$test_sessions_id};v2={$record['instructor_id']};v3=;v4=testing");
        }
        
        $eq_CancelSession               = EncryptQuery("class=Sessions_CancelSignup;v1={$record['sessions_id']};v3={$user_type}");
        $eq_RateSessionCustomer         = EncryptQuery("class=Sessions_RatingsUser;v1={$record['sessions_id']};v2={$record['customer_wh_id']};v3={$record['instructor_id']}");
        $eq_RateSessionInstructor       = EncryptQuery("class=Sessions_RatingsInstructor;v1={$record['sessions_id']};v2={$record['customer_wh_id']};v3={$record['instructor_id']}");
        $eq_Ical                        = EncryptQuery("class=Sessions_iCal;v1={$record['sessions_id']}");
        $eq_ViewInstructor  	        = EncryptQuery("class=InstructorProfile_View;v1={$record['instructor_id']}");
        $eq_UploadVideo                 = EncryptQuery("class=Sessions_UploadVideo;v1={$record['sessions_id']}");
        $eq_SessionDetails              = EncryptQuery("class=Sessions_Details;v1={$record['sessions_id']}");
        $eq_IntakeTherapyForm           = EncryptQuery("class=Profile_FormTherapyIntake;v1={$record['sessions_id']};v2={$record['customer_wh_id']};v3=true");
        $eq_IntakeStandardForm          = EncryptQuery("class=Profile_FormStandardIntake;v1={$record['sessions_id']};v2={$record['customer_wh_id']};v3=true");

        
        # setup the actions
        # =======================================================
        $actions = "";
        
        // Session Details - INSTRUCTOR
        $actions .= ($this->Is_Instructor && $this->show_session_details) ? "<div><a href='#' class='link_arrow' onclick=\"top.parent.appformCreateOverlay('Session Details', getClassExecuteLinkNoAjax('{$eq_SessionDetails}'), 'apps'); return false;\">VIEW SESSION DETAILS</a></div>" : '';
        
        
        // Intake Forms - INSTRUCTOR
        $actions .= ($this->Is_Instructor && !$session_past && $record['type_standard'] == 1 && $this->show_intake_form) ? "<div><a href='#' class='link_arrow' onclick=\"top.parent.appformCreateOverlay('YOGA: FITNESS FORM', getClassExecuteLinkNoAjax('{$eq_IntakeStandardForm}'), 'apps'); return false;\">YOGA: FITNESS FORM</a></div>" : '';
        $actions .= ($this->Is_Instructor && !$session_past && $record['type_therapy'] == 1 && $this->show_intake_form) ? "<div><a href='#' class='link_arrow' onclick=\"top.parent.appformCreateOverlay('YOGA THERAPY: HEALTH FORM', getClassExecuteLinkNoAjax('{$eq_IntakeTherapyForm}'), 'apps'); return false;\">YOGA THERAPY: HEALTH FORM</a></div>" : '';
        
        
        // Launch Session
        $actions .= ((!$session_past && $this->show_launch_session) || $this->Force_Show_Launch_Session) ? "<div><a href='#' class='link_arrow' onclick=\"LaunchSessionNewWindow('{$eq_LaunchSession}');\">BEGIN SESSION</a></div>" : '';
        
        
        // Test Session
        //$actions .= (!$session_past && $this->show_test_session) ? "<div><a href='#' class='link_arrow' onclick=\"LaunchSessionNewWindow('{$eq_TestSession}');\">TEST SESSION</a></div>" : '';
        
        // Add to Outlook Calendar
        $actions .= (!$session_past && $this->show_ical)         ? "<div><a href='#' class='link_arrow' onclick=\"top.parent.appformCreateOverlay('Download iCal File', getClassExecuteLinkNoAjax('{$eq_Ical}'), 'apps'); return false;\">ADD TO OUTLOOK CALENDAR</a></div>" : '';
        
        // Instructor Profile - CUSTOMER
        $actions .= ($this->show_instructor_profile && !$this->Is_Instructor)                                           ? "<div><a href='#' class='link_arrow' onclick=\"top.parent.appformCreateOverlay('View Instructor Profile', getClassExecuteLinkNoAjax('{$eq_ViewInstructor}'), 'apps'); return false;\">VIEW INSTRUCTOR PROFILE</a></div>" : '';
        
        // Cancel session
        $actions .= (($cancellable) && $this->show_user_cancel_session)                                                 ? "<div><a href='#' class='link_arrow' onclick=\"top.parent.appformCreateOverlay('Cancel Session', getClassExecuteLinkNoAjax('{$eq_CancelSession}'), 'apps'); return false;\">CANCEL SESSION</a></div>" : '';
        
        
        // Upload Video - INSTRUCTOR
        if ($this->Is_Instructor) {
            $instructor_video_uploaded = $record['instructor_video_uploaded'];
            if ($instructor_video_uploaded && $this->Verify_Instructor_Video_Exists) {
                # verify that video IS actually on server
                $OBJ_VIDEO                  = new Sessions_UploadVideo($record['sessions_id']);
                $result                     = $OBJ_VIDEO->CheckIfFileExistsFromSessionID();
                $instructor_video_uploaded  = ($result) ? true : false;
            }
            $actions .= ($this->show_instructor_upload_video && !$instructor_video_uploaded && $session_past)                            ? "<div><a href='#' class='link_arrow' onclick=\"top.parent.appformCreateOverlay('Upload Session Audio', getClassExecuteLinkNoAjax('{$eq_UploadVideo}'), 'apps'); return false;\">UPLOAD SESSION AUDIO</a></div>" : '';
        }
        
        // Rate Session - CUSTOMER || INSTRUCTOR
        if ($this->Is_Instructor) {
            if ($session_past) {
                $rating_style = "padding:5px; background-color:#ddd; border:1px solid #bbb;"; //#d4d0c8
                $actions .= (!$instructor_rated && $this->show_instructor_rate_session)    ? "<div><a href='#' class='link_arrow' onclick=\"RateSession('{$eq_RateSessionInstructor}')\">RATE SESSION (INSTRUCTOR)</a></div>" : "<div style='$rating_style'>Instructor: {$session_rating_customer_val}</div>";
                $actions .= ($user_rated && $this->show_user_rate_session)                                                      ? "<div style='$rating_style'>User Rating: {$session_rating_customer_val}</div>" : '<br />User has NOT rated session yet.';
                //$actions .= ($instructor_rated && $this->show_instructor_rate_session)                                          ? "{$session_rating_instructor_val}<br />" : '';
            }
        } else {
            $actions .= ($session_past && !$user_rated && $this->show_user_rate_session)            ? "<div><a href='#' class='link_arrow' onclick=\"RateSession('{$eq_RateSessionCustomer}')\">RATE SESSION</a></div>" : '';
            $actions .= ($user_rated && $this->show_user_rate_session)                                                  ? "{$session_rating_customer_val}<br />" : '';
        }
        
        # return the actions
        # =======================================================
        return $actions;
    }
    
    public function FormatSession($record, $testing=false)
    {
        $record['sessions_id'] = $record['SID'];
        
        if ($record['cancelled'] == 0) {

            if ($this->Show_Timezone_Conversion) {
                echo "<br /><hr><br />";            
                echo "<br />SESSIONS ID ==> {$record['sessions_id']}";
            }
        
            # FORMAT DATE & TIME - to local for user
            # ============================
            $input_date_time        = $record['utc_start_datetime'];
            $input_timezone         = 'UTC';
            $output_timezone        = $GLOBALS['USER_LOCAL_TIMEZONE'];
            $output_format          = 'Y-m-d g:i a';
            $converted_datetime     = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format, $this->Show_Timezone_Conversion);
            $parts                  = explode(' ', $converted_datetime);
            $date_start_local       = $parts[0];
            $time_start_local       = $parts[1];
            $time_start_local_ampm  = $parts[2];
            $date_time_start_local  = $converted_datetime;
            
            if ($this->Show_Timezone_Conversion) {
                echo "<br />utc_start_datetime ==> {$record['utc_start_datetime']}";
                echo "<br />start ==> $converted_datetime";
            }
            
            $input_date_time        = $record['utc_end_datetime'];
            $input_timezone         = 'UTC';
            $output_timezone        = $GLOBALS['USER_LOCAL_TIMEZONE'];
            $output_format          = 'Y-m-d g:i a';
            $converted_datetime     = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format, $this->Show_Timezone_Conversion);
            $parts                  = explode(' ', $converted_datetime);
            $date_end_local         = $parts[0];
            $time_end_local         = $parts[1];
            $time_end_local_ampm    = $parts[2];
            $date_time_end_local    = $converted_datetime;

            if ($this->Show_Timezone_Conversion) {
                echo "<br />utc_end_datetime ==> {$record['utc_end_datetime']}";
                echo "<br />end ==> $converted_datetime";
            }
            
            
            # Create the date display
            # ============================
            $input_date_time        = $record['utc_start_datetime'];
            $input_timezone         = 'UTC';
            $output_timezone        = $GLOBALS['USER_LOCAL_TIMEZONE'];
            $output_format          = 'M jS, Y';
            $date_start_local_formatted     = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
            
            
            # determine if session completed
            # =======================================================
            $session_has_closed     = ($record['session_completed'] == 1) ? true : false;
            $session_has_closed     = ($this->User_Rated_Current_Session) ? true : $session_has_closed;
            
            
            # determine if session passed
            # =======================================================
            date_default_timezone_set($GLOBALS['USER_LOCAL_TIMEZONE']);
            $now_unix           = strtotime(date('Y-m-d g:i a'));
            $session_end_unix   = strtotime($date_time_end_local);
            $session_past       = ($session_end_unix < $now_unix) ? true : false;
            
            
            # determine if launch button should be shown
            # =======================================================
            $session_start_unix = strtotime($date_time_start_local);
            $time_diff          = $session_start_unix - $now_unix;
            $show_launch_button = (!$session_past && $time_diff < $this->Time_Show_Launch_Button) ? true : false;
            $session_upcoming   = ($time_diff < $this->Time_Show_Launch_Button && $time_diff > 0) ? true : false;
            
            
            # get today's date
            # =======================================================
            date_default_timezone_set($GLOBALS['USER_LOCAL_TIMEZONE']);
            $today_date     = date('Y-m-d');
            
            
            # SETUP ACTIONS
            # ============================
            $actions = $this->CalculateActions($record);
            
            
            # setup the encrypted queries
            # =======================================================
            if ($this->Is_Instructor) {
                //$eq_LaunchSession           = EncryptQuery("class=Sessions_ProcessInstructor;v1={$record['session_checklists_id']};v2={$record['instructor_id']}");
                $eq_LaunchSession           = EncryptQuery("class=Sessions_Launch;v1={$record['sessions_id']};v2={$record['instructor_id']};v3=instructor");
            } else {
                //$eq_LaunchSession           = EncryptQuery("class=Sessions_ProcessUser;v1={$record['session_checklists_id']};v2={$record['customer_wh_id']}");
                $eq_LaunchSession           = EncryptQuery("class=Sessions_Launch;v1={$record['sessions_id']};v2={$record['customer_wh_id']};v3=;");
            }
            #$launch_button			= (($date_start_local == $today_date) && $this->show_launch_session) ? MakeButton('regular', 'BEGIN SESSION', '', '', '', "LaunchSessionNewWindow('{$eq_LaunchSession}')") : '';
            $launch_button          = (!$session_past && $show_launch_button && $this->show_launch_session) ? "<a href='#' onclick=\"LaunchSessionNewWindow('{$eq_LaunchSession}');\"><div class='btn_beginSession'>&nbsp; </div></a>" : '';
            
            
            $admin_datetime = "
                <br /><br />
                <b>Stored UTC Date/Time</b> <br />
                <div>{$record['utc_start_datetime']} - {$record['utc_end_datetime']}</div>
                ";
            $admin_datetime = ($this->show_admin_datetime) ? $admin_datetime : '';
            
            $user_datetime = "
                <div class='lowercase' style='font-weight:bold; font-size:13px; color:#AA1149;'>{$date_start_local_formatted}</div>
                <div style='line-height: 12px;'>
                    <div><b>{$time_start_local} {$time_start_local_ampm} - {$time_end_local} {$time_end_local_ampm}</b></div>
                    <div style='font-size:11px;'>({$GLOBALS['USER_LOCAL_TIMEZONE_DISPLAY']})</div>
                </div>
                ";
            
            
            
            # CREATE THE OUTPUT
            # ============================
            $actions        = ($actions) ? "<br /><div>{$actions}</div>" : '';
            $launch_button  = ($launch_button || $testing) ? "<br /><div>{$launch_button}</div>" : '';
            $id_style       = ($this->show_session_id) ? '' : 'display:none;';
            $style_wrap     = ($session_past || $session_has_closed) ? "style='background-color:#eee;'" : "";            
            $class          = '';
            
            if ($this->Format_Session_Customer_Not_Rated) {
                $output = "
                <div class='customer_session_outter_wrapper {$class}' {$style_wrap}>
                <div class='customer_session_inner_wrapper left_content' {$style_wrap}>
                    <table cellpadding='0' cellspacing='0' width='100%'>
                    <tr>
                    <td>
                        <div style='{$id_style}'>sessions_id ==> {$record['sessions_id']}</div>
                        <div>{$admin_datetime}</div>
                        <div>{$user_datetime}</div>
                    </td>
                    <td>
                        {$actions}
                        <center>{$launch_button}</center>
                    </td>
                    </tr>
                    </table>
                </div>
                </div>";
            
            } else {
                $output = "
                <div class='customer_session_outter_wrapper {$class}' {$style_wrap}>
                <div class='customer_session_inner_wrapper left_content' {$style_wrap}>
                    <div style='{$id_style}'>sessions_id ==> {$record['sessions_id']}</div>
                    <div>{$admin_datetime}</div>
                    <div>{$user_datetime}</div>
                    {$actions}
                    <center>{$launch_button}</center>
                </div>
                </div>";
            }
            
            
            # STORE THE OUTPUT
            # ============================
            if ($testing) {
                $this->sessions_testing         = $output;
            } else {
            
                if ($session_upcoming && !$session_past && !$session_has_closed && !$this->Have_Session_Today) {
                    $this->sessions_next            = $output;
                    $this->Have_Session_Today       = true;
                }
                
                $this->sessions_today_temp[]    = (($date_start_local == $today_date) && !$session_has_closed && !$session_past) ? $output : '';
                $this->sessions_future_temp[]   = (!$session_past) ? $output : '';
                $this->sessions_past           .= ($session_past) ? $output : '';       //  || $session_has_closed
            }
            
            
        } //end checking if cancelled
    }

    public function Execute()
    {
        $output = $this->GetAllSessions();
        return $output;
    }
    
    public function TodaysSessions()
    {
        global $USER_LOCAL_TIMEZONE_DISPLAY, $USER_LOCAL_TIMEZONE;
        
    
        $have_sessions      = $this->Have_Session_Today;
        $sessions           = ($have_sessions) ? "<br /><br /><div class='red left_header'>today's next session</div><div style='text-align:left;'>{$this->sessions_next}</div>" : '';
        
        
        if ($this->Create_Testing_Session && !$have_sessions) {
            $this->FormatSession(array(
                'SID'                       => 666,
                'sessions_id'               => 666,
                'cancelled'                 => 0,
                'date'                      => date("Y-m-d 13:00:00"),
                'start_datetime'            => date("Y-m-d 13:00:00"),
                'utc_start_datetime'        => date("Y-m-d 13:00:00"),
                'utc_end_datetime'          => date("Y-m-d 14:00:00"),
                'primary_pictures_id'       => 666,
                'first_name'                => 'Test',
                'last_name'                 => 'Customer',
                'instructor_id'             => 666,
                'rating_user'               => 0,
                'rating_instructor'         => 0,
                'session_checklists_id'     => 0,
                'customer_wh_id'                => 666,
                'session_completed'         => 0,
                'instructor_skill'          => 0,
                'instructor_knowledge'      => 0,
                'technical_ease'            => 0,
                'technical_quality_video'   => 0,
                'technical_quality_audio'   => 0,
            ), true);
            
            $sessions = $this->sessions_testing;
        }
        
        
        
        $link               = ($this->Is_Instructor) ? "<a class='link_arrow' href='{$GLOBALS['PAGE_instructor_all_sessions']}' class='link_arrow'>SEE ALL YOUR SESSIONS</a>" : "<a href='{$GLOBALS['PAGE_customer_all_sessions']}' class='link_arrow'>SEE ALL YOUR SESSIONS</a>";
        //$btn_session        = ($this->Is_Instructor) ? '' : "<a href='{$GLOBALS['LINK_SESSION_SIGNUP']}'><div class='btn_scheduleASession'>&nbsp;</div></a>";
        $btn_session        = ($this->Is_Instructor) ? '' : "<center><a href='{$GLOBALS['LINK_SESSION_SIGNUP']}'><div class='{$this->Btn_ScheduleSession_Class}'>{$this->Btn_ScheduleSession_Text}</div></a></center>";
        
        
        date_default_timezone_set($USER_LOCAL_TIMEZONE);
        $today              = date("l, M j, Y");
        $timezone           = $_SESSION['USER_LOGIN']['LOGIN_RECORD']['tz_display'];
        
        
        // Test Computer
        $test_sessions_id   = time() . '-T';
        $eq_TestSession     = EncryptQuery("class=Sessions_Launch;v1={$test_sessions_id};v2=666;v3=;v4=testing");
        
        // Intake Form        
        $temp_sessions_id       = 0;
        //$eq_IntakeTherapyForm   = EncryptQuery("class=Profile_FormTherapyIntake;v1={$temp_sessions_id};v2={$this->WH_ID};v3=true");
        //$eq_IntakeStandardForm  = EncryptQuery("class=Profile_FormStandardIntake;v1={$temp_sessions_id};v2={$this->WH_ID};v3=true");
        $eq_IntakeStandardForm   = EncryptQuery("class=Profile_FormStandardIntake;v1=;v2=$this->WH_ID");
        $eq_IntakeTherapyForm    = EncryptQuery("class=Profile_FormTherapyIntake;v1=;v2=$this->WH_ID");
        
        $output = "
            <div style='text-align:center;'>
                <br />
                <div class='red left_header lowercase'>{$today}</div>
                <div class='red left_content'>your timezone setting: {$timezone}</div>
                <div class='black left_content'>{$sessions}</div>
                <br /><br />
                <div><center>{$btn_session}</center></div>
                
                <br /><br /><br />
                <div style='float:left; text-align:left;'>
                <div class='left_content'>{$link}</div>
                <div class='left_content'><a href='#' class='link_arrow' onclick=\"LaunchSessionNewWindow('{$eq_TestSession}');\">TEST YOUR COMPUTER SYSTEM</a></div>
                ";
        $output .= (!$this->Is_Instructor) ? " <div class='left_content'><a href='#' class='link_arrow' onclick=\"top.parent.appformCreateOverlay('YOGA: FITNESS FORM', getClassExecuteLinkNoAjax('{$eq_IntakeStandardForm}'), 'apps'); return false;\">YOGA: FITNESS FORM</a></div>" : '';
        $output .= (!$this->Is_Instructor) ? "<div class='left_content'><a href='#' class='link_arrow' onclick=\"top.parent.appformCreateOverlay('YOGA THERAPY: HEALTH FORM', getClassExecuteLinkNoAjax('{$eq_IntakeTherapyForm}'), 'apps'); return false;\">YOGA THERAPY: HEALTH FORM</a></div>" : '';
        
        $output .= "
                </div>
            </div>
            ";
        return $output;
    }
    
    
}  // -------------- END CLASS --------------