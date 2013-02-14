
function mnuFlashClick(menu) {
    var loadUrl = "/x/content/" + menu + ".php";
    var ajax_load = "<img src='images/upload.gif' alt='loading...' />";
    
    //alert(loadUrl);
    $("#content_area").fadeOut('fast').load(loadUrl).fadeIn('slow');
}


