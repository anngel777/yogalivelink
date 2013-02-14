<?php
//require_once "$ROOT/phplib/swift4/swift_required.php";
//require_once '/var/www/html/phplib/swift4/swift_required.php';
class Email_MailWh extends BaseClass//Email_MailSwift
{
    public $show_query                      = false;

    public $table_email_designs             = 'email_designs';
    public $table_email_designs_id          = 'email_designs_id';
    public $table_email_store               = 'email_sent_logs';
    public $table_email_templates           = 'email_content_templates';
    public $table_email_templates_id        = 'email_content_templates_id';
    
    public  $Bounce                         = 'bounce@mailwh.com';
    
    public $prepared_message_html           = '';
    public $prepared_message_text           = '';
    public $prepared_message_from_name      = '';
    public $prepared_message_from_email     = '';
    public $prepared_message_subject        = '';
    
    
    public $remove_unused_swaps             = true; // will remove any remaining @@xxx@@ swaps found in message
    public $add_sent_message_to_store       = true; // if message is sent - will add copy to email store - might set to false during testing???
    
    private $Sent_Name      = '';
    private $Sent_Email     = '';
    private $From_Name      = '';
    private $From_Email     = '';
    private $Cc_List        = '';
    private $Bcc_List       = '';
    private $Sent_Wh_Id     = '';
    private $Sent_Time      = '';
    
    private $Translation    = '';

    public $preview_email                   = false;
    public $preview_email_sent_logs_id      = 0;
    
    
    public function  __construct()
    {
        $this->SetSQL();
        
        $this->SetParameters(func_get_args());
        $this->preview_email_sent_logs_id   = ($this->GetParameter(0)) ? $this->GetParameter(0) : 0;
        
        $action = ($this->GetParameter(1)) ? $this->GetParameter(1) : 0;
        $this->preview_email = ($action == 'preview') ? true : false;
        
    }
    
    
    public function SetSQL()
    {
        if (empty($this->SQL)) {
            $this->SQL = Lib_Singleton::GetInstance('Lib_Pdo');
        }
    }
    
    public function Execute()
    {
        if ($this->preview_email) {
            $record = $this->GetEmailFromStore($this->preview_email_sent_logs_id);
            $this->PreviewEmail($record);
        } else {
            echo "<div style='font-size:14px; color:#990000;'>UNABLE TO LAOD EMAIL</div>";
        }
    }
    
    public function PreviewEmail($record)
    {
        $email = unserialize($record['email_complete_blob']);
        array_walk_recursive($email,'formatSerializeRev');
        
        $to_name    = $email['to_name'];
        $to_email   = $email['to_email'];
        $subject    = $email['subject'];
        $email      = $email['email_html'];
        
        echo "<h2 class='pagehead'>Email Preview</h2>";
        echo "<div class='email_border'>";
        echo "<div class='email_preview_to'>To: {$to_name} ({$to_email})</div>";
        echo "<div class='email_preview_subject'>Subject: {$subject}</div>";
        echo "</div><br />";
        echo "<div class='email_preview_content'>{$email}</div>";
        
        $style = "
            .email_border {
                border:1px solid #000;
                padding:5px;
                background-color:#f2f2f2;
            }
            .email_preview_to {
                color:#990000;
                font-size:13px;
                font-weight:bold;
            }
            .email_preview_subject {
                color:#990000;
                font-size:13px;
                font-weight:bold;
            }
            .email_preview_content {
                border:1px solid #000;
                padding:5px;
            }
        ";
        AddStyle($style);
        
        /*
        $email_array  =  array(
            'to_name'       => $this->Sent_Name,
            'to_email'      => $this->Sent_Email,
            'subject'       => $this->Subject,
            'email_html'    => $this->Body_Html,
            'email_text'    => $this->Body_Text,
            'from_name'     => $this->From_Name,
            'from_email'    => $this->From_Email,
            'cc'            => $this->Cc_List,
            'bcc'           => $this->Bcc_List,
            'error'         => $this->Error,
        );
        */
    }
    
