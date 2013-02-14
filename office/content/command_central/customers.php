<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: customers
    Description: CLASS :: Profile_CustomerProfile --> View a list of customers
==================================================================================== */

$Obj = new Profile_CustomerProfile();

if ($AJAX) {
    $Obj->AjaxHandle();
} else {
    
    $class      = 'Profile_CustomerProfile';
    $win_type   = 'blank'; //'window'
    $list       = $Obj->CreateListingByAlphabet($class, 'customers', $win_type);
    
    echo $list;
}