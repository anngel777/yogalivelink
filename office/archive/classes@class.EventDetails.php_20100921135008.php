<?php

// FILE: class.EventDetails.php

class EventDetails
{

    public $Months = array(
        '00'=>'&nbsp;',
        '01'=>'[T~MONTH_JANUARY]', '02'=>'[T~MONTH_FEBRUARY]', '03'=>'[T~MONTH_MARCH]', '04'=>'[T~MONTH_APRIL]',
        '05'=>'[T~MONTH_MAY]', '06'=>'[T~MONTH_JUNE]', '07'=>'[T~MONTH_JULY]', '08'=>'[T~MONTH_AUGUST]',
        '09'=>'[T~MONTH_SEPTEMBER]', '10'=>'[T~MONTH_OCTOBER]', '11'=>'[T~MONTH_NOVEMBER]', '12'=>'[T~MONTH_DECEMBER]'
    );

    public $Location   = '';
    public $Event_Type = '';
    public $Event_Geo  = '';
    public $Events_Id  = '';
    public $Event_Record = '';

    public $Event_Setups = '';

    public $Site_Dir   = '';
    
    public $Step_Array = array(
        1 => 'Start',
        2 => 'Contact Details',
        3 => 'Registration',
        4 => 'Payment',
        5 => 'Review',
        6 => 'Complete',
    );
    
    public $Step_Translation = 'Step'; // translation id or text for the word "Step"


    public function __construct($events_id = '', $event_type = '', $event_geo = '')
    {
        $this->SetEventsId($events_id);
        $this->SetEventType($event_type);
        $this->SetEventGeo($event_geo);
    }

    public function SetEventsId($events_id)
    {
        $this->Events_Id = intOnly($events_id);
        if ($this->Events_Id) {
            $this->Event_Record = db_GetRecord('events', '*', "`events_id`=$events_id");
            if ($this->Event_Record) {
                //
            }
        }

    }

    public function SetLocation($event_location)
    {
        $this->Location = $event_location;
    }


    public function SetEventType($event_type)
    {
        $this->Event_Type = $event_type;
    }

    public function SetEventGeo($event_geo)
    {
        $this->Event_Geo = $event_geo;
    }


    public function GetPath($event_setups_id)
    {
        global $SITE_DIR;
        $this->Site_Dir = $SITE_DIR;
        $path = "$SITE_DIR/$this->Event_Type/$this->Event_Geo/$this->Events_Id/$event_setups_id/";
        return substr(preg_replace('/\/+/', '/', $path), 0, -1);
    }
    
    
    # FUNCTION CALL
    #     $current_step - the current step
    #     $this->Step_Array - array containing the text for each of the steps
    #     $this->Step_Translation - this is the translation id or text for the word "Step"
    # =========================================
        //echo $ED->GetProcessSteps($current_step);
    # =========================================

    public function GetProcessSteps($current_step)
    {
        $output = '';
        foreach ($this->Step_Array AS $step => $val) {
            if ($current_step == $step) {
                $status_class = 'stepbox_on';
            } elseif ($current_step > $step) {
                $status_class = 'stepbox_completed';
            } else {
                $status_class = 'stepbox_off';
            }

            $output .= "
                <div class=\"stepwrap\">
                    <div>$this->Step_Translation $step</div>
                    <div class=\"stepbox\"><div class=\"$status_class\">&nbsp;</div></div>
                    <div>$val</div>
                </div>";
        }
        $output .= '<div style="clear:both;"></div>';

        return $output;
    }

    public function DisplayCalendar()
    {
        $CAL = new EventCalendar;

        $TABS = new Tabs;

        $max_date = $CAL->Max_Date;
        
        $month1 =  (int) date('n');
        $month2 = ($month1 == 12)? 1 : $month1 + 1;
        $month3 = ($month2 == 12)? 1 : $month2 + 1;
        $month2_out = sprintf('%02d', $month2);
        $month3_out = sprintf('%02d', $month3);
        
        $year1 = date('Y');
        $year2 = ($month1 == 12)? $year1 + 1 : $year1;
        $year3 = ($month2 == 12)? $year2 + 1 : $year2;

        // $TABS->AddTab($CAL->Months[date('n')], $CAL->GetCalendar(date('Y-m-d')));
        // $TABS->AddTab($CAL->Months[date('n', strtotime('+1 month'))], $CAL->GetCalendar('+1 month'));
        // $TABS->AddTab($CAL->Months[date('n', strtotime('+2 month'))], $CAL->GetCalendar('+2 month'));
        
        $TABS->AddTab($CAL->Months[$month1], $CAL->GetCalendar(date('Y-m-d')));
        $TABS->AddTab($CAL->Months[$month2], $CAL->GetCalendar("$year2-$month2_out-01"));
        $TABS->AddTab($CAL->Months[$month3], $CAL->GetCalendar("$year3-$month3_out-01"));

        $TABS->OutputTabs();
    }

