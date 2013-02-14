<?php
// --------- reads the site logs -----------
class SiteLogs
{
    public $Agent_Array         = array();
    public $Bot_Keys            = array();
    public $Ref_Array           = array();
    public $Ref_Sites           = array();
    public $Search_Results      = array();
    public $Page_Array_Sessions = array();
    public $Page_Array_Bots     = array();
    public $Page_Array_Missing  = array();
    public $Site_Pages          = array();
    public $Blocked_Array       = array();
    public $Search_Engines      = array (
                                    'google' => 0,
                                    'yahoo' => 0,
                                    'livesearch' => 0,
                                    'msn' => 0,
                                    'aol' => 0,
                                    'other' => 0
                                    );
    public $Search_Terms        = array();

    public $Log_Dir        = '';
    public $Log_File       = '';
    public $Read_Type      = 0;

    public $Bot_Count      = 0;
    public $Session_Count  = 0;
    public $Search_Count   = 0;
    public $Error_Sessions = '';

    public $Ip_Lookup      = 'http://www.dnsstuff.com/tools/whois.ch?ip=';

    public $SQL = '';


    public function __construct()
    {
        global $ROOT, $SITECONFIG;

        $this->Log_Dir = $ROOT.$SITECONFIG['logdir'];

        include_once "$ROOT/classes/Lib/Pdo.php";
        $this->SQL = new Lib_Pdo;

        $this->SQL->SetTrace(false);  //<<<<<<<<<<---------- Set True for testing ----------<<<<<<<<<<


        $this->Log_File = $this->Log_Dir . '/sitelog.db';
        if (file_exists($this->Log_File)) {
            $this->SQL->ConnectSqLite($this->Log_File);

        } else {
            $this->SQL->ConnectSqLite($this->Log_File);
            $this->SQL->CreateSqLiteTable('sessions', 'id INTEGER PRIMARY KEY ASC, date_time, referrer, remote_addr, user_agent, bot');
            $this->SQL->CreateSqLiteTable('pages', 'id INTEGER PRIMARY KEY ASC, sessions_id, elapsed_time, page');
        }
    }

    public function AddToSiteLog()
    {
        global $PAGE, $SITECONFIG, $ROOT, $SITE_DIR;

        if (AdminRunning()) {
            return;
        }

        $SITE_TRACKING = str_replace('/', '_', "SITE_TRACKING$SITE_DIR");

        if (!isset($_SESSION[$SITE_TRACKING])) {
            $_SESSION[$SITE_TRACKING] = array();
        }

        $pagename = $PAGE['pagename'];

        if (empty($_SESSION[$SITE_TRACKING]['PAGE'][$pagename])) {
            $_SESSION[$SITE_TRACKING]['PAGE'][$pagename] = 1;
            if (empty($_SESSION[$SITE_TRACKING]['START_TIME'])) {
                $_SESSION[$SITE_TRACKING]['START_TIME'] = time();
                $elapsedtime = 0;
            } else {
                $elapsedtime = time() - $_SESSION[$SITE_TRACKING]['START_TIME'];
            }

            if (empty($_SESSION[$SITE_TRACKING]['SESSION_ID'])) {
                $date = date('Y-m-d H:i:s');
                $HTTP_REFERER = Server('HTTP_REFERER');
                $ADDR = Server('REMOTE_ADDR');
                $USER_AGENT = Server('HTTP_USER_AGENT');
                $bot = $this->IsBot($USER_AGENT)? 1 : 0;

                $this->SQL->AddRecord('sessions', 'date_time, referrer, remote_addr, user_agent, bot', "'$date', '$HTTP_REFERER', '$ADDR', '$USER_AGENT', $bot");
                $_SESSION[$SITE_TRACKING]['SESSION_ID'] = $this->SQL->Last_Insert_Id;
            }
            $sessions_id = $_SESSION[$SITE_TRACKING]['SESSION_ID'];
            $this->SQL->AddRecord('pages', 'sessions_id, elapsed_time, page', "$sessions_id, $elapsedtime, '$pagename'");
        }

    }

    private function LoadSitePages()
    {
        global $ROOT, $SITECONFIG;
        $site_page_files = GetDirectory($ROOT.$SITECONFIG['contentdir'],'.php');
        foreach ($site_page_files as $file) {
            $page = RemoveExtension($file);
            $this->Site_Pages[$page] = true;
        }
    }


    //================= GET AGENT / BOT INFORMATION =====================

