<?php
class Chat_TouchpointChatSettings extends BaseClass
{
    public function  __construct()
    {
        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Description' => 'Create and manage touchpoint_chat_settings table',
        );
        
        $this->Table                    = 'touchpoint_chat_settings';
        $this->Add_Submit_Name          = 'TOUCHPOINT_CHAT_SETTINGS_SUBMIT_ADD';
        $this->Edit_Submit_Name         = 'TOUCHPOINT_CHAT_SETTINGS_SUBMIT_EDIT';
        $this->Index_Name               = 'touchpoint_chat_settings_id';
        $this->Flash_Field              = 'touchpoint_chat_settings_id';
        $this->Default_Where            = '';  // additional search conditions
        $this->Default_Sort             = 'touchpoint_chat_settings_id';  // field for default table sort
        $this->Default_Fields           = 'setting_name,setting_value';
        $this->Unique_Fields            = '';
        
        $this->Field_Titles = array(
            'touchpoint_chat_settings_id' => 'Touchpoint Chat Settings Id',
            'setting_name' => 'Setting Name',
            'setting_value' => 'Setting Value',
            'active' => 'Active',
            'updated' => 'Updated',
            'created' => 'Created'
        );


        

    } // -------------- END __construct --------------


    public function SetFormArrays()
    {
        $base_array = array(
            "form|$this->Action_Link|post|db_edit_form",
            'textarea|Setting Name|setting_name|N|60|4',
            'textarea|Setting Value|setting_value|N|60|4',
            'checkbox|Active|active||1|0',
        );

        if ($this->Action == 'ADD') {
            $base_array[] = "submit|Add Record|$this->Add_Submit_Name";
            $base_array[] = 'endform';
            $this->Form_Data_Array_Add = $base_array;
        } else {
            $base_array[] = 'checkbox|Active|active||1|0';
            $base_array[] = "submit|Update Record|$this->Edit_Submit_Name";
            $base_array[] = 'endform';
            $this->Form_Data_Array_Edit = $base_array;
        }
    }


}  // -------------- END CLASS --------------