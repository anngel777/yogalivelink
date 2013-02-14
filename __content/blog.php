<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: blog
    Description: Blog page - all blog stuff handled by this one file. Blog calls 
                 a remote website to load content - see blog documentation for 
                 more information
==================================================================================== */

if (!Get('bypass')) {
    exit();
}

global $PAGE, $BID;


// EDIT THIS: your auth parameters
$client_id          = '386'; //584
$password           = 'k87dg$#dHJn1';
$api_version        = '1.0';                                        // API version to use
$page_link          = "{$PAGE['basename']}{$PAGE['pagename']}";
$website_blog_id    = $BID;
$stylesheet         = 'StyleYogaLiveLink';                          // Stylesheet class to include
$image_dir          = 'yogalivelink';                               // Remote directory to get images from

$username           = 'joe'; 
$keyword            = 'ozh';        // optional keyword
$format             = 'simple';     // output format: 'json', 'xml' or 'simple'


// EDIT THIS: the URL of the API file
$api_url = 'http://webmanager.whhub.com/api_blog.php';

// Init the CURL session
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_HEADER, 0);            // No header in the result
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return, do not echo result
curl_setopt($ch, CURLOPT_POST, 1);              // This is a POST request
curl_setopt($ch, CURLOPT_POSTFIELDS, array(     // Data to POST
		'api_version'       => $api_version,
		'keyword'           => $keyword,
		'format'            => $format,
		'action'            => 'loadblogsimple',
		'username'          => $username,
		'password'          => $password,
        'client_id'         => $client_id,
        'page_link'         => $page_link,
        'website_blog_id'   => $website_blog_id,
        'stylesheet'        => $stylesheet,
        'image_directory'   => $image_dir,
        'link_get_vars'     => ';bypass',
	));

// Fetch and return content
$data = curl_exec($ch);
curl_close($ch);

// Do something with the result. Here, we just echo it.
$content    = "<div style='padding-left:150px;'>";
$content   .=  $data;
$content   .= "</div>";



// ---------- GET CONTENT FROM DATABASE AND SWAP INTO PAGE ----------
AddSwap('@@CONTENT@@',$content);
AddSwap('@@PAGE_HEADER_TITLE@@','');
?>