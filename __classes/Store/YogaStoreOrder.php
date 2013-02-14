<?php
// file: Store/YogaStoreOrder.php

class Store_YogaStoreOrder extends Store_StoreOrder
{
    public $Promo_Info      = '';

    public $Options_Array   = array();


    public $Step_Array = array(
        1 => 'Your Information',
        2 => 'Billing Information'
    );


    public $Promo_Form = array(
        '@text|Promotional Code&nbsp;|code|Y|20|20||||&nbsp;',
        '@submit|Submit Code|PROMO_SUBMIT'
    );

    public function __construct()
    {
        global $SITECONFIG;
        
        // =========== AUTHORIZE.NET SETUP ============
        $this->Authorize_Net_Login_Id  = 'YogaLiveLink2010';
        $this->Authorize_Net_Trans_Key = 'Hauptsegel2010';
        $this->Authorize_Net_Testing = false;  //<<<<<<<<<<---------- REMOVE ----------<<<<<<<<<<
        

        $this->SetSQL();

        $this->Ajax_Cart = true;

        $this->Product_Table = 'store_products';

        $this->Order_Table_Options = 'style="background-color:#9E9D41; border:2px solid #9E9D41; width:500px; font-family:Arial,Verdana,Helvetica,sans-serif;" cellspacing="1" align="center"';
        $this->Order_Th_Options    = 'align="right" style="background-color:#9E9D41; color:#000; padding:0.25em; width:200px"';

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
        $this->Return_Email      = 'order@mailwh.com';  //<<<<<<<<<<---------- CHANGE or use site variable ----------<<<<<<<<<<
        #$this->Store_Recipients  = 'Yoga Store Order<michael@mailwh.com>,Yoga Store Order<richard@mailwh.com>'; //<<<<<<<<<<---------- CHANGE or use site variable ----------<<<<<<<<<<
        #$this->Store_Recipients  = 'Yoga Store Order<michael@mailwh.com>'; //<<<<<<<<<<---------- CHANGE or use site variable ----------<<<<<<<<<<


        $this->Text['EMAIL_BUYER_SUBJECT']  = 'Your Yoga Store Online Order';
        $this->Text['EMAIL_SELLER_SUBJECT'] = 'Yoga Online Order';



        $this->Product_Table_Field_Translations = array(
            'part_number' => 'part_number',
            'category' => 'categories',
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

    public function CleanPartNumber($PN) // <------- EXTENDED
    {
        return preg_replace('/[^0-9a-zA-Z\-~\.]/', '', $PN);
    }


    public function GetItemTitleOptions($options)
    {
        $TITLE_RESULT = '';
        $PRICE_RESULT = 0;
        $options_list = explode(',', $options);
        foreach ($options_list as $option) {
            list($id, $value) = explode('.', $option);
            $id = intOnly($id);

            if (empty($this->Options_Array[$id])) {
                $this->Options_Array[$id] = $this->SQL->GetRecord(
                    'store_product_options', 'option_title,option_values', "store_product_options_id=$id"
                );
            }
            $option_record = $this->Options_Array[$id];
            if ($option_record) {
                $item_list = explode("\n", $option_record['option_values']);
                foreach($item_list as $item) {
                    list($code, $value_title, $price) = explode('|', $item . '||');
                    $price = preg_replace('/[^0-9\.\-]/', '', $price);
                    if ($value == $code) {
                        $TITLE_RESULT .= '<b>' .$option_record['option_title'] . ':</b> ' . trim($value_title) . ', ';
                        $PRICE_RESULT += $price;
                    }
                }
            }
        }
        if ($TITLE_RESULT) {
            $TITLE_RESULT = '<br /><span style="font-size:0.8em">'. substr($TITLE_RESULT, 0, -2) . '</span>';
        }
        return array($TITLE_RESULT, $PRICE_RESULT);
    }

    public function GetItemRecord($PN, $db_record='')
    {
        $part_number = intOnly(strTo($PN, '~'));
        $options     = str_replace('~', ',', strFrom($PN, '~'));

        if (empty($db_record)) {
            $db_record = $this->SQL->GetRecord(array(
                'table' => $this->Product_Table,
                'keys'  => '*',
                'where' => "`{$this->Product_Table}_id`=$part_number AND active=1"
            ));
        }

        if ($db_record['sale_percent'] != 0) {
            $db_record['price']  = $db_record['price'] - ($db_record['price'] * ($db_record['sale_percent']/100));
        } elseif ($db_record['sale_dollar'] != 0) {
            $db_record['price']  = $db_record['price'] - $db_record['sale_dollar'];
        }

        # DETERMINE IF A SPECIAL DISCOUNT PRICE HAS BEEN SET FOR THE PRODUCT
        if (isset($this->Discount_Product_Array[$part_number])) {
            $db_record['price'] = $this->Discount_Product_Array[$part_number];
        }
        
        if ($options) {
            $db_record['part_number'] .= "[$options]";
            list($option_mod, $price_mod) = $this->GetItemTitleOptions($options);  // returns array($TITLE_RESULT, $PRICE_RESULT);
            if ($option_mod) {
                $db_record['title'] .= $option_mod;
                $db_record['price'] += $price_mod;
            }
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

        //---------- PROMO ----------
        $PROMO = Session('PROMO');
        if ($PROMO and $record) {
            //$record['price'] = $PROMO['p' . $record['part_number']];
        }

        if ($this->Show_Shipping_Options) {
            // compute shipping here
        }

        return $RESULT;
    }


    public function GetBuyerInfo()
    {
        $content = parent::GetBuyerInfo();
        $STEP = new General_Steps;
        return $STEP->GetSteps($this->Step_Array, 1, $content, 700);
    }

    public function GetBillingInfo()
    {
        $content = parent::GetBillingInfo();
        $STEP = new General_Steps;
        return $STEP->GetSteps($this->Step_Array, 2, $content, 700);
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
#                'fieldset|Additional Information|class="order_fieldset"||h2|'. $this->H2_Style,
#                // add additional fields here
#                'textarea|Comments|comments|N|40|6|',
#                'endfieldset',
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