    public function UpdateBotList()
    {
        if (empty($this->Agent_Array)) {
            $this->LoadAgentArray();
        }

        echo '<ol>';
        foreach ($this->Agent_Array as $key => $value) {
            if (strpos($value, ' BOT') !== false) {
                $count = $this->SQL->Count(array(
                   'table' => 'sessions',
                   'where' => "`user_agent` LIKE '%$key%' AND bot=0"
                ));
                if ($count) {
                    $this->SQL->UpdateRecord(array(
                        'table' => 'sessions',
                        'key_values' => 'bot=1',
                        'where' => "`user_agent` LIKE '%$key%' AND bot=0"
                    ));
                }
                $response = ($count)? ' - <span style="background-color:#ff7">Updated</span>' : ' - OK';
                echo "<li>$key ($value) => $count$response</li>\n";
            }
        }
        echo '</ol>';
    }


    public function IsBot($user_agent)
    {
        $short_name = $this->AgentLookUp($user_agent);
        return strpos($short_name, ' BOT') !== false;
    }

    public function AgentLookUp($user_agent)
    {
        if (empty($this->Agent_Array)) {
            $this->LoadAgentArray();
        }

        foreach ($this->Agent_Array as $key=>$value) {
            if ($key and $user_agent and strpos($user_agent, $key) !== false) {
                return $value;
            }
        }
        return $user_agent;
    }


    private function LoadAgentArray()
    {
        global $LIB;

        $user_agent_data = file("$LIB/user_agent_list.dat");

        foreach ($user_agent_data as $line) {
            $line = trim($line);
            if ($line) {
                list($agent_text, $title) = explode('|', $line);
                $this->Agent_Array[$agent_text] = $title;
                if (strpos($title, ' BOT') !== false) {
                    $this->Bot_Keys[] = $agent_text;
                }
            }
        }
    }


    public function ReadLine($line)  // for converting text-based data
    {
        //TIMExxx|REF|REFERRER PAGE[IP][USER AGENT]
        //TIMExxx|ELAPSED TIME|PAGE NAME
        $RESULT = array();

        $parts = explode('|', trim($line));

        $RESULT['id']         = $parts[0];
        $RESULT['time']       = substr($RESULT['id'], 0, 10);
        $RESULT['date']       = $date = date('Y-m-d H:i:s', $RESULT['time']);

        if ($parts[1] == 'REF') {
            $RESULT['have_ref']   = true;
            $RESULT['referrer']   = strTo($parts[2], '[');
            $RESULT['ip']         = TextBetween('[', ']', $parts[2]);
            $RESULT['user_agent'] = substr(strFrom($parts[2], ']['), 0, -1);
            $RESULT['short_name'] = $this->AgentLookUp($RESULT['user_agent']);
            $RESULT['have_bot']   = strpos($RESULT['short_name'], ' BOT') !== false;
            if ($RESULT['have_bot']) {
                $RESULT['ip'] = 'B' . $RESULT['ip'];
            }
        } else {
            $RESULT['have_ref']      = false;
            $RESULT['elapsed_time']  = $parts[1];
            $RESULT['page']          = $parts[2];
        }

        return $RESULT;
    }


    public function GetPageArray($filename, $read_type=0, $sort=1)
    {
        if (empty($this->Ref_Array) or ($this->Read_Type != $read_type)) {
            $this->GetSiteLogReferences($filename, $read_type);
        }

        if (empty($this->Site_Pages)) {
            $this->LoadSitePages();
        }

        readln($this->Log_File);

        $this->Page_Array_Sessions = array();
        $this->Page_Array_Bots = array();

        while($L = readln()) {
            if (strpos($L, '|REF|') === false) {
                $parts = explode('|', trim($L));

                if (count($parts > 2)) {
                    $id       = $parts[0];
                    $pagename = $parts[2];

                    if (isset($this->Ref_Array[$id])) {
                        if (isset($this->Site_Pages[$pagename])) {
                            // page found
                            if ($this->Ref_Array[$id] == 'S') {
                                if (isset($this->Page_Array_Sessions[$pagename])) {
                                    $this->Page_Array_Sessions[$pagename]++;
                                } else {
                                    $this->Page_Array_Sessions[$pagename] = 1;
                                }
                            } elseif ($this->Ref_Array[$id] == 'B') {
                                if (isset($this->Page_Array_Bots[$pagename])) {
                                    $this->Page_Array_Bots[$pagename]++;
                                } else {
                                    $this->Page_Array_Bots[$pagename] = 1;
                                }
                            }
                        } else {
                            // not found page
                            if (isset($this->Page_Array_Bots[$pagename])) {
                                $this->Page_Array_Missing[$pagename]++;
                            } else {
                                $this->Page_Array_Missing[$pagename] = 1;
                            }
                        }
                    } else {
                        $this->Error_Sessions .= "$L ($id) -- failed<br />";
                    }
                }
            }
        }
        readln('close');

        if ($sort == 1) {
            ksort($this->Page_Array_Sessions);
            ksort($this->Page_Array_Bots);
        } else {
            arsort($this->Page_Array_Sessions);
            arsort($this->Page_Array_Bots);
        }


    }

