<?php
class Website_Instructors extends BaseClass
{
    public $Show_Query              = false;    // TRUE = output the database queries ocurring on this page
    public $Use_Seo_Urls            = true;     // TRUE = use SEO friendly urls
    public $Use_Encrypted_Query     = true;     // TRUE = encrypt the query in the URL
    
    public $Instructor_Logo         = "/images/template/instructor_logo.jpg";
    public $Instructor_Logo_Legend  = "/images/template/instructor_logo_legend.jpg";
    public $Instructor_Logo_Gap     = "/images/spacer.gif";

    public $Page_Link               = '';
    public $Articles_Records        = null;
    
    public $Btn_ScheduleSession_Class       = "button_simple_red";              // button class
    public $Btn_ScheduleSession_Text        = "Schedule a session!";            // button text
    
    public $Instructor_Logo_Width           = "100";
    public $Instructor_Logo_Height          = "150";
    public $Link_BackToInstructors_Text     = "Back To Yoga Instructors";               // link text
    public $Link_ViewSchedule_Text          = "View Schedule...";                       // link text
    public $Link_ViewProfile_Text           = "Read more...";                           // link text
    public $Error_LoadInstructor_Text       = "UNABLE TO LOAD INSTRUCTOR PROFILE";      // error message
    public $Error_LoadInstructorsAll_Text   = "NO RECORDS FOUND";                       // error message
    public $Text_SearchByStyle              = "search by yoga style";                   // content
    public $Text_SearchingBy                = "Searching Instructors By Yoga Type:";    // content - in javascript
    public $Text_InstructorSignup           = "want to join our team?";                 // content
    public $Link_InstructorSignup_Link      = "/signup_instructor";                     // link - actual link location
    public $Link_InstructorSignup_Text      = "Apply Now";                              // link text
        
    public function  __construct()
    {
        parent::__construct();      
        
        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => 'RAW',
            'Updated'     => '2012-06-12',
            'Version'     => '1.0',
            'Description' => 'Output instructors on the instructors page on website',
        );
        
        /* UPDATE LOG ======================================================================================
        
            2012-02-07  -> added back $this->Table so queries would function
            2012-05-30  -> modified classes from "article" to "instructor". 
                        -> shifted text and classes into public variables
                        -> moved CSS from inline to stylesheet
            2012-06-12  => modified left menu column for new template design
            
        ====================================================================================== */
        
        $this->Table = 'instructor_profile';
        
