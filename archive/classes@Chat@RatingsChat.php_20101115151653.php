<?php
class Chat_RatingsChat extends BaseClass
{
    public $ShowQuery               = false;
    public $IsTesting               = true;
    public $ShowArray               = false;
    
    public $img_delete              = '/office/images/buttons/delete.gif';
    public $img_star                = '/office/images/buttons/star.gif';    

    public $sessions_id             = 0;
    public $wh_id                   = 0;
    public $touchpoint_chats_id     = 0;
    
    public function  __construct()
    {
        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => '',
            'Description' => 'Create and manage touchpoint_chats_ratings',
            'Created'     => '2010-10-26',
            'Updated'     => '2010-10-26'
        );

        $this->Table                = 'touchpoint_chats_ratings';
        $this->Add_Submit_Name      = 'SESSION_RATINGS_USER_SUBMIT_ADD';
        $this->Edit_Submit_Name     = 'SESSION_RATINGS_USER_SUBMIT_EDIT';
        $this->Index_Name           = 'touchpoint_chats_ratings_id';
        $this->Flash_Field          = 'touchpoint_chats_ratings_id';
        $this->Default_Where        = '';  // additional search conditions
        $this->Default_Sort         = 'touchpoint_chats_ratings_id';  // field for default table sort
        $this->Default_Fields       = 'touchpoint_chats_id,wh_id,instructor_skill,instructor_knowledge,technical_ease,technical_quality_video,technical_quality_audio';
        $this->Unique_Fields        = '';

        $this->Field_Titles = array(
            'touchpoint_chats_ratings_id'   => 'touchpoint_chats_ratings Id',
            'touchpoint_chats_id'           => 'Touchpoints Chats Id',
            'wh_id'                         => 'Wh Id',
            'question_answered'             => 'Was Question Answered',
            'recommend_others'              => 'Recommend ToOthers',
            'notes'                         => 'Notes',
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
            
            "hidden|touchpoint_chats_id|$this->touchpoint_chats_id",
            "info|touchpoint_chats_id|$this->touchpoint_chats_id",
            
            "hidden|wh_id|$this->wh_id",
            "info|wh_id|$this->wh_id",
            
            'code|<br /><br />',
            '@select|<b>Was Your Question Answered?</b>&nbsp;|question_answered|N||1=Yes|0=No',
            'code|<br />',
            '@select|<b>Would you recommend our service to your friends?</b>&nbsp;|recommend_others|N||1=Yes|0=No',
            
            
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