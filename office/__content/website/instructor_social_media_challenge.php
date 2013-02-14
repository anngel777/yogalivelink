<?php

#$PAGE['template'] = 'special';
#$_GET['template'] = 'special';

$CONTENT = ($PAGE['template'] == 'template_inner_1col.html') ? "<br /><br />" : '';


$CONTENT .= <<<CONTENT




<table style="color: #333333; font-family: Georgia, serif; font-size: 10px;" border="0" cellspacing="0" cellpadding="0" width="100%">
<tbody>
<tr>
<td style="padding-bottom: 0px; padding-left: 15px; padding-right: 15px; font-family: Verdana, Arial, Helvetica, sans-serif; color: #313230; font-size: 24px; padding-top: 15px;" align="left" width="100%" height="20" valign="center">
    <div style="color: #774617; font-size: 40px;"><span style="color: #ff0000; font-size: 36px;">Social Media Sharing Challenge</span></div>
    <div style="color: #774617; font-size: 40px;"><span class="yiv1343035201" style="color: #313230; font-size: 20px;"><br /></span></div>
    <div style="color: #774617; font-size: 40px;"><span class="yiv1343035201" style="color: #000080; font-size: 20px;">You can&nbsp;SHARE AND WIN with&nbsp;<a style="text-decoration: none;" href="http://yogalivelink.com/" target="_blank">YogaLiveLink.com</a>!</span></div>
</td>
</tr>
<tr>
<td align="left" width="100%" height="20" valign="center">
    <div style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px;"><span class="yiv1343035201" style="color: #ff0000;"><strong>Share about YogaLiveLink.com on Facebook</strong>,&nbsp;<strong>Twitter, blogs</strong>, and other social networks and you can&nbsp;<strong>win MONEY</strong> for you and&nbsp;<strong>DISCOUNTS</strong> for your customers! </span></div>
    <div style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px;"><strong><br /><span style="color: #ff0000;">THREE Instructors will WIN! </span></strong></div>
    <br />
    <div style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px;">We will award<strong> $60 to the first 3 Instructors</strong> who have&nbsp;<strong>2 customers</strong> book sessions as a result of learning about YogaLiveLink in a social networking post.</div>
    <br />
    <div style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px;"><strong>PLUS:</strong> The&nbsp;<strong>customers will get their session for only $25! </strong></div>
    <br />
    <div style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px;"><strong>ALSO: </strong>Customers will be entered into&nbsp;<strong>a drawing to win a FREE SESSION</strong> when they send us an email detailing the social media posting that drew them to YogaLiveLink.</div>
</td>
</tr>
</tbody>
</table>

<div style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px;">
<h2>How To Win!</h2>
<ul>
<li><b>YOU SHARE A POSTING about YogaLiveLink.com</b> on your social networks -- Facebook, Twitter, blogs, and other social networks </li>
<li>You have <b>2 customers book sessions with you</b> as a result of your postings </li>
<li>Your <b>CUSTOMERS MUST</b>:  </li>
    <ul>
    <li><b>Send an email to info@yogalivelink.com stating the details of your posting</b> that led the customer to book a session with YogaLiveLink </li>
    <li><b>Like us on Facebook</b> (become a fan) </li>
    </ul>
<li>You must <b>Like us on Facebook </b></li>
<li>You must <b>post a comment on our Facebook</b> page </li>
</ul>

<h2>Increase your chances of winning!</h2>
<ul>
<li><b>Share about YogaLiveLink.com with your friends on ALL of your social networks</b> -- be part of the buzz about YogaLiveLink's innovative service! </li>
<li>Make sure you <b>enter all of your available session time slots in your YogaLiveLink calendar!</b> -- If you haven't entered time slots into your schedule, then customers can't book sessions with you </li>
<li>Tell people in your posts that <b>you are available to teach</b> online yoga sessions with YogaLiveLink </li>
<li><b>Give people the details</b> about the Sharing Challenge:  </li>
    <ul>
    <li>Let people know they can <b>win a session for only $25</b></li>
    <li>Every customer in October will be <b>entered into a drawing for a FREE SESSION</b></li>
    </ul>
<li><b>Encourage your friends to visit</b> YogaLiveLink on Facebook and Like our page </li>
<li><b>Make multiple posts over time</b> -- keep sharing about YogaLiveLink and your availability for yoga sessions </li>
<li><b>REMINDER:</b> Your customers must book a session with you, Like our Facebook page, and send us an email about your posting for you to qualify to win </li>
</ul>

</div>



<table style="background-color: #69A1BA;" border="0" cellspacing="0" cellpadding="10" width="100%">
<tbody>
<tr>
<td style="font-family: Verdana, Arial, Helvetica, sans-serif;" align="middle" width="25%" valign="top">
<div style="font-size: 36px; padding: 3px;">$60&nbsp;<br /><span class="yiv1343035201" style="font-size: 14px;">plus customer discounts and &nbsp;incentives to sign up for more sessions </span></div>
</td>
<td style="font-family: Verdana, Arial, Helvetica, sans-serif;" align="left" width="75%" valign="center">
<div style="font-size: 22px;">Three Instructors Will Win!</div>
<div style="font-size: 12px;"></div>
<div style="font-size: 12px;">The first three instructors to complete the challenge will win the $60 and incentives. The winning customers will be charged only $25 for their session. All customers in will be entered into a drawing to win a free session. The session must be used within 2 months of the drawing.</div>
</td>
</tr>
</tbody>
</table>







 
CONTENT;

echo $CONTENT;

$content_left = '';
$content_bottom = '';
$content_right = '';

AddSwap('@@CONTENT_LEFT@@',$content_left);
AddSwap('@@CONTENT_BOTTOM@@',$content_bottom);
AddSwap('@@CONTENT_RIGHT@@',$content_right);
AddSwap('@@PAGE_HEADER_TITLE@@','Social Media Challenge');
?>

