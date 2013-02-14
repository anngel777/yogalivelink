<?PHP
# ===============================================================
# ===============================================================
# THESE ARE GLOBAL FUNCTIONS NEEDED FOR YOGA WEBSITE
# ===============================================================
# ===============================================================
/*
COLOR SWATCHES
===============================
Orange      #F2925A
Red         #AA1149
Light Green #D1D289
Dark Green  #A3A548
Dark        #333333
Light       #EAE6CD
*/


// GLOBAL VARIABLES
// =========================================
$CLASS_EXECUTE_LINK_AJAX            = '/office/AJAX/class_execute';
$CLASS_EXECUTE_LINK                 = '/office/class_execute';

$LINK_SESSION_SIGNUP                = '/office/website/sessions_schedule;type=instructor;customer=on';
$LINK_SESSION_SIGNUP_INSTRUCTOR     = '/office/website/sessions_schedule;type=instructor;customer=on';
$LINK_SESSION_SIGNUP_DATE           = '/office/website/sessions_schedule;type=date;customer=on';

$DEFAULT_LOCAL_TIMEZONE             = 'America/Los_Angeles';
$DEFAULT_LOCAL_TIMEZONE_DISPLAY     = 'Pacific Time';
$DEFAULT_LOCAL_TIMEZONE_OFFSET      = -8;
$USER_LOCAL_TIMEZONE                = (isset($_SESSION['USER_LOGIN']['LOGIN_RECORD']['tz_name'])) ? $_SESSION['USER_LOGIN']['LOGIN_RECORD']['tz_name'] : $DEFAULT_LOCAL_TIMEZONE;
$USER_LOCAL_TIMEZONE_OFFSET         = (isset($_SESSION['USER_LOGIN']['LOGIN_RECORD']['tz_offset'])) ? $_SESSION['USER_LOGIN']['LOGIN_RECORD']['tz_offset'] : $DEFAULT_LOCAL_TIMEZONE_OFFSET;
$USER_LOCAL_TIMEZONE_DISPLAY        = (isset($_SESSION['USER_LOGIN']['LOGIN_RECORD']['tz_display'])) ? $_SESSION['USER_LOGIN']['LOGIN_RECORD']['tz_display'] : $DEFAULT_LOCAL_TIMEZONE_DISPLAY;
$SERVER_TIMEZONE                    = 'UTC'; //date_default_timezone_get();

$YOGA_TYPE_LIST                     = "Anusara|Ashtanga|Hatha|Kundalini|Vinyasa|Yin|Yoga-Nidra|Yoga Therapy|Other";
$CONTACT_PHONE_NUMBER               = "Local: 503-427-1922<br />Toll Free: 1-800-562-1259";
$SUPPORT_EMAIL_ADDRESS              = "support@YogaLiveLink.com";


$INSTRUCTOR_HANDBOOK_LINK           = 'http://www.yogalivelink.com/download/Instructor_Guide_v11-2.pdf';
$INSTRUCTOR_HANDBOOK_TITLE          = 'VIEW INSTRUCTOR HANDBOOK<br />Version 11-2';
$INSTRUCTOR_HANDBOOK_IMAGE          = '/office/images/pdf.png';



# ==================================================================================
# INSTRUCTOR IMAGES
# KEEP THESE IMAGES THE SAME RATIO of 0.75:1 OR YOU'LL GET DISTORTION
# ==================================================================================
$INSTRUCTOR_PICTURE_WIDTH_SIZING            = 200; // used to create original image - largest version we'll have
$INSTRUCTOR_PICTURE_HEIGHT_SIZING           = 267; // used to create original image - largest version we'll have

$INSTRUCTOR_PICTURE_WIDTH_SIZING_PREVIEW    = 200; // used to create original image - preview of the image
$INSTRUCTOR_PICTURE_HEIGHT_SIZING_PREVIEW   = 267; // used to create original image - preview of the image

$INSTRUCTOR_PICTURE_WIDTH                   = 75;
$INSTRUCTOR_PICTURE_HEIGHT                  = 100;

$INSTRUCTOR_PICTURE_WIDTH_LARGER            = 150;
$INSTRUCTOR_PICTURE_HEIGHT_LARGER           = 200;


$INSTRUCTOR_PICTURE_WIDTH_PROFILE           = 200; // used in the profile window
$INSTRUCTOR_PICTURE_HEIGHT_PROFILE          = 267; // used in the profile window

