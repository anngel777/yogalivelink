<?php

    #$Ins = new General_ModuleInstructions;
    #$Ins->AddInstructions('dev_richard/customer_profile');

$Obj = new Profile_CustomerProfile();
$Obj->Default_Tab = (Get('tab')) ? Get('tab') : 0;

if ($AJAX) {
    $Obj->AjaxHandle();    
} else {
    $Obj->Execute();
    
/*
printqn("<p><a href=`#` class=`stdbuttoni` 
onclick=`return RateSession('y6uGY5ChTbV1K74c-DJaI1k2ApE5Qx3ivW5vytHgQFk9x4pFA3U-mwI~');`>TEST RATE SESSION</a></p>");

printqn("<br /><p><a href=`#` class=`stdbuttoni` 
onclick=`return TestChat('');`>TEST CHAT</a></p>");
*/


}