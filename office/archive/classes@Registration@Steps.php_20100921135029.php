<?php
class Registration_Steps
{
    public $StepCurrent         = 0;
    public $StepPrevious        = 0;
    public $StepNext            = 0;
    
    public $StepCurrentContent  = '';
    public $StepArray           = '';
    public $StepTranslation     = 'Step';
    public $Style               = ''; #Overrides default style if set
    
    private $StepBoxWidth       = '90px';
    private $StepTablePadding   = '5px';
    private $StepStepPadding    = '5px';
    public $StepTableWidth      = '590px';
    
    
    public function  __construct()
    {
        $Description = "
        # ==================================================================================
        # FUNCTION CALL
        #     CURSTEP - the current step
        #     ARRAY - array containing the text for each of the steps
        #     TRANS_STEP - this is the translation id or text for the word 'Step'
        # ==================================================================================";

        $this->ClassInfo = array(
            'Created By'  => 'RAW',
            'Description' => 'Generic Registration Class',
            'Created'     => '2010-04-10',
            'Updated'     => '2010-04-11',
            'Description' => $Description,
        );        
    } // -------------- END __construct --------------

    
    public function CreateStepsContent()
    {
        $this->CalculateStepWidth();
        $Steps                  = $this->CreateStepsHeader();
        $StepsHeader            = $this->StepArray[$this->StepCurrent];
        $StepsContent           = $this->StepCurrentContent;
        $StepPrevious           = $this->StepPrevious;
        $StepNext               = $this->StepNext;
        
        $output = <<<OUTPUT
            <div style="padding:25px;">
            <div class="stepwrapper">
                <div class="steps">
                    {$Steps}
                </div>
                <div class="stepcontents">
                    <div class="stepheader">{$StepsHeader}</div>
                    <div style="color:#990000; font-size:11px;">Fields marked with a <span class="formrequired" style="font-size:16px;">&middot;</span> are required.</div>
                    <br />
                    {$StepsContent}
                </div>
                <div style="border-bottom:1px solid #d2d2d2;"></div>
                <br />
                <div style="color:#990000; padding:10px;">NOTE: You cannot go back to previous registration steps. If you make any mistakes you will need to restart the registration process by clicking [<a href='@@PAGENAME@@;step=0'>here</a>].</div>
                
            </div>
            </div>
OUTPUT;
        #<a href='@@PAGENAME@@;step={$StepPrevious}'>< prev</a> | <a href='@@PAGENAME@@;step={$StepNext}'> next ></a>
        return $output;
    }
    
    private function CalculateStepWidth()
    {
    
        $Pad_Table      = intOnly($this->StepTablePadding); # Padding for entire table
        $Pad_Step       = intOnly($this->StepStepPadding);  # Padding for each step
    
        $Steps_Count    = count($this->StepArray);
        $Table_Width    = intOnly($this->StepTableWidth) - (2 * $Pad_Table) - ($Steps_Count * (2 * $Pad_Step));
        $Box_Width      = $Table_Width / $Steps_Count;
        
        #echo "<br /> Table_Width ===> $Table_Width";
        #echo "<br /> Steps_Count ===> $Steps_Count";
        #echo "<br /> Box_Width ===> $Box_Width";
        
        $this->StepBoxWidth = "{$Box_Width}px";
    }
    
    public function CreateStepsHeader()
    {
        $CURRENT    = $this->StepCurrent;
        $ARRAY      = $this->StepArray;
        $TSTEP      = $this->StepTranslation;
        
        $StepFirst  = 0;
        $StepLast   = count($ARRAY)-1;
        
        $output = '';
        foreach ($ARRAY AS $step => $val) {
            $statusClass    = ($CURRENT==$step) ? 'r_stepbox_on' : 'stepbox_off';
            $stepTop        = (($step == $StepFirst) || ($step == $StepLast)) ? '&nbsp;' : "$TSTEP $step";
            $output        .= "
                <div class='stepwrap'>
                    <div>$stepTop</div>
                    <div class='stepbox'><div class='$statusClass'>&nbsp;</div></div>
                    <div>$val</div>
                </div>";
        }
        $output .= "<div style='clear:both;'></div>";

        return $output;
    }
    
    public function GetStyle()
    {
        $STEPBOXWIDTH       = $this->StepBoxWidth;
        $STEPTABLEWIDTH     = $this->StepTableWidth;
        
        #echo "<br /> STEPTABLEWIDTH===> $STEPTABLEWIDTH";
        #echo "<br />STEPBOXWIDTH ===> $STEPBOXWIDTH";
        
        
        # STYLE FOR REGISTRATION STEPS BOXES
        # =========================================
        $style = "
            .stepwrapper {
                border: 1px solid #d2d2d2; padding:5px;
                background-color:#e2e2e2;
                width: $STEPTABLEWIDTH;
            }        
            .steps {
                background-color:#f2f2f2;
                padding:{$this->StepTablePadding};
                /*border-bottom: 1px solid #d2d2d2;*/
            }
            .stepheader {
                font-size:20px; 
                font-family:verdana;
                border-bottom: 1px solid #d2d2d2;
            }
            .stepcontents {
                padding:10px 50px 50px 10px;
            }
            .stepwrap {
                float: left;
                text-align: center;
                font-size: 10px;
                padding: {$this->StepStepPadding};
                width: $STEPBOXWIDTH;
            }
            .stepbox {
                border: 1px solid #ddd;
                padding: 3px;
                
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
              /* background-color: #990000; */
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
        ";
        
        $style = ($this->Style != '') ? $this->Style : $style;
        return $style;
    }
    
}  // -------------- END CLASS --------------