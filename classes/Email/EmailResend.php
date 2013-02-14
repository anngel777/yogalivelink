<?php
class Email_EmailResend extends BaseClass
{
    public $email_preview_location          = '/office/email/email_preview';
    public $Email_Sent_Logs_Id              = 0;
    public $Email_Blob                      = array();
    public $Email_Form_Array                = array();
    public $style_fieldset                  = "style='color:#990000; font-size:14px; font-weight:bold;'";
    
    public function  __construct()
    {
        parent::__construct();
        
        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Description' => 'Resend an email that has already been sent',
        );
        
        $this->SetParameters(func_get_args());
        $this->Email_Sent_Logs_Id  = ($this->GetParameter(0)) ? $this->GetParameter(0) : 0;
        
        echo "<div style='font-weight:bold; color:#990000; background-color:#eee; padding:5px; font-size:16px;'>RE-SEND EMAIL ({$this->Email_Sent_Logs_Id})</div><br />";
    } // -------------- END __construct --------------
    
    
    public function Execute()
    {
        $this->GetOriginalEmail();
        $this->PrePopulateForm();
        $this->AddRecord();
    }
    
    
    public function GetOriginalEmail()
    {
        $record = $this->SQL->GetRecord(array(
            'table' => 'email_sent_logs',
            'keys'  => '*',
            'where' => "`email_sent_logs_id` = {$this->Email_Sent_Logs_Id}",
        ));
        
        $blob   = $record['email_complete_blob'];
        
        $this->Email_Blob = unserialize($blob);
        $this->Email_Blob['wh_id'] = $record['wh_id'];
        
        #echo ArrayToStr($this->Email_Blob);
    }
    
    
    public function PrePopulateForm()
    {
        global $FormPrefix;

        $this->Default_Values = array(
            'to_name'       => $this->Email_Blob['to_name'],
            'to_email'      => $this->Email_Blob['to_email'],
            'subject'       => $this->Email_Blob['subject'],
            'email_html'    => $this->Email_Blob['email_html'],
            'email_text'    => $this->Email_Blob['email_text'],
            'from_name'     => $this->Email_Blob['from_name'],
            'from_email'    => $this->Email_Blob['from_email'],
            'cc'            => $this->Email_Blob['cc'],
            'bcc'           => $this->Email_Blob['bcc'],
            'error'         => $this->Email_Blob['error'],
            'wh_id'         => $this->Email_Blob['wh_id'],
        );
    }
    
    
    public function SetFormArrays()
    {
        global $FormPrefix;
        
        $this->AddScript();
        
        $base_array = array(
            "form|$this->Action_Link|post|db_edit_form",
            "hidden|wh_id",
            "text|To Name|to_name|N|40|255",
            "text|To Email|to_email|N|40|255",
            "text|Subject|subject|N|40|255",
            "html|HTML Content|email_html|N|60|4",
            "button|Preview Html|previewPopUp('{$FormPrefix}email_html', '{$FormPrefix}subject', 'html');",
            "button|Generate Text|htmlToText('{$FormPrefix}email_html', '{$FormPrefix}email_text');",
            "textarea|Text Content|email_text|N|60|4",
            "button|Preview Text|previewPopUp('{$FormPrefix}email_text', '{$FormPrefix}title', 'text');",
            
            "code|<br /><br />",
            "fieldset||options_fieldset|$this->style_fieldset",
                "submit|Send Email|$this->Add_Submit_Name",
            "endfieldset",
            "code|<br /><br />",
            
            "fieldset||options_fieldset|$this->style_fieldset",
                "text|From Name|from_name|N|40|255",
                "text|From Email|from_email|N|40|255",
                "textarea|CC|cc|N|60|1",
                "textarea|BCC|bcc|N|60|1",
            "endfieldset",
            
            'endform',
        );
        
        $this->Form_Data_Array_Add = $base_array;
    }
    
    
    public function PostProcessFormValues($FormArray) // extended from parent
    {
        global $ROOT;
        require_once "$ROOT/phplib/swift4/swift_required.php";
        $MAIL = new Email_MailWh;
        
        $subject        = $FormArray['subject'];
        $message_html   = $FormArray['email_html'];
        $message_text   = $FormArray['email_text'];
        $to_name        = $FormArray['to_name'];
        $to_email       = $FormArray['to_email'];
        $from_name      = $FormArray['from_name'];
        $from_email     = $FormArray['from_email'];
        $cc             = $FormArray['cc'];
        $bcc            = $FormArray['bcc'];
        $wh_id          = $FormArray['wh_id'];
        
        if ($MAIL->Mail($subject, $message_html, $message_text, $to_name, $to_email, $from_name, $from_email, $cc, $bcc, $wh_id)) {
            echo "<br />Email Sent to: $to_name <$to_email>";
            AddFlash("Email Sent to: $to_name <$to_email>");
        } else {
            echo "<br />Message Failed: $MAIL->Error";
            AddError("Message Failed: $MAIL->Error");
        }
        
        exit();
        
        unset($FormArray);
        return $FormArray;
    }

    
    
    public function AddScript()
    {
        $script = <<<SCRIPT

            // -------------- function for email conversion of HTML to Text --------------
            function htmlToText(inputName, outputName)
            {
                var formdata = $('#'+inputName).serialize();
                var formdata = formdata + '&inputFieldName=' + inputName;
                
                
                $.post('helper/ajax_html_to_text_helper.php',
                      {data : formdata},
                    function(data) {
                        $('#'+outputName).val(data);
                    });
                
            }
            
            function previewPopUp(sourceField, subjectField, content_type)
            {
                var file = '{$this->email_preview_location};type=' + content_type + ';source_dialog_id=' + dialogNumber + ';source_field=' + sourceField + ';subject_field=' + subjectField;
                top.parent.appformCreate('Email Preview', file, 'apps');
            }

SCRIPT;
        AddScript($script);
    }
    
    
}  // -------------- END CLASS --------------