<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: standard
    Description: Customer intake form - standard session - DEPRECATED ???
==================================================================================== */
?>


@@PAGE_CONTENT@@


<?php
// ---------- CALL CLASS FOR PROCESSING ----------
$Obj = new Website_StandardFormCustomer;

$step = (Get('step')) ? Get('step') : 'start';
echo $Obj->HandleStep($step);