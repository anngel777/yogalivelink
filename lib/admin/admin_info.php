<div id="infobox" style="display:none;">
<h2>Admin Information</h2>
<p style="text-align:center;"><a href="#" class="mainbutton" onclick="hideId('infobox'); return false;">Close</a></p>
<?php

function AssocArrayToStrInfo($array, $template = '<b>KEY</b> = VALUE<br />')
{
    if(empty($array)) {
        return '';
    }
    
    $RESULT='';
    ksort($array);
    
    foreach ($array as $key=>$value) {
        if ((substr($key,0,1) != '_') and ($key != 'GLOBALS')) {
            if (is_array($value)){
                $value = 'ARRAY('.count($value).')';
            } else {
                $value = htmlentities($value);
            }
        
            $RESULT .= str_replace(array('KEY','VALUE'),array($key,$value),$template);
        }
    }
    return $RESULT;
}

function AssocArrayToStrConstants($array, $template = '<b>KEY</b> = VALUE<br />')
{
    if(empty($array)) {
        return '';
    }
    
    $RESULT='';
    ksort($array);
    
    foreach ($array as $key=>$value) {
        if (substr($key,0,6) == 'ADMIN_') {
            if (is_array($value)){
                $value = 'ARRAY('.count($value).')';
            } else {
                $value = htmlentities($value);
            }
        
            $RESULT .= str_replace(array('KEY','VALUE'),array($key,$value),$template);
        }
    }
    return $RESULT;
}


if (!function_exists('memory_get_usage')) $MEMUSED = '(Not available on this server)';
else $MEMUSED = round(memory_get_usage(true)/1024).'K';

echo "<p>Memory Usage = $MEMUSED of ".get_cfg_var('memory_limit').'</p>';

echo "<h3>Variables Defined</h3>";
echo AssocArrayToStrInfo($GLOBALS);

echo "<h3>SITECONFIG</h3>";
echo AssocArrayToStrInfo($SITECONFIG);

echo "<h3>Constants Defined</h3>";
echo AssocArrayToStrConstants(get_defined_constants());

echo "<h3>Session</h3>";
echo ArrayToStr($_SESSION);


?>

<p style="text-align:center;"><a href="#" class="mainbutton" onclick="hideId('infobox'); return false;">Close</a></p>
</div>