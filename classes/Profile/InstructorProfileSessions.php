<?php
class Profile_InstructorProfileSessions extends BaseClass
{
    public $Show_Query                      = false;   // TRUE = output the database queries ocurring on this page
    
    public $session_cancel_within_time_hrs  = 2;        // Dont' allow cancelling if within X hrs of current time
    
    public $show_instructor_profile			= true;     // TRUE = show option to view instructor profile
    public $show_launch_session             = true;     // TRUE = show option to launch session
    public $show_test_session               = false;    // TRUE = show option to test session
    public $show_ical                       = true;     // TRUE = show option to get iCal file
    public $show_user_rate_session          = true;     // TRUE = show option for user to rate past session
    public $show_user_cancel_session        = true;     // TRUE = show option for user to cancel booked session
    
    public $show_sessions_today             = true;     // TRUE = show sessions happening today
    public $show_sessions_future            = true;     // TRUE = show sessions hapenning in the future
    public $show_sessions_past              = true;     // TRUE = show sessions that already happened
	
    public $picture_dir                     = '/office/';
    public $image_dir_instructor            = 'images/instructors/';
    public $image_no_pic                    = 'thumbnail_no_picture.jpg';
    
    public $table_sessions                  = 'sessions';
    public $table_session_checklists        = 'session_checklists';
    public $table_instructor_profile        = 'instructor_profile';
    
    
    // ---------- NON-MODIFIABLE VARIABLES ----------
    public $WH_ID                           = 0;
    public $link_session_window             = '';
    public $sessions_array                  = array();
    public $sessions_today                  = '';
    public $sessions_future                 = '';
    public $sessions_past                   = '';
    public $OBJ_SESSION_CANCEL              = null;
    public $OBJ_TIMEZONE                    = null;
    
    
    public function  __construct()
    {
        parent::__construct();
        
        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Description' => 'Show a list of all the instructor sessions - booked future and past',
        );
        
        $this->OBJ_SESSION_CANCEL   = new Sessions_CancelSignup();
        $this->OBJ_TIMEZONE         = new General_TimezoneConversion();
        
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
            function LaunchSession(eq) {
                //top.parent.appformCreate('Window', getClassExecuteLink(eq), 'apps'); return false;
                
                alert('code to LAUNCH session would go here');
            }
            
            function LaunchSessionNewWindow(eq) {
                //top.parent.window.location = getClassExecuteLinkNoAjax(eq);
                var link = getClassExecuteLinkNoAjax(eq) + ';template=launch';
                var width = 880;
                var height = 570;
                window.open(link,'blank','toolbar=no,width='+width+',height='+height+',location=no');
            }
            
