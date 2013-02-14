<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: signup
    Description: Customer Registration Page
==================================================================================== */
?>


@@PAGE_CONTENT@@


<?php
// ---------- CALL CLASS FOR PROCESSING ----------
$Obj = new Website_SignupCustomer;

$step = (Get('step')) ? Get('step') : 'start';
echo $Obj->HandleStep($step);