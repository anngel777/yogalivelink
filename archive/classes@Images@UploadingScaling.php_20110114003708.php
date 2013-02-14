<?php
class Images_UploadingScaling
{
    public $return_window_id        = '';
    public $return_object_id        = '';


    public $upload_dir              = "images";         // The directory for the images to be saved in
    public $image_handling_file     = "";               // The location of the file that will handle the upload and resizing (RELATIVE PATH ONLY!)
    public $large_image_prefix      = "resize_"; 		// The prefix name to large image
    public $thumb_image_prefix      = "thumbnail_";		// The prefix name to the thumb image
    public $max_file                = "1"; 				// Maximum file size in MB
    public $max_width               = "500";			// Max width allowed for the large image
    public $thumb_width             = null;			// Width of thumbnail image
    public $thumb_height            = null;			// Height of thumbnail image
    public $thumb_ratio             = 0;

    public $page_width              = "500";
    public $uploaded_preview_width  = "300";
    public $uploaded_height         = "200";
    
    
    public $upload_path             = '';               // The path to where the image will be saved
    public $large_image_name        = '';               // New name of the large image (append the timestamp to the filename)
    public $thumb_image_name        = '';               // New name of the thumbnail image (append the timestamp to the filename)
    
    public $large_image_location    = '';
    public $thumb_image_location    = '';
    
    
    // Only one of these image types should be allowed for upload
    public $allowed_image_types     = array('image/pjpeg'=>"jpg",'image/jpeg'=>"jpg",'image/jpg'=>"jpg",'image/png'=>"png",'image/x-png'=>"png",'image/gif'=>"gif");
    public $allowed_image_ext       = '';
    public $image_ext               = '';
    


    public function  __construct()
    {
        # GLOBAL VARIABLES
        # ===========================================
        $this->thumb_width = $GLOBALS['INSTRUCTOR_PICTURE_WIDTH'];
        $this->thumb_height = $GLOBALS['INSTRUCTOR_PICTURE_HEIGHT'];
        
        
        //$this->thumb_ratio = ($this->thumb_width / $this->thumb_height);
        $this->thumb_ratio = ($this->thumb_height / $this->thumb_width);
        
        //only assign a new timestamp if the session variable is empty
        if (!isset($_SESSION['random_key']) || strlen($_SESSION['random_key'])==0){
            $_SESSION['random_key'] = strtotime(date('Y-m-d H:i:s')); //assign the timestamp to the session variable
            $_SESSION['user_file_ext']= "";
        }
        
        
        $this->large_image_name       = $this->large_image_prefix . Session('random_key');
        $this->thumb_image_name       = $this->thumb_image_prefix . Session('random_key');
        
        
        $this->allowed_image_ext = array_unique($this->allowed_image_types);
        foreach ($this->allowed_image_ext as $mime_type => $ext) {
            $this->image_ext.= strtoupper($ext)." ";
        }
        
    } // -------------- END __construct --------------

    public function SetPath()
    {
        $this->upload_path            = "{$this->upload_dir}/";
        
        //Image Locations
        $this->large_image_location = $this->upload_path.$this->large_image_name;
        $this->thumb_image_location = $this->upload_path.$this->thumb_image_name;
        
        //Create the upload directory with the right permissions if it doesn't exist
        if(!is_dir($this->upload_dir)){
            mkdir($this->upload_dir, 0777);
            chmod($this->upload_dir, 0777);
        }
    }
    
