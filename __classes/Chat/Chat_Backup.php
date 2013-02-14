<?php
class Chat_Chat //extends BaseClass
{
    public $DIALOGID                = 0;
    
    //private $script_location        = "./shoutbox/jquery-shoutbox/daddy-shoutbox.php?action=add";
    private $script_location        = "/AJAX/chat_user.php?action=add_chat_content";
    private $pending_chat_count     = 0;
    private $active_chat_count      = 0;
    private $trunc_length           = 40;
    public $SQL                     = '';
    private $TableChats             = 'touchpoint_chats';
    private $TableChatSettings      = 'touchpoint_chat_settings';
    private $settings               = array();
    private $LockRecordOnAdminOpen  = false;
    private $current_chat_id        = 0;
    private $current_chat_code      = '';
    
    private $chat_newline_char      = '[~]';
    private $chat_section_char      = '[|]';
    
    public function  __construct()
    {
        $this->SQL = Lib_Singleton::GetInstance('Lib_Pdo');
        $this->GetSettings();
        
        
        $this->ClassInfo = array(
            'Created By'    => 'Richard WItherspoon',
            'Description'   => 'Actual chat windows',
            'Created'       => '2010-09-15',
            'Updated'       => '2010-10-09',
            'Revision'          => '1.00.04',
            'Revision Title'    => 'ALPHA'
        );
        
        //parent::__construct();
/*
        $this->ClassInfo = array(
            'Created By'  => 'MVP',
            'Description' => 'Create and manage contacts',
            'Created'     => '2009-10-23',
            'Updated'     => '2009-10-23'
        );

        //-------------- SET PARAMETERS -------------
        $this->SetParameters(func_get_args());
        $this->Companies_Id = $this->GetParameter(0);

        if ($this->Companies_Id) {
            $this->Default_Values['companies_id'] = $this->Companies_Id;
        }

        // if (function_exists('addmessage')) {
            // AddMessage('Companies Id=' . $this->Companies_Id);
        // }

        if ($this->Companies_Id) {
            $this->AddDefaultWhere("`contacts`.`companies_id`=$this->Companies_Id");
        }

        $this->Table  = 'contacts';
        $this->Add_Submit_Name  = 'CONTACTS_SUBMIT_ADD';
        $this->Edit_Submit_Name = 'CONTACTS_SUBMIT_EDIT';
        $this->Index_Name = 'contacts_id';
        $this->Flash_Field = 'contacts_id';
        $this->Default_Sort  = '';
        $this->Field_Titles = array();
        $this->Join_Array = array();
        $this->Default_Fields = '';
        $this->Unique_Fields = '';
*/

    } // -------------- END __construct --------------

    private function echoScript($script)
    {
        if ($script) {
            echo "<script language='text/javascript'>$script</script>";
        }
    }
    
    public function ProcessAjax()
    {
        $this->SQL = Lib_Singleton::GetInstance('Lib_Pdo');
        
        $action = Get('action');
        switch ($action) {
            case 'change_my_status':
                $new_status = Get('status');
                echo "<br /><br />Passed in status ===> $new_status";
            break;
            case 'open_existing_chat':
                $code = Get('chat_code');
                echo "<br /><br />Passed in code ===> $code";
                $script = "top.parent.appformCreate('Launch', '/office/chat/chat_user;code=$code', 'apps');";
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
                    $script = "top.parent.appformCreate('Launch', '/office/chat/chat_user;code=$code', 'apps');";
                    $this->echoScript($script);                
                }
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
                    'table' => 'touchpoint_chats',
                    'keys'  => 'chat,line_count',
                    'where' => "touchpoint_chats_code='$code'",
                ));
                
                $lines = explode($this->chat_newline_char, trim($record['chat']));
                $total_rows = count($lines)-1;
                if ($total_rows > $curr_rows) {
                    # THERE ARE NEW ROWS OF INFO NOT CURRENTLY DISPLAYED
                    $temp_count = 1;
                    foreach ($lines as $line)
                    {
                        if (trim($line)) { # if its not a blank line
                            if ($temp_count > $curr_rows) {
                                $aTemp = null;
                                list($aTemp['time'], $aTemp['nickname'], $aTemp['message']) = explode($this->chat_section_char, $line);
                                //$aTemp['nickname'] .= " -- $total_rows ===> $curr_rows";
                                if ($aTemp['message']) $data[] = $aTemp;
                            }
                            $temp_count++;
                        }
                    }
                    
                    # OUTPUT THE JSON DATA
                    $json = new Services_JSON();
                    $out = $json->encode($data);
                    print $out;
                }
            break;
            case 'add_chat_content':
