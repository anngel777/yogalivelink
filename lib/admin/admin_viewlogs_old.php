<?php
SetPost('viewtype showagent refonly');
SetGet('ALLBLOCK');

$AgentCodes = array(
    'MSIE 6.0'=>'MSIE-6.0',
    'MSIE 7.0'=>'MSIE-7.0',
    'Firefox'=>'Firefox',
    'Safari'=>'Safari',
    'WebTV'=>'WebTV',
    'msnbot'=>'--MSN bot--',
    'Googlebot'=>'--Google bot--',
    'Yahoo'=>'--Yahoo bot--',
    'CazoodleBot'=>'--Cazoodle bot--',
    'Ask Jeeves/Teoma'=>'--Ask Jeeves bot--',
    'ia_archiver'=>'--Archive.org bot--',
    'entireweb'=>'--speedy_spider bot--',
    'WebAlta'=>'--WebAlta bot--',
    'globalspec.com/Ocelli'=>'--globalspec bot--',
    'Baiduspider'=>'-- Baiduspider bot --',
    'searchme.com'=>'-- searchme.com bot --',
    'OpenAcoon'=>'-- OpenAcoon bot --',
    'ScoutJet'=>'-- ScoutJet bot --',
    'DoCoMo'=>'-- DoCoMo bot --',
    'panscient'=>'-- Prancient bot --',
    'Java/' => '-- Java bot --',
    'Mail.Ru/' => '-- Mail.Ru bot --',
    'Wget/' => '-- Wget bot --',
    'ShopWiki' => '-- ShopWiki bot --',
    'W3C_Validator' => '-- W3C Validator bot --',
    'Bot'  => '-- bot --',
    'diavola' => '-- bot --',
    'Crawler' => '-- Crawler bot --',
    'crawler' => '-- Crawler bot --',
    'POE-Component-' => '-- Bad bot --'
);


$logfiledir = $ROOT.$SITECONFIG['logdir'];

