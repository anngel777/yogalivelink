<?php
class Email_EmailContentTemplates extends BaseClass
{
    public $email_preview_location          = '/office/email/email_preview';
    public $table_email_designs             = 'email_designs';
    public $table_email_designs_id          = 'email_designs_id';
    public $style_fieldset                  = "style='color:#990000; font-size:14px; font-weight:bold;'";
    
    public function  __construct()
    {
        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => '',
            'Description' => 'Create and manage email_content_templates',
            'Created'     => '2010-12-03',
            'Updated'     => '2010-12-03'
        );

        $this->Table  = 'email_content_templates';

        $this->Add_Submit_Name  = 'EMAIL_CONTENT_TEMPLATES_SUBMIT_ADD';
        $this->Edit_Submit_Name = 'EMAIL_CONTENT_TEMPLATES_SUBMIT_EDIT';

        $this->Index_Name = 'email_content_templates_id';

        $this->Flash_Field = 'email_content_templates_id';

        $this->Default_Where = '';  // additional search conditions

        $this->Default_Sort  = 'email_content_templates_id';  // field for default table sort

        $this->Field_Titles = array(
            'email_content_templates_id' => 'Email Content Templates Id',
            'email_design_id' => 'Email Design Id',
            'title' => 'Title',
            'description' => 'Description',
            'created_by_wh_id' => 'Created By Wh Id',
            'from_name' => 'From Name',
            'from_email' => 'From Email',
            'subject' => 'Subject',
            'content_text' => 'Content Text',
            'content_html' => 'Content Html',
            'active' => 'Active',
            'updated' => 'Updated',
            'created' => 'Created'
        );


        $this->Default_Fields = 'email_design_id,title,description,created_by_wh_id,from_name,from_email,subject,content_text,content_html';
        $this->Unique_Fields = '';
        
