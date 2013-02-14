<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: chat.php
    Description: Initiate a chat window - DEV
==================================================================================== */

# NOTES
# ======================================================================
/*
#
http://www.ajaxdaddy.com/demo-jquery-shoutbox.html

This is some great code, has a huge memory leak issue. Since they are adding a datetime number as they pull the chat.txt every 8th of a second, it caches that page everytime in your end users temporary internet cache. The only solution is to make a seperate .php page that pulls the .txt file, and set the .php file to expire several days in the past, and remove the datetime variable being added at the end of .txt.

You can fix the utf-8 problem by replacing line 39 in daddy-shoutbox.php with this.: $data['message'] = htmlentities(stripslashes($_POST['message']), ENT_QUOTES, "UTF-8" );

If you're having issues with this script not refreshing in IE, the following code might help: function refresh() { var stamp = new Date(); stamp = stamp.getTime(); $.getJSON(files+"daddy-shoutbox.php?action=view&time=" + lastTime + "&stamp=" + stamp
#
*/
# ======================================================================
# ======================================================================
# ======================================================================




# HTML CONTENT
# ======================================================================
$script_location = "./shoutbox/jquery-shoutbox/daddy-shoutbox.php?action=add";

$content = <<<CONTENT
  <center>
  <div id="daddy-shoutbox">
    <div id="daddy-shoutbox-list"></div>
    <br />
    <form id="daddy-shoutbox-form" action="{$script_location}" method="post"> 
        Name: {$_SESSION['USER_LOGIN']['USER_NAME']}<input type="hidden" name="nickname" value="{$_SESSION['USER_LOGIN']['USER_NAME']}" />
        <br /><br />
        Comment: <textarea id="daddy-shoutbox-message" name="message" cols="30" rows="3"></textarea>
    <input type="submit" value="Submit" />
    <span id="daddy-shoutbox-response"></span>
    </form>
  </div>
  </center>
CONTENT;
echo $content;





# SCRIPT
# ======================================================================
AddScriptInclude("/jslib/jquery.form.js");

$script = <<<SCRIPT

    var count = 0;
    var files = './shoutbox/jquery-shoutbox/';
    var lastTime = 0;
    
    function prepare(response) {
//alert('response');
//alert(response.message);
      var d = new Date();
      count++;
      d.setTime(response.time*1000);
      var mytime = d.getHours()+':'+d.getMinutes()+':'+d.getSeconds();
      var string = '<div class="shoutbox-list" id="list-'+count+'">'
          + '<div class="shoutbox-list-time">'+mytime+'</div>'
          + '<div class="shoutbox-list-nick">'+response.nickname+':</div>'
          + '<div class="shoutbox-list-message">'+response.message+'</div>'
          + '<div class="clear"></div>'
          +'</div>';
      
      return string;
    }
    
    function success(response, status)  { 
//alert('success');
//alert(response);
//alert(status);
      if(status == 'success') {
        lastTime = response.time;
        $('#daddy-shoutbox-response').html('<img src="'+files+'images/accept.png" />');
        $('#daddy-shoutbox-list').append(prepare(response));
        
        $('#daddy-shoutbox-message').attr('value', '').focus();
        //$('input[@name=message]').attr('value', '').focus();
        
        $('#list-' + count).fadeIn('slow');
        
        // SCROLL TO BOTTOM OF DIV
        $("#daddy-shoutbox-list").attr({ scrollTop: $("#daddy-shoutbox-list").attr("scrollHeight") });
        
        timeoutID = setTimeout(refresh, 3000);
      }
    }
    
    function validate(formData, jqForm, options) {
//alert('validate');
      for (var i=0; i < formData.length; i++) { 
          if (!formData[i].value) {
              alert('Please fill in all the fields'); 
              $('input[@name='+formData[i].name+']').css('background', 'red');
              return false; 
          } 
      } 
      $('#daddy-shoutbox-response').html('<img src="'+files+'images/loader.gif" />');
      clearTimeout(timeoutID);
    }

    function refresh() {
//alert('refresh');
      $.getJSON(files+"daddy-shoutbox.php?action=view&time="+lastTime, function(json) {
        if(json.length) {
          for(i=0; i < json.length; i++) {
            $('#daddy-shoutbox-list').append(prepare(json[i]));
            $('#list-' + count).fadeIn('slow');
            
            // SCROLL TO BOTTOM OF DIV
            $("#daddy-shoutbox-list").attr({ scrollTop: $("#daddy-shoutbox-list").attr("scrollHeight") });
          }
          var j = i-1;
          lastTime = json[j].time;
        }
        //alert(lastTime);
      });
      timeoutID = setTimeout(refresh, 3000);
    }
SCRIPT;
AddScript($script);


$script = <<<SCRIPT
    var options = { 
      dataType:       'json',
      beforeSubmit:   validate,
      success:        success
    }; 
    $('#daddy-shoutbox-form').ajaxForm(options);
    timeoutID = setTimeout(refresh, 100);
SCRIPT;
addScriptOnReady($script);

# STYLE
# ======================================================================
$style = <<<STYLE
    #daddy-shoutbox {
      padding: 10px;
      background: #3E5468;
      color: white;
      width: 600px;
      font-family: Arial,Helvetica,sans-serif;
      font-size: 11px;
    }
    .shoutbox-list {
      border-bottom: 1px solid #627C98;
      
      padding: 5px;
      display: none;
    }
    #daddy-shoutbox-list {
      text-align: left;
      margin: 0px auto;
      height: 200px;
      overflow: auto;
      overflow: -moz-scrollbars-vertical;
        overflow-y: scroll;
        border:1px solid #fff;
    }
    #daddy-shoutbox-form {
      text-align: left;
      
    }
    .shoutbox-list-time {
      color: #8DA2B4;
      float:left;
      display:none;
    }
    .shoutbox-list-nick {
      margin-left: 5px;
      font-weight: bold;
      color:#dedede;
      float:left;
    }
    .shoutbox-list-message {
      margin-left: 5px;
      font-size:13px;
      float:left;
    }
    .clear {
        clear:both;
    }
STYLE;
AddStyle($style);