<?php
class Sessions_Analysis extends BaseClass
{
    public $Show_Query                      = false;    // TRUE = output the database queries ocurring on this page
    public $Show_Inactive_Sessions          = false;    // TRUE = include inactive sessions in the analysis
    public $section_width                   = 15;
    public $max_score                       = 5;
    
    public $rating_graphics = array(
        0 => '/office/images/transparent/transpRed75.png',
        1 => '/office/images/transparent/transpRed75.png',
        2 => '/office/images/transparent/transpOrange75.png',
        3 => '/office/images/transparent/transpOrange75.png',
        4 => '/office/images/transparent/transpBlue75.png',
        5 => '/office/images/transparent/transpBlue75.png',
    );
    
    
    // ---------- NON-MODIFIABLE VARIABLES ----------
    public $OBJ_TIMEZONE                    = null;
    public $user_local_timezone             = '';
    public $sessions_id_list                = '';
    public $Instructor_WHID                 = 0;
    public $Is_Instructor                   = false;
    public $table_sessions                  = null;
    public $table_session_checklists        = null;
    public $table_instructor_profile        = null;
    
    
    public function  __construct()
    {
        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Description' => 'Get sessions from database for analysis - does not do the charting',
        );
        
        $this->OBJ_TIMEZONE                     = new General_TimezoneConversion();
        $this->user_local_timezone              = $GLOBALS['USER_LOCAL_TIMEZONE'];
        $this->table_sessions                   = $GLOBALS['TABLE_sessions'];
        $this->table_session_checklists         = $GLOBALS['TABLE_session_checklists'];
        $this->table_instructor_profile         = $GLOBALS['TABLE_instructor_profile'];
        
        
        # INITIALIZE CLASS FUNCTIONS
        # ==============================================================
        $this->SetParameters(func_get_args());
        $this->Instructor_WHID = $this->GetParameter(0);
        if ($this->Instructor_WHID) {
            $this->AddDefaultWhere("`instructor_profile`.`wh_id`='$this->Instructor_WHID'");
        }
        

        $this->Table                = 'session_checklists';
        $this->Add_Submit_Name      = 'SESSION_CHECKLISTS_SUBMIT_ADD';
        $this->Edit_Submit_Name     = 'SESSION_CHECKLISTS_SUBMIT_EDIT';
        $this->Index_Name           = 'session_checklists_id';
        $this->Flash_Field          = 'session_checklists_id';
        $this->Default_Where        = '';  // additional search conditions
        $this->Default_Sort         = 'session_checklists_id';  // field for default table sort
        $this->Default_Fields       = 'sessions_id,utc_start_datetime,utc_end_datetime,user-instructor_skill,rating_user,rating_instructor';
        $this->Unique_Fields        = '';
        $this->Joins =
            "LEFT JOIN sessions                     ON sessions.sessions_id                         = session_checklists.sessions_id
            LEFT JOIN instructor_profile            ON instructor_profile.wh_id                     = sessions.instructor_id
            LEFT JOIN session_ratings_user          ON session_ratings_user.sessions_id             = session_checklists.sessions_id
            LEFT JOIN session_ratings_instructor    ON session_ratings_instructor.sessions_id       = session_checklists.sessions_id
            ";

/*
        $records = $this->SQL->GetArrayAll(array(
            'table' => "$this->table_session_checklists",
            'keys'  => "$this->table_session_checklists.*, $this->table_sessions.*, $this->table_instructor_profile.*, $this->table_session_checklists.wh_id AS user_wh_id",
            'where' => "$this->table_session_checklists.active=1",
            'joins' => "
                LEFT JOIN $this->table_sessions ON $this->table_sessions.sessions_id = $this->table_session_checklists.sessions_id
                LEFT JOIN $this->table_instructor_profile ON $this->table_instructor_profile.wh_id = $this->table_sessions.instructor_id
                ",
        ));
*/

