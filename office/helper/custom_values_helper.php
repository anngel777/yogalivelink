<?php

/* =========================================================================
 Custom Site Configuration Variable Helper
 Michael V. Petrovich 2009-12-16

 $CUSTOM_VALUES_FILE (path from root) my be predefined before loading this
 helper.

 A form array must be passed to the updating function
 and does not require form and endform lines, only value lines.

 Updating requires form_helper be loaded.

 =========================================================================== */

if (empty($CUSTOM_VALUES_FILE)) {
    $CUSTOM_VALUES_FILE = '/config/custom_vars.dat';
}

function CustomValue($key)
{
    static $array;

    if (empty($array)) {
        $array = GetCustomValuesData();
    }

    return (($key != '') and isset($array) and isset($array[$key]))? $array[$key] : '';
}


function GetCustomValuesData()
{
    global $CUSTOM_VALUES_FILE;

    if (file_exists(RootPath($CUSTOM_VALUES_FILE))) {
        $content = file_get_contents(RootPath($CUSTOM_VALUES_FILE));
        if ($content) {
            if ($RESULT = unserialize($content)) {
                if (is_array($RESULT)) {
                    return $RESULT;
                }
            }
        }
    }
    return array();
}

function SaveCustomValuesData($array)
{
    global $CUSTOM_VALUES_FILE;
    return file_put_contents(RootPath($CUSTOM_VALUES_FILE), serialize($array));
}

function UpdateCustomValues($custom_values_form_array)
{
    if (empty($custom_values_form_array)) {
        AddError('custom_values_form_array is not defined');
    }

    $action = $_SERVER['REQUEST_URI'];

    $form_array = array_merge(
        array("form|$action|post"),
        $custom_values_form_array,
        array('submit|Submit|SUBMIT_CUSTOM', 'endform')
    );

    $ERROR = '';
    $SUBMIT = HaveSubmit('SUBMIT_CUSTOM');
    if ($SUBMIT) {
        $result_array = ProcessFormNT($form_array, $ERROR);
        if (!$ERROR) {
            SaveCustomValuesData($result_array);
            AddFlash('Custom Data Saved!');
        }
    }

    if (!$SUBMIT) {
        //preload data
        $array = GetCustomValuesData();
        Form_PostArray($array);
    }

    AddError($ERROR);
    echo OutputForm($form_array, $SUBMIT);
}


