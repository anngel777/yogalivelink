<?php
class Sessions_Sessions extends BaseClass
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
            'Description' => 'Create and manage sessions in the back-office',
        );

        $this->Table                = 'sessions';
        $this->Add_Submit_Name      = 'SESSIONS_SUBMIT_ADD';
        $this->Edit_Submit_Name     = 'SESSIONS_SUBMIT_EDIT';
        $this->Index_Name           = 'sessions_id';
        $this->Flash_Field          = 'sessions_id';
        $this->Default_Where        = '';  // additional search conditions
        $this->Default_Sort         = 'sessions_id';  // field for default table sort
        $this->Default_Fields       = 'sessions_id,session_types_id,title,description,instructor_id,date,start_datetime,end_datetime,notes,display_on_website,booked,booked_wh_id,locked,locked_wh_id,locked_start_datetime';
        $this->Unique_Fields        = '';
        
        $this->Field_Titles = array(
            'sessions_id'           => 'Sessions Id',
            'session_types_id'      => 'Session Types Id',
            'title'                 => 'Title',
            'description'           => 'Description',
            'instructor_id'         => 'Instructor Id',
            'date'                  => 'Date',
            'start_datetime'        => 'Start Datetime',
            'end_datetime'          => 'End Datetime',
            'notes'                 => 'Notes',
            'display_on_website'    => 'Display On Website',
            'booked'                => 'Booked',
            'booked_wh_id'          => 'Booked Wh Id',
            'locked'                => 'Locked',
            'locked_wh_id'          => 'Locked Wh Id',
            'locked_start_datetime' => 'Locked Start Datetime',
            'active'                => 'Active',
            'updated'               => 'Updated',
            'created'               => 'Created'
        );

    } // -------------- END __construct --------------


    public function SetFormArrays()
    {
        $base_array = array(
            "form|$this->Action_Link|post|db_edit_form",
            'text|Session Types Id|session_types_id|N|11|11',
            'textarea|Title|title|N|60|4',
            'textarea|Description|description|N|60|4',
            'text|Instructor Id|instructor_id|N|11|11',
            'textarea|Date|date|N|60|4',
            'textarea|Start Datetime|start_datetime|N|60|4',
            'textarea|End Datetime|end_datetime|N|60|4',
            'textarea|Notes|notes|N|60|4',
            'checkbox|Display On Website|display_on_website||1|0',
            'checkbox|Booked|booked||1|0',
            'text|Booked Wh Id|booked_wh_id|N|20|20',
            'checkbox|Locked|locked||1|0',
            'text|Locked Wh Id|locked_wh_id|N|20|20',
            'text|Locked Start Datetime|locked_start_datetime|N||',
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