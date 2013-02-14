<?php
set_time_limit(0);
ini_set('memory_limit', '100M');
require "$LIB/class.SiteLogs.php";
require "$LIB/form_helper.php";
echo $DOCTYPE_XHTML;
?>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>View Log Files - <?php echo $SITECONFIG['sitename'] ?></title>
    <link rel="stylesheet" type="text/css" href="/lib/site_admin/common/admin.css" />
    <script type="text/javascript" src="/lib/mvp.js"></script>

<script type="text/javascript">

function setTab(num)
{
  hideGroupExcept('tab', num);
  setClassGroup('tablink', num, 'tablink', 'tabselect');
}

function setEnabled()
{
    var buttons = new Array('NEXT','SHOW','FIRST','PREVIOUS','SEARCH','MISSING_PAGES','PAGE_REPORT','REFEERER');

    var monthSelectValue = getId('FORM_MONTH').value;
    var elems = getElementsByClass('need_month');
    var vOpacity = (monthSelectValue == 'START_SELECT_VALUE')? 0.3 : 1;
    for (var i=0; i < elems.length; i++ ) {
        elems[i].style.opacity = vOpacity;
    }
    var vDisabled = (vOpacity != 1);
    for (var i=0; i < buttons.length; i++ ) {
        if (getId(buttons[i])) {
            getId(buttons[i]).disabled = vDisabled;
        }
    }
}

// @select|WHERE_VAR|N|onchange=\"updateWhereSearch();\"|page=Page|date_time=Date Time|referrer=Referrer|remote_addr=Remote Addr|user_agent=User Agent|$E
    // @select||WHERE_TYPE|N||N|IN=Includes|EQ=Equals|GT=&gt;|LT=&lt;|$E
    // @text||WHERE_VALUE|N|20|255|$E

function updateWhereSearch()
{
    var whereVar = getId('FORM_WHERE_VAR').value;
    var selectState = (whereVar == 'START_SELECT_VALUE');
    getId('FORM_WHERE_TYPE').disabled = selectState;
    getId('FORM_WHERE_VALUE').disabled = selectState;    
}

window.onload = function() {
    setEnabled();
    updateWhereSearch();
}

</script>


<style type="text/css">
body {
  margin : 10px 0px 10px 0px;
}

#container {
  background-color : #aaa;
  border : 10px solid #66c;
  width : 100%;
}

h1, h2 {
  font-size : 1.2em;
  text-align : center;
}

.need_month {
  opacity : 0.3;
}

fieldset {
  width : 400px;
  margin : 20px;
  text-align : center;
  background-color : #fff;
}

legend {
  font-size : 1em;
  font-weight : bold;
  padding : 0px 3px;

}


input.formsubmit {
  font-family : Arial,Helvetica,sans-serif;
  font-size : 1em;
  padding : 0.25em 0.4em;
  color : #FFF;
  border : 1px solid #fff;
  cursor : pointer;
  background-color : #555;
}

input.formsubmit:hover {
  color : #FFF;
  background-color : #888;
}

div.formtitle {
  width : 150px;
}
div.forminfo {
  margin-left : 155px;
  text-align : left;
  font-size : 0.8em;
}

.sessiontype {
  background-color : #fff;
  padding : 2px 3px 0px 3px;
  border : 1px solid #ccc;
  font-size : 0.8em;
  color : #000;
}

#search_header h1 {
  font-size : 1em;
  margin : 3px 3px;
  color : #fff;
}

table.monthly_report {
  background-color : #ccc;
  font-size : 1em;
}

table.monthly_report td {
  background-color : #fff;
  text-align : right;
  vertical-align : top;
}

#missing_report td {
  text-align : left;
}

table.monthly_report th {
  background-color : #eee;
  vertical-align : top;
}

table.monthly_report .monthly_report_month {
  background-color : #eee;
}


