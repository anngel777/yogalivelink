<?php

class BaseClass extends Lib_BaseClass
{

    public $Is_Mgmt          = false;

    public $Company_Query    = "CONCAT(companies.company_name, IF(CHAR_LENGTH(companies.city)>0 OR CHAR_LENGTH(companies.state)>0, ' -- ', ''), companies.city, IF(CHAR_LENGTH(companies.city)>0 AND CHAR_LENGTH(companies.state)>0, ', ', ''), companies.state)";

    public $Ac_Company         = "ac_table=companies&ac_key=companies_id&ac_field=";

    public $Ac_Contact         = "ac_table=contacts&ac_key=contacts_id&ac_field=CONCAT(first_name, ' ', last_name, ', ', companies.company_name, IF(contacts.city!='' OR contacts.state!='', ' -- ', ''), contacts.city, IF(contacts.city !='' AND contacts.state!='', ', ', ''), contacts.state)&ac_joins=LEFT JOIN companies ON companies.companies_id=contacts.companies_id";

    public $Admin_Concat       = "CONCAT(admin_users.first_name, ' ', admin_users.last_name, ' - ', admin_users.company_name)";

    public $Opportunity_Concat = "CONCAT('[', FORMAT(opportunities.opportunities_id, 0), '] ', opportunities.line_type,
    IF (principal_parts.principal_parts_id IS NOT NULL, CONCAT(': ', principal_parts.part_description, ' (', principal_parts.part_number, ')'), ''))";
    
    public $Ac_Program         = "ac_table=programs&ac_key=programs_id&ac_field=CONCAT('[', FORMAT(programs_id, 0), '] ',`company_name`, ' - ', program_name, ' - ', application)&ac_joins=LEFT JOIN companies ON companies.companies_id=programs.customer_id";
    
    public $User_Contacts_Id    = '';
    public $Super_User          = false;

    // ----------- construction ---------------
    public function  __construct()
    {
        $this->Ac_Company .= $this->Company_Query;
        parent::__construct();
        $this->Default_List_Size = 1000;
        
        $this->Is_Mgmt = in_array('user_management', $this->User_Info['MODULE_ROLES']);
        $this->User_Contacts_Id = intOnly($this->User_Info['LOGIN_RECORD']['contacts_id']);  
        $this->Super_User = ($this->User_Info['SUPER_USER'] == 1);

        //$_SESSION['WANT_DB_QUERIES'] = 1;//<<<<<<<<<<---------- REMOVE ----------<<<<<<<<<<        
    } //------------------- end construct -------------------

    
    
    

    public function GetAddCompanyLink($id_name='companies_id')
    {
        $dialog_id = Get('DIALOGID');
        $eq_company_add = EncryptQuery("class=Companies&dialog=$dialog_id&return_function=setFormAutoCompleteValue&return_parameters='$id_name'");
        return qqn("<a href=`#` title=`Add Company` onclick=`tableAddClick('$eq_company_add', 'Companies'); return false;`><img src=`/wo/images/b_add.gif` width=`17` height=`15` border=`0` alt=`Add Company` /></a>");
    }
    
    public function GetAddContactLink($id_name='contacts_id')
    {
        $dialog_id = Get('DIALOGID');
        $eq_contact_add = EncryptQuery("class=Contacts&dialog=$dialog_id&return_function=setFormAutoCompleteValue&return_parameters='$id_name'");
        return qqn("<a href=`#` title=`Add Contact` onclick=`tableAddClick('$eq_contact_add', 'Contacts'); return false;`><img src=`/wo/images/b_add.gif` width=`17` height=`15` border=`0` alt=`Add Contact` /></a>");
    }

}