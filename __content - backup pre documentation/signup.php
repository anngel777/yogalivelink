@@PAGE_CONTENT@@



<?php
$Obj = new Website_SignupCustomer;

$step = (Get('step')) ? Get('step') : 'start';
echo $Obj->HandleStep($step);