<?php
if (empty($DB_INFO)) {
	require $_SERVER['DOCUMENT_ROOT'] . '/office/config/db_info.php';
}

if (empty($DB_INFO)) {
    AddError("DB_INFO not Found");
    return;
}

$ADMIN_LOGIN_NAME = $ADMIN->AdminSession('AdminName');

SetPost('TABLE NEWENTRY');

$tables = $SQL->GetTables();
$table_select = '<select name="TABLE" onchange="tableSelect(this.value);">
<option value="">-- Select --</option>';
foreach ($tables as $table) {
    $select = ($TABLE==$table)? ' selected="selected"' : '';
        $table_select .= qqn("<option value=`$table`$select>$table</option>");
}
$table_select .= '</select>';

AddStyle('
    body {}
    #header {width : 100%; background-color:#888;}
    #header td { white-space : nowrap;}
    #result_content {background-color : #eee; margin-bottom: 10px;}
    #dialogcontainer {width : 100%; border:0px}
    #dialogcontainer td {padding : 0px;}

    h1{color:#fff; font-size:1.1em; padding:0px; margin:0px}
    h1 span, h2 span {color:#f00;}
    h2{font-size: 1.1em;}
    #definition_table {margin:0px 2em 2em 2em; background-color:#999; font-size:0.9em; }
    #definition_table td{padding:2px 10px; background-color: #fff;}
    #definition_table th{padding:2px 10px; background-color:#888; color:#fff; font-weight:bold;}
    #definition_table tr.odd td{background-color:#eef;}
    #tableselection {color:#fff; font-weight:bold; text-align:right;}
    #dummywidth {width : 600px; height : 1px;}
    #CTEXT {margin-top : 10px;}
    
');

Addscript("
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
        setAutoTextAreaHeight('CTEXT');
        mainOnload();
    }
");

echo <<<LBL1
<form name="infotableform" method="post" action="$ADMIN->Admin_File_Query">
<table id="header" border="0" cellspacing="0" cellpadding="0">
<tbody>
<tr>
    <td><h1>PHP Class Setup: <span>&ldquo;{$DB_INFO['NAME']}&rdquo; Database</span></h1></td>
    <td id="tableselection">
        Select Table: $table_select
    </td>
</tr>

LBL1;

if($TABLE) {

    $TableInfo = $SQL->TableFieldInfo($TABLE);

    $UCtable = strtoupper($TABLE);
    $Ttable  = str_replace(' ','',NameToTitle($TABLE));

    $Index_Name = $TableInfo[0]['Field'];

    $OUTPUT = "&lt;?php

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

        \$this->Add_Submit_Name  = '{$UCtable}_SUBMIT_ADD';
        \$this->Edit_Submit_Name = '{$UCtable}_SUBMIT_EDIT';

        \$this->Index_Name = '$Index_Name';

        \$this->Flash_Field = '$Index_Name';

        \$this->Default_Where = '';  // additional search conditions

        \$this->Default_Sort  = '$Index_Name';  // field for default table sort

";

    $FieldTitlesArray = $SQL->TableFieldTitleNames($TABLE);
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

    $n = "',\n";
    $spc  = '    ';
    $spc2 = $spc . $spc;
    $start = "$spc2$spc'";

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
                $TableFormDataAdd  .= "{$start}textarea|$title|$field|N|60|4$n";

            } elseif (($size == 1) and ($kind == 'tinyint') and ($field != 'active')) {
                $TableFormDataAdd  .= "{$start}checkbox|$title|$field||1|0$n";

            } elseif ($kind =='enum')  {
                $value_list = str_replace("','", '|', $size);
                $value_list = str_replace("'", '', $value_list);

                if ((strtolower($value_list) == 'no|yes') or (strtolower($value_list) == 'yes|no')) {
                    $TableFormDataAdd  .= "{$start}radioh|$title|$field|N||$value_list$n";
                } else {
                    $TableFormDataAdd  .= "{$start}select|$title|$field|N||$value_list$n";
                }

            } elseif ($kind=='set')  {
                $value_list = str_replace("','", '|', $size);
                $value_list = str_replace("'", '', $value_list);
                $TableFormDataAdd  .= "{$start}checkboxlistset|$title|$field|N||$value_list$n";

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

    //$OUTPUT .= "\n\n        \$this->Autocomplete_Fields ='';  // associative array: field => table|field|variable";

    $OUTPUT .= '

    } // -------------- END __construct --------------
';



$OUTPUT .=<<<LBLARRAYS


    public function SetFormArrays()
    {
        \$base_array = array(
            "form|\$this->Action_Link|post|db_edit_form",
$TableFormDataAdd
        );

        if (\$this->Action == 'ADD') {
            \$base_array[] = "submit|Add Record|\$this->Add_Submit_Name";
            \$base_array[] = 'endform';
            \$this->Form_Data_Array_Add = \$base_array;
        } else {
            \$base_array[] = 'checkbox|Active|active||1|0';
            \$base_array[] = "submit|Update Record|\$this->Edit_Submit_Name";
            \$base_array[] = 'endform';
            \$this->Form_Data_Array_Edit = \$base_array;
        }
    }

LBLARRAYS;

$OUTPUT .= '

}  // -------------- END CLASS --------------';

    echo '
<tr>
    <td colspan="2" id="result_content">';
    echo "<h2>Table: <span>$TABLE</span></h2>";
    echo $SQL->OutputTable($TableInfo, '', 'id="definition_table"');
    echo '
</td>
</tr>
<tr>
    <td colspan="2">';
    echo '<textarea id="CTEXT" style="width:100%;" cols="80" rows="40">'.$OUTPUT.'</textarea>';
    //echo '</td>';

} else {
    //echo '</td>';
}
echo '
</td>
</tr>
</tbody>
</table>
</form>';
