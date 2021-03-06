<?php
// FILE: /Lib/Lib_Authentication.php

class Lib_Authentication
{
    public $Login_Title    = 'Office';
    public $Session_Name   = 'USER_LOGIN';

    public $Welcome_Email_Title         = '';
    public $Welcome_Email_Message       = '';
    
    public $User_Table     = 'admin_users';
    public $User_Table_Id  = 'admin_users_id';
    public $Template_File  = '/office/templates/login.html';

    public $Mask_Username  = '^([0-9a-z]+)([0-9a-z\.-]+)@([0-9a-z\.-]+)\.([a-z]{2,6})$';
    public $Mask_Password  = '^[a-zA-Z0-9!\@\#\$\%\^&\*\(\)_\+\=\{\}\[\]|\/\:\;\,\.\?~|\-]+$';  //!@#$%^&*()-_+={}[]|/:;,.?~|

    public $Admin_Module_Roles     = 'admin_module_roles';
    public $Admin_Class_Roles      = 'admin_class_roles';
    public $Admin_Class_Role_Items = 'admin_class_role_items';

    public $Logfile        = '/office/logs/login.dat';
    public $Page_Link      = '';
    public $Exit_On_Login  = true;
    public $Login_Code     = 0;

    public $Login_Ok       = ''; //Check this variable to see if user has logged in
    public $Login_Record   = ''; // THIS IS THE USER RECORD
    public $User_Name      = ''; // the name of the person;
    public $User_Id        = ''; // the id of the users
    public $Super_User     = ''; // when this is set, the user is a super user
    public $Module_Roles   = ''; // this is what roles are owned by a  person.
    public $Class_Roles    = ''; // this is what roles are owned by a  person.
    public $Error          = ''; // initialize for error checking below
    
    public $Password_Syllables   = 3;
    public $Password_Characters  = 'CNS';

    protected $Message     = '';
    protected $User        = '';
    protected $Pass        = '';
    protected $Subject     = '';
    public $SQL;

    public $Allowed_Modules = array('index', 'update_profile', 'view_record', 'add_record', 'edit_record', 'copy_record');

    public $Reset_Password_Message = '
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

    public function __construct($check_only = false)
    {
        if ($check_only) {
            $this->SetVariables();
            return;
        }

        $this->Page_Link = str_replace(array('LOGOUT=1', 'LOGOUT'),'', Server('REQUEST_URI'));
        $this->Login();
    } // ===================== END CONSTRUCT =======================


    public function SetSQL()
    {
        $this->SQL = Lib_Singleton::GetInstance('Lib_Pdo');
    }

    public function Login()
    {
        if (Get('LOGOUT')) {
            $this->Logout();
            return;
        }

        $this->SetSQL();

        if (POST('RESET_PASSWORD')) {
            $this->ResetPassword();
        }

        if (Post('LOGIN')) {
            $this->LogInProcess();
        }

        $this->SetVariables();

        if (empty($this->Login_Ok)) {
            $this->LogInForm();
        }
    }

    public function CheckModulePermission($pagename)
    {
        $PERMISSION = (in_array($pagename, $this->Module_Roles) or $this->Super_User or in_array($pagename, $this->Allowed_Modules));
        if (!$PERMISSION) {
           MText('Access Denied','<h1>ACCESS DENIED</h1><p>You do not have permission to view this module.</p>');
        }
    }

