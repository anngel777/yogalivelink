<?php
//==========================================================
//                    VIEW ALL IMAGES
//==========================================================
if ($DELETE_IMAGE) {
    if(file_exists(ADMIN_IMAGE_DIR."/$DELETE_IMAGE")) {
       unlink(ADMIN_IMAGE_DIR."/$DELETE_IMAGE");
    } else {
        AddError('IMAGE: '.ADMIN_IMAGE_LINK_DIR."/$DELETE_IMAGE -- NOT FOUND");
    }
    $VG=1;
}

if ($RENAMEFILE_IMAGE) {
    if (!(file_exists(ADMIN_IMAGE_DIR."/$NEWNAME_IMAGE"))) {
        $filename1  = ADMIN_IMAGE_DIR."/$OLDNAME_IMAGE";
        $filename2  = ADMIN_IMAGE_DIR."/$NEWNAME_IMAGE";
        rename($filename1,$filename2);
        LogUpdate(ADMIN_USERNAME,'Rename Image File',"$OLDNAME_IMAGE - $NEWNAME_IMAGE");
        $VG=1;
    } else {
        $RENAME_IMAGE=$OLDNAME_IMAGE; $DuplicateFile=1;
    }
}

if ($RENAME_IMAGE) {
    print <<<RENAMEIMAGELABEL
  <form method=post action="$ADMIN_FILE">
  <input type="hidden" name="OLDNAME_IMAGE" value="$RENAME_IMAGE">
  <table align="center" border="0" cellpadding="3" id="rename_image">
    <tr>
      <td align="right">Old Filename:</td><td>$RENAME_IMAGE</td>
    </tr>
    <tr>
      <td align="right">New Filename:</td>
      <td>
        <input type="text" name="NEWNAME_IMAGE" size="30" value="$RENAME_IMAGE" />
      </td>
    </tr>
      <td>
      </td>
      <td>
        <input type="Submit" name="RENAMEFILE_IMAGE" value="Rename File" />
      </td>
    </tr>
  </table>
RENAMEIMAGELABEL;
}

if (Get('RESIZE_IMAGE') or Post('RESIZE_IMAGE_SUBMIT')) {
    include "$lib/form_helper.php";
    include "$ROOT/classes/class.Image.php";
}

if (Post('RESIZE_IMAGE_SUBMIT')) {
    $image = GetPostItem('IMAGE');
    $filename = ADMIN_IMAGE_DIR . "/$image";
    
    $height = intOnly(GetPostItem('new_image_height'));
    $width  = intOnly(GetPostItem('new_image_width'));
    if (empty($height)) $height = 0;
    if (empty($width))  $width  = 0;
    
    if (($width > 0 ) or ($height > 0)) {
    
        if (file_exists($filename) and !is_dir($filename)) {
            $type  = strToUpper(strFromLast($filename, '.'));
            $image = new Image($filename);
            switch ($type) {
                case 'GIF' : $image->type = IMAGETYPE_GIF;
                    break;
                case 'JPG' : $image->type = IMAGETYPE_JPEG;
                    break;
                case 'PNG' : $image->type = IMAGETYPE_PNG;
            }

            $image->scale($width, $height); // scales the image but maintains the aspect ratio
            // $filename = $image->write('thumb_image'); // write the image to the specified file, using the default extension
            $content = $image->write($filename, array('extension' => false)); // write the image to the specified file, but don't use the default extension
            //AdminWriteFile($filename, $content);

            LogUpdate(ADMIN_USERNAME,'Resize Image File',"$filename - $width x $height");
            AddFlash("Image File: $filename Resized");
        }
    } else {
        AddError('Resized File Not Found!');
    }
    $VG=1;
}

if (Get('RESIZE_IMAGE')) {
    $image = Get('RESIZE_IMAGE');
    $filename = ADMIN_IMAGE_DIR . "/$image";

    if (file_exists($filename)) {

        $t=date("m\/d\/Y",filemtime($filename));
        list($width, $height, $type, $attr) = getimagesize($filename);
        $fsize = number_format (filesize($filename)/1024,1).'KB';
        $file_link = ADMIN_IMAGE_LINK_DIR . '/' . $image;

        $form = Array(
            'code|<div id="configform">',
            "form|$ADMIN_FILE?VG=1|post",
            "hidden|IMAGE|$image",
            "h2|Resize Image",
            "info|File|$file_link",
            "info|Attributes|$width x $height, $fsize",
            "info||<img src=\"/lib/image.php?f=$file_link;h=200;w=200\" alt=\"Image Thumb\" />",
            "integer|New Width ($width)|new_image_width|N|10|10",
            "integer|New Height ($height)|new_image_height|N|10|10",
            'submit|Resize Image|RESIZE_IMAGE_SUBMIT',
            'endform',
            'code|</div>'
        );
        echo OutputForm($form);
    } else {
        WriteError('Image File Not Found');
    }
}

