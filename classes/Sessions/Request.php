<?php
class Sessions_Request extends BaseClass
{
    protected $Instructor_WH_ID;

    function __construct($Instructor_WH_ID){
        $this->SetSQL();
        $this->Instructor_WH_ID = $Instructor_WH_ID;
    }
    function execute(){
        if(isset($_POST['FORM_action']) && $_POST['FORM_action'] == "submit"){
            $this->submit();
        } else {
            $this->printForm();
        }
    }

    function printForm($errors = null){
        $postname = isset($_POST['FORM_name'])? $_POST['FORM_name'] : "";
        $postemail = isset($_POST['FORM_email'])? $_POST['FORM_email'] : "";
        $posttimezone = isset($_POST['FORM_time_zones_id'])? $_POST['FORM_time_zones_id'] : "";
        $postdate1 = isset($_POST['date1'])? $_POST['date1'] : "";
        $posthour1 = isset($_POST['hour1'])? $_POST['hour1'] : "";
        $postmin1 = isset($_POST['min1'])? $_POST['min1'] : "";
        $postdate2 = isset($_POST['date2'])? $_POST['date2'] : "";
        $posthour2 = isset($_POST['hour2'])? $_POST['hour2'] : "";
        $postmin2 = isset($_POST['min2'])? $_POST['min2'] : "";

        $timezone_list      = $this->SQL->GetAssocArray($GLOBALS['TABLE_timezones'], 'time_zones_id', 'tz_display', '`active`=1');
        $timezone_types     = Form_AssocArrayToList($timezone_list);
        $timezone_form_options = "";
        $t = explode("|",$timezone_types);
        $records = $this->SQL->GetArrayAll(array(
                'table' => $GLOBALS['TABLE_instructor_profile'],
                'keys'  => '*',
                'where' => "`display`=1 AND `active`=1",
                'order' => '`sort_order` ASC',
            ));

        $instructors = "";
        foreach($records as $record){
            $instructors .= (isset($_POST['FORM_instructor_id']) && $_POST['FORM_instructor_id'] == $record['wh_id'])? "<option selected value=\"{$record['wh_id']}\">" : "<option value=\"{$record['wh_id']}\">";
            $instructors .= "{$record['first_name']} {$record['last_name']}</option>";
        }
        foreach($t as $tz ){
            $temp = explode("=",$tz);
            $timezone_form_options .= ($posttimezone == $temp[1])? "<option selected>" : "<option>";
            $timezone_form_options .= "{$temp[1]}</option>";
        }
        if ($this->Instructor_WH_ID){
            $record = $this->SQL->GetArrayAll(array(
                    'table' => $GLOBALS['TABLE_instructor_profile'],
                    'keys'  => '*',
                    'where' => "`display`=1 AND `active`=1 AND WH_ID = {$this->Instructor_WH_ID} LIMIT 1",
                ));
            $instructor_form_option =  "<h4>You are requesting a session with: <br> {$record[0]['first_name']} {$record[0]['last_name']}</h4>";
            $instructor_form_option .= "<input type=\"hidden\" value=\"{$this->Instructor_WH_ID}\" name=\"FORM_instructor_id\" id=\"FORM_instructor_id\">";
            $thankyou_text = "Thank you! We will check with the instructor about your request and we'll get back with you.";
            ;
        } else {
            $instructor_form_option = <<<EOF
                <h4>Select An Instructor</h4>
                <select name="FORM_instructor_id">
                    <option value="0">Any Instructor</option>
                    {$instructors}
                </select>
EOF;
            $thankyou_text = "Thank you! We will check with our instructors about your request and we'll get back with you.";
        }

        $tod1 = "<option>am</option>";
        $tod1 .= (isset($_POST['tod1']) && $_POST['tod1'] == "pm") ? "<option selected>" : "<option>";
        $tod1 .= "pm</option>";

        $tod2 = "<option>am</option>";
        $tod2 .= (isset($_POST['tod2']) && $_POST['tod2'] == "pm") ? "<option selected>" : "<option>";
        $tod2 .= "pm</option>";

        $errorout = "";
        if($errors){
            foreach($errors as $error){
                $errorout .= "*". $error . "<br>";
            }
        }
        $output = <<< EOF
<link rel="stylesheet" type="text/css" href="/office/css/fullcalendar_redmond_theme.css">
<style>
form.fix label{ width:30px; display:inline-block; margin-bottom:10px }
form.fix .formsubmit:[type=submit]{ border:none; background:blue}
</style>
<div style="font-size:12px">
    <div id="errors" style="color:#AA1149; font-size:16px">{$errorout}
    </div>
    <form class="fix" name="customer_edit_form" id="customer_edit_form" accept-charset="utf-8" method="post" action="">
        {$instructor_form_option}

        <h4>Your Information</h4>
        <br class="formtitlebreak"><div class="formtitle">Your Name:</div>
        <div class="forminfo"><input type="text" maxlength="100" size="40" name="FORM_name" id="FORM_name" alt="Your Name" value="{$postname}"></div>

        <br class="formtitlebreak"><div class="formtitle">Your Email:</div>
        <div class="forminfo"><input type="text" maxlength="100" size="40" name="FORM_email" id="FORM_email" alt="Your Email" value="{$postemail}"></div>

        <br class="formtitlebreak"><div class="formtitle">Your Time Zone:</div>

        <div class="forminfo"><select name="FORM_time_zones_id" id="FORM_time_zones_id">
<option value="0">-- select --</option>
{$timezone_form_options}
</select>
</div>

<h4>Please tell us two session times that would work for you.</h4>
<div>
    <label>Date:</label> <input type="text" name="date1" class="date" style="width:70px" value="{$postdate1}"><br>
    <label>Time:</label> <input type=text" name="hour1" maxlength="2" style="width:20px" value="{$posthour1}">:<input value="{$postmin1}" name="min1" type="text" maxlength="2" style="width:20px"">
    <select name="tod1">
        {$tod1}
    </select>
    <br><br>
</div>

<div>
    <label>Date:</label> <input name="date2" type="text" class="date" style="width:70px" value="{$postdate2}"><br>
    <label>Time:</label> <input name="hour2" type=text" maxlength="2" style="width:20px" value="{$posthour2}">:<input value="{$postmin2}" name="min2" type="text" maxlength="2" style="width:20px"">
    <select name="tod2">
        {$tod2}
    </select>
</div>

<div style="clear:both"></div>

<input type="hidden" value="submit" name="FORM_action" id="FORM_action">

<h4>{$thankyou_text}</h4>

<div class="forminfo"><input type="submit" onclick="this.value='Processing. . .';" value="submit" name="" id="FORM_" class="formsubmit">
</div>
</form>
</div>
<script type="text/javascript">
    $("input.date").datepicker();
</script>
EOF;
        echo $output;
    }

