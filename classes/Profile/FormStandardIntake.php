<?php
class Profile_FormStandardIntake extends BaseClass
{
    public $Show_Query                      = false;    // TRUE = output the database queries ocurring on this page
    
    // ---------- NON-MODIFIABLE VARIABLES ----------
    public $Sessions_Id                     = 0;
    public $WH_ID                           = 0;
    public $Close_On_Save                   = true;
    public $Is_Instructor                   = false;
    
    
    public function  __construct()
    {
        parent::__construct();
        
        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Description' => 'Output an intake form - Standard Session',
        );
        
        $this->SetParameters(func_get_args());
        $this->Sessions_Id      = ($this->GetParameter(0)) ? $this->GetParameter(0) : 0;
        $this->WH_ID            = ($this->GetParameter(1)) ? $this->GetParameter(1) : 0;
        $this->Is_Instructor    = ($this->GetParameter(2)) ? $this->GetParameter(2) : $this->Is_Instructor;
        
        
        $this->Close_On_Success = true;

        $this->Table                = $GLOBALS['TABLE_intake_form_standard'];
        $this->Add_Submit_Name      = 'FORM_STANDARD_SUBMIT_ADD';
        $this->Edit_Submit_Name     = 'FORM_STANDARD_SUBMIT_EDIT';
        $this->Index_Name           = 'intake_form_standard_id';
        $this->Flash_Field          = 'intake_form_standard_id';
        
        
        
