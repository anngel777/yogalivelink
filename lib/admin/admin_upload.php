<table class="upload" align="center">
<tr><td>
<?php

//================ Input Upload Image File ================
if ($IU or $IMAGEUPLOAD) {
  $folders = GetFolders(ADMIN_IMAGE_DIR);
  $folders[] = ADMIN_IMAGE_LINK_DIR;
  for($i=0; $i<count($folders); $i++) if ($folders[$i]!=ADMIN_IMAGE_LINK_DIR) $folders[$i] = ADMIN_IMAGE_LINK_DIR.'/'.$folders[$i];
  natcasesort($folders);
  if (count($folders) == 1 ) $select = qq("<input type=`hidden` name=`IMAGEDIR` value=`{$folders[0]}` />");
  elseif (count($folders) > 1 ) {
        $select = '<p><select name="IMAGEDIR">';
        foreach($folders as $idir) {
           $have = ($idir == $IMAGEDIR)? 'selected' : '';
           $select .= qq("<option value=`$idir` $have>$idir</option>");
        }
        $select .= '</select></p>';
    }
  else $select = '<input type="hidden" name="IMAGEDIR" value="'.ADMIN_IMAGE_LINK_DIR.'" />';

print <<<LBL_IU
<p>
  Upload an Image file to the server:
</p>
<form action="$ADMIN_FILE" method="post" enctype="multipart/form-data">
$select
  <input type="hidden" name="MAX_FILE_SIZE" value="20480000" />
  <p>
    <input type="file" name="ImageFile" size="60" onchange="getId('submit').style.display=(this.value=='')? 'none' : '';" />
  </p>
  <p>
    <input id="submit" style="display:none;" type="submit" name="IMAGEUPLOAD" value="Upload Image" />
  </p>
</form>
LBL_IU;
}

//======================Upload Image==========================
if ($IMAGEUPLOAD) {
    $NewImageFile = $_FILES['ImageFile']['name'];
    $NewImageSize = $_FILES['ImageFile']['size'];
    $TempFile     = $_FILES['ImageFile']['tmp_name'];
    $NewImageFile = str_replace(' ', '_', $NewImageFile);
    printqn("<p>File Name: <span style=`color:#ff6;`>$IMAGEDIR/$NewImageFile</span><br />");
    printqn("File Size: <span style=`color:#ff6;`>$NewImageSize</span> bytes</p>");
    print '<h2>';
    $newfile = "$ROOT/$IMAGEDIR/$NewImageFile";
    if (move_uploaded_file ($TempFile, $newfile)) {
        chmod("$ROOT/$IMAGEDIR/$NewImageFile", 0666);
        printqn("Your file was successfully uploaded!</h2>");
        $ext = strtolower(strFrom($NewImageFile,'.'));

        $image_size = '';
        if ($ext != 'swf') {
            $image_size = getimagesize($newfile);
            list($width, $height, $type, $attr) = $image_size;
            printqn("<p style=`font-size:8pt`>Width: $width&nbsp;&nbsp;Height: $height</p>");
            printqn("<p><img src=`$IMAGEDIR/$NewImageFile` alt=`New Image` /></p>");
        }


        if (!$FILE_SET and AdminSession('IMAGE_FILES', SESSION_FILES)) {
            $_SESSION['SITE_ADMIN'][SESSION_FILES]['IMAGE_FILES'][] = $NewImageFile;
            $_SESSION['SITE_ADMIN'][SESSION_FILES]['IMAGE_FILE_SIZES'][$NewImageFile] = $image_size;
            ksort($_SESSION['SITE_ADMIN'][SESSION_FILES]['IMAGE_FILES']);
            ksort($_SESSION['SITE_ADMIN'][SESSION_FILES]['IMAGE_FILE_SIZES']);
        }
    }
    else {print '<span style="background-color:#f00;">Your file could not be uploaded!</span></h2>';}
}


//================ Input Upload Document File ================
if ($DU or $DOCUMENTUPLOAD) {
    print "<!-- ==================UPLOAD DOCUMENT=================== -->\n";
    if (count($DocLinkDirs) == 1 ) {
        $select = qq("<input type=`hidden` name=`DOCUMENTDIR` value=`{$DocLinkDirs[0]}` />");
    } elseif (count($DocLinkDirs) > 1 ) {
        $select = '<p><select name="DOCUMENTDIR">';
        foreach($DocLinkDirs as $dir) {
            $have = ($dir == $DOCUMENTUPLOAD)? 'selected' : '';
            $select .= qq("<option value=`$dir` $have>$dir</option>");
        }
        $select .= '</select></p>';
    } else {
        $select = '<input type="hidden" name="DOCUMENTDIR" value="/docs" />';
    }

    print <<<LBL_DU
<p>Upload a Document file to the server:</p>
<form action="$ADMIN_FILE" method="post" enctype="multipart/form-data">
$select
    <input type="hidden" name="MAX_FILE_SIZE" value="20480000" />
  <p>
    <input type="file" name="DocumentFile" size="60"  onchange="getId('submit').style.display=(this.value=='')? 'none' : '';" />
  </p>
  <p>
    <input id="submit" type="submit" style="display:none;" name="DOCUMENTUPLOAD" value="Upload Document" />
  </p>
</form>
LBL_DU;

}

//======================Upload Document==========================
if ($DOCUMENTUPLOAD) {
    print "<!-- ==================UPLOAD DOCUMENT=================== -->\n";
    $docdir = $ROOT . $DOCUMENTDIR;
    $NewDocFile = $_FILES['DocumentFile']['name'];
    $NewDocSize = $_FILES['DocumentFile']['size'];
    $TempFile   = $_FILES['DocumentFile']['tmp_name'];
    $NewDocFile = str_replace(' ', '_', $NewDocFile);
    print "<p>File Name: <span style=\"color:#ff6\">$NewDocFile</span><br />\n";
    print "File Size: <span style=\"color:#ff6\">$NewDocSize</span> bytes</p>\n";
    print '<h2>';
    if (move_uploaded_file ($TempFile, "$docdir/$NewDocFile")) {
        chmod("$docdir/$NewDocFile", 0666);
        print("Your file was successfully uploaded!</h2>\n");
    } else {
        print '<span style="background-color:#f00;">Your file could not be uploaded!</span></h2>';
    }
}
?>
</td></tr>
</table>
