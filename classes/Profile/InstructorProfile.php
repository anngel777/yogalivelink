<?php
class Profile_InstructorProfile extends BaseClass
{
    public $Ajax_Page_Link              = '/office/AJAX/command_central/instructor_profile';
    
    public $URL_Purchases_View_Orders   = '/office/store/view_orders';

    public $load_all_records            = true; // If TRUE - will load ALL records
    public $Default_Tab                 = 0;
    
    public $num_actions_per_col         = 5;    // How many actions to show in each row - COMMON ACTIONS
    
    public $Show_Profile                = true; // Show the customer profile tab
    public $Show_Purchases              = true; // Show purchases made by customer tab
    public $Show_Sessions               = true; // Show booked session for cutomer tab
    public $Show_Touchpoints            = true; // Show all communications made with customer tab
    public $Show_Actions                = true; // Show common actions that can be done with this customer
    public $Show_Helpcenter             = true; // Show common actions that can be done with this customer
    public $Show_Session_Search         = true; // Show session search

    public $WH_ID                       = 0;
    public $table_sessions              = 'sessions';
    public $table_sessions_checklist    = 'session_checklists';
    public $table_instructor_profile    = 'instructor_profile';

    public $Loading_Image               = '/wo/images/upload.gif';
    public $Loading_Image_Html          = '<p><img src="/wo/images/upload.gif" width="32" height="32" border="0" alt="Loading..." /></p>';



    // ----------- styles --------------
    public $border_color                = "#D7D7D7";
    public $background_color_primary    = "#FFFFFF";
    public $background_color_secondary  = "#F5F5F5";
    public $header_color                = "#044577";
    public $highlite_color              = "#FC7E22";

    public $item_picture_width          = '160px';  // Viewing all products - width of product image
    public $item_picture_height         = '120px';  // Viewing all products - height of product image
    public $product_wrapper_width       = '160px';  // Viewing all products - width of holder (should be close to item_picture_width)
    public $product_wrapper_height      = '290px';  // Viewing all products - height of holder
    public $product_wrapper_padding     = '10px';   // Gap between products - note that actual gap with be twice this width

    public $category_wrapper_width       = '200px';  // Viewing all products - width of category holder
    public $category_wrapper_height      = '350px';  // Viewing all products - height of category holder --> NOT USED


    public $single_product_col_left_width      = '500px';   // Viewing single product - left column
    public $single_product_gap_width           = '50px';    // Viewing single product - gap between columns
    public $single_product_col_right_width     = '200px';   // Viewing single product - right column

    public $arrow_image_location        = "/office/images/arrow_dotted.gif";
    public $description_len_trunc       = 60; // How many characters to show before truncating description on general listing

    public $total_contents_width        = '950px';  // width of whole product-listing table
    public $categories_width            = '200px';  // width of categories area - needs to match or be larger than "category_wrapper_width"
    public $category_contents_gap       = '50px;';  // gap between categories and products
    public $products_width              = '700px';  // width of prodcuts area


    public $page_location               = '';
    public $product_detail_link         = '';

    public $colgap                      = '&nbsp;&nbsp;';

    public $category                    = '';
    public $where                       = '';
    public $breadcrumb                  = '';

    // ------ Transactions ------
    public $Transaction_Table           = 'store_transactions';
    public $Transaction_Items_Table     = 'store_transaction_items';

    public $OBJ_PURCHASES               = null;
    public $OBJ_SESSIONS                = null;
    public $OBJ_TOUCHPOINTS             = null;
    //public $OBJ_HELPCENTER              = null;
    public $OBJ_TABS                    = null;
    public $OBJ_OVERVIEW                = null;
    public $OBJ_SESSIONSEARCH           = null;
    
    
    
    public $Tabs_Div_Prefix             = 'instructor_profile_';
    public $Tabs_Function_Prefix        = 'InstructorProfile';
    public $Tab_Array                   = array();
    

    // ==================================== CONSTRUCT ====================================
    public function  __construct()
    {
        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Description' => 'Admin overview of an instructor in back-office - tabbed window',
        );
        
        $this->SetSQL();
        
        $default_Wh_Id = $_SESSION['USER_LOGIN']['LOGIN_RECORD']['wh_id'];
        
