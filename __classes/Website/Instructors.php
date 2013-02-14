<?php
class Website_Instructors extends BaseClass
{
    public $Page_Link               = '';
    public $Articles_Records        = null;
    public $Days_Most_Recent        = 5;        // if article within this many days - show it as most recent
    public $Instructor_Logo         = "/images/template/instructor_logo.jpg";
    public $Instructor_Logo_Legend  = "/images/template/instructor_logo_legend.jpg";
    public $Instructor_Logo_Gap     = "/images/spacer.gif";

    public $Use_Seo_Urls            = true;
    public $Use_Encrypted_Query     = true;
    
    public $Show_Query              = false;
    
    public function  __construct()
    {
        parent::__construct();      
        
        global $PAGE;
        $this->Page_Link = $PAGE['pagelink'];
        
        
        $this->Add_Submit_Name      = 'DISCOUNTS_SUBMIT_ADD';
        $this->Edit_Submit_Name     = 'DISCOUNTS_SUBMIT_EDIT';
        $this->Table                = 'instructor_profile';
        $this->Flash_Field          = 'instructor_profile';
        $this->Index_Name           = 'instructor_profile_id';
        
        $this->Field_Titles = array(
            'website_blog_id'       => 'Id',
            'title'                 => 'Title',
            'author'                => 'Author',
            'source'                => 'Source',
            'content'               => 'Content',
            'footer'                => 'Footer',
            'category'              => 'Category',
            'display'               => 'Display on Website',
            'active'                => 'Active'
        );

        $this->Default_Fields       = 'title,author,source,content,footer,display,category';
        $this->Default_Values       = array ('display'=>1, 'sort_order'=>0);
        $this->Unique_Fields        = '';      
        $this->Autocomplete_Fields  = array();
        $this->Join_Array           = array();
        
    } // ---------- end construct -----------

