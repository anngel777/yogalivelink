<?php

// FILE: classes/Store/Categories.php

class Store_Categories extends BaseClass
{
    public function  __construct()
    {
        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => 'Michael Petrovich',
            'Description' => 'Create and manage store_categories',
            'Created'     => '2010-11-30',
            'Updated'     => '2010-11-30'
        );

        $this->Table  = 'store_categories';

        $this->Index_Name = 'store_categories_id';

        $this->Flash_Field = 'store_categories_id';

        $this->Default_Where = '';  // additional search conditions

        $this->Default_Sort  = 'store_categories_id';  // field for default table sort

        $this->Field_Titles = array(
            'store_categories_id' => 'ID',
            //'parent_id' => 'Parent ID',            
            "IF(parent_id != 0, (SELECT `title` FROM `store_categories` AS `SC` WHERE `SC`.`store_categories_id`=`store_categories`.`parent_id`), '') AS PARENT" => 'Parent',
            
            'title' => 'Title',
            'comments' => 'Comments',
            "IF(display=1, 'Yes', 'No') AS DISPLAY" => 'Display',
            'active' => 'Active',
            'updated' => 'Updated',
            'created' => 'Created'
        );


        $this->Default_Fields = 'PARENT,title,comments,display';

        $this->Default_Values = array('display' => 1);

        $this->Unique_Fields = '';

    } // -------------- END __construct --------------


    public function SetFormArrays()
    {    
        $extra_where = ($this->Action == 'EDIT')? "&ac_where=store_categories_id != $this->Edit_Id" : '';    
        $eq_parent = EncryptQuery("ac_table=store_categories&ac_key=store_categories_id&ac_field=CONCAT(
    IF(parent_id != 0, CONCAT((SELECT `title` FROM `store_categories` WHERE `store_categories_id`=`parent_id`),'\&rarr\;'),''), title)$extra_where");

    
        $base_array = array(
            "form|$this->Action_Link|post|db_edit_form",
            'autocomplete|Parent|parent_id|N|60|255||addAutoCompleteFunctionality|' . $this->Auto_Complete_Helper . '?eq=' . $eq_parent,
            'text|Title|title|N|60|80',
            'textarea|Comments|comments|N|60|4',
            'checkbox|Display|display||1|0',
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

