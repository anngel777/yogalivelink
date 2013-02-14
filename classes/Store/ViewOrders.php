<?php
class Store_ViewOrders
{
    public $WH_ID = 0;

    // ----------- SHIPMENT TRACKING ---------------
    
    private $tracking_test_fedex     = "999999999999";
    private $tracking_test_ups       = "1Z6567640301069592";
    private $tracking_test_usps      = "";
    
    private $tracking_link_fedex     = "http://www.fedex.com/Tracking?action=track&tracknumbers=";
    private $tracking_link_ups       = "http://wwwapps.ups.com/tracking/tracking.cgi?tracknum=";
    private $tracking_link_usps      = "";


    // ----------- styles --------------
    
    public $border_color                = "#D7D7D7";
    public $background_color_primary    = "#FFFFFF";
    public $background_color_secondary  = "#F5F5F5";
    public $header_color                = "#044577";
    public $highlite_color              = "#FC7E22";
    
    public $page_location               = '';

    
    public $colgap                      = '&nbsp;&nbsp;';    
    
    
    public $H2_Style = 'style="color:#d47; margin:3px 3px;"';

    public $Order_Table_Options = 'style="background-color:#69f; border:2px solid #69f; width:500px; font-family:Arial,Verdana,Helvetica,sans-serif;" cellspacing="1" align="center"';
    public $Order_Th_Options    = 'align="right" style="background-color:#69f; color:#fff; padding:0.25em; width:200px"';
    public $Order_Td_Options    = 'style="background-color:#fff; padding:0.25em;"';

    public $Cart_Table_Options  = 'id="shoppingcart" align="center" border="0" cellpadding="0" cellspacing="2"';
    public $Cart_Th_Options     = '';
    public $Cart_Td_Options     = '';
    public $Cart_Table_Span     = 'class="cart_convert"';
    public $Cart_Table_Note     = 'id="cart_note"';
    public $Cart_Table_Total    = 'id="cart_total"';
    public $End_Note_Td_Options = 'style="font-size:0.8em;background-color:#fff; padding:0.25em;"';


    public $Bill_Table_Options  = 'style="background-color:#ccc; width:500px;" align="center"  border="0" cellpadding="0" cellspacing="1"';
    public $Bill_Th_Options     = 'style="background-color:#ccc; color:#666; padding:0.25em;"';
    public $Bill_Td_Options     = 'style="background-color:#fff; padding:0.25em;"';
    public $Bill_Table_Span     = 'style="color:#f00;background-color:#eee;"';
    public $Bill_Table_Note     = 'style="color:red; font-size:0.8em"';
    public $Bill_Table_Total    = 'style="background-color:#ff7;background-color:#fff;padding:0.25em;"';

    // ------- Order Info -------
    public $Order_Id = '';
    public $Save_Transactions = 0;
    public $Final_Array = array();
    public $Promo_Code  = '';

    // ------ System Variables -----
    public $SQL = '';  //SQL PDO connection object
    public $Error = '';
    public $Send_Emails = true; // use in testing
    public $Clear_Cart_At_End = true; // use in testing

