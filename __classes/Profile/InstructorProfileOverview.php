<?php
class Profile_InstructorProfileOverview
{
    public $WH_ID                           = 0;

    public $Show_Query                      = false;
    
    public $Ajax_Page_Link                  = '/office/AJAX/command_central/instructor_profile';

    public $Ico_Email                       = null;
    public $Ico_Chat                        = null;
    public $Ico_Unknown                     = null;
    public $Ico_Logout                      = null;
    public $Ico_Lock                        = null;
    public $Ico_Yes                         = null;
    public $Ico_No                          = null;
    
    public $Show_Profile                    = true;
    public $Show_Profile_Public             = true;
    public $Show_Session                    = true;
    public $Show_Chat                       = true;
    public $Show_Session_Search             = false;
    public $Show_Logout                     = true;
    public $Show_Email_Subscription         = true;
    public $Show_Change_Password            = true;
    
    public function  __construct($wh_id=0)
    {
        $this->SetSQL();
        $this->WH_ID = $wh_id;
		
        
		# INITIALIZE GLOBAL VARIABLES
        # ========================================================================
		$this->Ico_Email 	= $GLOBALS['ICO_EMAIL'];
		$this->Ico_Chat 	= $GLOBALS['ICO_CHAT'];
		$this->Ico_Unknown 	= $GLOBALS['ICO_UNKNOWN'];
        $this->Ico_Logout   = $GLOBALS['ICO_LOGOUT'];
        $this->Ico_Yes      = $GLOBALS['ICO_YES'];
        $this->Ico_No       = $GLOBALS['ICO_NO'];
        $this->Ico_Lock     = $GLOBALS['ICO_LOCK'];
        
        
    } // -------------- END __construct --------------

