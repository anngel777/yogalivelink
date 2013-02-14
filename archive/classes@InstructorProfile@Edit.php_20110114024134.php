<?php
class InstructorProfile_Edit extends BaseClass
{
    public $wh_id                   = 0;
    public $IsTesting               = true;
    public $ShowArray               = true;
    public $Show_Query              = false;
    
    public $Instructor_Edit_Record  = true;
    
    private $original_record        = array();
    
    private $require_admin_approval             = false;
    private $profile_require_admin_approval     = 'first_name,last_name,profile'; //if these fields are changed - will require administrator approval
    private $pending_profile_exists             = false; // set this to TRUE if a pending or rejected record exists

    private $deactivate_pending_records_on_update   = true;
    private $delete_pending_records_on_update       = false;
            
    public function  __construct()
    {
        parent::__construct();
    
        $this->Close_On_Success = false;
    
        $this->ClassInfo = array(
            'Created By'  => '',
            'Description' => 'Create and manage instructor_profile',
            'Created'     => '2010-10-17',
            'Updated'     => '2010-10-17'
        );

        
        $this->SetParameters(func_get_args());
        $this->WH_ID = $this->GetParameter(0);
        #if ($this->WH_ID) {
            $this->AddDefaultWhere("`$this->Table`.`wh_id`=$this->WH_ID");
        #}
        
        
        $this->Table                = 'instructor_profile';
        $this->Add_Submit_Name      = 'INSTRUCTOR_PROFILE_SUBMIT_ADD';
        $this->Edit_Submit_Name     = 'INSTRUCTOR_PROFILE_SUBMIT_EDIT';
        $this->Index_Name           = 'instructor_profile_id';
        $this->Flash_Field          = 'instructor_profile_id';
        $this->Default_Where        = '';  // additional search conditions
        $this->Default_Sort         = 'instructor_profile_id';  // field for default table sort
        $this->Default_Fields       = 'wh_id,first_name,last_name,yoga_types,profile,experience_years,location_city,locaion_state,primary_pictures_id,secondary_array_pictures_id';
        $this->Unique_Fields        = '';
        
        #$this->Default_Values       = array(
        #    'original_record' => ArrayToStr($this->original_record),
        #);
        

        $this->Field_Titles = array(
            'instructor_profile_id'         => 'Instructor Profile Id',
            'wh_id'                         => 'Wh Id',
            'first_name'                    => 'First Name',
            'last_name'                     => 'Last Name',
            'yoga_types'                    => 'Yoga Types',
            'profile'                       => 'Profile',
            'experience_years'              => 'Experience Years',
            'location_city'                 => 'Location City',
            'location_state'                => 'Location State',
            'primary_pictures_id'           => 'Primary Pictures Id',
            'secondary_array_pictures_id'   => 'Secondary Array Pictures Id',
            'active'                        => 'Active',
            'updated'                       => 'Updated',
            'created'                       => 'Created'
        );
        
    } // -------------- END __construct --------------

    public function ProcessAjax()
    {
        #echo "<br /><h1>ProcessAjax()</h1>";
    }

    public function Execute()
    {
        $output = $this->EditRecordSpecial();
        echo $output;
    }

    public function ExecuteAjax()
    {
        $output = $this->EditRecordSpecial();
        echo $output;
    }


    public function EditRecordSpecial($WH_ID='')
    {
        global $FORM_VAR;
        
        if ($WH_ID) {
            $this->WH_ID = $WH_ID;
        }

        $this->Instructor_Edit_Record = true;
        $output = $this->EditRecordText($this->WH_ID, 'wh_id');
        
        return $output;
    }
    
