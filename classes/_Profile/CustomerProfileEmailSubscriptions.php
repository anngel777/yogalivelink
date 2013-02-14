<?php
class Profile_CustomerProfileEmailSubscriptions extends BaseClass
{
    public $Show_Query              = false;

    public $WH_ID                   = 0;
    public $contacts_id             = 0;
    public $Customer_Edit_Record    = false;
    public $Table_Salutations       = 'contact_salutations';
    public $Table_Timezones         = 'time_zones';
    
    public  $Email_Subscriptions    = array();
    
    
    public function  __construct()
    {
        parent::__construct();
        
        
        global $EMAIL_SUBSCRIPTIONS;
        $this->Email_Subscriptions = $EMAIL_SUBSCRIPTIONS;


        $this->SetParameters(func_get_args());
        $this->WH_ID = $this->GetParameter(0);
        $this->AddDefaultWhere("`$this->Table`.`wh_id`=$this->WH_ID");
        
        
        
        $this->Table                = 'contacts';
        $this->Add_Submit_Name      = 'SESSION_CHECKLISTS_SUBMIT_ADD';
        $this->Edit_Submit_Name     = 'SESSION_CHECKLISTS_SUBMIT_EDIT';
        $this->Index_Name           = 'contacts_id';
        $this->Flash_Field          = 'contacts_id';
        #$this->Default_Where        = '';  // additional search conditions
        $this->Default_Sort         = 'contacts_id';  // field for default table sort
        $this->Default_Fields       = '';
        $this->Unique_Fields        = '';
        /*
        $this->Joins =
            "LEFT JOIN sessions ON sessions.sessions_id = session_checklists.sessions_id
            LEFT JOIN instructor_profile ON instructor_profile.wh_id = sessions.instructor_id";
        */



        $this->Field_Titles = array(
            'contacts.contacts_id' => 'Contacts Id',
            'contacts.wh_id' => 'Wh Id',
            'contacts.contact_salutations_id' => 'Contact Salutations Id',
            'contacts.first_name' => 'First Name',
            'contacts.last_name' => 'Last Name',
            'contacts.middle_name' => 'Middle Name',
            'contacts.address_1' => 'Address 1',
            'contacts.address_2' => 'Address 2',
            'contacts.address_3' => 'Address 3',
            'contacts.city' => 'City',
            'contacts.state' => 'State',
            'contacts.country_code' => 'Country Code',
            'contacts.postal_code' => 'Postal Code',
            'contacts.phone_home' => 'Phone Home',
            'contacts.phone_work' => 'Phone Work',
            'contacts.phone_cell' => 'Phone Cell',
            'contacts.email_address' => 'Email Address',
            'contacts.comments' => 'Comments',
            'contacts.language_code' => 'Language Code',
            'contacts.email_subscriptions' => 'Email Subscriptions',
            'contacts.type_customer' => 'Type Customer',
            'contacts.type_instructor' => 'Type Instructor',
            'contacts.type_administrator' => 'Type Administrator',
            'contacts.skype_username' => 'Skype Username',
            'contacts.skype_phone_number' => 'Skype Phone Number',
            'contacts.password' => 'Password',
            'contacts.super_user' => 'Super User',
            'contacts.module_roles' => 'Module Roles',
            'contacts.class_roles' => 'Class Roles',
            'contacts.created_by' => 'Created By',
            'contacts.time_zones_id' => 'Time Zones Id',
            'contacts.active' => 'Active',
            'contacts.updated' => 'Updated',
            'contacts.created' => 'Created'
        );


        $this->Default_Fields = 'wh_id,contact_salutations_id,first_name,last_name,middle_name,address_1,address_2,address_3,city,state,country_code,postal_code,phone_home,phone_work,phone_cell,email_address,comments,language_code,email_subscriptions,type_customer,type_instructor,type_administrator,skype_username,skype_phone_number,password,super_user,module_roles,class_roles,created_by,time_zones_id';

        
        $this->Default_Values = array(
            'email_subscriptions' => implode(',', array_keys($this->Email_Subscriptions))
        );
        #$this->Close_On_Success = true;

    } // -------------- END __construct --------------

    public function ProcessAjax()
    {
        #echo "<br /><h1>ProcessAjax()</h1>";
    }

    public function Execute()
    {
        #echo "<h1>Execute()</h1><br />";
        #echo "<h1>{$this->WH_ID}</h1><br />";

        $output = $this->EditRecordSpecial();
        echo $output;
    }

