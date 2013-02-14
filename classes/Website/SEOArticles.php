<?php
class Website_SEOArticles extends Website_Articles
{
    public $Show_Query          = false;    // TRUE = output the database queries ocurring on this page
    
    public function  __construct()
    {
        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Description' => 'Manage article SEO settings in back-office',
        );
        
        global $PAGE;
        //echo ArrayToStr($PAGE);
        $this->Page_Link = $PAGE['pagelink'];
        
        $this->Add_Link             = '';
        $this->Add_Submit_Name      = 'DISCOUNTS_SUBMIT_ADD';
        $this->Edit_Submit_Name     = 'DISCOUNTS_SUBMIT_EDIT';
        $this->Table                = 'website_articles';
        $this->Flash_Field          = 'Website Articles';
        $this->Index_Name           = 'website_articles_id';
        $this->Default_Fields       = 'title,author,source,content,footer,display,category,keywords';
        $this->Default_Values       = array ('display'=>1, 'sort_order'=>0);
        $this->Unique_Fields        = '';      
        $this->Autocomplete_Fields  = array();
        $this->Join_Array           = array();
        
        $this->Field_Titles = array(
            'website_blog_id'       => 'Id',
            'title'                 => 'Title',
            'author'                => 'Author',
            'source'                => 'Source',
            'content'               => 'Content',
            'footer'                => 'Footer',
            'category'              => 'Category',
            'keywords'              => 'Keywords',
            'display'               => 'Display on Website',
            'active'                => 'Active'
        );

    } // ---------- end construct -----------
    
    public function Execute()
    {
        $this->ListTable();
    }
    
    public function SetFormArrays() // overrides parent
    {
        $style_fieldset = "style='color:#990000; font-size:14px; font-weight:bold;'";
        $div_width      = '500px';
        $categories     = $this->SQL->GetFieldValues($this->Table, 'category', "category != ''");
        $category_list  = Form_ArrayToList($categories);
        
        $style_scroll = " width:450px; overflow:auto; font-size:12px; border:1px solid #000; padding:5px;";
        
        global $FormPrefix;
        
        //echo ArrayToStr($_POST);
        
        $base_array = array(
            "code|<div style='width:$div_width;'>",
            'textarea|Keywords|keywords|N|5|5',
            "code|<br />",
            
            "fieldset|Article|options_fieldset|$style_fieldset",
                "code|<b>Content</b><br /><div style='height:100px; $style_scroll'>{$_POST[$FormPrefix.'content']}</div>",
                "code|<br />",
                "code|<b>Title</b><br /><div style='height:20px; $style_scroll'>{$_POST[$FormPrefix.'title']}</div>",
                "code|<b>Category</b><br /><div style='height:20px; $style_scroll'>{$_POST[$FormPrefix.'category']}</div>",
                "code|<b>Author</b><br /><div style='height:20px; $style_scroll'>{$_POST[$FormPrefix.'author']}</div>",
                "code|<b>Source</b><br /><div style='height:20px; $style_scroll'>{$_POST[$FormPrefix.'source']}</div>",
            "endfieldset",
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
}
