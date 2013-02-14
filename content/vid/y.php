<?php
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