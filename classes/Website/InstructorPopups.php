<?php
class Website_InstructorPopups extends BaseClass
{
    public $Show_Query                  = false;    // TRUE = output the database queries ocurring on this page
    public $Show_Array                  = false;    // TRUE = show array of info pulled from database call
    
    public $Page_Preview_Location       = "/office/website_page_preview";
    public $Page_Link                   = '';
    public $Page_Name                   = '';
    
    public $Notice_Area_Title           = "SPECIAL NOTICES FROM YOGALIVELINK.COM";      // Title for top of notice area
    public $Notice_Area_Link            = "VIEW DETAILS";                               // Text for all notice links
    
    public $Create_Popup                = true; // TRUE = create  aauto-popup for the first instructor notice
    
    public function  __construct()
    {
        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2012-03-27',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Description' => 'Manage Instructor profile page popup content',
        );
        
        global $PAGE;
        $this->Page_Link = $PAGE['pagelink'];
        
        
        $this->Add_Submit_Name      = 'CONTENT_SUBMIT_ADD';
        $this->Edit_Submit_Name     = 'CONTENT_SUBMIT_EDIT';
        $this->Table                = 'website_instructor_popups';
        $this->Flash_Field          = 'Instructor Popup Contents';
        $this->Index_Name           = 'website_instructor_popups_id';
        
        $this->Field_Titles = array(
            'website_instructor_popups_id'  => 'Id',
            'item_number'               => 'Item Number',
            
            'title'                     => 'Title',
            'description'               => 'Description',
            'content'                   => 'Content',
            
            'sort_order'                => 'Sort Order',
            'comments'                  => 'Comments',
            'display'                   => 'Show On Website',
            'active'                    => 'Active'
        );