    public function GetMonthlySessionFiles()
    {
        $this->Monthly_Session_Files = GetDirectory($this->Log_Dir, 'log-20','CONV-');
        return $this->Monthly_Session_Files;
    }

    public function GetMonthArray()
    {
        return $this->SQL->GetFieldValues(array(
            'table' => 'sessions',
            'key'   => 'SUBSTR(date_time, 0,7)'
        ));
    }


    public function GetMonthSessionCount($MONTH)
    {
        $record = $this->SQL->GetRecord(array(
            'table' => 'sessions',
            'keys'  => "SUBSTR(date_time, 0, 7) AS MONTH, COUNT(*)-SUM(bot) AS SESSION_COUNT,
                SUM(bot) AS BOT_COUNT, SUM(LIKE('%search%', referrer)) AS SEARCH",
            'where' => "SUBSTR(date_time, 0, 7) == '$MONTH'"
        ));

        $this->Session_Count = $record['SESSION_COUNT'];
        $this->Bot_Count     = $record['BOT_COUNT'];
        $this->Search_Count  = $record['SEARCH'];
    }

    public function GetMonthlyData()
    {
        $records = $this->SQL->GetArrayAll(array(
            'table' => 'sessions',
            'keys'  => "SUBSTR(date_time, 0, 7) AS MONTH, COUNT(*) - SUM(bot) AS SESSION_COUNT,
                SUM(bot) AS BOT_COUNT, SUM(LIKE('%search%', referrer)) AS SEARCH",
            'where' => '1 GROUP BY MONTH'
        ));

        foreach ($records as $record) {
            extract($record, EXTR_OVERWRITE);
            $RESULT[] = array(
                'month'   => $MONTH,
                'sessions'=> $SESSION_COUNT,
                'bots'    => $BOT_COUNT,
                'search'  => $SEARCH
            );
        }
        return $RESULT;
    }

    public function GetDailyData($MONTH)
    {
        $records = $this->SQL->GetArrayAll(array(
            'table' => 'sessions',
            'keys'  => "SUBSTR(date_time, 0, 10) AS DAY, COUNT(*) - SUM(bot) AS SESSION_COUNT,
                SUM(bot) AS BOT_COUNT, SUM(LIKE('%search%', referrer)) AS SEARCH",
            'where' => "SUBSTR(date_time, 0, 7)='$MONTH' GROUP BY DAY"
        ));

        foreach ($records as $record) {
            extract($record, EXTR_OVERWRITE);
            $RESULT[] = array(
                'day'     => $DAY,
                'sessions'=> $SESSION_COUNT,
                'bots'    => $BOT_COUNT,
                'search'  => $SEARCH
            );
        }
        return $RESULT;
    }


    private function AddSearchTerm($term)
    {
        $term = trim($term);

        if (!empty($term)) {
            if (isset($this->Search_Terms[$term])) {
                $this->Search_Terms[$term]++;
            } else {
                $this->Search_Terms[$term] = 1;
            }
        }
    }

