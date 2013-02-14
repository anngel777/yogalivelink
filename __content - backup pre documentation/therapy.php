@@PAGE_CONTENT@@



<?php
$Obj = new Website_TherapyFormCustomer;

$step = (Get('step')) ? Get('step') : 'start';
echo $Obj->HandleStep($step);