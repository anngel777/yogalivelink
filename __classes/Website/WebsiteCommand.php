<?php
class Website_WebsiteCommand
{
    public $Loading_Image               = '/wo/images/upload.gif';
    public $Loading_Image_Html          = '<p><img src="/wo/images/upload.gif" width="32" height="32" border="0" alt="Loading..." /></p>';
    public $Default_Tab                 = 0;
    public $num_actions_per_col         = 5;    // How many actions to show in each row - COMMON ACTIONS
    public $wh_id                       = 0;
    public $page_location               = '';
    public $product_detail_link         = '';
    
    public $OBJ_TABS                    = null;
    public $OBJ_ARTICLES                = null;
    public $OBJ_CONFIGURATION           = null;
    public $OBJ_INDEXBOXES              = null;
    public $OBJ_PAGECONTENTS            = null;
    public $OBJ_HELPCENTER_FAQ          = null;
    
    
    public $Tabs_Div_Prefix             = 'website_command_';
    public $Tabs_Function_Prefix        = 'WebsiteCommand';
    public $Tab_Array                   = array();
    
    // ==================================== CONSTRUCT ====================================
    public function  __construct()
    {
        $this->SetSQL();
        
        // === INITIALIZE ALL CLASSES ===
        // === have to do this or we can't get scripts onto the pages in the right locations
        $this->OBJ_TABS                         = new Tabs('tab', 'tab_edit');
        $this->OBJ_ARTICLES                     = new Website_Articles();
        $this->OBJ_CONFIGURATION                = new Website_Configuration();
        $this->OBJ_INDEXBOXES                   = new Website_IndexBoxes();
        $this->OBJ_PAGECONTENTS                 = new Website_PageContents();
        $this->OBJ_HELPCENTER_FAQ               = new Website_HelpcenterFAQs();
        
        $this->Tab_Array = array(
            '1' => 'common_actions',
            '2' => 'configuration',
            '3' => 'articles',
            '4' => 'index_boxes',
            '5' => 'page_contents',
            '6' => 'helpcenter_faqs',
        );
    }
    
        
    public function SetSQL()
    {
        if (empty($this->SQL)) {
            $this->SQL = Lib_Singleton::GetInstance('Lib_Pdo');
        }
    }

    public function Execute()
    {
        // === output all class scripts
        // i.e. --> $this->OBJ_TOUCHPOINTS->AddScript();
        
        // === Load tabs into window
        echo "<div style='width:800px;'></div>";
        $this->LoadTabs();
    }
    
    public function SwitchTab($TABNUM)
    {
        $TABNUM = is_numeric($TABNUM) ? $TABNUM : "'$TABNUM'";
        $script = "setTabWebsiteCommand($TABNUM, 'tab', 'tablink', 'tabselect');";
        AddScriptOnReady($script);
    }
    
    public function AjaxHandle()
    {
        $action = Get('action');
        if ($action) {
            $this->LoadTabContent($action);
        }
    }

    public function GetCommonActions()
    {
    /*
        $common_actions = array();
        
        $common_actions[] = array(
            'title' => 'HOLDING',
            'class' => '',
            'vars'  => '',
        );
        
        
        
        $output = "";
        
        $temp_count = 0;
        foreach ($common_actions AS $action) {
            $output .= ($temp_count == 0) ? "<div class='col'>" : '';
        
            $link   = getClassExecuteLinkNoAjax(EncryptQuery("class={$action['class']};{$action['vars']}"));
            $script = "top.parent.appformCreate('Window', '{$link}', 'apps'); return false;";
            $output .= "<div class='btn_actions'><a href='#' onclick=\"{$script}\">{$action['title']}</a></div>";
            
            $output .= ($temp_count == ($this->num_actions_per_col-1)) ? "</div><div class='col'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>" : '<br />';
            
            $temp_count++;
            $temp_count = ($temp_count == $this->num_actions_per_col) ? 0 : $temp_count;
        }
        
        $output .= (($temp_count != $this->num_actions_per_col) && ($temp_count !=0)) ? "</div>" : '';
        $output .= "<div class='clear'></div>";

    */
        $output = '';
        return $output;
    }

