<?php
class Profile_HelpCenter
{
    public $Show_Query                      = false;     // TRUE = output the database queries ocurring on this page
    public $Use_Seo_Urls                    = true;     // TRUE = use SEO URLs
    
    public $Show_Chat                       = true;     // TRUE = show contact by 'chat' option
    public $Show_Email                      = true;     // TRUE = show contact by 'email' option
    public $Show_Phone                      = true;     // TRUE = show contact by 'phone' option
    public $Show_Youtube                    = false;    // TRUE = show view YouTube videos option
    public $Show_FAQ                        = true;     // TRUE = show FAQ section
    public $Use_Display_All_Category        = true;     // TRUE = Give option to 'disaply all faq'
    public $Use_Common_Category             = false;
    
    public $All_Category_Id                 = 9999;
    public $Common_Category_Id              = 0;
    public $Default_Category_Id             = 1;        // used if $Use_Common_Category = FALSE
    
    // ---------- NON-MODIFIABLE VARIABLES ----------
    public $WH_ID                           = 0;
    public $Table_Helpcenter_FAQs           = null;
    public $Ico_Email                       = null;
    public $Ico_Chat                        = null;
    public $Ico_Phone                       = null;
    public $Ico_Youtube                     = null;
    public $Is_Instructor                   = false;
    
    
    public function  __construct()
    {
        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => 'Richard Witherspoon',
            'Updated'     => '2012-06-20',
            'Version'     => '1.2',
            'Description' => 'Output the helpcenter on the website',
        );
        
        /* UPDATE LOG ======================================================================================
        
            2012-04-19  -> made modifications for the default instructor category
            2012-06-19  -> Modified button to show in the menu - GetInfographic()
            2012-06-20  -> Turned off queries which were still showing from previous work
        
        ====================================================================================== */
        
        $this->SetSQL();
		
        $this->Default_Category_Id = ($this->Use_Common_Category) ? 0 : $this->Default_Category_Id;
        
        

        
        