if ($DuplicateFile) {
    print '<p class="duplicatefile">File: <b>'.$NEWNAME_IMAGE.'</b> already exists.</p>';
    print '</form>';
}

if ($VG=='1') {

    $use_resize = (file_exists("$ROOT/classes/class.Image.php"));

    print <<<LBL_VG
    <table id="viewallimages" cellpadding="5" align="center" width="95%">
    <tr>
        <th class="header" align="center" colspan="2">
           <span class="subheader">Image Files</span>

            <p>Filter: <input type="text" id="imagefilter" size="40" maxlength="80" onkeyup="
            var filter = this.value;
            filter = filter.toLowerCase();
            filter = filter.replace('/', '::');
            var check = false;
            var rowId = '';
            var i = 0;
            var table = document.getElementById('viewallimages');
            var rows = table.getElementsByTagName('tr');
            for (i in rows ) {
                rowId = rows[i].id;
                check = rowId.indexOf(filter)
                if (check > -1) {
                    showId(rowId);
                } else {
                    hideId(rowId);
                }
            }" /></p>
        </th>
    </tr>
LBL_VG;

    $files = GetDirectory(ADMIN_IMAGE_DIR,ADMIN_IMAGE_TYPES);

    $count=0;
    foreach ($files as $fi) {
        if (!(eregi('.LCK',$fi))) {
            $Lfilename = ADMIN_IMAGE_LINK_DIR."/$fi";
            $filename = ADMIN_IMAGE_DIR."/$fi";
            $t=date("m\/d\/Y",filemtime($filename));
            list($width, $height, $type, $attr) = getimagesize($filename);
            $fsize = number_format (filesize($filename)/1024,1).'KB';
            $deletelink = "$ADMIN_FILE?DELETE_IMAGE=$fi";
            $count++;

            if($width > $height){
                $thumbwidth  = min(200,$width);
                $thumbheight = round($thumbwidth * $height/$width);
            } else {
                $thumbheight  = min(200,$height);
                $thumbwidth = round($thumbheight * $width/$height);
            }

            $margintop = $thumbheight + 6;

            if (($width > 200) or ($height > 200)) {
                $imageout = <<< IMAGEOUT1
            <a class="imagelink" href="#" style="width:{$thumbwidth}px;" onclick="showId('picturediv$count'); return false;">
                <img src="$Lfilename" border="0" width="$thumbwidth" height="$thumbheight" alt="$Lfilename" />
            </a>
            <div id="picturediv$count" class="viewpicture" style="display:none; margin-top:-{$margintop}px;"  onclick="hideId('picturediv$count');">
                <img style="border:4px solid #fff;" src="$Lfilename" border="0" width="$width" height="$height" alt="$Lfilename" />
            </div>
IMAGEOUT1;
            } else {
                $imageout = qq("<img src=`$Lfilename` border=`1` width=`$thumbwidth` height=`$thumbheight` alt=`$Lfilename` />");
            }

            $row_id = str_replace('/', '::', strtolower($Lfilename));
            $row_id = str_replace(ADMIN_IMAGE_DIR, '', $row_id);

            if ($use_resize) {
                $resize = "
                <a class=\"titlebutton\" href=\"$ADMIN_FILE?RESIZE_IMAGE=$fi\">Resize</a>";
            } else {
                $resize = '';
            }

            print <<< IMAGEROW1
    <tr id="IMAGE_ROW_$row_id">
        <td width="205">
            $imageout
        </td>
        <td>
            <div>$count. <b>$Lfilename</b>&nbsp;&nbsp;&nbsp;
            <span class="version">Version: $t &mdash; Width: $width&nbsp;&nbsp;Height: $height &mdash; Size: $fsize </span>
            <br />
            <div class="buttons">
                <a class="titlebutton" href="$ADMIN_FILE?RENAME_IMAGE=$fi">Rename</a>$resize
                <a class="contentbutton" href="$deletelink" onclick="return confirm('Are you sure you want to delete [$fi]?')">Delete</a>
            </div>
            </div>
        </td>
    </tr>

IMAGEROW1;
        }
    }
    print "</table>\n";
}


?>