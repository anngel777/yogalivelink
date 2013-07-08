<?php

class Sessions_Goto extends BaseClass
{
    private $record;
    private $sessions_id;
    private $gotoAccount;
    private $start_time;
    private $end_time;

    public function __construct($session_id, $start_time, $end_time, $gotoId){
        parent::__construct();
        $this->sessions_id  = $session_id;
        $this->start_time = str_replace(" ","T",$start_time);
        $this->end_time = str_replace(" ","T",$end_time);

        $this->gotoAccount = $this->SQL->GetRecord(array(
            'table' => 'gotomeeting',
            'keys'  => "*",
            'where' => "`email`='{$gotoId}'",
        ));
    }

    public function create(){
        $data = array(
            "subject" => "Meeting",
            "starttime" => $this->start_time,
            "endtime" => $this->end_time,
            "passwordrequired" => "false",
            "conferencecallinfo" => "Hybrid",
            "timezonekey" => "",
            "meetingtype" => "Scheduled"
        );
        $data_string = json_encode($data);

        $ch = curl_init("https://api.citrixonline.com/G2M/rest/meetings");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/json',
            'Content-type: application/json',
            'Authorization: OAuth oauth_token='. $this->gotoAccount['access_token'],
            'Content-Length: ' . strlen($data_string))
        );

        $result = curl_exec($ch);
        $result = json_decode($result);
        curl_close($ch);

        //echo "<pre>";
        //print_r($result);

        if(!isset($result->int_err_code)){
            $result = $result[0];
            $key_values = $this->FormatDataForUpdate(array(
                'goto_meeting_join_url'        => $result->joinURL,
                'goto_meeting_id'  => $result->meetingid,
            ));

            $result = $this->SQL->UpdateRecord(array(
                'table'         => "sessions",
                'key_values'    => $key_values,
                'where'         => "`sessions_id`={$this->sessions_id}",
            ));
            return "<div style=\"font-size:16px; width:400px;\"><br><br>A Goto Meeting Session Has Been Created</div>";
        } else {
            return "<div style=\"font-size:16px; width:400px;\"><br><br>".$result->msg."</div>";
        }
    }
}