    public function AjaxHandle()
    {
        ########################################################
        #	UPLOAD THE IMAGE								   #
        ########################################################
        if (Post('upload') == "Upload") { 
            //Get the file information
            $userfile_name      = $_FILES['image']['name'];
            $userfile_tmp       = $_FILES['image']['tmp_name'];
            $userfile_size      = $_FILES['image']['size'];
            $userfile_type      = $_FILES['image']['type'];
            $filename           = basename($_FILES['image']['name']);
            $file_ext           = strtolower(substr($filename, strrpos($filename, '.') + 1));
            
            //Only process if the file is a JPG and below the allowed limit
            if((!empty($_FILES["image"])) && ($_FILES['image']['error'] == 0)) {
                
                foreach ($this->allowed_image_types as $mime_type => $ext) {
                    //loop through the specified image types and if they match the extension then break out
                    //everything is ok so go and check file size
                    if($file_ext==$ext && $userfile_type==$mime_type){
                        $error = "";
                        break;
                    }else{
                        $error = "Only <strong>".$this->image_ext."</strong> images accepted for upload<br />";
                    }
                }
                //check if the file size is above the allowed limit
                if ($userfile_size > ($this->max_file*1048576)) {
                    $error.= "Images must be under ".$this->max_file."MB in size";
                }
                
            }else{
                $error= "Please select an image for upload";
            }
            
            //Everything is ok, so we can upload the image.
            if (strlen($error)==0){
                
                if (isset($_FILES['image']['name'])){
                    //this file could now has an unknown file extension (we hope it's one of the ones set above!)
                    $large_image_location = $this->large_image_location.".".$file_ext;
                    $thumb_image_location = $this->thumb_image_location.".".$file_ext;
                    
                    //put the file ext in the session so we know what file to look for once its uploaded
                    if($_SESSION['user_file_ext']!=$file_ext){
                        $_SESSION['user_file_ext']="";
                        $_SESSION['user_file_ext']=".".$file_ext;
                    }
                    
                    move_uploaded_file($userfile_tmp, $large_image_location);
                    chmod($large_image_location, 0777);
                    
                    $width = $this->getWidth($large_image_location);
                    $height = $this->getHeight($large_image_location);
                    //Scale the image if it is greater than the width set above
                    if ($width > $max_width){
                        $scale = $max_width/$width;
                        $uploaded = $this->resizeImage($large_image_location,$width,$height,$scale);
                    }else{
                        $scale = 1;
                        $uploaded = $this->resizeImage($large_image_location,$width,$height,$scale);
                    }
                    //Delete the thumbnail file so the user can create a new one
                    if (file_exists($thumb_image_location)) {
                        unlink($thumb_image_location);
                    }
                    //echo $this->upload_dir;
                    echo "success|".$large_image_location."|".$this->getWidth($large_image_location)."|".$this->getHeight($large_image_location);
                }
            }else{
                echo "error|".$error;
            }
        }

        ########################################################
        #	CREATE THE THUMBNAIL							   #
        ########################################################
        if (Post('save_thumb') == "Save Thumbnail") { 
            //Get the new coordinates to crop the image.
            $x1     = $_POST["x1"];
            $y1     = $_POST["y1"];
            $x2     = $_POST["x2"];
            $y2     = $_POST["y2"];
            $w      = $_POST["w"];
            $h      = $_POST["h"];
            
            //Scale the image to the thumb_width set above
            $large_image_location       = $this->large_image_location.$_SESSION['user_file_ext'];
            $thumb_image_location       = $this->thumb_image_location.$_SESSION['user_file_ext'];
            $scale                      = $this->thumb_width/$w;
            $cropped                    = $this->resizeThumbnailImage($thumb_image_location, $large_image_location,$w,$h,$x1,$y1,$scale);
            
            echo "success|".$large_image_location."|".$thumb_image_location;
            
            unset($_SESSION['random_key']);
            unset($_SESSION['user_file_ext']);
        }

        #####################################################
        #	DELETE BOTH IMAGES								#
        #####################################################
        if (Post('a') == "delete" && strlen(Post('large_image'))>0 && strlen(Post('thumbnail_image'))>0){
        //get the file locations 
            $large_image_location   = $_POST['large_image'];
            $thumb_image_location   = $_POST['thumbnail_image'];
            if (file_exists($large_image_location)) {
                unlink($large_image_location);
            }
            if (file_exists($thumb_image_location)) {
                unlink($thumb_image_location);
            }
            echo "success|Files have been deleted";
        }
    }
    
