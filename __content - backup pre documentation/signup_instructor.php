@@PAGE_CONTENT@@



<?php
$Obj = new Website_SignupInstructor;

$step = (Get('step')) ? Get('step') : 'start';
echo $Obj->HandleStep($step);