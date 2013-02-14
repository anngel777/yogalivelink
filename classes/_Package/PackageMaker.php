<?php
// FILE: /Lib/Lib_Install.php
// DESCRIPTION: Used to install modules into a back-office

class Package_PackageMaker extends Package_PackageBase
{
    # ===================================================================================
    # NOTES
    # ===================================================================================
    # 
    # 
    # ===================================================================================
    # TO DO
    # ===================================================================================
    # The function OutputMessage() needs to track errors and successes into a series 
    #   of variables and output the status at the end of process in a better looking way
    #
    # Need to create the proper information for info.php
    #
    # GET THE ADMIN_MODULE_ROLES DATA - to create a role named for this package
    # ===================================================================================



    private $Install_File_Content_FILES             = '';
    private $Install_File_Content_TABLES            = '';
    private $Install_File_Content_TABLE_LIST        = '';
    private $Install_File_Content_TABLE_QUERIES     = '';
    private $Install_File_Content_ADMIN             = '';
    private $Install_File_Content_ADMIN_QUERIES     = '';
    private $Install_File_Content_MODULE_INFO       = '';
    
    private $Remove_File_Content_FILES              = '';
    
    # VARIABLES FOR FILE INSTALLATION
    # ================================================================================
    public $Module_Name             = '';                   # module name
    public $Package_Directory       = 'packages';           # directory to create the packages in (default: $root/install/)
    public $Package_Folder          = '';                   # folder containing this specific module inside $Package_Directory # gets initialized later
    public $Package_Files           = array();              # list of files that will be copied ARRAY() => 'current location/filename' => '$Package_Directory/$Package_Folder/folder/filename'
    
    
    # VARIABLES FOR info.php FILE
    # ================================================================================
    public $Info_Module_Created_By      = '';
    public $Info_Module_Description     = '';
    public $Info_Module_Title           = '';
    public $Package_Created_By          = '';
    
    
    # VARIABLES FOR DATABASE TABLE INSTALLATION
    # ================================================================================
    public $Table_Rename_Prepend        = 'OLD_';
    public $Table_List                  = array();
    
    
    # VARIABLES FOR ADMIN MODULE RECORD
    # ================================================================================
    public $Admin_Module_Record_Table       = 'admin_modules';
    public $Admin_Module_Record_ClassName   = '';               # gets initialized later
    
    
    
    public function  __construct()
    {
        parent::__construct();
    }

    public function ExecutePakageCreation()
    {
        # initialize some variables
        $this->Package_Folder               = $this->Module_Name;
        $this->Admin_Module_Record_ClassName     = $this->Module_Name;
    
        # rename any existing package folder - with a timestamp
        $this->RenamePackage();
    
        # Copy all the files - creating directories if needed
        $this->CopyFiles();
        
        # Get queries for creating database tables
        $this->GetTableRecords();
        
        # Get queries for admin table registration
        $this->GetAdminModuleRecords();
        
        # Create the install.php file
        $this->FileCreateInstall();
        
        # create the remove.php file
        $this->FileCreateRemove();
        
        # Create the info.php file
        $this->FileCreateInfo();
        
        
        
        
        
        # Summarize Output
        $this->OutputMessage ("Package Creation Process Complete.", 'title', 1);
        
        # Output summary to screen
        $this->OutputSummary();
    }
    

 
 
 

 
 private function RenamePackage()
 {
    # FUNCTION: Rename any existing package with the same name
    
    $root           = $_SERVER['DOCUMENT_ROOT'];
    $folder_orig    = "{$root}/{$this->Package_Directory}/{$this->Package_Folder}";
    
    $time           = date("m.d.y - H:i:s");
    $new_name       = "{$this->Package_Folder} - {$time}";
    $folder_new     = "{$root}/{$this->Package_Directory}/{$new_name}";
    
    $result = rename($folder_orig, $new_name);
    
    #echo "<br />ORIGINAL FILENAME ==> ".$folder_orig;
    #echo "<br />NEW FILENAME ==> ".$folder_new;
    
    if ($result) {
        $this->OutputMessage("Package folder already exists - folder renamed to '{$new_name}'");
    }
    
 }
 
    
 
 
# =====================================================================================================================
# FUNCTIONS FOR DATABASE MANAGEMENT
# =====================================================================================================================
    
