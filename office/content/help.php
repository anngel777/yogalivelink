<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: help.php
    Description: View help files for using the admin system
==================================================================================== */


function FAQs()
{
    global $SQL;
    $TABLE_helpcenter_faqs          = 'helpcenter_faqs';
    $TABLE_helpcenter_categories    = 'helpcenter_categories';

    $records_faq = $SQL->GetArrayAll(array(
        'table' => $TABLE_helpcenter_faqs,
        'keys'  => '*',
        'where' => "type_administrator=1 AND `show_on_website`=1 AND `active`=1",
    ));
    
    $content = '';
    
    $tab_content_faqs = "<ul class='faqs'>";
    foreach ($records_faq as $record) {
        
        $answer = nl2br($record['answer']);
        
        $tab_content_faqs .= "
        <li>
            <a class='faq_q'>{$record['question']}</a>
            <div class='faq_a' style='display: none;'>{$answer}</div>
        </li>";
    }
    $tab_content_faqs .= "</ul><br /><br />";
    $content .= $tab_content_faqs;
    
    return $content;
}

AddStyle("
    .faq_a {
        border:  1px solid #ccc;
        padding: 5px;
    }
");

$script = "
    function InitializeOnReady_Profile_CustomerProfileHelpCenter() {
        $('.faq_q').bind('click', function() {
            $(this).parent().find('div').toggle();
        });
    }
    
    InitializeOnReady_Profile_CustomerProfileHelpCenter();
    ";
    
AddScriptOnReady($script);

$output = '';
$output .= "<div style='font-size:22px; font-weight:bold;'>ALL HELP AND INSTRUCTIONS</div>";
$output .= "<div style='min-width:500px;'>&nbsp;</div>"; 
$output .= FAQs();
$output .= "<div style='height:100px;'>&nbsp;</div>"; 
echo $output;
?>