//echo "<br />ADDING CHAT CONTENT.";
//$_POST['chat_code'] = "HS8WDK";
//$_POST['chat_message'] = "Test message goes here";
//$_POST['chat_nickname'] = "Testing System";

                $code       = Post('chat_code');
                $message    = addslashes(htmlentities(str_replace('|', '-', Post('chat_message'))));
                $user       = addslashes(htmlentities(str_replace('|', '-', Post('chat_nickname'))));
                $time       = time();
                $addr       = $_SERVER['REMOTE_ADDR'];
      
      
      # UPDATE THE RECORD
      #update photo_img set dtl=concat(dtl,'site_data to add') where gal_id='22' 
      
      //echo "<br />MESSAGE ===> $message";
      
      
      //$data['response'] = 'Good work';
      //$data['nickname'] = $_POST['nickname'];
      //$data['message'] = $_POST['message'];
      //$data['time'] = $time;

                $chat       = "{$this->chat_newline_char}$time{$this->chat_section_char}$user{$this->chat_section_char}$message{$this->chat_section_char}$addr";

//echo "chat ===> $chat";

                
                $result = $this->SQL->UpdateConcatRecord(array(
                    'table'       => 'touchpoint_chats',
                    'key'         => 'chat',
                    'value'       => "'$chat'",
                    'where'       => "touchpoint_chats_code='$code'",
                ));
                
                if (!$result) {
                    echo "ERROR::Unable to Update Database Record";
                } else {
                    # have to pass back this message for ajax call to finish itself up correctly.
                    echo "success";
                }
            break;
        }
        exit;
    }


    
    public function GetSettings()
    {
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
    }
    
    
    public function InitializeChatWindowUser($code='')
    {
        if ($code) {
            # LOAD ACTUAL CHAT WINDOW
            $this->current_chat_code = $code;
            $this->CreateUserChat();
            $this->AddScriptChatWindow();
            $this->AddStyle();
        } else {
            # LOAD USER LOGIN FOR A CHAT
            $this->CreateUserSetup();
            $this->AddScript();
            $this->AddStyle();
        }
    }
    
    private function CreateUserChat()
    {
        $code = $this->current_chat_code;
        $content = <<<CONTENT
            <center>
            <div id="daddy-shoutbox">
            <div id="daddy-shoutbox-list"></div>
            <br />
            <form id="daddy-shoutbox-form" action="{$this->script_location}" method="post"> 
                Name: {$_SESSION['USER_LOGIN']['USER_NAME']}<input type="text" name="chat_nickname" id="chat_nickname" value="{$_SESSION['USER_LOGIN']['USER_NAME']}" />
                <br /><br />
                Comment: <textarea id="chat_message" name="chat_message" cols="30" rows="3"></textarea>
            <input type="submit" value="Submit" />

            <div>Chat Code: <input type="textbox" name="chat_code" id="chat_code" value="$code" /></div>
            <span id="daddy-shoutbox-response"></span>
            </form>
            </div><br />
            <div># OF ROWS: <input type="textbox" id="chat_num_rows" value="0" /></div>

            </center>
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
    
    
    
    public function InitializeChatWindowAdmin()
    {
                    $address = $this->SQL->GetRecord(array(
                        'table' => 'companies',
                        'keys'  => 'address_1,address_2,city,state,country_code,postal_code,phone_number,fax_number',
                        'where' => "companies_id=$companies_id",
                    ));
    }

    
    private function GetChatRequests($type='all')
    {
        # GET ALL CHAT REQUESTS - BASED ON TYPES REQUESTED. TYPES COULD BE AN ARRAY
        # =============================================================================
        #$type = 'ALL|BILLING|TECHNICAL|GENERAL'
     



        $this->pending_chat_count     = 5;
        $this->active_chat_count      = 12;

        
        $records = $this->SQL->GetArrayAll(array(
            'table' => $this->TableChats,
            'keys'  => '*',
            'where' => 'active=1',
        ));
        
        $chat_boxes = '';
        foreach ($records as $record) 
        {
            # CALCULATE TIME SINCE THIS WAS CREATED - UPDATING TIME HAPPENS WITH SEPERATE AJAX CALLS
            # ========================================================================================     
            #'chat_start_timestamp' => 'Chat Start Timestamp',
        
            $admin_id = 666;
        
            $username           = $record['user_name'];
            $question_long      = $record['chat'];
            $question_short     = TruncStr($question_long, $this->trunc_length);
            $type               = strtoupper($record['category']);
            $code               = $record['touchpoint_chats_code'];
            $id                 = $record['touchpoint_chats_id'];
            $time               = "30 seconds";
            $action             = "open_chat_request";
            $extra_vars         = "chat_code=$code;chat_id=$id;admin_id=$admin_id";
            $onclick            = "AjaxCall('$action', '$extra_vars');";
            $chat_box           = "
                <div class='chat_request_holder'>
                <a href='#' onclick=\"$onclick\" title='$question_long'>
                    <div style='float:left;'>
                        <div class='chat_request_category'>$type</div>
                    </div>
                    <div style='float:right;'>
                        <div class='chat_request_time'>$time</div>
                        <div class='chat_request_chatid'>$code</div>
                    </div>
                    <div style='clear:both;'></div>
                    
                    <div class='chat_request_question'>$question_short</div>
                    <div class='chat_request_user'>$username</div>
                </a>
                </div>
                
            ";
            
            $chat_boxes .= "<br />$chat_box";
        }
        
        return $chat_boxes;
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
    
    
    
        $chat_boxes             = $this->GetChatRequests('all');
        $content = <<<CONTENT
        
        <div style="border:1px solid blue; padding:5px;">
        
            <div class="chat_header">CHAT REQUESTS</div>
            
            <div id="chat_area_requests">
                $chat_boxes
            </div>
            <br />
            <div>
                <div style="float:left;">Pending Chats</div><div style="float:right;">$this->pending_chat_count</div>
                <div style="clear:both;"></div>
                <div style="float:left;">Active Chats</div><div style="float:right;">$this->active_chat_count</div>
                <div style="clear:both;"></div>
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
    
    
    
    private function AddStyle()
    {
        $style = "
        #daddy-shoutbox {
          padding: 10px;
          background: #3E5468;
          color: white;
          width: 600px;
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
                
                    timeoutID = setTimeout(chat_refresh, 3000);
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
                //$.getJSON("/AJAX/chat_user.php?action=get_chat_content&time="+lastTime+"&code=HS8WDK&currentRows="+existing_rows, function(json) {
                //$.getJSON("http://yoga.whhub.com/office/AJAX/chat/chat_user.php?action=get_chat_content&time="+lastTime+"&code=HS8WDK&currentRows="+existing_rows, function(json) {
                $.getJSON("/office/AJAX/chat/chat_user.php?action=get_chat_content&time="+lastTime+"&code={$this->current_chat_code}&currentRows="+existing_rows, function(json) {
                    if(json.length) {
                        for(i=0; i < json.length; i++) {
                            $('#daddy-shoutbox-list').append(prepare(json[i]));
                            $('#list-' + count).fadeIn('slow');
                    
                            // SCROLL TO BOTTOM OF DIV
                            $("#daddy-shoutbox-list").attr({ scrollTop: $("#daddy-shoutbox-list").attr("scrollHeight") });
                        }
                        var j = i-1;
                        lastTime = json[j].time;
                    }
                
                    // UPDATE NUMBER OF ROWS IN THIS CHAT BOX
                    // ==================================================================
                    var existing_rows = $('#chat_num_rows').val();
                    var new_rows = (Number(existing_rows) + Number(json.length));
                    $('#chat_num_rows').val(new_rows);
                });
                timeoutID = setTimeout(chat_refresh, 3000);
            }
SCRIPT;
        AddScript($script);


        $script = <<<SCRIPT

        //alert('code loaded');
        
            $.ajaxSetup ({
                cache: false
            });
            
            var chat_options = { 
                dataType:       'json',
                beforeSubmit:   chat_validate,
                success:        chat_success
            }; 
            $('#daddy-shoutbox-form').ajaxForm(chat_options);
            timeoutID = setTimeout(chat_refresh, 50); //immediately call for chat refresh
            //alert('code loaded');
            //alert(timeoutID);
SCRIPT;
        addScriptOnReady($script);
        
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
        
SCRIPT;
        addScriptOnReady($SCRIPT);

        //AddScriptInclude('/jslib/ui/jquery-ui-1.7.2.custom.js');
        AddScriptInclude('/jslib/jquery.tools.min.js');

        $dialog_id = 0;
        
        
        
        $script = <<<SCRIPT
        
        function AjaxCall(action, extra_vars) {
            //var loadUrl         = "http://yoga.whhub.com/office/AJAX/chat/chat_panel.php?PARENT_DIALOGID=" + {$dialog_id} + "&action=" + action + "&" + extra_vars;
            var loadUrl         = "/office/AJAX/chat/chat_panel.php?PARENT_DIALOGID=" + {$dialog_id} + "&action=" + action + "&" + extra_vars;
            var ajax_load       = "<img src='/images/loading.gif' alt='loading...' />";
            
            //alert(loadUrl);
            $("#ajax_status").html(ajax_load).load(loadUrl);
        }
        
        
        function GetReservationsAjaxSPECIAL(extra_var) {
            //alert('GetReservationsAjaxSPECIAL');
            var res_code        = $('#cal_rescode').val();
            var location_id     = $('#cal_location').val();
            var sort_order      = $('#cal_boatsort').val();
            var date            = $('#cal_date').val();
            var loadUrl         = "helper/sailing_reservations_helper_dev.php?PARENT_DIALOGID=" + {$dialog_id} + "&location_id=" + location_id + "&date=" + date + "&sort=" + sort_order + "&res_code=" + res_code + "&" + extra_var;
            var ajax_load       = "<img src='/office/images/upload.gif' alt='loading...' />";
            
            $("#result").html(ajax_load).load(loadUrl);
            $('#cal_rescode').val('');
        }

SCRIPT;
        addScript($script);

        
        
        
    
    }
    
    
}  // -------------- END CLASS --------------















# NOTES
# ======================================================================
/*
#
http://www.ajaxdaddy.com/demo-jquery-shoutbox.html

This is some great code, has a huge memory leak issue. Since they are adding a datetime number as they pull the chat.txt every 8th of a second, it caches that page everytime in your end users temporary internet cache. The only solution is to make a seperate .php page that pulls the .txt file, and set the .php file to expire several days in the past, and remove the datetime variable being added at the end of .txt.

You can fix the utf-8 problem by replacing line 39 in daddy-shoutbox.php with this.: $data['message'] = htmlentities(stripslashes($_POST['message']), ENT_QUOTES, "UTF-8" );

If you're having issues with this script not refreshing in IE, the following code might help: function refresh() { var stamp = new Date(); stamp = stamp.getTime(); $.getJSON(files+"daddy-shoutbox.php?action=view&time=" + lastTime + "&stamp=" + stamp
#
*/
# ======================================================================
# ======================================================================
# ======================================================================

