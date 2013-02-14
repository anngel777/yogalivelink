<?php
class Sessions_AnalysisChart extends BaseClass
{
    public $Show_Query                      = false;    // TRUE = output the database queries ocurring on this page
    public $Chart_Title                     = 'SUMMARY OF TEST SCORES';
    public $Chart_Sub_Title                 = '';
    
    public $table_sessions                  = 'sessions';
    public $table_session_checklists        = 'session_checklists';
    public $table_instructor_profile        = 'instructor_profile';
    public $table_ratings_user              = 'session_ratings_user';
    public $table_ratings_instructor        = 'session_ratings_instructor';
    
    // ---------- CHART VARIABLES ----------
    private $chart_width                    = '600';
    private $chart_height                   = '200';
    private $chart_data_1                   = '0,';
    private $chart_data_2                   = '0,';
    private $chart_dot_size                 = '10';
    private $chart_dot_color                = 'FF9900';
    private $chart_fill_color               = '3399CC44';
    private $chart_min_value                = 0;
    private $chart_max_value                = 5;
    
    // ---------- NON-MODIFIABLE VARIABLES ----------
    public $chart_data_instructor_average   = '';
    public $chart_data_user_average         = '';
    public $sessions_id_list                = '';
    public $records                         = array();
    
    
    public function  __construct()
    {
        parent::__construct();
        
        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Description' => 'Chart sessions from database for analysis',
        );

        $this->SetParameters(func_get_args());
        $this->sessions_id_list = ($this->GetParameter(0)) ? $this->GetParameter(0) : 0;
        