            function CancelSession(eq) {
                top.parent.appformCreate('Window', getClassExecuteLink(eq), 'apps'); return false;
            }
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
                //top.parent.appformCreate('View Instructor Profile', 'instructor_profile/instructor_profile_view;wh_id='+code,'apps');
				top.parent.appformCreateOverlay('Instructor Profile', getClassExecuteLinkNoAjax(eq), 'apps'); return false;
            }
            
            
            

            ";
        AddScript($script);
    }

    public function GetAllSessions($WH_ID='')
    {
        if ($WH_ID) {
            $this->WH_ID = $WH_ID;
        }
        
        
        # GET ALL THE SESSIONS & CREATE OUTPUTS
        # ============================================
        $this->sessions_array = $this->GetSessions();
        
        $this->RandomlyDateSessions();
        
        
        # SORT THE SESSIONS BY DATE
        # ============================================
        $array                  = $this->sessions_array;
        $on                     = 'utc_start_datetime'; //'date';
        $order                  = 'SORT_ASC';
        $this->sessions_array   = array_sort($array, $on, $order);
        
        foreach ($this->sessions_array as $session) {
            $data = $this->FormatSession($session);
        }
        
        
        # OUTPUT ALL THE SESSIONS
        # ============================================
        $output = "";
        $output .= ($this->show_sessions_today) ? "<br /><div class='sessions_header'>TODAY'S SESSIONS</div><br />{$this->sessions_today}<br /><br />" : '';
        $output .= ($this->show_sessions_future) ? "<br /><div class='sessions_header'>FUTURE SESSIONS</div><br />{$this->sessions_future}<br /><br />" : '';
        $output .= ($this->show_sessions_past) ? "<br /><div class='sessions_header'>PAST SESSIONS</div><br />{$this->sessions_past}<br /><br />" : '';
        

        
        
        return $output;
    }
    
    
    public function GetSessions()
    {
        $records = $this->SQL->GetArrayAll(array(
            'table' => "$this->table_session_checklists",
            'keys'  => "$this->table_session_checklists.*, $this->table_sessions.*, $this->table_instructor_profile.*, $this->table_session_checklists.wh_id AS user_wh_id",
            //'where' => "`wh_id`=$this->WH_ID AND $this->table_chats.active=1",
            'where' => "$this->table_session_checklists.active=1",
            'joins' => "
                LEFT JOIN $this->table_sessions ON $this->table_sessions.sessions_id = $this->table_session_checklists.sessions_id 
                LEFT JOIN $this->table_instructor_profile ON $this->table_instructor_profile.wh_id = $this->table_sessions.instructor_id
                ",
        ));
        if ($this->Show_Query) echo '<br />' . $this->SQL->Db_Last_Query;
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
    
    
    public function FormatSession($record)
    {
        global $USER_LOCAL_TIMEZONE;
    
        if ($record['cancelled'] == 0) {
        
        $today_date = date('Y-m-d');
        
        
        # FORMAT DATE & TIME - to local for user
        # ============================
        $input_date_time        = $record['utc_start_datetime'];
        $input_timezone         = 'UTC';
        $output_timezone        = $USER_LOCAL_TIMEZONE;
        $output_format          = 'Y-m-d g:i a';
        $converted_datetime     = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
        $parts                  = explode(' ', $converted_datetime);
        $date_start_local       = $parts[0];
        $time_start_local       = $parts[1];
        
        $input_date_time        = $record['utc_end_datetime'];
        $input_timezone         = 'UTC';
        $output_timezone        = $USER_LOCAL_TIMEZONE;
        $output_format          = 'Y-m-d g:i a';
        $converted_datetime     = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
        $parts                  = explode(' ', $converted_datetime);
        $date_end_local         = $parts[0];
        $time_end_local         = $parts[1];  
        
        
        # FORMAT DATE
        # ============================
        $date       = explode('-', $date_start_local);
        $y          = $date[0];
        $m          = $date[1];
        $d          = $date[2];
        $m_display  = date("M", mktime(0, 0, 0, $m+1, 0, 0));
        
        
        # FORMAT TIME - from database - in its stored timezone
        # ============================
        $time_start     = date("g:i a", strtotime("{$date_start_local} {$time_start_local}"));
        $time_end       = date("g:i a", strtotime("{$date_end_local} {$time_end_local}"));
        
        
        # SETUP INSTRUCTOR
        # ============================
        $instructor_picture = ($record['primary_pictures_id']) ? "{$this->picture_dir}{$record['primary_pictures_id']}" : "{$this->picture_dir}{$this->image_dir_instructor}{$this->image_no_pic}";
        $instructor_name    = ucwords(strtolower("{$record['first_name']} {$record['last_name']}"));
        $instructor = "
            <div class='CPS_instructor_outter_wrapper'>
            <div class='CPS_instructor_name'>{$instructor_name}</div>
            <div><img src='{$instructor_picture}' border='0' height='50' alt='' /></div>
            <div class='CPS_instructor_link'><a href='#' onclick=\"ViewInstructorProfile('{$record['instructor_id']}')\">[view profile]</a></div>
            </div>
            ";

        
        # SETUP ACTIONS
        # ============================
        # see if session can be cancelled
        $this->OBJ_SESSION_CANCEL->session_record = $record;
        $cancellable = ($date_start_local < $today_date) ? false : $this->OBJ_SESSION_CANCEL->CheckIfSessionCanBeCancelled();
        //$cancellable = ($date_start_local < $today_date) ? false : CheckIfSessionCanBeCancelled($record, $this->session_cancel_within_time_hrs);
        
        # see if user has rated this session already
        $user_rated = ($record['rating_user'] == 0) ? false : true;
        
        
        $eq_LaunchSession       = EncryptQuery("class=Sessions_ProcessUser;v1={$record['session_checklists_id']};v2={$record['user_wh_id']}");
        $eq_CancelSession       = EncryptQuery("class=Sessions_CancelSignup;v1={$record['sessions_id']}");
        $eq_TestSession         = EncryptQuery("class=Sessions_TestLogin;v1={$record['sessions_id']}");
        $eq_RateSession         = EncryptQuery("class=Sessions_RatingsUser;v1={$record['sessions_id']};v2={$record['user_wh_id']}");
        $eq_Ical                = EncryptQuery("class=Sessions_iCal;v1={$record['sessions_id']}");
        $eq_ViewInstructor  	= EncryptQuery("class=InstructorProfile_View ;v1={$record['instructor_id']}");
        
        
        $actions = "";
        $actions .= (($date_start_local == $today_date) && $this->show_launch_session) ? "<div><a href='#' onclick=\"LaunchSessionNewWindow('{$eq_LaunchSession}')\">BEGIN SESSION</a></div>" : '';
        $actions .= (($date_start_local == $today_date || $date_start_local > $today_date) && $this->show_test_session) ? "<div><a href='#' onclick=\"TestSession('{$eq_TestSession}')\">TEST SESSION</a></div>" : '';
        #$actions .= (($date_start_local == $today_date || $date_start_local > $today_date) && $this->show_ical) ? "<div><a href='#' onclick=\"GetOutlookCalendar('{$eq_GetOutlookCalendar}')\">ADD TO OUTLOOK CALENDAR</a></div>" : '';
        $actions .= (($date_start_local == $today_date || $date_start_local > $today_date) && $this->show_ical) ? "<div><a href='#' onclick=\"top.parent.appformCreateOverlay('Download iCal File', getClassExecuteLinkNoAjax('{$eq_Ical}'), 'apps'); return false;\">ADD TO OUTLOOK CALENDAR</a></div>" : '';
        $actions .= (($date_start_local < $today_date && !$user_rated) && $this->show_user_rate_session) ? "<div><a href='#' onclick=\"RateSession('{$eq_RateSession}')\">RATE SESSION</a></div>" : '';
        $actions .= (($cancellable) && $this->show_user_cancel_session) ? "<div><a href='#' onclick=\"CancelSession('{$eq_CancelSession}')\">CANCEL SESSION</a></div>" : '';
        $actions .= ($this->show_instructor_profile) ? "<div><a href='#' onclick=\"ViewInstructorProfile('{$eq_ViewInstructor}')\">VIEW INSTRUCTOR PROFILE</a></div>" : '';
		
        
        
        # SETUP OTHER STUFF
        # ============================
        $img_type               = '';
        $class                  = '';
        $LEFT                   = 0;
        $instructor_img_width   = 100;
		
        
        $input_date_time        = $date_start_local;
        $input_timezone         = $USER_LOCAL_TIMEZONE;
        $output_timezone        = $USER_LOCAL_TIMEZONE;
        $output_format          = 'M jS, Y';
        
        
        
        $date_start_local_formatted     = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
        
        
		
		$launch_button			= (($date_start_local == $today_date) && $this->show_launch_session) ? MakeButton('regular', 'BEGIN SESSION', '', '', '', "LaunchSessionNewWindow('{$eq_LaunchSession}')") : '';
 
		$show_admin_datetime = false;
		$admin_datetime = "
			<br /><br />
			<b>Stored UTC Date/Time</b> <br />
			<div>{$record['utc_start_datetime']} - {$record['utc_end_datetime']}</div>
            ";
		$admin_datetime = ($show_admin_datetime) ? $admin_datetime : '';
		
		$user_datetime = "
			<div style='padding:2px; color:#990000; font-weight:bold;'>LOCAL DATE/TIME</div>
			<div>({$USER_LOCAL_TIMEZONE})</div>
			<div>{$date_start_local_formatted}</div>
			<div>{$time_start} - {$time_end}</div>
			";
		
		
        
        # CREATE THE OUTPUT
        # ============================
        $output = "
        <div class='customer_session_outter_wrapper {$class}'>
        <div class='customer_session_inner_wrapper'>
            
            <div class='col' style='width:75px;'>
                <div class='blog_postdate' title='{$m_display} {$d}, {$y}'>
                    <div class='blog_month blog_m-{$m}'>{$m_display}</div>
                    <div class='blog_day blog_d-{$d}'>{$d}</div>
                    <div class='blog_year blog_y-{$y}'>{$y}</div>
                </div>
                <div style='display:none;'>
                    <div>sessions_id ==> {$record['sessions_id']}</div>
                </div>
            </div>
            
            <div class='col touchpoint_icon'>
                <img src='{$img_type}' border='0' width='{$instructor_img_width}' alt='' />
            </div>
            <div class='col touchpoint_info'>
				{$admin_datetime}
				{$user_datetime}
            </div>
            

            
            <div class='col'>
                &nbsp;&nbsp;&nbsp;
            </div>
            
            <div class='col touchpoint_actions'>
                <div style='padding:2px; color:#990000; font-weight:bold;'>ACTIONS</div>
                <div>{$actions}</div>
            </div>
			
			<div class='col'>
                &nbsp;&nbsp;&nbsp;<br />{$launch_button}
            </div>
			
            <div class='clear'></div>
        </div>
        </div>
        ";
        
        
        $this->sessions_today     .= ($date_start_local == $today_date) ? $output : '';
        $this->sessions_future    .= ($date_start_local > $today_date) ? $output : '';
        $this->sessions_past      .= ($date_start_local < $today_date) ? $output : '';
        
    } //end checking if cancelled    

    }


    
    
}  // -------------- END CLASS --------------