<?php
//FAQ requires toggleDisplay and hideGroup Javascript functions
// needs $faqfile

/*
Styles
.faqs li {
    margin-top : 0.5em;
}

.faqs li a.faq_question {
  text-decoration : none;
  font-weight : bold;
}
.faq_answer {
  margin:5px 2em 1em 2em;
  display:none
}
*/

if(empty($faqfile)) {
    $faqfile = 'faq.dat';
}

//$js_toggle_function = "\$('#@').slideToggle()";  // jquery function
if(empty($js_toggle_function)) {
    $js_toggle_function = "toggleDisplay('@')";
}

//-----------read the directory----------

$FAQdata = file_get_contents("$ROOT{$SITECONFIG['listdir']}/$faqfile");

$FAQs = TextBetweenArray('<faq>','</faq>',$FAQdata);

//---------------Output the FAQs-------

$count=0;
if ($FAQs) {

    echo '<ol class="faqs">';

    foreach ($FAQs as $FAQ) {
        $question = TextBetween('<ques>','</ques>',$FAQ);
        $answer   = TextBetween('<ans>','</ans>',$FAQ);

        if ($question and $answer) {
            $count++;
            $toggle = str_replace('@', "faq$count", $js_toggle_function);
            printqn("
<li>
<a href=`#` class=`faq_question` onclick=`$toggle; return false;`>$question</a>
<div class=`faq_answer` id=`faq$count`>
$answer
</div>
</li>

");

        }
    }
    echo '</ol>';
}
//<script type="text/javascript">hideGroup('faq');</script>