    public function PrepareMailToSend($msg_array)
    {
        # =====================================================================
        # FUNCTION ::   Gets email design and templates - does all swaps - 
        #               then calls swift to send the message
        # =====================================================================
        
        
        # INITIALIZE VALUES
        # ===========================================================
        $design_text                = '@@CONTENT@@';
        $design_html                = '@@CONTENT@@';
        $template_text              = (isset($msg_array['template_text']))? $msg_array['template_text'] : '';
        $template_html              = (isset($msg_array['template_html']))? $msg_array['template_html'] : '';
        $template_custom_swaps      = (isset($msg_array['template_custom_swaps']))? $msg_array['template_custom_swaps'] : '';
        $swap_array                 = array();
        $final_message_text         = '';
        $final_message_html         = '';
        
        
        # GET THE DESIGN FROM THE DATABASE
        # ===========================================================
        if ($msg_array['email_design_id'] > 0) {
            $design = $this->SQL->GetRecord(array(
                'table' => $this->table_email_designs,
                'keys'  => '*',
                'where' => "`{$this->table_email_designs_id}`={$msg_array['email_design_id']} AND `active`=1",
            ));
            if ($this->show_query) echo '<br /><br />QUERY => ' . $this->SQL->Db_Last_Query;
            
            if ($design) {
                $design_text = $design['content_text'];
                $design_html = $design['content_html'];
                
                #echo "<br /><br /><span style='color:#990000;'>design_text</span><hr><br />".$design_text;
                #echo "<br /><br /><span style='color:#990000;'>design_html</span><hr><br />".$design_html;
                #echo "<br /><br />";
            }
        }
        
        
        # GET THE TEMPLATE FROM THE DATABASE
        # ===========================================================
        if ($msg_array['email_template_id'] > 0) {
            $template = $this->SQL->GetRecord(array(
                'table' => $this->table_email_templates,
                'keys'  => '*',
                'where' => "`{$this->table_email_templates_id}`={$msg_array['email_template_id']} AND `active`=1",
            ));
            if ($this->show_query) echo '<br /><br />QUERY => ' . $this->SQL->Db_Last_Query;
            
            if ($template) {
                $template_text          = $template['content_text'];
                $template_html          = $template['content_html'];
                $template_custom_swaps  = $template['custom_swaps'];
                
                $template_from_name     = $template['from_name'];
                $template_from_email    = $template['from_email'];
                $template_subject       = $template['subject'];
            }
        }
        
        
        # SWAP DATABASE VALUES INTO TEMPLATE
        # ===========================================================
        //$swap_array['@@CONTENT@@'] = ;
        
        
        
        # TAKE CUSTOM SWAPS FROM TEMPLATE - AND PUSH INTO DESIGN
        # ===========================================================
        $swap_array_custom = array();
        if ($template_custom_swaps != '') {
            $lines = explode("\n", $template_custom_swaps);
            foreach ($lines as $line) {
                $parts              = explode('|', $line);
                $swap               = $parts[0];
                $value              = $parts[1];
                $swap_array_custom[$swap]  = $value;
            }
        }
        
        
        # SWAP TEMPLATE INTO DESIGN
        # ===========================================================
        $swap_array_text['@@CONTENT@@'] = $template_text;
        $swap_array_combined_text = array_merge($swap_array_custom, $swap_array_text);
        
        $swap_array_html['@@CONTENT@@'] = $template_html;
        $swap_array_combined_html = array_merge($swap_array_custom, $swap_array_html);
        
        
        # PERFORM ALL NORMAL SWAPS
        # ===========================================================
        $this->prepared_message_text    = $this->MailSwap($swap_array_combined_text, $design_text);
        $this->prepared_message_html    = $this->MailSwap($swap_array_combined_html, $design_html);
        
        
        #echo "<br /><br />prepared_message_text<hr><br />".$this->prepared_message_text;
        #echo "<br /><br />prepared_message_html<hr><br />".$this->prepared_message_html;
        #echo "<br /><br />";
        
        # REMOVE ALL UNUSED SWAPS
        # ===========================================================
        $swap_array_text = array();
        $swap_array_html = array();
        
        if ($this->remove_unused_swaps) {
            $s_text = TextBetweenArray('@@', '@@', $this->prepared_message_text);
            foreach($s_text AS $key => $val) {
                $swap_array_text["@@$val@@"] = '';
            }
            
            $s_html = TextBetweenArray('@@', '@@', $this->prepared_message_html);
            foreach($s_html AS $key => $val) {
                $swap_array_html["@@$val@@"] = '';
            }
            
            $this->prepared_message_text    = $this->MailSwap($swap_array_text, $this->prepared_message_text);
            $this->prepared_message_html    = $this->MailSwap($swap_array_html, $this->prepared_message_html);
        }
        
        
        # ADDITIONAL EMAIL SENDING INFO NEEDED
        # ===========================================================
        $this->prepared_message_from_name     = $template_from_name;
        $this->prepared_message_from_email    = $template_from_email;
        $this->prepared_message_subject       = $template_subject;
    }
    
    
    