        # if list was sent in with a trailing comma - strip it off
        # =======================================================================
        $len = strlen($this->sessions_id_list - 1);
        $last_char = substr($this->sessions_id_list, $len, 1);
        if ($last_char == ',') {
            $this->sessions_id_list = substr($this->sessions_id_list, 0, -1);
        }
        
    } // -------------- END __construct --------------
    
    
    public function ExecuteAjax()
    {
        if (Post('data')) {
            $this->sessions_id_list = Post('data');
            
            $len = strlen($this->sessions_id_list - 1);
            $last_char = substr($this->sessions_id_list, $len, 1);
            if ($last_char == ',') {
                $this->sessions_id_list = substr($this->sessions_id_list, 0, -1);
            }
            
            $this->Chart_Title      = (Post('title')) ? Post('title') : $this->Chart_Title;
            $this->Chart_Sub_Title  = (Post('subtitle')) ? Post('subtitle') : $this->Chart_Sub_Title;
            
            $this->Execute();
        } else {
            echo '<div clas="error_message">NO SESSION IDS PASSED IN</div>';
        }
    }
    
    
    public function Execute()
    {
        $records = $this->SQL->GetArrayAll(array(
            'table' => $this->table_session_checklists,
            'keys'  => "$this->table_session_checklists.sessions_id AS SID, $this->table_ratings_user.*, $this->table_ratings_instructor.*",
            'where' => "$this->table_session_checklists.sessions_id IN ($this->sessions_id_list)",
            'joins' => "
                LEFT JOIN $this->table_ratings_user          ON $this->table_ratings_user.sessions_id           = $this->table_session_checklists.sessions_id
                LEFT JOIN $this->table_ratings_instructor    ON $this->table_ratings_instructor.sessions_id     = $this->table_session_checklists.sessions_id
                ",
        ));
        if ($this->Show_Query) echo '<br />' . $this->SQL->Db_Last_Query;
        
        $this->records = $records;
        $this->FakeModifyScores();
        $this->CreateAndOutputSummary();
    }
    
    
    public function FakeModifyScores()
    {
        # FUNCTION :: Randomly give scores to records
        
        $records = $this->records;
        $new_records = array();
        
        foreach ($records AS $record) {
            #USER RATINGS
            $record['instructor_skill']         = rand(0,5);
            $record['instructor_knowledge']     = rand(0,5);
            $record['technical_ease']           = rand(0,5);
            $record['technical_quality_video']  = rand(0,5);
            $record['technical_quality_audio']  = rand(0,5);
    
            #INSTRUCTOR RATINGS
            $record['user_skill']               = rand(0,5);
            $record['user_knowledge']           = rand(0,5);
            $record['technical_ease']           = rand(0,5);
            $record['technical_quality_video']  = rand(0,5);
            $record['technical_quality_audio']  = rand(0,5);
            
            $new_records[] = $record;
        }
        $this->records = $new_records;
    }
    
    public function CreateAndOutputSummary()
    {
        $chart = '';
        $table = '';
        $OBJ_CHART = new General_GoogleChart();
        
        if (!$this->records) {
            echo "<div style='color:red; font-weight:bold; text-align:center;'>NO TESTS HAVE BEEN TAKEN - UNABLE TO COMPUTE SCORES</div></td>";
        } else {
        
            # make timestamp in human-readable form
            //$dt         = strtotime($updated);
            //$newtime    = date("F jS Y g:i a", $dt);
        
        
        //echo '<br />'.ArrayToStr($this->records);
            $sessions_id_legend                            = '';
            $chart_data_instructor_average      = '';
            $chart_data_user_average            = '';
            $chart_data_1    = '';
            $chart_data_2    = '';
            $chart_data_3    = '';
            $chart_data_4    = '';
            $chart_data_5    = '';
            $chart_data_6    = '';
            $chart_data_7    = '';
            $chart_data_8    = '';
            $chart_data_9    = '';
            $chart_data_10   = '';
            $chart_data_11   = '';
                
            foreach ($this->records AS $record) {
                
                
                #USER RATINGS
                $instructor_skill           = $record['instructor_skill'];
                $instructor_knowledge       = $record['instructor_knowledge'];
                $technical_ease             = $record['technical_ease'];
                $technical_quality_video    = $record['technical_quality_video'];
                $technical_quality_audio    = $record['technical_quality_audio'];
                
                $count = 0;
                $count = ($instructor_skill > 0) ? $count+1 : $count;
                $count = ($instructor_knowledge > 0) ? $count+1 : $count;
                $count = ($technical_ease > 0) ? $count+1 : $count;
                $count = ($technical_quality_video > 0) ? $count+1 : $count;
                $count = ($technical_quality_audio > 0) ? $count+1 : $count;
                
                $user_average = ($count) ? round((($instructor_skill + $instructor_knowledge + $technical_ease + $technical_quality_video + $technical_quality_audio) / $count), 0) : 0;
                
                
                #INSTRUCTOR RATINGS
                $user_skill                 = $record['user_skill'];
                $user_knowledge             = $record['user_knowledge'];
                $technical_ease             = $record['technical_ease'];
                $technical_quality_video    = $record['technical_quality_video'];
                $technical_quality_audio    = $record['technical_quality_audio'];
                
                $count = 0;
                $count = ($user_skill > 0) ? $count+1 : $count;
                $count = ($user_knowledge > 0) ? $count+1 : $count;
                $count = ($technical_ease > 0) ? $count+1 : $count;
                $count = ($technical_quality_video > 0) ? $count+1 : $count;
                $count = ($technical_quality_audio > 0) ? $count+1 : $count;
                
                $instructor_average = ($count) ? round((($user_skill + $user_knowledge + $technical_ease + $technical_quality_video + $technical_quality_audio) / $count), 0) : 0;
                

                
                $chart_data_1    .= "0,";
                $chart_data_2    .= "{$instructor_skill},";
                $chart_data_3    .= "{$instructor_knowledge},";
                $chart_data_4    .= "{$technical_ease},";
                $chart_data_5    .= "{$technical_quality_video},";
                $chart_data_6    .= "{$technical_quality_audio},";
                
                $chart_data_7    .= "{$user_skill},";
                $chart_data_8    .= "{$user_knowledge},";
                $chart_data_9    .= "{$technical_ease},";
                $chart_data_10   .= "{$technical_quality_video},";
                $chart_data_11   .= "{$technical_quality_audio},";
                
                $chart_data_instructor_average      .= "{$instructor_average},";
                $chart_data_user_average            .= "{$user_average},";
                
                $sessions_id_legend                 .= "{$record['SID']}|";
                
            }
            
            
            # trim off trailing comma
            $chart_data_1     = substr($chart_data_1, 0, -1);
            $chart_data_2     = substr($chart_data_2, 0, -1);
            $chart_data_3     = substr($chart_data_3, 0, -1);
            $chart_data_4     = substr($chart_data_4, 0, -1);
            $chart_data_5     = substr($chart_data_5, 0, -1);
            $chart_data_6     = substr($chart_data_6, 0, -1);
            $chart_data_7     = substr($chart_data_7, 0, -1);
            $chart_data_8     = substr($chart_data_8, 0, -1);
            $chart_data_9     = substr($chart_data_9, 0, -1);
            $chart_data_10    = substr($chart_data_10, 0, -1);
            $chart_data_11    = substr($chart_data_11, 0, -1);
            
            $chart_data_instructor_average      = substr($chart_data_instructor_average, 0, -1);
            $chart_data_user_average            = substr($chart_data_user_average, 0, -1);
            $sessions_id_legend                 = substr($sessions_id_legend, 0, -1);
            
            
            # make chart image
            $OBJ_CHART->InititalizeChart(array(
                'Title'       => "User and Instructor Averages",
                'Legend_X'    => $sessions_id_legend,
                'Legend_Y'    => '0|1|2|3|4|5',
                'Legend_R'    => '0|1|2|3|4|5',
                'Scale_Y_Min' => 0,
                'Scale_Y_Max' => 5,
            ));
            $OBJ_CHART->AddData(array(
                'data'      => $chart_data_user_average,
                'color'     => 'A2C180',
                'legend'    => 'Customer AVG',
            ));
            $OBJ_CHART->AddData(array(
                'data'      => $chart_data_instructor_average,
                'color'     => 'FF9900', //'3D7930',
                'legend'    => 'Instructor AVG',
            ));
            
            
            
            $chart = $OBJ_CHART->BarChart();
            $chart .= '<br /><br />';
            $chart .= $OBJ_CHART->LineChart();
            


            $datas = array(
                '2'     => $chart_data_2,
                '3'     => $chart_data_3,
                '4'     => $chart_data_4,
                '5'     => $chart_data_5,
                '6'     => $chart_data_6,
                '7'     => $chart_data_7,
                '8'     => $chart_data_8,
                '9'     => $chart_data_9,
                '10'    => $chart_data_10,
                '11'    => $chart_data_11,
            );
            $titles = array(
                '2'     => 'User Rated -> instructor_skill',
                '3'     => 'User Rated -> instructor_knowledge',
                '4'     => 'User Rated -> technical_ease',
                '5'     => 'User Rated -> technical_quality_video',
                '6'     => 'User Rated -> technical_quality_audio',
                '7'     => 'Instructor Rated -> user_skill',
                '8'     => 'Instructor Rated -> user_knowledge',
                '9'     => 'Instructor Rated -> technical_ease',
                '10'    => 'Instructor Rated -> technical_quality_video',
                '11'    => 'Instructor Rated -> technical_quality_audio',
            );
            
            for ($i=2; $i<12; $i++) {
                $OBJ_CHART->ClearData();
                $OBJ_CHART->Title = $titles[$i];
                $OBJ_CHART->AddData(array(
                    'data'      => $datas[$i],
                    'color'     => 'FF9900', //'3D7930',
                    'legend'    => '',
                ));
                $chart .= '<br /><br />';
                $chart .= $OBJ_CHART->LineChart();
            }
            
            
            
        
        } // end else loop
        
   
        # output the summary
        $summary = <<<SUMMARY
            <div class='title' style='font-size:16px; font-weight:bold;'>{$this->Chart_Title}</div>
            <div class='title' style='font-size:14px; font-weight:bold;'>{$this->Chart_Sub_Title}</div>
            <br /><br />
            {$chart}
            <br /><br />
            <div class='table'>{$table}</div>
SUMMARY;

        echo $summary;
    }
    
    
    
    
    
}  // -------------- END CLASS --------------