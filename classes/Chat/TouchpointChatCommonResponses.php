<?php
class Chat_TouchpointChatCommonResponses extends BaseClass
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
            'Description' => 'Create and manage touchpoint_chat_common_responses table',
        );
        
        $this->Table                = 'touchpoint_chat_common_responses';
        $this->Add_Submit_Name      = 'TOUCHPOINT_CHAT_COMMON_RESPONSES_SUBMIT_ADD';
        $this->Edit_Submit_Name     = 'TOUCHPOINT_CHAT_COMMON_RESPONSES_SUBMIT_EDIT';
        $this->Index_Name           = 'touchpoint_chat_common_responses_id';
        $this->Flash_Field          = 'touchpoint_chat_common_responses_id';
        $this->Default_Where        = '';  // additional search conditions
        $this->Default_Sort         = 'touchpoint_chat_common_responses_id';  // field for default table sort
        $this->Default_Fields       = 'title,text';
        $this->Unique_Fields        = '';
        
        $this->Field_Titles = array(
            'touchpoint_chat_common_responses_id' => 'Touchpoint Chat Common Responses Id',
            'title' => 'Title',
            'text' => 'Text',
            'active' => 'Active',
            'updated' => 'Updated',
            'created' => 'Created'
        );
        
    } // -------------- END __construct --------------
    
    public function SetFormArrays()
    {
        $base_array = array(
            "form|$this->Action_Link|post|db_edit_form",
            'textarea|Title|title|N|60|4',
            'textarea|Text|text|N|60|4',
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

    public function GetCommonResponses()
    {
        # FUNCTION :: Get all the common responses from the database table
        
        $result = $this->SQL->GetAssocArray(array(
            'table' => $this->Table,
            'key'   => 'title',
            'value' => 'text',
            'where' => 'active=1',
        ));
        
        return $result;
    }

}  // -------------- END CLASS --------------