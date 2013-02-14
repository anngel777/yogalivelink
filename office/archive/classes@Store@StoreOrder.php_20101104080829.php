<?php
// ---------- Store Order System
// file: /Store/StoreOrder.php

class Store_StoreOrder
{

    // ----------- styles --------------
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

    //------- Email -------
    public $Return_Name       = 'xxx Order';
    public $Return_Email      = 'order@xxx.com';
    public $Store_Recipients  = '';
    public $Buyer_Name        = '';
    public $Buyer_Email       = '';
    public $Final_Table       = '';

    public $Buyer_Header_Template = '<div style="text-align:center; font-family:Arial,Verdana,Helvetica,sans-serif;">
<h1 style="text-align:center; color:#6AE;">@EMAIL_BUYER_SUBJECT@</h1>
<p style="font-weight:bold; font-size:1.1em; color:#080;">@EMAIL_SAVE_NOTICE@</p>
</div>';

    public $Seller_Header     = '';


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

    // ------ Billing Info ------
    public $Billing_Info = array();
    public $Order_Total  = 0;

    // ------ Merchant System -------
    public $Merchant_Id   = '';
    public $Merchant_Pin  = '';
    public $Merchant_Url  = '';
    public $Merchant_Page = '';

    // ------ Base Buyer Form ------
    public $Buyer_Form_Data = array(
        'text|First Name|first_name|Y|30|40',
        'text|Last Name|last_name|Y|30|40',
        'text|Company|company_name|N|30|40',
        'email|Email|email_address|Y|30|80',
        'text|Address 1|address1|Y|30|80',
        'text|Address 2|address2|N|30|80',
        'text|City|city|Y|30|80',
        'countrystate|Country|country_code:state|Y',
        'text|Postal Code|postal_code|Y|6|20|',
        'phone|Phone|phone|Y'
    );

    public $Prevous_Transaction_Input = array(
        '!fieldset|Retrieve Previous Order Information|id="previous_order_fieldset" class="order_fieldset"',
        '!@text|<b>Previous Order Id:&nbsp;</b>|PREVIOUS_ORDER_ID|N|20|20|',
        '!@submit|&raquo;|RETRIEVE_PREVIOUS|class="checkout_button"',
        '!endfieldset'
    );

    // ------ Transactions ------
    public $Transaction_Table = 'store_transactions';
    public $Transaction_Items_Table = 'store_transaction_items';

    public $Transaction_Fields = array(
        // form_variable => transaction_table field
        'order_id' => 'order_id',
        'first_name' => 'first_name',
        'last_name' => 'last_name',
        'company_name' => 'company_name',
        'email_address' => 'email_address',
        'address1' => 'address1',
        'city' => 'city',
        'state' => 'state',
        'country_code' => 'country_code',
        'postal_code' => 'postal_code',
        'phone' => 'phone',
        'CARD_TYPE' => 'card_type',
        'CARD_NUMBER' => 'card_ending',
        'CARD_NAME' => 'card_name',
        'CARD_ADDRESS1' => 'card_address1',
        'CARD_ADDRESS2' => 'card_address2',
        'CARD_CITY' => 'card_city',
        'CARD_STATE' => 'card_state',
        'CARD_COUNTRY' => 'card_country',
        'CARD_POSTAL_CODE' => 'card_postal_code',
        'CARD_PHONE' => 'card_phone',
        'BILL_TOTAL' => 'bill_total',
        'ITEMS_TOTAL' => 'items_total',
        'SHIPPING_TOTAL' => 'shipping_total',
        'TAX_TOTAL' => 'tax_total'
    );

    // ------ Base Billing Form ------
    public $Billing_Form_Data = array(
        'creditcard|Credit Card Number|CARD_NUMBER|Y',
        'text|Name on Credit Card|CARD_NAME|Y|30|40|',
        'datecc|Expiration Date|CARD_EXP|Y|H',
        'integer|Card Security Code|CARD_SECURITY_CODE|Y|4|4||H',
        'text|Address 1|CARD_ADDRESS1|Y|30|80|',
        'text|Address 2|CARD_ADDRESS2|N|30|80|',
        'text|City|CARD_CITY|Y|30|80',
        'countrystate|Country|CARD_COUNTRY:CARD_STATE|Y',
        'text|Postal Code|CARD_POSTAL_CODE|Y|5|20',
        'phone|Phone|CARD_PHONE|Y'
    );

    // ------ XML for CC Processing ------
  //<FIELD KEY="password">@@$MERCHANT_PASS@@</FIELD>
    public $Transaction_Xml =
'<?xml version="1.0" encoding="UTF-8"?>
<TRANSACTION>
 <FIELDS>
  <FIELD KEY="merchant">@@MERCHANT_ID@@</FIELD>
  <FIELD KEY="gateway_id">@@MERCHANT_PIN@@</FIELD>
  <FIELD KEY="operation_type">sale</FIELD>
  <FIELD KEY="order_id">@@ORDER_ID@@</FIELD>
  <FIELD KEY="total">@@TOTAL@@</FIELD>
  <FIELD KEY="card_name">@@CARD_TYPE@@</FIELD>
  <FIELD KEY="card_number">@@CARD_NUMBER@@</FIELD>
  <FIELD KEY="card_exp">@@CARD_EXP@@</FIELD>
  <FIELD KEY="owner_name">@@CARD_NAME@@</FIELD>
  <FIELD KEY="owner_street">@@CARD_ADDRESS@@</FIELD>
  <FIELD KEY="owner_city">@@CARD_CITY@@</FIELD>
  <FIELD KEY="owner_state">@@CARD_STATE@@</FIELD>
  <FIELD KEY="owner_zip">@@CARD_POSTAL_CODE@@</FIELD>
  <FIELD KEY="owner_country">@@CARD_COUNTRY@@</FIELD>
  <FIELD KEY="owner_phone">@@CARD_PHONE@@</FIELD>
  <FIELD KEY="recurring">0</FIELD>
  <FIELD KEY="recurring_type"></FIELD>
</FIELDS>
</TRANSACTION>';


