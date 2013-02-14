<?php
class Store_YogaStoreCreditOrder extends Store_YogaStoreOrder
{
    public $Show_Query      = false;
    public $User_Record     = '';
    public $Status          = 'START';
    
    
    public $Invoice_Num         = 0;
    public $Transaction_Id      = 0;
    
    
    public $Discount_Product_Array  = array();
    
    public $Email_Subject           = 'Thank You For Purchasing Credits For YogaLiveLinks.com';
    public $Email_Template_Id       = 5;
    
    // ------ Base Buyer Form ------
    // -- RAW -- And the fields we need access to in the final email
    public $Buyer_Form_Data = array(
        'text|WHID|wh_id|Y|30|40',
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
    
    // ------ Base Billing Form ------
    public $Billing_Form_Data = array(
        'creditcard|Credit Card Number|CARD_NUMBER|Y',
        'text|Name on Credit Card|CARD_NAME|Y|30|40|',
        'datecc|Expiration Date|CARD_EXP|Y|H',
        'integer|Card Security Code|CARD_SECURITY_CODE|Y|4|4||H',
        #'text|Address 1|CARD_ADDRESS1|Y|30|80|',
        #'text|Address 2|CARD_ADDRESS2|N|30|80|',
        #'text|City|CARD_CITY|Y|30|80',
        #'countrystate|Country|CARD_COUNTRY:CARD_STATE|Y',
        #'text|Postal Code|CARD_POSTAL_CODE|Y|5|20',
        #'phone|Phone|CARD_PHONE|Y'
    );
    
    public function __construct()
    {
        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Description' => 'Special functions for BUYING CREDITS',
        );
        
        $this->Ajax_Cart                = false;
        $this->Use_Cart                 = true; // needed to show shopping cart in bill
        $this->Cart_Itemize_Shipping    = false;
        $this->Shipping_Options         = array();
        $this->User_Record              = ArrayValue(Session('USER_LOGIN'), 'LOGIN_RECORD');
        
        
        
        $this->Order_Table_Options = 'style="background-color:#9E9D41; border:2px solid #9E9D41; width:500px; font-family:Arial,Verdana,Helvetica,sans-serif;" cellspacing="1" align="center"';
        $this->Order_Th_Options    = 'align="right" style="background-color:#9E9D41; color:#000; padding:0.25em; width:200px"';

        $this->Bill_Table_Options  = 'style="background-color:#9E9D41; width:500px;" align="center"  border="0" cellpadding="0" cellspacing="1"';
        $this->Bill_Th_Options     = 'style="background-color:#9E9D41; color:#fff; padding:0.25em;"';

        
        
        // =========== AUTHORIZE.NET SETUP ============
        $this->Authorize_Net_Login_Id  = '4M38vSbvkQa'; //'YogaLiveLink2010';
        $this->Authorize_Net_Trans_Key = '798waz84BJ4MWb56'; //'Hauptsegel2010';
        $this->Authorize_Net_Testing = false;  //<<<<<<<<<<---------- REMOVE ----------<<<<<<<<<<
        
    }


