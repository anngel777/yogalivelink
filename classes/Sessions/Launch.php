<?php
class Sessions_Launch extends BaseClass
{
    public $Show_Query              = false;    // TRUE = output the database queries ocurring on this page
    
    public $Is_Instructor           = false;    // TRUE = user acessing this file is an instructor
    public $wh_id                   = 0;        // WHID of user acessing this file
    public $customer_wh_id          = 0;
    public $instructor_id           = 0;
    
    public $sessions_id             = 0;
    public $script_location         = "/office/AJAX/sessions/session_process_user";
    public $script_location_noajax  = "/office/sessions/session_process_user";
    public $Room_Id                 = null;
    
    public $Testing_Room            = false;    // TRUE = we are testing the system - turns on test features
    public $Show_Chat               = false;    // TRUE = show the option to contact YLL by chat
    public $Show_Phone              = true;     // TRUE = show the option to contact YLL by phone
    public $Show_Emergency          = true;     // TRUE = allow emergency contact button to appear on screen (instructors only)
    
    
    public $Video_Path              = "https://www.yogalivelink.com/office/faces/dynamic_1/faces.php";      // Actual path to video file
    public $Window_Height_IE        = 680;                                                                  // window height in internet explorer
    public $Window_Height_Other     = 650;                                                                  // window height in non-IE browser
    public $Window_Width_IE         = 970;                                                                  // window width in internet explorer
    public $Window_Width_Other      = 1000;                                                                 // window width in non-IE browesr
    public $Help_Area_Width_IE      = 250;                                                                  // help area width in internet explorer
    public $Help_Area_Width_Other   = 280;                                                                  // help area width in non-IE browesr
    public $Help_Topic_ID           = 33;                                                                   // FAQ id code - for topic to be shown in help area
    
    
    public function  __construct()
    {
        parent::__construct();
        
        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Description' => 'Manage launching the user into a session and displaying video chat',
        );
        
        $this->SetParameters(func_get_args());
        $this->sessions_id      = ($this->GetParameter(0)) ? $this->GetParameter(0) : 0;
        $this->wh_id            = ($this->GetParameter(1)) ? $this->GetParameter(1) : 0;
        $this->Is_Instructor    = ($this->GetParameter(2)) ? true : false;
        $this->Testing_Room     = ($this->GetParameter(3)) ? true : false;
        $this->Room_Id          = $this->sessions_id;
        
        if ($this->Is_Instructor) {
            $this->instructor_id = $this->wh_id;
        } else {
            $this->customer_wh_id = $this->wh_id;
        }
        
        if ($this->Testing_Room) {
            $this->Video_Path               = "https://www.yogalivelink.com/faces/dynamic_1/faces.php";
            $this->script_location          = "/AJAX/sessions/session_process_user";
            $this->script_location_noajax   = "/sessions/session_process_user";
        }
        
