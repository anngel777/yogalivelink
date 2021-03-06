<?php
// FILE: /Package/Package_PackageBase.php
// DESCRIPTION: Base functions for package creation, installation, and removal

class Package_PackageBase
{
    public $SQL                        = '';
    
    # DEBUG VARIABLES
    # ================================================================================
    public $show_all_query              = false;
    public $show_all_array              = false;
    
    
    # OUTPUT VARIABLES
    # ================================================================================
    public $Output_Message_Error       = '';
    public $Output_Message_Content     = '';
    public $Show_Output_Message        = true;
    
    
    
    
    public function  __construct()
    {
        $this->SQL = Lib_Singleton::GetInstance('Lib_Pdo');
    }



# =====================================================================================================================
# FUNCTIONS FOR FILE AND FOLDER CREATION
# =====================================================================================================================
    
    public function makeAll($dir, $mode = 0777, $recursive = true) 
    {
        /**
        * Create a new directory, and the whole path.
        *
        * If  the  parent  directory  does  not exists, we will create it,
        * etc.
        * @todo
        *     - PHP5 mkdir functoin supports recursive, it should be used
        * @author baldurien at club-internet dot fr 
        * @param string the directory to create
        * @param int the mode to apply on the directory
        * @return bool return true on success, false else
        * @previousNames mkdirs
        */

        if( is_null($dir) || $dir === "" ){
            return FALSE;
        }
        
        if( is_dir($dir) || $dir === "/" ){
            return TRUE;
        }
        if( $this->makeAll(dirname($dir), $mode, $recursive) ){
            return mkdir($dir, $mode);
        }
        return FALSE;
    }

    public function smartCopy($source, $dest, $options=array('folderPermission'=>0755,'filePermission'=>0755))
    {
        /**
        * Copies file or folder from source to destination, it can also do
        * recursive copy by recursively creating the dest file or directory path if it wasn't exist
        * Use cases:
        * - Src:/home/test/file.txt ,Dst:/home/test/b ,Result:/home/test/b -> If source was file copy file.txt name with b as name to destination
        * - Src:/home/test/file.txt ,Dst:/home/test/b/ ,Result:/home/test/b/file.txt -> If source was file Creates b directory if does not exsits and copy file.txt into it
        * - Src:/home/test ,Dst:/home/ ,Result:/home/test/** -> If source was directory copy test directory and all of its content into dest      
        * - Src:/home/test/ ,Dst:/home/ ,Result:/home/**-> if source was direcotry copy its content to dest
        * - Src:/home/test ,Dst:/home/test2 ,Result:/home/test2/** -> if source was directoy copy it and its content to dest with test2 as name
        * - Src:/home/test/ ,Dst:/home/test2 ,Result:->/home/test2/** if source was directoy copy it and its content to dest with test2 as name
        * @todo
        *  - Should have rollback so it can undo the copy when it wasn't completely successful
        *  - It should be possible to turn off auto path creation feature f
        *  - Supporting callback function
        *  - May prevent some issues on shared enviroments : <a href="http://us3.php.net/umask" title="http://us3.php.net/umask">http://us3.php.net/umask</a>
        * @param $source //file or folder
        * @param $dest ///file or folder
        * @param $options //folderPermission,filePermission
        * @return boolean
        */
    
        $result=false;
        
        //For Cross Platform Compatibility
        if (!isset($options['noTheFirstRun'])) {
            $source=str_replace('\\','/',$source);
            $dest=str_replace('\\','/',$dest);
            $options['noTheFirstRun']=true;
        }
        
        if (is_file($source)) {
            if ($dest[strlen($dest)-1]=='/') {
                if (!file_exists($dest)) {
                    $this->makeAll($dest,$options['folderPermission'],true);
                }
                $__dest=$dest."/".basename($source);
            } else {
                $__dest=$dest;
            }
            $result=copy($source, $__dest);
            chmod($__dest,$options['filePermission']);
            
        } elseif(is_dir($source)) {
            if ($dest[strlen($dest)-1]=='/') {
                if ($source[strlen($source)-1]=='/') {
                    //Copy only contents
                } else {
                    //Change parent itself and its contents
                    $dest=$dest.basename($source);
                    @mkdir($dest);
                    chmod($dest,$options['filePermission']);
                }
            } else {
                if ($source[strlen($source)-1]=='/') {
                    //Copy parent directory with new name and all its content
                    @mkdir($dest,$options['folderPermission']);
                    chmod($dest,$options['filePermission']);
                } else {
                    //Copy parent directory with new name and all its content
                    @mkdir($dest,$options['folderPermission']);
                    chmod($dest,$options['filePermission']);
                }
            }

            $dirHandle=opendir($source);
            while($file=readdir($dirHandle))
            {
                if($file!="." && $file!="..")
                {
                    $__dest=$dest."/".$file;
                    $__source=$source."/".$file;
                    //echo "$__source ||| $__dest<br />";
                    if ($__source!=$dest) {
                        $result=$this->smartCopy($__source, $__dest, $options);
                    }
                }
            }
            closedir($dirHandle);
            
        } else {
            $result=false;
        }
        return $result;
    }

    
    
# =====================================================================================================================
# FUNCTIONS FOR DEBUGGING
# =====================================================================================================================

    public function ShowLastQuery()
    {
        if ($this->show_all_query) {
            echo "<br />Query --> " . $this->SQL->Db_Last_Query . "<br /><br />";
        }
    }
    
    public function ShowArray($array, $name='', $force_show=false)
    {
        if ($this->show_all_array || $force_show) {
            echo "<br /><br /><hr>{$name}<hr>";
            echo ArrayToStr($array);
            echo '<hr><br /><br />';
        }
    }
    
    public function JSAlert($string)
    {
        $output = "
            <script type='text/javascript'>
                alert('{$string}');
            </script>";
        echo $output;
    }
    
    public function ShowVariables()
    {
        echo "<br />Install_Directory ==> {$this->Install_Directory}";
        echo "<br />Install_Files ==> {$this->Install_Files}";
        echo "<br />Module_Name ==> {$this->Module_Name}";
        echo "<br />Install_Folder ==> {$this->Install_Folder}";
        echo "<br />Tables ==> " . ArrayToStr($this->Tables);
        
    }
    
    public function OutputMessage($MSG, $TYPE)
    {
        switch($TYPE) {
            case 'error':
                $this->Output_Message_Error = true;
                $output = "<div style='font-weight:bold; color:red;'>{$MSG}</div>";
            break;
            case 'title':
                $this->Output_Message_Error = true;
                $output = "<div style='font-weight:bold; font-size:16px; color:blue; background-color:#ccc; text-transform:capitalize;'>{$MSG}</div>";
            break;
            default:
                $output = "<div style='font-weight:normal; color:#000;'>{$MSG}</div>";
            break;
        }
        
        $this->Output_Message_Content .= "<br />{$output}<hr>";
        
        if ($this->Show_Output_Message) {
            #echo "<br />{$output}";
        }
    }
   
    public function OutputSummary()
    {
        if ($this->Show_Output_Message) {
            $output = "
                <html><body>
                <div style='width:500px; border:1px solid #000; padding:20px;'>
                    $this->Output_Message_Content
                </dv>
                </body></html>
                ";
            echo $output;
        }
    }

}