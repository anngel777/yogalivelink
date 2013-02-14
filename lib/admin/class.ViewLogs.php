<?php
// --------- reads the site logs -----------
class ViewLogs
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


    public function __construct()
    {
        global $ROOT, $SITECONFIG;
        $this->Log_Dir = $ROOT.$SITECONFIG['logdir'];
        $this->LoadAgentArray();
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

    private function AgentLookUp($user_agent)
    {
        foreach ($this->Agent_Array as $key=>$value) {
            if ($key and $user_agent and strpos($user_agent, $key) !== false) {
                return $value;
            }
        }
        return $user_agent;
    }


    public function ReadLine($line)
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


    public function GetFileSessionCount($filename)
    {
        $this->Log_File  = $this->Log_Dir . "/$filename";

        $this->Bot_Count      = 0;
        $this->Session_Count  = 0;
        $this->Search_Count   = 0;

        readln($this->Log_File);

        while($L = readln()) {

            if (strpos($L, '|REF|') !== false) {

                if (ArrayItemsWithinStr($this->Bot_Keys, strFrom($L, ']['))) {
                    $this->Bot_Count++;
                } else {
                    $this->Session_Count++;
                }

                $page = TextBetween('|REF|', '[', $L);
                if ((stripos($page, 'search') !== false) and ( strpos($page, 'translate') === false)) {
                    $this->Search_Count++;
                }
            }
        }
        readln('close');
    }

    public function GetSiteLogReferences($filename, $read_type=0)
    {
        // read_type 0=all, 1=sessions only, 2=bots only, 3=none
        $this->Log_File  = $this->Log_Dir . "/$filename";
        $this->Read_Type = $read_type;

        if ($read_type < 3) {
            $this->Ref_Array = array();
        }

        $this->Bot_Count      = 0;
        $this->Session_Count  = 0;

        readln($this->Log_File);

        while($L = readln()) {

            if (strpos($L, '|REF|') !== false) {

                $id = strTo($L, '|');

                if (ArrayItemsWithinStr($this->Bot_Keys, strFrom($L, ']['))) {
                    $this->Bot_Count++;
                    if (($read_type == 0) or ($read_type == 2)) {
                        $this->Ref_Array[$id] = 'B';
                    }

                } else {
                    $this->Session_Count++;
                    if (($read_type == 0) or ($read_type == 1)) {
                        $this->Ref_Array[$id] = 'S';
                    }
                }
            }
        }
        readln('close');
        $this->Ref_Array = array_reverse($this->Ref_Array, true);
    }

    public function GetLogArray($filename, $read_type=0, $start=1, $rows=100)
    {
        if (empty($this->Ref_Array) or ($this->Read_Type != $read_type)) {
            $this->GetSiteLogReferences($filename, $read_type);
        }

        $search_array = array_slice($this->Ref_Array, $start-1,  $start + $rows - 1, true);
        readln($this->Log_File);

        $temp_array  = array();
        $final_array = array();

        while($L = readln()) {
            $line_array = $this->ReadLine($L);
            if (isset($search_array[$line_array['id']])) {
                $temp_array[] = $line_array;
            }
        }
        foreach ($search_array as $search=>$type) {
            $id_array = array();
            foreach ($temp_array as $row) {
                if ($row['id'] == $search) {
                    $id_array[] =  $row;
                }
            }
            $final_array = array_merge($final_array, $id_array);
        }

        if ($read_type < 3) {
            $this->Search_Results = $final_array;
        }
        readln('close');
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
                    $pagename  = $parts[2];

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
        $this->Monthly_Session_Files = GetDirectory($this->Log_Dir, 'log-20');
        return $this->Monthly_Session_Files;
    }


    public function GetMonthlyData()
    {
        $files = $this->GetMonthlySessionFiles();
        $RESULT = array();
        foreach ($files as $file) {
            $this->GetFileSessionCount($file);
            $month = TextBetween('log-', '.', $file);
            $RESULT[] = array(
                'month' => $month,
                'sessions' => $this->Session_Count,
                'bots'=> $this->Bot_Count,
                'search'=> $this->Search_Count
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

    public function GetSearchData($filename, $sort=1)
    {
        $this->Log_File  = $this->Log_Dir . "/$filename";

        $search_ref = array();

        readln($this->Log_File);
        while($L = readln()) {
            if (strpos($L, '|REF|') !== false) {
                $page = TextBetween('|REF|', '[', $L);
                if (stripos($page, 'search') !== false) {
                    $term = rawurldecode(strtolower($page));
                    $search_ref[] = str_replace('+', ' ', $term);
                }
            }
        }
        readln('close');

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
    
    
    
    public function GetReferralSites($filename, $sort=1)
    {
        $this->Log_File  = $this->Log_Dir . "/$filename";

        readln($this->Log_File);
        while($L = readln()) {
            if (strpos($L, '|REF|') !== false) {
                $REF = TextBetween('|REF|', '[', $L);
                if (stripos($REF, 'http://') !== false) {
                    $item = str_replace('www.', '', strFrom($REF, 'http://'));
                    $item = strTo($item,'/');
                    if (empty($this->Ref_Sites[$item])) {
                        $this->Ref_Sites[$item] = 1;
                    } else {
                        $this->Ref_Sites[$item] = $this->Ref_Sites[$item] + 1;
                    }
                }
            }
        }
        readln('close');
        
        if ($sort == 1) {
            ksort($this->Ref_Sites);
        } else {
            arsort($this->Ref_Sites);
        }
    }



    // ===================================================================
    //                             REPORTS
    // ===================================================================
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


    public function OutputMissingPageReport($filename)
    {
        $FILE = $this->Log_Dir . "/$filename";
        if (file_exists($FILE)) {
            $lines = array_reverse(file($FILE));
            echo '<table id="missing_report" class="monthly_report" align="center" cellspacing="1"><tbody>';
            echo "<tr><th>Date</th><th>Page</th><th>Refer</th><th>IP</th><th>Agent</th></tr>";
            foreach ($lines as $line) {
                if (!empty($line)) {
                    $line = str_replace('|', '</td><td>', trim($line));
                    echo "<tr><td>$line</td></tr>\n";
                }
            }
            echo '</tbody></table>';
        }
    }


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

    public function OutputPageReport($filename, $sort=1)
    {
        $this->GetPageArray($filename, 0, $sort);

        if (empty($this->Page_Array_Sessions)) {
            return;
        }
        
        echo '<table align="center" class="monthly_report" cellspacing="1" cellpadding="0"><tbody>';
        echo '<tr><td colspan="2"><h2>Sessions</h2></td></tr>';
        echo "\n<tr><th>Page</th><th>Count</th></tr>\n";

        $total = 0;
        foreach ($this->Page_Array_Sessions as $page => $freq) {
            $total += $freq;
            $freq = number_format($freq);
            echo "<tr><td><b>$page</b></td><td>$freq</td></tr>\n";
        }
        $total = number_format($total);

        echo "<tr><th class=\"monthly_report_month\">Total</th><th align=\"right\">$total</th></tr>\n";
        //echo "</tbody></table>\n";

        //echo "\n<h2>Bots</h2>\n";
        echo '<tr><td colspan="2"><h2>Bots</h2></td></tr>';
        //echo '<table class="monthly_report" cellspacing="1" cellpadding="0"><tbody>';

        echo "\n<tr><th>Page</th><th>Count</th></tr>\n";
        $total = 0;
        foreach ($this->Page_Array_Bots as $page => $freq) {
            $total += $freq;
            $freq = number_format($freq);
            echo "<tr><td><b>$page</b></td><td>$freq</td></tr>\n";
        }
        $total = number_format($total);
        echo "<tr><th class=\"monthly_report_month\"><b>Total</b></th><th align=\"right\">$total</th></tr>\n";
        echo "</tbody></table>\n";
    }

    public function OutputSessionArray($filename, $read_type=0, $show_agent=false, $ref_only=false)
    {
        $count = 0;
        $last_row_id = '';

        $this->GetSiteLogReferences($filename, $read_type);

        if (empty($this->Search_Results)) {
            return;
        }

        echo '<table class="log_session_report" cellspacing="1" cellpadding="0"><tbody>';
        echo "\n<tr><th>No.</th><th>Date - Time</th><th>Time<br />(sec)</th><th align=\"left\">Page or Referrer[IP]</th></tr>\n";

        foreach ($this->Search_Results as $row) {
            if ($row['id'] != $last_row_id) {
                $count++;
                $last_row_id = $row['id'];
            }

            if ($row['have_ref']) {
                $ip_link    = ($row['ip'])? "<a target=\"_blank\" href=\"{$this->Ip_Lookup}{$row['ip']}\">{$row['ip']}</a>" : '';
                //$referrer   = urlencode($row['referrer']);
                $referrer   = $row['referrer'];
                $ref_title  = htmlentities($row['referrer']);
                $short_ref  = preg_replace('/(\?|;|=).+/', '', $row['referrer']);
                $short_ref  = htmlentities($short_ref);
                $ref_link   = ($referrer)? "<a target=\"_blank\" title=\"$ref_title\" href=\"$referrer\">$short_ref</a>" : '';
                $agent_name = $show_agent?  $row['user_agent'] : $row['short_name'];
                $class = ($row['have_bot'])? 'bot_ref' : 'ref';

                echo "
                    <tr class=\"$class\">
                        <td>$count.</td><td>{$row['date']}</td>
                        <td align=\"right\">REF</td>
                        <td>$ref_link&nbsp;[$ip_link]&nbsp;[$agent_name]</td>
                    </tr>\n";
            } else {
                if (!$ref_only) {
                    echo "
                    <tr>
                        <td colspan=\"2\"></td>
                        <td align=\"right\">{$row['elapsed_time']}</td>
                        <td>{$row['page']}</td>
                    </tr>\n";
                }
            }

        }
        echo "</tbody></table>\n";

    }

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


    function OutputSearch($filename, $sort=1)
    {
        $this->GetSearchData($filename, $sort);

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
    
    function OutputReferralSites($filename, $sort=1)
    {
        $this->GetReferralSites($filename, $sort);
        
        echo '<table align="center" class="monthly_report" cellspacing="1" cellpadding="0"><tbody>';
        echo "\n<tr><th>Referring Site</th><th>Count</th></tr>\n";

        $total = 0;
        foreach ($this->Ref_Sites as $site => $freq) {
            $total += $freq;
            $freq = number_format($freq);
            echo "<tr><td><b>$site</b></td><td>$freq</td></tr>\n";
        }
        $total = number_format($total);
        echo "<tr><th align=\"left\">TOTAL</th><th align=\"right\">$total</th></tr>";
        echo '</tbody></table>';        
    }



}