    public function MailSwap($swap_array, $content_stream)
    {
        $content_stream = astr_replace($swap_array, $content_stream);
        return $content_stream;
    }
    
    
    public function AddToMailStore()
    {
        # =====================================================================
        # FUNCTION :: SAVES A COPY OF THE EMAIL MESSAGE TO THE PERMANENT STORE
        # =====================================================================
        
        
        $email_array  =  array(
            'to_name'       => $this->Sent_Name,
            'to_email'      => $this->Sent_Email,
            'subject'       => $this->Subject,
            'email_html'    => $this->Body_Html,
            'email_text'    => $this->Body_Text,
            'from_name'     => $this->From_Name,
            'from_email'    => $this->From_Email,
            'cc'            => $this->Cc_List,
            'bcc'           => $this->Bcc_List,
            'error'         => $this->Error,
        );
        array_walk_recursive($email_array,'formatSerialize');
        $email_complete_blob = serialize($email_array);
        
        
        $key_values  =  array(
            'email_q_send_id'               => 0,
            'email_setups_id'               => 0,
            'email_complete_blob'           => $email_complete_blob,
            'wh_id'                         => $this->Sent_Wh_Id,
            'email_sent_datetime'           => 'NOW()',
            'email_bounced_datetime'        => '',
            'user_unsubscribed_datetime'    => '',
            'user_opened_datetime'          => '',
            'user_responded_datetime'       => '',
        );
        
        
        $keys   = $this->SQL->Keys($key_values);
        $values = $this->SQL->Values($key_values);
        $result = $this->SQL->AddRecord($this->table_email_store, $keys, $values);
        
        
        return $result;
    }


    public function GetEmailFromStore($email_sent_logs_id=0)
    {
        $record = $this->SQL->GetRecord(array(
            'table' => $this->table_email_store,
            'keys'  => 'email_complete_blob',
            'where' => "`email_sent_logs_id`={$email_sent_logs_id}",
        ));
        if ($this->show_query) echo '<br /><br />QUERY => ' . $this->SQL->Db_Last_Query;
        
        return $record;
        
        #$val = unserialize($record['email_complete_blob']);
        #array_walk_recursive($val,'formatSerializeRev');
        # note - if getting unserialize errors - its because there are non-valid characters in the design or template - like Word characters

    }
    
    
    public function MailWhIdTemplate($wh_id, $templateId='', $keyvalue='', $cc='', $bcc='')
    {
        //-----REPLACES KEY VALUES IN EMAIL TEMPLATE AND SENDS IT TO WH-ID CONTACT------
        
        if (is_array($wh_id)) extract($wh_id, EXTR_OVERWRITE);

        $wh_id = intOnly($wh_id);
        $templateId = intOnly($templateId);

        $db_table  = "email_templates";
        $keys      = "subject, email_html, email_text, from_name, from_email";
        $where     = "id=$templateId";
        $template_record  = db_GetRecord($db_table, $keys, $where);

        if (!$template_record) {
            return false;
        }

        $message_html  = $template_record['email_html'];
        $message_text  = $template_record['email_text'];

        $subject   = $template_record['subject'];

        $this->From_Name  = $template_record['from_name'];
        $this->From_Email = $template_record['from_email'];

        if ($keyvalue) {
            $message_html = astr_replace($keyvalue, $message_html);
            $message_text = astr_replace($keyvalue, $message_text);
            $subject  = astr_replace($keyvalue, $subject);
        }

        $contact = db_GetRecord('contacts', 'first_name,last_name,email_address', "wh_id=$wh_id");
        if ($contact) {

            $this->Sent_Wh_Id = $wh_id;
            $this->Sent_Name = trim ($contact['first_name'] . ' ' . $contact['last_name']);
            $this->Sent_Email = $contact['email_address'];

            $this->Error = '';
            $this->Error_Extended = '';
            $this->SetFrom( "$this->From_Name:$this->From_Email" );
            $this->SetTo("$this->Sent_Name:$this->Sent_Email");
            $this->SetCc($cc);
            $this->SetBcc($bcc);
            $this->SetSubject($subject);
            $this->SetBodyHtml($message_html);
            $this->SetBodyText($message_text);
            $this->SetReturnPath($this->Bounce);

            $result = $this->SendMessage();
            if ($result) {
                $this->AddToMailStore();
            }

            return $result;

        } else {
            return false;
        }

    }//end of function