$INSTRUCTOR_PICTURE_DIR             = '/office/';
# ==================================================================================





$USER_DISPLAY_TIME                  = 'g:ia';       // Time as displayed to the user
$USER_DISPLAY_DATE                  = 'M jS, Y';    // Date as displayed to the user
$USER_DISPLAY_DATE_CALC             = 'Y-m-d';      // Date format used to calculate - DON'T MODIFY THIS - hould be: 'Y-m-d'

$LOADER                             =  '/images/loading_small.gif';
$LOADER_FULL_IMG                    =  "<img src='/images/loading_small.gif' alt='loading' border='0' />";

AddStyleSheet('/jslib/themes/base/ui.dialog.css');

$G_Table_Helpcenter_FAQs            = 'helpcenter_faqs';


$EMAIL_FROM_NAME                = "YogaLiveLink.com";
$EMAIL_FROM_EMAIL               = "support@YogaLiveLink.com";
$EMAIL_SUBJECT_ADMIN  		    = 'YogaLiveLink.com - Contact Request';
$EMAIL_SUBJECT_USER  		    = 'YogaLiveLink.com - Contact Request';
$EMAIL_MESSAGE_TEXT_ADMIN 	    = "A contact request has ocurred<br /><br /> <b>WH_ID:</b> @@WH_ID@@ <br /> <b>REQUESTOR_NAME:</b> @@REQUESTOR_NAME@@ <br /> <b>REQUESTOR_EMAIL:</b> @@REQUESTOR_EMAIL@@ <br /> <b>CATEGORY:</b> @@CATEGORY@@ <br /> <b>REQUEST:</b> @@REQUEST@@";
$EMAIL_MESSAGE_TEXT_USER        = "You have sent a request to the YogaLiveLink.com team. Below is a copy of that request for your records: <br /><br /> <b>CATEGORY:</b> @@CATEGORY@@ <br /> <b>REQUEST:</b> @@REQUEST@@";
$EMAIL_MESSAGE_HTML 	        = '';
$EMAIL_ADMIN_EMAIL              = 'support@YogaLiveLink.com';    
$EMAIL_CATEGORY_EMAIL_LIST      = array(
    'General'                   => 'support@YogaLiveLink.com',
    'Website Technical Issue'   => 'support@YogaLiveLink.com',
    'Course Question'           => 'support@YogaLiveLink.com',
    'Billing'                   => 'support@YogaLiveLink.com',
    );
	
$ICO_EMAIL                      = "/images/icons/YLL_icon_envelope.png";
$ICO_CHAT                       = "/images/icons/YLL_icon_liveChat.png";
$ICO_PHONE                      = "/images/icons/YLL_icon_phone.png";
$ICO_YOUTUBE                    = "/images/icons/YLL_icon_youTubeLogo.png";
$ICO_MEDICAL                    = "/images/icons/medical.png";

$ICO_LOGOUT                     = "/wo/images/menu_icons/delete.png";
$ICO_LOCK                       = "/office/images/touchpoint/ico_lock.png";
$ICO_NONE                       = "/office/images/spacer.gif";
#$ICO_YES                        = "/office/images/buttons/save.png";
#$ICO_NO                         = "/office/images/buttons/cancel.png";

$ICO_YES                        = "/images/icons/YLL_checkBoxChecked.png";
$ICO_NO                         = "/images/icons/YLL_checkBoxEmpty.png";

$ICO_UNKNOWN                    = "/office/images/spacer.gif";

$URL_SITE_LOGIN                 = "http://www.yogalivelink.com/office/";
$URL_SITE_HOME                  = "http://www.yogalivelink.com/";

$EMAIL_SUBSCRIPTIONS = array(
    'CONFIRM'   => 'Session Confirmation Messages|Explanation of what this email (POSITION 111) entails goes here.',
    'PROMO'     => 'Promotional Offers|Explanation of what this email (POSITION 222) entails goes here.',
    'OTHER'     => 'Other Messages|Explanation of what this email (POSITION 333) entails goes here.'
);
    
$EMAIL_SUBSCRIPTIONS_INSTRUCTOR = array(
    'SESSION'   => 'Session Confirmation Messages|Notification of sessions being booked or cancelled.',
    'PROFILE'   => 'Profile Changes|Notifications when profile changes have been approved or rejected by administrator.',
    'OTHER'     => 'Other Messages|Additional messages'
);
    
