<?php
// CREATED BY RICHARD
// Customer viewing their own orders

class Store_ShoppingProduct
{
    public $ShowQuery                   = true;
    public $show_specifications         = true; // Show the product specifications tab
    public $show_gallery                = true; // Show the product gallery tab
    public $show_reviews                = true; // Show the product review tab
    public $Show_Faqs                   = false; // Show the product FAQs tab

    public $Money_Format                = '%n';

    public $Product_Table               = 'store_products';
    public $Category_Table              = 'store_categories';
    public $Transaction_Table           = 'store_transactions';
    public $Transaction_Items_Table     = 'store_transaction_items';
    public $Product_Options_Table       = 'store_product_options';


    public $Image_Directory             = '/images/store/';

    public $tab_over_pixels             = 20; //how many pixels to the right should each sub-T/P be shifted over

    public $Category_Array              = array(); // MVP: Multi-Dimentional Array of Category IDs
    public $Categories                  = array(); // MVP: Associative Array with ID => Array('parent_id,title,product_options')

    public $Products_Array              = array(); // MVP: Associative Array with ID => Array of product record
    public $Product_Sort                = 'title'; // used in sorting
    public $Default_Where               = '`store_products`.`active`=1 AND `store_products`.`display`=1
  AND (`store_products`.`show_date`=0 OR `store_products`.`show_date` <= NOW())
  AND (`store_products`.`hide_date`=0 OR `store_products`.`hide_date` >= NOW())';

    // ----------- styles --------------


    public $Store_Item_Picture_Width    = '160';  // --OK-- Viewing all products - width of product image
    public $Store_Item_Picture_Height   = '120';  // --OK-- Viewing all products - height of product image

    public $Description_Truncate_Length = 60; // How many characters to show before truncating description on general listing

    public $Page_Name                   = ''; // MVP

    public $Store_Categories_Id         = '';
    public $Breadcrumb                  = '';
    public $Breadcrumb_Prefix           = ' <span>&raquo;</span> ';

    public $Product_Final_Price         = 0;


    // ==================================== CONSTRUCT ====================================
    public function  __construct()
    {
        $this->SetSQL();
        setlocale(LC_MONETARY, 'en_US');
    }


    public function SetSQL()
    {
        if (empty($this->SQL)) {
            $this->SQL = Lib_Singleton::GetInstance('Lib_Pdo');
        }
    }



    public function Execute($pagename, $cid, $product_id)
    {
        $this->Page_Name             = $pagename;
        $this->Store_Categories_Id   = $cid;

        $this->GetBreadcrumb($cid);

        if ($product_id) {
            //case 'single':
            $this->AddScript();

            //$product    = $this->ModifyProductInformation($product, $product_id); //<<<<<<<<<<---------- Need to replace ----------<<<<<<<<<<

            $this->GetAllProducts("`store_products_id`=$product_id AND $this->Default_Where");
            $product = $this->Products_Array[$product_id];
            $this->FormatAndOutputSingleProduct($product);

        } else {

            $this->AddScript();

            $where = '';
            if ($cid) {
                $category_children_array = explode(',', $this->GetCategoryChildren($cid));  // returns comma-delimited list
                foreach ($category_children_array as $id) {
                    if ($where) {
                        $where .= ' OR ';
                    }
                    $where .= "FIND_IN_SET($id, `categories`)";
                }
                $where = "($where)";

            }

            $where = ($where) ? "$where AND $this->Default_Where" : $this->Default_Where;

            $products   = $this->GetAllProducts($where);
            //$products   = $this->ModifyProductInformation($products, ''); //<<<<<<<<<<---------- Need to replace ----------<<<<<<<<<<

            $this->FormatAndOutputAllProducts($products);
        }
    }

    public function AddToBreadcrumb($title, $link)
    {
        $prefix = ($this->Breadcrumb)? $this->Breadcrumb_Prefix : '';
        $this->Breadcrumb .= "$prefix<a href=\"$link\">$title</a>";
    }