    public function ProcessOrderPage()  // extended
    {
        if (Post('RETURN')) {
            header('Location: ' . $this->GetReturnPage());
        }

        $RESULT = '';

        $action = $this->getFormAction();
        $RESULT .= "\n<form action=\"$action\" method=\"post\" name=\"ORDERFORM\">\n";

        if (!isset($_SESSION['STORE_ORDER']['BUYER_INFO'])) {
            // this code loads the user information into the buyer form for display and provides info for post processing
            Form_PostArray($this->User_Record);
            Form_SetShowPosted(false);
            $error = '';
            $array = ProcessForm(
                $this->Buyer_Form_Data,
                $table,
                $this->Order_Table_Options, $this->Order_Th_Options, $this->Order_Td_Options,
                $error
            );
            $_SESSION['STORE_ORDER']['BUYER_INFO'] = $array;
            $_SESSION['STORE_ORDER']['BUYER_INFO_TABLE'] = $table;
        }

        $this->SetBillingForm();

        if (Post('ORDER')) {
            // ----- get the billing information ----
            if (Post('ORDER')) {
                $array = ProcessFormNT($this->Billing_Form_Data, $this->Error);
                AddError($this->Error);
                $_SESSION['STORE_ORDER']['BILLING_INFO'] = $array;
                if (!$this->Error) {
                    // -- got  successful billing info
                    $this->Billing_Info = $array;
                    $this->Final_Array = $array;
                    
                    // -- PROCESS THE FORM FINALLY
                    
//echo ArrayToStr($_SESSION['STORE_ORDER']['BUYER_INFO']['email_address']);
//echo ArrayToStr($this->Billing_Info);
//exit();
                    
                    $result = $this->FinalOrderProcess();
                    if ($result) {
                        $this->Status = 'OK';
                        $RESULT .= "$this->Final_Order_Text</form>\n";
                        return $RESULT;  // ------ exits this function
                    } else {
                        $this->Status = 'FAILED';
                        AddError($this->Error);
                    }
                }
            }
        }

        if (!isset($_SESSION['STORE_ORDER']['BILLING_INFO'])) {
            $this->PreloadBillingData();
        }

        Form_PostArray($_SESSION['STORE_ORDER']['BILLING_INFO']);
        $RESULT .= $this->GetBillingInfo();

        $RESULT .= "</form>\n";
        return $RESULT;
    }


    
    
    
    public function SetDiscountPriceOnProduct($product_id=0, $price=0) //Discount the credits for the users
    {
        if ($price != 0) {
            $this->Discount_Product_Array[$product_id] = $price;
        }
    }
    
    /*
    public function GetDiscountPriceOnProduct($product_id) //Discount the credits for the users
    {
        $this->Discount_Product_Array[$product_id] = $price;
    }
    */
    
    
    public function FinalOrderProcess()
    {
        // process credit card
        //
        if ($this->ProcessCreditCard()) {
            
            // if OK, email result
            $this->GetFinalTable();

            $this->SendEmailsRAW();
            /*
            if ($this->Send_Emails) {
                $this->SendEmails();
                echo "<h2>SEND EMAILS</h2>";
            }
            */

            // output Message
            #$this->Final_Order_Text = $this->GetSuccessfulOrderMessage() . $this->Final_Table;
            #$this->Final_Order_Text = $this->GetSuccessfulOrderMessage();
            $this->Final_Order_Text = '<h2><div>Your purchase has been successfully completed!</div>
                <div>You will receive a confirmation e-mail shortly.</div><br />
                <div>Select one of the options below to continue.</div><br />
                </h2>';
            
            // update transaction tables
            $this->AddTransaction();

            
            
            // ADD THE CREDITS
            // =======================================================================
            
            foreach ($_SESSION['STORE_ORDER']['CART'] AS $id => $qty) {
                $product_id = $id;
            }
            
            $record = $this->SQL->GetRecord(array(
                'table' => 'store_products',
                'keys'  => 'part_number,credit_type_standard,credit_type_therapy',
                'where' => "`store_products_id`=$product_id AND `active`=1",
            ));
            if ($record) {
                $part_number    = $record['part_number'];
                $parts          = explode('-', $part_number);
                $credits        = $parts[1];
                
                if (!$credits) {
                    $this->Error = "ERROR - PRODUCT ID NOT ABLE TO CALCULATE NUMBER OF CREDITS - PLEASE CONTACT support@yogalivelink.com";
                }
            } else {
                $this->Error = "ERROR - UNABLE TO ADD CREDITS - PLEASE CONTACT support@yogalivelink.com";
            }
            
            
            $credits_code = 'ABCDEF';
            
            //echo ArrayToStr($_SESSION['STORE_ORDER']['CART']);
            
            $NUMBER = $credits;
            $DETAILS = array(
                'wh_id'                 => $_SESSION['STORE_ORDER']['BUYER_INFO']['wh_id'],
                'credits_code'          => $credits_code,
                'type_standard'         => $record['credit_type_standard'],
                'type_therapy'          => $record['credit_type_therapy'],
                'order_id'              => $_SESSION['STORE_ORDER']['ORDER_ID'],
                'invoice_number'        => $this->Invoice_Num,
                'payment_conf_number'   => $this->Transaction_Id,
            );
            $this->AddCreditsPurchased($NUMBER, $DETAILS);
            
            
            
            // clear cart, order_id
            if ($this->Clear_Cart_At_End) {
                $_SESSION['STORE_ORDER']['CART'] = '';
                $_SESSION['STORE_ORDER']['ORDER_ID'] = '';
            }
            return true;
        } else {
            $this->Error = "ERROR - UNABLE TO PROCESS CREDIT CARD INFORMATION"; // - PLEASE CONTACT support@yogalivelink.com
            return false;
        }
    }
    
    
    
