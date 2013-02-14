<?php
class Website_Configuration extends BaseClass
{
    public $Page_Link       = '';
    public $SITE_CONFIG     = array();
    public $User_Logged_In  = null;
    
    public function  __construct()
    {
        parent::__construct();

        global $PAGE;
        //echo ArrayToStr($PAGE);
        $this->Page_Link = $PAGE['pagelink'];
        
        
        $this->Add_Submit_Name      = 'DISCOUNTS_SUBMIT_ADD';
        $this->Edit_Submit_Name     = 'DISCOUNTS_SUBMIT_EDIT';
        $this->Table                = 'website_configuration';
        $this->Flash_Field          = 'Website Configuration';
        $this->Index_Name           = 'website_configuration_id';
        
        $this->Field_Titles = array(
            'website_configuration_id'      => 'Id',
            'menu'                          => 'Menu',
            'menu_logged_out'               => 'Menu Logged ut',
            'menu_logged_in_customer'       => 'Menu Customer Logged In',
            
            'menu_instructor'               => 'Menu Instructor General',
            'menu_logged_in_instructor'     => 'Menu Instructor Logged In',
            
            'footer_links'                  => 'Footer Links',
            'footer_copyright'              => 'Footer Copyright',
            'footer_other'                  => 'Footer Other',
            'active'                        => 'Active',
        );

        $this->Default_Fields       = 'menu,menu_logged_in,menu_logged_out,footer_links,footer_copyright,footer_other';
        $this->Default_Values       = array ('display'=>1, 'sort_order'=>0);
        $this->Unique_Fields        = '';      
        $this->Autocomplete_Fields  = array();
        $this->Join_Array           = array();
        
    } // ---------- end construct -----------

    public function SetFormArrays() // overrides parent
    {
        $style_fieldset = "style='color:#990000; font-size:14px; font-weight:bold;'";
        $div_width = '500px';
        
        $base_array = array(
            "code|<div style='width:$div_width;'>",
            "fieldset|Content|options_fieldset|$style_fieldset",
                "html|Menu General|menu|N|20|4",
                "html|Menu Logged OUT|menu_logged_out|N|20|4",
                "html|Menu CUSTOMER Logged IN|menu_logged_in_customer|N|20|4",
                
                "code|<br />",
                "html|Menu INSTRUCTOR General (after log in)|menu_instructor|N|20|4",
                #"html|Menu INSTRUCTOR Logged OUT|menu_logged_out_instructor|N|20|4",
                "html|Menu INSTRUCTOR Logged IN|menu_logged_in_instructor|N|20|4",
                "code|<br />",
                
                "html|Footer Links|footer_links|N|20|4",
                "html|Footer Copyright|footer_copyright|N|20|4",
                "html|Footer Other|footer_other|N|20|4",
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
    
    
    public function GetSiteConfigSwaps()
    {
        foreach ($this->SITE_CONFIG AS $swap => $value) {
            AddSwap($swap, $value);
        }
    }
    
    public function GetUserLoggedInStatus()
    {
        $this->User_Logged_In = isset($_SESSION['USER_LOGIN']['LOGIN_RECORD']) ? true : false;
    }
    
    public function GetWebsiteConfiguration($ID=1)
    {
        global $PAGE;
        
        $record = $this->SQL->GetRecord(array(
            'table' => $this->Table,
            'keys'  => '*',
            'where' => "`$this->Index_Name`=$ID AND `active`=1",
        ));
        
        if ($record) {
            
            # HANDLE THE MENU
            # Title|Link
            # ==============================================
            $this->GetUserLoggedInStatus();
            
            $_SESSION['USER_TYPE'] = (Session('USER_TYPE')) ? Session('USER_TYPE') : 'unknown';
            
            switch ($_SESSION['USER_TYPE']) {
                case 'instructor':
                    $section = ($this->User_Logged_In) ? $record['menu_logged_in_instructor'] : $record['menu_logged_out'];
                break;
                
                case 'customer':
                default:
                    $section = ($this->User_Logged_In) ? $record['menu_logged_in_customer'] : $record['menu_logged_out'];
                break;
            }
            
            $output = '';
            if ($section) {
                $output = '<ul>';
                $lines = explode("\n", $section);
                foreach ($lines AS $line) {
                    if ($line) {
                        $parts      = explode("|", $line);
                        $title      = ($parts[0]) ? trim($parts[0]) : '';
                        $link       = ($parts[1]) ? trim($parts[1]) : '';
                        $class      = ($link == $PAGE['pagename']) ? 'active' : 'inactive';
                        
                        $output    .= "<li><a href='$link' class='$class'>$title</a></li>";
                    }
                }
                $output .= '</ul>';
            }
            $this->SITE_CONFIG['@@MENU_LOGGED@@'] = $output;
            
            
            # NORMAL MENU - always showing
            # ==============================================
            $section = ($_SESSION['USER_TYPE']=='instructor') ? $record['menu_instructor'] : $record['menu'];
            $output = '';
            if ($section) {
                $output = '<ul>';
                $lines = explode("\n", $section);
                foreach ($lines AS $line) {
                    if ($line) {
                        $parts      = explode("|", $line);
                        $title      = ($parts[0]) ? trim($parts[0]) : '';
                        $link       = ($parts[1]) ? trim($parts[1]) : '';
                        $class      = ($link == $PAGE['pagename']) ? 'active' : 'inactive';
                        
                        $output    .= "<li><a href='$link' class='$class'>$title</a></li>";
                    }
                }
                $output .= '</ul>';
            }
            $this->SITE_CONFIG['@@MENU@@'] = $output;
            
            
            # HANDLE THE FOOTER LINKS
            # Title|Link
            # ==============================================
            $section = $record['footer_links'];
            
            $output = '';
            if ($section) {
                $output = '<ul>';
                $lines = explode("\n", $section);
                foreach ($lines AS $line) {
                    if ($line) {
                        $parts      = explode("|", $line);
                        $title      = ($parts[0]) ? $parts[0] : '';
                        $link       = ($parts[1]) ? $parts[1] : '';
                        $output    .= "<li><a href='$link' class='footer_link'>$title</a></li>";
                    }
                }
                $output .= '</ul>';
            }
            $this->SITE_CONFIG['@@FOOTER_LINKS@@'] = $output;
            
            
            
            # HANDLE THE FOOTER OTHER ITEMS
            # Title|Link
            # ==============================================
            $this->SITE_CONFIG['@@FOOTER_COPYRIGHT@@']      = ($record['footer_copyright']) ? $record['footer_copyright'] : '';
            $this->SITE_CONFIG['@@FOOTER_OTHER@@']          = ($record['footer_other']) ? $record['footer_other'] : '';
            
            
        } else {
            $output = "NO SITE CONFIGURATION RECORD FOUND";
        }
        
        return $output;
    }
    
    
    

} // END CLASS