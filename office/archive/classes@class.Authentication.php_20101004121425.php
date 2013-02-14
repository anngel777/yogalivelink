<?php
// FILE: class.Authentication.php

class Authentication extends Lib_Authentication
{

    public function __construct($check_only = false)
    {
        $this->Login_Title            = 'Office';
        $this->Session_Name           = 'USER_LOGIN';

        $this->User_Table             = 'contacts';
        $this->User_Table_Id          = 'contacts_id';
        $this->Template_File          = '/office/templates/login.html';

        $this->Admin_Module_Roles     = 'admin_module_roles';
        $this->Admin_Class_Roles      = 'admin_class_roles';
        $this->Admin_Class_Role_Items = 'admin_class_role_items';

        $this->Logfile                = '/office/logs/login.dat';

        $this->Reset_Password_Message = '
Dear @@NAME@@,
<br /><br />
Your password has been reset to: <b>@@PASSWORD@@</b>
<br /><br />
Once you login, you may change your password.
<br /><br />
To login, click <a href="@@LINK@@">here</a>.
<br /><br />
Thank you!
';
      
        parent::__construct($check_only);

    } // ===================== END CONSTRUCT =======================

}
