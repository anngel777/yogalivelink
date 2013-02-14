<?php
class Store_Categories extends BaseClass
{
    public function  __construct()
    {
        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => 'Michael Petrovich',
            'Created'     => '2010-11-30',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Description' => 'Create and manage store_categories in back-office',
        );
        
        $this->Table            = 'store_categories';
        $this->Index_Name       = 'store_categories_id';
        $this->Flash_Field      = 'store_categories_id';
        $this->Default_Where    = '';  // additional search conditions
        $this->Default_Sort     = 'store_categories_id';  // field for default table sort
        $this->Default_Fields   = 'PARENT,title,comments,display';
        $this->Default_Values   = array('display' => 1);
        $this->Unique_Fields    = '';
        
        $this->Field_Titles = array(
            'store_categories_id' => 'ID',
            
            "(SELECT CONCAT(
                IF(t7.title IS NULL, '', CONCAT(t5.title,'&rarr;')),
                IF(t6.title IS NULL, '', CONCAT(t5.title,'&rarr;')),
                IF(t5.title IS NULL, '', CONCAT(t5.title,'&rarr;')),
                IF(t4.title IS NULL, '', CONCAT(t4.title,'&rarr;')),
                IF(t3.title IS NULL, '', CONCAT(t3.title,'&rarr;')),
                IF(t2.title IS NULL, '', CONCAT(t2.title,'&rarr;')),
                t1.title) FROM `store_categories` AS t1
           LEFT JOIN `store_categories` AS `t2` ON `t2`.`store_categories_id`=`t1`.`parent_id`
           LEFT JOIN `store_categories` AS `t3` ON `t3`.`store_categories_id`=`t2`.`parent_id`
           LEFT JOIN `store_categories` AS `t4` ON `t4`.`store_categories_id`=`t3`.`parent_id`
           LEFT JOIN `store_categories` AS `t5` ON `t5`.`store_categories_id`=`t4`.`parent_id`
           LEFT JOIN `store_categories` AS `t6` ON `t5`.`store_categories_id`=`t4`.`parent_id`
           LEFT JOIN `store_categories` AS `t7` ON `t5`.`store_categories_id`=`t4`.`parent_id`
           WHERE `t1`.`store_categories_id` = `store_categories`.`store_categories_id`
        ) AS PATH" => 'Path',
           
            'title' => 'Title',
            
            "(SELECT GROUP_CONCAT(CONCAT('&bull;&nbsp;', admin_title) SEPARATOR '<br />')
                FROM `store_product_options` WHERE active=1 
                AND  CONCAT(',', `store_categories`.`product_options`, ',') LIKE CONCAT('%,', `store_product_options_id`, ',%')
            ) AS OPTIONS" => 'Product Options',
            
            "IF(display=1, 'Yes', 'No') AS DISPLAY" => 'Display',
            'comments' => 'Comments',
            'active' => 'Active',
            'updated' => 'Updated',
            'created' => 'Created'
        );

    } // -------------- END __construct --------------


    public function SetFormArrays()
    {    
        $extra_where = ($this->Action == 'EDIT')? " AND `store_categories`.`store_categories_id` != $this->Edit_Id" : '';    
        $eq_parent = EncryptQuery("ac_table=store_categories&ac_key=store_categories_id&ac_field=
        (SELECT CONCAT(
                IF(t7.title IS NULL, '', CONCAT(t5.title,'\&rarr\;')),
                IF(t6.title IS NULL, '', CONCAT(t5.title,'\&rarr\;')),
                IF(t5.title IS NULL, '', CONCAT(t5.title,'\&rarr\;')),
                IF(t4.title IS NULL, '', CONCAT(t4.title,'\&rarr\;')),
                IF(t3.title IS NULL, '', CONCAT(t3.title,'\&rarr\;')),
                IF(t2.title IS NULL, '', CONCAT(t2.title,'\&rarr\;')),
                t1.title) FROM `store_categories` AS t1
           LEFT JOIN `store_categories` AS `t2` ON `t2`.`store_categories_id`=`t1`.`parent_id`
           LEFT JOIN `store_categories` AS `t3` ON `t3`.`store_categories_id`=`t2`.`parent_id`
           LEFT JOIN `store_categories` AS `t4` ON `t4`.`store_categories_id`=`t3`.`parent_id`
           LEFT JOIN `store_categories` AS `t5` ON `t5`.`store_categories_id`=`t4`.`parent_id`
           LEFT JOIN `store_categories` AS `t6` ON `t5`.`store_categories_id`=`t4`.`parent_id`
           LEFT JOIN `store_categories` AS `t7` ON `t5`.`store_categories_id`=`t4`.`parent_id`
           WHERE `t1`.`store_categories_id` = `store_categories`.`store_categories_id`$extra_where
        )");
    
        $options_raw = $this->SQL->GetAssocArray(array(
            'table' => 'store_product_options',
            'key'   => 'store_product_options_id',
            'value' => 'admin_title',
            'where' => 'active=1',
            'order' => 'admin_title'
        ));

        $product_options_list = Form_AssocArrayToList($options_raw);

    
        $base_array = array(
            "form|$this->Action_Link|post|db_edit_form",
            'autocomplete|Parent|parent_id|N|60|255||addAutoCompleteFunctionality|' . $this->Auto_Complete_Helper . '?eq=' . $eq_parent,
            'text|Title|title|N|60|80',
            'textarea|Comments|comments|N|60|4',
            'checkbox|Display|display||1|0',
            
            'infotemplate|
<div class="forminfo">
<div style="overflow:auto;height:100px;border:1px dotted #888;">
@
</div>
</div>
',
            'checkboxlistset|Product Options|product_options|N||' . $product_options_list,
            'infotemplate|STD',
            
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

