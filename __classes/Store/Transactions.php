<?php

// FILE: /Store/Transactions.php

class Store_Transactions extends BaseClass
{
    public function  __construct()
    {
        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => '',
            'Description' => 'Create and manage store_transactions',
            'Created'     => '2010-09-01',
            'Updated'     => '2010-09-01'
        );

        $this->Table  = 'store_transactions';

        $this->Add_Submit_Name  = 'STORE_TRANSACTIONS_SUBMIT_ADD';
        $this->Edit_Submit_Name = 'STORE_TRANSACTIONS_SUBMIT_EDIT';

        $this->Index_Name = 'store_transactions_id';

        $this->Flash_Field = 'store_transactions_id';

        $this->Default_Where = '';  // additional search conditions

        $this->Default_Sort  = 'store_transactions_id';  // field for default table sort

        $this->Field_Titles = array(
            'store_transactions_id' => 'Store Transactions Id',
            'order_id' => 'Order Id',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'company_name' => 'Company Name',
            'email_address' => 'Email Address',
            'address1' => 'Address 1',
            'address2' => 'Address 2',
            'city' => 'City',
            'state' => 'State',
            'countries.country_name' => 'Country',
            'postal_code' => 'Postal Code',
            'phone' => 'Phone',
            'comments' => 'Comments',
            'card_type' => 'Card Type',
            'card_ending' => 'Card Ending',
            'card_name' => 'Card Name',
            'card_address1' => 'Card Address 1',
            'card_address2' => 'Card Address 2',
            'card_city' => 'Card City',
            'card_state' => 'Card State',
            'card_country' => 'Card Country',
            'card_postal_code' => 'Card Postal Code',
            'card_phone' => 'Card Phone', 

            "(SELECT GROUP_CONCAT(CONCAT('&bull;&nbsp;', `store_transaction_items`.`item_number`, ' &mdash; ', products.title) SEPARATOR '<br />') 
            FROM `store_transaction_items` LEFT JOIN products ON `products`.`pn`=`store_transaction_items`.`item_number`
                    WHERE `store_transaction_items`.`store_transactions_id`=`store_transactions`.`store_transactions_id`) AS ITEMS" => 'Items',

            
            'bill_total' => 'Bill Total',
            'items_total' => 'Items Total',
            'shipping_total' => 'Shipping Total',
            'tax_total' => 'Tax Total',
            'store_transactions.active' => 'Active',
            'store_transactions.updated' => 'Updated',
            'store_transactions.created' => 'Created'
        );
        
        $this->Join_Array = array(
            'countries' => 'LEFT JOIN countries ON countries.country_code = store_transactions.country_code',
        );


        $this->Default_Fields = 'order_id,first_name,last_name,city,state,country_name,bill_total';

        $this->Default_Sort = 'store_transactions_id DESC';

    } // -------------- END __construct --------------


    public function SetFormArrays()
    {
        $base_array = array(
            "form|$this->Action_Link|post|db_edit_form",
            'text|Order Id|order_id|N|40|40',
            'text|First Name|first_name|N|40|40',
            'text|Last Name|last_name|N|40|40',
            'text|Company Name|company_name|N|40|40',
            'email|Email Address|email_address|N|60|80',
            'text|Address 1|address1|N|60|80',
            'text|Address 2|address2|N|60|80',
            'text|City|city|N|60|80',
            'countrystate|Country|country_code:state|N|',
            'text|Postal Code|postal_code|N|20|20',
            'phone|Phone|phone|N',
            'textarea|Comments|comments|N|60|4',
            'select|Card Type|card_type|N||Visa=Visa|MasterCard=MasterCard|Amex=American Express|Discover=Discover',
            'integer|Card Ending|card_ending|N|4|4',
            'text|Card Name|card_name|N|60|80',
            'text|Card Address 1|card_address1|N|60|80',
            'text|Card Address 2|card_address2|N|60|80',
            'text|Card City|card_city|N|60|80',
            'countrystate|Card Country|card_country:card_state|N|',
            'text|Card Postal Code|card_postal_code|N|20|20',
            'phone|Card Phone|card_phone|N|',            
            'dollar|Bill Total|bill_total|N|10|10',
            'dollar|Items Total|items_total|N|10|10',
            'dollar|Shipping Total|shipping_total|N|10|10',
            'dollar|Tax Total|tax_total|N|10|10',
        );

        if ($this->Action == 'ADD') {
            $base_array[] = "submit|Add Record|$this->Add_Submit_Name";
            $base_array[] = 'endform';
            $this->Form_Data_Array_Add = $base_array;
        } else {
            $base_array[] = 'checkbox|Active|active||1|0';
            $base_array[] = "submit|Update Record|$this->Edit_Submit_Name";
            $base_array[] = 'endform';
            $this->Form_Data_Array_Edit = $base_array;
        }
    }
    
    // ----------- Process a record table cell before it is output when viewing a record  ---------------
    public function ProcessRecordCell($field, &$value, &$td_options)
    {
        if ($field == 'ITEMS') {
            $td_options = 'style="white-space:nowrap;"';
        }
        return;
    }

    // ----------- Process a record table cell before it is output when viewing a table  ---------------
    public function ProcessTableCell($field, &$value, &$td_options, $id='')
    {
        parent::ProcessTableCell($field, $value, $td_options, $id);
        if (($field == 'active') and ($value == 'No')) $td_options = 'style="background-color:#f00; color:#fff;"';
                if ($field == 'ITEMS') {
            $td_options = 'style="white-space:nowrap;"';
        }
        return;
    }


}  // -------------- END CLASS --------------



