<?php
class Website_SignupInstructor extends BaseClass
{
    public $Page_Link_Query                 = '';
    public $URL_Login                       = '';
    public $URL_Homepage                    = '/index';
    public $script_location                 = "/signup_instructor";

    
    public $WH_ID                           = 0;
    
    public $Admin_Module_Role               = 37; //yoga_instructor
    public $Admin_Class_Role                = 0;

    public $user_record                     = array();

    public $show_query                      = true;
    public $Current_Step                    = 0;
    public $Current_Step_Array              = null;
    public $Current_Step_Output             = '';

    public $Table_Credits                   = 'credits';


    public $booking_session_credits_list = '';
    
    public $OBJ_STEP                    = null;
    
    public $Step_Array = array(
        1 => 'General Information',
        2 => 'Your Details',
        3 => 'Confirmation',
    );

    public $Step_Array_With_Error = array(
        1 => 'General Information',
        2 => 'Your Details',
        3 => 'ERROR',
        4 => 'Confirmation',
    );
    
    public $Step_Array_Error_Only = array(
        1 => 'ERROR',
    );



    public function  __construct()
    {
        /*
        # IF USER IS CURRENTLY LOGGED IN - NEED TO LOG THEM OUT
        $OBJ_AUTH = new Authentication;
        $OBJ_AUTH->LogoutUserIfLoggedIn();
        */
        
        parent::__construct();
        $this->OBJ_STEP = new General_Steps();
        
        global $URL_SITE_LOGIN;
        $this->URL_Login = $URL_SITE_LOGIN;
        
        $this->Page_Link_Query = preg_replace('/;step=[a-zA-Z0-9_\-]*;/', ';', Server('REQUEST_URI'));  // removes step

    } // -------------- END __construct --------------

    public function HandleStep($step)
    {
        switch ($step) {

            case 'start':
                $this->Current_Step = 1;
                $this->Current_Step_Array = $this->Step_Array;
                
                $btn_cancel     = MakeButton('negative', 'CANCEL SIGNUP', "{$this->script_location};step=cancel");
                $btn_next       = MakeButton('positive', 'NEXT', "{$this->script_location};step=create_account");
                
                $this->Current_Step_Output = "
                    [T~INSTRUCTOR_REGISTRATION_INSTRUCTIONS_START]
                    <br /><br />
                    <div>
                        <div style='border-top:1px solid #9E9D41; padding-bottom:10px;'></div>
                        <div class='col' style='float:left;'>$btn_cancel</div>
                        <div class='col' style='float:right;'>$btn_next</div>
                        <div class='clear'></div>
                    </div>
                ";
            break;
            
            case 'create_account':
                $this->Current_Step = 2;
                $this->Current_Step_Array = $this->Step_Array;
            
                $this->Current_Step_Output = "[T~INSTRUCTOR_REGISTRATION_INSTRUCTIONS]<br /><br />";
            
                $OBJ_ACCOUNT = new Profile_CreateAccountInstructor();
                $OBJ_ACCOUNT->num_free_credits          = 0;
                $OBJ_ACCOUNT->num_paid_credits          = 0;
                $OBJ_ACCOUNT->URL_Success_Redirect      = "{$this->script_location};step=complete_success";
                $OBJ_ACCOUNT->Allow_Email_Not_Unique    = false; // allow duplicate emails
                $OBJ_ACCOUNT->Admin_Module_Role         = $this->Admin_Module_Role;
                $OBJ_ACCOUNT->Admin_Class_Role          = $this->Admin_Class_Role;
                
                $this->Current_Step_Output .= $OBJ_ACCOUNT->ExecuteUserSignup();
                
                
                
                $btn_cancel     = MakeButton('negative', 'CANCEL SIGNUP', "{$this->script_location};step=cancel");
                //$btn_next       = MakeButton('positive', 'NEXT', "{$this->script_location};step=create_account");
                $btn_next = ''; //$OBJ_ACCOUNT->UserSignupSpecialSubmitButton();
                
                
                $this->Current_Step_Output .= "
                    
                    <br /><br />
                    <div>
                        <div style='border-top:1px solid #9E9D41; padding-bottom:10px;'></div>
                        <div class='col' style='float:left;'>$btn_cancel</div>
                        <div class='col' style='float:right;'>$btn_next</div>
                        <div class='clear'></div>
                    </div>
                ";
            break;
            
            case 'cancel':
                $this->Current_Step = 3;
                $this->Current_Step_Array = $this->Step_Array_With_Error;
                $this->Current_Step_Output = "
                    <h2 style='color:#990000;'>Your Signup Has Been Cancelled</h2>
                    [<a class='link_arrow' href='{$this->URL_Homepage}'>Click To Return To Homepage</a>]
                ";
            break;

            case 'complete_success':
                $this->Current_Step = 3;
                $this->Current_Step_Array = $this->Step_Array;
                $this->Current_Step_Output = "
                    [T~INSTRUCTOR_REGISTRATION_SUCCESS]<br /><br />
                    [<a class='link_arrow' href='{$this->URL_Login}'>LOGIN</a>]
                ";
            break;

            default:
                $this->Current_Step = 1;
                $this->Current_Step_Array = $this->Step_Array_Error_Only;
                $this->Current_Step_Output = 'NO STEP PASSED IN';
            break;

        }

        
        $step_output = '';
        #$step_output .= '<center>';
        #$step_output .= '<div style="text-align:left;">';
        $step_output .= $this->OBJ_STEP->GetSteps($this->Current_Step_Array, $this->Current_Step, $this->Current_Step_Output, 700);
        #$step_output .= '</div>';
        #$step_output .= '</center>';
        
        
        AddStyle("
            .stepwrapper {
                background-color:#9E9D41;
            }
            .steps {
                background-color:#EAE6CD;
            }
        ");
        
        return $step_output;
    }



}  // -------------- END CLASS --------------