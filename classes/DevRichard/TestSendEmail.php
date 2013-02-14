<?php
class DevRichard_TestSendEmail {
    
    # FUNCTION TURNS ON OR OFF THE SESSION VARIABLES TO TRICK SYSTEM INTO THINKING
    # USER HAS LOGGED INTO THE ADMIN+ PAGE. THIS WILL THEN SHOW ALL ERRORS ON THE SCREEN
    
    public function  __construct()
    {
        
    }
    
    public function Execute()
    {
        global $ROOT;
        require_once "$ROOT/phplib/swift4/swift_required.php";
        $MAIL = new Email_MailWh;
        
        $email_design_id        = 2;
        $email_template_id      = 1;
        $wh_id                  = 1;
        
        $msg_array = array(
            'email_template_id'     => $email_template_id,
            'email_design_id'       => $email_design_id,
        );
        $MAIL->PrepareMailToSend($msg_array);
        
        $to_name        = 'richard';
        $to_email       = 'richard@mailwh.com';
        $cc             = '';
        $bcc            = '';
        $from_name      = $MAIL->prepared_message_from_name;
        $from_email     = $MAIL->prepared_message_from_email;
        $subject        = $MAIL->prepared_message_subject;
        $message_html   = $MAIL->prepared_message_html;
        $message_text   = $MAIL->prepared_message_text;
        
        if ($MAIL->Mail($subject, $message_html, $message_text, $to_name, $to_email, $from_name, $from_email, $cc, $bcc, $wh_id)) {
            AddFlash("Email Sent to: $to_name <$to_email>");
            //AddMessage($MAIL->GetMessageDetails());
        } else {
            AddError("Message Failed: $MAIL->Error");
        }
        
    }
    
    
}