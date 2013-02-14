<?php
class Chat_Chat //extends BaseClass
{
    public $Chat_Status_Online              = true;
    public $Ico_Chat                        = null;
    
    public $DIALOGID                    = 0;
    public $Added_Vars                  = '';
    
    private $local_location             = "/office/AJAX/chat/chat_user.php";
    private $local_email_chat           = "/office/chat/chat_user_email";
    private $script_location            = "/office/AJAX/chat/chat_user.php?action=add_chat_content";
    private $script_location_notes      = "/office/AJAX/chat/chat_user.php?action=add_chat_notes";
    private $location_end_chat_user     = "/office/AJAX/chat/chat_user.php?action=end_chat_user";
    
    # DELETE THIS LATER
    private $location_end_chat_admin    = "/office/AJAX/chat/chat_admin.php?action=end_chat_admin";
    #########################
    
    
    public $SQL                     = '';
    private $TableChats             = 'touchpoint_chats';
    private $TableChatSettings      = 'touchpoint_chat_settings';
    private $TableContacts          = 'contacts';
    private $LockRecordOnAdminOpen  = false;
    private $current_chat_id        = 0;
    private $current_chat_code      = '';
    
    public $reset_settings          = false;
    private $settings               = array();
    
    private $LoadCommonResponses    = true;
    private $LoadUserInformation    = true;
    private $LoadClassVersion       = true;
    
    public function  __construct()
    {
        $this->SQL = Lib_Singleton::GetInstance('Lib_Pdo');
        $this->GetSettings();
        
        $this->Ico_Chat                 = $GLOBALS['ICO_CHAT'];
        
        $this->ClassInfo = array(
            'Created By'    => 'Richard WItherspoon',
            'Description'   => 'Actual chat windows',
            'Created'       => '2010-09-15',
            'Updated'       => '2010-10-09',
            'Revision'          => '1.00.04',
            'Revision Title'    => 'ALPHA'
        );
        
    } // -------------- END __construct --------------

    public function ModifyURLs()
    {
        $this->local_location             .= $this->Added_Vars;
        $this->local_email_chat           .= $this->Added_Vars;
        $this->script_location            .= $this->Added_Vars;
        $this->script_location_notes      .= $this->Added_Vars;
        $this->location_end_chat_user     .= $this->Added_Vars;
    }

    public function OutputChatStatusBox()
    {
        $online = $this->Chat_Status_Online;
        $message = ($online) ? "ONLINE" : "NOT AVAILABLE";
        //$message .= "<br />[<a href='#' onclick=\"top.parent.appformCreate('Chat User', '/office/chat/chat_user','apps'); return false;\">click here</a>]";
        //$message .= "<br />[<a href='/office/chat/chat_user' target='_blank'>click here</a>]";
        $message .= ($online) ? "<br />[<a href=\"#\" onclick=\"javascript:parent.window.open('/office/chat/chat_user;resize=true','blank','toolbar=no,width=250,height=250,location=no')\">CLICK HERE</a>]" : '';
        //$message .= "<br /><br /><span onclick=\"return openPerfectPopup(300,'Title','Contents','/office/chat/chat_user')\">new window</span>";
    
        $output = AddBox_Type2('Live Chat', $message, $this->Ico_Chat);
        return $output;
    }
    
    public function OutputChatStatusBoxType3()
    {
        $online     = $this->Chat_Status_Online;
        $message    = ($online) ? "live chat online" : "chat unavailable";
        $link       = ($online) ? "<a href=\"#\" onclick=\"javascript:parent.window.open('/office/chat/chat_user;resize=true','blank','toolbar=no,width=250,height=250,location=no')\">" : '';
        $output     = AddBox_Type3($message, $link, $this->Ico_Chat);
        
        return $output;
    }
    
