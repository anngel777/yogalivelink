<?php
class DevRichard_BugReport extends BaseClass
{
    public function  __construct()
    {

        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Description' => 'Create and manage website bugs',
            'Created'     => '2009-03-09',
            'Updated'     => '2009-03-09',
        );
        
        $this->Add_Submit_Name          = 'DISCOUNTS_SUBMIT_ADD';
        $this->Edit_Submit_Name         = 'DISCOUNTS_SUBMIT_EDIT';
        $this->Table                    = 'bug_report';
        $this->Flash_Field              = 'bug';
        $this->Autocomplete_Fields      = array();
        $this->Unique_Fields            = '';        
        $this->Joins                    = '';
        $this->Table_Creation_Query     = '';
        
        $this->Field_Titles = array(
            'id' => 'Id',
            'request_type' => 'Request Type',
            'reporter' => 'Reporter',
            'date_report' => 'Date Reported',
            'date_fix' => 'Date Fixed',
            'event' => 'Event',
            'url' => 'URL',
            'title' => 'Title',
            'description' => 'Description',
            'priority' => 'Priority',
            'status' => 'Status',
            'assigned_to' => 'Assigned To',
            'notes' => 'Notes',
            'fix_time_spent' => 'Fix Time Spent',
            'active' => 'Active',
            'updated' => 'Updated',
            'created' => 'Created'
        );

        $PRIORITY_LIST = "3=3 (Low)|2=2|1=1 (High)";
        $STATUS_LIST = "open|ready for review - programmer|ready for review - PM|need more info - programmer|need more info - PM|closed|working|stalled|future|rejected";
        $EVENT_LIST = "ISS NAMO|ISS EMEA|ISS APAC|Webinar|TST NAMO|TST EMEA|ICC|IRD|Back Office|Framework";
        $REQUEST_TYPE = "bug|feature";

        $base_array = array(
            "text|Bug Reporter|reporter|N|30|255",
            "dateYMD|Report Date|date_report|Y-M-D|N|2009|NOW|1",
            "code|<br/>",
            "select|Request Type|request_type|Y||$REQUEST_TYPE",
            #"select|Event/Project|event|Y||$EVENT_LIST",
            "text|Title|title|N|60|255",
            "textarea|Description|description|N|50|6",
            "textarea|Website Url|url|N|30|2",
            "code|<br/>",
            "text|Assigned To|assigned_to|N|60|255",
            "select|Priority|priority|N||$PRIORITY_LIST",
            "select|Status|status|N||$STATUS_LIST",
            "code|<br/>",
            "textarea|Developer Notes|notes|N|50|6",
            "text|Time Spent Fixing Issue|fix_time_spent|N|60|255",
            "dateYMD|Fixed Date|date_fix|Y-M-D|N|2009|NOW|1"
        );

        $this->Form_Data_Array_Edit = $this->Form_Data_Array_Add;
        
        $this->Form_Data_Array_Add = array_merge(
            array(
                "form|$this->Action_Link|post|db_edit_form"
            ),
            $base_array,
            array(
                "submit|Add Record|$this->Add_Submit_Name",
                "endform"
            )
        );
        
        $this->Form_Data_Array_Edit = array_merge(
            array(
                "form|$this->Action_Link|post|db_edit_form"
            ),
            $base_array,
            array(
                "checkbox|Active|active||1|0",
                "submit|Update Record|$this->Edit_Submit_Name",
                "endform"
            )
        );
        

        $this->Default_Fields = 'id,event,title,description,date_report,priority,status,assigned_to,notes';
        
        $this->Default_Values = array (
            'reporter'      => Session('ADMIN_LOGIN_NAME'),
            'date_report'   => date('Y-m-d'),
            'priority'      => 2,
            'status'        => 'open',
        );
        
        
        
    } // ---------- end construct -----------

    
   
    public function ProcessTableCell($field, &$value, &$td_options, $id='')
    {   
        parent::ProcessTableCell($field, $value, $td_options, $id);


        if ($field == 'status') {
            $colors = array(
                'open' => '7f7',
                'ready for review' => 'ff7',
                'needing more info' => 'fb7',
                'closed' => '',
                'working' => 'bbb',
                'stalled' =>  '777',
                'future' => 'ccc',
                'rejected' => 'f77',
            );
            
            
            if (ArrayValue($colors, $value)) {
                $td_options = "style=\"background-color:#{$colors[$value]};\"";                
            }         
        }
        
        if (($field == 'description') or ($field == 'notes')) {
            $value = TruncStr(strip_tags($value), 100);
            $value = nl2br($value);
        }

        
    }    
    public function ProcessRecordCell($field, &$value, &$td_options)
    {

        if ($field == 'description' || $field == 'notes') {
            $value = nl2br($value);
        }
    }
    
}

