<?php
class Sessions_Signup extends BaseClass
{
    public $Show_Query                                  = false;
    public $Bypass_Time_Expire_Check                    = true;    // TRUE = Won't check to see if time has expired
    public $Signup_Offline                              = false;    // TRUE = won't allow users to sign up for sessions (get past by doing ;session_booking_online=on before beginning)
    private $FORCE_TEST_ON                              = false;    // TRUE = will turn on a bunch of testing infomration
    public $Bypass_Customer_Discount                    = false;
    public $Show_Time_Area                              = false;
    public $Show_Instructor_Profile                     = false;
    private $Simple_Signup                              = true;     // TRUE = one-page signup
    private $Simple_Signup_Credit_Product_Id            = 0;
    private $Simple_Signup_Credit_Product_Price         = 0;
    private $Simple_Signup_OBJ_STORE                    = null;
    
    
    // ---------- TESTING VARIABLES ----------
    public $TESTING                                     = true;     // TRUE = turns on testing variables
    public $TESTING_Add_Standard_Credits_To_Customer    = 0;        // fake pool of credits in user account;
    public $TESTING_Add_Therapy_Credits_To_Customer     = 0;        // fake pool of credits in user account;
    public $TESTING_Show_Credit_Breakdown               = false;    // TRUE = show how many credits are real and fake
    public $TESTING_Show_Time_Expire_Button             = false;    // TRUE = show button allowing user to 'force' the time countdown to 0 seconds
    public $TESTING_Show_Unlock_Button                  = true;     // TRUE = show button to unlock locked sessions during booking
    public $TESTING_Show_Times                          = false;
    public $TESTING_Bypass_Time_Check                   = false;    // TRUE = Won't check to see if time has expired
    public $TESTING_Show_Processing_Book_Session        = false;    // TRUE = show all database messages while booking the session
    public $TESTING_Force_Fail_Credit_Use               = false;    // TRUE = Causes any use of credits to fail in purchasing session
    
    // ---------- EMAIL VARIABLES ----------
    public $email_send_to_user              = true;
    public $email_send_to_instructor        = true;
    public $email_send_to_admin             = true;
    public $instructor_email_template_id    = 6;
    public $customer_email_template_id      = 7;
    public $Email_Content_Session_Box       = '';
    public $Email_Content_Instructor_Box    = '';
    public $TEMP_INSTRUCTOR_NAME            = 'yll instructor';
    public $TEMP_INSTRUCTOR_EMAIL           = 'yll_instructor@mailwah.com';
    
    public $Default_Credit_Id               = 4;  //<<<<<<<<<<---------- Or get this from a query ----------<<<<<<<<<<
    public $Picture_Dir                     = '/office/';
    public $Image_Dir_Instructor            = 'images/instructors/';
    public $Image_No_Pic                    = 'thumbnail_no_picture.jpg';
    public $script_location                 = "/office/sessions/signup";
    public $Session_Search_URL              = "http://yoga.whhub.com/office/command_central/customer_profile;tab=session_search;action=session_search";
    public $Session_Search_URL_Link         = "/office/command_central/customer_profile;tab=session_search;action=session_search";
    
    public $time_expire_duration            = 600; //(seconds) 300 seconds = 5 mins
    public $time_restart_duration           = 2; //(seconds)
    public $Add_Time_Seconds                = 300; //(seconds)
    
    public $Table_Credits                   = 'credits';
    public $Table_Instructors               = 'instructor_profile';
    public $Table_Contacts                  = 'contacts';
    public $Table_Sessions                  = 'sessions';
    public $Table_Session_Checklists        = 'session_checklists';
    public $Table_Products                  = 'store_products';
    public $Credit_Categories               = 6;
    
    public $credits_style_1                 = "font-weight:bold; font-size:13px;";
    public $credits_style_2                 = "font-weight:bold; font-size:11px;";
    public $credits_style_3                 = "padding:0px 10px 0px 10px; font-size:13px;";
    
    
    
    
    
    // ---------- NON-MODIFIABLE VARIABLES ----------
    public $Page_Link_Query                         = '';
    public $Is_Overlay                              = false;
    public $Session_Expired_Error                   = false;
    public $Session_Locked_Error                    = false;
    public $Session_Locked                          = false;    
    public $WH_ID                                   = 0;
    public $Server_Timezone                         = '';
    
    public $sessions_id                             = 0;
    public $Sessions_Type                           = '';
    public $time_release_user                       = '';
    public $time_release_server                     = '';
    public $time_release_javascript_user            = '';
    public $time_release_javascript_server          = '';    
    public $heq_time_release_user                   = '';
    public $heq_time_release_server                 = '';
    public $session_record                          = array();
    public $user_record                             = array();
    public $current_step                            = 0;
    public $customer_wh_id                          = 0;
    
    public $Result_Email_Customer_Sent              = false;
    public $Result_Email_Instructor_Sent            = false;
    public $booking_session_credits_list            = '';
    public $OBJ_TIMEZONE                            = null; # note ---> might want to do thiswith ajax calls like the user starting a session
    public $OBJ_STEP                                = null; # note ---> might want to do thiswith ajax calls like the user starting a session
    public $Credit_Discount_Standard_Percent        = 0;
    public $Credit_Discount_Standard_Dollar         = 0;
    public $Credit_Discount_Standard_Notice         = '';
    public $Credit_Discount_Therapy_Percent         = 0;
    public $Credit_Discount_Therapy_Dollar          = 0;
    public $Credit_Discount_Therapy_Notice          = '';
    
    
    public $Step_Intake_Array = array(
        1 => 'Session Intake Form',
        #2 => 'Payment',
        #3 => 'Confirmation',
    );
    
    public $Step_Array = array(
        1 => 'Session Information',
        2 => 'Payment',
        3 => 'Confirmation',
    );

    public $Step_Array_With_Error = array(
        1 => 'Session Information',
        2 => 'Payment',
        3 => 'ERROR',
        4 => 'Confirmation',
    );
    
    public $Step_Array_Buy_Credits = array(
        1 => 'Session Information',
        2 => 'Payment',
        3 => 'Credit Card Payment',
        4 => 'Credit Card Payment Result',
        5 => 'Confirmation',
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
            'Description' => 'Customer signing up for a session',
        );
        
        $this->OBJ_TIMEZONE         = new General_TimezoneConversion();
        $this->OBJ_STEP             = new General_Steps();
        $this->WH_ID                = (isset($_SESSION['USER_LOGIN']['LOGIN_RECORD']['wh_id'])) ? $_SESSION['USER_LOGIN']['LOGIN_RECORD']['wh_id'] : 0;
        $this->Server_Timezone      = date_default_timezone_get();
        $this->Page_Link_Query      = preg_replace('/;step=[a-zA-Z0-9_\-]*;/', ';', Server('REQUEST_URI'));  // removes step
        
        
        $this->SetParameters(func_get_args());
        $this->sessions_id          = ($this->GetParameter(0)) ? $this->GetParameter(0) : 0;
        
        $this->Signup_Offline       = (Session('session_booking_online')) ? false : $this->Signup_Offline;
        
