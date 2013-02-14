<?php

// FILE: /Store/Products.php

class Store_Products extends BaseClass
{
    public $Store_Image_Directory = '/images/store';
    public $Image_Types        = '.jpg,.png,.gif,.bmp';

    public function  __construct()
    {
        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => '',
            'Description' => 'Create and manage store_products',
            'Created'     => '2010-10-31',
            'Updated'     => '2010-10-31'
        );

        $this->Table  = 'store_products';

        $this->Add_Submit_Name  = 'STORE_PRODUCTS_SUBMIT_ADD';
        $this->Edit_Submit_Name = 'STORE_PRODUCTS_SUBMIT_EDIT';

        $this->Index_Name = 'store_products_id';

        $this->Flash_Field = 'store_products_id';

        $this->Default_Where = '';  // additional search conditions

        $this->Default_Sort  = 'store_products_id';  // field for default table sort

        $this->Field_Titles = array(
            'store_products_id'       => 'Store Products Id',
            'part_number'             => 'Part Number',
            'manufacturer_part_number'=> 'Manufacturer Part Number',
            'manufacturer_id'         => 'Manufacturer Id',
            'distributor_id'          => 'Distributor Id',

            "(SELECT GROUP_CONCAT(CONCAT('&bull;&nbsp;',
                IF(parent_id != 0,
                CONCAT((SELECT `title` FROM `store_categories` AS `SC` WHERE `SC`.`store_categories_id`=`SCM`.`parent_id`),
                    '&rarr;', `SCM`.`title`), `SCM`.`title`)) SEPARATOR '<br />')
                FROM `store_categories` AS `SCM`
                WHERE active=1 AND 
                CONCAT(',', `store_products`.`categories`, ',') LIKE CONCAT('%,', `SCM`.`store_categories_id`, ',%')
                ) AS GCATEGORIES" => 'Categories',

            'title'           => 'Title',
            'description'     => 'Description',
            'description_long'=> 'Description Long',
            'price'           => 'Price',
            'sale_percent'    => 'Sale Percent',
            'sale_dollar'     => 'Sale Dollar',
            'shipping'        => 'Shipping',
            'show_date'       => 'Show Date',
            'hide_date'       => 'Hide Date',
            'discounts'       => 'Discounts',
            'picture1'        => 'Picture 1',
            'picture2'        => 'Picture 2',
            
                        
            "(SELECT GROUP_CONCAT(CONCAT('&bull;&nbsp;', admin_title) SEPARATOR '<br />')
                FROM `store_product_options` WHERE active=1 
                AND  CONCAT(',', `store_products`.`product_options`, ',') LIKE CONCAT('%,', `store_product_options_id`, ',%')
            ) AS OPTIONS" => 'Product Options',
            
            'display' => 'Display on Website',
            'active' => 'Active',
            'updated' => 'Updated',
            'created' => 'Created'
        );


        $this->Default_Fields = 'part_number,GCATEGORIES,title,description,price,picture1,show_date,hide_date,display';

        $this->Unique_Fields = 'part_number';
        
        $this->Default_Values = array(
            'display' => 1,
            );

    } // -------------- END __construct --------------


    public function GetImageList()
    {
        $RESULT = '';

        $directory = RootPath($this->Store_Image_Directory);
        $ifiles = GetDirectory($directory, $this->Image_Types);

        if ($ifiles) {
            $count = 0;
            foreach ($ifiles as $file) {
                $RESULT .= "$file::style=\"background: url(/gimage/40x40$this->Store_Image_Directory/$file) no-repeat;\"|";
            }
            $RESULT = substr($RESULT, 0, -1);
        }
        return $RESULT;
    }


    public function SetFormArrays()
    {
        global $Mask_Real;
        $categories_raw = $this->SQL->GetAssocArray(array(
            'table' => 'store_categories',
            'key'   => 'store_categories_id',
            'value'   => "IF(parent_id != 0, CONCAT(
    (SELECT `title` FROM `store_categories` AS `SC` WHERE `SC`.`store_categories_id`=`store_categories`.`parent_id`), '&rarr;', `store_categories`.`title`), title) AS T",
            'where' => 'active=1',
            'order' => 'T'
        ));

        $categories_list = Form_AssocArrayToList($categories_raw);
        
        
        $options_raw = $this->SQL->GetAssocArray(array(
            'table' => 'store_product_options',
            'key'   => 'store_product_options_id',
            'value' => 'admin_title',
            'where' => 'active=1',
            'order' => 'admin_title'
        ));

        $product_options_list = Form_AssocArrayToList($options_raw);

        $image_list = $this->GetImageList();

        addStyle('
#imagelists select option {
  height : 40px;
  padding-left : 44px;
  border-bottom : 1px dotted #888;
}

