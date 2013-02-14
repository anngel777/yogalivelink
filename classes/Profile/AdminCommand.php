<?php

/* ==========================================================================
    CLASS :: Profile_AdminCommand

    Used by administrators to manage the website - doing common tasks. This
    one class is made to replace having multiple icons on the desktop. Only
    use this class to instantiate other classes within a tabbed window.

    EXAMPLE ->
    Instead of having a reporting module - this class will instantiate
    the reporting class within its own tab.

# ========================================================================== */

class Profile_AdminCommand
{

    public $Loading_Image               = '/wo/images/upload.gif';
    public $Loading_Image_Html          = '<p><img src="/wo/images/upload.gif" width="32" height="32" border="0" alt="Loading..." /></p>';
    public $Default_Tab                 = 0;
    
    public $num_actions_per_col         = 5;    // How many actions to show in each row - COMMON ACTIONS
    public $load_all_records            = true; // If TRUE - will load ALL records

    public $show_actions                = true; // Show common actions that can be done
    public $show_customers              = true; // Show all customers
    public $show_instructors            = true; // Show all instructors
    public $show_administrators         = false; // Show all administrators
    public $show_test_procedures        = true; // Show test procedures
    public $show_session_analysis       = true; // Show analysis of sessions
    
    public $wh_id                       = 0;
    public $table_sessions              = 'sessions';
    public $table_sessions_checklist    = 'session_checklists';
    public $table_instructor_profile    = 'instructor_profile';


    public $arrow_image_location        = "/office/images/arrow_dotted.gif";
    public $description_len_trunc       = 60; // How many characters to show before truncating description on general listing


    public $page_location               = '';
    public $product_detail_link         = '';
    
    public $OBJ_TABS                    = null;
    public $OBJ_TESTING                 = null;
    public $OBJ_SESSION_ANALYSIS        = null;
    public $OBJ_BASE                    = null;

    // ==================================== CONSTRUCT ====================================
    public function  __construct()
    {
        $this->SetSQL();
        
        // === INITIALIZE ALL CLASSES ===
        // === have to do this or we can't get scripts onto the pages in the right locations
        
        $this->OBJ_BASE                           = new BaseClass();
    }

    public function SetSQL()
    {
        if (empty($this->SQL)) {
            $this->SQL = Lib_Singleton::GetInstance('Lib_Pdo');
        }
    }

    public function Execute()
    {
        $ajax_page_link = $GLOBALS['PAGE']['ajaxlink'];  // global from the $PAGE array
        
        
        $script = "
        
        var haveTabContents = ''; // variable to prevent loading after inital load
        function loadAdminCommandTabContent(name)
        {
            //if (haveTabContents.indexOf(name) < 0 ) {
                //alert('load content');
                var id = 'admin_command_' + name;
                var link = '$ajax_page_link' + ';action=' + name;
                $('#' + id).load(link, function() {
                    //haveTabContents += ',' + name;
                    if (haveDialogTemplate) ResizeIframe();
                });
            //}
        }

        
        ResizeIframe();
        ";
        AddScript($script);
        
        
        echo $this->GetCommonActions();
        
        
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
        $common_actions = array();
        
        $common_actions[] = array(
            'title' => 'SORT INSTRUCTOR PROFILES',
            'class' => 'InstructorProfile_Sort',
            'vars'  => '',
        );
        
        $common_actions[] = array(
            'title' => 'CREATE CUSTOMER ACCOUNT',
            'class' => 'Profile_CreateAccountCustomerAdministrator',
            'vars'  => '',
        );
        
        $common_actions[] = array(
            'title' => 'CREATE INSTRUCTOR ACCOUNT',
            'class' => 'Profile_CreateAccountInstructor',
            'vars'  => '',
        );
        
        $common_actions[] = array(
            'title' => 'DELETE CUSTOMER ACCOUNT',
            'class' => 'Profile_CancelAccountCustomer',
            'vars'  => '',
        );
        
        $common_actions[] = array(
            'title' => 'DELETE INSTRUCTOR ACCOUNT',
            'class' => 'Profile_CancelAccountInstructor',
            'vars'  => '',
        );
        
        $common_actions[] = array(
            'title' => 'GIVE FREE CREDITS',
            'class' => 'Profile_CustomerProfileFreeCredits',
            'vars'  => '',
        );
        
        $common_actions[] = array(
            'title' => 'CREATE SESSIONS FOR ALL INSTRUCTORS',
            'class' => 'Profile_InstructorAddSessions',
            'vars'  => '',
        );
        
        $common_actions[] = array(
            'title' => 'CREATE contacts RECORDS FOR ALL INSTRUCTORS',
            'class' => 'Profile_InstructorCreateContactFromProfile',
            'vars'  => '',
        );
        
        
        /*
        $common_actions[] = array(
            'title' => 'UPDATE SESSIONS WITH UTC TIMEZONES',
            'class' => 'Sessions_UpdateWithUTCDate',
            'vars'  => '',
        );
        */
        
        /*
        $common_actions[] = array(
            'title' => 'TEST SEND AN EMAIL MESSAGE',
            'class' => 'DevRichard_TestSendEmail',
            'vars'  => '',
        );
        */
        
        $common_actions[] = array(
            'title' => 'PHP_INFO()',
            'class' => 'General_PHPInfo',
            'vars'  => '',
        );
        
        /*
        $common_actions[] = array(
            'title' => 'Create Timezone Query',
            'class' => 'DevRichard_CreateTimezoneDatabaseQuery',
            'vars'  => '',
        );
        */
        
        $common_actions[] = array(
            'title' => 'Test Transaction - Use Credit',
            'class' => 'DevRichard_TestTransactionCreditUse',
            'vars'  => '',
        );
        
        $common_actions[] = array(
            'title' => 'Test Buy Credits',
            'class' => 'DevRichard_TestBuyCredits',
            'vars'  => '',
        );
        
        
        $output = "";
        
        $temp_count = 0;
        foreach ($common_actions AS $action) {
            $output .= ($temp_count == 0) ? "<div class='col'>" : '';
        
            $link   = getClassExecuteLinkNoAjax(EncryptQuery("class={$action['class']};{$action['vars']};pagetitle=tmp"));
            $script = "top.parent.appformCreate('Window', '{$link}', 'apps'); return false;";
            $output .= "<div class='btn_actions'><a href='#' onclick=\"{$script}\">{$action['title']}</a></div>";
            
            $output .= ($temp_count == ($this->num_actions_per_col-1)) ? "</div><div class='col'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>" : '<br />';
            
            $temp_count++;
            $temp_count = ($temp_count == $this->num_actions_per_col) ? 0 : $temp_count;
        }
        
        $output .= (($temp_count != $this->num_actions_per_col) && ($temp_count !=0)) ? "</div>" : '';
        $output .= "<div class='clear'></div>";


        return $output;
    }
    
    
    
} // END CLASS