    public function ListRecordSpecial($WH_ID)
    {
        $this->WH_ID = $WH_ID;

        $record = $this->SQL->GetRecord(array(
            'table' => $this->Table,
            'keys'  => '*',
            'where' => "`wh_id`=$this->WH_ID AND $this->Table.active=1",
        ));
        if ($this->Show_Query) echo $this->SQL->Db_Last_Query;

        if ($record) {
            $output = $record;
        } else {
            $output = "<h1>UNABLE TO LOAD INSTRUCTOR PUBLIC PROFILE</h1>";
        }

        return $output;
    }
    
    
    public function SetFormArrays()
    {
        global $FormPrefix;
        $title_extra = '';
        $status_reject_reason   = 'NONE PROVIDED';
        
        
        # CHECK IF A PENDING RECORD EXISTS
        # =========================================================
        $wh_id = Post($FormPrefix.'wh_id');
        $record = $this->SQL->GetRecord(array(
            'table' => 'instructor_profile_pending',
            'keys'  => '*',
            'where' => "`wh_id`='{$wh_id}'AND active=1",
        ));
        
        if ($record) {
            $this->pending_profile_exists = true;
            
            $output_status          = '';
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
                $output_status = "<div style='font-size:14px; background-color:orange; color:#000; padding:5px; border:1px solid #ccc;'><b>YOUR PROFILE IS PENDING ADMINISTRATOR APPROVAL.</b><br />Any changes you make to this profile will overwrite the current pending request and start a new request to the system administrator. Your profile as dispalyed below is the current pending profile - NOT necessarily what customers see when viewing your profile. CLICK HERE to see your profile as viewed by customers.</div><br /><br />";
            }
            
            if ($status_rejected) {
                $title_extra = "<span style='color:#990000;'>(REJECTED)</span>";
                $output_status = "<div style='font-size:14px; background-color:red; color:#fff; padding:5px; border:1px solid #ccc;'><b>YOUR PROFILE HAS BEEN REJECTED BY ADMINISTRATOR.</b><br />Any changes you make to this profile will overwrite the current pending request and start a new request to the system administrator. Your profile as dispalyed below is the current rejected profile - NOT necessarily what customers see when viewing your profile. CLICK HERE to see your profile as viewed by customers.</div><br /><br />";
                $rejected_reason = ($record['rejected_reason'] != '') ? $record['rejected_reason'] : $status_reject_reason;
                $status_reject_reason = "info|Rejected Reason|{$rejected_reason}";
            }
            
            echo $output_status;
        }
        
        
        $tmp = Get('DIALOGID');
        $photo_link = <<<OUT
        <a href="#" onclick="top.appformCreate('Photo Upload', 'image_upload_crop;upload_dir=images/instructors;ret_diag={$tmp};ret_field=FORM_primary_pictures_id','apps'); return false;">ADD/EDIT PHOTO</a>
OUT;
        

        if ($this->Instructor_Edit_Record) {
            $base_array = array(
                "form|$this->Action_Link|post|db_edit_form",
                $status_reject_reason,
                'hidden|wh_id',
                "text|First Name {$title_extra}|first_name|N|60|255",
                "text|Last Name {$title_extra}|last_name|N|60|255",
                "checkboxlistset|Yoga Types|yoga_types|N||{$this->yoga_type_list}",
                "textarea|Profile {$title_extra}|profile|N|60|4",
                'text|Experience Years|experience_years|N|2|2',
                #'text|Location City|location_city|N|45|45',
                #'text|Location State|location_state|N|45|45',
                "text|Pictures|primary_pictures_id|N|45|255||||$photo_link",
            );
        } else {
            $base_array = array(
                "form|$this->Action_Link|post|db_edit_form",
                $status_reject_reason,
                'hidden|wh_id',
                "text|First Name {$title_extra}|first_name|N|60|255",
                "text|Last Name {$title_extra}|last_name|N|60|255",
                "checkboxlistset|Yoga Types|yoga_types|N||{$this->yoga_type_list}",
                "textarea|Profile {$title_extra}|profile|N|60|4",
                'text|Experience Years|experience_years|N|2|2',
                #'text|Location City|location_city|N|45|45',
                #'text|Location State|location_state|N|45|45',
                "text|Pictures|primary_pictures_id|N|45|255||||$photo_link",
            );
        }
        