table.monthly_report td, table.monthly_report th {
  padding : 1px 3px;
  vertical-align : top;
}

table.log_session_report {
  background-color : #ccc;
}


table.log_session_report td {
  background-color : #fff;
}

table.log_session_report th {
  background-color : #eee;
}

table.log_session_report .ref td {
  background-color : #8f9;
}

table.log_session_report .bot_ref td {
  background-color : #ff7;
}

table.log_session_report td, table.log_session_report th {
  white-space : nowrap;
  padding : 1px 3px;
  font-size : 1em;
}

.left {
  text-align : left;
}
.right {
  text-align : right;
}
div.tabspacer{
  background-color:#66c;
  border-top:1px solid #66c;
}

#tab1, #tab2 {
  padding : 10px;
}

</style>
</head>
<body class="admin">
<div id="search_header">
    <h1><?php echo $SITECONFIG['sitename'] ?> &mdash; Log Files</h1>
</div>
<table id="container" border="0" cellspacing="0" cellpadding="0"><tbody><tr><td>

<form method="post" action="<?php echo $THIS_PAGE_QUERY ?>">

<!-- end header -->
<?php

$LOG = new SiteLogs;

//======================Get Files==========================

$months = $LOG->GetMonthArray();
rsort($months);

$ERROR = '';

$SHOW     = GetPostItem('SHOW');
$ROWS     = GetPostItem('ROWS');
$START    = GetPostItem('START');
$FIRST    = GetPostItem('FIRST');
$NEXT     = GetPostItem('NEXT');
$PREVIOUS = GetPostItem('PREVIOUS');
$MONTH    = GetPostItem('MONTH');
$ACTION   = Get('SITELOG');

$SUBMIT   = count($_POST);

if (!$SUBMIT) {
    Form_PostValue('READ_TYPE', 0);
    Form_PostValue('SEARCH_SORT_ORDER', 1);
    Form_PostValue('PAGE_SORT_ORDER', 1);
    Form_PostValue('REFERRER_SORT_ORDER', 1);
    Form_PostValue('BOT_SORT_ORDER', 1);
}

if (!$ROWS) {
    $ROWS = 100;
    $_POST[$FormPrefix . 'ROWS'] = $ROWS;
}
if (!$START or $FIRST) {
    $START = 1;
    $_POST[$FormPrefix . 'START'] = $START;
}
if ($NEXT) {
    $_POST[$FormPrefix . 'START'] = $START + $ROWS;
}
if ($PREVIOUS) {
    $START = max(1, $START - $ROWS);
    $_POST[$FormPrefix . 'START'] = $START;
}

$month_list = '';
$count = 0;
foreach ($months as $month) {
    $count++;
    $title = date("F Y", strtotime("$month-01"));
    $month_list .= "$month=$count. $title|";
}

$first    = ($START > 1)? '@submit|&lt;&lt;|FIRST||' . $E : '';
$previous = ($START > 1)? '@submit|&nbsp;&nbsp;&lt;|PREVIOUS||' . $E : '';

$VOID = ($ACTION == 'bots')? '' : 'XXX';