        $this->Field_Titles = array(

            'sessions.sessions_id'              => 'Sessions ID',
            'instructor_profile.gender'         => 'Instructor Gender',
            "CONCAT(`instructor_profile.first_name`, ' ', `instructor_profile.last_name`) AS instructor_name"     => 'Instructor Name',
            
            
            'sessions.utc_start_datetime'          => 'UTC Start',
            'sessions.utc_end_datetime'              => 'UTC End',
            //"CONCAT(`sessions.date_utc`, ' ', `sessions.time_utc_start`) AS local_start"        => "Local Start ($this->user_local_timezone)",
            //"CONCAT(`sessions.date_utc`, ' ', `sessions.time_utc_end`) AS local_end"            => "Local End ($this->user_local_timezone)",


            #USER RATINGS
            ###'session_ratings_user.instructor_skill'                          => 'instructor skill',
            "`session_ratings_user.instructor_skill` AS user-instructor_skill"                          => 'instructor skill',
#            "`session_ratings_user.instructor_knowledge` AS user-instructor_knowledge"                  => 'instructor knowledge',
#            "`session_ratings_user.technical_ease` AS user-technical_ease"                              => 'technical ease',
#            "`session_ratings_user.technical_quality_video` AS user-technical_quality_video"            => 'technical quality_video',
#            "`session_ratings_user.technical_quality_audio` AS user-technical_quality_audio"            => 'technical quality_audio',

            #INSTRUCTOR RATINGS
#            "`session_ratings_instructor.user_skill` AS instructor-user_skill"                              => 'user skill',
#            "`session_ratings_instructor.user_knowledge` AS instructor-user_knowledge"                      => 'user knowledge',
#            "`session_ratings_instructor.technical_ease` AS instructor-technical_ease"                      => 'technical ease',
#            "`session_ratings_instructor.technical_quality_video` AS instructor-technical_quality_video"    => 'technical quality_video',
#            "`session_ratings_instructor.technical_quality_audio` AS instructor-technical_quality_audio"    => 'technical quality_audio',


            'session_checklists.rating_user'                        => 'Rating User',
            'session_checklists.rating_instructor'                  => 'Rating Instructor',
        );



    } // -------------- END __construct --------------

    public function SetFormArrays()
    {
        /*
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
        */
    }
    
    public function AddScript()
    {
        $eq = EncryptQuery("class=Sessions_AnalysisChart");
        
        $script = "
        function tableSearchAnalysis(action, eq, idx)
        {
            var formdata = $('#TABLE_SEARCH_SELECTION' + idx +', #TABLE_STARTROW' + idx + ', #TABLE_ROWS' + idx + ', #NUMBER_ROWS' + idx).serialize();
            
            $('#TABLE_DISPLAY' + idx +' tbody').empty().append('<tr><td style=\"text-align:center; padding:1em;\">Processing . . .<br /><br /><img src=\"/wo/images/upload.gif\" /></td></tr>');
            
            $.post( tableAjaxHelperFile + '?table_search=1&eq=' + eq + '&idx=' + idx + '&action=' + action,
                {data : formdata},
                function(data) {
                    $('#TABLE_DISPLAY' + idx +' tbody').empty().append(data);

                    var sessions_id_list = $('#sessions_id_list').html();
                    if (sessions_id_list) {
                        
                        var link = getClassExecuteLink('$eq');
                        $.post( link ,
                            {data : sessions_id_list},
                            function(data) {
                                $('#SESSION_CHART_AREA').empty().append(data);
                        });
                    }

                    if (haveDialogTemplate) {
                        ResizeIframe();
                    }
                });
        }

        //function setTabSearchTableAnalysis(num, group, eq, idx)
        function setTabSearchTable(num, group, eq, idx)
        {
            var linkname = group + 'link';
            var num2 = 3 - num;

            $('#' + group + num2).hide();
            $('#' + group + num).show();


            $('#' + linkname + num2).removeClass('tabselect').addClass('tablink');
            $('#' + linkname + num).removeClass('tablink').addClass('tabselect');

            if (num == 2) {
                tableSearchAnalysis('SHOW', eq, idx);

            } else {
                if (haveDialogTemplate) {
                    ResizeIframe();
                }
            }
        }


        ";
        AddScript($script);
    }
    
    public function Execute($return=false)
    {
        if ($return) {
            $output = $this->ListTableText();
            $output .= "<div id='SESSION_CHART_AREA'>SESSION_CHART_AREA</div>";
            return $output;

        } else {
            echo $this->ListTableText();
            echo "<div id='SESSION_CHART_AREA'>SESSION_CHART_AREA</div>";
        }
    }

    public function AjaxHandle()
    {
        $output = '';
        
        ###echo ArrayToStr($_POST);
        
        switch (Post('action')) {
            case 'UpdateAnalysis':
                $period     = Post('data');
                $period     = str_replace (' ', '_', $period);
                $period     = strtolower($period);
                $output     = $this->GetAnalysisByTimePeriod($period);
            break;
        }
        
        echo $output;
    }
    
    public function AjaxTableDisplay($posted_data, $action, $idx, $row_id='')
    {
        parent::AjaxTableDisplay($posted_data, $action, $idx, $row_id);
        echo "<tr><td id='sessions_id_list' style='display:none;'>" . $this->sessions_id_list . '</td></tr>';
    }
    
    public function ProcessTableCell($field, &$value, &$td_options, $id='')
    {
        parent::ProcessTableCell($field, $value, $td_options, $id);
        //echo "<br />field ===> $field";
        switch ($field) {
            case 'gender':
                $value = 'aaaaa';
                //$td_options = "style=\"display:none;\"";
            break;
            case 'utc_start_datetime':
            case 'utc_end_datetime':
                $input_date_time        = $value;
                $input_timezone         = 'UTC';
                $output_timezone        = 'UTC';
                $output_format          = 'Y-m-d g:i a';
                $value = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
            break;
            case 'local_start':
            case 'local_end':
                $input_date_time        = $value;
                $input_timezone         = 'UTC';
                $output_timezone        = $this->user_local_timezone;
                $output_format          = 'Y-m-d g:i a';
                $value = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
            break;
            case 'sessions_id':
                $this->sessions_id_list .= "$value,";
                $value = "$value";
            break;
            case 'user-instructor_skill':
            case 'user-instructor_knowledge':
            case 'user-technical_ease':
            case 'user-technical_quality_video':
            case 'user-technical_quality_audio':
            case 'instructor-user_skill':
            case 'instructor-user_knowledge':
            case 'instructor-technical_ease':
            case 'instructor-technical_quality_video':
            case 'instructor-technical_quality_audio':
            case 'rating_user':
                $score = rand(0,5);
                $image = $this->rating_graphics[$score];
                $width = $this->section_width * ($score + 1);
                $width_style = $this->section_width * ($this->max_score + 1); + 15;
                $value = "<div style='border:1px solid #bbb; padding:2px; background-color:#fff; width:{$width_style}px;'><img src='{$image}' height='15' width='{$width}' border='0' alt='{$score}'/> {$score}</div>";
            break;
        }

    }
    
    public function GetAnalysisByTimePeriod($PERIOD='')
    {
        # FUNCTION :: Set the time period and get the session analysis
        
        $sessions_id_list = '';
        
        switch ($PERIOD) {
            case 'current_day':
                $date_start         = date('Y-m-d');    // Today
                $date_end           = date('Y-m-d');    // Today
                $sessions_id_list   = $this->GetSessionIdList($date_start, $date_end);
            break;
            
            case 'current_week':
                $date_start         = date("Y-m-d", strtotime('-7 days', strtotime(date('Y-m-d'))) );     // 7 days ago
                $date_end           = date('Y-m-d');    // Today
                $sessions_id_list   = $this->GetSessionIdList($date_start, $date_end);
            break;

            case 'current_month':
                $date_start         = date("Y-m-01");   // First day of current month
                $date_end           = date("Y-m-t");    // Last day of current month
                $sessions_id_list   = $this->GetSessionIdList($date_start, $date_end);
            break;
            
            case 'last_month':
                $date_start         = date("Y-m-d", strtotime('-1 month', strtotime(date('Y-m-01'))) );     // First day of last month
                $date_end           = date("Y-m-t", strtotime("last month", strtotime(date('Y-m-01'))));    // Last day of last month
                $sessions_id_list   = $this->GetSessionIdList($date_start, $date_end);
            break;
            
            case 'all':
            default:
                $date_start         = '1999-01-01';
                $date_end           = '2999-01-01';
                $sessions_id_list   = $this->GetSessionIdList($date_start, $date_end);
            break;
        }
        
        return $sessions_id_list;
    }
    
    public function GetSessionIdList($DATE_START='', $DATE_END='')
    {
        # FUNCTION :: Get session IDs that fit within the given dates
        
        if (false) {
            echo "<br />DATE_START ===> $DATE_START";
            echo "<br />DATE_END ===> $DATE_END";
            echo "<br /><br />";
        }
        
        # CALCULATE THE DATES
        # ========================================================================
        $input_date_time        = "{$DATE_START} 0000";
        $input_timezone         = $GLOBALS['USER_LOCAL_TIMEZONE'];
        $output_timezone        = 'UTC';
        $output_format          = 'Y-m-d H:i:s';
        $utc_start_datetime     = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
        
        $input_date_time        = "{$DATE_END} 2300";
        $input_timezone         = $GLOBALS['USER_LOCAL_TIMEZONE'];
        $output_timezone        = 'UTC';
        $output_format          = 'Y-m-d H:i:s'; //'Y-m-d Hi';
        $utc_end_datetime       = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
        
        
        # GET THE RECORDS
        # ========================================================================
        $where_whid             = "`instructor_id`='{$this->Instructor_WHID}' ";
        $where_date             = " AND (`utc_start_datetime`>='{$utc_start_datetime}' AND `utc_start_datetime` <= '{$utc_end_datetime}') ";
        $where_rating           = ''; //" AND `rating_user`=1 ";
        $where_active           = (!$this->Show_Inactive_Sessions) ? " AND `{$GLOBALS['TABLE_sessions']}`.`active`=1" : '';
        
        $keys                   = "{$GLOBALS['TABLE_sessions']}.sessions_id";
        $order                  = "utc_start_datetime ASC";
        
        $records = $this->SQL->GetArrayAll(array(
            'table'     => $GLOBALS['TABLE_sessions'],
            'keys'      => $keys,
            'where'     => "$where_whid $where_date $where_rating $where_active",
            'order'     => $order,
        ));
        if ($this->Show_Query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        
        
        # FORMAT THE RECORDS
        # ========================================================================
        $list = '';
        
        if ($records) {
            foreach ($records AS $record) {
                $list .= "{$record['sessions_id']},";
            }
            $list = substr($list, 0, -1);
        }
        
        
        # RETURN THE LIST OF SESSION IDS
        # ========================================================================
        return $list;
    }
    
    public function InstructorAnalysis_MenuLeft() 
    {
        
        $dates_list = "Current Day|Current Week|Current Month|Last Month|All";
        
        $options_form = OutputForm(array(
            'form||post|OPTIONS_YOGA_TYPES',
            "code|<b>Search by Time Period</b><br />",
            "@select||dates_search|N||$dates_list",
            
            //"checkboxlist|title||user=User Ratings|instructor=Instructor Ratings",
            //"checkboxlist|title|options|value1=text|value2=text"
            //"datepick|Start Date|date_start|N|2011|NOW|function|options|aftertext"
            //"datepick|Start Date|date_start|N|2011",
            //"datecc|title|varname|N",
            'endform',
        ));
        
        $output = "
            <div style='text-align:center;'>
                <br /><br />
                <div class='orange left_header lowercase'>view ratings</div>
                <div class='black left_content' id='search_current_instructor_profile'></div>
                <br />
                <div class='left_content'>{$options_form}</div>
            </div>
            ";
        
        $script = "
            $('#FORM_dates_search').change(function(){
                ChangeAnalysisSearchPeriod();
            });
            
            $('#FORM_dates_search').val('All');
            ChangeAnalysisSearchPeriod();
            
            InitializeOnReady_Sessions_Search();
            ";
        AddScriptOnReady($script);
        
        $eq_analysis_chart  = EncryptQuery("class=Sessions_AnalysisChart");
        $eq_analysis        = EncryptQuery("class=Sessions_Analysis");
        
        global $PAGE;
        $script_link        = $PAGE['ajaxlink'] . ';action=upload';
        
        $script = "
            function InitializeOnReady_Sessions_Search() {
                $.ajaxSetup ({
                    cache: false
                });
            }
            
            function ChangeAnalysisSearchPeriod() {
                // FUNCTION :: Make AJAX call to determine session ids
                // =================================================================================
                var period      = $('#FORM_dates_search').val();
                var link        = '$script_link';
                var noDataFound = '<h2>Loading Records For: '+ period +'<br />No Records Found For This Time Period.</h2>';
                $.post(link,
                    {data : period,
                    action : 'UpdateAnalysis'},
                    function(data) {
                        if (data) {
                            //alert(data);
                            GetAnalysis(data, '', period);
                        } else {
                            $('#SESSION_CHART_AREA').empty().append(noDataFound);
                        }
                });
            }
            
            function GetAnalysis(sessions_id_list, chart_title, chart_subtitle) {
                // FUNCTION :: Actually makes the call to get the analysis based on session ids
                // =================================================================================
                if (sessions_id_list) {
                    var link = getClassExecuteLink('$eq_analysis_chart');
                    $.post( link ,
                        {data : sessions_id_list,
                        title : chart_title,
                        subtitle : chart_subtitle},
                        function(data) {
                            $('#SESSION_CHART_AREA').empty().append(data);
                    });
                }
            }
        ";
        AddScript($script);
        
        return $output;
    }

}  // -------------- END CLASS --------------