$EMAIL_CONTENT_TEMPLATES = array(
    'INS_PROFILE_APPROVED' => 13,
    'INS_PROFILE_REJECTED' => 14,
    
    'INS_ACCOUNT_APPROVED' => 13,
    'INS_ACCOUNT_REJECTED' => 14,    
);
    
    
$CSS_GREEN_DARK     = '#A3A548';
$CSS_GREEN_LIGHT    = '#D1D289';
$CSS_GREEN_WHITE    = '#EAE6CD';
$CSS_RED            = '#D04944';
$CSS_ORANGE         = '#F2925A';
$CSS_BLACK          = '#333333';



# TABLES
# ===============================================
$TABLE_sessions                         = 'sessions';
$TABLE_contacts                         = 'contacts';
$TABLE_instructors                      = 'instructor_profile';
$TABLE_instructor_profile               = 'instructor_profile';
$TABLE_instructor_profile_pending       = 'instructor_profile_pending';
$TABLE_instructor_checklist             = 'instructor_checklist';
$TABLE_salutations                      = 'contact_salutations';
$TABLE_timezones                        = 'time_zones';
$TABLE_helpcenter_faqs                  = 'helpcenter_faqs';
$TABLE_helpcenter_categories            = 'helpcenter_categories';
$TABLE_contact_discounts                = 'contact_discounts';
$TABLE_credits                          = 'credits';
$TABLE_session_checklists               = 'session_checklists';
$TABLE_products                         = 'store_products';
$TABLE_emergency_requests               = 'emergency_requests';

$TABLE_intake_form_standard             = 'intake_form_standard';
$TABLE_intake_form_therapy              = 'intake_form_therapy';

$TABLE_touchpoint_chat_online_status    = 'touchpoint_chat_online_status';


# PAGE LINKS
# ===============================================
$PAGE_customer_all_sessions     = '/office/website/sessions';
$PAGE_instructor_all_sessions   = '/office/website/instructor_sessions';



# ICAL SETTINGS
# ===============================================
$ICAL_FILENAME          = 'calendar.ics';
$ICAL_TITLE             = 'YogaLiveLink.com Event';
$ICAL_URL               = 'http://www.yogalivelink.com/office/';
$ICAL_DESCRIPTION       = '';
$ICAL_DESCRIPTION      .= "<br />";
$ICAL_DESCRIPTION      .= "Thank you for registering for your event. The details are below:<br />";
$ICAL_DESCRIPTION      .= "<br />";
$ICAL_DESCRIPTION      .= "Event Start: @@start_datetime_local@@ (@@USER_LOCAL_TIMEZONE@@)<br />";
$ICAL_DESCRIPTION      .= "Event End: @@end_datetime_local@@ (@@USER_LOCAL_TIMEZONE@@)<br />";
$ICAL_DESCRIPTION      .= "<br />";
$ICAL_DESCRIPTION      .= "Event Login:</strong> <a href='@@calendar_url@@'>@@calendar_url@@</a>";



$JS_CLOSE_WINDOW_SCRIPT = "
    function CloseOverlay() {
        var dialogNumber = '';
        var dialogID = '';
        if (window.frameElement) {
            if (window.frameElement.id.substring(0, 13) == 'appformIframe') {
                dialogNumber = window.frameElement.id.replace('appformIframe', '');
                dialogID = 'appform' + dialogNumber;
            }
        }
        top.parent.appformCloseOverlay(dialogID);
    }
    
    function RefreshParentAndCloseOverlay() {
        //top.parent.appformCloseOverlay(dialogID);
        //window.opener.location.reload()
        top.parent.location.reload()
        CloseOverlay();
    }
";


function ProcessStringForSeoUrl($STRING='') {
    $output = str_replace(array(' ', "'", '"'), array('_', '', ''), $STRING);
    return $output;
}