    public function SetSQL()
    {
        if (empty($this->SQL)) {
            $this->SQL = Lib_Singleton::GetInstance('Lib_Pdo');
        }
    }
    
    
    public function AddScript()
    {
        $script = "
            function InitializeOnReady_Profile_CustomerProfileOverview()
            {
            return;  // this should do something ???? <--- RAW (12-21) - This is a holding function for future scripts that haven't been implemented yet
            }
        ";
        AddScript($script);
    }
    
    
    public function ProfileBox($record)
    {
        $address = FormatAddress("<div>{$record['address_1']}\n{$record['address_2']}\n{$record['address_3']}\n{$record['city']}, {$record['state']}\n{$record['postal_code']}</div>");


        $contact = MakeTable(array(
            "ID:|{$record['wh_id']}",
            "Name:|{$record['contact_salutation']} {$record['first_name']} {$record['middle_name']} {$record['last_name']}",
            "|<br />",
            "Address:|{$address}",
            "|<br />",
            "Email:|{$record['email_address']}",
            "Phone:|{$record['phone_home']}",
        ));


        $other = MakeTable(array(
            "Skype U/N: |{$record['skype_username']}",
            "Skype Phone #: |{$record['skype_phone_number']}",
            "Timezone: |{$record['tz_name']}",
        ));
        //"Timezone: |{$record['tz_name']} (GMT {$record['tz_offset']})",
 
        $output = "
            <div>{$contact}</div>
            <br />
            <div>{$other}</div>
        ";

        return $output;
    }
    
    
    public function PublicProfileBox($record)
    {
        $contact = MakeTable(array(
            "ID:|{$record['wh_id']}",
            "Name:|{$record['first_name']} {$record['last_name']}",
        ));
 
        $output = "
            <div>{$contact}</div>
        ";

        return $output;
    }
    
    
    public function EmailSubscriptionsBox($record)
    {
        $user_subscriptions     = explode(',', $record['email_subscriptions']);
        $ico_yes                = "<img src='$this->Ico_Yes' alt='' />";
        $ico_no                 = "<img src='$this->Ico_No' alt='' />";
        
        $subscriptions = array();
        foreach ($GLOBALS['EMAIL_SUBSCRIPTIONS_INSTRUCTOR'] AS $val => $description) {
            
            if (in_array($val, $user_subscriptions)) {
                $subscriptions[] = "$ico_yes|$description";
            } else {
                $subscriptions[] = "$ico_no|$description";
            }
        }
        $subscriptions = MakeTable($subscriptions);
        
        $output = "
            <div style='width:200px; background-color:#eee; padding:3px;'>[T~CPO_001]</div>
            <br />
            <div>{$subscriptions}</div>
        ";

        return $output;
    }
    
    
    public function GetCustomerProfileContent($WH_ID='')
    {
		$wh_id          = ($WH_ID) ? $WH_ID : $this->WH_ID;
        $OBJ_CONTACTS   = new Profile_CustomerProfileContacts();
        $record         = $OBJ_CONTACTS->ListRecordSpecial($wh_id);
        return $this->ProfileBox($record);
    }
    
    
    public function GetCustomerEmailSubscriptions($WH_ID='')
    {
		$wh_id          = ($WH_ID) ? $WH_ID : $this->WH_ID;
        $OBJ_CONTACTS   = new Profile_CustomerProfileEmailSubscriptions();
        $record         = $OBJ_CONTACTS->ListRecordSpecial($wh_id);
        return $this->EmailSubscriptionsBox($record);
    }
    
    
    public function GetInstructorPublicProfileContent($WH_ID='')
    {
		$wh_id          = ($WH_ID) ? $WH_ID : $this->WH_ID;
        $OBJ_PROFILE    = new InstructorProfile_Edit();
        $record         = $OBJ_PROFILE->ListRecordSpecial($wh_id);
        return $this->PublicProfileBox($record);
    }   

    
    
    
    public function ExecuteAjax()
    {
		
	}
	
	
    public function AjaxHandle()
    {
	
    }
    
    
    public function Execute()
    {
		#printqn("<br /><p><a href=`#` class=`stdbuttoni` onclick=`return reloadCustomerProfile('');`>TEST CHAT</a></p>");
        #$tab_content_profile = $this->OBJ_CONTACTS->EditRecordSpecial($this->WH_ID);
        #$eq = EncryptQuery("class=Profile_CustomerProfileContacts;v1=$this->WH_ID");
        #$profile_link_2 = getClassExecuteLink($eq);
        
        
        $box_profile                = '';
        $box_profile_public         = '';
        $box_session                = '';
        $box_chat                   = '';
        $box_session_search         = '';
        $box_logout                 = '';
        $box_email_subscription     = '';
        $box_change_password        = '';
        
        #$GLOBALS['PAGE']['ajaxlink']
		$eq_WHID                                = EncryptQuery("wh_id=$this->WH_ID");
        $profile_link                           = $this->Ajax_Page_Link . ';action=customer_profile;eq='.$eq_WHID;
        $profile_public_link                    = $this->Ajax_Page_Link . ';action=customer_public_profile;eq='.$eq_WHID;
        $email_link                             = $this->Ajax_Page_Link . ';action=customer_email_subscriptions;eq='.$eq_WHID;
		
        $eq_CustomerProfileContacts             = EncryptQuery("class=Profile_CustomerProfileContacts;v1=$this->WH_ID");
        $eq_CustomerProfileEmailSubscriptions   = EncryptQuery("class=Profile_CustomerProfileEmailSubscriptions;v1=$this->WH_ID");
		$eq_CustomerProfilePassword             = EncryptQuery("class=Profile_CustomerProfilePassword;v1=$this->WH_ID");
        
        $TEMP_WHID = 666;
        $eq_InstructorProfileEdit               = EncryptQuery("class=InstructorProfile_Edit;v1=$TEMP_WHID");
        $eq_InstructorProfileView               = EncryptQuery("class=InstructorProfile_View;v1=$TEMP_WHID");
        
        AddScript("
		function reloadCustomerProfile() {
			$('#customer_profile_info').load('$profile_link');
		}
        function reloadInstructorPublicProfile() {
			$('#customer_public_profile_info').load('$profile_public_link');
		}
        function reloadEmailSubscriptions() {
			$('#customer_email_subscriptions_info').load('$email_link');
		}
        alert('asdffas');
		");
        
        
        if ($this->Show_Profile) {
            $content                = "\n<div id='customer_profile_info'>\n" . $this->GetCustomerProfileContent() . "\n</div>\n";
            $edit_link              = "<a href='#' onclick=\"top.parent.appformCreateOverlay('INSTRUCTOR PROFILE', getClassExecuteLinkNoAjax('{$eq_CustomerProfileContacts}'), 'apps'); return false;\">[T~CPO_006]</a>";
            $box_profile            = AddBox('PERSONAL INFORMATION', $content, $edit_link) . '<br /><br />';
        }
        
        if ($this->Show_Profile_Public) {
            $content                = "\n<div id='customer_public_profile_info'>\n" . $this->GetInstructorPublicProfileContent() . "\n</div>\n";
            $edit_link              = "<a href='#' onclick=\"top.parent.appformCreateOverlay('INSTRUCTOR PROFILE', getClassExecuteLinkNoAjax('{$eq_InstructorProfileView}'), 'apps'); return false;\">View Profile</a>";
            $edit_link              .= "<br /><a href='#' onclick=\"top.parent.appformCreateOverlay('INSTRUCTOR PROFILE', getClassExecuteLinkNoAjax('{$eq_InstructorProfileEdit}'), 'apps'); return false;\">Edit Profile</a>";
            $edit_link              .= "<br /><a href='#' onclick=\"top.parent.appformCreateOverlay('INSTRUCTOR PROFILE', getClassExecuteLinkNoAjax('{$eq_InstructorProfileEdit}'), 'apps'); return false;\">profile as others see it   </a>";
            $box_profile_public     = AddBox('INSTRUCTOR PROFILE<br />[Viewed by Customers]', $content, $edit_link) . '<br /><br />';
        }
        
        
        if ($this->Show_Session_Search) {
            $content                = '[T~CPO_004]';
            $box_session_search     = AddBox('[T~CPO_005]', $content) . '<br /><br />';
        }
        
        
        if ($this->Show_Chat) {
            $OBJ_CHAT   = new Chat_Chat();
            $box_chat   = $OBJ_CHAT->OutputChatStatusBox() . '<br /><br />';
        }
        
        
        if ($this->Show_Session) {
            $content        = "<a href='#' onclick=\"setTabCustomerProfile(3, 'tab', 'tablink', 'tabselect'); return false;\">[T~CPO_012]</a>";
            $box_session    = AddBox_Type2('[T~CPO_013]', $content, '') . '<br /><br />';
        }
        
        
        if ($this->Show_Logout) {
            $content        = '<a href="/office/LOGOUT">[T~CPO_008]</a>';
            $box_logout     = AddBox_Type2('', $content, $this->Ico_Logout) . '<br /><br />';
        }
        
        
        if ($this->Show_Email_Subscription) {
            $content                    = "\n<div id='customer_email_subscriptions_info'>\n" . $this->GetCustomerEmailSubscriptions() . "\n</div>\n";
            $edit_link                  = "<a href='#' onclick=\"top.parent.appformCreateOverlay('[T~CPO_009]', getClassExecuteLinkNoAjax('{$eq_CustomerProfileEmailSubscriptions}'), 'apps'); return false;\">[T~CPO_010]</a>";
            $box_email_subscription     = AddBox('[T~CPO_011]', $content, $edit_link) . '<br /><br />';
        }
        
        
        if ($this->Show_Change_Password) {
            $content                    = "<a href='#' onclick=\"top.parent.appformCreateOverlay('Change Your Password', getClassExecuteLinkNoAjax('{$eq_CustomerProfilePassword}'), 'apps'); return false;\">Change Your Password</a>";
            $box_change_password        = AddBox_Type2('', $content, $this->Ico_Lock) . '<br /><br />';
        }

        
        $output = "
        <div class='customer_profile_header_text'>INSTRUCTOR PROFILE</div>
        <br /><br />
        <div>
            <div class='col' style='width:50px;'>
            &nbsp;
            </div>
            <div class='col'>
                {$box_profile}
                
                {$box_email_subscription}
            </div>
            <div class='col' style='width:50px;'>
            &nbsp;
            </div>
            <div class='col'>
                {$box_profile_public}
            </div>
            <div class='col' style='width:50px;'>
            &nbsp;
            </div>
            <div class='col'>
                {$box_session_search}
                
                {$box_logout}
                
                {$box_change_password}
                
                {$box_session}
                
                {$box_chat}
            </div>
            <div class='clear'></div>
        </div>
        <br /><br />

        ";

        $script = "InitializeOnReady_Profile_CustomerProfileOverview();";
        $output .= EchoScript($script);

        return $output;
    }


}  // -------------- END CLASS --------------