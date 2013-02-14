<?php

// FILE: class.TouchpointChats.php

class Chat_TouchpointChats extends BaseClass
{

    public $ShowArray               = false;
    public $NewChatUser             = false;
    public $NewChatUserCode         = 0;
    private $TableChatSettings      = 'touchpoint_chat_settings';
    
    public function  __construct()
    {
        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => '',
            'Description' => 'Create and manage touchpoint_chats',
            'Created'     => '2010-09-22',
            'Updated'     => '2010-09-22'
        );

        $this->Table  = 'touchpoint_chats';

        $this->Add_Submit_Name  = 'TOUCHPOINT_CHATS_SUBMIT_ADD';
        $this->Edit_Submit_Name = 'TOUCHPOINT_CHATS_SUBMIT_EDIT';

        $this->Index_Name = 'touchpoint_chats_id';

        $this->Flash_Field = 'touchpoint_chats_id';

        $this->Default_Where = '';  // additional search conditions

        $this->Default_Sort  = 'touchpoint_chats_id';  // field for default table sort

        $this->Field_Titles = array(
            'touchpoint_chats_id' => 'Touchpoint Chats Id',
            'touchpoint_chats_code' => 'Touchpoint Chats Code',
            'wh_id' => 'Wh Id',
            'shopping_order_id' => 'Shopping Order Id',
            'category' => 'Category',
            'chat' => 'Chat',
            'line_count' => 'Line Count',
            'locked' => 'Locked',
            'notes' => 'Notes',
            'user_name' => 'User Name',
            'user_email' => 'User Email',
            'admin_contacts_id' => 'Admin Contacts Id',
            'admin_name' => 'Admin Name',
            'admin_email' => 'Admin Email',
            'chat_start_timestamp' => 'Chat Start Timestamp',
            'chat_end_timestamp' => 'Chat End Timestamp',
            'followup_required' => 'Followup Required',
            'active' => 'Active',
            'updated' => 'Updated',
            'created' => 'Created'
        );


        $this->Default_Fields = 'touchpoint_chats_code,wh_id,shopping_order_id,category,chat,line_count,locked,notes,user_name,user_email,admin_contacts_id,admin_name,admin_email,chat_start_timestamp,chat_end_timestamp,followup_required';

        $this->Unique_Fields = '';

    } // -------------- END __construct --------------

    public function AddRecordUserChat()
    {
        # GET ALL THE CATEGORIES
        # ============================================================
        $record = $this->SQL->GetRecord(array(
            'table' => $this->TableChatSettings,
            'keys'  => 'setting_value',
            'where' => "setting_name='categories' AND active=1",
        ));
        
        $categories = explode('|', $record['setting_value']);
        
        $category_types = '';
        foreach ($categories AS $category) {
            $value              = strtolower($category);
            $display            = ucwords($category);
            $category_types    .= "$value=$display|";
        }
        $category_types = substr($category_types,0,-1);
        
        
        # CREATE THE BASE ARRAY
        # ============================================================
        $base_array = array(
            "form|$this->Action_Link|post|db_edit_form",
            
            'text|Your Name|user_name|N|60|255',
            'text|Your Email Address|user_email|N|60|255',
            #'text|Shopping Order Id|shopping_order_id|N|11|11',
            "select|Category|category|N||$category_types",
            
            'textarea|Chat|chat|N|60|4',
            
            'hidden|touchpoint_chats_code',
            'hidden|line_count|1',
            'hidden|locked|0',
            'hidden|active|1',
            
            'text|Chat Start Timestamp|chat_start_timestamp|N||',
            
        );

        
        $base_array[] = "submit|Add Record|$this->Add_Submit_Name";
        $base_array[] = 'endform';
        $this->Form_Data_Array_Add = $base_array;
        
    }

    public function SetFormArrays()
    {
        if ($this->NewChatUser) {
            $this->AddRecordUserChat();
        } else {
        
            $base_array = array(
                "form|$this->Action_Link|post|db_edit_form",
                'text|Touchpoint Chats Code|touchpoint_chats_code|N|6|6',
                'text|Wh Id|wh_id|N|11|11',
                'text|Shopping Order Id|shopping_order_id|N|11|11',
                'text|Category|category|N|45|45',
                'textarea|Chat|chat|N|60|4',
                'text|Line Count|line_count|N|11|11',
                'text|Locked|locked|N|4|4',
                'textarea|Notes|notes|N|60|4',
                'text|User Name|user_name|N|60|255',
                'text|User Email|user_email|N|60|255',
                'text|Admin Contacts Id|admin_contacts_id|N|45|45',
                'text|Admin Name|admin_name|N|45|45',
                'text|Admin Email|admin_email|N|45|45',
                'text|Chat Start Timestamp|chat_start_timestamp|N||',
                'text|Chat End Timestamp|chat_end_timestamp|N||',
                'checkbox|Followup Required|followup_required||1|0',
                'checkbox|Active|active||1|0',
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
    }
    
    
    
    public function PostProcessFormValues($FormArray)
    {
        // extend this function to process values -- simply return the array back
        if ($this->ShowArray) echo ArrayToStr($FormArray);
        
        
        # CREATE A RESERVATION_CODE (IF THERE ISN'T ONE) AND INJECT IT INTO THE FORM ARRAY
        # ============================================================
        if (!$FormArray['touchpoint_chats_code']) {
            $code = $this->GenerateCode();
            $FormArray['touchpoint_chats_code'] = $code;
            $this->NewChatUserCode = $code;
        }
        
        return $FormArray;
    }
    
    
    public function SuccessfulAddRecord()
    {
        if ($this->NewChatUser) {
            header("Location: chat_user;code={$this->NewChatUserCode}");
        }
    }
    
    
    
    private function GenerateCode($STR_LENGTH=6)
    {
        $characters = array(
        "A","B","C","D","E","F","G","H","J","K","L","M",
        "N","P","Q","R","S","T","U","V","W","X","Y","Z",
        "2","3","4","5","6","7","8","9");

        //make an "empty container" or array for our keys
        $keys = array();

        //first count of $keys is empty so "1", remaining count is 1-6 = total 7 times
        while(count($keys) < $STR_LENGTH) {
            //"0" because we use this to FIND ARRAY KEYS which has a 0 value
            //"-1" because were only concerned of number of keys which is 32 not 33
            //count($characters) = 33
            $x = mt_rand(0, count($characters)-1);
            if(!in_array($x, $keys)) {
               $keys[] = $x;
            }
        }
        
        $random_chars = '';
        foreach($keys as $key){
           $random_chars .= $characters[$key];
        }
        
        return $random_chars;
    }


}  // -------------- END CLASS --------------