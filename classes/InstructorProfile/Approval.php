<?php
class InstructorProfile_Approval extends BaseClass
{
    public $Show_Query                              = false;                            // TRUE = output the database queries ocurring on this page
    private $profile_require_admin_approval         = 'first_name,last_name,profile';   // if these fields are changed - will require administrator approval
    public $Send_Email                              = true;                             // TRUE = send an email to instructor notifying that a profile has been approved
    public $Email_Bcc_To_Admin                      = false;                            // TRUE = send a copy of email to admin system
    private $Deactivate_Pending_Records_On_Update   = true;                             // TRUE = deactivate the pending request on profile approval    // ----- only one of these should be true -----
    private $Delete_Pending_Records_On_Update       = false;                            // TRUE = delete the pending record on profile approval         // ----- only one of these should be true -----
    
    // ---------- NON-SETTABLE VARIABLES ----------
    public $Profile_Approved_Email_Template_Id      = '';
    public $Profile_Rejected_Email_Template_Id      = '';
    public $Email_Administrator_Email               = '';
    public $WH_ID                                   = 0;        // WHID of record being approved
    private $original_record                        = array();
    private $pending_profile_exists                 = false;    // gets set to TRUE if a pending or rejected record exists
    
    
    public function  __construct()
    {
        parent::__construct();
        
        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Description' => 'Admnistrator approving changes to an instructor profile',
        );
        
        $this->Profile_Approved_Email_Template_Id      = $GLOBALS['EMAIL_CONTENT_TEMPLATES']['INS_PROFILE_APPROVED'];
        $this->Profile_Rejected_Email_Template_Id      = $GLOBALS['EMAIL_CONTENT_TEMPLATES']['INS_PROFILE_REJECTED'];
        $this->Email_Administrator_Email               = $GLOBALS['EMAIL_ADMIN_EMAIL'];
        
        $this->Table                = 'instructor_profile_pending';
        $this->Add_Submit_Name      = 'INSTRUCTOR_PROFILE_SUBMIT_ADD';
        $this->Edit_Submit_Name     = 'INSTRUCTOR_PROFILE_SUBMIT_EDIT';
        $this->Index_Name           = 'instructor_profile_pending_id';
        $this->Flash_Field          = 'instructor_profile_pending_id';
        $this->Default_Where        = '';  // additional search conditions
        $this->Default_Sort         = 'instructor_profile_pending_id';  // field for default table sort
        $this->Default_Fields       = 'NAME, LOCATION,yoga_types,profile,status_pending,status_rejected,rejected_reason';
        $this->Unique_Fields        = '';
        
        #$this->Default_Values       = array(
        #    'original_record' => ArrayToStr($this->original_record),
        #);
        

