<?php
class DevRichard_BlogEdit extends BaseClass
{
    public $ShowArray               = false;
    private $table_blog             = 'website_blog ';
    public $client_id               = 0;
    
    
    public function  __construct()
    {
        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => '',
            'Description' => 'Create and manage touchpoint_chats',
            'Created'     => '2010-09-22',
            'Updated'     => '2010-09-22'
        );

        $this->Table                = 'website_blog';
        $this->Add_Submit_Name      = 'TOUCHPOINT_CHATS_SUBMIT_ADD';
        $this->Edit_Submit_Name     = 'TOUCHPOINT_CHATS_SUBMIT_EDIT';
        $this->Index_Name           = 'website_blog_id';
        $this->Flash_Field          = 'website_blog_id';
        $this->Default_Where        = '';  // additional search conditions
        $this->Default_Sort         = 'website_blog_id';  // field for default table sort
        $this->Default_Fields       = 'website_blog_id,client_wh_id,datetime,title,text,links_list,pictures_list';
        $this->Unique_Fields        = '';

        $this->Field_Titles = array(
            'website_blog_id'   => 'Blog ID',
            'client_wh_id'      => 'Client WHID',
            'datetime'          => 'Date Time',
            'title'             => 'Title',
            'text'              => 'Text',
            'links_list'        => 'Links',
            'pictures_list'     => 'Pictures',
            'active'            => 'Active',
            'updated'           => 'Updated',
            'created'           => 'Created'
        );
        
        $this->Default_Values       = array(
            'client_wh_id' => 666,
            'datetime' => date('Y-m-d H:i:s'),
        );
    } // -------------- END __construct --------------

     
    public function SetFormArrays()
    {
        $tmp = Get('DIALOGID');
        $photo_link_1 = "<a href='#' onclick=\"top.appformCreate('Photo Upload', 'image_upload_crop;upload_dir=images/client_{$this->client_id}/blog;ret_diag={$tmp};ret_field=FORM_pictures_list_1','apps'); return false;\">ADD/EDIT PHOTO</a>";
        $photo_link_2 = "<a href='#' onclick=\"top.appformCreate('Photo Upload', 'image_upload_crop;upload_dir=images/client_{$this->client_id}/blog;ret_diag={$tmp};ret_field=FORM_pictures_list_2','apps'); return false;\">ADD/EDIT PHOTO</a>";
        $photo_link_3 = "<a href='#' onclick=\"top.appformCreate('Photo Upload', 'image_upload_crop;upload_dir=images/client_{$this->client_id}/blog;ret_diag={$tmp};ret_field=FORM_pictures_list_3','apps'); return false;\">ADD/EDIT PHOTO</a>";

        $base_array = array(
            "form|$this->Action_Link|post|db_edit_form",
            'text|Client Wh Id|client_wh_id|N|11|11',
            'text|Datetime|datetime|N|20|255',
            'textarea|Title|title|N|60|4',
            'text|Text|text|N|60|255',
            
            'code|<br /><br />',
            
            "textarea|Link 1|links_list_1|N|45|1",
            "textarea|Link 1|links_list_2|N|45|1",
            "textarea|Link 1|links_list_3|N|45|1",
            
            'code|<br /><br />',
            
            "text|Picture 1|pictures_list_1|N|45|255||||$photo_link_1",
            "text|Picture 2|pictures_list_2|N|45|255||||$photo_link_2",
            "text|Picture 3|pictures_list_3|N|45|255||||$photo_link_3",
        );

        if ($this->Action == 'ADD') {
            $base_array[] = "submit|Add Record|$this->Add_Submit_Name";
            $base_array[] = 'endform';
            $this->Form_Data_Array_Add = $base_array;
        } else {
            $base_array[] = 'checkbox|Active|active||1|0';
            $base_array[] = "submit|Update Record|$this->Edit_Submit_Name";
            $base_array[] = 'endform';
            $this->Form_Data_Array_Edit = $base_array;
        }
    }
    
    
    
    public function PostProcessFormValues($FormArray)
    {
        // extend this function to process values -- simply return the array back
        if ($this->ShowArray) echo ArrayToStr($FormArray);
        
        
        # COMBINE ALL PICTURES
        # ======================================
        $FormArray['pictures_list'] = "{$FormArray['pictures_list_1']}\n{$FormArray['pictures_list_2']}\n{$FormArray['pictures_list_3']}";
        
        unset($FormArray['pictures_list_1']);
        unset($FormArray['pictures_list_2']);
        unset($FormArray['pictures_list_3']);
        
        
        # COMBINE ALL LINKS
        # ======================================
        $FormArray['links_list'] = "{$FormArray['links_list_1']}\n{$FormArray['links_list_2']}\n{$FormArray['links_list_3']}";
        
        unset($FormArray['links_list_1']);
        unset($FormArray['links_list_2']);
        unset($FormArray['links_list_3']);
        
        
        
        
        
        return $FormArray;
    }
    
    
    public function PrePopulateFormValues($id, $field='')
    {
        parent::PrePopulateFormValues($id, $field);
        $FormPrefix = 'FORM_';
        
        # explode picture list
        # ================================================================================
        $search     = array("\r\n", "\n", "\r");
        $text       = str_replace($search, '|', $_POST[$FormPrefix.'pictures_list']);
        $items      = explode('|', $text);
        
        $count = 1;
        foreach ($items as $item) {
            $_POST[$FormPrefix."pictures_list_".$count] = $item;
            $count++;
        }
        
        # explode link list
        # ================================================================================
        $search     = array("\r\n", "\n", "\r");
        $text       = str_replace($search, '|', $_POST[$FormPrefix.'links_list']);
        $items      = explode('|', $text);
        
        $count = 1;
        foreach ($items as $item) {
            $_POST[$FormPrefix."links_list_".$count] = $item;
            $count++;
        }
    }
    
    
    public function SuccessfulAddRecord()
    {
        if ($this->NewChatUser) {
            header("Location: chat_user;code={$this->NewChatUserCode}");
        }
    }

   



   
    public function ProcessTableCell($field, &$value, &$td_options, $id='')
    {
        parent::ProcessTableCell($field, $value, $td_options, $id);

        switch($field) {
            case 'text':
                $value = TruncStr($value, 60) . '...';
            break;
            case 'links_list':
                $search     = array("\r\n", "\n", "\r");
                $value      = str_replace($search, '<br /><br />', $value);
            break;
            case 'pictures_list':
                $search     = array("\r\n", "\n", "\r");
                $value      = str_replace($search, '<br /><br />', $value);
            break;
        }
        
        
        
        
        
        /*
        if ($field == 'class_roles' || $field == 'module_roles') {
            $value = $this->GetRoleList($field, $value);
        } elseif ($field == 'super_user') {
            if ($value == 'Yes') {
                $td_options = 'style="background-color:#7f7;"';
            }
        }
        */
    }

    public function ProcessRecordCell($field, &$value, &$td_options)
    {
        if ($field == 'class_roles' || $field == 'module_roles') {
            $value = $this->GetRoleList($field, $value);
        }
    }

    public function ViewRecordText($id, $field_list='', $id_field='')
    {
        $Obj_View = new DevRichard_BlogView();
        $RESULT = $Obj_View->GetBlogEntry($id);
        
        $RESULT .= '<br /><hr><br />';
        
        $Obj_Comments = new DevRichard_BlogComments();
        $RESULT .= $Obj_Comments->GetCommentsPending($id);
        
        return $RESULT;
    }


}  // -------------- END CLASS --------------