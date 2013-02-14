<?php
ini_set('display_errors','1');
if (!session_id()) {
    ini_set("url_rewriter.tags",'');
    ini_set("session.use_trans_sid", false);
    session_start();
}

include $_SERVER['DOCUMENT_ROOT'] .'/lib/mvptools.php';

$ADMIN_SUPER_USER = Session('ADMIN_LOGIN_SUPER_USER');

include "$ROOT/office/config/db_info.php";
include "$LIB/dbi_helper.php";
include "$LIB/form_helper.php";


if (!$ADMIN_SUPER_USER) {
    include "$ROOT/global/global_su_auth.php";
}

$ADMIN_LOGIN_NAME = Session('ADMIN_LOGIN_NAME');

SetPost('TABLE NEWENTRY');

$tables = db_GetTables();
$table_select = '<select name="TABLE" onchange="tableSelect(this.value);"><option value="">-- Select --</option>';
foreach ($tables as $table) {
    $select = ($TABLE==$table)? ' selected="selected"' : '';
        $table_select .= qqn("<option value=`$table`$select>$table</option>");
}
$table_select .= '</select>';


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>Database Table Information Setup</title>
  <style type="text/css">
    body{background-color:#eee; font-family:Arial,Sans Serif; margin:0px;}
    #header {padding:10px 1em; background-color:#888; border-bottom:1px solid #fff;}
    #content{padding:1em;}
    h1{color:#fff; font-size:1.1em; padding:0px; margin:0px}
    h1 span, h2 span {color:#f00;}
    h2{font-size: 1.1em;}
    td{padding:2px 10px; background-color: #fff;}
    th{padding:2px 10px; background-color:#888; color:#fff; font-weight:bold;}
    tr.odd td{background-color:#eef;}
    a.stdbutton {font-size:9pt; text-align:center; text-decoration:none; padding:0.2em 0.4em;
    background-color:#eee; color:#000; border:1px solid; border-color: #ddd #888 #777 #ccc;}
    a.stdbutton:active{border-color:#777 #ccc #ddd #888; }
    a.stdbutton:hover, a.orderbutton:active {background-color: #888; color:#fff; cursor:pointer;}
    #definition_table {margin-left:210px; margin-bottom: 2em; background-color:#999; font-size:0.9em; }
    .formitem {background-color:#fff; border:1px solid #000; }
    .formitemerror {background-color:#ff7; border:1px solid #000; }
    span.formrequired {color:#f00; font-weight:bold; padding-right:2px; }
    div.formtitle {clear:both; float:left; text-align:right; font-weight:bold; width:200px; padding:5px 0px; font-size:0.8em; }
    div.forminfo {margin-left:210px; padding:5px 0px;}
    input.formsubmit {color:#000; cursor:pointer; font-size:1em; }
    div.error { margin:10px auto; border:2px solid #f00; background-color:#f88; padding:0.5em; width:300px; text-align:center; }
    #tableselection {color:#fff; font-weight:bold; float:right; }
    #flash{position:absolute; top:100px; left:50%; margin-left:-210px; width:400px;  border:2px solid;  border-color:#ccc #666 #555 #bbb; background-color:#000; color:#fff; padding:10px; text-align:center; z-index:10000; }
  </style>
  <script type="text/javascript" src="/lib/mvpeffects.js"></script>
  <script type="text/javascript">
        function tableSelect(table) {
            document.infotableform.submit();
        }

        function setAutoTextAreaHeight(id){
            var myelem = getId(id);
            if(myelem){
                if (myelem.scrollHeight > myelem.offsetHeight) myelem.style.height = myelem.scrollHeight + 50 + 'px';
            }
        }

        window.onload = function() {
            setTimeout("closeCenter('flash')",4000);
            setTimeout("setAutoTextAreaHeight('CTEXT')", 100);
        }
  </script>
  </head>
<body>
<form name="infotableform" method="post" action="table_php_info_create.php">
<div id="header">
<div id="tableselection">
Select Table: <?php echo $table_select; ?>
</div>
<h1>PHP Class Setup: <span><?php echo $DB_INFO['NAME']; ?></span></h1>
</div>
<div id="content">
<?php
if($TABLE) {

    $TableInfo = db_TableFieldInfo($TABLE);

    $UCtable = strtoupper($TABLE);
    $Ttable  = str_replace(' ','',NameToTitle($TABLE));

    $Index_Name = $TableInfo[0]['Field'];

    $OUTPUT = "<?php

// FILE: class.$Ttable.php

class $Ttable extends BaseClass
{
    public function  __construct()
    {
        parent::__construct();

        \$this->ClassInfo = array(
            'Created By'  => '$ADMIN_LOGIN_NAME',
            'Description' => 'Create and manage $TABLE',
            'Created'     => '". date('Y-m-d'). "',
            'Updated'     => '". date('Y-m-d'). "'
        );

        \$this->Table  = '$TABLE';

        \$this->Add_Submit_Name  = '{$UCtable}_SUBMIT_EDIT';
        \$this->Edit_Submit_Name = '{$UCtable}_SUBMIT_EDIT';

        \$this->Index_Name = '$Index_Name';

        \$this->Flash_Field = '$Index_Name';

        \$this->Default_Where = '';  // additional search conditions

        \$this->Default_Sort  = '$Index_Name';  // field for default table sort
";

    $_POST[$FormPrefix.'table_title'] = NameToTitle($TABLE);
    $_POST[$FormPrefix.'sort_variables'] = $Index_Name;
    $_POST[$FormPrefix.'flash_variable'] = $Index_Name;

    $FieldTitlesArray = db_TableFieldTitleNames($TABLE);
    $FieldTitles = '';
    foreach($FieldTitlesArray as $var=>$title) {
        $FieldTitles .= "            '$var' => '$title',\n";
    }

    $FieldTitles = substr($FieldTitles,0,-2);
    $OUTPUT .= '        $this->Field_Titles = array(' .  "
$FieldTitles
        );
";

    $TableFormDataAdd = '';
    $TableFormDataEdit = '';
    $Default_Fields = '';
    $no_default = array(
        $Index_Name,
        'updated',
        'created',
        'active'
    );

    $n = "\",\n";
    $spc  = '    ';
    $spc2 = $spc . $spc;
    $start = "$spc2$spc\"";

    foreach($TableInfo as $ROW) {
        $kind   = $ROW['Kind'];
        $size   = $ROW['Size'];
        $field  = $ROW['Field'];
        $extra  = $ROW['Extra'];
        $title  = NameToTitle($field);
        $default= $ROW['Default'];

        if (!in_array($field, $no_default)) {
            $Default_Fields .= "$field,";
        }

        if (($extra != 'auto_increment') and ($default != 'CURRENT_TIMESTAMP')
           and ($field != 'created') and ($field != 'updated')) {
            if ($kind=='text')  {
                $TableFormDataAdd  .= "{$start}textarea|$title|$field|N|80|10$n";

            } elseif ($kind=='date')  {
                $TableFormDataAdd  .= "{$start}dateYMD|$title|$field|Y-M-D|N|NOW|5$n";
            } elseif ($field=='country')  {
                $TableFormDataAdd  .= "{$start}country|$title|$field|N$n";
            } elseif ($field=='active')  {
                $TableFormDataEdit .= "{$start}checkbox|Active|active||1|0$n";
            } else {
                $colsize = ($size<60)? $size : 60;
                if ($field != 'active') $TableFormDataAdd .= "{$start}text|$title|$field|N|$colsize|$size$n";
                $TableFormDataEdit .= "{$start}text|$title|$field|N|$colsize|$size$n";
            }
        }
    }
    $TableFormDataAdd = substr($TableFormDataAdd,0,-1);

    $Default_Fields = substr($Default_Fields,0,-1);
    $OUTPUT .= "\n\n        \$this->Default_Fields = '$Default_Fields';";

    $OUTPUT .= "\n\n        \$this->Unique_Fields = '';";

    $OUTPUT .= "\n\n        \$this->Autocomplete_Fields ='';  // associative array: field => table|field|variable";

    $OUTPUT .= '

    } // -------------- END __construct --------------
';



$OUTPUT .=<<<LBLARRAYS


    public function SetFormArrays()
    {
        \$base_array = array(
$TableFormDataAdd
        );

        \$this->Form_Data_Array_Add = array_merge(
            array(
                "form|\$this->Action_Link|post|db_edit_form"
            ),
            \$base_array,
            array(
                "submit|Add Record|\$this->Add_Submit_Name",
                "endform"
            )
        );

        \$this->Form_Data_Array_Edit = array_merge(
            array(
                "form|\$this->Action_Link|post|db_edit_form"
            ),
            \$base_array,
            array(
                "checkbox|Active|active||1|0",
                "submit|Update Record|\$this->Edit_Submit_Name",
                "endform"
            )
        );
    }
LBLARRAYS;

$OUTPUT .= '

}  // -------------- END CLASS --------------';


//    $this->Table_Creation_Query = "";


    echo "<div class=\"forminfo\"><h2>Table: <span>$TABLE</span></h2></div>";
    echo db_output_table($TableInfo,'','id="definition_table"');


    echo '<textarea id="CTEXT" style="width:100%;" cols="80" rows="40">'.$OUTPUT.'</textarea>';

}
writedbquery();

?>
</div>
</form>

</body>
</html>