    public function ExecuteAjax()
    {

        $output = $this->EditRecordSpecial();
        echo $output;
    }


    public function EditRecordSpecial($WH_ID='')
    {
        global $FORM_VAR;
        
        if ($WH_ID) {
            $this->WH_ID = $WH_ID;
        }
        
        $this->Customer_Edit_Record = true;
        $output = $this->EditRecordText($this->WH_ID, 'wh_id');
        //$this->SQL->WriteDbQuery();
        return $output;
    }

    public function ListRecordSpecial($WH_ID)
    {
        $this->WH_ID = $WH_ID;

        $record = $this->SQL->GetRecord(array(
            'table' => $this->Table,
            #'keys'  => "{$this->Table}.*, {$this->Table_Timezones}.*, {$this->Table_Salutations}.salutation AS contact_salutation",
            'keys'  => "{$this->Table}.email_subscriptions",
            'where' => "`wh_id`=$this->WH_ID AND $this->Table.active=1",
            #'joins'  => "LEFT JOIN {$this->Table_Timezones} ON {$this->Table_Timezones}.time_zones_id = {$this->Table}.time_zones_id 
            #             LEFT JOIN {$this->Table_Salutations} ON {$this->Table_Salutations}.contact_salutations_id = {$this->Table}.contact_salutations_id",
        ));
        if ($this->Show_Query) echo $this->SQL->Db_Last_Query;

        if ($record) {
            $output = $record;
        } else {
            $output = "<h1>UNABLE TO LOAD EMAIL SUBSCRIPTIONS</h1>";
        }

        return $output;
    }


    public function SuccessfulEditRecord($flash, $id, $id_field)
    {
		echoScript("
			top.parent.reloadEmailSubscriptions();
			
			var dialogNumber = '';
			var dialogID = '';
			if (window.frameElement) {
				if (window.frameElement.id.substring(0, 13) == 'appformIframe') {
					dialogNumber = window.frameElement.id.replace('appformIframe', '');
					dialogID = 'appform' + dialogNumber;
				}
			}
			top.parent.appformCloseOverlay(dialogID);
			
		");
    }

    public function SetFormArrays()
    {
        $timezone_list      = $this->SQL->GetAssocArray($this->Table_Timezones, 'time_zones_id', 'tz_name', '`active`=1');
        $timezone_types     = Form_AssocArrayToList($timezone_list);
        
        $salutation_list    = $this->SQL->GetAssocArray($this->Table_Salutations, 'contact_salutations_id', 'salutation', '`active`=1 AND `languages_id`=4');
        $salutation_types   = Form_AssocArrayToList($salutation_list);
        
        $email_subscriptions_explanations = '';
        foreach ($this->Email_Subscriptions as $var => $title_description) {
            $parts          = explode('|', $title_description);
            $title          = $parts[0];
            $description    = $parts[1];
            
            $this->Email_Subscriptions[$var] = $title;
            $email_subscriptions_explanations .= "<div style='font-weight:bold;'>$title</div><div>$description</div><br />";
        }
        
        $email_subscriptions_list = Form_AssocArrayToList($this->Email_Subscriptions);
        
        
        if ($this->Customer_Edit_Record) {
            $base_array = array(
                "form|$this->Action_Link|post|customer_edit_form",
                
                'code|<br /><br />',
                'info||Info about email subscriptions goes here',
                "checkboxlistset|Subscriptions|email_subscriptions|N||$email_subscriptions_list",
                
                'code|<br /><br />',
                "info||$email_subscriptions_explanations",
                
            );
        } else {
            $base_array = array(
                "form|$this->Action_Link|post|db_edit_form",
                
                'code|<br /><br />',
                'info||Info about email subscriptions goes here',
                "checkboxlistset|Lists|email_subscriptions|N||$email_subscriptions_list",
            );
        }
        
        
        if ($this->Action == 'ADD') {
            $base_array[] = "submit|Add Record|$this->Add_Submit_Name";
            $base_array[] = 'endform';
            $this->Form_Data_Array_Add = $base_array;
        } else {
            $base_array = BaseArraySpecialButtons($base_array, $this->Edit_Submit_Name);
            $base_array[] = 'endform';
            $this->Form_Data_Array_Edit = $base_array;
        }
    }


}  // -------------- END CLASS --------------