    public function GetBreadcrumb($CID) // --------- MVP function
    {
        $this->GetCategories();

        $RESULT = '';

        if ($CID) {
            $title     = $this->Categories[$CID]['title'];
            $link      = "/$this->Page_Name/$CID/";
            $RESULT    = "$this->Breadcrumb_Prefix<a href=\"$link\">$title</a>" . $RESULT;
            $parent_id = $this->Categories[$CID]['parent_id'];

            while ($parent_id != 0) {
                $title     = $this->Categories[$parent_id]['title'];
                $link      = "/$this->Page_Name/$parent_id/";
                $RESULT    = "$this->Breadcrumb_Prefix<a href=\"$link\">$title</a>" . $RESULT;
                $parent_id = $this->Categories[$parent_id]['parent_id'];
            }
        }

        $RESULT = "<a href=\"/$this->Page_Name\">Home</a>" . $RESULT;
        $this->Breadcrumb = $RESULT;
    }


    public function ModifyProductInformation($p_array, $product_id)
    {
        //===========================NEEDS REAL VALUES FROM DATABASE=====================================

        # ADD SOME VALUES TO THE PRODUCTS ARRAY
        $temp[1]['sale_percent']    = 15;
        $temp[1]['sale_dollar']     = 0;

        $temp[2]['sale_percent']    = 0;
        $temp[2]['sale_dollar']     = 0;

        $temp[3]['sale_percent']    = 0;
        $temp[3]['sale_dollar']     = 2.75;

        if ($product_id) {
            $p_array['sale_percent']    = $temp[$product_id]['sale_percent'];
            $p_array['sale_dollar']     = $temp[$product_id]['sale_dollar'];
            return $p_array;
        } else {
            for($t=0; $t<count($p_array); $t++) {
                $id = $p_array[$t]['store_products_id'];
                $p_array[$t]['sale_percent']    = $temp[$id]['sale_percent'];
                $p_array[$t]['sale_dollar']     = $temp[$id]['sale_dollar'];
            }
            return $p_array;
        }
    }


    public function FormatMoney($value)
    {
        $value = preg_replace('/[^0-9\.\-]/', '', $value);
        return money_format($this->Money_Format, floatval($value));
    }


    public function FormatPrice($price, $sale_percent=0, $sale_dollar=0)
    {
        $item_price = $this->FormatMoney($price);

        if ($sale_percent != 0) {

            # CALCULATE A SALE PERCENTAGE
            $calculated_sale_price      = $price - ($price * ($sale_percent/100));
            $this->Product_Final_Price  = $calculated_sale_price;
            $sale_price                 = $this->FormatMoney($calculated_sale_price);
            $sale_text                  = "(Save $sale_percent%)";

            $price_display = <<<PRICELBL
                <span class="store_item_price_wrapper">
                    <span class="store_item_price_sale">$item_price</span>
                    <span class="store_item_price_sale_text">&nbsp;ON SALE</span>
                    <span class="store_item_price_normal">$sale_price</span>
                    <span class="store_item_price_sale_text">$sale_text</span>
                </span>
PRICELBL;
        } elseif ($sale_dollar != 0) {
            # CALCULATE A SALE DOLLAR AMOUNT
            $calculated_sale_price      = $price - $sale_dollar;
            $this->Product_Final_Price  = $calculated_sale_price;
            $sale_price                 = $this->FormatMoney($calculated_sale_price);
            $sale_dollar                = $this->FormatMoney($sale_dollar);
            $sale_text                  = "(Save $sale_dollar)";

            $price_display = <<<PRICELBL2
                <span class="store_item_price_wrapper">
                    <span class="store_item_price_sale">$item_price</span>
                    <span class="store_item_price_sale_text">&nbsp;ON SALE</span>
                    <br style="clear:both;" />
                    <span class="store_item_price_normal">$sale_price</span>
                    <span class="store_item_price_sale_text">$sale_text</span>
                </span>
PRICELBL2;
        } else {
            $price_display = <<<PRICELBL3
                <span class="store_item_price_wrapper">
                    <span class="store_item_price_normal">$item_price</span>
                </span>
PRICELBL3;
        }

        return $price_display;
    }

