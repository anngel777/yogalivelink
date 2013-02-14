<?php

// FILE: class.Actions.php

class Actions extends BaseClass
{
    public function  __construct()
    {
        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => '',
            'Description' => 'Create and manage actions',
            'Created'     => '2009-11-30',
            'Updated'     => '2009-11-30'
        );

        $this->Table  = 'actions';

        $this->Add_Submit_Name  = 'ACTIONS_SUBMIT_ADD';
        $this->Edit_Submit_Name = 'ACTIONS_SUBMIT_EDIT';

        $this->SetParameters(func_get_args());
        $programs_id = $this->GetParameter(0);
        $opportunities_id = $this->GetParameter(1);

        if ($programs_id) {
            $this->AddDefaultWhere("`actions`.`programs_id`=$programs_id");
        }
        
        if (!$this->Is_Mgmt and !$this->Super_User) {
            $this->AddDefaultWhere("(SELECT CONCAT(',', salespeople, ',') FROM companies WHERE companies_id=programs.customer_id) LIKE '%,$this->User_Contacts_Id,%'"); 
        }

        $this->Index_Name = 'actions_id';

        $this->Flash_Field = 'actions_id';

        $this->Default_Sort  = 'actions_id';  // field for default table sort

        $this->Field_Titles = array(
            'actions_id' => 'Id',
            'actions.programs_id' => 'Program Id',
            'programs.program_name' => 'Program',
            "$this->Opportunity_Concat AS OPPORTUNITY" => 'Opportunity',
            'action' => 'Action',
            'metrics' => 'Metrics',
            "(SELECT $this->Admin_Concat FROM admin_users WHERE admin_users_id=`actions`.`owner_id`) AS OWNER"  => 'Owner',
            'due_date' => 'Due Date',
            "$this->Admin_Concat AS CREATED_BY"  => 'Created By',
            'actions.active' => 'Active',
            'actions.updated' => 'Updated',
            'actions.created' => 'Created'
        );

        $this->Join_Array = array(
            'programs'     => 'LEFT JOIN programs ON programs.programs_id=actions.programs_id',
            'opportunities'=> 'LEFT JOIN opportunities ON opportunities.opportunities_id=actions.opportunities_id',
            'admin_users'  => 'LEFT JOIN admin_users ON admin_users_id=actions.created_by_id',
            'principal_parts'=> 'LEFT JOIN principal_parts ON principal_parts.principal_parts_id=opportunities.principal_parts_id'
        );


        $this->Default_Fields = 'program_name,OPPORTUNITY,action,OWNER,due_date,CREATED_BY';

        $this->Unique_Fields = '';

    } // -------------- END __construct --------------

    public function ProcessAjax()
    {
        $action = Get('action');
        if ($action == 'line') {
            $programs_id = intOnly(Get('programs_id'));

            $ac_where = ($programs_id)? "&ac_where=opportunities.programs_id=$programs_id" : '';

            $eq_opportunities = EncryptQuery(
                "ac_table=opportunities&ac_key=opportunities_id" .
                "&ac_field=$this->Opportunity_Concat" .
                "&ac_joins=LEFT JOIN programs ON programs.programs_id=opportunities.programs_id " .
                "LEFT JOIN principal_parts ON principal_parts.principal_parts_id=opportunities.principal_parts_id" .
                $ac_where
            );

            echo "$this->Auto_Complete_Helper?eq=$eq_opportunities";
        }
        exit;
    }


    public function SetFormArrays()
    {
        $this->Default_Values['created_by_id'] = $this->User_Info['USER_ID'];

        $eq_admin =
            EncryptQuery("ac_table=admin_users&ac_key=admin_users_id&ac_field=CONCAT(first_name, ' ', last_name, ' - ', company_name)");

        $eq_program = EncryptQuery($this->Ac_Program);

        $programs_id = GetPostItem('programs_id');
        if (empty($programs_id)) {
            $programs_id = $this->GetParameter(0);
        }
        $opportunities_id = $this->GetParameter(1);

        $ac_where = ($programs_id)? "&ac_where=opportunities.programs_id=$programs_id" : '';

        $eq_opportunities = EncryptQuery(
            "ac_table=opportunities&ac_key=opportunities_id" .
            "&ac_field=$this->Opportunity_Concat" .
            "&ac_joins=LEFT JOIN programs ON programs.programs_id=opportunities.programs_id " .
            "LEFT JOIN principal_parts ON principal_parts.principal_parts_id=opportunities.principal_parts_id" .
            $ac_where
        );

        $base_array = array(
            "form|$this->Action_Link|post|db_edit_form",

            "js|
                function opportunityCompleteFunction(docId, value) {
                    var line_type = $('#FORM_line_type').val();
                    $.get('@@AJAXLINK@@&action=line&programs_id=' + value, '', function(data) {
                        if (data) {
                            $('#AC_FORM_opportunities_id').setOptions({url : data});
                        }
                    });
                }
            ",

            "autocomplete|Programs|programs_id|Y|60|255||addAutoCompleteFunctionality|$this->Auto_Complete_Helper?eq=$eq_program|opportunityCompleteFunction",
            "autocomplete|Opportunity|opportunities_id|N|60|255||addAutoCompleteFunctionality|$this->Auto_Complete_Helper?eq=$eq_opportunities",
            "textarea|Action|action|N|60|4",
            "text|Metrics|metrics|N|60|255",

            "autocomplete|Owner|owner_id|N|60|80||addAutoCompleteFunctionality|$this->Auto_Complete_Helper?eq=$eq_admin",

            'text|Due Date|due_date|N|10|10|class="date_entry"',
            'hidden|created_by_id'
        );

        if ($this->Action == 'ADD') {
            $this->Default_Values['programs_id']      = $programs_id;
            $this->Default_Values['opportunities_id'] = $opportunities_id;

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

    public function ProcessTableCell($field, &$value, &$td_options, $id='')
    {
        parent::ProcessTableCell($field, $value, $td_options, $id);

        if ($field == 'action') {
            $value = TruncStr(strip_tags($value), 100);
        }

    }



}  // -------------- END CLASS --------------