        global $PAGE;
        $this->Page_Link = $PAGE['pagelink'];
        
    } // ---------- end construct -----------
    
    
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
        # FUNCTION :: Get a single isntructor and format output for the screen
        
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
            $Instructor_Logo_Gap    = "<div style='width:{$this->Instructor_Logo_Width}px; height:{$this->Instructor_Logo_Height}px; border:1px solid red;'>&nbsp;</div>";
            $Instructor_Picture     = ($record['primary_pictures_id'] != '') ? "<img src='/office/{$record['primary_pictures_id']}' alt='' border='0' width='{$GLOBALS['INSTRUCTOR_PICTURE_WIDTH_LARGER']}' height='{$GLOBALS['INSTRUCTOR_PICTURE_HEIGHT_LARGER']}' />" : $Instructor_Logo_Gap;
            $back_link              = "<h3><a href='{$this->Page_Link}' class='link_arrow'>{$this->Link_BackToInstructors_Text}</a></h3>";
            $link_schedule          = "{$GLOBALS['LINK_SESSION_SIGNUP_INSTRUCTOR']};instructor_whid={$record['wh_id']};retpage=instructors";
            
            
            
            $output .= "
                <div class='instructor_backlink_top_wrapper'>{$back_link}<br /></div>
                <br /><br />
                <div class='instructor_picture_col' style='width:{$GLOBALS['INSTRUCTOR_PICTURE_WIDTH_LARGER']}px;'>{$Instructor_Picture}</div>
                <div class='instructor_content_col'>
                    <div class=\"instructor_title\">{$name}</div>
                    <div class=\"instructor_p\">{$record['profile']}</div>
                    <br />
                    <div class=\"instructor_all_link\"><a href='{$link_schedule}' class='link_arrow'>{$this->Link_ViewSchedule_Text}</a></div>
                </div>
                <div class='clear'></div>
                <br /><br />
                <div class='instructor_backlink_bottom_wrapper'>{$back_link}</div>
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
            $output .= $this->Error_LoadInstructor_Text;
        }
        
        return $output;
    }
    
    
    public function GetAllInstructors($QUERY='')
    {
        # FUNCTION :: Get all instructors from database 
        
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
            
            
            $output .= "<div class=\"instructors_holder\">";
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
                        <div class='instructor_all_picture_col'><a href='{$link}'>{$Instructor_Picture}</a></div>
                        <div class='instructor_all_content_col'>
                            <div class=\"instructor_all_title\">{$name}</div>
                            <br />
                            <div class=\"instructor_all_content\">{$record['profile']}</div>
                            <br />
                            <div class=\"instructor_all_link\"><a href='{$link}' class='link_arrow'>{$this->Link_ViewProfile_Text}</a></div>
                            <div class=\"instructor_all_link\"><a href='{$link_schedule}' class='link_arrow'>{$this->Link_ViewSchedule_Text}</a></div>
                        </div>
                        <div class='clear'></div>
                    </div>
                    ";
            }
            
            $output .= "</div>";
            $output .= "</div>";
        
        
        } else {
            $output .= $this->Error_LoadInstructorsAll_Text;
        }
        
        return $output;
    }
    
    
    public function GetInstructorMenu()
    {
        # FUNCTION :: Create menu for instructors page
        
        //$btn_session        = "<a href='{$GLOBALS['LINK_SESSION_SIGNUP']}'><div class='btn_scheduleASession'>&nbsp;</div></a>";
        //$btn_session        = "<a href='{$GLOBALS['LINK_SESSION_SIGNUP']}'><div class='{$this->Btn_ScheduleSession_Class}'>{$this->Btn_ScheduleSession_Text}</div></a>";
        
        //if (Get('template') == 'new_inner') {
        $btn_session        = "<center><a href='{$GLOBALS['LINK_SESSION_SIGNUP']}'><img src='/images/btn_get_started_2.png' height='40'></a></center>";
        //}
        
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
            <center>
            <br />
            <div class='instructor_category_title'>{$this->Text_SearchByStyle}</div>
            <div style='padding-top:5px;'></div>
            <div class='left_content'>{$options_form}</div>
            </center>
            <br />
            <hr style='color:#BEC685; background-color:#BEC685; height:1px; border:0px;'>
            <br /><br />
            {$btn_session}
            <br /><br />
            <div class='orange left_header'>{$this->Text_InstructorSignup}</div>
            <div class='left_content'>@@INSTRUCTOR_BECOME_INFO@@ <a class='link_arrow' href='{$this->Link_InstructorSignup_Link}'>{$this->Link_InstructorSignup_Text}</a></div>
            <br />
        ";
        
        
        return $output;
    }
    
    public function AddScript()
    {
        $script = "
            $('#FORM_yoga_types').change(function(){
                ChangeInstructorsByYogaType();
            });";
            
            if(isset($_GET['style']) && $_GET['style'] == "therapy"){
                $script .= "
                $('#FORM_yoga_types').val('Yoga Therapy');
                ChangeInstructorsByYogaType();
                ";
            } else {
                $script .= "
                $('#FORM_yoga_types').val('All');
                ChangeInstructorsByYogaType();
                ";
            }
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
                    var tString = '<h1>{$this->Text_SearchingBy} '+tClass+'</h1>';
                }
                $('#search_current_instructor_list_notice').html(tString);
                
            }
            ";
                        
        AddScript($script);
    }
    
    public function myTruncate($string, $limit, $break=".", $pad="...") 
    {
        # FUNCTION :: Truncate the article for display
    
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