<?php
// file: Store/YogaStoreOrder.php

class Store_YogaStoreOrder extends Store_StoreOrder
{
    public $Promo_Info      = '';

    public $Promo_Form = array(
        '@text|Promotional Code&nbsp;|code|Y|20|20||||&nbsp;',
        '@submit|Submit Code|PROMO_SUBMIT'
    );

    public function __construct()
    {
        global $SITECONFIG;
        
        $this->SetSQL();       

        $this->Ajax_Cart = true;
        
        $this->Product_Table = 'store_products';

        $this->Order_Table_Options = 'style="background-color:#939; border:2px solid #939; width:500px; font-family:Arial,Verdana,Helvetica,sans-serif;" cellspacing="1" align="center"';
        $this->Order_Th_Options    = 'align="right" style="background-color:#ffc; color:#000; padding:0.25em; width:200px"';

        $this->Bill_Table_Options  = 'style="background-color:#939; width:500px;" align="center"  border="0" cellpadding="0" cellspacing="1"';
        $this->Bill_Th_Options     = 'style="background-color:#b5b; color:#fff; padding:0.25em;"';


        $this->H2_Style = 'style="color:#939; margin:3px 3px; font-size:1.1em;"';
        
        //$this->Use_Currency_Conversion = true;
        //$this->Currency_Api_key = 'GET-KEY-FOR-THIS-SITE';
        //$this->Show_Country_State_On_Cart = true;

        $this->Cart_Itemize_Shipping = true;  //??????????? must determine how shipping is to occur

        // $this->Use_Sales_Tax = true;
        // $this->State_Sales_Tax_Array = array(
            // 'HI' =>  4.712
        // );

        // TRANSACTION form_variable => transaction_table field
        $this->Transaction_Fields['comments'] = 'comments';


        $this->Return_Name       = 'Yoga Store Order';
        $this->Return_Email      = 'order@xxxxxx.com';
        $this->Store_Recipients  = 'Yoga Store Order<xxx@xxxx.com>';

        
        $this->Text['EMAIL_BUYER_SUBJECT']  = 'Your Yoga Store Online Order';
        $this->Text['EMAIL_SELLER_SUBJECT'] = 'Yoga Online Order';



        $this->Product_Table_Field_Translations = array(
            'part_number' => 'part_number',
            'category' => 'category',
            'category2' => 'category2',
            'category3' => 'category2',
            'title' => 'title',
            'image' => 'picture1',
            'description' => 'description',
            'description_long' => 'description_long',
            'weight' => 'weight',
            'shipping' => 'shipping',
            'price' => 'price',
            'std_price' => 'price'
        );

        $this->Default_Return_Page = '/store';

        $this->Shipping_Options = array(
            // enter shipping options here
            //'STD' => 'Standard Shipping (5-10 days)',  
            //'2DAY' => '2-Day Shipping ($25 per Item)'
        );

        //$shipping_list = Form_AssocArrayToList($this->Shipping_Options);
        //$this->Shipping_Options_Form_Item = 'select|Shipping|shipping_option|N||N|' . $shipping_list;

        parent::__construct();

        // if ($this->Show_Shipping_Options) {
            // $this->ProcessShippingOption();
        // }

        //$this->Promo_Code = (!empty($_SESSION['PROMO']))? $_SESSION['PROMO']['code'] : '';
    }


    public function GetItemRecord($PN, $db_record='') // <------- EXTENDED
    {
        $record = parent::GetItemRecord($PN, $db_record);

        $PROMO = Session('PROMO');
        if ($PROMO and $record) {
            //$record['price'] = $PROMO['p' . $record['part_number']];
        }

        if ($this->Show_Shipping_Options) {
            // compute shipping here
        }


        return $record;
    }

    public function SetBuyerForm() // <------- EXTENDED
    {
        $this->Buyer_Form_Data = array_merge(
            array(
                //'h2|Shipping Information|' . $this->H2_Style
                'fieldset|Shipping Information|class="order_fieldset"||h2|'. $this->H2_Style,
            ),
            $this->Buyer_Form_Data,
            array(
                'endfieldset',
                'fieldset|Additional Information|class="order_fieldset"||h2|'. $this->H2_Style,
                // add additional fields here
                'textarea|Comments|comments|N|40|6|',
                'endfieldset',
                'submit|Next|GETCC|class="checkout_button"'
            )
        );
    }

    public function SetBillingForm() // <------- EXTENDED
    {
        $first_array = array(
            'fieldset|Billing Information|class="order_fieldset"||h2|'. $this->H2_Style,
            'select|Credit Card Type|CARD_TYPE|Y||' . Form_AssocArrayToList($this->Card_Types)
        );

        $this->Billing_Form_Data = array_merge(
            $first_array,
            $this->Billing_Form_Data,
            array(
                'endfieldset',
                'submit|Submit Order|ORDER|class="checkout_button"'
            )
        );
    }

    public function ProcessCreditCard() // <------- EXTENDED
    {
        //need to extend this function
        return true;
    }


    function OutputPromoForm()
    {
        //echo OutputForm($this->Promo_Form, Post('PROMO_SUBMIT'));
    }
    
    public function getFormAction()  //<<<<<<<<<<---------- REMOVE THIS FUNCTION - work around without SSL ----------<<<<<<<<<<
    {
        $result = parent::getFormAction();
        return str_replace('https', 'http', $result);        
    }

} // --------------------- END CLASS ----------------------