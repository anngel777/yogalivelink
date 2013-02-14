<?php
class Touchpoint_ContactForm extends BaseClass
{
    # used for javascript handling of form
    public $JS_Form_Name            = 'contact_edit_form';
    public $JS_Execute_Function     = 'contactFormSaveChanges()';
    public $JS_Execute_Button_Value = 'Send Request';


    public $WH_ID                   = 0;
    public $ShowArray               = false;
    private $table_settings         = 'touchpoint_form_settings';
    public $admin_email             = '';    
    public $category_email_list     = array();
    public $allow_user_copy_email   = true;
    public $style_fieldset          = "style='color:#990000; font-size:14px; font-weight:bold;'";
    public $textbox_width           = 60; //width of text boxes
    
    # EMAIL SETTINGS
    # ===============================================================================================
    public $from_name               = '';
    public $from_email              = '';
    public $subject_admin  		    = '';
    public $subject_user  		    = '';
    public $message_text_admin 	    = '';
    public $message_text_user       = '';
    public $message_html 	        = '';
    
    
    public $Email_Subject           = 'Thank You For Contacting YogaLiveLinks.com';
    public $Email_Template_Id       = 5;
    public $Email_Send_To_Admin     = true;
    
    
    public function  __construct()
    {
        parent::__construct();
        
		$this->Close_On_Success = false;
		
		global $EMAIL_FROM_NAME, $EMAIL_FROM_EMAIL, $EMAIL_SUBJECT_ADMIN, $EMAIL_SUBJECT_USER, 
		$EMAIL_MESSAGE_TEXT_ADMIN, $EMAIL_MESSAGE_TEXT_USER, $EMAIL_MESSAGE_HTML,
		$EMAIL_ADMIN_EMAIL, $EMAIL_CATEGORY_EMAIL_LIST;
		
		$this->from_name 			= $EMAIL_FROM_NAME;
		$this->from_email 			= $EMAIL_FROM_EMAIL;
		$this->subject_admin 		= $EMAIL_SUBJECT_ADMIN;
		$this->subject_user 		= $EMAIL_SUBJECT_USER;
		$this->message_text_admin 	= $EMAIL_MESSAGE_TEXT_ADMIN;
		$this->message_text_user 	= $EMAIL_MESSAGE_TEXT_USER;
		$this->message_html 		= $EMAIL_MESSAGE_HTML;
		$this->admin_email          = $EMAIL_ADMIN_EMAIL;
		$this->category_email_list  = $EMAIL_CATEGORY_EMAIL_LIST;
		
        $this->SetParameters(func_get_args());
        $this->WH_ID = $this->GetParameter(0);
        
        
        $this->Table            = 'touchpoint_forms';
        $this->Index_Name       = 'touchpoint_forms_id';
        $this->Flash_Field      = 'touchpoint_forms_id';
        $this->Add_Submit_Name  = 'TOUCHPOINT_FORMS_SUBMIT_ADD';
        $this->Edit_Submit_Name = 'TOUCHPOINT_FORMS_SUBMIT_EDIT';        
        $this->Default_Where    = '';  // additional search conditions
        $this->Default_Sort     = 'touchpoint_forms_id';  // field for default table sort
        
        $this->Field_Titles     = array(
            'touchpoint_forms_id'   => 'Id',
            'active'                => 'Active',
            'updated'               => 'Updated',
            'created'               => 'Created'
        );
        
        $this->Default_Fields   = 'touchpoint_forms_id';
        $this->Unique_Fields    = '';
        
        if (isset($_SESSION['USER_LOGIN']['LOGIN_RECORD'])) {
            $this->Default_Values   = array(
                'requestor_name'    => "{$_SESSION['USER_LOGIN']['LOGIN_RECORD']['first_name']} {$_SESSION['USER_LOGIN']['LOGIN_RECORD']['last_name']}",
                'requestor_email'   => $_SESSION['USER_LOGIN']['LOGIN_RECORD']['email_address'],
            );
        }

    } // -------------- END __construct --------------
    
    public function ProcessAjax()
    {
        # NOTE :: RAW (12-21) :: This function must be here to stop AJAX processing when the 
        # class is initially called. Otherwise the BaseClass call will execute and exit();
        
        #echo "<br /><h1>ProcessAjax()</h1>";
    }
    
    public function Execute()
    {
        $output = $this->AddRecordSpecial();
        echo $output;
    }    
    
    public function ExecuteAjax()
    {
        $output = $this->AddRecordSpecial();
        echo $output;
    }

    public function AddRecordSpecial($WH_ID='')
    {
        global $FORM_VAR;
        
		/*
        # RE-POPULATE THE FORM WITH DATA
        if (Post('data')) {
            Form_AjaxToPost('data');
            //$_POST[$this->Edit_Submit_Name] = $FORM_VAR['submit_click_text'];
            $_POST[$this->Add_Submit_Name] = $FORM_VAR['submit_click_text'];
        }
		*/
    
        if ($WH_ID) {
            $this->WH_ID = $WH_ID;
        }
        
        $output = $this->AddRecordText();
        return $output;
    }

