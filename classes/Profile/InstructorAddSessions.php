<?php
class Profile_InstructorAddSessions extends BaseClass
{
    public $num_sessions                    = 2;    // Number of sessions to add
    public $num_booked_sessions             = 2;    // Number of sessions to mark as booked
    public $num_free_credits                = 5;    // Number of fake credits
    
    // ---------- NON-MODIFIABLE VARIABLES ----------
    public $wh_id                           = 0;
    public $record_total_success            = true;
    
    
    public function  __construct()
    {
        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Description' => 'Add fake sessions for all instructors',
        );
        
        $this->SetSQL();
        $this->Close_On_Success = false;
    }
    
    public function SetSQL()
    {
        if (empty($this->SQL)) {
            $this->SQL = Lib_Singleton::GetInstance('Lib_Pdo');
        }
    }
    
    public function Execute()
    {
        $num_months         = 2;    // How far in future to create events
        $num_days           = 15;   // How many days out of each month to create events for
        $num_sessions_day   = 2;    // How many sessions during any given day should it create
        
        $dates_list         = '';   // List of dates to create events for
        $time_list          = '';   // List of times to create events for
        $instructors_list   = '';   // List of all instructor WH_IDs
        
        
        # GET ALL THE INSTRUCTORS
        # ====================================================
        $records = $this->SQL->GetArrayAll(array(
            'table' => $GLOBALS['TABLE_instructor_profile'],
            'keys'  => 'wh_id',
            'where' => 'active=1',
        ));
        
        $instructors_list = '';
        foreach ($records as $record) {
            $instructors_list .= "{$record['wh_id']}|";
        }
        $instructors_list = substr($instructors_list, 0, -1);
        
        
        # CREATE ALL THE TIMES
        # ====================================================
        $time_list = '0200|0400|0600|0800|1000|1200|1400|1600|1800|2000|2200';
        
        
        # CREATE ALL THE DATES
        # ====================================================
        $days_array = array();
        for ($m=0; $m<$num_months; $m++) {
            for ($z=0; $z<$num_days; $z++) {
                do {
                    $d = rand (0,29);
                    $new_date = mktime(0, 0, 0, date("m")+$m, date("d")+$d, date("Y"));
                    $new_date = date('Y-m-d', $new_date);
                } while (in_array($new_date, $days_array));
                $days_array[] = $new_date;
                $dates_list .= "{$new_date}|";
            }
        }
        $dates_list  = substr($dates_list, 0, -1);
        
        
        
        #echo "<br /><br />dates_list ===> $dates_list";
        #echo "<br /><br />time_list ===> $time_list";
        #echo "<br /><br />instructors_list ===> $instructors_list";
        
        $dates          = explode('|', $dates_list);
        $times          = explode('|', $time_list);
        $instructors    = explode('|', $instructors_list);
        
        
        
        # CREATE THE RECORDS
        # ====================================================
        foreach ($instructors as $instructor) {
        
            foreach ($dates as $date) { // each randomly selected date
                
                $used_times = array();
                for ($i=0; $i<$num_sessions_day; $i++) {
                
                    do {
                        $r = rand (0,(count($times)-1));
                        $time = $times[$r];
                    } while (in_array($time, $used_times));
                    $used_times[] = $time;
                    
                    $wh_id      = $instructor;
                    $date_start = $date;
                    $time_start = $time;
                    echo "<br /> AddInstructorSessionByDateTime($wh_id, $date_start, $time_start)";
                    $this->AddInstructorSessionByDateTime($wh_id, $date_start, $time_start);
                    
                }
                
            } //end date
            
        } // end instructor
        
    }
    
    
    public function AddInstructorSessionByDateTime($instructor_wh_id, $date_start, $time_start)
    {
        if (!$instructor_wh_id) return false;
        if (!$date_start) return false;
        if (!$time_start) return false;
        
        $date           = $date_start;
        
        $time_start     = $time_start;
        $time_end       = $time_start + 100;
        
        $time_start     = str_pad($time_start, 4, "0", STR_PAD_LEFT);
        $time_end       = str_pad($time_end, 4, "0", STR_PAD_LEFT);
        
        # ACTUALLY USE THE INSTRUCTOR SCHEDULING CLASS TO ADD A RECORD
        # =================================================================
        $session_record = array(
            'instructor_id'         => $instructor_wh_id,
            'date'                  => $date,
            'ci_time_start'         => $time_start,
            'ci_time_end'           => $time_end,
            'ci_cancel_before_time' => 'Created By: Profile_InstructorAddSessions',
            'ci_id'                 => 0,
            'ci_status'             => 'adding',
            'testing'               => 1,
        );
        $session_records[0] = $session_record;
        $SESSION = new Sessions_InstructorScheduling();
        $SESSION->Processing_Records        = $session_records;
        $SESSION->WH_ID                     = $instructor_wh_id;
        $SESSION->Processing_Records_Date   = $date;
        $SESSION->ProcessRecords();
        
        $sessions_id = $SESSION->Last_Sessions_Id;
        
        echo "<br />Inserted Record ===> $sessions_id";
        
    }
    
} //end class