echo $DOCTYPE_XHTML;
echo "<head>
<title>View Log Files - {$SITECONFIG['sitename']}</title>
";
?>
<style type="text/css">
/* ============================== STYLES ================================ */
body {background-color:#fff; font-family:  Arial, Helvetica, sans-serif; margin:0px;}
body.print{background-color:#fff}
#container{padding:0.5em;}
#header{color:#036; border-bottom:2px solid #036;}
#pagetitle {font-size:1.2em; font-weight:bold;}
/* =================BUTTONS================= */
a.stdbutton {font-size:9pt; text-decoration:none; padding:0.2em 0.4em; display:inline;
          background-color:#eee; color:#000; border:1px solid #888; display:inline;}
a.stdbutton:active {border-color:#345 #cde #def #678; }
a.stdbutton:hover {background-color: #888; color:#fff; cursor:pointer;}

.fileview{background-color:#eee; border:1px solid #888; padding:0.5em;}
.fileview pre {background-color:#fff; padding:0.25em;}
table {background-color:#ccc;}
td {background-color:#fff; padding:1px 3px;}
th {background-color:#eef; padding:1px 3px; font-weight:bold;}
td.page {background-color:#ffb; font-weight:bold;}
#missingtable{white-space:nowrap;}
.left {text-align:left;}
.right {text-align:right;}
tr.ref td {background-color:#8f9;}

/* ============================= END STYLES ============================ */
</style>
<script type="text/javascript" src="/lib/mvp.js"></script>
<script type="text/javascript">

</script>
</head>
<body>
<div id="container">
<div id="header">

<?php
printqn("<form method=`post` action=`$THIS_PAGE?SITELOG=2`>");
echo '<a class="stdbutton" style="float:right;" href="'.$THIS_PAGE.'?LOGOUT=1">Logout</a>';
echo '<a class="stdbutton" style="float:right;" href="'.$THIS_PAGE.'">Admin</a>';
printqn("<span id=`pagetitle`>{$SITECONFIG['sitename']} &mdash; Session Log Files</span>&nbsp;&nbsp;");
//======================Get Files==========================

$files = GetDirectory($logfiledir,'.dat');
rsort($files);

SetPost('FILE START ROWS NEXT FIRST PREVIOUS');

if (!$ROWS)  $ROWS = 100;
if (!$START or $FIRST) $START = 1;
if ($NEXT) $START = $START + $ROWS;
if ($PREVIOUS) $START = max(1,$START - $ROWS);

printqn("<select name=`FILE`>");
//         <option value=`0`>----Choose File----</option>");
//--write file select
$count = 0;
foreach ($files as $file) {
    $count++;
    if (substr($file,0,4)!='log-'){
        $link =  $file;
        $title=  $file;
        $selected = ($FILE==$file)? ' selected="selected"' : '';
    } else {
        $selected = ("log-$FILE.dat"==$file)? ' selected="selected"' : '';
        $link = substr($file,4,7);
        $title = date("F Y",strtotime("$link-01"));
    }
    printqn("<option value=`$link`$selected>$count. $title</option>");
}
echo '</select>';

printqn("<p>Row Start: <input type=`text` name=`START` value=`$START` size=`5` />&nbsp;&nbsp;");
printqn("Sessions: <input type=`text` name=`ROWS` value=`$ROWS` size=`5` />&nbsp;&nbsp;");
if ($START > 1) printqn("<input type=`submit` name=`FIRST` value=`First` />&nbsp;");
if ($START > 1) printqn("<input type=`submit` name=`PREVIOUS` value=`&lt;&ndash; Previous` />&nbsp;");


$c1 = ($viewtype=='NOBOTS')? 'checked="checked"' : '';
$c2 = ($viewtype=='BOTS')? 'checked="checked"' : '';
$c4 = ($viewtype=='SEARCH')? 'checked="checked"' : '';

$c3 = (empty($c1) and empty($c2) and empty($c4))? $c3 = 'checked="checked"' : '';

$sacheck = ($showagent==1)? 'checked="checked"' : '';
$refcheck = ($refonly==1)? 'checked="checked"' : '';

print <<<LBLFORM
<input type="submit" name="NEXT" value="Next &ndash;&gt;" />&nbsp;
<input type="submit" name="SHOW" value="Show" />&nbsp;&nbsp;
<input type="radio" name="viewtype" value="NOBOTS" $c1 />No Bots
<input type="radio" name="viewtype" value="BOTS" $c2 />Bots Only
<input type="radio" name="viewtype" value="ALL" $c3 />All
<input type="radio" name="viewtype" value="SEARCH" $c4 />Search
&nbsp;&nbsp;<input type="checkbox" name="showagent" value="1" $sacheck /> Show Agent Info
&nbsp;&nbsp;<input type="checkbox" name="refonly" value="1" $refcheck /> Show Ref Only
&nbsp;&nbsp;<a class="stdbutton" href="$THIS_PAGE?SITELOG=1{$SV}ALLBLOCK=1">View All Blocked</a></p>
</form>
</div>
<!-- end header -->
LBLFORM;

//======================Display File==========================

$RefArray = array();

if ($ALLBLOCK) {
    $files = GetDirectory($logfiledir,'block-');
    rsort($files);
    printqn("<table align=`center`>\n<tr><th>IP</th><th>Page</th><th>Time</th></tr>");
    foreach ($files as $file) {
    printqn("<tr><td colspan=`3` align=`center`><h3>$file</h3></td></tr>");
        $lines = file("$logfiledir/$file");
        TrimArray($lines);
        foreach ($lines as $line) {
            if (!empty($line)) {
                $items = explode('|',$line);
                echo "<tr><td>{$items[0]}</td><td>{$items[1]}</td>";
                if (!empty($items[2])) echo "<td>{$items[2]}</td></tr>";
                else echo "<td></td>";
            }

        }
        //echo nl2br(file_get_contents("$logfiledir/$file"));
    }
    echo "</table>";

} elseif ($FILE and (substr($FILE,-4)=='.dat')) {
    if (strpos($FILE,'contactlog') !== false) {
        $lines = array_reverse(file("$logfiledir/$FILE"));
        echo '<table id="missingtable" align="center" cellspacing="1">';
        echo "<tr><th>Date</th><th>Name</th><th>Email</th><th>Subject</th><th>IP</th><th>Refer</th></tr>";
        foreach ($lines as $line) {
            $line = str_replace('|','</td><td>',trim($line));
            if (!empty($line)) echo "<tr><td>$line</td></tr>\n";
        }
        echo '</table>';
    } elseif (strpos($FILE,'missing') !== false) {
        $lines = array_reverse(file("$logfiledir/$FILE"));
        echo '<table id="missingtable" align="center" cellspacing="1">';
        echo "<tr><th>Date</th><th>Page</th><th>Refer</th><th>IP</th><th>Agent</th></tr>";
        foreach ($lines as $line) {
            $line = str_replace('|','</td><td>',trim($line));
            if (!empty($line)) echo "<tr><td>$line</td></tr>\n";
        }
        echo '</table>';
    } else echo nl2br(htmlentities(file_get_contents("$logfiledir/$FILE")));

} elseif ($FILE and ($FILE != 0)){
    $DNSstuff = 'http://www.dnsstuff.com/tools/whois.ch?ip=';
    $lines = file("$logfiledir/log-$FILE.dat");
    rsort($lines);
    $count = 0;
    $botcount = 0;
    $sessioncount = 0;
    foreach ($lines as $line) {
        if (substr($line,15,3) == 'REF') {
            $checkline = TextBetween('][',']',$line);
            $count++;
            $botcheck = false;
            foreach($AgentCodes as $key => $value){
                if ((strpos($checkline,$key)!==false) and (strpos($value,'bot')!==false)) {
                    $botcheck = true; break;
                }
            }
            if ($botcheck) $botcount++;
            else $sessioncount++;
        }
    }
    echo "<p style=\"font-size:0.8em;\">Sessions: ".number_format($sessioncount).
    "&nbsp;&nbsp;Bot Hits: ".number_format($botcount)."</p>\n";

    $number = 0;
    $end = $ROWS+$START-1;

    if ($viewtype != 'SEARCH') {
        echo '<table align="center">';
        echo '<tr><th>No.</th><th>Date - Time</th><th>Time<br />(sec)</th><th align="left">Page or Referrer[IP]</th></tr>';
    } else {
        $SearchRef = array();
    }

    $linecount = count($lines);
    for ($i=0; $i < $linecount; $i++) {
        list($tid,$ET,$page) = explode('|',trim($lines[$i]));
        if ($ET == 'REF'){
            $number++;
            if ($number > $end) break;
            if ($number >= $START){
                $REF = strTo($page,'[');
                $info = TextBetweenArray('[',']',$page);
                $URL = (!empty($info[0]))? $info[0] : '';
                $AGENT = (!empty($info[1]))? $info[1] : '';
                $AGENT1 = $AGENT;
                if ($AGENT != '') {
                    foreach($AgentCodes as $key => $value){
                        if (strpos($AGENT,$key)!==false) { $AGENT = $value; break; }
                    }
                }
                if (($viewtype=='NOBOTS') and (strpos($AGENT,'bot')!==false)) $number--;
                elseif (($viewtype=='BOTS') and (strpos($AGENT,'bot')===false)) $number--;
                elseif ($viewtype == 'SEARCH') {
                    if (stripos($REF,'search') === false) $number--;
                    else {
                        $term = rawurldecode($REF);
                        $term = strtolower(str_replace('+',' ',$term));
                        $SearchRef[] = strtolower($term);
                    }
                } else {
                    $page = ($REF)? '<a target="_blank" href="'.$REF.'">'.$REF.'</a>' : '';
                    if ($refonly) {
                        $refintem = str_replace('www.','',strFrom($REF,'http://'));
                        $RefArray[] = strTo($refintem,'/');
                    } else {
                        if ($URL) $page .= '&nbsp;[<a target="_blank" href="'."$DNSstuff$URL".'">'.$URL.'</a>]';
                        if ($AGENT){
                          if ($showagent) $page .= "&nbsp;[$AGENT1]";
                            else $page .= "&nbsp;[$AGENT]";
                        }
                    }
                    $date = substr($tid,0,-4);
                    $date = ($ET=='REF')? str_replace(' ','&nbsp;',date("Y-m-d H:i:s",$date)):'';
                    printqn("<tr class=`ref`><td>$number.</td><td>$date</td><td>$ET</td><td>$page</td></tr>");
                    if (!$refonly) {
                        $rows = array();
                        for ($j=$i+1; $j < count($lines); $j++){
                            list($tid2,$ET,$page) = explode('|',trim($lines[$j]));
                            if ($tid2 == $tid){
                                $rows[] = qq("<tr><td colspan=`2`></td><td align=`right`>$ET</td><td>$page</td></tr>");
                            } else {
                                natsort($rows);
                                foreach($rows as $row) echo "$row\n";
                                break;
                            }
                        }
                    }
                }
            }
        }
    }
    if ($viewtype != 'SEARCH') echo "</table>";
    
    if ($refonly) {
        $FreqArray = array();
        foreach ($RefArray as $ref) {
            if (!empty($ref)) {
                if (array_key_exists($ref,$FreqArray)) $FreqArray[$ref]++;
                else $FreqArray[$ref] = 1;
            }
        }
        asort($FreqArray);
        echo '<br /><br /><table align="center">' . ArrayToStr($FreqArray,'<tr><td>KEY</td><td>VALUE</td></tr>').
             '</table>';    
    }
    
    if ($viewtype == 'SEARCH') {
        $countarr = array ('google' => 0, 'yahoo' => 0, 'livesearch' => 0, 'msn' => 0, 'aol' => 0, 'other' => 0);
        $searchterms = array();
        foreach ($SearchRef as $line) {
            if ( strpos($line, 'search.aol') !== false) {
                $countarr['aol']++;
                $term = HexDecodeString(trim(strTo(strFrom($line,'encquery='),'&')));
                if (empty($term)) $term = $term = trim(strTo(strFrom($line,'query='),'&'));
                if (!empty($term)) {
                    if (array_key_exists($term,$searchterms)) $searchterms[$term]++;
                    else $searchterms[$term] = 1;
                }

            }
            elseif ( strpos($line, 'yahoo') !== false) {
                $countarr['yahoo']++;
                $term = trim(strTo(strFrom($line,'p='),'&'));
                if (!empty($term)) {
                    if (array_key_exists($term,$searchterms)) $searchterms[$term]++;
                    else $searchterms[$term] = 1;
                }
            }
            elseif ( strpos($line, 'search.msn') !== false) {
                $countarr['msn']++;
                $term = trim(strTo(strFrom($line,'q='),'&'));
                if (!empty($term)) {
                    if (array_key_exists($term,$searchterms)) $searchterms[$term]++;
                    else $searchterms[$term] = 1;
                }
            }
            elseif ( strpos($line, 'search.live') !== false) {
                $countarr['livesearch']++;
                $term = trim(strTo(strFrom($line,'q='),'&'));
                if (!empty($term)) {
                    if (array_key_exists($term,$searchterms)) $searchterms[$term]++;
                    else $searchterms[$term] = 1;
                }
            }
            elseif ( strpos($line, 'google') !== false) {
                if ( strpos($line, 'translate') === false) {
                    $countarr['google']++;
                    $line = str_replace('aq=','x=',$line);
                    $term = trim(strTo(strFrom($line,'q='),'&'));
                    if (!empty($term)) {
                        if (array_key_exists($term,$searchterms)) $searchterms[$term]++;
                        else $searchterms[$term] = 1;
                    }
                }
            }
            else {
                $countarr['other']++;
                //echo "<p>OTHER = $line</p>";
            }
        }
        ksort($searchterms);
        echo '<table align="center">';
        echo '<tr><th>Term</th><th>Count</th></tr>';
        foreach ($searchterms as $term => $freq) {

            echo "<tr><td>$term</td><td>$freq</td></tr>";

        }
        echo '</table><br /><br />';
        echo '<table align="center">'.AssocArrayToStr($countarr,'<tr><th class="left">KEY</th><td class="right">VALUE</td></tr>').
             '<tr><th class="left">Total</th><td class="right">'.array_sum($countarr).'</td></tr></table>';
    }
}
?>
</div> <!--  End container -->
</body>
</html>