    public function SetFormArrays()
    {
        # GET ALL THE CATEGORIES
        # ============================================================
        $category_types = '';
        foreach ($this->category_email_list AS $category => $email)
        {
            $value              = $category; #strtolower($category);
            $display            = ucwords($category);
            $category_types    .= "$value=$display|";
        }
        $category_types = substr($category_types,0,-1);
        
        
        # CREATE THE BASE ARRAY
        # ============================================================
        $base_array = array(
            "form|$this->Action_Link|post|$this->JS_Form_Name",
            'hidden|active|1',
            "hidden|wh_id|$this->WH_ID",
                
            "code|<br />",
                "text|Your Name|requestor_name|N|$this->textbox_width|255",
                "text|Your Email Address|requestor_email|N|$this->textbox_width|255",
                "select|Category|category|Y||$category_types",            
                "textarea|Request|notes|Y|$this->textbox_width|10",
        );        
        
        if ($this->allow_user_copy_email) {
			$base_array[] = 'code|<div style="text-align:right; font-weight:bold;">';
			$base_array[] = '@checkbox|Send a Copy of Request to my Email Address|requestor_copy_email||1|0';
			$base_array[] = 'code|</div><br />';
		}
        
        
        $base_array = BaseArraySpecialButtons($base_array, $this->Add_Submit_Name, 'SEND EMAIL');
        $base_array[] = 'endform';
        $this->Form_Data_Array_Add = $base_array;
    }
    
    public function SuccessfulAddRecord()
    {
        $email_send_to_user     = Post('FORM_requestor_copy_email');
        $email_send_to_admin    = $this->Email_Send_To_Admin;
        
        
        
        
        if ($email_send_to_user || $email_send_to_admin)
        {
            # INITIALIZE THE EMAIL CLASS
            # ==================================
            global $ROOT;
            require_once "$ROOT/phplib/swift4/swift_required.php";
            $MAIL = new Email_MailWh;
        }
        
        
        
        $this->subject_admin    .= ' - ' . Post('FORM_category');
        $swap_list              = array (
            '@@WH_ID@@'             => Post('FORM_wh_id'),
            '@@REQUEST@@'           => Post('FORM_notes'),
            '@@CATEGORY@@'          => Post('FORM_category'),
            '@@REQUESTOR_NAME@@'    => Post('FORM_requestor_name'),
            '@@REQUESTOR_EMAIL@@'   => Post('FORM_requestor_email'),
        );
        
        
        
        
        
        # SEND MESSAGE TO ADMINISTRATOR
        # =============================================================
        if ($email_send_to_admin) {
        
            # MAKE THE BODY CONTENT
            # ==================================
            $message = astr_replace($swap_list, $this->message_text_admin);
            $swap_array = array(
                '@@email_body_content@@'        => $message,
            );
            
            
            # PREP THE MESSAGE ARRAY
            # ==================================
            $msg_array = array(
                'email_template_id'     => $this->Email_Template_Id,
                'swap_array'            => $swap_array,
                'subject'               => $this->subject_admin,
                'to_name'               => 'Administrator',
                'to_email'              => $this->category_email_list[Post('FORM_category')],
                'cc'                    => '',
                'bcc'                   => '',
                'wh_id'                 => 666,
            );
            $MAIL->PrepareMailToSend($msg_array);
            
            
            # SEND THE PREPARED MESSAGE
            # ==================================
            if ($MAIL->MailPrepared()) {
                echo "<h1>Thank You - Your request email has been sent.</h1>";
            } else {
                $ADMIN_EMAIL = 'adin@yogalivelink.com';
                echo "<h1>Unable to send your message. Please use your normal email program and send your request to $ADMIN_EMAIL.</h1>";
            }
        }
        
        
        
        # SEND MESSAGE TO USER (if applicable)
        # =============================================================
        if ($email_send_to_user) {
        
            # MAKE THE BODY CONTENT
            # ==================================
            $message = astr_replace($swap_list, $this->message_text_user);
            $swap_array = array(
                '@@email_body_content@@'        => $message,
            );
            
            
            # PREP THE MESSAGE ARRAY
            # ==================================
            $msg_array = array(
                'email_template_id'     => $this->Email_Template_Id,
                'swap_array'            => $swap_array,
                'subject'               => $this->Email_Subject,
                'to_name'               => Post('FORM_requestor_name'),
                'to_email'              => Post('FORM_requestor_email'),
                'cc'                    => '',
                'bcc'                   => '',
                'wh_id'                 => Post('FORM_wh_id'),
            );
            $MAIL->PrepareMailToSend($msg_array);
            
            
            # SEND THE PREPARED MESSAGE
            # ==================================
            if ($MAIL->MailPrepared()) {
                echo "<h1>A copy of the message has been sent to your email address.</h1>";
            } else {
                echo "<h1>Unable to send a copy of the message to your email address.</h1>";
            }
        }
        
		echo "<div style='width:300px;'>&nbsp;</div>";
    }
    

}  // -------------- END CLASS --------------