    public function RecursiveCategoryBuild($id = 0) // --------- MVP function
    {
        $RESULT = array();
        foreach ($this->Categories as $cat_id => $record) {
            if ($record['parent_id'] == $id) {
                $RESULT[$cat_id] = $this->RecursiveCategoryBuild($cat_id);
            }
        }
        return $RESULT;
    }


    public function GetCategories($where='') // --------- MVP function
    {
        if (!empty($this->Categories)) return;  // already have the categories

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

    public function ItemInCart($pn) // --------- MVP function
    {
        if (!empty($_SESSION['STORE_ORDER']['CART'])) {
            if ($pn == 0) {
                return true;  // use PN=0, if checking if cart is not empty
            }
            $cart_items = $_SESSION['STORE_ORDER']['CART'];
            foreach ($cart_items as $part_number => $qty) {
                if (strTo($part_number, '~') == $pn) {
                    return true;
                }
            }
        }
        return false;
    }

    public function GetSubCategoryMenu($cat_array, $level) // --------- MVP function
    {
        $RESULT = '';
        $pad = str_pad('', $level*4);  // used for HTML padding for easier reading of source
        if (!empty($cat_array)) {
            $RESULT .= "$pad<div class=\"store_subcategory_menu\">\n";
            foreach ($cat_array as $CID => $array) {
                $title = $this->Categories[$CID]['title'];
                $class = ($this->Store_Categories_Id == $CID)? ' class="selected_category"' : '';
                $RESULT .=  "$pad<a$class href=\"/$this->Page_Name/$CID/\">$title</a>\n";
                if ($array) {
                    $RESULT .= $this->GetSubCategoryMenu($array, $level+1);
                }
            }
            $RESULT .= "$pad</div>\n";
        }
        return $RESULT;
    }

    public function GetCategoryMenu() // --------- MVP function
    {
        $this->GetCategories();

        $RESULT = "\n<!-- ================= STORE CATEGORY MENU ================= -->\n";
        $RESULT .= "<div id=\"store_category_menu\">\n    <a href=\"/$this->Page_Name\">ALL PRODUCTS</a>\n";
        $RESULT .= $this->GetSubCategoryMenu($this->Category_Array, 1);
        $RESULT .= "</div>\n<!-- ================= end store category menu ================= -->\n";
        return $RESULT;
    }


    public function GetProductCategories($product_id) // --------- MVP function
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

    public function GetCategoryChildren($CID) // --------- MVP function
    {
        $RESULT = $CID;
        foreach ($this->Categories as $cat_id => $array) {
            if ($array['parent_id'] == $CID) {
                $RESULT .= ',' . $this->GetCategoryChildren($cat_id);
            }
        }
        return $RESULT;
    }


    // ---------- get options in DB ----------
    public function GetAllOptions() // --------- MVP function
    {
        if (!empty($this->Options_Array)) return;

        $this->Options_Array = $this->SQL->GetArrayAssoc(array(
            'table'        => 'store_product_options',
            'reference_key'=> 'store_product_options_id',
            'keys'         => '*',
            'where'        => 'active=1',
            'order'        => 'store_product_options_id'
        ));
    }

    public function GetProductOptions($product_id) // --------- MVP function
    {
        $RESULT = array();
        $this->GetAllOptions();

        //--- options are assigned to products or categories
        $categories = $this->GetProductCategories($product_id);
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


    public function GetProductOptionForm($product_id, $options) // --------- MVP function
    {
        $form = array(
            'form||post|STORE_ITEM_FORM' . $product_id,
            'code|<ul class="store_form">',
            'titletemplate|<li><span class="store_formtitle">@</span>' ."\n",
            'infotemplate|<span class="store_forminfo">@</span></li>' . "\n\n"
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
                    list($code, $value_title, $price) = explode('|', trim($value) . '||');
                    if ($price) {
                        $price = preg_replace('/[^0-9\.\-]/', '', $price);
                        $value_title .= '&nbsp;(' . $this->FormatMoney($price) . ')';
                    }
                    $list .= "|$code=$value_title";
                }
            }


            switch ($option['selection_type']) {
                case 'text':
                    $form[] = "text|$title:|OPTION_{$product_id}_$option_id|N|40|80";
                    break;
                case 'checkbox':
                    $title .= '&nbsp;(' . $this->FormatMoney($price) . ')';
                    $form[] = "checkbox|$title:|OPTION_{$product_id}_$option_id||$code";
                    break;
                case 'checkboxes':
                    $form[] = "checkboxlistbar|$title:<br />|OPTION_{$product_id}_$option_id.|N|" . $list;
                    break;
                case 'select':
                    $form[] = "select|$title:|OPTION_{$product_id}_$option_id|N|" . $list_start . $list;
                    break;
                case 'radio':
                    $form[] = "radio|$title:<br />|OPTION_{$product_id}_$option_id|N|" . $list;
                    break;
                case 'radioh':
                    $form[] = "radioh|$title:|OPTION_{$product_id}_$option_id|N|" . $list;
                    break;
            }
        }
        $form[] = 'code|</ul>';
        $form[] = 'endform';
        return $form;
    }


