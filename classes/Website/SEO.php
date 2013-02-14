<?php
class Website_SEO extends BaseClass
{
    public $Show_Query          = false;
    public $Page_Name           = '';
    public $Title_End           = ', YogaLiveLink.com';
    public $Author              = 'YogaLiveLink.com';
    
    public $META_TITLE          = '';
    public $META_DESCRIPTION    = '';
    public $META_KEYWORDS       = '';
    public $META_HIDDEN         = '';
    public $META_AUTHOR         = '';
    
    // ---------- files allowed to have SEO done on them ----------
    public $File_List_Allowed = array(
        'index',
        'how_yll_works',
        'signup',
        'signup_instructor',
        'about_us',
        'contact_us',
        'terms_and_conditions',
        'liability_waiver',
        'privacy_policy',
        'links',
        'store',
        'help',
        
        'office/website/sessions_schedule',
    );
    
    // ---------- directories allowed to have SEO done on them ----------
    public $File_Directories = array(
        '/content/',
        '/office/content/website/',
    );
    
    
    
    public function  __construct()
    {

        parent::__construct();
        
        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Description' => 'Manage website SEO settings for website pages in back-office and do SEO swaps on website',
        );
        
        $this->Table                    = 'website_seo';
        $this->Add_Submit_Name          = 'ADMIN_MODULES_SUBMIT_EDIT';
        $this->Edit_Submit_Name         = 'ADMIN_MODULES_SUBMIT_EDIT';
        
        $this->Field_Titles = array(
            'website_seo_id'        => 'Id',
            'filename'              => 'Filename',
            'title'                 => 'Title',
            'description'           => 'Description',
            'keywords'              => 'Keywords',
            'hidden'                => 'Hidden',
            'active'                => 'Active',
            'updated'               => 'Updated',
            'created'               => 'Created'
        );

        $this->Index_Name               = 'website_seo_id';
        $this->Default_Fields           = 'filename, title, description, keywords, hidden';
        $this->Flash_Field              = '';
        $this->Unique_Fields            = '';
        $this->Default_Sort             = 'filename';
        $this->Table_Creation_Query     = '';
        
        $this->Default_List_Size        = 500;

    } // ================== END CONSTRUCT =================


    public function ProcessRecordCell($field, &$value, &$td_options)  // extended from parent
    {
        if ($field == 'image') {
            $value = $value . '<br /><br /><img src="' . $this->Image_Dir . '/'. $value .'" alt="' . $value . '" height="60" border="0" />';
        }

        return;
    }

    public function ProcessTableCell($field, &$value, &$td_options, $id='')
    {
        parent::ProcessTableCell($field, $value, $td_options, $id);
        if ($field == 'image') {
            $value = $value . '<br /><img src="' . $this->Image_Dir . '/'. $value .'" alt="' . $value . '" height="60" border="0" />';
        }

        return;
    }

    public function Execute()
    {
        //$this->AddRecord();
        $this->ListTable();
    }
    
    public function GetMetaAndSwapContents()
    {
        // GET CONTENTS
        // ======================================================
        $record = $this->SQL->GetRecord(array(
            'table' => $this->Table,
            'keys'  => '*',
            'where' => "`active`=1 AND `filename`='{$this->Page_Name}'",
        ));
        if ($this->Show_Query) echo '<br />' . $this->SQL->Db_Last_Query;
        
        if ($record) {
            $this->META_TITLE           = $record['title'];
            $this->META_DESCRIPTION     = $record['description'];
            $this->META_KEYWORDS        = $record['keywords'];
            $this->META_HIDDEN          = $record['hidden'];
            $this->META_AUTHOR          = '';
            
            $this->AddSwaps();
        }
    }
    
    public function AddSwaps()
    {
        $this->META_TITLE .= $this->Title_End;
        $this->META_AUTHOR = $this->Author;
        
        addSwapCustom('@@TITLE@@',              $this->META_TITLE);
        addSwapCustom('@@META_DESCRIPTION@@',   $this->META_DESCRIPTION);
        addSwapCustom('@@META_KEYWORDS@@',      $this->META_KEYWORDS);
        addSwapCustom('@@META_HIDDEN@@',        $this->META_HIDDEN);
        addSwapCustom('@@META_AUTHOR@@',        $this->META_AUTHOR);
    }
    
    
    public function SetFormArrays()
    {

        global $ROOT, $FormPrefix;

        $new_pages = $this->FindNewPages();
        
        $module_list = '';
        $module_titles = '';
        foreach ($new_pages as $name=>$title) {
            $valid_name = str_replace('/', '__', $name);
            $valid_name = str_replace('-', '_', $valid_name);
            $module_list   .= "|$name";
            $module_titles .= "$valid_name : '$title',";
        }
        $module_titles = substr($module_titles, 0, -1);

        if ($new_pages) {
            $this->Form_Data_Array_Add = array(
                "form|$this->Action_Link|post|db_edit_form",
                
                "select|Filename|filename|Y|
                   onchange=\"var myvalue = this.value.replace('/', '__');
                   var myvalue = myvalue.replace('-', '_');
                   getId('{$FormPrefix}title').value = module_titles[myvalue];
                \"$module_list",
                'js|var module_titles = {' . $module_titles . '}',
                
                'code|<br />',
                'textarea|Title|title|N|5|5',
                'textarea|Description|description|N|5|5',
                'textarea|Keywords|keywords|N|5|5',
                'textarea|Hidden|hidden|N|5|5',
                
                "submit|Add Record|$this->Add_Submit_Name",
                "endform"
            );

        } else {
            $this->Form_Data_Array_Add = array(
                'code|<h2>No New Pages Found!</h2>'
            );
        }

        $this->Form_Data_Array_Edit = array(
            "form|$this->Action_Link|post|db_edit_form",
            
            'code|<br />',
            'text|Filename|filename|Y|60|80',
            
            'code|<br />',
            'textarea|Title|title|N|5|5',
            'textarea|Description|description|N|5|5',
            'textarea|Keywords|keywords|N|5|5',
            'textarea|Hidden|hidden|N|5|5',

            "checkbox|Active|active||1|0",
            "submit|Update Record|$this->Edit_Submit_Name",
            "endform"
        );


    }

    public function FindNewPages()
    {
        global $SITE_ROOT;
        
        $RESULT             = array();
        $current_pages      = $this->SQL->FieldArray($this->Table, 'filename');
        
        /*
        $master_arr = array();
        foreach ($this->File_Directories as $directory) {
            $dir        = "{$SITE_ROOT}{$directory}";
            
            echo "<br /><br />SITE_ROOT ===> $SITE_ROOT";
            echo "<br /><br />dir ===> $dir";
            
            $files      = GetDirectory($dir, '.php');
            $files      = SubTextBetweenArray('', '.php', $files);
            
            echo ArrayToStr($files);
            
            $new_arr    = array_merge($master_arr, $files);
            $master_arr = $new_arr;
        }
        
        $files = $master_arr;
        */
        
        $files = $this->File_List_Allowed;
        foreach ($files as $file) {
            // check if already module
            if (!in_array($file, $current_pages)) {
                
                $RESULT[$file] = $file;
                
                /*
                // check if has a def file
                $def_file = "$SITE_ROOT/content/$file.def";
                if (file_exists($def_file)) {
                    // check if has a name
                    $def_title = TextBetween('<name>', '</name>', file_get_contents($def_file));
                    if ($def_title) {
                        // add to result
                        $RESULT[$file] = $def_title;
                    }
                }
                */
                
            }
        }
        return $RESULT;
    }
}
