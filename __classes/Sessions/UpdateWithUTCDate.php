<?php
class Sessions_UpdateWithUTCDate extends BaseClass
{
    # ===========================================================
    # CLASS :: Will randomly assign US timezones and then shift to UTC
    # ===========================================================

    public $show_query                      = true;
    public $execute                         = true;
    
    public $table_credits                   = 'credits';
    public $table_sessions                  = 'sessions';
    public $table_sessions_checklists       = 'session_checklists';
    public $table_sessions_XXX              = 'XXX';
    
    public $OBJ_TIMEZONE                    = null;
    
    public function  __construct()
    {
        $this->SetSQL();
        
        $this->OBJ_TIMEZONE = new General_TimezoneConversion();
    } // -------------- END __construct --------------
    
    
    public function SetSQL()
    {
        if (empty($this->SQL)) {
            $this->SQL = Lib_Singleton::GetInstance('Lib_Pdo');
        }
    }
    
    
    public function Execute()
    {
        echo "<div style='width:600px;'>&nbsp;</div>";
        
        if ($this->execute) {
            $this->UpdateSessions();
        }
    }
    
    
    public function UpdateSessions()
    {
        # GET ALL SESSIONS - that don't have a timezone
        # ======================================================================
        $records = $this->SQL->GetArrayAll(array(
            'table'     => $this->table_sessions,
            'keys'      => "*",
            #'where'     => "`timezone`='' AND `active`=1 AND `sessions_id`<10",
            #'where'     => "`active`=1 AND `sessions_id`<10",
            'where'     => "",
        ));
        if ($this->show_query) echo '<br /><br />QUERY => ' . $this->SQL->Db_Last_Query;
        
        
        foreach ($records as $record) {
            
            $num = rand(1,2);
            switch($num) {
                case 1:
                    #$timezone = "(GMT-08:00) Pacific Time";
                    $timezone = "America/Los_Angeles";
                break;
                case 2:
                    #$timezone = "(GMT-05:00) Eastern Time (US &amp; Canada)";
                    $timezone = "America/New_York";
                break;
            }
            
            
            
            # CONVERT TIMEZONE INFORMATION
            # ============================================================
            $date               = $record['date'];
            $start_datetime     = $record['start_datetime'];
            $end_datetime       = $record['end_datetime'];
            
            
            $input_date_time        = "{$record['date']} {$record['start_datetime']}";
            $input_timezone         = $timezone;
            $output_timezone        = 'UTC';
            $output_format          = 'Y-m-d H:i:s';
            $utc_start_datetime     = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
            
            
            $input_date_time        = "{$record['date']} {$record['end_datetime']}";
            $input_timezone         = $timezone;
            $output_timezone        = 'UTC';
            $output_format          = 'Y-m-d H:i:s';
            $utc_end_datetime     = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
            

            
            
            # UPDATE THE RECORD
            # ============================================================
            $sessions_id        = $record['sessions_id'];
            
            $FormArray = array(
                'timezone'              => $timezone,
                'utc_start_datetime'    => $utc_start_datetime,
                'utc_end_datetime'      => $utc_end_datetime,
                
            );
            $key_values = $this->FormatDataForUpdate($FormArray);
            
            $result = $this->SQL->UpdateRecord(array(
                'table'         => $this->table_sessions,
                'key_values'    => $key_values,
                'where'         => "`sessions_id`=$sessions_id",
            ));
            if ($this->show_query) echo '<br /><br />QUERY => ' . $this->SQL->Db_Last_Query;
            $echo = ($result) ? "SUCCESS -> Session updated." : "FAILED to update session.";
            echo "<br /><br />$echo";
        } // end foreach
    }
    
    
    
    
    
    
    
    
    

   

}  // -------------- END CLASS --------------