    public function DisplayEventLinkMenu()
    {
        $LINK = '@@SITEDIR@@';
        if (empty($this->Event_Type)) {
            $items = db_GetFieldValues('event_setups', 'event_type',
                'active=1 AND event_date_end > NOW()', 'L');
            echo "<h1>Select Event Type</h1>";
            foreach ($items as $item) {
                $uitem = strtoupper($item);
                echo "<a class=\"stdbutton\" href=\"$LINK/$item/\">$uitem</a>\n";
            }
            return;

        } elseif (empty($this->Event_Geo)) {
            echo "<p class=\"center\"><a class=\"stdbuttoni\" href=\"$LINK/\">Event Types</a></p>\n";
            $items = db_GetFieldValues('event_setups', 'geo',
                "active=1 AND event_date_end > NOW() AND event_type='$this->Event_Type'", 'L');
            echo "<h1>Select GEO</h1>";
            foreach ($items as $item) {
                $uitem = strtoupper($item);
                echo "<a class=\"stdbutton\" href=\"$LINK/$this->Event_Type/$item/\">$uitem</a>\n";
            }
            return;

        } elseif (empty($this->Events_Id)) {
            echo "<p class=\"center\"><a class=\"stdbuttoni\" href=\"$LINK/\">Geos</a></p>\n";
            $items = db_GetAssocArray(
                'event_setups',
                'events.events_id',
                'events.event_name' ,
                "event_setups.active=1 AND event_setups.event_date_end > NOW()
                    AND event_setups.event_type='$this->Event_Type' AND event_setups.geo='$this->Event_Geo'",
                '',
                'LEFT JOIN events ON events.events_id=event_setups.events_id', 'event_name'
            );
            echo "<h1>Select Event</h1>";
            foreach ($items as $key => $value) {
                echo "<a class=\"stdbutton\" href=\"$LINK/$this->Event_Type/$this->Event_Geo/$key/\">$value</a>\n";
            }
            return;
        }
    }



    public function GetEventListings($EVENTS_ID)
    {
        # NEED TO PUT IN THE OVERRIDE FOR THE HEADER TEXT
        $Events_Id  = ($EVENTS_ID) ? $EVENTS_ID : $this->Events_Id;
        $result     = db_GetRecord('events', 'override_event_list_header', "`events_id`='$Events_Id'");
        $OVERRIDE   = $result['override_event_list_header'];
        
        $HEADING = ($OVERRIDE) ? $OVERRIDE : '[T~CE_S_0078]';
        $OUTPUT = "
        <div id='EVENT_LIST_HEADING'>$HEADING</div>
        <div id='event_listing'>";

        $COUNTRIES = $this->GetCountries();

        foreach ($COUNTRIES as $code=>$name) {

            $OUTPUT .= "<div class=\"geo_header\">$name</div>";
            $OUTPUT .= $this->OutputEvents($code);
            $OUTPUT .= "<div class=\"section_break\"></div>";
            $OUTPUT .= "<div class=\"section_break\"></div>";

        }

        $OUTPUT .= '</div>';
        return $OUTPUT;
    }


     public function OutputRegistrationSupport($ret_type='print')
     {

        $support_contact_list = $this->OutputSupportContacts($this->Events_Id);

        $output = <<<LBL_RSB
        <div id="REGISTRATION_SUPPORT_BOX">
            <div id="REGISTRATION_SUPPORT_BOX_1"
               style="background-image: url(/channelevents/images/event_unique/@@BOX_HEADER_IMAGE@@);">[T~CE_S_0077]
            </div>
            <div id="REGISTRATION_SUPPORT_BOX_2">
                <table>
                <tr>
                <td valign="top"></td>
                <td valign="top"><div style="font-size:10px;">$support_contact_list</div></td>
                </tr>
                </table>
            </div>
        </div>
LBL_RSB;

        if ($ret_type == 'return') {
            return $output;
        } else {
            print $output;
        }
    }