$form_data = "
    code|<table align=\"center\"><tbody><tr><td colspan=\"2\">|$E

    select|Month|MONTH|N|onchange=\"setEnabled();\"|$month_list$E

    code|</td></tr><tr valign=\"top\"><td>|$E

    fieldset|Sessions in Month|class=\"need_month\"|$E
    radioh|Type|READ_TYPE|||0=All|1=No Bots|2=Bots Only|$E
    checkbox|Show Agent Info|SHOW_AGENT||1|0|$E
    checkbox|Show REF Only|REF_ONLY||1|0|$E
    
    @select|<p><b>Where:</b>&nbsp;|WHERE_VAR|N|onchange=\"updateWhereSearch();\"|page=Page|date_time=Date Time|referrer=Referrer|remote_addr=Remote Addr|user_agent=User Agent|$E
    @select||WHERE_TYPE|N||N|IN=Includes|EQ=Equals|GT=&gt;|LT=&lt;|$E
    @text||WHERE_VALUE|N|20|255|$E
    code|</p>|$E
        
    @integer|&nbsp;&nbsp;Start&nbsp;|START|N|5|5|$E
    @integer|&nbsp;&nbsp;Rows&nbsp;|ROWS|N|5|5|$E
    $first
    $previous
    @submit|&nbsp;&nbsp;&gt;|NEXT||$E
    @submit|&nbsp;&nbsp;Show|SHOW||$E
    endfieldset|$E

    fieldset|Sessions By Month|$E
    @submit|Show Monthly Session Report|MONTHLY_SESSIONS|$E
    endfieldset|$E

    fieldset|Sessions Per Day in Month|class=\"need_month\"|$E
    @submit|Show Daily Session Report|DAILY_SESSIONS|$E
    endfieldset|$E    
    
    fieldset|Searches|class=\"need_month\"|$E
    radio|Sort Order|SEARCH_SORT_ORDER|N||1=Alphabetical|2=Descending Frequencies|$E
    @submit|Search Report|SEARCH||$E
    endfieldset|$E

    code|</td><td>|$E

    fieldset|Missing Pages|class=\"need_month\"|$E
    @submit|Show Missing Pages in Month|MISSING_PAGES||$E
    endfieldset|$E

    fieldset|Page Report|class=\"need_month\"|$E
    radio|Sort Order|PAGE_SORT_ORDER|N||1=Alphabetical|2=Descending Frequencies|$E
    @submit|Show Page Access in Month|PAGE_REPORT||$E
    endfieldset|$E

    fieldset|Blocked Report|$E
    @submit|All Blocked Pages|ALL_BLOCKED|$E
    endfieldset|$E

    fieldset|Referrers|class=\"need_month\"|$E
    radio|Sort Order|REFERRER_SORT_ORDER|N||1=Alphabetical|2=Descending Frequencies|$E
    @submit|Referrers Sites|REFERRER|$E
    endfieldset|$E    


    fieldset|Bot Report|class=\"need_month\"|$E
    radio|Sort Order|BOT_SORT_ORDER|N||1=Alphabetical|2=Descending Frequencies|$E
    @submit|Bot Report|BOT_REPORT|$E
    @submit|Bot Update|BOT_UPDATE|$E
    endfieldset|$E  

    
    {$VOID}fieldset|Update Bots|$E
    {$VOID}@submit|Update Bots|UPDATE_BOTS|$E
    {$VOID}endfieldset|$E
    
    code|</td></tr><tr><td colspan=\"2\">|$E
    code|<p style=\"text-align:center;\"><a href=\"#\" class=\"contentbutton\" 
    onclick=\"toggleDisplay('enter_custom_query'); toggleDisplay('queries'); window.scrollBy(0,1000); return false;\">Queries</a></p>
    <div id=\"enter_custom_query\" style=\"display:none;\">|$E
    fieldset|Custom Query|$E
    html|Custom Query|QUERY|N|80|4|$E
    submit|Submit Query|CUSTOM_QUERY||$E
    endfieldset|$E
    code|</div>|$E
        
    code|</td></tr></tbody></table>|$E
";

$class1 = ($SUBMIT)? 'tablink' : 'tabselect';
$class2 = ($SUBMIT)? 'tabselect' : 'tablink';

$tab2 = ($SUBMIT)? '<a id="tablink2" class="' . $class2 . '" href="#" onclick="setTab(2); return false;">Results</a>' : '';


print <<<TABSLBL
<! --------- TABS ---------- -->
<a id="tablink1" class="$class1" href="#" onclick="setTab(1); return false;">Selection</a>
$tab2
<div class="tabspacer">&nbsp;</div>
TABSLBL;


