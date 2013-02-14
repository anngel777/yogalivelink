<?php
class Touchpoint_PhoneCall extends BaseClass
{
    public $wh_id = 0;
    
    public function  __construct()
    {
        parent::__construct();
        
        $this->SetParameters(func_get_args());
        $this->wh_id = ($this->GetParameter(0)) ? $this->GetParameter(0) : 666;
        
        $this->ClassInfo = array(
            'Created By'  => '',
            'Description' => 'Create and manage touchpoint_calls',
            'Created'     => '2010-11-18',
            'Updated'     => '2010-11-18'
        );

        $this->Table                = 'touchpoint_calls';
        $this->Add_Submit_Name      = 'TOUCHPOINT_CALLS_SUBMIT_ADD';
        $this->Edit_Submit_Name     = 'TOUCHPOINT_CALLS_SUBMIT_EDIT';
        $this->Index_Name           = 'touchpoint_calls_id';
        $this->Flash_Field          = 'touchpoint_calls_id';
        $this->Default_Where        = '';  // additional search conditions
        $this->Default_Sort         = 'touchpoint_calls_id';  // field for default table sort
        $this->Default_Fields       = 'wh_id,admin_wh_id,call_category,call_notes,shopping_order_id,followup_required';
        $this->Unique_Fields        = '';

        $this->Field_Titles = array(
            'touchpoint_calls_id' => 'Touchpoint Calls Id',
            'wh_id' => 'Wh Id',
            'admin_wh_id' => 'Admin Wh Id',
            'call_category' => 'Call Category',
            'call_notes' => 'Call Notes',
            'shopping_order_id' => 'Shopping Order Id',
            'followup_required' => 'Followup Required',
            'active' => 'Active',
            'updated' => 'Updated',
            'created' => 'Created'
        );
        
        $this->Default_Values = array(
            'admin_wh_id'   => $_SESSION['USER_LOGIN']['LOGIN_RECORD']['wh_id'],
            'wh_id'         => $this->wh_id,
        );



    } // -------------- END __construct --------------


    public function SetFormArrays()
    {
        $category_types = "Billing|Account|General";
        
        $display_value  = "CONCAT(first_name, ' ', last_name, ' - ', city, ', ', state, ' (', email_address, ') (#', wh_id, ')')";
        $eq_wh_id       = EncryptQuery("ac_table=contacts&ac_key=wh_id&ac_field={$display_value}");
        
        
        
        $style_fieldset = "style='color:#990000; font-size:14px; font-weight:bold;'";
        $base_array = array(
            "form|$this->Action_Link|post|db_edit_form",

            "fieldset|Customer Information|options_fieldset|$style_fieldset",
                "autocomplete|Customer (wh id)|wh_id|N|60|80||addAutoCompleteFunctionality|$this->Auto_Complete_Helper?eq=$eq_wh_id",
            "endfieldset",
            
            "fieldset|Call Information|options_fieldset|$style_fieldset",
                'select|Call Type|call_type|Y|||Inbound|Outbound',
                "select|Call Category|call_category|Y||$category_types",
                'textarea|Call Notes|call_notes|N|60|4',
            "endfieldset",
            
            
            'code|<br />',
            'text|Shopping Order Id|shopping_order_id|N|11|11',
            'text|Admin Wh Id|admin_wh_id|N|11|11',
            'checkbox|Followup Required|followup_required||1|0',
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