		# GLOBAL VARIABLES
        # ================================================
		$this->Ico_Email 	            = $GLOBALS['ICO_EMAIL'];
		$this->Ico_Chat                 = $GLOBALS['ICO_CHAT'];
		$this->Ico_Phone 	            = $GLOBALS['ICO_PHONE'];
        $this->Ico_Youtube              = $GLOBALS['ICO_YOUTUBE'];
        $this->Table_Helpcenter_FAQs    = $GLOBALS['TABLE_helpcenter_faqs'];
        
    } // -------------- END __construct --------------

    public function SetSQL()
    {
        if (empty($this->SQL)) {
            $this->SQL = Lib_Singleton::GetInstance('Lib_Pdo');
        }
    }
    
    public function AddScript()
    {
        $eq = EncryptQuery("class=Touchpoint_ContactForm;v1=$this->WH_ID");
        $profile_link = $GLOBALS['PAGE']['ajaxlink'] . ';action=customer_profile';
        
        
        $script = "
            function InitializeOnReady_Profile_CustomerProfileHelpCenter() {
                $('.faq_q').bind('click', function() {
                    $(this).parent().find('div').toggle();
                });
            }
            
            InitializeOnReady_Profile_CustomerProfileHelpCenter();
            ";
            
        AddScriptOnReady($script);
    }
    
    
    public function ColumnLeft()
    {
        $output = $this->GetCategories();
        $output .= $this->GetInfographic();
        
        return $output;
    }
    
    
    public function GetInfographic()
    {
        $output = "
            <br /><br /><br /><br />
            
            <center>            
            <a href='/how_yll_works'><div class='buttonImg'><img src='/images/buttons/btn_how_works_off.png'></div></a>
            <a href='/pricing'><div class='buttonImg'><img src='/images/buttons/btn_pricing_off.png'></div></a>
            <a href='/signup'><div class='buttonImg'><img src='/images/buttons/btn_get_started_off.png'></div></a>
            </center>
            <br />
        ";
        
        return $output;
    }
    
    
    public function Execute()
    {
        $box_chat       = '';
        $box_phone      = '';
        $box_email      = '';
        $box_faq        = '';
        $box_youtube    = '';
        
        if ($this->Show_Chat) {
            $OBJ_CHAT       = new Chat_Chat();
            $box_chat       = $OBJ_CHAT->OutputChatStatusBoxType3();
        }
        
        if ($this->Show_Phone) {
            global $CONTACT_PHONE_NUMBER;
            $link           = "";
            $box_phone      = AddBox_Type3($CONTACT_PHONE_NUMBER, $link, $this->Ico_Phone);
        }
        
        if ($this->Show_Email) {
            $eq_contactform = EncryptQuery("class=Touchpoint_ContactForm;v1=$this->WH_ID");
            $link           = "<a href='#' onclick=\"top.parent.appformCreateOverlay('Contact Us', getClassExecuteLinkNoAjax('{$eq_contactform}')+';template=overlay', 'apps'); return false;\">";
            $box_email      = AddBox_Type3('email', $link, $this->Ico_Email);
        }
        
        if ($this->Show_Youtube) {
            $link           = "<a href='http://www.youtube.com' target='_blank'>";
            $box_youtube    = AddBox_Type3('watch our help videos!', $link, $this->Ico_Youtube);
        }
        
        if ($this->Show_FAQ) {
            $box_faq        = $this->FAQs();
        }
        
        
        
        $output = "
        <div>
            <div class='col'>
                {$box_youtube}
            </div>
            <div class='col' style='width:50px;'>
                &nbsp;
            </div>        
            <div class='col'>
                {$box_email}
            </div>
            <div class='col' style='width:50px;'>
                &nbsp;
            </div>
            <div class='col'>
                {$box_chat}
            </div>
            <div class='col' style='width:50px;'>
                &nbsp;
            </div>
            <div class='col'>
                {$box_phone}
            </div>
            <div class='clear'></div>
        </div>
        <br /><br />
        {$box_faq}
        ";
        
        $script = "InitializeOnReady_Profile_CustomerProfileHelpCenter();";
        $output .= EchoScript($script);
        
        return $output;
    }
    
    public function GetCategories()
    {
        # FUNCTION :: Get and configure the categories for the questions
        
        
        global $PAGE;
        $output = '';
        
        
        if ($this->Is_Instructor) {
            $type       = " AND `type_instructor`=1";
        } else {
            $type       = " AND `type_customer`=1";
        }
        
        
        # GET THE CURRENTLY SELECTED CATEGORY
        # ============================================================================
        $default_selected_cid   = ($this->Use_Common_Category)  ? $this->Common_Category_Id : $this->Default_Category_Id;
        $eq_cat                 = (Get('eq'))                   ? GetEncryptQuery(Get('eq'), false) : null;
        $selected_cid           = (isset($eq_cat['cid']))       ? $eq_cat['cid'] : $default_selected_cid;
        
        if ($this->Use_Common_Category) {
            $selected_common    = (isset($eq_cat['special']) && $eq_cat['special'] == 'common') ? true : false;
            $selected_common    = ($selected_cid == $this->Common_Category_Id) ? true : $selected_common;
        } else {
            $selected_common    = ($selected_cid == $this->Common_Category_Id) ? true : false;
        }
        
        if ($this->Use_Display_All_Category) {
            $selected_all       = (isset($eq_cat['special']) && $eq_cat['special'] == 'all') ? true : false;
            $selected_all       = ($selected_cid == $this->All_Category_Id) ? true : $selected_all;
        }
        //$selected_all = true;
        
        
        # GET ALL THE CATEGORIES
        # ============================================================================
        $records = $this->SQL->GetArrayAll(array(
            'table' => $GLOBALS['TABLE_helpcenter_categories'],
            'keys'  => '*',
            'where' => "`active`=1 $type",
        ));
        if ($this->Show_Query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        
        
        
        # OUTPUT THE CATEGORIES MENU
        # ============================================================================
        $output    .= '<div class="orange left_header">HELP CENTER TOPICS</div><br />';
        $output    .= '<div class="left_content">';
        
        # OUTPUT COMMON CATEGORY
        if ($this->Use_Common_Category) {
            $eq         = EncryptQuery("cat=Most Common;cid=0;special=common");
            $link       = "{$PAGE['pagelink']};eq=$eq";
            $class      = ($selected_common) ? 'faq_selected_category' : '';
            $output    .= "<div class='$class'><a href='{$link}' class='link_arrow' >Most Common</a></div><br />";
        }
        
        # OUTPUT ALL DATABASE CATEGORIES
        foreach ($records as $record) {
            
            # MAKE LINKS
            if ($this->Use_Seo_Urls) {
                $title          = ProcessStringForSeoUrl($record['title']);
                $query_link     =  '/' . EncryptQuery("cid={$record['helpcenter_categories_id']}");
                $link           = "{$PAGE['pagelink']}/{$title}" . $query_link;
            } else {
                $eq             = EncryptQuery("cat={$record['title']};cid={$record['helpcenter_categories_id']}");
                $link           = "{$PAGE['pagelink']};eq=$eq";
            }
            
            $class      = ($record['helpcenter_categories_id'] == $selected_cid) ? 'faq_selected_category' : '';
            $output    .= "<div class='$class'><a href='{$link}' class='link_arrow' >{$record['title']}</a></div>";
        }
        
        # OUTPUT DISPLAY ALL CATEGORY
        if ($this->Use_Display_All_Category) {
            
            # MAKE LINKS
            if ($this->Use_Seo_Urls) {
                $title          = ProcessStringForSeoUrl('View All');
                $query_link     =  '/' . EncryptQuery("cid={$this->All_Category_Id}");
                $link           = "{$PAGE['pagelink']}/{$title}" . $query_link;
            } else {
                $eq             = EncryptQuery("cat=View All;cid={$this->All_Category_Id};special=all");
                $link           = "{$PAGE['pagelink']};eq=$eq";
            }
            
            $class      = ($selected_all) ? 'faq_selected_category' : '';
            $output    .= "<br /><div class='$class'><a href='{$link}' class='link_arrow' >View All</a></div>";
        }
        
        $output    .= '</div>';
        
        
        AddStyle("
        .faq_selected_category {
            background-color:#F2935B;
            color:#fff;
        }
        ");
        
        return $output;
        

    }
    
    public function FAQs()
    {
        # FUNCTION :: Get the FAQs out of the database and display them
        
        
        if ($this->Is_Instructor) {
            $type       = " AND `type_instructor`=1";
            $cat_field  = 'categories_instructor';
            
            $this->Default_Category_Id = 0;
        
        } else {
            $type       = " AND `type_customer`=1";
            $cat_field  = 'categories_customer';
        }
        
        # DECRYPT THE ENCRYPTED QUERY
        # ====================================================
        $eq             = (Get('eq'))               ? GetEncryptQuery(Get('eq'), false) : null;
        $cid            = (isset($eq['cid']))       ? $eq['cid'] : $this->Default_Category_Id;
        $cat            = (isset($eq['cat']))       ? $eq['cat'] : '';
        
        $special        = (isset($eq['special']))   ? $eq['special'] : '';
        $special        = ($eq == null)             ? 'force_common' : $special;
        
        $category       = ($cid != 0)               ? " AND CONCAT(',', `{$cat_field}`, ',') LIKE '%,{$cid},%' " : '';
        $cat_title      = ($cat)                    ? $cat : "frequently asked questions";
        
        $cat_special    = '';
        if ($this->Use_Common_Category) {
            switch ($special) {
                case 'common':
                case 'force_common':
                    $cat_special = " AND `most_common`=1 ";
                break;
            }
        }
        
        if ($this->Use_Display_All_Category) {
            $category = ($cid == $this->All_Category_Id) ? '' : $category;
        }
        
        $records = $this->SQL->GetArrayAll(array(
            'table' => $GLOBALS['TABLE_helpcenter_faqs'],
            'keys'  => '*',
            'where' => "`active`=1 $type $category $cat_special",
            'order' => 'sort_order ASC',
        ));
        if ($this->Show_Query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        
        $tab_content_faqs =  "<ul class='faqs'>";
        foreach ($records as $record) {
            $tab_content_faqs .= "
            <li>
                <a class='faq_q'>{$record['question']}</a>
                <div class='faq_a' style='display: none;'>{$record['answer']}</div>
            </li>";
        }
        $tab_content_faqs .= "</ul>";
        
        $output = "
            <div class='faq_title'>{$cat_title}</div>
            <div id='helpcenter_faq'>$tab_content_faqs</div>";
        
        return $output;
    }

    
    
}  // -------------- END CLASS --------------