<h1 style="padding:0px;">Look-Up Tables</h1>
<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: look_ups.php
    Description: Do a database lookup
==================================================================================== */

$baselink = strTo($THIS_PAGE_QUERY, ';LOOKUP_LIST') . ';LOOKUP_LIST';

$lookup_list = array(
    'countries',
    'column_titles',
    'admin_custom_searches'
);

sort($lookup_list);

$table = Get('LOOKUP_LIST');

echo "<p style=\"margin:0px 50px; padding:0px;\"><select onchange=\"window.location='$baselink' + '=' + this.value;\">\n";
printqn("<option value=``>-- Select --</option>\n");

foreach($lookup_list as $item) {
    $selected = ($table == $item)? ' selected="selected"' : '';
    $title = NameToTitle($item);
    printqn("<option value=`$item`$selected>$title</option>\n");
}
echo "</select></p>\n";

if ($table) {    
    $_SESSION['LOOKUP_TABLE'] = $table;

    $Obj = new Lib_LookUps();
    $Obj->ListTable();
}
