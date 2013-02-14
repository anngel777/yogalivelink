<?php
class DevRichard_ToDo extends BaseClass
{
    public function  __construct()
    {

    parent::__construct();


    $this->ClassInfo = array(
        'Created By'  => 'Michael Petrovich',
        'Description' => 'Create and manage to_do',
        'Created'     => '2008-11-22',
        'Updated'     => '2008-11-22'
    );

    $this->Add_Submit_Name  = 'TO_DO_SUBMIT_EDIT';
    $this->Edit_Submit_Name = 'TO_DO_SUBMIT_EDIT';
    $this->Table  = 'to_do';
    $this->Flash_Field = '';

    $this->Field_Titles = array(
        'id' => 'Id',
        'topic' => 'Topic',
        'priority' => 'Priority',
        'description' => 'Description',
        'assign_to' => 'Assign To',
        'completion_date' => 'Completion Date',
        'status' => 'Status',
        'created_by' => 'Created By',
        'active' => 'Active',
        'updated' => 'Updated',
        'created' => 'Created'
    );

    $this->Form_Data_Array_Add = array(
        "form|$this->Action_Link|post|db_edit_form",
        "text|Topic|topic|Y|60|80",
        "selectcount|Priority|priority|Y|1|10",
        "textarea|Description|description|Y|80|10",
        "text|Assign To|assign_to|N|60|80",
        "dateYMD|Completion Date|completion_date|Y-M-D|N|NOW|5|",
        "select|Status|status|Y||N|Not Scheduled|Active|Postponed|Dropped",
        "text|Created By|created_by|Y|60|80",
        "submit|Add Record|$this->Add_Submit_Name",
        "endform"
    );

    $this->Form_Data_Array_Edit = array(
        "form|$this->Action_Link|post|db_edit_form",
        "text|Topic|topic|Y|60|80",
        "selectcount|Priority|priority|Y|1|10",
        "textarea|Description|description|Y|80|10",
        "text|Assign To|assign_to|N|60|80",
        "dateYMD|Completion Date|completion_date|Y-M-D|N|NOW|5|",
        "select|Status|status|Y||N|Not Scheduled|Active|Postponed|Dropped",
        "text|Created By|created_by|Y|60|80",
        "text|Active|active|Y|1|1",
        "submit|Update Record|$this->Edit_Submit_Name",
        "endform"
    );

    $this->Default_Fields = 'topic,priority,description,assign_to,completion_date,status,created';

    $this->Unique_Fields = '';

    $this->Table_Creation_Query = "";
    }
}