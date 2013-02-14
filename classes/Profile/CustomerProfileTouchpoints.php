<?php
class Profile_CustomerProfileTouchpoints extends BaseClass
{
    public $Show_Query                      = false;    // TRUE = output the database queries ocurring on this page
    
    public $get_tp_forms                    = false;    // TRUE = get contact forms filled out
    public $get_tp_emails                   = true;     // TRUE = get emails send to user
    public $tab_over_pixels                 = 20;       //how many pixels to the right should each sub-T/P be shifted over
    
    public $ico_phone                       = "/office/images/touchpoint/ico_phone.jpg";
    public $ico_email                       = "/office/images/touchpoint/ico_email.jpg";
    public $ico_chat                        = "/office/images/touchpoint/ico_chat.jpg";
    public $ico_clock                       = "/office/images/touchpoint/ico_clock.jpg";
    public $ico_unknown                     = "/office/images/touchpoint/ico_unknown.png";
    
    public $type_chat                       = 'chat';
    public $type_call                       = 'call';
    public $type_form                       = 'form';
    public $type_email                      = 'email';
    
    public $link_chat_window                = "/office/chat/chat_admin";
    public $table_chats                     = 'touchpoint_chats';
    public $table_calls                     = 'touchpoint_calls';
    public $table_forms                     = 'touchpoint_forms';
    public $table_emails                    = 'email_sent_logs';
    public $TableChatSettings               = 'touchpoint_chat_settings';
    
    // ---------- NON-MODIFIABLE VARIABLES ----------
    public $WH_ID                           = 0;
    public $sub_touchpoint_list             = array();
    public $touchpoints                     = array();
    public $touchpoints_sub                 = array();
    public $output_global                   = '';
    public $OBJ_MAIL                        = null;
    public $settings_chat                   = array();
    public $reset_settings_chat             = false;
    
    
    
    public function  __construct()
    {
        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Description' => 'Get all contact interactions with a customer',
        );
        
        $this->SetSQL();
        