    public function SendPasswordEmail($password)
    {
        global $SCRIPT_URI, $HTTP_HOST;

        $name       = trim($this->Login_Record['first_name'] . ' ' . $this->Login_Record['last_name']);
        $to_email   = $this->Login_Record['email_address'];
        $from_name  = $this->Login_Title;
        $from_email = "no-reply@$HTTP_HOST";

        $recipient = "$name <$to_email>";

        $swap = array (
            '@@NAME@@'     => $name,
            '@@PASSWORD@@' => $password,
            '@@LINK@@'     => $SCRIPT_URI
        );

        $message_body = astr_replace($swap, $this->Reset_Password_Message);

        $subject = $this->Login_Title . ' - Password Reset';

        return SendHTMLmail($from_name, $from_email, $recipient, $subject, $message_body);
    }

    
# ================================================================================    
# START RAW ADDED
# ================================================================================
    public function SendWelcomeEmail($password)
    {
        global $SCRIPT_URI, $HTTP_HOST;

        $name       = trim($this->Login_Record['first_name'] . ' ' . $this->Login_Record['last_name']);
        $to_email   = $this->Login_Record['email_address'];
        $from_name  = $this->Welcome_Email_Title;
        $from_email = "no-reply@$HTTP_HOST";
        $recipient  = "$name <$to_email>";
        
        $swap = array (
            '@@NAME@@'      => $name,
            '@@PASSWORD@@'  => $password,
            '@@LINK@@'      => $SCRIPT_URI,
            '@@EMAIL@@'     => $to_email,
        );

        $message_body   = astr_replace($swap, $this->Welcome_Email_Message);
        $subject        = $this->Welcome_Email_Title;
        
        global $MailCR;
        $headers  = "From: $FromName <$FromEmail>$MailCR";
        $headers .= "MIME-Version: 1.0$MailCR";
        $headers .= "Content-type: text/html; charset=utf-8$MailCR";

        $Recipientlist = $recipient;
        $Message = $message_body;
        $Subject = $subject;        
        
        $count = 0;
        do {
            $RESULT = mail($Recipientlist, "$Subject", $Message, $headers);
            $count++;
            if (!$RESULT) sleep(1);
        } while (($count < 10) and (!$RESULT));
    }
    
    public function ResetPasswordAndSendWelcomeMail()
    {
        $this->User = TransformContent(Post('USER'), 'TQ');
        $this->Login_Code = 2;
        $this->Message = '';

        if (empty($this->User)) {
            $this->Error = 'User Name is missing';

        } else {
            $search_user = $this->SQL->QuoteValue($this->User);
            $this->Login_Record = $this->SQL->GetRecord($this->User_Table, '*', "`email_address`=$search_user AND active=1");

            if ($this->Login_Record) {
                $new_password = Lib_Password::MakePassword(3, 'CNS');
                $update_password = $this->SQL->QuoteValue(Lib_Password::GetPasswordHash($new_password));
                $result = $this->SQL->UpdateRecord($this->User_Table,
                    "`password`=$update_password",
                    "`email_address`=$search_user");
                if ($result) {
                    if ($this->SendWelcomeEmail($new_password)) {
                        $this->Message = 'Your welcome email has been sent!';
                        $this->Login_Code = 0;
                        $RESULT_RETURN = 1;
                    } else {
                        $this->Error = 'Sending Welcome Message Failed';
                        $this->Error .= '<br /><br />' . $this->SQL->Db_Last_Query;
                        $RESULT_RETURN = 0;
                    }
                }
            } else {
                $this->Error = 'User name not found';
            }
        }
        #$this->OutputPage();
        return $RESULT_RETURN;
    }
# ================================================================================
# END RAW ADDED
# ================================================================================
    
    
    public function ResetPassword()
    {
        $this->User = TransformContent(Post('USER'), 'TQ');
        $this->Login_Code = 2;
        $this->Message = '';

        if (empty($this->User)) {
            $this->Error = 'User Name is missing';

        } else {
            $search_user = $this->SQL->QuoteValue($this->User);
            $this->Login_Record = $this->SQL->GetRecord($this->User_Table, '*', "`email_address`=$search_user AND active=1");

            if ($this->Login_Record) {
                $new_password = Lib_Password::MakePassword($this->Password_Syllables, $this->Password_Characters);
                $update_password = $this->SQL->QuoteValue(Lib_Password::GetPasswordHash($new_password));
                $result = $this->SQL->UpdateRecord($this->User_Table,
                    "`password`=$update_password",
                    "`email_address`=$search_user");
                if ($result) {
                    if ($this->SendPasswordEmail($new_password)) {
                        $this->Message = 'Your New Password has been sent!';
                        $this->Login_Code = 0;
                    } else {
                        $this->Error = 'Sending Password Message Failed';
                    }
                }
            } else {
                $this->Error = 'User name not found';
            }
        }
        $this->OutputPage();
    }