        if ($this->Action == 'ADD') {
            $base_array[] = "submit|Add Record|$this->Add_Submit_Name";
            $base_array[] = 'endform';
            $this->Form_Data_Array_Add = $base_array;
        } else {
            $base_array = BaseArraySpecialButtons($base_array, $this->Edit_Submit_Name);
            $base_array[] = 'endform';
            $this->Form_Data_Array_Edit = $base_array;
        }
    }


 
    
    public function PostProcessFormValues($FormArray)
    {
        //return $FormArray;
        //echo ArrayToStr($FormArray);
        
        
        $pass_forward_array = array();
        $require_approval = explode(',', $this->profile_require_admin_approval);
    
        # GET THE ORIGINAL RECORD
        //$qid = $this->SQL->QuoteValue($id);
        $record = $this->SQL->GetRecord(array(
            'table' => $this->Table,
            'keys'  => $this->profile_require_admin_approval,
            'where' => "`wh_id`='{$FormArray['wh_id']}'",
        ));
        //echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        $this->original_record = $record;
        
        # CHECK SUBMITTED RECORD
        foreach ($FormArray as $var => $val) {
            # 1. look at each form value
            # 2. see if its in the list of admin needing to approve
            # 3. if it is - see if its changed - thus triggering an admin approval
            
            $pass_forward_array[$var] = $val;
            
            if (in_array($var, $require_approval)) {
                $cur_val    = $val;
                $orig_val   = $this->original_record[$var];
                
                # store the original value
                $pass_forward_array[$var] = $orig_val;
                
                $this->require_admin_approval = ($cur_val != $orig_val) ? true : $this->require_admin_approval;
                ###echo "<br />$cur_val => $orig_val";
            }
        }
        
        # IF THIS WAS AN EDIT RECORD THAT WAS PREVIOUSLY PENDING ADMIN APPROVAL - 
        # FORCE APPROVAL EVEN IF RECORDS HAVE BEEN RESTORED TO PREVIOUSLY APPROVED VALUES. THE
        # EARLIER CHECK WOULD NOT SHOW THIS RECORD NEEDING APPROVAL - BUT ALSO WOULDN'T DELETE ANY
        # PENDING RECORDS - I SUPPOSE WE COULD USE THIS LOGIC TO DELETE PENDING RECORDS ALSO
        $this->require_admin_approval = ($this->pending_profile_exists) ? true : $this->require_admin_approval;
        
        
        
        if ($this->require_admin_approval) {
        
            # 1. Remove any existing pending records
            # 2. Add the profile as pending record
            # 3. Reset the form values of admin-approved back to the original values
            # 4. Allow the form array to pass forward
            
            echo "<br /><h1>REQUIRE ADMINISTRATOR APPROVAL</h1>";
            
            if ($this->deactivate_pending_records_on_update) {
                # DE-ACTIVATE EXISTING RECORDS
                $result = $this->SQL->UpdateRecord(array(
                    'table'         => 'instructor_profile_pending',
                    'key_values'    => "`active`=0",
                    'where'         => "`wh_id`='{$FormArray['wh_id']}'",
                ));
            }
            
            if ($this->delete_pending_records_on_update) {
                # DELETE EXISTING RECORDS
                $result = $this->SQL->DeleteRecord(array(
                    'table'         => 'instructor_profile_pending',
                    'where'         => "`wh_id`='{$FormArray['wh_id']}'",
                ));
            }
            
            //echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
            if ($result) {
                echo "<br />EXISTING PENDING RECORDS DELETED";
            }
            
            
            # ADD NEW RECORD
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
            //echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
            if ($result) {
                echo "<br />PENDING RECORD ADDED";
            }
            
            
            # RESET THE FORM ARRAY
            $FormArray = $pass_forward_array;
        }
        
        #exit();
        return $FormArray;
    }
    
    
    
    public function SuccessfulAddRecord()
    {
        //echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        //echo "<br />LAST QUERY = " . $this->SQL->Db_Query;
        
        echo "<br /><hr><br />COMPLETED SUCCESSFULLY";
        //exit();
    }
    
    public function SuccessfulEditRecord($flash, $id, $id_field)
    {
        //echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        //echo "<br />LAST QUERY = " . $this->SQL->Db_Query;
        //
        echo "<br /><hr><br />COMPLETED SUCCESSFULLY";
        //exit();
        
        /*
        echoScript("
			top.parent.reloadInstructorPublicProfile();
			
			var dialogNumber = '';
			var dialogID = '';
			if (window.frameElement) {
				if (window.frameElement.id.substring(0, 13) == 'appformIframe') {
					dialogNumber = window.frameElement.id.replace('appformIframe', '');
					dialogID = 'appform' + dialogNumber;
				}
			}
			top.parent.appformCloseOverlay(dialogID);
			
		");
        */
    }
    

    public function ProcessTableCell($field, &$value, &$td_options, $id='')
    {
        switch ($field) {
            case 'profile':
                $value = TruncStr($value, 60);
            break;
        }
    }
    
    
}  // -------------- END CLASS --------------
