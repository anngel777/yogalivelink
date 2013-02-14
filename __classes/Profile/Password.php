<?php
class Profile_Password extends BaseClass
{
    public $Show_Query              = false;

    public $WH_ID                   = 0;
    public $Customer_Edit_Record    = false;
    
    public $Pwd_Min_Length          = 6;
    public $Pwd_Max_Length          = 40;
    public $require_uppercase       = true;
    public $require_lowercase       = true;
    public $require_numbers         = false;
    public $require_symbols         = false;
    
    
    public function  __construct()
    {
        parent::__construct();
        
        

        $this->SetParameters(func_get_args());
        $this->WH_ID = $this->GetParameter(0);
        $this->AddDefaultWhere("`$this->Table`.`wh_id`=$this->WH_ID");
        
        
        
        $this->Table                = 'contacts';
        $this->Add_Submit_Name      = 'SESSION_CHECKLISTS_SUBMIT_ADD';
        $this->Edit_Submit_Name     = 'SESSION_CHECKLISTS_SUBMIT_EDIT';
        $this->Index_Name           = 'contacts_id';
        $this->Flash_Field          = 'contacts_id';
        #$this->Default_Where        = '';  // additional search conditions
        $this->Default_Sort         = 'contacts_id';  // field for default table sort
        $this->Default_Fields       = '';
        $this->Unique_Fields        = '';
        
        #$this->Close_On_Success = true;

    } // -------------- END __construct --------------

    public function ProcessAjax()
    {
        
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
        
        $this->Customer_Edit_Record = true;
        $output = $this->EditRecordText($this->WH_ID, 'wh_id');
        return $output;
    }


    public function SuccessfulEditRecord($flash, $id, $id_field)
    {
		echoScript("
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
    }

    
    public function PrePopulateFormValues($id, $field='') // --- extended from parent
    {
        global $FormPrefix;
        parent::PrePopulateFormValues($id,$field);
        $_POST[$FormPrefix.'password'] = '';
    }

    public function PostProcessFormValues($FormArray) // --- extended from parent
    {
        global $PASSWORD_HASH_INFO;
        if (!$this->Error) {

            if ($FormArray['password'] == '') {
                unset($FormArray['password']);

            } else {
                if (empty($PASSWORD_HASH_INFO)) {
                    $this->Error = 'Program Error, Cannot find Password Configuration';
                } else {
         
                    # MAKING MY OWN SECURE PASSWORD CHECKS
                    # =====================================================
                    $check_password = $FormArray['password'];
                    
                    $passed_check = true;
                    $temp_error = '';
                    if ($this->require_uppercase && !preg_match('/[A-Z]/', $check_password)) {
                        $passed_check = false;
                        $temp_error .= 'One uppercase letter is required.<br />';
                    }
                    if ($this->require_lowercase && !preg_match('/[a-z]/', $check_password)) {
                        $passed_check = false;
                        $temp_error .= 'One lowercase letter is required.<br />';
                    }
                    if ($this->require_numbers && !preg_match('/[0-9]/', $check_password)) {
                        $passed_check = false;
                        $temp_error .= 'One number is required.<br />';
                    }
                    if ($this->require_symbols && !preg_match('/[\@\#\$\%\^\*\(\)_\+\=\{\}\[\]|\/\:\;\,\.\?~|\-]/', $check_password)) {
                        $passed_check = false;
                        $temp_error .= 'One symbol is required.<br />';
                    }
                    
                    if (!$passed_check) {
                        $this->Error = $temp_error;
                    }

                    $FormArray['password'] = Lib_Password::GetPasswordHash($FormArray['password']);
                }
            }
        }
        return $FormArray;
    }
    
    public function SetFormArrays()
    {
        $password = ($this->Action == 'ADD') ? "password|Password|password|Y|{$this->Pwd_Max_Length}|{$this->Pwd_Min_Length},{$this->Pwd_Max_Length}|autocomplete=\"off\""
                      : "password|New Password|password|N|{$this->Pwd_Max_Length}|{$this->Pwd_Min_Length},{$this->Pwd_Max_Length}|autocomplete=\"off\"";
        
        
        $info = "<div style='font-size:12px;'>";
        $info .= "<b>Enter your new password below.</b>";
        $info .= "<ul style='padding-left:5px;'>";
        $info .= ($this->Pwd_Min_Length) ? "<li>Must be a minimum of {$this->Pwd_Min_Length} characters</li>" : '';
        $info .= ($this->Pwd_Max_Length) ? "<li>Cannot be more than {$this->Pwd_Max_Length} characters</li>" : '';
        $info .= ($this->require_uppercase) ? '<li>At least one <b>uppercase letter</b> is required.</li>' : '';
        $info .= ($this->require_lowercase) ? '<li>At least one <b>lowercase letter</b> is required.</li>' : '';
        $info .= ($this->require_numbers) ? '<li>At least one <b>number</b> is required.</li>' : '';
        $info .= ($this->require_symbols) ? '<li>At least one <b>symbol</b> is required.</li>' : '';
        $info .= "</ul>";
        $info .= "</div>";
        
        
        if ($this->Customer_Edit_Record) {
            $base_array = array(
                "form|$this->Action_Link|post|customer_edit_form",
                "info||$info",
                $password,
            );
        } else {
            $base_array = array(
                "form|$this->Action_Link|post|db_edit_form",
                "info||$info",
                $password,
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


}  // -------------- END CLASS --------------