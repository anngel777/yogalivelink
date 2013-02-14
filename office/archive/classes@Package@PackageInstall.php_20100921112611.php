<?php
// FILE: /Lib/Lib_Install.php
// DESCRIPTION: Used to install modules into a back-office

class Package_PackageInstall extends Package_PackageBase
{
    
    
    # VARIABLES FOR FILE INSTALLATION
    # ================================================================================
    public $Install_Directory       = 'install';    # root install directory folder (default: $root/install/)
    public $Install_Folder          = '';           # folder containing this specific module inside $install_directory
    public $Install_Files           = array();      # list of files that will be installed ARRAY() => 'install location/filename' => 'final location/filename'
    public $Module_Name             = '';           # module name
    
    
    # VARIABLES FOR DATABASE TABLE INSTALLATION
    # ================================================================================
    public $Tables                  = array();
    public $Table_List              = array();
    public $Table_Rename_Prepend    = 'OLD_';
    public $tbl_standard_fields     = "
        `active` tinyint(4) NOT NULL default '1',
        `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
        `created` datetime NOT NULL,
    ";
    
    
    # VARIABLES FOR ADMIN MODULE RECORD
    # ================================================================================
    public $Module_Queries                  = array();               # queries run to insert admin_module record
    public $Admin_Module_Record_Table       = 'admin_modules';
    public $Admin_Module_Record_ClassName   = '';
    
    

    
    
    public function  __construct()
    {
        parent::__construct();
    }

    public function ExecuteInstall()
    {
        # initialize some variables
        $this->OutputMessage ("Initializing Variables.", 'title', 1);
        $this->Install_Folder = $this->Module_Name;
        $time = date("m-d-y_H-i-s");
        $this->Table_Rename_Prepend = "{$this->Table_Rename_Prepend}{$time}_";
        $this->OutputMessage ("DONE", '');
        
        # Install all the files - creating directories if needed
        $this->OutputMessage ("Copying files.", 'title', 1);
        $this->CopyFiles();
        
        # Rename any existing database tables
        $this->OutputMessage ("Rename existing database tables.", 'title', 1);
        $this->RenameTables();
        
        # Create new database tables - verify each is there afterwards
        $this->OutputMessage ("Create new database tables.", 'title', 1);
        $this->CreateTables();
        
        # Insert testing data into database tables
        
        
        # Register the new module into the admin DB
        # NOTE: Need to check if record exists already - and disable it???
        $this->OutputMessage ("Processing admin_module records.", 'title', 1);
        $this->InsertAdminModuleRecord();
        
        # Summarize Output
        $this->OutputMessage ("Installation Process Complete.", 'title', 1);
        
        # Output summary to screen
        $this->OutputSummary();
    }
    

 
 
 

 
 
 
    
 
 
# =====================================================================================================================
# FUNCTIONS FOR DATABASE MANAGEMENT
# =====================================================================================================================
    
    private function CreateTables()
    {
        foreach ($this->Tables AS $query) {
            $this->OutputMessage("Creating Database Table");
            $result = $this->SQL->Query('CreateTables()', $query);
            
            if ($result) {
                $q = $this->SQL->Db_Last_Query;
                $this->OutputMessage ("PASSED. <br />Query Used ==> $q", '');
            } else {
                $q = $this->SQL->Db_Last_Query;
                $this->OutputMessage ("FAILED. <br />Query Used ==> $q", 'error');
            }
        }
    }
    
    private function RenameTables()
    {
        # FUNCTION: Rename any existing tables that match the ones we're installing
        
        foreach ($this->Table_List AS $table) {
            # NOTE: this method maks a DB call for each loop - might want to modify to a single call
            $table_exist = $this->SQL->TableExists($table);
            if ($table_exist) {
                $this->OutputMessage("Table `{$table}` Already Exists. Renaming to `{$this->Table_Rename_Prepend}{$table}`");
                $query = "ALTER TABLE `{$table}` RENAME `{$this->Table_Rename_Prepend}{$table}`;";
                $result = $this->SQL->Query('RenameTables()', $query);
                
                if ($result) {
                    $this->OutputMessage ("PASSED.", '');
                } else {
                    $q = $this->SQL->Db_Last_Query;
                    $this->OutputMessage ("FAILED. <br />Query Used ==> $q", 'error');
                }
            }
        }
    }
    
    public function InsertAdminModuleRecord()
    {
        # 1. Deactivate any original records
        # ===========================================================
        $this->OutputMessage ("Deactivate existing records.", 'title', 1);
        $result = $this->SQL->UpdateRecord(array(
                'table'         => $this->Admin_Module_Record_Table,
                'key_values'    => "active=0",
                'where'         => "`class_name`='{$this->Admin_Module_Record_ClassName}'"
            ));
        
        if ($result) {
            $this->OutputMessage ("PASSED.", '');
        } else {
            $q = $this->SQL->Db_Last_Query;
            $this->OutputMessage ("FAILED. <br />Query Used ==> $q", 'error');
        }
        
        
        # 2. Insert new record(s)
        # ===========================================================
        $this->OutputMessage ("Insert new records.", 'title', 1);
        foreach ($this->Module_Queries AS $query) {
            $result = $this->SQL->Query('InsertAdminModuleRecord()', $query);
            
            if ($result) {
                $this->OutputMessage ("PASSED.", '');
            } else {
                $q = $this->SQL->Db_Last_Query;
                $this->OutputMessage ("FAILED. <br />Query Used ==> $q", 'error');
            }    
        }
        
        
    }
    
    
    
# =====================================================================================================================
# FUNCTIONS FOR FILE AND FOLDER CREATION
# =====================================================================================================================

    private function CopyFiles()
    {
        $root = $_SERVER['DOCUMENT_ROOT'];
        
        foreach ($this->Install_Files AS $source => $destination) 
        {
            $source         = "{$root}/{$this->Install_Directory}/{$this->Install_Folder}/{$source}";
            $destination    = "{$root}/{$destination}";
            $result         = $this->smartCopy($source, $destination);
            
            $this->OutputMessage ("Copying File:<br /><b>Source</b> ==> {$source} <br /><b>Destination</b> ==> {$destination}.", '');
            
            if ($result) {
                $this->OutputMessage ("PASSED.", '');
            } else {
                $this->OutputMessage ("FAILED.", 'error');
            }
        }
    }
    
    

}