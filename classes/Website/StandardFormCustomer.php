<?php
class Website_StandardFormCustomer extends BaseClass
{
    public $Page_Link_Query                 = '';
    public $URL_Login                       = '';
    public $URL_Homepage                    = '/index';
    public $script_location                 = "/signup";
    
    // ---------- NON-MODIFIABLE VARIABLES ----------
    public $OBJ_STEP                        = null;
    public $Current_Step                    = 0;
    public $Current_Step_Array              = null;
    public $Current_Step_Output             = '';
    
    
    public $Step_Array = array(
        1 => 'Your Details',
        2 => 'Confirmation',
    );

    public $Step_Array_With_Error = array(
        1 => 'Your Details',
        2 => 'ERROR',
        3 => 'Confirmation',
    );
    
    public $Step_Array_Error_Only = array(
        1 => 'ERROR',
    );



    public function  __construct()
    {
        parent::__construct();
        
        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Description' => 'Output a standard-yoga intake form',
        );
        
        $this->OBJ_STEP = new General_Steps();
        
        global $URL_SITE_LOGIN;
        $this->URL_Login = $URL_SITE_LOGIN;
        
        $this->Page_Link_Query = preg_replace('/;step=[a-zA-Z0-9_\-]*;/', ';', Server('REQUEST_URI'));  // removes step

    } // -------------- END __construct --------------

    public function HandleStep($step)
    {
        switch ($step) {

            case 'start':
            case 'create_account':
                $this->Current_Step = 1;
                $this->Current_Step_Array = $this->Step_Array;
            
                $this->Current_Step_Output = "";
            
                $OBJ_ACCOUNT = new Profile_FormStandardIntake();
                $OBJ_ACCOUNT->URL_Success_Redirect      = "{$this->script_location};step=complete_success";
                
                $this->Current_Step_Output .= $OBJ_ACCOUNT->ExecuteUserSignup();
                
                $btn_cancel     = MakeButton('negative', 'CANCEL SIGNUP', "{$this->script_location};step=cancel");
                $btn_next       = '';
                
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
                $this->Current_Step = 2;
                $this->Current_Step_Array = $this->Step_Array_With_Error;
                $this->Current_Step_Output = "
                    <h2 style='color:#990000;'>Your Signup Has Been Cancelled</h2>
                    [<a class='link_arrow' href='{$this->URL_Homepage}'>Click To Return To Homepage</a>]
                ";
            break;

            case 'complete_success':
                $this->Current_Step = 2;
                $this->Current_Step_Array = $this->Step_Array;
                $this->Current_Step_Output = "
                    [T~USER_REGISTRATION_SUCCESS]<br /><br />
                    [<a class='link_arrow' href='{$this->URL_Login}'>LOGIN</a>]
                ";
            break;

            default:
                $this->Current_Step = 1;
                $this->Current_Step_Array = $this->Step_Array_Error_Only;
                $this->Current_Step_Output = 'NO STEP PASSED IN';
            break;

        }
        
        $step_output = $this->OBJ_STEP->GetSteps($this->Current_Step_Array, $this->Current_Step, $this->Current_Step_Output, 700);
        
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