    // ==================================== CONSTRUCT ====================================
    public function  __construct()
    {

        if (Post('ajaxdata')) {
            Form_AjaxToPost('ajaxdata');
            $this->Ajax_Cart = true;
        }

        if (Post('RESET')) {
            $this->ResetStoreOrder();
        }

        if (!isset($_SESSION['STORE_ORDER'])) {
            $_SESSION['STORE_ORDER'] = array();
        }
        if (empty($_SESSION['STORE_ORDER']['ORDER_ID'])) {
            $_SESSION['STORE_ORDER']['ORDER_ID'] = date('Ymd-His-') . rand(100,999);
        }

        if (isset($_SESSION['STORE_ORDER']['CART'])) {
            $this->Cart = $_SESSION['STORE_ORDER']['CART'];
        }

        $this->Order_Id = $_SESSION['STORE_ORDER']['ORDER_ID'];

        $this->SetSQL();

        if ($this->Use_Currency_Conversion) {
            $this->CC = Lib_Singleton::GetInstance('Store_ConvertCurrency');
            $this->CC->Api_Key = $this->Currency_Api_key;

            if (Post('CURRENCY')) {
                $this->CC->SetConversionSelection(Post('CURRENCY'));
            }
            $this->Currency_Code = $this->CC->GetConversionSelection();
            if ($this->Currency_Code != $this->CC->Default_Code) {
                $this->Conversion_Rate = $this->CC->GetConversion($this->CC->Default_Code, $this->Currency_Code);
                $this->Have_Conversion = ($this->Conversion_Rate > 0);
            }
        }

        if ($this->Show_Country_State_On_Cart) {
            if (GetPostItem('country')) {
                $error = '';
                $array = ProcessFormNT('countrystate|Country|country:state', $error);
                if (!$error) {
                    $_SESSION['STORE_ORDER']['LOCATION']['country'] = $array['country'];
                    $_SESSION['STORE_ORDER']['LOCATION']['state']   = $array['state'];
                }
            } else {
                if (empty($_SESSION['STORE_ORDER']['LOCATION'])) {
                    $record = Lib_IpLocation::GetLocationRecord();
                    $_SESSION['STORE_ORDER']['LOCATION']['country'] = $record['country_code'];
                    $_SESSION['STORE_ORDER']['LOCATION']['state']   = Form_GetStateCodeFromName($record['state_region']);
                }
            }
            $this->Location_Record = $_SESSION['STORE_ORDER']['LOCATION'];
        }
    }


    public function getFormAction()
    {

        $HTTP_HOST   = Server('HTTP_HOST');
        $REQUEST_URI = Server('REQUEST_URI');
        if (empty($REQUEST_URI)) {
            $REQUEST_URI = Server('SCRIPT_NAME');
        }
        $REQUEST_URI = str_replace('/AJAX', '', preg_replace('/(;|\?|:).+$/', '', $REQUEST_URI));
        $HTTPS_URI   = 'https://'.$HTTP_HOST . $REQUEST_URI;

        return $HTTPS_URI;
    }

    // ==================================== OUTPUT ORDER PAGE ====================================
    public function ProcessOrderPage()
    {
        if (Post('RETURN')) {
            header('Location: ' . $this->GetReturnPage());
        }

        WriteFormStart($this->getFormAction(), 'post', 'ORDERFORM');

        if ($this->Use_Cart) {
            $this->ProcessCart();
            if (empty($this->Cart)) {
                echo $this->GetEmptyCart();
                WriteFormEnd();
                return;
            }
        }

        if (Post('RETRIEVE_PREVIOUS')) {
            $order_id = GetPostItem('PREVIOUS_ORDER_ID');
            $order_id = preg_replace('/[^0-9\-]/', '', $order_id);
            if ($order_id) {
                $this->RetrieveFormerTransactionInfo($order_id);
            }
            $_POST = array();
        }

        $this->SetBuyerForm();
        $this->SetBillingForm();

        if (Post('GETCC')) {
            //$array = ProcessFormNT($this->Buyer_Form_Data, $this->Error);
            Form_SetShowPosted(false);
            $array = ProcessForm($this->Buyer_Form_Data, $table,
                $this->Order_Table_Options, $this->Order_Th_Options, $this->Order_Td_Options, $this->Error);
            AddError($this->Error);
            // ----- put array into into session
            $_SESSION['STORE_ORDER']['BUYER_INFO'] = $array;
            $_SESSION['STORE_ORDER']['BUYER_INFO_TABLE'] = $table;
        }

        if ((Post('GETCC') and !$this->Error) or (Post('ORDER'))){
            // ----- get the billing information ----
            if (Post('ORDER')) {
                $array = ProcessFormNT($this->Billing_Form_Data, $this->Error);
                AddError($this->Error);
                $_SESSION['STORE_ORDER']['BILLING_INFO'] = $array;
                if (!$this->Error) {
                    // -- got  successful billing info
                    $this->Billing_Info = $array;
                    if ($this->FinalOrderProcess()) {
                        WriteFormEnd();
                        return;
                    } else {
                        AddError($this->Error);
                    }
                }
            }

            if (!isset($_SESSION['STORE_ORDER']['BILLING_INFO'])) {
                $this->PreloadBillingData();
            }
            Form_PostArray($_SESSION['STORE_ORDER']['BILLING_INFO']);
            echo $this->GetBillingInfo();
        }


        if ((Post('CHECKOUT') or Get('CHECKOUT') or ($this->Error and Post('GETCC'))) or (!$this->Use_Cart and empty($_POST))) {
            if (isset($_SESSION['STORE_ORDER']['BUYER_INFO'])) {
                Form_PostArray($_SESSION['STORE_ORDER']['BUYER_INFO']);
            }
            echo $this->GetBuyerInfo();
        } elseif (!Post('GETCC') and !Post('ORDER') and $this->Use_Cart) {
            echo $this->GetShoppingCart();
        }
        WriteFormEnd();
    }