function BaseArraySpecialButtons($base_array, $Edit_Submit_Name, $positive_text='', $negative_text='', $positive_show=true, $negative_show=true)
{
    global $FORM_VAR, $JS_CLOSE_WINDOW_SCRIPT, $CSS_GREEN_DARK;
    
    $onclick 	        = "this.value='{$FORM_VAR['submit_click_text']}';";
    $id 		        = Form_GetIdFromVar($Edit_Submit_Name);
    $name 		        = $Edit_Submit_Name;
    $positive_text      = ($positive_text) ? $positive_text : 'SAVE';
    $btn_edit 	        = ($positive_show) ? MakeButton('positive', $positive_text, '', '', $id, $onclick, 'submit', $name) : '';
    
    $onclick 	        = "CloseOverlay();";
    $id 		        = 'btn_cancel';
    $name 		        = 'btn_cancel';
    $negative_text      = ($negative_text) ? $negative_text : 'CANCEL';
    $btn_cancel         = ($negative_show) ? MakeButton('negative', $negative_text, '', '', $id, $onclick, 'button', $name) : '';

    $base_array[] = "code|<br /><div style='border-top:1px solid {$CSS_GREEN_DARK};'>&nbsp;</div>";
    $base_array[] = "js|$JS_CLOSE_WINDOW_SCRIPT";
    $base_array[] = "info||{$btn_edit}&nbsp;&nbsp;{$btn_cancel}";
    
    return $base_array;
}

function AddBox($title, $content, $footer='', $class_title='')
{
    $footer = ($footer) ? "<div class='yogabox_box_footer'>{$footer}</div>" : "";
    $class_title = ($class_title) ? $class_title : 'yogabox_box_title';

    $output = "
    <div class='yogabox_outter_wrapper'>
        <div class='{$class_title}'>
            {$title}
        </div>
        <div class='yogabox_box_content'>
            {$content}
        </div>
        {$footer}
    </div>";

    return $output;
}


function AddBox_Error($title, $content, $footer='')
{
    $footer = ($footer) ? "<div class='yogabox_error_box_footer'>{$footer}</div>" : "";
    
    $output = "
    <div class='yogabox_error_outter_wrapper'>
        <div class='yogabox_error_box_title'>
            {$title}
        </div>
        <div class='yogabox_error_box_content'>
            {$content}
        </div>
        {$footer}
    </div>";
    
    return $output;
}

function AddBox_Type1($title, $content, $footer='')
{
    $footer = ($footer) ? "<div class='yogabox_box_footer'>{$footer}</div>" : "";
    
    $output = "
    <div class='yogabox_outter_wrapper'>
        <div class='yogabox_box_title'>
            {$title}
        </div>
        <div class='yogabox_box_content'>
            {$content}
        </div>
        {$footer}
    </div>";
    
    return $output;
}

function AddBox_Type2($title, $content, $image='')
{
    $image = ($image) ? "<img src='{$image}' border='0' height='50' alt='' />" : '';
    
    $output = "
    <div class='yogabox_outter_wrapper_type2' style='width:200px;'>
    <div class='yogabox_inner_wrapper_type2'>
        <div class='col touchpoint_icon'>
            {$image}
        </div>
        <div class='col'>
            &nbsp;&nbsp;&nbsp;
        </div>
        <div class='col'>
            <div><b>{$title}</b></div>
            <div><br />{$content}</div>
        </div>
        <div class='clear'></div>
    </div>
    </div>";
    
    return $output;
}
    
function AddBox_Type3($title, $link='', $image='')
{
    #$image = ($image) ? "<img src='{$image}' border='0' height='50' alt='' />" : '';
    $image = ($image) ? "<img src='{$image}' border='0' alt='' />" : '';
    
    $link_start = ($link) ? $link : '';
    $link_end   = ($link) ? '</a>' : '';
    
    $output = "
        <div class='box_type_3_wrapper'>
            <div class='box_type_3_icon'>{$link_start}{$image}{$link_end}</div>
            <div class='box_type_3_content'>{$link_start}{$title}{$link_end}</div>
        </div>";
    
    return $output;
}


