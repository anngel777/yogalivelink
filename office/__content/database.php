<?php
$Obj = new Survey_Database();

if (Get('execute')) {
    $Obj->InitializeDatabase();
} else {
    echo ";execute";
}