    public function ResetStoreOrder()
    {
        $_SESSION['STORE_ORDER'] = array();
        $_POST = array();
        AddFlash($this->Text['CART_RESET_FLASH_NOTICE']);
    }

    public function GetEmptyCart()
    {
        $RESULT = "<h2>{$this->Text['CART_EMPTY_NOTICE']}</h2>\n<p align=\"center\">";

        if ($this->Ajax_Cart) {
            $RESULT .= '<input type="button" onclick="closeShoppingCart();" value="' . $this->Text['CART_BUTTON_CONTINUE_SHOPPING']
                . '" name="RETURN" class="return_button" />';
        } else {
            $RESULT .= '<input type="submit" value="' . $this->Text['CART_BUTTON_CONTINUE_SHOPPING']
                . '" name="RETURN" class="return_button" />';
        }
        $RESULT .= "\n</p>\n";
        return $RESULT;
    }

    // ==================================== FINALIZE ORDER ====================================
    public function GetSuccessfulOrderMessage()
    {
        return '<h3>' . $this->Text['SUCCESSFUL_ORDER_MESSAGE_H3'] . '</h3>' .
         '<p>' . $this->Text['SUCCESSFUL_ORDER_MESSAGE_P1'] . '</p>' .
         '<p>' . $this->Text['SUCCESSFUL_ORDER_MESSAGE_P2'] . '</p>';
    }

    public function FinalOrderProcess()
    {
        // process credit card

        if ($this->ProcessCreditCard()) {
            // if OK, email result
            $this->GetFinalTable();

            // $this->Send_Emails = false; //<<<<<<<<<<---------- REMOVE ----------<<<<<<<<<<
            // $this->Clear_Cart_At_End = false; //<<<<<<<<<<---------- REMOVE ----------<<<<<<<<<<

            if ($this->Send_Emails) {
                $this->SendEmails();
            }

            // output Message
            echo $this->GetSuccessfulOrderMessage();
            echo $this->Final_Table;

            // update transaction tables
            $this->AddTransaction();

            // clear cart, order_id
            if ($this->Clear_Cart_At_End) {
                $_SESSION['STORE_ORDER']['CART'] = '';
                $_SESSION['STORE_ORDER']['ORDER_ID'] = '';
            }
            return true;
        } else {
            return false;
        }
    }


    public function GetFinalTable()
    {
        Form_SetShowPosted(true);

        Form_PostArray($_SESSION['STORE_ORDER']['BUYER_INFO']);
        Form_PostArray($_SESSION['STORE_ORDER']['BILLING_INFO']);

        $cart = ($this->Use_Cart)? array(
                'h2|' . $this->Text['FORM_BILLING_ITEMS'] . '|' . $this->H2_Style,
                'cell|' . $this->GetShoppingCart(false)
                ) : array();

        $form_array = array_merge(
            array(
                'info|' . $this->Text['FORM_ORDER_ID'] . '|' . $this->Order_Id
            ),
            $this->Buyer_Form_Data,
            $cart,
            $this->Billing_Form_Data
        );

        $this->Final_Array = ProcessForm($form_array, $table,
            $this->Order_Table_Options, $this->Order_Th_Options, $this->Order_Td_Options,
            $this->Error);
        if (!$this->Error) {
            $this->Final_Table = $table;
        } else {
            $this->Final_Table = '';
        }
    }


    // ==================================== RETRIEVE INFO FROM TRANSACTION RECORD ====================================

    public function RetrieveFormerTransactionInfoSuccess()
    {
        AddFlash('Previous Information Retrieved!');
    }

    public function RetrieveFormerTransactionInfoFailure()
    {
        AddFlash('Previous Information Retrieval Failed!');
    }