    public function OutputSupportContacts($EVENTS_ID='')
    {
        $Events_Id = ($EVENTS_ID) ? $EVENTS_ID : $this->Events_Id;
        $result = db_GetRecord('events', 'support_contact_list', "`events_id`='$Events_Id'");
        $result = $result['support_contact_list'];

        $OUTPUT = '';
        $list = explode("\n", $result);
        if ($result!='') {
            foreach ($list as $row) {
                if (strpos($row, '|') !== false) {
                    list($lineDisplay, $lineDescription) = explode("|", $row);

                    # CHECK IF EMAIL ADDRESS
                    if (strpos($lineDescription , '@') !== false) {
                        $lineDescription = trim($lineDescription);
                        $lineDescription = '<a href="mailto:' . $lineDescription. '">' . $lineDescription . '</a>';
                    }
                    $OUTPUT .= "
                        <strong>$lineDisplay</strong><br/>
                        $lineDescription<br/><br/>";
                }
            }

        } else {
            $OUTPUT = '';
        }
        return $OUTPUT;
    }

    public function OutputEvents($code)
    {
        $LOCATION = strtoupper($code);
        $order = "event_date_start ASC";

        $conditions = "`event_type`='$this->Event_Type'
            AND `events_id`=$this->Events_Id
            AND geo='$this->Event_Geo'
            AND `location_country`='$LOCATION'
            AND `display_status`='Showing'
            AND `approval_status`='Approved' AND active=1";

        $this->Event_Setups = db_GetArrayAll('event_setups', '*', $conditions, $order);

        $OUTPUT = '';
        if (count($this->Event_Setups) > 0) {
            foreach ($this->Event_Setups as $event_setup) {
                $OUTPUT .= $this->OutputEvent($event_setup);
            }
        } else {
            $OUTPUT = "
            <div class='geo_event_wrap'>
                <div class='geo_event'>
                    <span class='geo_location'><strong>NO EVENTS AVAILABLE AT CURRENT TIME</strong></span>
                </div>
            </div>";
        }
        return $OUTPUT;
    }


    public function OutputEvent($EVENT_SETUP)
    {
        global $FORM_COUNTRY_CODES, $EVENT_PATH, $SITE_DIR;

        $ADDRESS = '';
        $DateFormat = ($EVENT_SETUP['date_format_listing']) ? $EVENT_SETUP['date_format_listing'] : '<M> <D>';
       
        $DATE_START = '';
        if (isset($EVENT_SETUP['event_date_start'])) {
            $DATE_START = explode('-', $EVENT_SETUP['event_date_start']);

            $swap = array (
                '<D>' => "[T~DAY_{$DATE_START[2]}]",
                '<Y>' => $DATE_START[0],
                '<M>' => $this->Months[$DATE_START[1]]
            );
            
            $DATE_START = astr_replace($swap, $DateFormat);

        }

        $DATE_END = '';
        if (isset($EVENT_SETUP['event_date_end'])) {
            $DATE_END = explode('-', $EVENT_SETUP['event_date_end']);

            $swap = array (
                '<D>' => "[T~DAY_{$DATE_END[2]}]",
                '<Y>' => $DATE_END[0],
                '<M>' => $this->Months[$DATE_END[1]]
            );
            
            $DATE_END = astr_replace($swap, $DateFormat);
        }

        #  ======================= DISPLAY COUNTRY NAME AT START OF LIST  =======================
        $country = $this->CheckCountryForTranslation($EVENT_SETUP['location_country']);

        # ======================== SETUP THE LOCATION INFORMATION ========================
        $EVENT_SETUPS_ID = $EVENT_SETUP['event_setups_id'];

        $path = $this->GetPath($EVENT_SETUPS_ID);

        $DATE_DISPLAY = ($EVENT_SETUP['event_date_start'] == $EVENT_SETUP['event_date_end']) ?
                $DATE_START : "$DATE_START - $DATE_END";
        $LOCATION_NAME = "<strong>{$EVENT_SETUP['location_name']}</strong>";
        if ($ADDRESS) {
            $LOCATION_NAME .= ", $ADDRESS";
        }


        $CITY_STATE = "({$EVENT_SETUP['location_city']}, {$EVENT_SETUP['location_state']})";
        $CITY_STATE = str_replace(array('(, ', ', )'), array('(', ')'), $CITY_STATE);
        $CITY_STATE = str_replace("()", "", $CITY_STATE);

        #  ======================= OUTPUT THE LOCATION INFORMATION  =======================
        if ($EVENT_SETUP['event_list_display_override']) {

            $OUTPUT = <<<LBL1
        <div class="geo_event_wrap">
            <div class="geo_event">
                <a href="$path/event_details" style="text-decoration:none;">
                <img src="/channelevents/images/ico_arrow.gif" alt="" border="0" />
                <span class="geo_location">{$EVENT_SETUP['event_list_display_override']}</span>
                </a>
            </div>
        </div>
LBL1;

        } else {

            $OUTPUT = <<<LBL2
        <div class="geo_event_wrap">
            <div class="geo_event">
                <a href="$path/event_details" style="text-decoration:none;">
                <img src="/channelevents/images/ico_arrow.gif" alt="" border="0" />
                <span class="geo_date" style="width:300px;">$DATE_DISPLAY</span>
                <span class="geo_location">$LOCATION_NAME</span>
                <span class="geo_country">$CITY_STATE</span>
                </a>
            </div>
        </div>
LBL2;

        }

        return $OUTPUT;
    }

public function CheckCountryForTranslation($COUNTRY) {
    switch ($COUNTRY) {
        case 'DE':
            $COUNTRY = '[T~COUNTRY_DE]';
            break;
        case 'AT':
            $COUNTRY = '[T~COUNTRY_AT]';
            break;
        case 'KR':
            $COUNTRY = '[T~COUNTRY_KR]';
            break;
        case 'VN':
            $COUNTRY = '[T~COUNTRY_VN]';
            break;
        case 'RU':
            $COUNTRY = '[T~COUNTRY_RU]';
            break;
        default:
            $COUNTRY = GetCountryNameOrCode($COUNTRY);
            break;
    }
    return $COUNTRY;
}

