<?php
class Sessions_Search extends BaseClass
{
    public $Show_Query                  = false;    // TRUE = output the database queries ocurring on this page
    public $Show_Instructor_WHID        = false;    // TRUE = show the instructor's WHID in the output
    public $Show_Sessions_Id            = false;    // TRUE = show the session ID in the output
    public $Use_Fake_Course_Data        = false;    // TRUE = Create fake events for the instructor - don't pull from database
    public $Show_Booked_Sessions        = false;    // TRUE = display sessions that are currently booked
    public $Show_Locked_Sessions        = true;     // TRUE = display sessions that are currently locked
    public $Show_Non_Display_Sessions   = false;    // TRUE = display sessions that have been set not to show in database
    public $Show_Inactive_Sessions      = false;    // TRUE = display sessions that have been set to active=0 in database
    public $Show_Sessions_Before_Today  = false;    // TRUE = Will show sessions before today - ONLY in the search BY DATE method
    public $Show_Hidden_Instructors     = false;    // TRUE = display sessions that are from instructors currently hidden on the site
    
    public $Session_Signup_Link         = '/office/sessions/signup';
    public $script_location             = "/office/AJAX/sessions/search";
    public $Table_Sessions              = 'sessions';
    public $Table_Instructors           = 'instructor_profile';
    public $img_no                      = '/office/images/buttons/cancel.png';
    public $img_yes                     = '/office/images/buttons/save.png';
    public $SuccessRedirectChatPage     = 'chat_user';
    
    public $Cal_Date_Start              = '-0D';           // search by instructor - first day to show
    public $Cal_Date_End                = '+2M +0D';      // search by instructor - last day to show
    
    public $SearchByDateLeftText        = "search by date";
    public $SearchByInstructorLeftText  = "search by instructor";
        
        
    // ---------- NON-MODIFIABLE VARIABLES ----------
    public $Return_Page                             = null;
    public $Is_Instructor                           = false;
    public $Existing_Records                        = array();
    public $Existing_Records_Date                   = '';
    public $Existing_Records_Timezone               = '';
    public $Existing_Records_Timezone_Display       = '';
    public $Searching_Yoga_Style_Display            = '';
    public $Existing_Records_Wh_Id                  = '';
    public $FormattedRecords                        = array();
    public $instructor_picture_list                 = '';
    public $instructor_active_picture               = '';
    public $Existing_Records_Instructor             = array();
    public $Instructor_Records                      = array();
    public $processing_records                      = array();
    public $processing_records_date                 = '';
    public $processing_records_wh_id                = '';
    public $rowData                                 = '';
    public $courseDetailsData                       = '';
    public $Yoga_Instructor_Types_Where             = '';
    public $Yoga_Session_Types_Where                = '';
    public $Search_Session_Types_Standard           = false;
    public $Search_Session_Types_Therapy            = false;
    public $Dont_Search_Instructor_Yoga_Types       = false;
    public $OBJ_TIMEZONE                            = null;
    public $User_Today_Date                         = ''; //Today's date
    public $User_First_Day_Month                    = ''; //Gives the first day of the current month
    
    
    public function  __construct()
    {
        parent::__construct();
        
        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => 'Richard Witherspoon',
            'Updated'     => '2012-06-19',
            'Version'     => '1.2',
            'Description' => 'Allow users to search for sessions on the website',
        );
        
        /* UPDATE LOG ======================================================================================
        
            2012-03-27  -> Function :: AddScript() :: Restricted dates on instructor search. Added vars "Cal_Date_Start" and "Cal_Date_End".
            2012-06-19  -> Modified button to show in the menu - SearchByInstructor_MenuLeft() && SearchByDate_MenuLeft()
        
        ====================================================================================== */
        
        
        
        $this->Return_Page       = (Get('retpage')) ? "http://www.yogalivelink.com/" . Get('retpage') : '';
        
        # IF AN INSTRUCTOR_WHID HAS BEEN PUSHED IN - LOAD THAT SCHEDULE
        # =================================================================
        if (Get('instructor_whid')) {
            $whid = Get('instructor_whid');
            AddScriptOnReady("
                LoadInstructorProfile({$whid});     // select the instructor
                LoadInstructorCalendar({$whid});    // load that instructor's calendar
            ");
        }
        
        
        $this->OBJ_TIMEZONE = new General_TimezoneConversion();
        
        
        # GET THE USER'S LOCAL DATE
        # =================================================================
        date_default_timezone_set($GLOBALS['USER_LOCAL_TIMEZONE']);
        $this->User_Today_Date          = date('Y-m-d');
        $this->User_First_Day_Month     = date('Y-m-1');
        date_default_timezone_set($GLOBALS['SERVER_TIMEZONE']);
        
        //echo ArrayToStr($_SESSION['USER_LOGIN']['LOGIN_RECORD']);
        
        if (isset($_SESSION['USER_LOGIN']['LOGIN_RECORD']['tz_name']) && isset($_SESSION['USER_LOGIN']['LOGIN_RECORD']['tz_display'])) {
            $this->Existing_Records_Timezone            = $_SESSION['USER_LOGIN']['LOGIN_RECORD']['tz_name'];
            $this->Existing_Records_Timezone_Display    = $_SESSION['USER_LOGIN']['LOGIN_RECORD']['tz_display'];
        } else {
            $this->Existing_Records_Timezone            = $GLOBALS['USER_LOCAL_TIMEZONE'];
            $this->Existing_Records_Timezone_Display    = $GLOBALS['USER_LOCAL_TIMEZONE_DISPLAY'];
        }
        
        #echo "<br />" . $this->Existing_Records_Timezone;
        #echo "<br />" . $this->Existing_Records_Timezone_Display;
        
    } // -------------- END __construct --------------

    
    public function MakeYogaTypesWhereFromOptions($options='')
    {
        $this->Search_Session_Types_Standard    = true;
        $this->Search_Session_Types_Therapy     = false;
    
        if(!$options) {
            $this->Searching_Yoga_Style_Display = 'All';
            return false;
        }
        
        $query = '';
        
        $list = explode('|', $options);
        foreach ($list as $item) {
            $parts = explode('~', $item);
            $query .= " `yoga_types` LIKE \"%{$parts[1]}%\" OR ";
            
            if ($parts[1] == 'All') {
                $this->Dont_Search_Instructor_Yoga_Types = true;
            }
            
            switch ($parts[1]) {
                case 'Yoga Therapy':
                    $this->Search_Session_Types_Standard = false;
                    $this->Search_Session_Types_Therapy = true;
                break;
                case 'All':
                    $this->Search_Session_Types_Standard = true;
                    $this->Search_Session_Types_Therapy = true;
                break;
            }
            
            switch ($parts[1]) {
                case '':
                case 'START_SELECT_VALUE':
                    $this->Searching_Yoga_Style_Display = 'All';
                break;
                default:
                    $this->Searching_Yoga_Style_Display = $parts[1];
                break;
            }
            
            
        }
        $query = substr($query, 0, -4);
        
        $this->Yoga_Instructor_Types_Where = ($this->Dont_Search_Instructor_Yoga_Types) ? "" : "({$query})";
    }
    
    public function AjaxHandle()
    {
        $this->Return_Page                  = Get('retpage');
        
        
        
        
        # figure out the timezone
        if (Get('timezone')) {
            $timezone   = Get('timezone');
            $list       = explode('|', $timezone);
            foreach ($list as $item) {
                $parts      = explode('~', $item);
                $tz         = $parts[1];
            }
            $this->Existing_Records_Timezone            = $tz;
            $this->Existing_Records_Timezone_Display    = str_replace('~', ' ', Get('timezoneDisplay'));
        } else {
            
        }
    
        $action = Get('action');
        switch ($action) {
        
            case 'LoadExistingRecords':
                $this->Existing_Records_Wh_Id       = Get('instructor_id');
                $this->Existing_Records_Date        = Get('date');
                
                $options = Get('formOptions');
                $this->MakeYogaTypesWhereFromOptions($options);
                
                $this->GetCourses();
                $this->FormatCourses();
                $this->CreateSchedule();
                $this->OutputSchedule();
                ###$this->OutputInstructorPictureScript();
            break;
            
            case 'LoadExistingRecordsForInstructor':
                $this->Existing_Records_Wh_Id       = Get('instructor_id');
                $this->Existing_Records_Date        = Get('date');
                
                $this->GetCourses();
                $this->FormatCourses();
                $this->CreateScheduleForInstructor();
                $this->OutputSchedule();
                //$this->OutputInstructorPictureScript();
            break;
            
            /*
            case 'ProcessRecords':
                $this->processing_records           = unserialize(Post('contentArray'));
                $this->processing_records_wh_id     = Get('wh_id');
                $this->processing_records_date      = Get('date');
                $this->ProcessRecords();
            break;
            */
            
            case 'LoadInstructors':
                # GET ALL INSTRUCTOR PROFILES FROM A PIPE-SEPERATED LIST
                $whid_sessions      = Get('whid_sessions');
                $whid_sessions      = explode('|', $whid_sessions);
                
                $profile = '';
                foreach ($whid_sessions as $record) {
                    $parts          = explode(',', $record);
                    $wh_id          = $parts[0];
                    $session_id     = $parts[1];
                    
                    $profile       .= $this->LoadInstructorProfileForCalendarSession($wh_id, $session_id);
                }
                echo $profile;
            break;
            
            case 'LoadInstructorProfile':
                $wh_id      = Get('whid');
                $profile    = $this->LoadInstructorProfile($wh_id);
                echo $profile;
            break;
            
            case 'LoadInstructorCalendar':
                $wh_id      = Get('whid');
                $calendar   = $this->LoadCalendarScheduleForInstructor($wh_id);
                
                //$calendar = '#############';
                
                echo $calendar;
            break;
            
            default:
                $p = Post('contentArray');
                $p_array = unserialize($p);
                
                #echo "<br />p ==> " . $p;
                #echo "<br />p_array ==> " . $p_array;
                
                echo "<br /><br />P ==> ".ArrayToStr($p_array);
            break;
        }
    }
    
    
    
