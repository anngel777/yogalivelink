<?php

# ========================================================================================
# CLASS :: Shows the customer or instructor profile page with all various sections
# ========================================================================================

class Profile_ProfileOverview
{
    public $WH_ID                           = 0;

    public $Show_Query                      = false;
    
    public $Is_Instructor                           = false;
    public $Instructor_Account_Limited              = false; // TRUE = limited account - won't show everything on the profile page
    public $Force_Instructor_Account_Limited        = false; // TRUE = override limited instructor limits
    public $Force_Instructor_Account_Not_Limited    = false; // TRUE = force instructor to be limited
    
    public $Have_Session_Customer_Not_Rated         = false;
    
    public $Administrator_Editing_Record            = false;
    
    //public $Ajax_Page_Link                  = '/office/AJAX/command_central/customer_profile';
    public $Ajax_Page_Link                  = '';

    public $Ico_Email                       = null;
    public $Ico_Chat                        = null;
    public $Ico_Unknown                     = null;
    public $Ico_Logout                      = null;
    public $Ico_Lock                        = null;
    public $Ico_Yes                         = null;
    public $Ico_No                          = null;
    
    public $Show_Profile                    = true;
    public $Show_Session                    = true;
    public $Show_Chat                       = false;
    public $Show_Session_Search             = true;
    public $Show_Logout                     = true;
    public $Show_Email_Subscription         = false;
    public $Show_Change_Password            = true;
    public $Show_Instructor_Profile         = true;
    public $Show_Instructor_Checklist       = true;
    public $Show_Instructor_Limited_Message = true;
    public $Show_Intake_Forms               = true;
    public $Show_Session_Ratings            = true;
    
    
    public $Profile_Inactive                = false;
    
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
        
        $this->Force_Instructor_Account_Not_Limited     = Get('unlimited');
        //$this->Administrator_Editing_Record             = ($GLOBALS['IS_SUPERUSER'] || $GLOBALS['IS_ADMINISTRATOR']) ? true : false;
        
        global $PAGE;
        $this->Ajax_Page_Link = $PAGE['ajaxlink'];
        
        
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
    
    public function IntakeFormsBox($record='')
    {
        $msg = "Filling out your Health/Fitness Information form helps your Instructor tailor the best session for you. Update your information here.";
        $table = MakeTable(array(
            "ID|$msg",
            "|<br />",
            "Email|",
        ));
        
        
        
        $output = "<div>{$msg}</div><br />";
        
        return $output;
    }
    
    public function ProfileBox($record)
    {
        $address = FormatAddress("<div>{$record['address_1']}\n{$record['address_2']}\n{$record['address_3']}\n{$record['city']}, {$record['state']}\n{$record['postal_code']}</div>");


        $contact = MakeTable(array(
            "ID|{$record['wh_id']}",
            "Name|{$record['contact_salutation']} {$record['first_name']} {$record['middle_name']} {$record['last_name']}",
            "|<br />",
            "Address|{$address}",
            "|<br />",
            "Email|{$record['email_address']}",
            "Phone|{$record['phone_home']}",
        ));


        $other = MakeTable(array(
            "Timezone |{$record['tz_display']}",
        ));
 
        $output = "
            <div>{$contact}</div>
            <br />
            <div>{$other}</div>
        ";

        return $output;
    }
    
    
    public function EmailSubscriptionsBox($record)
    {
        global $AJAX;
        $this->Is_Instructor = ($AJAX && Get('instructor')) ? true : $this->Is_Instructor;
    
        $Email_Subscriptions = ($this->Is_Instructor) ? $GLOBALS['EMAIL_SUBSCRIPTIONS_INSTRUCTOR'] : $GLOBALS['EMAIL_SUBSCRIPTIONS'];
        
        $user_subscriptions     = explode(',', $record['email_subscriptions']);
        $ico_yes                = "<img src='$this->Ico_Yes' alt='' />";
        $ico_no                 = "<img src='$this->Ico_No' alt='' />";
        
        $subscriptions = array();
        foreach ($Email_Subscriptions AS $val => $description) {
            
            if (in_array($val, $user_subscriptions)) {
                $subscriptions[] = "$ico_yes|$description";
            } else {
                $subscriptions[] = "$ico_no|$description";
            }
        }
        $subscriptions = MakeTable($subscriptions);
        
        $output = "
            <br />
            <div>{$subscriptions}</div>
        ";

        return $output;
    }
    
    
    public function InstructorChecklistBox($record, $field_titles)
    {
        $ico_yes                = "<img src='$this->Ico_Yes' alt='' />";
        //$ico_no                 = "<img src='$this->Ico_No' alt='' />";
        $ico_no                 = "<img src='/images/spacer.gif' height='25' width='24' alt='' />";
        
        /*
        echo "<br />";
        echo ArrayToStr($record);
        echo "<br />";
        echo "<br />";
        echo ArrayToStr($field_titles);
        echo "<br />";
        */
        
        $checklist = array();
        foreach ($record AS $field => $value) {
            $ico            = ($value == 0) ? $ico_no : $ico_yes;
            $description    = $field_titles[$field];
            $checklist[]    = "$ico|$description";
        }
        $table = MakeTable($checklist);
        
        $output = "
            <br />
            <div>{$table}</div>
        ";

        return $output;
    }
    
    
    
    
    
