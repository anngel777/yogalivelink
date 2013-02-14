<?php
class Utilities_LoginLog
{
    public $Log_Delimiter                   = "|";                                  // Character that breaks each section of log line
    public $Login_Tracking_Filename         = "/office/logs/login_extended.dat";    // Name of the file to track logins
    public $Log_Array                       = array();                              // will hold the log rows
    
    public function  __construct()
    {
        $this->ClassInfo = array(
            'Created By'    => 'Richard Witherspoon',
            'Created'       => '2009-08-09',
            'Updated By'    => '',
            'Updated'       => '',
            'Description'   => 'Read the login log file (as a text file)',
            'Version'       => '1.0.0',
        );
        
        /* ============= UPDATE NOTES =============
            2009-08-09 (v1.0.0) -> Created this file
        
        
        ======================================== */
    }
    
    public function Execute()
    {
        // FUNCTION :: Handle all actions needed to open and output the log
        
        $result = $this->ReadLog();                         // open the log file
        if ($result) {
            echo $this->ConvertLogToTable();                // output the logfile as a table
        }
    }
    
    public function ReadLog()
    {
        global $ROOT;
        
        $log_arr = array();
        $log_arr[] = array(
            'timestamp' => 'Timestamp',
            'action'    => 'Action Attempted',
            'username'  => 'Username',
            'password'  => 'Password',
            'message'   => 'Message',
            'ip'        => 'IP Address',
            'file'      => 'File Accesed',
        );
        
        
        $fh = fopen($ROOT . $this->Login_Tracking_Filename, "r");               // open the file
        if($fh) { 
            while(!feof($fh)) { 
                $line = fgets($fh);                                             // get the file line
                if ($line) {
                    $parts = explode($this->Log_Delimiter, $line);              // explode into individual parts
                    $log_arr[] = array(                                         // build array of log parts
                        'timestamp' => (isset($parts[0])) ? $parts[0] : '',
                        'action'    => (isset($parts[1])) ? $parts[1] : '',
                        'username'  => (isset($parts[2])) ? $parts[2] : '',
                        'password'  => (isset($parts[3])) ? $parts[3] : '',
                        'message'   => (isset($parts[4])) ? $parts[4] : '',
                        'ip'        => (isset($parts[5])) ? $parts[5] : '',
                        'file'      => (isset($parts[6])) ? $parts[6] : '',
                    );
                }
            }
            $this->Log_Array = $log_arr;                                        // assign array to class variable
            fclose($fh);                                                        // close the file
            return true;                                                        // return success
        } else { 
           // unable to open file for reading 
           echo "<br />ERROR :: Class " . get_class($this) . " :: Function ReadLog() :: Unable to open log file";
           return false;                                                        // return failure
        }
    }
    
    public function ConvertLogToTable()
    {
        // FUNCTION :: Convert logfile array to a sortable table
        
        
        AddScriptOnReady('$("#myTable").tablesorter( {sortList: [[0,0]]} ); ');     // initialize JQuery info
        AddScriptInclude("/jslib/tablesort/jquery.tablesorter.min.js");             // include JS file
        AddStylesheet("/jslib/tablesort/themes/blue/style.css");                    // include CSS file
        
        $output = "";
        $curline = 0;
        
        
        $output .= '<table id="myTable" class="tablesorter">'; 
        foreach ($this->Log_Array AS $line) {
            
            $cell_type = ($curline == 0) ? 'th' : 'td';
            
            switch ($curline) {
                case 0:
                    $section_top = '<thead>';
                    $section_end = '</thead>';
                break;
                case 1:
                    $section_top = '<tbody>';
                    $section_end = '';
                break;
                default:
                    $section_top = '';
                    $section_end = '';
                break;
            }
            
            $output .= $section_top;
            $output .= "<tr>";
            foreach ($line AS $key => $value) {
                $output .= "<{$cell_type}>$value</{$cell_type}>";
            }
            $output .= "</tr>";
            $output .= $section_end;
            
            $curline++;
        }
        $output .= '</tbody>';
        $output .= '</table>';
        
        return $output;
    }
    
} // end class