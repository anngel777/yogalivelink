<?php

// FILE: class.UserProfilePage.php


class UserProfilePage
{
    public $ShowTestingVars             = false;
    public $EmailsShowPerLoadMore       = 10;
    public $EmailsShowInitialLoad       = 10;

    public $PageVersionToShow           = '';

    public $Testing_Var                 = false;
    public $ShowRegisterNow             = false;
    public $ShowCalendar                = false;
    public $ShowProfile                 = false;
    public $ShowEmployees               = false;
    public $ShowEmail                   = false;
    public $ShowRegistrationHistory     = false;
    public $EVENTS_ID                   = 0;
    public $EVENT_SETUPS_ID             = 0;

    private $UserInfo                   = array();
    public  $WHID                       = 0;
    private $EMAIL                      = '';
    private $PRIMARY_CONTACT            = false;
    private $Pagename                   = '';
    private $Language;
    private $Translation;

    private $Reg_Record                 = '';

    public $MasterColor                 = '#00467A'; #990000


    public $Messages = array(
        'CANT_LOCATE_INFORMATION'   => '<p class="center">We are not able to locate your information.
                                        Please log back in and try again.</p>
                                        <p class="center"><a class="stdbuttoni" href="@@PAGEDIR@@@@EVENTPATH@@/event_login">CLICK HERE TO LOGIN</a></p>',
        'NO_EMAIL_ADDRESS'          => 'NO EMAIL ADDRESS FOUND FOR USER - UNABLE TO LOAD EMAIL MESSAGES',
        'UNREGISTERED'              => '[T~CE_RS_UNREGISTERED]',
        'SURE_YOU_WANT_TO_CANCEL'   => '[T~CE_L_0039]'
    );