    public function RetrieveFormerTransactionInfo($order_id)
    {
        $order_id = $this->SQL->QuoteValue($order_id);
        $record = $this->SQL->GetRecord(array(
            'table' => $this->Transaction_Table,
            'keys'  => '*',
            'where' => "order_id=$order_id"
        ));

        if ($record) {
            foreach ($record as $key => $value) {
                if (strpos($key, 'CARD_') !== false) {
                    $_SESSION['STORE_ORDER']['BILLING_INFO'][$key] = $value;
                } elseif (strpos($key, '_TOTAL') === false) {
                    $_SESSION['STORE_ORDER']['BUYER_INFO'][$key] = $value;
                }
            }
            $this->RetrieveFormerTransactionInfoSuccess();
        } else {
            $this->RetrieveFormerTransactionInfoFailure();
        }
    }

    // ==================================== ADD TRANSACTION RECORD ====================================
    public function AddTransaction()
    {

        // public $Transaction_Table = 'store_transactions';
        // public $Transaction_Items_Table = 'store_transaction_items';
        // public $Transaction_Fields = array()
        //have $this->Cart
        //have $this->Order_Id
        $STORE_ORDER = Session('STORE_ORDER');

        $this->Final_Array['order_id']       = $this->Order_Id;
        $this->Final_Array['CARD_NUMBER']    = substr($this->Final_Array['CARD_NUMBER'], -4);
        $this->Final_Array['BILL_TOTAL']     = ArrayValue($STORE_ORDER, 'BILL_TOTAL');
        $this->Final_Array['ITEMS_TOTAL']    = ArrayValue($STORE_ORDER, 'ITEMS_TOTAL');
        $this->Final_Array['SHIPPING_TOTAL'] = ArrayValue($STORE_ORDER, 'SHIPPING_TOTAL');
        $this->Final_Array['TAX_TOTAL']      = ArrayValue($STORE_ORDER, 'TAX_TOTAL');

        $array = array();
        foreach ($this->Final_Array as $key => $value) {
            $field = ArrayValue($this->Transaction_Fields, $key);
            if ($field and $value) {
                $array[$field] = $value;
            }
        }
        $this->SQL->StartTransaction();

        $this->SQL->AddRecord(array(
            'table'  => $this->Transaction_Table,
            'keys'   => $this->SQL->Keys($array) . ',created',
            'values' => $this->SQL->Values($array) . ',NOW()'
        ));

        $insert_id = $this->SQL->GetLastInsertId();

        foreach ($this->Cart as $PN => $QTY) {
            $array = array(
                'store_transactions_id' => $insert_id,
                'order_id' => $this->Order_Id,
                'item_number' => $PN,
                'quantity' => $QTY,
                'created' => 'NOW()'
            );
            $this->SQL->AddRecord(array(
                'table'  => $this->Transaction_Items_Table,
                'keys'   => $this->SQL->Keys($array),
                'values' => $this->SQL->Values($array)
            ));
        }

        $this->SQL->TransactionCommit();
        return;
    }

    // ==================================== EMAILS ====================================

    public function GetBuyerHeader()
    {
        return str_replace(
            array('@EMAIL_BUYER_SUBJECT@', '@EMAIL_SAVE_NOTICE@'),
            array($this->Text['EMAIL_BUYER_SUBJECT'], $this->Text['EMAIL_SAVE_NOTICE']),
            $this->Buyer_Header_Template
        );
    }

    public function GetBuyerEmailHeader()
    {
        return $this->GetBuyerHeader();
    }

    public function GetSellerEmailHeader()
    {
        return $this->Seller_Header;
    }

    public function GetBuyerEmailContent()
    {
        return $this->GetBuyerEmailHeader() . $this->Final_Table;
    }

    public function GetSellerEmailContent()
    {
        return $this->GetSellerEmailHeader() . $this->Final_Table;
    }

    public function SendEmail($from_name, $from_email, $recipient_list, $subject, $content, $cc='', $bcc='')
    {
        return SendHtmlMail($from_name, $from_email, $recipient_list, $subject, $content, $cc, $bcc);
    }

    public function SendEmailBuyer()
    {
        $content = $this->GetBuyerEmailContent();
        $to_email = $this->Buyer_Name . '<' . $this->Buyer_Email . '>';
        $RESULT = $this->SendEmail($this->Return_Name, $this->Return_Email, $to_email, $this->Text['EMAIL_BUYER_SUBJECT'], $content);
        if (!$RESULT) {
            AddError($this->Text['ERROR_EMAIL_TO_CUSTOMER_FAILED']);
        }
        return $RESULT;
    }

    public function SendEmailSeller()
    {
        $content = $this->GetSellerEmailContent();
        $to_email = $this->Return_Name . '<' . $this->Buyer_Email . '>';
        $subject = $this->Text['EMAIL_SELLER_SUBJECT'] . ' - ' . $this->Buyer_Name;
        return $this->SendEmail($this->Buyer_Name, $this->Buyer_Email, $this->Store_Recipients, $subject, $content);
        if (!$RESULT) {
            AddError($this->Text['ERROR_EMAIL_TO_COMPANY_FAILED']);
        }
        return $RESULT;
    }


    public function SendEmails()
    {
        $buyer = $_SESSION['STORE_ORDER']['BUYER_INFO'];
        $this->Buyer_Name = trim($buyer['first_name'] . ' ' . $buyer['last_name']);
        $this->Buyer_Email = $buyer['email_address'];

        $this->SendEmailBuyer();
        $this->SendEmailSeller();
    }