function AddBox_Type4($title, $content, $footer='')
{
    $footer = ($footer) ? "<div class='yogabox_box_footer'>{$footer}</div>" : "";
    
    $output = "
    <div class='yogabox_outter_wrapper' style='border:1px solid #9E9D41; background-color:#EAE6CD;'>
        <div class='yogabox_box_title' style='background-color:#fff;'>
            {$title}
        </div>
        <div class='yogabox_box_content'>
            {$content}
        </div>
        {$footer}
    </div>";
    
    return $output;
} 
  
    
function MakeTable($data, $style_table='', $style_col_left='', $style_col_right='')
{  
    // mvp modified: added style conditions, trimed input, and created conditional output of lines (non-empty lines only)
    $style_table    = ($style_table)? " style='$style_table'" : '';
    $style_left     = ($style_col_left)? " style='$style_col_left'" : '';
    $style_right    = ($style_col_right)? " style='$style_col_right'" : '';
    $gap            = "&nbsp;&nbsp;&nbsp;";
    
    
    $output = "<table border='0' cellspacing='0' cellpadding='0'$style_table>";
    foreach ($data as $line) {
        list($part1, $part2)  = explode('|', $line . '|');
        $part1 = trim($part1);
        $part2 = trim($part2);
        if ($part1 or $part2) {
            $output .= "
<tr valign='top'>
    <td class='tbl_row_header' $style_left>{$part1}{$gap}</td>
    <td class='tbl_row_content' $style_right>{$part2}</td>
</tr>";
        }
    }
    $output .= "</table>";
    return $output;
}





function getClassExecuteLink($eq)
{
    // RAW (12-21) -- This function exactly mirrors the JS function of identical name - but usable for PHP.
    global $CLASS_EXECUTE_LINK_AJAX;
    return $CLASS_EXECUTE_LINK_AJAX . '?eq=' . $eq;
}

function getClassExecuteLinkNoAjax($eq)
{
    // RAW (12-21) -- This function exactly mirrors the JS function of identical name - but usable for PHP.
    global $CLASS_EXECUTE_LINK;
    return $CLASS_EXECUTE_LINK . '?eq=' . $eq;
}

function EchoScript($script)
{
    //echo "<script type='text/javascript'>{$script}</script>";
   echo "\n" . JavaScriptString($script) . "\n";  // modified by MVP
}

function formatSerialize(&$strItem, $strKey)
{
    $strItem = str_replace('&', '[amp;]',$strItem);
}
   
function formatSerializeRev(&$strItem, $strKey)
{
    $strItem = str_replace('[amp;]', '&',$strItem);
}
    
function strtocamel($str, $capitalizeFirst = true, $allowed = 'A-Za-z0-9') 
{
    return preg_replace(
        array(
            '/([A-Z][a-z])/e', // all occurances of caps followed by lowers
            '/([a-zA-Z])([a-zA-Z]*)/e', // all occurances of words w/ first char captured separately
            '/[^'.$allowed.']+/e', // all non allowed chars (non alpha numerics, by default)
            '/^([a-zA-Z])/e' // first alpha char
        ),
        array(
            '" ".$1', // add spaces
            'strtoupper("$1").strtolower("$2")', // capitalize first, lower the rest
            '', // delete undesired chars
            'strto'.($capitalizeFirst ? 'upper' : 'lower').'("$1")' // force first char to upper or lower
        ),
        $str
    );
}




function array_sort($array, $on, $order='SORT_DESC')
{
  $new_array = array();
  $sortable_array = array();

  if (count($array) > 0) {
      foreach ($array as $k => $v) {
          if (is_array($v)) {
              foreach ($v as $k2 => $v2) {
                  if ($k2 == $on) {
                      $sortable_array[$k] = $v2;
                  }
              }
          } else {
              $sortable_array[$k] = $v;
          }
      }

      switch($order)
      {
          case 'SORT_ASC':   
              #echo "ASC";
              asort($sortable_array);
          break;
          case 'SORT_DESC':
              #echo "DESC";
              arsort($sortable_array);
          break;
      }

      foreach($sortable_array as $k => $v) {
          $new_array[] = $array[$k];
      }
  }
  return $new_array;
} 




