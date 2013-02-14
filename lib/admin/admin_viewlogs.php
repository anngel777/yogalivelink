<?php
   require "$LIB/admin/class.ViewLogs.php";
   require "$LIB/form_helper.php";
   echo $DOCTYPE_XHTML;
?>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>View Log Files - <?php echo $SITECONFIG['sitename'] ?></title>
    <link rel="stylesheet" type="text/css" href="/lib/admin/admin_style.css" />
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

    var fileSelectValue = getId('FORM_FILE').value;
    var elems = getElementsByClass('need_month');
    var vOpacity = (fileSelectValue == 'START_SELECT_VALUE')? 0.3 : 1;
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

window.onload = function() {
    setEnabled();
}

</script>


<style type="text/css">
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
  font-size : 0.8em;
  font-weight : bold;
  padding : 0px 3px;

}


input.formsubmit {
  font-family : Arial,Helvetica,sans-serif;
  font-size : 0.76em;
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

.tabfolder {
  background-color : #eef;
  padding : 5px;
}

table.monthly_report {
  background-color : #ccc;
  font-size : 0.8em;
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
  font-size : 0.8em;
}

.left {
  text-align : left;
}
.right {
  text-align : right;
}
</style>
</head>
<body class="admin">
<div id="container">

<form method="post" action="<?php echo $THIS_PAGE ?>?SITELOG=1">

<div id="search_header">
    <h1><?php echo $SITECONFIG['sitename'] ?> &mdash; Log Files</h1>
</div>
<!-- end header -->
<?php

$LOG = new ViewLogs;

//======================Get Files==========================

$files = $LOG->GetMonthlySessionFiles();
rsort($files);

$ERROR = '';

$SHOW     = GetPostItem('SHOW');
$ROWS     = GetPostItem('ROWS');
$START    = GetPostItem('START');
$FIRST    = GetPostItem('FIRST');
$NEXT     = GetPostItem('NEXT');
$PREVIOUS = GetPostItem('PREVIOUS');
$FILE     = GetPostItem('FILE');

$SUBMIT   = count($_POST);

if (!$SUBMIT) {
    $_POST[$FormPrefix.'READ_TYPE']           = 0;
    $_POST[$FormPrefix.'SEARCH_SORT_ORDER']   = 1;
    $_POST[$FormPrefix.'PAGE_SORT_ORDER']     = 1;
    $_POST[$FormPrefix.'REFERRER_SORT_ORDER'] = 1;
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

$file_list = '';
$count = 0;
foreach ($files as $file) {
    $count++;
    $link = substr($file,4,7);
    $title = date("F Y", strtotime("$link-01"));
    $file_list .= "$link=$count. $title|";
}

$first    = ($START > 1)? '@submit|&lt;&lt;|FIRST||' . $E : '';
$previous = ($START > 1)? '@submit|&nbsp;&nbsp;&lt;|PREVIOUS||' . $E : '';

$form_data = "
    code|<table align=\"center\"><tbody><tr><td colspan=\"2\">|$E

    select|Month|FILE|N|onchange=\"setEnabled();\"|$file_list$E

    code|</td></tr><tr valign=\"top\"><td>|$E

    fieldset|Sessions in Month|class=\"need_month\"|$E
    radio|Type|READ_TYPE|||0=All|1=No Bots|2=Bots Only|$E
    checkbox|Show Agent Info|SHOW_AGENT||1|0|$E
    checkbox|Show REF Only|REF_ONLY||1|0|$E
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
    @submit|Referrers Sites|REFERRER||$E
    endfieldset|$E
    code|</td></tr></tbody></table>|$E
";

$class1 = ($SUBMIT)? 'tablink' : 'tabselect';
$class2 = ($SUBMIT)? 'tabselect' : 'tablink';

$tab2 = ($SUBMIT)? '<a id="tablink2" class="' . $class2 . '" href="#" onclick="setTab(2); return false;">Results</a>' : '';

print <<<TABSLBL
<a id="tablink1" class="$class1" href="#" onclick="setTab(1); return false;">Selection</a>
$tab2
<div class="tabspacer">&nbsp;</div>
TABSLBL;


$style1 = ($SUBMIT)? 'none' : 'block';
$style2 = ($SUBMIT)? 'block' : 'none';

print '
<div class="tabfolder">
    <div id="tab1" style="display:' . $style1 . ';" class="tabselect">
';

$files = $LOG->GetMonthlySessionFiles();
$result = ProcessFormNT($form_data, $ERROR);


echo OutputForm($form_data);
// echo ArrayToStr($result);
// echo ArrayToStr($_POST);

echo '</div>';

if (!$ERROR and $SUBMIT) {
    echo '
        <div id="tab2" style="display:' . $style2 . ';" class="tabselect">
';

    $log_file = "log-$FILE.dat";
    $month    = date("F Y", strtotime("$FILE-01"));
    if (HaveSubmit('MONTHLY_SESSIONS')) {

        echo "<h1>Monthly Sessions</h1>";
        $LOG->OutputMonthlyReport();

    } elseif(HaveSubmit('MISSING_PAGES')) {
        $missing_file = "missingpage-$FILE.dat";
        echo "<h1>Missing Pages &mdash; $month</h1>";
        $LOG->OutputMissingPageReport($missing_file);

    } elseif(HaveSubmit('PAGE_REPORT')) {
        echo "<h1>Page Report &mdash; $month</h1>";
        $LOG->OutputPageReport($log_file, $result['PAGE_SORT_ORDER']);

    } elseif(HaveSubmit('ALL_BLOCKED')) {
        echo "<h1>All Blocked</h1>";
        $LOG->OutputAllBlocked();

    } elseif(HaveSubmit('SEARCH')) {
        echo "<h1>Search &mdash; $month</h1>";
        $LOG->OutputSearch($log_file, $result['SEARCH_SORT_ORDER']);
    
    } elseif(HaveSubmit('REFERRER')) {
        echo "<h1>Referrer Sites &mdash; $month</h1>";        
        $LOG->OutputReferralSites($log_file, $result['REFERRER_SORT_ORDER']);

    } else {
        echo "<h1>Sessions &mdash; $month</h1>";
        $LOG->GetLogArray($log_file, $result['READ_TYPE'], $result['START'], $result['ROWS']);
        $LOG->GetFileSessionCount($log_file);
        $scount = number_format($LOG->Session_Count, 0);
        $bcount = number_format($LOG->Bot_Count, 0);
        $search_count = number_format($LOG->Search_Count, 0);
        echo "<p><b>Sessions: $scount, Bots: $bcount, Search: $search_count</b></p>";

        $LOG->OutputSessionArray($log_file, $result['READ_TYPE'], $result['SHOW_AGENT'], $result['REF_ONLY']);

    }
    echo '</div>';
}
?>
</div>
</form>
</div> <!--  End container -->
</body>
</html>
