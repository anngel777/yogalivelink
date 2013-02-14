<?php
class Touchpoint_ContactForm extends BaseClass
{
    public $WH_ID                   = 0;
    public $ShowArray               = false;
    private $table_settings         = 'touchpoint_form_settings';
    private $admin_email            = 'FlyingFlashcards_Support@mailwh.com';    
    private $category_email_list    = array(
        'General'                   => 'ff_general@mailwh.com',
        'Website Technical Issue'   => 'ff_technical@mailwh.com',
        'Course Question'           => 'ff_course_question@mailwh.com',
        'Billing'                   => 'ff_billing@mailwh.com',
    );
    public $allow_user_copy_email   = true;
    public $style_fieldset          = "style='color:#990000; font-size:14px; font-weight:bold;'";
    
    # EMAIL SETTINGS
    # ===============================================================================================
    public $from_name               = "FlyingFlashcards.com";
    public $from_email              = "support@FlyingFlashcards.com";
    public $subject_admin  		    = 'FlyingFlashcards.com - Contact Request';
    public $subject_user  		    = 'FlyingFlashcards.com - Contact Request';
    public $message_text_admin 	    = "A contact request has ocurred\n\n WH_ID: @@WH_ID@@ \n REQUESTOR_NAME: @@REQUESTOR_NAME@@ \n REQUESTOR_EMAIL: @@REQUESTOR_EMAIL@@ \n CATEGORY: @@CATEGORY@@ \n REQUEST: @@REQUEST@@";
    public $message_text_user       = "You have sent a request to the FlyingFlahcards.com team. Below is a copy of that request for your records: \n\n CATEGORY: @@CATEGORY@@ \n REQUEST: @@REQUEST@@";
    public $message_html 	        = '';

    
    
    public function  __construct()
    {
        parent::__construct();
        
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
        $this->Default_Values   = array(
            'requestor_name'    => "{$_SESSION['USER_LOGIN']['LOGIN_RECORD']['first_name']} {$_SESSION['USER_LOGIN']['LOGIN_RECORD']['last_name']}",
            'requestor_email'   => $_SESSION['USER_LOGIN']['LOGIN_RECORD']['email_address'],
        );

    } // -------------- END __construct --------------

    
    public function AddRecordCustom()
    {
        # GET ALL THE CATEGORIES
        # ============================================================
        /*
        $record = $this->SQL->GetRecord(array(
            'table' => $this->TableChatSettings,
            'keys'  => 'setting_value',
            'where' => "setting_name='categories' AND active=1",
        ));
        */
        
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
        $base_array_1 = array(
            "form|$this->Action_Link|post|db_edit_form",
            "code|<br />",
            "fieldset|Contact Form|options_fieldset|$this->style_fieldset",
                'text|Your Name|requestor_name|N|60|255',
                'text|Your Email Address|requestor_email|N|60|255',
                "select|Category|category|Y||$category_types",            
                'textarea|Request|notes|Y|60|10',
        );        
        
        if ($this->allow_user_copy_email) $base_array_1[] = 'checkbox|Send Copy of Request to my Email Address|requestor_copy_email||1|0';        
        
        $base_array_2 = array(        
                'hidden|active|1',
                "hidden|wh_id|$this->WH_ID",
            "endfieldset",
        );
        
        $base_array = array_merge($base_array_1, $base_array_2);
        $base_array[] = "submit|Send Request|$this->Add_Submit_Name";
        $base_array[] = 'endform';
        $this->Form_Data_Array_Add = $base_array;
    }

    
    public function SetFormArrays()
    {
        $this->AddRecordCustom();
    }
    
        
    public function PostProcessFormValues($FormArray)
    {
        // extend this function to process values -- simply return the array back
        if ($this->ShowArray) echo ArrayToStr($FormArray);
        
        /*
        # CREATE A RESERVATION_CODE (IF THERE ISN'T ONE) AND INJECT IT INTO THE FORM ARRAY
        # ============================================================
        if (!$FormArray['touchpoint_chats_code']) {
            $code = $this->GenerateCode();
            $FormArray['touchpoint_chats_code'] = $code;
            $this->NewChatUserCode = $code;
        }
        */
        
        return $FormArray;
    }
    
    
    public function SuccessfulAddRecord()
    {
        $email_send_to_user     = Post('FORM_requestor_copy_email');
        $email_send_to_admin    = true;
        $header                 = "From: {$this->from_name} <{$this->from_email}>\r\n";
        
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
            $message                = astr_replace($swap_list, $this->message_text_admin);
            $to                     = $this->category_email_list[Post('FORM_category')]; #$this->admin_email;
            $result                 = mail($to, $this->subject_admin, $message, $header);
            $result_msg             = ($result) ? "<h1>Thank You - Your request email has been sent.</h1>" : "<h1>Unable to send your message. Please use your normal email program and send your request to $ADMIN_EMAIL.</h1>";
            echo $result_msg;
        }
        
        
        # SEND MESSAGE TO USER (if applicable)
        # =============================================================
        if ($email_send_to_user) {
            $message                = astr_replace($swap_list, $this->message_text_user);
            $to                     = Post('FORM_requestor_email');
            $result                 = mail($to, $this->subject_user, $message, $header);
            $result_msg             = ($result) ? "<h1>A copy of the message has been sent to your email address.</h1>" : "<h1>Unable to send a copy of the message to your email address.</h1>";
            echo $result_msg;
        }
    }
    



}  // -------------- END CLASS --------------




/*
=================== DATABASE TABLES =====================
CREATE TABLE IF NOT EXISTS `touchpoint_forms` (
  `touchpoint_forms_id` int(11) NOT NULL auto_increment,
  `wh_id` bigint(20) default NULL,
  `requestor_name` varchar(255) NOT NULL,
  `requestor_email` varchar(255) NOT NULL,
  `requestor_copy_email` tinyint(1) NOT NULL default '0',
  `touchpoint_form_categories_id` int(11) default NULL,
  `category` varchar(255) NOT NULL,
  `order_number` varchar(255) default NULL,
  `notes` text,
  `contact_method` varchar(45) default NULL,
  `contact_method_info` varchar(255) default NULL,
  `followup_required` tinyint(1) default '0',
  `active` tinyint(1) NOT NULL default '1',
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`touchpoint_forms_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;
*/