    public function OutputUploadForm()
    {
        $this->AddScript();
        $DID = Get('DIALOGID');
        
        $allowed_image_types = '';
        foreach ($this->allowed_image_types AS $type => $extension) {
            $extension = strtoupper($extension);
            if (strpos($allowed_image_types, $extension) === false) {
                $allowed_image_types .= "$extension, ";
            }
        }
        $allowed_image_types = substr($allowed_image_types, 0, -2);
        
        
        $btn_save_thumbnail = MakeButton('positive', 'Save Thumbnail', '', '', 'save_thumb', '', 'submit', 'save_thumb');
        #<input type="submit" name="save_thumb" value="Save Thumbnail" id="save_thumb" />
        
        $output = <<<OUTPUT
        <div style='border:1px solid #ddd; padding:5px; width:{$this->page_width}px'>
        
            <div id="upload_status" style="font-size:12px; w_idth:80%; m_argin:10px; padding:5px; display:none; border:1px #999 dotted; background:#eee;"></div>
            
            
            <div id="upload_step_1" style="border: 1px solid #990000; padding:10px; background-color:#eee;">
                <span style="font-size:14px; font-weight:bold;">STEP 1.</span> <span style="font-size:12px;">Select a file to upload</span><br /><b>Allowed File Types:</b> {$allowed_image_types}<br /><br />
                <a id="upload_link" style="background:#39f; font-size: 18px; color: white;" href="#">Click here to upload a photo</a><br />
                <span id="loader" style="display:none;"><img src="/office/images/loader.gif" alt="Loading..."/></span> <span id="progress"></span>
            </div>
            
            
            <div id="upload_step_2" style="border: 1px solid #990000; padding:10px; background-color:#eee; display:none;">
                <span style="font-size:14px; font-weight:bold;">STEP 2.</span> <span style="font-size:12px;">Draw a box around picture to create thumbnail</span><br /><br />
                <div id="uploaded_image" style="float:left;"></div>
                <div id="uploaded_preview" style="float:left;"></div>
                <div style="clear:both;"></div><br />
                <div id="thumbnail_form" style="display:none;">
                    <form name="form" action="" method="post">
                        <input type="hidden" name="x1" value="" id="x1" />
                        <input type="hidden" name="y1" value="" id="y1" />
                        <input type="hidden" name="x2" value="" id="x2" />
                        <input type="hidden" name="y2" value="" id="y2" />
                        <input type="hidden" name="w" value="" id="w" />
                        <input type="hidden" name="h" value="" id="h" />
                        <input type="hidden" name="sx" value="" id="sx" />
                        <input type="hidden" name="sy" value="" id="sy" />
                        {$btn_save_thumbnail}

                    </form>
                </div>
            </div>
            
            <div id="upload_step_3" style="border: 1px solid #990000; padding:10px; background-color:#eee; display:none;">
                <span style="font-size:14px; font-weight:bold;">COMPLETE.</span> <span style="font-size:12px;">Your photo has been cropped and uploaded</span><br /><br />
                
                CLOSE THIS WINDOW
                <br /><br />
                <div id="uploaded_final"></div>
            </div>
        </div>
OUTPUT;
        echo $output;
    }
    
    

    
    
    
    
    
    
    
    