    public function GetSearchData($MONTH, $sort=1)
    {
        $search_ref = array();

        $query = "SELECT `referrer` FROM `sessions` WHERE `referrer` != '' AND SUBSTR(date_time, 0, 7) = '$MONTH'";

        $query_result = $this->SQL->Query("CUSTOM(Search Data)", $query, PDO::FETCH_NUM);

        if ($query_result) {
            while ($row = $query_result->fetch()) {
                $page = $row[0];
                $term = rawurldecode(strtolower($page));
                $search_ref[] = str_replace('+', ' ', $term);
            }
            $query_result->closeCursor();
        }

        foreach ($this->Search_Engines as $key => $value) {
            $this->Search_Engines[$key] = 0;
        }

        $this->Search_Terms = array();

        foreach ($search_ref as $line) {
            // ---------------- AOL ------------------
            if ( strpos($line, 'search.aol') !== false) {

                $this->Search_Engines['aol']++;

                $term = HexDecodeString(trim(strTo(strFrom($line,'encquery='), '&')));
                if (empty($term)) {
                    $term = TextBetween('query=', '&', $line );
                }
                $this->AddSearchTerm($term);

            // ---------------- YAHOO ------------------
            } elseif ( strpos($line, 'yahoo') !== false) {

                $this->Search_Engines['yahoo']++;

                $term = TextBetween('p=', '&', $line );
                $this->AddSearchTerm($term);

            // ---------------- MSN ------------------
            } elseif ( strpos($line, 'search.msn') !== false) {

                $this->Search_Engines['msn']++;
                $term = TextBetween('q=', '&', $line );
                $this->AddSearchTerm($term);

            // ---------------- SEARCH.LIVE ------------------
            } elseif ( strpos($line, 'search.live') !== false) {

                $this->Search_Engines['livesearch']++;
                $term = TextBetween('q=', '&', $line );
                $this->AddSearchTerm($term);

            // ---------------- GOOGLE ------------------
            } elseif ( strpos($line, 'google') !== false) {
                if ( strpos($line, 'translate') === false) {
                    $this->Search_Engines['google']++;
                    $line = str_replace('aq=','x=',$line);
                    $term = TextBetween('q=', '&', $line );
                    $this->AddSearchTerm($term);
                }
            } else {
                $this->Search_Engines['other']++;
            }
        }
        if ($sort == 1) {
            ksort($this->Search_Terms);
        } else {
            arsort($this->Search_Terms);
        }
    }



    public function GetReferralSites($MONTH, $sort=1)
    {

        $query = "SELECT `referrer`, COUNT(*) AS THE_COUNT FROM `sessions` WHERE `referrer` != '' AND SUBSTR(date_time, 0, 7) = '$MONTH' GROUP BY `referrer`";

        $query_result = $this->SQL->Query("GetFreq", $query, PDO::FETCH_NUM);

        $this->Ref_Sites = array();
        if ($query_result) {
            while ($row = $query_result->fetch()) {
                $REF   = $row[0];
                $COUNT = $row[1];
                $item  = str_replace('www.', '', strFrom($REF, 'http://'));
                $item  = strTo($item,'/');
                if (empty($this->Ref_Sites[$item])) {
                    $this->Ref_Sites[$item] = $COUNT;
                } else {
                    $this->Ref_Sites[$item] = $this->Ref_Sites[$item] + $COUNT;
                }
            }
            $query_result->closeCursor();
        }

        if ($sort == 1) {
            ksort($this->Ref_Sites);
        } else {
            arsort($this->Ref_Sites);
        }

    }



    // ===================================================================
    //                             REPORTS
    // ===================================================================

    //---------DONE---------
    public function OutputMonthlyReport()
    {
        echo '<table  align="center" class="monthly_report" cellspacing="1" cellpadding="0"><tbody>';
        echo "\n<tr><th>Month</th><th>Sessions</th><th>Bots</th><th>Search</th></tr>\n";

        $monthly = $this->GetMonthlyData();
        foreach ($monthly as $m) {
            $sessions = number_format($m['sessions']);
            $bots     = number_format($m['bots']);
            $search   = number_format($m['search']);
            echo "
            <tr>
                <td class=\"monthly_report_month\">{$m['month']}</td>
                <td>$sessions</td>
                <td>$bots</td>
                <td>$search</td>
            </tr>";
        }
        echo "</tbody></table>\n";

    }

    public function OutputDailyReport($MONTH)
    {
        echo '<table  align="center" class="monthly_report" cellspacing="1" cellpadding="0"><tbody>';
        echo "\n<tr><th>Month</th><th>Sessions</th><th>Bots</th><th>Search</th></tr>\n";

        $dailies = $this->GetDailyData($MONTH);
        foreach ($dailies as $d) {
            $sessions = number_format($d['sessions']);
            $bots     = number_format($d['bots']);
            $search   = number_format($d['search']);
            echo "
            <tr>
                <td class=\"monthly_report_month\">{$d['day']}</td>
                <td>$sessions</td>
                <td>$bots</td>
                <td>$search</td>
            </tr>";
        }
        echo "</tbody></table>\n";

    }

    //---------DO---------
    public function OutputMissingPageReport($filename)
    {
        $FILE = $this->Log_Dir . "/$filename";
        if (file_exists($FILE)) {
            $lines = array_reverse(file($FILE));
            echo '<table id="missing_report" class="monthly_report" align="center" cellspacing="1"><tbody>';
            echo "<tr><th>Date</th><th>Page</th><th>Refer</th><th>IP</th><th>Agent</th></tr>";
            foreach ($lines as $line) {
                if (!empty($line)) {
                    $line = str_replace('|', '</td><td>', htmlentities(trim($line)));
                    echo "<tr><td>$line</td></tr>\n";
                }
            }
            echo '</tbody></table>';
        }
    }


