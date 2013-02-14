<?php
//file: /class.Contact.php

class Contact
{

/*
USE:
if (empty($FormPrefix)) {
    include "$LIB/form_helper.php";
}
include "$LIB/class.Contact.php";


REQUIRED VARIABLES
$SITECONFIG['emaillist'] : determines the recipients
$SITECONFIG['emailtopics'] : is an array of drop-down topics.
$SITECONFIG['companyaddress'] : the company address to output
$SITECONFIG['emailsubjectprefix'] = Subject prefix in email sent
$SITECONFIG['emailplaintext']     = 0 for HTML, 1 for Plaintext;
$SITECONFIG['emailsendswift']     = 0;
$SITECONFIG['contactaddress']     = 0 for no address, 1 for address box;

OPTION VARIABLES
$_GET['mail'] is 'get' variable for $recipient  :  /contact:mail=My_Name  (underscores for spaces)
$_GET['subject'] is 'get' variable for the subject  :  /contact:subject=mysubject
$_GET['message'] is 'get' variable for a default message  :  /contact:message=mymessage
$this->SetTimeShift() --> Sets $FORM_TIME_SHIFT: is used to change posted time from server time.
$this->SetContactUsText($text) : is the introductory text before the form

TABLE STYLES:
$this->SetPostedCellStyle($style) : is the style used for the posted table cell at the bottom of the message.  Changes form_helper default.
$this->Table_Option: is the options for the HTML table head.
$this->Th_Option: is the <th> heading options.
$this->Td_Options: is the <td> cell options.

// SESSION varible set upon each loading of a new page.  Stops double submitting and machine submission.

*/
    public $Table_Option = 'align="center" width="90%" border="0" cellspacing="1" cellpadding="2" style="background-color:#888; font-family:Arial, Helvetica, sans-serif; color:#000;"';
    public $Th_Option    = 'width="20%" style="padding:3px; text-align:right; background-color:#ccf; font-weight:bold;"';
    public $Td_Option    = 'style="padding:3px; text-align:left; background-color:#fff;"';

    public $Action_Link     = '';
    public $Site_Config     = array();
    public $Addr            = '';
    public $Recipient_Name  = '';
    public $Recipient_Email = '';
    public $Form_Data       = '';
    public $Error           = '';

    public $Contact_Page_Link = '/contact';

    public $Block_List_Terms = array('[/link]', '[/url]');

    public $Contact_Us_Text = '<p>Please contact us with any questions or comments that you might have.</p>';
    public $Bcc             = '';
    public $Send_Text       = '';
    public $Eq_Var          = 'eq';
    public $Company_Info    = '';

    public function __construct()
    {
        global $SITECONFIG, $FORM_VAR;
        $this->Site_Config = $SITECONFIG;
        $this->Action_Link = Server('REQUEST_URI');
        $this->Addr        = Server('REMOTE_ADDR');
        $_SESSION['CONTACTCHECK'] = Post('SEND')? Session('CONTACTCHECK') : md5(uniqid(rand(), true));

        $this->Send_Text  = '<p>You may complete the following form to send us a message.<br />' .
            $FORM_VAR['required_text'] . '<i>Indicates required entry.</i></p>';
    }

    public function MailLink($name, $email, $company='', $subject='', $message='')
    {
        $eq = "name=$name;email=$email";
        if ($company) {
            $eq .= ";company=$company";
        }
        if ($subject) {
            $eq .= ";subject=$subject";
        }
        if ($message) {
            $eq .= ";message=$message";
        }

        $link = $this->Contact_Page_Link . ':' . $this->Eq_Var . '=' . EncryptQuery($eq);
        return $link;
    }

    public function ProcessContactPage()
    {
        $this->SetQueryVars();
        $this->SetFormDataArray();

        if (HaveSubmit('SEND')) {
            $this->SendMessage();
        }

        if (!HaveSubmit('SEND') or ($this->Error)) {
            $this->OutputIntro();
            WriteError($this->Error);
            echo OutputForm($this->Form_Data, Post('SEND'));
        }
    }