    public function GetProductTitleUrl($title)
    {
        $RESULT = str_replace(' ', '-', strtolower($title));
        return preg_replace('/[^0-9a-z\-]/', '', $RESULT);
    }

    public function FormatAndOutputAllProducts($products)
    {
        $output  = "<div class=\"product_contents\">";

        $have_items = ($this->ItemInCart(0))? ' inline;' : 'none;';

        $output .=<<<BREADLBL

<!-- =============== BREADCRUMB =============== -->
<div class="breadcrumb">{$this->Breadcrumb}
    <a style="display:$have_items" id="button_view_cart" href="#" onclick="return viewShoppingCart();">View Cart</a>
</div>
BREADLBL;

        $categories = $this->GetCategoryMenu();

        //CATEGORIES
        $output .= '
<!-- =============== CATEGORIES =============== -->
        <div id="category_menu" class="col">
            <div class="category_outter_wrapper">
            <div class="category_inner_wrapper">
                <div class="category_header">CATEGORIES</div>
                <div class="category_list">
                ' . $categories . '
                </div>
            </div>
            </div>
        </div> <!-- END col -->' . "\n";



        //PRODUCT WRAPPER
        $output .= '<div id="products_wraper" class="col">';

        if ($products) {
            if (empty($this->Store_Categories_Id)) {
                $this->Store_Categories_Id = 0;
            }
            foreach ($products as $product) {
                # FORMAT VARIOUS ITEMS
                # =============================
                $price_display  = $this->FormatPrice($product['price'], $product['sale_percent'], $product['sale_dollar']);

                $description    = TruncStr($product['description'], $this->Description_Truncate_Length);

                $store_item_url_title = $this->GetProductTitleUrl($product['title']);

                $product_id = $product['store_products_id'];

                $have_item_class = ($this->ItemInCart($product_id))? ' store_item_in_cart' : '';

                $output .= <<<ITEM
<!-- =============== STORE ITEM =============== -->
                <div class="col product_gapping">
                <div class="store_item_outter_wrapper$have_item_class">
                <div class="store_item_inner_wrapper">

                <a class="store_catalog_product" href="/$this->Page_Name/$this->Store_Categories_Id/$product_id/$store_item_url_title">
                    <img class="store_catalog_picture" src="{$this->Image_Directory}{$product['picture1']}" width="{$this->Store_Item_Picture_Width}" height="{$this->Store_Item_Picture_Height}" border="0" alt="{$product['picture1']}" />
                    <span class="store_item_content_wrapper">
                        <span class="store_item_title">{$product['title']}</span>
                        <span class="store_item_code">{$product['part_number']}</span>
                        <span class="store_item_description">{$description}</span>
                        <span class="store_item_price">{$price_display}</span>
                    </span>
                </a>

                </div>
                </div>
                </div>
<!-- =============== END STORE ITEM =============== -->
ITEM;
            }
        } else {
            $output .= "<h2>UNABLE TO LOCATE PRODUCTS</h2>";
        }

        $output .= '<div class="clear"></div>'; //clear product divs

        //END PRODUCT WRAPPER
        $output .= "\n</div>\n";
        $output .= '<div class="clear"></div>'; //clear category -> product divs
        $output .= "\n</div>\n"; //end master wrapper

        echo $output;
    }