        $this->ModifyScriptLocation();
    } // -------------- END __construct --------------
    
    public function ModifyScriptLocation()
    {
        $instructor     = ($this->Is_Instructor)    ? ";instructor=true" : '';
        $testing_room   = ($this->Testing_Room)     ? ";testing_room=true" : '';
        $this->script_location = $this->script_location . ";sessions_id={$this->sessions_id};wh_id={$this->wh_id};room_id={$this->Room_Id}{$instructor}{$testing_room}";
    }
    
    public function AjaxHandle()
    {
        $this->sessions_id      = Get('sessions_id');
        $this->wh_id            = Get('wh_id');
        $this->Room_Id          = Get('room_id');
        $this->Is_Instructor    = Get('instructor');
        $this->Testing_Room     = Get('testing_room');
        $action                 = Get('action');
        $this->ModifyScriptLocation();
        
        
        switch ($action) {
            case 'LogInUser':
                if (!$this->Testing_Room) {
                    echo "<div class='process_status_message_success'>Logging user into session.</div>";
                    $result = $this->LogUserIntoSession();
                    
                    if ($result) {
                        echo "<div class='process_status_message_success'>You have been logged into session.</div>";
                        $this->EchoScript("ajaxCallGet(\"action=OpenVideoAgent\", \"video_area\");");
                    } else {
                        echo "<div class='process_status_message_error'>ERROR - unable to log user in.</div>";
                    }
                } else {
                    $this->EchoScript("ajaxCallGet(\"action=OpenVideoAgent\", \"video_area\");");
                }
            break;
            case 'OpenVideoAgent':
                $this->EchoScript("showDivArea(\"holder_video\");");
                $this->EchoScript("hideDivArea(\"holder_instructions\");");
                
                $result = $this->OpenVideoAgent();
                
                $this->EchoScript("ajaxCallGet(\"action=SessionInProgressNotice\");");
            break;
            case 'SessionInProgressNotice':
                echo "<div class='process_status_message_success'>Session In Progress.</div>";
                
                $result = $this->ShowFormSessionInProgress();
            break;
            case 'CloseSession':
                if (!$this->Testing_Room) {
                    echo "<div class='process_status_message_success'>Closing session.</div>";
                    $result = $this->LogUserOutSession();
                }
                $this->EchoScript("ajaxCallGet(\"action=RateSessionUser\");");
            break;
            case 'RateSessionUser':
                if (!$this->Testing_Room) {
                    echo "<div class='process_status_message_success'>Opening user session rating - HEADER REDIRECT.</div>";
                    
                    if ($this->Is_Instructor) {
                        $eq_RateSession = EncryptQuery("class=Sessions_RatingsInstructor;v1={$this->sessions_id};v2={$this->customer_wh_id};v3={$this->instructor_id}");
                    } else {
                        $eq_RateSession = EncryptQuery("class=Sessions_RatingsUser;v1={$this->sessions_id};v2={$this->customer_wh_id};v3={$this->instructor_id}");
                    }
                    
                    $this->EchoScript("
                        LaunchRatingWindow('{$eq_RateSession}');
                        window.close();
                    ");
                } else {
                    $this->EchoScript("window.close();");
                }
            default:
            break;
        }
    }

    public function ExecuteAjax()
    {
        $this->Execute();
    }
    
    public function Execute()
    {
        if ($this->sessions_id != 0) {
            $gap                = '&nbsp;&nbsp;&nbsp;';
            $link_login         = "ajaxCallGet(\"action=LogInUser\"); return false;";
            $link_logout        = "ajaxCallGet(\"action=CloseSession\"); return false;";
            
            # ========== GET THE HELP CONTENTS ==========
            $OBJ_HELP                       = new Website_HelpcenterFAQs();
            $help                           = $OBJ_HELP->GetSingleFAQ($this->Help_Topic_ID);
            $customer_help_arr              = $help;
            $instructor_help_arr            = $help;
            $video_help_content_testing     = $customer_help_arr['answer'];
            $video_help_content_instructor  = $instructor_help_arr['answer'];
            $video_help_content_customer    = $customer_help_arr['answer'];
            
            
            # ========== SETUP THE TOP AREA ==========
            if ($this->Testing_Room) {
                AddSwap('@@CONTENT_TOP@@', '');
            } else {
                $top = '<table><tr>';
                
                $top    .= "<td><a class='index_button_simple' href='#' onclick='{$link_logout}'>END SESSION</a></td>";
                $top    .= "<td><a class='index_button_simple' href='#' onclick='window.location.reload()'>RELOAD SESSION</a></td>";
                
                if ($this->Is_Instructor && $this->Show_Emergency) {
                    $instructor_wh_id   = $_SESSION['USER_LOGIN']['LOGIN_RECORD']['wh_id'];
                    $box_emergency      = 'EMERGENCY CONTACT';
                    $OBJ_EMERGENCY      = new General_EmergencyContact();
                    $box_emergency      = $OBJ_EMERGENCY->OutputEmergencyBox($this->wh_id, $this->sessions_id, $instructor_wh_id, $this->Room_Id);
                    $top               .= "<td>$box_emergency</td>";
                }
                
                if ($this->Show_Chat) {
                    $OBJ_CHAT   = new Chat_Chat();
                    $box_chat   = $OBJ_CHAT->OutputChatStatusBox();
                    $top       .= "<td>$box_chat</td>";
                }
                
                if ($this->Show_Phone) {
                    global $CONTACT_PHONE_NUMBER;
                    $link           = "";
                    $box_phone      = AddBox_Type2($CONTACT_PHONE_NUMBER, $link, $GLOBALS['ICO_PHONE']);
                    $top           .= "<td>$box_phone</td>";
                    #$top       .= "<img src='{$GLOBALS['ICO_PHONE']}' border='0' alt='' />";
                }
                
                $top .= '</tr></table>';
                AddSwap('@@CONTENT_TOP@@', $top);
            }
            
            
            # ========== SETUP THE LOWER AREA ==========
            if ($this->Testing_Room) {
                $video_help_content     = $video_help_content_testing;
            } else {
                $video_help_content     = ($this->Is_Instructor) ? $video_help_content_instructor : $video_help_content_customer;
            }
            
            
            $help_width = ($this->ae_detect_ie()) ? $this->Help_Area_Width_IE : $this->Help_Area_Width_Other;
            
            $output = "
            <div style='width:100%; padding:10px; border:0px solid red;'>
                <div id='holder_instructions'>
                    <div id='step_1' style='border:1px solid #ddd;'>
                        <center>
                            <a href='#' onclick='{$link_login}'>
                            <div class='index_header'>
                                <table cellpadding='0' cellspacing='3'>
                                <tr><td valign='top' align='left'>1.</td>   <td valign='top' align='left'>Before you begin: Plug in your video and audio equipment <br />and make sure everything is turned on.</td></tr>
                                <tr><td valign='top' align='left'>2.</td>   <td valign='top' align='left'>Click HERE to start session.</td></tr>
                                <tr><td align='center' colspan='2'><img src='/office/images/videochat/step_plugin.jpg' border='0' alt='' /></td></tr>
                                </table>
                            </div>
                            </a>
                        </center>
                    </div>
                </div>
                
                <div id='holder_video' style='display:none;'>
                    <table cellpadding='0' cellspacing='0'>
                        <tr>
                            <td valign='top' width='250'>
                            
                                <!-- START :: VIDEO HELP AREA -->
                                <div id='video_area_help' style='border:0px solid green; background-color:white;'>
                                    <div style='height:60px; padding:10px 10px 0px 10px; background-color:#eee;'>
                                        <div class='index_header'>video chat help</div>
                                        <div class='index_header' style='font-size:12px;'>Room ID: {$this->Room_Id}</div>
                                    </div>
                                    <div style='height:420px; width:{$help_width}px; overflow:scroll; padding:0px 10px 10px 10px;'>
                                        <div >
                                            <div class='index_content'>
                                            {$video_help_content}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- END :: VIDEO HELP AREA -->
                                
                            </td>
                            <td valign='top' width='100%'>
                            
                                <!-- START :: VIDEO AREA -->
                                <div id='video_area' style='height:500px; border:0px solid #000;'></div>
                                <!-- END :: VIDEO AREA -->
                                
                            </td>
                            <td valign='top' width='20'>&nbsp;</td>
                        </tr>
                    </table>
                </div>
                
                <div id='holder_status' style='display:none;'>
                
                    <div id='ajax_status' style='border:1px solid #ddd; padding:10px; background-color:yellow;'>
                        <span id='loader' style='display:none;'>
                        {$GLOBALS['LOADER_FULL_IMG']}
                        </span>
                    </div>
                
                    <div id='ajax_result' style='border:1px solid #ddd; padding:10px; background-color:#FDC422; width:300px;'>&nbsp;</div>
                    
                    <div id='session_links' style='border:1px solid #ddd; padding:10px; background-color:#eee; text-align:center; width:300px;'>
                        <h3>READY TO BEGIN SESSION?</h3>
                        <a href='#' onclick='{$link_login}'><h4>Click HERE to start</h4></a>
                        <a href='#' onclick='{$link_logout}'><h4>Click HERE to END SESSION</h4></a>
                    </div>
                    
                </div>
            </div>
            ";
            
            $this->AddScript();
            echo $output;
            
            // ----- resize the containment window to fit contents - based on setup dimensions
            $this->ResizeLaunchWindow();
        }
    }
    
    public function ShowFormSessionStart()
    {
        # FUNCTION :: Show content to user before they start a session - instructions on plugging in equipment
        
        $link = "ajaxCallGet(\"action=LogInUser\"); return false;";
        $link2 = "ajaxCallGet(\"action=CloseSession\"); return false;";
    
        $this->AddScript();
        
        $out = '';
        for ($i=1; $i<20; $i++) {
            $out .= "<a href='{$this->script_location_noajax};sessions_id={$i}'>{$i}</a>&nbsp;&nbsp;&nbsp;";
        }
        echo $out . '<br /><br /><br />';
        
        
        echo "<div style='border:1px solid #ddd; padding:10px; background-color:#eee; text-align:center;'>
            <h3>READY TO BEGIN SESSION?</h3>
            <a href='#' onclick='{$link}'><h4>Click HERE to start</h4></a>
            <br /><br />
            <a href='#' onclick='{$link2}'><h4>Click HERE to END SESSION</h4></a>
            </div>";
    }
    
    public function ShowFormSessionInProgress()
    {
        $link = "ajaxCallGet(\"action=CloseSession\"); return false;";
    
        $this->AddScript();
        echo "<div style='border:1px solid #ddd; padding:10px; background-color:#eee; text-align:center;'>
            <h3>SESSION IN PROGRESS</h3>
            <a href='#' onclick='{$link}'><h4>Click HERE to complete</h4></a>
            </div>";
    }    
    
    
    public function LogUserIntoSession()
    {
        # FUNCTION :: Update the session record to show this user has logged in
        
        $keys_values = $this->FormatDataForUpdate(array(
            'login_user' => 1,
            'login_user_datetime' => Date("Y-m-d H:i:s"),
        ));
        
        $result = $this->SQL->UpdateRecord(array(
            'table'         => 'session_checklists',
            'key_values'    => $keys_values,
            'where'         => "`sessions_id`='{$this->sessions_id}'",
        ));
        if ($this->Show_Query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        
        return $result;
    }
    
    public function OpenVideoAgent()
    {
        // ========== INJECT THE CHAT WINDOW CONTENT ONTO THE PAGE ==========
        $output = "
            <iframe src='{$this->Video_Path}?roomid={$this->Room_Id}' width='100%' height='100%' frameborder='0' scrolling='no'>
                <p>Your browser does not support iframes. You must upgrade your browser to take Yoga sessions.</p>
            </iframe>";
        
        echo $output;
        
        // ========== RESIZE THE CURRENT FRAME TO FIT CONTENTS ==========
        $script = "
            var dialogNumber = '';
            if (window.frameElement) {
                if (window.frameElement.id.substring(0, 13) == 'appformIframe') {
                    dialogNumber = window.frameElement.id.replace('appformIframe', '');
                }
            }
            ResizeIframe();
            ";
        AddScript($script);
        
        $result = true;
        return $result;
    }
    
    public function LogUserOutSession()
    {
        # FUNTION :: Log user out of the session for tracking - NOT USING AT MOMENT - FUTURE FEATURE
        
        echo "<br />CLOSE THE VIDEO AGENT";
        
        $result = true;
        return $result;
    }
    
    public function ResizeLaunchWindow()
    {
        # FUNCTION :: Resize the launch window to specific dimensions depending upon browser
        
        $width  = ($this->ae_detect_ie()) ? $this->Window_Width_IE : $this->Window_Width_Other;
        $height = ($this->ae_detect_ie()) ? $this->Window_Height_IE : $this->Window_Height_Other;
        $script = "window.resizeTo($width, $height)";
        $this->EchoScript($script);
    }
    
    private function ae_detect_ie()
    {
        # FUNTION :: Detect window size in Internet Explorer
        
        if (isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)) {
            return true;
        } else {
            return false;
        }
    }
    
    public function AddScript()
    {
        # FUNCTION :: Scripts needed for this class
        
        $script = <<<SCRIPT
        function ajaxCallPost(data){
            loadingmessage('Please wait, deleting images...', 'show');
            $.ajax({
                type: 'POST',
                url: '{$this->script_location}',
                data: 'a=delete&large_image='+large_image+'&thumbnail_image='+thumbnail_image,
                cache: false,
                success: function(response){
                    loadingmessage('', 'hide');
                    response = unescape(response);
                    var response = response.split("|");
                    var responseType = response[0];
                    var responseMsg = response[1];
                    if(responseType=="success"){
                        $('#upload_status').show().html('<b>Success</b> - '+responseMsg+'');
                        $('#uploaded_image').html('');
                    }else{
                        $('#upload_status').show().html('<b>Unexpected Error</b> - Please try again - '+response);
                    }
                }
            });
        }
        
        function ajaxCallGet(data, divLocation){
            divLocation = typeof(divLocation) != 'undefined' ? divLocation : 'ajax_result';
            loadingmessage('Please wait, doing action...', 'show');
            $.ajax({
                type: 'GET',
                url: '{$this->script_location}',
                data: data,
                cache: false,
                success: function(response){
                    loadingmessage('', 'hide');
                    
                    response = unescape(response);
                    response = response + "<br />";
                    
                    $('#' + divLocation).append(response);
                }
            });
        }
        
        //show and hide the loading message
        function loadingmessage(msg, show_hide){
            if(show_hide=="show"){
                $('#loader').show();
                $('#ajax_status').show().text(msg);
            }else if(show_hide=="hide"){
                $('#loader').hide();
                $('#ajax_status').text('').hide();
            }else{
                $('#loader').hide();
                $('#ajax_status').text('').hide();
            }
        }
        
        function hideDivArea(theDiv){
            $('#' + theDiv).hide();
        }
        
        function showDivArea(theDiv){
            $('#' + theDiv).show();
        }
        
        function LaunchRatingWindow(eq) {
            var link = getClassExecuteLinkNoAjax(eq) + ';template=overlay;pagetitle=Rate Yoga Session;wintype=window';
            var width = 540;
            var height = 570;
            window.open(link,'Rating','toolbar=no,width='+width+',height='+height+',location=no,scrollbars=yes');
        }
        
SCRIPT;
        AddScript($script);
    }
    
    public function EchoScript($script)
    {
        # FUNTION :: Used to wrap a script in tags - needed for instant AJAX calls
        # NOTE : in future should be moved to BaseClass
        
        echo "<script type='text/javascript'>{$script}</script>";
    }
    
    
}  // -------------- END CLASS --------------