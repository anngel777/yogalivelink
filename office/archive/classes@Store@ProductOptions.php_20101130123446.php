<?php

// FILE: Store/ProductOptions.php

class Store_ProductOptions extends BaseClass
{
    public $Selection_Types = array(
        'text' => 'Text Box',
        'checkboxes' => 'Check Boxes',
        'select' => 'Drop Down',
        'radio' => 'Radio (Vertical)',
        'radioh' => 'Radio (Horizontal)'
    );

    public $Store_Products_Id = 0;

    public function  __construct()
    {
        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => 'MVP',
            'Description' => 'Create and manage store_product_options',
            'Created'     => '2010-11-01',
            'Updated'     => '2010-11-01'
        );

        $this->Table  = 'store_product_options';
        $this->SetParameters(func_get_args());
        $this->Store_Products_Id = $this->GetParameter(0);
        if ($this->Store_Products_Id) {
            $this->AddDefaultWhere("`$this->Table`.`store_products_id`=$this->Store_Products_Id");
        }

        $this->Index_Name = 'store_product_options_id';

        $this->Flash_Field = 'store_product_options_id';

        $this->Default_Sort  = 'store_product_options_id';  // field for default table sort

        $this->Field_Titles = array(
            'store_product_options_id' => 'ID',
            'store_products_id' => 'Product ID',
            'option_title' => 'Title',
            'option_description' => 'Description',
            'option_values' => 'Values',
            'selection_type' => 'Selection Type',
            'active' => 'Active',
            'updated' => 'Updated',
            'created' => 'Created'
        );


        $this->Default_Fields = 'option_title,option_description,option_values,selection_type';

        $this->Unique_Fields = '';
        $this->Use_Selection_Tab = false;
        $this->Show_Export = false;

    } // -------------- END __construct --------------


    public function SetFormArrays()
    {
        $this->Add_Submit_Name  = 'STORE_PRODUCT_OPTIONS_SUBMIT_ADD';
        $this->Edit_Submit_Name = 'STORE_PRODUCT_OPTIONS_SUBMIT_EDIT';

        $store_products_id = $this->GetParameter(0);
        $product = $this->SQL->GetValue('store_products', 'title', "store_products_id=$store_products_id");

        $selection_type_list = Form_AssocArrayToList($this->Selection_Types);

        $base_array = array(
            "form|$this->Action_Link|post|db_edit_form",
            "hidden|store_products_id|$store_products_id",
            "info|Product|<b>$product ($store_products_id)</b>",           
            'text|Title|option_title|Y|60|80',
            'textarea|Description|option_description|N|60|4',
            'textarea|Values<br />(One per Line)|option_values|Y|60|4',
            'select|Selection Type|selection_type|Y||' . $selection_type_list,
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

    public function ProcessCell($field, &$value, &$td_options)
    {

        if ($field == 'option_values') {
            $value = preg_replace('/\n+/', "\n", trim($value));
            $value = str_replace("\n", '<br />&bull;&nbsp;', $value);
            $value = '&bull;&nbsp;' . $value;
            $td_options = 'style="white-space:nowrap;"';
        } elseif ($field == 'selection_type') {
            $value = $this->Selection_Types[$value];
        }


    }

    public function ProcessRecordCell($field, &$value, &$td_options)
    {
        $this->ProcessCell($field, $value, $td_options);
        return;
    }

    // ----------- Process a record table cell before it is output when viewing a table  ---------------
    public function ProcessTableCell($field, &$value, &$td_options, $id='')
    {
        parent::ProcessTableCell($field, $value, $td_options, $id);
        $this->ProcessCell($field, $value, $td_options);
        return;
    }

    public function GetTableHeading($colcount)
    {
        return '<tr class="TABLE_TITLE"><td colspan="'. $colcount. '">Product Options</td></tr>';
    }


}  // -------------- END CLASS --------------