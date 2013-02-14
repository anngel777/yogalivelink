<?php
class Website_Articles extends BaseClass
{
    public $Page_Link               = '';
    public $Page_Title              = 'Select_An_Article';
    public $Articles_Records        = null;
    public $Days_Most_Recent        = 5;        // if article within this many days - show it as most recent
    public $Instructor_Logo         = "/images/template/instructor_logo.jpg";
    public $Instructor_Logo_Legend  = "/images/template/instructor_logo_legend.jpg";
    public $Instructor_Logo_Gap     = "/images/spacer.gif";
    
    public $Use_Seo_Urls            = true;
    public $Use_Encrypted_Query     = true;
    
    public $Show_Query              = false;
    public $Show_Link_Details       = false;
    
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
    
    
    
    public function HandleArticle($ID='', $EQ='')
    {
        
        if ($this->Use_Encrypted_Query) {
            if ($EQ) {
                $eq = GetEncryptQuery($EQ, false);
                $ID = (isset($eq['article_id'])) ? IntOnly($eq['article_id']) : 0;
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
        
            $output = $this->GetAllArticles($Query);
            
        } else {
            $output = $this->GetSingleArticle($ID);
        }
        
        return $output;
    }
    
    
    
    
    public function GetSingleArticle($ID='')
    {
        $output = '';
        
        if ($ID) {
            $record = $this->SQL->GetRecord(array(
                'table' => $this->Table,
                'keys'  => '*',
                'where' => "`website_articles_id`=$ID AND `active`=1",
            ));
            if ($this->Show_Query) $output .= '<br />' . $this->SQL->Db_Last_Query;
        } else {
            $record = null;
        }
        
        if ($record) {
            
            foreach ($record as $field => $value) {
                $record[$field] = str_replace("\n", "<br />", $value);
            }
            
            
            # GET THE CURRENTLY SELECTED CATEGORY
            # ============================================================================
            $eq_retpage     = (Get('eq')) ? GetEncryptQuery(Get('eq'), false) : null;
            $retpage        = (isset($eq_retpage['retpage'])) ? $eq_retpage['retpage'] : $this->Page_Link;
            $category       = (isset($eq_retpage['category'])) ? $eq_retpage['category'] : '';
            $back_link      = "<h3><a href='{$retpage}' class='link_arrow'>Back To Yoga Articles</a></h3>";
            
            if ($this->Show_Link_Details) {
                $l_temp = (isset($eq_retpage['retpage'])) ? "<div class=\"article_all_link\">retpage ==> {$eq_retpage['retpage']}</div>" : '';
                $back_link .= "
                        <div style='color:blue;'>
                        {$l_temp}
                        <div class=\"article_all_link\">Page_Link ==> {$this->Page_Link}</div>
                        <div class=\"article_all_link\">category ==> {$category}</div>
                        </div>
                        <br />";
            }
            
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
                
                
            # CREATE THE SEO FIELDS
            # =======================================================
            # description
            $description = $record['title'] . ' ' . $record['content'];
            $description = substr($description, 0, 120);
            
            $OBJ_SEO                        = new Website_SEO();
            $OBJ_SEO->META_TITLE            = $record['title'];
            $OBJ_SEO->META_DESCRIPTION      = $description;
            $OBJ_SEO->META_KEYWORDS         = $record['keywords'];
            $OBJ_SEO->META_HIDDEN           = $record['keywords'];
            $OBJ_SEO->AddSwaps();
            
        } else {
            $output .= "UNABLE TO LOAD ARTICLE";
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
                $record['content']      = $this->myTruncate($record['content'], 250);
                $Instructor_Picture     = ($record['instructor'] == 1) ? $Instructor_Logo : $Instructor_Logo_Gap;
                
                
                # CREATE LINKS
                $eq                 = Get('eq');
                $retpage            = "{$this->Page_Link}/{$this->Page_Title}/{$eq}";
                $eq_decode          = (Get('eq')) ? GetEncryptQuery(Get('eq'), false) : null;
                $category           = (isset($eq_decode['category'])) ? $eq_decode['category'] : '';
                $eq_link            = EncryptQuery("article_id={$record['website_articles_id']};retpage={$retpage};category={$category}");
                $non_eq_link        = ";article_id={$record['website_articles_id']};retpage={$retpage};category={$category}";
                
                
                # MAKE LINKS
                if ($this->Use_Seo_Urls) {
                    $title          = ProcessStringForSeoUrl($record['title']);
                    $query_link     = ($this->Use_Encrypted_Query) ? '/' . $eq_link : $non_eq_link;
                    $link           = "{$this->Page_Link}/{$title}" . $query_link;
                } else {
                    $query_link     = ($this->Use_Encrypted_Query) ? ';eq=' . $eq_link : $non_eq_link;
                    $link           = "{$this->Page_Link}" . $query_link;
                }
                
                $link_info = '';
                if ($this->Show_Link_Details) {
                    $link_info = "<div style='color:blue;'>
                            <div class=\"article_all_link\">website_articles_id ==> {$record['website_articles_id']}</div>
                            <div class=\"article_all_link\">retpage ==> {$retpage}</div>
                            <div class=\"article_all_link\">category ==> {$category}</div>
                            </div>";
                }
                
                
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
                            {$link_info}
                            
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
            #'recent'                        => 0,
            #'by_YogaLiveLink_instructors'   => 0,
        );
        
        
        $day        = date("d")-($this->Days_Most_Recent + 1);
        $day        = str_pad($day, 2, "0", STR_PAD_LEFT);
        $timestamp  = date("Y-m-{$day} H:i:s");
        
        
        $cat_list_query = array(
            'all'                           => "",
            #'recent'                        => "`updated` > '{$timestamp}'",
            #'by_YogaLiveLink_instructors'   => "`instructor`::1",
        );
        
        
        foreach ($this->Articles_Records as $record) {
            $cat            = str_replace(' ', '_', $record['category']);
            $cat_list[$cat] = (isset($cat_list[$cat])) ? $cat_list[$cat] + 1 : 1;
            
            # CREATE THE 'ALL' CATEGORY
            $cat_list['all'] = $cat_list['all'] + 1;
            
            /*
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
            */
        }
        
        
        # GET THE CURRENTLY SELECTED CATEGORY
        # ============================================================================
        $eq_cat             = (Get('eq')) ? GetEncryptQuery(Get('eq'), false) : null;
        $selected_cat       = (isset($eq_cat['category'])) ? $eq_cat['category'] : 'all';
        
        
        
        
        # OUTPUT THE CATEGORY LIST
        # ==================================================
        $output = "<div class='article_category_title'>sort by category</div>";
        
        foreach ($cat_list as $category => $count) {
            $category_title     = str_replace('_', ' ', $category);
            $class              = (strtolower($category_title) == strtolower($selected_cat)) ? 'article_selected_category' : 'article_category';
            
            if (isset($cat_list_query[$category])) {
                $str        = "QUERY={$cat_list_query[$category]};category={$category_title}";
                $eq         = EncryptQuery($str);
                $cat_link   = "{$this->Page_Link}/{$this->Page_Title}/{$eq}";
            } else {
                $str        = "QUERY=category::'$category_title';category={$category_title}";
                $eq         = EncryptQuery($str);
                $cat_link   = "{$this->Page_Link}/{$this->Page_Title}/{$eq}";
            }
            
            $output    .= "<div class='{$class}'><a href='{$cat_link}'>{$category_title} ({$count})</a></div>";
            
            if ($this->Show_Link_Details) {
                $l_temp = (isset($cat_list_query[$category])) ? "<div class=\"article_all_link\">QUERY ==> {$cat_list_query[$category]}</div>" : '';
                $output .= "
                        <div style='color:blue;'>
                        {$l_temp}
                        <div class=\"article_all_link\">category_title ==> {$category_title}</div>
                        </div>
                        <br />";
            }
        }
        
        $output .= "
            <br /><br /><br />
            
            <div style='display:none;'>
            <img src='{$this->Instructor_Logo_Legend}' alt='' border='0' />
            <br /><br />
            </div>
            
            {$btn_session}
            <br /><br />
        ";
        
        return $output;
    }
    
    
    
    
    
    public function ProcessTableCell($field, &$value, &$td_options, $id='')
    {
        parent::ProcessTableCell($field, $value, $td_options, $id);

        switch ($field) {
            case 'keywords':
            case 'content':
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