    public function SetQueryVars()
    {
        if (Get('mail')) {
            Form_PostValue('MAIL', str_replace('_', ' ', Get('mail')));
        }
        if (Get('subject')) {
            Form_PostValue('SUBJECT', Get('subject'));
        }
        if (Get('message')) {
            Form_PostValue('MESSAGE', Get('message'));
        }

        if (Get($this->Eq_Var)) {
            $data = GetEncryptQuery($this->Eq_Var);

            if (ArrayValue($data, 'mail')) {
                Form_PostValue('mail', $data['mail']);
            }

            if (ArrayValue($data, 'subject')) {
                Form_PostValue('SUBJECT', $data['subject']);
            }

            if (ArrayValue($data, 'message')) {
                Form_PostValue('MESSAGE', $data['message']);
            }

            if (ArrayValue($data, 'name')) {
                $this->Recipient_Name = $data['name'];
            }

            if (ArrayValue($data, 'email')) {
                $this->Recipient_Email = $data['email'];
                if ((strpos($this->Recipient_Email, ',') === false) and (strpos($this->Recipient_Email, '<') === false)) {
                    $this->Recipient_Email = $this->Recipient_Name . '<' . $this->Recipient_Email . '>';
                }
            }

            if (ArrayValue($data, 'company')) {
                $this->Company_Info = $data['company'];
            }
        }

    }

    public function SetFormDataArray()
    {
        $this->Form_Data = array(
            "form|$this->Action_Link|post",
            $this->GetEmailDropDownList(),
            'text|Name|NAME|Y|30|80|',
            'phone|phone|PHONE|N',
            $this->GetAddress(),
            $this->GetEmailTopics(),
            "email|Email|EMAIL|Y|30|80",
            "text|Subject|SUBJECT|Y|45|80|",
            "textarea|Message|MESSAGE|Y|45|6|",
            'hidden|CHECK|' . Session('CONTACTCHECK'),
            "submit|Send Message|SEND",
            'endform'
        );

    }

    public function GetEmailTopics()
    {
        if (count($this->Site_Config['emailtopics']) > 0) {
            $RESULT = 'select|Topic|TOPIC|N|';
            foreach ($this->Site_Config['emailtopics'] as $i) {
                $RESULT .= "|$i";
            }
        } else  {
            $RESULT = '';
        }

        return $RESULT;
    }

    public function GetAddress()
    {
        $RESULT = '';
        if (!empty($this->Site_Config['contactaddress'])) {
            $RESULT = "textarea|Address|ADDRESS|N|30|3|";
        }
        return $RESULT;
    }

    public function SetContactUsText($text)
    {
        $this->Contact_Us_Text = $text;
    }

    public function SetTimeShift($time_shift)
    {
        global $FORM_TIME_SHIFT;
        $FORM_TIME_SHIFT = $time_shift;
    }

    public function SetPostedCellStyle($style)
    {
        global $FORM_VAR;
        $FORM_VAR['posted_cell_style'] = $style;  // used in form_helper
    }

    public function GetEmailDropDownList()
    {
        $RESULT = '';
        if (!empty($this->Site_Config['emaillist'])) {
            if (count($this->Site_Config['emaillist']) > 1) {
                $RESULT = 'select|Recipient|MAIL|Y||N';
                foreach ($this->Site_Config['emaillist'] as $key => $value) {
                    $RESULT .= "|$key=" . str_replace('_',' ',$key);
                }
            }
        }
        return $RESULT;
    }