    public function GetCountries()
    {
        #GET ALL COUNTRIES FOR EVENT SETUPS
        global $FORM_COUNTRY_CODES;

        $WHERE = "event_type='$this->Event_Type'
                AND `events_id`=$this->Events_Id
                AND geo='$this->Event_Geo'
                AND `display_status`='Showing'
                AND `approval_status`='Approved'
                AND active=1";

        $COUNTRY_LIST = db_GetFieldValues('event_setups', 'location_country', $WHERE);

        $COUNTRIES = array();
        Form_LoadCountryCodes();

        $FORM_COUNTRY_CODES['RU'] = '[T~COUNTRY_RU]';
        $FORM_COUNTRY_CODES['UA'] = '[T~COUNTRY_UA]';
        $FORM_COUNTRY_CODES['TW'] = '[T~COUNTRY_TW]';
        $FORM_COUNTRY_CODES['VN'] = '[T~COUNTRY_VN]';

        $FORM_COUNTRY_CODES['DE'] = '[T~COUNTRY_DE]';
        $FORM_COUNTRY_CODES['AT'] = '[T~COUNTRY_AT]';
        $FORM_COUNTRY_CODES['KR'] = '[T~COUNTRY_KR]';

        foreach ($COUNTRY_LIST as $country_code) {
            $country_name = $FORM_COUNTRY_CODES[$country_code];
            $COUNTRIES[$country_code] = $country_name;
        }

        return $COUNTRIES;
    }


    public function FormatDisplay($TITLE, $CONTENT_IDENTIFIER) {
        $OUTPUT = <<< LBL3
            <tr>
                <td valign="top"><div class="section_break"></div>
                    <div class="event_header">$TITLE</div>
                </td>
                <td align="left" valign="top">
                    <div class="section_break"></div>
                    <div class="event_info">$CONTENT_IDENTIFIER</div>
                </td>
            </tr>
LBL3;
        return $OUTPUT;
    }

    public function ProcessLineBr($LINE='', $END='', $NO_LINE='') {
        $LINE .= ($LINE != '') ? $END : $NO_LINE;
        return $LINE;
    }

    public function CreateParkingDetails($EVENT) {
        return $this->ProcessLineBr($EVENT['parking_details'], '', '');
    }

    public function CreateHotelDetails($EVENT) {
        return $this->ProcessLineBr($EVENT['hotel_information'], '', '');
    }

