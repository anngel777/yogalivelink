<?php
class Profile_CustomerProfileContacts extends BaseClass
{
    public $Show_Query              = false;

    public $WH_ID                   = 0;
    public $contacts_id             = 0;
    public $Customer_Edit_Record    = false;
    
    
    public function  __construct()
    {
        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => '',
            'Description' => 'Create and manage session_checklists',
            'Created'     => '2010-10-27',
            'Updated'     => '2010-10-27'
        );



        $this->SetParameters(func_get_args());
        $this->WH_ID = $this->GetParameter(0);
        #if ($this->WH_ID) {
            $this->AddDefaultWhere("`$this->Table`.`wh_id`=$this->WH_ID");
        #}


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

        #$this->Close_On_Success = true;

    } // -------------- END __construct --------------

    public function ProcessAjax()
    {
        #echo "<br /><h1>ProcessAjax()</h1>";
    }

    public function Execute()
    {
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
        
        return $output;
    }

    public function ListRecordSpecial($WH_ID)
    {
        $this->WH_ID = $WH_ID;

        $record = $this->SQL->GetRecord(array(
            'table' => $this->Table,
            'keys'  => "{$this->Table}.*, {$GLOBALS['TABLE_timezones']}.*, {$GLOBALS['TABLE_salutations']}.salutation AS contact_salutation",
            'where' => "`wh_id`=$this->WH_ID AND $this->Table.active=1",
            'joins'  => "LEFT JOIN {$GLOBALS['TABLE_timezones']} ON {$GLOBALS['TABLE_timezones']}.time_zones_id = {$this->Table}.time_zones_id 
                         LEFT JOIN {$GLOBALS['TABLE_salutations']} ON {$GLOBALS['TABLE_salutations']}.contact_salutations_id = {$this->Table}.contact_salutations_id",
        ));
        if ($this->Show_Query) echo $this->SQL->Db_Last_Query;

        if ($record) {
            $output = $record;
        } else {
            $output = "<h1>UNABLE TO LOAD PROFILE</h1>";
        }

        return $output;
    }


    public function SuccessfulEditRecord($flash, $id, $id_field)
    {
        $this->UpdateCustomerTimezone($this->WH_ID);
        
		echoScript("
			top.parent.reloadCustomerProfile();
			
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
    
    
    public function UpdateCustomerTimezone($WH_ID='')
    {
        $wh_id = ($WH_ID) ? $WH_ID : $this->WH_ID;

        $record = $this->SQL->GetRecord(array(
            'table' => $this->Table,
            'keys'  => "{$this->Table}.contacts_id, {$GLOBALS['TABLE_timezones']}.*",
            'where' => "`wh_id`=$wh_id AND $this->Table.active=1",
            'joins'  => "LEFT JOIN {$GLOBALS['TABLE_timezones']} ON {$GLOBALS['TABLE_timezones']}.time_zones_id = {$this->Table}.time_zones_id",
        ));
        if ($this->Show_Query) echo $this->SQL->Db_Last_Query;

        if ($record) {
            global $USER_LOCAL_TIMEZONE, $USER_LOCAL_TIMEZONE_OFFSET;
            
            $_SESSION['USER_LOGIN']['LOGIN_RECORD']['tz_name']      = $record['tz_name'];
            $_SESSION['USER_LOGIN']['LOGIN_RECORD']['tz_offset']    = $record['tz_offset'];
            
            $USER_LOCAL_TIMEZONE                                    = $record['tz_name'];
            $USER_LOCAL_TIMEZONE_OFFSET                             = $record['tz_offset'];
        } else {
            $output = "<h1>UNABLE TO LOAD TIMEZONE INFORMATION</h1>";
        }
    }
    
    
    public function SetFormArrays()
    {
        $timezone_list      = $this->SQL->GetAssocArray($GLOBALS['TABLE_timezones'], 'time_zones_id', 'tz_name', '`active`=1');
        $timezone_types     = Form_AssocArrayToList($timezone_list);
        
        $salutation_list    = $this->SQL->GetAssocArray($GLOBALS['TABLE_salutations'], 'contact_salutations_id', 'salutation', '`active`=1 AND `languages_id`=4');
        $salutation_types   = Form_AssocArrayToList($salutation_list);
        
        
        
        if ($this->Customer_Edit_Record) {
            $base_array = array(
                "form|$this->Action_Link|post|customer_edit_form",
                
                "select|Salutation|contact_salutations_id|Y||$salutation_types",
                'text|First Name|first_name|Y|40|100',
                'text|Last Name|last_name|Y|40|100',
                
                
                'text|Address 1|address_1|N|60|100',
                'text|Address 2|address_2|N|60|100',
                'text|Address 3|address_3|N|60|100',
                'text|City|city|N|60|100',
                'countrystate|Country:State|country_code:state|N|5|5',
                'text|Postal Code|postal_code|N|12|12',
                
                
                #'text|State|state|N|60|100',
                
                'code|<br /><br />',
                
                'email|Email Address|email_address|Y|60|100',
                'phone|Phone Number|phone_home|N|',
                
                'code|<br /><br />',
                
                'text|Skype Username|skype_username|N|45|45',
                'text|Skype Phone Number|skype_phone_number|N|45|45',
                
                'code|<br /><br />',
                
                "select|Time Zone|time_zones_id|Y||$timezone_types",
                
                
                #'text|Middle Name|middle_name|N|40|100',
                #'text|Address 1|address_1|N|60|100',
                #'text|Address 2|address_2|N|60|100',
                #'text|Address 3|address_3|N|60|100',
                #'countrystate|Country:State|country_code:state|N|5|5',
                #'text|Postal Code|postal_code|N|12|12',
                #'phone|Phone Work|phone_work|N|',
                #'phone|Phone Cell|phone_cell|N|',
            );
        } else {
            $base_array = array(
                "form|$this->Action_Link|post|db_edit_form",
                'text|Wh Id|wh_id|N|11|11',
                'text|Contact Salutations Id|contact_salutations_id|N|3|3',
                'text|First Name|first_name|N|60|100',
                'text|Last Name|last_name|N|60|100',
                'text|Middle Name|middle_name|N|60|100',
                'text|Address 1|address_1|N|60|100',
                'text|Address 2|address_2|N|60|100',
                'text|Address 3|address_3|N|60|100',
                'text|City|city|N|60|100',
                'text|State|state|N|60|100',
                'text|Country Code|country_code|N|5|5',
                'text|Postal Code|postal_code|N|50|50',
                'text|Phone Home|phone_home|N|50|50',
                'text|Phone Work|phone_work|N|50|50',
                'text|Phone Cell|phone_cell|N|50|50',
                'text|Email Address|email_address|N|60|100',
                'textarea|Comments|comments|N|60|4',
                'text|Language Code|language_code|N|3|3',
                'checkboxlistset|Email Subscriptions|email_subscriptions|N||ICC|IRD|ISS|TST|ICAS|WEBINAR|OTHER',
                'checkbox|Type Customer|type_customer||1|0',
                'checkbox|Type Instructor|type_instructor||1|0',
                'checkbox|Type Administrator|type_administrator||1|0',
                'text|Skype Username|skype_username|N|45|45',
                'text|Skype Phone Number|skype_phone_number|N|45|45',
                'text|Password|password|N|60|80',
                'checkbox|Super User|super_user||1|0',
                'text|Module Roles|module_roles|N|60|255',
                'text|Class Roles|class_roles|N|60|255',
                'text|Created By|created_by|N|60|64',
                
                
                "select|Time Zone|time_zones_id|Y||$timezone_types",
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