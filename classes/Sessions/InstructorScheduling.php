<?php
class Sessions_InstructorScheduling extends BaseClass
{
    public $Show_Query                  = false;    // TRUE = output the database queries ocurring on this page
    public $Scheduling_Offline          = false;    // TRUE = take system offline - when doing upgrades
    public $Show_Fake_Schedule_Events   = false;    // TRUE = Create fake events with all statuses on the 1st of this month
    
    public $Scheduling_Offline_Msg      = "<div style='border: 2px solid #990000; background-color:#f5f5f5; padding:10px; font-size:14px; color:#990000;'><b>NOTE: </b> WE ARE CURRENTLY UPGRADING THE SCHEDULING SYSTEM - PLEASE DO NOT ADD ANY SESSIONS - CHECK BACK LATER FOR SCHEUDLING</div><br /><br />";
    // Note: if you want to see contents being sent or received search 
    //       and comment out the lines below. And change the variable below
    //
    // COMMENT THIS OUT IF YOU WANT TO SEE SEND && RECEIVE CONTENTS
    // GetReservationsAjax();
    public $Show_Send_Receive           = false;    // TRUE = output AJAX stuff for testing - You also need to comment out a line - search for ##@@##
    
    public $Div_Gap                     = '20px';
    public $Table_Sessions              = 'sessions';
    public $Img_No                      = '/office/images/buttons/cancel.png';
    public $Img_Yes                     = '/office/images/buttons/save.png';
    public $URL_Process_Records         = "/office/AJAX/sessions/instructor_scheduling;action=ProcessRecords";
    public $URL_Load_Existing_Records   = "/office/AJAX/sessions/instructor_scheduling.php?action=LoadExistingRecords";
    
    
    // ---------- NON-MODIFIABLE VARIABLES ----------
    public $WH_ID = 0;
    public $Last_Sessions_Id            = 0;
    public $Existing_Records            = array();
    public $Existing_Records_Date       = '';
    public $Formatted_Records           = array();
    public $Processing_Records          = array();
    public $Processing_Records_Date     = '';
    public $Row_Data                    = '';
    public $Schedule                    = '';
    public $OBJ_TIMEZONE                = null;
    public $Session_Type_Standard       = false;
    public $Session_Type_Therapy        = false;
    
    
    public function  __construct()
    {
        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => 'Richard Witherspoon',
            'Updated'     => '2012-11-19',
            'Version'     => '1.3',
            'Description' => 'Instructor add or modify their schedule on the website. Also customers searching on instructor schedule availability.',
        );
        
        /*
            2012-03-27 -> added functionality to allow instructors to CLICK to add a session
            2012-04-19 -> modified function Instructions() to make content a drop-down - embedded CSS
            2012-11-19 -> modified function ProcessRecords() to correct bug in deleting records
        */
        
        $this->Show_Fake_Schedule_Events = Get('showfake') ? true : false;
        
