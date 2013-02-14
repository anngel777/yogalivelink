<h1>Get Field Frequencies</h1>
<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: frequencies.php
    Description: Get field repeats in table
==================================================================================== */

$tables = $SQL->GetTables();
$options = '';
foreach ($tables as $table) {
    $options .= "|$table";
}

$FormArray = "
  form|@@PAGELINKQUERY@@|post|infotableform|$E
  select|Select Table|TABLE|Y|onchange=\"document.infotableform.submit();\"$options|$E
";

if (!Post('SUBMIT')) {
   $_POST[$FormPrefix.'LIMIT'] = 50;
   $_POST[$FormPrefix.'ORDER'] = 2;
}

$TABLE = GetPostItem('TABLE');

if (!empty($TABLE)) {
    $fields = $SQL->TableFieldNames($TABLE);
    $list = '';
    foreach ($fields as $field_name) {
        $list .= "|$field_name";
    }

  $FormArray .= "
  select|Select Field|FIELD|Y|$list|$E
  radioh|Order by|ORDER|Y||1=Field|2=Frequencies (DESC)|$E
  checkbox|Active|active||1|0|$E
  integer|Limit|LIMIT|N|4|4|$E
  submit|Submit|SUBMIT|$E
";

}


$FormArray = "
  $FormArray
  endform|$E
";

echo OutputForm($FormArray, Post('SUBMIT'));

if ($TABLE or Post('SUBMIT')) {
   addScriptOnload('ResizeIframe();');
}

$ERROR = '';
if (Post('SUBMIT')) {
   $array = ProcessFormNT($FormArray, $ERROR);
   $orderby = ($array['ORDER'] == 1)?  1 : 2;
   $desc = ($orderby == 2)? 'DESC' : '';
   $limit = $array['LIMIT'];
   $where = (ArrayValue($array, 'active')==1)? 'active=1' : '';
   $freq = $SQL->GetFreq($SQL->QuoteTables($array['TABLE']), $array['FIELD'], $orderby, $desc, $where, '', $limit);
   if ($freq) {
      echo "<h3>[{$array['FIELD']}] Values</h3>
      <div id=\"freq_results\">";
      foreach ($freq as $value=>$freq) {
          echo "<br class=\"formtitlebreak\" />
               <div class=\"formtitle\">$value:</div>
               <div class=\"forminfo\">$freq</div>\n\n";
      }
      echo "</div>";
   }
}
