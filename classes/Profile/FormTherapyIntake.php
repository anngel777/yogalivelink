<?php
class Profile_FormTherapyIntake extends BaseClass
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
            'Description' => 'Output an intake form - Therapy Session',
        );
        
        $this->SetParameters(func_get_args());
        $this->Sessions_Id      = ($this->GetParameter(0)) ? $this->GetParameter(0) : 0;
        $this->WH_ID            = ($this->GetParameter(1)) ? $this->GetParameter(1) : 0;
        $this->Is_Instructor    = ($this->GetParameter(2)) ? $this->GetParameter(2) : $this->Is_Instructor;
        
        
        $this->Close_On_Success     = false;

        $this->Table                = $GLOBALS['TABLE_intake_form_therapy'];
        $this->Add_Submit_Name      = 'FORM_STANDARD_SUBMIT_ADD';
        $this->Edit_Submit_Name     = 'FORM_STANDARD_SUBMIT_EDIT';
        $this->Index_Name           = 'intake_form_therapy_id';
        $this->Flash_Field          = 'intake_form_therapy_id';
        
        $this->Default_Values = array(
            'signed_date'   => date('m-d-Y'),
            'wh_id'         => $this->WH_ID,
        );
        
        $this->Field_Titles = array(
            'emergency_phone_number'    => 'Emergency Phone number',
            'emergency_address'         => 'Home address',
            'emergency_contact'         => 'Emergency Contact Information:',
            'age'                       => 'Age',
            'work'                      => 'What do you currently do for work?',
            'referred'                  => 'Who referred you/how did you find YogaLiveLink.com Yoga Therapy?',
            'services'                  => 'Please list any services you are receiving, such as therapies or support groups:',
            'therapies'                 => 'What other therapies have you received in the past?',
            'medications'               => 'Please list any medications you are taking:',
            'motor_skills'              => 'Please describe any observations you\'ve made about your fine and gross motor skills as a result of your condition, or as a result of medications or side effects:',
            'cognitive_skills'          => 'Please describe any observations you have made about your speech/language or cognitive skills, as a result of your condition, or as a result of medications or side effects:',
            'social_life'               => 'How would you describe your social life?  How has it changed as a result of your condition?  What changes would you like to make in this area?',
            'emotional_state'           => 'How would you describe your general emotional state?',
            'typical_day'               => 'Briefly describe a typical day for you:',
            'wanted_from_therapy'       => 'What do you hope to get from this therapy approach?  What would you most hope to have addressed?',
            'additional_information'    => 'Additional Information:',
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
            'table' => $GLOBALS['TABLE_intake_form_therapy'],
            'keys'  => 'intake_form_therapy_id',
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
                
                $output = $this->ViewRecordText($record['intake_form_therapy_id'], '', $this->Index_Name);
                $output .= "<br /><br /><br /><br />";
            } else {
                $output = $this->EditRecord($record['intake_form_therapy_id']);
            }
        } else {
            if($this->Is_Instructor) {
                $output = "NO FORM AVAILABLE FOR CUSTOMER";
            } else {
                $output = $this->AddRecord();
            }
        }
        
        echo $output;
        /*
        if ($RETURN) {
        
        
            return $output;
        } else {
            echo $output;
        }
        */
        
        /*
        if ($this->Sessions_Id !=0 && $this->WH_ID !=0) {
            $output = $this->AddRecordSpecial();
            echo $output;
        }
        */
    }
    
    public function SetFormArrays()
    {
        $account_types      = "Customer|Administrator";
        $card_types         = "MasterCard|VISA|AmEx";
        $country_types      = "USA|Canada";
        $agree_types        = "1=I Agree";
        
        $style_fieldset         = "style='color:#990000; font-size:18px; font-weight:bold;'";
        $div_content_style      = "style='width:600px; font-size:14px;'";
        $div_content_style_2    = ''; //"style='max-height:500px; overflow:scroll;'";
        $div_content_style_pad  = "style='padding:20px;;'";
        
        $base_array = array(
            "form|$this->Action_Link|post|db_edit_form",
            
            "hidden|wh_id",
            "hidden|signed_date",
            
            "code|<div {$div_content_style_2}>",
            "code|<div {$div_content_style_pad}>",
            
            
            "fieldset|Yoga Therapy Intake Form|options_fieldset|$style_fieldset",
            "code|<div {$div_content_style}>",
            "code|
                Thank you for choosing YogaLiveLink.com for your Yoga Therapy. We look forward to working with you!  
                <br /><br />
                Please take some time to fill out this initial intake form. It will help your Yoga Therapist get to know you, and provide a framework for your work together. If there is any information that you would like to share at this time, but it is not addressed in this form, please feel free to add it at the end in the 'Additional Information' box.
                <br /><br /> 
                Only the Yoga Therapist(s) that you select to work with will be able to view this information. All of your private information is encrypted and is only accessible to the certified Yoga Therapist(s) whom you select. This is to keep your confidentiality intact. 
                <br /><br />
                Your Yoga Therapist(s) will take notes and keep them in your confidential file for review, and these notes, too, are only viewable by the certified Yoga Therapist(s) you select. 
            ",
            'code|</div>',
            "endfieldset",
            
            'code|<br />',
            
            "fieldset|Emergency Information|options_fieldset|$style_fieldset",
            "code|<div {$div_content_style}>",
                "code|Because Yoga Therapy involves working with existing medical conditions - we request you to provide the phone number and physical address you will be at during your session. If your instructor needs to contact emergency medical services - this information will be passed along to them.",
                "code|<br /><br />",
                'text|Phone number|emergency_phone_number|N|40|255',
                'textarea|Home address|emergency_address|N|60|3',
                'info||Emergency Contact Information:',
                'textarea||emergency_contact|N|60|3',
            'code|</div>',
            "endfieldset",
            
            'code|<br />',
            
            
            "fieldset|Client Information|options_fieldset|$style_fieldset",
                
                "code|<div {$div_content_style}>",
                'text|Age|age|N|40|255',
                
                '@info||<br />What do you currently do for work?',
                '@textarea||work|N|60|2',
                
                '@info||<br />Who referred you/how did you find YogaLiveLink.com Yoga Therapy?',
                '@textarea||referred|N|60|2',
                
                '@info||<br />Please list any services you are receiving, such as therapies or support groups:',
                '@textarea||services|N|60|2',
                
                '@info||<br />What other therapies have you received in the past?',
                '@textarea||therapies|N|60|2',
                
                '@info||<br />Please list any medications you are taking:',
                '@textarea||medications|N|60|2',
                
                '@info||<br />Please describe any observations you\'ve made about your fine and gross motor skills as a result of your condition, or as a result of medications or side effects:',
                '@textarea||motor_skills|N|60|2',
                
                '@info||<br />Please describe any observations you have made about your speech/language or cognitive skills, as a result of your condition, or as a result of medications or side effects:',
                '@textarea||cognitive_skills|N|60|2',
                
                '@info||<br />How would you describe your social life?  How has it changed as a result of your condition?  What changes would you like to make in this area?',
                '@textarea||social_life|N|60|2',
                
                '@info||<br />How would you describe your general emotional state?',
                '@textarea||emotional_state|N|60|2',
                
                '@info||<br />Briefly describe a typical day for you:',
                '@textarea||typical_day|N|60|2',
                
                '@info||<br />What do you hope to get from this therapy approach?  What would you most hope to have addressed?',
                '@textarea||wanted_from_therapy|N|60|2',
                
                '@info||<br />Additional Information:',
                '@textarea||additional_information|N|60|2',
                
            "code|</div>",
            "endfieldset",
            
            
            /*
            'code|<br />',
            
            "fieldset|Confidentiality Agreement|options_fieldset|$style_fieldset",
            "code|<div {$div_content_style}>",
                "code|
                    YogaLiveLink.com respects your privacy. All of your personal information and any information about your private Yoga Therapy sessions will not be shared with anyone outside your Yoga Therapist's practice and clinical supervisors. If needed, YogaLiveLink.com and your Yoga Therapist will receive a signed consent form from you before discussing your information with outside persons or agencies. Your Yoga Therapist also may need to break confidentiality if you indicate you are in danger of serious harm or threaten serious harm to self or others.
                    <br /><br />
                    I have read the above Confidentiality Agreement.  I understand and accept its breadth and limitations.
                ",
                "code|<br />",
                'text|Name|signed_name|N|40|255',
                #'text|Date|signed_date|N|40|255',
            "code|</div>",
            "endfieldset",
            */
            
            
            "code|</div>",
            "code|</div>",
            
        );        
        
        if ($this->Action == 'ADD') {
            $base_array[] = "submit|Save Intake Form|$this->Add_Submit_Name"; 
            $base_array[] = 'endform';
            $base_array[] = 'code|<br /><br /><br />';
            $this->Form_Data_Array_Add = $base_array;
        } else {
            $base_array[] = "submit|Save Updates|$this->Edit_Submit_Name";
            $base_array[] = 'endform';
            $base_array[] = 'code|<br /><br /><br />';
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