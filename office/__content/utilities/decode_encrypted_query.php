<h1>Decode Encrypted Query</h1>
<?php

$form = "
  form|@@PAGELINKQUERY@@|post|$E
  xxtext|Optional Key|key|N|12|80|$E
  text|Encrypted Query|query|Y|80|1024|$E
  submit|Submit|SUBMIT|$E
  endform|$E
";


echo OutputForm($form, Post('SUBMIT'));

if (Post('SUBMIT')) {
    $array = ProcessFormNT($form, $ERROR);
    $eq = $array['query'];

    $query_array = GetEncryptQuery($eq, false);
    AddError($ERROR);
    if (!$ERROR) {
        echo "<h3>Result</h3>\n" . ArrayToStr($query_array);
        if (!empty($query_array['parameters'])) {
            $BC = new BaseClass;
            $BC->SetParameters(array($query_array['parameters']));
            echo "<h3>Parameters</h3>\n" . ArrayToStr($BC->Parameters);
        }
    }
}