    public function CreateTransportationDetails($EVENT) {
        return $this->ProcessLineBr($EVENT['transportation_information'], '', '');
    }

    public function CreateLocation($EVENT_SETUP) {
        $LOCATION = '';
        $LOCATION .= $this->ProcessLineBr($EVENT_SETUP['location_city'], ', ');
        $LOCATION .= $this->ProcessLineBr($EVENT_SETUP['location_state'], ', ');
        $LOCATION .= $this->ProcessLineBr($this->CheckCountryForTranslation($EVENT_SETUP['location_country']), '');

        return $LOCATION;
    }

    public function CreateAddressTitle($EVENT_SETUP) {
        //$ADDRESS = "[D~location_city], [D~location_state], [D~location_country]";
        $ADDRESS = $EVENT_SETUP['location_city'];
        if (!empty($EVENT_SETUP['location_state'])) {
            $ADDRESS .= (!empty($ADDRESS)) ? ', ' : '';
            $ADDRESS .= $EVENT_SETUP['location_state'];
        }
        if (!empty($EVENT_SETUP['location_country'])) {
            $ADDRESS .= (!empty($ADDRESS)) ? ', ' : '';
            $ADDRESS .= $this->CheckCountryForTranslation($EVENT_SETUP['location_country']);
        }

        return $ADDRESS;
    }

    public function CreateAddressEventLocation($EVENT_SETUP) {
        global $FORM_COUNTRY_CODES;
        $ADDRESS = '<table border="0"><tr><td valign="top" style="font-size:11px; font-family:verdana,arial;">';
        $ADDRESS .= '<strong>' . $this->ProcessLineBr($EVENT_SETUP['location_name'], '<br />') . '</strong>';
        $ADDRESS .= $this->ProcessLineBr($EVENT_SETUP['location_address_1'], '<br />');
        $ADDRESS .= $this->ProcessLineBr($EVENT_SETUP['location_address_2'], '<br />');
        $ADDRESS .= $this->ProcessLineBr($EVENT_SETUP['location_city'], ', ');
        $ADDRESS .= $this->ProcessLineBr($EVENT_SETUP['location_state'], ', ');
        $ADDRESS .= $this->ProcessLineBr($this->CheckCountryForTranslation($EVENT_SETUP['location_country']), '<br />');
        $ADDRESS .= $this->ProcessLineBr($EVENT_SETUP['location_postal_code'], '<br />');
        $ADDRESS .= "</td>";

        $ADDRESS .= "</tr></table>";
        return $ADDRESS;
    }

    public function CreateDate($EVENT_SETUP)
    {
        $DateFormat = ($EVENT_SETUP['date_format']) ? $EVENT_SETUP['date_format'] : '<M> <D>, <Y>';
        $DATE = '';
        if (isset($EVENT_SETUP['event_date_start'])) {
            $DATE = explode('-', $EVENT_SETUP['event_date_start']);

            $swap = array (
                '<D>' => "[T~DAY_{$DATE[2]}]",
                '<Y>' => $DATE[0],
                '<M>' => $this->Months[$DATE[1]]
            );

            $DATE = astr_replace($swap, $DateFormat);

        }
        return $DATE;
    }

    public function CreateDateEnd($EVENT_SETUP)     
    {
        $DateFormat = ($EVENT_SETUP['date_format']) ? $EVENT_SETUP['date_format'] : '<M> <D>, <Y>';
        $DATE = '';
        if (isset($EVENT_SETUP['event_date_end'])) {
            $DATE = explode('-', $EVENT_SETUP['event_date_end']);
            $swap = array (
                '<D>' => "[T~DAY_{$DATE[2]}]",
                '<Y>' => $DATE[0],
                '<M>' => $this->Months[$DATE[1]],
            );

            $DATE = astr_replace($swap, $DateFormat);
        }
        return $DATE;
    }

    public function CreateAgenda($EVENT) {
        $list = explode("\n", $EVENT['agenda_items']);
        if ($EVENT['agenda_items']!='') {
            $OUTPUT = "<table class='event_agenda_table' border='0'>";
            for ($y=0; $y<count($list); $y++) {
                if (strpos($list[$y], '|') !== false)
                {
                    $agenda = explode("|", $list[$y]);
                    $agendaDisplay = ($agenda[1]!='') ? "$agenda[0] - $agenda[1]" : $agenda[0];
                    $agendaDescription = ArrayValue($agenda, 2);
                } else {
                    $agendaDisplay = $list[$y];
                    $agendaDescription = '';
                }

                $OUTPUT .= "
                <tr>
                    <td class='agenda_time' width='200'>$agendaDisplay</td>
                    <td class='agenda_description'>$agendaDescription</td>
                </tr>";
            }
            $OUTPUT .= "</table>";
        } else {
            $OUTPUT = '';
        }
        return $OUTPUT;
    }

