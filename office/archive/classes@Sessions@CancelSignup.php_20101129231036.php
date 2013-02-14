<?php
class Sessions_CancelSignup extends BaseClass
{
    public $table_credits                   = 'credits';
    public $table_sessions                  = 'sessions';
    public $table_sessions_checklists       = 'session_checklists';
    public $sessions_id                     = 0;
    
    public function  __construct()
    {
        $this->SetSQL();
        
        $this->SetParameters(func_get_args());
        $this->sessions_id = ($this->GetParameter(0)) ? $this->GetParameter(0) : 0;
    } // -------------- END __construct --------------
    
    
    public function SetSQL()
    {
        if (empty($this->SQL)) {
            $this->SQL = Lib_Singleton::GetInstance('Lib_Pdo');
        }
    }
    
    
    public function Execute()
    {
        if ($this->sessions_id != 0) {
            echo "ABOUT TO CANCEL A CLASS SIGNUP";
            echo "<br />sessions_id ===> " . $this->sessions_id;
            echo "<br /><br /> NOT ACTUALLY GONNA DELETE - BECUASE NOT TESTED YET";
            #$this->DeactivateSingleSession($this->sessions_id);
        }
    }
    
    
    public function DeactivateSingleSession($sessions_id)
    {
        # 1. deactivate single session via checklist
        # ==============================================================
        $FormArray = array(
            'cancelled'         => 1,
            'cacelled_reason'   => 'User account being deleted',
            'active'            => 0,
        );
        $key_values = $this->FormatDataForUpdate($FormArray);
        
        $result = $this->SQL->UpdateRecord(array(
            'table'         => $this->table_sessions_checklists,
            'key_values'    => $key_values,
            'where'         => "`sessions_id`=$sessions_id AND `active`=1",
        ));
        echo '<br /><br />' . $this->SQL->Db_Last_Query;
        
        
        
        # 2. update the session so it can be booked again
        # ==============================================================
        $FormArray = array(
            'booked'        => 2,
            'booked_wh_id'  => 2,
            'locked'        => 2,
        );
        $key_values = $this->FormatDataForUpdate($FormArray);
        
        $result = $this->SQL->UpdateRecord(array(
            'table'         => $this->table_sessions,
            'key_values'    => $key_values,
            'where'         => "`sessions_id`=$sessions_id AND `active`=1",
        ));
        echo '<br /><br />' . $this->SQL->Db_Last_Query;
        
        
        
        # 3. refund credits
        # ==============================================================
        $date = date('now');
        $note = "\n\n [Date:$date] Credit Refunded from session cancellation (session_id=$sessions_id).";
        $result = $this->SQL->AppendValue(array(
            'table'       => $this->table_credits,
            'key'         => 'notes',
            'value'       => "'$note'",
            'where'       => "`sessions_id`=$sessions_id AND `active`=1",
        ));
        
        $FormArray = array(
            'used'          => 0,
            //'sessions_id'   => 0,
        );
        $key_values = $this->FormatDataForUpdate($FormArray);
        
        $result = $this->SQL->UpdateRecord(array(
            'table'         => $this->table_credits,
            'key_values'    => $key_values,
            'where'         => "`sessions_id`=$sessions_id AND `active`=1",
        ));
        
        
        
        # 4. send email to instructor
        # ==============================================================
        echo "<br /><br />SEND CONFIRMATION EMAIL TO INSTRUCTOR";
        
        
        # 5. send email to customer
        # ==============================================================
        echo "<br /><br />SEND CONFIRMATION EMAIL TO CUSTOMER";
        
        
        return $result;
    }


}  // -------------- END CLASS --------------