    public function GetCustomerProfileContent($WH_ID='')
    {
        # =========================================================================================
        # FUNCTION :: Called from "Profile_CustomerProfile" to reload content after update
        # =========================================================================================
        
		$wh_id          = ($WH_ID) ? $WH_ID : $this->WH_ID;
        $OBJ_CONTACTS   = new Profile_CustomerProfileContacts();
        $record         = $OBJ_CONTACTS->ListRecordSpecial($wh_id, true);
        
        # determine if record is inactive
        $this->Profile_Inactive     = ($record) ? false : true;
        if ($this->Profile_Inactive && $GLOBALS['IS_SUPERUSER']) {
            $record     = $OBJ_CONTACTS->ListRecordSpecialInactive($wh_id, true);
        }
        
        return $this->ProfileBox($record);
    }
    
    public function GetCustomerEmailSubscriptions($WH_ID='', $INSTRUCTOR='')
    {
        # =========================================================================================
        # FUNCTION :: Called from "Profile_CustomerProfile" to reload content after update
        # =========================================================================================
        
        if ($INSTRUCTOR) $this->Is_Instructor = $INSTRUCTOR;
        
		$wh_id          = ($WH_ID) ? $WH_ID : $this->WH_ID;
        $OBJ_CONTACTS   = new Profile_EmailSubscriptions();
        $record         = $OBJ_CONTACTS->ListRecordSpecial($wh_id);
        return $this->EmailSubscriptionsBox($record);
    }
    
    public function GetInstructorPublicProfile($WH_ID='')
    {
        # =========================================================================================
        # FUNCTION :: Called from "Profile_CustomerProfile" to reload content after update
        # =========================================================================================
        
        $eq_InstructorProfileEdit       = EncryptQuery("class=InstructorProfile_Edit;v1=$this->WH_ID");
        $edit_link                      = "<a href='#' onclick=\"top.parent.appformCreateOverlay('Edit Your Public Profile', getClassExecuteLinkNoAjax('{$eq_InstructorProfileEdit}'), 'apps'); return false;\">edit my public profile</a>";
        
        $OBJ2 = new InstructorProfile_View();
        $OBJ2->check_pending_status = true;
        $OBJ2->Show_Profile_Status = true;
        $wh_id = ($WH_ID) ? $WH_ID : $this->WH_ID;
        $output = $OBJ2->InitializeProfileWindow($wh_id, true);
        return $output;
    }