function MakeButton($CLASS, $TEXT, $LOCATION='', $IMAGE='', $ID='', $ONCLICK='', $TYPE='', $NAME='')
{
    # CLASS     => regular | negative | positive
    # TEXT      => text to display on button
    # LOCATION  => URL of onclick
    # IMAGE     => alternate image to the default selected from class

    $location   = ($LOCATION) ? "onclick=\"window.location='{$LOCATION}'\"" : '';
    $onclick    = ($ONCLICK && !$location) ? "onclick=\"{$ONCLICK}\"" : '';
    $id         = ($ID) ? "id='{$ID}'" : '';
	$name       = ($NAME) ? "name='{$NAME}'" : '';
    $type       = ($TYPE) ? $TYPE : 'button';
	$text       = ucwords(strtolower($TEXT));
    
    
    /*
    switch ($CLASS) {
        case 'positive':
            //$output = "\n<button type='{$type}' $location $onclick class='{$CLASS}' {$id} {$name}>{$image_out}&nbsp;&nbsp;{$text}</button>\n";
            $output = "\n<span class='buttonImg' $location $onclick {$id} {$name}><img src='/images/buttons/btn_how_works_off.png'></span>\n";
        break;
        case 'negative':
            $output = "\n<button type='{$type}' $location $onclick class='{$CLASS}' {$id} {$name}>{$image_out}&nbsp;&nbsp;{$text}</button>\n";
        break;
        case 'regular':
        default:
            $output = "\n<button type='{$type}' $location $onclick class='{$CLASS}' {$id} {$name}>{$image_out}&nbsp;&nbsp;{$text}</button>\n";
        break;
    }
    */
    
    
    
    
    
    
    switch ($CLASS) {
        case 'positive':
            $image = "/office/images/buttons/save.png";
        break;
        case 'negative':
            $image = "/office/images/buttons/cancel.png";
        break;
        case 'regular':
        default:
            $image = '';
        break;
    }

    $image_out = ($image)? "<img src='$image' alt='$text' />" : '';  //mvp added
    
    $output = "\n<button type='{$type}' $location $onclick class='{$CLASS}' {$id} {$name}>{$image_out}&nbsp;&nbsp;{$text}</button>\n";
    
    return $output;
}



function truncate ($string, $max = 50, $rep = '') 
{
    if (strlen ($string) > $max) {
        $leave = $max - strlen ($rep);
        $output = substr_replace($string, $rep, $leave);
    } else {
        $output = $string;
    }
    return $output;
}


function GenerateCode($STR_LENGTH=6)
{
    $characters = array(
    "A","B","C","D","E","F","G","H","J","K","L","M",
    "N","P","Q","R","S","T","U","V","W","X","Y","Z",
    "2","3","4","5","6","7","8","9");

    //make an "empty container" or array for our keys
    $keys = array();

    //first count of $keys is empty so "1", remaining count is 1-6 = total 7 times
    while(count($keys) < $STR_LENGTH) {
        //"0" because we use this to FIND ARRAY KEYS which has a 0 value
        //"-1" because were only concerned of number of keys which is 32 not 33
        //count($characters) = 33
        $x = mt_rand(0, count($characters)-1);
        if(!in_array($x, $keys)) {
           $keys[] = $x;
        }
    }
    
    $random_chars = '';
    foreach($keys as $key){
       $random_chars .= $characters[$key];
    }
    
    return $random_chars;
}







  // Set timezone
  date_default_timezone_set("UTC");
 
  // Time format is UNIX timestamp or
  // PHP strtotime compatible strings
  function dateDiff($time1, $time2, $precision = 6) 
  {
    // If not numeric then convert texts to unix timestamps
    if (!is_int($time1)) {
      $time1 = strtotime($time1);
    }
    if (!is_int($time2)) {
      $time2 = strtotime($time2);
    }
 
    // If time1 is bigger than time2
    // Then swap time1 and time2
    if ($time1 > $time2) {
      $ttime = $time1;
      $time1 = $time2;
      $time2 = $ttime;
    }
 
    // Set up intervals and diffs arrays
    $intervals = array('year','month','day','hour','minute','second');
    $diffs = array();
 
    // Loop thru all intervals
    foreach ($intervals as $interval) {
      // Set default diff to 0
      $diffs[$interval] = 0;
      // Create temp time from time1 and interval
      $ttime = strtotime("+1 " . $interval, $time1);
      // Loop until temp time is smaller than time2
      while ($time2 >= $ttime) {
	$time1 = $ttime;
	$diffs[$interval]++;
	// Create new temp time from time1 and interval
	$ttime = strtotime("+1 " . $interval, $time1);
      }
    }
 
    $count = 0;
    $times = array();
    // Loop thru all diffs
    foreach ($diffs as $interval => $value) {
      // Break if we have needed precission
      if ($count >= $precision) {
	break;
      }
      // Add value and interval 
      // if value is bigger than 0
      if ($value > 0) {
	// Add s if value is not 1
	if ($value != 1) {
	  $interval .= "s";
	}
	// Add value and interval to times array
	$times[] = $value . " " . $interval;
	$count++;
      }
    }
 
    // Return string with times
    return implode(", ", $times);
  }