    // ==================================== SYSTEM ====================================

    public function SetSQL()
    {
        if (empty($this->SQL)) {
            $this->SQL = Lib_Singleton::GetInstance('Lib_Pdo');
        }
    }

    public function ClearOrderId()
    {
        $this->Order_Id = $_SESSION['STORE_ORDER']['ORDER_ID'] = '';
    }

    public function SetReturnPage($page)
    {
        $_SESSION['STORE_ORDER']['RETURN_PAGE'] = $page;
    }

    public function GetReturnPage()
    {
        $RESULT = ArrayValue($_SESSION['STORE_ORDER'], 'RETURN_PAGE');
        return (empty($return))? $this->Default_Return_Page : $RESULT;
    }


    // ==================================== SHOPPING CART ====================================

    public function UpdateSessionCart()
    {
        $_SESSION['STORE_ORDER']['CART'] = $this->Cart;
    }

    public function AddToCart($PN, $QTY=1)
    {
        $record = $this->GetItemRecord($PN); // check for valid pn
        if ($record) {
            if (empty($this->Cart[$PN])) {
                $this->Cart[$PN] = $QTY;
            } else {
                $this->Cart[$PN] += $QTY;
            }
            $this->UpdateSessionCart();
        }
    }

    public function PartNumberToId($PN)
    {
        return preg_replace('/[^0-9a-zA-Z_]/', '_', $PN);
    }


    public function UpdateCart()
    {
        foreach ($this->Cart as $PN => $QTY) {
            $pn_id = $this->PartNumberToId($PN);
            if (Post("REMOVE_$pn_id")) {
                unset($this->Cart[$PN]);
            } else {
                $new_qty = intOnly(Post("QTY_$pn_id"));
                if ($new_qty) {
                    $this->Cart[$PN] = $new_qty;
                }
            }
        }
        $this->UpdateSessionCart();
    }

    public function ClearCart()
    {
        $this->Cart = array();
        $this->UpdateSessionCart();
    }

    public function CleanPartNumber($PN)
    {
        return preg_replace('/[^0-9a-zA-Z\-]/', '', $PN);
    }

    public function ProcessCart()
    {
        $PN = $this->CleanPartNumber(Get('PN'));

        if ($PN) {
            $this->AddToCart($PN);

        } elseif (Post('UPDATECART')) {
            $this->UpdateCart();

        } elseif (Post('CLEARALL')) {
            $this->ClearCart();
        }
    }

    public function AddCartEndNote($note)
    {
        if (!empty($note)) {
            if (!empty($this->Cart_End_Notes)) {
                $this->Cart_End_Notes .= '<br />';
            }
            $this->Cart_End_Notes .= $note;
        }
    }

