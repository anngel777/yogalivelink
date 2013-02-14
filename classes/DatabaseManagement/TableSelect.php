<?php
class DatabaseManagement_TableSelect extends BaseClass
{
    public $no_show_tables          = array('admin_modules', 'admin_log');  // List of tables not to display
    public $dialog_id               = 0;
    
    public function  __construct()
    {
        parent::__construct();
        
        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Description' => 'Backoffice Database Management - Show all tables in database',
        );

    } // -------------- END __construct --------------

    
    public function GetTableInfo($table='', $show_array=false)
    {
        # FUNCTION :: Get the field information for a given table
        
        $table_info = $this->SQL->TableFieldInfo($table);
        if ($show_array) echo ArrayToStr($table_info);
        return $table_info;
    }
    
    public function ShowAllTables()
    {
        # FUNCTION :: Show all the tables in the database
        
        $function   = 'GET ALL TABLES';
        $query      = 'SHOW TABLES';
        $results    = $this->SQL->Query($function, $query);
        
        $OUTPUT = "
            <h1>DATABASE TABLE SELECT</h1><br />
            <br /><br />
            <ul>";
        
        foreach ($results as $table) {
            foreach ($table as $var => $val) {
                if (!in_array($val, $this->no_show_tables)) {
                    $OUTPUT .= "<li><a href='db_table_select;DIALOGID={$this->dialog_id};table={$val}'>{$val}</a></li>";
                }
            }
        }

        $OUTPUT .= "</ul>";
        echo $OUTPUT;
    }
    

}  // -------------- END CLASS --------------