<?php
// FILE: /Package/Package_PackageRemove.php
// DESCRIPTION: Used to remove modules from a back-office

class Package_PackageRemove extends Package_PackageBase
{
    # ===================================================================================
    # NOTES
    # ===================================================================================
    # 
    # 
    # ===================================================================================
    # TO DO
    # ===================================================================================
    # Remove blank folders if they are related to this package only
    #
    # Remove the install files as well
    #
    # Write an uninstall log
    #
    # ===================================================================================

    
  
    # VARIABLES FOR FILE INSTALLATION
    # ================================================================================
    public $Remove_Files           = array();      # list of files that will be installed ARRAY() => 'install location/filename' => 'final location/filename'
    public $Module_Name             = '';           # module name
    
    
    # VARIABLES FOR DATABASE TABLE INSTALLATION
    # ================================================================================
    public $Tables                  = array();
    public $Table_List              = array();
    public $Table_Rename_Prepend    = 'OLD_';
    
    
    # VARIABLES FOR ADMIN MODULE RECORD
    # ================================================================================
    public $Admin_Module_Record_Table       = 'admin_modules';
    public $Admin_Module_Record_ClassName   = '';
    
    
    
    
    public function  __construct()
    {
        parent::__construct();
    }

    public function ExecuteRemove()
    {
        # initialize some variables
        $time = date("m-d-y_H-i-s");
        $this->Table_Rename_Prepend = "{$this->Table_Rename_Prepend}{$time}_";
        
        # Install all the files - creating directories if needed
        $this->DeleteFiles();
        
        # Rename any existing database tables
        $this->RenameTables();
       
        # Register the new module into the admin DB
        $this->RemoveAdminModuleRecord();
        
        # Summarize Output
        $this->OutputMessage("<div style='font-size:16px; font-weight:bold; color:blue;'>UNINSTALL PROCESS COMPLETE</div>");
    }
    
    
    
# =====================================================================================================================
# FUNCTIONS FOR DATABASE MANAGEMENT
# =====================================================================================================================
    
    private function RenameTables()
    {
        # FUNCTION: Rename any existing tables that match the ones we're installing
        $this->OutputMessage ("Deactivating any active module tables.");
        
        foreach ($this->Table_List AS $table) {
            # NOTE: this method maks a DB call for each loop - might want to modify to a single call
            $table_exist = $this->SQL->TableExists($table);
            if ($table_exist) {
                $this->OutputMessage("Table `{$table}` exists. Renaming to `{$this->Table_Rename_Prepend}{$table}`");
                $query = "ALTER TABLE `{$table}` RENAME `{$this->Table_Rename_Prepend}{$table}`;";
                $this->RunQuery($query);
            }
        }
    }
    
    public function RemoveAdminModuleRecord()
    {
        # 1. Deactivate any original records
        # ===========================================================
        $this->SQL->UpdateRecord(array(
                'table'         => $this->Admin_Module_Record_Table,
                'key_values'    => "active=0",
                'where'         => "`class_name`='{$this->Admin_Module_Record_ClassName}'"
            ));
        $q = $this->Db_Last_Query;
        $this->OutputMessage ("Deactivating admin_module records. QUERY ==> $q");
    }

    private function RunQuery($QUERY)
    {
        if (is_array($QUERY)) {
            foreach ($this->Tables AS $q) {
                $this->OutputMessage("Creating Database Table");
                $this->SQL->Query('Database::TableCreate', $q);
                $this->OutputMessage($q);
            }
        } else {
            $this->SQL->Query('Lib_Install::RunQuery()', $QUERY);
        }
    }
    
    
    
# =====================================================================================================================
# FUNCTIONS FOR FILE AND FOLDER CREATION
# =====================================================================================================================

    private function DeleteFiles()
    {
        $this->OutputMessage ("Deleting module files.");
        
        $root = $_SERVER['DOCUMENT_ROOT'];
        
        echo '<br />Remove_Files ==> '.ArrayToStr($this->Remove_Files);
        
        foreach ($this->Remove_Files AS $filename) 
        {
            # remove the file
            $destination    = "{$root}/{$filename}";
            $delete_result  = unlink($destination);
            
            if (!$delete_result) {
                $this->OutputMessage ("Unable to delete file: {$destination}", 'error');
            } else {
                $this->OutputMessage ("File deleted: {$destination}");
            }
            
            # remove the directory if it is empty
            
            
        }        
    }
 
}