    public function GetInstructorChecklist($WH_ID='')
    {
        # =========================================================================================
        # FUNCTION :: Gets the instructor checklist
        # =========================================================================================
        
		$wh_id          = ($WH_ID) ? $WH_ID : $this->WH_ID;
        $OBJ_CHECKLIST  = new Profile_InstructorChecklist();
        $record         = $OBJ_CHECKLIST->GetInstructorChecklist($wh_id);
        $fields         = $OBJ_CHECKLIST->Field_Titles;
        
        return $this->InstructorChecklistBox($record, $fields);
    }
    
    public function GetLeftColumnContent($WH_ID='')
    {
        # =========================================================================================
        # FUNCTION :: Called from "Profile_CustomerProfile" to reload content after update
        # =========================================================================================
        
        return $this->TodaysSessions();
    }
    
    public function GetSessionsNotRatedContent($WH_ID='')
    {
        $wh_id              = ($WH_ID) ? $WH_ID : $this->WH_ID;
        
        $OBJ_RATE           = new Profile_CustomerProfileSessions();
        $OBJ_RATE->WH_ID    = $wh_id; //$_SESSION['USER_LOGIN']['LOGIN_RECORD']['wh_id'];
        $OBJ_RATE->AddScript();
        
        $OBJ_RATE->Format_Session_Customer_Not_Rated    = true;
        $OBJ_RATE->show_instructor_profile              = false;
        $OBJ_RATE->show_session_id                      = false;
        $OBJ_RATE->show_launch_session                  = false;
        $OBJ_RATE->show_test_session                    = false;
        $OBJ_RATE->show_ical                            = false;
        $OBJ_RATE->show_user_rate_session               = true;
        $OBJ_RATE->show_user_cancel_session             = false;
        $OBJ_RATE->show_instructor_rate_session         = false;
        $OBJ_RATE->show_instructor_upload_video         = false;
        $OBJ_RATE->show_session_details                 = false;
        $OBJ_RATE->show_intake_form                     = false;
        $OBJ_RATE->show_admin_datetime                  = false;
        
        $sessions = $OBJ_RATE->GetAllSessionsNotRatedByCustomer();
        
        $content = '';
        if ($OBJ_RATE->Have_Session_Customer_Not_Rated) {
            $this->Have_Session_Customer_Not_Rated = true;
            
            $content    = "Filling out the session ratings helps YogaLiveLink.com maintain the highest quality of yoga teaching. Please take a moment to complete the ratings for your completed session below.";
            $content   .= "<br /><br />";
            $content   .= $sessions;
        }
        
        return $content;
    }
    
    
    
    
    
