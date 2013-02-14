<?php
class Website_InstructorProfile extends BaseClass
{
    public $Show_Query              = false;    // TRUE = output the database queries ocurring on this page
    public $Show_Instructor_WHID    = false;    // TRUE = dispaly the WHID in the output - DEV ONLY
    
    public $WH_ID                   = 0;
    
    public function  __construct()
    {
        parent::__construct();
    
        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Description' => 'Get an instructor profile and output to website based on WH ID',
        );
        
        $this->SetParameters(func_get_args());
        $this->WH_ID = ($this->GetParameter(0)) ? $this->GetParameter(0) : 0;
        
        if (!$this->WH_ID) {
            echo "ERROR :: Unable to load Profile :: No instructor WHID provided.";
            exit();
        }
        
    } // ---------- end construct -----------
    
    public function SetSQL()
    {
        if (empty($this->SQL)) {
            $this->SQL = Lib_Singleton::GetInstance('Lib_Pdo');
        }
    }
    
    public function Execute()
    {
        echo $this->GetInstructorProfile();
    }
    
    private function GetInstructorProfile()
    {
        $record = $this->SQL->GetRecord(array(
            'table' => $GLOBALS['TABLE_instructor_profile'],
            'keys'  => '*',
            'where' => "`wh_id`={$this->WH_ID} AND `active`=1",
        ));
        if ($this->Show_Query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        
        $output = '';
        if ($record) {
            $picture    = "<img src='/office/{$record['primary_pictures_id']}'  alt='{$record['first_name']} {$record['last_name']}' border='0' />";
            $name       = "{$record['first_name']} {$record['last_name']}";
            $profile    = $record['profile'];
            $wh_id      = ($this->Show_Instructor_WHID) ? "<br />{$record['wh_id']}" : '';
            
            $output = "
                <div style='width:300px;'>
                <table cellpadding='0' cellspacing='5' border='0'>
                <tr>
                    <td valign='bottom' align='left' class='search_current_instructor_profile_picture'>
                        <div class='instructor_holder'>{$picture}</div>
                    </td>
                    <td valign='middle' class='search_current_instructor_profile_name article_title'>{$name}{$wh_id}</td>
                </tr>
                <tr>
                    <td colspan='2' class='search_current_instructor_profile_profile article_all_content'>{$profile}</td>
                </tr>
                </table>
                </div>
                ";
        }
        return $output;
    }

    
} // END CLASS