    public function FormatAndOutputSingleProduct($product)
    {
        global $SCRIPT_URI;
        if ($product) {

            $product_id = $product['store_products_id'];

            # FORMAT BREADCRUMB
            # ==============================================
            $title  = $product['title'];
            $store_item_url_title = $this->GetProductTitleUrl($product['title']);

            $link = "/$this->Page_Name/$this->Store_Categories_Id/$product_id/$store_item_url_title";
            $this->AddToBreadcrumb($title, $link);

            // $product['store_products_id'];
            // $product['part_number'];
            // $product['manufacturer_part_number'];
            // $product['manufacturer_id'];
            // $product['distributor_id'];
            // $product['categories'];
            // $product['title'];
            // $product['description'];
            // $product['description_long'];
            // $product['price'];
            // $product['shipping'];
            // $product['show_date'];
            // $product['hide_date'];
            // $product['discounts'];
            // $product['picture1'];
            // $product['picture2'];

            # PRODUCT OPTIONS
            # =========================================================
            //$options = ($product['product_options']) ? $this->GetProductOptions($product['product_options']) : '';

            $options = $this->GetProductOptions($product_id);

            if (!empty($options)) {
                $options_form = OutputForm($this->GetProductOptionForm($product_id, $options), 0);
            } else {
                $options_form = 'NO PRODUCT OPTIONS';
            }

            // # FAQs SECTION
            // # =========================================================
            // $faq[0]['question']     = "How long does it take to get my order?";
            // $faq[0]['answer']       = "Plain envelopes usually ship the same or next business day. Printed Envelopes have a production time of 5 business days, standard production, or 2 business days, rush production. Once your envelopes ship, they can take 1-5 business days depending on the level of service selected at checkout.";
            // $faq[1]['question']     = "Do you send samples?";
            // $faq[1]['answer']       = "Yes, samples are $1.00 each, and you get a $1.00 coupon to use on your next order.";
            // $faq[2]['question']     = "Do you have discounts for resellers?";
            // $faq[2]['answer']       = "Yes! We have a trade discount program for printing, advertising, graphic design, and greeting card companies. Please visit our <a href='/ae/control/tradediscounts'>Trade Discount Program</a> page.";
            // $faq[3]['question']     = "How do I order a printed envelope?";
            // $faq[3]['answer']       = "On the product page, select the printed tab. You will need to select how many ink colors will be printed on the front and back of the envelope. The back of the envelope has the flap. Then select the production time that will suit your needs. Next, Choose quantity, and select if you want to design your envelope online, or you will be uploading a digital file. You can review our <a href='/ae/control/printguidelines'>Artwork Specs</a>.";
            // $faq[4]['question']     = "How much is shipping?";
            // $faq[4]['answer']       = "Shipping is calculated based upon the weight of the shipment and the destination zip code. On the product page you can enter your zip code to see the shipping costs once you select a quantity.";
            // $faq[5]['question']     = "How do I order a quantity not shown?";
            // $faq[5]['answer']       = "There is a text entry box in the 'Prices &amp; Options' area of the product page. Enter any quantity (increments of 50, for plain envelopes, 500, for printed envelopes) and your price for that quantity will appear. Select it, and add to cart.";

            // $tab_content_faqs = $this->MakeFAQ($faq);
            // unset($faq);


            # GALLERY SECTION
            # =========================================================
            $images = array('mat.png', 'camera.png', 'gloves.png'); //<<<<<<<<<<---------- REPLACE ----------<<<<<<<<<<
            $gallery_images = $this->MakeGallery($images);


            # OTHER STUFF


            $price_display          = $this->FormatPrice($product['price'], $product['sale_percent'], $product['sale_dollar']);

            # ========================= SOCIAL NETWORKING ================================
            $FACEBOOK               = new General_Facebook;
            $facebook_button        = $FACEBOOK->CreateButton($SCRIPT_URI);
            $TWITTER                = new General_Twitter;
            $twitter_text           = "";
            $twitter_button         = $TWITTER->CreateButton($SCRIPT_URI, $twitter_text);

            $facebook_button = ''; //<<<<<<<<<<---------- REMOVE UNTIL CLEAN ----------<<<<<<<<<<
            $twitter_button  = ''; //<<<<<<<<<<---------- REMOVE UNTIL CLEAN ----------<<<<<<<<<<

            # SPECIFICATIONS TAB SECTION
            # =========================================================
            $TABS = new Tabs('tab', 'tab_edit');

            $tab_content_specifications         = $product['description_long'];
            $tab_content_gallery                = ($gallery_images) ? $gallery_images : "This product has no additional pictures.";
            $tab_content_reviews                = "This product hasn't been reviewed yet.<br /><br />Be the first to review this product!";
            //$tab_content_faqs                   = "$tab_content_faqs";


            if ($this->show_specifications)     $TABS->AddTab('Specifications', "<div class='tab_content_wrapper'>{$tab_content_specifications}</div>");
            if ($this->show_gallery)            $TABS->AddTab('Gallery', "<div class='tab_content_wrapper'>{$tab_content_gallery}</div>");
            if ($this->show_reviews)            $TABS->AddTab('Reviews', "<div class='tab_content_wrapper'>{$tab_content_reviews}</div>");
            //if ($this->Show_Faqs)               $TABS->AddTab('FAQs', "<div class='tab_content_wrapper'>{$tab_content_faqs}</div>");

            $tab_content = $TABS->OutputTabs(true);


            $have_items = ($this->ItemInCart(0))? ' inline;' : 'none;';

            $output =<<<PRODLBL

            <div class="product_contents">
<!-- =============== BREADCRUMB =============== -->
<div class="breadcrumb">{$this->Breadcrumb}
    <a style="display:$have_items" id="button_view_cart" href="#" onclick="return viewShoppingCart();">View Cart</a>
</div>
            <br />
            <div class="col single_product_col_left_width">


                <div class="picture_wrapper" id="product_picture_holder">
                    <center>
                    <img src="{$this->Image_Directory}{$product['picture1']}" border="0" alt="Product Picture" />
                    </center>
                </div>
                <!-- div>{$facebook_button}</div>
                <div>{$twitter_button}</div -->

                <br /><br />

                <div class="specifications_wrapper">
                    {$tab_content}
                </div>


            </div>

            <div class="col single_product_gap_width">&nbsp;</div>

            <div class="col single_product_col_right_width">

                <div class="">

                    <div class="col section_number">1.</div>
                    <div class="col picture_wrapper" style="width:85%; padding:0px;">
                        <div class="section_title">BASIC PRODUCT PRICING</div>
                        <br />
                        <div class="pad_20_special product_price">$price_display</div>
                    </div>
                    <div class="clear"></div>
                    <br /><br /><br />


                    <div class="col section_number">2.</div>
                    <div class="col picture_wrapper" style="width:85%; padding:0px;">
                        <div class="section_title">SELECT OPTIONS</div>
                        <br />
                        <div class="pad_20_special">$options_form</div>
                    </div>
                    <div class="clear"></div>
                    <br /><br /><br />


                    <div class="col section_number">3.</div>
                    <div class="col picture_wrapper" style="width:85%; padding:0px;">
                        <div class="section_title">ADD TO CART</div>
                        <div class="store_order_button_div"><a class="orderbutton" href="#" onclick="return shoppingCartAddItem($product_id);">Order Now!</a></div>
                    </div>
                    <div class="clear"></div>

                </div>


            </div>
            <div class="clear"></div>
            </div>
PRODLBL;

        } else {
            $output = "<h2>UNABLE TO LOCATE PRODUCT</h2>";
        }

        echo $output;
    }




