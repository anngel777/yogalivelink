<?php
class Sessions_Checklists extends BaseClass
{
    public function  __construct()
    {
        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => '',
            'Description' => 'Create and manage session_checklists',
            'Created'     => '2010-10-27',
            'Updated'     => '2010-10-27'
        );

        $this->Table  = 'session_checklists';

        $this->Add_Submit_Name  = 'SESSION_CHECKLISTS_SUBMIT_ADD';
        $this->Edit_Submit_Name = 'SESSION_CHECKLISTS_SUBMIT_EDIT';

        $this->Index_Name = 'session_checklists_id';

        $this->Flash_Field = 'session_checklists_id';

        $this->Default_Where = '';  // additional search conditions

        $this->Default_Sort  = 'session_checklists_id';  // field for default table sort

        $this->Field_Titles = array(
            'session_checklists_id' => 'Session Checklists Id',
            'sessions_id' => 'Sessions Id',
            'wh_id' => 'Wh Id',
            'paid' => 'Paid',
            'payment_id' => 'Payment Id',
            'email_booked_user_sent' => 'Email Booked User Sent',
            'email_booked_instructor_sent' => 'Email Booked Instructor Sent',
            'email_reminder_1_user_sent' => 'Email Reminder 1 User Sent',
            'email_reminder_1_instructor_sent' => 'Email Reminder 1 Instructor Sent',
            'email_reminder_2_user_sent' => 'Email Reminder 2 User Sent',
            'email_reminder_2_instructor_sent' => 'Email Reminder 2 Instructor Sent',
            'cancelled' => 'Cancelled',
            'cacelled_reason' => 'Cacelled Reason',
            'cancelled_by_wh_id' => 'Cancelled By Wh Id',
            'email_cancelled_user_sent' => 'Email Cancelled User Sent',
            'email_cancelled_instructor_sent' => 'Email Cancelled Instructor Sent',
            'login_user' => 'Login User',
            'login_user_datetime' => 'Login User Datetime',
            'login_instructor' => 'Login Instructor',
            'login_instructor_datetime' => 'Login Instructor Datetime',
            'session_started' => 'Session Started',
            'session_started_datetime' => 'Session Started Datetime',
            'session_completed' => 'Session Completed',
            'session_completed_datetime' => 'Session Completed Datetime',
            'rating_user' => 'Rating User',
            'rating_instructor' => 'Rating Instructor',
            'instructor_video_uploaded' => 'Instructor Video Uploaded',
            'active' => 'Active',
            'updated' => 'Updated',
            'created' => 'Created'
        );


        $this->Default_Fields = 'sessions_id,wh_id,paid,payment_id,email_booked_user_sent,email_booked_instructor_sent,email_reminder_1_user_sent,email_reminder_1_instructor_sent,email_reminder_2_user_sent,email_reminder_2_instructor_sent,cancelled,cacelled_reason,cancelled_by_wh_id,email_cancelled_user_sent,email_cancelled_instructor_sent,login_user,login_user_datetime,login_instructor,login_instructor_datetime,session_started,session_started_datetime,session_completed,session_completed_datetime,rating_user,rating_instructor,instructor_video_uploaded';

        $this->Unique_Fields = '';

    } // -------------- END __construct --------------


    public function SetFormArrays()
    {
        $base_array = array(
            "form|$this->Action_Link|post|db_edit_form",
            'text|Sessions Id|sessions_id|N|11|11',
            'text|Wh Id|wh_id|N|11|11',
            'checkbox|Paid|paid||1|0',
            'text|Payment Id|payment_id|N|11|11',
            'checkbox|Email Booked User Sent|email_booked_user_sent||1|0',
            'checkbox|Email Booked Instructor Sent|email_booked_instructor_sent||1|0',
            'checkbox|Email Reminder 1 User Sent|email_reminder_1_user_sent||1|0',
            'checkbox|Email Reminder 1 Instructor Sent|email_reminder_1_instructor_sent||1|0',
            'checkbox|Email Reminder 2 User Sent|email_reminder_2_user_sent||1|0',
            'checkbox|Email Reminder 2 Instructor Sent|email_reminder_2_instructor_sent||1|0',
            'checkbox|Cancelled|cancelled||1|0',
            'textarea|Cacelled Reason|cacelled_reason|N|60|4',
            'text|Cancelled By Wh Id|cancelled_by_wh_id|N|20|20',
            'checkbox|Email Cancelled User Sent|email_cancelled_user_sent||1|0',
            'checkbox|Email Cancelled Instructor Sent|email_cancelled_instructor_sent||1|0',
            'checkbox|Login User|login_user||1|0',
            'text|Login User Datetime|login_user_datetime|N||',
            'checkbox|Login Instructor|login_instructor||1|0',
            'text|Login Instructor Datetime|login_instructor_datetime|N||',
            'checkbox|Session Started|session_started||1|0',
            'text|Session Started Datetime|session_started_datetime|N||',
            'checkbox|Session Completed|session_completed||1|0',
            'text|Session Completed Datetime|session_completed_datetime|N||',
            'checkbox|Rating User|rating_user||1|0',
            'checkbox|Rating Instructor|rating_instructor||1|0',
            'checkbox|Instructor Video Uploaded|instructor_video_uploaded||1|0',
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