    public function CreateLocationMapURL($EVENT) {
        if ($EVENT['location_map_url']!='') {        
            //$OUTPUT = "<a href='{$EVENT['location_map_url']}' target='_blank'>{$EVENT['location_map_url']}</a>\n";
            $title = strTo($EVENT['location_map_url'], '?');
            $OUTPUT = "<a href=\"{$EVENT['location_map_url']}\" target=\"_blank\">$title</a>\n";

        } else {
            $OUTPUT = '';
        }
        return $OUTPUT;
    }

    public function CreateCourses($EVENT) {
        $list = explode("\n", $EVENT['courses']);
        if ($EVENT['courses']!='') {
            $OUTPUT = "";
            for ($y=0; $y<count($list); $y++) {
                if (strpos($list[$y], '|') !== false)
                {
                    $item = explode("|", $list[$y]);
                    $courseName = $item[0];
                    $courseDescription = $item[1];
                } else {
                    $courseName = $list[$y];
                    $courseDescription = '';
                }

                $OUTPUT .= "
                <strong>$courseName</strong><br/>
                $courseDescription<br/><br/>";
            }
        } else {
            $OUTPUT = '';
        }
        return $OUTPUT;
    }

    public function CreateFiles($EVENT) {
        # Displayed Filename|File Extension|Description|URL
        $list = explode("\n", trim($EVENT['file_items']));
        
        //echo "<pre>(TESTING)EVENT:\n{$EVENT['file_items']}</pre>";
        //echo ArrayToStr($list);
        
        if ($EVENT['file_items']!='') {
            $OUTPUT = "";
            $OUTPUT .= "<table cellpadding='5' cellspacing='0'>";
            for ($y=0; $y<count($list); $y++) {
                $item = explode("|", $list[$y]);

                $image_array = array(
                    'pdf' => 'ico_pdf.gif',
                    'doc' => 'ico_word.jpg',
                    'xls' => 'ico_xls.gif',
                    'ppt' => 'ico_ppt.gif',
                    'map' => 'ico_map.jpg',
                    'txt' => 'ico_txt.jpg'
                );

                $img = ArrayValue($image_array, strtolower($item[1]));
                if (empty($img)) {
                    $img = 'ico_txt.jpg';
                }

                $displayName = $item[0];
                $description = $item[2];
                $img = "<a href='$item[3]' target='_blank'><img src='/channelevents/images/ico/$img' alt='Document' height='50px' border='0'/></a>";
                $url = "<a href='$item[3]' target='_blank'>[download]</a>";

                $OUTPUT .= "
                    <tr>
                    <td valign='top'>$img</td>
                    <td valign='top'><strong>$displayName</strong><br/>$description<br/><br/>$url</td>
                    </tr>
                    <tr><td colspan='2'>&nbsp;</td></tr>
                    ";
            }
            $OUTPUT .= "</table>";
        } else {
            $OUTPUT = '';
        }
        return $OUTPUT;
    }

    public function CreateOtherItems($EVENT) {
        $list = explode("\n", $EVENT['other_items']);
        if ($EVENT['other_items']!='') {
            $OUTPUT = "";
            for ($y=0; $y<count($list); $y++) {
                $item = explode("|", $list[$y]);
                $OUTPUT .= "<div style='font-weight:bold; font-size:14px; color:#24486c; border-bottom:1px solid #24486c;'>{$item[0]}</div><br/>";
                //$OUTPUT .= html_entity_decode($item[1]);
                $OUTPUT .= (html_entity_decode($item[1]));
                $OUTPUT .= "<br/><br/>";
            }
        } else {
            $OUTPUT = '';
        }
        return $OUTPUT;
    }