    public function ProcessAjax()
    {
        $this->SQL = Lib_Singleton::GetInstance('Lib_Pdo');
        
        
        $action = Get('action');
        switch ($action) {
            case 'open_chat_request':
                $chat_id            = Get('chat_id');
                $code               = Get('chat_code');
                $admin_contacts_id  = Get('admin_id');
                
                # CHECK IF CHAT IS LOCKED - OR STILL AVAILABLE
                # =====================================================
                $record = $this->SQL->GetRecord(array(
                    'table' => $this->TableChats,
                    'keys'  => 'locked',
                    'where' => "touchpoint_chats_id=$chat_id",
                ));
                
                if ($record['locked'] == 1) {
                    echo "<h1>CANNOT OPEN --> CHAT HAS ALREADY BEEN LOCKED BY ANOTHER ADMIN</h1>";
                } else {
                    # LOCK THE CHAT
                    # =====================================================
                    # mark the chat as taken by admin 
                    # ---> so it can't be opened by another admin
                    # ---> will dssapear from chat list in a few seconds
                    //$this->SQL->SetWantQuery();
                    if ($this->LockRecordOnAdminOpen) 
                    {
                        $result = $this->SQL->UpdateRecord(array(
                            'table'         => $this->TableChats,
                            'key_values'    => "`locked`=1,`admin_contacts_id`=$admin_contacts_id",
                            'where'         => "touchpoint_chats_id=$chat_id",
                        ));
                        
                        $last_Query = $this->SQL->GetLastQuery();
                        echo "<br />Last Query ===> " . $last_Query;
                        echo "<br />update result ===> " . $result;
                    }
                    
                    
                    # OPEN THE CHAT WINDOW FOR THE ADMIN
                    # =====================================================
                    //$script = "top.parent.appformCreate('Launch', '/office/chat/chat_user;code=$code', 'apps');";
                    $script = "top.parent.appformCreate('Launch', '/office/chat/chat_admin;code=$code', 'apps');";
                    $this->echoScript($script);                
                }
            break;
            case 'open_contact_window':
                $wh_id  = Get('wh_id');
                $script = "top.parent.appformCreate('Contact Information', '/office/administration/admin_users;wh_id=$wh_id', 'apps');";
                $this->echoScript($script);                
            break;
            case 'start_new_chat_user':
                $chat_id            = Get('chat_id');
                $code               = Get('chat_code');
                $admin_contacts_id  = Get('admin_id');
                
                /*
                $_POST['chat_name']
                $_POST['chat_email']
                $_POST['chat_category']
                $_POST['chat_request']
                
                $record = $this->SQL->AddRecord(array(
                    'table'     => $this->TableChats,
                    'keys'      => 'locked',
                    'values'    => 'locked',
                ));
                */
            break;
            case 'get_chat_content':
                $code       = Get('code');
                $curr_rows  = Get('currentRows');
                
                $data = array();
                
                $record = $this->SQL->GetRecord(array(
                    'table' => $this->TableChats,
                    'keys'  => 'chat,line_count',
                    'where' => "touchpoint_chats_code='$code'",
                ));
                
                $lines = explode($this->settings['chat_newline_char'], trim($record['chat']));
                $total_rows = count($lines)-1;
                if ($total_rows > $curr_rows) {
                    # THERE ARE NEW ROWS OF INFO NOT CURRENTLY DISPLAYED
                    $temp_count = 1;
                    foreach ($lines as $line)
                    {
                        if (trim($line)) { # if its not a blank line
                            if ($temp_count > $curr_rows) {
                                $aTemp = null;
                                list($aTemp['time'], $aTemp['nickname'], $aTemp['message']) = explode($this->settings['chat_section_char'], $line);
                                //$aTemp['nickname'] .= " -- $total_rows ===> $curr_rows";
                                if ($aTemp['message']) $data[] = $aTemp;
                            }
                            $temp_count++;
                        }
                    }
                    
                    # OUTPUT THE JSON DATA
                    if (count($data) > 0) {
                        $json = new Services_JSON();
                        $out = $json->encode($data);
                    } else {
                        $out = '';
                    }
                    print $out;
                }
            break;
            case 'add_chat_content':
                //$_POST['chat_code']     = '3BXQ86';
                //$_POST['chat_message']  = 'MESSAGE HERE';
                //$_POST['chat_nickname'] = 'Nick Namerson';
                
                $code       = Post('chat_code');
                $message    = addslashes(htmlentities(str_replace('|', '-', Post('chat_message'))));
                $user       = addslashes(htmlentities(str_replace('|', '-', Post('chat_nickname'))));
                $time       = time();
                $addr       = $_SERVER['REMOTE_ADDR'];
                $chat       = "{$this->settings['chat_newline_char']}{$time}{$this->settings['chat_section_char']}{$user}{$this->settings['chat_section_char']}{$message}{$this->settings['chat_section_char']}{$addr}";
                
                $result = $this->SQL->AppendValue(array(
                    'table'       => $this->TableChats,
                    'key'         => 'chat',
                    'value'       => "'$chat'",
                    'where'       => "touchpoint_chats_code='$code'",
                ));
                
                //echo '<br />LAST QUERY ===> ' . $this->SQL->Db_Last_Query . '<br /><br />';
                
                if (!$result) {
                    $data[] = "ERROR::Unable to Update Database Record";
                } else {
                    $data[] = "success";
                }
                
                $json = new Services_JSON();
                $out = $json->encode($data);
                echo $out;
            break;
            case 'add_chat_notes':
                //$_POST['chat_code_notes']   = '3BXQ86';
                //$_POST['chat_notes']        = 'notes are here';
                
                $code       = Post('chat_code_notes');
                $notes      = addslashes(htmlentities(str_replace('|', '-', Post('chat_notes'))));
                
                $result = $this->SQL->UpdateRecord(array(
                    'table'       => $this->TableChats,
                    'key_values'  => "notes='$notes'",
                    'where'       => "touchpoint_chats_code='$code'",
                ));
                
                //echo '<br />LAST QUERY ===> ' . $this->SQL->Db_Last_Query . '<br /><br />';
                
                if (!$result) {
                    $data[] = "ERROR::Unable to Update Database Record";
                } else {
                    $data[] = "success";
                }
                
                $json = new Services_JSON();
                $out = $json->encode($data);
                echo $out;
            break;
            case 'end_chat_user':
                $code       = Get('code');
                $email      = Get('email');
                $message    = 'CHAT SESSION CLOSED BY USER';
                $user       = '';
                $time       = time();
                $addr       = $_SERVER['REMOTE_ADDR'];
                $chat       = "{$this->settings['chat_newline_char']}{$time}{$this->settings['chat_section_char']}{$user}{$this->settings['chat_section_char']}{$message}{$this->settings['chat_section_char']}{$addr}";
                
                $result_1 = $this->SQL->AppendValue(array(
                    'table'       => $this->TableChats,
                    'key'         => 'chat',
                    'value'       => "'$chat'",
                    'where'       => "touchpoint_chats_code='$code'",
                ));
                
                $result_2 = $this->SQL->UpdateRecord(array(
                    'table'       => $this->TableChats,
                    'key_values'  => "completed=1",
                    'where'       => "touchpoint_chats_code='$code'",
                ));
                
                if ($result_1 && $result_2) {
                    //echo "GOING TO REDIRECT";
                    header("Location: {$this->local_email_chat}?code={$code};DIALOGID={$this->DIALOGID};email={$email}");
                } else {
                    echo "<h1>UNABLE TO UPDATE TABLES</h1>";
                }
            break;
            case 'end_chat_admin':
                $code       = Get('code');
                $email      = Get('email');
                $message    = 'CHAT SESSION CLOSED BY ADMINISTRATOR';
                $user       = '';
                $time       = time();
                $addr       = $_SERVER['REMOTE_ADDR'];
                $chat       = "{$this->settings['chat_newline_char']}{$time}{$this->settings['chat_section_char']}{$user}{$this->settings['chat_section_char']}{$message}{$this->settings['chat_section_char']}{$addr}";
                
                $result_1 = $this->SQL->AppendValue(array(
                    'table'       => $this->TableChats,
                    'key'         => 'chat',
                    'value'       => "'$chat'",
                    'where'       => "touchpoint_chats_code='$code'",
                ));
                
                $result_2 = $this->SQL->UpdateRecord(array(
                    'table'       => $this->TableChats,
                    'key_values'  => "completed=1",
                    'where'       => "touchpoint_chats_code='$code'",
                ));
                
                if ($result_1 && $result_2) {
                    echo "<div style='background-color:#990000; color:#fff; text-align:center; border:1px solid #000;'>CHAT ENDED</div>";
                } else {
                    echo "<h1>UNABLE TO UPDATE TABLES</h1>";
                }
            break;
            default:
                echo "ACTION ===> $action";
            break;
        }
        exit;
    }


    

    
    