    public function SetFormArrays() // overrides parent
    {
        $style_fieldset = "style='color:#990000; font-size:14px; font-weight:bold;'";
        $div_width      = '500px';
        $categories     = $this->SQL->GetFieldValues($this->Table, 'category', "category != ''");
        $category_list  = Form_ArrayToList($categories);
        
        $base_array = array(
            "code|<div style='width:$div_width;'>",
            "fieldset|Content|options_fieldset|$style_fieldset",
                "selecttext|Category|category|Y|40|80||$category_list",
                "checkbox|Author is Instructor|instructor||1|0",
                
                "html|Title|title|Y|20|1",
                "html|Content|content|Y|20|6",
                "html|Footer|footer|N|20|3",
            "endfieldset",
            "fieldset|General Info|options_fieldset|$style_fieldset",
                
                
                
                
                "html|Author|author|N|20|1",
                "html|Source|source|N|20|1",
            "endfieldset",
            "fieldset|Display Settings|options_fieldset|$style_fieldset",
                "checkbox|Display on Website|display||1|0",
                #"datetime|Show Date|show_date|N|2010||",
                #"datetime|Hide Date|hide_date|N|2010||",
                "info||Order of appearance on website. <br />0 = no order and appears at start of ordered list. <br />99 = no order and appears at end of ordered list.",
                "text|Order|sort_order|N|3|2",
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
    
    
    
    public function HandleInstructor($ID='', $EQ='')
    {
        
        if ($this->Use_Encrypted_Query) {
            if ($EQ) {
                $eq = GetEncryptQuery($EQ, false);
                $ID = (isset($eq['instructor_id'])) ? IntOnly($eq['instructor_id']) : 0;
            } else {
                $ID = 0;
            }
        } else {
            $ID = IntOnly($ID);
        }
        
        
        if (!$ID) {
            
            if ($EQ) {
                $eq     = GetEncryptQuery($EQ, false);
                $Query  = (isset($eq['QUERY'])) ? str_replace('::', '=', $eq['QUERY']) : '';
            } else {
                $Query  = '';
            }
        
            $output = $this->GetAllInstructors($Query);
            
        } else {
            $output = $this->GetSingleInstructor($ID);
        }
        
        return $output;
    }
    
    
    public function GetSingleInstructor($ID='')
    {
        $output = '';
        
        if ($ID) {
            $record = $this->SQL->GetRecord(array(
                'table' => $this->Table,
                'keys'  => '*',
                'where' => "`instructor_profile_id`=$ID AND `active`=1 AND `display`=1",
            ));
            if ($this->Show_Query) $output .= '<br />' . $this->SQL->Db_Last_Query;
        } else {
            $record = null;
        }
        
        if ($record) {
            
            foreach ($record as $field => $value) {
                $record[$field] = str_replace("\n", "<br />", $value);
            }
            
            $name                   = "{$record['first_name']} {$record['last_name']}";
            $Instructor_Logo_Gap    = "<div style='width:100px; height:150px; border:1px solid red;'>&nbsp;</div>";
            $Instructor_Picture     = ($record['primary_pictures_id'] != '') ? "<img src='/office/{$record['primary_pictures_id']}' alt='' border='0' width='{$GLOBALS['INSTRUCTOR_PICTURE_WIDTH_LARGER']}' height='{$GLOBALS['INSTRUCTOR_PICTURE_HEIGHT_LARGER']}' />" : $Instructor_Logo_Gap;
            $back_link              = "<h3><a href='{$this->Page_Link}' class='link_arrow'>Back To Yoga Instructors</a></h3>";
            $link_schedule          = "{$GLOBALS['LINK_SESSION_SIGNUP_INSTRUCTOR']};instructor_whid={$record['wh_id']};retpage=instructors";
            
            $output .= "
                <div style='border-bottom:1px solid #F2935B;'>{$back_link}<br /></div>
                <br /><br />
                <div class='article_all_picture_col' style='width:{$GLOBALS['INSTRUCTOR_PICTURE_WIDTH_LARGER']}px;'>{$Instructor_Picture}</div>
                <div class='article_all_content_col' style='width:72%; border:0px solid #000;'>
                    <div class=\"article_title\">{$name}</div>
                    <div class=\"article_p\">{$record['profile']}</div>
                    <br />
                    <div class=\"article_all_link\"><a href='{$link_schedule}' class='link_arrow'>View Schedule...</a></div>
                </div>
                <div class='clear'></div>
                <br /><br />
                <div style='border-top:1px solid #F2935B;'>{$back_link}</div>
                ";
            
            # CREATE THE SEO FIELDS
            # =======================================================
            $types = $record['yoga_types'];
            $types = str_replace (',', ', ', $types);
            
            $keywords = $types;
            $hidden = $types;
            
            # title
            $title = "$name, Instructor of $types";
            
            # description
            $description = $name . ' ' . $record['profile'];
            $description = substr($description, 0, 120);
            
            $OBJ_SEO                        = new Website_SEO();
            $OBJ_SEO->META_TITLE            = $title;
            $OBJ_SEO->META_DESCRIPTION      = $description;
            $OBJ_SEO->META_KEYWORDS         = $keywords;
            $OBJ_SEO->META_HIDDEN           = $hidden;
            $OBJ_SEO->AddSwaps();
            
        } else {
            $output .= "UNABLE TO LOAD INSTRUCTOR PROFILE";
        }
        
        return $output;
    }
    
    
    public function GetAllInstructors($QUERY='')
    {
        $output = '';
    
        $Where = ($QUERY) ? " AND $QUERY" : '';
    
        $records = $this->SQL->GetArrayAll(array(
            'table' => $GLOBALS['TABLE_instructor_profile'],
            'keys'  => '*',
            'where' => "`display`=1 AND `active`=1 $Where",
            'order' => '`sort_order` ASC',
        ));
        if ($this->Show_Query) $output .= '<br />' . $this->SQL->Db_Last_Query;
        
        
        if ($records) {
            $Instructor_Logo_Gap    = "<div style='width:{$GLOBALS['INSTRUCTOR_PICTURE_WIDTH']}px; height:{$GLOBALS['INSTRUCTOR_PICTURE_HEIGHT']}px'>&nbsp;</div>";
            
            
            $output .= "<div class=\"articles_holder\">";
            $output .= "<div id='search_current_instructor_list_notice'></div>";
            $output .= "<div id='list_all_instructors'>";
            
            foreach ($records AS $record) {
                
                # PREP EACH FIELD
                foreach ($record as $field => $value) {
                    $record[$field] = str_replace("\n", "<br />", $value);
                }
                
                
                # CUSTOM PREPS
                $record['profile']      = $this->myTruncate($record['profile'], 250);
                $Instructor_Picture     = ($record['primary_pictures_id']) ? "<img src='/office/{$record['primary_pictures_id']}' alt='' border='0' width='{$GLOBALS['INSTRUCTOR_PICTURE_WIDTH']}' height='{$GLOBALS['INSTRUCTOR_PICTURE_HEIGHT']}' />" : $Instructor_Logo_Gap;
                $name                   = "{$record['first_name']} {$record['last_name']}";
                
                
                # MAKE LINKS
                if ($this->Use_Seo_Urls) {
                    $query_link     = ($this->Use_Encrypted_Query) ? '/' . EncryptQuery("instructor_id={$record['instructor_profile_id']}") : ";instructor_id={$record['instructor_profile_id']}";
                    $link           = "{$this->Page_Link}/{$record['first_name']}_{$record['last_name']}" . $query_link;
                } else {
                    $query_link     = ($this->Use_Encrypted_Query) ? ';eq=' . EncryptQuery("instructor_id={$record['instructor_profile_id']}") : ";instructor_id={$record['instructor_profile_id']}";
                    $link           = "{$this->Page_Link}" . $query_link;
                }
                
                global $PAGE;
                $link_schedule  = "{$GLOBALS['LINK_SESSION_SIGNUP_INSTRUCTOR']};instructor_whid={$record['wh_id']};retpage=instructors";
                
                
                # MAKE INSTRUCTOR YOGA TYPES - FOR CLASSES
                $yoga_types_list    = explode (',', $record['yoga_types']);
                $yoga_types_class   = 'All START_SELECT_VALUE ';
                foreach ($yoga_types_list as $type) {
                    $yoga_types_class .= "$type ";
                }
                $yoga_types_class   = substr($yoga_types_class, 0, -1);
                
                
                # OUTPUT RECORD
                $output .= "
                    <div class='instructor_all_wrapper $yoga_types_class'>
                        <div class='article_all_picture_col'><a href='{$link}'>{$Instructor_Picture}</a></div>
                        <div class='article_all_content_col'>
                            <div class=\"article_all_title\">{$name}</div>
                            <br />
                            <div class=\"article_all_content\">{$record['profile']}</div>
                            <br />
                            <div class=\"article_all_link\"><a href='{$link}' class='link_arrow'>Read more...</a></div>
                            <div class=\"article_all_link\"><a href='{$link_schedule}' class='link_arrow'>View Schedule...</a></div>
                        </div>
                        <div class='clear'></div>
                    <br /><br /><br />
                    </div>
                    ";
            }
            
            $output .= "</div>";
            $output .= "</div>";
        
        
        } else {
            $output .= "NO RECORDS FOUND";
        }
        
        return $output;
    }
    
    
    public function GetInstructorMenu()
    {
        
        $btn_session        = "<a href='{$GLOBALS['LINK_SESSION_SIGNUP']}'><div class='btn_scheduleASession'>&nbsp;</div></a>";
        
        $this->AddScript();
        
        $output = "";
        
        
        # MAKE FORM FOR SEARCHING BY YOGA TYPES
        $types = "All|{$this->yoga_type_list}";
        $options_form = OutputForm(array(
            'form||post|OPTIONS_YOGA_TYPES',
            "@select||yoga_types|N||$types",
            'endform',
        ));
        
        
        $output .= "
            <div class='article_category_title'>search by yoga style</div>
            <div class='left_content'>{$options_form}</div>
            
            <br /><br />
            {$btn_session}
            <br /><br />
            <div class='orange left_header'>want to join our team?</div>
            <div class='left_content'>@@INSTRUCTOR_BECOME_INFO@@ <a href='/signup_instructor'>Apply Now</a></div>
            <br />
        ";
        
        return $output;
    }
    
    public function AddScript()
    {
        $script = "
            $('#FORM_yoga_types').change(function(){
                ChangeInstructorsByYogaType();
            });
            
            $('#FORM_yoga_types').val('All');
            ChangeInstructorsByYogaType();
            ";
        AddScriptOnReady($script);
        
        $script = "
            function ChangeInstructorsByYogaType() {
                // ==============================================================================
                // FUNCTION :: Change drop-down to search by different yoga type
                // ==============================================================================
                
                // Change the classes to show or hide instructor
                var tClass = $('#FORM_yoga_types').val();
                $('#list_all_instructors > .instructor_all_wrapper').each(function(index) {
                    if ($(this).hasClass(tClass)) {
                        $(this).css('display', '');
                    } else {
                        $(this).css('display', 'none');
                    }
                });
                
                // Update notice to user of search type
                if (tClass == 'All' || tClass == 'START_SELECT_VALUE') {
                    var tString = '';
                } else {
                    var tString = '<h1>Searching Instructors By Yoga Type: '+tClass+'</h1>';
                }
                $('#search_current_instructor_list_notice').html(tString);
                
            }
            ";
        AddScript($script);
    }
    
    
    
    public function ProcessTableCell($field, &$value, &$td_options, $id='')
    {
        parent::ProcessTableCell($field, $value, $td_options, $id);

        switch ($field) {
            case 'description':
                $value = TruncStr(strip_tags($value), 100);
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

    function myTruncate($string, $limit, $break=".", $pad="...") 
    {
        // return with no change if string is shorter than $limit 
        if(strlen($string) <= $limit) return $string; 
        // is $break present between $limit and the end of the string? 
        if(false !== ($breakpoint = strpos($string, $break, $limit))) { 
            if($breakpoint < strlen($string) - 1) { 
                $string = substr($string, 0, $breakpoint) . $pad; 
            } 
        } 
        return $string; 
    }

    
} // END CLASS