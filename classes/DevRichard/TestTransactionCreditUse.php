<?php
class DevRichard_TestTransactionCreditUse extends BaseClass
{
    
    public $show_query = true;
    public $sessions_id = 666;
    public $Table_Credits = 'credits';
    public $WH_ID = 1000000001;
    
    
    public function  __construct()
    {
        parent::__construct();
    }
    
    

    
    
    
    public function Execute()
    {
        
        $output = '';
        $passed = true;
        $cost_in_credits    = 10;
        
        
        $this->SQL->StartTransaction();
        
        
        # BLOCK OUT THE CREDITS
        # ============================================================
        $key_values = $this->FormatDataForUpdate(array(
            'used'          => 9,
            'sessions_id'   => $this->sessions_id,
        ));
        $result = $this->SQL->UpdateRecord(array(
            'table'         => $this->Table_Credits,
            'key_values'    => $key_values,
            'where'         => "`wh_id`=$this->WH_ID AND `used`=0 AND `active`=1 LIMIT $cost_in_credits",
        ));
        if ($this->show_query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        
        echo "<div class='success_message'>Affected_Rows ===> " . $this->SQL->Affected_Rows . "</div>";
        
        
        
        $passed = (!$result || $this->SQL->Affected_Rows != $cost_in_credits) ? false : $passed;
        //$passed = $result;
        
        if ($passed) {
            $output .= "<div class='success_message'>COMMITING TRANSACTION</div>";
            $this->SQL->TransactionCommit();
        } else {
            $output .= "<div class='error_message'>ROLLBACK TRANSACTION</div>";
            $this->SQL->Rollback();
        }
        
        
        
        //$passed = (!$result || $this->SQL->Affected_Rows != $cost_in_credits) ? false : $passed;
        $output .= ($passed) ? "<div class='success_message'>Using Credits PASSED</div>" : "<div class='error_message'>Using Credits FAILED</div>";
        
        echo $output;
        
    }
    
    
}