    // ------ Translatable Text ------
    public $Text = array(
        'EMAIL_BUYER_SUBJECT'           => 'Your Online Order',
        'EMAIL_SELLER_SUBJECT'          => 'Online Order',
        'EMAIL_SAVE_NOTICE'             => 'Save this e-mail for your records!',
        'SUCCESSFUL_ORDER_MESSAGE_H3'   => 'Order Successfully Completed!',
        'SUCCESSFUL_ORDER_MESSAGE_P1'   => 'Print this page for your records.',
        'SUCCESSFUL_ORDER_MESSAGE_P2'   => 'You will receive a confirmation e-mail shortly.',
        'CART_RESET_FLASH_NOTICE'       => 'Store Information is Reset',
        'CART_EMPTY_NOTICE'             => 'Shopping Cart is Empty',
        'ERROR_CARD_DID_NOT_PROCESS'    => 'Order Failed, Credit Card did not Process!',
        'ERROR_CARD_DECLINED'           => 'Order Failed, Credit Card Declined!',
        'ERROR_CARD_PROCESS_ERROR'      => 'Credit Card Process Error!',
        'ERROR_EMAIL_TO_CUSTOMER_FAILED'=> 'Sending Confirmation Message to you failed!',
        'ERROR_EMAIL_TO_COMPANY_FAILED' => 'Sending Message to Company failed!',
        'CART_TITLE'                    => 'Shopping Cart',
        'CART_HEADING_REMOVE'           => 'Remove',
        'CART_HEADING_NEW_QTY'          => 'New Qty',
        'CART_HEADING_SHIPPING'         => 'Shipping',
        'CART_HEADING_ITEM'             => 'Item',
        'CART_HEADING_TITLE'            => 'Title',
        'CART_HEADING_QTY'              => 'Qty',
        'CART_HEADING_PRICE'            => 'Price',
        'CART_HEADING_TOTAL'            => 'Total',
        'CART_TBD'                      => '*TBD',
        'CART_TBD_NOTE'                 => '*TBD = To Be Determined',
        'CART_APPROXIMATE_CONVERSION_TO'=> 'Approximate Conversion to',
        'CART_SALES_TAX'                => 'Sales Tax',
        'CART_SHIPPING_AND_HANDLING'    => 'Shipping &amp; Handling',
        'CART_PROMO_CODE_NOTE'          => 'Promotional Code',
        'CART_TOTAL'                    => 'Total',
        'CART_BUTTON_UPDATE_CART'       => 'Update Cart',
        'CART_BUTTON_CLEAR_ALL'         => 'Clear All',
        'CART_BUTTON_CHECKOUT'          => 'Checkout',
        'CART_BUTTON_CONTINUE_SHOPPING' => 'Continue Shopping',
        'CART_BUTTON_BACK'              => 'Back',
        'FORM_BILLING_ITEMS'            => 'Billing Items',
        'FORM_ORDER_ID'                 => 'Order ID',
        'FORM_YOUR_ORDER_INFORMATION'   => 'Your Order Information',
        'FORM_BILLING_INFORMATION'      => 'Billing Information',
        'FORM_CREDIT_CARD_TYPE'         => 'Credit Card Type',
        'FORM_SUBMIT_ORDER'             => 'Submit Order',
        'FORM_CURRENCY'                 => 'Currency',
        'FORM_COUNTRY'                  => 'Country',
        
    );



    // ------- Cart Options ------
    public $Use_Cart = true;
    public $Cart = array();
    public $Calculate_Shipping = false;
    public $Default_Return_Page    = '/product_page';
    public $Use_Currency_Conversion= false;
    public $Show_Country_State_On_Cart = false;
    public $Location_Record = '';
    public $Cart_Itemize_Shipping = false;
    public $Use_Sales_Tax = false;
    public $State_Sales_Tax_Array = array();  // state_code => tax_rate

    public $Ajax_Cart = false;


    public $Cart_End_Notes  = '';
    public $Shipping_Option = '';
    public $Shipping_Options = array();
    public $Shipping_Item_Field = 'shipping_option';
    public $Show_Shipping_Options = false;
    public $Shipping_Options_Form_Item = '';

    // ------ Currrency Conversions ------
    public $Conversion_Rate        = 1;
    public $Currency_Code          = 'USD';
    public $Have_Conversion        = false;
    public $Currency_Api_key = '';
    public $CC = '';  // Currency conversion object


    // ----- Product Database -----
    public $Product_Table = 'products';
    public $Product_Part_Number_field = 'part_number';
    public $Product_Table_Field_Translations = array(
        'part_number' => 'part_number',
        'category' => 'category',
        'title' => 'title',
        'description' => 'description',
        'weight' => 'weight',
        'shipping' => 'shipping',
        'price' => 'price'
    );

    // ------ Credit Cards -------
    public $Card_Types  = array(
        'Visa' => 'Visa',
        'MasterCard' => 'MasterCard',
        'Amex' => 'American Express',
        'Discover' => 'Discover'
    );



    // ------ Transactions ------
    public $Transaction_Table = 'store_transactions';
    public $Transaction_Items_Table = 'store_transaction_items';


    // ==================================== CONSTRUCT ====================================
    public function  __construct()
    {
        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2010-09-01',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Description' => 'Customer viewing their past orders',
        );
        
