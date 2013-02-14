<?php
class Profile_InstructorChecklist extends BaseClass
{
    public $WH_ID                                   = 0;
    public $Show_Array                              = false;
    public $Show_Query                              = false;
    
    public $Fields_Show_To_Instructor               = array();
    
    public $Send_Email                              = false;
    public $Email_Bcc_To_Admin                      = false;
    public $Account_Approved_Email_Template_Id      = '';
    public $Account_Rejected_Email_Template_Id      = '';
    public $Email_Administrator_Email               = '';
    
    public $Page_Link_Ajax                          = '';
    
    public function  __construct()
    {
        parent::__construct();
        

        
        $this->Account_Approved_Email_Template_Id      = $GLOBALS['EMAIL_CONTENT_TEMPLATES']['INS_ACCOUNT_APPROVED'];
        $this->Account_Rejected_Email_Template_Id      = $GLOBALS['EMAIL_CONTENT_TEMPLATES']['INS_ACCOUNT_REJECTED'];
        $this->Email_Administrator_Email               = $GLOBALS['EMAIL_ADMIN_EMAIL'];
        
        $this->Table                = 'instructor_checklist';
        $this->Add_Submit_Name      = 'INSTRUCTOR_CHECKLIST_SUBMIT_ADD';
        $this->Edit_Submit_Name     = 'INSTRUCTOR_CHECKLIST_SUBMIT_EDIT';
        $this->Index_Name           = 'instructor_checklist_id';
        $this->Flash_Field          = 'instructor_checklist_id';
        $this->Default_Where        = '';
        $this->Default_Sort         = 'instructor_checklist_id';
        $this->Default_Fields       = 'NAME,contacts.email_address,account_created,login_created,certification_provided,certification_verified,profile_provided,profile_picture_provided,hours_taught_provided,yoga_styles_provided,insurance_provided,insurance_verified,equipment_verified,contract_sent,contract_signed,process_complete,contacts.instructor_account_limited';
        $this->Unique_Fields        = '';
        
        $this->Joins                = 'LEFT JOIN contacts on contacts.wh_id = instructor_checklist.wh_id';
        //$this->Span_Tables          = array('contacts' => 'LEFT JOIN contacts on contacts.wh_id = instructor_checklist.wh_id');
        
        $this->Field_Titles = array(
            'instructor_checklist_id'       => 'Instructor Checklist Id',
            "{$this->Table}.wh_id"                         => 'WH ID',
            
            "CONCAT(`contacts`.`first_name`, ' ', `contacts`.`last_name`) AS NAME"          => 'Name',
            'contacts.email_address'                                                    => 'Email Address',
            
            'account_created'               => 'Account Created',
            'login_created'                 => 'Login Created',
            'certification_provided'        => 'Certification Provided',
            'certification_verified'        => 'Certification Verified',
            'profile_provided'              => 'Profile Provided',
            'profile_picture_provided'      => 'Profile Picture Provided',
            'hours_taught_provided'         => 'Hours Taught Provided',
            'yoga_styles_provided'          => 'Yoga Styles Provided',
            'insurance_provided'            => 'Insurance Provided',
            'insurance_verified'            => 'Insurance Verified',
            'equipment_verified'            => 'Equipment Verified',
            'contract_sent'                 => 'Contract Sent',
            'contract_signed'               => 'Contract Signed',
            'process_complete'              => 'Process Complete',
            
            'contacts.instructor_account_limited'                                       => 'Limited Account',
            
            "{$this->Table}.active"                        => 'Active',
            "{$this->Table}.updated"                       => 'Updated',
            "{$this->Table}.created"                       => 'Created'
        );
        
        $this->Fields_Show_To_Instructor = array(
            'account_created'               ,
            'login_created'                 ,
            'certification_provided'        ,
            'certification_verified'        ,
            'profile_provided'              ,
            'profile_picture_provided'      ,
            'hours_taught_provided'         ,
            'yoga_styles_provided'          ,
            'insurance_provided'            ,
            'insurance_verified'            ,
            'equipment_verified'            ,
            'contract_sent'                 ,
            'contract_signed'               ,
            'process_complete'              ,
        );
        
        
        $link       = "/office/AJAX/class_execute?eq=" . EncryptQuery("class=Profile_InstructorChecklist") . ";action=load_instructor_profile_from_checklist_id;id=@VALUE@";
        $output     = "<div style='font-size:11px;'><a href='$link' target='_blank' style='text-decoration:none;'>PROFILE</a></div>";
        
        $this->Edit_Links  = qqn("
            
            <td align=`center`><a href=`#` class=`row_edit`   title=`Edit`              onclick=`tableEditClick('@IDX@','@VALUE@','@EQ@'); return false;`></a></td>
            <td align=`center`><a href=`#` class=`row_delete` title=`Delete`            onclick=`tableDeleteClick('@IDX@','@VALUE@','@EQ@'); return false; `></a></td>
            <td align=`center`>$output</td>");
            //<td align=`center`><a href=`#` class=`row_view`   title=`View`              onclick=`tableViewClick('@IDX@','@VALUE@','@EQ@'); return false;`></a></td>
        $this->Edit_Links_Count = 3;
        
    } // -------------- END __construct --------------


    public function SetFormArrays()
    {
        global $FormPrefix;
        
        /*
        $instructor_name = $_POST[$FormPrefix.'email_address'];
        echo ArrayToStr($_POST);
        $instructor_name
        contacts.email_address
        "info|Instructor|$instructor_name",
        */
        
        $base_array = array(
            "form|$this->Action_Link|post|db_edit_form",
            
            'hidden|wh_id',
            
            
            'checkbox|Account Created|account_created||1|0',
            'checkbox|Login Created|login_created||1|0',
            'checkbox|Certification Provided|certification_provided||1|0',
            'checkbox|Certification Verified|certification_verified||1|0',
            'checkbox|Profile Provided|profile_provided||1|0',
            'checkbox|Profile Picture Provided|profile_picture_provided||1|0',
            
            'checkbox|Hours Taught Provided|hours_taught_provided||1|0',
            'checkbox|Yoga Styles Provided|yoga_styles_provided||1|0',
            'checkbox|Insurance Provided|insurance_provided||1|0',
            'checkbox|Insurance Verified|insurance_verified||1|0',
            'checkbox|Equipment Verified|equipment_verified||1|0',
            'checkbox|Contract Sent|contract_sent||1|0',
            'checkbox|Contract Signed|contract_signed||1|0',
            
            'code|<br /><br />',
            'checkbox|Process Complete|process_complete||1|0',
            
            
            #'code|<br /><br />',
            #'checkbox|Limited Account|contacts__instructor_account_limited||1|0',
            
            
            #"button|APPROVE||id='btn_approve'",
            #"button|REJECT||id='btn_reject'",
        );
        
        if ($this->Action == 'ADD') {
            $base_array[] = "text|WHID|wh_id|Y|11|11";
            $base_array[] = "submit|Add Record|$this->Add_Submit_Name";            
            $base_array[] = 'endform';
            $this->Form_Data_Array_Add = $base_array;
        } else {
            $base_array[] = "submit|Update Record|$this->Edit_Submit_Name";
            $base_array[] = 'endform';
            $this->Form_Data_Array_Edit = $base_array;
        }
        
        /*
        $script = <<<SCRIPT
            $('#btn_approve').click(function() {
                //alert('approve');
                //alert('NEED CODE TO SAVE THE APPROVED RECORD');
                $('#{$FormPrefix}status_pending').attr('value', 0);
                $('#{$FormPrefix}status_rejected').attr('value', 0);
                $('#{$FormPrefix}{$this->Edit_Submit_Name}').trigger('click');
            });
            
            $('#btn_reject').click(function() {
                //alert('reject');
                $('#{$FormPrefix}status_pending').attr('value', 0);
                $('#{$FormPrefix}status_rejected').attr('value', 1);
                $('#{$FormPrefix}{$this->Edit_Submit_Name}').trigger('click');
            });
SCRIPT;
        addScriptOnReady($script);
        */
    }


 
    public function GetInstructorChecklist($WH_ID)
    {
        $keys = '';
        foreach ($this->Fields_Show_To_Instructor as $key) {
            $keys .= "$key,";
        }
        $keys = substr($keys, 0, -1);
    
        $record = $this->SQL->GetRecord(array(
            'table' => $this->Table,
            'keys'  => "{$keys}",
            'where' => "`wh_id`=$WH_ID AND $this->Table.active=1",
        ));
        if ($this->Show_Query) echo $this->SQL->Db_Last_Query;

        if ($record) {
            $output = $record;
        } else {
            $output = "<h1>UNABLE TO LOAD INSTRUCTOR CHECKLIST</h1>";
        }

        return $output;
    }
    
    /*
    public function PostProcessFormValues($FormArray)
    {
        
    }
    */
    
    /*
    public function SuccessfulAddRecord()
    {
        echo "<br /><hr><br />COMPLETED SUCCESSFULLY";
        exit();
    }
    */
    
    /*
    public function SuccessfulEditRecord($flash, $id, $id_field)
    {
        echo "<br /><hr><br />COMPLETED SUCCESSFULLY";
        exit();
    }
    */
    
    /*
    public function ProcessTableCell($field, &$value, &$td_options, $id='')
    {
        # ============ WHEN VIEWING A TABLE ============
        
        parent::ProcessTableCell($field, $value, $td_options, $id);


    }
    */
    
    public function ExecuteAjax()
    {
        
        $action         = Get('action');
        $checklist_id   = Get('id');

    
		switch ($action) {
            case 'load_instructor_profile_from_checklist_id':
                # 1. take in a checklist_id
                # 2. get the wh_id for that record
                # 3. get the user information for that wh_id
                # 4. call the instructor profile class now that we have the right information
                
                $instructor_checklist_id = $checklist_id;
                
                $t_ic       = $GLOBALS['TABLE_instructor_checklist'];
                $t_c        = $GLOBALS['TABLE_contacts'];
                $record     = $this->SQL->GetRecord(array(
                    'table' => $GLOBALS['TABLE_instructor_checklist'],
                    'keys'  => "$t_ic.wh_id, $t_c.first_name, $t_c.last_name, $t_c.email_address, $t_c.active",
                    'where' => "instructor_checklist_id=$instructor_checklist_id",
                    'joins' => "LEFT JOIN {$GLOBALS['TABLE_contacts']} ON {$GLOBALS['TABLE_contacts']}.wh_id = {$GLOBALS['TABLE_instructor_checklist']}.wh_id",
                ));
                if ($this->Show_Query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
                
                if ($record){
                    $class      = 'Profile_InstructorProfile';
                    $pagetitle  = "{$record['last_name']}, {$record['first_name']}";
                    $link       = getClassExecuteLinkNoAjax(EncryptQuery("class={$class};v1={$record['wh_id']}"));
                    $link      .= ";pagetitle=Instructor: {$pagetitle}";
                    header("Location: $link");
                } else {
                    echo "ERROR :: Unable to find user record";
                    exit();
                }
                
                
                /*
                $CLASS_EXECUTE_LINK     = '/office/class_execute';
                $eq                     = EncryptQuery("class=Email_EmailResend;v1={$id};");
                $link                   = $CLASS_EXECUTE_LINK . '?eq=' . $eq;
                $script                 = "top.parent.appformCreate('Window', '{$link}', 'apps'); return false;";
                */
                
            break;
        }
	}
    
    public function ProcessTableCell($field, &$value, &$td_options, $id='')
    {
        switch ($field) {
            case 'status_pending':
                $td_options = ($value==1) ? 'style="background-color:yellow;"' : '';
                $value = ($value==1) ? 'YES' : 'NO';                
            break;
            case 'status_rejected':
                $td_options = ($value==1) ? 'style="background-color:#990000; color:#fff;"' : '';
                $value = ($value==1) ? 'YES' : 'NO';
            break;
            case 'profile':
                $value = TruncStr($value, 20);
            break;
            
            case 'instructor_account_limited':
                $td_options = ($value==1) ? 'style="background-color:#990000; color:#fff;"' : '';
                $value = ($value==1) ? 'YES' : 'NO';
            break;
            
            case 'process_complete':
                $td_options = ($value==1) ? 'style="background-color:#559955; color:#fff;"' : '';
                $value = ($value==1) ? 'YES' : 'NO';
            break;
            
            default:
                switch ($value) {
                    case '0':
                        $td_options = 'style="text-align:center; color:#990000; font-weight:bold;"';
                        $value = 'NO';
                    break;
                    case '1':
                        $td_options = 'style="text-align:center;"';
                        $value = 'Y';
                    break;
                    default:
                        $value = $value;
                    break;
                }
            break;
        }
    }


    /*
    # EMAIL SENDING FUNCTIONS
    # ====================================================================
    private function SendEmail($type='')
    {
        $email_template_id = 0;
        switch($type) {
            case 'approved':
                $email_template_id = $this->Account_Approved_Email_Template_Id;
            break;
            case 'rejected':
                $email_template_id = $this->Account_Rejected_Email_Template_Id;
            break;
        }
        
        if ($this->Send_Email && $email_template_id) {
            # INITIALIZE THE EMAIL CLASS
            # =====================================================
            global $ROOT;
            require_once "$ROOT/phplib/swift4/swift_required.php";
            $MAIL = new Email_MailWh;
            
            # PREP THE SWAP ARRAY
            # =====================================================
            $swap_array = array ();
            
            # PREP THE MESSAGE ARRAY
            # =====================================================
            $bcc = ($this->Email_Bcc_To_Admin) ? $GLOBALS['EMAIL_ADMIN_EMAIL'] : '';
            $MAIL->PrepareMailToSendWHID(array(
                'email_template_id'     => $email_template_id,
                'swap_array'            => $swap_array,
                'to_name'               => '',
                'to_email'              => '',
                'bcc'                   => $this->Email_Administrator_Email,
                'wh_id'                 => $this->WH_ID,
            ));
            
            
            # SEND THE PREPARED MESSAGE
            # =====================================================
            if ($MAIL->MailPrepared()) {
                echo "<h1>Message send to INSTRUCTOR.</h1>";
            } else {
                echo "<h1>Unable to send message to INSTRUCTOR.</h1>";
            }
            
        }
    }
    */
    
    
}  // -------------- END CLASS --------------