        global $TRANSLATION;
        $Translations = array(
            'SS_001'    => 'ERROR',
            'SS_002'    => 'SESSION HAS BEEN BOOKED BY ANOTHER CUSTOMER',
            'SS_003'    => 'CANCEL PURCHASE',
            'SS_004'    => 'YOUR PURCHASE SESSION HAS BEEN CANCELLED',
            'SS_005'    => 'RETURN TO SESSION SEARCH',
            'SS_006'    => 'YOUR YOGA SESSION HAS BEEN BOOKED<br /><br />You will shortly receive an email confirmation with your session information.',
            'SS_007'    => 'RETURN TO SESSION SEARCH',
            'SS_008'    => 'NO STEP PASSED IN',
            'SS_009'    => '',
            'SS_010'    => '',
            'SS_011'    => '',
            'SS_012'    => '',
            'SS_013'    => '',
            'SS_014'    => '',
            'SS_015'    => '',
            'SS_016'    => '',
            'SS_017'    => '',
            'SS_018'    => '',
            'SS_019'    => '',
            'SS_020'    => '',
        );
        $TRANSLATION->AddWordsToTranlateArrayFake($Translations);
        
        
        # INITIALIZE OVERLAY
        # ===================================
        $this->Is_Overlay = (Get('template')=='overlay') ? true : false;
        if ($this->Is_Overlay) {
            $this->Page_Link_Query .= ';template=overlay';
            $this->script_location .= ';template=overlay';
        }
        
        
        # INITIALIZE PAGE LINKS
        # ===================================
        global $PAGE;
        $this->Page_Link_Query                 = "/office/{$PAGE['pagename']};{$PAGE['query']}";
        $this->Script_Location                 = "/office/{$PAGE['pagename']};{$PAGE['query']}";
        
    } // -------------- END __construct --------------

    
    public function Execute()
    {
        $this->sessions_id  = (Get('sid')) ? Get('sid') : $this->sessions_id;
        
        $step               = (Get('step')) ? Get('step') : 'start';
        $output             = $this->HandleStep($step);
        
        echo $output;
    }
    
    
    public function HandleStep($step)
    {
        # TURN ON SSL IMAGES IN STEP HEADER
        # ===================================================
        $this->OBJ_STEP->SSL_Enabled    = true;
        $this->OBJ_STEP->SSL_Image      = "<img src='/office/images/godaddy_verified.png' alt='Verified by GoDaddy' border='0' />";
        
        
        # IF USER NOT LOGGED IN - GO TO LOGIN/SIGNUP PAGE
        # ===================================================
        if (!isset($_SESSION['USER_LOGIN']['LOGIN_RECORD']['wh_id']) && $step != 'signup_summary') {
            $link = $this->MakeStepLink('signup_summary');
            header("Location: {$link}");
            #exit();
        }
        
        
        # PROCESS THE EXPIRATION TIME
        # ===================================================
        $testing_bypass = ($this->TESTING && $this->TESTING_Bypass_Time_Check) ? true : false;
        if (Get('tr') && !$this->Bypass_Time_Expire_Check && !$testing_bypass) {
        
            $time_release_server = HexDecodeString(Get('tr'));
            $this->GetSessionExpireTime($time_release_server);
            
            if ($this->Session_Expired && Get('step')!='timeexpire') {
                $link = $this->MakeStepLink('timeexpire');
                header("Location: {$link}");
            }
        }
        
        
        # GET THE SESSION TYPE
        # ===================================================
        $this->Sessions_Type = (Get('stype')) ? Get('stype') : 'standard';
        
        
        # MODIFY THE SENT IN STEP - IF NEEDED
        # ===================================================
        $step = ($this->Simple_Signup && $step=='start') ? 'start_simple_signup' : $step;
        $step = ($this->Signup_Offline) ? 'offline' : $step;
        
        
        # CREATE THE CONTENT
        # ===================================================
        $output = '';
        switch ($step) {
            case 'offline':
                $output .= '<h2><div style="color:#990000;">The booking system is currently OFFLINE. Please check back later or contact support@YogaLiveLink.com.</div></h2>';
                $step_output = $this->OBJ_STEP->GetSteps($this->Step_Array, 3, $output, 700);
            break;
            
            case 'start':
                $this->current_step = 1;
                $this->GetSessionRecord();
                
                if ($this->session_record['booked']) {
                    $link = $this->MakeStepLink('booked');
                    header("Location: {$link}");
                }
                
                $this->CheckIfSessionLocked();
                
                if ($this->Session_Locked_Error) {
                    # THIS SESSION HAS BEEN LOCKED BY ANOTHER USER
                    # ===================================================
                    $output .= '<br />' . AddBox_Error('[T~SS_001]', '[T~SS_002]') . '<br /><br />';
                    
                    if ($this->Is_Overlay) {
                        $output .= MakeButton('negative', 'GO BACK TO SEARCH', '', '', '', "parent.CloseOverlay();");
                    } else {
                        $output .= MakeButton('negative', 'GO BACK TO SEARCH', "{$this->Session_Search_URL_Link}");
                    }
                    
                    #$session_info_hidden = $this->ShowSessionInformation();
                    #$output .= "<div style='disp_lay:none;'>$session_info_hidden</div>";
                    
                    # LET TESTING USERS UNLOCK A SESSION
                    # ===================================================
                    if ($this->TESTING && $this->TESTING_Show_Unlock_Button) {
                        $output .= MakeButton('negative', 'UNLOCK SESSION', $this->MakeStepLink('testing_unlock_session'));
                    }    
                } else {
                    # LOCK THE SESSION
                    # ============================================================
                    if (!$this->Session_Locked) {
                        $this->LockSession();
                    }
                    
                    # VERIFY THAT TIME HASN'T EXPIRED DURING A BROWSER REFRESH
                    # ============================================================
                    $this->GetSessionExpireTime(); # has to be done AFTER the Session_Locked check
                    if ($this->Session_Expired) {
                        $link = $this->MakeStepLink('timeexpire');
                        header("Location: {$link}");
                    }
                
                    # OUTPUT CONTENT
                    # ============================================================
                    $output .= $this->ShowSessionInformation();
                }

                $step_output = $this->OBJ_STEP->GetSteps($this->Step_Array, 1, $output, 700);
            break;

# ============================================================================================================================================
# ============================================================================================================================================
# ============================================================================================================================================
            case 'start_simple_signup':
                $this->current_step = 2;
                $this->GetSessionRecord();
                
                # CHECK IF BOOKED
                # ===================================================
                if ($this->session_record['booked']) {
                    $link = $this->MakeStepLink('booked');
                    header("Location: {$link}");
                }
                
                # IF THIS IS A MULTI-TYPE SESSION - FIND OUT HOW TO BOOK IT
                # - Note: Session not locked at this point in time
                # ===================================================
                if ($this->session_record['type_standard'] && $this->session_record['type_therapy'] && !Get('stype')) {
                    $link = $this->MakeStepLink('select_session_type_simple_signup');
                    header("Location: {$link}");
                }
                
                
                $this->CheckIfSessionLocked();
                
                if ($this->Session_Locked_Error) {
                    # THIS SESSION HAS BEEN LOCKED BY ANOTHER USER
                    # ===================================================
                    $output .= '<br />' . AddBox_Error('[T~SS_001]', '[T~SS_002]') . '<br /><br />';
                    
                    if ($this->Is_Overlay) {
                        $output .= MakeButton('negative', 'GO BACK TO SEARCH', '', '', '', "parent.CloseOverlay();");
                    } else {
                        $output .= MakeButton('negative', 'GO BACK TO SEARCH', "{$this->Session_Search_URL_Link}");
                    }
                    
                    #$session_info_hidden = $this->ShowSessionInformation();
                    #$output .= "<div style='disp_lay:none;'>$session_info_hidden</div>";
                    
                    # LET TESTING USERS UNLOCK A SESSION
                    # ===================================================
                    if ($this->TESTING && $this->TESTING_Show_Unlock_Button) {
                        $output .= MakeButton('negative', 'UNLOCK SESSION', $this->MakeStepLink('testing_unlock_session'));
                    }    
                } else {
                    # LOCK THE SESSION
                    # ============================================================
                    if (!$this->Session_Locked) {
                        $this->LockSession();
                    }
                    
                    # VERIFY THAT TIME HASN'T EXPIRED DURING A BROWSER REFRESH
                    # ============================================================
                    $this->GetSessionExpireTime(); # has to be done AFTER the Session_Locked check
                    if ($this->Session_Expired) {
                        $link = $this->MakeStepLink('timeexpire');
                        header("Location: {$link}");
                    }
                
                    # OUTPUT CONTENT
                    # ============================================================
                    //$output .= "<br /><h1>SIMPLE SIGNUP MODE</h1><br />";
                    $output .= $this->ShowSessionInformation();         // session information
                    $output .= $this->SimpleSignupPaymentDisplay();     // payment options
                    $output .= $this->ShowSimpleSignupFooter();         // footer - time left and misc buttons
                    
                    
                    
                    # HANDLE RESULTS FROM PAYMENT PROCESS - IF NEEDED
                    # ============================================================
                    if (isset($this->Simple_Signup_OBJ_STORE) && $this->Simple_Signup_OBJ_STORE->Status == 'OK') {
                        // buy_credits_success
                        
                        # ADD ADDITIONAL TIME TO CHECKOUT PROCESS
                        $this->AddTimeToSessionExpireTime($this->Add_Time_Seconds);
                        
                        # REDIRECT TO PAYMENT PROCESSING
                        $link = $this->MakeStepLink('payment_process');
                        header("Location: {$link}");
                    }
                 
                    
                    
                    
                    
                }

                $step_output = $this->OBJ_STEP->GetSteps($this->Step_Array, 1, $output, 700);
            
            break;
# ============================================================================================================================================
# ============================================================================================================================================
# ============================================================================================================================================
            
            case 'signup_summary':
                $output .= '<h4>In order to book this session you must first Log-In or create an account. Click the continue button below to continue. Once you have completed the Log-In or Sign-Up process you will be returned to book this session. However, you only have a limited time before this session will be released and another user may book it.</h4>';
                
                //$_SESSION['LOGIN_RETURN_URL'] = $this->Page_Link_Query;
                $_SESSION['LOGIN_RETURN_URL'] = $this->MakeStepLink('start');
                
                $output .= MakeButton('positive', 'CONTINUE', '/office/index');
                //$output .= MakeButton('negative', 'GO BACK TO SEARCH', '', '', '', "parent.CloseOverlay();");
                $output .= MakeButton('positive', 'GO BACK TO SEARCH', '', '', '', "top.window.location.reload();");
                $step_output = $this->OBJ_STEP->GetSteps($this->Step_Array, 1, $output, 700);
            break;
            
            case 'payment':
                $this->current_step = 2;
                $this->GetSessionRecord();
                $output .= $this->ShowPaymentInformation();

                $step_output = $this->OBJ_STEP->GetSteps($this->Step_Array, 2, $output, 700);
            break;

            case 'payment_process':
                $this->current_step = 2;
                $this->GetSessionRecord();
                $output .= $this->BookSession();

                $step_output = $this->OBJ_STEP->GetSteps($this->Step_Array, 2, $output, 700);
            break;
            
            case 'select_session_type':
                $box_content    = "<a href='".$this->MakeStepLink('payment').";stype=standard'>Click HERE to<br />Book this as a Yoga Session</a>";
                $box_standard   = AddBox_Type4('YOGA SESSION', $box_content);
                
                $box_content    = "<a href='".$this->MakeStepLink('payment').";stype=therapy'>Click HERE to<br />Book this as a Yoga Therapy Session</a>";
                $box_therapy    = AddBox_Type4('YOGA THERAPY SESSION', $box_content);
                
                $output         .= '<h4>This session may be booked as either a standard "Yoga Session" or as a "Yoga Therapy Session". Please select the type of session you would like to book.</h4>';
                $output         .= "
                <table border='0' align='center'>
                    <tr>
                    <td>$box_standard</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>$box_therapy</td>
                    </tr>
                </table>
                <br /><br />
                ";
                $output .= $this->ShowTimeRemainingInformation();
                $output .= "<br /><br />";
                $output .= MakeButton('negative', 'GO BACK', $this->MakeStepLink('start'));
                $step_output = $this->OBJ_STEP->GetSteps($this->Step_Array, 1, $output, 700);
            break;
            
            case 'select_session_type_simple_signup':
                $box_content    = "<a href='".$this->MakeStepLink('start_simple_signup').";stype=standard'>Click HERE to<br />Book this as a Yoga Session</a>";
                $box_standard   = AddBox_Type4('YOGA SESSION', $box_content);
                
                $box_content    = "<a href='".$this->MakeStepLink('start_simple_signup').";stype=therapy'>Click HERE to<br />Book this as a Yoga Therapy Session</a>";
                $box_therapy    = AddBox_Type4('YOGA THERAPY SESSION', $box_content);
                
                $output         .= '<h4>This session may be booked as either a standard "Yoga Session" or as a "Yoga Therapy Session". Please select the type of session you would like to book.</h4>';
                $output         .= "
                <table border='0' align='center'>
                    <tr>
                    <td>$box_standard</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>$box_therapy</td>
                    </tr>
                </table>
                <br /><br />
                ";
                #$output .= $this->ShowTimeRemainingInformation();
                $output .= "<br /><br />";
                $output .= MakeButton('negative', 'GO BACK', $this->MakeStepLink('start'));
                $step_output = $this->OBJ_STEP->GetSteps($this->Step_Array, 1, $output, 700);
            break;
            
            case 'buy_credits':
                $product_id     = (Get('product_id'))? Get('product_id') : $this->Default_Credit_Id;
                $price          = (Get('product_price'))? Get('product_price') : 0;
                
                #$skip_processing_card = true;
                
                #if (!$skip_processing_card) {
                    $STORE = new Store_YogaStoreCreditOrder();
                    $STORE->SetDiscountPriceOnProduct($product_id, $price);
                    $STORE->SetCart($product_id);
                    $output .= $STORE->ProcessOrderPage();
                #}
                
                $this->GetSessionRecord();
                
                
                
                if ($STORE->Status == 'OK') {
                    # buy_credits_success
                    
                    # ADD ADDITIONAL TIME TO CHECKOUT PROCESS
                    $this->AddTimeToSessionExpireTime($this->Add_Time_Seconds);
                    
                    //$output .= '<br /><br />';
                    
                    
                    $box_content    = "<a href='".$this->MakeStepLink('payment_process')."'>Click HERE to<br />Continue and BOOK SESSION</a>";
                    $box_book       = AddBox_Type4('BOOK SESSION', $box_content);
                    
                    $box_content    = "<a href='".$this->MakeStepLink('payment')."'>Click HERE to<br />Go back to payment overview page</a>";
                    $box_payment    = AddBox_Type4('PAYMENT OVERVIEW', $box_content);
                    
                    
                    $output        .= "
                    <table border='0' align='center'>
                        <tr>
                        <td>$box_book</td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td>$box_payment</td>
                        </tr>
                    </table>
                    <br /><br />
                    ";
                    
                    $output .= $this->ShowTimeRemainingInformation();
                    //$output .= '<br /><br />';
                    //$output .= MakeButton('positive', 'CONTINUE', $this->MakeStepLink('payment'));
                    $step_output = $this->OBJ_STEP->GetSteps($this->Step_Array_Buy_Credits, 4, $output, 700);
                } elseif ($STORE->Status == 'FAILED') {
                    # buy_credits_failed
                    $output .= $this->ShowTimeRemainingInformation();
                    $output .= '<br /><br />';
                    $output .= MakeButton('negative', 'GO BACK', $this->MakeStepLink('payment'));
                    $step_output = $this->OBJ_STEP->GetSteps($this->Step_Array_Buy_Credits, 4, $output, 700);
                } else {
                    # buying credits
                    
                    # ADD ADDITIONAL TIME TO CHECKOUT PROCESS
                    $this->AddTimeToSessionExpireTime($this->Add_Time_Seconds);
                    
                    $output .= $this->ShowTimeRemainingInformation();
                    $output .= '<br /><br />';
                    $output .= MakeButton('negative', 'GO BACK', $this->MakeStepLink('payment'));
                    $step_output = $this->OBJ_STEP->GetSteps($this->Step_Array_Buy_Credits, 3, $output, 700);
                }

            break;
            
            case 'timeexpire':
                $this->current_step = 1;
                $output .= $this->ShowTimeExpiredInformation();
                
                $step_output = $this->OBJ_STEP->GetSteps($this->Step_Array_With_Error, 3, $output, 700);
            break;

            case 'cancel':
                $this->current_step = 3;
                $this->UnlockSession();
                $output .= "<center>";
                $output .= "<h2 style='color:#990000;'>[T~SS_004]</h2>";
                
                if ($this->Is_Overlay) {
                    $output .= MakeButton('negative', 'GO BACK TO SEARCH', '', '', '', "parent.CloseOverlay();");
                } else {
                    $output .= MakeButton('positive', '[T~SS_005]', "{$this->Session_Search_URL}");
                }
                
                $output .= "</center>";
                
                $step_output = $this->OBJ_STEP->GetSteps($this->Step_Array_With_Error, 3, $output, 700);
            break;

            case 'booked_success':
                $this->current_step = 3;
                $output .= "<center>";
                $output .= "<h2 style='color:#990000;'>[T~SS_006]</h2>";
                $output .= "<br /><br />";
                
                
                switch ($this->Sessions_Type) {
                    default:
                    case 'standard':
                        $eq_IntakeForm      = EncryptQuery("class=Profile_FormStandardIntake;v1={$this->sessions_id};v2={$this->WH_ID};v3=true");
                        $link_intake_form   = getClassExecuteLinkNoAjax("{$eq_IntakeForm}") . ';template=overlay';
                    break;
                    case 'therapy':
                        $eq_IntakeForm      = EncryptQuery("class=Profile_FormTherapyIntake;v1={$this->sessions_id};v2={$this->WH_ID};v3=true");
                        $link_intake_form   = getClassExecuteLinkNoAjax("{$eq_IntakeForm}") . ';template=overlay';
                    break;
                }
                
                $box_content    = "Filling out your Health Information form helps your Instructor tailor the best session for you. If you would like to update your information - click the button below.<br /><br />";
                $box_content   .= MakeButton('positive', 'GO TO HEALTH/FITNESS FORM', $link_intake_form);
                $box_1          = AddBox_Type4('HEALTH/FITNESS FORM', $box_content);
                
                
                if ($this->Is_Overlay) {
                    //$btn_close = MakeButton('positive', 'CLOSE WINDOW', '', '', '', "parent.CloseOverlay();");
                    $btn_close = MakeButton('positive', 'CLOSE WINDOW', '', '', '', "top.window.location.reload();");
                } else {
                    $btn_close = MakeButton('positive', 'CLOSE WINDOW', "{$this->Session_Search_URL}");
                }
                
                $box_content    = "<br />$btn_close<br />";
                $box_2          = AddBox_Type4('&nbsp;', $box_content);
                
                $output         .= "
                <table border='0' align='center'>
                    <tr>
                    <td width='75%'>$box_1</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <tdwidth='25%'>$box_2</td>
                    </tr>
                </table>
                <br /><br />
                ";
                
                
                
                
                $output .= "</center>";
                
                $step_output = $this->OBJ_STEP->GetSteps($this->Step_Array, 3, $output, 700);
            break;
            
            case 'intake_form':
                $this->current_step = 3;

                $output .= "<br />WH_ID ===> $this->WH_ID";
                $output .= "<br />sessions_id ===> $this->sessions_id";
                $output .= "<br /><br />";

                $output .= "<center>";
                #$output .= "<h2 style='color:#990000;'>[T~SS_006]</h2>";
                //$output .= MakeButton('positive', '[T~SS_005]', "{$this->Session_Search_URL}");
                
                if ($this->Is_Overlay) {
                    //$output .= MakeButton('positive', 'RETURN TO SESSION SEARCH', '', '', '', "parent.CloseOverlay();");
                    $output .= MakeButton('positive', 'RETURN TO SESSION SEARCH', '', '', '', "top.window.location.reload();");
                } else {
                    $output .= MakeButton('positive', 'RETURN TO SESSION SEARCH', "{$this->Session_Search_URL}");
                }
                
                $output .= "</center>";
                
                $step_output = $this->OBJ_STEP->GetSteps($this->Step_Intake_Array, 1, $output, 700);
            break;
            
            case 'testing_unlock_session':
                $this->current_step = 3;
                $this->UnlockSession();
                $output .= "<h1>SESSION HAS BEEN UNLOCKED</h1><br />";
                $output .= MakeButton('positive', 'GO BACK TO BOOK SESSION', $this->MakeStepLink('start'));
                
                $step_output = $this->OBJ_STEP->GetSteps($this->Step_Array_With_Error, 3, $output, 700);
            break;
            
            case 'booked':
                $this->current_step = 3;
                $output .= "<h1>SESSION HAS ALREADY BEEN BOOKED</h1><br />";
                
                if ($this->Is_Overlay) {
                    $output .= MakeButton('positive', 'RETURN TO SESSION SEARCH', '', '', '', "parent.CloseOverlay();");
                } else {
                    $output .= MakeButton('positive', 'RETURN TO SESSION SEARCH', "{$this->Session_Search_URL}");
                }
                
                $step_output = $this->OBJ_STEP->GetSteps($this->Step_Array_With_Error, 3, $output, 700);
            break;
            
            default:
                $output .= '[T~SS_008]';

                $step_output = $this->OBJ_STEP->GetSteps($this->Step_Array, 1, $output, 700);
            break;
            
            /*
            case 'test_time':
                $this->GetSessionRecord();
                #$this->GetSessionExpireTime();
                
                $this->AddTimeToSessionExpireTime($this->Add_Time_Seconds);
                echo "<br />time_release_user ==> " . $this->time_release_user;
                
                #$output .= $this->ShowPaymentInformation();
                $output .= $this->ShowTimeRemainingInformation();
                $output .= MakeButton('positive', 'CONTINUE', $this->MakeStepLink('test_time_2'));
                $step_output = $this->OBJ_STEP->GetSteps($this->Step_Array, 2, $output, 700);
            break;
            */
            
            /*
            case 'test_time_2':
                $this->GetSessionRecord();
                #$this->GetSessionExpireTime();
                
                $output .= $this->ShowTimeRemainingInformation();
                $output .= MakeButton('positive', 'CONTINUE', $this->MakeStepLink('test_time'));
                $step_output = $this->OBJ_STEP->GetSteps($this->Step_Array, 2, $output, 700);
            break;
            */
            
            /*
            case 'signup_summary_TEMP':
                $output .= '<h4>DEVELOPMENT - LOG INTO SYSTEM BEFORE BOOKING THIS SESSION</h4>';
                
                //$_SESSION['LOGIN_RETURN_URL'] = $this->Page_Link_Query;
                
                //$output .= MakeButton('positive', 'CONTINUE', '/office/index');
                $output .= MakeButton('negative', 'GO BACK TO SEARCH', '', '', '', "parent.CloseOverlay();");
                $step_output = $this->OBJ_STEP->GetSteps($this->Step_Array, 1, $output, 700);
            break;
            */

        }

        $this->AddScript();

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

    public function UnlockSession()
    {
        $key_values = $this->FormatDataForUpdate(array(
            'locked' => 0,
            'locked_wh_id' => '',
            'locked_start_datetime' => '',
        ));
        $record = $this->SQL->UpdateRecord(array(
            'table'         => 'sessions',
            'key_values'    => $key_values,
            'where'         => "`sessions_id`='{$this->sessions_id}' AND active=1",
        ));
        if ($this->Show_Query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
    }


    public function LockSession()
    {
        $tmp_date = Date("Y-m-d H:i:s");
        $key_values = $this->FormatDataForUpdate(array(
            'locked' => 1,
            'locked_wh_id' => $this->WH_ID,
            'locked_start_datetime' => $tmp_date,
        ));
        $record = $this->SQL->UpdateRecord(array(
            'table'         => 'sessions',
            'key_values'    => $key_values,
            'where'         => "`sessions_id`='{$this->sessions_id}' AND active=1",
        ));
        if ($this->Show_Query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;


        # UPDATE THE SESSION WITH NEWLY LOCKED INFORMATION - SAVES A RECALL OF DATABASE RECORD
        $this->session_record['locked']                 = 1;
        $this->session_record['locked_wh_id']           = $this->WH_ID;
        $this->session_record['locked_start_datetime']  = $tmp_date;
    }


    public function BookSession()
    {
        $output             = '';
        $output_return      = '';
        $cost_in_credits    = ($this->session_record['credits_cost']) ? $this->session_record['credits_cost'] : 0;

        $show_msg = ($this->TESTING && $this->TESTING_Show_Processing_Book_Session) ? true : false;
        
        
        # VERIFY ALL NEEDED VARIABLES ARE PRESENT
        # ============================================================
        $passed = true;
        $passed = ($this->sessions_id == 0) ? false : $passed;
        $passed = ($this->Sessions_Type == '') ? false : $passed;
        $passed = ($this->WH_ID == 0) ? false : $passed;
        $passed = ($this->TESTING && $this->TESTING_Force_Fail_Credit_Use) ? false : $passed;
        
        
        
        # START TRANSACTION
        # ============================================================
        if ($show_msg) $output .= "<div class='success_message'>PROCESSING TRANSACTION...</div>";
        if ($show_msg) $output .= "<div class='success_message'>TRANSACTION STARTED</div>";
        $this->SQL->StartTransaction();



        # BLOCK OUT THE CREDITS
        # ============================================================
        if ($passed) {
            switch($this->Sessions_Type) {
                default:
                case 'standard':
                    $query_credit_type = "`type_standard`=1";
                break;
                case 'therapy':
                    $query_credit_type = "`type_therapy`=1";
                break;
            }
            $key_values = $this->FormatDataForUpdate(array(
                'used'          => 1,
                'sessions_id'   => $this->sessions_id,
            ));
            $result = $this->SQL->UpdateRecord(array(
                'table'         => $this->Table_Credits,
                'key_values'    => $key_values,
                'where'         => "`wh_id`=$this->WH_ID AND `used`=0 AND `active`=1 AND $query_credit_type LIMIT $cost_in_credits",
            ));
            if ($this->Show_Query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;

            $passed = (!$result || $this->SQL->Affected_Rows != $cost_in_credits) ? false : $passed;
            $output .= ($passed) ? "<div class='success_message'>Using Credits PASSED</div>" : "<div class='error_message'>Using Credits FAILED - You do not have enough credits available!</div>";

            if (!$passed) {
                if ($show_msg) $output .= "<div class='error_message'>ROLLING BACK TRANSACTION</div>";
                $this->SQL->Rollback();
            }
        }



        # ADD NOTES TO  THE CREDITS
        # ============================================================
        if ($passed) {
            $date = date('now');
            $note = "\n\n [Date:$date] Credit Used to purchase session (session_id=$this->sessions_id).";
            $result = $this->SQL->AppendValue(array(
                'table'         => $this->Table_Credits,
                'key'           => 'notes',
                'value'         => "$note",
                'where'         => "`wh_id`=$this->WH_ID AND `sessions_id`='{$this->sessions_id}' AND `used`=1 AND `active`=1",
                'limit'         => $cost_in_credits,
            ));
            if ($this->Show_Query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;

            $passed = (!$result || $this->SQL->Affected_Rows != $cost_in_credits) ? false : $passed;
            if ($show_msg) $output .= ($passed) ? "<div class='success_message'>Adding Notes To Credits PASSED</div>" : "<div class='error_message'>Adding Notes To Credits FAILED</div>";

            if (!$passed) {
                if ($show_msg) $output .= "<div class='error_message'>ROLLING BACK TRANSACTION</div>";
                $this->SQL->Rollback();
            }
        }



        # CREATE THE CHECKLIST
        # ============================================================
        if ($passed) {

            # GENERATE A CODE
            # ====================================================
            do {
                # generate a code
                $code = GenerateCode();

                # verify code is unique
                $unique = $this->SQL->IsUnique(array(
                    'table' => $this->Table_Session_Checklists,
                    'key'   => 'payment_id',
                    'value' => $code,
                ));
            } while (!$unique);

            $FormArray = array(
                'sessions_id'   => $this->sessions_id,
                'wh_id'         => $this->WH_ID,
                'paid'          => 1,
                'payment_id'    => $code,
            );

            $keys_values    = $this->FormatDataForInsert($FormArray);
            $parts          = explode('||', $keys_values);
            $keys           = $parts[0];
            $values         = $parts[1];

            $result = $this->SQL->AddRecord(array(
                'table'     => $this->Table_Session_Checklists,
                'keys'      => $keys,
                'values'    => $values,
            ));
            if ($this->Show_Query) echo "<br /><br />LAST QUERY = " . $this->SQL->Db_Last_Query;

            $passed = (!$result) ? false : $passed;
            if ($show_msg) $output .= ($passed) ? "<div class='success_message'>Session Checklist Created PASSED</div>" : "<div class='error_message'>Session Checklist Created FAILED</div>";

            if (!$passed) {
                if ($show_msg) $output .= "<div class='error_message'>ROLLING BACK TRANSACTION</div>";
                $this->SQL->Rollback();
            }
        }
        
        
        
        # UPDATE THE SESSION RECORD
        # ============================================================
        if ($passed) {
            $key_values = $this->FormatDataForUpdate(array(
                'booked'                => 1,
                'booked_wh_id'          => $this->WH_ID,
                'locked'                => 0,
                'locked_wh_id'          => 0,
                'locked_start_datetime' => '',
            ));
            $result = $this->SQL->UpdateRecord(array(
                'table'         => $GLOBALS['TABLE_sessions'],
                'key_values'    => $key_values,
                'where'         => "`sessions_id`=$this->sessions_id", // AND `active`=1
            ));
            if ($this->Show_Query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
            
            $passed = (!$result) ? false : $passed;
            $output .= ($passed) ? "<div class='success_message'>Session marked as booked PASSED</div>" : "<div class='error_message'>Session marked as booked FAILED</div>";
            
            if (!$passed) {
                if ($show_msg) $output .= "<div class='error_message'>ROLLING BACK TRANSACTION</div>";
                $this->SQL->Rollback();
            }
        }
        
        
        
        # SEND VARIOUS EMAILS
        # ============================================================
        if ($passed) {
            $this->SendRegistrationSuccessEmail();
            $send_customer      = ($this->Result_Email_Customer_Sent) ? 1 : 0;
            $send_instructor    = ($this->Result_Email_Instructor_Sent) ? 1 : 0;
            
            
            # UPDATE THE CHECKLIST
            # ==============================================================
            $key_values = $this->FormatDataForUpdate(array(
                'email_booked_user_sent'        => $send_customer,
                'email_booked_instructor_sent'  => $send_instructor,
            ));
            $result = $this->SQL->UpdateRecord(array(
                'table'         => $GLOBALS['TABLE_session_checklists'],
                'key_values'    => $key_values,
                'where'         => "`sessions_id`={$this->sessions_id}",
            ));
            if ($this->Show_Query) echo '<br /><br />QUERY => ' . $this->SQL->Db_Last_Query;
            
            $passed = (!$result) ? false : $passed;
            $output .= ($passed) ? "<div class='success_message'>Session marked as booked PASSED</div>" : "<div class='error_message'>Session marked as booked FAILED</div>";
            
            if (!$passed) {
                if ($show_msg) $output .= "<div class='error_message'>ROLLING BACK TRANSACTION</div>";
                $this->SQL->Rollback();
            }
        }
        
        
        
        # COMMIT CHANGES
        # ============================================================
        if ($passed) {
            if ($show_msg) $output .= "<div class='success_message'>COMMITING TRANSACTION</div>";
            $this->SQL->TransactionCommit();
            
            if ($show_msg) {
                $output_return .= AddBox('', $output);
                $output_return .= MakeButton('positive', 'SUCCESS', $this->MakeStepLink('booked_success'));
            } else {
                $link = $this->MakeStepLink('booked_success');
                header("Location: $link");
            }
            
        } else {
            $output_return .= AddBox('TRANSACTION STATUS', $output);
            $output_return .= '<br /><br />';

            
            
            $btn_cancel = MakeButton('negative', 'CANCEL PURCHASE', $this->MakeStepLink('cancel'));
            $btn_back   = MakeButton('positive', 'GO BACK', $this->MakeStepLink('payment'));
            
            $title = "<div class='error_message'>AN ERROR HAS OCCURRED - UNABLE TO COMPLETE PURCHASE</div>";
            $content = "<div style='font-size:13px;'>
                An error has occurred and we were unable to complete your transaction. <b>No credits have been debited from your account.</b> 
                Please use the Back button and try your purchase again. If you get this error again - please contact support@yogalivelink.com.</div>
                <br /><br /><div style='text-align:center'>{$btn_back}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$btn_cancel}</div><br />";

            $output_return .= AddBox($title, $content);
        }




        return $output_return;
    }


    public function GetSessionRecord()
    {
        if (!$this->session_record || ($this->session_record['sessions_id'] != $this->sessions_id)) {
            $this->session_record = $this->SQL->GetRecord(array(
                'table' => $this->Table_Sessions,
                'keys'  => "{$this->Table_Sessions}.*, {$this->Table_Instructors}.*",
                'where' => "`sessions_id`='{$this->sessions_id}' AND {$this->Table_Sessions}.active=1",
                'joins' => "LEFT JOIN {$this->Table_Instructors} ON {$this->Table_Instructors}.wh_id = {$this->Table_Sessions}.instructor_id",
            ));
            if ($this->Show_Query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        }
        $this->Session_Locked = $this->session_record['locked'];
    }


    public function GetUserCreditDiscount()
    {
        global $USER_LOCAL_TIMEZONE;
        
        # GET USER DISCOUNTS
        # =============================
        if (!$this->Bypass_Customer_Discount) {
        
            $record = $this->SQL->GetRecord(array(
                'table' => $this->Table_Contacts,
                'keys'  => "`{$this->Table_Contacts}`.contact_discounts_id, `{$this->Table_Contacts}`.created AS USER_CREATED, {$GLOBALS['TABLE_contact_discounts']}.*",
                'where' => "`{$this->Table_Contacts}`.`wh_id`='{$this->WH_ID}' AND `{$this->Table_Contacts}`.active=1",
                'joins' => "LEFT JOIN `{$GLOBALS['TABLE_contact_discounts']}` ON `{$GLOBALS['TABLE_contact_discounts']}`.`contact_discounts_id` = `{$this->Table_Contacts}`.`contact_discounts_id`",
            ));
            if ($this->Show_Query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
            if ($this->FORCE_TEST_ON) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
            
            if ($record) {
                $user_created_date  = $record['USER_CREATED'];
                
                $days_standard      = $record['discount_credits_days'];
                $discount_standard  = $record['discount_credits'];
                $type_standard      = $record['discount_credits_type'];
                
                $days_therapy       = $record['discount_credits_therapy_days'];
                $discount_therapy   = $record['discount_credits_therapy'];
                $type_therapy       = $record['discount_credits_therapy_type'];
                
                # CONVERT CREATED_DATE TO FORMAT FOR DOING TIME DIFFERENCE
                # =================================================================
                $input_date_time        = $user_created_date;
                $input_timezone         = $USER_LOCAL_TIMEZONE;
                $output_timezone        = $USER_LOCAL_TIMEZONE;
                $output_format          = 'm/d/Y H:i:s';
                
                if ($this->FORCE_TEST_ON) echo "<br /><br />ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format)<br /><br />";
                $created_date_funct     = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
                
                # CALCULATE TIME DIFFERENCE - TO SEE IF DISCOUNT STILL ACTIVE
                # =================================================================
                $now            = strtotime("now");
                $end            = strtotime($created_date_funct);
                $days_diff      = floor(((($now - $end)/60)/60)/24);
                
                if ($this->FORCE_TEST_ON) echo "<br />now ---> $now";
                if ($this->FORCE_TEST_ON) echo "<br />end ---> $end";
                if ($this->FORCE_TEST_ON) echo "<br />days_diff ---> $days_diff";
                
                # CALCULATE STANDARD DISCOUNT
                # =================================================================
                if ($days_diff < $days_standard) {
                
                    # DISCOUNT IS USABLE
                    # =============================
                    if ($this->FORCE_TEST_ON) echo "<br /> ===== USE DISCOUNT =====";
                    
                    $this->Credit_Discount_Standard_Percent  = ($type_standard == 'percent') ? $discount_standard : $this->Credit_Discount_Standard_Percent;
                    $this->Credit_Discount_Standard_Dollar   = ($type_standard == 'dollar') ? $discount_standard : $this->Credit_Discount_Standard_Dollar;
                    $this->Credit_Discount_Standard_Notice   = ''; //"<div class='discount_notice'>You are receiving a discount because you <br />are still within the first 30 days of your account.</div>";
                    
                    if ($this->FORCE_TEST_ON) echo "<br />Credit_Discount_Standard_Percent ---> {$this->Credit_Discount_Standard_Percent}";
                    if ($this->FORCE_TEST_ON) echo "<br />Credit_Discount_Standard_Dollar ---> {$this->Credit_Discount_Standard_Dollar}";
                }
                
                # CALCULATE THERAPY DISCOUNT
                # =================================================================
                if ($days_diff < $days_therapy) {
                    # DISCOUNT IS USABLE
                    # =============================
                    $this->Credit_Discount_Therapy_Percent  = ($type_therapy == 'percent') ? $discount_therapy : $this->Credit_Discount_Therapy_Percent;
                    $this->Credit_Discount_Therapy_Dollar   = ($type_therapy == 'dollar') ? $discount_therapy : $this->Credit_Discount_Therapy_Dollar;
                    $this->Credit_Discount_Therapy_Notice   = ''; //"<div class='discount_notice'>You are receiving a discount because you <br />are still within the first 30 days of your account.</div>";
                }
            }
        }
    }
    
    public function GetProductCredits()
    {
        # DETERMINE TYPE OF SESSION
        # =============================
        switch($this->Sessions_Type) {
            default:
            case 'standard':
                $query_credit_type      = " AND `credit_type_standard`=1";
                $query_simple_signup    = ($this->Simple_Signup) ? " AND `part_number`='CREDIT-1'" : ''; // LIMIT TO 1-CREDIT PURCHASE IF SIMPLE SIGNUP
            break;
            case 'therapy':
                $query_credit_type      = " AND `credit_type_therapy`=1";
                $query_simple_signup    = ($this->Simple_Signup) ? " AND `part_number`='CREDITTHERAPY-1'" : ''; // LIMIT TO 1-CREDIT PURCHASE IF SIMPLE SIGNUP
            break;
        }
        
        # GET USER DISCOUNTS
        # =============================
        $this->GetUserCreditDiscount();
        
        
        # GET THE PRODUCT INFORMATION
        # =============================
        /*
        $records = $this->SQL->GetArrayAll(array(
            'table' => $this->Table_Products,
            'keys'  => '*',
            'where' => "`categories`='{$this->Credit_Categories}' AND active=1 {$query_credit_type} {$query_simple_signup}",
        ));
        if ($this->Show_Query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        */
        
        $where      = "`categories`='{$this->Credit_Categories}' AND active=1 {$query_credit_type} {$query_simple_signup}";
        $OBJ_STORE  = new Store_ShoppingProduct();
        $products   = $OBJ_STORE->GetAllProducts($where);
        if ($this->Show_Query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        
        $table = "
            <table>
                <tr>
                <td colspan='4'>{$this->Credit_Discount_Standard_Notice}</td>
                </tr>
        ";
        
        foreach ($products as $product) {
            
            # FORMAT VARIOUS ITEMS
            # =============================
            $product_id         = $product['store_products_id'];
            $part_number        = $product['part_number'];
            $parts              = explode('-', $part_number);
            $credit_qty         = $parts[1];
            
            if ($this->FORCE_TEST_ON) echo "<br />@@ Credit_Discount_Standard_Percent ---> {$this->Credit_Discount_Standard_Percent}";
            if ($this->FORCE_TEST_ON) echo "<br />@@ Credit_Discount_Standard_Dollar ---> {$this->Credit_Discount_Standard_Dollar}";
            
            # FORMAT SALE PRICE
            switch($this->Sessions_Type) {
                default:
                case 'standard':
                    $sale_percent       = $product['sale_percent'] + $this->Credit_Discount_Standard_Percent;
                    $sale_dollar        = $product['sale_dollar'] + ($this->Credit_Discount_Standard_Dollar * $credit_qty);
                break;
                case 'therapy':
                    $sale_percent       = $product['sale_percent'] + $this->Credit_Discount_Therapy_Percent;
                    $sale_dollar        = $product['sale_dollar'] + ($this->Credit_Discount_Therapy_Dollar * $credit_qty);
                break;
            }
            
            # FORMAT PRICE DISPLAY
            $price_display      = $OBJ_STORE->FormatPrice($product['price'], $sale_percent, $sale_dollar);
            $price_sale         = $OBJ_STORE->Product_Final_Price;
            
            # CREATE BUY CREDITS LINK
            $btn_buy_credits    = MakeButton('regular', 'Buy', $this->Page_Link_Query . ";step=buy_credits;product_id={$product_id};product_price={$price_sale}", '', "btn_purchase_credits_{$credit_qty}");
            
            if ($this->Simple_Signup) {
                
                if ($this->FORCE_TEST_ON) echo "<br />SIMPLE SIGNUP";
                if ($this->FORCE_TEST_ON) echo "<br />product_id ---> $product_id";
                if ($this->FORCE_TEST_ON) echo "<br />price_sale ---> $price_sale";
                
                $this->Simple_Signup_Credit_Product_Id    = $product_id;
                $this->Simple_Signup_Credit_Product_Price = $price_sale;
            }
            
            $table .="
                <tr>
                <td align='right'><div style='$this->credits_style_1'>$credit_qty</div></td>
                <td><div style='$this->credits_style_3'>for</div></td>
                <td><div style='$this->credits_style_2'>$price_display</div></td>
                <td>$btn_buy_credits</td>
                </tr>
            ";
        }
        $table .= '</table>';
        
        return $table;
    }
    
    public function GetUserCredits()
    {
        # GET CREDITS FOR THIS USER
        $records = $this->SQL->GetArrayAll(array(
            'table' => $this->Table_Credits,
            'keys'  => '*',
            'where' => "`wh_id`='{$this->WH_ID}' AND active=1 AND used=0",
        ));
        if ($this->Show_Query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        
        
        # TALLY TYPES OF CREDITS
        $total_credits                      = 0;
        $total_credits_free                 = 0;
        $total_credits_purchased_standard   = 0;
        $total_credits_purchased_therapy    = 0;
        $total_credits_testing_standard     = 0;
        $total_credits_testing_therapy      = 0;
        
        foreach ($records AS $record) {
            $total_credits++;
            if ($record['type'] == 'free') $total_credits_free++;
            if ($record['type_standard']) $total_credits_purchased_standard++;
            if ($record['type_therapy']) $total_credits_purchased_therapy++;
            
            //if ($record['type'] != 'free') $total_credits_purchased++;
        }
        
        if ($this->TESTING && $this->TESTING_Add_Standard_Credits_To_Customer) {
            $total_credits_testing_standard = $this->TESTING_Add_Standard_Credits_To_Customer;
            $total_credits = $total_credits + $total_credits_testing_standard;
        }
        
        if ($this->TESTING && $this->TESTING_Add_Therapy_Credits_To_Customer) {
            $total_credits_testing_therapy = $this->TESTING_Add_Therapy_Credits_To_Customer;
            $total_credits = $total_credits + $total_credits_testing_therapy;
        }
        
        
        # STORE TYPES OF CREDITS
        $this->user_record['credits']                       = $total_credits;
        $this->user_record['credits_free']                  = $total_credits_free;
        $this->user_record['credits_purchased_standard']    = $total_credits_purchased_standard;
        $this->user_record['credits_purchased_therapy']     = $total_credits_purchased_therapy;
        $this->user_record['credits_testing_standard']      = $total_credits_testing_standard;
        $this->user_record['credits_testing_therapy']       = $total_credits_testing_therapy;
    }


    public function CheckIfSessionLocked()
    {
        # check if the session is already locked out
        # and if so - is it by this user
        # SET an error  if locked by a diferent user
        
        $locked         = $this->session_record['locked'];
        $locked_wh_id   = $this->session_record['locked_wh_id'];
        $user_wh_id     = $this->WH_ID;
        
        #echo "<br />locked_wh_id ===> ".$locked_wh_id;
        #echo "<br />user_wh_id ===> ".$user_wh_id;
        
        $this->Session_Locked_Error = ($locked && $locked_wh_id!=$user_wh_id) ? true : false;
    }
    
    public function AddTimeToSessionExpireTime($SECONDS) 
    {
        # NOTE: To be effective - you must have already run the function GetSessionExpireTime() 
        # to set the true expiration time before this function is run.
        
        if ($SECONDS) {
            # 1. GET THE CURRENT EXPIRE TIME
            $time_release_server_orig               = $this->time_release_server;
            
            # 2. ADD THE NUMBER OF SECONDS BEING ADDED
            $timestamp                              = strtotime("$time_release_server_orig");
            $etime                                  = strtotime("+$SECONDS seconds", $timestamp);
            $time_release_server                    = date('Y-m-d H:i:s', $etime);
            
            # 3. RUN STANDARD CONVERSIONS OF TIME
            $this->GetSessionExpireTime($time_release_server);
        }
    }

    public function GetSessionExpireTime($SERVER_EXPIRE_TIME='')
    {
        global $USER_LOCAL_TIMEZONE;
        
        # GET THE SESSION RECORD - IF IT DOESN'T EXIST YET
        # =================================================================
        if (!$this->session_record) {
            $this->GetSessionRecord();
        }
        
        # FIGURE OUT THE EXPIRE TIME - BASED ON SERVER TIME
        # =================================================================
        if (!$SERVER_EXPIRE_TIME) {
            $time_locked            = $this->session_record['locked_start_datetime']; //'2010-10-28 22:14:00';
            $event_length           = $this->time_expire_duration;
            $timestamp              = strtotime("$time_locked");
            $etime                  = strtotime("+$event_length seconds", $timestamp);
            $time_release_server    = date('Y-m-d H:i:s', $etime);
        } else {
            $time_release_server    = $SERVER_EXPIRE_TIME;
        }
        
        # GET THE EXPIRE TIME - BASED ON USER TIME
        # =================================================================
        $input_date_time        = $time_release_server;
        $input_timezone         = $this->Server_Timezone;
        $output_timezone        = $USER_LOCAL_TIMEZONE;
        $output_format          = 'Y-m-d H:i:s';
        $time_release_user      = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
        
        
        # FORMAT THE DATE SO IT'S READABLE BY JAVASCRIPT LATER
        # =================================================================
        $this->time_release_user                = $time_release_user;
        $this->time_release_server              = $time_release_server;
        $this->time_release_javascript_user     = $this->convertDateTimeToJS($time_release_user);
        $this->time_release_javascript_server   = $this->convertDateTimeToJS($time_release_server);
        $this->heq_time_release_user            = HexEncodeString($time_release_user);
        $this->heq_time_release_server          = HexEncodeString($time_release_server);
        
        
        # CHECK IF EXPIRE TIME HAS PASSED
        # =================================================================
        $current_server_time        = date('Y-m-d H:i:s');
        $this->Session_Expired      = ($current_server_time > $time_release_server) ? true : false;
        
        #echo "AAAAAAAAA";
        #exit();
        
        if ($this->TESTING && $this->TESTING_Show_Times) {
            echo "<br /><br /><hr>";
            echo "<br />current_server_time =========> " . $current_server_time;
            echo "<br />time_release_server =========> " . $time_release_server;
            echo "<br />time_release_user =========> " . $time_release_user;                
            echo "<br />time_release_javascript_server --> " . $this->time_release_javascript_server;
            echo "<br />time_release_javascript_user --> " . $this->time_release_javascript_user;
            echo "<br />time_release_server --> " . $this->time_release_server;
            echo "<br />time_release_user --> " . $this->time_release_user;
            echo "<br />";
            echo "<br />Session_Expired --> " . $this->Session_Expired;
            echo "<hr><br /><br />";
        }
        
        #return $this->session_record['locked'];
    }




    public function ShowSimpleSignupFooter()
    {
        $time_remaining_display     = ($this->Show_Time_Area) ? $this->ShowTimeRemainingInformation() : '';
        //$next_step                  = ($this->session_record['type_standard'] && $this->session_record['type_therapy']) ? 'select_session_type' : 'payment';
        $btn_continue               = ''; //MakeButton('positive', 'CONTINUE', "{$this->script_location};step={$next_step};sid={$this->sessions_id};tr={$this->heq_time_release_server}");
        $btn_time_expired           = ($this->TESTING_Show_Time_Expire_Button) ? MakeButton('negative', 'FORCE TIME EXPIRED', "{$this->script_location};step=timeexpire;sid={$this->sessions_id}") : '';
        $btn_cancel                 = MakeButton('negative', 'CANCEL PURCHASE', "{$this->script_location};step=cancel;sid={$this->sessions_id}");
        
        $output = "
                <br /><br />
                {$time_remaining_display}

                <br /><br />


                <div>
                    <div style='float:right;'>
                        {$btn_continue}<br />
                        {$btn_time_expired}
                    </div>

                    <div style='float:left;'>
                        {$btn_cancel}
                    </div>
                    <div style='clear:both;'></div>
                </div>";
        return $output;
    }
    
    public function ShowSessionInformation()
    {
        # ========================================================================
        # FUNCTION :: Create the output for how the session will be displayed
        # ========================================================================
        
        global $USER_LOCAL_TIMEZONE, $USER_DISPLAY_DATE, $USER_DISPLAY_TIME, $USER_DISPLAY_DATE_CALC, $USER_LOCAL_TIMEZONE_DISPLAY;

        $input_date_time        = $this->session_record['utc_start_datetime'];
        $input_timezone         = 'UTC';
        $output_timezone        = $USER_LOCAL_TIMEZONE;
        $output_format          = "$USER_DISPLAY_DATE|$USER_DISPLAY_TIME";
        $user_start_datetime    = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
        $parts                  = explode('|', $user_start_datetime);
        $user_start_date        = $parts[0];
        $user_start_time        = $parts[1];

        $input_date_time        = $this->session_record['utc_end_datetime'];
        $input_timezone         = 'UTC';
        $output_timezone        = $USER_LOCAL_TIMEZONE;
        $output_format          = "$USER_DISPLAY_DATE_CALC|$USER_DISPLAY_TIME";
        $user_end_datetime      = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
        $parts                  = explode('|', $user_end_datetime);
        $user_end_date          = $parts[0];
        $user_end_time          = $parts[1];
        
        
        # GET SESSION EXPIRE TIME INFO
        # =================================================================
        #$this->GetSessionExpireTime();
        $time_remaining_display = $this->ShowTimeRemainingInformation();
        
        
        # FORMAT THE SESSION INFO
        # =================================================================
        $next_step          = ($this->session_record['type_standard'] && $this->session_record['type_therapy']) ? 'select_session_type' : 'payment';
        $btn_continue       = MakeButton('positive', 'CONTINUE', "{$this->script_location};step={$next_step};sid={$this->sessions_id};tr={$this->heq_time_release_server}");
        $btn_time_expired   = ($this->TESTING_Show_Time_Expire_Button) ? MakeButton('negative', 'FORCE TIME EXPIRED', "{$this->script_location};step=timeexpire;sid={$this->sessions_id}") : '';
        $btn_cancel         = MakeButton('negative', 'CANCEL PURCHASE', "{$this->script_location};step=cancel;sid={$this->sessions_id}");


        $data = array(
            "SESSION ID:|{$this->session_record['sessions_id']}",
            "DATE:|{$user_start_date}",
            "TIME:|{$user_start_time} - {$user_end_time}",
            "|($USER_LOCAL_TIMEZONE_DISPLAY)",
        );
        $session_box_content        = MakeTable($data, 'font-size:13px;');

        $instructor_name            = ucwords(strtolower("{$this->session_record['first_name']} {$this->session_record['last_name']}"));
        $instructor_picture         = ($this->session_record['primary_pictures_id']) ? "{$this->Picture_Dir}{$this->session_record['primary_pictures_id']}" : "{$this->Picture_Dir}{$this->Image_Dir_Instructor}{$this->Image_No_Pic}";
        $instructor_picture         = "<img src='{$instructor_picture}' border='0' height='50' alt='' />";
        $instructor_box_content     = "<div style='font-size:14px;'>{$instructor_picture}</div>";


        $session_box                = AddBox_Type1('SESSION INFORMATION', $session_box_content);
        $instructor_box             = AddBox_Type1("{$instructor_name}", $instructor_box_content); #INSTRUCTOR INFORMATION

        $this->Email_Content_Session_Box = $session_box;
        $this->Email_Content_Instructor_Box = $instructor_box;
        
        $instructor_box         = ($this->Show_Instructor_Profile) ? "<div class='col' style='width:47%;'>{$instructor_box}</div>" : '';
        
        
        if ($this->Simple_Signup) {
            $output = "
            <div style='border:1px solid #9e9d41; padding:10px;'>
                <div class='col' style='width:47%;'>
                    {$session_box}
                </div>
                <div class='col'>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                </div>
                {$instructor_box}
                <div class='clear'></div>
            </div>
            ";
        } else {
            $output = "
                <div class='col' style='width:47%;'>
                    {$session_box}
                </div>
                <div class='col'>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                </div>
                {$instructor_box}
                <div class='clear'></div>
            
                <br /><br />
                {$time_remaining_display}

                <br /><br />


                <div>
                    <div style='float:right;'>
                        {$btn_continue}<br />
                        {$btn_time_expired}
                    </div>

                    <div style='float:left;'>
                        {$btn_cancel}
                    </div>
                    <div style='clear:both;'></div>
                </div>

            ";
        }
        
        return $output;
    }


    
    
    public function SimpleSignupPaymentDisplay()
    {
        # FIGURE OUT THE CREDITS
        # =================================================================
        $this->GetUserCredits();

        $credits_user               = $this->user_record['credits'];
        $credits_user_standard      = $this->user_record['credits_purchased_standard'];
        $credits_user_therapy       = $this->user_record['credits_purchased_therapy'];
        
        $credits_course_cost        = $this->session_record['credits_cost'];
        $credits_course_cost_title  = ($credits_course_cost == 1) ? 'session' : 'sessions';
        
        switch($this->Sessions_Type) {
            default:
            case 'standard':
                $credits_remaining_standard         = ($credits_user_standard - $credits_course_cost);
                $credits_remaining_title_standard   = ($credits_remaining_standard == 1) ? 'session' : 'sessions';
                
                $credits_remaining_therapy          = ($credits_user_therapy);
                $credits_remaining_title_therapy    = ($credits_remaining_therapy == 1) ? 'session' : 'sessions';
                
                $session_type                       = 'yoga';
                $have_credits_of_session_type       = ($credits_user_standard > 0) ? true : false;
            break;
            case 'therapy':
                $credits_remaining_standard         = ($credits_user_standard);
                $credits_remaining_title_standard   = ($credits_remaining_standard == 1) ? 'session' : 'sessions';
                
                $credits_remaining_therapy          = ($credits_user_therapy - $credits_course_cost);
                $credits_remaining_title_therapy    = ($credits_remaining_therapy == 1) ? 'session' : 'sessions';
                
                $session_type                       = 'yoga therapy';
                $have_credits_of_session_type       = ($credits_user_therapy > 0) ? true : false;
            break;
        }
    
        
        
        //$have_credits_of_session_type = false;
        $output = '';
        if ($have_credits_of_session_type) {
            // user has existing credits
            
            $btn_continue = MakeButton('positive', 'CONTINUE', $this->MakeStepLink('payment_process'));
            
            $payment_content = "
                You have pre-purchased sessions available for use. After you click the [Continue] button we will deduct 
                <span style='font-size:14px; font-weight:bold; color:blue;'>{$credits_course_cost} {$session_type} {$credits_course_cost_title}</span> from your account. 
                After your session is booked you will have 
                <span style='font-size:12px; font-weight:bold;'>{$credits_remaining_standard} yoga {$credits_remaining_title_standard}</span> and 
                <span style='font-size:12px; font-weight:bold;'>{$credits_remaining_therapy} yoga therapy {$credits_remaining_title_therapy}</span> remaining in your account.
                <br /><br />
                {$btn_continue}
                ";
            
            $output   .= "<br /><br /><div style='border:1px solid #9e9d41; padding:10px;'>";
            $output   .= AddBox('PAYMENT INFORMATION', $payment_content);
            $output   .= "</div>";
            
            
        } else {
            // user needs to buy a credit - show payment form
            
            $this->GetProductCredits();
            
            $product_id     = $this->Simple_Signup_Credit_Product_Id;
            $price          = $this->Simple_Signup_Credit_Product_Price;
            
            if ($this->FORCE_TEST_ON) echo "<br />product_id ===> $product_id";
            if ($this->FORCE_TEST_ON) echo "<br />price ===> $price";
            
            #$skip_processing_card = true;
            
            #if (!$skip_processing_card) {
                $this->Simple_Signup_OBJ_STORE = new Store_YogaStoreCreditOrder();
                $this->Simple_Signup_OBJ_STORE->SetDiscountPriceOnProduct($product_id, $price);
                $this->Simple_Signup_OBJ_STORE->SetCart($product_id);
                $output .= $this->Simple_Signup_OBJ_STORE->ProcessOrderPage();
            #}
        }
        return $output;
    
    
    }
    
    
    
    
    
    
    
    public function ShowPaymentInformation()
    {
        # FIGURE OUT THE EXPIRE TIME
        # =================================================================
        $time_remaining                         = $this->ShowTimeRemainingInformation();
        
        
        # FIGURE OUT THE CREDITS
        # =================================================================
        $this->GetUserCredits();

        $credits_user               = $this->user_record['credits'];
        $credits_user_standard      = $this->user_record['credits_purchased_standard'];
        $credits_user_therapy       = $this->user_record['credits_purchased_therapy'];
        
        $credits_course_cost        = $this->session_record['credits_cost'];
        $credits_course_cost_title  = ($credits_course_cost == 1) ? 'session' : 'sessions';
        
        switch($this->Sessions_Type) {
            default:
            case 'standard':
                $credits_remaining_standard         = ($credits_user_standard - $credits_course_cost);
                $credits_remaining_title_standard   = ($credits_remaining_standard == 1) ? 'session' : 'sessions';
                
                $credits_remaining_therapy          = ($credits_user_therapy);
                $credits_remaining_title_therapy    = ($credits_remaining_therapy == 1) ? 'session' : 'sessions';
            break;
            case 'therapy':
                $credits_remaining_standard         = ($credits_user_standard);
                $credits_remaining_title_standard   = ($credits_remaining_standard == 1) ? 'session' : 'sessions';
                
                $credits_remaining_therapy          = ($credits_user_therapy - $credits_course_cost);
                $credits_remaining_title_therapy    = ($credits_remaining_therapy == 1) ? 'session' : 'sessions';
            break;
        }
        
        
        # FORMAT THE SESSION INFO
        # =================================================================
        
        $btn_time_expired       = ($this->TESTING_Show_Time_Expire_Button) ? MakeButton('negative', 'FORCE TIME EXPIRED', $this->MakeStepLink('timeexpire')) : '';
        $btn_cancel             = MakeButton('negative', 'CANCEL PURCHASE', $this->MakeStepLink('cancel'));

        
        $error                  = "<br /><div style='font-size:10px; color:#990000; font-weight:bold;'>PLEASE PURCHASE SESSIONS</div>";
        $btn_use_credits        = ($credits_user >= $credits_course_cost) ? MakeButton('regular', 'Use', '', '', 'btn_purchase_use_credits') : $error;
        $purchase_credits_box   = $this->GetProductCredits();

        
        
        ###$btn_buy_credits    = MakeButton('regular', 'Buy', $this->Page_Link_Query . ";step=buy_credits;product_id={$product_id}", '', "btn_purchase_credits_{$credit_qty}");
        
        
        if ($this->TESTING_Show_Credit_Breakdown) {
            $data = array(
                "|<br />",
                "Free:|{$this->user_record['credits_free']}",
                "Purchased:|{$this->user_record['credits_purchased']}",
                "Testing:|{$this->user_record['credits_testing']}",
            );
            $table = MakeTable($data, 'font-size:10px;');
        } else {
            $table = '';
        }

        
        $form_action = $this->MakeStepLink('payment_process');
        
        $existing_credits_standard_box_content = "
            <center>
            <div style='font-size:14px; font-weight:bold;'>
                {$credits_user_standard} {$btn_use_credits}
            </div>
            {$table}
            </center>
            ";

        $existing_credits_therapy_box_content = "
            <center>
            <div style='font-size:14px; font-weight:bold;'>
                {$credits_user_therapy} {$btn_use_credits}
            </div>
            </center>
            ";
            
        $data = array(
            "Standard Sessions:|{$credits_user_standard}",
            "Yoga Therapy Sessions:|{$credits_user_therapy}",
        );
        $summary_table = MakeTable($data, 'font-size:12px;');
        $existing_credits_summary_box_content = "
            <div style='font-size:12px; width:290px;'>
            Below is a summary of your pre-purchased sessions.
            <center>
            {$summary_table}
            </center>
            </div>
        ";
    
        switch($this->Sessions_Type) {
            default:
            case 'standard':
                $credit_type = 'standard';
                $existing_credits_box_content = $existing_credits_standard_box_content;
            break;
            case 'therapy':
                $credit_type = 'therapy';
                $existing_credits_box_content = $existing_credits_therapy_box_content;
            break;
        }
        
        $session_cost_box       = AddBox('SESSION COST', "<div style='font-size:14px; font-weight:bold;'>{$credits_course_cost} {$credit_type} {$credits_course_cost_title}</div>");
        $existing_credits_box   = AddBox('PRE-PURCHASED<br />SESSIONS', $existing_credits_box_content);
        $summary_credits_box    = AddBox('SUMMARY', $existing_credits_summary_box_content);
        $purchase_session_box   = AddBox('BUY SESSIONS', $purchase_credits_box);


        $output = "
            <div style='display:none;'>
                <form id='testconfirmJQ' name='testconfirmJQ' method='post' action='$form_action'>
                    <input id='submitJQ' name='submitJQ' type='submit' value='Use' />
                </form>
            </div>
            
            <table width='100%' border='0' cellpadding='0' cellspacing='15'>
                <tr>
                    <td valign='top' align='left'><div style='text-align:left;'>$session_cost_box</div></td>
                    <td valign='top' align='center'><div style='text-align:left; background-color:#EAE6CD;'>$existing_credits_box</div></td>
                    <td valign='top' align='right' rowspan='2'><div style='text-align:left;'>$purchase_session_box</div></td>
                </tr>
                <tr>
                    <td valign='bottom' align='right' colspan='2'><div style='text-align:left; border-top:1px solid #9E9D41;'>$summary_credits_box</div></td>
                </tr>
            </table>
            
            <br /><br />
            $time_remaining
            <br /><br />

            <div>
                <div style='float:right;'>
                    

                    $btn_time_expired
                </div>

                <div style='float:left;'>
                    $btn_cancel
                </div>
                <div style='clear:both;'></div>
            </div>

            <div id='dialog' title='Confirm Session Purchase'>
            <br />
            Please confirm that you would like to use your pre-purchased session to book this 
            <span style='font-size:14px; font-weight:bold; color:blue;'>{$credits_course_cost} {$credits_course_cost_title}</span>. After your session is booked you will have 
            <span style='font-size:12px; font-weight:bold;'>{$credits_remaining_standard} standard {$credits_remaining_title_standard}</span> and 
            <span style='font-size:12px; font-weight:bold;'>{$credits_remaining_therapy} yoga therapy {$credits_remaining_title_therapy}</span> remaining in your account.
            <br /><br />
            To continue, click Book Session.
            <br /><br />To cancel transaction, click Cancel.
            </div>
";


/*
$credits_course_cost
$credits_course_cost_title
$credits_remaining_standard
$credits_remaining_title_standard
$credits_remaining_therapy
$credits_remaining_title_therapy
*/

        return $output;
    }


    public function ShowTimeExpiredInformation()
    {
        # UNLOCK THE SESSION
        # =================================================================
        $this->UnlockSession();


        # FORMAT THE OUTPUT
        # =================================================================
        $btn_cancel         = MakeButton('negative', 'CANCEL PURCHASE', "{$this->script_location};step=cancel;sid={$this->sessions_id}");
        $btn_restart        = MakeButton('positive', 'RESTART PURCHASE', "{$this->script_location};step=start;sid={$this->sessions_id}");

        $content = "
            <br />
            We will allow you to re-start the purchase process in <span id='restart_process_countdown_monitor' style='font-weight:bold;'></span>.
            <br /><br />
            <div id='restart_process_link' style='display:none; text-align:center;'>
                {$btn_restart}
            </div>
            ";
        $session_box = AddBox_Type1('TIME HAS EXPIRED', $content);


        $output = "
            <div id='restart_process_countdown' style='width:240px; height:45px; display:none;'></div>

            {$session_box}
            <br /><br />
            <div>
                <div style='float:right;'>
                    {$btn_cancel}
                </div>

                <div style='float:left;'>

                </div>
                <div style='clear:both;'></div>
            </div>
            ";
            
        return $output;
    }


    public function ShowTimeRemainingInformation()
    {
        if ($this->TESTING && $this->TESTING_Bypass_Time_Check) {
            $output = "
                <div class='time_remaining_holder'>
                    NOT CHECKING TIME
                </div>
            ";
        } else {
            $output = "
                <div class='time_remaining_holder'>
                    <div class='time_remaining_time'>TIME REMAINING: <span id='time_remaining_monitor'></span></div>
                    <div class='time_remaining_text'>You must complete your transaction within this time or another person may book this session</div>
                </div>
                <div id='shortly' style='width:240px; height:45px; display:none;'></div>
            ";
        }
        return $output;
    }

    public function MakeStepLink($STEP)
    {
        //$template = ($this->Is_Overlay) ? ';template=overlay' : '';
        $template = '';
        return "{$this->script_location};step={$STEP};sid={$this->sessions_id};tr={$this->heq_time_release_server}{$template};stype={$this->Sessions_Type}";
    }
    
    public function AddScript()
    {
        AddScriptInclude('/jslib/countdown/jquery.countdown.pack.js');

        $script = <<<SCRIPT
            $('#dialog').dialog({
                autoOpen: false,
                width: 400,
                modal: true,
                resizable: false,
                buttons: {
                    "Book Session": function() {
                        document.testconfirmJQ.submit();
                    },
                    "Cancel": function() {
                        $(this).dialog("close");
                    }
                }
            });


            // BUY DIALOG POPUP
            // ====================================================
            $('form#testconfirmJQ').submit(function(){
                $('#dialog').dialog('open');
                return false;
            });

            $('#btn_purchase_continue').click(function(){
                $('#dialog').dialog('open');
                return false;
            });

            $('#btn_purchase_use_credits').click(function(){
                $('#dialog').dialog('open');
                return false;
            });

            $('input#TBcancel').click(function(){
                tb_remove();
            });

            $('input#TBsubmit').click(function(){
                document.testconfirmTB.submit();
            });


            // COUNTDOWN TIMER - PURCHASING
            // ====================================================
            var expireTime = new Date({$this->time_release_javascript_user});
            //alert('expireTime ===> '+ expireTime);
            
            $('#shortly').countdown({
                until: expireTime,
                onExpiry: liftOff,
                onTick: watchCountdown,
                format: 'MS',
                expiryUrl: '{$this->script_location};step=timeexpire;sid={$this->sessions_id}'
                });

            function liftOff() {
                alert('You have not completed session purchase in time!');
            }

            function watchCountdown(periods) {
                if (periods[6] < 10) {
                    var seconds = '0' + periods[6];
                } else {
                    var seconds = periods[6];
                }

                $('#time_remaining_monitor').text(periods[5] + ':' + seconds);

                //if ($.countdown.periodsToSeconds(periods) == 5) {
                //    $(this).addClass('highlight');
                //}
            }


            // COUNTDOWN TIMER - RESTART PURCHASE
            // ====================================================
            $('#restart_process_countdown').countdown({
                until: +{$this->time_restart_duration},
                format: 'MS',
                onTick: restartProcessCountdown,
                onExpiry: allowRestartProcess,
            });

            function allowRestartProcess() {
                $('#restart_process_link').show();
            }

            function restartProcessCountdown(periods) {
                if (periods[6] < 10) {
                    var seconds = '0' + periods[6];
                } else {
                    var seconds = periods[6];
                }

                $('#restart_process_countdown_monitor').text(periods[5] + ':' + seconds);
            }
SCRIPT;
        AddScriptOnReady($script);
        
        global $JS_CLOSE_WINDOW_SCRIPT;
        AddScript($JS_CLOSE_WINDOW_SCRIPT);
    }

    
    public function SendRegistrationSuccessEmail()
    {
    
        if ($this->email_send_to_user || $this->email_send_to_admin || $this->email_send_to_instructor)
        {
            # INITIALIZE THE EMAIL CLASS
            # ==================================
            global $ROOT;
            require_once "$ROOT/phplib/swift4/swift_required.php";
            $MAIL = new Email_MailWh;
        }
        
        
        # MAKE SESSION CONTENT
        # =====================================================
        $this->ShowSessionInformation();
        
        # MAKE SESSION INFORMATION FOR EMAIL
        $Email_Content_Session_Box       = $this->Email_Content_Session_Box;
        
        # MAKE INSTRUCTOR INFORMATION FOR EMAIL
        $Email_Content_Instructor_Box    = ''; //$this->Email_Content_Instructor_Box;
        
        # MAKE CUSTOMER INFORMATION FOR EMAIL
        $record = $this->SQL->GetRecord(array(
            'table' => $this->Table_Contacts,
            'keys'  => '*',
            'where' => "`wh_id`='{$this->WH_ID}' AND active=1",
        ));
        if ($this->Show_Query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        
        $customer_name  = "{$record['first_name']} {$record['last_name']}";
        $customer_email = $record['email_address'];
        
        $Email_Content_Customer_Box    = "customer info goes here<br /><b>customer_name</b>: $customer_name<br /><br /><b>customer_email</b>: $customer_email";
        
        
        
        
        global $URL_SITE_LOGIN;
        
        $swap_array              = array (
            '@@login_url@@'         => $URL_SITE_LOGIN,
            '@@session_info@@'      => $Email_Content_Session_Box,
            '@@instructor_info@@'   => $Email_Content_Instructor_Box,
            '@@customer_info@@'     => $Email_Content_Customer_Box,
        );
        
        
        
        # SEND MESSAGE TO USER
        # =============================================================
        if ($this->email_send_to_user) {
            
            # SETUP THE EMAIL ADDRESSES
            # ==================================
            global $EMAIL_ADMIN_EMAIL;
            $bcc = ($this->email_send_to_admin) ? $EMAIL_ADMIN_EMAIL : '';
            
            # PREP THE MESSAGE ARRAY
            # ==================================
            $msg_array = array(
                'email_template_id'     => $this->customer_email_template_id,
                'swap_array'            => $swap_array,
                #'subject'               => '',
                'to_name'               => $customer_name,
                'to_email'              => $customer_email,
                'cc'                    => '',
                'bcc'                   => $bcc,
                'wh_id'                 => $this->WH_ID,
            );
            
            $MAIL->PrepareMailToSend($msg_array);
            
            
            # SEND THE PREPARED MESSAGE
            # ==================================
            if ($MAIL->MailPrepared()) {
                echo "<h1>Message send to CUSTOMER.</h1>";
                $this->Result_Email_Customer_Sent = true;
            } else {
                echo "<h1>Unable to send message to CUSTOMER.</h1>";
                $this->Result_Email_Customer_Sent = false;
            }
            
            

            
            
        }
        
        
        
        
        # SEND MESSAGE TO INSTRUCTOR
        # =============================================================
        if ($this->email_send_to_instructor) {
            
            # SETUP THE EMAIL ADDRESSES
            # ==================================
            global $EMAIL_ADMIN_EMAIL;
            $bcc = ($this->email_send_to_admin) ? $EMAIL_ADMIN_EMAIL : '';
            
            # PREP THE MESSAGE ARRAY
            # ==================================
            $msg_array = array(
                'email_template_id'     => $this->instructor_email_template_id,
                'swap_array'            => $swap_array,
                #'subject'               => '',
                'to_name'               => $this->TEMP_INSTRUCTOR_NAME,
                'to_email'              => $this->TEMP_INSTRUCTOR_EMAIL,
                'cc'                    => '',
                'bcc'                   => $bcc,
                'wh_id'                 => $this->WH_ID,
            );
            
            $MAIL->PrepareMailToSend($msg_array);
            
            
            # SEND THE PREPARED MESSAGE
            # ==================================
            if ($MAIL->MailPrepared()) {
                echo "<h1>Message send to INSTRUCTOR.</h1>";
                $this->Result_Email_Instructor_Sent = true;
            } else {
                echo "<h1>Unable to send message to INSTRUCTOR.</h1>";
                $this->Result_Email_Instructor_Sent = false;
            }
        }
        
    }
    
    public function checkIntakeForm(){
        $record = $this->SQL->GetRecord(array(
            'table' => 'intake_form_standard',
            'keys'  => '*',
            'where' => "`wh_id`='{$this->WH_ID}'",
        ));
        
        if(!$record){
            return false;
        }
        return true;
    }

}  // -------------- END CLASS --------------