    public function  __construct($PAGETYPE='')
    {
        global $PAGE, $EVENT_SETUPS_ID;
        $this->Pagename = $PAGE['pagename'];
        $this->PageVersionToShow = $PAGETYPE;

        $this->EVENT_SETUPS_ID = $EVENT_SETUPS_ID;

        addStyleSheet('/css/superfish.css,/css/css.UserProfilePage.css');

        addScriptInclude("
            /jslib/jquery.ezpz_tooltip.min.js,
            /jslib/jquery.hoverIntent.js,
            /jslib/jquery.superfish.js,
            /jslib/jquery.supersubs.js,
            /jslib/jquery.dimensions.js,
            /js/js.UserProfilePage.js
        ");
    }

    public function CancelEventRegistration()
    {
        $DATA  = GetEncryptQuery('eq');

        $event_registrations_id     = ArrayValue($DATA, 'event_registrations_id');
        $wh_id                      = ArrayValue($DATA, 'wh_id');
        $event_setups_id            = intonly($DATA['event_setups_id']);
        $events_id                  = intonly($DATA['events_id']);


        if (!$event_registrations_id or !$wh_id) {
            return;
        }

        $ER = new EventRegistrations;
        $ER->CancelRegistration($event_registrations_id);


        //=============================== SEND OUT THE CANCELATION EMAIL ==============================

        $contact_record   = db_GetRecord('contacts', 'first_name,last_name', "wh_id=$wh_id");
        $event_record     = db_GetRecord('event_setups', 'display_title,event_date_start,event_setups_id,events_id', "event_setups_id=$event_setups_id");

        $template_id = 409;
        $bcc         = '';

        $keyvalue = array(
            '[[FIRST_NAME]]'          => $contact_record['first_name'],
            '[[LAST_NAME]]'           => $contact_record['last_name'],
            '[[EVENT_DISPLAY_TITLE]]' => $event_record['display_title'],
            '[[EVENT_DATE_START]]'    => $event_record['event_date_start'],
            '[[EVENT_SETUPS_ID]]'     => $event_record['event_setups_id'],
            '[[EVENTS_ID]]'           => $event_record['events_id']
        );

        $MAIL = new MailWh;
        $MAIL->MailWhIdTemplate($wh_id, $template_id, $keyvalue, '', $bcc);

        //=============================== END SEND OUT THE CANCELATION EMAIL ==============================
        if (Get('cancel') == 1) {
            echo $this->TranslateContents($UPP->GetEmployee($wh_id));
        } else {
            echo $this->TranslateContents('<span style="color:red;">[T~CE_L_0017]</span>');
        }
    }

    private function TranslateContents($CONTENT) {
        $this->Translation = Lib_Singleton::GetInstance('Translations');
        if (empty($this->Translation->LANGUAGE)) {
            $this->Language = $this->Translation->SetLanguage();
        } else {
            $this->Language = $this->Translation->LANGUAGE;
        }
        $output  = $this->Translation->TranslateText($CONTENT, $this->Language);
        return $output;
    }

    public function displayProfilePage($WHID) {
        global $EVENT_SETUPS_ID;

        # INITIATE USER INFORMATION
        $CONTACT = new Contacts;
        $this->UserInfo = $CONTACT->GetAllContactDetails($WHID);
        $this->WHID = $WHID;
        $this->EMAIL = $this->UserInfo['email_address'];
        $this->PRIMARY_CONTACT = $this->UserInfo['primary_contact'];

// # FORCE THIS TO BE A RIMARY CONTACT
// $this->PRIMARY_CONTACT = 1;
// $this->UserInfo['primary_contact'] = 1;

        # SET THE SESSION VARIABLES - NEEDED FOR ADDING NEW EMPLOYEES
        $_SESSION['WH_CONTACT_RECORD'] = $this->UserInfo;
        $_SESSION['MASTER_WHID'] = $this->WHID;
        $_SESSION['ADMIT_EVENT'] = $EVENT_SETUPS_ID;
        $_SESSION['PRIMARY_CONTACT'] = ($this->PRIMARY_CONTACT) ? true : false;

        $already_registered = (1 == db_GetValue('event_registrations', 'active', "wh_id=$this->WHID AND event_setups_id=$EVENT_SETUPS_ID"));

        # DECIDE WHAT SHOULD OR SHOULD NOT SHOW ON THE PAGE
        switch ($this->PageVersionToShow) {
            case 'registration':
                $this->ShowRegisterNow = !$already_registered;  //---------- do not show if already registered ----------
                $this->ShowCalendar = false;
                $this->ShowProfile = true;
                $this->ShowEmail = true;
                $this->ShowRegistrationHistory = true;
                $this->ShowEmployees = $this->PRIMARY_CONTACT; //Show employees if person is a primary contact
                break;
            case 'account':
                $this->ShowRegisterNow = false;
                $this->ShowCalendar = true;
                $this->ShowProfile = true;
                $this->ShowEmail = true;
                $this->ShowRegistrationHistory = true;
                $this->ShowEmployees = false;
                break;
        }

        if ($this->ShowTestingVars) {
            $this->outputTestingVars();
        }

        # CALL THE OUTPUT
        $output = $this->outputForm();
        return $output;
    }


    public function DisplayHomeProfilePage($WHID)  // similar to above
    {
        # INITIATE USER INFORMATION
        $CONTACT = new Contacts;
        $this->UserInfo = Session('WH_CONTACT_RECORD');
        $this->WHID = Session('WHID');
        $this->EMAIL = $this->UserInfo['email_address'];
        $this->PRIMARY_CONTACT = $this->UserInfo['primary_contact'];

        # SET THE SESSION VARIABLES - NEEDED FOR ADDING NEW EMPLOYEES
        $_SESSION['MASTER_WHID'] = $this->WHID;
        $_SESSION['ADMIT_EVENT'] = 0;
        $_SESSION['PRIMARY_CONTACT'] = ($this->PRIMARY_CONTACT)? true : false;

        # DECIDE WHAT SHOULD OR SHOULD NOT SHOW ON THE PAGE
        $this->ShowRegisterNow = false;
        $this->ShowCalendar = false;
        $this->ShowProfile = true;
        $this->ShowEmail = true;
        $this->ShowRegistrationHistory = true;
        $this->ShowEmployees = false;

        if ($this->ShowTestingVars) {
            $this->outputTestingVars();
        }

        # CALL THE OUTPUT
        $output = $this->outputForm();
        return $output;
    }



    private function outputTestingVars() {

        $output = "
            <div style='border: 1px solid red; background-color:yellow; padding:20px;'>
            SESSION[MASTER_WHID] => ".Session('MASTER_WHID')." <br />
            WHID => $this->WHID <br />
            EMAIL => $this->EMAIL <br />
            PRIMARY CONTACT => $this->PRIMARY_CONTACT
            </div><br /><br />
        ";
        echo $output;
    }

    public function AjaxFunctions()
    {
        $DATA = GetEncryptQuery('eq');
        $output = '';
        $type = ArrayValue($DATA, 'type');

        switch ($type)
        {
            case 'EmailLoad':
                $email_address  = ArrayValue($DATA, 'email_address');
                $limit_start    = ArrayValue($DATA, 'limit_start');
                $limit_stop     = ArrayValue($DATA, 'limit_stop');
                $output = $this->LoadContactEmail($email_address, $limit_start, $limit_stop);
            break;
            case 'CancelEventRegistration':
                $this->CancelEventRegistration();
            break;
            default:
            $message_id = intOnly(ArrayValue($DATA, 'message_id'));
            $resend  = Get('resend');

            if ($message_id) {
                if ($resend) {

                    if(Post('data')) {
                        $var_pairs = explode('&', $_POST['data']);

                        // get post data
                        $post_data = array();
                        foreach ($var_pairs as $field) {
                            list($key, $value) = explode('=', $field);
                            $key               = urldecode($key);
                            $value             = urldecode($value);
                            $DATA[$key]        = $value;
                        }
                        $email = $DATA['RESEND_EMAIL_ADDRESS'];

                        // resend
                        $STORE = new EmailStore;
                        
                        if ($STORE->ResendMessage($message_id, $email)) {
                            $output = 'ok';
                        } else {
                            $output = $STORE->Error;
                        }

                    } else {
                       $output = "CANNOT GET POST DATA!";
                    }

                } else {
                    $output = $this->DisplayEmail($message_id);
                }

            }

            break;
        } //end switch

        $output = $this->TranslateContents($output);
        return $output;
    }

    public function outputForm() {

        # SHOW ERROR IF WE CAN'T DETERMINE WHO THIS PERSON IS
        # ====================================================================
        if (!$this->WHID || !$this->EMAIL) {
            echo $this->Messages['CANT_LOCATE_INFORMATION'];
            return;
        }

        # SET NEEDED SESSION VARIABLES
        # ====================================================================
        $eq_EMAIL = EncryptQuery("email_address=$this->EMAIL;type=EmailLoad");
        addScript("var eq_EMAIL = '$eq_EMAIL';");
        $_SESSION['WHID']  = $this->WHID;
        $_SESSION['PRIMARY_WHID'] = ($this->PRIMARY_CONTACT) ? $this->WHID : ''; //SET THE PRIMARY WHID - WHICH IS REQUIRED FOR REGISTERING ADDITIONAL PEOPLE

        # BUILD THE PAGE OUTPUT
        # ====================================================================
        $output = '';
        if ($this->ShowRegisterNow) $output .= $this->outputRegisterNow();
        if ($this->ShowProfile) $output .= $this->outputContact();
        if ($this->ShowEmployees) $output .= $this->outputEmployees();
        if ($this->ShowCalendar) $output .= $this->outputCalendar();

        return $output;
    }


    # OUTPUT THE USERS CONTACT INFORMATION
    private function outputContact() {
        $profile         = ($this->ShowProfile) ? $this->BoxContact() . '<br /><br />': '&nbsp;';
        $email           = ($this->ShowEmail) ? $this->BoxContactEmail() : '&nbsp;';
        $registration    = ($this->ShowRegistrationHistory) ? $this->BoxEventRegistrations() : '&nbsp;';
        $output = "<table width=\"100%\">
            <tr>
                <td valign=\"top\">$profile $email</td>
                <td valign=\"top\" id=\"user_registrations_list\">$registration</td>
            </tr>
        </table>";
        return $output;
    }

    # OUTPUT THE EVENT CALENDAR OPTION
    private function outputCalendar() {
        $output = '
        <table width="100%">
          <tr>
            <td valign="top" colspan="3"><br /><br />'.$this->BoxCalendarHeader().'</td>
          </tr>
        </table>
        <br />';
        return $output;
    }

    # OUTPUT ALL EMPLOYEES FORTHIS COMPANY
    private function outputEmployees($ajax=0) {
        if (!$ajax) {
            return $this->BoxEmployeesHeader();
        } else return '
        <!-- ====================== EMPLOYEES ====================== -->
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tbody>
          <tr>
            <td id="output_employees" valign="top" colspan="3" style="padding-top:1em;">
                ' . $this->BoxEmployeesHeader() . '
            </td>
          </tr>
        </tbody>
        </table>';
    }

    # OUTPUT REGISTER NOW BOX
    private function outputRegisterNow() {
        $output = '
        <table width="100%">
          <tr>
            <td valign="top" colspan="3"><br /><br />'.$this->BoxRegisterNowHeader().'</td>
          </tr>
        </table><br /><br /><br />';
        return $output;
    }





    # SHOW REGISTER NOW AREA
    # ============================================================================================
    public function BoxRegisterNowHeader() {
        global $EVENTS_ID, $EVENT_SETUPS_ID, $EVENT_PATH, $PAGE_DIR, $FORM_COUNTRY_CODES;

        $where = '';
        $where .= " AND event_setups.event_setups_id=$EVENT_SETUPS_ID";

        $db_table         = "event_setups";
        $keys             = "`event_setups`.`event_type`, `events`.`event_name`, `event_setups`.`title`,
                             `event_setups`.`display_title`, `event_setups`.`approval_status`,
                             `event_setups`.`display_status`, `event_setups`.`event_date_start`,
                             `event_setups`.`event_date_end`, `event_setups`.`onsite_languages`,
                             `event_setups`.`location_name`, `event_setups`.`location_address_1`,
                             `event_setups`.`location_address_2`, `event_setups`.`location_city`,
                             `event_setups`.`location_state`, `event_setups`.`location_country`,
                             `event_setups`.`location_postal_code`, `event_setups`.`event_setups_id`";
        $conditions       = "`event_setups`.`active`=1 $where";
        $order            = 'event_date_start DESC';
        $joins            = "LEFT JOIN `events` ON `events`.`events_id`=`event_setups`.`events_id`";
        $result           = db_GetRecord($db_table, $keys, $conditions, $joins);
        $row = $result;

        $data = '';
        #$data .= $this->DisplayItem($row['title'], ', ', '', '') . '<br />';
        $data .= $this->DisplayItem($row['location_name'], ', ', '', '') . '<br />';
        $data .= $this->DisplayItem($row['location_city'], ', ', '', '');
        $data .= $this->DisplayItem($row['location_state'], ', ', '', '');
        if (!empty($row['location_country'])) {
            $data .= $this->DisplayItem($FORM_COUNTRY_CODES[$row['location_country']], '');
        }
        $register_now_url = "https://channeleventsponsors.intel.com$PAGE_DIR$EVENT_PATH/event_registration_process{$this->Testing_Var}";

        $output = <<<OUTPUT
        <!-- ============ REGISTER NOW ============ -->
        <div class="box_wrapper_full">
        <div>
            <div class="box_header" style="background-color:$this->MasterColor;">
                <div class="box_header_small_left" style="color:#ffffff; font-size:14px;">[T~CE_S_0010]</div>
                <div class="box_header_small_right">
                    <div>&nbsp;</div>
                </div>
                <div class="box_clear"></div>
            </div>
            <div class="box_body" style="border:1px solid $this->MasterColor; background-color:#FDC806;">
                <div style="text-align:left; font-size:16px;">
                    <br />
                    <table align="center" cellpadding="2">
                    <tr>
                    <td valign="top">[T~CE_L_0037]<strong>[T~CE_S_0010]:</strong> </td>
                    <td valign="top">$data</td>
                    </tr>
                    </table>
                    <br />
                </div>
                <div style="text-align:center; font-size:16px;">
                    <button type="button" onclick="window.location='$register_now_url'">[T~CE_S_0011]</button><br />
                </div>
                <br />
            </div>
        </div>
        </div>
OUTPUT;
#[T~CE_L_0015] <-- LOCATION
        return $output;
    }





    # HEADER DIV FOR OTHER EMPLOYEES IN COMPANY
    # ==========================================================================================================
    private function BoxEmployeesHeader() {
        global $PAGE_DIR, $EVENT_PATH;

        $employees = $this->BoxEmployees();
        $add_employee_url = "https://channeleventsponsors.intel.com$PAGE_DIR$EVENT_PATH/add_contact";

        $output = <<<OUTPUT
        <!-- ============ EMPLOYEES ============ -->
        <div class='box_wrapper_full'>
        <div class='box_wrapper_JQUERY'>
            <div class='box_header' style='background-color:$this->MasterColor;'>
                <div class='box_header_small_left' style='color:#ffffff;'>[T~CE_L_0008]</div>
                <div class='box_header_small_right'>
                    <div class='box_toggle box_expanded'></div>
                </div>
                <div class='box_clear'></div>
            </div>
            <div class='box_body'>
                [T~CE_L_0009]
                <br /><br />
                <button type="button" onclick="window.location='$add_employee_url'">[T~CE_L_0038]</button><br />
                <br /><br />
                $employees
            </div>
        </div>
        </div>
OUTPUT;

        return $output;
    }



    # GET ALL OTHER EMPLOYEE IN COMPANY
    # ============================================================================================
    public function BoxEmployees()
    {
        $wh_cid           = $this->UserInfo['wh_cid'];
        $db_table         = 'contacts';
        $key              = 'wh_id';
        $conditions       = "wh_cid=$wh_cid AND active=1";
        $order            = 'last_name';
        //$result           = db_GetArrayAll($db_table, $keys, $conditions, $order);

        $employees = db_FieldArray($db_table, $key, $conditions, $order);

        $output = '';
        foreach ($employees as $wh_id) {
            $output .= $this->getEmployee($wh_id) . '<br />';
        }

        return $output;
    }

    # GET A SINGLE EMPLOYEE RECORD - FROM A PASSED IN WH_ID
    # ============================================================================================

    public function GetEmployee($WHID) {
        global $PAGE_DIR, $EVENT_PATH, $AJAX, $EVENTS_ID, $EVENT_SETUPS_ID;

        $CONTACT = new Contacts;
        $row = $CONTACT->GetAllContactDetails($WHID);

        $name    = '';
        $contact = '';

        $name.= $this->DisplayItem($row['salutation'], ' ');
        $name.= $this->DisplayItem($row['first_name'], ' ');
        $name.= $this->DisplayItem($row['middle_name'], ' ');
        $name.= $this->DisplayItem($row['last_name'], ' ');

        $contact .= $this->DisplayItem($row['email_address'], '<br />');
        $contact .= $this->DisplayItem($row['phone_number'], '<br />');

        $contact .= $this->DisplayItem($row['wh_id'], '<br />');
        // $contact .= $this->DisplayItem($row['active'], '<br />');

        $reg_status = $this->getEmployeeRegistration($WHID, $this->EVENT_SETUPS_ID, true);
        $reg_record = $this->Reg_Record;

        $REGISTER_LINK_RESET = '';
        $WHID = '';
        $link = $PAGE_DIR . $EVENT_PATH . '/event_registration_process;iframe=1;';
        $eq = EncryptQuery("wh_id={$row['wh_id']};register=1;events_id=$EVENTS_ID;event_setups_id=$EVENT_SETUPS_ID");

        $eq_cancel = EncryptQuery("type=CancelEventRegistration;wh_id={$row['wh_id']};events_id=$EVENTS_ID;event_setups_id=$this->EVENT_SETUPS_ID;event_registrations_id={$reg_record['event_registrations_id']}");

        $cancel_link = '@@PAGEDIR@@/AJAX@@EVENTPATH@@/@@PAGENAME@@';

        if (strpos($reg_status, $this->Messages['UNREGISTERED'])) {
            $reg_link = "<br />[<a class=\"register_link\" href=\"#\" onclick=\"overlayWindow('$eq', '&nbsp;', '$link'); return false;\">[T~CE_MNU_REGISTER]</a>]";
            $class = '';
        } else {
            $id = 'box_contact_' . $row['wh_id'];
            $reg_link = <<<LBL_LINK
                <br />[<a class="cancel_link" href="#"
                onclick="if (confirm('[T~CE_L_0039]')) {
                    $('#$id').load('$cancel_link?cancel=1;eq=$eq_cancel');
                }
                return false;">[T~CE_EML_CONF_0012]</a>]
LBL_LINK;
            $class = ' box_have_registration';
        }


        if ($AJAX) {
            $START = '';
            $END   = '';

        } else {
            $START = '<div id="box_contact_' . $row['wh_id'] . '" class="box_wrapper_full">';
            $END   = '</div>';
        }



        $output = <<<OUTPUT
                $START
                    <div class="box_header">
                        <div class="box_header_left">$name</div>
                        <div class="box_header_right">

                        </div>
                        <div class="box_clear"></div>
                    </div>
                    <div class="box_body$class">
                        <table width="600"><tr><td width="300">$contact</td><td width="300">$reg_status$reg_link</td></tr></table>
                    </div>
                $END
OUTPUT;

        return $output;
    }

    # DISPLAY THE CONTENT OF A PREVIOUSLY SENT EMAIL - (IN iFRAME)
    # ==========================================================================================================
    public function DisplayEmail ($ID) {
        $OUTPUT = '';
        if ($ID) {
            $db_table         = "email_store";
            $keys             = "subject, email_html, email, created";
            $conditions       = "id='$ID'";
            $order            = '';
            $joins            = "";
            $result           = db_GetArrayAll($db_table, $keys, $conditions, $order, $joins);

            $eq = EncryptQuery("message_id=$ID");

            if ($result) {
                $row          = $result[0];
                $subject      = $row['subject'];
                $html         = $row['email_html'];
                $email        = $row['email'];
                $created      = $row['created'];

                $OUTPUT = <<<EMAILOUTPUT
                <div style="width:800px;">
                <div style="border:1px solid #dddddd; background-color:$this->MasterColor; color:#fff; padding:5px;">
                <a class="CLOSE_BUTTON" href="#" onclick="closeContactEdit(); return false;">X</a>
                [T~CE_L_0001]</div>
                <div style="border:1px solid #dddddd; background-color:#f7f7f7; padding:10px;">

                    <div style="color:$this->MasterColor; float:left; text-align:right; width:200px; font-weight:bold;">[T~CE_L_0002]: </div>
                    <div style="color:#000000; float:left; padding-left:5px;">$created</div>
                    <div style="clear:both;"></div><br />

                    <div style="color:$this->MasterColor; float:left; text-align:right; width:200px; font-weight:bold;">[T~CE_L_0003]:</div>
                    <div style="color:#000000; float:left; padding-left:5px;">$email</div>
                    <div style="clear:both;"></div>

                    <div style="color:$this->MasterColor; float:left; text-align:right; width:200px; font-weight:bold;">[T~CE_L_0004]: </div>
                    <div style="color:#000000; float:left; padding-left:5px;">$subject</div>
                    <div style="clear:both;"></div>

                    <br />

                    <div style="border:1px solid #dddddd; background-color:#fff; padding:5px;">
                        <div style="height:300px; overflow:scroll; border:1px solid #dddddd;">$html</div>
                    </div>

                <div id="RESEND_EMAIL_DIV" style="border:1px solid #dddddd; background-color:#d3d3d3; color:$this->MasterColor;
                     padding:5px; text-align:center; white-space:nowrap;">

                    <form action="{$this->Pagename}" method="post">
                     [T~CE_L_0005]:&nbsp;<input id="RESEND_EMAIL_ADDRESS" name="RESEND_EMAIL_ADDRESS" type="text" value="$email" size="40"
                     maxlength="100" />&nbsp;<input
                     type="button" value="[T~CE_L_0006]" name="RESEND_EMAIL" onclick="resendEmail('$eq');" />
                    </form>
                    <div style="clear:both;"></div>
                </div>
                </div>
                </div>
EMAILOUTPUT;
            }
        }
    return $OUTPUT;
    }

    # OUTPUT THE USERS CONTACT/PROFILE INFORMATION
    # ==========================================================================================================
    public function BoxContact () {

        $eq =  $eq = EncryptQuery("wh_id=$this->WHID");
        $row = $this->UserInfo;

        $name = '';
        $contact = '';
        $company = '';

        $name .= $this->DisplayItem($row['salutation'], ' ');
        $name .= $this->DisplayItem($row['first_name'], ' ');
        $name .= $this->DisplayItem($row['middle_name'], ' ');
        $name .= $this->DisplayItem($row['last_name'], ' ');

        $contact .= $this->DisplayItem($row['email_address'], '<br />');
        $contact .= $this->DisplayItem($row['phone_number'], '<br />');

        $company .= $this->DisplayItem($row['company_name'], '<br />');
        $company .= $this->DisplayItem($row['address_1'], '<br />');
        $company .= $this->DisplayItem($row['address_2'], '<br />');
        $company .= $this->DisplayItem($row['address_3'], '<br />');
        $company .= $this->DisplayItem($row['city'], ',');
        $company .= $this->DisplayItem($row['state'], ' ');
        $company .= $this->DisplayItem($row['postal_code'], '');
        $company .= $this->DisplayItem($row['country_code'], '', '', '<br />');

        $output = '
        <div id="EDIT_CONTACT_PROFILE"></div>
        <div class="box_wrapper_400">
        <div class="box_wrapper_JQUERY">
            <div class="box_header">
                <div class="box_header_left"> [T~CE_L_0007] </div>
                <div class="box_header_right"><a href="#" onclick="editMyProfile(\'' .$eq. '\', false); return false;">[T~CE_L_0028]</a> </div>
                <div class="box_clear"></div>
            </div>
            <div class="box_body">
                '.$name.'
                <br />
                '.$contact.'
                <br />
                '.$company.'
            </div>
        </div>
        </div>';

        return $output;
    }





    # UNUSED FUNCTION
    # ============================================================
    public function BoxEmployeeActions() {
        $output = '';

        $output .= "<div class='box_wrapper_300'>";
        $output .= "<div class='box_header'>";
        $output .= "<div class='box_header_small_left'>[T~CE_L_0010]</div>";
        $output .= "<div class='box_header_small_right'>  </div>";
        $output .= "<div class='box_clear'></div>";
        $output .= "</div>";
        $output .= "<div class='box_body'>";

        $output .= "TEXT DESCRIPTION GOES HERE";
        $output .= "<br />";
        $output .= "{BUTTON}";

        $output .= "</div>";
        $output .= "</div>";

        return $output;
    }


    # SHOW LIST OF EMAILS SENT TO THIS PERSON
    # ==========================================================================================================
    public function LoadContactEmail($EMAIL_ADDRESS='', $LIMIT_START, $LIMIT_STOP)
    {
        $EMAIL = ($EMAIL_ADDRESS) ? $EMAIL_ADDRESS : $this->EMAIL;

        if ($EMAIL) {

            $LIMIT_START = ($LIMIT_START) ? $LIMIT_START : 0;
            $LIMIT_STOP = ($LIMIT_STOP) ? $LIMIT_STOP: $this->EmailsShowInitialLoad;

            $db_table         = "email_store";
            $keys             = "subject, created, id";
            $conditions       = "email='$EMAIL'";
            $order            = "created DESC LIMIT $LIMIT_START, $LIMIT_STOP";
            $joins            = "";
            $result           = db_GetArrayAll($db_table, $keys, $conditions, $order, $joins);
            $count            = db_Count($db_table, $conditions);

            $output = '';
            if ($result) {
                foreach ($result as $row) {
                    $id = $row['id'];
                    $subject = $row['subject'];
                    $created = $row['created'];
                    $eq = EncryptQuery("message_id=$id");

                    $output .= '
                            <a class="email_list_item" href="#" onclick="emailView(\''.$eq.'\');
                            return false;"><span>'.$subject.'</span><br />'.$created.'</a>';
                }

                $LIMIT_START = $LIMIT_START + $this->EmailsShowPerLoadMore;
                $LIMIT_STOP = $LIMIT_STOP + $this->EmailsShowPerLoadMore;

                $eq = EncryptQuery("email_address=$EMAIL;type=EmailLoad;limit_start=$LIMIT_START;limit_stop=$LIMIT_STOP");

                if ($count > $LIMIT_STOP) {
                    $id = md5(uniqid(rand(), true));
                    $output .= <<<LINK
                    <a id="id_$id" class="email_list_item" href="#"
                        onclick="LoadMoreEmails('$eq');
                            $('#id_$id').fadeOut('slow',
                                function(){
                                    $('#id_$id').remove();
                            }); return false;">

                    >> [T~CE_L_0011]
                    </a>
LINK;
                }
            } else {
                $output .= "<div style='color:$this->MasterColor;'>END OF EMAIL MESSAGES</div>";
            }

        } else {
            $output = $this->Messages['NO_EMAIL_ADDRESS'];
        }

        return $output;
    }

    public function BoxContactEmail() {

        $output = <<<LBL_EMAIL_LIST
        <div class="box_wrapper_400" id="EMAIL_LIST">
        <div class="box_wrapper_JQUERY">
            <div class="box_header">
                <div class="box_header_left">[T~CE_L_0012]
                <span style="font-size:80%; font-weight:normal; color:#bbbbbb;">([T~CE_L_0013])</span></div>
                <div class="box_header_right">
                    <div class="box_toggle box_collapsed"></div>
                </div>
                <div class="box_clear"></div>
            </div>
            <div class="box_body">
                <div id="box_recent_email_content">
                    <img src="/office/images/upload.gif" alt="" />
                </div>

            </div>
        </div>
        </div>
LBL_EMAIL_LIST;

        return $output;
    }


    # OUTPUT ALL EVENTS USER IS CURRENTLY REGISTERED FOR


    # ==========================================================================================================
    public function BoxEventRegistrations() {
        global $EVENT_SETUPS_ID, $PAGE_DIR;

        if ($this->WHID) {
            $db_table       = "event_registrations";
            $keys           = "`events`.`event_name`, `events`.`event_type`, `event_setups`.`title`, `event_setups`.`event_date_start`,
                           `event_setups`.`event_date_end`, `event_setups`.`location_city`, `event_setups`.`location_country`,
                           `event_setups`.`location_state`, `contacts`.`first_name`, `contacts`.`last_name`,
                           `event_registration_statuses`.`registration_status`,
                           event_registrations.event_registrations_id, event_registrations.active,
                           event_setups.event_setups_id, events.events_id";
            $conditions     = "event_registrations.wh_id=$this->WHID";
            $order          = 'event_registrations.active DESC, event_registrations.event_registration_statuses_id, event_registrations.created DESC';
            $joins          = "
                LEFT JOIN `event_registrations_details`
                ON `event_registrations_details`.`event_registrations_id` = `event_registrations`.`event_registrations_id`
                LEFT JOIN `contacts`
                ON `contacts`.`wh_id` = `event_registrations`.`wh_id`
                LEFT JOIN `countries`
                ON `countries`.`code` = `contacts`.`country_code`
                LEFT JOIN `companies`
                ON `companies`.`wh_cid`=`contacts`.`wh_cid`
                LEFT JOIN `events`
                ON `events`.`events_id`= `event_registrations`.`events_id`
                LEFT JOIN `event_setups`
                ON `event_setups`.`event_setups_id`=`event_registrations`.`event_setups_id`
                LEFT JOIN `event_registration_statuses`
                ON `event_registration_statuses`.`event_registration_statuses_id`=`event_registrations`.`event_registration_statuses_id`";

            $result = db_GetArrayAll($db_table, $keys, $conditions, $order, $joins);

            $id = 0;
            $registrations = '';
            foreach ($result as $row) {
                $id++;
                $event_info = '';
                $event_info .= $this->DisplayItem($row['event_type'], '<br />', '', '<strong>[T~CE_L_0014]: </strong>');
                $event_info .= $this->DisplayItem($row['location_city'], ', ', '', '<strong>[T~CE_L_0015]: </strong>');
                $event_info .= $this->DisplayItem($row['location_state'], '<br />');
                $event_info .= $this->DisplayItem($row['location_country'], '<br />');
                $event_info .= $this->DisplayItem($row['event_date_start'], '', '', '<strong>[T~CE_L_0016]: </strong>');
                $event_info .= ($row['event_date_end'] != $row['event_date_start']) ? $this->DisplayItem($row['event_date_end'], '<br />', '', ' - ') : '<br />';

                $cancel_event_info = $event_info;

                $rs = strtoupper($row['registration_status']);
                $active = $row['active'];
                if (!$active or ($rs == 'CANCELED')) {

                    $out = '<span style="color:red;">[T~CE_L_0017]</span>';
                    $box_header_class = ' box_cancelled_registration';
                    $action_menu = '';
                    $collapse = 'box_collapsed';

                } else {
                        $reg_status = "[T~CE_RS_{$rs}]";

                        $out = '<span id="registration_status_' . $id . '"><span style="color:blue;">' . $reg_status . '</span></span>';
                        $box_header_class = ($EVENT_SETUPS_ID == $row['event_setups_id']) ? ' box_have_registration' : '';

                        $path = (strpos($PAGE_DIR, 'channelevents') !== false)? $PAGE_DIR : '/channelevents';
                        $link_update_url = "$path/event_registration_process;iframe=1";
                        $link_update_eq  = EncryptQuery("update=1;events_id={$row['events_id']};event_setups_id={$row['event_setups_id']};event_registrations_id={$row['event_registrations_id']}");

                        $eq_cancel = EncryptQuery("type=CancelEventRegistration;events_id={$row['events_id']};wh_id=$this->WHID;event_setups_id={$row['event_setups_id']};event_registrations_id={$row['event_registrations_id']}");

                        $cancel_link = '@@PAGEDIR@@/AJAX@@EVENTPATH@@/@@PAGENAME@@';
                        $cancel_onclick = "
                            onclick=\"if (confirm('{$this->Messages['SURE_YOU_WANT_TO_CANCEL']}')) {
                                \$('#registration_status_{$id}').load('$cancel_link?cancel=2;eq=$eq_cancel');
                                \$('#registration_status_{$id}_list').remove();
                            }
                            return false;\"";

                        $action_menu = <<<LBLMENU
                        <ul class="sf-menu" id="registration_status_{$id}_list">
                               <li class="current"> <span style="color:$this->MasterColor">[[T~CE_L_0019]]</span>
                                   <ul>
                                       <li class="sf-menu-first-item"> <a href="#" onclick="overlayWindow('{$link_update_eq}', 'UPDATE REGISTRATION', '{$link_update_url}'); return false;">[T~CE_L_0021]</a> </li>
                                       <li class="sf-menu-last-item"> <a href="#" $cancel_onclick>[T~CE_L_0022]</a> </li>
                                   </ul>
                               </li>
                        </ul>
LBLMENU;
                        $collapse = 'box_expanded';  // this changes the class name to prevent collapsing
                }
                $event_info .= $this->DisplayItem($out, '', '', '<strong>[T~CE_L_0018]: </strong>');


                $registrations .= <<<EVENTREGISTRATION

                <!-- ============ registration ============ -->
                <div class="box_wrapper_300">
                <div class="box_wrapper_JQUERY">
                    <div class="box_header$box_header_class">
                        <div class="box_header_small_left">{$row["title"]}</div>
                        <div class="box_header_small_right">
                            <div class="box_toggle $collapse"></div>
                        </div>
                        <div class="box_clear"></div>
                    </div>
                    <div class="box_body">
                        <div class="box_action_menu" style="display:block;">
                           $action_menu

                       <div class="box_clear"></div>
                       </div>

                       <div style="z-index:0;">
                           $event_info
                       </div>
                       <br style="clear:both" />
                    </div>
                </div>
                </div>
                <br />
EVENTREGISTRATION;
            }

            $output = <<<HEADER
            <div class="box_header" style="background-color:$this->MasterColor;">
                <div class="box_header_small_left" style="color:#ffffff;">[T~CE_L_0023]</div>
                <div class="box_header_small_right"></div>
                <div class="box_clear"></div>
            </div>
            <br />$registrations
HEADER;
            return $output;
        }
    }

