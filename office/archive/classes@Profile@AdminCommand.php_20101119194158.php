<?php

/* ========================================================================== 
    CLASS :: Profile_AdminCommand
    
    Used by administrators to manage the website - doing common tasks. This
    one class is made to replace having multiple icons on the desktop. Only
    use this class to instantiate other classes within a tabbed window.
    
    EXAMPLE ->
    Instead of having a reporting module - this class will instantiate 
    the reporting class within its own tab.
    
# ========================================================================== */

class Profile_AdminCommand
{
    public $load_all_records = true; // If TRUE - will load ALL records

    public $show_profile                = true; // Show the customer profile tab
    public $show_purchases              = true; // Show purchases made by customer tab
    public $show_sessions               = true; // Show booked session for cutomer tab
    public $show_touchpoints            = true; // Show all communications made with customer tab
    
    
    public $show_actions                = true; // Show common actions that can be done
    public $show_customers              = true; // Show all customers
    public $show_instructors            = true; // Show all instructors
    public $show_administrators         = true; // Show all administrators
    public $show_test_procedures        = true; // Show test procedures
    
    
    
    
    
    public $wh_id                       = 0;
    public $table_sessions              = 'sessions';
    public $table_sessions_checklist    = 'session_checklists';
    public $table_instructor_profile    = 'instructor_profile';
    
    public $item_picture_width          = '160px';  // Viewing all products - width of product image
    public $item_picture_height         = '120px';  // Viewing all products - height of product image
    public $product_wrapper_width       = '160px';  // Viewing all products - width of holder (should be close to item_picture_width)
    public $product_wrapper_height      = '290px';  // Viewing all products - height of holder
    public $product_wrapper_padding     = '10px';   // Gap between products - note that actual gap with be twice this width
    
    public $category_wrapper_width       = '200px';  // Viewing all products - width of category holder
    public $category_wrapper_height      = '350px';  // Viewing all products - height of category holder --> NOT USED
    
    
    public $single_product_col_left_width      = '500px';   // Viewing single product - left column
    public $single_product_gap_width           = '50px';    // Viewing single product - gap between columns
    public $single_product_col_right_width     = '200px';   // Viewing single product - right column
    
    public $arrow_image_location        = "/office/images/arrow_dotted.gif";
    public $description_len_trunc       = 60; // How many characters to show before truncating description on general listing

    public $total_contents_width        = '950px';  // width of whole product-listing table
    public $categories_width            = '200px';  // width of categories area - needs to match or be larger than "category_wrapper_width"
    public $category_contents_gap       = '50px;';  // gap between categories and products
    public $products_width              = '700px';  // width of prodcuts area

    
    // ----------- styles --------------
    public $border_color                = "#D7D7D7";
    public $background_color_primary    = "#FFFFFF";
    public $background_color_secondary  = "#F5F5F5";
    public $header_color                = "#044577";
    public $highlite_color              = "#FC7E22"; 
    
   
    
    
    
    public $page_location               = '';
    public $product_detail_link         = '';
    

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
      
    public function Execute()
    {
        
        
        $this->AddStyle();
        $this->AddStyleAllProducts();
        
        
        $this->LoadTabs();
    }
    
    

    

    
    public function GetCommonActions()
    {
        
        
        $link_0     = "/office/dev_richard/class_execute;class=Profile_CreateAccountCustomerAdministrator";
        $script_0   = "top.parent.appformCreate('Window', '{$link_0}', 'apps'); return false;";
        
        $link_4     = "/office/dev_richard/class_execute;class=Profile_CreateAccountInstructor";
        $script_4   = "top.parent.appformCreate('Window', '{$link_4}', 'apps'); return false;";
        
        $link_1     = "/office/dev_richard/class_execute;class=Profile_CancelAccountCustomer";
        $script_1   = "top.parent.appformCreate('Window', '{$link_1}', 'apps'); return false;";
        
        $link_5     = "/office/dev_richard/class_execute;class=Profile_CancelAccountInstructor";
        $script_5   = "top.parent.appformCreate('Window', '{$link_5}', 'apps'); return false;";
        
        $link_2     = "/office/dev_richard/class_execute;class=Profile_CustomerProfileFreeCredits";
        $script_2   = "top.parent.appformCreate('Window', '{$link_2}', 'apps'); return false;";
        
        $link_3     = "/office/dev_richard/class_execute;class=Profile_CustomerProfileAddAccount";
        $script_3   = "top.parent.appformCreate('Window', '{$link_3}', 'apps'); return false;";
        
        $output = "
        <div class='btn_actions'><a href='#' onclick=\"{$script_0}\">CREATE CUSTOMER ACCOUNT</a></div>
        <br />
        <div class='btn_actions'><a href='#' onclick=\"{$script_4}\">CREATE INSTRUCTOR ACCOUNT</a></div>
        <br /><br />
        <div class='btn_actions'><a href='#' onclick=\"{$script_1}\">DELETE CUSTOMER ACCOUNT</a></div>
        <br />
        <div class='btn_actions'><a href='#' onclick=\"{$script_5}\">DELETE INSTRUCTOR ACCOUNT</a></div>
        <br /><br />
        <div class='btn_actions'><a href='#' onclick=\"{$script_2}\">GIVE FREE CREDITS</a></div>
        <br />
        <div class='btn_actions'><a href='#' onclick=\"{$script_3}\">CREATE TEST ACCOUNT</a></div>
        ";
        
        $style = "
            .btn_actions {
                border:1px solid #d5d5d5;
                background-color: #eee;
                font-size:14px;
                font-weight:bold;
                padding:10px;
                width:200px;
            }
            .btn_actions a {
                text-decoration:none;
            }
        ";
        AddStyle($style);
        
        return $output;
    }
    