    public function AddScript()
    {
        AddScriptInclude("/jslib/jquery.imgareaselect.min.js");
        AddScriptInclude("/jslib/jquery.ocupload-packed.js");


        $script = <<<SCRIPT


        //create a preview of the selection
        function preview(img, selection) { 
            //get width and height of the uploaded image.
            var current_width = $('#uploaded_image').find('#thumbnail').width();
            var current_height = $('#uploaded_image').find('#thumbnail').height();
            
            var scaleX = ({$this->thumb_width} / selection.width); 
            var scaleY = ({$this->thumb_height} / selection.height); 
            
            //$('#uploaded_image').find('#thumbnail_preview').css({ 
            $('#uploaded_preview').find('#thumbnail_preview').css({ 
                width: Math.round(scaleX * current_width) + 'px', 
                height: Math.round(scaleY * current_height) + 'px',
                marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px', 
                marginTop: '-' + Math.round(scaleY * selection.y1) + 'px' 
            });
            $('#x1').val(selection.x1);
            $('#y1').val(selection.y1);
            $('#x2').val(selection.x2);
            $('#y2').val(selection.y2);
            $('#w').val(selection.width);
            $('#h').val(selection.height);
        } 

        //show and hide the loading message
        function loadingmessage(msg, show_hide){
            if(show_hide=="show"){
                $('#loader').show();
                $('#progress').show().text(msg);
                $('#uploaded_image').html('');
            }else if(show_hide=="hide"){
                $('#loader').hide();
                $('#progress').text('').hide();
            }else{
                $('#loader').hide();
                $('#progress').text('').hide();
                $('#uploaded_image').html('');
            }
        }

        //delete the image when the delete link is clicked.
        function deleteimage(large_image, thumbnail_image){
            loadingmessage('Please wait, deleting images...', 'show');
            $.ajax({
                type: 'POST',
                url: '{$this->image_handling_file}',
                data: 'a=delete&large_image='+large_image+'&thumbnail_image='+thumbnail_image,
                cache: false,
                success: function(response){
                    loadingmessage('', 'hide');
                    response = unescape(response);
                    var response = response.split("|");
                    var responseType = response[0];
                    var responseMsg = response[1];
                    if(responseType=="success"){
                        $('#upload_status').show().html('<b>Success</b> - '+responseMsg+'');
                        $('#uploaded_image').html('');
                    }else{
                        $('#upload_status').show().html('<b>Unexpected Error</b> - Please try again - '+response);
                    }
                }
            });
        }
        
        function pushValueIntoForm(responseThumbImage)
        {
            if (('{$this->return_window_id}' != '') && ('{$this->return_object_id}' != '')) {
                parent.document.getElementById('appformIframe{$this->return_window_id}').contentWindow.ReplaceFieldValue('{$this->return_object_id}', responseThumbImage);
            }
        }
SCRIPT;
        AddScript($script);


        $script = <<<SCRIPT

                $('#loader').hide();
                $('#progress').hide();
                var myUpload = $('#upload_link').upload({
                   name: 'image',
                   action: '{$this->image_handling_file}',
                   enctype: 'multipart/form-data',
                   params: {upload:'Upload'},
                   autoSubmit: true,
                   onSubmit: function() {
                        $('#upload_status').html('').hide();
                        loadingmessage('Please wait, uploading file...', 'show');
                   },
                   onComplete: function(response) {
                        loadingmessage('', 'hide');
                        response = unescape(response);
                        response = jQuery.trim(response);
                        //alert(response);
                        
                        var response = response.split("|");
                        var responseType = response[0];
                        var responseMsg = response[1];
                        if(responseType=="success"){
                            var current_width = response[2];
                            var current_height = response[3];
                            
                            //display message that the file has been uploaded
                            $('#upload_status').show().html('<b>Success</b> - The image has been uploaded');
                            
                            //put the image in the appropriate div
                            $('#uploaded_image').html('<b>UPLOADED IMAGE</b><br /><img src="/office/'+responseMsg+'" style="float: left; margin-right: 10px;" id="thumbnail" alt="Create Thumbnail" width="{$this->uploaded_preview_width}px" />')
                            $('#uploaded_preview').html('<b>PREVIEW</b><br /><div style="border:1px #000 solid; float:left; position:relative; overflow:hidden; width:{$this->thumb_width}px; height:{$this->thumb_height}px;"><img src="/office/'+responseMsg+'" style="position: relative;" id="thumbnail_preview" alt="Thumbnail Preview" /></div>')
                            
                            //find the image inserted above, and allow it to be cropped
                            $('#uploaded_image').find('#thumbnail').imgAreaSelect({ 
                                aspectRatio: '1:{$this->thumb_ratio}', 
                                //aspectRatio: '1:2', 
                                onSelectChange: preview ,
                                x1: 0, y1: 0, x2: {$this->thumb_width}, y2: {$this->thumb_height}
                                });
                            
                            //display the hidden form
                            $('#thumbnail_form').show();
                            
                            
                            $('#upload_step_1').hide();
                            $('#upload_step_2').show();
                            $('#upload_step_3').hide();
                            ResizeIframe();
                            
                        }else if(responseType=="error"){
                            $('#upload_status').show().html('<b>Error</b> - '+responseMsg+'');
                            $('#uploaded_image').html('');
                            $('#thumbnail_form').hide();
                        }else{
                            $('#upload_status').show().html('<b>Unexpected Error</b> - Please try again - '+response);
                            $('#uploaded_image').html('');
                            $('#thumbnail_form').hide();
                        }
                   }
                });
            
            //create the thumbnail
            $('#save_thumb').click(function() {
                var x1 = $('#x1').val();
                var y1 = $('#y1').val();
                var x2 = $('#x2').val();
                var y2 = $('#y2').val();
                var w = $('#w').val();
                var h = $('#h').val();
                if(x1=="" || y1=="" || x2=="" || y2=="" || w=="" || h==""){
                    alert("You must make a selection first");
                    return false;
                }else{
                    //hide the selection and disable the imgareaselect plugin
                    $('#uploaded_image').find('#thumbnail').imgAreaSelect({ disable: true, hide: true }); 
                    loadingmessage('Please wait, saving thumbnail....', 'show');
                    $.ajax({
                        type: 'POST',
                        url: '{$this->image_handling_file}',
                        data: 'save_thumb=Save Thumbnail&x1='+x1+'&y1='+y1+'&x2='+x2+'&y2='+y2+'&w='+w+'&h='+h,
                        cache: false,
                        success: function(response){
                            loadingmessage('', 'hide');
                            response = unescape(response);
                            response = jQuery.trim(response);
                            //alert(response);
                            
                            var response = response.split("|");
                            var responseType = response[0];
                            var responseLargeImage = response[1];
                            var responseThumbImage = response[2];
                            if(responseType=="success"){
                                $('#upload_status').show().html('<b>Success</b> - The thumbnail has been saved!');
                                
                                //load the new images
                                $('#uploaded_final').html('<b>CREATED IMAGE</b><br /><img src="/office/'+responseThumbImage+'" alt="Thumbnail Image"/><br /><a href="javascript:deleteimage(\''+responseLargeImage+'\', \''+responseThumbImage+'\');">Delete Images</a>');
                                
                                
                                //hide the thumbnail form
                                $('#thumbnail_form').hide();
                                
                                $('#upload_step_1').hide();
                                $('#upload_step_2').hide();
                                $('#upload_step_3').show();
                                ResizeIframe();
                                
                                //PUSH THE VALUE BACK INTO THE MAIN FORM
                                //parent.document.getElementById('appformIframe{$this->return_window_id}').contentWindow.ReplaceFieldValue('{$this->return_object_id}', responseThumbImage);
                                pushValueIntoForm(responseThumbImage);
    
                                
                            }else{
                                $('#upload_status').show().html('<b>Unexpected Error</b> - Please try again - '+response);
                                //reactivate the imgareaselect plugin to allow another attempt.
                                $('#uploaded_image').find('#thumbnail').imgAreaSelect({ aspectRatio: '1:{$this->thumb_ratio}', onSelectChange: preview }); 
                                $('#thumbnail_form').show();
                            }
                        }
                    });
                    
                    return false;
                }
            });


SCRIPT;
        AddScriptOnReady($script);
    }
    