        $this->Default_Values = array(
            'created_by_wh_id' => $_SESSION['USER_LOGIN']['LOGIN_RECORD']['wh_id'],
        );
        

    } // -------------- END __construct --------------
    
    
    public function PrePopulateFormValues($id, $field='')
    {
        if (!Post('SEND_TEST')) {
            parent::PrePopulateFormValues($id, $field);
        }
    }
    
    
    public function SetFormArrays()
    {
        $this->AddScript();
        
        global $FormPrefix, $ROOT;
        
        if (Post('SEND_TEST')) {
            $to                     = GetPostItem('TEST_EMAIL');
            $from                   = GetPostItem('from_name') . ':' . GetPostItem('from_email');
            $subject                = GetPostItem('subject');
            $template_html          = Form_GetPostHTML('content_html');
            $template_text          = GetPostItem('content_text');
            $template_custom_swaps  = Form_GetPostHTML('custom_swaps');
            $email_design_id        = GetPostItem('email_design_id');
            
            $to_name        = $to;
            $to_email       = $to;
            $from_name      = $from;
            $from_email     = $from;
            $cc             = '';
            $bcc            = '';
            $wh_id          = '';
            
            #$PDF          = Form_GetPostHTML('pdf');

            require_once "$ROOT/phplib/swift4/swift_required.php";
            $MAIL = new Email_MailWh;
            
            $replace_array = array (
                '[[WHID]]'        => 'WH-ID',
                '[[NAME]]'        => 'TEST NAME',
                '[[name]]'        => 'TEST NAME',
                '[[GUID]]'        => 'GUID',
                '[[guid]]'        => 'GUID',
                '[[FIRST_NAME]]'  => 'TEST FIRST NAME',
                '[[LAST_NAME]]'   => 'TEST LAST NAME',
                '[[FULL_NAME]]'   => 'TEST FULL NAME',
                '[[EMAIL]]'       => $to,
                '[[SALUTATION]]'  => 'TEST SALUTATION'
            );
            
            /*
            if ($PDF) {
                require_once "$ROOT/global/pdf_helper.php";
                $message_pdf = astr_replace($replace_array, $PDF);
                $pdf = OutputPdf($message_pdf, '', 'S');
                $MAIL->SetAttachedDynamicFiles(array($pdf, 'attachment.pdf', 'application/pdf'));
            }
            */
            
            $msg_array = array(
                'from'                  => $from,
                'to'                    => $to,
                'subject'               => $subject,
                'template_html'         => $template_html,
                'template_text'         => $template_text,
                'template_custom_swaps' => $template_custom_swaps,
                'email_template_id'     => 0,
                'email_design_id'       => $email_design_id,
            );
            $MAIL->PrepareMailToSend($msg_array);
            
            $message_html = $MAIL->prepared_message_html;
            $message_text = $MAIL->prepared_message_text;
            
            if ($MAIL->Mail($subject, $message_html, $message_text, $to_name, $to_email, $from_name, $from_email, $cc, $bcc, $wh_id)) {
                AddFlash("Email Sent to: $to");
                //AddMessage($MAIL->GetMessageDetails());
            } else {
                AddError("Message Failed: $MAIL->Error");
            }

        }
        
        #$attachments = GetDirectory("$ROOT/mail/attachments");
        #$attachment_list = Form_ArrayToList($attachments);
        
        # DESIGN AUTO-COMPLETE
        $display_value      = 'title'; //"CONCAT(title, ' ', last_name, ' ', email_address)";
        $eq_design          = EncryptQuery("ac_table={$this->table_email_designs}&ac_key={$this->table_email_designs_id}&ac_field={$display_value}");
        
        $base_array = array(
            "form|$this->Action_Link|post|db_edit_form",
            'hidden|created_by_wh_id',
            
            
            "fieldset|Template Information|options_fieldset|$this->style_fieldset",
                'text|Title|title|N|60|255',
                'textarea|Description|description|N|60|2',
                "autocomplete|Email Design|email_design_id|N|60|80||addAutoCompleteFunctionality|$this->Auto_Complete_Helper?eq=$eq_design",
            "endfieldset",
            
            
            "fieldset|Template Content|options_fieldset|$this->style_fieldset",
                'text|From Name|from_name|N|60|255',
                'text|From Email|from_email|N|60|255',
                'text|Subject|subject|N|60|255',
                
                'html|Content Html (@@CONTENT@@)|content_html|N|60|4',
                "button|Preview Html|previewPopUp('{$FormPrefix}content_html', '{$FormPrefix}subject', 'html');",
                "button|Generate Text|htmlToText('{$FormPrefix}content_html', '{$FormPrefix}content_text');",
                'html|Content Text (@@CONTENT@@)|content_text|N|60|4',
                "button|Preview Text|previewPopUp('{$FormPrefix}content_text', '{$FormPrefix}title', 'text');",
                'html|Custom Swaps (@@*****@@)|custom_swaps|N|60|4',
            "endfieldset",
            
            
            'code|<p class="TEMPLATE_LOAD">',
            "@text|<b>Email:</b>&nbsp;|TEST_EMAIL|N|40|80",
            "@submit|Send Test|SEND_TEST",
            '</p>'
        );

        if ($this->Action == 'ADD') {
            $base_array[] = "submit|Add Record|$this->Add_Submit_Name";
            $base_array[] = 'endform';
            $this->Form_Data_Array_Add = $base_array;
        } else {
            #$base_array[] = 'checkbox|Active|active||1|0';
            $base_array[] = "submit|Update Record|$this->Edit_Submit_Name";
            $base_array[] = 'endform';
            $this->Form_Data_Array_Edit = $base_array;
        }
    }
    
    
    public function PostProcessFormValues($FormArray) // extended from parent
    {
        // extend this function to process values -- simply return the array back
        unset($FormArray['TEST_EMAIL']);
        unset($FormArray['template']);

        //$not_allowed = array('<html>','<head>', '<body>','<style','</html>','</head>', '</body>','</style>');
        //if (ArrayItemsWithinStr($not_allowed, $FormArray['email_html'])) {
        //    $this->Error = '<p>Illegal HTML for Email.  Remove Header code.</p>';
        //}

        return $FormArray;
    }


    public function ProcessTableCell($field, &$value, &$td_options, $id='')
    {
        parent::ProcessTableCell($field, $value, $td_options, $id);

        if (($field == 'email_html') or ($field == 'email_text') or ($field == 'pdf')) {
            $value = TruncStr(strip_tags($value), 100);
        }

    }
    
    
    public function AddScript()
    {
        $script = <<<SCRIPT
            // -------------- function for email setup --------------
            function setupEmail(queryStr)
            {
                top.parent.appformCreate('Email Setup', 'email_setup;query=' + queryStr, 'apps');
            }

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

            function tableSendMailClick(idx, value, eq)
            {
                var idbase = 'TABLE_ROW_ID' + idx + '_';

                $('#' + idbase + value +' td').css('background-color','#ff7');

                top.parent.appformCreate('Mail Send', 'mail_send;eq=' + eq, 'apps');
            }
SCRIPT;
        AddScript($script);
    }
    
    
}  // -------------- END CLASS --------------