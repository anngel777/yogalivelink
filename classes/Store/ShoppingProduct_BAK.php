<?php
// CREATED BY RICHARD
// Customer viewing their own orders

class Store_ShoppingProduct
{
    public $ShowQuery                   = true;

    public $show_specifications         = true; // Show the product specifications tab
    public $show_gallery                = true; // Show the product gallery tab
    public $show_reviews                = false; // Show the product review tab
    public $show_faqs                   = false; // Show the product FAQs tab
    
    public $product_link_base           = "http://yoga.whhub.com/office/index";
    public $money_format                = '%n';
    
    public $Product_Table               = 'store_products';
    public $Category_Table              = 'store_categories';
    public $Transaction_Table           = 'store_transactions';
    public $Transaction_Items_Table     = 'store_transaction_items';
    public $Product_Options_Table       = 'store_product_options';
    
    
    public $image_directory             = '/images/store/';
    public $category_sort_on_field      = 'title';
    public $category_sort_order         = 'SORT_DESC';
    public $tab_over_pixels             = 20; //how many pixels to the right should each sub-T/P be shifted over    
    
    public $categories                  = array();
    public $categories_sub              = array();
    public $categories_global           = '';
    public $sub_category_list           = array();
    
    // ----------- styles --------------
    
    public $border_color                = "#D7D7D7";
    public $background_color_primary    = "#FFFFFF";
    public $background_color_secondary  = "#F5F5F5";
    public $header_color                = "#044577";
    public $highlite_color              = "#FC7E22";
    
    public $item_picture_width          = '160px';  // Viewing all products - width of product image
    public $item_picture_height         = '120px';  // Viewing all products - height of product image
    public $product_wrapper_width       = '160px';  // Viewing all products - width of holder (should be close to item_picture_width)
    public $product_wrapper_height      = '290px';  // Viewing all products - height of holder
    public $product_wrapper_padding     = '10px';   // Gap between products - note that actual gap with be twice this width
    
    public $category_wrapper_width       = '200px';  // Viewing all products - width of category holder
    public $category_wrapper_height      = '350px';  // Viewing all products - height of category holder --> NOT USED
    
    
    public $single_product_col_left_width      = '500px';   // Viewing single product - left column
    public $single_product_gap_width           = '50px';    // Viewing single product - gap between columns
    public $single_product_col_right_width     = '350px';   // Viewing single product - right column
    
    public $arrow_image_location        = "/office/images/arrow_dotted.gif";
    public $description_len_trunc       = 60; // How many characters to show before truncating description on general listing

    public $total_contents_width        = '950px';  // width of whole product-listing table
    public $categories_width            = '200px';  // width of categories area - needs to match or be larger than "category_wrapper_width"
    public $category_contents_gap       = '50px;';  // gap between categories and products
    public $products_width              = '700px';  // width of prodcuts area
    
    
    
    
    
    public $page_location               = '';
    public $product_detail_link         = '';
    
    public $colgap                      = '&nbsp;&nbsp;';    
    
    public $category                    = '';
    public $store_categories_id         = '';
    public $where                       = '';
    public $breadcrumb                  = '';
    



    // ==================================== CONSTRUCT ====================================
    public function  __construct()
    {
        $this->SetSQL();
        setlocale(LC_MONETARY, 'en_US');
    }
    
    public function AddToBreadcrumb($title, $link)
    {
        $this->breadcrumb = ($this->breadcrumb) ? "{$this->breadcrumb} >> <a href='$link'>$title</a>" : "<a href='$link'>$title</a>";
    }
    
    public function SetSQL()
    {
        if (empty($this->SQL)) {
            $this->SQL = Lib_Singleton::GetInstance('Lib_Pdo');
        }
    }
    
    
    public function Execute($type, $product_id)
    {
        $title  = 'Home';
        $link   = "{$this->product_detail_link};type=all";
        $this->AddToBreadcrumb($title, $link);
        
        if ($this->category) {
            $title  = $this->category;
            $link   = "{$this->product_detail_link};type=all;category={$this->category}";
            $this->AddToBreadcrumb($title, $link);
        }
        
        switch (strtolower($type)) {
            case 'single':
                $this->AddStyle();
                $this->AddStyleAllProducts();
                $this->AddScript();
                
                $product    = $this->GetProduct($product_id);
                $product    = $this->ModifyProductInformation($product, $product_id);
                
                $this->FormatAndOutputSingleProduct($product);
            break;
            case 'all':
                $this->AddStyleAllProducts();
                $this->AddScript();
                
                $where      = ($this->store_categories_id) ? "`categories` LIKE '%{$this->store_categories_id}%'" : '';
                $products   = $this->GetAllProducts($where);
                $products   = $this->ModifyProductInformation($products, '');
                
                $this->FormatAndOutputAllProducts($products);
            break;
        }
    }
    