    public function GetShoppingCart($shopping=true)
    {
        $RESULT = ($shopping)? "\n\n<!-- ============= SHOPPING CART ============= -->\n\n" : '';

        $ITEMS_TOTAL = 0;
        $SHIPPING_TOTAL = 0;
        $TAX_TOTAL = 0;

        if (!empty($this->Cart)) {

            if ($shopping) {
                $RESULT .= '<h2 id="cart_heading">' . $this->Text['CART_TITLE'] . '</h2>';
                $table_options = $this->Cart_Table_Options;
                $td_options = empty($this->Cart_Td_Options)? '' : ' ' . $this->Cart_Td_Options;
                $th_options = empty($this->Cart_Th_Options)? '' : ' ' . $this->Cart_Th_Options;
                $total_cell = $this->Cart_Table_Total;
                $RESULT .= $this->GetLocationCurrencyForm();
                $span = $this->Cart_Table_Span;
                $note_options = $this->Cart_Table_Note;

            } else  {
                $table_options = $this->Bill_Table_Options;
                $td_options = empty($this->Bill_Td_Options)? '' : ' ' . $this->Bill_Td_Options;
                $th_options = empty($this->Bill_Th_Options)? '' : ' ' . $this->Bill_Th_Options;
                $total_cell = $this->Bill_Table_Total;
                $span = $this->Bill_Table_Span;
                $note_options = $this->Bill_Table_Note;
            }

            $RESULT .= ($shopping)? "\n\n<!-- ============= SHOPPING CART TABLE ============= -->\n\n" : '';
            $RESULT .=  "<table $table_options>\n<tbody>\n<tr align=\"center\">\n  ";

            if ($shopping) {
                $RESULT .= "<th$th_options>{$this->Text['CART_HEADING_REMOVE']}</th><th$th_options>{$this->Text['CART_HEADING_NEW_QTY']}</th>";
            }

            $shipping_heading = $this->Cart_Itemize_Shipping? "<th$th_options>{$this->Text['CART_HEADING_SHIPPING']}</th>" : '';

            $RESULT .= "<th$th_options>{$this->Text['CART_HEADING_ITEM']}</th><th$th_options>{$this->Text['CART_HEADING_TITLE']}</th><th$th_options>{$this->Text['CART_HEADING_QTY']}</th><th$th_options>{$this->Text['CART_HEADING_PRICE']}</th>$shipping_heading<th$th_options>{$this->Text['CART_HEADING_TOTAL']}</th>\n</tr>\n";

            $total  = 0;
            $total_weight = 0;
            $count  = -1;
            $weight = 0;



            foreach ($this->Cart as $PN => $QTY) {
                $missing_shipping = false;
                $record = $this->GetItemRecord($PN);
                $total_weight += $record['weight'];

                $subtotal = $record['price'] * $QTY;
                if ($record['shipping'] == -1) {
                    $subshipping = 0;
                    $shippingtext = "<span $span>{$this->Text['CART_TBD']}</span>";
                    $this->Cart_End_Notes = $this->Text['CART_TBD_NOTE'];
                    $missing_shipping = true;
                } else {
                    $subshipping = $record['shipping'] * $QTY;
                    $shippingtext = '$' . number_format($subshipping, 2);
                }

                $total += $subtotal + $subshipping;
                $ITEMS_TOTAL += $subtotal;
                $SHIPPING_TOTAL += $subshipping;

                $pricetext    = '$' . number_format($record['price'], 2);
                $subtotaltext = '$' . number_format($subtotal + $subshipping, 2);

                if ($this->Have_Conversion) {
                    $cov_pricetext    = "&nbsp;USD<br /><span $span>"
                        . number_format($this->Conversion_Rate * $record['price'], 2)
                        . '&nbsp;' . $this->Currency_Code . '</span>';
                    if ($missing_shipping) {
                        $cov_shippingtext = '';
                    } else  {
                        $cov_shippingtext = "&nbsp;USD<br /><span $span>"
                            . number_format($this->Conversion_Rate * $subshipping, 2)
                            . '&nbsp;' . $this->Currency_Code . '</span>';
                    }
                    $cov_subtotaltext = "&nbsp;USD<br /><span $span>"
                        . number_format($this->Conversion_Rate * ($subtotal + $subshipping), 2)
                        . '&nbsp;' . $this->Currency_Code . '</span>';
                } else {
                    $cov_pricetext    = '';
                    $cov_shippingtext = '';
                    $cov_subtotaltext = '';
                }


                $RESULT .= "<tr>\n  ";

                $pn_id = $this->PartNumberToId($PN);

                if ($shopping) {
                    $RESULT .= qqn("<td class=`cart_input` align=`center`><input type=`checkbox` name=`REMOVE_$pn_id` value=`1` /></td>");
                    $RESULT .= qqn("<td class=`cart_input` align=`center`><input type=`text` size=`3` maxlength=`4` name=`QTY_$pn_id` /></td>");
                }
                $RESULT .= "<td$td_options>$PN</td>";

                $RESULT .= "<td$td_options>{$record['title']}</td><td$td_options align=\"center\">$QTY</td><td$td_options align=\"right\">$pricetext$cov_pricetext</td>";

                if ($this->Cart_Itemize_Shipping) {
                    $RESULT .= "<td$td_options align=\"right\">$shippingtext$cov_shippingtext</td>";
                }
                $RESULT .= "<td$td_options align=\"right\">$subtotaltext$cov_subtotaltext</td>\n</tr>\n";
            }




            $base_columns = $this->Cart_Itemize_Shipping? 5 : 4;
            $colspan = ($shopping) ? $base_columns + 2 : $base_columns;

            $conversion_text = '';
            if ($this->Have_Conversion) {
                $conversion_text = "<br /><span $span>{$this->Text['CART_APPROXIMATE_CONVERSION_TO']} " . $this->CC->Currency_Array[$this->Currency_Code] . '</span>';

            }

            $tax = 0;
            if ($this->Use_Sales_Tax and !empty($this->Location_Record)) {
                $state = $this->Location_Record['state'];
                $tax_rate = ArrayValue($this->State_Sales_Tax_Array, $state);
                if ($tax_rate) {
                    $tax = $tax_rate * $ITEMS_TOTAL/100;
                    $tax_text = number_format($tax, 2);
                    $total += $tax;
                    $RESULT .= "<tr>\n  <th$th_options colspan=\"$colspan\" align=\"left\">$state {$this->Text['CART_SALES_TAX']} ($tax_rate%)</th><td$td_options align=\"right\">\$$tax_text</td>\n</tr>\n";
                }
            }

            // ------ Get shipping -----
            if ($this->Calculate_Shipping) {
                $shipping = $this->CalculateShipping($total, $total_weight);
                if ($shipping) {
                    $RESULT .= "<tr>\n  <th$th_options colspan=\"$colspan\" align=\"left\">{$this->Text['CART_SHIPPING_AND_HANDLING']}</th><td$td_options align=\"right\">\$$shipping</td>\n</tr>\n";
                    $total += $shipping;
                }
            } else {
                $shipping = 0;
            }

            $totaltext = number_format($total, 2);

            $_SESSION['STORE_ORDER']['BILL_TOTAL'] = $total;
            $_SESSION['STORE_ORDER']['ITEMS_TOTAL'] = $ITEMS_TOTAL;
            $_SESSION['STORE_ORDER']['SHIPPING_TOTAL'] = $SHIPPING_TOTAL;
            $_SESSION['STORE_ORDER']['TAX_TOTAL'] = $tax;

            if ($this->Have_Conversion) {
                $totaltext .= "&nbsp;USD<br /><span $span>" . number_format($this->Conversion_Rate * $total, 2) . '&nbsp;' . $this->Currency_Code . '</span>';
            }

            if ($this->Show_Shipping_Options) {
                $this->AddCartEndNote(ArrayValue($this->Shipping_Options, $this->Shipping_Option));
            }

            if ($this->Promo_Code) {
                $this->AddCartEndNote($this->Text['CART_PROMO_CODE_NOTE'] . ' = ' . $this->Promo_Code);
            }

            $RESULT .= "<tr>\n  <th$th_options colspan=\"$colspan\" align=\"left\">{$this->Text['CART_TOTAL']}$conversion_text</th><td $total_cell align=\"right\">\$$totaltext</td>\n</tr>\n";

            if ($this->Cart_End_Notes) {
                $colspan++;
                $RESULT .= "<tr>\n  <td $this->End_Note_Td_Options colspan=\"$colspan\">$this->Cart_End_Notes</td>\n</tr>\n";
            }

            $RESULT .= "</tbody>\n</table>\n";


            if ($shopping) {
                $RESULT .= $this->GetCartButtons($total);
                $RESULT .= "\n<!-- ============= END CART ============= -->\n\n";
            }
        }
        return $RESULT;
    }

