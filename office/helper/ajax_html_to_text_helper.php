<?php
require $_SERVER['DOCUMENT_ROOT'] . '/wo/wo_ajax_helper.php';

function mb_wordwrap($str, $width = 75, $break = "\n", $cut = false, $charset ='utf-8') 
{
    if ($charset === null) $charset = mb_internal_encoding();

    $pieces = split($break, $str);
    $result = array();
    foreach ($pieces as $piece) {
      $current = $piece;
      while ($cut && mb_strlen($current) > $width) {
        $result[] = mb_substr($current, 0, $width, $charset);
        $current = mb_substr($current, $width, 2048, $charset);
      }
      $result[] = $current;
    }
    return implode($break, $result);
}

$field_name     = $DATA['inputFieldName'];
$text           = $DATA[$field_name];
//$text           = $DATA['FORM_content_html'];

$head = TextBetween('<head>', '</head>', $text);

if ($head) {
    $text = mb_str_replace("<head>$head</head>", '', $text);
}


$E = chr(27);
$replace_array = array(
  '</p>' => $E,
  '<br>' => $E,
  '<br/>' => $E,
  '<br />' => $E,
  '</div>' => $E,
  '</li>' => $E,
  '</h1>' => $E,
  '</h2>' => $E,
  '</h3>' => $E,
  '</h4>' => $E,
  '</h5>' => $E,
  '</h6>' => $E,
  "\n"  =>  ''
);

$text = astr_replace($replace_array, $text);
$text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

$tags = TextBetweenArray('<', '>', $text);

$tags = array_unique($tags);


foreach ($tags as $tag) {
    $text= mb_str_replace("<$tag>", '', $text);
}


$text = mb_ereg_replace("\s+", ' ', $text);
$text = mb_ereg_replace("\s+$E", "$E", $text);
$text = mb_ereg_replace("$E\s+", "$E", $text);
$text = mb_ereg_replace("$E$E$E+", "$E$E", $text);
$text = mb_ereg_replace($E, "\n", $text);

$text = mb_wordwrap(trim($text));

echo $text;
