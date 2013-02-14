<?php

class Profile_CustomerProfileAddAccount
{
    public $wh_id               = 1000666;
    public $num_free_credits    = 5;
    public $num_paid_credits    = 5;


    public $added_credits_ids = '';
    
    public $ShowArray   = true;
    public $ShowQuery   = true;
    
    
    
    public function  __construct()
    {
        $this->SetSQL();
    }
    
    public function SetSQL()
    {
        if (empty($this->SQL)) {
            $this->SQL = Lib_Singleton::GetInstance('Lib_Pdo');
        }
    }
    
    
    public function Execute()
    {
        echo "<br /><br />Profile_CustomerProfileAddAccount";
    
        # CHECK IF TEST USER ACCOUNT IS CURRENTLY ACTIVE
        # ========================================================================
        $record = $this->SQL->GetRecord(array(
            'table' => 'contacts',
            'keys'  => '*',
            'where' => "`wh_id`=$this->wh_id AND active=1",
        ));
        
        
        if (!$record) {
            
            # ADD AN ACCOUNT - contacts
            # ========================================================================
            $this->AddContacts();
            
            
            # ADD ACCOUNT SETTINGS - contacts_account
            # ========================================================================
            $this->AddContactsAccount();
            
            
            # ADD BILLING INFORMATION - contacts_billing_storage
            # ========================================================================
            $this->AddContactsBillingStorage();
            
            
            # ADD FREE CREDITS - credits
            # ========================================================================
            $this->AddCreditsFree();
            
            
            # ADD PURCHASED CREDITS - credits
            # ========================================================================
            $this->AddCreditsPurchased();
            
            
        } else {
            echo "<br /><br /><h2>RECORD ALREADY EXISTS - CAN'T ADD TESINTG USER</h2>";
        } // end checking for record
        
        
    }
    
    
    public function AddContacts()
    {
        $db_record = array(            
                'wh_id'         => $this->wh_id,
                'first_name'    => 'Test',
                'last_name'     => 'Testerson',
                'email_address' => 'test.testerson@mailwh.com',
                'super_user'    => 1,
                'created_by'    => 'Class::Profile_CustomerProfileAddAccount',
            );
        $this->AddRecordLoc('contacts', $db_record);
    }
    
    public function AddContactsAccount()
    {
        $db_record = array(            
                'wh_id'         => $this->wh_id,
                'setting'       => 'enabled',
                'value'         => 'true',                
            );
        $this->AddRecordLoc('contacts_account', $db_record);
    }
    
    public function AddContactsBillingStorage()
    {
        $db_record = array(            
                'wh_id'                 => $this->wh_id,
                'payment_option'        => '',
                'card_type'             => '',
                'card_name'             => '',
                'card_number_reference' => '',
                'card_month'            => '',
                'card_year'             => '',
                'card_ccv'              => '',
                'card_address'          => '',
                'card_postal_code'      => '',
                'bill_address_1'        => '',
                'bill_address_2'        => '',
                'bill_address_3'        => '',
                'bill_city'             => '',
                'bill_state'            => '',
                'bill_country_code'     => '',
                'bill_postal_code'      => '',
                'bill_contact'          => '',
                'bill_contact_phone'    => '',
            );
        $this->AddRecordLoc('contacts_billing_storage', $db_record);
    }
    
    public function AddCreditsFree()
    {
        $date = date('now');
        $db_record = array(            
            'wh_id'                 => $this->wh_id,
            'credits_code'          => 'TEST01',
            'order_id'              => 1,
            'payment_conf_number'   => '',
            'refund_conf_number'    => '',
            'type'                  => 'free',
            'notes'                 => "Created via Class::Profile_CustomerProfileAddAccount. [Date:$date].",
        );
        $added_credits_ids = '';
        for ($z=0; $z<$this->num_free_credits; $z++) {
            $this->AddRecordLoc('credits', $db_record);
            $added_credits_ids .= $this->SQL->Last_Insert_Id . '|';
        }
        $added_credits_ids = substr($added_credits_ids, 0, -1);
        $this->added_credits_ids = $added_credits_ids;
    }
    
    public function AddCreditsPurchased()
    {
        $date = date('now');
        $db_record = array(            
                'wh_id'                 => $this->wh_id,
                'credits_code'          => 'TEST02',
                'order_id'              => '1001-5',
                'payment_conf_number'   => 'TST-01-X',
                'refund_conf_number'    => '',
                'type'                  => 'Purchase',
                'notes'                 => "Created via Class::Profile_CustomerProfileAddAccount. [Date:$date].",
            );
        for ($z=0; $z<$this->num_paid_credits; $z++) {
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
        if ($this->ShowQuery) echo "<br /><br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        if ($result) {
            echo "<br /><br />RECORD ADDED";
        }
    }
    
    private function UpdateRecordLoc($table, $db_record, $where) 
    {
        $key_values = '';
        foreach ($db_record as $var => $val) {
            $val = addslashes($val);
            $key_values .= "`$var`='$val', ";
        }
        $key_values = substr($key_values, 0, -2);
        
        $result = $this->SQL->UpdateRecord(array(
            'table'         => $table,
            'key_values'    => $key_values,
            'where'         => "{$where} AND active=1",
        ));
        if ($this->ShowQuery) echo "<br /><br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        if ($result) {
            echo "<br /><br />RECORD UPDATED";
        }
    }
    
    
    
    
}