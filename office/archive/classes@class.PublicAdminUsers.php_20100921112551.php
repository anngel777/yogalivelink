<?php

// FILE: class.AdminUsers.php 

// class for public management

class PublicAdminUsers extends Lib_AdminUsers
{

    public function  __construct()
    {

        parent::__construct();

        $this->Table_Title = 'Users';
        
        $this->Field_Titles = array(
            'admin_users_id' => 'ID',
            'email_address' => 'Email Address',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'company_name' => 'Company Name',
            'module_roles' => 'Module Roles',
            'class_roles' => 'Class Roles',
            'created_by' => 'Created By',
            'active' => 'Active',
            'updated' => 'Updated',
            'created' => 'Created'
        );

        $this->AddDefaultWhere('super_user=0');

        $this->Default_Fields = 'email_address,first_name,last_name,company_name';

        $this->Unique_Fields = 'email_address';
        

    } // -------------- END __construct --------------


    public function SetFormArrays()
    {
        $E = chr(27);

        $this->GetRoleNames();


        if ($this->Module_Role_Names) {
            $module_roles_list = Form_AssocArrayToList($this->Module_Role_Names);
            $this->Module_Roles_Form_Item = "checkboxlistset|Module Roles|module_roles|N||$module_roles_list";
        } else {
            $this->Module_Roles_Form_Item = 'h3|No Module Roles Defined!|style="text-align:center; color:#f00;"';
        }

        if ($this->Class_Role_Names) {
            $class_roles_list = Form_AssocArrayToList($this->Class_Role_Names);
            $this->Class_Roles_Form_Item = "
                fieldset|Class Access|$E
                checkboxlistset|Class Roles|class_roles|N||$class_roles_list|$E
                endfieldset|$E
            ";
        } else {
            $this->Class_Roles_Form_Item = ''; 'h3|No Class Roles Defined!|style="text-align:center; color:#f00;"';
        }


        $this->Non_Profile_Form_Items = ($this->Updating_Profile)? '' : "
            fieldset|Page Access|style=\"margin-bottom:20px;\"|$E
            $this->Module_Roles_Form_Item|$E
            endfieldset|$E
            $this->Class_Roles_Form_Item
            ";

        $active = ($this->Updating_Profile)? '' : "checkbox|Active|active||1|0|$E";


        $name_info = "
            text|First Name|first_name|Y|40|40|$E
            text|Last Name|last_name|Y|40|40|$E
            email|Email Address|email_address|Y|40|80|$E";

        $company = "text|Company Name|company_name|N|40|80|$E";


        $this->Form_Data_Array_Add = "
            form|$this->Action_Link|post|db_add_form|$E
            $name_info
            password|Password|password|Y|40|40|autocomplete=\"off\"|$E
            $company
            $this->Non_Profile_Form_Items
            hidden|created_by|$this->User_Name|$E
            submit|Add Record|$this->Add_Submit_Name|$E
            endform|$E";


        $this->Form_Data_Array_Edit = "
            form|$this->Action_Link|post|db_edit_form|$E
            $name_info
            password|New Password|password|N|40|40|autocomplete=\"off\"|$E
            $company
            $this->Non_Profile_Form_Items
            $active
            submit|Update Record|$this->Edit_Submit_Name|$E
            endform|$E";
    }

  

    public function ViewRecordText($id, $field_list='', $id_field='')
    {
        $this->User_Info['SUPER_USER'] = 0;
        $RESULT = parent::ViewRecordText($id, $field_list, $id_field);        
        return $RESULT;
    }

}  // -------------- END CLASS --------------



