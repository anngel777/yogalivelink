<h1>Custom Site Variables</h1>
<?php
include "$ROOT/office/helper/custom_values_helper.php";

$CUSTOM_VALUES_FILE = '/office/config/custom_vars.dat';

$CUSTOM_VALUES_FORM_ARRAY = array(
    'text|Var 1|var1|N|40|40',
    'text|Var 2|var2|N|40|40',
    'text|Var 3|var3|N|40|40',
    'text|Var 4|var4|N|40|40',
    'text|Var 5|var5|N|40|40',
);

UpdateCustomValues($CUSTOM_VALUES_FORM_ARRAY);


//--- below is just for testing ---

AddMessage('DATA' . ArrayToStr(GetCustomValuesData()));