    //---------DO---------
    public function OutputMissingPageReportWithinLogs($filename)
    {
        $this->GetPageArray($filename, 0);

        if (empty($this->Page_Array_Missing)) {
            return;
        }

        echo "\n<h2>Missing Pages</h2>\n";
        echo '<table align="center" class="monthly_report" cellspacing="1" cellpadding="0"><tbody>';
        echo "\n<tr><th>Page</th><th>Count</th></tr>\n";
        $total = 0;
         foreach ($this->Page_Array_Missing as $page => $freq) {
            $total += $freq;
            $freq = number_format($freq);
            echo "<tr><td><b>$page</b></td><td>$freq</td></tr>\n";
        }
        $total = number_format($total);
        echo "<tr><th class=\"monthly_report_month\">Total</th><th align=\"right\">$total</th></tr>\n";
        echo "</tbody></table>\n";
    }

    //---------DO---------
    public function OutputPageReport($MONTH, $sort=1)
    {

        if ($sort == 1) {
            $sort_dir = 'ASC';
        } else {
            $sort_dir = 'DESC';
        }

        $records_sessions = $this->SQL->GetFreq(array(
            'table' => 'pages',
            'key'  => 'page',
            'where' => "SUBSTR(date_time, 0, 7) = '$MONTH' AND bot=0",
            'joins' => 'LEFT JOIN sessions ON sessions.id = pages.sessions_id',
            'order' => $sort,
            'order_direction' => $sort_dir
        ));

        $records_bots= $this->SQL->GetFreq(array(
            'table' => 'pages',
            'key'  => 'page',
            'where' => "SUBSTR(date_time, 0, 7) = '$MONTH' AND bot=1",
            'joins' => 'LEFT JOIN sessions ON sessions.id = pages.sessions_id',
            'order' => $sort,
        ));



        echo '<table align="center" class="monthly_report" cellspacing="1" cellpadding="0"><tbody>';
        echo '<tr><td colspan="2"><h2>Sessions</h2></td></tr>';
        echo "\n<tr><th>Page</th><th>Count</th></tr>\n";

        if (!empty($records_sessions)) {
            $total = 0;
            foreach ($records_sessions as $page => $freq) {
                $total += $freq;
                $freq = number_format($freq);
                echo "<tr><td><b>$page</b></td><td>$freq</td></tr>\n";
            }
            $total = number_format($total);

            echo "<tr><th class=\"monthly_report_month\">Total</th><th align=\"right\">$total</th></tr>\n";
        }

        if (count($records_bots)>0) {
            echo '<tr><td colspan="2"><h2>Bots</h2></td></tr>';

            echo "\n<tr><th>Page</th><th>Count</th></tr>\n";
            $total = 0;

            foreach ($this->Page_Array_Bots as $page => $freq) {
                $total += $freq;
                $freq = number_format($freq);
                echo "<tr><td><b>$page</b></td><td>$freq</td></tr>\n";
            }

            $total = number_format($total);
            echo "<tr><th class=\"monthly_report_month\"><b>Total</b></th><th align=\"right\">$total</th></tr>\n";
        }
        echo "</tbody></table>\n";
    }

