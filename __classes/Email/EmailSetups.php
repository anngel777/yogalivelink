<?php
class Email_EmailSetups extends BaseClass
{
    public function  __construct()
    {
        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => '',
            'Description' => 'Create and manage email_setups',
            'Created'     => '2010-12-03',
            'Updated'     => '2010-12-03'
        );

        $this->Table  = 'email_setups';

        $this->Add_Submit_Name  = 'EMAIL_SETUPS_SUBMIT_ADD';
        $this->Edit_Submit_Name = 'EMAIL_SETUPS_SUBMIT_EDIT';

        $this->Index_Name = 'email_setups_id';

        $this->Flash_Field = 'email_setups_id';

        $this->Default_Where = '';  // additional search conditions

        $this->Default_Sort  = 'email_setups_id';  // field for default table sort

        $this->Field_Titles = array(
            'email_setups_id' => 'Email Setups Id',
            'email_designs_id' => 'Email Designs Id',
            'email_content_templates_id' => 'Email Content Templates Id',
            'email_lists_query_id' => 'Email Lists Query Id',
            'created_by_wh_id' => 'Created By Wh Id',
            'send_datetime' => 'Send Datetime',
            'send_delay' => 'Send Delay',
            'setup_to_q_started_datetime' => 'Setup To Q Started Datetime',
            'setup_to_q_ended_datetime' => 'Setup To Q Ended Datetime',
            'active' => 'Active',
            'updated' => 'Updated',
            'created' => 'Created'
        );


        $this->Default_Fields = 'email_designs_id,email_content_templates_id,email_lists_query_id,created_by_wh_id,send_datetime,send_delay,setup_to_q_started_datetime,setup_to_q_ended_datetime';

        $this->Unique_Fields = '';

    } // -------------- END __construct --------------


    public function SetFormArrays()
    {
        $base_array = array(
            "form|$this->Action_Link|post|db_edit_form",
            'text|Email Designs Id|email_designs_id|N|11|11',
            'text|Email Content Templates Id|email_content_templates_id|N|11|11',
            'text|Email Lists Query Id|email_lists_query_id|N|11|11',
            'text|Created By Wh Id|created_by_wh_id|N|20|20',
            'text|Send Datetime|send_datetime|N||',
            'checkbox|Send Delay|send_delay||1|0',
            'text|Setup To Q Started Datetime|setup_to_q_started_datetime|N||',
            'text|Setup To Q Ended Datetime|setup_to_q_ended_datetime|N||',
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