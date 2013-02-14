<?php
class Chat_TouchpointChatAdminPanel //extends BaseClass
{
    public $DIALOGID                = 0;
    
    public $link_chat_window        = "/office/chat/chat_admin";
    
    public $SQL                     = '';
    private $TableChats             = 'touchpoint_chats';
    private $TableChatSettings      = 'touchpoint_chat_settings';
    private $LockRecordOnAdminOpen  = false;
    
    public $reset_settings          = false;
    private $settings               = array();
    
    public function  __construct()
    {
        $this->SQL = Lib_Singleton::GetInstance('Lib_Pdo');
        $this->GetSettings();
    
        $this->ClassInfo = array(
            'Created By'    => 'Richard Witherspoon',
            'Description'   => 'Chat Panel - for administrators',
            'Created'       => '2010-09-15',
            'Updated'       => '2010-10-09',
            'Revision'          => '1.00.04',
            'Revision Title'    => 'ALPHA'
        );
        
    } // -------------- END __construct --------------

    
    public function ProcessAjax()
    {
        $this->SQL = Lib_Singleton::GetInstance('Lib_Pdo');
        
        $action = Get('action');
        switch ($action) {
            case 'get_pending_chats':
                $type = Get('chatType');
                $chats = $this->GetChatRequests($type);
                echo $chats;
                //echo "<br /><br />Passed in status ===> $new_status";
            break;
            case 'change_my_status':
                $new_status = Get('status');
                echo "<br /><br />Passed in status ===> $new_status";
            break;
            case 'open_existing_chat':
                $code = Get('chat_code');
                echo "<br /><br />Passed in code ===> $code";
                $script = "top.parent.appformCreate('Existing Chat: {$code}', '{$this->link_chat_window};code=$code', 'apps');";
                $this->echoScript($script);
            break;
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
                    $script = "top.parent.appformCreate('New Chat: {$code}', '{$this->link_chat_window};code=$code', 'apps');";
                    $this->echoScript($script);                
                }
            break;
        }
        exit;
    }



    


    public function InitializeChatPanel()
    {
        $this->AddStyle();
        $this->AddScript();
        
        $cur_status         = "online";
        $selected_online    = ($cur_status == 'online') ? 'selected' : '';
        $selected_offline   = ($cur_status == 'offline') ? 'selected' : '';
        
        
        
        $status_select = "
        <select id='chat_my_status'>
            <option value=''>-- SELECT --</option>
            <option value='online' $selected_online>Online</option>
            <option value='offline' $selected_offline>Offline</option>
        </select>";
    
    
    
        //$chat_boxes = $this->GetChatRequests('all');
        $content = <<<CONTENT
        
        <div style="border:1px solid blue; padding:5px; height:600px;">
        
            <div class="chat_header">CHAT REQUESTS</div>
            
            <div id="chat_area_requests">
                <div style="padding:40px; text-align:center;">
                <img src="/office/images/loading_small.gif" alt="Loading..." border="0" width="96" />
                </div>
            </div>
            <br /><br />
            <div class="chat_header">CHAT SETTINGS</div>
            <div>Open Chat <input type='texbox' id='chat_open_code' size='20'> <input type='button' id='chat_open_button' value='GO'></div>
            <div>My Status $status_select <input type='button' id='my_status_button' value='GO'></div>
            <br />
            <div>My Chat Categories</div>
            <div>[] checkbox</div>
            <div>[] checkbox</div>
            <div>[] checkbox</div>
        
            <br /><br />
            <div id="ajax_status"></div>
        
        
        </div>
        
CONTENT;
        echo $content;
    }
       
    private function GetChatRequests($type='all')
    {
        # GET ALL CHAT REQUESTS - BASED ON TYPES REQUESTED. TYPES COULD BE AN ARRAY
        # =============================================================================
        #$type = 'ALL|BILLING|TECHNICAL|GENERAL'
     


       
        $records = $this->SQL->GetArrayAll(array(
            'table' => $this->TableChats,
            'keys'  => 'locked, completed, chat, category, touchpoint_chats_code, touchpoint_chats_id, chat_start_timestamp',
            'where' => 'completed=0 AND active=1',
        ));
        
        $chat_boxes = '';
        $pending_chat_count = 0;
        $active_chat_count = 0;
        foreach ($records as $record) 
        {
            if ($record['locked'] == 0) $pending_chat_count++;
            if ($record['locked'] == 1 && $record['completed'] == 0) $active_chat_count++;
        
            if ($record['locked'] == 0) {
                # CALCULATE TIME SINCE THIS WAS CREATED - UPDATING TIME HAPPENS WITH SEPERATE AJAX CALLS
                # ========================================================================================
                $time_start     = $record['chat_start_timestamp'];
                $time_now       = date("Y-m-d  H:i:s");
                $time           = $this->dateDiff ($time_start, $time_now, 3);
            
                $admin_id = 666;
            
                
                
                # explode and get the first line &  strip out the info you need
                # ========================================================================================
                $lines              = explode($this->settings['chat_newline_char'], trim($record['chat']));
                $line               = $lines[1];
                $line_parts         = explode($this->settings['chat_section_char'], $line);
                $question_long      = $line_parts[2];
                
                $question_short     = TruncStr($question_long, $this->settings['chat_admin_panel_trunc_length']);
                //$username           = "{$record['user_name']} ({$record['wh_id']})";
                $type               = strtoupper($record['category']);
                $code               = $record['touchpoint_chats_code'];
                $id                 = $record['touchpoint_chats_id'];
                $action             = "open_chat_request";
                $extra_vars         = "chat_code=$code;chat_id=$id;admin_id=$admin_id";
                $onclick            = "AjaxCall('$action', '$extra_vars');";
                $chat_box           = "
                    <div class='chat_request_holder'>
                    
                        
                        <div class='chat_background_area'>
                            <div style='float:left;'>
                                <div class='chat_request_category'>$type</div>
                                <div class='chat_request_chatid'><a href='#' onclick=\"$onclick\" title='$question_long'>$code</a></div>
                            </div>
                            <div style='float:right;'>
                                <div class='chat_request_time'>$time</div>
                            </div>
                            <div style='clear:both;'></div>
                        </div>
                    <a href='#' onclick=\"$onclick\" title='$question_long'>
                        <div class='chat_request_question'>$question_short</div>
                    </a>
                    </div>
                    
                ";
                
                $chat_boxes .= "<br />$chat_box";
            }
        }
                
        #$this->pending_chat_count = $pending_chat_count;
        #$this->active_chat_count = $active_chat_count;
        
        $chat_boxes .= "
            <br />
            <div>
                <div style='float:left;'>Pending Chats</div><div style='float:right;'>$pending_chat_count</div>
                <div style='clear:both;'></div>
                <div style='float:left;'>Active Chats</div><div style='float:right;'>$active_chat_count</div>
                <div style='clear:both;'></div>
            </div>";
            
        return $chat_boxes;
    }
    
    
    
    
    private function AddStyle()
    {
        $style = "
        .clear {
            clear:both;
        }
        
        
        
        .chat_background_area {
            background-color:#ccc;
            font-size: 10px;
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
            /*-webkit-transform: rotate(-90deg);*/
            /*-moz-transform: rotate(-90deg);*/
            /*filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=3);*/
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
    
    private function AddScript()
    {
        $SCRIPT = <<<SCRIPT
        
        $.ajaxSetup ({
            cache: false
        });  
        
        
        $("#chat_open_button").click(function(){	
            //alert('chat_open_button GO CLICKED');
            
            var code = $('#chat_open_code').val();
            
            var action      = 'open_existing_chat';
            var extra_vars  = 'chat_code='+code;
            AjaxCall(action, extra_vars);
            
            $('#chat_open_code').val('');
        });
        
        $("#chat_my_status").change(function(){	
            //alert('chat_my_status CHANGED');
            
            var statusVal = $('#chat_my_status').val();
            
            switch(statusVal) {
                case 'online':
                    var action      = 'change_my_status';
                    var extra_vars  = 'status=online';
                    AjaxCall(action, extra_vars);
                break;
                case 'offline':
                    var action      = 'change_my_status';
                    var extra_vars  = 'status=offline';
                    AjaxCall(action, extra_vars);
                break;
            }
            
        });
        
        
        $("a").tooltip({
            // place tooltip on the right edge
            //position: "center right",
            // a little tweaking of the position
            offset: [10, 0],
            // use the built-in fadeIn/fadeOut effect
            //effect: "fade",
            // custom opacity setting
            opacity: 0.9
        }); 
        
        //tooltip();
        
        timeoutID = setTimeout(chat_refresh, 50); //immediately call for chat refresh
SCRIPT;
        addScriptOnReady($SCRIPT);

        //AddScriptInclude('/jslib/jquery.tools.min.js');

        $dialog_id = 0;
        
        $script = <<<SCRIPT
        
        function AjaxCall(action, extra_vars) {
            var loadUrl         = "/office/AJAX/chat/chat_panel.php?PARENT_DIALOGID=" + {$dialog_id} + "&action=" + action + "&" + extra_vars;
            var ajax_load       = "<img src='/images/loading.gif' alt='loading...' />";
            
            //alert(loadUrl);
            $("#ajax_status").html(ajax_load).load(loadUrl);
        }
        
        function prepare(data) {
            return data;
        }
        
        var chatType = 'all';
        function chat_refresh() {
            $.get("/office/AJAX/chat/chat_panel.php?action=get_pending_chats&chatType="+chatType, function(data) {
                if(data.length) {
                    $('#chat_area_requests').html(prepare(data));
                }
            });
            timeoutID = setTimeout(chat_refresh, {$this->settings['chat_admin_panel_refresh_timeout']});
        }
SCRIPT;
        addScript($script);
        
    
    }
    
    
    
    private function dateDiff($time1, $time2, $precision = 6) 
    {
        // Time format is UNIX timestamp or
        // PHP strtotime compatible strings
        
        // If not numeric then convert texts to unix timestamps
        if (!is_int($time1)) $time1 = strtotime($time1);
        if (!is_int($time2)) $time2 = strtotime($time2);
        
        // If time1 is bigger than time2
        // Then swap time1 and time2
        if ($time1 > $time2) {
            $ttime = $time1;
            $time1 = $time2;
            $time2 = $ttime;
        }
        
        // Set up intervals and diffs arrays
        //$intervals = array('yr','mo','day','hr','min','sec');
        $intervals = array('year','month','day','hour','minute','second');
        $diffs = array();
        
        // Loop thru all intervals
        foreach ($intervals as $interval) {
            $diffs[$interval] = 0;                              // Set default diff to 0          
            $ttime = strtotime("+1 " . $interval, $time1);      // Create temp time from time1 and interval
            
            while ($time2 >= $ttime) {
                // Loop until temp time is smaller than time2
                $time1 = $ttime;
                $diffs[$interval]++;
                $ttime = strtotime("+1 " . $interval, $time1);  // Create new temp time from time1 and interval
            }
        }
        
        $count = 0;
        $times = array();
        // Loop thru all diffs
        foreach ($diffs as $interval => $value) {
            // Break if we have needed precission
            if ($count >= $precision) break;
            
            // Add value and interval if value is bigger than 0
            if ($value > 0) {
                // Add s if value is not 1
                if ($value != 1) {
                    $interval .= "s";
                }
                
                // Add value and interval to times array
                $times[] = $value . " " . $interval;
                //$times[] = $value;
                $count++;
            }
        }
        
        // Return string with times
        $difference = implode(", ", $times);
        $search     = array('year','month','day','hour','minute','second');
        $replace    = array('yr','mo','day','hr','min','sec');
        $difference = str_replace($search, $replace, $difference);
        
        return $difference;
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