	public function GetCourses($WH_ID=0, $DATE='')
	{
        // =====================================================================
        // FUNCTION :: GETS COURSES THAT MATCH SEARCH CRITERIA PASSED IN
        // =====================================================================
        /*
            1. get the users date
            2. convert over to UTC
            3. search database based on UTC dates that fall in the local user dates
        */
        // =====================================================================
        
        
        $this->Existing_Records_Timezone            = ($this->Existing_Records_Timezone) ? $this->Existing_Records_Timezone : $GLOBALS['DEFAULT_LOCAL_TIMEZONE'];
        $this->Existing_Records_Timezone_Display    = ($this->Existing_Records_Timezone_Display) ? $this->Existing_Records_Timezone_Display : $GLOBALS['DEFAULT_LOCAL_TIMEZONE_DISPLAY'];
        
        $USER_LOCAL_TIMEZONE = $this->Existing_Records_Timezone;

        if ($this->Use_Fake_Course_Data) {
            $records = $this->GetFakeRecordsFromDatabase(3, '0800|1200');
            $this->Existing_Records = $records;
            //echo ArrayToStr($records) . "<br /><hr><br />";
        } else {
        
            # CONVERT DATETIME FROM USER LOCAL TO UTC - FOR DATABASE SEARCH
            # ======================================================================
            $search_date    = ($this->Existing_Records_Date) ? $this->Existing_Records_Date : $this->User_Today_Date;
            
            if (!$this->Show_Sessions_Before_Today && ($search_date < date('Y-m-d'))) {
                $records = null;
            } else {
                $input_date_time        = "{$search_date} 0000";
                $input_timezone         = $USER_LOCAL_TIMEZONE;
                $output_timezone        = 'UTC';
                $output_format          = 'Y-m-d H:i:s';
                $utc_start_datetime     = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
                
                $input_date_time        = "{$search_date} 2300";
                $input_timezone         = $USER_LOCAL_TIMEZONE;
                $output_timezone        = 'UTC';
                $output_format          = 'Y-m-d H:i:s'; //'Y-m-d Hi';
                $utc_end_datetime       = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
                
                # GET THE RECORDS
                # ======================================================================
                /*
                echo "<br /><br />";
                echo "<br />USER_LOCAL_TIMEZONE ====> $USER_LOCAL_TIMEZONE";
                echo "<br />SERVER_TIMEZONE ====> {$GLOBALS['SERVER_TIMEZONE']}";
                echo "<br />input_date_time ====> $input_date_time";
                echo "<br />search_date ====> $search_date";
                echo "<br />USER_LOCAL_TIMEZONE ====> $USER_LOCAL_TIMEZONE";
                echo "<br />utc_start_datetime ====> $utc_start_datetime";
                echo "<br />utc_end_datetime ====> $utc_end_datetime";
                echo "<br /><br />";
                */
                $records = $this->GetCoursesFromDatabase($utc_start_datetime, $utc_end_datetime);
            }
        }
        
        # PROCESS THE RECORDS
        # ======================================================================
        $this->ProcessCourseRecords($records);
    }
    
	public function GetCoursesInstructor($SEARCH_MONTHS=1)
	{
        // =====================================================================
        // FUNCTION :: GETS COURSES THAT MATCH SEARCH CRITERIA PASSED IN
        // =====================================================================
        /*
            1. get the users date
            2. convert over to UTC
            3. search database based on UTC dates that fall in the local user dates
        */
        // =====================================================================
        $this->Existing_Records_Timezone            = ($this->Existing_Records_Timezone) ? $this->Existing_Records_Timezone : $GLOBALS['DEFAULT_LOCAL_TIMEZONE'];
        $this->Existing_Records_Timezone_Display    = ($this->Existing_Records_Timezone_Display) ? $this->Existing_Records_Timezone_Display : $GLOBALS['DEFAULT_LOCAL_TIMEZONE_DISPLAY'];
        
        $USER_LOCAL_TIMEZONE = $this->Existing_Records_Timezone;
        
        if ($this->Use_Fake_Course_Data) {
            
            $num_months         = $SEARCH_MONTHS;
            $num_days           = 15;
            $num_sessions_day   = 2;
            
            $days_array = array();
            $days_list = '';
            for ($m=0; $m<$num_months; $m++) {
                for ($z=0; $z<$num_days; $z++) {
                    do {
                        $d = rand (0,29);
                        $new_date = mktime(0, 0, 0, date("m")+$m, date("d")+$d, date("Y"));
                        $new_date = date('Y-m-d', $new_date);
                    } while (in_array($new_date, $days_array));
                    $days_array[] = $new_date;
                    $days_list .= "{$new_date}|";
                }
            }
            $days_list  = substr($days_list, 0, -1);
            $records    = $this->GetFakeRecordsForInstructor($this->Existing_Records_Wh_Id, $num_sessions_day, '0200|0400|0600|0800|1000|1200|1400|1600|1800|2000|2200', $days_list);
            #echo ArrayToStr($records) . "<br /><hr><br />";
        } else {
        
        
        
        
            # CONVERT DATETIME FROM USER LOCAL TO UTC - FOR DATABASE SEARCH
            # ======================================================================
            $search_date_start = ($this->Is_Instructor && $this->Show_Sessions_Before_Today) ? $this->User_First_Day_Month : $this->User_Today_Date;
            $search_date_end        = mktime(0, 0, 0, date("m")+$SEARCH_MONTHS, date("d"), date("Y"));
            $search_date_end        = date('Y-m-d', $search_date_end);
            
            $input_date_time        = "{$search_date_start} 0000";
            $input_timezone         = $USER_LOCAL_TIMEZONE;
            $output_timezone        = 'UTC';
            $output_format          = 'Y-m-d H:i:s';
            $utc_start_datetime     = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
            
            $input_date_time        = "{$search_date_end} 2300";
            $input_timezone         = $USER_LOCAL_TIMEZONE;
            $output_timezone        = 'UTC';
            $output_format          = 'Y-m-d H:i:s';
            $utc_end_datetime       = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
            
            # GET THE RECORDS
            # ======================================================================
            $records = $this->GetCoursesFromDatabase($utc_start_datetime, $utc_end_datetime);
        }
        
        # PROCESS THE RECORDS
        # ======================================================================
        $this->ProcessCourseRecords($records);
    }
    
