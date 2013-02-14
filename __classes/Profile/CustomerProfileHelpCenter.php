<?php
class Profile_CustomerProfileHelpCenter extends Profile_HelpCenter
{
/*
    public $WH_ID                           = 0;
    public $Show_Query                      = true;
    
    public $Table_Helpcenter_FAQs           = null;
    public $Ico_Email                       = null;
    public $Ico_Chat                        = null;
    public $Ico_Phone                       = null;
    public $Ico_Youtube                     = null;


    public $Show_Chat                       = true;
    public $Show_Email                      = true;
    public $Show_Phone                      = true;
    public $Show_Youtube                    = false;
    public $Show_FAQ                        = true;
    
    public $Is_Instructor                   = false;
    
    
    public function  __construct()
    {
        $this->SetSQL();
		
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
                    $(this).parent().find('p').toggle();
                });
            }
            
            InitializeOnReady_Profile_CustomerProfileHelpCenter();
            ";
            
        AddScriptOnReady($script);
    }
    
    
    public function ColumnLeft()
    {
        $output = $this->GetCategories();
        
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
            $link_eq        = getClassExecuteLinkNoAjax($eq_contactform);
            $link           = "<a href='#' onclick=\"top.parent.appformCreateOverlay('Contact Us', '{$link_eq}', 'apps'); return false;\">";
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
        global $PAGE;
        
        if ($this->Is_Instructor) {
            $type       = " AND `type_instructor`=1";
        } else {
            $type       = " AND `type_customer`=1";
        }
        
        
        # GET THE CURRENTLY SELECTED CATEGORY
        # ============================================================================
        $eq_cat             = (Get('eq')) ? GetEncryptQuery(Get('eq'), false) : null;
        $selected_cid       = (isset($eq_cat['cid'])) ? $eq_cat['cid'] : 0;
        $selected_common    = (isset($eq_cat['special']) && $eq_cat['special'] == 'common') ? true : false;
        $selected_common    = ($selected_cid == 0) ? true : $selected_common;
        
        
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
        $output     = '<div class="orange left_header">HELP CENTER TOPICS</div><br />';
        $output    .= '<div class="left_content">';
        
        $eq         = EncryptQuery("cat=Most Common;cid=0;special=common");
        $link       = "{$PAGE['pagelink']};eq=$eq";
        $class      = ($selected_common) ? 'faq_selected_category' : '';
        $output    .= "<div class='$class'><a href='{$link}' class='link_arrow' >Most Common</a></div><br />";
        
        foreach ($records as $record) {
        
            $class      = ($record['helpcenter_categories_id'] == $selected_cid) ? 'faq_selected_category' : '';
        
            $eq         = EncryptQuery("cat={$record['title']};cid={$record['helpcenter_categories_id']}");
            $link       = "{$PAGE['pagelink']};eq=$eq";
            $output    .= "<div class='$class'><a href='{$link}' class='link_arrow' >{$record['title']}</a></div>";
        }
        
        #$class      = ($selected_all) ? 'faq_selected_category' : '';
        #$output    .= "<br /><div class='$class'><a href='{$PAGE['pagelink']}' class='link_arrow' >View All</a></div>";
        
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
        if ($this->Is_Instructor) {
            $type       = " AND `type_instructor`=1";
            $cat_field  = 'categories_instructor';
        } else {
            $type       = " AND `type_customer`=1";
            $cat_field  = 'categories_customer';
        }
        
        # DECRYPT THE ENCRYPTED QUERY
        # ====================================================
        $eq             = (Get('eq')) ? GetEncryptQuery(Get('eq'), false) : null;
        $cid            = (isset($eq['cid'])) ? $eq['cid'] : 0;
        $cat            = (isset($eq['cat'])) ? $eq['cat'] : '';
        $special        = (isset($eq['special'])) ? $eq['special'] : '';
        $special        = ($eq == null) ? 'force_common' : $special;
        
        
        //$category       = ($cid != 0) ? " AND `categories` IN ('$cid')" : '';
        $category       = ($cid != 0) ? " AND CONCAT(',', `{$cat_field}`, ',') LIKE '%,{$cid},%' " : '';
        $cat_title      = ($cat) ? $cat : "frequently asked questions";
        
        $cat_special    = '';
        switch ($special) {
            case 'common':
            case 'force_common':
                $cat_special = " AND `most_common`=1 ";
            break;
        }
        
        
        
        $records = $this->SQL->GetArrayAll(array(
            'table' => $GLOBALS['TABLE_helpcenter_faqs'],
            'keys'  => '*',
            'where' => "`active`=1 $type $category $cat_special",
        ));
        if ($this->Show_Query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        
        $tab_content_faqs =  "<ul class='faqs'>";
        foreach ($records as $record) {
            $tab_content_faqs .= "
            <li>
                <a class='faq_q'>{$record['question']}</a>
                <p class='faq_a' style='display: none;'>{$record['answer']}</p>
            </li>";
        }
        $tab_content_faqs .= "</ul>";
        
        $output = "
            <div class='faq_title'>{$cat_title}</div>
            <div id='helpcenter_faq'>$tab_content_faqs</div>";
        
        return $output;
    }

    
*/
}  // -------------- END CLASS --------------