<?php
class Email_EmailSentLogs extends BaseClass
{
    public function  __construct()
    {
        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => '',
            'Description' => 'Create and manage email_sent_logs',
            'Created'     => '2010-12-03',
            'Updated'     => '2010-12-03'
        );

        $this->Table  = 'email_sent_logs';

        $this->Add_Submit_Name  = 'EMAIL_SENT_LOGS_SUBMIT_ADD';
        $this->Edit_Submit_Name = 'EMAIL_SENT_LOGS_SUBMIT_EDIT';

        $this->Index_Name = 'email_sent_logs_id';

        $this->Flash_Field = 'email_sent_logs_id';

        $this->Default_Where = '';  // additional search conditions

        $this->Default_Sort  = 'email_sent_logs_id';  // field for default table sort

        $this->Field_Titles = array(
            'email_sent_logs_id' => 'Email Sent Logs Id',
            'email_q_send_id' => 'Email Q Send Id',
            'email_setups_id' => 'Email Setups Id',
            'email_complete_blob' => 'Email Complete Blob',
            'wh_id' => 'Wh Id',
            'sent_datetime' => 'Sent Datetime',
            'email_sent_datetime' => 'Email Sent Datetime',
            'email_bounced_datetime' => 'Email Bounced Datetime',
            'user_unsubscribed_datetime' => 'User Unsubscribed Datetime',
            'user_opened_datetime' => 'User Opened Datetime',
            'user_responded_datetime' => 'User Responded Datetime',
            'active' => 'Active',
            'updated' => 'Updated',
            'created' => 'Created'
        );


        $this->Default_Fields = 'email_q_send_id,email_setups_id,email_complete_blob,wh_id,sent_datetime,email_sent_datetime,email_bounced_datetime,user_unsubscribed_datetime,user_opened_datetime,user_responded_datetime';

        $this->Unique_Fields = '';

    } // -------------- END __construct --------------


    public function SetFormArrays()
    {
        $base_array = array(
            "form|$this->Action_Link|post|db_edit_form",
            'text|Email Q Send Id|email_q_send_id|N|11|11',
            'text|Email Setups Id|email_setups_id|N|11|11',
            'text|Email Complete Blob|email_complete_blob|N||',
            'text|Wh Id|wh_id|N|11|11',
            'text|Sent Datetime|sent_datetime|N||',
            'text|Email Sent Datetime|email_sent_datetime|N||',
            'text|Email Bounced Datetime|email_bounced_datetime|N||',
            'text|User Unsubscribed Datetime|user_unsubscribed_datetime|N||',
            'text|User Opened Datetime|user_opened_datetime|N||',
            'text|User Responded Datetime|user_responded_datetime|N||',
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