    public function GetCartButtons($total)
    {
        $RESULT = "\n<div id=\"cartbuttons\">\n";
        if (!$this->Ajax_Cart) {
            if ($total > 0) {
                $RESULT .= '<input type="submit" value="' 
                . $this->Text['CART_BUTTON_UPDATE_CART'] . '" name="UPDATECART" class="submit_button" />'
                . '&nbsp;&nbsp;<input type="submit" value="' 
                . $this->Text['CART_BUTTON_CLEAR_ALL'] . '" name="CLEARALL" class="submit_button" />'
                . '&nbsp;&nbsp;<input type="submit" value="' 
                . $this->Text['CART_BUTTON_CHECKOUT'] . '" name="CHECKOUT" class="checkout_button" />';
            }
            $RESULT .= "\n<p class=\"center\">\n";
            $RESULT .= '<input type="submit" value="' 
                . $this->Text['CART_BUTTON_CONTINUE_SHOPPING'] . '" name="RETURN" class="return_button" />';
            $RESULT .= "\n</p>\n";
        } else {
            if ($total > 0) {
                $RESULT .= '<input type="button" onclick="updateShoppingCart();" value="' 
                    . $this->Text['CART_BUTTON_UPDATE_CART'] . '" name="UPDATECART" class="submit_button" />'
                    . '&nbsp;&nbsp;<input type="button" onclick="clearShoppingCart();" value="' 
                    . $this->Text['CART_BUTTON_CLEAR_ALL'] . '" name="CLEARALL" class="submit_button" />'
                    . '&nbsp;&nbsp;<input type="submit" value="' 
                    . $this->Text['CART_BUTTON_CHECKOUT'] . '" name="CHECKOUT" class="checkout_button" />';
            }
            $RESULT .= "\n<p class=\"center\">\n";
            $RESULT .= '<input type="button" onclick="closeShoppingCart();" value="' 
                . $this->Text['CART_BUTTON_CONTINUE_SHOPPING'] . '" name="RETURN" class="return_button" />';
            $RESULT .= "\n</p>\n";
        }
        $RESULT .= "</div>\n";
        return $RESULT;
    }

    public function CalculateShipping($total, $weight) // must extend this function
    {
        return 0;
    }