    //---------DO---------
    public function OutputSessionArray($MONTH, $read_type, $show_agent, $ref_only, 
        $start, $rows, $where_var, $where_type, $where_value)
    {
    

        // read_type => 0=All|1=No Bots|2=Bots Only

        $month_where = "SUBSTR(date_time, 0, 7) = '$MONTH'";
        $where = $month_where;
        
        if ($where_var) {            
            $types = array('EQ' => '=', 'IN' => 'LIKE', 'GT' => '>', 'LT' => '<');
            if ($where_type == 'IN') {
                $where_value = "%$where_value%";
            }
            $where_value = $this->SQL->QuoteValue($where_value);
            $where .= " AND `$where_var` {$types[$where_type]} $where_value";
        }
        
        if ($read_type == 1) {
            $where .= ' AND bot!=1';
        } elseif ($read_type == 2) {
            $where .= '  AND bot=1';
        }

        //'sessions' => 'id, date_time, referrer, remote_addr, user_agent, bot'
        //'pages' =>  'id, sessions_id, elapsed_time, page'

        $offset = $start - 1;
        
        if ($where_var == 'page') {
            $start_session_id_record = $this->SQL->GetArrayAll(array(
                'table' => 'pages',
                'keys'  => 'sessions.id',
                'where' => $where,
                'order' => "sessions_id, elapsed_time LIMIT 1 OFFSET $offset",
                'joins' => 'LEFT JOIN sessions ON sessions.id = pages.sessions_id',
            ));
        } else {
            $start_session_id_record = $this->SQL->GetArrayAll(array(
                'table' => 'sessions',
                'keys'  => 'id',
                'where' => "$where LIMIT 1 OFFSET $offset"
            ));
        }
        
        if (empty($start_session_id_record)) {
            return;
        }
        $start_session_id = ArrayValue($start_session_id_record[0], 'id');


        $offset = $offset + $rows - 1;
        
        if ($where_var == 'page') {
            $end_session_id_record = $this->SQL->GetArrayAll(array(
                'table' => 'pages',
                'keys'  => 'sessions.id',
                'where' => $where,
                'order' => "sessions_id, elapsed_time LIMIT 1 OFFSET $offset",
                'joins' => 'LEFT JOIN sessions ON sessions.id = pages.sessions_id',
            ));
        } else {
            $end_session_id_record = $this->SQL->GetArrayAll(array(
                'table' => 'sessions',
                'keys'  => 'id',
                'where' => "$where LIMIT 1 OFFSET $offset"
            ));
        }


        $end_session_id = ArrayValue($end_session_id_record[0], 'id');

        if ($ref_only) {
            $where .= ' AND elapsed_time=0';
        }

        $where .= " AND sessions_id >= $start_session_id";
        if ($end_session_id) {
            $where .= " AND sessions_id <= $end_session_id";
            $where = strFrom($where, "$month_where AND "); // do not need month, have session ids
        }

        
        $records = $this->SQL->GetArrayAll(array(
            'table' => 'pages',
            'keys'  => 'elapsed_time, page, date_time, referrer, remote_addr, user_agent, bot',
            'where' => $where,
            'order' => 'sessions_id, elapsed_time',
            'joins' => 'LEFT JOIN sessions ON sessions.id = pages.sessions_id',
        ));

        if (empty($records)) {
            return;
        }


        $count = $start - 1;

        echo '<table class="log_session_report" cellspacing="1" cellpadding="0"><tbody>';
        echo "\n<tr><th>No.</th><th>Date - Time</th><th>Time<br />(sec)</th><th align=\"left\">Page or Referrer[IP]</th></tr>\n";

        foreach ($records as $row) {

            if ($row['elapsed_time'] == 0) {
                $ip_link    = ($row['remote_addr'])? "<a target=\"_blank\" href=\"{$this->Ip_Lookup}{$row['remote_addr']}\">{$row['remote_addr']}</a>" : '';
                $referrer   = $row['referrer'];
                $ref_title  = htmlentities($row['referrer']);
                $short_ref  = preg_replace('/(\?|;|=).+/', '', $row['referrer']);
                $short_ref  = htmlentities($short_ref);

                if ($show_agent) {
                    $agent_name = $row['user_agent'];
                } else {
                    $agent_name = $this->AgentLookUp($row['user_agent']);
                }

                $ref_link   = ($referrer)? "<a target=\"_blank\" title=\"$ref_title\" href=\"$ref_title\">$short_ref</a>" : '';

                $class = ($row['bot'] == 1)? 'bot_ref' : 'ref';

                $count++;
                echo "
                    <tr class=\"$class\">
                        <td>$count.</td><td>{$row['date_time']}</td>
                        <td align=\"right\">REF</td>
                        <td>$ref_link&nbsp;[$ip_link]&nbsp;[$agent_name]</td>
                    </tr>\n";
            }

            if (!$ref_only) {
                echo "
                <tr>
                    <td colspan=\"2\"></td>
                    <td align=\"right\">{$row['elapsed_time']}</td>
                    <td>{$row['page']}</td>
                </tr>\n";
            }


        }
        echo "</tbody></table>\n";

    }

