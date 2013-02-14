<div style="background-color:#eef;padding:2em;">
<h2>Create Data List Class</h2>
<?php
if (empty($FormPrefix)) {
    include "$LIB/form_helper.php";
}


$form = array(
  "form|$SCRIPT_URI|post",
  'text|Class Name|class_name|Y|30|255',
  'text|Filename<br />(From Root)|filename|Y|60|255',
  'textarea|Variables<br />(One per Line)|variables|Y|30|8',
  'submit|Create Class|SUBMIT|class="contentsubmit s15"',
  'endform'
);


$template =<<<LBL1
&lt;?php
// file class.@CLASSNAME@.php

class @CLASSNAME@ extends Lib_DataList
{
    public function __construct()
    {
        \$this->File = '@FILENAME@';
        \$this->Field_Titles = array(
            @FIELDTITLES@
        );
        parent::__construct();
    }

    public function SetFormArray()
    {
        \$this->Form_Data_Array = array(
            @FORMITEMS@
        );
    }

}
LBL1;

$ERROR = '';

if (HaveSubmit('SUBMIT')) {
    $formdata = ProcessFormNT($form, $ERROR);
}
WriteError($ERROR);
echo OutputForm($form, HaveSubmit('SUBMIT'));

if (HaveSubmit('SUBMIT') and !$ERROR) {
    AddScriptOnload("setAutoTextAreaHeight('CTEXT'); mainOnload();");
    $template = str_replace('@FILENAME@', $formdata['filename'], $template);
    $template = str_replace('@CLASSNAME@', $formdata['class_name'], $template);
    $vars = explode("\n", $formdata['variables']);
    TrimArray($vars);
    $vars = array_filter($vars);
    $varlist = '';
    $formlist = '';
    foreach ($vars as $var) {
        $varlist  .= "            '$var' => '" . NameToTitle($var) . "',\n";
        $formlist .= "            'text|" . NameToTitle($var) . "|$var|N|60|255',\n";
    }
    $template = str_replace('@FIELDTITLES@', trim($varlist), $template);
    $template = str_replace('@FORMITEMS@', trim($formlist), $template);
    echo '<textarea id="CTEXT" cols="80" rows="20">' . $template . '</textarea>';
}
?>
</div>