<?php
class Website_IndexBoxes extends BaseClass
{
    public $Page_Link           = '';
    public $Img_Not_Found       = '/images/template/image_not_found.jpg';
    public $Img_Width           = 175;
    public $Img_Height          = 101;
    
    public function  __construct()
    {
        parent::__construct();

        global $PAGE;
        //echo ArrayToStr($PAGE);
        $this->Page_Link = $PAGE['pagelink'];
        
        
        $this->Add_Submit_Name      = 'DISCOUNTS_SUBMIT_ADD';
        $this->Edit_Submit_Name     = 'DISCOUNTS_SUBMIT_EDIT';
        $this->Table                = 'website_index_boxes';
        $this->Flash_Field          = 'Website Index Boxes';
        $this->Index_Name           = 'website_index_boxes_id';
        
        $this->Field_Titles = array(
            'website_index_boxes_id'       => 'Id',
            'title'                 => 'Title',
            'content'                => 'Content',
            'link'                => 'Link',
            'image'               => 'Image',
            'display'               => 'Display on Website',
            'active'                => 'Active'
        );

        $this->Default_Fields       = 'title,content,link,image,display';
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
            #"fieldset|Content|options_fieldset|$style_fieldset",
                "text|Title|title|N|20|255",
                "html|Content|content|N|20|6",
                "text|Link|link|N|20|255",
                "text|Image|image|N|20|255",
                "checkbox|Display on Website|display||1|0",
                #"datetime|Show Date|show_date|N|2010||",
                #"datetime|Hide Date|hide_date|N|2010||",
                "info||Order of appearance on website. <br />0 = no order and appears at start of ordered list. <br />99 = no order and appears at end of ordered list.",
                "text|Order|sort_order|N|3|2",
            #"endfieldset",
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
    
    
    
    public function GetIndexBoxes()
    {
        $records = $this->SQL->GetArrayAll(array(
            'table' => $this->Table,
            'keys'  => '*',
            'where' => "`display`=1 AND `active`=1",
            'order' => '`sort_order` ASC',
        ));
        
        if ($records) {
            
            $output = '<div>';
            foreach ($records AS $record) {
                $content = str_replace("\n", "<br />", $record['content']);
                $content .= ($record['link']) ? " <span class=\"index_box_link\"><a href=\"{$record['link']}\">Read more...</a></span>" : '';
                
                $image = ($record['image']) ? "<img src='{$record['image']}' alt='' border='0' width='{$this->Img_Width}' height='{$this->Img_Height}' />" : "<img src='{$this->Img_Not_Found}' alt='' border='0' width='{$this->Img_Width}' height='{$this->Img_Height}' />";
                
                $output .= "
                    <div class=\"index_box_holder\">
                        <div class=\"index_box_header\">{$record['title']}</div>
                        <div class=\"index_box_image\">{$image}</div>
                        <div class=\"index_box_content\">{$content}</div>
                    </div>
                    <div class=\"index_box_gapper\">&nbsp;</div>
                ";
            }
            $output .= '</div><div class="clear"></div>';
            
        } else {
            $output = "NO RECORDS FOUND";
        }
        
        return $output;
    }
    
    
    public function GetIndexBoxesAsTable()
    {
        $records = $this->SQL->GetArrayAll(array(
            'table' => $this->Table,
            'keys'  => '*',
            'where' => "`display`=1 AND `active`=1",
            'order' => '`sort_order` ASC',
        ));
        
        if ($records) {
            
            $output     = '<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr>';
            $count      = 0;
            $maxcount   = count($records);
            
            
            
            foreach ($records AS $record) {
                $content = str_replace("\n", "<br />", $record['content']);
                $content .= ($record['link']) ? " <span class=\"index_box_link\"><a href=\"{$record['link']}\">Read more...</a></span>" : '';
                
                $image = ($record['image']) ? "<img src='{$record['image']}' alt='' border='0' width='{$this->Img_Width}' height='{$this->Img_Height}' />" : "<img src='{$this->Img_Not_Found}' alt='' border='0' width='{$this->Img_Width}' height='{$this->Img_Height}' />";
                $image = ($record['link']) ? "<a href=\"{$record['link']}\">{$image}</a>" : $image;
                
                $count++;
                $align = 'center';
                $align = ($count == 1) ? 'left' : $align;
                $align = ($count == $maxcount) ? 'right' : $align;
                
                $title = ($record['link']) ? "<a class='index_box_header' href='{$record['link']}'>{$record['title']}</a>" : $record['title'];
                
                $output .= "
                    <td align='{$align}' valign='top'>
                    <div class=\"index_box_holder\">
                        <div class=\"index_box_header\">{$title}</div>
                        <div class=\"index_box_image\">{$image}</div>
                        <div class=\"index_box_content\">{$content}</div>
                    </div>
                    </td>
                ";
            }
            $output .= '</tr></table>';
            
        } else {
            $output = "NO RECORDS FOUND";
        }
        
        return $output;
    }
    
    
    public function ProcessTableCell($field, &$value, &$td_options, $id='')
    {
        parent::ProcessTableCell($field, $value, $td_options, $id);

        switch ($field) {
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

} // END CLASS