    public function SendMessage()
    {

        //-----CHECK FOR SESSION (PREVENT DOUBLE CLICK OR SPAM ATTACK)------
        if (Session('CONTACTCHECK') != GetPostItem('CHECK')) {
            WriteError('<h1>Message Blocked!</h1>');
            return;
        }

        $form_array = ProcessForm($this->Form_Data, $table,
            $this->Table_Option, $this->Th_Option, $this->Td_Option, $this->Error);
            //AddError($this->Error);

        //-----CHECK FOR SPAM ENTRY------

        foreach($this->Block_List_Terms as $term) {
            if (stripos($form_array['MESSAGE'], $term) !== false) {
                $message_ok = false;
                $blockfile = $this->$Site_Config['logdir'] . '/block-' . date("Y-m-d") . '.dat';
                append_file($blockfile, $this->Addr . "|Contact\n");
                ob_clean();
                MText('Error', '<h1>Blocked</h1>'); // will exit;
            }
        }


        //-----CHECK FOR VALID REFERRER WHEN POSTING------
        if (!FromThisDomain()) {
            WriteError('<h1>Invalid Processing - Blocked for Security!</h1>');
            return;
        }

        if (!$this->Error) {
            //-----ENTRIES OK THEN SEND E-MAIL------
            if ($this->Recipient_Email) {
                $recipient_name = $this->Recipient_Name;
                $recipient_list = $this->Recipient_Email;
            } else {
                if (count($this->Site_Config['emaillist']) > 1) {
                    $recipient_list = $this->Site_Config['emaillist'][$form_array['MAIL']];
                    $recipient_name = str_replace('_',' ',$form_array['MAIL']);
                    $table = str_replace($form_array['MAIL'], $recipient_name, $table);
                } else {
                    $values = array_values($this->Site_Config['emaillist']);
                    $recipient_list = $values[0];
                    $recipient_name = $this->Site_Config['companyname'];
                }
            }

            $message   = $table;
            $from_name = $form_array['NAME'];
            $phone     = $form_array['PHONE'];
            $from_email= $form_array['EMAIL'];
            $subject   = $this->Site_Config['emailsubjectprefix'] . ' ' . html_entity_decode($form_array['SUBJECT'], ENT_QUOTES);

            //-----Write Log File------
            $logfile  = RootPath($this->Site_Config['logdir']) . '/contactlog.dat';
            $logtime  = date("Y-m-d:H:i:s");
            $referrer = Server('HTTP_REFERER');
            $line= "$logtime|$from_name|$from_email|{$form_array['SUBJECT']}|$this->Addr|$referrer\n";
            append_file($logfile,$line);


            if (!empty($this->Site_Config['emailplaintext'])) {
                $phoneText = !empty($phone)? "PHONE: $phone\n\n" : '';
                $text_message = "NAME: $from_name\n\n{$phoneText}EMAIL: $from_email\n\nSUBJECT: $subject\n\n".
                "MESSAGE: {$form_array['MESSAGE']}\n\nPOSTED: ".date('M d, Y')."\n";
                $MAIL_RESPONSE = mail($recipient_list, $subject, $text_message, "From: $from_name <$from_email>\r\n");

            } else {
                $MAIL_RESPONSE = SendHTMLmail($from_name, $from_email, $recipient_list, $subject, $message, $this->Bcc);
            }

            if ($MAIL_RESPONSE == false) {
                $this->Error = '<h2>Sending Your Message Failed!</h2>';
            } else {
              unset($_SESSION['CONTACTCHECK']);
              echo "<div style=\"text-align:center;\">
              <p><b>Your Message to <i>$recipient_name</i> has been Sent!</b></p>
              <p>Thank you for your message.</p>
              <p>Here&rsquo;s what you sent us:</p>
              $table
              </div>";
              return;
            }
        }
    }


    public function OutputIntro()
    {
        if (empty($this->Company_Info)) {
            $this->Company_Info = ($this->Site_Config['companyname'] and $this->Site_Config['companyaddress'])?
                "<p id=\"company_and_address\"><span><b>{$this->Site_Config['companyname']}</b></span>
                <br />{$this->Site_Config['companyaddress']}</p>" : '';
        }

        $n = "\n";

        echo $this->Contact_Us_Text . $n . '<div class="forminfo">' . $n . $this->Company_Info . $n . $this->Send_Text . '</div>';
    }

}