    public function AddCreditsPurchased($NUMBER, $DETAILS)
    {
        $date = date('now');
        $db_record = array(            
                'wh_id'                 => $DETAILS['wh_id'],
                'credits_code'          => $DETAILS['credits_code'],
                'order_id'              => $DETAILS['order_id'],
                'payment_conf_number'   => $DETAILS['payment_conf_number'],
                'type_standard'         => $DETAILS['type_standard'],
                'type_therapy'          => $DETAILS['type_therapy'],
                'refund_conf_number'    => '',
                'type'                  => 'Purchase',
                'notes'                 => "[Date:$date] Created via Class::Store_YogaStoreCreditOrder.",
            );
        for ($z=0; $z<$NUMBER; $z++) {
            $this->AddRecordLoc('credits', $db_record);
        }
    }
    
    private function AddRecordLoc($table, $db_record) 
    {
        $keys   = '';
        $values = '';            
        foreach ($db_record as $var => $val) {
            $val = addslashes($val);
            
            $keys   .= "`$var`, ";
            $values .= "'$val', ";
        }
        $keys   = substr($keys, 0, -2);
        $values = substr($values, 0, -2);
        
        $result = $this->SQL->AddRecord(array(
            'table'     => $table,
            'keys'      => $keys,
            'values'    => $values,
        ));
        if ($this->Show_Query) echo "<br /><br />LAST QUERY = " . $this->SQL->Db_Last_Query;
    }
    
    
    
    
    
    



    public function SendEmailsRAW()
    {
        

        #$this->SendEmailBuyer();
        #$this->SendEmailSeller();
        
        
        
        ##==================================
        #==================================
        #==================================
        #==================================
        #==================================
        
        
        $email_send_to_user     = false;
        $email_send_to_admin    = false;
        
        
        if ($email_send_to_user || $email_send_to_admin)
        {
            # INITIALIZE THE EMAIL CLASS
            # ==================================
            global $ROOT;
            require_once "$ROOT/phplib/swift4/swift_required.php";
            $MAIL = new Email_MailWh;
        }
        
        
        
        
        $swap_list              = array (
            '@@WH_ID@@'             => Post('FORM_wh_id'),
            '@@REQUEST@@'           => Post('FORM_notes'),
            '@@CATEGORY@@'          => Post('FORM_category'),
            '@@REQUESTOR_NAME@@'    => Post('FORM_requestor_name'),
            '@@REQUESTOR_EMAIL@@'   => Post('FORM_requestor_email'),
        );
        
        
        # SEND MESSAGE TO USER (if applicable)
        # =============================================================
        if ($email_send_to_user || $email_send_to_admin) {
        
            # MAKE THE BODY CONTENT
            # ==================================
            $content = $this->GetBuyerEmailContent();
            
            $message = astr_replace($swap_list, $content);
            $swap_array = array(
                '@@email_body_content@@'        => $message,
            );
            
            
            # SETUP THE EMAIL ADDRESSES
            # ==================================
            global $EMAIL_ADMIN_EMAIL;
            $bcc = ($email_send_to_admin) ? $EMAIL_ADMIN_EMAIL : '';
            
            $buyer              = $_SESSION['STORE_ORDER']['BUYER_INFO'];
            $this->Buyer_Name   = trim($buyer['first_name'] . ' ' . $buyer['last_name']);
            $this->Buyer_Email  = $buyer['email_address'];
            $buyer_whid         = $buyer['wh_id'];
            
            
            # PREP THE MESSAGE ARRAY
            # ==================================
            $msg_array = array(
                'email_template_id'     => $this->Email_Template_Id,
                'swap_array'            => $swap_array,
                'subject'               => $this->Email_Subject,
                'to_name'               => $this->Buyer_Name,
                'to_email'              => $this->Buyer_Email,
                'cc'                    => $bcc,
                'bcc'                   => $bcc,
                'wh_id'                 => $buyer_whid,
            );
            
            ###echo ArrayToStr($msg_array);
            
            $MAIL->PrepareMailToSend($msg_array);
            
            
            # SEND THE PREPARED MESSAGE
            # ==================================
            if ($MAIL->MailPrepared()) {
                echo "<h1>A copy of the message has been sent to your email address.</h1>";
            } else {
                echo "<h1>Unable to send a copy of the message to your email address.</h1>";
            }
        }
        
        
        
        
        
        
        
        
        
        
        
        
        
        #==================================
        #==================================
        #==================================
        #==================================
        #==================================
        
        
    }
    
    
    public function SendEmailBuyer()
    {
        return true;
    }

