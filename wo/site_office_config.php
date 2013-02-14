<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/config/db_info.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Lib/Singleton.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Lib/Pdo.php';

$SQL = Lib_Singleton::GetInstance('Lib_Pdo');
$SQL->ConnectMySql($DB_INFO);