#picture1 img, #picture2 img {
  border : 1px dotted #888;
}
');

        $base_array = array(
            "form|$this->Action_Link|post|db_edit_form",
            'text|Part Number|part_number|Y|30|80',
            'text|Manufacturer Part Number|manufacturer_part_number|N|30|80',
            'integer|Manufacturer Id|manufacturer_id|N|11|11',
            'integer|Distributor Id|distributor_id|N|11|11',
            'infotemplate|
<div class="forminfo">
<div style="overflow:auto;height:100px;border:1px dotted #888;">
@
</div>
</div>
',
            'checkboxlistset|Categories|categories|Y||' . $categories_list,
            'infotemplate|STD',
            'text|Title|title|Y|60|255',
            'text|Description|description|Y|60|255',
            'textarea|Description Long|description_long|N|60|4',
            'dollar|Price|price|Y|8|8',
            "integer|Sale Percent|sale_percent|N|8|8|||%",
            'dollar|Sale Dollar|sale_dollar|N|8|8',
            'text|Shipping|shipping|N|30|30',
            'datepick|Show Date|show_date|N|NOW|2|addDatePick',
            'datepick|Hide Date|hide_date|N|NOW|2|addDatePick',
            
            'checkbox|Display on Website|display||1|0',
            
            'textarea|Discounts|discounts|N|60|4',
            'code|<div id="imagelists">',
            'select|Picture 1|picture1|N|onchange="updateImages(1);"|' . $image_list,
            'info||<div id="picture1"></div>',
            'select|Picture 2|picture2|N|onchange="updateImages(2);"|' . $image_list,
            'info||<div id="picture2"></div>',
            'code|</div>',
            "js|function updateImages(pict) {
                var file = $('#FORM_picture' + pict).val();
                if (file == 'START_SELECT_VALUE') file = '';
                if (file != '') {
                    file = '<img src=\"/gimage/200/$this->Store_Image_Directory/' + file + '\" alt=\"' + file +'\" \/>';
                }
                $('#picture' + pict).html(file);
            }
            updateImages(1);
            updateImages(2);",
            
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

    public function EditRecord($id, $id_field='')  // extends parent
    {
        // $TABS = new Tabs('tab', 'tab_edit');
        $RESULT = parent::EditRecordText($id, $id_field);
        // $TABS->AddTab('Product', $RESULT);

        // $OBJ = new Store_ProductOptions($id);
        // $TABS->AddTab('Options', $OBJ->ListTableText());

        // $TABS->OutputTabs();

        $RESULT .= $this->SQL->WriteDBQueryText();
        echo $RESULT;

    }

    // public function SuccessfulAddRecord()
    // {
        // $this->Close_On_Success = false;
        // parent::SuccessfulAddRecord();
        // $eq = EncryptQuery("class=$this->Class_Name&id=$this->Last_Update_Id");
        // header("Location: edit_record?eq=$eq;DIALOGID=" . Get('DIALOGID'));
    // }


    public function ProcessCell($field, &$value, &$td_options)
    {
        switch($field) {
            case 'display':
                $value = ($value == 0) ? 'No' : 'Yes';
            break;
            case 'show_date':
            case 'hide_date':
                $value = ($value == '0000-00-00') ? '' : $value;
            break;
        }
    }

    public function ProcessRecordCell($field, &$value, &$td_options)
    {
        if (($value !='') and ($field == 'picture1' || $field == 'picture2')) {
            $value = "$value<br /><img src=\"$this->Store_Image_Directory/$value\" alt=\"$value\" />";
        } else {
            $this->ProcessCell($field, $value, $td_options);
        }
        return;
    }

    public function ProcessTableCell($field, &$value, &$td_options, $id='')
    {

        if (($value !='') and ($field == 'picture1' || $field == 'picture2')) {
            $value = "<img src=\"/gimage/40x40$this->Store_Image_Directory/$value\" alt=\"$value\" />";
        } else {
            parent::ProcessTableCell($field, $value, $td_options, $id);
            $this->ProcessCell($field, $value, $td_options);
        }

        return;
    }



}  // -------------- END CLASS --------------

