<?php
class General_GoogleChart
{
    
    # CHART VARIABLES
    # ================================================================
    private $Chart_Width                    = '600';
    private $Chart_Height                   = '200';
    
    private $Chart_Dot_Size                 = '5';
    private $Chart_Dot_Color                = 'FF9900';
    private $Chart_Fill_Color               = '3399CC44';

    public $Data                    = array();
    public $Google_Link             = 'http://chart.apis.google.com/chart';
    public $Title                   = 'Default Data Chart';
    
    public $Scale_X_Min             = 0;
    public $Scale_X_Max             = 100;
    public $Scale_Y_Min             = 0;
    public $Scale_Y_Max             = 100;
    
    public $Legend_X                = '';
    public $Legend_Y                = '';
    public $Legend_R                = '';
    
    public $Label_Font_Size         = 11;
    public $Label_Color             = '000000';
    
    public $Default_Color           = '000000';
    
    public $Space_Between_Bars      = 0;
    public $Space_Between_Groupings = 13;   
    
    
    public function __construct() 
    {
        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Description' => 'Make charts and graphs using the google charts API',
        );
    } // -------------- END __construct --------------
    
    public function AddData($DATA_ARRAY)
    {
        $this->Data[] = $DATA_ARRAY;
    }
    
    public function ClearData()
    {
        $this->Data = array();
    }
    
    public function InititalizeChart($SETTING_ARRAY)
    {
        foreach($SETTING_ARRAY as $name => $value) {
            $this->{$name} = $value;
        }
    }
    
    public function PrepareChartValues($CHART_TYPE='')
    {
    
        switch($CHART_TYPE) {
            case 'line':
                $chm_symbol     = 'o';
                $cht            = 'lc';
                $label_size     = $this->Chart_Dot_Size;
                $chbh           = "";
            break;
            case 'bar':
            default:
                $chm_symbol     = 'N';
                $cht            = 'bvg';
                $label_size     = $this->Label_Font_Size;
                $chbh           = "a,{$this->Space_Between_Bars},{$this->Space_Between_Groupings}";
            break;
        }
    
        $title  = str_replace(' ', '+', $this->Title);
        
        $chco   = '';
        $chd    = '';
        $chm    = '';
        $chdl   = '';
        
        $data_count = 0;
        foreach ($this->Data as $data) {
            if ($data) {
                $chco   .= ($data['color']) ? "{$data['color']}," : "{$this->Default_Color},";
                $chd    .= ($data['data']) ? "{$data['data']}|" : "{$this->data}|";
                $chm    .= "{$chm_symbol},{$this->Label_Color},{$data_count},-1,{$label_size}|";
                
                $legend  = ($data['legend']) ? str_replace(' ', '+', $data['legend']) : '';
                $chdl   .= "{$legend}|";
            }
            
            $data_count++;
        }
        $chco   = substr($chco, 0, -1);
        $chd    = "t:" . substr($chd, 0, -1);
        $chm    = substr($chm, 0, -1);
        $chdl   = substr($chdl, 0, -1);
        
        $chxt = '';
        $chxt .= ($this->Legend_X) ? 'x,' : '';
        $chxt .= ($this->Legend_Y) ? 'y,' : '';
        $chxt .= ($this->Legend_R) ? 'r,' : '';
        $chxt = substr($chxt, 0, -1);
        
        $chxl = '';
        $chxl .= ($this->Legend_X) ? "0:|{$this->Legend_X}|" : '';
        $chxl .= ($this->Legend_Y) ? "1:|{$this->Legend_Y}|" : '';
        $chxl .= ($this->Legend_R) ? "2:|{$this->Legend_R}|" : '';
        $chxl = substr($chxl, 0, -1);
        
        $chds   = "{$this->Scale_Y_Min},{$this->Scale_Y_Max}";
        $chs    = "{$this->Chart_Width}x{$this->Chart_Height}";
        $chxr   = "0,{$this->Scale_X_Min},{$this->Scale_X_Max}|1,{$this->Scale_Y_Min},{$this->Scale_Y_Max}";
        
        
        
        
        
        /* LEGEND
        chxr = axis range
        chxt = what axis to show labels
        chxl = x-axis labels
        chdl = legend text
        chco = legend color
        */
        
        
        $link = "
            {$this->Google_Link}
            ?chxr={$chxr}
            &chds={$chds}
            &chxt={$chxt}
            &chxl={$chxl}
            &chbh={$chbh}
            &chs={$chs}
            &cht={$cht}
            &chtt={$title}
            &chdl={$chdl}
            &chco={$chco}
            &chd={$chd}
            &chm={$chm}
            ";
            
        return $link;
    }
    
    public function BarChart()
    {
        $link   = $this->PrepareChartValues('bar');
        $link   = str_replace(' ', '', $link);
        $chart  = "<img src='{$link}' width='{$this->Chart_Width}' height='{$this->Chart_Height}' alt='' />";
        
        return $chart;       
    }
    
    
    public function LineChart()
    {
        $link   = $this->PrepareChartValues('line');
        $link   = str_replace(' ', '', $link);
        $chart  = "<img src='{$link}' width='{$this->Chart_Width}' height='{$this->Chart_Height}' alt='' />";
        
        return $chart;       
    }
    
}  // -------------- END CLASS --------------