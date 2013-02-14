<?php
/*
REQUIRED VARIABLES
$SITECONFIG['emaillist'] : determines the recipients
$SITECONFIG['emailtopics'] : is an array of drop-down topics.
$SITECONFIG['companyaddress'] : the company address to output
$SITECONFIG['emailsubjectprefix'] = Subject prefix in email sent
$SITECONFIG['emailplaintext']    = 0 for HTML, 1 for Plaintext;
$SITECONFIG['emailsendswift']   = 0;
$SITECONFIG['contactaddress']   = 0 for no address, 1 for address box;

OPTION VARIABLES
$QUERYVAR['mail'] is 'get' variable for $recipient  :  /contact:mail=My_Name  (underscores for spaces)
$QUERYVAR['subject'] is 'get' variable for the subject  :  /contact:subject=mysubject
$QUERYVAR['message'] is 'get' variable for a default message  :  /contact:message=mymessage
$FORM_TIME_SHIFT: is used to change posted time from server time.
$ContactUsText: is the introductory text before the form

TABLE STYLES:
$SetPostedCell: is the style used for the posted table cell at the bottom of the message.  Changes form_helper default.
$TableOption: is the options for the HTML table head.
$ThOption: is the <th> heading options.
$TdOptions: is the <td> cell options.

// SESSION varible set upon each loading of a new page.  Stops double submitting and machine submission.

*/

if (empty($FormPrefix)) {
    include "$LIB/form_helper.php";
}

//-----VARIABLES SET-UP------
$FormAction = $PAGE['pagelink'];

if (!empty($SetTimeShift))  $FORM_TIME_SHIFT = $SetTimeShift;   // used in form_helper
if (!empty($SetPostedCell)) $FORM_VAR['posted_cell_style'] = $SetPostedCell;  // used in form_helper
if (empty($TableOption))    $TableOption = 'align="center" width="90%" border="0" cellspacing="1" cellpadding="2" style="background-color:#888; font-family:Arial, Helvetica, sans-serif; color:#000;"';
if (empty($ThOption))       $ThOption = 'width="20%" style="padding:3px; text-align:right; background-color:#ccf; font-weight:bold;"';
if (empty($TdOption))       $TdOption = 'style="padding:3px; text-align:left; background-color:#fff;"';

SetPost('SEND');

if (Get('mail')) {
    $_POST[$FormPrefix.'mail']  = str_replace('_',' ',$QUERYVAR['mail']);
}
if (Get('subject')) {
    $_POST[$FormPrefix.'SUBJECT'] = Get('subject');
}
if (Get('message')) {
    $_POST[$FormPrefix.'MESSAGE'] = Get('message');
}

$ADDR  = Server('REMOTE_ADDR');

//-----CREATE EMAIL DROP DOWN LIST------
if (count($SITECONFIG['emaillist'])>1) {
  $EmailDropDown='select|Recipient|mail|Y||N';
  foreach ($SITECONFIG['emaillist'] as $key => $value) $EmailDropDown .= "|$key=".str_replace('_',' ',$key);
} else $EmailDropDown = '';

$Address = !empty($SITECONFIG['contactaddress'])? "textarea|Address|ADDRESS|N|30|3||$Mask_General" : '';

if (count($SITECONFIG['emailtopics'])>0) {
  $TopicStr = 'select|Topic|TOPIC|N|';
  foreach ($SITECONFIG['emailtopics'] as $i) $TopicStr .= "|$i";
} else $TopicStr = '';


$_SESSION['CONTACTCHECK'] = empty($SEND)? md5(uniqid(rand(), true)) : Session('CONTACTCHECK');

$FormDataArray =array(
  "form|$FormAction|post",
  $EmailDropDown,
  "text|Name|NAME|Y|30|80||$Mask_Name",
  "phone|Phone|PHONE|N",
  $Address,
  $TopicStr,
  "email|Email|EMAIL|Y|30|80",
  "text|Subject|SUBJECT|Y|45|80||$Mask_General_Line",
  "textarea|Message|MESSAGE|Y|45|6||$Mask_General",
  'hidden|CHECK|'.$_SESSION['CONTACTCHECK'],
  "submit|Send Message|SEND",
  'endform'
);

$ErrorMsg = '';