        $this->WH_ID = $_SESSION['USER_LOGIN']['LOGIN_RECORD']['wh_id'];
        
        
        # SETUP WHICH SESSIONS AN INSTRUCTOR CAN ADD
        //echo "<br /> ===> " . ArrayToStr($_SESSION['USER_LOGIN']['LOGIN_RECORD']);
        $this->Session_Type_Standard = $_SESSION['USER_LOGIN']['LOGIN_RECORD']['type_instructor_standard'];
        $this->Session_Type_Therapy = $_SESSION['USER_LOGIN']['LOGIN_RECORD']['type_instructor_therapy'];
        
        
        $this->OBJ_TIMEZONE = new General_TimezoneConversion();
    } // -------------- END __construct --------------

    
    public function Instructions()
    {
        $OBJ    = new Website_PageContents();
        
        $output = "";
        $output .= "<br /><div id='toggle_instructor_scheduling_instructions' class='div_toggle'>
        <div class='left'>Click to View Instructions</div>
        <div class='right'>+&nbsp;&nbsp;</div>
        <div class='clear'></div>
        </div>
        <br /><br />";
        $output .= "<div id='instructor_scheduling_instructions' style='display:none;'>";
        $output .= $OBJ->GetContentFromIdentifier('##INSTRUCTOR_SCHEDULING_INSTRUCTIONS##');
        $output .= "</div>";
        
        AddScriptOnReady("
            $('#toggle_instructor_scheduling_instructions').click(function () {
              $('#instructor_scheduling_instructions').slideToggle('slow');
            });");
        
        AddStyle("
            .div_toggle {
                font-size:          13px;
                font-weight:        bold;
                font-family:        arial;
                border:1px solid    #000;
                background-color:   #F2935B;
                color:              #fff;
                padding:            3px;
                cursor:             pointer;
            }
            .left   { float:left; }
            .right  { float:right; }
            ");
        
        return $output;
    }
    
    public function AjaxHandle()
    {
        $action = Get('action');
        switch ($action) {
            case 'LoadExistingRecords':
                #$this->WH_ID   = Get('wh_id');
                $this->Existing_Records_Date    = Get('date');
                
                $this->GetCourses();
                $this->FormatCourses();
                $this->CreateHoldingSchedule();
                $this->OutputSchedule();
            break;
            case 'ProcessRecords':
                $this->Processing_Records           = unserialize(Post('contentArray'));
                #$this->WH_ID     = Get('wh_id');
                $this->Processing_Records_Date      = Get('date');
                $this->ProcessRecords();
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
    
    
    public function Execute()
    {
        $this->AddScript();
        $this->AddStyle();
        
        $this->GetCourses();
        $this->FormatCourses();
        $this->CreateHoldingSchedule();
        $this->Schedule = $this->OutputSchedule(true);
        $output = $this->OutputScheduleCreator();
        
        return $output;
    }
    
    
    public function AddBox($title, $content)
    {
        $output = "
        <div class='helpcenter_outter_wrapper'>
            <div class='helpcenter_box_title'>
                {$title}
            </div>
            <div class='helpcenter_box_content'>
                {$content}
            </div>
        </div>";
        
        return $output;
    }
    
    
    public function OutputScheduleCreator()
    {
        # FUNCTION :: Output the HTML for instructors managing their schedules
        
        $content_step_1 = "<div id='cal_date'></div>";
        $content_step_2 = "<div id='elements'>
                           <div id='tbox'><img src='/office/images/btn_drag_schedule.gif' border='0' alt='Course Drag' /></div>
                           </div>";
        
        $content_step_3 = "<input type='button' class='btn_submit' name='btn_submit' value='SAVE ALL RECORDS' /><br /><br />
                           <input type='button' class='btn_reset' name='btn_reset' value='RESET SCHEDULE' />
                           <div style='display:none;'><input type='button' onclick='ReapplyJqueryFunctionalityToObjects();' value='Make Droppable'/></div>";
        
        $content_step_4 = "<div id='schedule_holder'>
                            {$this->Schedule}
                           </div>";
        
        $area_step_1        = $this->AddBox('<div class="red left_content">1. Select a date</div>', $content_step_1);
        $area_step_2        = $this->AddBox('<div class="red left_content">2. Drag and drop "Add Session" <br />into the timeslot you want</div>', $content_step_2);
        $area_step_3        = $this->AddBox('<div class="red left_content">3. Save your changes</div>', $content_step_3);
        $area_step_4        = $this->AddBox('SCHEDULE', $content_step_4);
        
        $schedule_note      = ($this->Scheduling_Offline) ? $this->Scheduling_Offline_Msg : '';
        $div_gap            = "<div style='width:{$this->Div_Gap}; height:{$this->Div_Gap};'>&nbsp;</div>";
        
        $output = "
        <div style='width:600px;'>
            {$schedule_note}
            <div class='col'>
                {$area_step_1}
                {$div_gap}
                {$area_step_2}
                {$div_gap}
                {$area_step_3}
            </div>
            <div class='col'>{$div_gap}</div>
            <div class='col'>                
                {$area_step_4}
            </div>
            <div style='clear:both;'></div>
        </div>
        ";
        
        
        return $output;
    }
    
    
    public function GetFakeCourses()
    {
        # FUNCTION :: Create fake courses for testing
    
        #$dates = '2010-10-20|2010-10-21|2010-10-22|2010-10-23';
        #$dates = '2010-10-202010-10-21|2010-10-22|2010-10-23';
        #$times = '0200|0400|0600|0800';
        
        
        $date_times['2010-10-20'] = '0200|0400|0600|0800';
        $date_times['2010-10-21'] = '0600|0800';
        $date_times['2010-10-22'] = '0200|0300|0400';
        $date_times['2010-10-23'] = '0800|1000';
        $date_times['2010-10-24'] = '1000|1200';
        
        $date = $this->Existing_Records_Date;
        $records = array();
        
        if (isset($date_times[$date])) {
            //$date_list = explode('|', $dates);
            $time_list = explode('|', $date_times[$date]);
            
            //foreach ($date_list as $date) {
                foreach ($time_list as $time) {
                    $records[$date][$time]['ci_cancel_before_time']     = '15min';
                    $records[$date][$time]['ci_time_start']             = $time;
                    $records[$date][$time]['ci_time_end']               = $time + 100;
                }
            //}
        }
        
        $this->Existing_Records = $records;
    }
    
    
	public function GetCourses()
	{
        // ================================================================================
        // FUNCTION :: Get courses for this instructor  - based on UTC date/time
        // ================================================================================
        global $USER_LOCAL_TIMEZONE;   
        
        #$this->GetFakeCourses($WH_ID, $DATE);
        #return;
        
        $WH_ID = $this->WH_ID;
        #$date = $this->Existing_Records_Date;
        #$date = ($date) ? $date : date('Y-m-d');
        
        
        # CONVERT DATETIME FROM USER LOCAL TO UTC - FOR DATABASE SEARCH
        # ======================================================================
        $search_date    = ($this->Existing_Records_Date) ? $this->Existing_Records_Date : date('Y-m-d');
        
        $input_date_time        = "{$search_date} 0000";
        $input_timezone         = $USER_LOCAL_TIMEZONE;
        $output_timezone        = 'UTC';
        $output_format          = 'Y-m-d H:i:s';
        $utc_start_datetime     = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format, false);
        
        $search_date_2          = $this->OBJ_TIMEZONE->ShiftForwardOneDay($search_date, $output_format='Y-m-d');
        $input_date_time        = "{$search_date_2} 0000";
        $input_timezone         = $USER_LOCAL_TIMEZONE;
        $output_timezone        = 'UTC';
        $output_format          = 'Y-m-d H:i:s';
        $utc_end_datetime       = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format, false);
        
        
        $where_date             = "(`utc_start_datetime`>='{$utc_start_datetime}' AND `utc_end_datetime` <= '{$utc_end_datetime}') AND ";
        
        
        $records = $this->SQL->GetArrayAll(array(
            'table' => $GLOBALS['TABLE_sessions'],
            'keys'  => '*',
            'where' => "`instructor_id`='$WH_ID' AND $where_date `active`=1",
        ));
        if ($this->Show_Query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        
        $temp_records = array();
        foreach ($records as $record) {
        
            # CONVERT TIMES TO THE INSTRUCTOR LOCAL TIME
            # ======================================================================
            $input_date_time        = $record['utc_start_datetime'];
            $input_timezone         = 'UTC';
            $output_timezone        = $USER_LOCAL_TIMEZONE;
            $output_format          = 'Y-m-d Hi'; //'Y-m-d H:i:s';
            $user_start_datetime    = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
            $parts                  = explode(' ', $user_start_datetime);
            $date_start_user        = $parts[0];
            $time_start_user        = $parts[1];
            
            $input_date_time        = $record['utc_end_datetime'];
            $input_timezone         = 'UTC';
            $output_timezone        = $USER_LOCAL_TIMEZONE;
            $output_format          = 'Y-m-d Hi';
            $user_end_datetime      = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
            $parts                  = explode(' ', $user_end_datetime);
            $date_end_user          = $parts[0];
            $time_end_user          = $parts[1];
            
            # CREATE THE RECORD ARRAY
            # ======================================================================
            $temp_records[$date_start_user][$time_start_user]['sessions_id']                = $record['sessions_id'];
            $temp_records[$date_start_user][$time_start_user]['ci_cancel_before_time']      = $record['notes'];
            $temp_records[$date_start_user][$time_start_user]['ci_time_start']              = $time_start_user;
            $temp_records[$date_start_user][$time_start_user]['ci_time_end']                = $time_end_user;
            $temp_records[$date_start_user][$time_start_user]['ci_session_type_standard']   = $record['type_standard'];
            $temp_records[$date_start_user][$time_start_user]['ci_session_type_therapy']    = $record['type_therapy'];
            
            $temp_records[$date_start_user][$time_start_user]['booked']                     = $record['booked'];
            $temp_records[$date_start_user][$time_start_user]['locked']                     = $record['locked'];
            
            #echo "<br />date_start_user ===> $date_start_user";
            #echo "<br />time_start_user ===> $time_start_user";
            #echo "<br /><br />";
        }
        
        
        if ($this->Show_Fake_Schedule_Events) {
            $date_fake      = '2011-' . date('m') . '-01';
            $time_fake_1    = 0100;
            $time_fake_2    = 0200;
            
            # make locked record
            $temp_records[$date_fake][$time_fake_1]['sessions_id']                = 1;
            $temp_records[$date_fake][$time_fake_1]['ci_cancel_before_time']      = '';
            $temp_records[$date_fake][$time_fake_1]['ci_time_start']              = '0100';
            $temp_records[$date_fake][$time_fake_1]['ci_time_end']                = '0200';
            $temp_records[$date_fake][$time_fake_1]['booked']                     = 0;
            $temp_records[$date_fake][$time_fake_1]['locked']                     = 1;
            
            # make booked record
            $temp_records[$date_fake][$time_fake_2]['sessions_id']                = 1;
            $temp_records[$date_fake][$time_fake_2]['ci_cancel_before_time']      = '';
            $temp_records[$date_fake][$time_fake_2]['ci_time_start']              = '0200';
            $temp_records[$date_fake][$time_fake_2]['ci_time_end']                = '0300';
            $temp_records[$date_fake][$time_fake_2]['booked']                     = 1;
            $temp_records[$date_fake][$time_fake_2]['locked']                     = 0;
        }
        
        
        $this->Existing_Records = $temp_records;
	}
    
    public function FormatCourses() 
    {
        // ================================================================================
        // FUNCTION :: Format how each sesion time is displayed into schedule
        // ================================================================================
        
        
        foreach ($this->Existing_Records as $record_date) {
            foreach ($record_date as $record_time) {
                
                $parentID               = "time_{$record_time['ci_time_start']}";
                $selectExpireTime       = $record_time['ci_cancel_before_time'];
                $time_start_user        = $record_time['ci_time_start'];
                $time_end_user          = $record_time['ci_time_end'];
                
                
            #$time_start     = $time_base + (($i)*100);
            #$time_end       = $time_base + (($i+1)*100);
            #$time_start     = str_pad($time_start, 4, "0", STR_PAD_LEFT);
            #$time_end       = str_pad($time_end, 4, "0", STR_PAD_LEFT);
            #$time           =  date("g A", strtotime($time_start));
                
                
                $status                 = 'open';
                $status                 = ($record_time['locked'] == 1) ? 'locked' : $status;
                $status                 = ($record_time['booked'] == 1) ? 'booked' : $status;
                $status_class           = "instructor_schedule_session_{$status}";
                
                switch ($status) {
                    case 'open':
                        $status_description     = 'Session is avaiable';
                    break;
                    case 'locked':
                        $status_description     = 'Session is being booked';
                    break;
                    case 'booked':
                        $id         = $record_time['sessions_id'];
                        $arr        = "class=Sessions_Details;v1={$id}";
                        $eq         = EncryptQuery($arr);
                        $link       = getClassExecuteLinkNoAjax($eq);
                        $onclick    = "top.parent.appformCreateOverlay('Session Details', '{$link}', 'apps');";
                        $status_description     = "Session has been booked <br /><a href='#' onClick=\"{$onclick}\">Click here for details</a>";
                    break;
                }
                
                
                $input_date_time        = $time_start_user;
                $output_format          = 'g:i a';
                $time_start_display     = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, 'UTC', 'UTC', $output_format);
                
                $input_date_time        = $time_end_user;
                $output_format          = 'g:i a';
                $time_end_display       = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, 'UTC', 'UTC', $output_format);
                
                $delete_area            = ($status == 'open') ? "<img class='btn_delete' parentID='{$parentID}' src='{$this->Img_No}' alt='yes' border='0' />" : '';
                
                
                
                $input_date_time        = $time_start_user;
                $output_format          = 'g A';
                $time                   = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, 'UTC', 'UTC', $output_format);
                
                
                $session_type_description   = 'Type:';
                $session_type_description  .= ($record_time['ci_session_type_standard']) ? ' [Standard] ' : '';
                $session_type_description  .= ($record_time['ci_session_type_therapy']) ? ' [Therapy] ' : '';
                
                
                $content    = " <div>
                                SESSION TIME
                                <br />
                                {$status_description}
                                <br />
                                {$session_type_description}
                                </div>
                                ";
                
                $this->Formatted_Records[$parentID]['data_content']     = $content;
                $this->Formatted_Records[$parentID]['data_delete']      = $delete_area;
                $this->Formatted_Records[$parentID]['id']               = $record_time['sessions_id'];
                $this->Formatted_Records[$parentID]['class']            = $status_class;
            }
        }
    }
    




    public function CreateHoldingSchedule()
    {
        # FUNCTION :: Create the row data that will be put into a schedule
        
        $existing_records = $this->Formatted_Records;
    
        $time_base  = 0000;
        $row_data   = '';
        for ($i=0; $i<24; $i++) {
            $time_start = $time_base + (($i)*100);
            $time_end   = $time_base + (($i+1)*100);
            
            $time_start = str_pad($time_start, 4, "0", STR_PAD_LEFT);
            $time_end   = str_pad($time_end, 4, "0", STR_PAD_LEFT);
            
            $divID      = "time_{$time_start}";          
            
            $input_date_time        = $time_start;
            $output_format          = 'g:i a';
            $time_start_display     = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, 'UTC', 'UTC', $output_format);
            
            $input_date_time        = $time_end;
            $output_format          = 'g:i a';
            $time_end_display       = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, 'UTC', 'UTC', $output_format);
            
            
            $input_date_time        = $time_start;
            $output_format          = 'g A';
            $time                   = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, 'UTC', 'UTC', $output_format);
            
            if (isset($existing_records[$divID])) {
                $content        = $existing_records[$divID]['data_content'];
                $delete         = $existing_records[$divID]['data_delete'];
                $display        = $this->CreateTimeBox($time, $content, $delete);
                
                $orig_content   = '';
                $orig_time      = $time;
                $classes        = 'zones zone_existing_data';
                $classes       .= " {$existing_records[$divID]['class']}";
                $status         = 'existing';
                $recordID       = $existing_records[$divID]['id'];
            } else {
                $content        = "OPEN TIME <img style='float:right;' src='/office/images/add.png' alt='Click to Add Session' border='0' class='add_session_btn' parentID='{$divID}' width='24' />";
                $display        = $this->CreateTimeBox($time, $content);
                
                $orig_content   = htmlentities(htmlspecialchars($content, ENT_QUOTES));     // encode content for quotes and chevrons
                $orig_time      = $time;
                $classes        = 'zones';
                $status         = '';
                $recordID       = '';
            }
            
            $row_data  .=  "<div id='{$divID}' class='z_zone {$classes} zones_droppable' status='{$status}' recordID='{$recordID}' timestart='{$time_start}' timeend='{$time_end}' originaltime='{$orig_time}' originalcontent='{$orig_content}'>{$display}</div><div style='clear:both;'></div>";
        }
        
        $this->Row_Data = $row_data;
    }
    
    public function CreateTimeBox($TIME='', $CONTENT='', $DELETE='')
    {
        # FUNCTION :: Create a single box with information for schedule
        
        $output = "
        <div style='float:left;' class='search_col_1 search_timesection_time'>{$TIME}</div>
        <div style='float:left; padding-top:2px;' class='search_col_2 search_timesection_content'>{$CONTENT}</div>
        <div style='float:right;' class='search_col_3 search_timesection_delete'>{$DELETE}</div>
        <div style='clear:both;'></div>
        ";
        
        return $output;
    }
    
    public function OutputSchedule($RETURN=false)
    {
        # FUNCTION :: Output HTML for an instructor's schedule via AJAX call
        
        //$date = date("l, M j, Y", $this->Existing_Records_Date);
        
        $date = ($this->Existing_Records_Date) ? $this->Existing_Records_Date : date('Y-m-d');
        $date = $this->datefmt($date, 'yyyy-mm-dd', "l, M j, Y");
        
        $send_receive = ($this->Show_Send_Receive) ? "<div id='result_send' style='border:1px solid blue;'></div><br /><div id='result_receive' style='border:1px solid green;'></div><br />" : '';
        
        
        $OUTPUT = "
            {$send_receive}
            <div style='clear:both;'></div>
            <div style='font-size:16px; font-weight:bold; padding:5px; background-color:#f9f9f9;'>Date: {$date}</div>
            <br />
            <div>
                {$this->Row_Data}
            </div>
            <script type='text/javascript'>ReapplyJqueryFunctionalityToObjects();</script>
            ";
            
        if ($RETURN) {
            return $OUTPUT;
        } else {
            echo $OUTPUT;
        }
    }
    




    public function ProcessRecords()
    {
        # FUNCTION :: Modify, add, or delete an instructor session in teh database - time shifting stuff
        
        global $USER_LOCAL_TIMEZONE;
        
        echo "<br /><br />P ==> ".ArrayToStr($this->Processing_Records);
        
        $date           = $this->Processing_Records_Date;
        $date_start     = $date;
        $date_end       = $date;
        $wh_id          = $this->WH_ID;
        
        foreach ($this->Processing_Records as $record) {
            
            
            echo "<br /><b>RECORD:</b>";
            echo ArrayToStr($record);
            
            //$where      = "`wh_id`='{$wh_id}'";
            
            $where      = "`sessions_id`='{$record['ci_id']}'";
            $action     = $record['ci_status'];
            
            if (($action == 'adding') || ($action == 'editing')) {
                
                # shift day forward
                if ($record['ci_time_end'] == '2400') {
                    $date_end = $this->OBJ_TIMEZONE->ShiftForwardOneDay($date_end);
                    $record['ci_time_end'] = '0000';
                }
                
                //$time_start     = wordwrap($record['ci_time_start'], 2, ":");
                //$time_end       = wordwrap($record['ci_time_end'], 2, ":");
                
                $time_start     = chunk_split($record['ci_time_start'], 2, ":");
                $time_end       = chunk_split($record['ci_time_end'], 2, ":");
                
                $time_start     = substr($time_start,0,5);
                $time_end       = substr($time_end,0,5);
                
                    echo "<br />";
                    echo "<br />USER_LOCAL_TIMEZONE ===> {$USER_LOCAL_TIMEZONE}";
                    echo "<br />date_start ===> {$date_start}";
                    echo "<br />ci_time_start ===> {$record['ci_time_start']}";
                    echo "<br />time_start ===> {$time_start}";
                    echo "<br />date_end ===> {$date_end}";
                    echo "<br />ci_time_end ===> {$record['ci_time_end']}";
                    echo "<br />time_end ===> {$time_end}";
                    echo "<br />";
                
                
                
                
                # CONVERT DATETIME FROM USER LOCAL TO UTC - FOR DATABASE
                # ======================================================================
                $input_date_time        = "{$date_start} {$time_start}";
                $input_timezone         = $USER_LOCAL_TIMEZONE;
                $output_timezone        = 'UTC';
                $output_format          = 'Y-m-d H:i:s';
                $utc_start_datetime     = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format, true);
                
                $input_date_time        = "{$date_end} {$time_end}";
                $input_timezone         = $USER_LOCAL_TIMEZONE;
                $output_timezone        = 'UTC';
                $output_format          = 'Y-m-d H:i:s';
                $utc_end_datetime       = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format, true);
                
                echo "<br />utc_start_datetime ===> {$utc_start_datetime}";
                echo "<br />utc_end_datetime ===> {$utc_end_datetime}";
                echo "<br />";
                
                # HANDLE THE DATABASE ACTIONS
                # ======================================================================
                $db_record = array(            
                    'instructor_id'         => $wh_id,
                    'utc_start_datetime'    => $utc_start_datetime,
                    'utc_end_datetime'      => $utc_end_datetime,
                    'notes'                 => $record['ci_cancel_before_time'],
                    'testing'               => $record['testing'],
                    'type_standard'         => $record['ci_session_type_standard'],
                    'type_therapy'          => $record['ci_session_type_therapy'],
                );
                
                switch($action) {
                    case 'adding':
                        $this->AddRecordLoc($db_record);
                    break;
                    case 'editing':
                        $this->UpdateRecordLoc($db_record, $where);
                    break;
                }
                
            }
            
            if ($action == 'deleting') {
                $this->DeleteRecordLoc($where);
            }
        } // end foreach
    }

    private function AddRecordLoc($db_record) 
    {
        # FUNCTION :: Add record to database - special function
        
        $keys   = '';
        $values = '';            
        foreach ($db_record as $var => $val) {
            $val = addslashes($val);
            
            $keys   .= "`$var`, ";
            $values .= "'$val', ";
        }
        $keys   = substr($keys, 0, -2);
        $values = substr($values, 0, -2);
        
        $result = $this->SQL->AddRecord(array(
            'table'     => $this->Table_Sessions,
            'keys'      => $keys,
            'values'    => $values,
        ));
        $this->Last_Sessions_Id = $this->SQL->Last_Insert_Id;
        if ($this->Show_Query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        if ($result) {
            echo "<br />RECORD ADDED";
        }
    }
    
    private function UpdateRecordLoc($db_record, $where) 
    {
        # FUNCTION :: Update a record in database - special function
    
        $key_values = '';
        foreach ($db_record as $var => $val) {
            $val = addslashes($val);
            $key_values .= "`$var`='$val', ";
        }
        $key_values = substr($key_values, 0, -2);
        
        $result = $this->SQL->UpdateRecord(array(
            'table'         => $this->Table_Sessions,
            'key_values'    => $key_values,
            'where'         => "{$where} AND active=1",
        ));
        if ($this->Show_Query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        if ($result) {
            echo "<br />RECORD UPDATED";
        }
    }

    private function DeleteRecordLoc($where) 
    {
        # FUNCTION :: Delete a record in database - special function
        $result = $this->SQL->UpdateRecord(array(
            'table'         => $this->Table_Sessions,
            'key_values'    => "`active`='0'",
            'where'         => "{$where} AND active=1",
        ));
        if ($this->Show_Query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        if ($result) {
            echo "<br />RECORD DELETED";
        }
    }
    
    public function AddScript()
    {
        
        # SETUP THE JS FOR SELECTING TYPE OF SESSION
        $show_checkbox_options  = ($this->Session_Type_Standard && $this->Session_Type_Therapy) ? true : false;
        $style                  = (!$show_checkbox_options) ? "style='display:none;'" : '';
        $checkbox_standard      = ($this->Session_Type_Standard) ? "<input class='ci_session_type_standard' type='checkbox' value='1' checked />Standard " : '';
        $checkbox_therapy       = ($this->Session_Type_Therapy) ? "<input class='ci_session_type_therapy' type='checkbox' value='1' checked />Therapy " : '';
        $session_type_area      = "<div class='course_info' {$style}>{$checkbox_standard}{$checkbox_therapy}</div>";
        
    
        # SCRIPT
        # ======================================================================
        $SCRIPT = <<<SCRIPT

        $(document).ready(function() {
            
            // DELETE BUTTON FOR A SPECIFIC CLASS HAS BEEN CLICKED
            // ===========================================================================
            $('.btn_delete').livequery('click', function(event) {
                var parentID = "#" + $(this).attr("parentID");
                
                var status = $(parentID).attr("status");
                if (status == 'existing') {
                    $(parentID).attr("status", "deleting");
                }
                if (status == 'adding') {
                    $(parentID).attr("status", "");
                }
                
                $(parentID).removeClass("zone_editing_data zone_adding_data zone_existing_data zone_deleted_data");
                $(parentID).addClass("zone_deleted_data");
                //$(parentID).empty();
                
                var time        = $(parentID).attr("originaltime");
                var content     = $(parentID).attr("originalcontent");
                content         = $('<div />').html(content).text();        // jquery method to decode HTML 
                
                var html = "\
                        <div style='float:left; width:60px;' class='search_timesection_time'>" + time + "</div>\
                        <div style='float:left; padding-top:2px;' class='search_col_2 search_timesection_content'>" + content + "</div>\
                        <div style='clear:both;'></div>\
                        ";
                $(parentID).empty();
                $(html).appendTo($(parentID));
                
            });
            
            
            
            // SUBMIT BUTTON FOR SAVING RECORDS HAS BEEN CLICKED
            // ===========================================================================
            $('.btn_submit').livequery('click', function(event) {
                // LOOP THROUGH FORM
                // GET ALL THE RECORDS
                // FORM RECORD INFO INTO AN ARRAY
                // POST THAT ARRAY VIA AJAX
                
                
                // for each DIV 'zones' with a DIV 'course_info'
                var sendArray = new Array();
                //$('.zones:has(.course_info)').each(function(){
                $('.z_zone').each(function(){
                    var status      = $(this).attr("status");
                    if (status == 'editing' || status == 'adding')
                    {
                        var tempArray       = new Array();
                        tempArray.length    = 0;
                        var thisID          = $(this).attr("id");
                        
                        tempArray['ci_id']                      = $(this).attr("recordID");
                        tempArray['ci_status']                  = $(this).attr("status");
                        tempArray['ci_time_start']              = $(this).attr("timestart");
                        tempArray['ci_time_end']                = $(this).attr("timeend");
                        tempArray['ci_cancel_before_time']      = '';
                        tempArray['ci_session_type_standard']   = $(this).find('.ci_session_type_standard').is(':checked');
                        tempArray['ci_session_type_therapy']    = $(this).find('.ci_session_type_therapy').is(':checked');
                        
                        //alert('ending time => ' + tempArray['ci_time_end']);
                        //alert(tempArray['ci_session_type_standard']);
                        //alert(tempArray['ci_session_type_therapy']);
                        
                        sendArray[thisID] = tempArray;
                    } else if (status == 'deleting') {
                        
                        var tempArray       = new Array();
                        tempArray.length    = 0;
                        var thisID          = $(this).attr("id");
                        
                        tempArray['ci_id']                  = $(this).attr("recordID");
                        tempArray['ci_status']              = $(this).attr("status");
                        tempArray['ci_time_start']          = '';
                        tempArray['ci_time_end']            = '';
                        tempArray['ci_cancel_before_time']  = '';
                        
                        sendArray[thisID] = tempArray;
                    }
                });
                
                //alert(sendArray);
                
                var date                = $('#cal_date').val();
                var serialize_array     = serialize(sendArray);
                serialize_array         = 'contentArray=' + serialize_array;
                var url                 = "{$this->URL_Process_Records};wh_id={$this->WH_ID};date=" + date;
                
                
                
                $('#result_send').html(serialize_array);
                
                $('body').css('cursor','wait');
                $.ajax({
                    type: "POST",
                    url: url,
                    data: serialize_array,
                    dataType: "html",
                    success: function(data) {
                        $('#result_receive').html(data);
                        $('body').css('cursor','auto');
                        
                        // COMMENT THIS OUT IF YOU WANT TO SEE SEND && RECEIVE CONTENTS ##@@##
                        GetReservationsAjax();
                    }
                });
                return false;
                $('body').css('cursor','auto');
            
            });
            
            
            // RESET BUTTON FOR SAVING RECORDS HAS BEEN CLICKED
            // ===========================================================================
            $('.btn_reset').livequery('click', function(event) {
                GetReservationsAjax();
            });
            
            
            
            $.ajaxSetup ({
                cache: false
            });
            
            $("#cal_date").change(function(){	
                GetReservationsAjax();
            });
            
            $("#cal_date").datepicker({
                dateFormat: 'yy-mm-dd',
                altField: '#display_date',
                altFormat: 'DD, MM d, yy',
                changeMonth: true,
                changeYear: true
            });
            
        }); //END ON READY PORTION


        
        // THESE FUNCTIONS NEED TO BE RE-APPLIED ONCE THE PAGE HAS BEEN REFRESHED
        // ===========================================================================
        function ReapplyJqueryFunctionalityToObjects() {
            
            // MAKE THE ADD/EDIT DIV DRAGGABLE
            // ===========================================================================
            $("#tbox").draggable({revert: true});
            
            
            // ZONE IS DROPPED ON TO BECOME ACTIVELY EDITED
            // ===========================================================================
            $(".zones_droppable").droppable({
                hoverClass: 'drophover',
                drop: function(){
                        HandleDropAction($(this));     // note - must be embedded in function or it will called at load since passing vars in ().
                    },
            });
            
            
            // ---------- CLICKING A SESSION ADD IMAGE SO NEED TO CAL FUNCTION AS THOUGH DROPPING ON TIME ----------
            $(".add_session_btn").click(function() {
                var parentID    = $(this).attr("parentID");
                var obj         = $("#" + parentID);
                HandleDropAction(obj);
            });	
            
            
            function HandleDropAction (obj) {
                //$('#tbox').animate({top:'0',left:'0'});
                
                // SET ATTRIBUTES FOR THE BOX AREA
                var status = obj.attr("status");
                
                obj.removeClass("zone_editing_data zone_adding_data zone_existing_data zone_deleted_data");
                
                switch (status) {
                    case 'deleting':
                    case 'existing':
                        obj.attr("status", "editing");
                        obj.addClass("zone_editing_data");
                    break;
                    case 'editing':
                        obj.attr("status", "editing");
                        obj.addClass("zone_editing_data");
                    break;
                    default:
                        obj.attr("status", "adding");
                        obj.addClass("zone_adding_data");
                    break;
                }
                
                
                // GET THE IDS
                var parentID        = obj.attr("id");
                
                // SET VARIABLES
                var time_start      = obj.attr("timestart");
                var time_end        = obj.attr("timeend");
                var time_display    = time_start + ' - ' + time_end;
                
                var selectExpireTime = "\
                    <select class='ci_cancel_before_time'>\
                        <option value='6hr'>6hrs Prior</option>\
                        <option value='15min'>15min Prior</option>\
                    </select>";
                
                //var selectType = "\
                //    <select class='ci_session_type'>\
                //        <option value='0'>Standard</option>\
                //        <option value='1'>Therapy</option>\
                //    </select>\
                //    <input class='ci_session_type_standard' type='checkbox' value='1' checked />Standard<br />\
                //    <input class='ci_session_type_therapy' type='checkbox' value='1' checked />Therapy<br />";
                
                var time        = obj.attr("originaltime");
                var content     = "\
                    <div style='padding:3px; width:200px;'>\
                        <div style='float:left;'>SESSION TIME<br />(Pending Being Saved)</div>\
                        <div style='float:right;'></div>\
                        <div style='clear:both;'></div>\
                    </div>\
                    ";
                var btnDelete   = "<img class='btn_delete' parentID='" + parentID + "' src='{$this->Img_No}' alt='yes' border='0' />";
                
                var html = "\
                    <div style='float:left;' class='search_col_1 search_timesection_time'>" + time + "</div>\
                    <div style='float:left; padding-top:2px;' class='search_col_2 search_timesection_content'>" + content + "{$session_type_area}</div>\
                    <div style='float:right;' class='search_col_3 search_timesection_delete'>" + btnDelete + "</div>\
                    <div style='clear:both;'></div>\
                    ";
                
                //When should class expire: " + selectExpireTime +
                
                obj.empty();
                $(html).appendTo(obj);
                
            } // end function HandleDropAction()
            
        } // end function ReapplyJqueryFunctionalityToObjects()

        function GetReservationsAjax() {
            var date            = $('#cal_date').val();
            var loadUrl         = "{$this->URL_Load_Existing_Records}&wh_id={$this->WH_ID}&date=" + date;
            var ajax_load       = "<img src='/office/images/upload.gif' alt='loading...' />";
            $("#schedule_holder").html(ajax_load).load(loadUrl);
        }

SCRIPT;
        AddScript($SCRIPT);
    
    
    
        $script = <<<SCRIPT
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
        //AddScriptInclude('/jslib/jquery-ui.js');
        AddScriptInclude('/jslib/jquery.livequery.js');
    }

    public function AddStyle()
    {
        # STYLES
        # =====================================================================
        AddStyle("
            .insert { height:50%; }
            .boxes { height:30px; width:200px; border:dashed 1px green;}
            
            
            .zones { 
                height:30px; width:300px; 
                border:solid 1px #ccc;
                background-color:#f9f9f9;
                display:block;
            }
            
            .zones_time {
                border:0px solid #990000;
                height:30px; width:50px;
                
                font-size:13px; font-weight:bold;
                font-family:Lucida Sans, Verdana;
                /*padding:8px 5px 2px 10px;*/
                padding:2px;
            }
            
            .zones_data {
                border:0px solid #990000;
                height:30px; width:230px;
                
                padding-top:2px;
            }
            
            .drophover { height:60px; w_idth:230px; border:dashed 1px red; }
            #tbox {cursor:pointer;}
            .ui-state-highlight { height: 3em; line-height: 1.2em; }
            
            .zone_adding_data {
                height:60px;
                background-color: #990000;
                color:#fff;
            }
            .zone_editing_data {
                height:60px;
                background-color: #EBBE2E;
                color:#000;
            }
            .zone_existing_data {
                height:70px;
                /*background-color:green;*/
                color:#fff;
            }
            .zone_deleted_data {
                background-color:orange;
            }
            
            .fs_wrapper {
                margin-bottom : 20px;
                background-color : #ccc;
                width:0px;
            }

            .fs_wrapper legend {
                font-weight : bold;
                font-size : 1.1em;
                color : #990000;
            }
            
            .sec_header {
                color: #990000;
                font-weight:bold;
                font-size:14px;
            }
            .sec_wrapper {
                border:1px solid #ccc;
                background-color: #ddd;
                padding: 2px;
            }
            
            
            .instructor_schedule_session_open {
                background-color:green;
            }
            .instructor_schedule_session_locked {
                background-color:#EBBE2E;
            }
            .instructor_schedule_session_booked {
                background-color:red;
            }
            
            
            .search_timesection_delete {
                padding: 5px 5px 0px 0px;
            }
            
            .search_col_1 {
                width:60px;
            }
            .search_col_2 {
                width:190px;
                font-size:13px; font-weight:bold;
                font-family:Lucida Sans, Verdana;
            }
            .search_col_3 {
                width:20px;
            }
        ");
    }


}  // -------------- END CLASS --------------