<?php
class General_TestingInstructions extends BaseClass
{
    public function  __construct()
    {
        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Description' => 'Create and manage module_testing_instructions - testing procedures put onto page during development',
        );
        
        $this->Table                = 'module_testing_instructions';
        $this->Add_Submit_Name      = 'module_testing_instructions_SUBMIT_ADD';
        $this->Edit_Submit_Name     = 'module_testing_instructions_SUBMIT_EDIT';
        $this->Index_Name           = 'module_testing_instructions_id';
        $this->Flash_Field          = 'module_testing_instructions_id';
        $this->Default_Where        = '';  // additional search conditions
        $this->Default_Sort         = 'module_testing_instructions_id';  // field for default table sort
        $this->Default_Fields       = 'title';
        $this->Unique_Fields        = '';
        $this->Autocomplete_Fields  ='';  // associative array: field => table|field|variable
        
        $this->Field_Titles = array(
            'module_testing_instructions_id'        => 'Ahu Table Instructions Id',
            'admin_modules_id'              => 'Module ID',
            'module_name'                   => 'Module Name',
            'module_subpage_identifier'     => 'Module Subpage Identifier',
            'title'                         => 'Title',
            'test_explanation'                   => 'Explanation',
            'testing_procedure'                        => 'Procedure',
            'expected_outcome'                       => 'Outcome',
            'created'                       => 'Created',
            'active'                       => 'Active',
        );
        
    } // -------------- END __construct --------------


    public function SetFormArrays()
    {
        $modules        = $this->SQL->GetFieldValues('admin_modules', 'filename', "`active`=1 AND `is_folder`=0");
        $module_list    = Form_ArrayToList($modules);
    
        $base_array = array(
            
            "selecttext|Module Name|module_name|N|40|80||$module_list",
            "text|Subpage Identifier|module_subpage_identifier|N|60|255",
            "text|Title|title|N|60|255",
            
            "textarea|Explanation|test_explanation|N|60|8",
            "textarea|Testing Procedure|testing_procedure|N|60|8",
            "textarea|Expected Outcome|expected_outcome|N|60|8",
        );

        $this->Form_Data_Array_Add = array_merge(
            array(
                "form|$this->Action_Link|post|db_edit_form"
            ),
            $base_array,
            array(
                "submit|Add Record|$this->Add_Submit_Name",
                "endform"
            )
        );

        $this->Form_Data_Array_Edit = array_merge(
            array(
                "form|$this->Action_Link|post|db_edit_form"
            ),
            $base_array,
            array(
                "checkbox|Active|active||1|0",
                "submit|Update Record|$this->Edit_Submit_Name",
                "endform"
            )
        );
    }
    
    public function ShowInstructions($module_name='', $module_subpage_identifier='') 
    {
        $where = ($module_subpage_identifier) ? " AND `module_subpage_identifier`='$module_subpage_identifier'" : " AND `module_subpage_identifier`=''";
        $result = $this->SQL->GetRecord(array(
            'table'     => $this->Table,
            'keys'      => '*',
            'where'     => "module_name='{$module_name}' AND active=1 $where",
        ));
        //echo $this->SQL->Db_Last_Query;
        
        $title          = strtoupper($result['title']);
        
        $search         = array("\r\n", "\n", "\r");
        $description    = str_replace($search, '<br />', $result['description']);
        $description    = html_entity_decode($description);
        
        if ($result) {
            $DIALOGID = Get('DIALOGID');
            $OUTPUT = "
                <div class='instructions_outter'>
                    <div class='instructions_inner'>
                        <div class='instructions_header'>
                            {$title}
                        </div>
                        <div class='instructions_text'>
                            {$description}
                        </div>
                    </div>
                </div>
                <script language='text/javascript'>
                    
                    //appformActivate(this.id);
                    
                    //$('#{$DIALOGID}').css('z-index', 100);
                    
                </script>
                ";
            
            echo $OUTPUT;
            //$script = "this.window.focus();";
            $script = "appformActivate(this.id)";
            AddScriptOnReady($script);
            $this->AddStyle();
        }
    }
    
    public function AddInstructions($module_name='', $module_subpage_identifier='') 
    {
        # 1. CREATE A HELP BAR
        # 2. MAKE IT LINK TO HELP FILE
        $where = ($module_subpage_identifier) ? " AND `module_subpage_identifier`='$module_subpage_identifier'" : " AND `module_subpage_identifier`=''";
        
        $result = $this->SQL->GetRecord(array(
            'table'     => $this->Table,
            'keys'      => 'module_instructions_id ',
            'where'     => "module_name='{$module_name}' AND active=1 $where",
        ));
        
        if ($result) {
            $link = "/office/administration/module_instructions;module={$module_name};subpage={$module_subpage_identifier}";
            $script = "top.parent.appformCreate('Application Instructions', '{$link}', 'apps'); return false;";
            
            $output = "
                <div style='border-bottom:1px solid blue; background-color:#eee;'>
                    <div style='float:right;' onclick=\"{$script}\"><img src='/wo/images/menu_icons/help.png' width='32' height='32' border='0' /></div>
                    <div style='clear:both;'></div>
                </div><br />
                ";
            echo $output;
        }
    }
    
    private function AddStyle() 
    {
        $STYLE = "
        .instructions_outter {
            padding:5px;
            border:1px solid #ddd;
            width:400px;
        }
        .instructions_inner {
            padding:5px;
            background-color:#eee;
        }
        .instructions_header {
            font-weight:bold;
            font-size:16px;
            border-bottom:1px solid #bbb;
        }
        .instructions_text {
            font-size:12px;
            padding-left:10px;
            padding-top:3px;
        }
        ";
        AddStyle($STYLE);
    }

}  // -------------- END CLASS --------------