<?php
class General_Steps
{
    public $StepCurrent         = 0;
    public $StepPrevious        = 0;
    public $StepNext            = 0;

    public $StepCurrentContent  = '';
    public $StepArray           = '';
    public $StepTranslation     = 'Step';
    public $Style               = ''; #Overrides default style if set

    private $Step_Box_Width     = '90px';
    private $Step_Table_Padding = '5px';
    private $Step_Step_Padding  = '5px';
    public  $StepTableWidth     = '590px';
    
    public $SSL_Enabled         = false;
    public $SSL_Image           = '';
    
    
    public function  __construct()
    {
        # ==================================================================================
        # FUNCTION CALL
        #     CURSTEP - the current step
        #     ARRAY - array containing the text for each of the steps
        #     TRANS_STEP - this is the translation id or text for the word 'Step'
        # ==================================================================================
        
        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Description' => 'Manage output of multi-step pages',
        );
        
    } // -------------- END __construct --------------

    public function SetCurrentContent($content)  // mvp function
    {
        $this->StepCurrentContent = $content;
    }

    public function SetCurrentStep($current_step)  // mvp function
    {
        $this->StepCurrent = $current_step;
    }

    public function SetStepArray($steps_array)  // mvp function
    {
        $this->StepArray = $steps_array;
    }

    public function SetStepTableWidth($table_width)  // mvp function
    {
        if (!empty($table_width)) {
            $this->StepTableWidth = intOnly($table_width) . 'px';
        }
    }

    public function GetSteps($steps_array, $current_step, $content, $width='')  // mvp function
    {
        $this->SetStepArray($steps_array);
        $this->SetCurrentStep($current_step);
        $this->SetCurrentContent($content);
        $this->SetStepTableWidth($width);
        AddStyle($this->GetStyle());
        return $this->CreateStepsContent();
    }


    public function CreateStepsContent()
    {
        $steps                  = $this->CreateStepsHeader();
        $step_header            = $this->StepArray[$this->StepCurrent];
        $step_content           = $this->StepCurrentContent;
        //$step_previous          = $this->StepPrevious;
        //$step_next              = $this->StepNext;

        $output = <<<OUTPUT
            <div style="padding:25px;">
            <div class="stepwrapper">
                <div class="steps">
                    {$steps}
                </div>
                <div class="stepcontents">
                    <div class="stepheader index_header">{$step_header}</div>
                    <br />
                    {$step_content}
                </div>
                <div style="border-bottom:1px solid #d2d2d2;"></div>
            </div>
            </div>
OUTPUT;
        #<a href='@@PAGENAME@@;step={$step_previous}'>< prev</a> | <a href='@@PAGENAME@@;step={$step_next}'> next ></a>
        return $output;
    }


    public function CreateStepsHeader()
    {
        $CURRENT    = $this->StepCurrent;
        $TSTEP      = $this->StepTranslation;

        $step_first  = 999; //0;
        $step_last   = 999; //count($ARRAY)-1;

        $output = "";
        
        if ($this->SSL_Enabled) {
            $status_class    = 'stepbox_off';
            $output         .= "
    <div class=\"stepwrap_ssl\">
        <div style='line-height: 8px;'>&nbsp;</div>
        <div class=\"stepbox_ssl\">{$this->SSL_Image}</div>
    </div>";
        }
        #<div class=\"$status_class\"></div>
        
        $output .= "
    <!-- =============== STEPS =============== -->";
        foreach ($this->StepArray AS $step => $val) {
            $status_class    = ($CURRENT==$step) ? 'r_stepbox_on' : 'stepbox_off';
            $step_top        = (($step == $step_first) || ($step == $step_last)) ? '&nbsp;' : "$TSTEP $step";
            $output         .= "
    <div class=\"stepwrap\">
        <div>$step_top</div>
        <div class=\"stepbox\"><div class=\"$status_class\">&nbsp;</div></div>
        <div>$val</div>
    </div>";
        }
        //$output .= "<div style=\"clear:both;\"></div>";  //<<<<<<<<<<---------- MVP REMOVED ----------<<<<<<<<<<
        $output .= "
    <br class=\"steps_break\" />
    <!-- =============== end steps =============== -->\n\n";

        return $output;
    }

    private function CalculateStepWidth()
    {
        $pad_table      = intOnly($this->Step_Table_Padding); # Padding for entire table
        $pad_step       = intOnly($this->Step_Step_Padding);  # Padding for each step

        $steps_count    = count($this->StepArray);
        
        if ($this->SSL_Enabled) { $steps_count++; }
        
        $table_width    = intOnly($this->StepTableWidth) - (2 * $pad_table) - ($steps_count * (2 * $pad_step));

        $box_width      = $table_width / $steps_count;
        $this->Step_Box_Width = $box_width . 'px';
    }

    public function GetStyle()
    {
        $this->CalculateStepWidth();
        $STEP_TABLE_WIDTH  = $this->StepTableWidth;
        $STEP_BOX_WIDTH    = $this->Step_Box_Width;
        $STEP_PADDING      = $this->Step_Table_Padding;

        #echo "<br /> STEPTABLEWIDTH===> $STEPTABLEWIDTH";
        #echo "<br />STEPBOXWIDTH ===> $Step_Box_Width";


        # STYLE FOR REGISTRATION STEPS BOXES
        # =========================================
        $style = "
/* --------- variable css --------- */
.stepwrapper {
    width: $STEP_TABLE_WIDTH;
}
.steps, .stepwrap {
    padding: $STEP_PADDING;
}

.stepwrap , .stepbox {
    width: $STEP_BOX_WIDTH;
}
/* --------- static css --------- */
    .stepwrapper {
        border: 1px solid #d2d2d2; padding:5px;
        background-color:#e2e2e2;
    }
    .steps {
        background-color:#f2f2f2;
    }
    .stepheader {
        font-size:20px;
        font-family:verdana;
        border-bottom: 1px solid #d2d2d2;
    }
    .stepcontents {
        padding:10px 50px 50px 10px;
        background-color: #fff;
        font-size: 14px;
    }
    .stepwrap {
        float: left;
        text-align: center;
        font-size: 10px;
        width: $STEP_BOX_WIDTH;
    }
    .stepwrap_ssl {
        float: left;
        text-align: center;
        font-size: 10px;
    }
    .stepbox {
        border: 1px solid #ddd;
        padding: 3px;
    }
    .stepbox_ssl {
        border: 1px solid #ddd;
        padding: 3px;
        background-color:#92C800;
    }
    .stepbox_off {
        background-color: #fff;
        color: #000000;
        font-size: 9px;
    }
    .r_stepbox_on {
      background-color: #990000;
      color: #990000;
      font-size: 9px;
    }
    .stepbox_on {
      background-color: #0f0;
      background-image: url(/images/semi-transparent.gif);
      color: #990000;
      font-size: 9px;
    }
    .stepbox_completed {
      background-color: #888;
      background-image: url(/images/semi-transparent.gif);
      font-size: 9px;
    }
    .steps_break {
        clear : both;
    }
";

        $style = ($this->Style != '') ? $this->Style : $style;
        return $style;
    }

}  // -------------- END CLASS --------------