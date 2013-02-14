<?php
class Website_HelpcenterFAQs extends BaseClass
{
    public function  __construct()
    {
        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => '',
            'Description' => 'Create and manage helpcenter_faqs',
            'Created'     => '2010-12-20',
            'Updated'     => '2010-12-20'
        );

        $this->Table                = 'helpcenter_faqs';
        $this->Add_Submit_Name      = 'HELPCENTER_FAQS_SUBMIT_ADD';
        $this->Edit_Submit_Name     = 'HELPCENTER_FAQS_SUBMIT_EDIT';
        $this->Index_Name           = 'helpcenter_faqs_id';
        $this->Flash_Field          = 'helpcenter_faqs_id';
        $this->Default_Where        = '';  // additional search conditions
        $this->Default_Sort         = 'helpcenter_faqs_id';  // field for default table sort
        $this->Default_Fields       = 'question,answer,show_on_website,type_customer,type_instructor,sort_order';
        $this->Unique_Fields        = '';
        
        $this->Default_Values       = array(
            'show_on_website'   => 1,
            'sort_order'        => 9999,
        );
        
        $this->Field_Titles = array(
            'helpcenter_faqs_id'    => 'Helpcenter Faqs Id',
            'question'              => 'Question',
            'answer'                => 'Answer',
            'show_on_website'       => 'Show On Website',
            'type_customer'         => 'Show To Customers',
            'type_instructor'       => 'Show To Instructors',
            'sort_order'            => 'Sort Order',
            'active'                => 'Active',
            'updated'               => 'Updated',
            'created'               => 'Created'
        );
        
    } // -------------- END __construct --------------
    
    
    public function SetFormArrays()
    {
        #$this->AddScript();
        
        
        $categories_raw_customer = $this->SQL->GetAssocArray(array(
            'table' => 'helpcenter_categories',
            'key'   => 'helpcenter_categories_id',
            'value' => 'title',
            'where' => 'active=1 AND `type_customer`=1',
        ));
        
        $categories_raw_instructor = $this->SQL->GetAssocArray(array(
            'table' => 'helpcenter_categories',
            'key'   => 'helpcenter_categories_id',
            'value' => 'title',
            'where' => 'active=1 AND `type_instructor`=1',
        ));
        
        $categories_list_customer = Form_AssocArrayToList($categories_raw_customer);
        $categories_list_instructor = Form_AssocArrayToList($categories_raw_instructor);
    
        $style = 'style="color:#990000; font-weight:bold; font-size:13px;"';
    
        $base_array = array(
            "info||<div $style>ADD CATEGORIES FIRST - you will then have to re-edit or re-add the record.</div>",
            
            'infotemplate|
                <div class="forminfo">
                <div style="overflow:auto;height:100px;border:1px dotted #888;">
                @
                </div>
                </div>
                ',
            'checkboxlistset|Categories CUSTOMER|categories_customer|N||' . $categories_list_customer,
            'checkboxlistset|Categories INSTRUCTOR|categories_instructor|N||' . $categories_list_instructor,
            'infotemplate|STD',
            "button|Add Category|addNewCategory();",
            'code|<br /><br />',
            
            
            'html|Question|question|N|60|4',
            'html|Answer|answer|N|60|4',
            
            'code|<br />',
            'text|Sort Order|sort_order|N|4|4',
            'info||9999 = Not Sorted',
            'code|<br />',
            
            'checkbox|Show On Website|show_on_website||1|0',
            'checkbox|Most Common Questions|most_common||1|0',
            'checkbox|Show To Customers|type_customer||1|0',
            'checkbox|Show To Instructors|type_instructor||1|0',
        );
        
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
    
    public function GetSingleFAQ($ID=0)
    {
        if ($ID) {
            $record = $this->SQL->GetRecord(array(
                'table' => $this->Table,
                'keys'  => 'question, answer',
                'where' => "`helpcenter_faqs_id`={$ID} AND `active`=1",
            ));
            
            if ($record) {
                return $record;
            }
        }
    }
    
    public function AddScript()
    {
        $link   = getClassExecuteLinkNoAjax(EncryptQuery("class=Website_HelpcenterFAQsCategories"));
        //echo $link;
        
        $script = "
            // -------------- function for email setup --------------
            function addNewCategory()
            {
                //top.parent.appformCreate('Window', '{$link}', 'apps');
                //parent.appformCreate('Window', '{$link}', 'apps');
                window.location = \"{$link}\";
            }
            ";
        AddScript($script);
    }
    
    
    public function ProcessTableCell($field, &$value, &$td_options, $id='')
    {
        switch ($field) {
            case 'show_on_website':
                $td_options     = ($value==0) ? 'style="background-color:#990000;"' : '';
                $value          = ($value==1) ? 'YES' : 'NO';
            break;
            
            case 'type_customer':
            case 'type_instructor':
                $td_options     = ($value==0) ? 'style="color:#990000; font-weight:bold;"' : '';
                $value          = ($value==1) ? 'YES' : 'NO';
            break;
        }
    }


}  // -------------- END CLASS --------------