<?php
// file: Store/YogaStoreCatalog.php

class Store_YogaStoreCatalog
{
    public $Product_Table         = 'store_products';
    public $Product_Options_Table = 'store_product_options';
    public $Product_Sort          = 'category, category2, category3, title';
    public $Records               = array();
    public $Categories            = array();
    public $Default_Where         = '`store_products`.`active`=1';
    public $Store_Image_Directory = '/images/store';

/*
table variables
store_products_id
part_number
manufacturer_part_number
manufacturer_id
distributor_id
category
category2
category3
title
description
description_long
price
*/
    // uppercase variable names for swap
    public $Item_Template = '
<div class="store_item">
    <div class="store_item_image"><img src="/gimage/150/images/store/@PICTURE1@" alt="@DESCRIPTION@" /></div>
    <div class="store_item_title">@TITLE@ (@PART_NUMBER@)</div>
    <div class="store_item_description">@DESCRIPTION_LONG@</div>
    <div class="store_item_options">@OPTIONS@</div>
    <div class="store_item_price">$@PRICE@</div>
    <div class="store_order_button_div"><a class="orderbutton" href="#" onclick="return shoppingCartAddItem(\'@PART_NUMBER@\');">Order Now!</a></div>
    <br style="clear:both" />
</div>
';


    public function __construct()
    {
        $this->SetSQL();
    }

    public function SetSQL()
    {
        $this->SQL = Lib_Singleton::GetInstance('Lib_Pdo');
    }

    public function GetCategories($where='')
    {
        $where = ($where)? $this->Default_Where . " AND $where" : $this->Default_Where;
        $this->Records = $this->SQL->GetFieldValues(array(
            'table' => $this->Product_Table,
            'key'  => 'category',
            'where' => $where
        ));
    }

    public function GetProducts($where='')
    {
        $where = ($where)? $this->Default_Where . " AND $where" : $this->Default_Where;
        $this->Records = $this->SQL->GetArrayAll(array(
            'table' => $this->Product_Table,
            'keys'  => '*',
            'where' => $where,
            'order' => $this->Product_Sort
        ));
    }
    
    
    
    
    public function DisplayCatalog($where='')
    {
        $this->GetProducts($where);
        if ($this->Records) {
        
            // ---------- get options ----------
            $options_db = $this->SQL->GetArrayAll(array(
                'table' => 'store_product_options',
                'keys'  => 'store_product_options_id,store_products_id,option_title,option_description,option_values,selection_type',
                'where' => 'active=1',
                'order' => 'store_product_options_id'
            ));
            $options = array();
            foreach ($options_db as $idx => $opt) {
                $key = $opt['store_products_id'];
                if (empty($options[$key])) {
                    $options[$key] = array();
                }
                $options[$key][] = $opt;
            }
            unset($options_db);  // conserve memory;
            
            foreach ($this->Records as $record) {
                $item = $this->Item_Template;
                foreach ($record as $key => $value) {
                    $item = str_replace(strtoupper("@$key@"), $value, $item);
                }               
                if (!empty($options[$record['store_products_id']])) {
                    $form = OutputForm($this->GetItemOptionForm($options[$record['store_products_id']]), 0); 
                } else {
                    $form = '';
                }
                $item = str_replace('@OPTIONS@', $form, $item);
                echo $item;
            }
        }
    }
    
    public function GetItemOptionForm($options)
    {
        // 'text' => 'Text Box',
        // 'checkboxes' => 'Check Boxes',
        // 'select' => 'Drop Down',
        // 'radio' => 'Radio (Vertical)',
        // 'radioh' => 'Radio (Horizontal)'    
        //fields -> 'store_products_id,option_title,option_description,option_values,selection_type',
        $product_id    = $options[0]['store_products_id'];
        $form = array(
            "form||post|STORE_ITEM$product_id"
        );

        foreach ($options as $option) {                    
            $product_id    = $option['store_products_id'];
            $option_id     = $option['store_product_options_id'];
            $title         = $option['option_title'];
            $description   = $option['option_description'];
                   
            $list = preg_replace('/\n+/', "|", trim($option['option_values']));
            switch ($option['selection_type']) {
                case 'text':
                    $form[] = "text|$title|OPTION_$option_id|N|40,80";
                    break;
                case 'checkboxes':
                    $form[] = "checkboxlistbar|$title|OPTION_$option_id|N||" . $list;
                    break;
                case 'select':
                    $form[] = "select|$title|OPTION_$option_id|N||" . $list;
                    break;                
                case 'radio':
                    $form[] = "radio|$title|OPTION_$option_id|N||" . $list;
                    break;
                case 'radioh':
                    $form[] = "radioh|$title|OPTION_$option_id|N||" . $list;
                    break;                
            }
        }
        $form[] = 'endform';
        return $form;
    }

} //---------------- END CLASS ----------------