        $this->Field_Titles = array(
            'instructor_profile_pending_id'                                     => 'Instructor Profile Id',
            "CONCAT(`first_name`, ' ', `last_name`) AS NAME"                    => 'Name',
            "CONCAT(`location_city`, ', ', `location_state`) AS LOCATION"       => 'Location',
            'yoga_types'                    => 'Yoga Types',
            'profile'                       => 'Profile',
            'status_pending'             	=> 'Pending',
            'status_rejected' 	            => 'Rejected',
            'rejected_reason'               => 'Rejected Reason',
            'active'                        => 'Active',
            'updated'                       => 'Updated',
            'created'                       => 'Created'
        );
        
    } // -------------- END __construct --------------


    public function SetFormArrays()
    {
        global $FormPrefix;
        $title_extra = '';
        
        
        # CHECK IF A PENDING RECORD EXISTS
        # =========================================================
        $wh_id = $_POST[$FormPrefix.'wh_id'];
        $record = $this->SQL->GetRecord(array(
            'table' => 'instructor_profile_pending',
            'keys'  => '*',
            'where' => "`wh_id`='{$wh_id}'AND active=1",
        ));
        
        if ($record) {
            $this->pending_profile_exists = true;
            
            $output_status          = '';
            $status_reject_reason   = '';
            $status_pending         = $record['status_pending'];
            $status_rejected        = $record['status_rejected'];
            
            $list = explode(',', $this->profile_require_admin_approval);
            foreach ($list as $field) {
                if (!havesubmit($this->Add_Submit_Name) && !havesubmit($this->Edit_Submit_Name)) {
                    $_POST[$FormPrefix.$field] = $record[$field];
                }
            }
            
            if ($status_pending) {
                $title_extra = "<span style='color:#990000;'>(PENDING)</span>";
                $output_status = "<div style='font-size:12px; background-color:orange; color:#000; padding:2px; border:1px solid #ccc;'><b>PROFILE PENDING</b></div><br /><br />";
            }
            
            if ($status_rejected) {
                $title_extra = "<span style='color:#990000;'>(REJECTED)</span>";
                $output_status = "<div style='font-size:12px; background-color:red; color:#fff; padding:2px; border:1px solid #ccc;'><b>PROFILE REJECTED</b></div><br /><br />";
                $status_reject_reason = "info|Rejected Reason|{$record['rejected_reason']}";
            }
            
            echo $output_status;
        }
    
    
        # INJECT STYLESHEET FOR PROFILE PREVIEW - HAS TO BE DONE HERE
        $OBJ_View = new InstructorProfile_View();
        #$OBJ_View->CalculateStyle();
        #$OBJ_View->AddStyle();
        $preview_width = $OBJ_View->window_width;
        $preview_holder_width = $preview_width + 10;
        
        $left_width             = 400;
        $right_width            = $preview_width + 40;
        $outter_holder_width    = $left_width + $right_width + 40;
        
        $tmp = Get('DIALOGID');
        $photo_link = <<<OUT
        <a href="#" onclick="top.appformCreate('Photo Upload', 'image_upload_crop;upload_dir=images/instructors;ret_diag={$tmp};ret_field=FORM_primary_pictures_id','apps'); return false;">ADD/EDIT PHOTO</a>
OUT;
        
        $base_array = array(
            "code|<div style='width:{$outter_holder_width}px;'>",
            "code|<div style='float:left; width:{$left_width}px;'>",
            
                "form|$this->Action_Link|post|db_edit_form",
                
                "hidden|wh_id",
                "hidden|status_pending",
                "hidden|status_rejected",
                
                "text|First Name {$title_extra}|first_name|N|30|255",
                "text|Last Name {$title_extra}|last_name|N|30|255",
                "checkboxlistset|Yoga Types|yoga_types|N||{$this->yoga_type_list}",
                "textarea|Profile {$title_extra}|profile|N|60|4",
                #'text|Experience Years|experience_years|N|2|2',
                #'text|Location City|location_city|N|30|45',
                #'text|Location State|location_state|N|30|45',
                "text|Picture {$title_extra}|primary_pictures_id|N|25|255||||$photo_link",
                #'text|Secondary Array Pictures Id|secondary_array_pictures_id|N|11|11',
                "button|REFRESH PROFILE|ProfilePreview();",
                'code|<br /><br />',
                "textarea|Rejected Reason|rejected_reason|N|60|3",
                'code|<br /><br />',
                #"button|APPROVE|ProfileApprove();|id='btn_approve'",
                #"button|REJECT|ProfileReject();|id='btn_reject'",
                
                "button|APPROVE||id='btn_approve'",
                "button|REJECT||id='btn_reject'",
                
            "code|</div>",
            "code|<div style='float:right; width:{$right_width}px; height:100%; border:2px solid #ddd;'><div id='profile_preview' height:100%;'></div></div>",
            "code|<div style='clear:both;'></div>",
            "code|</div>",
        );
        
        if ($this->Action == 'ADD') {
            $base_array[] = "code|<div style='display:none;'>";
            $base_array[] = "submit|Add Record|$this->Add_Submit_Name";
            $base_array[] = "code|</div>";
            
            $base_array[] = 'endform';
            $this->Form_Data_Array_Add = $base_array;
        } else {
            $base_array[] = "code|<div style='display:none;'>";
            $base_array[] = "submit|Update Record|$this->Edit_Submit_Name";
            $base_array[] = "code|</div>";
            
            $base_array[] = 'endform';
            $this->Form_Data_Array_Edit = $base_array;
        }
        
        
        # SCRIPT FOR LOADING PROFILE PREVIEW
        $script = <<<SCRIPT
        function ProfilePreview() {
            var loadUrl         = "/office/AJAX/instructor_profile/instructor_profile_view.php?mod=true&action=get_profile";
            var ajax_load       = "<img src='/images/loading.gif' alt='loading...' />";
            var sendData        = $("#db_edit_form").serialize();
            
            $("#profile_preview").html(ajax_load);
            $.post(loadUrl, sendData, function(data){
                //alert("Data Loaded: " + data);
                $("#profile_preview").html(data);
            });
        }
SCRIPT;
        addScript($script);
        
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
        addScriptOnReady('ProfilePreview();');
    }


 
    
    public function PostProcessFormValues($FormArray)
    {
        # FUNCTION :: Process the submitted approval record
        
        
        # ---------- SET THE WH_ID ----------
        $this->WH_ID = $FormArray['wh_id'];
    
        # ---------- CHECK IF THIS IS BEING APPROVED ----------
        $approved = ($FormArray['status_pending'] == 0 && $FormArray['status_rejected'] == 0) ? true : false;
        
        # ---------- IF BEING - REJECTED - STORED THE PENDING RECORD AS REJECTED ----------
        $rejected = ($FormArray['status_rejected'] == 1) ? true : false;
        
        
        # ---------- REMOVE THE OLD RECORDS - for APPROVED or REJECTED records ----------
        if ($this->Deactivate_Pending_Records_On_Update) {
            # DE-ACTIVATE EXISTING RECORDS
            $result = $this->SQL->UpdateRecord(array(
                'table'         => 'instructor_profile_pending',
                'key_values'    => "`active`=0",
                'where'         => "`wh_id`='{$FormArray['wh_id']}'",
            ));
            if ($this->Show_Query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        }
        
        if ($this->Delete_Pending_Records_On_Update) {
            # DELETE EXISTING RECORDS
            $result = $this->SQL->DeleteRecord(array(
                'table'         => 'instructor_profile_pending',
                'where'         => "`wh_id`='{$FormArray['wh_id']}'",
            ));
            if ($this->Show_Query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        }
        
        
        if ($result) {
            echo "<br />EXISTING PENDING RECORDS DELETED";
        }
        
        
        if ($rejected) {
            # ADD NEW RECORD
            # =====================================================================
            $keys   = '';
            $values = '';            
            foreach ($FormArray as $var => $val) {
                $val = addslashes($val);
                
                $keys   .= "`$var`, ";
                $values .= "'$val', ";
            }
            $keys   = substr($keys, 0, -2);
            $values = substr($values, 0, -2);
            
            $result = $this->SQL->AddRecord(array(
                'table'     => 'instructor_profile_pending',
                'keys'      => $keys,
                'values'    => $values,
            ));
            if ($this->Show_Query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
            if ($result) {
                echo "<br />PENDING RECORD ADDED";
                $this->SendEmail('rejected');
            }
        }


        if ($approved) {
            # ADD NEW RECORD
            # =====================================================================
            
            # unset variables not used
            unset($FormArray['status_pending']);
            unset($FormArray['status_rejected']);
            unset($FormArray['rejected_reason']);
            
            $key_values = '';
            foreach ($FormArray as $var => $val) {
                $val = addslashes($val);
                $key_values .= "`$var`='$val', ";
            }
            $key_values = substr($key_values, 0, -2);
            
            $result = $this->SQL->UpdateRecord(array(
                'table'         => 'instructor_profile',
                'key_values'    => $key_values,
                'where'         => "`wh_id`='{$FormArray['wh_id']}'",
            ));
            if ($this->Show_Query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
            if ($result) {
                echo "<br />LIVE PROFILE UPDATED";
                $this->SendEmail('approved');
            }
        }

        # UPDATE THE PARENT TABLE
        # =====================================================================
        AddScript("parent.document.refresh();");
        
        
        
        # EXIT
        # =====================================================================
        exit();
    }
    
    
    
    public function SuccessfulAddRecord()
    {
        # FUNCTION :: Message to display on success
        
        echo "<br /><hr><br />COMPLETED SUCCESSFULLY";
        exit();
    }
    
    public function SuccessfulEditRecord($flash, $id, $id_field)
    {
        # FUNCTION :: Message to display on success
        
        echo "<br /><hr><br />COMPLETED SUCCESSFULLY";
        exit();
    }
    
    
    
    public function ProcessTableCell($field, &$value, &$td_options, $id='')
    {
        # FUNCTION :: Modify how data is displayed in table view
        
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
        }
    }


    
    # EMAIL SENDING FUNCTIONS
    # ====================================================================
    private function SendEmail($type='')
    {
        $email_template_id = 0;
        switch($type) {
            case 'approved':
                $email_template_id = $this->Profile_Approved_Email_Template_Id;
            break;
            case 'rejected':
                $email_template_id = $this->Profile_Rejected_Email_Template_Id;
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
}  // -------------- END CLASS --------------
 