    //---------DO---------
    function OutputAllBlocked($type=0)
    {
        //type 0 all, 1 day list, 2 summary
        $files = GetDirectory($this->Log_Dir, 'block-');
        rsort($files);

        echo '<table class="log_session_report" align="center"><tbody>';

        if ($type < 2) {
            echo '<tr><th>IP</th><th>Page</th><th>Time</th></tr>';
        }

        foreach ($files as $file) {

            if ($type < 2) {
                echo "<tr><td colspan=\"3\" align=\"center\"><h3>$file</h3></td></tr>\n";
            }
            $lines = file("{$this->Log_Dir}/$file");
            TrimArray($lines);
            foreach ($lines as $line) {
                if (!empty($line)) {
                    $items    = explode('|',$line);
                    $pagename = $items[1];


                    if (isset($this->Blocked_Array[$pagename])) {
                        $this->Blocked_Array[$pagename]++;
                    } else {
                        $this->Blocked_Array[$pagename] = 1;
                    }
                    if ($type < 2) {
                        echo "<tr><td>{$items[0]}</td><td>$pagename</td>";
                        if (!empty($items[2])) {
                            echo "<td>{$items[2]}</td></tr>\n";
                        } else {
                            echo "<td></td></tr>\n";
                        }
                    }
                }
            }
        }
        if (($type==0) OR ($type==3)) {
            echo "<tr><td colspan=\"3\" align=\"center\"><h3>SUMMARIES</h3></td></tr>\n";
            echo "<tr><th colspan=\"2\">Page</th><th>Count</th></tr>\n";
            arsort($this->Blocked_Array);
            $total = 0;
            foreach ($this->Blocked_Array as $pagename=>$count) {
                $total += $count;
                echo "<tr><td colspan=\"2\">$pagename</td><td align=\"right\">$count</td></tr>\n";
            }
            $total = number_format($total);
            echo "<tr><th colspan=\"2\">Total</th><th align=\"right\">$total</th></tr>\n";
        }

        echo "</tbody></table>\n";
    }


    //---------DO---------
    function OutputSearch($MONTH, $sort=1)
    {
        $this->GetSearchData($MONTH, $sort);

        echo '<table class="log_session_report" cellspacing="1" align="center"><tbody>';
        echo '<tr><th>Term</th><th>Count</th></tr>';
        foreach ($this->Search_Terms as $term => $freq) {
            $freq = number_format($freq);
            echo "<tr><td>$term</td><td align=\"right\">$freq</td></tr>";
        }
        echo '</tbody></table><br /><br />';
        echo '<table class="log_session_report" align="center"><tbody>';

        $total = 0;
        foreach ($this->Search_Engines as $key=>$value) {
            $total += $value;
            $value = number_format($value);
            echo "<tr><th align=\"left\">$key</th><td align=\"right\">$value</td></tr>";
        }
        $total = number_format($total);
        echo "<tr><th align=\"left\">TOTAL</th><th align=\"right\">$total</th></tr>";
        echo '</tbody></table>';
    }

    //---------DO---------
    function OutputReferralSites($MONTH, $sort=1)
    {
        $this->GetReferralSites($MONTH, $sort);

        echo '<table align="center" class="monthly_report" cellspacing="1" cellpadding="0"><tbody>';
        echo "\n<tr><th>Referring Site</th><th>Count</th></tr>\n";

        $total = 0;
        foreach ($this->Ref_Sites as $site => $freq) {
            $total += $freq;
            $freq = number_format($freq);
            echo "<tr><td style=\"text-align:left;\">$site</td><td>$freq</td></tr>\n";
        }
        $total = number_format($total);
        echo "<tr><th align=\"left\">TOTAL</th><th align=\"right\">$total</th></tr>";
        echo '</tbody></table>';
    }