    # OUTPUT A GIVEN LINE
    public function DisplayItem($ITEM, $END_FOUND='', $END_NOT_FOUND='', $START_FOUND='') {
        $output = ($ITEM) ? $START_FOUND . $ITEM . $END_FOUND : $END_NOT_FOUND;
        return $output;
    }


    //-------- LOGIN_PAGE_FUNCTIONS-2 ----------
    // THESE NEED WORK @@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    // @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    // @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

    # SHOW CALENDAR AREA
    # ============================================================================================
    public function BoxCalendarHeader() {
        $link_calendar_url = "/home/event_calendar;iframe=1;";
        $link_calendar_eq = EncryptQuery("CssTheme=/css/css.Lib_Calendar-WH.css");

        $output = <<<OUTPUT
        <div class="box_wrapper_full">
        <div>
            <div class="box_header" style="background-color:$this->MasterColor;">
                <div class="box_header_small_left" style="color:#ffffff;">[T~CE_L_0024]</div>
                <div class="box_header_small_right">
                    <div></div>
                </div>
                <div class="box_clear"></div>
            </div>
            <div class="box_body" style="border:1px solid $this->MasterColor;">


                       <a href="#" onclick="overlayWindow('eq={$link_calendar_eq}', '[T~CE_L_0027]', '{$link_calendar_url}'); return false;">
                       <img src="/images/UserProfilePage/view_calendar.jpg" alt="" width="128" height="128" border="0" /><br />[[T~CE_L_0025]]
                       </a>
                       <div class="box_clear"></div>



            </div>
        </div>
        </div>
OUTPUT;

        return $output;
    }