    public function GetCoursesFromDatabase($utc_start_datetime, $utc_end_datetime)
	{
    
    
        //$where_date             = "(`utc_start_datetime`>='{$utc_start_datetime}' AND `utc_end_datetime` <= '{$utc_end_datetime}') AND ";
        $where_date             = "(`utc_start_datetime`>='{$utc_start_datetime}' AND `utc_start_datetime` <= '{$utc_end_datetime}') AND ";
        $where_whid             = ($this->Existing_Records_Wh_Id) ? "`instructor_id`='{$this->Existing_Records_Wh_Id}' AND " : "";
        $where_yoga_types       = ($this->Yoga_Instructor_Types_Where) ? "{$this->Yoga_Instructor_Types_Where} AND " : '';
        $where_booked           = (!$this->Show_Booked_Sessions) ? "`$this->Table_Sessions`.`booked` = 0 AND " : '';
        $where_locked           = (!$this->Show_Locked_Sessions) ? "`$this->Table_Sessions`.`locked` = 0 AND " : '';
        $where_show_website     = (!$this->Show_Non_Display_Sessions) ? "`$this->Table_Sessions`.`display_on_website` = 1 AND " : '';
        $where_active           = (!$this->Show_Inactive_Sessions) ? "`$this->Table_Sessions`.`active` = 1 AND " : '';
        $where_hidden           = (!$this->Show_Hidden_Instructors) ? "`$this->Table_Instructors`.`display`=1 " : '';
        
        
        if ($this->Search_Session_Types_Standard && $this->Search_Session_Types_Therapy) {
            $where_session_type = " (`$this->Table_Sessions`.`type_standard`=1 OR `$this->Table_Sessions`.`type_therapy`=1) AND ";
        } elseif ($this->Search_Session_Types_Standard) {
            $where_session_type = " `$this->Table_Sessions`.`type_standard`=1 AND ";
        } elseif ($this->Search_Session_Types_Therapy) {
            $where_session_type = " `$this->Table_Sessions`.`type_therapy`=1 AND ";
        } else {
            $where_session_type = "";
        }

        $keys                   = ($where_yoga_types) ? "$this->Table_Sessions.*, $this->Table_Instructors.yoga_types" : '*';
        $joins                  = ($where_yoga_types || $where_hidden) ? "LEFT JOIN $this->Table_Instructors ON $this->Table_Instructors.wh_id = $this->Table_Sessions.instructor_id" : '';
        $order                  = "utc_start_datetime ASC";
        
        $records = $this->SQL->GetArrayAll(array(
            'table'     => $this->Table_Sessions,
            'keys'      => $keys,
            'where'     => "$where_whid $where_date $where_yoga_types $where_session_type $where_show_website $where_booked $where_locked $where_active $where_hidden",
            'joins'     => $joins,
            'order'     => $order,
        ));
        if ($this->Show_Query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        
        return $records;
    }
    
    
    
    public function ProcessCourseRecords($records) 
    {
        # =====================================================================================
        # FUNCTION :: Convert array of sessions to a usable array - including converting UTC
        #             timestamps to the user's local time.
        # =====================================================================================
        
        
        $this->Existing_Records_Timezone            = ($this->Existing_Records_Timezone) ? $this->Existing_Records_Timezone : $GLOBALS['DEFAULT_LOCAL_TIMEZONE'];
        $this->Existing_Records_Timezone_Display    = ($this->Existing_Records_Timezone_Display) ? $this->Existing_Records_Timezone_Display : $GLOBALS['DEFAULT_LOCAL_TIMEZONE_DISPLAY'];
        
        $USER_LOCAL_TIMEZONE = $this->Existing_Records_Timezone;
        
###echo "<h1>USER_LOCAL_TIMEZONE ===> $USER_LOCAL_TIMEZONE</h1>";
        
        $temp_records               = array();
        $instructor_picture_list    = '';
        
        if ($records) {
        foreach ($records as $record) {
            $instructor_picture_list .= "{$record['instructor_id']}|";
            
            # CONVERT DATE AND TIME BACK TO LOCAL USER DATE/TIME
            # =================================================================
            $input_date_time        = $record['utc_start_datetime'];
            $input_timezone         = 'UTC';
            $output_timezone        = $USER_LOCAL_TIMEZONE;
            $output_format          = 'Y-m-d Hi';
            
            $converted_date_time    = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
            $parts                  = explode(' ', $converted_date_time);
            $date_user_start        = $parts[0];
            $time_user_start        = $parts[1];
            
            $input_date_time        = $record['utc_end_datetime'];
            $input_timezone         = 'UTC';
            $output_timezone        = $USER_LOCAL_TIMEZONE;
            $output_format          = 'Y-m-d Hi';
            $converted_date_time    = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
            $parts                  = explode(' ', $converted_date_time);
            $date_user_end          = $parts[0];
            $time_user_end          = $parts[1];
            
            $start_12   = date("g:iA", strtotime($time_user_start));
            $end_12     = date("g:iA", strtotime($time_user_end));
            
            $start_12   = str_replace(array('AM', 'PM'), array('a', 'p'), $start_12);
            $end_12     = str_replace(array('AM', 'PM'), array('a', 'p'), $end_12);
            
            $blust = isset($temp_records[$date_user_start][$time_user_start]) ? count($temp_records[$date_user_start][$time_user_start]) : 0;
            $z = $blust+1;
            
            $temp_records[$date_user_start][$time_user_start][$z]['sessions_id']                = $record['sessions_id'];
            $temp_records[$date_user_start][$time_user_start][$z]['instructor_id']              = $record['instructor_id'];
            $temp_records[$date_user_start][$time_user_start][$z]['utc_start_datetime']         = $record['utc_start_datetime'];
            $temp_records[$date_user_start][$time_user_start][$z]['utc_end_datetime']           = $record['utc_end_datetime'];
            $temp_records[$date_user_start][$time_user_start][$z]['date_user_start']            = $date_user_start;
            $temp_records[$date_user_start][$time_user_start][$z]['date_user_end']              = $date_user_end;
            $temp_records[$date_user_start][$time_user_start][$z]['time_user_start']            = $time_user_start;
            $temp_records[$date_user_start][$time_user_start][$z]['time_user_end']              = $time_user_end;
            $temp_records[$date_user_start][$time_user_start][$z]['time_user_start_12hr']       = $start_12;
            $temp_records[$date_user_start][$time_user_start][$z]['time_user_end_12hr']         = $end_12;
            $temp_records[$date_user_start][$time_user_start][$z]['booked']                     = $record['booked'];
            $temp_records[$date_user_start][$time_user_start][$z]['locked']                     = $record['locked'];
            
            
        }
        } // end if exist
        
        $this->instructor_active_picture    = ($this->Existing_Records_Wh_Id) ? $this->Existing_Records_Wh_Id : '';
        $this->instructor_picture_list      = substr($instructor_picture_list, 0, -1);
        $this->Existing_Records             = $temp_records;
	}
    
    public function FormatCourses() 
    {
        // ============================================================================
        // FUNCTION :: FORMAT HOW EACH COURSE WILL BE OUTPUT IN THE CALENDAR VIEW
        // ============================================================================
        
        
        #echo '<hr>';
        #echo ArrayToStr($this->Existing_Records);
        #echo '<hr>';
        #echo '<br /><br />';
        
        
        foreach ($this->Existing_Records as $record_date) {
            foreach ($record_date as $record_time) {
                
                $z = 0;
                foreach ($record_time as $record) {
                //foreach ($this->Existing_Records as $record) {
                
                ###echo '<br />===>' . ArrayToStr($record);
                
                # DATE AND TIMES IN UTC
                
                    $parentID           = "time_{$record['time_user_start']}";
                    $time_start         = $record['time_user_start'];
                    $time_end           = $record['time_user_end'];
                    
                    
                    ##$temp_records[$date][$time][$z]['date_utc']                 = $record['date_utc'];
                    ##$temp_records[$date][$time][$z]['start_datetime_utc']       = $record['start_datetime_utc'];
                    ##$temp_records[$date][$time][$z]['end_datetime_utc']         = $record['end_datetime_utc'];
                    
                    
                    $html = "
                    <div style='padding:3px;'>
                        <div style='float:left;'>SESSION TIME</div>
                        <div style='float:right;'><img class='btn_delete' parentID='{$parentID}' src='{$this->img_no}' alt='yes' border='0' /></div>
                        <div style='clear:both;'></div>
                    </div>
                    <div class='course_info' style='padding:3px;'>
                        <span class='ci_time_start'>{$time_start}</span> - 
                        <span class='ci_time_end'>{$time_end}</span>
                    </div>
                    ";
                    
                    $temp = ''; //"Book Session ==> {$record['ci_time_start']} == {$record['ci_time_start']} == {$record['ci_time_end']}<br />";
                    $temp_2 = ''; // ==> {$record['instructor_id']} ==> {$record['instructor_name']}
                    $html_short = "
                    <div style='padding:3px; background-color:#f9f9f9;'>
                        <div style='float:left;'>
                        $temp
                        <a href='#' onclick=\"parent.appformCreate('View Instructor Profile', 'instructor_profile/instructor_profile_view;wh_id={$record['instructor_id']}','apps'); return false;\">
                            View Instructor Bio
                        </a> $temp_2
                        </div>
                        
                        
                        
                        <div style='float:right;'><img class='btn_select' parentID='{$parentID}' src='{$this->img_yes}' alt='yes' border='0' /></div>
                        <div style='clear:both;'></div>
                    </div>
                    ";
                    
                    $this->FormattedRecords[$parentID][$z]['data']              = $html;
                    $this->FormattedRecords[$parentID][$z]['sessions_id']       = $record['sessions_id'];
                    $this->FormattedRecords[$parentID][$z]['data_detail']       = $html_short;
                    $this->FormattedRecords[$parentID][$z]['instructor_id']     = $record['instructor_id'];
                    
                    $z++;
                } // end looping records
            } //end looping time
        } //end looping date
        
        
        #echo '<hr>';
        #echo ArrayToStr($this->FormattedRecords);
        #echo '<hr>';
        #echo '<br /><br />';
    }
    
    public function CreateSchedule()
    {
        // ================================================================================================
        // FUNCTION :: Create the calendar schedule - for a given day
        // ================================================================================================
        
        
        $existing_records = $this->FormattedRecords;
        
        #echo '<hr>';
        #echo ArrayToStr($existing_records);
        #echo '<hr>';
        
        $time_base          = 0000;
        $row_data           = '';
        $course_listing     = '';
        
        for ($i=0; $i<24; $i++) {
            $time_start     = $time_base + (($i)*100);
            $time_end       = $time_base + (($i+1)*100);
            $time_start     = str_pad($time_start, 4, "0", STR_PAD_LEFT);
            $time_end       = str_pad($time_end, 4, "0", STR_PAD_LEFT);
            $time           =  date("g A", strtotime($time_start));
            $divID          = "time_{$time_start}";
            
            if (isset($existing_records[$divID])) {
                # LOOP THROUGH ALL SESSIONS AT THIS TIME
                # ===================================================
                $count = 0;
                $instructors_list = '';
                $instructors_Session_list = '';
                $course_listing .= "<div id='courses_{$divID}' style='border:2px solid green; padding:3px;'>";
                foreach ($existing_records[$divID] as $record) {
                    //echo '<br />'.ArrayToStr($existing_records);
                    
                    $count++;
                    $course_listing .= "<div style='padding:3px;'><div style='border:1px solid #ccc; padding:3px;'>{$record['data_detail']}</div></div>";
                    $instructors_list .= $record['instructor_id'] . '|';
                    
                    $instructors_Session_list .= "{$record['instructor_id']},{$record['sessions_id']}|";
                }
                $course_listing .= "</div><br /><br />";
                $instructors_list = substr($instructors_list, 0, -1);
                
                $count_title    = ($count == 1) ? 'SESSION' : 'SESSIONS';                
                $display_a      = "<div class='search_timesection_content_1'>{$count} {$count_title} AVAILABLE</div><div class='search_timesection_content_2'><a href='#' onclick=\"ShowCourseListing('{$divID}', '{$instructors_Session_list}'); return false;\">Click</a> to view instructors</div>";
                $classes        = ($count == 1) ? 'search_zones search_timesection_single' : 'search_zones search_timesection_multiple';
                $status         = 'existing';
                $recordID       = '';
            } else {
                $display_a      = "<div style='padding-top:10px; color:#bbb;'>NO SESSIONS AVAILABLE</div>";
                $classes        = 'search_zones search_timesection_none';
                $status         = '';
                $recordID       = '';
            }
            
            
            
            
            if (isset($existing_records[$divID])){
                # output the actual calendar section
                # ============================================
                $display = "
                    <div style='float:left; width:60px;' class='search_timesection_time'>{$time}</div>
                    <div style='float:left; padding-top:2px;' class='search_timesection_content'>{$display_a}</div>
                    <div style='clear:both;'></div>
                    ";
            
            
                # OUTPUT DATA FOR THE MASTER LIST
                # =============================================

                $row_data  .=  "<div id='{$divID}' class='{$classes}' status='{$status}' recordID='{$recordID}' timestart='{$time_start}' timeend='{$time_end}'>{$display}</div>";
            }
        }
        
        //echo $row_data;
        
        $this->rowData = $row_data;
        $this->courseDetailsData = $course_listing;
    }

    

    public function OutputSchedule($RETURN=false)
    {
        // ========================================================================================
        // FUNCTION :: CREATE THE ENTIRE SCHEDULE CALENDAR THAT WILL BE SHOWN ON SCREEN
        // ========================================================================================
        
        
        $date       = ($this->Existing_Records_Date) ? $this->Existing_Records_Date : date('Y-m-d');
        $date       = $this->datefmt($date, 'yyyy-mm-dd', "l, M j, Y");
        $timezone   = $this->Existing_Records_Timezone_Display;
        
        $OUTPUT = <<<OUTPUT
            <div style='display:none;'>
                <div id="result_send" style='border:1px solid blue;'></div>
                <div id="result_receive" style='border:1px solid green;'></div>
            </div>
            
            <div style="clear:both;"></div>
            
            <div class='search_date_holder'>
                <span class='search_date'>Date:</span> <span class='search_date_date'>{$date}</span>
                <div class='red left_content'>Current Timezone: {$this->Existing_Records_Timezone_Display}</div>
                <div class='red left_content'>Yoga Style: {$this->Searching_Yoga_Style_Display}</div>
            </div>
            
            <div>
                {$this->rowData}
            </div>
            <div id="course_details_all" style="display:none;">
                {$this->courseDetailsData}
            </div>
OUTPUT;

        if ($RETURN) {
            return $OUTPUT;
        } else {
            echo $OUTPUT;
        }
    }
    
    


    
    
    
    
    public function CreateSessionsForInstructors($date)
    {
        $date = ($date) ? $date : date('Y-m-d');
    
        # GET ALL INSTRUCTORS
        # =====================================================
        $records = $this->SQL->GetArrayAll(array(
            'table' => 'instructor_profile',
            'keys'  => '*',
            'where' => "`active`=1",
        ));
        
        $number_of_sessions_in_day = rand(0, 6);
        
        foreach ($records as $instructor) {
            
            $time_array = array();
            
            for ($s=0; $s<$number_of_sessions_in_day; $s++) {
                
                $i              = rand(0,23);
                $time_base      = 0000;
                $time_start     = $time_base + (($i)*100);
                $time_end       = $time_base + (($i+1)*100);
                $time_start     = str_pad($time_start, 4, "0", STR_PAD_LEFT);
                $time_end       = str_pad($time_end, 4, "0", STR_PAD_LEFT);
                
                if (!in_array($time_start, $time_array)) {
                
                    $data_array = array(            
                        'credits_cost'      => '10',
                        'instructor_id'     => $instructor['wh_id'],
                        'date'              => $date,
                        'start_datetime'    => $time_start,
                        'end_datetime'      => $time_end,
                    );
                    $data   = explode('||', $this->FormatDataForInsert($data_array));
                    $keys   = $data[0];
                    $values = $data[1];
                    
                    $result = $this->SQL->AddRecord(array(
                        'table'     => 'sessions',
                        'keys'      => $keys,
                        'values'    => $values,
                    ));
                    if ($this->Show_Query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
                }
                
                $time_array[]   = $time_start;
                
            } //end for
            
        } //end foreach
        
    } //end function
    
    
  

    
    
    public function Execute()
    {
    
        #$this->GetCourses();
        #$this->FormatCourses();
        #$this->CreateSchedule();
        /*
        $schedule       = ''; //$this->OutputSchedule(true);
        $instructors    = ''; //$this->GetInstructors(true);
        
        
        $content_step_1 = "
            <div id='cal_date_search'></div>
        ";
        
        
        $options_form = OutputForm(array(
            'form||post|OPTIONS_YOGA_TYPES',
            #"code|<b>Yoga Types</b><br />",
            "@checkboxlistset||yoga_types|N||{$this->yoga_type_list}",
            'endform',
        ));
        
        $content_step_2 = "
            <div id='elements'>
                {$options_form}
            </div>
        ";
        

        $content_sessions = "
            <div id='search_schedule_holder'>{$schedule}</div>
        ";
        
        
        $selected_instructor = "
            <div id='instructors_active_selected_holder'; style='float:left;'>
                <ul id='instructors_active_selected_target' class='image-grid'></ul>
                <div class='backLink'></div>
            </div>
            <div style='clear:both;'></div>
        ";
        $active_instructors = "
            <div style='border:0px solid #990000; backgro_und-color:#eee;' class='picture_active'>
                <div style='float:left; wid__th:300px;'>
                    <ul id='instructors_active_target' class='image-grid'></ul>
                </div>
                <div style='clear:both;'></div>
            </div>
        ";
        $inactive_instructors = "
            <div style='border:0px solid #990000;' class='picture_inactive'>            
                <ul id='instructors_inactive_target' class='image-grid'>{$instructors}</ul>
            </div>
        ";
        
        $i_s_title = "<div class='session_search_title'>CURRENTLY SELECTED INSTRUCTOR</div>";
        $i_a_title = "<div class='session_search_title'>SELECTED INSTRUCTORS</div>";
        $i_i_title = "<div class='session_search_title'>ADDITIONAL INSTRUCTORS</div>";
        
        $selected_instructor        = AddBox($i_s_title, $selected_instructor, '', 'yogabox_box_title_sessionsearch');
        $active_instructors         = AddBox($i_a_title, $active_instructors, '', 'yogabox_box_title_sessionsearch');
        $inactive_instructors       = AddBox($i_i_title, $inactive_instructors, '', 'yogabox_box_title_sessionsearch');
        $content_instructors        = "<div id='search_instructors'>" . $selected_instructor . $active_instructors . $inactive_instructors . "</div>";
        
        
        $s_1_title = "<span class='session_search_step'>Step 1</span>&nbsp;&nbsp;&nbsp;&nbsp;<span class='session_search_title'>Pick a date</span>";
        $s_2_title = "<span class='session_search_step'>Step 2</span>&nbsp;&nbsp;&nbsp;&nbsp;<span class='session_search_title'>Pick A style</span>";
        
        $area_step_1        = AddBox($s_1_title, $content_step_1, '', 'yogabox_box_title_sessionsearch');
        $area_step_2        = AddBox($s_2_title, $content_step_2, '', 'yogabox_box_title_sessionsearch');
        $area_sessions      = AddBox('SESSIONS ON THIS DAY', $content_sessions, '', 'yogabox_box_title_sessioninfo');
        $area_instructors   = AddBox('INSTRUCTORS', $content_instructors, '', 'yogabox_box_title_sessioninfo');
        
        $div_gap = "<div style='width:10px; height:10px;'>&nbsp;</div>";
        
        $output = "
        <div style='width:950px;'>
            <div class='col' style='width:200px;'>
                {$area_step_1}                
                {$div_gap}
                {$area_step_2}
            </div>
            <div class='col'>{$div_gap}</div>
            <div class='col' style='width:300px; z-index:100;'>{$area_sessions}</div>
            <div class='col'>{$div_gap}</div>
            <div class='col' style='width:350px;'>{$area_instructors}</div>
            <div style='clear:both;'></div>
        </div>
        ";
        */
        #$script = "InitializeOnReady_Sessions_Search();";
        #$output .= EchoScript($script);
        
        #return $output;
    }
    
    
    # ========================================================================================
    # FUNCTIONS FOR INSTRUCTOR VIEWING THEIR OWN SCHEDULE
    # ========================================================================================
    
    public function SearchByInstructor_MenuLeft_Instructor() 
    {
        $OBJ    = new Website_PageContents();
        $output = $OBJ->GetContentFromIdentifier('##INSTRUCTOR_CALENDAR_INSTRUCTIONS##');
        
        return $output;
    }
    
    public function LoadCalendarScheduleForInstructor_Instructor($WH_ID=0)
    {
        $output = ''; //"==============<br />";
        $output .= "<div id='calendar'></div>";
        
        $instructor_id  = $WH_ID;
        $this->Existing_Records_Wh_Id   = $instructor_id; //Get('instructor_id');
        
        $SEARCH_MONTHS = 2;
        $this->GetCoursesInstructor($SEARCH_MONTHS);
        
        $records = $this->Existing_Records;
        #echo ArrayToStr($records);
        
        
        # COMPACT THE ARRAY OF EVENTS
        # ==============================================
        $new_records = array();
        foreach ($records as $dates) {
            foreach ($dates as $times) {
                foreach ($times as $record) {
                    $new_records[] = $record;
                }
            }
        }
        $records = $new_records;
        //echo ArrayToStr($records);
        
        
        # CREATE EVENT ARRAY FOR CALENDAR
        # ==============================================
        $events = '';
        foreach ($records as $record) {
            $id     = $record['sessions_id'];
            $name   = "NAME";
            $title  = "{$record['time_user_start_12hr']} - {$record['time_user_end_12hr']}";
            $start  = $record['date_user_start'];
            $end    = $record['time_user_end'];
            
            $arr    = "class=Sessions_Details;v1={$id}";
            $eq     = EncryptQuery($arr);
            
            
            # DETERMINE AND SET THE CLASS BASED ON BOOKED STATUS
            $class  = 'instructor_session_class';
            $class  = ($record['booked'] == 1) ? 'instructor_session_class_booked' : $class;
            $class  = ($record['locked'] == 1) ? 'instructor_session_class_locked' : $class;
            
            
            $events .= "$id|$eq|$title|$start|$end|$class|*Z";
        }
        $EVENT_ARRAY = $this->MakeEventArray($events);
        
        
        # ADD SCRIPT FOR CALENDAR
        # ==============================================
        $script = <<<SCRIPT
            var date = new Date();
            var d = date.getDate();
            var m = date.getMonth();
            var y = date.getFullYear();
            
            $('#calendar').fullCalendar({
                theme: true,
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'basicDay, basicWeek, month'
                },
                eventClick: function(calEvent, jsEvent, view) {
                    var eq          = calEvent.eq;
                    var eq_title    = calEvent.eq_title;
                    var link        = getClassExecuteLinkNoAjax(eq);
                    top.parent.appformCreateOverlay(eq_title, link, 'apps');
                },
                editable: true,
                events: $EVENT_ARRAY
            });
            
            
            //alert('BBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBB');

            
SCRIPT;
        EchoScript($script);
        
        return $output;
    }
    
    
    
    # ========================================================================================
    # FUNCTIONS FOR SEARCHING BY INSTRUCTOR
    # ========================================================================================
    
    public function SearchByInstructor() 
    {
        $output = $this->GetInstructors();
        return $output;
    }
    
    public function SearchByInstructor_MenuLeft() 
    {
        
        $types = "All|{$this->yoga_type_list}";
        $options_form = OutputForm(array(
            'form||post|OPTIONS_YOGA_TYPES',
            "code|<b>Search by Yoga Style</b><br />",
            "@select||yoga_types|N||$types",
            'endform',
        ));
        
        //$btn_date = "<a href='{$GLOBALS['LINK_SESSION_SIGNUP_DATE']}'><div class='btn_scheduleByDate'>&nbsp; </div></a>";
        $btn_date = "<a href='{$GLOBALS['LINK_SESSION_SIGNUP_DATE']}'><div class='buttonImg'><img src='/images/buttons/btn_schedule_date_off.png'></div></a>";
        

        
        $output = "
            <div style='text-align:center;'>
                <br /><br />
                <div class='orange left_header lowercase'>{$this->SearchByInstructorLeftText}</div>
                <div class='black left_content' id='search_current_instructor_profile'></div>
                <br />
                <div id='search_options_menu_left'>
                    <div class='left_content'>{$options_form}</div>
                </div>
                <br /><br />
                <center>
                <div>{$btn_date}</div>
                <br />
                </center>
            </div>
            ";
        
        return $output;
    }
    
    public function GetInstructors($RETURN=false)
    {
        $records = $this->SQL->GetArrayAll(array(
            'table' => 'instructor_profile',
            'keys'  => '*',
            'where' => "`display`=1 AND `active`=1",
            'order' => "sort_order ASC",
        ));
        if ($this->Show_Query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        
        $output = "
            <div class='black left_content' id='search_current_instructor_calendar'></div>
            <div id='search_current_instructor_list'>
            <div id='search_current_instructor_list_notice'></div>
            <ul id='list_all_instructors' class='image-grid'>
            ";
            
        if ($records) {
            foreach ($records as $record) {
            
                $this->Instructor_Records[$record['wh_id']] = $record;
                
                $yoga_types_list    = explode (',', $record['yoga_types']);
                $yoga_types_class   = 'All START_SELECT_VALUE ';
                foreach ($yoga_types_list as $type) {
                    $yoga_types_class .= "$type ";
                }
                $yoga_types_class   = substr($yoga_types_class, 0, -1);
                
                $name_first         = ($record['first_name'] != '') ? $record['first_name'] : '';
                $name_last          = ($record['last_name'] != '') ? ucwords(strtolower($record['last_name'])) : '';
                $name               = "{$name_first}<br />{$name_last}";
                
                $output .= "
                <li data-id='picture_{$record['wh_id']}' id='instructor_{$record['wh_id']}_li' class='$yoga_types_class'>
                    <div class='instructor_holder instructor_not_selected' id='instructor_{$record['wh_id']}'>
                        <a href='#' onclick=\"LoadInstructorProfile('{$record['wh_id']}'); return false;\">
                            <img src='/office/{$record['primary_pictures_id']}'  alt='{$record['first_name']} {$record['last_name']}' border='0' />
                            <div>{$name}</div>
                            <div style='display:none;'>{$record['wh_id']}</div>
                        </a>
                    </div>
                </li>
                ";
            }
        }
        $output .= '</ul></div>';
        
        
        if ($RETURN) {
            return $output;
        } else {
            echo $output;
        }
    }
    
    public function LoadInstructorProfile($WH_ID=0) 
    {
        $record = $this->SQL->GetRecord(array(
            'table' => 'instructor_profile',
            'keys'  => '*',
            'where' => "`wh_id`=$WH_ID AND `active`=1",
        ));
        if ($this->Show_Query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        
        $output = '';
        if ($record) {
            $picture    = "<img src='/office/{$record['primary_pictures_id']}'  alt='{$record['first_name']} {$record['last_name']}' border='0' />";
            $name       = "{$record['first_name']} {$record['last_name']}";
            $profile    = str_replace("\n", "<br />", $record['profile']);
            $wh_id      = ($this->Show_Instructor_WHID) ? "<br />{$record['wh_id']}" : '';
            
            $href       = ($this->Return_Page != null) ? $this->Return_Page : '#';
            $onclick    = ($this->Return_Page != null) ? '' : "ClearSelectedInstructor('{$record['wh_id']}'); return false;";
            
            $output = "
                <br />
                <table cellpadding='0' cellspacing='5' border='0'>
                <tr>
                    <td valign='bottom' align='left' class='search_current_instructor_profile_picture'>
                        <div class='instructor_holder'>{$picture}</div>
                    </td>
                    <td valign='middle' class='search_current_instructor_profile_name article_title'>{$name}{$wh_id}</td>
                </tr>
                <tr>
                    <td colspan='2' class='search_current_instructor_profile_profile article_all_content'>{$profile}</td>
                </tr>
                </table>
                <br />
                <div style='display:none;'><a href='#' onclick=\"LoadInstructorCalendar('{$record['wh_id']}'); return false;\" class='link_arrow'>See this instructor's availability!</a></div>
                <div><a href='{$href}' onclick=\"{$onclick}\" class='link_arrow'>Go Back to View all Instructors</a></div>
                <br /><br />
                ";
                
            ###$output .= ArrayToStr($record);
        }
        return $output;
    }
    
    public function LoadCalendarScheduleForInstructor($WH_ID=0)
    {
        //onClick='ViewAllInstructors(); return false;'
        
        $href       = ($this->Return_Page != null) ? $this->Return_Page : '#';
        $onclick    = ($this->Return_Page != null) ? '' : "ClearSelectedInstructor('{$record['wh_id']}'); return false;";
        
        
        $timezone_list      = $this->SQL->GetAssocArray($GLOBALS['TABLE_timezones'], 'tz_name', 'tz_display', '`active`=1', '', '', '`time_zones_id` ASC');
        $timezone_types     = Form_AssocArrayToList($timezone_list);
        
        $types = "All|{$this->yoga_type_list}";
        $options_timezone = OutputForm(array(
            'form||post|OPTIONS_TIMEZONE_BY_INSTRUCTOR',
            "@select||search_timezone_by_instructor|N||$timezone_types",
            'endform',
        ));
        
        
        
        $output = "
            <h1>CLICK ON TIME TO BOOK SESSION<br />
            <table><tr><td><div class='red'>Current Timezone: </div></td><td>{$options_timezone}</td></tr></table></h1>
            
            <h1><a href='{$href}' class='link_arrow' onclick=\"{$onclick}\">Go Back to View all Instructors</a></h1>
            <br />
            <div id='calendar'></div>
            ";
        
        $instructor_id  = $WH_ID;
        //$date           = '2011-02-17';
        
        
        $this->Existing_Records_Wh_Id   = $instructor_id; //Get('instructor_id');
        //$this->Existing_Records_Date    = $date; //Get('date');
        
        $SEARCH_MONTHS = 2;
        $this->GetCoursesInstructor($SEARCH_MONTHS);
        
        
        
        
        
        ////$this->GetAllCoursesForInstructorCalendar();
        $records = $this->Existing_Records;
        #echo ArrayToStr($records);
        
        
        # COMPACT THE ARRAY OF EVENTS
        # ==============================================
        $new_records = array();
        foreach ($records as $dates) {
            foreach ($dates as $times) {
                foreach ($times as $record) {
                    $new_records[] = $record;
                }
            }
        }
        $records = $new_records;
        #echo ArrayToStr($records);
        
        
        # CREATE EVENT ARRAY FOR CALENDAR
        # ==============================================
        $events = '';
        foreach ($records as $record) {
            $id     = $record['sessions_id'];
            $name   = "NAME";
            $title  = "{$record['time_user_start_12hr']} - {$record['time_user_end_12hr']}";
            $start  = $record['date_user_start'];
            $end    = $record['time_user_end'];
            
            $arr    = "class=Sessions_Signup;v1={$id}";
            $eq     = EncryptQuery($arr);
            $link   = "{$this->Session_Signup_Link};v1={$id}";
            
            $class  = 'instructor_session_class';
            
            $events .= "$id|$eq|$title|$start|$end|$class|*Z";
        }
        $EVENT_ARRAY = $this->MakeEventArray($events);
        
        
        # ADD SCRIPT FOR CALENDAR
        # ==============================================
        $script = <<<SCRIPT
            var date = new Date();
            var d = date.getDate();
            var m = date.getMonth();
            var y = date.getFullYear();
            
            $('#calendar').fullCalendar({
                theme: true,
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'basicDay, basicWeek, month'
                },
                eventClick: function(calEvent, jsEvent, view) {
                    var eq          = calEvent.eq;
                    var eq_title    = calEvent.eq_title;
                    var link        = getClassExecuteLinkNoAjax(eq);
                    top.parent.appformCreateOverlay(eq_title, link, 'apps');
                },
                editable: true,
                events: $EVENT_ARRAY
            });

            
            //alert('AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA');
            
            
            $('#FORM_search_timezone_by_instructor').change(function(){
                var whid = '{$this->Existing_Records_Wh_Id}';
                ReLoadInstructorCalendarWithTimezone(whid);
            });

            //$('#FORM_search_timezone_by_instructor').val('America/Los_Angeles');
            $('#FORM_search_timezone_by_instructor').val('{$this->Existing_Records_Timezone}');
            
            
            function ReLoadInstructorCalendarWithTimezone(whid) {
                // ==============================================================================
                // FUNCTION :: Called when searching by instructor and need to see the calendar
                //             based on a different timezone       
                // ==============================================================================
                
                var serialize_array_timezone    = $('#OPTIONS_TIMEZONE_BY_INSTRUCTOR').serialize();
                serialize_array_timezone        = serialize_array_timezone.replaceAll( "&", "|" );
                serialize_array_timezone        = serialize_array_timezone.replaceAll( "=", "~" );
                
                var tz_display          = $('#FORM_search_timezone_by_instructor option:selected').text();
                tz_display              = tz_display.replaceAll( " ", "~" );
                
                var loadUrl         = "{$this->script_location}.php?action=LoadInstructorCalendar&whid=" + whid + "&retpage={$this->Return_Page}" + "&timezoneDisplay=" + tz_display + "&timezone=" + serialize_array_timezone;
                var ajax_load       = '<img src="/office/images/upload.gif" alt="loading..." \/>';
                $("#search_current_instructor_calendar").html(ajax_load).load(loadUrl, function() {});
            }

SCRIPT;
        EchoScript($script);
        
        return $output;
    }
    
    
    
    
    # ========================================================================================
    # FUNCTIONS FOR SEARCHING BY DATE
    # ========================================================================================
    
    public function SearchByDate() 
    {
        $this->MakeYogaTypesWhereFromOptions();
        $this->GetCourses();
        $this->FormatCourses();
        $this->CreateSchedule();
        
        $schedule           = $this->OutputSchedule(true);
        $content_sessions   = "<div id='search_schedule_holder'>{$schedule}</div>";
        $area_sessions      = $content_sessions;
        
        
        $content_instructors    = "<div id='search_instructors_holder'></div>";
        $area_instructors       = $content_instructors;
        
        
        $div_gap = "<div style='width:10px; height:10px;'>&nbsp;</div>";
        
        $output = "
            <div class='col' style='width:300px; z-index:100;'>{$area_sessions}</div>
            <div class='col'>{$div_gap}</div>
            <div class='col' style='width:250px;'>{$area_instructors}</div>
            <div style='clear:both;'></div>
        
        ";
        
        return $output;
    }
    
    public function SearchByDate_MenuLeft() 
    {
        $types = "All|{$this->yoga_type_list}";
        $options_form = OutputForm(array(
            'form||post|OPTIONS_YOGA_TYPES',
            "code|<b>Search by Yoga Style</b><br />",
            "@select||yoga_types_by_date|N||$types",
            'endform',
        ));
        
        
        
        
        //$DEFAULT_LOCAL_TIMEZONE             = 'America/Los_Angeles';
        //$DEFAULT_LOCAL_TIMEZONE_DISPLAY     = 'Pacific Standard Time';
        
        
        //GetAssocArray($table, $key='', $value='', $where='', $key_case='', $joins='', $order='', $limit='')
        
        $timezone_list      = $this->SQL->GetAssocArray($GLOBALS['TABLE_timezones'], 'tz_name', 'tz_display', '`active`=1', '', '', '`time_zones_id` ASC');
        $timezone_types     = Form_AssocArrayToList($timezone_list);
        
        $types = "All|{$this->yoga_type_list}";
        $options_timezone = OutputForm(array(
            'form||post|OPTIONS_TIMEZONE',
            "code|<b>Current Timezone</b><br />",
            "@select||search_timezone|N||$timezone_types",
            'endform',
        ));
        
        
        
        
        
        
        //$btn_instructor = "<a href='{$GLOBALS['LINK_SESSION_SIGNUP_INSTRUCTOR']}'><div class='btn_scheduleByInstructor'>&nbsp;</div></a>";
        $btn_instructor = "<a href='{$GLOBALS['LINK_SESSION_SIGNUP_INSTRUCTOR']}'><div class='buttonImg'><img src='/images/buttons/btn_schedule_instructor_off.png'></div></a>";
        
        $output = "
            <div style='text-align:center;'>
            <center>
                <br />
                    <div style=\"font-size:14px; color:black; font-weight:bold; text-align:center; padding:3px 0; overflow:hidden\">
                        Don't see the session time you want? 
                        <a href=\"mailto:writer@yogalivelink.com\" style=\"color:#AA1149\">Let us know!</a> You can request a specific session time and we'll do our best to make that session time available to you. We'll let you know either way. <br>
                    </div>
                
                <br /><br />
                <div class='orange left_header lowercase'>{$this->SearchByDateLeftText}</div>
                
                <div id='cal_date_search'></div>
                <br /><br />
                <div class='left_content'>
                    {$options_form}
                    <br />
                    {$options_timezone}
                </div>
                <br /><br />
                <div>{$btn_instructor}</div>
                <br />
            </center>
            </div>
            ";
            
        return $output;
    }

    public function LoadInstructorProfileForCalendarSession($WH_ID=0, $SESSION_ID=0) 
    {
        $record = $this->SQL->GetRecord(array(
            'table' => 'instructor_profile',
            'keys'  => '*',
            'where' => "`wh_id`=$WH_ID AND `active`=1",
        ));
        if ($this->Show_Query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        
        $output = '';
        if ($record) {
            $picture    = "<img src='/office/{$record['primary_pictures_id']}'  alt='{$record['first_name']} {$record['last_name']}' border='0' />";
            $name       = "{$record['first_name']} {$record['last_name']}";
            $profile    = ''; //$record['profile'];
            $wh_id      = ($this->Show_Instructor_WHID) ? "<br />{$record['wh_id']}" : '';
            
            $link_book      = getClassExecuteLinkNoAjax(EncryptQuery("class=Sessions_Signup;v1={$SESSION_ID}"));
            $link_profile   = getClassExecuteLinkNoAjax(EncryptQuery("class=Website_InstructorProfile;v1={$WH_ID}"));
            
            $sessions_id    = ($this->Show_Sessions_Id) ? "<div>Sessions ID: $SESSION_ID</div>" : '';
            
            /*
            $display_a      = "<div style='padding-top:10px; color:#bbb;'>NO SESSIONS AVAILABLE</div>";
            $classes        = 'search_zones search_timesection_none';
            $status         = '';
            $recordID       = '';
            */
            
            # output the actual calendar section
            # ============================================
            $output = "
            <div class='search_instructor_zones search_zone_selected'>
            <div style='padding-top:2px;' class='search_timesection_content'>
            <div class='search_timesection_content_1'>
                <table cellpadding='0' cellspacing='5' border='0'>
                <tr>
                    <td valign='bottom' class='search_current_instructor_profile_picture'>
                        <div class='instructor_holder'>{$picture}</div>
                    </td>
                    <td valign='top'>
                        <div class='s__earch_current_instructor_profile_name white'>{$name}{$wh_id}</div>
                        <div class='s__earch_current_instructor_profile_profile white'>{$profile}</div>
                        <br />
                        <div><a href='#' onclick=\"OpenOverlayFromLink('Instructor Profile', '{$link_profile}'); return false;\" class='link_arrow'>View Profile</a></div>
                        <div><a href='#' onclick=\"OpenOverlayFromLink('Book Session', '{$link_book}'); return false;\" class='link_arrow'>Book This Session</a></div>
                        {$sessions_id}
                    </td>
                </tr>
                </table>
            </div>
            </div>
            </div>
            ";
            
            ###$output .= ArrayToStr($record);
        }
        return $output;
    }

    
    


    public function AddScript()
    {
        $script_location = $this->script_location;
        
        $script = "
            $('#FORM_yoga_types').change(function(){
                ChangeInstructorsByYogaType();
            });
            
            $('#FORM_yoga_types_by_date').change(function(){
                GetReservationsAjax();
            });
            
            $('#FORM_search_timezone').change(function(){
                GetReservationsAjax();
            });
            
            $('#FORM_yoga_types').val('All');
            $('#FORM_yoga_types_by_date').val('All');
            $('#FORM_search_timezone').val('{$this->Existing_Records_Timezone}');
            ";
        if(isset($_GET['style']) && $_GET['style'] == "therapy"){
            $script .= "$('#FORM_yoga_types_by_date').val('Yoga Therapy');";
        }
        $script .= "
            GetReservationsAjax();
            ChangeInstructorsByYogaType();
            
            InitializeOnReady_Sessions_Search();
            ";
        AddScriptOnReady($script);
        
        
        $script = <<<SCRIPT
        
            function InitializeOnReady_Sessions_Search() {
                $.ajaxSetup ({
                    cache: false
                });
                
                $("#cal_date_search").change(function(){	
                    GetReservationsAjax();
                });
                
                $("#cal_date_search").datepicker({
                    dateFormat:     'yy-mm-dd',
                    altField:       '#display_date',
                    altFormat:      'DD, MM d, yy',
                    changeMonth:    true,
                    changeYear:     true,
                    minDate:        "{$this->Cal_Date_Start}", 
                    maxDate:        "{$this->Cal_Date_End}"
                });
            }
        
            // Replaces all instances of the given substring.
            String.prototype.replaceAll = function(
                strTarget, // The substring you want to replace
                strSubString // The string you want to replace in.
            ){
                var strText = this;
                var intIndexOfMatch = strText.indexOf( strTarget );
                
                // Keep looping while an instance of the target string
                // still exists in the string.
                while (intIndexOfMatch != -1){
                    // Relace out the current instance.
                    strText = strText.replace( strTarget, strSubString )
                    
                    // Get the index of any next matching substring.
                    intIndexOfMatch = strText.indexOf( strTarget );
                }
                
                // Return the updated string with ALL the target strings
                // replaced out with the new substring.
                return( strText );
            }

function followLink(link) {
    window.location = link;
}

function OpenOverlayFromLink(title, link) {
    top.parent.appformCreateOverlay(title, link, 'apps');
    return false;
}

function ChangeInstructorsByYogaType() {
    // ==============================================================================
    // FUNCTION :: Change drop-down to search by different yoga type
    // ==============================================================================
    
    // Change the classes to show or hide instructor
    var tClass = $('#FORM_yoga_types').val();
    $('#list_all_instructors li').each(function(index) {
        if ($(this).hasClass(tClass)) {
            $(this).css('display', '');
        } else {
            $(this).css('display', 'none');
        }
    });
    
    // Update notice to user of search type
    if (tClass == 'All' || tClass == 'START_SELECT_VALUE') {
        var tString = '';
    } else {
        var tString = '<h1>'+tClass+' Instructors:</h1>';
    }
    $('#search_current_instructor_list_notice').html(tString);
    
}



function ViewAllInstructors() {
    // ==============================================================================
    // FUNCTION :: Clicking to view all instructors
    // ==============================================================================
    
    // REMOVE THE CALENDAR CONTENT
    $("#search_current_instructor_calendar").fadeOut('slow', function() {
        $(this).html('').fadeIn('fast');
    });
    
    // MAKE INSTRUCTORS VISIBLE AGAIN - not A RELOAD OF DATA
    $("#search_current_instructor_list").css('display', '');
    
    ShowSearchArea();
}



function ClearSelectedInstructor() {
    // ==============================================================================
    // FUNCTION :: Clicking to remove the selected instructor
    // ==============================================================================
    
    $("#search_current_instructor_profile").fadeOut('slow', function() {
        $(this).html('').fadeIn('fast');
    });
    
    ClearAllSelectedInstructor();
    ViewAllInstructors();
    ShowSearchArea();
}
        
        
            function GetReservationsAjax() {
            //alert('GetReservationsAjax');
                // ==============================================================================
                // FUNCTION :: Called to get all sessions offered on a particular day
                // ==============================================================================
                
                var serialize_array_type        = $('#OPTIONS_YOGA_TYPES').serialize();
                serialize_array_type            = serialize_array_type.replaceAll( "&", "|" );
                serialize_array_type            = serialize_array_type.replaceAll( "=", "~" );
                
                var serialize_array_timezone    = $('#OPTIONS_TIMEZONE').serialize();
                serialize_array_timezone        = serialize_array_timezone.replaceAll( "&", "|" );
                serialize_array_timezone        = serialize_array_timezone.replaceAll( "=", "~" );
                
                var tz_display          = $('#FORM_search_timezone option:selected').text();
                tz_display              = tz_display.replaceAll( " ", "~" );
                
                var date                = $('#cal_date_search').val();
                var loadUrl             = "{$script_location}.php?action=LoadExistingRecords&date=" + date + "&timezoneDisplay=" + tz_display + "&formOptions=" + serialize_array_type + "&timezone=" + serialize_array_timezone;
                var ajax_load           = '<img src="/office/images/upload.gif" alt="loading..." \/>';
                
                $("#search_schedule_holder").html(ajax_load).load(loadUrl);
                $("#search_instructors_holder").html('');
                $("#class_info").empty();
                
                //$('#instructors_active_selected_holder').css({width: '50px'});
            }

            function ShowCourseListing(divID, instructors_list) {
            //alert('ShowCourseListing');
                // ==============================================================================
                // FUNCTION :: Called when clicking on a group of sessions - showing in calendar
                // ==============================================================================
                
                // 1. CLEAR ALL THE SPECIAL DIV CLASSES - DO AS A LOOP
                $('.search_zone_selected').each(function(index) {
                    $(this).removeClass('search_zone_selected').addClass('zone_existing_data');
                });
                
                
                // 2. MODIFY THE CLASS OF THE SELECTED DIV
                $('#' + divID).addClass('search_zone_selected').removeClass('zone_existing_data');
                //$('#time_0000').addClass('zone_selected');
                
                
                // 3. CALL THE INSTRUCTOR LIST
                //SetInstructorPictures(instructors_list, divID);
                SetInstructorSessionsFromCalendarTime(instructors_list, divID);
                
                
                //var courses         = $("#courses_"+divID).html();
                //var ajax_load       = '<img src="/office/images/upload.gif" alt="loading..." \/>';
                //$("#class_info").html(ajax_load).html(courses);
            }

            function GetCoursesForInstructor(instructor_id) {
                // ==============================================================================
                // FUNCTION :: Called when a single instructor's profile has been clicked
                // ==============================================================================
                var date            = $('#cal_date_search').val();
                var loadUrl         = "{$script_location}.php?action=LoadExistingRecordsForInstructor&instructor_id=" + instructor_id + "&date=" + date + "&retpage={$this->Return_Page}";
                var ajax_load       = '<img src="/office/images/upload.gif" alt="loading..." \/>';
                $("#search_schedule_holder").html(ajax_load).load(loadUrl, function() {
                    SetInstructorActivePicture(instructor_id);
                });
                $("#class_info").empty();
            }

            
function LoadInstructorProfile(whid) {
    // ==============================================================================
    // FUNCTION :: Called when searching by instructor and clicking on a specific 
    //             instructor's profile. Will cause the profile to be loaded.             
    // ==============================================================================
    
    var loadUrl         = "{$script_location}.php?action=LoadInstructorProfile&whid=" + whid + "&retpage={$this->Return_Page}";
    var ajax_load       = '<img src="/office/images/upload.gif" alt="loading..." \/>';
    $("#search_current_instructor_profile").html(ajax_load).load(loadUrl, function() {
        //ShowCourseListing(divID, instructors_list);
    });
    
    LoadInstructorCalendar(whid);
    ClearAllSelectedInstructor();
    HideSearchArea();
    
    $("#instructor_" + whid).removeClass('instructor_not_selected').addClass('instructor_selected');
}

function HideSearchArea() {
    // ==============================================================================
    // FUNCTION :: Hides the search drop-down when viewing an instructor profile.          
    // ==============================================================================
    $('#search_options_menu_left').css({display: 'none'});
}

function ShowSearchArea() {
    // ==============================================================================
    // FUNCTION :: Hides the search drop-down when viewing an instructor profile.          
    // ==============================================================================
    $('#search_options_menu_left').css({display: ''});
}

function ClearAllSelectedInstructor() {
    // REMOVE CLASS FOR SELECTED INSTRUCTOR - RUN ON ALL
    // =============================================================================
    $('#list_all_instructors li').each(function(index) {
        $(this).children('.instructor_holder').removeClass('instructor_selected').addClass('instructor_not_selected');
    });
}



function LoadInstructorCalendar(whid) {
    // ==============================================================================
    // FUNCTION :: Called when clicking on an instructor to see their calendar
    //             of availability.
    // ==============================================================================
    
    // HIDE INSTRUCTORS LIST
    $("#search_current_instructor_list").css('display', 'none');
    
    var loadUrl         = "{$script_location}.php?action=LoadInstructorCalendar&whid=" + whid + "&retpage={$this->Return_Page}";
    var ajax_load       = '<img src="/office/images/upload.gif" alt="loading..." \/>';
    $("#search_current_instructor_calendar").html(ajax_load).load(loadUrl, function() {
        //ShowCourseListing(divID, instructors_list);
    });
}

            
            function HandleClickingInstructorShowAll(divID, instructors_list) {
                // ==============================================================================
                // FUNCTION :: Called when looking at a particular instructor's sessions 
                //             and now wanting to go back and view all sessions on that time.
                // ==============================================================================
                
                var date            = $('#cal_date_search').val();
                var loadUrl         = "{$script_location}.php?action=LoadExistingRecords&date=" + date + "&retpage={$this->Return_Page}";
                var ajax_load       = '<img src="/office/images/upload.gif" alt="loading..." \/>';
                $("#search_schedule_holder").html(ajax_load).load(loadUrl, function() {
                    ShowCourseListing(divID, instructors_list);
                });
                $("#class_info").empty();
                
                //$('#instructors_active_selected_holder').css({width: '50px'});
            }

function SetInstructorSessionsFromCalendarTime(instructors_list, divID) {
    // ==============================================================================
    // FUNCTION :: Create list of active instructors for the given time period
    // ==============================================================================

    //var instructors = instructors_list.split("|");
    
    var loadUrl         = "{$script_location}.php?action=LoadInstructors&whid_sessions=" + instructors_list + "&retpage={$this->Return_Page}";
    var ajax_load       = '<img src="/office/images/upload.gif" alt="loading..." \/>';
    $("#search_instructors_holder").html(ajax_load).load(loadUrl, function() {
        
    });
}


            function SetInstructorPictures(instructors_list, divID) {
            //alert('SetInstructorPictures');
                // ==============================================================================
                // FUNCTION :: Create list of active instructors for the given time period
                // ==============================================================================
                
                var instructors = instructors_list.split("|");
                
                
                // 1. MOVE ALL IMAGES BACK TO THE INACTIVE LIST
                // =============================================================================
                $('#instructors_active_target li').each(function(index) {
                    $(this).appendTo('#instructors_inactive_target');
                });
                
                $('#instructors_active_selected_target li').each(function(index) {
                    $(this).appendTo('#instructors_inactive_target');
                });
                
                $('#instructors_active_target').html('');
                $('#instructors_active_selected_target').html('');
                
                
                // 2. MOVE ACTIVE IMAGES UP TO ACTIVE LIST
                // =============================================================================
                for (i=0; i<instructors.length; i++) {
                    //$("#picture_" + instructors[i] + "_li").appendTo('#instructors_active_target').show("slide", {direction: "up"}, "1000");
                    $("#picture_" + instructors[i] + "_li").appendTo('#instructors_active_target').show();
                }
                
                
                // 3. SETUP THE ACTIVE INSTRUCTOR BOX
                // =============================================================================
                var newData = '<a href="#" onclick="HandleClickingInstructorShowAll(\'' + divID + '\', \'' + instructors_list + '\');">VIEW ALL<\/a>';
                $('#instructors_active_selected_holder > .backLink').html('').append(newData);
                $('#instructors_active_selected_holder').hide();
                
            }

            function SetInstructorActivePicture(instructor_id) {
                // ==============================================================================
                // FUNCTION :: Show the currently selected instructor
                // ==============================================================================
                
                // MOVE PREVIOUSLY SELECTED INSTRUCTOR OUT OF LIST
                // =============================================================================
                $('#instructors_active_selected_target li').each(function(index) {
                    $(this).appendTo('#instructors_active_target'); //.removeClass('picture_selected')
                });
                $('#instructors_active_selected_target').html('');
                
                
                $("#picture_" + instructor_id + "_li").appendTo('#instructors_active_selected_target').show("slide", {direction: "up"}, "1000");
                //$('#picture_'+instructor_id).addClass('picture_selected');
                
                
                $('#instructors_active_selected_holder').show(); //css({width: '100px'});
                
                
            //    $('#instructors_active_target li .picture_holder').each(function(index) {
            //        $(this).removeClass('picture_selected');
            //    });
            }
        
        
        
            function serialize( mixed_value ) {
            var _getType = function( inp ) {
                var type = typeof inp, match;
                var key;
                if (type == 'object' && !inp) {
                    return 'null';
                }
                if (type == "object") {
                    if (!inp.constructor) {
                        return 'object';
                    }
                    var cons = inp.constructor.toString();
                    match = cons.match(/(\w+)\(/);
                    if (match) {
                        cons = match[1].toLowerCase();
                    }
                    var types = ["boolean", "number", "string", "array"];
                    for (key in types) {
                        if (cons == types[key]) {
                            type = types[key];
                            break;
                        }
                    }
                }
                return type;
            };
            var type = _getType(mixed_value);
            var val, ktype = '';
            
            switch (type) {
                case "function": 
                    val = ""; 
                    break;
                case "undefined":
                    val = "N";
                    break;
                case "boolean":
                    val = "b:" + (mixed_value ? "1" : "0");
                    break;
                case "number":
                    val = (Math.round(mixed_value) == mixed_value ? "i" : "d") + ":" + mixed_value;
                    break;
                case "string":
                    val = "s:" + mixed_value.length + ":\"" + mixed_value + "\"";
                    break;
                case "array":
                case "object":
                    val = "a";
                    var count = 0;
                    var vals = "";
                    var okey;
                    var key;
                    for (key in mixed_value) {
                        ktype = _getType(mixed_value[key]);
                        if (ktype == "function") { 
                            continue; 
                        }
                        
                        okey = (key.match(/^[0-9]+$/) ? parseInt(key, 10) : key);
                        vals += serialize(okey) +
                                serialize(mixed_value[key]);
                        count++;
                    }
                    val += ":" + count + ":{" + vals + "}";
                    break;
            }
            if (type != "object" && type != "array") {
              val += ";";
          }
            return val;
        }
SCRIPT;
        AddScript($script);
        
        
        # SCRIPT INCLUDES
        # ======================================================================
        AddScriptInclude('/jslib/jquery.livequery.js');
        AddScriptInclude('/jslib/jquery.fullcalendar.min.js');
        AddScriptInclude('/jslib/gcal.min.js');
        
        
        # STYLESHEET INCLUDES
        # ======================================================================
        AddStylesheet('/office/css/fullcalendar.css');
        AddStylesheet('/office/css/fullcalendar_redmond_theme.css');
        
        addStyle ("
            #calendar {
                width: 600px;
                margin: 0 auto;
                z-index: 1;
            }
            .key_title {
                padding:2px;
                font-weight:bold;
                border:1px solid #000;
            }
            .instructor_session_class, .instructor_session_class a {
                background-color: #fff;
                color: #000;
            }
            .instructor_session_class_booked, .instructor_session_class_booked a {
                background-color: #009900;
                color: #000;
            }
            .instructor_session_class_locked, .instructor_session_class_locked a {
                background-color: #990000;
                color: #fff;
            }
        ");
    }
    
    
    public function MakeEventArray($EVENTLIST, $SEPERATOR='|', $TERMINATOR='*Z') 
    {
        $event_array = array();
        $count = 0;
        $lines = explode($TERMINATOR, $EVENTLIST);
        $line_count = count($lines);
        foreach ($lines AS $line) {
            $count++;
            if ($count < $line_count) {
                $parts  = explode($SEPERATOR, $line);
                
                $id         = trim($parts['0']);
                $eq         = trim($parts['1']);
                #$link       = trim($parts['1']);
                $title      = trim($parts['2']);
                $start      = trim($parts['3']);
                $end        = trim($parts['4']);
                $classes    = trim($parts['5']);
                
                $item_array = array(
                    'id'        => $id,
                    'eq'        => $eq,
                    'eq_title'  => 'Book Session',
                    //'link'      => "$link",
                    'title'     => "$title",
                    'start'     => "$start",
                    'end'       => "$end",
                    'className' => "$classes"
                    );
                $event_array[$count-1] = $item_array;
            }
        }
        
        $output = json_encode($event_array);
        return $output;
    }
    
    
    
    
    # ========================================================================================
    # FUNCTIONS FOR MAKING FAKE DATA
    # ========================================================================================
    
    public function GetFakeRecordsFromDatabase($NUM_RECORDS=1, $TIMES='')
    {
        global $USER_LOCAL_TIMEZONE;
        
        $number_sessions_per_time_section = $NUM_RECORDS;
        
        $records            = array();
        $instructor_list    = array('900001','900002','900003','900004','900005','900006','900007','900008','900009','900010','900011','900012','900013','900014','900015','900016','900017','900018','900019','900020');
        $timezone           = $USER_LOCAL_TIMEZONE;
        
        $date               = ($this->Existing_Records_Date) ? $this->Existing_Records_Date : date('Y-m-d');
        
        $times              = $TIMES;
        $time_list          = explode('|', $times);
        
        $count = 0;
        foreach ($time_list as $time) {
            # CONVERT THE GIVEN TIME TO UTC
            # ==============================================================
            $time_start     = str_pad($time, 4, "0", STR_PAD_LEFT);
            $time_end       = str_pad(($time + 100), 4, "0", STR_PAD_LEFT);
            
            
            $input_date_time        = "{$date} {$time_start}";
            $input_timezone         = $USER_LOCAL_TIMEZONE;
            $output_timezone        = 'UTC';
            $output_format          = 'Y-m-d H:i:s';
            $utc_start_datetime    = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
            
            $input_date_time        = "{$date} {$time_end}";
            $input_timezone         = $USER_LOCAL_TIMEZONE;
            $output_timezone        = 'UTC';
            $output_format          = 'Y-m-d H:i:s';
            $utc_end_datetime       = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
            
            
            for ($n=0; $n<$number_sessions_per_time_section; $n++)
            {
                $id = ($count > count($instructor_list)-1) ? ($count - count($instructor_list)) : $count;
                
                $sessions_id        = rand(1,100);
                $instructor_id      = $instructor_list[$id];
                $date_user          = $date;
                $time_user_start    = $time_start;
                $time_user_end      = $time_end;
                $utc_start_datetime = $utc_start_datetime;
                $utc_end_datetime   = $utc_end_datetime;
                
                if ($this->Existing_Records_Wh_Id) {
                    if($this->Existing_Records_Wh_Id == $instructor_id) {
                        $add_record = true;
                    } else {
                        $add_record = false;
                    }
                } else {
                    $add_record = true;
                }
                
                $arr = array(
                    'sessions_id'           => $sessions_id,
                    'session_types_id'      => null,
                    'credits_cost'          => 10,
                    'title'                 => null,
                    'description'           => null,
                    'instructor_id'         => $instructor_id,
                    'date'                  => $date_user,
                    'start_datetime'        => $time_user_start,
                    'end_datetime'          => $time_user_end,
                    'time_user_start'       => $time_user_start,
                    'time_user_end'         => $time_user_end,
                    'timezone'              => $timezone,
                    'utc_start_datetime'    => $utc_start_datetime,
                    'utc_end_datetime'      => $utc_end_datetime,
                    'notes'                 => '',
                    'display_on_website'    => 1,
                    'booked'                => 0,
                    'locked'                => 0,
                    'active'                => 1,
                );
                
                if ($add_record) {
                    $records[] = $arr;
                }
                
                $count++;
                
            } // end the looping through number of records per time segment
        } // end foreach time
        
        return $records;
    }
    
    public function GetFakeRecordsForInstructor($WH_ID=0, $NUM_RECORDS=1, $TIMES='', $DATES='')
    {
        global $USER_LOCAL_TIMEZONE;
        
        $records            = array();
        $timezone           = $USER_LOCAL_TIMEZONE;
        $instructor_id      = $WH_ID;
        $dates              = $DATES;
        $date_list          = explode('|', $dates);
        $times              = $TIMES;
        $time_list          = explode('|', $times);
        
        
        foreach ($date_list as $date) {
            
            $used_times = array();
            for ($i=0; $i<$NUM_RECORDS; $i++) {
            
                do {
                    $r = rand (0,count($time_list));
                    $time = $time_list[$r];
                } while (in_array($time, $used_times));
                $used_times[] = $time;
                
                
                # CONVERT THE GIVEN TIME TO UTC
                # ==============================================================
                $time_start     = str_pad($time, 4, "0", STR_PAD_LEFT);
                $time_end       = str_pad(($time + 100), 4, "0", STR_PAD_LEFT);
                
                
                $input_date_time        = "{$date} {$time_start}";
                $input_timezone         = $USER_LOCAL_TIMEZONE;
                $output_timezone        = 'UTC';
                $output_format          = 'Y-m-d H:i:s';
                $utc_start_datetime    = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
                
                $input_date_time        = "{$date} {$time_end}";
                $input_timezone         = $USER_LOCAL_TIMEZONE;
                $output_timezone        = 'UTC';
                $output_format          = 'Y-m-d H:i:s';
                $utc_end_datetime       = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
                
                
                $sessions_id            = rand(1,100);
                $date_user              = $date;
                $time_user_start        = $time_start;
                $time_user_end          = $time_end;
                $utc_start_datetime     = $utc_start_datetime;
                $utc_end_datetime       = $utc_end_datetime;
                
                $arr = array(
                    'sessions_id'           => $sessions_id,
                    'session_types_id'      => null,
                    'credits_cost'          => 10,
                    'title'                 => null,
                    'description'           => null,
                    'instructor_id'         => $instructor_id,
                    'date'                  => $date_user,
                    'start_datetime'        => $time_user_start,
                    'end_datetime'          => $time_user_end,
                    'timezone'              => $timezone,
                    'utc_start_datetime'    => $utc_start_datetime,
                    'utc_end_datetime'      => $utc_end_datetime,
                    'notes'                 => '',
                    'display_on_website'    => 1,
                    'booked'                => 0,
                    'locked'                => 0,
                    'active'                => 1,
                );
                
                $records[] = $arr;
                
            } // end foreach time
        } // end foreach date
        
        return $records;
    }
    
    
    
    
    
}  // -------------- END CLASS --------------