<h1>Session Variables</h1>
<a class="stdbuttoni" href="@@PAGELINK@@">Refresh</a>

<?php

function CreateFormArray()
{
    
    $FormArray = array(
    'form|@@PAGELINKQUERY@@|post',
    'info|Variable|Remove'
    );
    
    foreach ($_SESSION as $key => $value) {
        if (is_array($value)) $value = '(ARRAY)';
        $value = htmlentities($value);
       //$FormArray[] = "checkbox|$key<br /><span>$value</span>|$key||1";
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
