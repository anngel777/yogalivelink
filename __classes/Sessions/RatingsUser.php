<?php
class Sessions_RatingsUser extends BaseClass
{
    # used for javascript handling of form
    public $JS_Form_Name            = 'rating_user_edit_form';
    public $JS_Execute_Function     = 'ratingsUserFormSaveChanges()';
    public $JS_Execute_Button_Value = 'Submit Rating';
    
    
    public $Is_Overlay              = true;
    
    public $ShowQuery               = false;
    public $IsTesting               = true;
    public $ShowArray               = false;
    
    public $Sessions_Id             = 0;
    public $Instructor_WH_ID        = 0;
    public $Customer_WH_ID          = 0;
    
    
    public function  __construct()
    {
        parent::__construct();
        
        
        $this->SetParameters(func_get_args());
        $this->Sessions_Id          = ($this->GetParameter(0)) ? $this->GetParameter(0) : 0;
        $this->Customer_WH_ID       = ($this->GetParameter(1)) ? $this->GetParameter(1) : 0;
        $this->Instructor_WH_ID     = ($this->GetParameter(2)) ? $this->GetParameter(2) : 0;
        
        
        if ($this->Sessions_Id && (!$this->Customer_WH_ID || !$this->Instructor_WH_ID) ) {
            $this->GetWHIDFromSessionRecord();
        }
        
        if (!$this->Sessions_Id || !$this->Customer_WH_ID || !$this->Instructor_WH_ID) {
            echo "<div class='red'><h1>UNABLE TO LOAD SESSION RATING - PLEASE TRY AGAIN LATER OR CONTACT support@YogaLiveLink.com</h1></div>";
            #exit();
        }
        
        
        if (Get('wintype') == 'window') {
            $this->Is_Overlay = false;
        }
        
        $this->ClassInfo = array(
            'Created By'  => '',
            'Description' => 'Create and manage session_ratings_user',
            'Created'     => '2010-10-26',
            'Updated'     => '2010-10-26'
        );
        
        $this->Close_On_Success = false;

        $this->Table                = 'session_ratings_user';
        $this->Add_Submit_Name      = 'SESSION_RATINGS_USER_SUBMIT_ADD';
        $this->Edit_Submit_Name     = 'SESSION_RATINGS_USER_SUBMIT_EDIT';
        $this->Index_Name           = 'session_ratings_user_id';
        $this->Flash_Field          = 'session_ratings_user_id';
        $this->Default_Where        = '';  // additional search conditions
        $this->Default_Sort         = 'session_ratings_user_id';  // field for default table sort
        $this->Default_Fields       = 'sessions_id,wh_id,instructor_skill,instructor_knowledge,technical_ease,technical_quality_video,technical_quality_audio';
        $this->Unique_Fields        = '';

        $this->Field_Titles = array(
            'session_ratings_user_id'   => 'Session Ratings User Id',
            'sessions_id'               => 'Sessions Id',
            'wh_id'                     => 'Wh Id',
            'instructor_skill'          => 'Instructor Skill',
            'instructor_knowledge'      => 'Instructor Knowledge',
            'technical_ease'            => 'Technical Ease',
            'technical_quality_video'   => 'Technical Quality Video',
            'technical_quality_audio'   => 'Technical Quality Audio',
            'active'                    => 'Active',
            'updated'                   => 'Updated',
            'created'                   => 'Created'
        );


        
    } // -------------- END __construct --------------

    public function ProcessAjax()
    {
        
    }
    
    public function ExecuteAjax()
    {
        echo "ExecuteAjax()";
        $this->Execute();
    }
    
    public function GetWHIDFromSessionRecord()
    {
        # GET THE CUSTOMER AND INSTRUCTOR WH_ID
        $record = $this->SQL->GetRecord(array(
            'table' => 'sessions',
            'keys'  => 'booked_wh_id, instructor_id',
            'where' => "`sessions_id`=$this->Sessions_Id",
        ));
        if ($this->ShowQuery) echo "<br />Last Query = " . $this->SQL->Db_Last_Query;
        
        if ($record) {
            $this->Customer_WH_ID   = ($this->Customer_WH_ID == 0) ? $record['booked_wh_id'] : $this->Customer_WH_ID;
            $this->Instructor_WH_ID = ($this->Instructor_WH_ID == 0) ? $record['instructor_id'] : $this->Instructor_WH_ID;
        }
    }
    
