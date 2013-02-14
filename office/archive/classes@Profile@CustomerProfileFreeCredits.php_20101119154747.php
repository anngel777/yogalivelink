<?php

/* ========================================================================== 
    CLASS :: Profile_CustomerProfileFreeCredits
    
    Used by administrators to give free credits to a customer. If a WHID has been
    passed in - it will go straight to the form. Otherwise it will ask the 
    administrator to identify the customer - then jump to the form.
    
# ========================================================================== */

class Profile_CustomerProfileFreeCredits extends BaseClass
{
    public $wh_id                           = 0;        // Customer WH_ID
    public $ShowArray                       = false;    // Show array values
    public $ShowQuery                       = false;    // Show database queries
    public $free_credits_link_location      = "/office/dev_richard/class_execute;class=Profile_CustomerProfileFreeCredits;";
    
    public function  __construct()
    {
        parent::__construct();
        
        $this->SetParameters(func_get_args());
        $this->wh_id = ($this->GetParameter(0)) ? $this->GetParameter(0) : 0;
        
        $this->Table                = 'credits';
        $this->Add_Submit_Name      = 'CREDITS_SUBMIT_ADD';
        $this->Edit_Submit_Name     = 'CREDITS_PRODUCTS_SUBMIT_EDIT';
        $this->Index_Name           = 'credits_id';
        $this->Flash_Field          = 'credits_id';
        
        $this->Field_Titles = array(
            'credits_id'    => 'Credits Id',
            'credits_code'  => 'Credits Code',
            'wh_id'         => 'Wh Id',
            'order_id'      => 'Order Id',
            'type'          => 'Type',
            'notes'         => 'Notes',
            'active'        => 'Active',
            'updated'       => 'Updated',
            'created'       => 'Created'
        );
        
    }
    
    public function Execute()
    {
        # CHECK TO SEE IF THIS HAS BEEN CALLED WITH A WH_ID PASSED IN
        # ====================================================================================
        # <> YES - just execute the free credits form on that wh_id
        # <> NO - display form asking admin to identify person
        # ====================================================================================
        if ($this->wh_id == 0) {
            $this->ShowCustomerSelect($this->free_credits_link_location);
        } else {
            $code = GenerateCode(6);
            $this->Default_Values = array(
                'wh_id'             => $this->wh_id,
                'type'              => 'free',
                'order_id'          => 'F-666',
                'admin_wh_id'       => $_SESSION['USER_LOGIN']['LOGIN_RECORD']['wh_id'],
                'email_contents'    => 'You have been given free credits.',
            );
            $this->AddRecord();
        }
    }

    public function ShowCustomerSelect()
    {
        $client_list        = $this->CreateClientListingByAlphabet($this->free_credits_link_location);
        echo "<div style='height:300px; width:500px; padding:10px; border:1px solid #990000; overflow:scroll;'>{$client_list}</div>";
    }
    
    public function SetFormArrays()
    {
        $base_array = array(
            'info||<div style="color:#990000;">NEED TO ENABLE SENDING EMAIL CONFIRMATION</div>',
            "form|$this->Action_Link|post|db_edit_form",
            'info||This module will assign free credits to the user. Admin MUST put in notes about why credits are being given.',
            #'text|Credits Code|credits_code|N|6|6',
            'text|Customer Wh Id|wh_id|N|11|11',
            'text|Admin Id|admin_wh_id|N|11|11',
            'text|Order Id|order_id|N|11|11',
            'text|Type|type|N|60|255',
            'text|Qty|qty|Y|11|11',
            'textarea|Notes|notes|Y|60|4',
            'code|<br /><br />',
            'checkbox|Send Email|email_send||1|0',
            'textarea|Email|email_contents|Y|60|4',
            "submit|Add Record|$this->Add_Submit_Name",
            'endform'
        );
        
        $this->Form_Data_Array_Add = $base_array;
    }
    
    public function PostProcessFormValues($FormArray)
    {
        if ($this->ShowArray) echo ArrayToStr($FormArray);
        
        
        # GENERATE A CODE
        # ====================================================
        do {
            # generate a code
            $code = GenerateCode();
            
            # verify code is unique
            $unique = $this->SQL->IsUnique(array(            
                'table' => $this->Table,
                'key'   => 'credits_code',
                'value' => $code,
            ));
        } while (!$unique);
        
        
        # SET SOME OTHER FORM VALUES
        # ====================================================
        $admin_wh_id        = $FormArray['admin_wh_id'];
        $email_send         = $FormArray['email_send'];
        $email_contents     = $FormArray['email_contents'];
        $qty                = intOnly($FormArray['qty']);
        
        $FormArray['credits_code']  = $code;
        $FormArray['notes']        .= "\n\n[Created By Admin:$admin_wh_id]";
        
        unset($FormArray['admin_wh_id']);
        unset($FormArray['qty']);
        unset($FormArray['email_send']);
        unset($FormArray['email_contents']);
        
        
        # ADD ALL THE RECORDS
        # ====================================================
        for ($i=0; $i<$qty; $i++) {
        
            $keys   = '';
            $values = '';            
            foreach ($FormArray as $var => $val) {
                $val = addslashes($val);
                
                $keys   .= "`$var`, ";
                $values .= "'$val', ";
            }
            $keys   = substr($keys, 0, -2);
            $values = substr($values, 0, -2);
            
            $result = $this->SQL->AddRecord(array(            
                'table'     => $this->Table,
                'keys'      => $keys,
                'values'    => $values,
            ));
            if (!$result) echo "<br /><br />UNABLE TO ADD RECORD  --> " . $this->SQL->Db_Last_Query;
        }
        
        
        # DESTROY THE FORM ARRAY
        # ====================================================
        unset($FormArray);
        
        
        # SEND THE EMAIL
        # ====================================================
        if ($email_send) {
            # actually send the email
            # store sending as acontact point in database
            
            echo "<br />Email has been SENT to the customer.";
        } else {
            echo "<br />You have opted NOT to send email.";
        }
        
        
        # COMPLETE PROCESS
        # ====================================================
        echo "<br /><h2>CREDITS HAVE BEEN ADDED</h2>";
        exit();
    }
    
    
}
