<?php
class DatabaseManagement_TableEdit extends BaseClass
{
    private $temp_base_array = array();
    
    public function  __construct($TABLE)
    {        
        parent::__construct();
        
        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Description' => 'Backoffice Database Management - Edit any database table',
        );
        
        # GET TABLE INFORMATION
        # =======================================================================
        $this->SetParameters(Func_Get_Args());
        $TABLE = $this->GetParameter(0);
        $table_info = $this->SQL->TableFieldInfo($TABLE);
        
        
        # PROCESS TABLE INFORMATION
        # =======================================================================
        $Field_Titles   = array();
        $Default_Fields = '';
        foreach ($table_info AS $field_array) {
            $name       = $field_array['Field'];
            $display    = str_replace('_', ' ', $field_array['Field']);
            
            $Field_Titles[$name] = $display;
            $Default_Fields .= "$name,";
        }
        $Default_Fields = substr($Default_Fields, 0, -1); //strip off ending comma
        $remove_fields  = array('active', 'updated', 'created'); //remove fields from display
        $Default_Fields = str_replace($remove_fields, ' ', $Default_Fields);
        
        
        # PUT INTO SPECIFIC VARIABLES
        # =======================================================================
        $this->Table                = $TABLE;
        $this->Index_Name           = "{$TABLE}_id";
        $this->Default_Sort         = "{$TABLE}_id";
        $this->Flash_Field          = "{$TABLE}_id";
        $this->Add_Submit_Name      = "{$TABLE}_SUBMIT_ADD";
        $this->Edit_Submit_Name     = "{$TABLE}_SUBMIT_EDIT";
        $this->Field_Titles         = $Field_Titles;
        $this->Default_Fields       = $Default_Fields;
        $this->Default_Where        = '';
        
        
        
        
        
        # CREATE THE FORM ARRAY - taken from MVP admin+ class (lib/site_admin/content/class_creation.php)
        # =====================================================================================================
        $TableFormDataAdd = '';
        $TableFormDataEdit = '';
        $Default_Fields = '';
        $no_default = array(
            $this->Index_Name,
            'updated',
            'created',
            'active'
        );

        $n = "',\n";
        $spc  = '    ';
        $spc2 = $spc . $spc;
        $start = "$spc2$spc'";

        foreach($table_info as $ROW) {
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
        $this->temp_base_array = $TableFormDataAdd;

        
        
    } // -------------- END __construct --------------


    public function SetFormArrays()
    {
        $base_array = array(
            "form|$this->Action_Link|post|db_edit_form",
        );
        
        $t_base = explode(',', $this->temp_base_array);
        foreach ($t_base as $row)
        {
            if ($row) {
                $row = trim($row);
                $row = substr($row, 0, -1);
                $row = substr($row, 1);                
                $base_array[] = $row;
            }
        }

        if ($this->Action == 'ADD') {
            $base_array[] = "submit|Add Record|$this->Add_Submit_Name";
            $base_array[] = 'endform';
            $this->Form_Data_Array_Add = $base_array;
        } else {
            $base_array[] = 'checkbox|Active|active||1|0';
            $base_array[] = "submit|Update Record|$this->Edit_Submit_Name";
            $base_array[] = 'endform';
            $this->Form_Data_Array_Edit = $base_array;
        }
    }
    
}  // -------------- END CLASS --------------