    public function ExecuteAjax()
    {
		
	}
	
	
    public function AjaxHandle()
    {
		switch (Get('action')) {
			case 'customer_profile':
				$QDATA = GetEncryptQuery('eq');
				$WH_ID = ArrayValue($QDATA, 'wh_id');
				echo $this->GetCustomerProfileContent($WH_ID);			
			break;
            
            case 'customer_email_subscriptions':
				$QDATA = GetEncryptQuery('eq');
				$WH_ID = ArrayValue($QDATA, 'wh_id');
				echo $this->GetCustomerEmailSubscriptions($WH_ID);			
			break;
            
            case 'instructor_public_profile':
				$QDATA = GetEncryptQuery('eq');
				$WH_ID = ArrayValue($QDATA, 'wh_id');
				echo $this->GetInstructorPublicProfile($WH_ID);			
			break;
            
            case 'left_column':
				$QDATA = GetEncryptQuery('eq');
				$WH_ID = ArrayValue($QDATA, 'wh_id');
				echo $this->GetLeftColumnContent($WH_ID);			
			break;
            
            case 'sessions_not_rated':
				$QDATA = GetEncryptQuery('eq');
				$WH_ID = ArrayValue($QDATA, 'wh_id');
				echo $this->GetSessionsNotRatedContent($WH_ID);			
			break;
            
			default:
				//$this->LoadTabContent($action);
			break;
		}
    }
    
    
    public function TodaysSessions()
    {
        $output = '';
        
        if (!$this->Instructor_Account_Limited) {
            $OBJ_SESS   = new Profile_CustomerProfileSessions();
            $OBJ_SESS->Create_Testing_Session = Get('havesessiontoday');
            $OBJ_SESS->Is_Instructor = $this->Is_Instructor;
            $OBJ_SESS->AddScript();
            $OBJ_SESS->WH_ID = $this->WH_ID;
            
            $output     = $OBJ_SESS->TodaysSessions();
            
            # OUTPUT INSTRUCTOR HANDBOOK
            if ($this->Is_Instructor) {
                $link       = $GLOBALS['INSTRUCTOR_HANDBOOK_LINK'];
                $img        = $GLOBALS['INSTRUCTOR_HANDBOOK_IMAGE'];
                $title      = $GLOBALS['INSTRUCTOR_HANDBOOK_TITLE'];
                $image      = "<img src='{$img}' alt='Download PDF' border='0' height='48' />";
                $output    .= "
                    <div class='clear'></div>
                    <br /><br />
                    <div class='yogabox_outter_wrapper_type2' style=''>
                    <table cellpadding='0' cellspacing='5' border='0'>
                    <tr>
                    <td valign='center'><a href='{$link}' target='_blank' >{$image}</a></td>
                    <td valign='center'><a href='{$link}' target='_blank' >{$title}</a></td>
                    </tr>
                    </table>
                    </div>";
            }
            
        } else {
            $output     = $this->ShowInstructorChecklist();
        }
        
        return $output;
    }
    
    public function ShowInstructorChecklist()
    {
        if ($this->Is_Instructor && $this->Show_Instructor_Checklist) {
            $header                     = "Note: This list is for reference only. Changes to this list are managed by the system administrator.\n";
            $content                    = "{$header}\n<div id='instructor_checklist_info'>\n" . $this->GetInstructorChecklist() . "\n</div>\n";
            $edit_link                  = "";
            $box_instructor_checklist   = AddBox('account status', $content, $edit_link) . '<br /><br />';
            
            return $box_instructor_checklist;
        }        
    }
    
