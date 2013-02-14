<?php
class Email_EmailQFailed extends BaseClass
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
            'Description' => 'Manage email_q_bounced in back-office -> tracks email blasts that failed to send to receiver',
        );

        $this->Table                = 'email_q_failed';
        $this->Add_Submit_Name      = 'EMAIL_Q_FAILED_SUBMIT_ADD';
        $this->Edit_Submit_Name     = 'EMAIL_Q_FAILED_SUBMIT_EDIT';
        $this->Index_Name           = 'email_q_failed_id';
        $this->Flash_Field          = 'email_q_failed_id';
        $this->Default_Where        = '';  // additional search conditions
        $this->Default_Sort         = 'email_q_failed_id';  // field for default table sort
        $this->Default_Fields       = 'email_setups_id,from_name,from_email,to_name,to_email,cc_list,bcc_list,subject,content_html,content_text,attachment_1,attachment_2,attachment_3,fail_reason';
        $this->Unique_Fields        = '';
        
        $this->Field_Titles = array(
            'email_q_failed_id'     => 'Email Q Failed Id',
            'email_setups_id'       => 'Email Setups Id',
            'from_name'             => 'From Name',
            'from_email'            => 'From Email',
            'to_name'               => 'To Name',
            'to_email'              => 'To Email',
            'cc_list'               => 'Cc List',
            'bcc_list'              => 'Bcc List',
            'subject'               => 'Subject',
            'content_html'          => 'Content Html',
            'content_text'          => 'Content Text',
            'attachment_1'          => 'Attachment 1',
            'attachment_2'          => 'Attachment 2',
            'attachment_3'          => 'Attachment 3',
            'fail_reason'           => 'Fail Reason',
            'active'                => 'Active',
            'updated'               => 'Updated',
            'created'               => 'Created'
        );

    } // -------------- END __construct --------------


    public function SetFormArrays()
    {
        $base_array = array(
            "form|$this->Action_Link|post|db_edit_form",
            'text|Email Setups Id|email_setups_id|N|11|11',
            'text|From Name|from_name|N|60|255',
            'text|From Email|from_email|N|60|255',
            'text|To Name|to_name|N|60|255',
            'text|To Email|to_email|N|60|255',
            'textarea|Cc List|cc_list|N|60|4',
            'textarea|Bcc List|bcc_list|N|60|4',
            'text|Subject|subject|N|60|255',
            'textarea|Content Html|content_html|N|60|4',
            'textarea|Content Text|content_text|N|60|4',
            'text|Attachment 1|attachment_1|N||',
            'text|Attachment 2|attachment_2|N||',
            'text|Attachment 3|attachment_3|N||',
            'textarea|Fail Reason|fail_reason|N|60|4',
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