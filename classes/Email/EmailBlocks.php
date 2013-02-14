<?php
class Email_EmailBlocks extends BaseClass
{
    public function  __construct()
    {
        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Description' => 'Manage email blocks in back-office -> email addresses that emails will not be blasted to',
        );
        
        $this->Table                = 'email_blocks';
        $this->Add_Submit_Name      = 'EMAIL_BLOCKS_SUBMIT_ADD';
        $this->Edit_Submit_Name     = 'EMAIL_BLOCKS_SUBMIT_EDIT';
        $this->Index_Name           = 'email_blocks_id';
        $this->Flash_Field          = 'email_blocks_id';
        $this->Default_Where        = '';  // additional search conditions
        $this->Default_Sort         = 'email_blocks_id';  // field for default table sort
        $this->Default_Fields       = 'wh_id,email_address,limited_ block';
        $this->Unique_Fields        = '';
        
        $this->Field_Titles = array(
            'email_blocks_id'   => 'Email Blocks Id',
            'wh_id'             => 'Wh Id',
            'email_address'     => 'Email Address',
            'limited_ block'    => 'Limited Block',
            'active'            => 'Active',
            'updated'           => 'Updated',
            'created'           => 'Created'
        );


    } // -------------- END __construct --------------


    public function SetFormArrays()
    {
        $base_array = array(
            "form|$this->Action_Link|post|db_edit_form",
            'text|Wh Id|wh_id|N|11|11',
            'text|Email Address|email_address|N|60|255',
            'checkbox|Limited Block|limited_ block||1|0',
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


}  // -------------- END CLASS --------------