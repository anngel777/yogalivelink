<?php
class Store_Promotions extends BaseClass
{
    public function  __construct()
    {
        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => 'Michael Petrovich',
            'Created'     => '2010-11-01',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Description' => 'Create and manage store_promotions in back-office',
        );
        
        $this->Table                = 'store_promotions';
        $this->Add_Submit_Name      = 'STORE_PROMOTIONS_SUBMIT_ADD';
        $this->Edit_Submit_Name     = 'STORE_PROMOTIONS_SUBMIT_EDIT';
        $this->Index_Name           = 'store_promotions_id';
        $this->Flash_Field          = 'store_promotions_id';
        $this->Default_Where        = '';  // additional search conditions
        $this->Default_Sort         = 'store_promotions_id';  // field for default table sort
        $this->Default_Fields       = 'promotional_code,description,start_date,end_date';
        $this->Unique_Fields        = '';

        $this->Field_Titles = array(
            'store_promotions_id'   => 'Store Promotions Id',
            'promotional_code'      => 'Promotional Code',
            'description'           => 'Description',
            'start_date'            => 'Start Date',
            'end_date'              => 'End Date',
            'special_offer'         => 'Special Offer',
            'active'                => 'Active',
            'updated'               => 'Updated',
            'created'               => 'Created'
        );

    } // -------------- END __construct --------------


    public function SetFormArrays()
    {
        $base_array = array(
            "form|$this->Action_Link|post|db_edit_form",
            'text|Promotional Code|promotional_code|N|30|60',
            'text|Description|description|N|60|255',
            'pickdate|Start Date|start_date|N|NOW|5|addDatePick',
            'pickdate|End Date|end_date|N|NOW|5|addDatePick',
            'textarea|Special Offer|special_offer|N|60|4',
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

