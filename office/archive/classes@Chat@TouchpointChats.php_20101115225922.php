<?php

// FILE: class.TouchpointChats.php

class Chat_TouchpointChats extends Chat_TouchpointChatBase
{
    public $DIALOGID                = 0;
    
    public $IsTesting               = true;
    public $ShowArray               = false;
    public $NewChatUser             = false;
    public $EndChatUser             = false;
    
    public $NewChatUserCode         = 0;
    public $current_chat_id         = 0;
    public $current_chat_code       = '';
    public $current_chat_email      = '';
    
    //private $TableChatSettings      = 'touchpoint_chat_settings';
    //private $TableChats             = 'touchpoint_chats';
    public $SuccessRedirectChatPage = 'chat_user';
    
    //public $reset_settings          = false;
    //private $settings               = array();
    
    
    public function  __construct()
    {
        parent::__construct();

        #$this->GetSettings();
  
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
        
        if ($this->IsTesting) {
            $this->Default_Values = array(
                'user_name'     => 'Test Testerson',
                'user_email'    => 'Test.Testerson@mailwh.com',
                'wh_id'         => '666',
                'chat_start_timestamp' => date("Y-m-d  H:i:s"),
            );
        } else {
            $this->Default_Values = array(
                'user_name'     => "{$_SESSION['USER_LOGIN']['LOGIN_RECORD']['first_name']} {$_SESSION['USER_LOGIN']['LOGIN_RECORD']['last_name']}",
                'user_email'    => $_SESSION['USER_LOGIN']['LOGIN_RECORD']['email_address'],
                'wh_id'         => $_SESSION['USER_LOGIN']['LOGIN_RECORD']['wh_id'],
                'chat_start_timestamp' => date("Y-m-d  H:i:s"),
            );
        }
        
        
    } // -------------- END __construct --------------

    /*
    public function GetSettings()
    {
        if (Session('settings_touchpoint_chat') && (!$this->reset_settings)) {
            $this->settings = Session('settings_touchpoint_chat');
        } else {
            
            $records = $this->SQL->GetArrayAll(array(
                'table' => $this->TableChatSettings,
                'keys'  => 'setting_name,setting_value',
                'where' => 'active=1',
            ));
            
            foreach ($records as $record) 
            {
                $name   = $record['setting_name'];
                $value  = $record['setting_value'];
                $this->settings[$name] = $value;
            }
            
            $_SESSION['settings_touchpoint_chat'] = $this->settings;
        }
    }
    */
    
    
    
    
   
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
            'hidden|chat_start_timestamp',
            'hidden|wh_id',
            'hidden|line_count|1',
            'hidden|locked|0',
            'hidden|active|1',
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
                'text|Chat Start Timestamp|chat_start_timestamp|N|45|255',
                'text|Chat End Timestamp|chat_end_timestamp|N|45|255',
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
        
        
        # MODIFY THE CHAT REQUEST SO THAT IT IS JSON FORMATTED
        # ============================================================
        if ($this->NewChatUser) {
            $message    = addslashes(htmlentities(str_replace('|', '-', $FormArray['chat'])));
            $user       = addslashes(htmlentities(str_replace('|', '-', $FormArray['user_name'])));
            $time       = time();
            $addr       = $_SERVER['REMOTE_ADDR'];
            $chat       = "{$this->settings['chat_newline_char']}$time{$this->settings['chat_section_char']}$user{$this->settings['chat_section_char']}$message{$this->settings['chat_section_char']}$addr";
            $FormArray['chat'] = $chat;
        }
        
        
        # CREATE A RESERVATION_CODE (IF THERE ISN'T ONE) AND INJECT IT INTO THE FORM ARRAY
        # ============================================================
        if (!$FormArray['touchpoint_chats_code']) {
            do {
                # generate a code
                $code = $this->GenerateCode();
                
                # verify code is unique
                $unique = $this->SQL->IsUnique(array(            
                    'table' => $this->Table,
                    'key'   => 'touchpoint_chats_code',
                    'value' => $code,
                ));
            } while (!$unique);
            
            $FormArray['touchpoint_chats_code'] = $code;
            $this->NewChatUserCode = $code;
        }
        
        return $FormArray;
    }
    
    
    public function SuccessfulAddRecord()
    {
        if ($this->NewChatUser) {
            header("Location: {$this->SuccessRedirectChatPage};code={$this->NewChatUserCode};DIALOGID={$this->DIALOGID}");
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