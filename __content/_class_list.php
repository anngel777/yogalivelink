<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: _class_list.php
    Description: DEVELOPER - Get a list of all class files in the /classes/ directory
==================================================================================== */

$ENABLED = false;

function recurseDir($dir, $strip_dir='') {
	if(is_dir($dir)) {
		if($dh = opendir($dir)){
			while($file = readdir($dh)){
				if($file != '.' && $file != '..'){
					if(is_dir($dir . $file)){
						
                        $dir_output = str_replace($strip_dir, '', $dir);
                        echo '<br /><br /><b>' . $dir_output . $file . '</b>';
						
                        // since it is a directory we recurse it.
						recurseDir($dir . $file . '/', $strip_dir);
					}else{
                        $dir_output = str_replace($strip_dir, '', $dir);
						//echo '<br />' . $dir_output . $file;   
                        echo '<br />' . $file;   
			 		}
				}
	 		}
		}
        closedir($dh);         
    }
}

if ($ENABLED) {
    global $ROOT;
    $dir = "{$ROOT}/classes/";
    $output = "<h1>DIRECTORY LIST = {$dir}</h1>";
    echo $output;
    recurseDir($dir, $ROOT);
} else {
    echo "<h1>FILE NOT ENABLED</h1>";
}

?>