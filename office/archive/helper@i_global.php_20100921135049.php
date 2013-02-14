<?php	

define('PAGE'				    , '');
define('REGISTER_MENU_LINK'		,  'https://secure.bluehost.com/~afterho2/afterhoursutilities/secure/register');
define('REGISTER_USER_LINK'		,  'https://secure.bluehost.com/~afterho2/afterhoursutilities/secure/registration?reg_type=user');
define('REGISTER_VENDOR_LINK'	,  'https://secure.bluehost.com/~afterho2/afterhoursutilities/secure/registration?reg_type=vendor');
define('REGISTER_CUSTOMER_LINK'	,  'https://secure.bluehost.com/~afterho2/afterhoursutilities/secure/registration?reg_type=customer');
define('CONTACT_PHONE'		,  '360-254-0561');
define('CONTACT_EMAIL'		,  'support@afterhoursutilites.com');
define('CONTACT_ADDRESS'	,  'AfterHoursUtilities.com<br />P.O. Box 871598<br />Vancouver, WA 98687-1598');
define('FOOTER'				,  "&copy;2009 AfterHoursUtilities.com | <a href='privacy_policy.php' style='color:#FFFFFF'>Privacy Policy</a> | <a href='terms_of_use.php' style='color:#FFFFFF'>Terms of Use</a> | Design By <a href='http://www.dipdot.com' target='_blank' style='color:#FFFFFF'>Dip Dot Multimedia</a>");
#define('ROOT'				,  $_SERVER['DOCUMENT_ROOT']);
define('TECHNICAL_PHONE'	, "360-254-0561");
define('TITLE_WEB'			, ".: After Hours Utilities :.");
define('LOGO'				, "images/logo.jpg");
define('TITLE_MENU'			, "AAI CONDUIT ver D.1.0");
define('IMG_DIR'			, "images/");
define('FOOTER'				, "©2010 AfterHours");
define('SHOW_ERROR'			, "true");		// SHOWS ALL ALERT MESSAGES EMBEDDED IN THE CODE (VALUES: true || false)
define('SEND_MAIL'			, "true");		// SEND EMAILS (VALUES: true || false)
define('SEND_MAIL_ADMIN'	, "false");		// SEND EMAIL COPY TO ADMIN (VALUES: true || false)
define('SHOW_REFBACK'		, "false");		// SHOWS PAGE REF BACK ARRAY (VALUES: true || false)
define('REQUIRE_PASSWORD'	, "false");		// SHOWS PAGE REF BACK ARRAY (VALUES: true || false)
define('NOTES_SORT_ORDER'	, "DESC");		// SHOWS PAGE REF BACK ARRAY (VALUES: DESC || ASC)
define('WEBSITE_PATH'		, "http://www.afterhoursutilities.com/conduit/login.php");	// absolute path to website root directory



//==================================================================================
// EMAIL SETTINGS
//==================================================================================
define('MAIL_HOST'			    , 'smtp.1and1.com'); 	                // specify main and backup server
define('MAIL_USERNAME'		    , 'intel_ysi@mailwh.com');  			// SMTP username
define('MAIL_PASSWORD'		    , 'pa55word'); 							// SMTP password
define('MAIL_FROM'			    , 'support@afterhoursutilities.com');	// from email address
define('MAIL_FROMNAME'		    , "After Hours Utilities");				// from name for email
//define('MAIL_SUBJECT'		    , "ISSUE TRACKER NOTICE");				// subject line for email
define('MAIL_TO_ADMIN_EMAIL'	, 'support@afterhoursutilities.com');	// email address for message copy to admin
define('MAIL_TO_ADMIN_NAME'		, 'AAU Administrator');	                // email address for message copy to admin
  
//==================================================================================
// DATE SETTINGS
//==================================================================================
date_default_timezone_set("America/Los_Angeles"); 	


$MESSAGE_TOP = '
	<table width="500" border="0" align="center" cellpadding="5" cellspacing="0" class="table_border">
    <tr>
      <td class="table_header">ALERT MESSAGE</td>
    </tr>
    <tr>
      <td align="right" valign="top" bgcolor="#E4EBF1">';
	  
$MESSAGE_BOTTOM = '</td>
    </tr>
	</table>';
?>