    public function CreateSponsors($EVENT) {
        $OUTPUT = "";
        
        $s_platinum      = array();
        $s_platinum_link = array();
        $s_gold          = array();
        $s_gold_link     = array();
        $s_sliver_link   = array();
        
        $EVENT_SETUPS_ID = $EVENT['event_setups_id'];
        $keys = "sponsor_opportunity_selections.*, sponsor_companies.company_name, sponsor_companies.website";
        $where = "sponsor_opportunities_id=4 AND locations LIKE '%{$EVENT_SETUPS_ID}%' AND sponsor_opportunity_selections.status='approved' AND sponsor_opportunity_selections.active=1";
        $order = "";
        $joins = "LEFT JOIN sponsor_companies ON sponsor_opportunity_selections.wh_sid = sponsor_companies.wh_sid";

        
        $sponsors = db_GetArrayAll('sponsor_opportunity_selections', $keys, $where, $order, $joins);
        foreach ($sponsors AS $sponsor) {
            $website = $sponsor['website'];
            if ($website and strpos($website, 'http') === false) {
                $website = "http://$website";
            }
            
            switch ($sponsor['level']) {
                case 'platinum':
                    $cnt = count($s_platinum) + 1;
                    $s_platinum[$cnt] = ($sponsor['booth_title'] != '') ? $sponsor['booth_title'] : $sponsor['company_name'];
                    $s_platinum_link[$cnt] = $website;
                    break;
                case 'gold':
                    $cnt = count($s_gold) + 1;
                    $s_gold[$cnt] = ($sponsor['booth_title'] != '') ? $sponsor['booth_title'] : $sponsor['company_name'];
                    $s_gold_link[$cnt] = $website;
                    break;
                case 'sliver':
                    $cnt = count($s_sliver) + 1;
                    $s_sliver[$cnt] = ($sponsor['booth_title'] != '') ? $sponsor['booth_title'] : $sponsor['company_name'];
                    $s_silver_link[$cnt] = $website;
                    break;
            }
        }

        if (count($s_platinum) > 0) {
            $OUTPUT .= "<div style='border-bottom:0px dashed #bbbbbb; font-weight: bold;'>Platinum Level Sponsors</div>";
            $OUTPUT .= "<ul style='color: #6699CC;'>";
            foreach ($s_platinum AS $k => $name) {
                $website = $s_platinum_link[$k];
                $name = ($website)? "<a target=\"_blank\" href=\"$website\">$name</a>" : $name;
                $OUTPUT .= "<li>{$name}</li>";
            }
            $OUTPUT .= "</ul>";
        }
        
        if (count($s_gold) > 0) {
            $OUTPUT .= "<div style='border-bottom:0px dashed #bbbbbb; font-weight: bold;'>Gold Level Sponsors</div>";
            $OUTPUT .= "<ul style='color: #6699CC;'>";
            foreach ($s_gold AS $k => $name) {
                $website = $s_gold_link[$k];
                $name = ($website)? "<a target=\"_blank\" href=\"$website\">$name</a>" : $name;
                $OUTPUT .= "<li>{$name}</li>";
            }
            $OUTPUT .= "</ul>";
        }
        
        if (count($s_sliver) > 0) {
            $OUTPUT .= "<div style='border-bottom:0px dashed #bbbbbb; font-weight: bold;'>Silver Level Sponsors</div>";
            $OUTPUT .= "<ul style='color: #6699CC;'>";
            foreach ($s_sliver AS $k => $name) {
                $website = $s_silver_link[$k];
                $name = ($website)? "<a target=\"_blank\" href=\"$website\">$name</a>" : $name;
                $OUTPUT .= "<li>{$name}</li>";
            }
            $OUTPUT .= "</ul>";
        }
        
        return $OUTPUT;
    }


    public function SplitPipedData($DATA) {

        $list = explode("\n", $DATA);

        $output = array();

        foreach ($list as $key => $row) {
            $row = trim($row);
            if (strpos($row, '|') !== false) {

                $items = explode('|', $row);
                $output[$key] = $items;

            } else {
                $output[$key][0] = $row;
            }
        }

        return $output;
    }