        $this->SetSQL();
    }

    public function SetSQL()
    {
        if (empty($this->SQL)) {
            $this->SQL = Lib_Singleton::GetInstance('Lib_Pdo');
        }
    }
    
    public function GetFakeOrdersForCustomer()
    {
        $records[1250][1] = array(
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
        
        $records[1392][1] = array(
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
        
        $records[1392][2] = array(
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
        
        return $records;
        
    }
    
    
    public function Execute($type, $order_number)
    {
        $content = '';
        
        switch ($type) {
            case 'single':
                $content = $this->OutputSingleOrder($order_number);
            break;
            case 'all':
                $content = $this->OutputAllOrders();
            break;
        }
        
        return $content;
    }
    
    
    public function OutputSingleOrder($order_number)
    {
        # GET ORDER
        # ===================================
        //$orders             = $this->GetOrdersForCustomer($WHID);
        $orders             = $this->GetFakeOrdersForCustomer();
        $order_id           = $order_number; //1392;
        $order              = $orders[$order_id];
        $order_date         = $order[1]['order_date'];
        $order_item_count   = count($order);
        
        # FORMAT ORDERS
        # ===================================
        $output = "
            <div class='order_table_order_wrapper order_detail_header'>
                
                <div>
                    <div class='order_detail_header_left'>
                        <span class='c_header'>ORDER #{$order_id}</span>
                    </div>
                    
                    <div class='order_detail_header_right'>
                        <div><span class='c_header'><b>Order Date:</b> {$order_date}</span></div>
                        <div><span class='c_header'><b>Items In Order:</b> {$order_item_count}</span></div>
                    </div>
                    
                    <div class='clear'></div>
                </div>
                
            </div>
        ";
        
        # PRICING VARIABLES
        # ===================================
        $order_subtotal     = 0;
        $order_ship_fees    = 0;
        $order_sales_tax    = 0;
        
        
        $output        .= "<br /><div class='order_table_order_wrapper' style='padding:20px;'>";
        $total_items    = count($order);
        $item_count     = 0;
        
        foreach ($order as $item) {
            $status     = ($item['status_ship'] == 1) ? "<div style='font-weight:bold;'>SHIPPED</div><div>Ship Date: {$item['status_ship_date']}</div>" : "<div style='font-weight:bold;'>PROCESSING</div><div>Expected Delivery Date: {$item['status_ship_delivery_expected']}</div>";
            $date       = ($item_count == 0) ? $item['order_date'] : '&nbsp;';
            
            switch ($item['status_ship_type']) {
                case 'UPS':
                    $ship = "<a href='{$this->tracking_link_ups}{$item['status_ship_tracking_number']}' target='_blank'>{$item['status_ship_tracking_number']}</a>";
                break;
                case 'FEDEX':
                    $ship = "<a href='{$this->tracking_link_fedex}{$item['status_ship_tracking_number']}' target='_blank'>{$item['status_ship_tracking_number']}</a>";
                break;
                default:
                    $ship = "n/a";
                break;
            }
            
            # MAKE DELIVERY INFO
            # ===================================
            $data               = array();
            $data[]             = "Delivery Method:{$this->colgap}|{$item['status_ship_method']}";
            $data[]             = "Ship Delivery Address:{$this->colgap}|{$item['status_ship_address']}";
            $data[]             = "Status:{$this->colgap}|{$status}";
            $data[]             = "Tracking #:{$this->colgap}|{$ship}";
            $delivery_info      = $this->MakeTable($data);
            unset($data);
            
            # MAKE RETURN INFO
            # ===================================
            $data               = array();
            $data[]             = "Returning Item?{$this->colgap}|<a href='#'>RETURN INFO</a>";
            $data[]             = "Changes / Questions?{$this->colgap}|<a href='#' onclick=\"top.parent.appformCreate('Contact Form', 'contact_form','apps'); return false;\">CONTACT US</a>";
            $return_info        = $this->MakeTable($data);
            unset($data);
            
            # CALCULATE PRICING INFORMATION
            # ===================================
            $order_subtotal     = $order_subtotal + $item['price'];
            $order_ship_fees    = $order_ship_fees + $item['price_shipping'];
            $order_sales_tax    = $order_sales_tax + $item['price_sales_tax'];
        
            $item_price         = money_format('%i', $item['price']);
           
            $output .= "
            <div class='order_table_row'>
                <div class='order_detail_picture'>
                    <img src='{$item['picture']}' alt='{$item['title']}' border='0' width='50' height='50'>
                </div>
                <div class='order_detail_description'>
                    <div class='order_detail_description_title'><span class='c_header'>{$item['title']}</span></div>
                    <div class='order_detail_description_description'>{$item['description']}</div>
                </div>
                <div class='order_detail_price'>
                    {$item_price}
                </div>
                <div class='clear'></div>
            </div>
            <br />
            <div class='order_table_row'>
                <div class='order_detail_picture'>
                    &nbsp;
                </div>
                <div class='order_detail_contentboxes'>
                    <div class='order_subcontent_wrapper'>
                        {$delivery_info}
                    </div>
                    <br />
                    <div class='order_subcontent_wrapper'>
                        {$return_info}
                    </div>
                </div>
                <div class='clear'></div>
            </div>
            
            ";
            
            $item_count++;
            
            //$output .= ($total_items > $item_count) ? "<br /><div class='order_table_row' style='border-bottom: 1px dashed {$this->border_color};'></div><br />" : '';
            $output .= "<br /><div class='order_table_row' style='border-bottom: 1px dashed {$this->border_color};'></div><br />";
            
        } //end order_item
        
        
        # MAKE SUBTOTAL INFO
        # ===================================
        $order_total        = $order_subtotal + $order_ship_fees + $order_sales_tax;
        
        $order_subtotal     = money_format('%i', $order_subtotal);
        $order_ship_fees    = money_format('%i', $order_ship_fees);
        $order_sales_tax    = money_format('%i', $order_sales_tax);
        $order_total        = money_format('%i', $order_total);

        $data           = array();
        $data[]         = "Subtotal:{$this->colgap}|{$order_subtotal}";
        $data[]         = "Shipping & Handling:{$this->colgap}|{$order_ship_fees}";
        $data[]         = "Sales Tax:{$this->colgap}|{$order_sales_tax}";
        $data[]         = "<span class='c_highlite order_total'>TOTAL:</span>{$this->colgap}|<span class='c_highlite order_total'>{$order_total}</span>";
        $subtotal_info  = $this->MakeTable($data);
        unset($data);
        
        
        
        
        
        $output .= "
            <div style='float:right; padding-left:50px;'>
                {$subtotal_info}            
            </div>
            <div class='clear'></div>
            ";
        
        $output .= "</div>";
        
        
        
        
        return $output;
    }
    
    
    public function OutputAllOrders()
    {
        # GET ORDERS
        //$orders = $this->GetOrdersForCustomer($WHID);
        $orders = $this->GetFakeOrdersForCustomer();
        
        # FORMAT ORDERS
        $output = "
            <div class='order_table_order_wrapper'>
            <div class='order_table_row'>
                <div class='order_date order_table_header'>ORDER DATE</div>
                <div class='order_number order_table_header'>ORDER #</div>
                <div class='order_item order_table_header'>ITEMS IN ORDER</div>
                <div class='order_status order_table_header'>STATUS</div>
                <div class='order_actions order_table_header'>ACTIONS</div>
                <div class='clear'></div>
            </div>
            </div>
        ";
        
        foreach ($orders as $order) {
            
            $output        .= "<br /><div class='order_table_order_wrapper'>";
            $total_items    = count($order);
            $item_count     = 0;
            
            foreach ($order as $item) {
                
                $view_order_link = "{$this->page_location};type=single;order_number={$item['order_number_master']}";
                //$link_1     = "/office/dev_richard/class_execute;class=Profile_CancelAccountCustomer;classVars={$this->wh_id}";
                $view_order_onclick   = "top.parent.appformCreate('View Order #{$item['order_number_master']}', '{$view_order_link}', 'apps'); return false;";
                
                
                $actions    = "
                    <div><a href='#' onclick=\"{$view_order_onclick}\">View Order Details</a></div>
                    <div>Cancel Order</div>
                    <div><a href='#' onclick=\"top.parent.appformCreate('Contact Form', 'contact_form','apps'); return false;\">Contact Us</a></div>";
                $actions    = ($item['status_ship'] == 1) ? "<div>Track Shipment</div>{$actions}" : $actions;
                $status     = ($item['status_ship'] == 1) ? "<div style='font-weight:bold;'>SHIPPED</div><div>Ship Date: {$item['status_ship_date']}</div>" : "<div style='font-weight:bold;'>PROCESSING</div><div>Expected Delivery Date: {$item['status_ship_delivery_expected']}</div>";
                $date       = ($item_count == 0) ? $item['order_date'] : '&nbsp;';
                
                $output .= "
                <div class='order_table_row'>
                    <div class='order_date'>{$date}</div>
                    <div class='order_number'><a href='#' onclick=\"{$view_order_onclick}\">{$item['order_number']}</a></div>
                    <div class='order_item'>{$item['title']}</div>
                    <div class='order_status'>{$status}</div>
                    <div class='order_actions'>{$actions}</div>
                    <div class='clear'></div>
                </div>
                ";
                
                $item_count++;
                
                $output .= ($total_items > $item_count) ? "<br /><div class='order_table_row' style='border-bottom: 1px dashed #990000;'></div><br />" : '';
                
            } //end order_item
            
            $output .= "</div>";
            
        } //end order
        
        
        
        
        return $output;
    }
    
    
    public function GetOrdersForCustomer($WHID)
    {
        # 1. GET TRANSATIONS
        $transactions = $this->SQL->GetArrayAll(array(
            'table' => $this->Transaction_Table,
            'keys'  => '*',
            'where' => "active=1"
        ));
        
        # 2. GET ITEMS FOR EACH TRANSACTION
        $items = $this->SQL->GetArrayAll(array(
            'table' => $this->Transaction_Items_Table,
            'keys'  => '*',
            'where' => "active=1"
        ));
    }
    
    
    public function AddStyleSwap() 
    {
        AddSwap('@@highlite_color@@', $this->highlite_color);
        AddSwap('@@header_color@@', $this->header_color);
        AddSwap('@@background_color_secondary@@', $this->background_color_secondary);
        AddSwap('@@border_color@@', $this->border_color);
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
