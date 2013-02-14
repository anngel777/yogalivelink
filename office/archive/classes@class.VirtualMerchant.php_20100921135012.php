<?php
class VirtualMerchant
{

    public $Xml_Url = 'https://www.myvirtualmerchant.com/VirtualMerchant/processxml.do?xmldata=';

    public $Send_Xml = '<txn>
<ssl_merchant_ID>@@MERCHANT_ID@@</ssl_merchant_ID>
<ssl_user_id>@@USER_ID@@</ssl_user_id>
<ssl_pin>@@PIN@@</ssl_pin>
<ssl_transaction_type>@@TRANSACTION_TYPE@@</ssl_transaction_type>
<ssl_card_number>@@CC_NUMBER@@</ssl_card_number>
<ssl_exp_date>@@CC_EXP_DATE@@</ssl_exp_date>
<ssl_amount>@@AMOUNT@@</ssl_amount>
<ssl_salestax>@@SALES_TAX@@</ssl_salestax>
<ssl_cvv2cvc2_indicator>@@CVV2_INDICATOR@@</ssl_cvv2cvc2_indicator>
<ssl_cvv2cvc2>@@CVV2@@</ssl_cvv2cvc2>
<ssl_invoice_number>@@INVOICE_NUMBER@@</ssl_invoice_number>
<ssl_customer_code>@@CUSTOMER_CODE@@</ssl_customer_code>
<ssl_first_name>@@FIRST_NAME@@</ssl_first_name>
<ssl_last_name>@@LAST_NAME@@</ssl_last_name>
<ssl_avs_address>@@ADDRESS1@@</ssl_avs_address>
<ssl_address2>@@ADDRESS2@@</ssl_address2>
<ssl_city>@@CITY@@</ssl_city>
<ssl_state>@@STATE@@</ssl_state>
<ssl_avs_zip>@@ZIP@@</ssl_avs_zip>
<ssl_phone>@@PHONE@@</ssl_phone>
<ssl_test_mode>@@TEST_MODE@@</ssl_test_mode>
</txn>';

    public $Send_Array = array(
        'MERCHANT_ID'     => '',
        'USER_ID'         => '',
        'PIN'             => '',
        'TRANSACTION_TYPE'=> 'ccsale',
        'CC_NUMBER'       => '',
        'CC_EXP_DATE'     => '',
        'AMOUNT'          => '',
        'SALES_TAX'       => '0.00',
        'CVV2_INDICATOR'  => 1,
        'CVV2'            => '',
        'INVOICE_NUMBER'  => '',
        'CUSTOMER_CODE'   => '',
        'FIRST_NAME'      => '',
        'LAST_NAME'       => '',
        'ADDRESS1'        => '',
        'ADDRESS2'        => '',
        'CITY'            => '',
        'STATE'           => '',
        'ZIP'             => '',
        'PHONE'           => '',
        'TEST_MODE'       =>  'false'
    );
    
    public $Approval_Code    = '';
    public $Response_Message = '';


    public function  __construct()
    {

    }

    public function FormatXml($str)
    {
       $str = htmlspecialchars($str);
       $items = TextBetweenArray('&lt;', '&gt;', $str);
       $str = str_replace('&gt;&lt;', '&gt;<br />&lt;', $str);
       foreach ($items as $item) {
           $old = "&lt;$item&gt;";
           $new = "<span style=\"color:#00f;\">$old</span>";
           $str = str_replace($old, $new, $str);
       }
       return $str;
    }

    public function SetSendValue($key, $value)
    {
        if (array_key_exists($key, $this->Send_Array)) {
            $this->Send_Array[$key] = $value;
            return true;
        } else {
            return false;
        }
    }

    public function SetMerchantId($merchant_id, $user_id, $pin)
    {
        $this->SetSendValue('MERCHANT_ID', $merchant_id);
        $this->SetSendValue('USER_ID', $user_id);
        $this->SetSendValue('PIN', $pin);
    }

    public function SendReceiveXML()
    {
        $XML = preg_replace("/\r\n/", '', $this->Send_Xml);

        foreach ($this->Send_Array as $key => $value) {
            $XML = str_replace('@@' . $key . '@@', $value, $XML);
        }

        $XML = urlencode($XML);
        $received_xml = file_get_contents($this->Xml_Url . $XML);
        if ($received_xml) {
            $keys = TextBetweenArray('</', '>', TextBetween('<txn>', '</txn>', $received_xml));
            $RESULT = array();
            foreach ($keys as $key) {
                $key_minus_ssl = str_replace('ssl_', '', $key);
                $value = TextBetween("<$key>", "</$key>", $received_xml);
                $RESULT[$key_minus_ssl] = $value;                
                if ($key_minus_ssl == 'result') {
                    $this->Approval_Code = $value;
                } elseif ($key_minus_ssl == 'result_message') {
                    $this->Response_Message = $value;
                }
            }
        } else {
            $RESULT = '';
        }

        return $RESULT;
    }



}