    public function MailWhId($wh_id, $subject='', $message_html='', $message_text='', $from_name='', $from_email='', $cc='', $bcc='')
    {
        //-----REPLACES KEY VALUES IN EMAIL TEMPLATE AND SENDS IT TO WH-ID CONTACT------

        if (is_array($wh_id)) extract($wh_id, EXTR_OVERWRITE);
        
        $wh_id = intOnly($wh_id);

        $this->From_Name  = $from_name;
        $this->From_Email = $from_email;

        $contact = db_GetRecord('contacts', 'first_name,last_name,email_address', "wh_id=$wh_id");
        if ($contact) {

            $this->Sent_Wh_Id = $wh_id;
            $this->Sent_Name  = trim ($contact['first_name'] . ' ' . $contact['last_name']);
            $this->Sent_Email = $contact['email_address'];

            $this->Error = '';
            $this->Error_Extended = '';

            $this->SetFrom( "$this->From_Name:$this->From_Email" );
            $this->SetTo("$this->Sent_Name:$this->Sent_Email");

            $this->SetCc($cc);
            $this->SetBcc($bcc);
            $this->SetSubject($subject);
            $this->SetBodyHtml($message_html);
            $this->SetBodyText($message_text);
            $this->SetReturnPath($this->Bounce);

            $result = $this->SendMessage();
            if ($result) {
                $this->AddToMailStore();
            }

            return $result;

        } else {
            return false;
        }

    }//end of function

    
    private function TranslateContents($CONTENT) 
    {
        $this->Translation = Lib_Singleton::GetInstance('Translations');
        if (empty($this->Translation->LANGUAGE)) {
            $this->Language = $this->Translation->SetLanguage();
        } else {
            $this->Language = $this->Translation->LANGUAGE;
        }
        $output  = $this->Translation->TranslateText($CONTENT, $this->Language);
        return $output;
    }


    public function Mail($subject, $message_html='', $message_text='', $to_name='', $to_email='', $from_name='', $from_email='', $cc='', $bcc='', $wh_id='')
    {
    
        if (is_array($subject)) extract($subject, EXTR_OVERWRITE);
        
        $this->Sent_Wh_Id   = $wh_id;
        
        $this->From_Name    = $from_name;
        $this->From_Email   = $from_email;
        
        $this->Sent_Name    = $to_name;
        $this->Sent_Email   = $to_email;
        
        
        
        $this->Error = '';
        $this->Error_Extended = '';
        $this->SetFrom( "$this->From_Name:$this->From_Email" );
        $this->SetTo("$this->Sent_Name:$this->Sent_Email");
        $this->SetCc($cc);
        $this->SetBcc($bcc);
        $this->SetSubject($subject);
        $this->SetBodyHtml($message_html);
        $this->SetBodyText($message_text);
        $this->SetReturnPath($this->Bounce);

        $result = $this->SendMessage();
        if ($result && $this->add_sent_message_to_store) {
            $this->AddToMailStore();
        }

        return $result;
    } //end of function


}