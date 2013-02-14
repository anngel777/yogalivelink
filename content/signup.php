<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: signup
    Description: Customer Registration Page
==================================================================================== */
?>


@@PAGE_CONTENT@@

<center>
<div class="index_header">Create an Account</div>
<a href="http://www.yogalivelink.com/signup">
<div style="display:none;"><img alt="" src="/images/pricing_current_special.png" border="0" /></div>
</a>
<br /><br />
<table width='500px' border='0'>
<tr>
    <td align="center"><a href="/how_yll_works"><div class="buttonImg"><img src="/images/buttons/btn_how_works_off.png"></div></a></td>
    <td align="center"><a href="/pricing"><div class="buttonImg"><img src="/images/buttons/btn_pricing_off.png"></div></a></td>
    <td align="center"><a href="/signup"><div class="buttonImg"><img src="/images/buttons/btn_get_started_on.png"></div></a></td>
</tr>
</table>
<br />
</center>


<?php
// ---------- CALL CLASS FOR PROCESSING ----------
$Obj = new Website_SignupCustomer;

$step = (Get('step')) ? Get('step') : 'start';
echo $Obj->HandleStep($step);
?>
