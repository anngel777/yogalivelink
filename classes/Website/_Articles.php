<?php
class Website_Articles extends BaseClass
{
    public $Page_Link               = '';
    public $Articles_Records        = null;
    public $Days_Most_Recent        = 5;        // if article within this many days - show it as most recent
    public $Instructor_Logo         = "/images/template/instructor_logo.jpg";
    public $Instructor_Logo_Legend  = "/images/template/instructor_logo_legend.jpg";
    public $Instructor_Logo_Gap     = "/images/spacer.gif";

    public $Show_Query              = false;
    
    public function  __construct()
    {
        parent::__construct();

        global $PAGE;
        //echo ArrayToStr($PAGE);
        $this->Page_Link = $PAGE['pagelink'];
        
        
        $this->Add_Submit_Name      = 'DISCOUNTS_SUBMIT_ADD';
        $this->Edit_Submit_Name     = 'DISCOUNTS_SUBMIT_EDIT';
        $this->Table                = 'website_articles';
        $this->Flash_Field          = 'Website Articles';
        $this->Index_Name           = 'website_articles_id';
        
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
    
    
    
    public function GetArticle($ID='', $EQ='')
    {
        $ID = IntOnly($ID);
        $output = '';
    
        if (!$ID) {
            
            if ($EQ) {
                $eq     = GetEncryptQuery($EQ, false);
                $Query  = str_replace('::', '=', $eq['QUERY']);
            } else {
                $Query  = '';
            }
        
            $output .= $this->GetAllArticles($Query);
            
        } else {
            $record = $this->SQL->GetRecord(array(
                'table' => $this->Table,
                'keys'  => '*',
                'where' => "`website_articles_id`=$ID AND `active`=1",
            ));
            if ($this->Show_Query) $output .= '<br />' . $this->SQL->Db_Last_Query;
            
            if ($record) {
                
                foreach ($record as $field => $value) {
                    $record[$field] = str_replace("\n", "<br />", $value);
                }
                
                $eq         = (Get('eq')) ? ';eq=' . Get('eq') : '';
                $back_link  = "<h3><a href='{$this->Page_Link}{$eq}' class='link_arrow'>Back To Articles</a></h3>";
                
                $output .= "
                    <div>{$back_link}</div>
                    <br /><br />
                    <div class=\"article_title\">{$record['title']}</div>
                    <div class=\"article_author\">by {$record['author']}</div>
                    <div class=\"article_source\">source: {$record['source']}</div>
                    <div class=\"article_p\">{$record['content']}</div>
                    <br /><br />
                    <div class=\"article_footer\">{$record['footer']}</div>
                    <br /><br />
                    <div>{$back_link}</div>
                    ";
            } else {
                $output .= "NO RECORD FOUND";
            }
        }
        
        return $output;
    }
    
    public function GetAllArticles($QUERY='')
    {
        $output = '';
    
        $Where = ($QUERY) ? " AND $QUERY" : '';
    
        $records = $this->SQL->GetArrayAll(array(
            'table' => $this->Table,
            'keys'  => '*',
            'where' => "`display`=1 AND `active`=1 $Where",
            'order' => '`sort_order` ASC',
        ));
        if ($this->Show_Query) $output .= '<br />' . $this->SQL->Db_Last_Query;
        
        
        if ($records) {
            $Instructor_Logo        = "<img src='{$this->Instructor_Logo}' width='100' height='150' alt='' border='0' />";
            $Instructor_Logo_Gap    = "<div style='width:100px; height:150px'>&nbsp;</div>";
            $Instructor_Picture     = "<img src='/office/images/instructors/thumbnail_no_picture.jpg' alt='' border='0' />";
            
            $output .= "<div class=\"articles_holder\">";
            
            foreach ($records AS $record) {
                
                # PREP EACH FIELD
                foreach ($record as $field => $value) {
                    $record[$field] = str_replace("\n", "<br />", $value);
                }
                
                
                # CUSTOM PREPS
                $record['content']  = $this->myTruncate($record['content'], 250);
                $Instructor_Picture = ($record['instructor'] == 1) ? $Instructor_Logo : $Instructor_Logo_Gap;
                
                
                # MAKE LINK
                $eq         = (Get('eq')) ? ';eq=' . Get('eq') : '';
                $link       = "{$this->Page_Link};article_id={$record['website_articles_id']}{$eq}";
                
                
                # OUTPUT RECORD
                $output .= "
                    <div class='article_all_wrapper'>
                        <div class='article_all_picture_col' style='display:none;'>{$Instructor_Picture}</div>
                        <div class='article_all_content_col'>
                        
                            <div class=\"article_all_title\">{$record['title']}</div>
                            <div class=\"article_all_author\">by {$record['author']}</div>
                            <br />
                            <div class=\"article_all_content\">{$record['content']}</div>
                            <div class=\"article_all_link\"><a href='{$link}'>Read more...</a></div>
                        </div>
                        <div class='clear'></div>
                    </div>
                    <br /><br />
                    ";
            }
            
            $output .= "</div>";
        
        
        } else {
            $output .= "NO RECORDS FOUND";
        }
        
        return $output;
    }
    
    
    public function GetArticleMenu()
    {
        $btn_session        = '<a href="#" class="index_button_complex" style="width:200px;">SCHEDULE a session!</a>';
        $btn_session        = "<a href='{$GLOBALS['LINK_SESSION_SIGNUP']}'><div class='btn_scheduleASession'>&nbsp;</div></a>";
        
        
        # GET ALL THE ARTICLES
        # ==================================================
        $records = $this->SQL->GetArrayAll(array(
            'table' => $this->Table,
            'keys'  => '*',
            'where' => "`display`=1 AND `active`=1",
            'order' => '`sort_order` ASC',
        ));
        
        if ($records) {
            $this->Articles_Records = $records;
        }
        
        
        # CREATE THE CATEGORY LIST
        # ==================================================
        $cat_list = array(
            'all'                           => 0,
            'recent'                        => 0,
            'by_YogaLiveLink_instructors'   => 0,
        );
        
        
        $day        = date("d")-($this->Days_Most_Recent + 1);
        $day        = str_pad($day, 2, "0", STR_PAD_LEFT);
        $timestamp  = date("Y-m-{$day} H:i:s");
        
        
        $cat_list_query = array(
            'all'                           => "",
            'recent'                        => "`updated` > '{$timestamp}'",
            'by_YogaLiveLink_instructors'   => "`instructor`::1",
        );
        
        
        foreach ($this->Articles_Records as $record) {
            $cat            = str_replace(' ', '_', $record['category']);
            $cat_list[$cat] = (isset($cat_list[$cat])) ? $cat_list[$cat] + 1 : 1;
            
            # CREATE THE 'ALL' CATEGORY
            $cat_list['all'] = $cat_list['all'] + 1;
            
            # CREATE THE 'RECENT' CATEGORY - based on UPDATED field
            $now            = strtotime("now");
            $article_time   = strtotime($record['updated']);
            $seconds_diff   = $now - $article_time;
            $days_diff      = floor(((($seconds_diff)/60)/60)/24);
            
            if ($days_diff < $this->Days_Most_Recent + 1) {
                $cat_list['recent'] = $cat_list['recent'] + 1;
            }
            
            # CREATE THE 'AUTHOR' CATEGORY
            if ($record['instructor'] == 1) {
                $cat_list['by_YogaLiveLink_instructors'] = $cat_list['by_YogaLiveLink_instructors'] + 1;
            }
        }
        
        
        # OUTPUT THE CATEGORY LIST
        # ==================================================
        $output = "<div class='article_category_title'>sort by category</div>";
        
        foreach ($cat_list as $category => $count) {
            $category_title = str_replace('_', ' ', $category);
            
            
            if (isset($cat_list_query[$category])) {
                $str        = "QUERY={$cat_list_query[$category]}";
                $eq         = EncryptQuery($str);
                $cat_link   = "{$this->Page_Link};eq={$eq}";
            } else {
                $str        = "QUERY=category::'$category_title'";
                $eq         = EncryptQuery($str);
                $cat_link   = "{$this->Page_Link};eq={$eq}";
            }
            
            $output    .= "<div class='article_category'><a href='{$cat_link}'>{$category_title} ({$count})</a></div>";
        
        }
        
        $output .= "
            <br /><br /><br />
            <img src='{$this->Instructor_Logo_Legend}' alt='' border='0' />
            <br /><br />
            {$btn_session}
            <br /><br />
        ";
        
        return $output;
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