<?php
define('CLASS_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('CLASS_LIB', CLASS_DIR . 'Lib' . DIRECTORY_SEPARATOR);
$AUTOLOAD_RECORD_ARRAY = array();

function ClassFileFromName($class_name)
{
    if (strpos($class_name, '_') !== false) {
        $file = strtr($class_name, '_', DIRECTORY_SEPARATOR);
    } else {
        $file = "class.$class_name";
    }
    $path_file = CLASS_DIR . $file . '.php';
    $AUTOLOAD_RECORD_ARRAY[$class_name] = $path_file;
    return $path_file;
}

function __autoload($class_name)
{
    $file = ClassFileFromName($class_name);    
    require_once $file;

}