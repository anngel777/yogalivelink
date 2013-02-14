<?php
// file: Store/YogaStoreCatalog.php

class Store_YogaStoreCatalog
{
    public $Product_Table         = 'store_products';
    public $Product_Options_Table = 'store_product_options';
    public $Product_Sort          = 'title';
    public $Products_Array        = array();
    public $Categories            = array();
    public $Category_Array        = array();
    public $Category_Menu         = '';
    public $Default_Where         = '`store_products`.`active`=1';
    public $Store_Image_Directory = '/images/store';
    public $Options_Array         = array();

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

<!-- =========================== STORE ITEM =========================== -->
<div class="store_item@HAVE_ITEM_CLASS@" id="store_item_@STORE_PRODUCTS_ID@">
    <div class="store_item_image"><img src="/gimage/150/images/store/@PICTURE1@" alt="@DESCRIPTION@" /></div>
    <div class="store_item_title">@TITLE@ (@PART_NUMBER@)</div>
    <div class="store_item_description">@DESCRIPTION_LONG@</div>
    <div class="store_item_options">@OPTIONS@</div>
    <div class="store_item_price">$@PRICE@</div>
    <div class="store_order_button_div"><a class="orderbutton" href="#" onclick="return shoppingCartAddItem(@STORE_PRODUCTS_ID@);">Order Now!</a></div>
    <br style="clear:both" />
</div>
<!-- ========================== end store item ========================== -->

';


    public function __construct()
    {
        $this->SetSQL();
    }

    public function SetSQL()
    {
        $this->SQL = Lib_Singleton::GetInstance('Lib_Pdo');
    }

    public function RecursiveCategoryBuild($id = 0)
    {
        $RESULT = array();
        foreach ($this->Categories as $cat_id => $record) {
            if ($record['parent_id'] == $id) {
                $RESULT[$cat_id] = $this->RecursiveCategoryBuild($cat_id);
            }
        }
        return $RESULT;
    }

    public function ItemInCart($pn)
    {
        if (!empty($_SESSION['STORE_ORDER']['CART'])) {
            $cart_items = $_SESSION['STORE_ORDER']['CART'];
            foreach ($cart_items as $part_number => $qty) {
                if (strTo($part_number, '~') == $pn) {
                    return true;
                }
            }
        }
        return false;
    }

    public function GetCategories($where='')
    {
        $where = "`store_categories`.`display`=1 AND `store_categories`.`active`=1";
        $where = ($where)? $where . " AND $where" : $where;

        $this->Categories = $this->SQL->GetArrayAssoc(array(
            'table' => 'store_categories',
            'reference_key' => 'store_categories_id',
            'keys'  => 'parent_id,title,product_options',
            'where' => $where
        ));

        $this->Category_Array = $this->RecursiveCategoryBuild();

    }
    
    public function RecursiveCategoryMenuBuild($cat_array, $level=0)
    {
        static $menu_id_count = 0;
        
        $RESULT = '';        
        $menu_id_count++;
        
        if ($level==0) {
            $RESULT .= "\n<!-- START CATEGORY MENU -->\n<div id=\"category_menu_group\">\n";
        }
        foreach ($cat_array as $id => $array) {
            $RESULT .= '<a href="">' . $this->Categories[$id]['title'] . "</a>\n";
            if (!empty($array)) {
                $RESULT .= "\n<div style=\"display:none;\" id=\"category_menu_group$menu_id_count\">\n";
                $RESULT .= $this->RecursiveCategoryMenuBuild($array, $level+1);
                $RESULT .= "</div>\n";
            }
        }
        if ($level==0) {
            $RESULT .= "aaa</div>\n<!-- END CATEGORY MENU -->\n";
        }
        return $RESULT;
    }
    
    public function DisplayCategoryMenu()
    {
        if (empty($this->Categories)) {
            $this->GetCategories();
        }
        $this->Category_Menu = '';
        echo $this->RecursiveCategoryMenuBuild($this->Category_Array);
    }

    public function GetItemCategories($product_id)
    {
        $item_categories = explode(',', $this->Products_Array[$product_id]['categories']);
        $RESULT = $item_categories;
        if ($item_categories) {
            foreach ($item_categories as $cat) {
                $parent_id = $this->Categories[$cat]['parent_id'];
                while ($parent_id != 0) {
                    $RESULT[] = $parent_id;
                    $parent_id = $this->Categories[$parent_id]['parent_id'];
                }
            }
        }
        return $RESULT;
    }

