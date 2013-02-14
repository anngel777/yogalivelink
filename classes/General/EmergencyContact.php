<?php
class General_EmergencyContact extends BaseClass
{
    public $Log_Execute_Call            = true;     // TRUE = Save record of emergency contact to database
    public $Show_Query                  = false;    // TRUE = output the database queries ocurring on this page
    
    /*
    private $local_location             = "/office/AJAX/chat/chat_user.php";
    private $local_email_chat           = "/office/chat/chat_user_email";
    private $script_location            = "/office/AJAX/chat/chat_user.php?action=add_chat_content";
    private $script_location_notes      = "/office/AJAX/chat/chat_user.php?action=add_chat_notes";
    private $location_end_chat_user     = "/office/AJAX/chat/chat_user.php?action=end_chat_user";
    private $current_chat_id            = 0;
    private $current_chat_code          = '';
    */
    
    public $SQL                         = '';
    public $Wh_Id                       = null;
    public $Instructor_Wh_Id            = null;
    public $Sessions_Id                 = null;
    public $Room_Id                     = null;
    
    
    public function  __construct()
    {
        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Description' => 'Output the emergency contact information to the chat window - for instructor use',
        );
        
        $this->SQL = Lib_Singleton::GetInstance('Lib_Pdo');
        
        $this->SetParameters(func_get_args());
        $this->Wh_Id                = ($this->GetParameter(0)) ? $this->GetParameter(0) : null;
        $this->Sessions_Id          = ($this->GetParameter(1)) ? $this->GetParameter(1) : null;
        $this->Instructor_Wh_Id     = ($this->GetParameter(2)) ? $this->GetParameter(2) : null;
        $this->Room_Id              = ($this->GetParameter(3)) ? $this->GetParameter(3) : null;
        
    } // -------------- END __construct --------------

    
    public function LogRecordToDatabase() 
    {
        # FUNCTION :: Store a database record that an emergency contact has been displayed
        
        $db_record = array(            
            'wh_id'             => $this->Wh_Id,
            'instructor_wh_id'  => $this->Instructor_Wh_Id,
            'sessions_id'       => $this->Sessions_Id,
        );
        $this->AddRecordLoc($db_record, $GLOBALS['TABLE_emergency_requests']);
    }
    
    private function AddRecordLoc($db_record, $TABLE) 
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
            'table'     => $TABLE,
            'keys'      => $keys,
            'values'    => $values,
        ));
        $this->Last_Sessions_Id = $this->SQL->Last_Insert_Id;
        if ($this->Show_Query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
    }
    
    public function OutputEmergencyBox($WHID=null, $SESSION_ID=null, $INSTRUCTOR_WHID=null, $ROOM_ID=null)
    {
        $eq = EncryptQuery("class=General_EmergencyContact;v1={$WHID};v2={$SESSION_ID};v3={$INSTRUCTOR_WHID};v4={$ROOM_ID}");
    
        if ($WHID && $SESSION_ID && $INSTRUCTOR_WHID && $ROOM_ID) {
            $message    = 'Clicking will be logged';
            //$message   .=  "<br />[<a href=\"#\" onclick=\"javascript:parent.window.open('/office/chat/chat_user;resize=true','blank','toolbar=no,width=250,height=250,location=no')\">CLICK HERE</a>]";
            $message   .=  "<br />[<a href=\"#\" onclick=\"LaunchEmergencyNewWindow('{$eq}')\">CLICK HERE</a>]";
            
            $output     = AddBox_Type2('Emergency', $message, $GLOBALS['ICO_MEDICAL']);
        } else {
            $output     = 'NO CUSTOMER INFO';
        }
        
        AddScript("
            function LaunchEmergencyNewWindow(eq) {
                var link    = getClassExecuteLinkNoAjax(eq) + ';template=blank;pagetitle=Emergency Contact Information';
                var width   = 350;
                var height  = 600;
                parent.window.open(link,'_blank_emergency','toolbar=no,width='+width+',height='+height+',location=no');
            }
        ");
        
        return $output;
    }
    
    
    
    public function Execute()
    {
        $style      = "border:1px solid #fff; padding:10px; font-size:14px; font-weight:bold;";
        $style_2    = "border:1px solid #fff; padding:10px; font-size:14px; font-weight:normal; background-color:#ccc; color:#000;";
    
        if ($this->Log_Execute_Call) {
            echo "<div style='{$style}'>Your request for emergency contact has been logged to database.</div>";
            $this->LogRecordToDatabase();
        }
        
        $wh_id          = $this->Wh_Id;
        $OBJ_CONTACTS   = new Profile_CustomerProfileContacts();
        $record         = $OBJ_CONTACTS->ListRecordSpecial($wh_id, true);
        
        $output     = "<br /><div style='padding-left:20px;'>";
        $output    .= $this->ProfileBox($record);
        $output    .= '</div>';
        
        $OBJ_HELP       = new Website_HelpcenterFAQs();
        $instructions   = $OBJ_HELP->GetSingleFAQ(32);
            
        $output    .= "<br />
            <div style='{$style_2} height:250px; overflow:scroll; padding:10px;'>
                <div style='padding:10px;'>
                {$instructions['answer']}
                </div>            
            </div>";
        
        AddStyle("
            body { background-color:#990000; padding:20px; color:#fff; }
        ");
        
        echo $output;
    }
    
    public function ProfileBox($record)
    {
        $address = FormatAddress("<div>{$record['address_1']}\n{$record['address_2']}\n{$record['address_3']}\n{$record['city']}, {$record['state']}\n{$record['postal_code']}</div>");
        
        $contact = MakeTable(array(
            
            "Room ID|{$this->Room_Id}",
            "User ID|{$record['wh_id']}",
            "|<br />",
            "Name|{$record['contact_salutation']} {$record['first_name']} {$record['middle_name']} {$record['last_name']}",
            "|<br />",
            "Address|{$address}",
            "|<br />",
            "Email|{$record['email_address']}",
            "Phone|{$record['phone_home']}",
        ));
        
        $output = "
            <div>{$contact}</div>
        ";
        
        return $output;
    }
    
}  // -------------- END CLASS --------------