    public function LoadTabs()
    {
        # TAB SECTION
        # =========================================================
        $wh_id = 1000000002;
        $OBJ_TABS                           = new Tabs('tab', 'tab_edit');
        #$OBJ_SESSIONS                       = new Profile_CustomerProfileSessions();
        #$OBJ_CONTACTS                       = new Profile_CustomerProfileContacts($wh_id);
        #$OBJ_TOUCHPOINTS                    = new Profile_CustomerProfileTouchpoints($wh_id);
        $OBJ_TESTING                        = new General_TestingInstructions();
        
        $date = date('His');
        
        #$tab_content_profile                = $OBJ_CONTACTS->EditRecordSpecial($wh_id);
        #$tab_content_purchases              = 'tab_content_purchases<br />===> ' . $date;
        #$tab_content_sessions               = $OBJ_SESSIONS->ListTableText();
        #$tab_content_touchpoints            = $OBJ_TOUCHPOINTS->GetAllTouchpoints($wh_id);
        $tab_content_actions                = $this->GetCommonActions();
        $tab_content_customers              = 'customers';
        $tab_content_instructors            = 'instructors';
        $tab_content_administrators         = 'administrators';
        $tab_content_testing_procedures     = $OBJ_TESTING->ListTableText();
        
        #if ($this->show_profile)            $OBJ_TABS->AddTab('Profile', "<div class='tab_content_wrapper'>{$tab_content_profile}</div>");
        #if ($this->show_purchases)          $OBJ_TABS->AddTab('Purchases', "<div class='tab_content_wrapper'>{$tab_content_purchases}</div>");
        #if ($this->show_sessions)           $OBJ_TABS->AddTab('Sessions', "<div class='tab_content_wrapper'>{$tab_content_sessions}</div>");
        #if ($this->show_touchpoints)        $OBJ_TABS->AddTab('Touchpoints', "<div class='tab_content_wrapper'>{$tab_content_touchpoints}</div>");
        if ($this->show_actions)            $OBJ_TABS->AddTab('Common Actions', "<div class='tab_content_wrapper'>{$tab_content_actions}</div>");
        
        
        
        if ($this->show_customers)          $OBJ_TABS->AddTab('Customers', "<div class='tab_content_wrapper'>{$tab_content_customers}</div>");
        if ($this->show_instructors)        $OBJ_TABS->AddTab('Instructors', "<div class='tab_content_wrapper'>{$tab_content_instructors}</div>");
        if ($this->show_administrators)     $OBJ_TABS->AddTab('Administrators', "<div class='tab_content_wrapper'>{$tab_content_administrators}</div>");
        if ($this->show_test_procedures)    $OBJ_TABS->AddTab('Testing Procedures', "<div class='tab_content_wrapper'>{$tab_content_testing_procedures}</div>");
        
        
        $tab_content = $OBJ_TABS->OutputTabs(true);
        
        $output = "
        <div style='width:900px;'>
            $tab_content
        </div>
        ";
        
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
        
        if ($record) {
            return $record;
        } else {
            echo "<h2>UNABLE TO LOCATE PRODUCT</h2>";
            //exit();
        }
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
        
        if ($records) {
            return $records;
        } else {
            echo "<h2>UNABLE TO LOCATE PRODUCTS</h2>";
            //exit();
        }
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
            /*height: 200px;*/
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
        $output = "<table border='0' colspacing='0' colpadding='0' style='{$style_table}'>";
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



}
