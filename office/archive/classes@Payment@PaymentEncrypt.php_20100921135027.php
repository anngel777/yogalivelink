<?php
class Payment_PaymentEncrypt
{
    public function  __construct()
    {
        // Encription of Credit Card Numbers support public functions
    }

    //-----------SUBSTRINGS-----------

    public function strTo($string, $to) 
    {
      $i = strpos($string,$to);
      if ( $i !== false ) return substr($string,0,$i);
        else return $string;
    }

    public function strFrom($string, $from) 
    {
      if (empty($from)) return $string;
      $i = strpos($string,$from);
      if ( $i !== false ) return substr($string,$i+strlen($from));
        else return '';
    }


    public function SetPost($str,$ModStr='ST')
    {
        $VARS = explode(' ',$str);
        foreach ($VARS as $PV) $GLOBALS[$PV] = isset($_POST[$PV])? trim($_POST[$PV]) : '';
    }


    public function ValidCreditCardNumber($cardNumber)
    {
        $cardNumber = ereg_replace('[^0-9]', '', $cardNumber);

        if ( empty($cardNumber) ) {
            return false;
        }

        $validFormat = ereg("^5[1-5][0-9]{14}|"  // mastercard
            . "^4[0-9]{12}([0-9]{3})?|" // visa
            . "^3[47][0-9]{13}|" // american express
            . "^3(0[0-5]|[68][0-9])[0-9]{11}|" //discover
            . "^6011[0-9]{12}|" //diners
            . "^(3[0-9]{4}|2131|1800)[0-9]{11}$", $cardNumber); //JC

        if (!$validFormat) {
            return false;
        }

        // Is the number valid?
        $revNumber = strrev($cardNumber);
        $numSum = 0;

        for ($i = 0; $i < strlen($revNumber); $i++) {

            $currentNum = substr($revNumber, $i, 1);

            // Double every second digit
            if ($i % 2 == 1) {
                $currentNum *= 2;
            }

            // Add digits of 2-digit numbers together
            if ($currentNum > 9) {
                $firstNum = $currentNum % 10;
                $secondNum = ($currentNum - $firstNum) / 10;
                $currentNum = $firstNum + $secondNum;
            }

            $numSum += $currentNum;
        }

        // If the total has no remainder it's OK
        $passCheck = ($numSum % 10 == 0);
        
        return $passCheck;
    }

    //======================== encryption ===========================
    public function HexEncodeString($str)
    {
        $RESULT = '';
        $strlen = strlen($str);
        for ($i=0; $i < $strlen; $i++) {
            $RESULT .= sprintf("%02x",ord(substr($str,$i,1)));
        }
        return $RESULT;
    }

    public function HexDecodeString($str)
    {
        $RESULT = '';
        $strlen = strlen($str);
        for ($i=0; $i < $strlen; $i+=2) {
            $RESULT .= chr(hexdec(substr($str,$i,2)));
        }
        return $RESULT;
    }

    public function EncryptStringHex($string, $key)
    {   // encrypts string using key and converts to a two-char per byte hex string
        srand(crc32($key));
        $td         = mcrypt_module_open(MCRYPT_RIJNDAEL_256, '', MCRYPT_MODE_CFB, '');  // open module
        $iv         = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        $ks         = mcrypt_enc_get_key_size($td);
        $keystr     = substr(sha1($key), 0, $ks);
        mcrypt_generic_init($td, $keystr, $iv);
        $RESULT     = mcrypt_generic($td, $string);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td); // close module
        
        return HexEncodeString($RESULT);  // convert binary to hex string and return
    }


    public function DecryptStringHex($string, $key)
    {
        srand(crc32($key));
        $UnHexStr   = $this->HexDecodeString($string);  // hex string back to binary
        $td         = mcrypt_module_open(MCRYPT_RIJNDAEL_256, '', MCRYPT_MODE_CFB, '');  // open module
        $iv         = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        $ks         = mcrypt_enc_get_key_size($td);
        $keystr     = substr(sha1($key), 0, $ks);
        mcrypt_generic_init($td, $keystr, $iv);        
        $RESULT     = mdecrypt_generic($td, $UnHexStr);        
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td); // close module
        
        return $RESULT;
    }

}  // -------------- END CLASS --------------