    public function InitializeChatWindowUser($code='')
    {
        if ($code) {
            $Ins = new General_ModuleInstructions;
            $Ins->AddInstructions('chat/chat_user', 'panel');
    
            
    
            # LOAD ACTUAL CHAT WINDOW
            $this->current_chat_code = $code;
            $this->CreateUserChat();
            $this->AddScriptChatWindow();
            $this->AddStyle();
        } else {
            $Ins = new General_ModuleInstructions;
            $Ins->AddInstructions('chat/chat_user', 'login');
            
            # LOAD USER LOGIN FOR A CHAT
            $this->CreateUserSetup();
            #$this->AddScript();
            $this->AddStyle();
        }
    }
    
    private function CreateUserChat()
    {
        # GET USER INFORMATION
        # ========================================================================
        $record = $this->SQL->GetRecord(array(
            'table' => $this->TableChats,
            'keys'  => "wh_id, user_name, user_email",
            'where' => "touchpoint_chats_code='$this->current_chat_code'",
        ));
        
        
        $class_version = '';
        if ($this->LoadClassVersion) {
            $class_version = "<div class='col' style='float:right; color:#fff; font-size:10px; font-weight:normal; font-style:italic;'>{$this->ClassInfo['Revision Title']} {$this->ClassInfo['Revision']}&nbsp;</div>";
        }
        
        
        $code = $this->current_chat_code;
        $content = <<<CONTENT
            <div style="background-color:#4F4F4F; padding:3px; border-bottom:0px solid #000;">
                <div class="col" style="color:#FE4902; font-size:16px">&diams;&nbsp;&nbsp;</div>
                <div class="col" style="color:#fff; font-size:14px; font-weight:bold; font-style:italic;">Chat ID: $code</div>
                {$class_version}
                <div class="clear"></div>
            </div>
            <div style="background-color:#fff; padding:5px;">
                <div id="daddy-shoutbox">
                    <div id="daddy-shoutbox-list"></div>
                    <br />
                    <form id="daddy-shoutbox-form" action="{$this->script_location}" method="post"> 
                        
                        Comment: <textarea id="chat_message" name="chat_message" cols="30" rows="3"></textarea>
                        
                        <div class="col" style="float:left;">
                            <button type="submit" class="positive" name="submit">
                                <img src="/office/images/buttons/save.png" alt=""/>
                                Submit
                            </button>
                        </div>
                        <div class="col" style="float:right;">
                            <button type="button" class="negative" name="complete" onclick="location.replace('{$this->location_end_chat_user}&code={$this->current_chat_code}&email={$record['user_email']}');">
                                <img src="/office/images/buttons/cancel.png" alt=""/>
                                Close Chat
                            </button>
                        </div>
                        <div class="clear"></div>
                        
                        <input type="hidden" name="chat_code" id="chat_code" value="$code" />            
                        <input type="hidden" id="chat_num_rows" value="0" />
                        <input type="hidden" name="chat_nickname" id="chat_nickname" value="{$record['user_name']}" />
                        <input type="hidden" name="chat_email" id="chat_email" value="{$record['user_email']}" />
                        
                    <span id="daddy-shoutbox-response"></span>
                    </form>
                </div>
            </div>
            <div id="ajax_status"></div>
CONTENT;
            echo $content;
    }
    
