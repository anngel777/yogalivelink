<?php
class Website_PageContents extends BaseClass
{
    public $Show_Query                  = false;    // TRUE = output the database queries ocurring on this page
    public $Show_Array                  = false;    // TRUE = show array of info pulled from database call
    public $Show_Identifier             = false;    // TRUE = show the swap identifier with the text
    public $Show_Identifier_Only        = false;    // TRUE = show ONLY the swap identifier and no content
    
    public $Page_Preview_Location       = "/office/website_page_preview";
    public $Page_Link                   = '';
    public $Page_Name                   = '';
    public $Default_Page_Content_Swap   = 'PAGE_CONTENT';
    
    
    public function  __construct()
    {
        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => '',
            'Updates'     => '',
            'Version'     => '1.0',
            'Description' => 'Manage page contents in back-office and pull page content for display on website',
        );
        
        global $PAGE;
        //echo ArrayToStr($PAGE);
        $this->Page_Link = $PAGE['pagelink'];
        
        
        $this->Add_Submit_Name      = 'DISCOUNTS_SUBMIT_ADD';
        $this->Edit_Submit_Name     = 'DISCOUNTS_SUBMIT_EDIT';
        $this->Table                = 'website_page_contents';
        $this->Flash_Field          = 'Website Page Contents';
        $this->Index_Name           = 'website_page_contents_id';
        
        $this->Field_Titles = array(
            'website_page_contents_id'  => 'Id',
            'item_number'               => 'Item Number',
            'identifier'                => 'Identifier',
            'description'               => 'Description',
            'page_name'                 => 'Page Name',
            'page_contents'             => 'Contents',
            'comments'                  => 'Comments',
            'display'                   => 'Show On Websit',
            'active'                    => 'Active'
        );

