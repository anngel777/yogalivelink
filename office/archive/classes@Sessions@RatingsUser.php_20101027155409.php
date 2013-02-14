<?php
class Sessions_RatingsUser extends BaseClass
{
    public $ShowQuery               = false;
    public $IsTesting               = true;
    public $ShowArray               = false;
    
    public $img_delete              = '/office/images/buttons/delete.gif';
    public $img_star                = '/office/images/buttons/star.gif';    

    public $sessions_id             = 0;
    public $wh_id                   = 0;
    
    
    public function  __construct()
    {
        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => '',
            'Description' => 'Create and manage session_ratings_user',
            'Created'     => '2010-10-26',
            'Updated'     => '2010-10-26'
        );

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

    

    public function SetFormArrays()
    {
        $this->AddStyle();
        $this->AddScript();
        
        $startlist = "1=&nbsp;|2=&nbsp;|3=&nbsp;|4=&nbsp;|5=&nbsp;";
    
        $base_array = array(
            
            'code|<h3>It has been a pleasure to provide services to you. Please help us improve our service by answering the following questions:</h3><br /><br />',
            
            'code|<div style="width:300px;">',
            "form|$this->Action_Link|post|db_edit_form",
            
            "hidden|sessions_id|$this->sessions_id",
            "info|sessions_id|$this->sessions_id",
            
            "hidden|wh_id|$this->wh_id",
            "info|wh_id|$this->wh_id",
            
            'code|<br /><br />',
            'info||&nbsp;&nbsp;Poor &nbsp;&nbsp;&nbsp; Excellent',
            "radioh|Instructor Skill|instructor_skill|N|star='star'|$startlist",
            "radioh|Instructor Knowledge|instructor_knowledge|N|star='star'|$startlist",
            "radioh|Technical Ease|technical_ease|N|star='star'|$startlist",
            "radioh|Technical Quality Video|technical_quality_video|N|star='star'|$startlist",
            "radioh|Technical Quality Audio|technical_quality_audio|N|star='star'|$startlist",
            'code|<br /><br />',
            
            "textarea|Notes|notes|N|4|5",
            'code|</div>',
        );

        if ($this->Action == 'ADD') {
            $base_array[] = "submit|Add Record|$this->Add_Submit_Name";
            $base_array[] = 'endform';
            $this->Form_Data_Array_Add = $base_array;
        } else {
            $base_array[] = "submit|Update Record|$this->Edit_Submit_Name";
            $base_array[] = 'endform';
            $this->Form_Data_Array_Edit = $base_array;
        }
    }

    
    protected function TriggerAfterInsert($db_last_insert_id)
    {
        $result = $this->SQL->UpdateRecord(array(
            'table'         => 'session_checklists',
            'key_values'    => "`rating_user`=1",
            'where'         => "`sessions_id`='{$this->sessions_id}'",
        ));
        
        if (!$result) {
            echo "<br />UNABLE TO UPDATE session_checklists with USER RATING";
        }
        
        echo "YOUR INPUT HAS BEEN RECEIVED<br /><br /><br />SPECIAL OFFER";
        exit();
    }
    

    public function AddStyle()
    {
        $style = "
            div.rating-cancel,div.star-rating{float:left;width:17px;height:15px;text-indent:-999em;cursor:pointer;display:block;background:transparent;overflow:hidden}
            div.rating-cancel,div.rating-cancel a{background:url({$this->img_delete}) no-repeat 0 -16px}
            div.star-rating,div.star-rating a{background:url({$this->img_star}) no-repeat 0 0px}
            div.rating-cancel a,div.star-rating a{display:block;width:16px;height:100%;background-position:0 0px;border:0}
            div.star-rating-on a{background-position:0 -16px!important}
            div.star-rating-hover a{background-position:0 -32px}
            /* Read Only CSS */
            div.star-rating-readonly a{cursor:default !important}
            /* Partial Star CSS */
            div.star-rating{background:transparent!important;overflow:hidden!important}
        ";
        
        AddStyle($style);
    }

    public function AddScript()
    {
        $script = <<<SCRIPT
            //$('.star').rating();
            $("[star='star']").rating();            
            

SCRIPT;
        AddScriptOnReady($script);
        
        
        # SCRIPT INCLUDES
        # ======================================================================
        AddScriptInclude('/jslib/jquery.rating.pack.js');
    }


}  // -------------- END CLASS --------------