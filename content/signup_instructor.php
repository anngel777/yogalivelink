<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: signup
    Description: Instructor Registration Page
==================================================================================== */
?>

@@PAGE_CONTENT@@
<center>

<div style="text-align:left; padding:left:40px;">
<?php


#AddSwap('@@PAGE_CONTENT@@','@@PAGE_CONTENT@@');


// ---------- CALL CLASS FOR PROCESSING ----------
$Obj = new Website_SignupInstructor;

$step = (Get('step')) ? Get('step') : 'start';
echo $Obj->HandleStep($step);

?>
</div>


<img src="/images/infographic_instructor.png" alt="How YogaLiveLink.com Works" border="0" />


</center>


