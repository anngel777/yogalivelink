<?php
class Sessions_InstructorScheduling extends BaseClass
{
    public $ShowQuery                   = false;
    public $IsTesting                   = true;
    public $ShowArray                   = false;

    public $Last_Sessions_Id            = 0;
    
    public $ExistingRecords             = array();
    public $existing_records_date       = '';
    public $existing_records_wh_id      = '';
    public $FormattedRecords            = array();
    
    public $processing_records          = array();
    public $processing_records_date     = '';
    public $processing_records_wh_id    = '';
    
    public $TableClassSchedule          = 'sessions';
    
    public $img_no                      = '/office/images/buttons/cancel.png';
    public $img_yes                     = '/office/images/buttons/save.png';

    
    public $rowData = '';
    
    
    //private $TableChatSettings          = 'touchpoint_chat_settings';
    //private $TableChats                 = 'touchpoint_chats';
    public $SuccessRedirectChatPage     = 'chat_user';
    
    //public $reset_settings              = false;
    //private $settings                   = array();
    
    
    public function  __construct()
    {
        parent::__construct();
        $this->AddScript();
    } // -------------- END __construct --------------

    
    
    
    public function AjaxHandle()
    {
        $action = Get('action');
        switch ($action) {
            case 'LoadExistingRecords':
                $this->existing_records_wh_id   = Get('wh_id');
                $this->existing_records_date    = Get('date');
                
                $this->GetCourses();
                $this->FormatCourses();
                $this->CreateHoldingSchedule();
                $this->OutputSchedule();
            break;
            case 'ProcessRecords':
                $this->processing_records           = unserialize(Post('contentArray'));
                $this->processing_records_wh_id     = Get('wh_id');
                $this->processing_records_date      = Get('date');
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
    
    public function FormatCourses() 
    {
        foreach ($this->ExistingRecords as $record_date) {
            foreach ($record_date as $record_time) {
                
                $parentID           = "time_{$record_time['ci_time_start']}";
                $selectExpireTime   = $record_time['ci_cancel_before_time'];
                $time_start         = $record_time['ci_time_start'];
                $time_end           = $record_time['ci_time_end'];
                
                $html = "
                <div style='padding:3px;'>
                    <div style='float:left;'>SESSION TIME</div>
                    <div style='float:right;'><img class='btn_delete' parentID='{$parentID}' src='{$this->img_no}' alt='yes' border='0' /></div>
                    <div style='clear:both;'></div>
                </div>
                <div class='course_info' style='padding:3px;'>
                    <span class='ci_time_start'>{$time_start}</span> - 
                    <span class='ci_time_end'>{$time_end}</span><br />
                    When should class expire: {$selectExpireTime}
                </div>
                ";
                
                $this->FormattedRecords[$parentID]['data'] = $html;
                $this->FormattedRecords[$parentID]['id'] = $record_time['sessions_id'];
                
            }
        }
    }
    
    
    public function GetFakeCourses($WH_ID=0, $DATE='')
    {
        #$dates = '2010-10-20|2010-10-21|2010-10-22|2010-10-23';
        #$dates = '2010-10-202010-10-21|2010-10-22|2010-10-23';
        #$times = '0200|0400|0600|0800';
        
        
        $date_times['2010-10-20'] = '0200|0400|0600|0800';
        $date_times['2010-10-21'] = '0600|0800';
        $date_times['2010-10-22'] = '0200|0300|0400';
        $date_times['2010-10-23'] = '0800|1000';
        $date_times['2010-10-24'] = '1000|1200';
        
        $date = $this->existing_records_date;
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
        
        $this->ExistingRecords = $records;
    }
    
    
	public function GetCourses($WH_ID=0, $DATE='')
	{
        #$this->GetFakeCourses($WH_ID, $DATE);
        #return;
        
        $WH_ID = $this->existing_records_wh_id;
        $date = $this->existing_records_date;
        $date = ($date) ? $date : date('Y-m-d');
        
		# FUNCTION :: LOADS INSTRUCTOR PROFILE FROM DATABASE
        $records = $this->SQL->GetArrayAll(array(
            'table' => $this->TableClassSchedule,
            'keys'  => '*',
            'where' => "`instructor_id`='$WH_ID' AND `date`='$date' AND`active`=1",
        ));
        if ($this->ShowQuery) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        
        $temp_records = array();
        foreach ($records as $record) {
            $date = $record['date'];
            $time = $record['start_datetime'];
            
            $temp_records[$date][$time]['sessions_id']              = $record['sessions_id'];
            $temp_records[$date][$time]['ci_cancel_before_time']    = $record['notes'];
            $temp_records[$date][$time]['ci_time_start']            = $record['start_datetime'];
            $temp_records[$date][$time]['ci_time_end']              = $record['end_datetime'];
        }
        $this->ExistingRecords = $temp_records;
	}
    





    public function CreateHoldingSchedule()
    {
        $existing_records = $this->FormattedRecords;
    
        $time_base  = 0000;
        $row_data   = '';
        for ($i=0; $i<25; $i++) {
            $time_start = $time_base + (($i)*100);
            $time_end   = $time_base + (($i+1)*100);
            
            $time_start = str_pad($time_start, 4, "0", STR_PAD_LEFT);
            $time_end   = str_pad($time_end, 4, "0", STR_PAD_LEFT);
            
            $divID      = "time_{$time_start}";          
            
            
            
            if (isset($existing_records[$divID])) {
                $display    = $existing_records[$divID]['data'];
                $classes    = 'zones zone_existing_data';
                $status     = 'existing';
                $recordID   = $existing_records[$divID]['id'];
            } else {
                $display    = "OPEN TIME: {$time_start} - {$time_end}";
                $classes    = 'zones';
                $status     = '';
                $recordID   = '';
            }
            
            $row_data  .=  "<div id='{$divID}' class='{$classes}' status='{$status}' recordID='{$recordID}' timestart='{$time_start}' timeend='{$time_end}'>{$display}</div>";
        }
        
        $this->rowData = $row_data;
    }
    
    public function OutputSchedule($RETURN=false)
    {
        //$date = date("l, M j, Y", $this->existing_records_date);
        
        $date = ($this->existing_records_date) ? $this->existing_records_date : date('Y-m-d');
        $date = $this->datefmt($date, 'yyyy-mm-dd', "l, M j, Y");
        
        $OUTPUT = <<<OUTPUT
            <div id="result_send" style='border:1px solid blue;'></div><br />
            <div id="result_receive" style='border:1px solid green;'></div><br />
            <div style="clear:both;"></div>
            
            <h2>Date: {$date}</h2>
            <div id="master">
                {$this->rowData}
            </div>
            
            <script type='text/javascript'>ReapplyJqueryFunctionalityToObjects();</script>
OUTPUT;
        if ($RETURN) {
            return $OUTPUT;
        } else {
            echo $OUTPUT;
        }
    }
    




    public function ProcessRecords()
    {
        echo "<br /><br />P ==> ".ArrayToStr($this->processing_records);
        
        $date   = $this->processing_records_date;
        $wh_id  = $this->processing_records_wh_id;
        
        foreach ($this->processing_records as $record) {
            
            //$where      = "`wh_id`='{$wh_id}'";
            
            $where      = "`sessions_id`='{$record['ci_id']}'";
            $action     = $record['ci_status'];
            
            
            $db_record = array(            
                'instructor_id'     => $wh_id,
                'date'              => $date,
                'start_datetime'    => $record['ci_time_start'],
                'end_datetime'      => $record['ci_time_end'],
                'notes'             => $record['ci_cancel_before_time'],
            );
            
            switch($action) {
                case 'adding':
                    $this->AddRecordLoc($db_record);
                break;
                case 'editing':
                    $this->UpdateRecordLoc($db_record, $where);
                break;
                case 'deleting':
                    $this->DeleteRecordLoc($where);
                break;
            }
        }
    }

    private function AddRecordLoc($db_record) 
    {
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
            'table'     => $this->TableClassSchedule,
            'keys'      => $keys,
            'values'    => $values,
        ));
        $this->Last_Sessions_Id = $this->SQL->Last_Insert_Id;
        if ($this->ShowQuery) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        if ($result) {
            echo "<br />RECORD ADDED";
        }
    }
    
    private function UpdateRecordLoc($db_record, $where) 
    {
        $key_values = '';
        foreach ($db_record as $var => $val) {
            $val = addslashes($val);
            $key_values .= "`$var`='$val', ";
        }
        $key_values = substr($key_values, 0, -2);
        
        $result = $this->SQL->UpdateRecord(array(
            'table'         => $this->TableClassSchedule,
            'key_values'    => $key_values,
            'where'         => "{$where} AND active=1",
        ));
        if ($this->ShowQuery) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        if ($result) {
            echo "<br />RECORD UPDATED";
        }
    }

    private function DeleteRecordLoc($where) 
    {
        $result = $this->SQL->UpdateRecord(array(
            'table'         => $this->TableClassSchedule,
            'key_values'    => "`active`='0'",
            'where'         => "{$where} AND active=1",
        ));
        if ($this->ShowQuery) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        if ($result) {
            echo "<br />RECORD DELETED";
        }
    }










    public function AddScript()
    {
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
        AddScriptInclude('/jslib/jquery-ui.js');
        AddScriptInclude('/jslib/jquery.livequery.js');
    }




}  // -------------- END CLASS --------------