    public function GetBackButton($name = "BACK")
    {
        return "\n<p><input class=\"return_button\" type=\"submit\" value=\"&larr; {$this->Text['CART_BUTTON_BACK']}\" name=\"$name\" /></p>\n";
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

    // ==================================== Shipping ====================================

    public function ProcessShippingOption()
    {
        $RESULT = '';
        if ($this->Show_Shipping_Options) {
            if (GetPostItem($this->Shipping_Item_Field)) {
                $error = '';
                $array = ProcessFormNT($this->Shipping_Options_Form_Item, $error);
                if (!$error) {
                    $RESULT =  $array[$this->Shipping_Item_Field];
                    $_SESSION['STORE_ORDER']['SHIPPING_OPTION'] = $RESULT;
                }
            } else {
                $RESULT = ArrayValue($_SESSION['STORE_ORDER'], 'SHIPPING_OPTION');
                Form_PostValue($this->Shipping_Item_Field, $RESULT);
            }
        }
        $this->Shipping_Option = $RESULT;
    }

    public function GetShippingOptions()
    {
        // function returns a form array field
        if ($this->Show_Shipping_Options) {
            return $this->Shipping_Options_Form_Item;
        } else {
            return '';
        }
    }
    // ==================================== CURRENCY & LOCATION ====================================
    public function GetLocationCurrencyForm($update_button = false)
    {
        $RESULT = '';
        if ($this->Show_Country_State_On_Cart or $this->Use_Currency_Conversion) {
            $RESULT .= "\n<div id=\"cart_location_currency\">\n";

            $form_data = array();
            if ($this->Show_Country_State_On_Cart) {
                $form_data[] = 'countrystate|' . $this->Text['FORM_COUNTRY'] . '|country:state|N';
                Form_PostArray($this->Location_Record);
            }

            if ($this->Use_Currency_Conversion) {
                $form_data[] = 'info|' 
                . $this->Text['FORM_CURRENCY'] . '|'. $this->CC->GetCurrencySelect('class="formitem" name="CURRENCY"');
            }

            $shipping = $this->GetShippingOptions();
            if ($shipping) {
                $form_data[] = $shipping;
            }

            if ($update_button) {
                $form_data[] = 'submit|' . $this->Text['CART_BUTTON_UPDATE_CART'] . '|UPDATE|class="submit_button"';
            }

            $RESULT .= OutputForm($form_data);
            $RESULT .= "\n</div>\n";
        }
        return $RESULT;

    }


    // ==================================== BUYER INFORMATION ====================================
    public function SetBuyerForm()
    {
        array_unshift($this->Buyer_Form_Data, 'h2|Your Information|' . $this->H2_Style);
    }

    public function GetBuyerInfo()
    {
        $RESULT = ($this->Use_Cart)? $this->GetBackButton('VIEWCART') : '';
        $RESULT .= OutputForm($this->Buyer_Form_Data, Post('GETCC'));
        return $RESULT;
    }


    // ==================================== BILLING INFORMATION ====================================
    public function PreloadBillingData()
    {
        $array = $_SESSION['STORE_ORDER']['BUYER_INFO'];
        $conversion_array = array(
            'CARD_NAME'       => trim($array['first_name'] . ' ' . $array['last_name']),
            'CARD_ADDRESS1'   => $array['address1'],
            'CARD_ADDRESS2'   => $array['address2'],
            'CARD_CITY'       => $array['city'],
            'CARD_COUNTRY'    => $array['country_code'],
            'CARD_STATE'      => $array['state'],
            'CARD_POSTAL_CODE'=> $array['postal_code'],
            'CARD_PHONE'      => $array['phone']
        );
        $_SESSION['STORE_ORDER']['BILLING_INFO'] = $conversion_array;
    }

    public function GetBillingInfo()
    {
        $RESULT = $this->GetBackButton('CHECKOUT');
        $RESULT .= "\n<fieldset class=\"order_fieldset\">\n<legend>{$this->Text['FORM_YOUR_ORDER_INFORMATION']}</legend>\n";
        $RESULT .= $_SESSION['STORE_ORDER']['BUYER_INFO_TABLE'];
        $RESULT .= '<br />';
        $RESULT .= $this->GetShoppingCart(false);
        $RESULT .= "\n</fieldset>\n";
        $RESULT .= OutputForm($this->Billing_Form_Data, Post('ORDER'));
        return $RESULT;
    }


    public function SetBillingForm()
    {
        $first_array = array(
            'h2|' . $this->Text['FORM_BILLING_INFORMATION'] . '|' . $this->H2_Style,
            'select|' . $this->Text['FORM_CREDIT_CARD_TYPE'] . '|CARD_TYPE|Y||' . Form_AssocArrayToList($this->Card_Types)
        );

        $this->Billing_Form_Data = array_merge(
            $first_array,
            $this->Billing_Form_Data,
            array('submit|' . $this->Text['FORM_SUBMIT_ORDER'] . '|ORDER|class="checkout_button"')
        );
    }


    // ==================================== CREDIT CARD ====================================
    public function ProcessCreditCard() // ------ must extend this function
    {
        // card processing
        // have $this->Billing_Info;
        $xml = $this->Transaction_Xml;

        $this->Billing_Info['CARD_ADDRESS'] = $this->Billing_Info['CARD_ADDRESS1'] . ', ' . $this->Billing_Info['CARD_ADDRESS2'];

        foreach ($this->Billing_Info as $key => $value) {
            $xml = str_replace('@@' . $key . '@@', $value, $xml);
        }
        $xml = str_replace('@@ORDER_ID@@', $this->Order_Id, $xml);
        $xml = str_replace('@@MERCHANT_ID@@', $this->Merchant_Id, $xml);
        $xml = str_replace('@@MERCHANT_PIN@@', $this->Merchant_Pin, $xml);

        $amount = $_SESSION['STORE_ORDER']['BILL_TOTAL'];
        $amount = number_format($amount, 2, '.', '');  // no commas

        $xml = str_replace('@@TOTAL@@', $amount, $xml);

        if ($this->Billing_Info['CARD_NUMBER'] == '4111111111111111') {
            $result_status = 1;

        } else {
            $data = SendReceiveXML($this->Merchant_Url, $this->Merchant_Page, $xml);
            $outdata = str_replace('</FIELD>',"</FIELD>\n  ",$data);
            echo "\n\n<!-- $outdata -->\n\n";

            //=============== generate response =================

            $result_status    = TextBetween('status">','</FIELD>',$data);            // 0-error  1-success  2-declined
            $result_response  = TextBetween('auth_response">','</FIELD>',$data);     // message from the bank
            $result_error     = TextBetween('error">','</FIELD>',$data);             // error message
            $result_reference = TextBetween('reference_number">','</FIELD>',$data);  // reference for use in credits/voids/settles
        }

        if ($result_status == 0) {
            $this->Error = $this->Text['ERROR_CARD_DID_NOT_PROCESS'] . "<br />[$result_error]";
            return false;
        } elseif ($result_status == 1) {
            return true;
        } elseif ($result_status == 2) {
            $this->Error = $this->Text['ERROR_CARD_DECLINED'];
            return false;
        } else {
            $this->Error = $this->Text['ERROR_CARD_PROCESS_ERROR'];
            return false;
        }
    }


}