        $this->Default_Fields       = 'title,description,content,comments,display';
        $this->Default_Values       = array ('display'=>1, 'sort_order'=>0);
        $this->Unique_Fields        = '';      
        $this->Autocomplete_Fields  = array();
        $this->Join_Array           = array();
        
    } // ---------- end construct -----------

    public function SetFormArrays() // overrides parent
    {
        global $FormPrefix;
        
        $this->AddScript();
        
        $style_fieldset     = "style='color:#990000; font-size:14px; font-weight:bold;'";
        $div_width          = '500px';
        
        $base_array = array(
            "code|<div style='width:$div_width;'>",
                
                
                'text|Title|title|N|60|255',
                'textarea|Description|description|N|60|4',
                
                'html|Full Content|content|N|60|4',
                "button|Preview|previewPopUp('{$FormPrefix}content');",
                
                
                'checkbox|Display on Website|display||1|0',
                "info||Order of appearance on website. <br />0 = no order and appears at start of ordered list. <br />99 = no order and appears at end of ordered list.",
                "text|Order|sort_order|N|3|2",
                
                "fieldset|Content|options_fieldset|$style_fieldset",
                    'checkbox|Bypass Text Processing|bypass_text_processing||1|0',
                    
                    #'text|Item Number|item_number|N|3|3',
                    #'text|Languages Id|languages_id|N|11|11',
                    'textarea|Comments|comments|N|60|4',
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
    
    
    public function GetContentFromId($ID='')
    {
        # FUNCTION :: Get page contents from the database when being called from another class - searches in identifier
        
        
        // GET PAGE CONTENTS
        // ======================================================
        $record = $this->SQL->GetRecord(array(
            'table' => $this->Table,
            'keys'  => 'content, bypass_text_processing',
            'where' => "`active`=1 AND `{$this->Index_Name}`='{$ID}'",
        ));
        if ($this->Show_Query) echo '<br />' . $this->SQL->Db_Last_Query;
        
        $content = $record['content'];
        
        if (!$record['bypass_text_processing']) {
            $content        = str_replace(array("\r\n", "\n", "\r"), '<br />', $content);	// Change new lines to line breaks
        }
        
        return $content;
    }
    
    public function MakePopup($records)
    {
        # FUNCTION :: Get the popup link and script output to the page
        
        $output = '';
        
        if ($records) {
        
        // create the link for popup
        $link = "https://www.yogalivelink.com/office/website/instructor_popup;id={$records[0]['website_instructor_popups_id']}";
        $output = "<a id='logo_overlay_trigger' class='iframe' href='{$link};template=blank;type=popup'>&nbsp;</a>";
        
        
        // reset the session var so popup can show again
        if (Get('reset')) {
            unset($_SESSION['HIDE_INFO_POPUP']);
        }
        
        // output the script for making the popup
        if (!Session('HIDE_INFO_POPUP')) {
            
            $ENABLE_POPUP = true;
            $_SESSION['HIDE_INFO_POPUP'] = true;
            
            if ($ENABLE_POPUP) {
                
                AddStylesheet("/css/jquery.fancybox-1.3.4.css");
                AddScriptInclude("/jslib/jquery.easing-1.3.pack.js");
                AddScriptInclude("/jslib/jquery.fancybox-1.3.4.pack.js");
                
                $script = <<<SCRIPT
                    var overlayContact = function(){
                        $("#logo_overlay_trigger").trigger('click');
                    };
                    setTimeout(overlayContact, 50); //run the script immediately
                    
                    $("#logo_overlay_trigger").fancybox({
                        'autoScale'         : true,
                        'autoDimensions'    : false,
                        'centerOnScroll'    : true,
                        'title'             : '',
                        'width'             : 650,
                        'height'            : 500
                    });
SCRIPT;
                addScriptOnReady($script);
            }
        } // end session check
        } // end check for records
        
        return $output;
    }
    
    
    public function GetContents()
    {
        # FUNCTION :: Get all page contents from the database and add swaps to the page
        
        $content = '';
        
        // GET PAGE CONTENTS
        // ======================================================
        $records = $this->SQL->GetArrayAll(array(
            'table' => $this->Table,
            'keys'  => '*',
            'where' => "`display`=1 AND `active`=1",
            'order' => '`sort_order` ASC',
        ));
        if ($this->Show_Query) echo '<br />' . $this->SQL->Db_Last_Query;
        if ($this->Show_Array) echo ArrayToStr($records);
        
        // output the popup content
        if ($records && $this->Create_Popup) {
            $content .= $this->MakePopup($records);
        }
        
        // output the main content
        $content .= $this->ProcessContents($records);
        
        return $content;
    }
    
    public function ProcessContents($records)
    {
        $output = '';
        $output_top = "
                <div class='yogabox_box_footer'>
                <center>
                <div class='red left_header lowercase'>{$this->Notice_Area_Title}</div>
                </center>
            ";
        $output_bottom = "</div>";
        
        $temp_swap_array = array();
        if ($records) {
            foreach ($records AS $record) {
                $output .= "<div style='padding:10px;'>
                            <div style='padding:10px; background-color:#FBF8EE;'>
                                <div style='font-size:16px; color:#000; font-weight:bold;'>{$record['title']}</div><br />
                                <div style='font-size:14px; color:#000;'>{$record['description']}</div><br />
                                <a href='/office/website/instructor_popup;id={$record['website_instructor_popups_id']}' class='link_arrow'>{$this->Notice_Area_Link}</a>
                            </div>    
                            </div>";
            }
        }
        
        $output = ($output) ? $output_top . $output . $output_bottom : '';
        
        return $output;
    }

    
    
    public function AddScript()
    {
        $script = <<<SCRIPT
            function previewPopUp(content)
            {
                var location = '{$this->Page_Preview_Location};source_dialog_id=' + dialogNumber + ';content=' + content;
                top.parent.appformCreate('Content Preview', location, 'apps');
            }
SCRIPT;
        AddScript($script);
    }
    
    
    
    
    public function ProcessTableCell($field, &$value, &$td_options, $id='')
    {
        parent::ProcessTableCell($field, $value, $td_options, $id);

        switch ($field) {
            case 'description':
            case 'content':
                $value = TruncStr(strip_tags($value), 200);
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

} // END CLASS