<?php

/* ========================================================================== 
    FUNCTION :: Profile_InstructorCreateContactFromProfile

    Loops through all instructor profiles and creates a real contact in 
    the contacts table - if it doesn't exist already
    
# ========================================================================== */


class Profile_InstructorCreateContactFromProfile extends BaseClass
{
    public $show_query              = true;
    
    public function  __construct()
    {
        $this->SetSQL();
        $this->Close_On_Success = false;
    }
    
    public function SetSQL()
    {
        if (empty($this->SQL)) {
            $this->SQL = Lib_Singleton::GetInstance('Lib_Pdo');
        }
    }
    
    public function Execute()
    {
        
        # GET ALL THE INSTRUCTORS
        # ====================================================
        $records = $this->SQL->GetArrayAll(array(
            'table' => $GLOBALS['TABLE_instructor_profile'],
            'keys'  => '*',
            'where' => 'active=1',
        ));
        
        foreach ($records as $record) {
            
            # CHECK IF THEY HAVE A contacts RECORD
            # ===========================================================
            $result = $this->SQL->GetRecord(array(
                'table'     => $GLOBALS['TABLE_contacts'],
                'keys'      => 'wh_id',
                'where'     => "wh_id={$record['wh_id']}",
            ));
            
            
            # CREATE ONE IF THEY DON'T
            # ===========================================================
            if (!$result) {
                $email = "{$record['first_name']}_{$record['last_name']}@mailwh.com";
                
                $FormArray = array(
                    'wh_id'                 => $record['wh_id'],
                    'first_name'            => $record['first_name'],
                    'last_name'             => $record['last_name'],
                    'email_address'         => $email,
                    'type_customer'         => 0,
                    'type_instructor'       => 1,
                    'type_administrator'    => 0,
                    'created_by'            => 'Profile_InstructorCreateContactFromProfile',
                    'time_zones_id'         => 5,
                    'active'                => 1,
                );
        
                $keys_values    = $this->FormatDataForInsert($FormArray);
                $parts          = explode('||', $keys_values);
                $keys           = $parts[0];
                $values         = $parts[1];

                $result = $this->SQL->AddRecord(array(
                    'table'     => $GLOBALS['TABLE_contacts'],
                    'keys'      => $keys,
                    'values'    => $values,
                ));
                if ($this->show_query) echo "<br /><br />LAST QUERY = " . $this->SQL->Db_Last_Query;
            } else {
                echo "<br /> Contact record already exists.";
            }
            
            echo "<div style='width:400px;'>&nbsp;</div>";
            
        } // end loop
        
    }
    
    
} //end class