    function submit(){
        $errors = array();
        $customer = array("name" => "", "first" => "", "email" => "", "timezone" => "");
        $sess1 = array("date" => "", "time" => "");
        $sess2 = array("date" => "", "time" => "");
        if(isset($_POST['FORM_name']) && $_POST['FORM_name'] != ""){
            $customer['name'] = $_POST['FORM_name'];
            $customer['first'] = explode(" ", $customer['name']);
            $customer['first'] = $customer['first'][0];
        } else {
            $errors['name'] = "Please Enter Your Name";
        }
        if(isset($_POST['FORM_email']) && $_POST['FORM_email'] != ""){
            $customer['email'] = $_POST['FORM_email'];
        } else {
            $errors['email'] = "Please Enter Your Email";
        }

        if(isset($_POST['FORM_time_zones_id']) && $_POST['FORM_time_zones_id'] != "0"){
            $customer['timezone'] = $_POST['FORM_time_zones_id'];
        } else {
            $errors['timezone'] = "Please Select A Timezone";
        }

        if(isset($_POST['date1']) && isset($_POST['hour1']) && isset($_POST['min1'])
            && $_POST['date1'] != "" && $_POST['hour1'] != "" && $_POST['min1'] != ""
        ){
            $sess1['date'] = $_POST['date1'];
            $sess1['time'] = $_POST['hour1'] . ":" . $_POST['min1'] . " " . $_POST['tod1'];

            if($_POST['hour1'] > 12 || $_POST['hour1'] < 1 || $_POST['min1'] > 60 || $_POST['min1'] < 0){
                $errors['sess1'] = "Please Enter A Valid Time For The First Session";
            } else {
                $dt = new DateTime($sess1['date']);
                $d = new DateTime();
                $d->setTime(0,0,0);
                $d = date_add($d, date_interval_create_from_date_string('1 day'));

                if($dt < $d){
                    $errors['sess1'] = "Please Enter A Valid Future Date and Time For The First Session";
                }
            }
        } else {
            $errors['sess1'] = "Please Enter A Valid Date/Time For The First Session";
        }

        if(isset($_POST['date2']) && isset($_POST['hour2']) && isset($_POST['min2'])
            && $_POST['date2'] != "" && $_POST['hour2'] != "" && $_POST['min2'] != ""
        ){
            $sess2['date'] = $_POST['date2'];
            $sess2['time'] = $_POST['hour2'] . ":" . $_POST['min2'] . " " . $_POST['tod2'];

            if($_POST['hour2'] > 12 || $_POST['hour2'] < 1 || $_POST['min2'] > 60 || $_POST['min2'] < 0){
                $errors['sess2'] = "Please Enter A Valid Time For The Second Session";
            } else {
                $dt = new DateTime($sess2['date']);
                $d = new DateTime();
                $d->setTime(0,0,0);
                $d = date_add($d, date_interval_create_from_date_string('1 day'));
                if($dt < $d){
                    $errors['sess2'] = "Please Enter A Valid Future Date and Time For The Second Session";
                }
            }
        } else {
            $errors['sess2'] = "Please Enter A Valid Date/Time For The Second Session";
        }

        if (!empty($errors)) {
            $this->printForm($errors);
            return;
        }

        $instructor_id = $_POST['FORM_instructor_id'];
        if($instructor_id != "0"){
            $record = $this->SQL->GetArrayAll(array(
                    'table' => 'contacts',
                    'keys'  => '`first_name`, `last_name`, `email_address`',
                    'where' => "`WH_ID` = {$instructor_id}",
                ));

            $instructor_firstname = $record[0]['first_name'];
            $instructor_lastname = $record[0]['last_name'];
            $instructor_fullname = $instructor_firstname . " " . $instructor_lastname;
            $instructor_email = $record[0]['email_address'];
        } else {
            $instructor_firstname = "Any Available Instructor";
            $instructor_fullname = "Any Available Instructor";
            $instructor_email = "";
        }

        $tz = "";

        if($customer['timezone'] == "Hawaii Time"){
            $tz = "HST";
        } else if($customer['timezone'] == "Alaska Time"){
            $tz = "AKST";
        } else if($customer['timezone'] == "Pacific Time"){
            $tz = "PST";
        } else if($customer['timezone'] == "Mountain Time"){
            $tz = "MST";
        } else if($customer['timezone'] == "Central Time"){
            $tz = "CST";
        } else if($customer['timezone'] == "Eastern Time"){
            $tz = "EST";
        }

        global $ROOT;
        require_once "$ROOT/phplib/swift4/swift_required.php";
        $MAIL = new Email_MailWh;

        $swap_array                 = array(
            '@@client_name@@'        => $customer['name'],
            '@@client_first@@'       => $customer['first'],
            '@@client_email@@'      => $customer['email'],
            '@@instructor_first@@'   => $instructor_firstname,
            '@@instructor_name@@'   => $instructor_fullname,
            '@@instructor_email@@'  => $instructor_email,
            '@@timezone@@'          => $customer['timezone'],
            '@@sesh1@@'             => $sess1['date'] . " " . $sess1['time'] . " " . $tz,
            '@@sesh2@@'             => $sess2['date'] . " " . $sess2['time'] . " " . $tz,
            '@@date@@'              => date("M jS, Y"),
            '@@tz@@'                => $tz,
        );

        //Email to Instructor
        $msg_array = array(
            'email_template_id'     => 16,
            'swap_array'            => $swap_array,
            'to_email'              => $instructor_email,
            'to_name'               => $instructor_fullname,
            'cc'                    => '',
            'bcc'                   => '',
            'WH_ID'                 => 0
        );

        if($instructor_id != "0"){
            $MAIL->PrepareMailToSend($msg_array);
            if ($m = $MAIL->MailPrepared()) { }
        }

        //Email to Client
        $msg_array = array(
            'email_template_id'     => 17,
            'swap_array'            => $swap_array,
            'to_email'              => $customer['email'],
            'to_name'               => $customer['name'],
            'cc'                    => '',
            'bcc'                   => '',
            'WH_ID'                 => 0
        );

        $MAIL->PrepareMailToSend($msg_array);
        if ($m = $MAIL->MailPrepared()) {  }


        //Email To LaTisha and Dieter
        $msg_array = array(
            'email_template_id'     => 18,
            'swap_array'            => $swap_array,
            'to_email'              => "support@yogalivelink.com",
            'to_name'               => "YLL Support",
            'cc'                    => '',
            'bcc'                   => '',
            'WH_ID'                 => 0
        );

        $MAIL->PrepareMailToSend($msg_array);
        if ($m = $MAIL->MailPrepared()) { }
        echo "<div style='margin:50px 0; font-size:16px; text-align:center; width:400px;'><b>Thank you!</b> Your request has been submitted. <br><br> You will receive an email at {$customer['email']} shortly.</div>";
    }
}