    public function Execute()
    {
        if ($this->Sessions_Id !=0 && $this->Customer_WH_ID !=0 && $this->Instructor_WH_ID !=0) {
            $output = $this->AddRecordSpecial();
            echo $output;
        }
    }
    
    public function AddRecordSpecial()
    {
        global $FORM_VAR;
        
        # RE-POPULATE THE FORM WITH DATA
        if (Post('data')) {
            Form_AjaxToPost('data');
            //$_POST[$this->Edit_Submit_Name] = $FORM_VAR['submit_click_text'];
            $_POST[$this->Add_Submit_Name] = $FORM_VAR['submit_click_text'];
        }
        
        # CHECK TO SEE IF A RECORD ALREADY EXISTS
        $record = $this->SQL->GetRecord(array(
            'table' => $this->Table,
            'keys'  => 'session_ratings_user_id',
            'where' => "`sessions_id`={$this->Sessions_Id}",
        ));
        if ($this->ShowQuery) echo "<br />Last Query = " . $this->SQL->Db_Last_Query;
        
        if ($record) {
            $output = $this->EditRecordText($record['session_ratings_user_id']);
        } else {
            $output = $this->AddRecordText();
        }
        return $output;
    }
    
    public function SetFormArrays()
    {
        $this->AddScript();
        
        $startlist      = "1=&nbsp;|2=&nbsp;|3=&nbsp;|4=&nbsp;|5=&nbsp;";
        $startlist_num  = "1=1|2=2|3=3|4=4|5=5";
        
        $style_text     = "style='font-size:14px;'";
        $style_scale    = "style='font-size:11px; font-weight:normal;'";
        $style_wrap     = "style='border:1px solid #ccc; padding:10px;'";
    
        $base_array = array(
            
            "code|<div id='rating_user_text'>[T~RATING_USER_001]</div>",
            
            'code|<div style="width:500px;">',
            "form|$this->Action_Link|post|$this->JS_Form_Name",
            
            "hidden|sessions_id|$this->Sessions_Id",
            "hidden|wh_id|$this->Customer_WH_ID",
            
            #"info|sessions_id|$this->Sessions_Id",
            #"info|wh_id|$this->Customer_WH_ID",
            
            
            "code|<div $style_wrap>",
                "code|<div $style_text>How did you like the instructor and they way they taught?<br /></div><div $style_scale>(1=Didn't Like 5=Really Liked)</span></div>",
                "code|<div id='rate_instructor_overall'>",
                    "code|<br />",
                    "@radioh|&nbsp;|instructor_overall|N|star='star'|$startlist",
                "code|</div>",
                "code|<div $style_text><br />If you didn't like the instructor - Why not?</div>",
                '@textarea||instructor_overall_explanation|N|30|2',
            "code|</div><br /><br />",

            "code|<div $style_wrap>",
                "code|<div $style_text>How well did the instructor meet your expectations for this yoga session?<br /></div><div $style_scale>(1=Didn't Meet 5=Met Expectations)</div>",
                "code|<div id='rate_instructor_expectations'>",
                    "code|<br />",
                    "@radioh|&nbsp;|instructor_expectations|N|star='star'|$startlist",
                "code|</div>",
                "code|<div $style_text><br />If your expectations weren't met - Please Explain:</div>",
                '@textarea||instructor_expectations_explanation|N|30|2',
            "code|</div><br /><br />",

            "code|<div $style_wrap>",
                "code|<div $style_text>How easy was it to register and take your yoga session?<br /></div><div $style_scale>(1=Very Difficult 5=Very Easy)</div>",
                "code|<div id='rate_system'>",
                    "code|<br />",
                    "@radioh|&nbsp;|system_ease|N|star='star'|$startlist",
                "code|</div>",
                "code|<div $style_text><br />If it was not easy - Please Explain:</div>",
                '@textarea||system_ease_explanation|N|30|2',
            "code|</div><br /><br />",
            
            "code|<div $style_wrap>",    
                "code|<span id='rate_technical_quality_video'>",
                    "radioh|Video Quality|technical_quality_video|N|star='star'|$startlist",
                "code|</span>",
                
                "code|<span id='rate_technical_quality_audio'>",
                    "radioh|Audio Quality|technical_quality_audio|N|star='star'|$startlist",
                "code|</span>",
                
                'code|<br /><br />',
                
                "code|<div $style_text>",
                    '@select|<br /><b>Did the session start on time?</b>&nbsp;|session_start_ontime|N||1=Yes|0=No',
                    '@select|<br /><b>Did the session end on time?</b>&nbsp;|session_end_ontime|N||1=Yes|0=No',
                    '@select|<br /><b>[T~RATING_USER_009]</b>&nbsp;|recommend_others|N||1=Yes|0=No',
                    'code|<br /><br />',
                    "textarea|Additional Comments|notes|N|4|5",
                'code|</div>',
            'code|</div>',
            
            'code|</div>', //master div width
        );
        
        
        $script = "$(\"#rate_instructor_overall\").stars();";
        $script .= "$(\"#rate_instructor_expectations\").stars();";
        $script .= "$(\"#rate_system\").stars();";
        $script .= "$(\"#rate_technical_quality_video\").stars();";
        $script .= "$(\"#rate_technical_quality_audio\").stars();";
        AddScriptOnReady($script);
        
        
        
        if ($this->Action == 'ADD') {
            #$base_array[] = "button|$this->JS_Execute_Button_Value|$this->JS_Execute_Function|class=\"submitbutton\"";
            $base_array = BaseArraySpecialButtons($base_array, $this->Add_Submit_Name);
            #$base_array[] = "submit|Add Record|$this->Add_Submit_Name";
            $base_array[] = 'endform';
            $base_array[] = 'code|<br /><br />';
            $this->Form_Data_Array_Add = $base_array;
        } else {
            $base_array[] = "submit|Update Record|$this->Edit_Submit_Name";
            $base_array[] = 'endform';
            $base_array[] = 'code|<br /><br />';
            $this->Form_Data_Array_Edit = $base_array;
        }
    }

    
    protected function TriggerAfterInsert($db_last_insert_id)
    {
        $result = $this->SQL->UpdateRecord(array(
            'table'         => 'session_checklists',
            'key_values'    => "`rating_user`=1",
            'where'         => "`sessions_id`='{$this->Sessions_Id}'",
        ));
        
        if (!$result) {
            echo "<br />UNABLE TO UPDATE session_checklists with USER RATING";
        }
        
        echo "<div style='width:400px;'>&nbsp;</div>";
        echo "<h1>THANK YOU FOR PROVIDING FEEDBACK ON YOUR YOGA SESSION.</h1><br /><br /><br />";
        
        if ($this->Is_Overlay) {
            EchoScript("{$GLOBALS['JS_CLOSE_WINDOW_SCRIPT']}");
            $close_window_link = "<h1><a href='#' onclick=\"RefreshParentAndCloseOverlay();\">CLOSE WINDOW</a></h1>";
        } else {
            $close_window_link = "<h1><a href='#' onclick=\"window.close();\">CLOSE WINDOW</a></h1>";
        }
        
        echo '<br /><br /><center>'.$close_window_link.'</center><br /><br />';
    }
    
