@@PAGE_CONTENT@@



<?php
$Obj = new Website_StandardFormCustomer;

$step = (Get('step')) ? Get('step') : 'start';
echo $Obj->HandleStep($step);