    private function CreateUserSetup()
    {
        # LOAD THE CHATS CLASS - AND SHOW FORM FOR USER CHAT CREATION
        $OBJ_CHAT = new Chat_TouchpointChats();
        $OBJ_CHAT->DIALOGID = $this->DIALOGID;
        $OBJ_CHAT->NewChatUser = true;
        $OBJ_CHAT->AddRecord();
    }
    
    
    
    public function InitializeChatWindowAdmin($code='')
    {
        if ($code) {
            # LOAD ACTUAL CHAT WINDOW
            $this->current_chat_code = $code;
            $this->CreateAdminChat();
            $this->AddScriptChatWindow();
            $this->AddStyle();
        }
    }

    private function CreateAdminChat()
    {
        $common_responses = '';
        $user_info = '';
        
        # GET COMMON RESPONSES
        # ========================================================================
        if ($this->LoadCommonResponses) {
            $OBJ_RESPONSE   = new Chat_TouchpointChatCommonResponses();
            $c_r_arr        = $OBJ_RESPONSE->GetCommonResponses();
            
            $cr_list = '';
            foreach ($c_r_arr AS $title => $value) {
                #$value      = AddSlashes($value);
                $onclick    = "CommonResponseLoad('$value');";
                $href       = "<a class='common_response' href='#' onclick=\"$onclick\">";
                $cr_list   .= "<li>{$href}{$title}</a></li>";
            }
            $common_responses = "            
                <div id='common_responses_title'>COMMON RESPONSES</div>
                <div id='common_responses_list'><ul>{$cr_list}</ul></div>";
            
        }
        
        
        # GET USER INFORMATION
        # ========================================================================
        if ($this->LoadUserInformation) {
            $record = $this->SQL->GetRecord(array(
                'table' => $this->TableChats,
                'keys'  => "{$this->TableChats}.wh_id, {$this->TableChats}.user_name, {$this->TableChats}.user_email, {$this->TableChats}.notes, {$this->TableContacts}.first_name, {$this->TableContacts}.last_name, {$this->TableContacts}.phone_home, {$this->TableContacts}.phone_work, {$this->TableContacts}.phone_cell, {$this->TableContacts}.email_address",
                'where' => "touchpoint_chats_code='$this->current_chat_code'",
                'joins' => "LEFT JOIN {$this->TableContacts} ON {$this->TableContacts}.wh_id = {$this->TableChats}.wh_id",
            ));
            
            $phn_hm = ($record['phone_home'] != '') ? "<br />Hm: {$record['phone_home']}" : '';
            $phn_wk = ($record['phone_work'] != '') ? "<br />Wk: {$record['phone_work']}" : '';
            $phn_cl = ($record['phone_cell'] != '') ? "<br />Cl: {$record['phone_cell']}" : '';
            $eml    = ($record['email_address'] != '') ? "<br />Email: {$record['email_address']}" : '';
            
            
            $action         = "open_contact_window";
            $extra_vars     = "wh_id={$record['wh_id']}";
            $onclick        = "AjaxCall('chat_admin.php', '$action', '$extra_vars');";
            $onclick        = "onclick=\"$onclick\" ";
            
            $user_info = "
                <br />
                <div class='user_info_header'>User Provided</div>
                <div>Name: {$record['user_name']}</div>
                <div>Email: {$record['user_email']}</div>
                <br />
                <div class='user_info_header'>Actual User Information</div>
                Name: {$record['first_name']} {$record['last_name']}
                {$eml}
                {$phn_hm}
                {$phn_wk}
                {$phn_cl}
                <br /><br />
                <button type='button' class='positive' name='submit' {$onclick}>
                    <img src='/office/images/buttons/textfield_key.png' alt=''/>
                    View User Information
                </button>
                <br /><br />
                ";
        }
        
        
        # FORM THE CHAT WINDOW
        # ========================================================================
        $code = $this->current_chat_code;
        
        
        $class_version = '';
        if ($this->LoadClassVersion) {
            $class_version = "<div class='col' style='float:right; color:#fff; font-size:10px; font-weight:normal; font-style:italic;'>{$this->ClassInfo['Revision Title']} {$this->ClassInfo['Revision']}&nbsp;</div>";
        }
        
        
        $content = <<<CONTENT
            <div style="background-color:#4F4F4F; padding:3px; border-bottom:0px solid #000;">
                <div class="col" style="color:#FE4902; font-size:16px">&diams;&nbsp;&nbsp;</div>
                <div class="col" style="color:#fff; font-size:14px; font-weight:bold; font-style:italic;">Chat ID: $code</div>
                {$class_version}
                <div class="clear"></div>
            </div>
            <div style="background-color:#fff; padding:5px;">
            
            
                <div style="width:810px;">
                <div class="col" style="width:630px;">
                
                    <div id="daddy-shoutbox">
                    <div id="daddy-shoutbox-list"></div>
                    <br />
                    <form id="daddy-shoutbox-form" action="{$this->script_location}" method="post"> 
                        Comment: <textarea id="chat_message" name="chat_message" cols="30" rows="3" onfocus="setbg('#e5fff3', 'chat_message');" onblur="setbg('white', 'chat_message');"></textarea>
                        
                        <div class="col" style="float:left;">
                            <button type="submit" class="positive" name="submit">
                                <img src="/office/images/buttons/save.png" alt=""/>
                                Send
                            </button>
                        </div>
                        <div class="col" style="float:right;">
                            <button type="button" class="negative" name="complete" onclick="AjaxCall('chat_admin.php', 'end_chat_admin', 'code={$this->current_chat_code}');">
                                <img src="/office/images/buttons/cancel.png" alt=""/>
                                Close Chat
                            </button>
                        </div>
                        <div class="clear"></div>
                        
                        <input type="hidden" id="chat_num_rows" value="0" />
                        <input type="hidden" name="chat_code" id="chat_code" value="$code" />
                        <input type="hidden" name="chat_nickname" id="chat_nickname" value="{$_SESSION['USER_LOGIN']['USER_NAME']}" />
                    
                    <span id="daddy-shoutbox-response"></span>
                    </form>
                    </div><br />
                    

                </div>
                <div class="col" style="width:170px;">
                
                    {$user_info}
                    <br />
                    <div>NOTES: (private)</div>
                    
                    <form id="chat-notes-form" action="{$this->script_location_notes}" method="post">
                        <textarea id="chat_notes" name="chat_notes" cols="5" rows="8" onfocus="setbg('#e5fff3', 'chat_notes');" onblur="setbg('white', 'chat_notes');">{$record['notes']}</textarea>
                        
                        <button type="submit" class="positive" name="submit">
                            <img src="/office/images/buttons/save.png" alt=""/>
                            Submit
                        </button>
                        
                        <input type="hidden" name="chat_code_notes" id="chat_code_notes" value="$code" />
                    </form>
                    
                </div>
                <div class="clear"></div>
                {$common_responses}
                </div>
                
                <div id="ajax_status"></div>
                
            </div>
CONTENT;
            echo $content;
    }
    
   
    
