<?php 
/*** Bandwidth Tester 0.92 ***/
/* Please wait for 1.0 patiently! */


// How many bytes to test with. Mimimum=70. 128KB=131072. 1MB=1048576
$testsize = 131072;


header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");                  // Date in the past
header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");     // always modified
header ("Cache-Control: no-cache, must-revalidate");                // HTTP/1.1
header ("Pragma: no-cache");                                        // HTTP/1.0



/* How does it work? The script generates a variable amount of random data
 * sends it to the client and measures the time taken for transmission. The
 * bandwidth is then calculated from the time using a simple algorithm.
 *
 * WARNING: This script can bog down your server - as absolutely NO
 *          optimization was used.
 *
 *
 * This script is best run on the Zend PHP Engine, with Zend Optimizer.
 * Any improvement in performance is not guaranteed with other
 * PHP Engines.
 * 
 * History: 
 *          0.9 - First public release
 *         0.91 - Reduced the size of the timing code
 *         0.92 - Reduced the size of the timing code even more
 * Forecast:
 *       0.921a - Adding a smaller test before the main to make results more accurate and to adjust test data according to first results
 *          1.0 - Looking to adding optimization code
 *          1.1 - Adding template support
 *          1.2 - Adding web-based administration
 */
 
 
 
 
// First, initialize the test comment


// seed random
srand ((double) microtime() * 1000000);




if($testsize<70) {
    die("<script>alert('The test string size is less than 70. Cannot test.')</script>");
}

$realtestsize = $testsize - 70;

function GetTestString($drealtestsize)
{
    $duhteststring = "<!"."--";
    for($i=0;$i<$drealtestsize; $i++){
        $duhteststring .= generatekeycode();
    }
    $duhteststring .= "-"."->";
    return $duhteststring;
}


function CalculateBandwidth($Ditt,$Dott)
{
    $Datasize=$Dott;
    $LS=$Datasize/$Ditt;
    $kbps=(($LS*8)*10*1.02)/10;
    $mbps=$kbps/1024;
    if ($mbps>=1) {
        $speed=$mbps." Mbps aka ".$kbps." Kbps";
    } else {
        $speed=$kbps." Kbps aka ".$mbps." Mbps";
    }
    $speed .="<br>Time taken to test connection: ".(($Ditt*1024)/1000)." Seconds <br>A number used to determine your speed: ".$LS."<br>Another number used to determine your speed: ".$Ditt."<br>Tested your connection with ".$Datasize."Bytes/".($Datasize/1024)."KB/".($Datasize/1048576)."MB of random data<br>";
    return $speed;
}

function generatekeycode()
{
    // srand ((double) microtime() * 1000000);
    
    // Made the randomizer a little more "random"! :)
    srand ((double) microtime() * rand(100000,1000000) / rand(1,15));
    $tester = rand(33,255);
    if($tester==45)return generatekeycode();
    return chr($tester);
}
?>






<html>
<head><title>Bandwidth Tester</title></head>
<body>

<?php
if($HTTP_SERVER_VARS["REQUEST_METHOD"]=="GET" && $HTTP_GET_VARS["execute"]!="1") {
    
    $OUTPUT = <<<OUTPUT
        <form action="{$HTTP_SERVER_VARS["SCRIPT_NAME"]}" method="GET">
            <input type="submit" value="Click Here To Begin Testing" onClick="this.value='Please wait while your request is being processed, it may take a while'">
            <input type="hidden" name="execute" value="1">
            <input type="hidden" name="DO.NOT.CACHE" value="'.rand(255,65536).'">
        </form>
OUTPUT;
    
} elseif ($HTTP_GET_VARS["execute"]=="1") {

    $teststring = GetTestString($realtestsize);
    $OUTPUT = <<<OUTPUT
        <form method="POST" action="{$HTTP_SERVER_VARS["SCRIPT_NAME"]}">
            <input type="hidden" name="td" value="No Test">
            <input type="button" value="Please wait while your request is being processed, it may take a while">
        </form>
        <script language="JavaScript">
            var Hi = new Date();
        </script>
        {$teststring}
        <script language="JavaScript">
            var Bye = new Date();
            var NiHao = new Array(Hi.getTime(),Bye.getTime());
            var Factor=1024;
            if(NiHao[1]==NiHao[0])
                Ditt=0;
            else
                Ditt=(NiHao[1]-NiHao[0])/Factor;
                document.forms[0].elements[0].value=Ditt;
                document.forms[0].submit();
        </script>

    <p>Tested. Now processing your request....</p>
OUTPUT;
    
} elseif ($HTTP_SERVER_VARS["REQUEST_METHOD"]=="POST" && $HTTP_POST_VARS["td"]>0) {

    $bandwidth = CalculateBandwidth($HTTP_POST_VARS["td"],$testsize);
    $OUTPUT = <<<OUTPUT
        <p>We have tested your Internet connection.<br>
        The speed to which you connected to us is {$bandwidth}
        Thank you!<br>
        </p>
OUTPUT;
    
} elseif ($HTTP_SERVER_VARS["REQUEST_METHOD"]=="POST"&&$HTTP_POST_VARS["td"]==0) {

    $rand_num = rand(255,65536);
    $bandwidth = CalculateBandwidth($HTTP_POST_VARS["td"],$testsize);
    $OUTPUT = <<<OUTPUT
        <p>We were unable to test your connection speed.<br>It was too fast to measure.<br>
        <a href="{$HTTP_SERVER_VARS["SCRIPT_NAME"]}?execute=1&DO.NOT.CACHE={$rand_num}" onClick="this.innerText='The system is now generating the random test data to benchmark your connection speed. It will take a while.'">If you would like to try testing again, click here.</a></p>
        <p>{$bandwidth}</p>
OUTPUT;
    
}

echo $OUTPUT;

?>
</body>
</html>