    public function SuccessfulEditRecord($flash, $id, $id_field)
    {
        echo "<div style='width:400px;'>&nbsp;</div>";
        echo "<h1>THANK YOU FOR PROVIDING FEEDBACK ON YOUR YOGA SESSION.</h1><br /><br /><br />";
        
        if ($this->Is_Overlay) {
            EchoScript("{$GLOBALS['JS_CLOSE_WINDOW_SCRIPT']}");
            $close_window_link = "<h1><a href='#' onclick=\"RefreshParentAndCloseOverlay();\">CLOSE WINDOW</a></h1>";
        } else {
            $close_window_link = "<h1><a href='#' onclick=\"window.close();\">CLOSE WINDOW</a></h1>";
        }
        
        echo '<br /><br /><center>'.$close_window_link.'</center><br /><br />';
    }
    
    
    public function AddScript()
    {
        $script = <<<SCRIPT
            //$('.star').rating();
            $("[star='star']").stars();
SCRIPT;
        AddScriptOnReady($script);
        
            AddScript("
                {$GLOBALS['JS_CLOSE_WINDOW_SCRIPT']}
                
                ");
        
        # SCRIPT INCLUDES
        # ======================================================================
        AddScriptInclude('/jslib/jquery.ui.stars.min.js');
    }


}  // -------------- END CLASS --------------