    # SHOW EVENTS USER REGISTERED FOR
    # ============================================================================================
    public function BoxCalendar()
    {
        $output = '';
        return $output;
    }



    # SHOW IF SUB-EMPLOYEE IS REGISTERED FOR EVENT
    # ============================================================================================
    public function GetEmployeeRegistration($WHID, $EVENT_SETUPS_ID, $SUBEMPLOYEE='')
    {

            $db_table        = "event_registrations";
            $keys           = "`events`.`event_name`, `events`.`event_type`, `event_setups`.`title`, `event_setups`.`event_date_start`, `event_setups`.`event_date_end`, `event_setups`.`location_city`, `event_setups`.`location_country`, `event_setups`.`location_state`, `contacts`.`first_name`, `contacts`.`last_name`, `event_registration_statuses`.`registration_status`, event_registrations.event_registrations_id, event_registrations.active";
            $conditions     = "event_registrations.wh_id=$WHID AND event_setups.event_setups_id=$EVENT_SETUPS_ID AND event_registrations.active=1";
            $joins          = "
                LEFT JOIN `event_registrations_details`
                ON `event_registrations_details`.`event_registrations_id` = `event_registrations`.`event_registrations_id`
                LEFT JOIN `contacts`
                ON `contacts`.`wh_id` = `event_registrations`.`wh_id`
                LEFT JOIN `countries`
                ON `countries`.`code` = `contacts`.`country_code`
                LEFT JOIN `companies`
                ON `companies`.`wh_cid`=`contacts`.`wh_cid`
                LEFT JOIN `events`
                ON `events`.`events_id`= `event_registrations`.`events_id`
                LEFT JOIN `event_setups`
                ON `event_setups`.`event_setups_id`=`event_registrations`.`event_setups_id`
                LEFT JOIN `event_registration_statuses`
                ON `event_registration_statuses`.`event_registration_statuses_id`=`event_registrations`.`event_registration_statuses_id`";
            $row  = db_GetRecord($db_table, $keys, $conditions, $joins);

            $this->Reg_Record = $row;

            $output = '';
            $output .= $this->DisplayItem($row['location_city'], ', ', '', '<strong>[T~CE_L_0015]: </strong>');
            $output .= $this->DisplayItem($row['location_state'], '<br />');

            switch ($row['active'])
            {
                case 0:
                    if($SUBEMPLOYEE) {
                        $out = "[T~CE_RS_UNREGISTERED]"; //$this->Messages['UNREGISTERED'];
                    } else {
                        $out = '<span style="color:red;">[T~CE_L_0017]</span>';
                    }
                    break;
                case 1:
                    $rs = strtoupper($row['registration_status']);
                    $reg_status = "[T~CE_RS_{$rs}]";
                    $out = '<span style="color:blue;">' . $reg_status . '</span>';
                    break;
            }

            $output .= $this->DisplayItem($out, '', '', '<strong>[T~CE_L_0018]: </strong>');

            return $output;
    }


    public function ReturnToPage() {
        global $PAGE_DIR, $EVENT_PATH;
        $iframe = Get('iframe');

        $link = ($iframe)? 'href="#" onclick="top.parent.closeContactEdit(); return false;"' :
                "href=\"$PAGE_DIR$EVENT_PATH/event_user_profile\"";


        $output = <<<OUTPUT
        <br /><br />
        <div style="border-bottom:1px solid $this->MasterColor;"></div>
        <br /><br />
        [T~CE_S_0056]
        <br /><br />
        <div style="text-align:center;">
            <span style="font-size:14px; font-weight:bold; border:1px solid #0F1E3D; padding:3px;">
            <span style="background-color:#D63E3E;">
            <a $link style="text-decoration:none; color:#fff;">&nbsp;&nbsp;&nbsp;[T~CE_S_0057]&nbsp;&nbsp;&nbsp;</a>
            </span>
            </span>
        </div>
        <br /><br />

OUTPUT;

        return $output;
    }

}