<?php
class Sessions_UploadVideo extends BaseClass
{
    public $Show_Query                  = false;
    public $Sessions_Id                 = 0;
    public $Upload_Directory            = '/office/session_videos';
    
    public function  __construct()
    {
        parent::__construct();
        
        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Description' => 'Allow instructor to upload recorded audio or video from a session',
        );
        
        $this->SetParameters(func_get_args());
        $this->Sessions_Id          = ($this->GetParameter(0)) ? $this->GetParameter(0) : 0;
        
    } // -------------- END __construct --------------

    
    public function ExecuteAjax()
    {
        switch (Get('action')) {
            
            case 'upload':
                $this->UploadFile();
            break;
            
            case 'check':
                $fileArray = array();
                foreach ($_POST as $key => $value) {
                    if ($key != 'folder') {
                        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $_POST['folder'] . '/' . $value)) {
                            $fileArray[$key] = $value;
                        }
                    }
                }
                echo json_encode($fileArray);
            break;
            
        }
    }
    
    public function Execute()
    {
        $this->AddStyle();
        $this->AddScript();
        
        
        $OBJ_DETAILS    = new Sessions_Details($this->Sessions_Id);
        $OBJ_DETAILS->GetSessionRecord();
        $session        = $OBJ_DETAILS->ShowSessionInformation();
        
        //<a href=\"javascript:$('#file_upload').uploadifyUpload()\">Upload Files</a>
        
        $content        = " <input id='file_upload' name='file_upload' type='file' />";
        $upload         = AddBox_Type1('UPLOAD AUDIO', $content);
        
        $content        = "
        <ol>
            <li>Verify this is the session you want to upload a audio for.</li>
            <li>Click the SELECT FILES button.</li>
            <li>Browse your computer to locate the audio file you want to upload.</li>
            <li>Click the Open button.</li>
            <li>File will be automatically uploaded to the server.</li>
        </ol>
        <ul>
            <li><b>Notes:</b></li>
            <li>Do NOT close window until the 'Completed' message has been displayed - indicating your file was successfully uploaded.</li>
            <li>Maximum File Uppload Size: 500MB</li>
            <li>Allowed File Types: ANY</li>
        </ul>
        ";
        $instructions   = AddBox_Type1('INSTRUCTIONS', $content);
        
        
        $output = " <div style='width:500px;'>&nbsp;</div>
                    <div style='padding:5px; border:1px solid #ccc;'>{$instructions}</div>
                    <br />
                    <div style='padding:5px; border:1px solid #ccc;'>{$session}</div>
                    <br />
                    <div style='padding:5px; border:1px solid #ccc;'>{$upload}</div>
                    <br /><br />";
        
        
        
        echo $output;
    }
    
    public function UploadFile()
    {
        # FUNCTION :: move the file onto the server
        # ==============================================================================
        
        if (!empty($_FILES)) {
            $tempFile       = $_FILES['Filedata']['tmp_name'];
            $targetPath     = $_SERVER['DOCUMENT_ROOT'] . $_REQUEST['folder'] . '/';
            
            $original_name  = $_FILES['Filedata']['name'];
            $name_parts     = explode('.', $original_name);
            $parts_count    = count($name_parts)-1;
            $extension      = $name_parts[$parts_count];
            
            $new_name       = "{$this->Sessions_Id}.{$extension}";
            $targetFile     = str_replace('//','/',$targetPath) . $new_name;
            
            $result = move_uploaded_file($tempFile, $targetFile);
            if (!$result) {
                echo "FAILED TO MOVE FILE";
            }
            echo str_replace($_SERVER['DOCUMENT_ROOT'], '', $targetFile);
            $this->UpdateSessionChecklist();
        }
    }
    
    public function CheckIfFileExistsFromSessionID()
    {
        global $ROOT;
        
        $result = false;
        
        if ($this->Sessions_Id ) {
            $filename   = $this->Sessions_Id . '.*';
            $files      = glob ("{$ROOT}{$this->Upload_Directory}/{$filename}");
            $result     = (isset($files) && count($files) > 0) ? true : false;
        }
        
        return $result;
    }
    
    public function UpdateSessionChecklist($FILENAME='')
    {        
        # update the database record
        $key_values = $this->FormatDataForUpdate(array(
            'instructor_video_uploaded' => 1,
        ));
        $record = $this->SQL->UpdateRecord(array(
            'table'         => $GLOBALS['TABLE_session_checklists'],
            'key_values'    => $key_values,
            'where'         => "`sessions_id`='{$this->Sessions_Id}' AND active=1",
        ));
        if ($this->Show_Query) echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
    }
    
    
    public function AddStyle()
    {
        AddStylesheet("/office/uploadify/uploadify.css");
    }
    
    public function AddScript()
    {
        global $PAGE;
        $script_link = $PAGE['ajaxlink'] . ';action=upload';
        
        AddScriptInclude("/office/uploadify/swfobject.js");
        AddScriptInclude("/office/uploadify/jquery.uploadify.v2.1.4.min.js");
        AddScriptOnReady("
            $('#file_upload').uploadify({
                'uploader'          : '/office/uploadify/uploadify.swf',
                'script'            : '{$script_link}',
                'cancelImg'         : '/office/uploadify/cancel.png',
                'folder'            : '{$this->Upload_Directory}',
                'auto'              : true,
                'removeCompleted'   : false

            });
        ");
    }
    
}  // -------------- END CLASS --------------