    private function GetAdminModuleRecords()
    {
        $this->OutputMessage("FUNCTION::GetAdminModuleRecords()");
        
        # Get all the records
        # ===========================================================
        $records = $this->SQL->GetArrayAll(array(
                'table'     => $this->Admin_Module_Record_Table,
                'keys'      => "*",
                'where'     => "active=1 AND class_name='{$this->Admin_Module_Record_ClassName}'",
            ));


        if ($records) {
            foreach ($records AS $record)
            {
                $this->OutputMessage("Records found in admin_modules table");
                $query = "INSERT INTO `$this->Admin_Module_Record_Table` VALUES ('', '{$record['category']}', '{$record['is_folder']}', '{$record['title']}', '{$record['filename']}', '{$record['class_name']}', '{$record['image']}', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00');";
            
                $output = '
                    array_push ($Obj->Module_Queries, "
                        '.$query.'
                    ");';
            
                # Store the query for the final file output
                # ===========================================================
                $this->Install_File_Content_ADMIN_QUERIES .= $output;
            }
        } else {
            $this->OutputMessage("NO Records found in admin_modules table. QUERY USED ==> {$this->SQL->Db_Last_Query}", 'error');
        }
        
        # Store the necessary info for deactivating amin roles
        # ===========================================================
        $this->Install_File_Content_ADMIN = '
            $Obj->Admin_Module_Record_ClassName = "'.$this->Admin_Module_Record_ClassName.'";
        ';
        
    }

    private function GetTableRecords()
    {
        $this->OutputMessage("FUNCTION::GetTableRecords()");
        
        $query_list = '';
        foreach ($this->Table_List AS $table) {
        
            $this->OutputMessage("Checking for existance of Table `{$table}`.");
        
            # NOTE: this method maks a DB call for each loop - might want to modify to a single call
            $table_exist = $this->SQL->TableExists($table);
            if ($table_exist) {
                $this->OutputMessage("Table `{$table}` EXIST getting creation query.");
                
                # Get the SQL query needed to re-create this table
                $query = $this->TableInformation($table);
                ###echo "<br />Table Creation Query ====> $query <br /><br /><br />";
                
            
                $query_list .= $query;
                
                
                
            
                # Create the array_push line
                $this->Install_File_Content_TABLE_LIST .= 'array_push ($Obj->Table_List, "'.$table.'");
                ';
                
            } else {
                $this->OutputMessage("Table `{$table}` DOES NOT EXIST. Unable to create a query.", 'error');
            }
        }
        
        # WRITE ALL THE QUERIES INTO ONE QUERY
            $output = '
                    array_push ($Obj->Tables, "
                        '.$query_list.'
                    ");';
            # Create the insert query
            $this->Install_File_Content_TABLE_QUERIES .= $output;
    }
        
 	private function TableInformation($table) 
    {
        # Get the field info
        $columns = $this->SQL->TableFieldInfo($table);
        
        // Structure Header
        $structure .= "-- \n";
        $structure .= "-- Table structure for table `{$table}` \n";
        $structure .= "-- \n\n";
        
        // Dump Structure
        $structure .= "DROP TABLE IF EXISTS `{$table}`; \n";
        $structure .= "CREATE TABLE `{$table}` (\n";
        
        # Form the table column data
        #unset($index);
        $primary_key = '';
        foreach ($columns AS $col)
        {
            $primary_key    = ($col['Key'] == 'PRI') ? $col['Field'] : $primary_key;
            $default        = ($col['Default'] == 'CURRENT_TIMESTAMP') ? $col['Default'] : "'{$col['Default']}'";
        
            $structure .= "  `{$col['Field']}` {$col['Type']}";
            $structure .= (!empty($col['Default'])) ? " DEFAULT $default" : false;
            $structure .= ($col['Null'] != "YES") ? " NOT NULL" : false;
            $structure .= (!empty($col['Extra'])) ? " {$col['Extra']}" : false;
            $structure .= ",\n";
        }
        
        $structure = ereg_replace(",\n$", "", $structure);
        $end_line = "ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ";
        $structure .= ", PRIMARY KEY  (`{$primary_key}`)";
        $structure .= "\n) {$end_line};\n\n";
      
    
        return $structure;
    
    
    
        
        # =========================== =========================== ===========================
        # OUTPUT THE DATA FROM THE TABLE
        # =========================== =========================== ===========================
        /*
        // Header
        $structure .= "-- \n";
        $structure .= "-- Dumping data for table `$table` \n";
        $structure .= "-- \n\n";

        // Dump data
        unset($data);
        $result     = mysql_query("SELECT * FROM `$table`");
        $num_rows   = mysql_num_rows($result);
        $num_fields = mysql_num_fields($result);

        for ($i = 0; $i < $num_rows; $i++) {

            $row = mysql_fetch_object($result);
            $data .= "INSERT INTO `$table` (";

            // Field names
            for ($x = 0; $x < $num_fields; $x++) {

                $field_name = mysql_field_name($result, $x);

                $data .= "`{$field_name}`";
                $data .= ($x < ($num_fields - 1)) ? ", " : false;

            }

            $data .= ") VALUES (";

            // Values
            for ($x = 0; $x < $num_fields; $x++) {
                $field_name = mysql_field_name($result, $x);

                $data .= "'" . str_replace('\"', '"', mysql_escape_string($row->$field_name)) . "'";
                $data .= ($x < ($num_fields - 1)) ? ", " : false;

            }

            $data.= ");\n";
        }

        $data.= "\n";

        $dump .= $structure . $data;
        $dump .= "-- --------------------------------------------------------\n\n";

        return $dump;
    */
    }


    
# =====================================================================================================================
# FUNCTIONS FOR FILE AND FOLDER CREATION
# =====================================================================================================================

    private function CopyFiles()
    {
        $root = $_SERVER['DOCUMENT_ROOT'];
        
        foreach ($this->Package_Files AS $source => $destination) 
        {
            $t_source       = "{$root}/{$source}";
            $t_destination  = "{$root}/{$this->Package_Directory}/{$this->Package_Folder}/{$destination}";
            $copy_result    = $this->smartCopy($t_source, $t_destination);
            
            if (!$copy_result) {
                $this->OutputMessage ("<b style='color:red;'>FILE DID NOT COPY:</b <br /><b>Source</b> ==> {$t_source} <br /><b>Destination</b> ==> {$t_destination} <br />");
            } else {
                $this->OutputMessage ("<b style='color:blue;'>FILE COPIED:</b <br /><b>Source</b> ==> {$t_source} <br /><b>Destination</b> ==> {$t_destination} <br />");
            }
            
            
            # write the contents for the file (install.php)
            $temp                   = explode('/', $source);
            $count                  = count($temp);
            
            $source_filename        = $temp[$count-1];
            $source_filename_length = strlen($source_filename);
            
            $temp_length            = -($source_filename_length);
            $destination_new        = substr($source, 0, $temp_length);
            $source_new             = "{$destination}{$source_filename}";
            
            $this->Install_File_Content_FILES .= "'{$source_new}' => '{$destination_new}',
            ";
            
            
            # write the contents for the file (remove.php)
            $delete_file = "{$destination_new}{$source_filename}";
            $this->Remove_File_Content_FILES .= "'{$delete_file}',
            ";
            
        }
        
        ### TO DO ###
        # determine if folder exists - create if not there
        # determine if file exists - copy if not - ask to overwrite if YES
    }
 

    
# =====================================================================================================================
# FUNCTIONS FOR CREATING FILES
# =====================================================================================================================

    private function FileCreateInstall()
    {
        $output = '<?php
        
        $ConfigPath = $_SERVER[\'DOCUMENT_ROOT\'];
        require_once $_SERVER[\'DOCUMENT_ROOT\']."/lib/page_helper.php";
        include "$ROOT/wo/site_office_config.php";
        include "$LIB/form_helper.php";
        include "$LIB/custom_error.php";
        include "$ROOT/classes/autoload.php";
        
        $Obj = new Package_PackageInstall();
        
        if ($Obj) {
        
            # ========================================================================================================
            # FILE AND FOLDER INFORMATION
            # ========================================================================================================
            # == $Obj->Install_Files = ARRAY
            # == install source location/filename => final destination location/
            $Obj->Install_Files = array(
                '.$this->Install_File_Content_FILES.'
            );
            
            '.$this->Install_File_Content_MODULE_INFO.'
            
            
            # ========================================================================================================
            # DATABASE INFORMATION
            # ========================================================================================================
            # Note: these tables will be renamed if they already exist
            '.$this->Install_File_Content_TABLE_LIST.'
            
            '.$this->Install_File_Content_TABLE_QUERIES.'
            
            
            # ========================================================================================================
            # ADMIN MODULE RECORD INFORMATION
            # ========================================================================================================
            '.$this->Install_File_Content_ADMIN.'
            
            $Obj->Module_Name = "'.$this->Module_Name.'";
            
            '.$this->Install_File_Content_ADMIN_QUERIES.'
            
            
            # ========================================================================================================
            # RUN THE INSTALLATION
            # ========================================================================================================
            $Obj->ExecuteInstall();
            #$Obj->ShowVariables();
            
            
        } else {
            echo "
                <div style=\'color:red;\'>
                UNABLE TO LOAD THE INSTALL CLASS
                </div>
                ";
        }
        ?>
        ';
        
        $this->CreateFile('install.php', $output);
    }
 
    private function FileCreateRemove()
    {
        $output = '<?php
        
        $ConfigPath = $_SERVER[\'DOCUMENT_ROOT\'];
        require_once $_SERVER[\'DOCUMENT_ROOT\']."/lib/page_helper.php";
        include "$ROOT/wo/site_office_config.php";
        include "$LIB/form_helper.php";
        include "$LIB/custom_error.php";
        include "$ROOT/classes/autoload.php";
        
        $Obj = new Package_PackageRemove();
        
        if ($Obj) {
        
            # ========================================================================================================
            # FILE AND FOLDER INFORMATION
            # ========================================================================================================
            # == $Obj->Remove_Files = ARRAY
            $Obj->Remove_Files = array(
                '.$this->Remove_File_Content_FILES.'
            );
            
            # ========================================================================================================
            # DATABASE INFORMATION
            # ========================================================================================================
            # Note: these tables will be renamed if they already exist
            '.$this->Install_File_Content_TABLE_LIST.'
            
            # ========================================================================================================
            # ADMIN MODULE RECORD INFORMATION
            # ========================================================================================================
            '.$this->Install_File_Content_ADMIN.'
            $Obj->Module_Name = "'.$this->Module_Name.'";
            
            # ========================================================================================================
            # RUN THE INSTALLATION
            # ========================================================================================================
            $Obj->ExecuteRemove();
            
        } else {
            echo "
                <div style=\'color:red;\'>
                UNABLE TO LOAD THE REMOVE CLASS
                </div>
                ";
        }
        ?>
        ';
        
        $this->CreateFile('remove.php', $output);
    }
    
    private function FileCreateInfo()
    {
        $time       = date("m.d.y - H:i:s");
        $output     = '
        <?php
        
        $info[\'module_name\']          = "'.$this->Module_Name.'";
        $info[\'module_created_by\']    = "'.$this->Info_Module_Created_By.'";
        $info[\'module_title\']         = "'.$this->Info_Module_Title.'";
        $info[\'module_description\']   = "'.$this->Info_Module_Description.'";
        
        $info[\'package_date\']         = "'.$time.'";
        $info[\'package_created_by\']   = "'.$this->Package_Created_By.'";
        $info[\'package_errors\']       = "'.$this->Output_Message_Error.'";
        $info[\'package_log\']          = "'.$this->Output_Message_Content.'";
        
        ?>
        ';
        
        $this->CreateFile('info.php', $output);
    }
    
    private function CreateFile($FILENAME, $CONTENT)
    {
        $this->OutputMessage("Creating File: ({$filename})");
    
        # create the file
        $root       = $_SERVER['DOCUMENT_ROOT'];
        $filename   = "{$root}/{$this->Package_Directory}/{$this->Package_Folder}/{$FILENAME}";
        
        if (!$handle = fopen($filename, 'w')) {
            $this->OutputMessage("Cannot open file ({$filename})");
            exit;
        } else {
            $this->OutputMessage("File opened: ({$filename})");
        }
        
        # output contents to file
        if (fwrite($handle, $CONTENT) === FALSE) {
            $this->OutputMessage("Cannot write to file ({$filename})");
            exit;
        } else {
            $this->OutputMessage("File written: ({$filename})");
        }
        
        # close the file
        fclose($handle);
    }
    
    
    
    
    
    
    
}