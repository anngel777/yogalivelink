<?php
class Sessions_Search extends BaseClass
{
    public $Use_Fake_Course_Data        = true;

    public $Session_Signup_Link         = '/office/sessions/signup';
    public $script_location             = "/office/AJAX/sessions/search";
    
    public $Show_Query                  = false;
    public $IsTesting                   = true;
    public $ShowArray                   = false;
    
    
    public $Existing_Records             = array();
    public $Existing_Records_Date       = '';
    public $Existing_Records_Wh_Id      = '';
    public $FormattedRecords            = array();
    public $instructor_picture_list     = '';
    public $instructor_active_picture   = '';
    
    public $processing_records          = array();
    public $processing_records_date     = '';
    public $processing_records_wh_id    = '';
    
    public $Table_Sessions              = 'sessions';
    public $Table_Instructors           = 'instructor_profile';
    
    
    public $img_no                      = '/office/images/buttons/cancel.png';
    public $img_yes                     = '/office/images/buttons/save.png';
    
    
    public $rowData                     = '';
    public $courseDetailsData           = '';
    public $Yoga_Types_Where            = '';
    public $SuccessRedirectChatPage     = 'chat_user';
    
    public $OBJ_TIMEZONE                = null;
    
    public function  __construct()
    {
        parent::__construct();
        $this->AddScript();
        
        $this->OBJ_TIMEZONE = new General_TimezoneConversion();
    } // -------------- END __construct --------------

    
    public function MakeYogaTypesWhereFromOptions($options)
    {
        if(!$options) return false;
        
        $query = '';
        
        $list = explode('|', $options);
        foreach ($list as $item) {
            $parts = explode('~', $item);
            $query .= " `yoga_types` LIKE \"%{$parts[1]}%\" OR ";
        }
        $query = substr($query, 0, -4);
        
        $this->Yoga_Types_Where = "({$query})";
    }
    