        $this->Field_Titles = array(
            'emergency_contact'         => 'Emergency Contact Information:',
            'age'                       => 'Age',
            'yoga_practiced'            => 'What types of yoga have you practiced?',
            'current_activity'          => 'What do you feel is your current level of activity?',
            'wanted_from_session'       => 'What do you want out of your private yoga sessions?',
            'limitations'               => 'Describe physical limitations/injuries do you currently have, if any:',
            'goals'                     => 'Describe your fitness and well-being goals:',
            'current_sports'            => 'What sports or physical activities do you do regularly?',
            'additional_information'    => 'Additional comments:',
        );
    }
    
    public function SetSQL()
    {
        if (empty($this->SQL)) {
            $this->SQL = Lib_Singleton::GetInstance('Lib_Pdo');
        }
    }
    
    public function ExecuteAjax()
    {
        if ($this->Sessions_Id !=0 && $this->WH_ID !=0) {
            $output = $this->AddRecordSpecial();
            echo $output;
        }
    }
    
    public function Execute($RETURN=false)
    {
        # CHECK TO SEE IF A FORM ALREADY EXISTS
        $record = $this->SQL->GetRecord(array(
            'table' => $GLOBALS['TABLE_intake_form_standard'],
            'keys'  => 'intake_form_standard_id',
            'where' => "`wh_id` = {$this->WH_ID}",
        ));
        if ($this->Show_Query) echo "<br />Last Query ==> " . $this->SQL->Db_Last_Query;
        
        if ($record) {
            if($this->Is_Instructor && $this->Is_Instructor != "false") {
                $this->Default_View_Table_Options = 'cellspacing="0" cellpadding="0" class="VIEW_INTAKE_FORM_TABLE"';
                AddStyle("                
                .VIEW_INTAKE_FORM_TABLE {
                  width : 600px;
                  background-color : #eee;
                }
                .VIEW_INTAKE_FORM_TABLE  th {
                  max-width : 300px;
                  background-color : #eee;
                  padding : 5px;  
                  vertical-align : top;
                  font-size:12px;
                  font-weight:normal;
                  border-top: 1px solid #ccc;
                  border-bottom: 1px solid #ccc;
                  border-left: 1px solid #ccc;
                }
                .VIEW_INTAKE_FORM_TABLE  td {
                  background-color : #fff;
                  padding : 5px;
                  vertical-align : top;
                  font-size:14px;
                  border-top: 1px solid #ccc;
                  border-bottom: 1px solid #ccc;
                  border-right: 1px solid #ccc;
                }
                ");
                
                $output = $this->ViewRecordText($record['intake_form_standard_id'], '', $this->Index_Name);
                $output .= "<br /><br /><br /><br />";
            } else {
                $output = $this->EditRecord($record['intake_form_standard_id']);
            }
        } else {
            if($this->Is_Instructor) {
                $output = "NO FORM AVAILABLE FOR CUSTOMER";
            } else {
                $output = $this->AddRecord();
            }
        }
        
        if ($RETURN) {
            return $output;
        } else {
            echo $output;
        }
        
        /*
        if ($this->Sessions_Id !=0 && $this->WH_ID !=0) {
            $output = $this->AddRecordSpecial();
            echo $output;
        }
        */
    }
    
    public function SetFormArrays()
    {
        //$yoga_levels        = "New=Never practiced yoga before|Beginner=Beginner|Intermediate=Intermediate|Advanced=Advanced";
        //$activity_levels    = "Sedentary=Sedentary|Moderate=Moderate activity|Average=Average activity|Active=Active and fit|Athletic=Athletic|Elite=Elite Athletic";
        
        $yoga_levels        = "Never practiced yoga before|Beginner|Intermediate|Advanced";
        $activity_levels    = "Sedentary|Moderate activity|Average activity|Active and fit|Athletic|Elite Athletic";
        
        $style_fieldset     = "style='color:#990000; font-size:14px; font-weight:bold;'";
        $style_form         = "style='width:600px; font-size:12px;'";
        $style              = "style='font-size:13px;'";
        
        $base_array = array(
            "form|$this->Action_Link|post|db_edit_form",
            
            "hidden|wh_id|{$this->WH_ID}",
            
            #'titletemplate|<div style="width:10px;">@</div>',
            
            "code|<div $style_form>",
            "fieldset|Yoga Intake Form|options_fieldset|$style_fieldset",
            "code|
                We're excited to work with you in your practice of yoga. Please take a moment to tell us about your goals, training, and experience. As always, we keep your information private and secure. This information and any notes that your Yoga Instructor(s) make regarding your sessions take will only be shared with the YogaLiveLink staff and the YogaLiveLink Instructor(s) you select. 
            ",
            "endfieldset",
            "code|</div>",
            
            'code|<br />',
            
            "code|<div $style_form>",
            "fieldset|Client Information|options_fieldset|$style_fieldset",
                
                


                'info||Emergency Contact Information:',
                'textarea||emergency_contact|N|60|3',
                
                
                
                'text|Age|age|N|40|255',
                
                "code|<br /><div $style>What types of yoga have you practiced?</div>",
                #'titletemplate|<div style="width:10px;">@</div>',
                "select||yoga_practiced|N||$yoga_levels",

                "code|<br /><div $style>What do you feel is your current level of activity?</div>",
                "select||current_activity|N||$activity_levels",
                
                "code|<br /><div $style>What do you want out of your private yoga sessions?</div>",
                '@textarea||wanted_from_session|N|30|4',
                
                "code|<br /><div $style>Describe physical limitations/injuries do you currently have, if any:<br />(This information will help your Yoga Instructor select poses for you that will work with your current state.)</div>",
                '@textarea||limitations|N|30|4',
                
                "code|<br /><div $style>Describe your fitness and well-being goals:<br />(Examples: Increase strength, improve balance, meditation practice, flexibility, ease back/body pain, establish life/work balance, personal development, to prepare for a marathon/sport, etc.)</div>",
                '@textarea||goals|N|30|4',
                
                "code|<br /><div $style>What sports or physical activities do you do regularly?</div>",
                '@textarea||current_sports|N|30|4',
                
                "code|<br /><div $style>Additional comments:</div>",
                '@textarea||additional_information|N|30|4',
                
            "endfieldset",
            "code|</div>",
        );
        
        if ($this->Action == 'ADD') {
            $base_array = BaseArraySpecialButtons($base_array, $this->Add_Submit_Name);
            //$base_array[] = "submit|Add Record|$this->Add_Submit_Name"; 
            $base_array[] = 'endform';
            $this->Form_Data_Array_Add = $base_array;
        } else {
            $base_array = BaseArraySpecialButtons($base_array, $this->Edit_Submit_Name);
            //$base_array[] = "submit|Update Record|$this->Edit_Submit_Name"; 
            $base_array[] = 'endform';
            $this->Form_Data_Array_Edit = $base_array;
        }
    }
    
    protected function TriggerAfterInsert($db_last_insert_id)
    {
        if ($this->Close_On_Save) {
            AddScript("
                {$GLOBALS['JS_CLOSE_WINDOW_SCRIPT']}
                CloseOverlay();
                ");
        }
    }
    
    protected function TriggerAfterUpdate($id, $id_field='', $tables='', $span_where='', $joins='')
    {
        if ($this->Close_On_Save) {
            AddScript("
                {$GLOBALS['JS_CLOSE_WINDOW_SCRIPT']}
                CloseOverlay();
                ");
        }
    }
    
}