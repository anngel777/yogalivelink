<?php
class Touchpoint_ContactForm extends BaseClass
{

    public $WH_ID                           = ''; //i.e. 10000000002
    public $course                          = ''; //i.e. Private
    public $class_code                      = ''; //i.e. PVT
    
    private $test_score_array               = array();
    private $NoTestsTaken                   = false;

    # CHART VARIABLES
    # ================================================================
    private $chart_width                    = '600';
    private $chart_height                   = '200';
    private $chart_data_1                   = '0,';
    private $chart_data_2                   = '0,';
    private $chart_dot_size                 = '10';
    private $chart_dot_color                = 'FF9900';
    private $chart_fill_color               = '3399CC44';
    
    
    public $category                        = '';
    private $question                       = '';
    private $question_id_array              = array();
    private $test_questions_array           = array();

    
    # DEBUG VARIABLES
    public $clear_questions_from_category   = true; # causes a reset of re-loading questions
    public $show_all_query                  = false;
    public $show_all_array                  = false;
    
    
    public function  __construct($course='')
    {      
        parent::__construct();
        $this->course = $course;
    }
    

    
    
    private function OutputStyle()
    {
        $style = "
            .score {
                font-size:16px;
                font-weight:bold;
                text-align:center;
            }
            .title {
                font-size:16px;
                font-weight:bold;
                text-align:center;
            }
            .tbl_score {
                width:580px;
                border:1px solid #{$this->chart_dot_color};
            }
            .table {
                padding-left:20px;
            }
            
            .tbl_score th {
                font-size: 12px;
                font-weight:bold;
                background-color:#F4F2D9;
            }
            .tbl_score td {
                font-size: 12px;
                font-weight:normal;
                background-color:#fff;
            }
        ";
        AddStyle($style);
    }

    
   
    
    
    public function CreateAndOutputSummary()
    {
        $this->OutputStyle();
        
        $table = "
            <table cellpadding='5' cellspacing='2' border='0' class='tbl_score'>
            <tr>
                <th>TEST DATE</th>
                <th>CORRECT / TOTAL</th>
                <th>SCORE</th>
            </tr>";
        
        if ($this->NoTestsTaken == true) {
            $table .= "
                <tr>
                    <td colspan='3'><div style='color:red; font-weight:bold; text-align:center;'>NO TESTS HAVE BEEN TAKEN - UNABLE TO COMPUTE SCORES</div></td>
                </tr>";
        } else {
            for ($i=0; $i<count($this->test_score_array); $i++) {
                $total      = $this->test_score_array[$i]['total'];
                $correct    = $this->test_score_array[$i]['correct'];
                $wrong      = $this->test_score_array[$i]['wrong'];
                $percent    = $this->test_score_array[$i]['percent'];
                $updated    = $this->test_score_array[$i]['updated'];
                $created    = $this->test_score_array[$i]['created'];
            
                # make timestamp in human-readable form
                $dt         = strtotime($updated);
                $newtime    = date("F jS Y g:i a", $dt);
            
                # make data for table
                $table .= "
                    <tr>
                        <td>{$newtime}</td>
                        <td>{$correct} / {$total}</td>
                        <td>{$percent}</td>
                    </tr>";
                
                # make data for chart
                $percent_data           = substr($percent, 0, -1);
                $this->chart_data_1    .= "0,";
                $this->chart_data_2    .= "{$percent_data},";
            }
        } // end for loop
        
        $table .= "</table>";
        
        
        # trim off trailing comma
        $this->chart_data_1 = substr($this->chart_data_1, 0, -1);
        $this->chart_data_2 = substr($this->chart_data_2, 0, -1);
        
        # make chart image
        $chart = ($this->NoTestsTaken == true) ? '' : "<img src='http://chart.apis.google.com/chart?chxr=0,0,0&chxt=x,y&chs={$this->chart_width}x{$this->chart_height}&cht=lc&chds=0,100,0,100&chd=t:{$this->chart_data_1}|{$this->chart_data_2}&chg=25,50,0,4&chls=0.75,-1,-1|2,4,1&chm=o,{$this->chart_dot_color},1,-1,{$this->chart_dot_size}|b,{$this->chart_fill_color},0,1,0,-1' width='{$this->chart_width}' height='{$this->chart_height}' alt='' />";
        
        # output the summary
        $summary = <<<SUMMARY
            <div class='title'>SUMMARY OF TEST SCORES</div>
            <br /><br />
            {$chart}
            <br /><br />
            <div class='table'>{$table}</div>
SUMMARY;

        echo $summary;
    }
    




    
   
   
# =====================================================================================================================
# FUNCTIONS FOR INITIALIZING
# =====================================================================================================================

    public function Initialize()
    {
        # 1. Get records from database
        # =============================================================================
        $this->LoadAllScoresFromDatabase();
        $this->ShowArray($this->test_score_array, 'test_score_array'); #DEBUG
        
        
        # 2. Output details
        # =============================================================================
        $this->CreateAndOutputSummary();
    }

 
    public function LoadAllScoresFromDatabase()
    {
        # FUNCTION: GET ALL TEST SCORES FROM DATABASE
        # =========================================================
        
        
        # get all the tests_id
        $test_results = $this->SQL->GetArrayAll(array(
            'table' => 'tests',
            'keys'  => 'tests_id, class',
            'where' => "`whid` = '{$this->WH_ID}' AND active=1",
        ));

        $this->ShowLastQuery();
        
        if (count($test_results) == 0) {
            $this->NoTestsTaken = true;
            return;
        }
        
        $i = 0;
        foreach ($test_results AS $test) {
            $score_result = $this->SQL->GetArrayAll(array(
                'table' => 'tests_score',
                'keys'  => 'score_total, score_correct, score_wrong, score_percent, updated, created',
                'where' => "`tests_id` = '{$test['tests_id']}' AND active=1",
                'order' => "tests_score_id ASC",
            ));
            
            $this->ShowLastQuery();
            
            foreach ($score_result AS $score) {
                $this->test_score_array[$i]['total']      = $score['score_total'];
                $this->test_score_array[$i]['correct']    = $score['score_correct'];
                $this->test_score_array[$i]['wrong']      = $score['score_wrong'];
                $this->test_score_array[$i]['percent']    = $score['score_percent'];
                $this->test_score_array[$i]['updated']    = $score['updated'];
                $this->test_score_array[$i]['created']    = $score['created'];
                $i++;
            }
        }
    }    
    
    
    
# =====================================================================================================================
# FUNCTIONS FOR DEBUGGING
# =====================================================================================================================

    private function ShowLastQuery()
    {
        if ($this->show_all_query) {
            echo "<br />Query --> " . $this->SQL->Db_Last_Query . "<br /><br />";
        }
    }
    
    private function ShowArray($array, $name='', $force_show=false)
    {
        if ($this->show_all_array || $force_show) {
            echo "<br /><br /><hr>{$name}<hr>";
            echo ArrayToStr($array);
            echo '<hr><br /><br />';
        }
    }
    
    private function JSAlert($string)
    {
        $output = "
            <script type='text/javascript'>
                alert('{$string}');
            </script>";
        echo $output;
    }
    
    
}