$style1 = ($SUBMIT)? 'none' : 'block';
$style2 = ($SUBMIT)? 'block' : 'none';

print '
<! --------- TAB1 ---------- -->
<div id="tab1" style="display:' . $style1 . ';">
';


$result = ProcessFormNT($form_data, $ERROR);


echo OutputForm($form_data);
// echo ArrayToStr($result);
// echo ArrayToStr($_POST);

echo '
</div>
<! --------- END TAB1 ---------- -->

';

if (!$ERROR and $SUBMIT) {

    echo '
<! --------- TAB2 ---------- -->    
<div id="tab2" style="display:' . $style2 . ';">
';

    $month    = date("F Y", strtotime("$MONTH-01"));
    if (HaveSubmit('MONTHLY_SESSIONS')) {
        echo "<h1>Monthly Sessions</h1>";
        $LOG->OutputMonthlyReport(); 
        
    } elseif (HaveSubmit('DAILY_SESSIONS')) {
        echo "<h1>Daily Sessions</h1>";
        $LOG->OutputDailyReport($MONTH);

    } elseif(HaveSubmit('MISSING_PAGES')) {
        $missing_file = "missingpage-$MONTH.dat";
        echo "<h1>Missing Pages &mdash; $month</h1>";
        $LOG->OutputMissingPageReport($missing_file);

    } elseif(HaveSubmit('PAGE_REPORT')) {
        echo "<h1>Page Report &mdash; $month</h1>";
        $LOG->OutputPageReport($MONTH, $result['PAGE_SORT_ORDER']);

    } elseif(HaveSubmit('ALL_BLOCKED')) {
        echo "<h1>All Blocked</h1>";
        $LOG->OutputAllBlocked();

    } elseif(HaveSubmit('UPDATE_BOTS')) {
        echo "<h1>Update Bots</h1>";
        $LOG->UpdateBots();       
        
    } elseif(HaveSubmit('SEARCH')) {
        echo "<h1>Search &mdash; $month</h1>";
        $LOG->OutputSearch($MONTH, $result['SEARCH_SORT_ORDER']);

    } elseif(HaveSubmit('REFERRER')) {
        echo "<h1>Referrer Sites &mdash; $month</h1>";
        $LOG->OutputReferralSites($MONTH, $result['REFERRER_SORT_ORDER']);
    
    } elseif(HaveSubmit('CUSTOM_QUERY')) {
        echo "<h1>Custom Query</h1>";
        $LOG->OutputCustomQuery($result['QUERY']);
        
    } elseif(HaveSubmit('BOT_REPORT')) {
        echo "<h1>Bots &mdash; $month</h1>";
        $LOG->OutputBotsInMonth($MONTH, $result['BOT_SORT_ORDER']);
    
    } elseif(HaveSubmit('BOT_UPDATE')) {
        echo "<h1>Bots Update Maintenance</h1>";
        $LOG->UpdateBotList();

    } else {
        echo "<h1>Sessions &mdash; $month</h1>";
        $LOG->GetMonthSessionCount($MONTH);
        $scount = number_format($LOG->Session_Count, 0);
        $bcount = number_format($LOG->Bot_Count, 0);
        $search_count = number_format($LOG->Search_Count, 0);
        echo "<p><b>Sessions: $scount, Bots: $bcount, Search: $search_count</b></p>";        
        
        $LOG->OutputSessionArray($MONTH, $result['READ_TYPE'], $result['SHOW_AGENT'], $result['REF_ONLY'], $result['START'], $result['ROWS'], $result['WHERE_VAR'], $result['WHERE_TYPE'], $result['WHERE_VALUE']);

    }
    echo '</div> 
<!-- END TAB2 -->
';
    echo '<div id="queries" style="display:none;">';
    echo $LOG->SQL->WriteDbQuery();
    echo '</div>';
}
?>

</form>
</td></tr></tbody></table> <!--  End container -->
</body>
</html>
