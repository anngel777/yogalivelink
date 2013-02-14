<?php
/* ====================================================================================
        Created: April 4, 2012
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: lib_sanitize.php
    Description: Used to sanitize form inputs on a global scale


    ========== UPDATE LOG ==========
    04-12-2012 - RAW - Created file
    
    
========== SAMPLE URLS - for YogaLiveLink.com ==========
//https://www.yogalivelink.com/office/website/sessions_schedule;type=instructor;customer=on;instructor_whid=1000147;retpage=instructors%3CSCRIPT%3Ealert%28%27asdf1234%27%29%3C/SCRIPT%3E
//https://www.yogalivelink.com/office/website/sessions_schedule;sanitize=true;type=instructor;customer=on;instructor_whid=1000147;retpage=instructors%3CSCRIPT%3Ealert%28%27asdf1234%27%29%3C/SCRIPT%3E

==================================================================================== */

function SanitizeArray(&$ARR, $show_array = false) {
    // FUNCTION :: Sanitize any array of values (SINGLE DIMENSION)
    if (is_array($ARR)) {
        if ($show_array) { echo '<hr>'.ArrayToStr($ARR); };
        $ARR = array_map("filter", $ARR);                      // call filter function
        if ($show_array) { echo '<hr>'.ArrayToStr($ARR); };
    }
}


function SanitizePage($show_array = false) {
    // FUNCTION :: Sanitize all $_POST variables by calling the proper function
    global $PAGE;
    
    if ($show_array) { echo '<hr>'.ArrayToStr($PAGE); };
    
    foreach ($PAGE as $k=>$v) {
        switch ($k) {
            case 'query':
            case 'printversionlink':
            case 'ajaxlink':
            case 'pagelink':
            case 'pagelinkquery':
            case 'url':
                $v = urldecode($v);     // convert chars back to HTML (i.e. '%3C' to  '<');
            break;
            
            default:
            break;
        }
        
        $PAGE[$k] = filter($v);                      // call filter function
    }
    
    
    if ($show_array) { echo '<hr>'.ArrayToStr($PAGE); };
}

function SanitizePost($show_array = false) {
    // FUNCTION :: Sanitize all $_POST variables by calling the proper function
    
    if ($show_array) { echo '<hr>'.ArrayToStr($_POST); };
    $post   = array_map("filter", $_POST);                      // call filter function
    $_POST  = $post;                                            // stick filtered values back into $_POST
    if ($show_array) { echo '<hr>'.ArrayToStr($_POST); };
}

function SanitizeGet($show_array = false) {
    // FUNCTION :: Sanitize all $_GET variables by calling the proper function
    
    if ($show_array) { echo '<hr>'.ArrayToStr($_GET); };
    $get    = array_map("filter", $_GET);                       // call filter function
    $_GET   = $get;                                             // stick filtered values back into $_GET
    if ($show_array) { echo '<hr>'.ArrayToStr($_GET); };
}

function SanitizeServer($show_array = false) {
    // FUNCTION :: Sanitize all $_GET variables by calling the proper function
    
    if ($show_array) { echo '<hr>'.ArrayToStr($_SERVER); };
    $server     = array_map("filter", $_SERVER);                // call filter function
    $_SERVER    = $server;                                      // stick filtered values back into $_SERVER
    if ($show_array) { echo '<hr>'.ArrayToStr($_SERVER); };
}

function filter($data) {
    // FUNCTION :: Call all the various filtering functions for the sent in variable
    // NOTE --> currently not using strip tags because that still eaves the text between the tags and we don't want that
    
    //$data = htmlspecialchars_decode($data);
    
    $data = trim(remove_HTML($data));
    
    //$data - filter_var($data, FILTER_SANITIZE_SPECIAL_CHARS);
    //$data = trim(htmlentities(strip_tags($data)));
    
    /*
    if (get_magic_quotes_gpc())
        $data = stripslashes($data);
 
    $data = mysql_real_escape_string($data);
    */
    return $data;
}


function remove_HTML($s , $keep = '' , $expand = 'script|style|noframes|select|option'){
    // FUNCTION :: remove_HTML --> removes all tags defined and removes content between those tags

    
    /**///prep the string
    $s = ' ' . $s;
   
    /**///initialize keep tag logic
    if(strlen($keep) > 0){
        $k = explode('|',$keep);
        for($i=0;$i<count($k);$i++){
            $s = str_replace('<' . $k[$i],'[{(' . $k[$i],$s);
            $s = str_replace('</' . $k[$i],'[{(/' . $k[$i],$s);
        }
    }
   
    //begin removal
    /**///remove comment blocks
    while(stripos($s,'<!--') > 0){
        $pos[1] = stripos($s,'<!--');
        $pos[2] = stripos($s,'-->', $pos[1]);
        $len[1] = $pos[2] - $pos[1] + 3;
        $x = substr($s,$pos[1],$len[1]);
        $s = str_replace($x,'',$s);
    }
   
    /**///remove tags with content between them
    if(strlen($expand) > 0){
        $e = explode('|',$expand);
        for($i=0;$i<count($e);$i++){
            while(stripos($s,'<' . $e[$i]) > 0){
                $len[1] = strlen('<' . $e[$i]);
                $pos[1] = stripos($s,'<' . $e[$i]);
                $pos[2] = stripos($s,$e[$i] . '>', $pos[1] + $len[1]);
                $len[2] = $pos[2] - $pos[1] + $len[1];
                $x = substr($s,$pos[1],$len[2]);
                $s = str_replace($x,'',$s);
            }
        }
    }
   
    /**///remove remaining tags
    while(stripos($s,'<') > 0){
        $pos[1] = stripos($s,'<');
        $pos[2] = stripos($s,'>', $pos[1]);
        $len[1] = $pos[2] - $pos[1] + 1;
        $x = substr($s,$pos[1],$len[1]);
        $s = str_replace($x,'',$s);
    }
   
    /**///finalize keep tag
    if(strlen($keep) > 0){
    for($i=0;$i<count($k);$i++){
        $s = str_replace('[{(' . $k[$i],'<' . $k[$i],$s);
        $s = str_replace('[{(/' . $k[$i],'</' . $k[$i],$s);
    }
    }
   
    return trim($s);
} 