    public function CreateSponsorsPage($PAGEDATA)
    {
        # NAME|LOGO|DESCRIPTION|LINK

        $array = $this->SplitPipedData($PAGEDATA);
        $HEADER_STYLE = "style='font-size:14px; background-color:#dddddd; padding:5px;'";
        $TEXT_STYLE = "style='font-size:12px;'";
        $IMAGE_STYLE = "style='padding-right:20px;'";

        $OUTPUT = "<table>";
        foreach ($array as $row) {

            $count = count($row);

            if ($count == 1)
            {
                $OUTPUT .= "
                    <tr><td colspan='2'><br/><br/></td></tr>
                    <tr><td colspan='2' $HEADER_STYLE>{$row[0]}</td></tr>
                    <tr><td colspan='2'><br/></td></tr>";
            } else {
                $OUTPUT .= "
                    <tr>
                    <td valign='top' $TEXT_STYLE><img src='/channelevents/images/event_logos/{$row[1]}.jpg' alt='{$row[0]}' border='0' width='200' height='72'  $IMAGE_STYLE/></td>
                    <td valign='top' $TEXT_STYLE>{$row[2]}<br/><br/><a href='http://{$row[3]}' target='_blank'>{$row[3]}</a></td>
                    </tr>
                    <tr><td colspan='2'><br/><br/></td></tr>";
            }
        }
        $OUTPUT .= "</table>";

        return $OUTPUT;
    }


    public function CreateMenuLinks() {
        global $PAGE_PATH;
        $result = db_GetValue('events', 'website_pages', "events_id=$this->Events_Id");

        if ($result) {
            $array = $this->SplitPipedData($result);

            $MENU = '';
            foreach ($array as $row) {
                $display = $row[0];
                $mnu_display = ('!' == substr($display, 0, 1)) ? substr($display, 1) : "[T~$display]";

                $pos = strpos($PAGE_PATH, 'sponsor/');
                if ($pos===false) {
                    $path = "@@SITEDIR@@@@EVENTPATH@@";
                } else {
                    $path = "@@SITEDIR@@@@PAGEPATH@@";
                }
$page = $row[1];
                $page_id = str_replace(array(';', '=', '&'), '_', trim($row[1]));
                $MENU .= "<a id=\"menu_$page_id\" href=\"$path/$page\">$mnu_display</a> | ";
            }
            $MENU = substr($MENU, 0, -2);
        } else {
            $MENU = '';
        }
        return $MENU;
    }


    public function CreateCustomLinks($EVENT) {
        $list = explode("\n", $EVENT['email_custom_links']);
        if ($EVENT['email_custom_links']!='') {
            $OUTPUT = "";
            for ($y=0; $y<count($list); $y++) {
                if (strpos($list[$y], '|') !== false)
                {
                    $item = explode("|", $list[$y]);
                    $display = $item[0];
                    $link = $item[1];
                } else {
                    $display = $list[$y];
                    $link = '';
                }

                $OUTPUT .= "<a href='$link' target='_blank' style='text-decoration:none;'>$display</a><br />";
            }
        } else {
            $OUTPUT = '';
        }
        return $OUTPUT;
    }





    public function OutputConvergysLivePerson() {

        $OUTPUT = '
    <!--BEGIN LivePerson Button Code x-->
    <table border="0" cellspacing="2" cellpadding="2">
      <tr>
        <td align="center"></td>
        <td align="center">
    <a id="_lpChatBtn" href="http://sales.liveperson.net/hc/56727252/?cmd=file&amp;file=visitorWantsToChat&amp;site=56727252&amp;byhref=1&amp;SESSIONVAR!skill=NAResellerPrograms-English&amp;imageUrl=http://www.intel.com/plt/cd/channel/channel/irc/images/livechat/eng/" target="chat56727252" >
    <img src="http://sales.liveperson.net/hc/56727252/?cmd=repstate&amp;site=56727252&amp;channel=web&amp;ver=1&amp;imageUrl=http://www.intel.com/plt/cd/channel/channel/irc/images/livechat/eng/&amp;kill=NAResellerPrograms-English" name="hcIcon" border="0" alt="Live Person Chat" /></a>
        </td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td align="center">
    <!--
    <div style="margin-top:5px;">
    <span style="font-size:10px; font-family:Arial, Helvetica, sans-serif;">
    <a href="http://solutions.liveperson.com/live-chat" style="text-decoration:none; color:#000" target="_blank"><b>Live Chat</b></a>
    <span style="color:#000"> by </span>
    <a href="http://www.liveperson.com/" style="text-decoration:none; color:#FF9900" target="_blank">LivePerson</a>
    </span>
    </div>
    -->
        </td>
      </tr>
    </table>
    <!--END LivePerson Button code -->
    ';

        return $OUTPUT;
    }

}
