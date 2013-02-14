<?php
class Website_HelpcenterFAQsCategories extends BaseClass
{
    public function  __construct()
    {
        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => '',
            'Updated'     => '',
            'Version'     => '1.0',
            'Description' => 'Manage helpcenter_categories in back-office',
        );
        
        $this->Table                = 'helpcenter_categories';
        $this->Add_Submit_Name      = 'HELPCENTER_CATEGORIES_SUBMIT_ADD';
        $this->Edit_Submit_Name     = 'HELPCENTER_CATEGORIES_SUBMIT_EDIT';
        $this->Index_Name           = 'helpcenter_categories_id';
        $this->Flash_Field          = 'helpcenter_categories_id';
        $this->Default_Where        = '';  // additional search conditions
        $this->Default_Sort         = 'helpcenter_categories_id';  // field for default table sort
        $this->Default_Fields       = 'title';
        $this->Unique_Fields        = '';
        
        $this->Default_Values       = array();
        
        $this->Field_Titles = array(
            'helpcenter_categories_id'  => 'Helpcenter Categories Id',
            'title'                     => 'Title',
            'type_customer'             => 'Customer',
            'type_instructor'           => 'Instructor',
            'active'                    => 'Active',
            'updated'                   => 'Updated',
            'created'                   => 'Created'
        );
        
    } // -------------- END __construct --------------
    
    
    public function Execute()
    {
        $this->AddRecord();
    }
    
    public function SetFormArrays()
    {
        $base_array = array(
            "form|$this->Action_Link|post|db_edit_form",
            'text|Title|title|N|60|255',
            'checkbox|Customer Category|type_customer||1|0',
            'checkbox|Instructor Category|type_instructor||1|0',
        );

        if ($this->Action == 'ADD') {
            $base_array[] = "submit|Add Record|$this->Add_Submit_Name";
            $base_array[] = 'endform';
            $this->Form_Data_Array_Add = $base_array;
        } else {
            $base_array[] = "submit|Update Record|$this->Edit_Submit_Name";
            $base_array[] = 'endform';
            $this->Form_Data_Array_Edit = $base_array;
        }
    }

}  // -------------- END CLASS --------------