    public function resizeImage($image,$width,$height,$scale) 
    {
        $image_data         = getimagesize($image);
        $imageType          = image_type_to_mime_type($image_data[2]);
        $newImageWidth      = ceil($width * $scale);
        $newImageHeight     = ceil($height * $scale);
        $newImage           = imagecreatetruecolor($newImageWidth,$newImageHeight);
        switch($imageType) {
            case "image/gif":
                $source = imagecreatefromgif($image); 
                break;
            case "image/pjpeg":
            case "image/jpeg":
            case "image/jpg":
                $source = imagecreatefromjpeg($image); 
                break;
            case "image/png":
            case "image/x-png":
                $source = imagecreatefrompng($image); 
                break;
        }
        imagecopyresampled($newImage,$source,0,0,0,0,$newImageWidth,$newImageHeight,$width,$height);
        
        switch($imageType) {
            case "image/gif":
                imagegif($newImage,$image); 
                break;
            case "image/pjpeg":
            case "image/jpeg":
            case "image/jpg":
                imagejpeg($newImage,$image,90); 
                break;
            case "image/png":
            case "image/x-png":
                imagepng($newImage,$image);  
                break;
        }
        
        chmod($image, 0777);
        return $image;
    }
    
    public function resizeThumbnailImage($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale)
    {
        list($imagewidth, $imageheight, $imageType) = getimagesize($image);
        $imageType          = image_type_to_mime_type($imageType);
        $newImageWidth      = ceil($width * $scale);
        $newImageHeight     = ceil($height * $scale);
        $newImage           = imagecreatetruecolor($newImageWidth,$newImageHeight);
        switch($imageType) {
            case "image/gif":
                $source = imagecreatefromgif($image); 
                break;
            case "image/pjpeg":
            case "image/jpeg":
            case "image/jpg":
                $source = imagecreatefromjpeg($image); 
                break;
            case "image/png":
            case "image/x-png":
                $source = imagecreatefrompng($image); 
                break;
        }
        imagecopyresampled($newImage,$source,0,0,$start_width,$start_height,$newImageWidth,$newImageHeight,$width,$height);
        switch($imageType) {
            case "image/gif":
                imagegif($newImage,$thumb_image_name); 
                break;
            case "image/pjpeg":
            case "image/jpeg":
            case "image/jpg":
                imagejpeg($newImage,$thumb_image_name,90); 
                break;
            case "image/png":
            case "image/x-png":
                imagepng($newImage,$thumb_image_name);  
                break;
        }
        chmod($thumb_image_name, 0777);
        return $thumb_image_name;
    }
    
    public function getHeight($image) 
    {
        $size = getimagesize($image);
        $height = $size[1];
        return $height;
    }
    
    public function getWidth($image) 
    {
        $size = getimagesize($image);
        $width = $size[0];
        return $width;
    }




} //END CLASS