    public function LoadTabContent($action)  // MVP function ------- used for ajax
    {
        $RESULT = '';
        switch ($action) {
            case 'configuration':
                $RESULT = $this->OBJ_CONFIGURATION->ListTableText();
            break;
            
            case 'articles':
                $RESULT = $this->OBJ_ARTICLES->ListTableText();
            break;
            
            case 'index_boxes':
                $RESULT = $this->OBJ_INDEXBOXES->ListTableText();
            break;
            
            case 'page_contents':
                $RESULT = $this->OBJ_PAGECONTENTS->ListTableText();
            break;
            
            case 'helpcenter_faqs':
                $RESULT = $this->OBJ_HELPCENTER_FAQ->ListTableText();
            break;
        }
        if (empty($RESULT)) {
            $RESULT = 'Not Found';
        }
        echo $RESULT;
    }
    
    
    public function LoadTabs()
    {
        $ajax_page_link = $GLOBALS['PAGE']['ajaxlink'];  // global from the $PAGE array
        
        
        $temp_script_1 = '';
        $temp_script_2 = '';
        foreach ($this->Tab_Array AS $number => $value) {
            $temp_script_1 .= "
            case '{$value}':
                newNum = {$number};
                break;
            ";
            
            $temp_script_2 .= "
            case {$number}:
                load{$this->Tabs_Function_Prefix}TabContent('{$value}');
                break;
            ";
        }
        
        
        $function_setTab = "
            function setTab{$this->Tabs_Function_Prefix}(num, group, tablink, tabselect) 
            {
                if (typeof(num)!='number') {
                    var newNum = 0;
                    switch(num) {
                        {$temp_script_1}
                    }
                    num = newNum;
                }
                
                var linkname = group + 'link';
                hideGroupExcept(group, num);
                setClassGroup(linkname, num, tablink, tabselect);
                
                switch(num) {
                    {$temp_script_2}
                }
                
                if (haveDialogTemplate) ResizeIframe();
                return false;
            }
        ";
        
        
        
        
        $script = "
        var haveTabContents = ''; // variable to prevent loading after inital load
        function load{$this->Tabs_Function_Prefix}TabContent(name)
        {
            if (haveTabContents.indexOf(name) < 0 ) {
                var id = '{$this->Tabs_Div_Prefix}' + name;
                var link = '$ajax_page_link' + ';action=' + name;
                $('#' + id).load(link, function() {
                    haveTabContents += ',' + name;
                    if (haveDialogTemplate) ResizeIframe();
                });
            }
        }
        
        {$function_setTab}
        ";
        AddScript($script);

        
        

        
        # TAB SECTION
        # =========================================================
        $this->OBJ_TABS->Tab_Set_Function_Name = "setTab{$this->Tabs_Function_Prefix}";
        #$tab_content_profile = $this->OBJ_OVERVIEW->Execute();
        $default_content = '';
        
        
        foreach ($this->Tab_Array AS $number => $value) {
            $title      = ucwords(strtolower(str_replace('_', ' ', $value)));
            $div        = $this->Tabs_Div_Prefix . $value;
            $id         = "id=\"{$div}\"";
            $class      = ($number == 1) ? 'class=\"tab_content_wrapper\"' : '';
            $content    = ($number == 1) ? $default_content : $this->Loading_Image_Html;
            
            $this->OBJ_TABS->AddTab($title, "<div $id $class>{$content}</div>");
        }
        
        
        $tab_content = $this->OBJ_TABS->OutputTabs(true);
        echo $tab_content;
        
        
        if ($this->Default_Tab != '') {
            $this->SwitchTab($this->Default_Tab);
        }
    }
    
    
    
    
} // END CLASS