    //---------DO---------
    function OutputBotsInMonth($MONTH, $sort=1)
    {
        $order = ($sort==1)? 'user_agent' : 'BOT_COUNT DESC';
        $array = $this->SQL->QueryToArray("SELECT count(*) as BOT_COUNT, user_agent FROM `sessions`
            WHERE bot=1 AND SUBSTR(date_time, 0, 7) == '$MONTH' GROUP BY user_agent ORDER BY $order");

        $new_array = array();
        foreach ($array as $i => $line) {
            $short_name = $this->AgentLookUp($line['user_agent']);
            if (array_key_exists($short_name, $new_array)) {
                $new_array[$short_name] += $line['BOT_COUNT'];
            } else {
                $new_array[$short_name] = $line['BOT_COUNT'];
            }
        }

        if ($sort == 1) {
            ksort($new_array);
        }

        $array = array();
        foreach ($new_array as $key => $value){
            $array[] = array('Bot' => $key, 'Count' => $value);
        }

        echo $this->SQL->OutputTable($array, '', 'align="center" class="monthly_report"');
    }

    //---------DO---------
    function OutputCustomQuery($QUERY)
    {
        $array = $this->SQL->QueryToArray($QUERY);
        echo $this->SQL->OutputTable($array, '', 'align="center" class="monthly_report"');
    }


    //---------DONE---------

    public function GetLogFileRefCounts($filename)
    {
        $RESULT = 0;
        readln($this->Log_Dir . '/' . $filename);

        while($L = readln()) {
            if (strpos($L, '|REF|') !== false) {
                $RESULT++;
            }
        }
        readln('close');
        return $RESULT;
    }


    public function ConvertSiteLogFile($filename)
    {
        $RESULT = 0;
        readln($this->Log_Dir . '/' . $filename);

        $SESSION_ID = 0;
        $row_time = microtime(true);
        echo '.&nbsp;';
        while ($row = readln()) {
            if (strIn($row, '|REF|')) {
                $record = $this->ReadLine($row);
                $bot = $record['have_bot']? 1 : 0;
                //$check = $this->SQL->GetValue('sessions', 'date_time', "date_time='{$record['date']}'" );
                //if (!$check) {
                    $this->SQL->AddRecord(
                        'sessions',
                        'date_time, referrer, remote_addr, user_agent, bot',
                        "'{$record['date']}', '{$record['referrer']}', '{$record['ip']}', '{$record['user_agent']}', $bot"
                    );
                    $RESULT++;
                    $SESSION_ID = $this->SQL->GetLastInsertId();

                    if ($RESULT % 10 == 0) {
                        if ($RESULT % 1000 == 0) {
                            $row_time = microtime(true) - $row_time;
                            echo ' ' . number_format($RESULT,0) . ' (t=' . number_format($row_time, 4) . ' sec)<br />';
                            $row_time =  microtime(true);
                        }
                        echo '.&nbsp;';
                    }


                //}
            } else {
                //TIMExxx|ELAPSED TIME|PAGE NAME
                $record = array();

                $parts = explode('|', trim($row));

                $record['id']         = $parts[0];
                $record['time']       = substr($record['id'], 0, 10);
                $record['date']       = $date = date('Y-m-d H:i:s', $record['time']);

                $record['elapsed_time']  = $parts[1];
                $record['page']          = $parts[2];
                if (!$SESSION_ID) {
                    $SESSION_ID = $this->SQL->GetValue('sessions', 'id', "date_time='{$record['date']}'" );
                }
                if ($SESSION_ID) {
                    //$check = $this->SQL->GetValue('pages', 'id', "page='{$record['page']}' AND sessions_id ='$SESSION_ID'" );
                    //if (!$check) {
                    $this->SQL->AddRecord(
                        'pages',
                        'sessions_id, elapsed_time, page',
                        "$SESSION_ID, {$record['elapsed_time']}, '{$record['page']}'"
                    );
                    //}
                }
            }

        }
        return $RESULT;

    }

    //---------DONE---------

/* ------- conversion page setup ---------
if ($AJAX) {
    apache_setenv('no-gzip', 1);
    ini_set('zlib.output_compression', 0);
    ini_set('implicit_flush', 1);
    for ($i = 0; $i < ob_get_level(); $i++) { ob_end_flush(); }
    ob_implicit_flush(1);
}

echo '<a class="menubutton" href="/AJAX/test">Process</a>';
if (!$AJAX) return;

echo "<h1>Processing Logs...</h1>";

include_once "$LIB/class.SiteLogs.php";
$SL = new SiteLogs;
$SL->ConvertSiteLogs();
*/

    public function ConvertSiteLogs()
    {
        $this->SQL->Db_Want_Query = false;
        set_time_limit(0);
        $START_TIME = microtime(true);

        $files = $this->GetMonthlySessionFiles();

        // one month each run
        $do_conversion = true;
        foreach ($files as $file) {
            $file_date = str_replace(array('log-', '.dat'), '', $file);
            $this->GetMonthSessionCount($file_date);
            if ($this->Session_Count == 0) {
                if ($do_conversion) {
                    $count = number_format($this->GetLogFileRefCounts($file), 0);
                    echo "<h3>Converting: $file ($count)</h3>\n";
                    $count = number_format($this->ConvertSiteLogFile($file),0);
                    echo "<p><span style=\"background-color:#ff7;\">Converted: <b>$file</b>, Sessions: $count</span></p>\n";
                    $do_conversion = false;
                } else {
                    $count = number_format($this->GetLogFileRefCounts($file), 0);
                    echo "<div>Needs Conversion: <b>$file</b> ($count)</div>\n";
                }
            } else {
                echo "<div>Already Converted: <b>$file</b>: "
                . number_format($this->Session_Count, 0) . ', '
                . number_format($this->Bot_Count, 0) . ', '
                . number_format($this->Search_Count, 0) . "</div>\n";
            }
        }
        $time = number_format((microtime(true) - $START_TIME)/60, 3);
        echo "<p>Elapsed Time: $time minutes</p>";
    }
    
}
