<?php
class Profile_CustomerProfileSessions extends BaseClass
{
    public $ShowQuery                       = false;
    public $link_session_window             = '';
    
    public $picture_dir                     = '/office/';
    public $image_dir_instructor            = 'images/instructors/';
    public $image_no_pic                    = 'thumbnail_no_picture.jpg';
    
    public $image_template_blog_dates       = '/office/images/templates/blog/dates.png';
    
    public $table_sessions                  = 'sessions';
    public $table_session_checklists        = 'session_checklists';
    public $table_instructor_profile        = 'instructor_profile';

    public $sessions_array                  = array();
    public $sessions_today                  = '';
    public $sessions_future                 = '';
    public $sessions_past                   = '';
    
    public $session_cancel_within_time_hrs  = 2; //dont' allow cancelling if within X hrs of current time
    
    public function  __construct()
    {
        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => '',
            'Description' => 'Create and manage session_checklists',
            'Created'     => '2010-10-27',
            'Updated'     => '2010-10-27'
        );
        
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


    public function GetAllSessions($WH_ID)
    {
        //$this->wh_id = 1000666;
        $this->wh_id = $WH_ID;
        
        
        $script = "
            function LaunchSession(code) {
                alert('code to LAUNCH session would go here');
                //top.parent.appformCreate('Existing Chat: '+code, '{$this->link_session_window};code='+code, 'apps');
            }
            function CancelSession(code) {
                var link_1 = '/office/dev_richard/class_execute;class=Sessions_CancelSignup;classVars='+code;
                top.parent.appformCreate('Window', link_1, 'apps'); return false;
            }
            function TestSession(code) {
                alert('code to TEST LAUNCH session would go here');
            }
            function GetOutlookCalendar(code) {
                alert('code to GET OUTLOOK CALENDAR session would go here');
            }
            function ViewInstructorProfile(code) {
                top.parent.appformCreate('View Instructor Profile', 'instructor_profile/instructor_profile_view;wh_id='+code,'apps');
            }
            
            ";
        AddScript($script);
        
        $this->AddStyleBlog();
        
        
        # GET ALL THE SESSIONS & CREATE OUTPUTS
        # ============================================
        $this->sessions_array = $this->GetSessions();
        
        $this->RandomlyDateSessions();
        
        
        # SORT THE SESSIONS BY DATE
        # ============================================
        $array                  = $this->sessions_array;
        $on                     = 'start_datetime'; //'date';
        $order                  = 'SORT_ASC';
        $this->sessions_array   = array_sort($array, $on, $order);
        
        foreach ($this->sessions_array as $session) {
            $data = $this->FormatSession($session);
        }
        
        
        # OUTPUT ALL THE SESSIONS
        # ============================================
        $output = "";
        $output .= "<br /><div class='sessions_header'>TODAYS SESSIONS</div><br />{$this->sessions_today}<br /><br />";
        $output .= "<br /><div class='sessions_header'>FUTURE SESSIONS</div><br />{$this->sessions_future}<br /><br />";
        $output .= "<br /><div class='sessions_header'>PAST SESSIONS</div><br />{$this->sessions_past}<br /><br />";
        
        $style = "
            .sessions_header {
                font-weight:bold;
                font-size:14px;
                color:#990000;
                border-bottom:1px solid #990000;
            }";
        AddStyle($style);
            
        
        #echo $output;
        return $output;
    }
    
    
    public function GetSessions()
    {
        $records = $this->SQL->GetArrayAll(array(
            'table' => "$this->table_session_checklists",
            'keys'  => "$this->table_session_checklists.*, $this->table_sessions.*, $this->table_instructor_profile.*",
            //'where' => "`wh_id`=$this->wh_id AND $this->table_chats.active=1",
            'where' => "$this->table_session_checklists.active=1",
            'joins' => "
                LEFT JOIN $this->table_sessions ON $this->table_sessions.sessions_id = $this->table_session_checklists.sessions_id 
                LEFT JOIN $this->table_instructor_profile ON $this->table_instructor_profile.wh_id = $this->table_sessions.instructor_id
                ",
        ));
        if ($this->ShowQuery) echo '<br />' . $this->SQL->Db_Last_Query;
        return $records;
    }
    
    
    public function RandomlyDateSessions()
    {
        for ($i=0; $i<count($this->sessions_array); $i++) {
            $num = rand(1,3);
            switch ($num) {
                case 1:
                    # today
                    $date = date('Y-m-d');
                break;
                case 2:
                    # past
                    $date   = "2010-05-11";
                break;
                case 3:
                    # future
                    $date   = "2010-12-30";
                break;
            }
            $this->sessions_array[$i]['date'] = $date;
        }
    }
    
    
    public function FormatSession($record)
    {
        if ($record['cancelled'] == 0) {
    
        $event_date = $record['date'];
        $today_date = date('Y-m-d');
        
        
        # FORMAT DATE
        # ============================
        $date       = explode(' ', $record['date']);
        $date       = explode('-', $date[0]);
        $y          = $date[0];
        $m          = $date[1];
        $d          = $date[2];
        $m_display  = date("M", mktime(0, 0, 0, $m+1, 0, 0));
        
        
        # FORMAT TIME
        # ============================
        $time_start     = date("g:i a", strtotime("{$record['start_datetime']}"));
        $time_end       = date("g:i a", strtotime("{$record['end_datetime']}"));
        
        
        # SETUP INSTRUCTOR
        # ============================
        $instructor_picture = ($record['primary_pictures_id']) ? "{$this->picture_dir}{$record['primary_pictures_id']}" : "{$this->picture_dir}{$this->image_dir_instructor}{$this->image_no_pic}";
        #$first_name         = strtocamel("{$record['first_name']}");
        #$last_name          = strtocamel("{$record['last_name']}");
        #$instructor_name    = "$first_name $last_name";
        $instructor_name    = ucwords(strtolower("{$record['first_name']} {$record['last_name']}"));
        $instructor = "
            <div style='padding:2px; border:1px solid #eee;'>
            <div style='background-color:#eee; padding:2px;'><b>{$instructor_name}</b></div>
            <div><img src='{$instructor_picture}' border='0' height='50' alt='' /></div>
            <div style='background-color:#eee; padding:2px;'><a href='#' onclick=\"ViewInstructorProfile('{$record['instructor_id']}')\">[view profile]</a></div>
            </div>
            ";
        
        
        # SETUP ACTIONS
        # ============================
        $cancellable = CheckIfSessionCanBeCancelled($record, $this->session_cancel_within_time_hrs);
        
        $actions = "";
        $actions .= ($event_date == $today_date) ? "<div><a href='#' onclick=\"TestSession('{$record['session_checklists_id']}')\">LAUNCH SESSION</a></div>" : '';
        $actions .= ($event_date == $today_date || $event_date > $today_date) ? "<div><a href='#' onclick=\"TestSession('{$record['session_checklists_id']}')\">TEST SESSION</a></div>" : '';
        $actions .= ($event_date == $today_date || $event_date > $today_date) ? "<div><a href='#' onclick=\"GetOutlookCalendar('{$record['session_checklists_id']}')\">ADD TO OUTLOOK CALENDAR</a></div>" : '';
        $actions .= ($cancellable) ? "<div><a href='#' onclick=\"CancelSession('{$record['session_checklists_id']}')\">CANCEL SESSION</a></div>" : '';
        
        
        # SETUP OTHER STUFF
        # ============================
        $img_type               = '';
        $class                  = '';
        $LEFT                   = 0;
        $instructor_img_width   = 100;
        
        # CREATE THE OUTPUT
        # ============================
        $output = "
        <div class='touchpoint_outter_wrapper {$class}'>
        <div class='touchpoint_inner_wrapper'>
            
            <div class='col' style='width:75px;'>
                <div class='blog_postdate' title='{$m_display} {$d}, {$y}'>
                    <div class='blog_month blog_m-{$m}'>{$m_display}</div>
                    <div class='blog_day blog_d-{$d}'>{$d}</div>
                    <div class='blog_year blog_y-{$y}'>{$y}</div>
                </div>
            </div>
            
            <div class='col touchpoint_icon'>
                <img src='{$img_type}' border='0' width='{$instructor_img_width}' alt='' />
            </div>
            <div class='col touchpoint_info'>
                <b>{$record['date']}</b> <br />
                {$time_start} - {$time_end}
            </div>
            
            <div class='col touchpoint_instructor'>
                <div style='padding:2px; color:#990000; font-weight:bold;'>INSTRUCTOR</div>
                <div>{$instructor}</div>
            </div>
            
            <div class='col'>
                &nbsp;&nbsp;&nbsp;
            </div>
            
            <div class='col touchpoint_actions'>
                <div style='padding:2px; color:#990000; font-weight:bold;'>ACTIONS</div>
                <div>{$actions}</div>
            </div>
            <div class='clear'></div>
        </div>
        </div>
        ";
        
        
        $this->sessions_today     .= ($event_date == $today_date) ? $output : '';
        $this->sessions_future    .= ($event_date > $today_date) ? $output : '';
        $this->sessions_past      .= ($event_date < $today_date) ? $output : '';
        
        
        
        
        
        
        $style = "
        .touchpoint_outter_wrapper {
            border: 1px solid #d5d5d5;
            padding: 5px;
            background-color: #eee;
            margin-bottom:10px;
        }
        .touchpoint_inner_wrapper {
            background-color: #fff;
        }
        .touchpoint_icon {
            width:70px;
        }
        .touchpoint_info {
            width:200px;
        }
        .touchpoint_actions {
            width:200px;
        }
        .touchpoint_instructor {
            width:150px;
        }
        .col {
            float:left;
        }
        .clear {
            clear:both;
        }
        a {
            text-decoration:none;
        }
        ";
        AddStyle($style);
        
    } //end checking if cancelled    

/*
Array
(
    [session_checklists_id] => 1
    [sessions_id] => 12
    [wh_id] => 900017
    [paid] => 1
    [payment_id] => 1666
    [email_booked_user_sent] => 0
    [email_booked_instructor_sent] => 0
    [email_reminder_1_user_sent] => 0
    [email_reminder_1_instructor_sent] => 0
    [email_reminder_2_user_sent] => 0
    [email_reminder_2_instructor_sent] => 0
    [cancelled] => 0
    [cacelled_reason] => 
    [cancelled_by_wh_id] => 
    [email_cancelled_user_sent] => 0
    [email_cancelled_instructor_sent] => 0
    [login_user] => 0
    [login_user_datetime] => 
    [login_instructor] => 0
    [login_instructor_datetime] => 
    [session_started] => 0
    [session_started_datetime] => 
    [session_completed] => 0
    [session_completed_datetime] => 
    [rating_user] => 0
    [rating_instructor] => 0
    [instructor_video_uploaded] => 0
    [active] => 1
    [updated] => 2010-11-04 15:10:00
    [created] => 0000-00-00 00:00:00
    [session_types_id] => 
    [credits_cost] => 10
    [title] => 
    [description] => 
    [instructor_id] => 900017
    [date] => 2010-10-23
    [start_datetime] => 0500
    [end_datetime] => 0600
    [notes] => 6hr
    [display_on_website] => 1
    [booked] => 0
    [booked_wh_id] => 
    [locked] => 0
    [locked_wh_id] => 
    [locked_start_datetime] => 
    [instructor_profile_id] => 24
    [gender] => F
    [first_name] => MARIANNA
    [last_name] => RUTHERFORD
    [yoga_types] => Bikram,Hatha,Other
    [profile] => Profile Goes Here
    [experience_years] => 1
    [location_city] => Portland
    [location_state] => OR
    [primary_pictures_id] => images/instructors/thumbnail_1288820625.jpg
    [secondary_array_pictures_id] => 
)
*/
    }

