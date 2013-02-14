<?php
class Sessions_Signup extends BaseClass
{
    public $sessions_id                 = 0;
    public $script_location             = "/office/sessions/signup";
    public $time_release                = '';
    public $time_release_javascript     = '';
    public $time_expire_duration        = 300; //(seconds)  //Minutes
    public $time_restart_duration       = 10; //(seconds)
    public $session_record              = array();
    public $user_record                 = array();
    
    public $show_query                  = false;
    public $current_step                = 0;
    
    public $customer_wh_id              = 0;
    
    public $table_credits               = 'credits';
    
    public $booking_session_credits_list = '';
    # note ---> might want to do thiswith ajax calls like the user starting a session
    
    
    public function  __construct()
    {
        parent::__construct();
    } // -------------- END __construct --------------

    public function HandleStep($step)
    {
        $output = '';
    
        switch ($step) {
            case 'start':
                $this->current_step = 1;
                $this->GetSessionRecord();
                $locked = $this->CheckIfSessionLocked();
                if ($locked) {
                    $output .= 'SESSION IS LOCKED - UNABLE TO BOOK RIGHT NOW';
                    
                    $this->LockSession();
                    $output .= $this->ShowSessionInformation();
                } else {
                    $this->LockSession();
                    $output .= $this->ShowSessionInformation();
                }
            break;
            case 'payment':
                $this->current_step = 2;
                $this->GetSessionRecord();
                $this->GetUserCredits();
                $output .= $this->ShowPaymentInformation();
            break;
            case 'timeexpire':
                $this->current_step = 1;
                $output .= $this->ShowTimeExpiredInformation();
            break;
            case 'cancel':
                $this->current_step = 3;
                //$output .= $this->ShowTimeExpiredInformation();
                $this->UnlockSession();
                $output .= "<h2 style='color:#990000;'>YOUR PURCHASE SESSION HAS BEEN CANCELLED</h2>";
            break;
            default:
                $output .= 'NO STEP PASSED IN';
            break;
        }
        
        $this->AddStyle();
        $this->AddScript();
        
        return $output;
    }
    
    
    public function UnlockSession()
    {
        $key_values = $this->FormatDataForUpdate(array(
            'locked' => 0,
            'locked_wh_id' => '',
            'locked_start_datetime' => '',
        ));
        $record = $this->SQL->UpdateRecord(array(
            'table'         => 'sessions',
            'key_values'    => $key_values,
            'where'         => "`sessions_id`='{$this->sessions_id}' AND active=1",
        ));
        if ($this->show_query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
    }
    
    
    public function LockSession()
    {
        $tmp_date = Date("Y-m-d H:i:s");
        $key_values = $this->FormatDataForUpdate(array(
            'locked' => 1,
            'locked_wh_id' => 666,
            'locked_start_datetime' => $tmp_date,
        ));
        $record = $this->SQL->UpdateRecord(array(
            'table'         => 'sessions',
            'key_values'    => $key_values,
            'where'         => "`sessions_id`='{$this->sessions_id}' AND active=1",
        ));
        if ($this->show_query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        
        
        # UPDATE THE SESSION WITH NEWLY LOCKED INFORMATION - SAVES A RECALL OF DATABASE RECORD
        $this->session_record['locked']                 = 1;
        $this->session_record['locked_wh_id']           = 666;
        $this->session_record['locked_start_datetime']  = $tmp_date;
    }

    
    public function BookSession()
    {
        # CREATE THE CHECKLIST
        # ============================================================
        $FormArray = array(
            'sessions_id'   => $this->sessions_id,
            'wh_id'         => $this->customer_wh_id,
            'paid'          => 1,
            'payment_id'    => 1666,
        );
    
        $keys_values    = $this->FormatDataForInsert($FormArray);
        $parts          = explode('||', $keys_values);
        $keys           = $parts[0];
        $values         = $parts[1];
        
        $result = $this->SQL->AddRecord(array(
            'table'     => 'session_checklists',
            'keys'      => $keys,
            'values'    => $values,
        ));
        if ($this->show_query) echo "<br /><br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        
        
        # BLOCK OUT THE CREDITS
        # ============================================================
        
        $cost_in_credits    = 5;
        $credits            = explode('|', $this->booking_session_credits_list);
        foreach ($credits as $credit) {
            $key_values = $this->FormatDataForUpdate(array(
                'used'          => 1,
                'sessions_id'   => $this->sessions_id,
            ));
            $record = $this->SQL->UpdateRecord(array(
                'table'         => 'credits',
                'key_values'    => $key_values,
                'where'         => "`credits_id`='{$credit}' AND active=1",
            ));
            if ($this->show_query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;

            
            $date = date('now');
            $note = "\n\n [Date:$date] Credit Used to purchase session (session_id=$this->sessions_id).";
            $result = $this->SQL->AppendValue(array(
                'table'         => $this->table_credits,
                'key'           => 'notes',
                'value'         => "$note",
                'where'         => "`credits_id`='{$credit}' AND active=1",
            ));
            
        }
        
        
        # SEND VARIOUS EMAILS
        # ============================================================
    }
    
    
    public function GetSessionRecord()
    {
        // note ---> need to also get the instructor name
        $this->session_record = $this->SQL->GetRecord(array(
            'table' => 'sessions',
            'keys'  => '*',
            'where' => "`sessions_id`='{$this->sessions_id}' AND active=1",
        ));
        if ($this->show_query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
    }

    
    public function GetUserCredits()
    {
        # GET CREDITS FOR THIS USER
        /*
        $record = $this->SQL->GetRecord(array(
            'table' => 'sessions',
            'keys'  => '*',
            'where' => "`sessions_id`='{$this->sessions_id}' AND active=1",
        ));
        if ($this->show_query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        
        
        $date           = $record['date'];
        $time           = "{$record['start_datetime']} - {$record['end_datetime']}";
        $instructor     = "Temporary Instructor"; //$record['instructor_id'];
        */
        
        
        # ===================================
        # FAKE DATA HERE
        # ===================================
        $this->user_record['credits'] = 15;
    }
    
    
    public function CheckIfSessionLocked()
    {
        # check if the session is already locked out
        return $this->session_record['locked'];
        //locked_start_datetime
    }
    
    
    public function ShowSessionInformation()
    {
        $date           = $this->session_record['date'];
        $time           = "{$this->session_record['start_datetime']} - {$this->session_record['end_datetime']}";
        $instructor     = "Temporary Instructor"; //$this->session_record['instructor_id'];
        
        
        
        # FIGURE OUT THE EXPIRE TIME
        # =================================================================
        $time_locked    = $this->session_record['locked_start_datetime']; //'2010-10-28 22:14:00';
        $event_length   = $this->time_expire_duration;
        $timestamp      = strtotime("$time_locked");
        #$etime          = strtotime("+$event_length minutes", $timestamp);
        $etime          = strtotime("+$event_length seconds", $timestamp);
        $time_release   = date('Y-m-d H:i:s', $etime);
        
        //echo "<br />time_locked ===> $time_locked";
        //echo "<br />time_release ===> $time_release";
        
        
        # FORMAT THE DATE SO IT'S READABLE BY JAVASCRIPT LATER
        # =================================================================
        $this->time_release_javascript  = $this->convertDateTimeToJS($time_release);
        $this->time_release             = $time_release;
        $time_remaining                 = $this->GetTimeRemaining();
        
        
        
        # FORMAT THE SESSION INFO
        # =================================================================
        $btn_continue       = MakeButton('positive', 'CONTINUE', "{$this->script_location};step=payment;time_release={$this->time_release_javascript}");
        $btn_time_expired   = MakeButton('negative', 'FORCE TIME EXPIRED', "{$this->script_location};step=timeexpire");
        $btn_cancel         = MakeButton('negative', 'CANCEL PURCHASE', "{$this->script_location};step=cancel");
        
        $output = "
            <div id='shortly' style='width:240px; height:45px; display:none;'></div>
            
            <div style='border:1px solid #990000; padding:10px; width:500px; font-size:14px;'>
            <div style='float:left; width:150px; text-align:right; font-weight:bold;'>
                DATE:&nbsp;&nbsp;<br />
                TIME:&nbsp;&nbsp;<br />
                INSTRUCTOR:&nbsp;&nbsp;
            </div>
            <div style='float:left; width:350px; text-align:left; font-weight:normal;'>
                {$date}<br />
                {$time}<br />
                {$instructor}
            </div>
            <div style='clear:both;'></div>
            </div>
            
            <br /><br />
            {$time_remaining}
            
            <br /><br />
            
            
            <div>
                <div style='float:right;'>
                    {$btn_continue}<br />
                    {$btn_time_expired}
                </div>
                
                <div style='float:left;'>
                    {$btn_cancel}
                </div>
                <div style='clear:both;'></div>
            </div>
            
        ";
        
        #$this->AddStyle();
        #$this->AddScript();
        
        return $output;
    }
    
    
    public function ShowPaymentInformation()
    {
        # FIGURE OUT THE EXPIRE TIME
        # =================================================================
        $this->time_release_javascript  = Get('time_release');
        $time_remaining                 = $this->GetTimeRemaining();
        
        
        
        # FIGURE OUT THE CREDITS
        # =================================================================
        $credits_user               = $this->user_record['credits'];
        $credits_course_cost        = $this->session_record['credits_cost'];
        $credits_remaining          = ($credits_user - $credits_course_cost);
        $credits_title              = ($credits_course_cost == 1) ? 'credit' : 'credits';
        $credits_remaining_title    = ($credits_remaining == 1) ? 'credit' : 'credits';
        
        $purchase_credits_box = "
            <div style='width:350px; font-size:14px;'>
                <div style='float:left; width:30px; text-align:right; font-weight:bold; border:0px solid red;'>
                    1 <br />
                    5 <br />
                    10
                </div>
                <div style='float:left; width:50px; text-align:left; font-weight:normal; border:0px solid red;'>
                    &nbsp;&nbsp; for <br />
                    &nbsp;&nbsp; for <br />
                    &nbsp;&nbsp; for 
                </div>
                <div style='float:left; width:60px; text-align:left; font-weight:bold; border:0px solid red;'>
                    $19.99 <br />
                    $75.00 <br />
                    $120
                </div>
                <div style='float:left; width:140px; text-align:left; font-weight:bold; border:0px solid red;'>
                    <br />
                    &nbsp;(25% savings)<br />
                    &nbsp;(50% savings)
                </div>
                <div style='float:left; width:50px; text-align:left; font-weight:normal; border:0px solid red;'>
                    &nbsp;[buy]<br />
                    &nbsp;[buy]<br />
                    &nbsp;[buy]
                </div>
                <div style='clear:both;'></div>
            </div>";
        
        
        
        
        
        # FORMAT THE SESSION INFO
        # =================================================================
        $btn_continue       = MakeButton('positive', 'CONTINUE', "{$this->script_location};step=payment;time_release={$this->time_release_javascript}");
        $btn_time_expired   = MakeButton('negative', 'FORCE TIME EXPIRED', "{$this->script_location};step=timeexpire");
        $btn_cancel         = MakeButton('negative', 'CANCEL PURCHASE', "{$this->script_location};step=cancel");
        
        $output = "
            <div id='shortly' style='width:240px; height:45px; display:none;'></div>
            
            <div class='sec_wrapper' style='width:200px;'>
                <div class='sec_header'>Session Cost</div>
                <div class='sec_body'>{$credits_course_cost} {$credits_title}</div>
            </div>
            
            <br /><br />
            
            <div name='use_credits_wrapper'>
                
                <div style='float:left;'>
                    <div class='sec_wrapper' style='width:150px;'>
                        <div class='sec_header'>Existing Credits</div>
                        <div class='sec_body'>
                            <center>
                            <form id='testconfirmJQ' name='testconfirmJQ' method='post'>
                                {$credits_user} <input id='submitJQ' name='submitJQ' type='submit' value='Use' />
                            </form>
                            </center>
                        </div>
                    </div>
                </div>
                
                <div style='float:right;'>
                    <div class='sec_wrapper'>
                        <div class='sec_header'>Buy Account Credits</div>
                        <div class='sec_body'>{$purchase_credits_box}</div>
                    </div>
                </div>
                
                <div style='clear:both;'></div>
                
            </div>
            
            <br /><br />
            {$time_remaining}
            
            <br /><br />
            
            
            <div>
                <div style='float:right;'>
                    {$btn_continue}<br />
                    {$btn_time_expired}
                </div>
                
                <div style='float:left;'>
                    {$btn_cancel}
                </div>
                <div style='clear:both;'></div>
            </div>
            
            

            
            
            <div id='dialog' title='Confirm Session Purchase'>
            <br />
            Please confirm that you would like to purchase this session for <span style='font-size:14px; font-weight:bold; color:blue;'>{$credits_course_cost} {$credits_title}</span>. After this purchase you will have <span style='font-size:12px; font-weight:bold;'>{$credits_remaining} {$credits_remaining_title}</span> remaining in your account.
            <br /><br />
            To make purchase, click Submit Form.</p><p>To cancel transaction, click Cancel.
            </div>

        ";
        
        #$this->AddStyle();
        #$this->AddScript();
        
        return $output;
    }    

    
    public function ShowTimeExpiredInformation()
    {
        # UNLOCK THE SESSION
        # =================================================================
        $this->UnlockSession();
        
        
        
        # FORMAT THE OUTPUT
        # =================================================================
        $btn_cancel         = MakeButton('negative', 'CANCEL PURCHASE', "{$this->script_location};step=cancel");
        $output = "
            <div id='restart_process_countdown' style='width:240px; height:45px; display:none;'></div>
            
            
            <div style='border:1px solid #990000; padding:10px; width:500px; font-size:14px;'>
                <div style='font-size:16px; color:#990000;'>TIME HAS EXPIRED</div><hr>
                <br />
                We will allow you to re-start the purchase process in <span id='restart_process_countdown_monitor' style='font-weight:bold;'></span>.
                <br /><br />
                <div id='restart_process_link' style='display:none; text-align:center;'>
                    <button type='button' class='positive' name='btn_restart_process' onclick=\"window.location='{$this->script_location};step=start'\">
                        <img src='/office/images/buttons/save.png' alt=''/>
                        RESTART PROCESS
                    </button>
                </div>
            </div>
            
            <div>
                <div style='float:right;'>
                    {$btn_cancel}
                </div>
                
                <div style='float:left;'>
                    
                </div>
                <div style='clear:both;'></div>
            </div>
        ";
        
        #$this->AddStyle();
        #$this->AddScript();
        
        return $output;
    }    
    
    
    public function GetTimeRemaining()
    {
        $output = "
            <div style='border:1px solid #990000; padding:10px; width:500px; font-size:14px;'>
                <div style='font-weight:bold;'>TIME REMAINING: <span id='time_remaining_monitor'></span></div>
                <div style='font-size:11px;'>You must complete your transaction within this time or another person may book this session</div>
            </div>
        ";
        return $output;
    }

    
    public function AddStyle()
    {
        $style = "
            .sec_wrapper {
                border:1px solid #990000;
            }
            .sec_header {
                background-color: #f2f2f2;
                font-size:14px;
                border-bottom:1px solid #990000;
                padding:5px;
            }
            .sec_body {
                background-color: #fff;
                font-size:12px;
                padding:5px;
            }
            
            
            
            /* jQuery Countdown styles 1.5.8. */
            .hasCountdown {
                border: 1px solid #ccc;
                background-color: #eee;
            }
            .countdown_rtl {
                direction: rtl;
            }
            .countdown_holding span {
                background-color: #ccc;
            }
            .countdown_row {
                clear: both;
                width: 100%;
                padding: 0px 2px;
                text-align: center;
            }
            .countdown_show1 .countdown_section {
                width: 98%;
            }
            .countdown_show2 .countdown_section {
                width: 48%;
            }
            .countdown_show3 .countdown_section {
                width: 32.5%;
            }
            .countdown_show4 .countdown_section {
                width: 24.5%;
            }
            .countdown_show5 .countdown_section {
                width: 19.5%;
            }
            .countdown_show6 .countdown_section {
                width: 16.25%;
            }
            .countdown_show7 .countdown_section {
                width: 14%;
            }
            .countdown_section {
                display: block;
                float: left;
                font-size: 75%;
                text-align: center;
            }
            .countdown_amount {
                font-size: 200%;
            }
            .countdown_descr {
                display: block;
                width: 100%;
            }
            .highlight {
                color: #990000;
            }
        ";
        
        AddStyle($style);
        AddStyleSheet('/jslib/themes/base/ui.dialog.css');
    }
    
    
    public function AddScript()
    {
        AddScriptInclude('/jslib/ui/ui.dialog.js');
        AddScriptInclude('/jslib/countdown/jquery.countdown.pack.js');
        
        $script = <<<SCRIPT
            $('#dialog').dialog({
                autoOpen: false,
                width: 400,
                modal: true,
                resizable: false,
                buttons: {
                    "Submit Form": function() {
                        document.testconfirmJQ.submit();
                    },
                    "Cancel": function() {
                        $(this).dialog("close");
                    }
                }
            });
            
            
            // BUY DIALOG POPUP
            // ====================================================
            $('form#testconfirmJQ').submit(function(){
                $('#dialog').dialog('open');
                return false;
            });
            
            $('input#TBcancel').click(function(){
                tb_remove();
            });
            
            $('input#TBsubmit').click(function(){
                document.testconfirmTB.submit();
            });
            
            
            // COUNTDOWN TIMER - PURCHASING
            // ====================================================
            var expireTime = new Date({$this->time_release_javascript});
            
            $('#shortly').countdown({
                until: expireTime, 
                onExpiry: liftOff, 
                onTick: watchCountdown, 
                format: 'MS', 
                expiryUrl: '/office/sessions/signup;step=timeexpire'
                });
             
            function liftOff() { 
                alert('You have not completed session purchase in time!'); 
            } 
             
            function watchCountdown(periods) {
                if (periods[6] < 10) {
                    var seconds = '0' + periods[6];
                } else {
                    var seconds = periods[6];
                }
                
                $('#time_remaining_monitor').text(periods[5] + ':' + seconds);
                
                //if ($.countdown.periodsToSeconds(periods) == 5) { 
                //    $(this).addClass('highlight'); 
                //} 
            }
            
            
            // COUNTDOWN TIMER - RESTART PURCHASE
            // ====================================================
            $('#restart_process_countdown').countdown({
                until: +{$this->time_restart_duration}, 
                format: 'MS', 
                onTick: restartProcessCountdown, 
                onExpiry: allowRestartProcess, 
            });
            
            function allowRestartProcess() { 
                $('#restart_process_link').show();
            } 
            
            function restartProcessCountdown(periods) {
                if (periods[6] < 10) {
                    var seconds = '0' + periods[6];
                } else {
                    var seconds = periods[6];
                }
                
                $('#restart_process_countdown_monitor').text(periods[5] + ':' + seconds);
            }
SCRIPT;
        AddScriptOnReady($script);
    }
    
    
}  // -------------- END CLASS --------------