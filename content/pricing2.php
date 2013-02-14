<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: pricing
    Description: Show the content - content pulled from database
==================================================================================== */

// ---------- GET CONTENT FROM DATABASE AND SWAP INTO PAGE ----------


$img = "
    <div class='index_header'>What does private yoga normally cost?</div>
    <div class='index_header_sub'></div>
    <img src='/images/price_map_sectioned.jpg' alt='' border='0' width='90%'/>
";

//$PAGE['pagename'] = 'pricing';

$CONTENT_RIGHT = <<<CONTENT_RIGHT


    
<br />
<center>
<div class="index_header">Ready to begin your private online yoga sessions?</div>


<br /><br />
<table width='500px' border='0'>
<tr>
    <td align='center'><a href='/how_yll_works' class='index_button_simple'>How it Works</a></td>
    <td align='center'><a href='/pricing' class='index_button_simple'>Pricing</a></td>
    <td align='center'><a href='/signup' class='index_button_simple_red'>Get Started</a></td>
</tr>
<tr>
    <td align='center'></td>
    <td align='center' style="background-color:#A71249; height:5px;"></td>
    <td align='center'></td>
</tr>
</table>

<br /><br /><br />

<table cellpadding="5" cellspacing="5" width="100%">
<tr>
<td valign="top" style="background-color:#EDE0C2;" width="50%">
<div class='index_header'>Benefits of YogaLiveLink 1-on-1 yoga sessions:</div>
<br />
<div style="font-size:15px; font-family: "Trebuchet MS",sans-serif;">
<b>Focused Attention:</b> You get the personal attention of your own yoga instructor.
<br /><br />
<b>Save money and time:</b> Private yoga sessions with an instructor at a studio cost more than our online sessions because there are no overhead costs. Neither the student nor the instructor need to commute. 
</div>
</td>
<td valign="top" width="50%">{$img}</td>
</tr>
</table>


<br /><br /><br />
</center>
<br /><br /><br />
<div class='index_content' style='width:80%; padding-left:50px;'>
    <b>Pricing and Policies</b>
        <ul>
        <li>Introductory offer: For your first month, each yoga session is $45 -- that's a savings of $20 per session off the standard $65 price.</li>
        <li>Each <a href="http://www.yogalivelink.com/yoga_therapy">yoga therapy session</a> is only $75, a saving of $20 per session off the standard $95 price. </li>
        <li>If <a href="http://www.yogalivelink.com/yoga_therapy">yoga therapy</a> is prescribed by a physician, often insurance companies cover the cost. So, check with your health insurance company for coverage.</li>
        
        </ul>
    <br />
    <b>Standard Pricing:</b>
        <ul>
        <li>Each private yoga session is $65.</li>
        <li>Each private <a href="http://www.yogalivelink.com/yoga_therapy">yoga therapy session</a> is $95.</li>
        
        </ul>
    <br />
    <div style='display:none;'>
    <b>Membership Benefits:</b>
        <ul>
        <li>When you sign up as a Member you receive the reduced rate of $50 per credit for 2 credits per month, automatically debited from your account.</li>
        <li>Plus, additional credits purchased by members are at the reduced rate of $60 per credit. </li>
        <li>Members also receive a 10% discount on items purchased through our web store.</li>
        <li>Memberships may be cancelled with 30-days written notice after six months.</li>
        </ul>
    <br />
    </div>
    <b>Cancellation and Refund Policy:</b>
        <ul>
        <li>If you cancel a session within 24 hours of the scheduled start time you will receive a credit to take another session. You may request a credit card refund by sending an email to support@YogaLiveLink.com</li>
        <li>Cancellations made less than 24 hours before the scheduled start time will be charged full price for the session. </li>
        <div style='display:none;'><li>Non-Refundable: Monthly accumulated membership credits -- the 2 monthly membership credits at $50 each -- are non-refundable. </li></div>
        </ul>
    <br />
    <b>Additional Information:</b>
        <ul>
        <li>Each one-on-one personal Yoga Training session or Yoga Therapy session is 50 minutes.</li>
        <li>You may purchase yoga sessions at the introductory price for 30 days from date of initial account sign-up. You may purchase as many sessions at this price as you like during the 30-day period.</li>
        <li>Sessions cannot be transferred to other YogaLiveLink.com users.</li>
        </ul>
    <br />
</div>



CONTENT_RIGHT;


AddSwap('@@CONTENT_LEFT@@','');
AddSwap('@@CONTENT_RIGHT@@',$CONTENT_RIGHT);
AddSwap('@@PAGE_HEADER_TITLE@@','');

?>