    public function AddStyleBlog()
    {
        $style = "
            /* DATE SPRITE SETTINGS */
            /* ======================================== */
            .blog_postdate {
              background-color:#F4F3EB;
              position: relative;
              width: 60px;
              height: 60px;
            }
            .blog_month, .blog_day, .blog_year {
              position: absolute;
              text-indent: -1000em;
              background-image: url({$this->image_template_blog_dates});
              background-repeat: no-repeat;
            }
            .blog_month { top: 2px; left: 0; width: 32px; height: 24px;}
            .blog_day { top: 25px; left: 0; width: 32px; height: 25px;}
            .blog_year { top: 2px; left: 32px; width: 17px; height: 48px;}
            /*.blog_year { bottom: 0; right: 0; width: 17px; height: 48px;}*/


            .blog_m-01 {background-position:0 4px}
            .blog_m-02 {background-position:0 -28px}
            .blog_m-03 {background-position:0 -57px}
            .blog_m-04 {background-position:0 -90px}
            .blog_m-05 {background-position:0 -121px}
            .blog_m-06 {background-position:0 -155px}
            .blog_m-07 {background-position:0 -180px}
            .blog_m-08 {background-position:0 -216px}
            .blog_m-09 {background-position:0 -246px}
            .blog_m-10 {background-position:0 -273px}
            .blog_m-11 {background-position:0 -309px}
            .blog_m-12 {background-position:0 -340px}
            ";
            
            
        for ($d=0; $d<31; $d++) {
            $pos_left           = ($d < 16) ? '-50' : '-100';
            $pos_top_offset     = 31;
            $pos_top            = ($d < 16) ? -($d * $pos_top_offset) : -(($d-16) * $pos_top_offset);
            
            $day_str            = $d + 1;
            $day                = str_pad($day_str, 2, "0", STR_PAD_LEFT);
            
            $style .= ".blog_d-{$day} { background-position: {$pos_left}px {$pos_top}px;}
            ";
        }
        
        
        
        for ($y=0; $y<9; $y++) {
            $pos_left           = '-150';
            $pos_top_offset     = 50;
            
            $year_str           = $y + 6;
            $pos_top            = -($y * $pos_top_offset);
            $year               = '20' . str_pad($year_str, 2, "0", STR_PAD_LEFT);
            
            $style .= ".blog_y-{$year} { background-position: {$pos_left}px {$pos_top}px;}
            ";
        }
        
        AddStyle($style);
    }
    
    
}  // -------------- END CLASS --------------