    public function ModifyProductInformation($p_array, $product_id)
    {
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



    
    public function GetFakeProduct($product_id)
    {
        $products[101] = array(
            'order_number_master'               => '1250',
            'order_number'                      => '1250-1',
            'order_date'                        => '2010-02-10',
            'title'                             => 'Web Camera',
            'description'                       => 'Sed ante arcu, pulvinar non malesuada eu, mattis id felis. In ut eros tortor. Suspendisse accumsan est a diam vestibulum ullamcorper. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nulla facilisi. Curabitur et urna elit. Proin at dictum nisi. Etiam lacus ante, pellentesque eu gravida ut, luctus sed nisi. Nunc sed leo sapien, ac aliquet quam. Sed diam eros, commodo non sollicitudin ac, dapibus',
            'status_ship'                       => 1,
            'status_ship_date'                  => '2010-02-11',
            'status_ship_method'                => '',
            'status_ship_type'                  => '',
            'status_ship_tracking_number'       => '',
            'status_ship_address'               => '',
            'status_processing'                 => 0,
            'status_ship_delivery_expected'     => '',
            'picture'                           => '/images/store/camera.png',
            'price'                             => '102.99',
            'price_shipping'                    => '5.11',
            'price_sales_tax'                   => '1.22',
        );
        
        $products[102] = array(
            'order_number_master'               => '1392',
            'order_number'                      => '1392-1',
            'order_date'                        => '2010-05-23',
            'title'                             => 'Yoga Mat - Purple',
            'description'                       => 'Sed ante arcu, pulvinar non malesuada eu, mattis id felis. In ut eros tortor. Suspendisse accumsan est a diam vestibulum ullamcorper. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nulla facilisi. Curabitur et urna elit. Proin at dictum nisi. Etiam lacus ante, pellentesque eu gravida ut, luctus sed nisi. Nunc sed leo sapien, ac aliquet quam. Sed diam eros, commodo non sollicitudin ac, dapibus',
            'status_ship'                       => 1,
            'status_ship_date'                  => '2010-05-10',
            'status_ship_method'                => 'FedEx Drop-Ship',
            'status_ship_type'                  => 'FEDEX',
            'status_ship_tracking_number'       => '487360615033134',
            'status_ship_address'               => '3336 NW 31st Circle<br />Camas, WA 98661',
            'status_processing'                 => 0,
            'status_ship_delivery_expected'     => '',
            'picture'                           => '/images/store/mat.png',
            'price'                             => '25.95',
            'price_shipping'                    => '5.11',
            'price_sales_tax'                   => '1.22',
        );
        
        $products[103] = array(
            'order_number_master'               => '1392',
            'order_number'                      => '1392-2',
            'order_date'                        => '2010-05-23',
            'title'                             => 'Yoga Socks',
            'description'                       => 'Sed ante arcu, pulvinar non malesuada eu, mattis id felis. In ut eros tortor. Suspendisse accumsan est a diam vestibulum ullamcorper. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nulla facilisi. Curabitur et urna elit. Proin at dictum nisi. Etiam lacus ante, pellentesque eu gravida ut, luctus sed nisi. Nunc sed leo sapien, ac aliquet quam. Sed diam eros, commodo non sollicitudin ac, dapibus',
            'status_ship'                       => 0,
            'status_ship_date'                  => '',
            'status_ship_method'                => 'UPS Drop-Ship',
            'status_ship_type'                  => 'UPS',
            'status_ship_tracking_number'       => '1Z9999999999999999',
            'status_ship_address'               => '3336 NW 31st Circle<br />Camas, WA 98661',
            'status_processing'                 => 1,
            'status_ship_delivery_expected'     => '2010-06-15',
            'picture'                           => '/images/store/socks.png',
            'price'                             => '32.95',
            'price_shipping'                    => '5.11',
            'price_sales_tax'                   => '1.22',
        );
        
        return $products[$product_id];
        
    }
    
    
    public function FormatPrice($price, $sale_percent=0, $sale_dollar=0)
    {
        $item_price             = money_format($this->money_format, $price);
        
        if ($sale_percent != 0) {
            # CALCULATE A SALE PERCENTAGE
            $calculated_sale_price  = $price - ($price * ($sale_percent/100));
            $sale_price             = money_format($this->money_format, $calculated_sale_price);
            $sale_text              = "(Save {$sale_percent}%)";
            
            $price_display = "
                <div class='item_price_wrapper'>
                    <div><div class='item_price_sale' style='float:left;'>{$item_price}</div><span class='item_price_sale_text' style='float:left;'>&nbsp;ON SALE</span><div style='clear:both;'></div></div>
                    <div class='item_price_normal'>{$sale_price}</div>
                    <div class='item_price_sale_text'>{$sale_text}</div>
                </div>
            ";
        } elseif ($sale_dollar != 0) {
            # CALCULATE A SALE DOLLAR AMOUNT
            $calculated_sale_price  = $price - $sale_dollar;
            $sale_price             = money_format($this->money_format, $calculated_sale_price);
            $sale_dollar            = money_format($this->money_format, $sale_dollar);
            $sale_text              = "(Save {$sale_dollar})";
            
            $price_display = "
                <div class='item_price_wrapper'>
                    <div><div class='item_price_sale' style='float:left;'>{$item_price}</div><span class='item_price_sale_text' style='float:left;'>&nbsp;ON SALE</span><div style='clear:both;'></div></div>
                    <div class='item_price_normal'>{$sale_price}</div>
                    <div class='item_price_sale_text'>{$sale_text}</div>
                </div>
            ";
        } else {
            $price_display = "
                <div class='item_price_wrapper'>
                    <div><div class='item_price_sale' style='float:left;'></div><span class='item_price_sale_text' style='float:left;'>&nbsp;</span><div style='clear:both;'></div></div>
                    <div class='item_price_normal'>{$item_price}</div>
                    <div class='item_price_sale_text'>&nbsp;</div>
                </div>
            ";
        }
        
        return $price_display;
    }
    
    public function GetAllCategories()
    {
        $categories         = array();
        $categories_sub     = array();
        
        
        # GET ALL CATEGORIES
        # =================================================
        $records = $this->SQL->GetArrayAll(array(
            'table' => $this->Category_Table,
            'keys'  => '*',
            'where' => "`active`=1 AND `display`=1",
        ));
        
        
        # ASSIGN TO SPLIT ARRAYS - allows for cascading categories
        # =================================================
        foreach ($records as $record) {
            if ($record['parent_id'] == 0) {
                $categories[] = $record;
            } else {
                $categories_sub[] = $record;
            }
        }
        
        
        # SORT THE CATEGORIES
        # ============================================
        $array          = $categories;
        $on             = $this->category_sort_on_field;
        $order          = $this->category_sort_order;
        $categories     = $this->array_sort($array, $on, $order);
        
        
        # CREATE OUTPUT
        # ============================================
        $this->categories           = $categories;
        $this->categories_sub       = $categories_sub;
        $this->categories_global    = '';
        $output                     = "<div class='category'><img src='{$this->arrow_image_location}' border='0' alt='' /><a href='{$this->product_detail_link};type=All;cid=0'>ALL PRODUCTS</a></div><br />";
        
        foreach ($categories as $category) {
            $this->sub_category_list = array();
            
            # output the category
            $id     = $category['store_categories_id'];
            $title  = $category['title'];
            $this->output_global .= $this->FormatCategory($id, $title, -1);
            
            # find out if there are sub categories
            array_push($this->sub_category_list, 'store_categories_id'); 
            $search = $category['store_categories_id'];
            $this->GetSubCategory($search);
        }
        $output .= $this->output_global;
        
        return $output;
    }

    public function GetSubCategory($SEARCH)
    {
        if (count($this->categories_sub) > 0) {
        
        $sub_tp_index   = $this->d2_search ('parent_id', $SEARCH, $this->categories_sub);
        $sub_tp_exists  = ($sub_tp_index > -1) ? true : false;
        
        if ($sub_tp_exists) {
            # get the actual sub T/P record
            $sub_category = $this->categories_sub[$sub_tp_index];
            
            # output the sub T/P
            $id     = $sub_category['store_categories_id'];
            $title  = $sub_category['title'];
            $tab    = (count($this->sub_category_list) > 0) ? count($this->sub_category_list) : 1;
            $this->output_global .= $this->FormatCategory($id, $title, $tab);
            
            # store the id in list so you can later check for sub T/P to this sub T/P
            $search = $sub_category['store_categories_id'];
            array_push($this->sub_category_list, $search); 
            
            # remove it off the sub T/P stack
            unset($this->categories_sub[$sub_tp_index]);
            
            # re-index array
            $this->categories_sub = array_values($this->categories_sub);
            
            #check for subpoints to this one
            $search = $sub_category['store_categories_id'];
            $this->GetSubCategory($search);
        } else {
            if (count($this->sub_category_list) > 0) {
                $new_search = array_pop($this->sub_category_list);
                $this->GetSubCategory($new_search);
            }
        }
        
        
        } // end checking if sub_tp array > 0
    }
    
    public function d2_search ($key, $value, $array)
    {
        # FUNCTION :: Will search for value in key in 2-demensional array
        # will return the first found record index
        
        $index = -1;
        for ($i=0; $i<count($array); $i++) {
            if ($array[$i][$key] == $value) {
                $index = $i;
                $i = count($array);
            }            
        }
        return $index;
    }
    
    public function FormatCategory($STORE_CATEGORIES_ID, $TITLE, $TAB=0)
    {
        $left   = $TAB * $this->tab_over_pixels;
        $output = "<div class='category' style='padding-left:{$left}px'><img src='{$this->arrow_image_location}' border='0' alt='' /><a href='{$this->product_detail_link};type=all;cid={$STORE_CATEGORIES_ID};category={$TITLE}'>{$TITLE}</a></div>";
        return $output;
    }
    
    
    
    
    public function GetProductOptions($store_product_options_ids)
    {
        # GET PRODUCT OPTIONS
        # ==============================================
        $records = $this->SQL->GetArrayAll(array(
            'table' => $this->Product_Options_Table,
            'keys'  => '*',
            'where' => "active=1 AND `store_product_options_id` IN ({$store_product_options_ids})",
        ));
        if ($this->ShowQuery) echo '<br />' . $this->SQL->Db_Last_Query;
        
        $options = '';
        
        if ($records) {
            # FORMAT THEM
            # ==============================================
            $i = 0;
            foreach ($records as $record) {
                $i++;
                
                #option_description
                #option_values
                #default_value
                #selection_type
                
                $variable   = "option_{$i}";
                $title      = $record['option_title'];
                $type       = 'select'; //$record['selection_type'];
                
                
                $select = '';
                $select .= "<select id='FORM_{$variable}' class='formitem' name='FORM_{$variable}'>";
                
                $lines = explode("\n", $record['option_values']);
                foreach ($lines as $line) {
                    
                    $parts              = explode('|', $line);
                    $selected           = ($record['default_value'] == $parts[0]) ? 'SELECTED' : '';
                    $price_formatted    = '';
                    
                    # format the price
                    if (isset($parts[2])) {
                        $price              = trim($parts[2]);                                          // remove whitespace
                        $symbol             = (strpos($price, '-') === false) ? '+' : '-';              // determine if dollar is negative
                        $price              = str_replace(array('+','-','$'), '', $price);              // strip off excess info
                        $price_formatted    = "{$symbol} " . money_format($this->money_format, $price); // assemble formatted price
                    }
                    
                    $price      = ($price_formatted) ? " (<span style='color:$990000;'>{$price_formatted}</span>)" : '';
                    
                    $select .= "<option value='{$parts[0]}' {$selected}>{$parts[1]}{$price}</option>";
                }
                $select .= "</select>";
                
                $options .= "
                <div>
                <div class='col' style='width:50px;'><b>{$title}:</b></div>
                <div class='col'>$select</div>
                <div class='clear'></div>
                </div><br />
                ";
            }
        }
        
        return $options;
    }
    
    
    public function FormatAndOutputAllProducts($products)
    {
        $output = "<div style='width:{$this->total_contents_width};'>";
        $output .= "<div class='breadcrumb'>{$this->breadcrumb}</div>";
        
        
        $categories = $this->GetAllCategories();
        
        
        
        //CATEGORIES WRAPPER
        $output .= "<div class='col' style='width:{$this->categories_width}'>";
        
        $output .= "
        <div class='col'>
            <div style='padding-top:{$this->product_wrapper_padding};'></div>
            <div class='category_outter_wrapper'>
            <div class='category_inner_wrapper'>
                <div class='category_header'>CATEGORIES</div>
                <div class='category_list'>
                    {$categories}
                </div>
            </div> <!-- END category_inner_wrapper -->
            </div> <!-- END category_outter_wrapper -->
        </div> <!-- END col -->
        ";
        
        //END CATEGORY WRAPPER
        $output .= "</div>";
        
        //CATEGORIES -> CONTENTS GAP
        $output .= "<div class='col' style='width:{$this->category_contents_gap}'>&nbsp;</div>";
        
        //PRODUCT WRAPPER
        $output .= "<div class='col' style='width:{$this->products_width};'>";
        #border:1px solid green; padding-top:5px; padding-left:5px;'
        if ($products) {
            foreach ($products as $product) {
                # FORMAT VARIOUS ITEMS
                # =============================
                $price_display  = $this->FormatPrice($product['price'], $product['sale_percent'], $product['sale_dollar']);
                $product['description'] .= ' Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean fermentum adipiscing magna ac tristique. Donec pretium nulla eu neque interdum hendrerit. In hac habitasse platea dictumst.';
                $description    = truncate($product['description'], $this->description_len_trunc, '...');
                
                
                $output .= <<<ITEM
                <div class="col product_gapping">
                <div class="item_outter_wrapper">
                <div class="item_inner_wrapper">
                <a href='{$this->product_detail_link};type=single;product_id={$product['store_products_id']};category={$this->category};cid={$this->store_categories_id}'>
                    <div class="item_picture"><img src="{$this->image_directory}{$product['picture1']}" width="{$this->item_picture_width}" height="{$this->item_picture_height}" border="0" alt="{$product['picture1']}" /></div>
                    <div class="item_content_wrapper">
                        <div class="item_title">{$product['title']}</div>
                        <div class="item_code">{$product['part_number']}</div>
                        <div class="item_description">{$description}</div>
                        <div>&nbsp;</div>
                        <div>{$price_display}</div>
                    </div>
                </a>
                </div> <!-- END item_inner_wrapper -->
                </div> <!-- END item_outter_wrapper -->
                </div> <!-- END col -->
ITEM;
            }
        } else {
            $output .= "<h2>UNABLE TO LOCATE PRODUCTS</h2>";
        }
        
        $output .= "<div class='clear'></div>"; //clear product divs
        
        
        //END PRODUCT WRAPPER
        $output .= "</div>";
        
        
        
        $output .= "<div class='clear'></div>"; //clear category -> product divs
        $output .= "</div>"; //end master wrapper
        
        echo $output;
    }
    
    
    public function FormatAndOutputSingleProduct($product)
    {
        if ($product) {
        
            # FORMAT BREADCRUMB
            # ==============================================
            $title  = $product['title'];
            $link   = "";
            $this->AddToBreadcrumb($title, $link);
            
            
            $product['store_products_id'];
            $product['part_number'];
            $product['manufacturer_part_number'];
            $product['manufacturer_id'];
            $product['distributor_id'];
            $product['categories'];
            $product['title'];
            $product['description'];
            $product['description_long'];
            $product['price'];
            $product['shipping'];
            $product['show_date'];
            $product['hide_date'];
            $product['discounts'];
            $product['picture1'];
            $product['picture2'];
            
            $fake_specifications = "
                Curabitur lacus augue, cursus vel tristique eget, elementum eget tellus. Curabitur ut dolor sit amet orci rhoncus mollis. Donec venenatis lobortis metus at luctus. Nullam malesuada, dolor in cursus pulvinar, tortor ante auctor nulla, vitae aliquet metus lectus nec augue. Curabitur dictum gravida aliquet. Etiam tincidunt, nibh ut consequat iaculis, nisi quam adipiscing ante, vel tempor justo tellus sit amet mi. Quisque ut tempus erat. Pellentesque eros mauris, porta ut venenatis vel, aliquam id felis. Curabitur facilisis mauris non mi condimentum feugiat. Lorem ipsum dolor sit amet.
                <br /><br />
                <ul>
                <li>Curabitur condimentum scelerisque erat, at convallis diam gravida in.</li>
                <li>Aenean convallis vehicula eros, a consequat odio vehicula quis.</li>
                <li>Quisque a orci ultrices nisi elementum dictum nec quis dui.</li>
                <li>Sed at augue a nisi convallis sollicitudin sit amet et purus.</li>
                <ul>
                <br />
                </ul>
                <li>Curabitur suscipit purus ac risus placerat molestie.</li>
                <li>In tincidunt dolor vitae justo aliquam pretium.</li>
                <li>Nunc molestie neque neque, et ultrices lectus.</li>
                </ul>";
            
            
            # PRODUCT OPTIONS
            # =========================================================
            $options = ($product['product_options']) ? $this->GetProductOptions($product['product_options']) : '';
            $options = ($options) ? "{$options}" : 'NO PRODUCT OPTIONS';
            
            
            # FAQs SECTION
            # =========================================================
            $faq[0]['question']     = "How long does it take to get my order?";
            $faq[0]['answer']       = "Plain envelopes usually ship the same or next business day. Printed Envelopes have a production time of 5 business days, standard production, or 2 business days, rush production. Once your envelopes ship, they can take 1-5 business days depending on the level of service selected at checkout.";
            $faq[1]['question']     = "Do you send samples?";
            $faq[1]['answer']       = "Yes, samples are $1.00 each, and you get a $1.00 coupon to use on your next order.";
            $faq[2]['question']     = "Do you have discounts for resellers?";
            $faq[2]['answer']       = "Yes! We have a trade discount program for printing, advertising, graphic design, and greeting card companies. Please visit our <a href='/ae/control/tradediscounts'>Trade Discount Program</a> page.";
            $faq[3]['question']     = "How do I order a printed envelope?";
            $faq[3]['answer']       = "On the product page, select the printed tab. You will need to select how many ink colors will be printed on the front and back of the envelope. The back of the envelope has the flap. Then select the production time that will suit your needs. Next, Choose quantity, and select if you want to design your envelope online, or you will be uploading a digital file. You can review our <a href='/ae/control/printguidelines'>Artwork Specs</a>.";
            $faq[4]['question']     = "How much is shipping?";
            $faq[4]['answer']       = "Shipping is calculated based upon the weight of the shipment and the destination zip code. On the product page you can enter your zip code to see the shipping costs once you select a quantity.";
            $faq[5]['question']     = "How do I order a quantity not shown?";
            $faq[5]['answer']       = "There is a text entry box in the 'Prices &amp; Options' area of the product page. Enter any quantity (increments of 50, for plain envelopes, 500, for printed envelopes) and your price for that quantity will appear. Select it, and add to cart.";
            
            $tab_content_faqs = $this->MakeFAQ($faq);
            unset($faq);
            
            
            # GALLERY SECTION
            # =========================================================
            $images = array('mat.png', 'camera.png', 'gloves.png');
            $gallery_images = $this->MakeGallery($images);
            
            
            # OTHER STUFF
            # =========================================================
            $price_display          = $this->FormatPrice($product['price'], $product['sale_percent'], $product['sale_dollar']);
                
            $product_link           = $this->product_link_base;
            
            $FACEBOOK               = new General_Facebook;
            $facebook_button        = $FACEBOOK->CreateButton($product_link);
            
            $TWITTER                = new General_Twitter;
            $twitter_text           = "";
            $twitter_button         = $TWITTER->CreateButton($product_link, $twitter_text);
            
            
            # SPECIFICATIONS TAB SECTION
            # =========================================================
            $TABS = new Tabs('tab', 'tab_edit');
            
            $tab_content_specifications         = "{$product['description_long']} {$fake_specifications}";
            $tab_content_gallery                = ($gallery_images) ? $gallery_images : "This product has no additional pictures.";
            $tab_content_reviews                = "This product hasn't been reviewed yet.<br /><br />Be the first to review this product!";
            $tab_content_faqs                   = "$tab_content_faqs";
            
            
            if ($this->show_specifications)     $TABS->AddTab('Specifications', "<div class='tab_content_wrapper'>{$tab_content_specifications}</div>");
            if ($this->show_gallery)            $TABS->AddTab('Gallery', "<div class='tab_content_wrapper'>{$tab_content_gallery}</div>");
            if ($this->show_reviews)            $TABS->AddTab('Reviews', "<div class='tab_content_wrapper'>{$tab_content_reviews}</div>");
            if ($this->show_faqs)               $TABS->AddTab('FAQs', "<div class='tab_content_wrapper'>{$tab_content_faqs}</div>");
            
            $tab_content = $TABS->OutputTabs(true);
            
            
            #<img src='{$this->image_directory}{$product['picture1']}' border='0' alt='Product Picture' />
            
            
            # OUTPUT
            # =========================================================
            $output = "
            
            <div style='width:{$this->total_contents_width};'>
            <div class='breadcrumb'>{$this->breadcrumb}</div>

            
            <br />
            
            <div class='col' style='width:{$this->single_product_col_left_width}'>
            
            
                <div class='picture_wrapper' id='product_picture_holder'>
                    <center>
                    <img src='{$this->image_directory}{$product['picture1']}' border='0' alt='Product Picture' />
                    </center>
                </div>
                <div>{$facebook_button}</div>
                <div>{$twitter_button}</div>
                
                <br /><br />
                
                <div class='specifications_wrapper'>
                    {$tab_content}
                </div>
            
            
            </div>
            
            <div class='col' style='width:{$this->single_product_gap_width}'>&nbsp;</div>
            
            <div class='col' style='width:{$this->single_product_col_right_width}'>
            
                <div class=''>
                    
                    
                    
                    
                    <div class='col  section_number'>1.</div>
                    <div class='col picture_wrapper' style='width:85%; padding:0px;'>
                        <div class='section_title'>BASIC PRODUCT PRICING</div>
                        <br />
                        <div class='pad_20_special product_price'>{$price_display}</div>
                    </div>
                    <div class='clear'></div>
                    <br /><br /><br />
                    
                    
                    <div class='col  section_number'>2.</div>
                    <div class='col picture_wrapper' style='width:85%; padding:0px;'>
                        <div class='section_title'>SELECT OPTIONS</div>
                        <br />
                        <div class='pad_20_special'>{$options}</div>
                    </div>
                    <div class='clear'></div>
                    <br /><br /><br />
                    
                    
                    <div class='col  section_number'>3.</div>
                    <div class='col picture_wrapper' style='width:85%; padding:0px;'>
                        <div class='section_title'>ADD TO CART</div>
                        <br />
                        <div class='pad_20_special'>[add to cart]</div>
                    </div>
                    <div class='clear'></div>
                    
                </div>
                
                
            </div>
            <div class='clear'></div>
            </div>
            ";
            
            $style = "
                .section_number {
                    font-size:16px;
                    font-weight:bold;
                    color: {$this->header_color};
                    background-color: {$this->border_color};
                    padding:10px;
                }
                .section_title {
                    font-size:14px;
                    font-weight:bold;
                    color: {$this->header_color};
                    background-color: {$this->border_color};
                    padding:10px;
                }
                .pad_20_special {
                    padding:5px 20px 20px 20px;
                }
                ";
            AddStyle($style);
            
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
    
    
    public function GetAllProducts($where='')
    {
        $where = ($where) ? " AND $where" : '';
        
        # 1. GET TRANSATIONS
        $records = $this->SQL->GetArrayAll(array(
            'table' => $this->Product_Table,
            'keys'  => '*',
            'where' => "active=1 $where",
        ));
        if ($this->ShowQuery) echo '<br />' . $this->SQL->Db_Last_Query;
        
        return $records;
    }
    
    
    public function AddScript()
    {
        $script = "
            $('.faq_q').bind('click', function() {
                $(this).parent().find('p').toggle();
            });
            
            $('.image_gallery_picture_wrapper img').bind('click', function() {
                //var new_img_src = $(this).attr('src');
                var new_img_src = $(this).attr('src_large');
                $('#product_picture_holder img').fadeOut(function() {
                    $('#product_picture_holder img').attr('src', new_img_src).fadeIn();
                    });
            });
        ";
        //<img src='{$this->image_directory}{$product['picture1']}' border='0' alt='{$product['title']}' />
        AddScriptOnReady($script);
    }
    
    
    public function AddStyle() 
    {
        $style = "
        #loader.loading {
            background: url(/office/images/loader.gif) no-repeat center center;
        }
        
        .product_title {
            color:{$this->header_color};
            font-size:18px;
            font-weight:bold;
        }
        
        .product_price {
            color:{$this->highlite_color};
            font-size:18px;
            font-weight:bold;
        }
        
        .c_highlite {
            color:{$this->highlite_color};
        }
        
        .c_header {
            color:{$this->header_color};
        }
        
        
        .picture_wrapper {
            border:1px solid {$this->border_color};
            padding:5px;
            background-color:{$this->background_color_secondary};
        }
        
        .specifications_wrapper {
            border:0px solid {$this->border_color};
        }
        
        
        .tab_content_wrapper {
            height: 200px;
			padding: 10px 15px 0 20px;
			overflow: auto;
        }
        
        /* faqs tab
        ---------------------------------------------------------------------- */
		.faqs {
			/*width: 390px;*/
			
		}
		.faqs ul { 
            list-style: none; 
            padding-left: 0px;
            margin-left: 0px;
        }
        .faqs li { 
            list-style: none; 
            margin: 0 0 10px 0; 
            
            padding-left: 0px;
            margin-left: 0px;
        }
		.faqs ul li a {
            text-decoration:none;
            line-height: 16px;
		}
        .faq_q {
            color:{$this->header_color};
            font-weight:bold;
            cursor:pointer;
        }
        .faq_a {
            border-bottom:1px solid {$this->border_color};
            padding-bottom:10px;
        }
		
        
        
        
        
        
        /* Image Gallery Tab 
        ---------------------------------------------------------------------- */

        .image_gallery_list {
          margin: 0px 0 0 -40px;
          padding-left: 45px;
          /*width: 440px;*/
        }

        .image_gallery_list:after {
          content: '';
          display: block;
          height: 0;
          overflow: hidden;
          clear: both;
        }

        .image_gallery_list li {
          /*width: 128px;*/
          /*margin: 20px 0 0 35px;*/
          float: left;
          text-align: center;
          font-family: 'Helvetica Neue', sans-serif;
          line-height: 17px;
          color: #686f74;
          /*height: 177px;*/
          overflow: hidden;
          padding-bottom:10px;
        }

        .image_gallery_list li img,
        .image_gallery_list li strong {
          display: block;
        }

        .image_gallery_list li strong {
          color: #fff;
        }
        
        .image_gallery_picture_wrapper {
            padding:3px;
            border:1px solid #ccc;
        }
        
        .image_gallery_picture_wrapper:hover img{
            border:1px solid #ddd;
            background-color:#ccc;
        }
        
        
        
        
        
        
        
        
        
        
        
        
        .col {
            float:left;
        }
        .col_l {
            float:left;
            border:1px solid blue;
        }
        .col_r {
            float:right;
            border:1px solid red;
        }
        .content_left {
            text-align:left;
        }
        .content_right {
            text-align:right;
        }
        .clear {
            clear:both;
        }
        ";
        AddStyle($style);
    }
    
    
    public function AddStyleAllProducts()
    {
        $style = "
        .product_gapping {
            padding:{$this->product_wrapper_padding};
        }
        .category_header {
            background-color:{$this->background_color_secondary};
            color:{$this->header_color};
            font-weight:bold;
            font-size:16px;
            padding:3px;
        }
        .breadcrumb {
            background-color:{$this->background_color_secondary};
            color:{$this->header_color};
            font-weight:bold;
            font-size:16px;
            padding:3px;
        }
        .breadcrumb a{
            text-decoration:none;
            color:{$this->header_color};
        }
        .category_list {
            padding:10px;
            font-size:14px;
        }
        .category_list a{
            text-decoration:none;
            font-weight:normal;
            color:{$this->highlite_color};
        }
        .category_list a:hover{
            text-decoration:none;
            border-bottom:1px solid #000;
            font-weight:bold;
            color:{$this->highlite_color};
        }
        .category_outter_wrapper {
            padding:5px;
            width:{$this->category_wrapper_width};
            border:1px solid {$this->border_color};
            /*background-color:#fff;*/
        }
        .category_inner_wrapper {
            width:{$this->category_wrapper_width};
            /*height:{$this->category_wrapper_height};*/
            /*border:1px solid red;*/
            background-color:{$this->background_color_primary};
        }
        .item_outter_wrapper {
            padding:5px;
            width:{$this->product_wrapper_width};
            border:1px solid {$this->border_color};
            /*background-color:#fff;*/
        }
        .item_inner_wrapper {
            width:{$this->product_wrapper_width};
            height:{$this->product_wrapper_height};
            /*border:1px solid red;*/
            background-color:{$this->background_color_primary};
        }
        .item_picture {
            
        }
        .item_content_wrapper {
            padding-top:10px;
        }
        .item_title {
            font-weight:bold;
            font-size:14px;
            color:{$this->header_color}; /*#44636E;*/
            padding-bottom:0px;
        }
        .item_code {
            font-weight:normal;
            font-size:8px;
            color:{$this->highlite_color}; /*#44636E;*/
            font-style:italic;
            padding-bottom:5px;
        }
        .item_description {
            font-weight:normal;
            font-size:12px;
            color:#999;
        }
        .item_price_wrapper {
            background-color:{$this->background_color_secondary};
            padding:5px;
        }
        .item_price_normal {
            font-weight:bold;
            font-size:18px;
            color:{$this->header_color}; /*#44636E;*/
        }
        .item_price_sale {
            font-weight:normal;
            font-size:12px;
            text-decoration:line-through;
            color:{$this->header_color}; /*#44636E;*/
        }
        .item_price_sale_text {
            font-weight:normal;
            font-size:12px;
            color:{$this->highlite_color};
        }
        
        
        
        .col {
            float:left;
        }
        .col_l {
            float:left;
            border:1px solid blue;
        }
        .col_r {
            float:right;
            border:1px solid red;
        }
        .content_left {
            text-align:left;
        }
        .content_right {
            text-align:right;
        }
        .clear {
            clear:both;
        }
        ";
        AddStyle($style);
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
            $output .= "<li>
                            <div class='image_gallery_picture_wrapper'>
                            <img src='/gimage/200x100{$this->image_directory}{$image}' src_large='/gimage/500x500{$this->image_directory}{$image}' border='0' alt='{$image}' />
                            </div>
                            </li>";
            //c1-1
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



    
    public function array_sort($array, $on, $order='SORT_DESC')
    {
      $new_array = array();
      $sortable_array = array();
 
      if (count($array) > 0) {
          foreach ($array as $k => $v) {
              if (is_array($v)) {
                  foreach ($v as $k2 => $v2) {
                      if ($k2 == $on) {
                          $sortable_array[$k] = $v2;
                      }
                  }
              } else {
                  $sortable_array[$k] = $v;
              }
          }
 
          switch($order)
          {
              case 'SORT_ASC':   
                  #echo "ASC";
                  asort($sortable_array);
              break;
              case 'SORT_DESC':
                  #echo "DESC";
                  arsort($sortable_array);
              break;
          }
 
          foreach($sortable_array as $k => $v) {
              $new_array[] = $array[$k];
          }
      }
      return $new_array;
    } 

    
    
} // end class