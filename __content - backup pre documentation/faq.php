<?php


function FAQs()
{
    global $SQL;
    $TABLE_helpcenter_faqs          = 'helpcenter_faqs';
    $TABLE_helpcenter_categories    = 'helpcenter_categories';

    $records_faq = $SQL->GetArrayAll(array(
        'table' => $TABLE_helpcenter_faqs,
        'keys'  => '*',
        'where' => "`show_on_website`=1 AND `active`=1",
    ));
    
    $records_categories = $SQL->GetArrayAll(array(
        'table' => $TABLE_helpcenter_categories,
        'keys'  => '*',
        'where' => "`active`=1",
    ));
    
    
    $content = '';
    foreach ($records_categories as $category) {
        $content .= "<div><h1>{$category['title']}</h1></div>";
        
        $tab_content_faqs = "<ul class='faqs'>";
        foreach ($records_faq as $record) {
        
            $all_categories     = ",{$record['categories']},";
            $search_categories  = ",{$category['helpcenter_categories_id']},";
            
            if (strpos($all_categories, $search_categories) !== false) {
                $tab_content_faqs .= "
                <li>
                    <a class='faq_q'>{$record['question']}</a>
                    <div class='faq_a' style='display: none;'>{$record['answer']}</div>
                </li>";
            }
        }
        $tab_content_faqs .= "</ul><br /><br />";
        $content .= $tab_content_faqs;
    }
    
    $content .= "<div><h1>Other</h1></div>";
    $tab_content_faqs = "<ul class='faqs'>";
    foreach ($records_faq as $record) {
        if ($record['categories'] == '') {
            $tab_content_faqs .= "
                <li>
                    <a class='faq_q'>{$record['question']}</a>
                    <div class='faq_a' style='display: none;'>{$record['answer']}</div>
                </li>";
        }
    }
    $tab_content_faqs .= "</ul><br /><br />";
    $content .= $tab_content_faqs;
    
    
    
    
    
    
    $title      = 'FREQUENTLY ASKED QUESTIONS';
    $content    = "<div id='helpcenter_faq'>$content<div>";
    $output     = AddBox($title, $content);
    
    return $output;
}

$script = "
    //function InitializeOnReady_Profile_InstructorProfileHelpCenter() {
        $('.faq_q').bind('click', function() {
            $(this).parent().find('p').toggle();
        });
    //}
    ";
    
AddScriptOnReady($script);
echo FAQs();
?>