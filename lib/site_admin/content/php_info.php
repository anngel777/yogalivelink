<?php
if ($AJAX) {
    $q = Post('q');
    if (empty($q)) {
	    $q = Get('q');
		if (empty($q)) exit;
		if (!function_exists('memory_get_usage')) $MEMUSED = '(Not available on this server)';
        else $MEMUSED = round(memory_get_usage(true)/1024).'K';
		echo "<p>Memory Usage = $MEMUSED of ".get_cfg_var('memory_limit').'</p>';
    }
    echo '<ol style="text-align:left;margin-left:100px; background-color:#fff; padding:1em 2em;">';

    $functions = get_defined_functions();

    $count = 0;
    foreach ($functions['internal'] as $function) {
        if (strpos($function, $q) !== false) {
            $count++;
            echo "<li>$function</li>\n";
        }
    }

    echo '</ol>';
    exit;
}

print <<<LBL1

<div id="tabdiv">
    <a id="tablink1" class="tabselect" href="#" onclick="setEditTab(1); return false;">PHP Info</a>
    <a id="tablink2" class="tablink" href="#" onclick="setEditTab(2); return false;">PHP Functions</a>
    <div class="tabspacer">&nbsp;</div>
</div>



<div id="mainpage2" class="tabfolder" style="display:none;">
<div class="formtitle">PHP Function Search:</div>
<div class="forminfo"><input class="autocomplete ac_input" type="text" size="40" maxlength="80" onkeyup="
    var item = this.value.toLowerCase();
    $('#php_functions').load('AJAX/php_info', {q : item}, function(){
        if (haveDialogTemplate) ResizeIframe();
    });" />
    
</div>
<div id="php_functions"></div>
</div>

<div id="mainpage1" class="tabfolder">
<div class="formtitle">Info Filter:</div>
<div class="forminfo"><input type="text" size="40" maxlength="80" onkeyup="
    var filter = this.value.toLowerCase();
    var row = '';
    var check = false;
    var odd = 2;

    var hidding = false;
    $('#dialogcontent tr').each(function(){
        row = $(this).html();
        row = row.replace(/<\/?[^>]+(>|$)/g, '|').toLowerCase();  // strips html tags and add bars to separate
        row = row.replace(/\|+/g, '|');  // strips html tags
        check = row.indexOf(filter);
        if (check > -1) {
            $(this).show();
        } else {
            $(this).hide();
            hidding = true;
        }
    });
    if (hidding) {
       $('h1, h2, hr, br').hide();
    } else {
       $('h1, h2, hr, br').show();
    }
" /></div>
LBL1;

ob_start(); phpinfo();
$PHP_INFO = ob_get_contents();
ob_end_clean();
$STYLE = TextBetween('<style type="text/css">', '</style>', $PHP_INFO);
$STYLE .= '
  body {font-size:1em;}
  a:link {color: #000; text-decoration: none; background-color: #ffffff;}
  a:hover {text-decoration: none;}
';

AddStyle($STYLE);

$PHP_INFO = TextBetween('<body>', '</body>', $PHP_INFO);
$PHP_INFO = preg_replace('/<a href(.*)<\/a>/', '', $PHP_INFO);

echo $PHP_INFO;
echo '</div>';


# ========================================================================================================
# ========================================================================================================
# ========================================================================================================
/*
$arr = get_defined_functions();

print_r($arr);
*/
/*

    $q = 'km'; //Post('q');
    if (empty($q)) {
	    $q = Get('q');
		if (empty($q)) exit;
		if (!function_exists('memory_get_usage')) $MEMUSED = '(Not available on this server)';
        else $MEMUSED = round(memory_get_usage(true)/1024).'K';
		echo "<p>Memory Usage = $MEMUSED of ".get_cfg_var('memory_limit').'</p>';
    }
    echo '<ol style="text-align:left;margin-left:100px; background-color:#fff; padding:1em 2em;">';

    $functions = get_defined_functions();

    $count = 0;
    foreach ($functions['internal'] as $function) {
        ##if (strpos($function, $q) !== false) {
        ##    $count++;
            echo "<li>$function</li>\n";
        ##}
    }

    echo '</ol>';
*/