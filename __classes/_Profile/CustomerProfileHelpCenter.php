<?php
class Profile_CustomerProfileHelpCenter
{
    public $WH_ID                           = 0;
    public $Show_Query                      = false;
    
    public $Table_Helpcenter_FAQs           = null;
    public $Ico_Email                       = null;
    public $Ico_Chat                        = null;
    public $Ico_Phone                       = null;

    public $Show_Chat                       = true;
    public $Show_Email                      = true;
    public $Show_Phone                      = true;
    public $Show_FAQ                        = true;
    
    
    
    public function  __construct()
    {
        $this->SetSQL();
		
		# GLOBAL VARIABLES
        # ================================================
		$this->Ico_Email 	            = $GLOBALS['ICO_EMAIL'];
		$this->Ico_Chat                 = $GLOBALS['ICO_CHAT'];
		$this->Ico_Phone 	            = $GLOBALS['ICO_PHONE'];
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
            ";
            
        AddScript($script);
    }
    
       
    public function Execute()
    {
        $box_chat   = '';
        $box_phone  = '';
        $box_email  = '';
        $box_faq    = '';
        
        
        if ($this->Show_Chat) {
            $OBJ_CHAT   = new Chat_Chat();
            $box_chat   = $OBJ_CHAT->OutputChatStatusBox();
        }
        
        if ($this->Show_Phone) {
            global $CONTACT_PHONE_NUMBER;
            $box_phone = AddBox_Type2('Call Us', $CONTACT_PHONE_NUMBER , $this->Ico_Phone);
        }
        
        if ($this->Show_Email) {
            $eq = EncryptQuery("class=Touchpoint_ContactForm;v1=$this->WH_ID");
            $content = "[<a href='#' onclick=\"top.parent.appformCreateOverlay('Contact Us', getClassExecuteLinkNoAjax('{$eq}'), 'apps'); return false;\">Click Here</a>]";
            $box_email = AddBox_Type2('Email Us', $content, $this->Ico_Email);
        }
        
        if ($this->Show_FAQ) {
            $box_faq = $this->FAQs();
        }
        
        
        
        $output = "
        <div class='customer_profile_header_text'>HELP CENTER</div>
        <br /><br />
        <div>        
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
    
    
    public function FAQs()
    {
        $records = $this->SQL->GetArrayAll(array(
            'table' => $this->Table_Helpcenter_FAQs,
            'keys'  => '*',
            'where' => "`type_customer`=1 AND `active`=1",
        ));
        
        $tab_content_faqs = "<ul class='faqs'>";
        foreach ($records as $record) {
            $tab_content_faqs .= "
            <li>
                <a class='faq_q'>{$record['question']}</a>
                <p class='faq_a' style='display: none;'>{$record['answer']}</p>
            </li>";
        }
        $tab_content_faqs .= "</ul>";
        
        $title      = 'FREQUENTLY ASKED QUESTIONS';
        $content    = "<div id='helpcenter_faq'>$tab_content_faqs<div>";
        $output     = AddBox($title, $content);
        
        return $output;
    }

    
    
}  // -------------- END CLASS --------------