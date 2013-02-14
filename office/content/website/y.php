<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: y.php
    Description: UNKNOWN DEV FILE
==================================================================================== */

global $ROOT;

$Sessions_Id        = 4579;
$Upload_Directory   = '/office/session_videos';
    
$filename   = $Sessions_Id . '.*';
//$search     = "{$ROOT}{$Upload_Directory}/{$filename}";
$search     = "{$Upload_Directory}/{$filename}";
$filefound  = glob ($search);
//$filefound  = file_exists($search);



echo "<h2>";
echo "<br /><br /><br /><br /><hr><br /><br /><br />";
echo "<br />search ===> $search";
echo "<br />filefound ===> $filefound";
echo "<br />root ===> $ROOT";
echo ArrayToStr($filefound);
echo "</h2>";
?>