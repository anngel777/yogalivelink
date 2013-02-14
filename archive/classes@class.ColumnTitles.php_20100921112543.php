<?php

// FILE: class.ColumnTitles.php

class ColumnTitles extends BaseClass
{
    public function  __construct()
    {
        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => 'MVP',
            'Description' => 'Create and manage column_titles',
            'Created'     => '2009-11-13',
            'Updated'     => '2009-11-13'
        );

        $this->Table  = 'column_titles';

        $this->Add_Submit_Name  = 'COLUMN_TITLES_SUBMIT_ADD';
        $this->Edit_Submit_Name = 'COLUMN_TITLES_SUBMIT_EDIT';

        $this->Index_Name = 'id';

        $this->Flash_Field = 'id';

        $this->Default_Where = '';  // additional search conditions

        $this->Default_Sort  = 'id';  // field for default table sort
        $this->Default_List_Size  = 100;  // field for default table sort

        $this->Field_Titles = array(
            'id' => 'Id',
            'title' => 'Title',
            'var_name' => 'Var Name',
            'sort_order' => 'Sort Order',
            'active' => 'Active',
            'updated' => 'Updated',
            'created' => 'Created'
        );


        $this->Default_Fields = 'title,var_name,sort_order';

        $this->Unique_Fields = '';

        $this->Autocomplete_Fields ='';  // associative array: field => table|field|variable

    } // -------------- END __construct --------------


    public function SetFormArrays()
    {
        $base_array = array(
            "text|Title|title|N|40|40",
            "text|Var Name|var_name|N|40|40",
            "text|Sort Order|sort_order|N|6|6",
        );

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
    }

}  // -------------- END CLASS --------------