        $this->Default_Fields       = 'identifier,description,page_name,page_contents,comments,display';
        $this->Default_Values       = array ('display'=>1, 'sort_order'=>0);
        $this->Unique_Fields        = '';      
        $this->Autocomplete_Fields  = array();
        $this->Join_Array           = array();
        
    } // ---------- end construct -----------

    public function SetFormArrays() // overrides parent
    {
        global $FormPrefix;
        
        $this->AddScript();
        
        $style_fieldset     = "style='color:#990000; font-size:14px; font-weight:bold;'";
        $div_width          = '500px';
        
        $base_array = array(
            "code|<div style='width:$div_width;'>",
                "info||Do NOT include @@ with identifier.",
                'text|Identifier|identifier|N|60|100',
                'text|Page Name|page_name|N|60|100',
                'html|Page Contents|page_contents|N|60|4',
                "button|Preview|previewPopUp('{$FormPrefix}page_contents');",
                
                
                'checkbox|Display on Website|display||1|0',
                "info||Order of appearance on website. <br />0 = no order and appears at start of ordered list. <br />99 = no order and appears at end of ordered list.",
                "text|Order|sort_order|N|3|2",
                
                "fieldset|Content|options_fieldset|$style_fieldset",
                    'checkbox|Bypass Text Processing|bypass_text_processing||1|0',
                    'text|Description|description|N|60|100',
                    'text|Item Number|item_number|N|3|3',
                    'text|Languages Id|languages_id|N|11|11',
                    'textarea|Comments|comments|N|60|4',
                "endfieldset",
            "code|</div>",
        );

        $this->Form_Data_Array_Edit = $this->Form_Data_Array_Add;

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
                "submit|Update Record|$this->Edit_Submit_Name",
                "endform"
            )
        );


    }
    
    
    public function PostProcessFormValues($FormArray)
    {
        #echo 'page_contents ---> ' . $FormArray['page_contents'];
        
        #echo ArrayToStr($FormArray);
        #exit();
        
        return $FormArray;
    }
    
    
    public function GetContentFromIdentifier($IDENTIFIER='')
    {
        # FUNCTION :: Get page contents from the database when being called from another class - searches in identifier
        
        
        // GET PAGE CONTENTS
        // ======================================================
        $record = $this->SQL->GetRecord(array(
            'table' => $this->Table,
            'keys'  => '*',
            'where' => "`active`=1 AND `identifier`='{$IDENTIFIER}'",
        ));
        if ($this->Show_Query) echo '<br />' . $this->SQL->Db_Last_Query;
        
        return $record['page_contents'];
    }
    
    public function GetContents()
    {
        # FUNCTION :: Get page contents from the database and add swaps to the page
        
        
        // GET PAGE CONTENTS
        // ======================================================
        $records = $this->SQL->GetArrayAll(array(
            'table' => $this->Table,
            'keys'  => '*',
            'where' => "`display`=1 AND `active`=1 AND `page_name`='{$this->Page_Name}'",
            'order' => '`sort_order` ASC',
        ));
        if ($this->Show_Query) echo '<br />' . $this->SQL->Db_Last_Query;
    
        $this->ProcessContents($records);
    }
    
    public function ProcessContents($records)
    {
        $temp_swap_array = array();
        if ($records) {
            
            // STORE ALL CONTENT IN AN ARRAY
            // ======================================================
            foreach ($records AS $record) {
            
                $identifier     = ($record['identifier']) ? $record['identifier'] : $this->Default_Page_Content_Swap;
                
                $content        = $record['page_contents'];
                
                if (!$record['bypass_text_processing']) {
                    $content        = str_replace(array("\r\n", "\n", "\r"), '<br />', $content);	// Change new lines to line breaks
                }
                
                if (isset($temp_swap_array[$identifier])) {
                    $content = $temp_swap_array[$identifier] . "<br /><br />" . $content;
                    $temp_swap_array[$identifier] = $content;
                } else {
                    $temp_swap_array[$identifier] = $content;
                }
            
            }
            
            
            // CREATE THE SWAPS
            // ======================================================
            foreach ($temp_swap_array AS $identifier => $content) {
                // PUT IDENTIFIERS BACK IN
                if ($this->Show_Identifier) { $content = "<span class='idetifier'>@@{$identifier}@@ ==> </span>{$content}"; }
                if ($this->Show_Identifier_Only) { $content = "<span class='idetifier'>@@{$identifier}@@</span>"; }
                
                AddSwap("@@{$identifier}@@", $content);
            }
            
        }
        
        // CLEAR OUT THE DEFAULT PAGE SWAP
        // ======================================================
        if (!isset($temp_swap_array[$this->Default_Page_Content_Swap])) { $temp_swap_array[$this->Default_Page_Content_Swap] = ''; };
        AddSwap("@@{$this->Default_Page_Content_Swap}@@", '');
        
        // OUTPUT THE ARRAY - if requested
        // ======================================================
        if ($this->Show_Array) echo '<br />' . ArrayToStr($temp_swap_array);
    }

    
    
    public function AddScript()
    {
        $script = <<<SCRIPT
            function previewPopUp(content)
            {
                var location = '{$this->Page_Preview_Location};source_dialog_id=' + dialogNumber + ';content=' + content;
                top.parent.appformCreate('Content Preview', location, 'apps');
            }
SCRIPT;
        AddScript($script);
    }
    
    
    
    
    public function ProcessTableCell($field, &$value, &$td_options, $id='')
    {
        parent::ProcessTableCell($field, $value, $td_options, $id);

        switch ($field) {
            case 'description':
            case 'page_contents':
                $value = TruncStr(strip_tags($value), 200);
                break;
            case 'display':
                $value = ($value == 1) ? 'Yes' : 'No';
                break;
        }
    }
    
    public function ProcessRecordCell($field, &$value, &$td_options)
    {
        switch ($field) {
            case 'description':
            case 'notes':
                $value = nl2br($value);
                break;
        }
    }

} // END CLASS