    public function AjaxHandle()
    {
        $action = Get('action');
        switch ($action) {
        
            case 'LoadExistingRecords':
                $this->Existing_Records_Wh_Id   = Get('instructor_id');
                $this->Existing_Records_Date    = Get('date');
                
                $options = Get('formOptions');
                $this->MakeYogaTypesWhereFromOptions($options);
                
                $this->GetCourses();
                $this->FormatCourses();
                $this->CreateSchedule();
                $this->OutputSchedule();
                $this->OutputInstructorPictureScript();
            break;
            
            case 'LoadExistingRecordsForInstructor':
                $this->Existing_Records_Wh_Id   = Get('instructor_id');
                $this->Existing_Records_Date    = Get('date');
                
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
        global $USER_LOCAL_TIMEZONE;
        
        if ($this->Use_Fake_Course_Data) {
            $records = $this->GetFakeRecordsFromDatabase(3, '0200|0400|0600|0800|1000|1200|1400|1600|1800|2000|2200');
            #echo ArrayToStr($records) . "<br /><hr><br />";
        } else {
        
            # CONVERT DATETIME FROM USER LOCAL TO UTC - FOR DATABASE SEARCH
            # ======================================================================
            $search_date    = ($this->Existing_Records_Date) ? $this->Existing_Records_Date : date('Y-m-d');
            
            $input_date_time        = "{$search_date} 0000";
            $input_timezone         = $USER_LOCAL_TIMEZONE;
            $output_timezone        = 'UTC';
            $output_format          = 'Y-m-d H:i:s';
            $utc_start_datetime     = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
            
            
            $input_date_time        = "{$search_date} 2300";
            $input_timezone         = $USER_LOCAL_TIMEZONE;
            $output_timezone        = 'UTC';
            $output_format          = 'Y-m-d Hi';
            $utc_end_datetime       = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
            
            
            $where_date             = "(`utc_start_datetime`>='{$utc_start_datetime}' AND `utc_end_datetime` <= '{$utc_end_datetime}') AND ";
            $where_whid             = ($this->Existing_Records_Wh_Id) ? "`instructor_id`='{$this->Existing_Records_Wh_Id}' AND " : "";
            $where_yoga_types       = ($this->Yoga_Types_Where) ? "{$this->Yoga_Types_Where} AND " : '';
            $where_booked           = (true) ? "`$this->Table_Sessions`.`booked` = 0 AND " : '';
            $where_locked           = (true) ? "`$this->Table_Sessions`.`locked` = 0 AND " : '';
            $where_show_website     = (true) ? "`$this->Table_Sessions`.`display_on_website` = 1 AND " : '';
            $where_active           = (true) ? "`$this->Table_Sessions`.`active`=1" : '';
            $keys                   = ($where_yoga_types) ? "$this->Table_Sessions.*, $this->Table_Instructors.yoga_types" : '*';
            $joins                  = ($where_yoga_types) ? "LEFT JOIN $this->Table_Instructors ON $this->Table_Instructors.wh_id = $this->Table_Sessions.instructor_id" : '';
            
            
            $records = $this->SQL->GetArrayAll(array(
                'table'     => $this->Table_Sessions,
                'keys'      => $keys,
                'where'     => "$where_whid $where_date $where_yoga_types $where_show_website $where_booked $where_locked $where_active",
                'joins'     => $joins,
            ));
            if ($this->Show_Query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        }
        
        
        
        
        $temp_records               = array();
        $instructor_picture_list    = '';
        
        foreach ($records as $record) {
            $instructor_picture_list .= "{$record['instructor_id']}|";
            
            
#echo ArrayToStr($record);
#echo "<hr>";
            
            
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
            
            
            $blust = isset($temp_records[$date_user_start][$time_user_start]) ? count($temp_records[$date_user_start][$time_user_start]) : 0;
            $z = $blust+1;
            
            $temp_records[$date_user_start][$time_user_start][$z]['sessions_id']              = $record['sessions_id'];
            $temp_records[$date_user_start][$time_user_start][$z]['instructor_id']            = $record['instructor_id'];
            $temp_records[$date_user_start][$time_user_start][$z]['utc_start_datetime']       = $record['utc_start_datetime'];
            $temp_records[$date_user_start][$time_user_start][$z]['utc_end_datetime']         = $record['utc_end_datetime'];
            $temp_records[$date_user_start][$time_user_start][$z]['date_user_start']          = $date_user_start;
            $temp_records[$date_user_start][$time_user_start][$z]['date_user_end']            = $date_user_end;
            $temp_records[$date_user_start][$time_user_start][$z]['time_user_start']          = $time_user_start;
            $temp_records[$date_user_start][$time_user_start][$z]['time_user_end']            = $time_user_end;
            
/*
echo "<br />date_user_start ===> $date_user_start";
echo "<br />time_user_start ===> $time_user_start";
echo "<br />date_user_end ===> $date_user_end";
echo "<br />time_user_end ===> $time_user_end";
echo "<hr><hr><br /><br />";
*/
        }
        
        $this->instructor_active_picture    = ($this->Existing_Records_Wh_Id) ? $this->Existing_Records_Wh_Id : '';
        $this->instructor_picture_list      = substr($instructor_picture_list, 0, -1);
        $this->Existing_Records             = $temp_records;
        
#echo ArrayToStr($this->Existing_Records);
	}
    
    public function FormatCourses() 
    {
        // ============================================================================
        // FUNCTION :: FORMAT HOW EACH COURSE WILL BE OUTPUT IN THE CALENDAR VIEW
        // ============================================================================
        
        foreach ($this->Existing_Records as $record_date) {
            foreach ($record_date as $record_time) {
                
                $z = 0;
                foreach ($record_time as $record) {    
                
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
                    
                    $html_short = "
                    <div style='padding:3px; background-color:#f9f9f9;'>
                        <div style='float:left;'>
                        Book Session ==> {$record['ci_time_start']} == {$record['ci_time_start']} == {$record['ci_time_end']}<br />
                        <a href='#' onclick=\"parent.appformCreate('View Instructor Profile', 'instructor_profile/instructor_profile_view;wh_id={$record['instructor_id']}','apps'); return false;\">
                            View Instructor Bio
                        </a> ==> {$record['instructor_id']} ==> {$record['instructor_name']}
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
    }
    
    public function CreateSchedule()
    {
        // ================================================================================================
        // FUNCTION :: Create the calendar schedule - for a given day
        // ================================================================================================
        
        
        $existing_records = $this->FormattedRecords;
        
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
                $course_listing .= "<div id='courses_{$divID}' style='border:2px solid green; padding:3px;'>";
                foreach ($existing_records[$divID] as $record) {
                    $count++;
                    $course_listing .= "<div style='padding:3px;'><div style='border:1px solid #ccc; padding:3px;'>{$record['data_detail']}</div></div>";
                    $instructors_list .= $record['instructor_id'] . '|';
                }
                $course_listing .= "</div><br /><br />";
                $instructors_list = substr($instructors_list, 0, -1);
                
                $count_title    = ($count == 1) ? 'SESSION' : 'SESSIONS';                
                $display_a      = "<div class='search_timesection_content_1'>{$count} {$count_title} AVAILABLE</div><div class='search_timesection_content_2'><a href='#' onclick=\"ShowCourseListing('{$divID}', '{$instructors_list}');\">Click</a> to view instructors</div>";
                $classes        = ($count == 1) ? 'search_zones search_timesection_single' : 'search_zones search_timesection_multiple';
                $status         = 'existing';
                $recordID       = '';
            } else {
                $display_a      = "<div style='padding-top:10px; color:#bbb;'>NO SESSIONS AVAILABLE</div>";
                $classes        = 'search_zones search_timesection_none';
                $status         = '';
                $recordID       = '';
            }
            
            
            
            
            
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
        
        $this->rowData = $row_data;
        $this->courseDetailsData = $course_listing;
    }

    public function CreateScheduleForInstructor()
    {
        // ================================================================================================
        // FUNCTION :: Create the calendar schedule - when a single instructor has been clicked
        // ================================================================================================
        
        
        $existing_records = $this->FormattedRecords;
        
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
            $sessions_id    = $existing_records[$divID][0]['sessions_id'];
            
            if (isset($existing_records[$divID])) {
                $display_a      = "<div style='padding-top:10px;'><div class='search_timesection_content_2'><a href='{$this->Session_Signup_Link};sid={$sessions_id}'>Click to book session</a></div></div>";
                $classes        = ($count == 1) ? 'search_zones search_timesection_single' : 'search_zones search_timesection_multiple';
                $status         = 'existing';
                $recordID       = '';
            } else {
                $display_a      = "<div style='padding-top:10px; color:#bbb;'>NO SESSIONS AVAILABLE</div>";
                $classes        = 'search_zones search_timesection_none';
                $status         = '';
                $recordID       = '';
            }            
            
            
            $display = "
                <div style='float:left; width:60px;' class='search_timesection_time'>{$time}</div>
                <div style='float:left; padding-top:2px;' class='search_timesection_content'>{$display_a}</div>
                <div style='clear:both;'></div>
                ";
            
            # OUTPUT DATA FOR THE MASTER LIST
            # =============================================
            $row_data  .=  "<div id='{$divID}' class='{$classes}' status='{$status}' recordID='{$recordID}' timestart='{$time_start}' timeend='{$time_end}'>{$display}</div>";
            
        }
        
        $this->rowData = $row_data;
        $this->courseDetailsData = $course_listing;
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
    
    
    
    public function OutputInstructorPictureScript()
    {
        if ($this->instructor_picture_list) {
            $script = "SetInstructorPictures('{$this->instructor_picture_list}');";
            EchoScript($script);
        }
    }
    

    
    
    public function Execute()
    {
    
        $this->GetCourses();
        $this->FormatCourses();
        $this->CreateSchedule();
        
        $schedule       = $this->OutputSchedule(true);
        $instructors    = $this->GetInstructors(true);
        
        
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
        
        $script = "InitializeOnReady_Sessions_Search();";
        $output .= EchoScript($script);
        
        return $output;
    }









    
    public function OutputSchedule($RETURN=false)
    {
        // ========================================================================================
        // FUNCTION :: CREATE THE ENTIRE SCHEDULE CALENDAR THAT WILL BE SHOWN ON SCREEN
        // ========================================================================================
        
        
        $date = ($this->Existing_Records_Date) ? $this->Existing_Records_Date : date('Y-m-d');
        $date = $this->datefmt($date, 'yyyy-mm-dd', "l, M j, Y");
        
        
        $OUTPUT = <<<OUTPUT
            <div style='display:none;'>
                <div id="result_send" style='border:1px solid blue;'></div>
                <div id="result_receive" style='border:1px solid green;'></div>
            </div>
            
            <div style="clear:both;"></div>
            
            <div class='search_date_holder'><span class='search_date'>Date:</span> <span class='search_date_date'>{$date}</span></div>
            <div>
            <br />
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
    

    
    
    
    
    
    
    public function GetInstructors($RETURN=false)
    {
        $records = $this->SQL->GetArrayAll(array(
            'table' => 'instructor_profile',
            'keys'  => '*',
            'where' => "`active`=1",
        ));
        if ($this->Show_Query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        
        $width = 50; //100;
        $height = 60; //120;
        
        
        
        


        
        $OUTPUT = '';
        //$OUTPUT = '<ul id="master_instructors_list" class="image-grid">';
        foreach ($records as $record) {
            $OUTPUT .= "
            
            <li data-id='picture_{$record['wh_id']}' id='picture_{$record['wh_id']}_li'>
                <div class='picture_holder picture_inactive' id='picture_{$record['wh_id']}'>
                <div class='picture_wrapper'>
                    <a href='#' onclick=\"GetCoursesForInstructor('{$record['wh_id']}'); return false;\">
                    <img src='/office/{$record['primary_pictures_id']}' width='{$width}px' height='{$height}px' alt='{$record['first_name']} {$record['last_name']}' border='0' />
                    <span style='disp___lay:none;'>{$record['wh_id']}</span>
                    </a>
                </div>
                </div>
            </li>
            
            ";
        }
        //$OUTPUT .= "<div style='clear:both;'></div>";
        //$OUTPUT .= '</ul>';
        
        
        if ($RETURN) {
            return $OUTPUT;
        } else {
            echo $OUTPUT;
        }
    }








    public function AddScript()
    {
        $script_location = $this->script_location;
        $WH_ID = 666;
        $script = <<<SCRIPT
        
        
        
            function InitializeOnReady_Sessions_Search() {

                
                $.ajaxSetup ({
                    cache: false
                });
                
                $("#cal_date_search").change(function(){	
                    GetReservationsAjax();
                });
                
                $("#cal_date_search").datepicker({
                    dateFormat: 'yy-mm-dd',
                    altField: '#display_date',
                    altFormat: 'DD, MM d, yy',
                    changeMonth: true,
                    changeYear: true
                });

                $("form#OPTIONS_YOGA_TYPES INPUT[type='checkbox']").change(function(){	
                    GetReservationsAjax();
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
        
            function GetReservationsAjax() {
                // ==============================================================================
                // FUNCTION :: Called to get all sessions offered on a particular day
                // ==============================================================================
                
                var serialize_array     = $('#OPTIONS_YOGA_TYPES').serialize();
                serialize_array         = serialize_array.replaceAll( "&", "|" );
                serialize_array         = serialize_array.replaceAll( "=", "~" );
                
                var date                = $('#cal_date_search').val();
                var loadUrl             = "{$script_location}.php?action=LoadExistingRecords&date=" + date + "&formOptions=" + serialize_array;
                var ajax_load           = '<img src="/office/images/upload.gif" alt="loading..." \/>';
                
                $("#search_schedule_holder").html(ajax_load).load(loadUrl);
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
                SetInstructorPictures(instructors_list, divID);
                
                
                //var courses         = $("#courses_"+divID).html();
                //var ajax_load       = '<img src="/office/images/upload.gif" alt="loading..." \/>';
                //$("#class_info").html(ajax_load).html(courses);
            }

            function GetCoursesForInstructor(instructor_id) {
                // ==============================================================================
                // FUNCTION :: Called when a single instructor's profile has been clicked
                // ==============================================================================
                var date            = $('#cal_date_search').val();
                var loadUrl         = "{$script_location}.php?action=LoadExistingRecordsForInstructor&instructor_id=" + instructor_id + "&date=" + date;
                var ajax_load       = '<img src="/office/images/upload.gif" alt="loading..." \/>';
                $("#search_schedule_holder").html(ajax_load).load(loadUrl, function() {
                    SetInstructorActivePicture(instructor_id);
                });
                $("#class_info").empty();
            }

            function HandleClickingInstructorShowAll(divID, instructors_list) {
                // ==============================================================================
                // FUNCTION :: Called when looking at a particular instructor's sessions 
                //             and now wanting to go back and view all sessions on that time.
                // ==============================================================================
                
                var date            = $('#cal_date_search').val();
                var loadUrl         = "{$script_location}.php?action=LoadExistingRecords&date=" + date;
                var ajax_load       = '<img src="/office/images/upload.gif" alt="loading..." \/>';
                $("#search_schedule_holder").html(ajax_load).load(loadUrl, function() {
                    ShowCourseListing(divID, instructors_list);
                });
                $("#class_info").empty();
                
                //$('#instructors_active_selected_holder').css({width: '50px'});
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
        AddScriptInclude('/jslib/jquery-ui.js');
        AddScriptInclude('/jslib/jquery.livequery.js');
        AddScriptInclude('/jslib/jquery.quicksand.min.js');
        //AddScriptInclude('/jslib/quicksand-custom.js');
    }
    
    
    
    
    public function GetFakeRecordsFromDatabase($NUM_RECORDS=1, $TIMES)
    {
        global $USER_LOCAL_TIMEZONE;
        
        $number_sessions_per_time_section = $NUM_RECORDS;
        
        $records            = array();
        $instructor_list    = array('900001','900002','900003','900004','900005','900006','900007','900008','900009','900010','900011','900012','900013','900014','900015','900016','900017','900018','900019','900020');
        $timezone           = $USER_LOCAL_TIMEZONE;
        $date               = date('Y-m-d');
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
                $id = ($count > count($instructor_list)) ? ($count - count($instructor_list)) : $count;
                
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
        }
        
        return $records;
    }
    
    
    
}  // -------------- END CLASS --------------