        $this->SetParameters(func_get_args());
        $this->WH_ID = ($this->GetParameter(0)) ? $this->GetParameter(0) : $default_Wh_Id;
        
        
        
        # DECODE THE WH ID - IF IT WAS PASSED IN LOOP - NECESSARY FOR ADMIN TO REVIEW A RECORD
        # ==========================================================================================
        if (Get('eq_whid')) {
            $QDATA          = GetEncryptQuery('eq_whid');
            $this->WH_ID    = ArrayValue($QDATA, 'wh_id');
        }
        
        
        
        // === INITIALIZE ALL CLASSES ===
        // === have to do this or we can't get scripts onto the pages in the right locations
        #$this->OBJ_PURCHASES        = new Store_ViewOrders();
        $this->OBJ_SESSIONS         = new Profile_InstructorProfileSessions();
        $this->OBJ_TOUCHPOINTS      = new Profile_CustomerProfileTouchpoints();
        //$this->OBJ_HELPCENTER       = new Profile_InstructorProfileHelpCenter();
        $this->OBJ_TABS             = new Tabs('tab', 'tab_edit');
        $this->OBJ_OVERVIEW         = new Profile_ProfileOverview($this->WH_ID);
        #$this->OBJ_SESSIONSEARCH    = new Sessions_Search();
        
        
        $this->OBJ_OVERVIEW->Is_Instructor                          = true;
        $this->OBJ_OVERVIEW->Force_Instructor_Account_Not_Limited   = true;
        $this->OBJ_OVERVIEW->Administrator_Editing_Record           = true;
        
        
        #$this->OBJ_PURCHASES->WH_ID     = $this->WH_ID;
        $this->OBJ_TOUCHPOINTS->WH_ID   = $this->WH_ID;
        //$this->OBJ_HELPCENTER->WH_ID    = $this->WH_ID;
        $this->OBJ_SESSIONS->WH_ID      = $this->WH_ID;
        
        
        $is_superuser = $_SESSION['USER_LOGIN']['LOGIN_RECORD']['super_user'];
        if ($is_superuser) {
            $this->Tab_Array = array(
                '1' => 'profile',
                '2' => 'common_actions',
                '3' => 'sessions',
                '4' => 'touchpoints',
            );
        } else {
            $this->Tab_Array = array(
                '1' => 'profile',
                '2' => 'session_search',
                '3' => 'sessions',
            );        
        }
        
    }

    public function SetSQL()
    {
        if (empty($this->SQL)) {
            $this->SQL = Lib_Singleton::GetInstance('Lib_Pdo');
        }
    }

    public function Execute()
    {
        if ($this->WH_ID == 0) {
            $class      = 'Profile_CustomerProfile';
            $list       = $this->CreateListingByAlphabet($class, 'customers');
            echo $list;
        } else {
        
            // === output all class scripts
            $this->OBJ_TOUCHPOINTS->AddScript();
            $this->OBJ_SESSIONS->AddScript();
            //$this->OBJ_HELPCENTER->AddScript();
            //$this->OBJ_SESSIONSEARCH->AddScript();
            $this->OBJ_OVERVIEW->AddScript();
            
            // === perform a css swap
            //$this->OBJ_PURCHASES->AddStyleSwap();
            
            // === add css styles
            AddStyle(".tabfolder{ background-color:#F6F0E1; }");
            
            // === Load tabs into window
            echo "<div style='width:800px;'></div>";
            $this->LoadTabs();
        }
    }
    
    
    public function GetCommonActions()
    {
        $common_actions = array();
        
        $common_actions[] = array(
            'title' => 'CANCEL ACCOUNT',
            'class' => 'Profile_CancelAccountInstructor',
            'vars'  => "v1={$this->WH_ID}",
        );
        
        $common_actions[] = array(
            'title' => 'RE-ACTIVATE ACCOUNT',
            'class' => 'Profile_ReactivateAccountInstructor',
            'vars'  => "v1={$this->WH_ID}",
        );
        
        $output = "";
        
        $temp_count = 0;
        foreach ($common_actions AS $action) {
            $output .= ($temp_count == 0) ? "<div class='col'>" : '';
        
            $link   = getClassExecuteLinkNoAjax(EncryptQuery("class={$action['class']};{$action['vars']}"));
            $script = "top.parent.appformCreateOverlay('{$action['title']}', '{$link}', 'apps'); return false;";
            $output .= "<div class='btn_actions'><a href='#' onclick=\"{$script}\">{$action['title']}</a></div>";
            
            $output .= ($temp_count == ($this->num_actions_per_col-1)) ? "</div><div class='col'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>" : '<br />';
            
            $temp_count++;
            $temp_count = ($temp_count == $this->num_actions_per_col) ? 0 : $temp_count;
        }
        
        $output .= (($temp_count != $this->num_actions_per_col) && ($temp_count !=0)) ? "</div>" : '';
        $output .= "<div class='clear'></div>";
        
        return $output;
    }

    public function SwitchTab($TABNUM)
    {
        $TABNUM = is_numeric($TABNUM) ? $TABNUM : "'$TABNUM'";
        $script = "setTab{$this->Tabs_Function_Prefix}($TABNUM, 'tab', 'tablink', 'tabselect');";
        AddScriptOnReady($script);
    }
    
    public function AjaxHandle()
    {
        $action = Get('action');
		switch ($action) {
			case 'customer_profile':
				$QDATA = GetEncryptQuery('eq');
				$WH_ID = ArrayValue($QDATA, 'wh_id');
				echo $this->OBJ_OVERVIEW->GetCustomerProfileContent($WH_ID);			
			break;
            
            case 'customer_email_subscriptions':
				$QDATA          = GetEncryptQuery('eq');
				$WH_ID          = ArrayValue($QDATA, 'wh_id');
                $INSTRUCTOR     = ArrayValue($QDATA, 'instructor');
				
                #echo "<br />INSTRUCTOR ===> $INSTRUCTOR";
                #$this->OBJ_OVERVIEW->Is_Instructor = $INSTRUCTOR;
                
                echo $this->OBJ_OVERVIEW->GetCustomerEmailSubscriptions($WH_ID, $INSTRUCTOR);
			break;
            
			default:
				$this->LoadTabContent($action);
			break;
		}
    }

    public function LoadTabContent($action)  // MVP function ------- used for ajax
    {
        $RESULT = '';
        switch ($action) {
            
            case 'profile':
                $RESULT = $this->OBJ_OVERVIEW->Execute();
            break;

            case 'sessions':
                $RESULT = $this->OBJ_SESSIONS->GetAllSessions($this->WH_ID); //ListTableText();
            break;

            case 'common_actions':
                $RESULT = $this->GetCommonActions();
            break;

            case 'touchpoints':
                $RESULT = $this->OBJ_TOUCHPOINTS->GetAllTouchpoints($this->WH_ID);
            break;
            
            case 'helpcenter':
                $RESULT = ''; //$this->OBJ_HELPCENTER->Execute();
            break;

        }
        if (empty($RESULT)) {
            $RESULT = 'Not Found';
        }
        echo $RESULT;
    }


    public function LoadTabs()
    {
        $ajax_page_link = $this->Ajax_Page_Link; //$GLOBALS['PAGE']['ajaxlink'];  // global from the $PAGE array
        
        $eq         = EncryptQuery("wh_id={$this->WH_ID}");
        $eq_wh_id   = "eq_whid={$eq}";
        
        
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
        function TestAlert(addedText) {
            alert('testing ==> ' + addedText);
        }

        var haveTabContents = ''; // variable to prevent loading after inital load
        function load{$this->Tabs_Function_Prefix}TabContent(name)
        {
            //if (haveTabContents.indexOf(name) < 0 ) {
                var id = '{$this->Tabs_Div_Prefix}' + name;
                var link = '$ajax_page_link' + ';action=' + name + ';$eq_wh_id';
                $('#' + id).load(link, function() {
                    //haveTabContents += ',' + name;
                    if (haveDialogTemplate) ResizeIframe();
                });
            //}
        }
        
        {$function_setTab}
        ";
        AddScript($script);

        
        

        
        # TAB SECTION
        # =========================================================
        $this->OBJ_TABS->Tab_Set_Function_Name = "setTab{$this->Tabs_Function_Prefix}";
        $tab_content_profile = $this->OBJ_OVERVIEW->Execute();
        $default_content = $tab_content_profile;
        
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



} //end class