    public function GetProducts($where='')
    {
        $where = ($where)? $this->Default_Where . " AND $where" : $this->Default_Where;
        $this->Products_Array = $this->SQL->GetArrayAssoc(array(
            'table'        => $this->Product_Table,
            'reference_key'=> 'store_products_id',
            'keys'         => '*',
            'where'        => $where,
            'order'        => $this->Product_Sort
        ));
    }



    // ---------- get options in DB ----------
    public function GetAllOptions()
    {
        $this->Options_Array = $this->SQL->GetArrayAssoc(array(
            'table'        => 'store_product_options',
            'reference_key'=> 'store_product_options_id',
            'keys'         => '*',
            'where'        => 'active=1',
            'order'        => 'store_product_options_id'
        ));
    }

    public function GetItemOptions($product_id)
    {
        $RESULT = array();
        if (empty($this->Options_Array)) {
            $this->GetAllOptions();
        }

        //--- options are assigned to products or categories
        $categories = $this->GetItemCategories($product_id);
        if (!empty($this->Products_Array[$product_id]['product_options'])) {
            $options = explode(',', $this->Products_Array[$product_id]['product_options']);
        } else {
            $options = array();
        }
        if ($categories) {
            //--- get options assigned to categories

            foreach ($categories as $cat) {
                if ($this->Categories[$cat]['product_options'] != '') {
                    $cat_options = explode(',', $this->Categories[$cat]['product_options']);
                    if ($options) {
                        $options = array_unique(array_merge($cat_options, $options));
                    } else {
                        $options = $cat_options;
                    }
                }
            }
        }
        if ($options) {
            foreach($options as $option) {
                $RESULT[] = $this->Options_Array[$option];
            }
        }
        return $RESULT;
    }



    public function DisplayCatalog($where='')
    {
        $this->GetProducts($where);
        $this->GetCategories($where='');

        if ($this->Products_Array) {

            foreach ($this->Products_Array as $record) {
                $item = $this->Item_Template;
                foreach ($record as $key => $value) {
                    $item = str_replace(strtoupper("@$key@"), $value, $item);
                }
                $options = $this->GetItemOptions($record['store_products_id']);
                if (!empty($options)) {
                    $form = OutputForm($this->GetItemOptionForm($record['store_products_id'], $options), 0);
                } else {
                    $form = '';
                }
                $item = str_replace('@OPTIONS@', "\n$form", $item);

                $have_item_class = $this->ItemInCart($record['store_products_id'])? ' store_item_in_cart' : '';
                $item = str_replace('@HAVE_ITEM_CLASS@', $have_item_class, $item);

                echo $item;
            }
        }
    }

    public function GetItemOptionForm($product_id, $options)
    {
        $form = array(
            "form||post|STORE_ITEM_FORM$product_id",
            'titletemplate|<div class="formtitle">@:</div>' ."\n"
        );

        foreach ($options as $option) {
            $option_id     = $option['store_product_options_id'];
            $title         = $option['option_title'];
            $description   = $option['option_description'];
            $values        = $option['option_values'];
            $default_value = $option['default_value'];

            $values        = preg_replace('/\n+/', "\n", trim($values));
            $value_array   = explode("\n", $values);

            if ($default_value) {
                Form_PostValue("OPTION_{$product_id}_$option_id", $default_value);
                $list_start = '|N';
            } else {
                $list_start = '';
            }

            $list = '';
            if ($values) {
                foreach ($value_array as $value) {
                    list($code, $value_title, $price) = explode('|', $value . '||');
                    $list .= "|$code=$value_title";
                }
            }


            switch ($option['selection_type']) {
                case 'text':
                    $form[] = "text|$title|OPTION_{$product_id}_$option_id|N|40|80";
                    break;
                case 'checkbox':
                    $form[] = "checkbox|$title|OPTION_{$product_id}_$option_id||$code";
                    break;
                case 'checkboxes':
                    $form[] = "checkboxlistbar|$title|OPTION_{$product_id}_$option_id|N|" . $list;
                    break;
                case 'select':
                    $form[] = "select|$title|OPTION_{$product_id}_$option_id|N|" . $list_start . $list;
                    break;
                case 'radio':
                    $form[] = "radio|$title|OPTION_{$product_id}_$option_id|N|" . $list;
                    break;
                case 'radioh':
                    $form[] = "radioh|$title|OPTION_{$product_id}_$option_id|N|" . $list;
                    break;
            }
        }
        $form[] = 'endform';
        return $form;
    }

} //---------------- END CLASS ----------------