    public function GetProduct($product_id)
    {
        # 1. GET TRANSATIONS
        $record = $this->SQL->GetRecord(array(
            'table' => $this->Product_Table,
            'keys'  => '*',
            'where' => "active=1 AND `store_products_id`=$product_id",
        ));

        return $record;
    }


    // public function GetAllProducts($where='')
    // {
        // $where = ($where) ? " AND $where" : '';

        // # 1. GET TRANSATIONS
        // $records = $this->SQL->GetArrayAll(array(
            // 'table' => $this->Product_Table,
            // 'keys'  => '*',
            // 'where' => "active=1 $where",
        // ));
        // return $records;

        // $this->Products_Array =
    // }

    public function GetAllProducts($where='') // MVP function
    {
        //$where = ($where)? $this->Default_Where . " AND $where" : $this->Default_Where;
        $this->Products_Array = $this->SQL->GetArrayAssoc(array(
            'table'        => $this->Product_Table,
            'reference_key'=> 'store_products_id',
            'keys'         => '*',
            'where'        => $where,
            'order'        => $this->Product_Sort
        ));
        return $this->Products_Array;
    }



    public function AddScript()
    {
        AddScriptOnReady("
    $('.faq_q').bind('click', function() {
        $(this).parent().find('p').toggle();
    });

    $('.image_gallery_picture_wrapper img').bind('click', function() {
        var new_img_src = $(this).attr('src_large');
        $('#product_picture_holder img').fadeOut(function() {
            $('#product_picture_holder img').attr('src', new_img_src).fadeIn();
            });
    });");
    }


    private function MakeTable($data, $style_table='', $style_col_left='', $style_col_right='')
    {
        $output = "<table border='0' cellspacing='0' cellpadding='0' style='{$style_table}'>";
        foreach ($data as $line) {
            $parts = explode('|', $line);
            $output .= "
                <tr>
                    <td class='tbl_row_header' style='{$style_col_left}' valign='top'>{$parts[0]}</td>
                    <td class='tbl_row_content' style='{$style_col_right}' valign='top'>{$parts[1]}</td>
                </tr>
            ";
        }
        $output .= "</table>";
        return $output;
    }


    private function MakeFAQ($data_array)
    {
        $output = "<ul class='faqs'>";
        foreach ($data_array as $q_a) {
            $output .= "
            <li>
                <a class='faq_q'>{$q_a['question']}</a>
                <p class='faq_a' style='display: none;'>{$q_a['answer']}</p>
            </li>";
        }
        $output .= "</ul>";
        return $output;
    }


    private function MakeGallery($image_array='')
    {
        if (!$image_array) { return; }

        $output = "<ul class='image_gallery_list'>";
        foreach ($image_array as $image) {
            $output .= "
                <li>
                    <div class=\"image_gallery_picture_wrapper\">
                    <img src=\"/gimage/200x100{$this->Image_Directory}{$image}\" border=\"0\" alt=\"{$image}\" />
                    </div>
                </li>";
        }
        $output .= "</ul>";
        return $output;
    }


    // ==================================== GET ITEM RECORD ====================================
    public function GetItemRecord($PN, $db_record='')
    {
        if (empty($db_record)) {
            $part_number_quote = $this->SQL->QuoteValue($PN);
            $db_record = $this->SQL->GetRecord(array(
                'table' => $this->Product_Table,
                'keys'  => '*',
                'where' => "`{$this->Product_Table_Field_Translations['part_number']}`=$part_number_quote AND active=1"
            ));
        }

        $RESULT = array();
        if ($db_record) {
            foreach ($this->Product_Table_Field_Translations as $field => $db_field) {
                $value = ArrayValue($db_record, $db_field);
                if ((($field == 'weight') or ($field == 'shipping')) and ($value == '')) {
                    $value = 0;
                }
                $RESULT[$field] = $value;
            }
        }

        return $RESULT;
    }



} // end class