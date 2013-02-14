<?php
echo '<div id="configform">
<h1>Session Variables</h1>';

include "$LIB/form_helper.php";

function CreateFormArray()
{
    global $ADMIN_FILE;

    $FormArray = array(
    "form|$ADMIN_FILE?SESSIONS=1|post",
    'info|Variable|Remove'
    );

    foreach ($_SESSION as $key => $value) {
        if (is_array($value)) $value = '(ARRAY)';
        $value = htmlentities($value);
        $FormArray[] = "checkbox|$key|$key||1";
    }

    $FormArray[] = 'submit|Remove Checked Items|SESSION_SUBMIT';
    $FormArray[] = 'endform';
    return $FormArray;
}

$FormArray = CreateFormArray();

$ERROR = '';


if (Post('SESSION_SUBMIT')) {

   $array = ProcessFormNT($FormArray, $ERROR);

   foreach ($array as $key=>$value) {
      if ($value == 1) {
         unset($_SESSION[$key]);
         addFlash("$key - Removed");
      }
   }
   $FormArray = CreateFormArray();
}


echo OutputForm($FormArray, Post('SESSION_SUBMIT'));


echo '<h3>Session Data</h3>';
echo ArrayToStr($_SESSION);
echo '</div>';