    public function SendEmailSeller()
    {
        return true;
    }
    
    public function SetBuyerForm() // <------- EXTENDED
    {
        return;  // removes modifications because we only need the output table
    }

    public function GetBillingInfo() // <------- EXTENDED
    {
        /*
        $RESULT  = "\n<fieldset class=\"order_fieldset\">\n<legend>{$this->Text['FORM_YOUR_ORDER_INFORMATION']}</legend>\n";
        $RESULT .= $_SESSION['STORE_ORDER']['BUYER_INFO_TABLE'];
        $RESULT .= "<br />\n";
        $RESULT .= $this->GetShoppingCart(false);
        $RESULT .= "\n</fieldset>\n";
        $RESULT .= OutputForm($this->Billing_Form_Data, Post('ORDER'));
        */
        
        $payment = OutputForm($this->Billing_Form_Data, Post('ORDER'));
        
        
        #$btn_continue       = MakeButton('positive', 'CONTINUE', "{$this->script_location};step=payment;sid={$this->sessions_id};time_release={$this->heq_time_release_user}");
        
        $buyer_info         = AddBox_Type1('BUYER INFO', $_SESSION['STORE_ORDER']['BUYER_INFO_TABLE']);
        $product_box        = AddBox_Type1('PRODUCT INFORMATION', $this->GetShoppingCart(false));
        $payment_box        = AddBox_Type1('PAYMENT INFORMATION', $payment);
        
        $buyer_info         = '';
        
        $RESULT = "$buyer_info <br /><br /> $product_box <br /><br /> $payment_box";
        
        return $RESULT;
    }


    public function SetBillingForm() // <------- EXTENDED
    {
        global $FORM_VAR;
        $onclick 	        = "this.value='{$FORM_VAR['submit_click_text']}';";
        $id 		        = Form_GetIdFromVar('ORDER');
        $name 		        = 'ORDER';
        $btn_submit 	    = MakeButton('positive', 'MAKE PAYMENT', '', '', $id, $onclick, 'submit', $name);
        
        $this->Billing_Form_Data = array_merge(
            array(
                'select|Credit Card Type|CARD_TYPE|Y||' . Form_AssocArrayToList($this->Card_Types),
            ),
            $this->Billing_Form_Data,
            array(
                'code|<br /><br />',
                "info||$btn_submit",
            )
        );
        
        #$this->Billing_Form_Data = BaseArraySpecialButtons($this->Billing_Form_Data, 'ORDER', 'MAKE PAYMENT');
    }

    public function ProcessCreditCard() // <------- EXTENDED
    {
        //return true;
        return parent::ProcessCreditCard();
        
        
        //need to extend this function
        //return true;
    }

    public function getFormAction()  // <------- EXTENDED
    {
        global $PAGE;
        $result = $PAGE['pagelinkquery'];
    }

} // --------------------- END CLASS ----------------------