if (HaveSubmit('SEND')) {

    //-----CHECK FOR SESSION (PREVENT DOUBLE CLICK OR SPAM ATTACK)------
    $CHECK = Post($FormPrefix.'CHECK');
    if ($_SESSION['CONTACTCHECK'] != $CHECK) {
        echo '<h1 style="text-align:center; color:red;">Message Blocked!</h1>';
        return;
    }

    $FormArray = ProcessForm($FormDataArray,$table,$TableOption,$ThOption,$TdOption,$ErrorMsg);

    //-----CHECK FOR SPAM ENTRY------
    $CBlockListTerms = array('[/link]','[/url]');
    $messageOK = true;
    $lcMessage = strtolower($FormArray['MESSAGE']);
    foreach($CBlockListTerms as $term) {
        if (strpos($lcMessage,$term)!==false) $messageOK = false;
    }
    if (!$messageOK) {
        $blockfile = "$logfiledir/block-".date("Y-m-d").'.dat';
        append_file($blockfile,"$ADDR|Contact\n");
        ob_clean();
        MText('Error','<h1>Blocked</h1>'); // will exit;
    }


    //-----CHECK FOR VALID REFERRER WHEN POSTING------
    if (!FromThisDomain()) {
        echo '<h1 style="text-align:center; color:red;">Invalid Processing - Blocked for Security!</h1>';
        return;
    }

    if (!$ErrorMsg) {
    //-----ENTRIES OK THEN SEND E-MAIL------
        if (count($SITECONFIG['emaillist'])>1) {
            $Recipientlist = $SITECONFIG['emaillist'][$FormArray['mail']];
            $recipientname = str_replace('_',' ',$FormArray['mail']);
            $table = str_replace($FormArray['mail'],$recipientname,$table);
        } else {
            $values = array_values($SITECONFIG['emaillist']);
            $Recipientlist = $values[0];
            $recipientname = $SITECONFIG['companyname'];
        }

        $Message   = $table;
        $FromName  = $FormArray['NAME'];
        $Phone     = $FormArray['PHONE'];
        $FromEmail = $FormArray['EMAIL'];
        $Subject   = $SITECONFIG['emailsubjectprefix'].' '.html_entity_decode($FormArray['SUBJECT'],ENT_QUOTES);

        //-----Write Log File------
        $logfile = RootPath($SITECONFIG['logdir']).'/contactlog.dat';
        $logtime = date("Y-m-d:H:i:s");
        $line= "$logtime|$FromName|$FromEmail|{$FormArray['SUBJECT']}|$ADDR|$REFERER\n";
        append_file($logfile,$line);


        if (empty($BCC)) $BCC = '';

        if (!empty($plaintext)) {
            $PhoneText = !empty($phone)? "PHONE: $Phone\n\n" : '';
            $text_message = "NAME: $FromName\n\n{$PhoneText}EMAIL: $FromEmail\n\nSUBJECT: $Subject\n\n".
            "MESSAGE: {$FormArray['MESSAGE']}\n\nPOSTED: ".date('M d, Y')."\n";
            $MAIL_RESPONSE = mail($Recipientlist, $Subject, $text_message, "From: $FromName <$FromEmail>\r\n");
        } else {
            $MAIL_RESPONSE = SendHTMLmail($FromName, $FromEmail, $Recipientlist, $Subject, $Message, $BCC);
        }

        if ($MAIL_RESPONSE == false) {
            $ErrorMsg = '<h2 style="color:#f00">Sending Your Message Failed!</h2>';
        } else {
          unset($_SESSION['CONTACTCHECK']);
          echo "<div style=\"text-align:center;\">
          <p><b>Your Message to <i>$recipientname</i> has been Sent!</b></p>
          <p>Thank you for your message.</p>
          <p>Here&rsquo;s what you sent us:</p>
          $table
          </div>";
          return;
        }
    }
} //----end if send


//===============================INTRO CONTENT================================

if (empty($ContactUsText)) {
    $ContactUsText = '<p>Please contact us with any questions or comments that you might have.</p>';
}

if (empty($COMPANY_INFO)) {
    $COMPANY_INFO = ($SITECONFIG['companyname'] and $SITECONFIG['companyaddress'])? "<p id=\"company_and_address\"><span><b>{$SITECONFIG['companyname']}</b></span><br />{$SITECONFIG['companyaddress']}</p>" : '';
}

print <<<FORMLABEL
$ContactUsText
<div class="forminfo">
$COMPANY_INFO
<p>You may complete the following form to send us a message.
<br />{$FORM_VAR['required_text']}<i>Indicates required entry.</i></p>
</div>

FORMLABEL;

if (!HaveSubmit('SEND') or ($ErrorMsg)) {
    WriteError($ErrorMsg);
    echo OutputForm($FormDataArray,Post('SEND'));
}