        global $ROOT;
        require_once "$ROOT/phplib/swift4/swift_required.php";
        $this->OBJ_MAIL = new Email_MailWh;
    }
    
    public function SetSQL()
    {
        if (empty($this->SQL)) {
            $this->SQL = Lib_Singleton::GetInstance('Lib_Pdo');
        }
    }
    
    
    public function GetSettingsChat()
    {
        if (Session('settings_touchpoint_chat') && (!$this->reset_settings_chat)) {
            $this->settings_chat = Session('settings_touchpoint_chat');
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
                $this->settings_chat[$name] = $value;
            }
            
            $_SESSION['settings_touchpoint_chat'] = $this->settings_chat;
        }
    }
    
    public function AddScript() 
    {
        $script = "
            function LaunchChat(code) {
                top.parent.appformCreate('Existing Chat: '+code, '{$this->link_chat_window};code='+code, 'apps');
            }
            
            function LaunchCall(code) {
                alert('code to launch call would go here');
                //top.parent.appformCreate('Existing Call: '+code, '{$this->link_chat_window};code='+code, 'apps');
            }
            
            function LaunchEmail(code) {
                var link = '/office/dev_richard/class_execute;class=Email_MailWh';
                top.parent.appformCreate('Email Preview: '+code, link+';classVars='+code+';action=preview', 'apps');
            }
            
            function LaunchForm(code) {
                alert('code to launch form would go here');
                //top.parent.appformCreate('Existing Call: '+code, '{$this->link_chat_window};code='+code, 'apps');
            }
            
            
            
            function ShowAll() {
                $('#touchpoints_mnu_calls').attr('status', 'hidden').html('SHOW Phone Calls');
                $('#touchpoints_mnu_forms').attr('status', 'hidden').html('SHOW Contact Forms');
                $('#touchpoints_mnu_chats').attr('status', 'hidden').html('SHOW Chats');
                $('#touchpoints_mnu_emails').attr('status', 'hidden').html('SHOW Emails');
                
                ShowHideCalls();
                ShowHideForms();
                ShowHideChats();
                ShowHideEmails();
            }
            
            function ShowHideCalls() {
                var status = $('#touchpoints_mnu_calls').attr('status');
                
                if (status == 'showing') {
                    // want to hide
                    $('#touchpoints_mnu_calls').attr('status', 'hidden').html('SHOW Phone Calls');
                    
                    $('.touchpoint_phone').each(function(index) {
                        $(this).css('display', 'none');
                    });
                } else {
                    // want to show
                    $('#touchpoints_mnu_calls').attr('status', 'showing').html('HIDE Phone Calls');
                    
                    $('.touchpoint_phone').each(function(index) {
                        $(this).css('display', '');
                    });
                }
            }
            
            function ShowHideForms() {
                var status = $('#touchpoints_mnu_forms').attr('status');
                
                if (status == 'showing') {
                    // want to hide
                    $('#touchpoints_mnu_forms').attr('status', 'hidden').html('SHOW Contact Forms');
                    
                    $('.touchpoint_email').each(function(index) {
                        $(this).css('display', 'none');
                    });
                } else {
                    // want to show
                    $('#touchpoints_mnu_forms').attr('status', 'showing').html('HIDE Contact Forms');
                    
                    $('.touchpoint_email').each(function(index) {
                        $(this).css('display', '');
                    });
                }
            }
            
            function ShowHideChats() {
                var status = $('#touchpoints_mnu_chats').attr('status');
                
                if (status == 'showing') {
                    // want to hide
                    $('#touchpoints_mnu_chats').attr('status', 'hidden').html('SHOW Chats');
                    
                    $('.touchpoint_chat').each(function(index) {
                        $(this).css('display', 'none');
                    });
                } else {
                    // want to show
                    $('#touchpoints_mnu_chats').attr('status', 'showing').html('HIDE Chats');
                    
                    $('.touchpoint_chat').each(function(index) {
                        $(this).css('display', '');
                    });
                }
            }
            
            function ShowHideEmails() {
                var status = $('#touchpoints_mnu_emails').attr('status');
                
                if (status == 'showing') {
                    // want to hide
                    $('#touchpoints_mnu_emails').attr('status', 'hidden').html('SHOW Emails');
                    
                    $('.touchpoint_email').each(function(index) {
                        $(this).css('display', 'none');
                    });
                } else {
                    // want to show
                    $('#touchpoints_mnu_emails').attr('status', 'showing').html('HIDE Emails');
                    
                    $('.touchpoint_email').each(function(index) {
                        $(this).css('display', '');
                    });
                }
            }
            
            function InitializeOnReady() {
                $('.touchpoint_data_popup a').hover(function() {
                    $(this).next('em').stop(true, true).animate({opacity: 'show', top: '-5'}, 'slow');
                }, function() {
                    $(this).next('em').animate({opacity: 'hide', top: '-70'}, 'fast');
                });
            }

            
            ";
        AddScript($script);
    }
    
    public function GetAllTouchpoints()
    {
        //$this->WH_ID = 1000666;
        
        $touchpoints = array();
        $touchpoints_sub = array();
        
        
        
        # GET ALL THE TOUCHPOINTS
        # ============================================
        # CHATS
        $this->GetSettingsChat();
        $chats = $this->GetChats();
        foreach ($chats as $chat) {
            $data = $this->FormatChatForTouchpoint($chat);
            
            if ($data['is_sub'] == 1) {
                $touchpoints_sub[] = $data;
            } else {
                $touchpoints[] = $data;
            }
        }
        
        # CALLS
        $calls = $this->GetCalls();
        foreach ($calls as $call) {
            $data = $this->FormatCallForTouchpoint($call);
            
            if ($data['is_sub'] == 1) {
                $touchpoints_sub[] = $data;
            } else {
                $touchpoints[] = $data;
            }
        }
        
        # FORMS
        if ($this->get_tp_forms) {
        $forms = $this->GetForms();
        foreach ($forms as $form) {
            $data = $this->FormatFormForTouchpoint($form);
            
            if ($data['is_sub'] == 1) {
                $touchpoints_sub[] = $data;
            } else {
                $touchpoints[] = $data;
            }
            
        }
        }
        
        # EMAILS
        if ($this->get_tp_emails) {
        $emails = $this->GetEmails();
        foreach ($emails as $email) {
            $data = $this->FormatEmailForTouchpoint($email);
            
            if ($data['is_sub'] == 1) {
                $touchpoints_sub[] = $data;
            } else {
                $touchpoints[] = $data;
            }
            
        }
        }        
        
        
        
        
        # SORT THE TOUCHPOINTS BY DATE
        # ============================================
        $array          = $touchpoints;
        $on             = 'timestamp';
        $order          = 'SORT_DESC';
        $touchpoints    = $this->array_sort($array, $on, $order);
        
        
        
        # OUTPUT THE TOUCHPOINTS
        # ============================================
        $output = "
        <div class='col touchpoint_btn_actions' id='' onclick='ShowAll(); return false;'>SHOW ALL</div>
        <div class='col touchpoint_btn_actions' id='touchpoints_mnu_calls' status='showing' onclick='ShowHideCalls(); return false;'>HIDE Phone Calls</div>
        <div class='col touchpoint_btn_actions' id='touchpoints_mnu_forms' status='showing' onclick='ShowHideForms(); return false;'>HIDE Contact Forms</div>
        <div class='col touchpoint_btn_actions' id='touchpoints_mnu_chats' status='showing' onclick='ShowHideChats(); return false;'>HIDE Chats</div>
        <div class='col touchpoint_btn_actions' id='touchpoints_mnu_emails' status='showing' onclick='ShowHideEmails(); return false;'>HIDE Emails</div>
        <div class='clear'></div>
        <br /><br />
        ";
        
        
        $this->touchpoints      = $touchpoints;
        $this->touchpoints_sub  = $touchpoints_sub;
        
        $this->output_global = '';
        foreach ($touchpoints as $touchpoint) {
            $this->sub_touchpoint_list = array();
            
            # output the T/P
            $this->output_global .= $this->OutputTouchpoint($touchpoint, -1);
            
            # find out if there are sub T/P
            $search = "{$touchpoint['type']}_{$touchpoint['id']}";
            array_push($this->sub_touchpoint_list, $search); 
            
            $this->GetSubTouchpoint($search);
        }
        $output .= $this->output_global;
        
        
        $script = "InitializeOnReady();";
        $output .= EchoScript($script);
        
        
        return $output;
    }
    
    public function GetSubTouchpoint($SEARCH)
    {
        ###echo "<br /><br />GetSubTouchpoint($SEARCH)";
    
        if (count($this->touchpoints_sub) > 0) {
        
        $sub_tp_index   = $this->d2_search ('master_id', $SEARCH, $this->touchpoints_sub);
        $sub_tp_exists  = ($sub_tp_index > -1) ? true : false;
        
        if ($sub_tp_exists) {
            # get the actual sub T/P record
            $sub_touchpoint = $this->touchpoints_sub[$sub_tp_index];
            
            # output the sub T/P
            $tab = (count($this->sub_touchpoint_list) > 0) ? count($this->sub_touchpoint_list) : 1;
            ###$this->output_global .= '<br />' . ArrayToStr($this->sub_touchpoint_list) . '<br />';
            $this->output_global .= $this->OutputTouchpoint($sub_touchpoint, $tab);
            
            # store the id in list so you can later check for sub T/P to this sub T/P
            $search = "{$sub_touchpoint['type']}_{$sub_touchpoint['id']}";
            array_push($this->sub_touchpoint_list, $search); 
            
            # remove it off the sub T/P stack
            unset($this->touchpoints_sub[$sub_tp_index]);
            
            # re-index array
            $this->touchpoints_sub = array_values($this->touchpoints_sub);
            
            #check for subpoints to this one
            $search = "{$sub_touchpoint['type']}_{$sub_touchpoint['id']}";
            $this->GetSubTouchpoint($search);
        } else {
            if (count($this->sub_touchpoint_list) > 0) {
                $new_search = array_pop($this->sub_touchpoint_list);
                $this->GetSubTouchpoint($new_search);
            }
        }
        
        
        } // end checking if sub_tp array > 0
    }
    
    public function d2_search ($key, $value, $array)
    {
        # FUNCTION :: Will search for value in key in 2-demensional array
        # will return the first found record index
        
        $index = -1;
        for ($i=0; $i<count($array); $i++) {
            if ($array[$i][$key] == $value) {
                $index = $i;
                $i = count($array);
            }            
        }
        return $index;
    }
    
    public function array_search_in_level($needle, $haystack, $key, &$result, $searchlevel = 0) 
    { 
        while(is_array($haystack) && isset($haystack[key($haystack)])) {
            if($searchlevel == 0 && key($haystack) == $key && $haystack[$key] == $needle) {
                $result = $haystack;
            } elseif($searchlevel > 0) {
                array_search_in_level($needle, $haystack[key($haystack)], $key, $result, $searchlevel - 1);
            }
            next($haystack);
        }
    } 
    
    public function multi_search($array, $key, $value)
    {
        $results = array();

        if (is_array($array))
        {
            if ($array[$key] == $value)
                $results[] = $array;

            foreach ($array as $subarray)
                $results = array_merge($results, $this->multi_search($subarray, $key, $value));
        }

        return $results;
    }

    
    public function GetChats()
    {
        $records = $this->SQL->GetArrayAll(array(
            'table' => $this->table_chats,
            'keys'  => "$this->table_chats.*, touchpoint_joins.master_calls_id, touchpoint_joins.master_chats_id, touchpoint_joins.master_forms_id, touchpoint_joins.master_emails_id",
            //'where' => "`wh_id`=$this->WH_ID AND $this->table_chats.active=1",
            'where' => "$this->table_chats.active=1",
            'joins' => "LEFT JOIN touchpoint_joins ON touchpoint_joins.touchpoint_chats_id = $this->table_chats.touchpoint_chats_id",
        ));
        if ($this->Show_Query) echo '<br />' . $this->SQL->Db_Last_Query;
        return $records;
    }
    
    public function GetCalls()
    {
        $records = $this->SQL->GetArrayAll(array(
            'table' => $this->table_calls,
            'keys'  => "$this->table_calls.*, touchpoint_joins.master_calls_id, touchpoint_joins.master_chats_id, touchpoint_joins.master_forms_id, touchpoint_joins.master_emails_id",
            //'where' => "`wh_id`=$this->WH_ID AND $this->table_calls.active=1",
            'where' => "$this->table_calls.active=1",
            'joins' => "LEFT JOIN touchpoint_joins ON touchpoint_joins.touchpoint_calls_id = $this->table_calls.touchpoint_calls_id",
        ));
        if ($this->Show_Query) echo '<br />' . $this->SQL->Db_Last_Query;
        return $records;
    }
    
    public function GetForms()
    {
        $records = $this->SQL->GetArrayAll(array(
            'table' => $this->table_forms,
            'keys'  => "$this->table_forms.*, touchpoint_joins.master_calls_id, touchpoint_joins.master_chats_id, touchpoint_joins.master_forms_id, touchpoint_joins.master_emails_id",
            //'where' => "`wh_id`=$this->WH_ID AND $this->table_forms.active=1",
            'where' => "$this->table_forms.active=1",
            'joins' => "LEFT JOIN touchpoint_joins ON touchpoint_joins.touchpoint_forms_id = $this->table_forms.touchpoint_forms_id",
        ));
        if ($this->Show_Query) echo '<br />' . $this->SQL->Db_Last_Query;
        return $records;
    }
    
    public function GetEmails()
    {
        $records = $this->SQL->GetArrayAll(array(
            'table' => $this->table_emails,
            'keys'  => "$this->table_emails.*, touchpoint_joins.master_calls_id, touchpoint_joins.master_chats_id, touchpoint_joins.master_forms_id, touchpoint_joins.master_emails_id",
            //'where' => "`wh_id`=$this->WH_ID AND $this->table_emails.active=1",
            'where' => "$this->table_emails.active=1",
            'joins' => "LEFT JOIN touchpoint_joins ON touchpoint_joins.touchpoint_emails_id = $this->table_emails.email_sent_logs_id",
        ));
        if ($this->Show_Query) echo '<br />' . $this->SQL->Db_Last_Query;
        return $records;
    }
    
    
    
    public function FormatChatForTouchpoint($record)
    {
        # FORMAT THE DATE
        # ======================================================================
        $date_time      = $record['chat_start_timestamp'];
        $parts          = explode(' ', $date_time);
        $date           = $parts[0];
        $time           = $parts[1];
        $inFormat       = 'yyyy-mm-dd';
        $outFormat      = 'm-d-Y';
        $formatted_date = $this->datefmt($date, $inFormat, $outFormat);
        
        $record['chat_start_timestamp'];
        $record['chat_end_timestamp'];
        
        # FOLLOWUP
        # ======================================================================
        //$followup = ($record['followup_required']) ? 1 : 0;
        $followup = rand(0,1);
        
        # FORMAT ACTIONS
        # ======================================================================
        $actions = "<div><a href='#' onclick=\"LaunchChat('{$record['touchpoint_chats_code']}')\">View Chat</a></div>";
        
        # FORMAT INFO
        # ======================================================================
        $completed = ($record['completed']) ? 'yes' : 'no';
        
        $info = '';
        $info .= "<div>Code: <b>{$record['touchpoint_chats_code']}</b></div>";
        $info .= "<div>Category: <b>{$record['category']}</b></div>";
        $info .= "<div>Completed: <b>{$completed}</b></div>";
        
        
        #$record['notes'];
        
        $am_a_sub = $record['master_calls_id'] + $record['master_chats_id'] + $record['master_forms_id'] + $record['master_emails_id'];
        $am_a_sub = ($am_a_sub > 0) ? 1 : 0;
        
        $master_id = '';
        $master_id = ($record['master_calls_id'] != 0) ? 'call_'.$record['master_calls_id'] : $master_id;
        $master_id = ($record['master_chats_id'] != 0) ? 'chat_'.$record['master_chats_id'] : $master_id;
        $master_id = ($record['master_forms_id'] != 0) ? 'form_'.$record['master_forms_id'] : $master_id;
        $master_id = ($record['master_emails_id'] != 0) ? 'form_'.$record['master_emails_id'] : $master_id;
        
        
        $data = array();
        $data['id']                     = $record['touchpoint_chats_id'];
        $data['is_sub']                 = $am_a_sub;
        $data['master_id']              = $master_id;
        $data['touchpoint_chats_id']    = $record['touchpoint_chats_id'];
        $data['master_calls_id']        = 'call_'.$record['master_calls_id'];
        $data['master_chats_id']        = 'chat_'.$record['master_chats_id'];
        $data['master_forms_id']        = 'form_'.$record['master_forms_id'];
        $data['master_emails_id']        = 'form_'.$record['master_emails_id'];
        
        $data['timestamp']  = $record['chat_start_timestamp'];
        $data['type']       = $this->type_chat;
        $data['date']       = $formatted_date;
        $data['info']       = $info;
        $data['actions']    = $actions;
        $data['followup']   = $followup;
        
        return $data;
    }
    
    public function FormatCallForTouchpoint($record)
    {
        # FORMAT THE DATE
        # ======================================================================
        $date_time      = $record['created'];
        $parts          = explode(' ', $date_time);
        $date           = $parts[0];
        $time           = $parts[1];
        $inFormat       = 'yyyy-mm-dd';
        $outFormat      = 'm-d-Y';
        $formatted_date = $this->datefmt($date, $inFormat, $outFormat);
        
        # FOLLOWUP
        # ======================================================================
        //$followup = ($record['followup_required']) ? 1 : 0;
        $followup = rand(0,1);
        
        # FORMAT ACTIONS
        # ======================================================================
        $actions = '';
        $actions .= "<div class='touchpoint_data_popup'><a>View (hover)</a><em>{$record['call_notes']}</em></div>";
        $actions .= "<div><a href='#' onclick=\"LaunchCall('{$record['touchpoint_calls_id']}')\">View Call Report</a></div>";
        
        # FORMAT INFO
        # ======================================================================
        //$completed = ($record['completed']) ? 'yes' : 'no';
        
        $info = '';
        $info .= "<div>Call ID: <b>{$record['touchpoint_calls_id']}</b></div>";
        $info .= "<div>Type: <b>{$record['call_type']}</b></div>";
        $info .= "<div>Category: <b>{$record['call_category']}</b></div>";
        
        
        
        $am_a_sub = $record['master_calls_id'] + $record['master_chats_id'] + $record['master_forms_id'] + $record['master_emails_id'];
        $am_a_sub = ($am_a_sub > 0) ? 1 : 0;
        
        $master_id = '';
        $master_id = ($record['master_calls_id'] != 0) ? 'call_'.$record['master_calls_id'] : $master_id;
        $master_id = ($record['master_chats_id'] != 0) ? 'chat_'.$record['master_chats_id'] : $master_id;
        $master_id = ($record['master_forms_id'] != 0) ? 'form_'.$record['master_forms_id'] : $master_id;
        $master_id = ($record['master_emails_id'] != 0) ? 'form_'.$record['master_emails_id'] : $master_id;
        
        $data = array();
        $data['id']                     = $record['touchpoint_calls_id'];
        $data['is_sub']                 = $am_a_sub;
        $data['master_id']              = $master_id;
        $data['touchpoint_calls_id']    = $record['touchpoint_calls_id'];
        $data['master_calls_id']        = 'call_'.$record['master_calls_id'];
        $data['master_chats_id']        = 'chat_'.$record['master_chats_id'];
        $data['master_forms_id']        = 'form_'.$record['master_forms_id'];
        $data['master_emails_id']        = 'form_'.$record['master_emails_id'];
        
        $data['timestamp']  = $record['created'];
        $data['type']       = $this->type_call;
        $data['date']       = $formatted_date;
        $data['info']       = $info;
        $data['actions']    = $actions;
        $data['followup']   = $followup;
        
        return $data;
    }
    
    public function FormatFormForTouchpoint($record)
    {
        # FORMAT THE DATE
        # ======================================================================
        $date_time      = $record['created'];
        $parts          = explode(' ', $date_time);
        $date           = $parts[0];
        $time           = $parts[1];
        $inFormat       = 'yyyy-mm-dd';
        $outFormat      = 'm-d-Y';
        $formatted_date = $this->datefmt($date, $inFormat, $outFormat);
        
        # FOLLOWUP
        # ======================================================================
        //$followup = ($record['followup_required']) ? 1 : 0;
        $followup = rand(0,1);
        
        # FORMAT ACTIONS
        # ======================================================================
        $actions = '';
        $actions .= "<div class='touchpoint_data_popup'><a>View (hover)</a><em>{$record['notes']}</em></div>";
        $actions .= "<div><a href='#' onclick=\"LaunchForm('{$record['touchpoint_forms_id']}')\">View Form Report</a></div>";
        
        # FORMAT INFO
        # ======================================================================
        //$completed = ($record['completed']) ? 'yes' : 'no';
        
        $info = '';
        $info .= "<div>Form ID: <b>{$record['touchpoint_forms_id']}</b></div>";
        $info .= "<div>Category: <b>{$record['category']}</b></div>";
        $info .= "<div>Order ID: <b>{$record['order_number']}</b></div>";
        
        
        
        $am_a_sub = $record['master_calls_id'] + $record['master_chats_id'] + $record['master_forms_id'] + $record['master_emails_id'];
        $am_a_sub = ($am_a_sub > 0) ? 1 : 0;
        
        $master_id = '';
        $master_id = ($record['master_calls_id'] != 0) ? 'call_'.$record['master_calls_id'] : $master_id;
        $master_id = ($record['master_chats_id'] != 0) ? 'chat_'.$record['master_chats_id'] : $master_id;
        $master_id = ($record['master_forms_id'] != 0) ? 'form_'.$record['master_forms_id'] : $master_id;
        $master_id = ($record['master_emails_id'] != 0) ? 'form_'.$record['master_emails_id'] : $master_id;
        
        $data = array();
        $data['id']                     = $record['touchpoint_forms_id'];
        $data['is_sub']                 = $am_a_sub;
        $data['master_id']              = $master_id;
        $data['touchpoint_forms_id']    = $record['touchpoint_forms_id'];
        $data['master_calls_id']        = 'call_'.$record['master_calls_id'];
        $data['master_chats_id']        = 'chat_'.$record['master_chats_id'];
        $data['master_forms_id']        = 'form_'.$record['master_forms_id'];
        $data['master_emails_id']       = 'form_'.$record['master_emails_id'];
        
        $data['timestamp']  = $record['created'];
        $data['type']       = $this->type_form;
        $data['date']       = $formatted_date;
        $data['info']       = $info;
        $data['actions']    = $actions;
        $data['followup']   = $followup;
        
        return $data;
    }
    
    public function FormatEmailForTouchpoint($record)
    {
        //$this->OBJ_MAIL->
        
        # UNSERIALIZE THE EMAIL INFORMATION
        # note - if getting unserialize errors - its because there are non-valid characters in the design or template - like Word characters
        # ======================================================================
        $email = unserialize($record['email_complete_blob']);
        array_walk_recursive($email,'formatSerializeRev');
        $record['touchpoint_emails_id'] = $record['email_sent_logs_id'];
        
        
        # FORMAT THE DATE
        # ======================================================================
        $date_time      = $record['email_sent_datetime']; //$record['created'];
        $parts          = explode(' ', $date_time);
        $date           = $parts[0];
        $time           = $parts[1];
        $inFormat       = 'yyyy-mm-dd';
        $outFormat      = 'm-d-Y';
        $formatted_date = $this->datefmt($date, $inFormat, $outFormat);
        
        # FOLLOWUP
        # ======================================================================
        //$followup = ($record['followup_required']) ? 1 : 0;
        $followup = rand(0,1);
        
        # FORMAT ACTIONS
        # ======================================================================
        $actions = '';
        //$actions .= "<div class='touchpoint_data_popup'><a>View (hover)</a><em>{$email['email_text']}</em></div>";
        $actions .= "<div><a href='#' onclick=\"LaunchEmail('{$record['email_sent_logs_id']}')\">View Email</a></div>";
        
        # FORMAT INFO
        # ======================================================================
        //$completed = ($record['completed']) ? 'yes' : 'no';
        
        $info = '';
        $info .= "<div>Email ID: <b>{$record['touchpoint_emails_id']}</b></div>";
        #$info .= "<div>Category: <b>{$record['category']}</b></div>";
        #$info .= "<div>Order ID: <b>{$record['order_number']}</b></div>";
        
        
        
        $am_a_sub = $record['master_calls_id'] + $record['master_chats_id'] + $record['master_forms_id'] + $record['master_emails_id'];
        $am_a_sub = ($am_a_sub > 0) ? 1 : 0;
        
        $master_id = '';
        $master_id = ($record['master_calls_id'] != 0) ? 'call_'.$record['master_calls_id'] : $master_id;
        $master_id = ($record['master_chats_id'] != 0) ? 'chat_'.$record['master_chats_id'] : $master_id;
        $master_id = ($record['master_forms_id'] != 0) ? 'form_'.$record['master_forms_id'] : $master_id;
        $master_id = ($record['master_emails_id'] != 0) ? 'form_'.$record['master_emails_id'] : $master_id;
        
        $data = array();
        $data['id']                     = $record['touchpoint_emails_id'];
        $data['is_sub']                 = $am_a_sub;
        $data['master_id']              = $master_id;
        $data['touchpoint_emails_id']    = $record['touchpoint_emails_id'];
        $data['master_calls_id']        = 'call_'.$record['master_calls_id'];
        $data['master_chats_id']        = 'chat_'.$record['master_chats_id'];
        $data['master_forms_id']        = 'form_'.$record['master_forms_id'];
        $data['master_emails_id']       = 'form_'.$record['master_emails_id'];
        
        $data['timestamp']  = $record['created'];
        $data['type']       = $this->type_email;
        $data['date']       = $formatted_date;
        $data['info']       = $info;
        $data['actions']    = $actions;
        $data['followup']   = $followup;
        
        return $data;
    }
    

    
    
    
    public function OutputTouchpoint($data, $tab=0)
    {
        switch ($data['type']) {
            case $this->type_call:
                $img_type = $this->ico_phone;
                $class = 'touchpoint_phone';
            break;
            case $this->type_form:
                $img_type = $this->ico_email;
                $class = 'touchpoint_email';
            break;
            case $this->type_email:
                $img_type = $this->ico_email;
                $class = 'touchpoint_email';
            break;
            case $this->type_chat:
                $img_type = $this->ico_chat;
                $class = 'touchpoint_chat';
            break;
            case 'default':
                $img_type = $this->ico_unknown;
                $class = 'touchpoint_other';
            break;
        }
        
        $followup = ($data['followup']) ? "<img src='{$this->ico_clock}' border='0' height='50' width='50' alt='' />" : "";
        
        
        $LEFT = $tab * $this->tab_over_pixels;
        
        ###{$data['id']} [{$tab}]
        $output = "
        <div style='padding-left:{$LEFT}px'>
        <div class='touchpoint_outter_wrapper {$class}'>
        
        <div class='touchpoint_inner_wrapper'>
            <div class='col touchpoint_icon'>
                <img src='{$img_type}' border='0' height='50' alt='' />
            </div>
            <div class='col touchpoint_info'>
                <b>{$data['date']}</b> <br />
                {$data['info']}
            </div>
            <div class='col touchpoint_actions'>
                <div style='padding:2px; color:#990000; font-weight:bold;'>ACTIONS</div>
                <div>{$data['actions']}</div>
            </div>
            <div class='col touchpoint_followup'>
                <div>{$followup}</div>
            </div>
            <div class='clear'></div>
        </div>
        </div>
        </div>
        ";
        
        return $output;
    }
    
    
    
    
    
    public function array_sort($array, $on, $order='SORT_DESC')
    {
      $new_array = array();
      $sortable_array = array();
 
      if (count($array) > 0) {
          foreach ($array as $k => $v) {
              if (is_array($v)) {
                  foreach ($v as $k2 => $v2) {
                      if ($k2 == $on) {
                          $sortable_array[$k] = $v2;
                      }
                  }
              } else {
                  $sortable_array[$k] = $v;
              }
          }
 
          switch($order)
          {
              case 'SORT_ASC':   
                  #echo "ASC";
                  asort($sortable_array);
              break;
              case 'SORT_DESC':
                  #echo "DESC";
                  arsort($sortable_array);
              break;
          }
 
          foreach($sortable_array as $k => $v) {
              $new_array[] = $array[$k];
          }
      }
      return $new_array;
    } 
    
    
    
}