<table class="upload" align="center" width="500">
<tr><td>
<h1>List PHP Functions</h1>
<p>Filter: <input  type="text" id="FILTER" value="" size="20" onkeyup="
    var filter = this.value.toLowerCase();
    var row = '';
    var check = false;
    var i = 1;

    while (getId('fli_' + i)) {
        
        row = getId('fli_' + i).innerHTML;
        check = row.indexOf(filter);
        if (check > -1) {
            showId('fli_' + i);
        } else {
            hideId('fli_' + i);
        }
        i++;
    }" />
</p>

<ol style="text-align:left;">
<?php

$functions = get_defined_functions();

$count = 0;
foreach ($functions['internal'] as $function) {
    $count++;
    echo "<li id=\"fli_$count\">$function</li>\n";
}
?>
</ol>
</td></tr>
</table>
