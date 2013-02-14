<?php
class Sessions_ProcessUser extends BaseClass
{
    public $ShowQuery               = true;
    public $ShowArray               = false;
    
    
    public $sessions_id             = 0;
    public $script_location         = "/office/AJAX/sessions/session_process_user";    
    public $script_location_noajax  = "/office/sessions/session_process_user";
    
    public function  __construct()
    {
        parent::__construct();
        
    } // -------------- END __construct --------------
    
    
    public function ModifyScriptLocation()
    {
        $this->script_location = $this->script_location . ";sessions_id={$this->sessions_id}";
    }
    
    public function AjaxHandle()
    {
        $this->sessions_id  = Get('sessions_id');
        $action             = Get('action');
        switch ($action) {
            case 'LogInUser':
                echo "<br />Logging user into session.";
                $result = $this->LogUserIntoSession();
                
                if ($result) {
                    echo "<br />User has been logged into session.";
                    $this->EchoScript("ajaxCallGet(\"action=OpenVideoAgent\");");
                } else {
                    echo "<br />ERROR - unable to log user in";
                }
            break;
            case 'OpenVideoAgent':
                echo "<br />Opening video agent.";
                $result = $this->OpenVideoAgent();
                $this->EchoScript("ajaxCallGet(\"action=SessionInProgressNotice\");");
            break;
            case 'SessionInProgressNotice':
                echo "<br />Session In Progress.";
                $result = $this->ShowFormSessionInProgress();
            break;
            case 'CloseSession':
                echo "<br />Closing session.";
                $result = $this->LogUserOutSession();
                $this->EchoScript("ajaxCallGet(\"action=RateSessionUser\");");
            break;
            case 'RateSessionUser':
                echo "<br />Opening user session rating - HEADER REDIRECT.";
                $this->EchoScript("
                    window.location='/office/sessions/session_rating_user;sessions_id={$this->sessions_id}';
                ");
                #header("Location: /office/sessions/session_rating");
                #$OBJ_RS = new Sessions_RatingsUser();
                #$OBJ_RS->AddRecord();
            default:
            break;
        }
    }


    
    
    public function ShowFormSessionStart()
    {
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
        echo "<br /><br />";
        echo "<div id='ajax_status' style='border:1px solid #ddd; padding:10px; background-color:yellow;'><span id='loader' style='display:none;'><img src='/office/images/loader.gif' alt='Loading...'/></span></div>";
        echo "<br /><br />";
        echo "<div id='ajax_result' style='border:1px solid #ddd; padding:10px; background-color:#FDC422'>&nbsp;z</div>";
    }
    

    public function ShowFormSessionInProgress()
    {
        $link = "ajaxCallGet(\"action=CloseSession\"); return false;";
    
        $this->AddScript();
        echo "<div style='border:1px solid #ddd; padding:10px; background-color:#eee; text-align:center;'>
            <h3>SESSION IN PROGRESS</h3>
            <a href='#' onclick='{$link}'><h4>Click HERE to complete</h4></a>
            </div>";
        echo "<br /><br />";
        #echo "<div id='ajax_status' style='border:1px solid #ddd; padding:10px; background-color:yellow;'><span id='loader' style='display:none;'><img src='/office/images/loader.gif' alt='Loading...'/></span></div>";
        #echo "<br /><br />";
        #echo "<div id='ajax_result' style='border:1px solid #ddd; padding:10px; background-color:#FDC422'>&nbsp;z</div>";
    }    
    
    
    public function LogUserIntoSession()
    {
        $keys_values = $this->FormatDataForUpdate(array(
            'login_user' => 1,
            'login_user_datetime' => Date("Y-m-d H:i:s"),
        ));
        
        $result = $this->SQL->UpdateRecord(array(
            'table'         => 'session_checklists',
            'key_values'    => $keys_values,
            'where'         => "`sessions_id`='{$this->sessions_id}'",
        ));
        if ($this->ShowQuery) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        
        return $result;
    }
    
    public function OpenVideoAgent()
    {
        echo "<h1>Code to open video agent goes here</h1>";
        
        $result = true;
        return $result;
    }
    
    public function LogUserOutSession()
    {
        #CLOSE THE VIDEO AGENT
        
        echo "<br />CLOSE THE VIDEO AGENT";
        
        $result = true;
        return $result;
    }
    
    
    
    
    
    
    
    
    
    
    
    
    

    
    
    
    
    
    
    
    public function AddScript()
    {
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
        
        function ajaxCallGet(data){
            loadingmessage('Please wait, doing action...', 'show');
            $.ajax({
                type: 'GET',
                url: '{$this->script_location}',
                data: data,
                cache: false,
                success: function(response){
                    loadingmessage('', 'hide');
                    
                    response = unescape(response);
                    response = response + "<hr><br />";
                    
                    $('#ajax_result').append(response);
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
        
SCRIPT;
        AddScript($script);
    }
    
    
    
    public function EchoScript($script)
    {
        echo "<script type='text/javascript'>{$script}</script>";
    }
    
    
    
    
    

}  // -------------- END CLASS --------------