<?php
class Chat_TouchpointChatsEmail extends BaseClass
{
    public $EndChatUser             = false;
    
    public $script_location         = '/office/AJAX/chat/chat_user_email.php';
    public $current_chat_id         = 0;
    public $current_chat_code       = '';
    public $current_chat_email      = '';
    
    private $TableChatSettings      = 'touchpoint_chat_settings';
    private $TableChats             = 'touchpoint_chats';
    public $SuccessRedirectChatPage = 'chat_user';

    public $from_name               = "YogaLiveLink.com";
    public $from_email              = "Support@YogaLiveLink.com";
    public $subject_user  		    = 'YogaLiveLink.com Chat Log';
    
    public $reset_settings          = false;
    private $settings               = array();
    
    
    public $Show_Chat_Rating        = false;
    
    
    public function  __construct()
    {
        parent::__construct();

        $this->GetSettings();
  
        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Description' => 'Send chat log email to user',
            'Created'     => '2010-10-06',
            'Updated'     => '2010-10-06'
        );
        
    } // -------------- END __construct --------------

    public function GetSettings()
    {
        if (Session('settings_touchpoint_chat') && (!$this->reset_settings)) {
            $this->settings = Session('settings_touchpoint_chat');
        } else {
            
            $records = $this->SQL->GetArrayAll(array(
                'table' => $this->TableChatSettings,
                'keys'  => 'setting_name,setting_value',
                'where' => 'active=1',
            ));
            
            foreach ($records as $record) 
            {
                $name   = $record['setting_name'];
                $value  = $record['setting_value'];
                $this->settings[$name] = $value;
            }
            
            $_SESSION['settings_touchpoint_chat'] = $this->settings;
        }
    }
    
    public function ProcessAjax()
    {
        $action = Get('action');
        switch ($action) {
            case 'send_email':
                $this->SendChatEmail();
            break;
        }
    }
    
    public function EmailChatToUser()
    {
        $this->AddStyle();
        
        
        # GET THE CHAT CONTENT
        # ============================================================
        $record = $this->SQL->GetRecord(array(
            'table' => $this->TableChats,
            'keys'  => 'chat, touchpoint_chats_id',
            'where' => "touchpoint_chats_code='$this->current_chat_code' AND active=1",
        ));
        //echo '<br />LAST QUERY ===> ' . $this->SQL->Db_Last_Query . '<br /><br />';
        $this->current_chat_id = $record['touchpoint_chats_id'];
        
        
        
        # FORMAT THE CHAT CONTENT
        # ============================================================
        $lines = explode($this->settings['chat_newline_char'], trim($record['chat']));
        $chatlog = '';
        foreach ($lines as $line)
        {
            if (trim($line)) { # if its not a blank line
                $aTemp = null;
                list($aTemp['time'], $aTemp['nickname'], $aTemp['message']) = explode($this->settings['chat_section_char'], $line);
                $chatlog .=  "\n\n{$aTemp['nickname']}: \n{$aTemp['message']}";    
            }
        }
        #echo $chatlog;
        
        
        
        # OUTPUT A CHAT SURVEY QUESTION
        # =======================================
        if ($this->Show_Chat_Rating) {
            $CHAT = new Chat_RatingsChat();
            $CHAT->touchpoint_chats_id = $this->current_chat_id;
            $CHAT->AddRecord();
        }
        
        $form = <<<FORM
            <div style='font-size:14px;'>
            <div style='font-size:14px; font-weight:bold; border-bottom:1px solid #000;'>Thank You</div>
            <br />
            <div>Click the button below to have a copy of this chat session emailed to you.</div>
            <br /><br />
            <form id="email_form" action="{$this->script_location}?action=send_email" method="post"> 
                Your Email Address: <input type="text" name="email" id="email" value="{$this->current_chat_email}" /><br />
                <input type="hidden" name="chat_message" id="chat_message" value="{$chatlog}" />
                <br />
                <button type="submit" class="positive" name="submit">
                    <img src="/office/images/buttons/save.png" alt=""/>
                    Send Email
                </button>
            </form>
            </div>
FORM;
        echo $form;
    }
    
    
    public function SendChatEmail()
    {
        //foreach ($_POST as $var => $val) {
        //    echo "<br />$var => $val";
        //}
        
        # SEND MESSAGE TO USER (if applicable)
        # =============================================================
        //if ($email_send_to_user) {
            $header                 = "From: {$this->from_name} <{$this->from_email}>\r\n";
            $message                = Post('chat_message');
            $to                     = Post('email');
            $result                 = mail($to, $this->subject_user, $message, $header);
            $result_msg             = ($result) ? "<h1>A copy of the message has been sent to your email address.</h1>" : "<h1>Unable to send a copy of the message to your email address.</h1>";
            echo $result_msg;
        //}    
    }
   
    
    
    private function AddStyle()
    {
        $style = "
        .buttons a, .buttons button{
            display:block;
            float:left;
            margin:0 7px 0 0;
            background-color:#f5f5f5;
            border:1px solid #dedede;
            border-top:1px solid #eee;
            border-left:1px solid #eee;

            font-family:'Lucida Grande', Tahoma, Arial, Verdana, sans-serif;
            font-size:12px;
            line-height:130%;
            text-decoration:none;
            font-weight:bold;
            color:#565656;
            cursor:pointer;
            padding:5px 10px 6px 7px; /* Links */
        }
        .buttons button{
            width:auto;
            overflow:visible;
            padding:4px 10px 3px 7px; /* IE6 */
        }
        .buttons button[type]{
            padding:5px 10px 5px 7px; /* Firefox */
            line-height:17px; /* Safari */
        }
        *:first-child+html button[type]{
            padding:4px 10px 3px 7px; /* IE7 */
        }
        .buttons button img, .buttons a img{
            margin:0 3px -3px 0 !important;
            padding:0;
            border:none;
            width:16px;
            height:16px;
        }

        /* STANDARD */

        button:hover, .buttons a:hover{
            background-color:#dff4ff;
            border:1px solid #c2e1ef;
            color:#336699;
        }
        .buttons a:active{
            background-color:#6299c5;
            border:1px solid #6299c5;
            color:#fff;
        }

        /* POSITIVE */

        button.positive, .buttons a.positive{
            background-color:#E6EFC2;
            border:1px solid #C6D880;
            color:#529214;
        }
        .buttons a.positive:hover, button.positive:hover{
            background-color:#fff;
            border:1px solid #C6D880;
            color:#529214;
        }
        .buttons a.positive:active{
            background-color:#529214;
            border:1px solid #529214;
            color:#fff;
        }

        /* NEGATIVE */

        .buttons a.negative, button.negative{
            background:#fbe3e4;
            border:1px solid #fbc2c4;
            color:#d12f19;
        }
        .buttons a.negative:hover, button.negative:hover{
            background:#fbe3e4;
            border:1px solid #fbc2c4;
            color:#d12f19;
        }
        .buttons a.negative:active{
            background-color:#d12f19;
            border:1px solid #d12f19;
            color:#fff;
        }

        /* REGULAR */

        button.regular, .buttons a.regular{
            color:#336699;
        }
        .buttons a.regular:hover, button.regular:hover{
            background-color:#dff4ff;
            border:1px solid #c2e1ef;
            color:#336699;
        }
        .buttons a.regular:active{
            background-color:#6299c5;
            border:1px solid #6299c5;
            color:#fff;
        }
        ";
        AddStyle($style);
    }



}  // -------------- END CLASS --------------