    private function AddStyle()
    {
        $style = "
        #dialogcontent {
            background: #eee;
        }
        
        .rotated {
            -webkit-transform: rotate(-90deg);
            -moz-transform: rotate(-90deg);
            filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=3);
        }
        
        #daddy-shoutbox {
          padding: 10px;
          background: #3E5468;
          color: white;
          width: 580px;
          font-family: Arial,Helvetica,sans-serif;
          font-size: 11px;
        }
        .shoutbox-list {
          border-bottom: 1px solid #627C98;
          
          padding: 5px;
          display: none;
        }
        #daddy-shoutbox-list {
          text-align: left;
          margin: 0px auto;
          height: 200px;
          overflow: auto;
          overflow: -moz-scrollbars-vertical;
            overflow-y: scroll;
            border:1px solid #fff;
        }
        #daddy-shoutbox-form {
          text-align: left;
          
        }
        .shoutbox-list-time {
          color: #8DA2B4;
          float:left;
          display:none;
        }
        .shoutbox-list-nick {
          margin-left: 5px;
          font-weight: bold;
          color:#dedede;
          float:left;
        }
        .shoutbox-list-message {
          margin-left: 5px;
          font-size:13px;
          float:left;
        }
        .clear {
            clear:both;
        }
        .col {
            float:left;
        }
        
        
        #common_responses_list {
          text-align: left;
          margin: 0px auto;
          height: 100px;
          overflow: auto;
          overflow: -moz-scrollbars-vertical;
            overflow-y: scroll;
            border:1px solid #fff;
            
            background: #3E5468;
            color: white;
            font-family: Arial,Helvetica,sans-serif;
            font-size: 11px;
        }
        .common_response {
            text-decoration: none;
            color: white;
            font-family: Arial,Helvetica,sans-serif;
            font-size: 11px;
        }
        #common_responses_title {
            color: #000;
            font-family: Arial,Helvetica,sans-serif;
            font-size: 13px;
        }
        
        
        .user_info_header {
            font-size:12px;
            border-bottom:1px solid #000;
        }
        textarea.chat_notes {
            font-size:8px;
        }
        #chat_message {
            border: 2px solid #cccccc;
            padding: 3px;
            font-family: Tahoma, sans-serif;
            font-size:12px;
            background-image: url(/office/images/textarea_bg.gif);
            background-position: bottom right;
            background-repeat: no-repeat;
        }
        #chat_notes {
            font-family: Tahoma, sans-serif;
            font-size:12px;
        }
        
        
        .chat_header {
            font-size:12px;
            font-weight:bold;
            border-bottom:1px solid #000;
        }
        .chat_request_holder {
            border:1px dashed #000;
            padding:5px;
        }
        .chat_request_category {
            background-color:#ccc;
            font-size: 10px;
        }
        .chat_request_user {
            background-color:#ccc;
            font-size: 10px;
        }
        .chat_request_question {
            font-size: 12px;
        }
        .chat_request_time {
            font-size: 10px;
        }
        .chat_request_chatid {
            font-size: 10px;
        }
        
        
        
        
        .tooltip {
            background-color:#000;
            border:1px solid #fff;
            padding:10px 15px;
            width:200px;
            display:none;
            color:#fff;
            text-align:left;
            font-size:12px;
            z-index:1000;

            /* outline radius for mozilla/firefox only */
            -moz-box-shadow:0 0 10px #000;
            -webkit-box-shadow:0 0 10px #000;
        }
        #tooltip {
            position:absolute;
            text-align:left;
            border:1px solid #333;
            background:#f7f5d1;
            padding:2px 5px;
            color:#333;
            display:none;
            z-index:100;
        }
        ";
        AddStyle($style);
    }

    private function AddScriptChatWindow()
    {
        # SCRIPT
        # ======================================================================
        AddScriptInclude("/jslib/jquery.form.js");

        $script = <<<SCRIPT

            var count = 0;
            var files = '/office/shoutbox/jquery-shoutbox/';
            var lastTime = 0;
            
            function prepare(response) {
                //alert('response');
                //alert(response.message);
                var d = new Date();
                count++;
                d.setTime(response.time*1000);
                var mytime = d.getHours()+':'+d.getMinutes()+':'+d.getSeconds();
                var string = '<div class="shoutbox-list" id="list-'+count+'">'
                    + '<div class="shoutbox-list-time">'+mytime+'</div>'
                    + '<div class="shoutbox-list-nick">'+response.nickname+':</div>'
                    + '<div class="shoutbox-list-message">'+response.message+'</div>'
                    + '<div class="clear"></div>'
                    +'</div>';
              
                return string;
            }
            
            function chat_success(response, status)  { 
                //alert('chat_success');
                //alert(response);
                //alert(status);
                if(status == 'success') {
                    lastTime = response.time;
                    $('#daddy-shoutbox-response').html('<img src="'+files+'images/accept.png" />');
                    //$('#daddy-shoutbox-list').append(prepare(response));
                    $('#chat_message').attr('value', '').focus();
                    //$('input[@name=message]').attr('value', '').focus();
                    //$('#list-' + count).fadeIn('slow');

                    // REFRESH THE SCREEN IMMEDIATELY
                    chat_refresh();
                
                    // SCROLL TO BOTTOM OF DIV
                    //$("#daddy-shoutbox-list").attr({ scrollTop: $("#daddy-shoutbox-list").attr("scrollHeight") });
                
                    timeoutID = setTimeout(chat_refresh, {$this->settings['user_chat_refresh']});
                } else {
                    alert(response);
                }
            }
            
            function chat_validate(formData, jqForm, chat_options) {
                //alert('chat_validate');
                //for (var i=0; i < formData.length; i++) { 
                //    if (!formData[i].value) {
                //        alert('Please fill in all the fields'); 
                //        $('input[@name='+formData[i].name+']').css('background', 'red');
                //        return false; 
                //    } 
                //} 
                
                $('#daddy-shoutbox-response').html('<img src="'+files+'images/loader.gif" />');
                clearTimeout(timeoutID);
            }

            function chat_refresh() {
                //alert('chat_refresh');
                var existing_rows = $('#chat_num_rows').val();
                $.getJSON("/office/AJAX/chat/chat_user.php?action=get_chat_content&time="+lastTime+"&code={$this->current_chat_code}&currentRows="+existing_rows, function(json) {
                    if(json) {
                        for(i=0; i < json.length; i++) {
                            $('#daddy-shoutbox-list').append(prepare(json[i]));
                            $('#list-' + count).fadeIn('slow');
                    
                            // SCROLL TO BOTTOM OF DIV
                            $("#daddy-shoutbox-list").attr({ scrollTop: $("#daddy-shoutbox-list").attr("scrollHeight") });
                        }
                        var j = i-1;
                        lastTime = json[j].time;
                        
                        // UPDATE NUMBER OF ROWS IN THIS CHAT BOX
                        // ==================================================================
                        var existing_rows = $('#chat_num_rows').val();
                        var new_rows = (Number(existing_rows) + Number(json.length));
                        $('#chat_num_rows').val(new_rows);
                    }
                });
                timeoutID = setTimeout(chat_refresh, {$this->settings['user_chat_refresh']});
            }
            
            function CommonResponseLoad (message) {
                $('#chat_message').append(stripslashes(message)).append(' ');
            }
            
            function setbg(color, elementId)
            {
                document.getElementById(elementId).style.background=color;
            }
        
            function AjaxCall(page, action, extra_vars) {
                var loadUrl         = "/office/AJAX/chat/" + page + "?action=" + action + "&" + extra_vars;
                var ajax_load       = "<img src='/images/loading.gif' alt='loading...' />";
                
                //alert(loadUrl);
                $("#ajax_status").html(ajax_load).load(loadUrl);
            }
            
SCRIPT;
        AddScript($script);


        $script = <<<SCRIPT
            $.ajaxSetup ({
                cache: false
            });
            
            var chat_options = { 
                dataType:       'json',
                beforeSubmit:   chat_validate,
                success:        chat_success
            }; 
            $('#daddy-shoutbox-form').ajaxForm(chat_options);
            $('#chat-notes-form').ajaxForm(chat_options);
            
            timeoutID = setTimeout(chat_refresh, 50); //immediately call for chat refresh
SCRIPT;
        addScriptOnReady($script);
        
    }
    
    private function echoScript($script)
    {
        if ($script) {
            echo "<script language='text/javascript'>$script</script>";
        }
    }
    
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
    
}  // -------------- END CLASS --------------