    public function OutputPage()
    {
        global $ROOT;
        $template = file_get_contents($ROOT . $this->Template_File);

        $error   = !empty($this->Error)? '<div id="error">' . $this->Error . '</div>' : '';
        $message = !empty($this->Message)? '<div id="message">' . $this->Message . '</div>' : '';

        $swap = array(
            '@@TITLE@@'   => $this->Login_Title,
            '@@LOGIN@@'   => $this->Login_Code,
            '@@ERROR@@'   => $error,
            '@@MESSAGE@@' => $message,
            '@@USER@@'    => $this->User,
            '@@PASS@@'    => $this->Pass,
            '@@PAGELINKQUERY@@' => $this->Page_Link,
        );


        echo astr_replace($swap, $template);
        if ($this->Exit_On_Login) {
            exit;
        }
    }

    public function Logout()
    {
        unset($_SESSION[$this->Session_Name]);

        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-42000, '/');
        }

        $this->Login_Code = 1;
        $this->OutputPage();
    }

    public function CheckUserName($user)
    {
        $RESULT = CheckEmail($user);
        if (!$RESULT) {
            $this->Error = 'User Name must be a valid email address';
        }
        return $RESULT;
    }

    public function CheckLogin($user, $pass)
    {
        $user = strtolower($user);

        $RESULT = false;        

        if (empty($user)) {
            $this->Error = 'User Name is missing';

        } elseif(empty($pass)) {
            $this->Error = 'Password is missing';
            
        } elseif (!$this->CheckUserName($user)) {
            return false;
            
        } elseif (preg_match("/$this->Mask_Password/", $pass)) {
            $search_user   = $this->SQL->QuoteValue($user);
            $this->Login_Record = $this->SQL->GetRecord($this->User_Table, '*', "`email_address`=$search_user AND active=1");
            $record_password = ArrayValue($this->Login_Record, 'password');

            if (($pass == 'NEW') and ($record_password == 'NEW')) {
                return true;
            }
            
            if (empty($this->Login_Record) or empty($record_password)) {
                $this->Error = 'User Name not found';
            } elseif ($record_password == 'RESET') {
                $this->Error = 'For security, you must reset your password';
                $this->Login_Code = 2;
            } elseif (Lib_Password::CheckPasswordHash($pass, $record_password)) {
            
                unset($this->Login_Record['password']);
                return true;

            } else {
                $this->Error = 'User Name/Password not correct';
            }

        } else {
            $this->Error = 'Illegal Characters in Password';
        }

        return $RESULT;
    }

    public function SetVariables()
    {
        // set the variables from session vars
        $session = Session($this->Session_Name);
        if ($session) {
            $this->Login_Record  = $session['LOGIN_RECORD']; // THIS IS THE USER RECORD
            $this->Login_Ok      = $session['LOGIN_OK'];
            $this->User_Name     = $session['USER_NAME'];
            $this->User_Id       = $session['USER_ID'];
            $this->Super_User    = $session['SUPER_USER'];
            $this->Module_Roles  = $session['MODULE_ROLES'];
            $this->Class_Roles   = $session['CLASS_ROLES'];
        } else {
            $this->Login_Ok      = '';
        }
    }


    public function SetLoginRoles()
    {

        $modules = array();
        if ($this->Admin_Module_Roles) {
            $module_roles = $this->Login_Record['module_roles'];

            if ($module_roles) {
                $roles = $this->SQL->GetFieldValues(
                    $this->Admin_Module_Roles,
                    'modules',
                    "admin_module_roles_id in ($module_roles) AND active=1"
                );

                foreach ($roles as $role) {
                    $role_modules = explode(',', $role);
                    TrimArray($role_modules);
                    $modules = array_merge($modules, $role_modules);
                }
                $modules = array_unique($modules);
            }

        }
        $_SESSION[$this->Session_Name]['MODULE_ROLES']   = $modules;

        $class_role_array = array();
        if ($this->Admin_Class_Roles) {
            $class_roles = $this->Login_Record['class_roles'];  // comma delimited list of class roles

            if ($class_roles) {
                $class_role_items_array =  $this->SQL->GetFieldValues(
                    $this->Admin_Class_Roles,
                    'class_role_items',
                    "`admin_class_roles_id` in ($class_roles) AND active=1"
                );

                // get a unique list and remove duplicates
                $class_role_items = implode(',',  $class_role_items_array);
                $class_role_items_array = explode(',', $class_role_items);
                $class_role_items_array = array_unique($class_role_items_array);
                $class_role_items = implode(',',  $class_role_items_array);

                $class_role_array = $this->SQL->GetArrayAll(
                    $this->Admin_Class_Role_Items,
                    'class, no_edit, demo_mode, where_clause, flags',
                    "`admin_class_role_items_id` in ($class_role_items) AND active=1"
                );
            }
        }
        $_SESSION[$this->Session_Name]['CLASS_ROLES'] = $class_role_array;
    }

    public function LoginAsAlias($id)
    {
            if (!$_SESSION[$this->Session_Name]['SUPER_USER']) {
                return;
            }
            session_regenerate_id();
            $id = intOnly($id);
            if ($id) {
                $this->Login_Record = $this->SQL->GetRecord($this->User_Table, '*', "`$this->User_Table_Id`=$id");
                if ($this->Login_Record) {
                    unset($this->Login_Record['password']);
                    $_SESSION[$this->Session_Name]['LOGIN_RECORD']  = $this->Login_Record;
                    $_SESSION[$this->Session_Name]['LOGIN_OK']      = 'OK';
                    $_SESSION[$this->Session_Name]['USER_NAME']     =
                        trim($this->Login_Record['first_name'] . ' ' . $this->Login_Record['last_name']);
                    $_SESSION[$this->Session_Name]['USER_ID']    = $this->Login_Record[$this->User_Table_Id];
                    $_SESSION[$this->Session_Name]['SUPER_USER'] = ArrayValue($this->Login_Record, 'super_user');
                    $this->SetLoginRoles();
                    $_SESSION['DB_UPDATE_USER_NAME']  = $_SESSION[$this->Session_Name]['USER_NAME'];
                }
            }
    }
    
    public function LoginPublic()
    {
        if (empty($_SESSION['PUBLIC'])) {
            session_regenerate_id();
            $_SESSION[$this->Session_Name]['LOGIN_RECORD']  = array();
            $_SESSION[$this->Session_Name]['LOGIN_OK']      = 'OK';
            $_SESSION[$this->Session_Name]['USER_NAME']     = 'Public User';
            $_SESSION[$this->Session_Name]['USER_ID']       = 0;
            $_SESSION[$this->Session_Name]['SUPER_USER']    = false;
            $_SESSION['DB_UPDATE_USER_NAME']  = 'Public User';
            $_SESSION[$this->Session_Name]['MODULE_ROLES']  = array();
            $_SESSION[$this->Session_Name]['CLASS_ROLES']   = array();
            $_SESSION['PUBLIC'] = 1;
        }
    }

    public function LogInProcess()
    {
        global $ROOT;
        $user  = TransformContent(Post('USER'), 'TQ');
        $pass  = TransformContent(Post('PASS'), 'TQ');

        if ($this->CheckLogin($user, $pass)) {

            session_regenerate_id();
            $_SESSION[$this->Session_Name]['LOGIN_RECORD']  = $this->Login_Record;
            $_SESSION[$this->Session_Name]['LOGIN_OK']      = 'OK';
            $_SESSION[$this->Session_Name]['USER_NAME']     =
                trim($this->Login_Record['first_name'] . ' ' . $this->Login_Record['last_name']);
            $_SESSION[$this->Session_Name]['USER_ID']    = $this->Login_Record[$this->User_Table_Id];
            $_SESSION[$this->Session_Name]['SUPER_USER'] = ArrayValue($this->Login_Record, 'super_user');

            // ==================== get list of roles ====================
            $this->SetLoginRoles();

            // ---- add tracking session for baseclass ----
            $_SESSION['DB_UPDATE_USER_NAME']  = $_SESSION[$this->Session_Name]['USER_NAME'];

            // ===========================================================

            //-------------------- update logfile --------------------
            $line = date('Y-m-d H:i:s'). '|' . $_SESSION[$this->Session_Name]['USER_NAME'] . "\n";
            append_file($ROOT . $this->Logfile, $line);
        }
    }


    public function LogInForm()
    {
        $this->User  = TransformContent(Post('USER'), 'TQ');
        $this->Pass  = TransformContent(Post('PASS'), 'TQ');
        $this->OutputPage();
    }


}
