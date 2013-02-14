<?php
class Sessions_Search2 extends BaseClass
{
    public $ShowQuery                   = false;
    public $IsTesting                   = true;
    public $ShowArray                   = false;
    
    
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
    public $courseDetailsData = '';
    
    
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
                $this->CreateSchedule();
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
                
                $z = 0;
                foreach ($record_time as $record) {    
                
                    $parentID           = "time_{$record['ci_time_start']}";
                    $selectExpireTime   = $record['ci_cancel_before_time'];
                    $time_start         = $record['ci_time_start'];
                    $time_end           = $record['ci_time_end'];
                    
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
                    
                    $this->FormattedRecords[$parentID][$z]['data'] = $html;
                    $this->FormattedRecords[$parentID][$z]['id'] = $record['sessions_id'];
                    $this->FormattedRecords[$parentID][$z]['data_detail'] = $html_short;
                    $this->FormattedRecords[$parentID][$z]['instructor_id'] = $record['instructor_id'];
                    
                    $z++;
                } // end looping records
            } //end looping time
        } //end looping date
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
    
    
    public function GetFakeCoursesMultipleInDay($WH_ID=0, $DATE='')
    {
        #$dates = '2010-10-20|2010-10-21|2010-10-22|2010-10-23';
        #$dates = '2010-10-202010-10-21|2010-10-22|2010-10-23';
        #$times = '0200|0400|0600|0800';
        
        
        $date_times['2010-11-01'] = '0200|0400|0600|0800';
        $date_times['2010-11-02'] = '0600|0800';
        $date_times['2010-11-03'] = '0200|0300|0400';
        $date_times['2010-11-04'] = '0800|1000';
        $date_times['2010-11-05'] = '1000|1200';
        
        $date = $this->existing_records_date;
        $date = ($date) ? $date : date('Y-m-d');
        
        $records = array();
        
        if (isset($date_times[$date])) {
            //$date_list = explode('|', $dates);
            $time_list = explode('|', $date_times[$date]);
            
            //foreach ($date_list as $date) {
                $k = 0;
                foreach ($time_list as $time) {
                    $k++;
                    for ($z=0; $z<$k; $z++){
                    
                        $instructor_id = rand(900001, 900029);
                    
                        $records[$date][$time][$z]['sessions_id']               = $z;
                        $records[$date][$time][$z]['ci_cancel_before_time']     = '15min';
                        $records[$date][$time][$z]['ci_time_start']             = $time;
                        $records[$date][$time][$z]['ci_time_end']               = $time + 100;
                        
                        $records[$date][$time][$z]['instructor_id']             = $instructor_id;
                        $records[$date][$time][$z]['instructor_name']           = 'Test Instructor';
                    }
                }
            //}
        }
        
        $this->ExistingRecords = $records;
    }
    
    
	public function GetCourses($WH_ID=0, $DATE='')
	{
        $this->GetFakeCoursesMultipleInDay($WH_ID, $DATE);
        return;
        
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
            
            
            #$temp_records[$date][$time]['instructor_id']            = $record['end_datetime'];
            #$temp_records[$date][$time]['instructor_name']          = $record['end_datetime'];
        }
        $this->ExistingRecords = $temp_records;
	}
    





    public function CreateSchedule()
    {
        $existing_records = $this->FormattedRecords;
    
        $time_base          = 0000;
        $row_data           = '';
        $course_listing     = '';
        
        
        for ($i=0; $i<25; $i++) {
            $time_start     = $time_base + (($i)*100);
            $time_end       = $time_base + (($i+1)*100);
            
            $time_start     = str_pad($time_start, 4, "0", STR_PAD_LEFT);
            $time_end       = str_pad($time_end, 4, "0", STR_PAD_LEFT);
            
            $divID          = "time_{$time_start}";          
            
            
            if (isset($existing_records[$divID])) {
                # LOOP THROUGH ALL CLASSES AT THIS TIME
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
                $display_a      = "<b>{$count}</b> {$count_title} AVAILABLE<br /><a href='#' onclick=\"ShowCourseListing('{$divID}'); SetInstructorPictures('{$instructors_list}');\">[ click to view ]</a>"; //$existing_records[$divID]['data'];
                $classes        = ($count == 1) ? 'zones zone_editing_data' : 'zones zone_existing_data';
                $status         = 'existing';
                $recordID       = ''; //$existing_records[$divID]['id'];
            } else {
                $display_a      = "<div style='padding-top:10px; color:#bbb;'>NO SESSIONS AVAILABLE</div>";
                $classes        = 'zones';
                $status         = '';
                $recordID       = '';
            }
            
            
            $display_1 =  date("g", strtotime($time_start));
            $display_2 =  date(" A", strtotime($time_start));
            
            $display = "
                <div style='float:left; width:60px; padding:2px;'>
                    <span style='font-size:18px; font-weight:bold;'>$display_1</span><span style='font-size:11px;'>$display_2</span>
                </div>
                <div style='float:left;'>
                    {$display_a}
                </div>
                <div style='clear:both;'></div>
                ";
            
            
            # OUTPUT DATA FOR THE MASTER LIST
            # =============================================
            $row_data  .=  "<div id='{$divID}' class='{$classes}' status='{$status}' recordID='{$recordID}' timestart='{$time_start}' timeend='{$time_end}'>{$display}</div>";
            
            
            # OUTPUT DATA FOR THE HIDDEN COURSE INFORMATION LIST
            # =============================================
        }
        
        $this->rowData = $row_data;
        $this->courseDetailsData = $course_listing;
    }
    
    public function OutputSchedule($RETURN=false)
    {
        //$date = date("l, M j, Y", $this->existing_records_date);
        
        $date = ($this->existing_records_date) ? $this->existing_records_date : date('Y-m-d');
        $date = $this->datefmt($date, 'yyyy-mm-dd', "l, M j, Y");
        
        $OUTPUT = <<<OUTPUT
            <div style='display:none;'>
                <div id="result_send" style='border:1px solid blue;'></div>
                <div id="result_receive" style='border:1px solid green;'></div>
            </div>
            
            <div style="clear:both;"></div>
            
            <div style='font-size:16px; font-weight:bold; padding:10px; background-color:#f9f9f9;'>Date: {$date}</div>
            <div id="master">
                {$this->rowData}
            </div>
            <div id="course_details_all" style="display:none;">
                {$this->courseDetailsData}
            </div>
            
            <script type='text/javascript'>
                SetInstructorPictures('900001|900003|900005|900007');
            </script>
            
OUTPUT;
//<script type='text/javascript'>ReapplyJqueryFunctionalityToObjects();</script>

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
        if ($this->ShowQuery) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        
        $width = 50; //100;
        $height = 60; //120;
        
        
        
        


        
        
        $OUTPUT = '<ul id="master_instructors_list" class="image-grid">';
        foreach ($records as $record) {
            $OUTPUT .= "
            
            <li data-id='picture_{$record['wh_id']}' id='picture_{$record['wh_id']}_li'>
                <div class='picture_holder picture_inactive' id='picture_{$record['wh_id']}'>
                <div class='picture_wrapper'>
                    <img src='/office/{$record['primary_pictures_id']}' width='{$width}px' height='{$height}px' alt='{$record['first_name']} {$record['last_name']}' border='0' />
                    <br />{$record['wh_id']}
                </div>
                </div>
            </li>
            
            ";
        }
        //$OUTPUT .= "<div style='clear:both;'></div>";
        $OUTPUT .= '</ul>';
        
        
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
        AddScriptInclude('/jslib/jquery.quicksand.min.js');
        //AddScriptInclude('/jslib/quicksand-custom.js');
    }


    public function AddStyle()
    {
        $style = "
        /*
        .picture_holder {
            padding:10px;
            float:left;
        }
        */
        .picture_wrapper {
            padding:3px;
        }
        .picture_inactive img{
            opacity:0.4;
            filter:alpha(opacity=40);
            border:1px solid #ddd;
            background-color:#fff;
        }
        .picture_active img{
            opacity:1.0;
            filter:alpha(opacity=100);
            border:1px solid #990000;
            background-color:#fff;
        }
        
        
        
        
        
        
        
        /* Image Grid 
        ---------------------------------------------------------------------- */

        .image-grid {
          margin: 0px 0 0 -40px;
          padding-left: 45px;
          width: 440px;
        }

        .image-grid:after {
          content: '';
          display: block;
          height: 0;
          overflow: hidden;
          clear: both;
        }

        .image-grid li {
          /*width: 128px;*/
          /*margin: 20px 0 0 35px;*/
          float: left;
          text-align: center;
          font-family: 'Helvetica Neue', sans-serif;
          line-height: 17px;
          color: #686f74;
          /*height: 177px;*/
          overflow: hidden;
        }

        .image-grid li img,
        .image-grid li strong {
          display: block;
        }

        .image-grid li strong {
          color: #fff;
        }
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
            .insert { height:50%; }
            .boxes { height:30px; width:200px; border:dashed 1px green;}
            .zones { height:30px; width:300px; 
                border:solid 1px #ccc;
                background-color:#f9f9f9;
            }
            
            #tbox {cursor:pointer;}
            #master {border:0px solid red; padding:10px;}
            .ui-state-highlight { height: 3em; line-height: 1.2em; }
            
            .zone_adding_data {
                
                background-color: #990000;
                color:#fff;
            }
            .zone_editing_data {
                
                background-color: yellow;
                color:#000;
            }
            .zone_existing_data {
                
                background-color:green;
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
            
        ";
        
        AddStyle($style);
    }

}  // -------------- END CLASS --------------