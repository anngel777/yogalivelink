<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: database.php
    Description: CLASS :: Survey_Database
==================================================================================== */

$Obj = new Survey_Database();

if (Get('execute')) {
    $Obj->InitializeDatabase();
} else {
    echo ";execute";
}