    public function Execute()
    {
        # DETERMINE IF THIS IS AN INSTRUCTOR
        # ========================================================================
        //$this->Is_Instructor = $_SESSION['USER_LOGIN']['LOGIN_RECORD']['type_instructor'];
        
        
        # SET LIMITED INSTRUCTOR MODE
        # ========================================================================
        $this->Instructor_Account_Limited = $_SESSION['USER_LOGIN']['LOGIN_RECORD']['instructor_account_limited'];
		$this->Instructor_Account_Limited = ($this->Force_Instructor_Account_Limited) ? true : $this->Instructor_Account_Limited;
        $this->Instructor_Account_Limited = ($this->Force_Instructor_Account_Not_Limited) ? false : $this->Instructor_Account_Limited;
        
        
        
        
        
        $box_profile                            = '';
        $box_session                            = '';
        $box_chat                               = '';
        $box_session_search                     = '';
        $box_logout                             = '';
        $box_email_subscription                 = '';
        $box_change_password                    = '';
        $box_instructor_profile                 = '';
        $box_instructor_limited_message         = '';
        $box_intake_forms                       = '';
        $box_sessions_not_rated                 = '';
        
        $eq_CustomerProfileContacts             = EncryptQuery("class=Profile_ContactInformation;v1=$this->WH_ID");
        $eq_CustomerProfileEmailSubscriptions   = EncryptQuery("class=Profile_EmailSubscriptions;v1=$this->WH_ID;v2=$this->Is_Instructor");
		$eq_CustomerProfilePassword             = EncryptQuery("class=Profile_Password;v1=$this->WH_ID");
        $eq_InstructorProfileEdit               = EncryptQuery("class=InstructorProfile_Edit;v1=$this->WH_ID");
        $eq_CustomerProfileIntakeFormStandard   = EncryptQuery("class=Profile_FormStandardIntake;v1=;v2=$this->WH_ID");
        $eq_CustomerProfileIntakeFormTherapy    = EncryptQuery("class=Profile_FormTherapyIntake;v1=;v2=$this->WH_ID");
        
        $profile_link                           = $this->Ajax_Page_Link . ";action=customer_profile;eq=" . EncryptQuery("wh_id=$this->WH_ID");
        $email_link                             = $this->Ajax_Page_Link . ";action=customer_email_subscriptions;eq=" . EncryptQuery("wh_id=$this->WH_ID;instructor=$this->Is_Instructor") . ";instructor=true";
		$instructor_link                        = $this->Ajax_Page_Link . ";action=instructor_public_profile;eq=" . EncryptQuery("wh_id=$this->WH_ID");
        $left_column_link                       = $this->Ajax_Page_Link . ";action=left_column;eq=" . EncryptQuery("wh_id=$this->WH_ID");
        $sessions_not_rated_link                = $this->Ajax_Page_Link . ";action=sessions_not_rated;eq=" . EncryptQuery("wh_id=$this->WH_ID");
        
        AddScript("
        var ajax_load       = '<img src=\"{$GLOBALS['LOADER']}\" alt=\"loading...\" />';
        function reloadCustomerProfile() {
			$('#customer_profile_info').html(ajax_load).load('$profile_link');
		}
        function reloadLeftColumn() {
			$('#left_column_content').html(ajax_load).load('$left_column_link');
		}
        function reloadEmailSubscriptions() {
			$('#customer_email_subscriptions_info').html(ajax_load).load('$email_link');
		}
        function reloadInstructorPublicProfile() {
			$('#instructor_public_profile_info').html(ajax_load).load('$instructor_link');
		}
        function reloadCustomerSessionsNotRated() {
			$('#customer_sessions_not_rated_info').html(ajax_load).load('$sessions_not_rated_link');
		}
		");
        
        
        if ($this->Show_Profile) {
            $content        = "\n<div id='customer_profile_info'>\n" . $this->GetCustomerProfileContent() . "\n</div>\n";
            $edit_link      = "<a href='#' onclick=\"top.parent.appformCreateOverlay('Edit Your Information', getClassExecuteLinkNoAjax('{$eq_CustomerProfileContacts}'), 'apps'); return false;\">edit my information</a>";
            $edit_link     .= ($this->Show_Change_Password) ? "<br /><a href='#' onclick=\"top.parent.appformCreateOverlay('Change Your Password', getClassExecuteLinkNoAjax('{$eq_CustomerProfilePassword}'), 'apps'); return false;\">change my password</a>" : '';
            
            $box_profile    = AddBox('personal information', $content, $edit_link) . '<br /><br />';
            $box_profile   .= ($this->Profile_Inactive) ? '<div style="background-color:red; padding:5px; color:#fff; font-size:14px;">THIS PROFILE IS INACTIVE</div>' : '';
        }
        
        if ($this->Show_Instructor_Profile && $this->Is_Instructor && !$this->Instructor_Account_Limited) {
            $edit_link      = "<a href='#' onclick=\"top.parent.appformCreateOverlay('Edit Your Public Profile', getClassExecuteLinkNoAjax('{$eq_InstructorProfileEdit}'), 'apps'); return false;\">edit my public profile</a>";
            
            $OBJ2 = new InstructorProfile_View();
            $OBJ2->check_pending_status = true;
            $OBJ2->Show_Profile_Status = true;
            $content = "\n<div id='instructor_public_profile_info' style='border:1px solid #000;'>\n" . $OBJ2->InitializeProfileWindow($this->WH_ID, true) . "\n</div>\n";
            
            $box_instructor_profile    = AddBox('your public profile', $content, $edit_link) . '<br /><br />';
        } 
        
        if ($this->Show_Session_Ratings) {
            $content                = "\n<div id='customer_sessions_not_rated_info'>\n" . $this->GetSessionsNotRatedContent() . "\n</div>\n";
            $edit_link              = '';
            
            if ($this->Have_Session_Customer_Not_Rated) {
                $box_sessions_not_rated = AddBox('session ratings', $content, $edit_link) . '<br /><br />';
            }
        }
        
        if ($this->Show_Instructor_Limited_Message && $this->Is_Instructor && $this->Instructor_Account_Limited) {
            $box_instructor_limited_message = "<div style='padding:10px; border:1px solid #990000; color:#990000;'><div style='font-size:20px;'>Welcome! You now have access to some areas of the Instructor website. Once you've completed all of the steps to become a YogaLiveLink Instructor you will have access to all areas of the website. Please review the checklist on the left to see the status of your account. Thank you!</div></div><br /><br />";
        }
        
        if ($this->Show_Session_Search) {
            $content                = '[T~CPO_004]';
            $box_session_search     = AddBox('[T~CPO_005]', $content) . '<br /><br />';
        }
        
        
        /*
        if ($this->Show_Logout) {
            $content        = '<a href="/office/LOGOUT">[T~CPO_008]</a>';
            $box_logout     = AddBox_Type2('', $content, $this->Ico_Logout) . '<br /><br />';
        }
        */
        
        
        if ($this->Show_Email_Subscription && !$this->Profile_Inactive) {
            $content                    = "\n<div id='customer_email_subscriptions_info'>\n" . $this->GetCustomerEmailSubscriptions() . "\n</div>\n";
            $edit_link                  = "<a href='#' onclick=\"top.parent.appformCreateOverlay('[T~CPO_009]', getClassExecuteLinkNoAjax('{$eq_CustomerProfileEmailSubscriptions}'), 'apps'); return false;\">edit my email subscriptions</a>";
            $box_email_subscription     = AddBox('email subscriptions', $content, $edit_link) . '<br /><br />';
        }
        
        
        if ($this->Show_Intake_Forms && !$this->Is_Instructor) {
            $content            = "\n<div id='customer_intake_forms'>\n" . $this->IntakeFormsBox() . "\n</div>\n";
            $edit_link          = "<a href='#' onclick=\"top.parent.appformCreateOverlay('Change Your YOGA: FITNESS FORM', getClassExecuteLinkNoAjax('{$eq_CustomerProfileIntakeFormStandard}'), 'apps'); return false;\">edit my yoga: fitness form</a>";
            $edit_link         .= "<br /><a href='#' onclick=\"top.parent.appformCreateOverlay('Change Your YOGA THERAPY: HEALTH FORM', getClassExecuteLinkNoAjax('{$eq_CustomerProfileIntakeFormTherapy}'), 'apps'); return false;\">edit my yoga therapy: health form</a>";
            $box_intake_forms   = AddBox('health/fitness forms', $content, $edit_link) . '<br /><br />';
        }
        

        $output = "
        {$box_instructor_limited_message}
        
        <div>
            <table cellpadding='0' cellspacing='0' width='100%' border='0'>
            <tr>
                <td valign='top' width='50'>&nbsp;</td>
                <td valign='top'>
                    {$box_profile}
                </td>
                <td valign='top' width='50'>&nbsp;</td>
                <td valign='top'>
                    {$box_email_subscription}
                    {$box_intake_forms}
                </td>
            </tr>
            <tr>
                <td colspan='4'><br /><br />{$box_sessions_not_rated}<br /><br /></td>
            </tr>
            <tr>
                <td colspan='4'><br /><br />{$box_instructor_profile}<br /><br /></td>
            </tr>
            </table>
        </div>
        ";
        
        $script = "InitializeOnReady_Profile_CustomerProfileOverview();";
        $output .= EchoScript($script);

        return $output;
        
        
    }


}  // -------------- END CLASS --------------