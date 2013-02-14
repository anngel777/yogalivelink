<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: email_preview.php
    Description: Preview how an email will look
==================================================================================== */

$type               = Get('type');
$source_field       = Get('source_field');
$subject_field      = Get('subject_field');
$source_dialog_id   = Get('source_dialog_id');
$tag                = ($type=='html')? 'div' : 'pre';
$iframe_id          = '#appformIframe'.$source_dialog_id;


echo "<h2 class=\"pagehead\">Email Preview &mdash; <span id=\"subject\"></span></h2>";
echo "<$tag id=\"email_content\"></$tag>";

AddScript("

  $(function() {
      var content = $('$iframe_id', top.document).contents().find('#$source_field').val();
      var subject = $('$iframe_id', top.document).contents().find('#$subject_field').val();
      $('#email_content').html(content);
      $('#subject').html(subject);
  });

");