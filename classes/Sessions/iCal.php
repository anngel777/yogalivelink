<?php
class Sessions_iCal extends BaseClass
{
    public $Show_Echo                       = false;                // TRUE = output testing variables - will not allow iCal to form
    public $Show_Query                      = false;                // TRUE = output the database queries ocurring on this page
    public $table_sessions                  = 'sessions';
    public $table_sessions_checklists       = 'session_checklists';
    
    // ---------- NON-MODIFIABLE VARIABLES ----------
    public $session_record                  = null;
    public $sessions_id                     = 0;
    public $calendar_filename 			    = '';
    public $calendar_title 			        = '';
    public $calendar_url 			        = '';
    public $ical_description                = '';
    public $OBJ_TIMEZONE                    = null;
    
    
    public function  __construct()
    {
        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Description' => 'Create an iCal file allowing user to add session to their calendar',
        );
        
        $this->SetSQL();
        
        $this->OBJ_TIMEZONE         = new General_TimezoneConversion();
        
        $this->SetParameters(func_get_args());
        $this->sessions_id          = ($this->GetParameter(0)) ? $this->GetParameter(0) : 0;
        #$this->approved_to_delete   = ($this->GetParameter(1)) ? $this->GetParameter(1) : false;
        
        
        global $ICAL_FILENAME, $ICAL_TITLE, $ICAL_URL, $ICAL_DESCRIPTION;
        $this->calendar_filename    = $ICAL_FILENAME;
        $this->calendar_title 		= $ICAL_TITLE;
        $this->calendar_url 		= $ICAL_URL;
        $this->ical_description 	= $ICAL_DESCRIPTION;
        
    } // -------------- END __construct --------------
    
    public function SetSQL()
    {
        if (empty($this->SQL)) {
            $this->SQL = Lib_Singleton::GetInstance('Lib_Pdo');
        }
    }
    
    public function ExecuteAjax()
    {
        $this->Show_Query = false;
        ###$this->Execute();
        
        if (!$this->session_record) {
            # GET THE NEEDED FIELDS FOR RECORD
            # ======================================================================
            $record = $this->SQL->GetRecord(array(
                'table'     => $this->table_sessions,
                'keys'      => "*",
                #'where'     => "`sessions_id`=$this->sessions_id AND `active`=1",
                'where'     => "`sessions_id`=$this->sessions_id",
            ));
            //if ($this->Show_Query) echo '<br /><br />QUERY => ' . $this->SQL->Db_Last_Query;
            
            $this->session_record = $record;
            
            if ($this->Show_Echo) {
                echo '<br /><br />QUERY => ' . $this->SQL->Db_Last_Query;
                echo '<br />ARRAY ===> '.ArrayToStr($this->session_record);
            }
        }
        
        $this->FakeLoadEventArray();
        $this->CreateCalendarFile();
    }
    
    public function Execute()
    {
        $this->ShowInstructions();
    }
    
    public function ShowInstructions()
    {
        $eq_Ical        = EncryptQuery("class=Sessions_iCal;v1={$this->sessions_id}");
        $link           = getClassExecuteLink("{$eq_Ical}");
        $btn_download   = MakeButton('positive', 'DOWNLOAD FILE', $link);
        
        $output = "
        <div style='min-width:400px; font-size:12px;'>
            <br />
            You are about to download an iCAL formatted file which may allow you to add this training session to your calendar.
            <br /><br />
            <b>Please Note: calendars do not work with all calendar programs.</b>
            <br /><br />
            <ul>
                <li>Click the [<a href='$link'>DOWNLOAD FILE</a>] button</li>
                <li>Choose the 'Open With' option</li>
                <li>Calendar should open in your default calendar program</li>
                <li>Click the 'SAVE' button in your default calendar program</li>
            </ul>
            <br /><br />
            <center>
                $btn_download
            </center>
        </div>
        ";
        
        echo $output;
    }
    
    public function CloseWindow()
    {
        # FUNCTION :: Closes the window that was opened to create calendar file
        # maybe don't want to do this but instead output instructions.
    }
    
    public function FakeLoadEventArray()
    {
        /*
        if ($this->Show_Echo) {
            echo "date_UTC ===>> $date_UTC";
            echo "\r\n";
            echo "\r\n";
        }
        
        
        $this->session_record['CAL_DATE_GMT']           = $date_UTC;
        $this->session_record['CAL_TIME_GMT']           = $time_UTC;
        $this->session_record['CAL_TIME_END_GMT']       = $time_end_UTC;
        $this->session_record['CAL_DURATION']           = 'PT2H0M0S';
        $this->session_record['CAL_descriptionHTML']    = 'Information about the evet goes here';
        */
    }
    
    public function CreateCalendarFile()
    {
        # ===================================================
        # iCal FIELD REFERENCES
        # http://www.kanzaki.com/docs/ical/
        # ===================================================
        global $USER_LOCAL_TIMEZONE;
        
        $EVENT = $this->session_record;
        
        $this->calendar_title .= " (Sessions ID => {$EVENT['sessions_id']})";
        
        
        
        # convert datetime to ical time format
        # ====================================================
        $time_start     = $EVENT['utc_start_datetime']; // should be set to UTC (or GMT)
        $time_end       = $EVENT['utc_end_datetime']; // should be set to UTC (or GMT)
        $dtstart        = date("Ymd\THi00",strtotime($time_start)) . "Z";
        $dtend          = date("Ymd\THi00",strtotime($time_end)) . "Z";
        
        
        
        # FORMAT DATE & TIME - to local for user
        # ============================
        $input_date_time        = $EVENT['utc_start_datetime'];
        $input_timezone         = 'UTC';
        $output_timezone        = $USER_LOCAL_TIMEZONE;
        $output_format          = 'l, F jS, Y,  g:i a';
        $start_datetime_local   = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
        
        $input_date_time        = $EVENT['utc_end_datetime'];
        $input_timezone         = 'UTC';
        $output_timezone        = $USER_LOCAL_TIMEZONE;
        $output_format          = 'l, F jS, Y,  g:i a';
        $end_datetime_local     = $this->OBJ_TIMEZONE->ConvertDateTimeBetweenTimezones($input_date_time, $input_timezone, $output_timezone, $output_format);
        
        
        
        # re-format descriptive fields if necessary
        # ====================================================
        $title 				= $this->icalFormat("SUMMARY:{$this->calendar_title}",75);
        $place 				= $this->icalFormat("LOCATION:{$this->calendar_url}",75);
        $description        = $this->ical_description;
        
        $swap_array = array(
            '@@start_datetime_local@@'  => $start_datetime_local,
            '@@end_datetime_local@@'    => $end_datetime_local,
            '@@USER_LOCAL_TIMEZONE@@'   => $USER_LOCAL_TIMEZONE,
            '@@calendar_url@@'          => $this->calendar_url,
        );
        
        foreach ($swap_array as $key=>$value) {
            $description = mb_str_replace($key, $value, $description);  // function from mvptools
        }
        
        $descriptionTEXT	= $this->clearHTML($description);
        $descriptionTEXT	= $this->icalFormat("DESCRIPTION:$descriptionTEXT",75);

        $descriptionHTML 	= $this->formatHTML($description);
        $descriptionHTML 	= $this->icalFormat("X-ALT-DESC;FMTTYPE=text/html:$descriptionHTML",75);
        
        
        # build other necessary fields...
        # ====================================================
        $dtstamp 			= date("Ymd\THi00",time())."Z";
        $uid 				= $dtstamp.getmypid()."@witherspoonandheath.com";
        $categories 		= "MEETING";
        
        
        # CREATE THE CALENDAR OUTPUT
        # ====================================================
        header("Content-Type: text/calendar");
        header("Content-Disposition: inline; filename={$this->calendar_filename}");
        echo "BEGIN:VCALENDAR\r\n";
        echo "VERSION:2.0\r\n";
        echo "PRODID:-//Microsoft Corporation//Outlook 11.0 MIMEDIR//EN\r\n";
        echo "METHOD:PUBLISH\r\n";
        echo "BEGIN:VEVENT\r\n";
        echo "DTSTART:".$dtstart."\r\n";
        echo "DTEND:".$dtend."\r\n";
        echo "$title\r\n";
        echo "$descriptionTEXT\r\n";
        echo "$descriptionHTML\r\n";
        echo "$place\r\n";
        echo "TRANSP:OPAQUE\r\n";
        echo "SEQUENCE:0\r\n";
        echo "UID:$uid\r\n";
        echo "DTSTAMP:$dtstamp\r\n";                    // when the iCal file was created
        echo "PRIORITY:5\r\n";
        echo "X-MICROSOFT-CDO-IMPORTANCE:1\r\n";
        echo "CLASS:PUBLIC\r\n";
        echo "CATEGORIES:$categories\r\n";
        echo "END:VEVENT\r\n";
        echo "END:VCALENDAR\r\n";
        
    }
    
    public function icalFormat($str,$len) 
    {
        # ============================================================================ 
        /*
            FUNCTION ::
            
            function icalFormat (string, int)
            Trims whitespace from the string.
            Escapes "," chars (which seems to be necessary for KOrganizer)
            Tests to see if string is longer than specified length.
            If true, returns a chunked string formatted to iCal specification.
            Otherwise, returns a string
        */
        # ============================================================================
        
        $str = str_replace(",", "\,", $str);
        if (strlen($str) > $len) {
            $str = chunk_split($str,$len,"\r\n ");
        }
        $str = trim($str, " ");
        return $str;
    }

    public function formatHTML ($str)
    {
        $search 	= array("<BR>", "<br>", "<Br>", "<bR>", "<BR/>", "<br/>", "<Br/>", "<bR/>", "</BR>", "</br>", "</Br>", "</bR>");
        $replace   	= array('<SPAN LANG="en-us"><B></B></SPAN>', "\\n", "\\n", "\\n", "\\n", "\\n", "\\n", "\\n", "\\n", "\\n", "\\n", "\\n");
        $newphrase 	= str_replace($search, $replace, $str);

        $head = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2//EN">\\n<HTML>\\n<HEAD>\\n<META NAME="Generator" CONTENT="MS Exchange Server version 08.00.0681.000">\\n<TITLE></TITLE>\\n</HEAD>\\n<BODY>\\n';
        $foot = '\\n\\n</BODY>\\n</HTML>';

        return $head . $newphrase . $foot;
    }

    public function clearHTML ($str)
    {
        $search 	= array("<BR>", "<br>", "<Br>", "<bR>", "<BR/>", "<br/>", "<Br/>", "<bR/>", "</BR>", "</br>", "</Br>", "</bR>", "<br />");
        $replace   	= array("\\n", "\\n", "\\n", "\\n", "\\n", "\\n", "\\n", "\\n", "\\n", "\\n", "\\n", "\\n", "\\n");
        $strNew 	= str_replace($search, $replace, $str);
        
        $allowed	= '<1><2>';
        $newphrase 	= strip_tags($strNew, $allowed);

        return $newphrase;
    }

    
}  // -------------- END CLASS --------------