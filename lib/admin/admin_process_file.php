<?PHP
//  WEBSITE ADMINISTRATION PROGRAM
//  Admin Process File

//======================NEW FILE==========================
$DuplicateFile = 0;

function HaveFtp()
{
    return !empty($_SESSION['SITE_ADMIN']['FTP']);
}

function AdminWriteFile($filename, $content)
{
    if (HaveFtp()) {
        extract($_SESSION['SITE_ADMIN']['FTP'], EXTR_OVERWRITE);
        $FTP_PERM = (int) octdec( str_pad( $FTP_PERM, 4, '0', STR_PAD_LEFT ) );
        $filename = $FTP_ROOT . PathFromRoot($filename);
        WriteFileFtp($FTP_USER, $FTP_PASS, $filename, $content);
        $conn_id = ftp_connect(Server('SERVER_ADDR'));
        $ftp_result = ftp_login($conn_id, $FTP_USER, $FTP_PASS);
        ftp_chmod($conn_id, $FTP_PERM, $filename);
        AddFlash("[$filename] Saved via FTP");
    } else {
        $filepointer = fopen($filename, 'w');
        fwrite($filepointer, $content);
        @chmod($filename, 0666);
        fclose($filepointer);
    }
}

function FilterFileName($name)
{
    return str_replace(array("'", '\\', ' '),array('', '', '-'), $name);
}

if ($NEW) {
    include_once "$LIB/form_helper.php";
    $ERROR = '';
    function AddNewFile($NEWFILETYPE, $NEWFILENAME)
    {
        global $ERROR, $ROOT, $SITECONFIG, $ADMIN_FILE;

        if (!$NEWFILENAME or !$NEWFILETYPE) return;
        $NEWFILETYPE = strtolower($NEWFILETYPE);
        $NEWFILENAME = FilterFileName($NEWFILENAME);

        $RESULT = '';

        $extensions = array(
            'helper'    => '.php',
            'page'      => ADMIN_CONTENT_STR,
            'class'     => '.php',
            'template'  => '.html',
            'css'       => '.css',
            'javascript'=> '.js',
            'list'      => '.dat',
            'other'     => ''
        );

        $paths = array(
            'helper'    => $ROOT.$SITECONFIG['sitedir'].'/helper',
            'page'      => ADMIN_CONTENT_DIR,
            'class'     => ADMIN_CLASS_DIR,
            'template'  => ADMIN_TEMPLATE_DIR,
            'css'       => ADMIN_CSS_DIR,
            'javascript'=> $ROOT.$SITECONFIG['jsdir'],
            'list'      => $ROOT.$SITECONFIG['sitedir'].'/lists',
            'other'     => $ROOT
        );

        $contents = array(
            'helper'    => "<?php\n\n?>",
            'page'      => '',
            'class'     => "<?php\n\n?>",
            'template'  => "<!-- TEMPLATE -->\n",
            'css'       => "/* ========= CSS ========= */\n",
            'javascript'=> "/* ========= Javascript ========= */\n",
            'list'      => '',
            'other'     => ''
        );

        $ext = $extensions[$NEWFILETYPE];

        if ($ext) $NEWFILENAME = str_replace($ext, '', $NEWFILENAME) . $ext;

        $path = $paths[$NEWFILETYPE];

        $file_to_write = "$path/$NEWFILENAME";

        $content = $contents[$NEWFILETYPE];

        if ($NEWFILETYPE == 'page') {
            if (!(file_exists($file_to_write))) {
                $F=$NEWFILENAME;
                if (substr($F,0,1) == '/') {
                    $F = substr($F,1);
                }
                if (!file_exists(dirname(ADMIN_CONTENT_DIR."/$F"))) {
                    mkdir(dirname(ADMIN_CONTENT_DIR."/$F"));
                }

                $TTEXT =  file_get_contents(ADMIN_FILES_DIR.'/blanktitle.dat');

                $text = NameToTitle(strTo(basename($NEWFILENAME),'.'));
                $OLDtext = array('<name></name>','<summary></summary>','<title></title>','<description></description>');
                $NEWtext = array("<name>$text</name>","<summary>$text</summary>","<title>$text</title>","<description>$text</description>");
                $TTEXT = str_replace($OLDtext, $NEWtext, $TTEXT);

                AdminWriteFile($file_to_write, "<h1>$text</h1>");

                $def_file_to_write = str_replace(ADMIN_CONTENT_STR, ADMIN_TITLE_STR, $file_to_write);
                AdminWriteFile($def_file_to_write, $TTEXT);

                $NEWFILENAME = RemoveExtension($NEWFILENAME);
                $link = "$ADMIN_FILE?F=$NEWFILENAME";
                LogUpdate(ADMIN_USERNAME, 'New File', $NEWFILENAME);
                $RESULT = "New File Created: <b><a href=\"$link\">$NEWFILENAME</a></b><br />\n";

            } else {
                $ERROR .= "Duplicate File: $NEWFILENAME<br />\n";
            }
        } else {
            if (!(file_exists($file_to_write))) {
                AdminWriteFile($file_to_write, $content);
                $RESULT = "New File Created: <b>$NEWFILENAME</b><br />\n";
            } else {
                $ERROR .= "Duplicate File: $NEWFILENAME<br />\n";
            }
        }
        return $RESULT;
    } // end add new file function function


    $NewFileFormArray = array(
       "form|$ADMIN_FILE?NEW=1|post",
       "select|File Type|NEWFILETYPE|Y||N|page=Page|helper=Helper|template=Template|javascript=Javascript|css=CSS|class=Class|list=List",
       "textarea|New File Names|NEWFILENAMES|Y|60|10",
       "submit|Add New Files|SUBMITNEWFILE",
       'endform'
    );


    $NEW_FILE_RESULT = '';
    if (HaveSubmit('SUBMITNEWFILE')) {
        $array = ProcessForm($NewFileFormArray, $table,'','','',$ERROR);
        if (!$ERROR) {
            $FILE_SET = 1;
            $new_files = explode("\n", $array['NEWFILENAMES']);
            foreach ($new_files as $file ) {
                $file = trim($file);
                if ($file) {
                    $result = AddNewFile($array['NEWFILETYPE'], $file);
                    if ($result) {
                        $NEW_FILE_RESULT .= "<p>$result</p>";
                    }
                }
            }
        }
    }
}

//===========================================================================

if ($RENAMEFILE) {
    $NEWNAME = FilterFileName($NEWNAME);
    if (!(file_exists(ADMIN_CONTENT_DIR."/$NEWNAME".ADMIN_CONTENT_STR))) {
        $filename1  = ADMIN_CONTENT_DIR."/$OLDNAME".ADMIN_CONTENT_STR;
        $titlename1 = ADMIN_CONTENT_DIR."/$OLDNAME".ADMIN_TITLE_STR;
        $filename2  = ADMIN_CONTENT_DIR."/$NEWNAME".ADMIN_CONTENT_STR;
        $titlename2 = ADMIN_CONTENT_DIR."/$NEWNAME".ADMIN_TITLE_STR;

        if (!file_exists(dirname(ADMIN_CONTENT_DIR."/$NEWNAME"))) {
            mkdir(dirname(ADMIN_CONTENT_DIR."/$NEWNAME"));
        }

        rename($filename1,$filename2);
        rename($titlename1,$titlename2);
        AddFlash("Files Renamed: <b>$OLDNAME</b> to <b>$NEWNAME</b>");
        LogUpdate(ADMIN_USERNAME,'Rename File',"$OLDNAME - $NEWNAME");
        $FM=1;
        $FILE_SET = 1;
    } else {
        $RENAME=$OLDNAME;
        $DuplicateFile=1;
    }
}


if ($COPYFILE) {
    $NEWNAME = FilterFileName($NEWNAME);
    if (!(file_exists(ADMIN_CONTENT_DIR."/$NEWNAME".ADMIN_CONTENT_STR))) {
        $filename1  = ADMIN_CONTENT_DIR."/$OLDNAME".ADMIN_CONTENT_STR;
        $titlename1 = ADMIN_CONTENT_DIR."/$OLDNAME".ADMIN_TITLE_STR;
        $filename2  = ADMIN_CONTENT_DIR."/$NEWNAME".ADMIN_CONTENT_STR;
        $titlename2 = ADMIN_CONTENT_DIR."/$NEWNAME".ADMIN_TITLE_STR;

        if (!file_exists(dirname(ADMIN_CONTENT_DIR."//$NEWNAME"))) {
            mkdir(dirname(ADMIN_CONTENT_DIR."//$NEWNAME"));
        }

        copy($filename1,$filename2);
        copy($titlename1,$titlename2);
        chmod($filename2, 0666);
        chmod($titlename2, 0666);
        AddFlash("File Copied: <b>$OLDNAME</b> to <b>$NEWNAME</b>");
        LogUpdate(ADMIN_USERNAME,'Copy File',"$OLDNAME - $NEWNAME");
        $FM=1;
        $FILE_SET = 1;
    } else {
        $COPY=$OLDNAME; $DuplicateFile=1;
    }
}


if ($DELETE) {
    $filename  = ADMIN_CONTENT_DIR."/$DELETE".ADMIN_CONTENT_STR;
    $titlename = ADMIN_CONTENT_DIR."/$DELETE".ADMIN_TITLE_STR;
    if (file_exists($filename)) {
        unlink($filename);
        AddFlash("File Deleted: <b>$DELETE</b>");
        $FILE_SET = 1;
    } else {
        $ERROR_MSG = 'File Not Found for Delete!';
    }

    if (file_exists($titlename)) {
        unlink($titlename);
        $FILE_SET = 1;
    } else {
        $ERROR_MSG = 'File Not Found for Delete!';
    }
}




function DateToStd($d) {
    return date('m/d/Y Hia', strtotime($d));
}

//--------------------- file processing------------------

function ExtractArchiveFile($file)
{
    $date = ExtractArchiveDate($file);
    return strTo($file, "_$date");
}

function ExtractArchiveDate($file)
{
    $date = strFromLast($file, '_20');
    $date = strTo($date, '.');
    return "20$date";
}


if ($F) {
    $ADMIN_PAGE_QUERY_LINK = str_replace('F=_','F=',$ADMIN_PAGE_QUERY_LINK);
    if (substr($F,0,1)=='_') {$draft=1; $FZ=substr($F,1);} else {$draft=0; $FZ=$F;}
    $DraftNotice = (($draft) or ($SAVEDRAFT))? '<span class="draft">DRAFT:</span> ' : '';

    if ($SP=='1') {
        $Tfilename = $F;
        $Cfilename = $F;
        $FS = basename($F);
    } else {
        $Tfilename = ADMIN_CONTENT_DIR."/$F".ADMIN_TITLE_STR;
        $Cfilename = ADMIN_CONTENT_DIR."/$F".ADMIN_CONTENT_STR;
        $SP = 0;
        $FS = $F;
    }

    if (file_exists($Tfilename)) {
        $TitleDate   = date('YmdHis', filemtime($Tfilename));
    } else {
        $TitleDate = 0;
    }

    if (file_exists($Cfilename)) {
        $ContentDate = date('YmdHis', filemtime($Cfilename));
    } else {
        $ContentDate=0;
    }

//-------------SAVE FILES--------------
    if ($PUBLISH or $SAVEDRAFT) {
        if ($SP != 1) {
            //---update title
            if ($TITLEDATE < $TitleDate) {
               AddError('File Date of Title File is Newer on Server. File Not Saved!');
               $TitleDate = $TITLEDATE;
            } else {
                $Tdraftfilename = ADMIN_CONTENT_DIR."/_$FZ".ADMIN_TITLE_STR;

                if ($SAVEDRAFT) {
                    $Tfilename = $Tdraftfilename;
                } else {
                    $Tfilename = ADMIN_CONTENT_DIR."/$FZ".ADMIN_TITLE_STR;
                }

                // $filepointer = fopen($Tfilename,"w");
                // fwrite($filepointer,$TTEXT);

                // if ($NEWFILE) {
                    // chmod($Tfilename, 0766);
                // }

                // fclose($filepointer);

                AdminWriteFile($Tfilename, $TTEXT);

                clearstatcache();
                $TitleDate = date("YmdHis",filemtime($Tfilename));

                if (file_exists($Tdraftfilename) and ($PUBLISH)) {
                    unlink($Tdraftfilename);
                }
            }
        }
        //---update content
        if ($CONTENTDATE < $ContentDate) {
            AddError("File Date of Content File is Newer on Server. File Not Saved! ($CONTENTDATE, $ContentDate)");
            $ContentDate = $CONTENTDATE;
        } else {
            if ($SP!= 1) {
                $Cdraftfilename = ADMIN_CONTENT_DIR."/_$FZ".ADMIN_CONTENT_STR;
                if ($SAVEDRAFT) {
                    $Cfilename=$Cdraftfilename;
                    $F='_'.$FZ;
                } else {
                    $Cfilename=ADMIN_CONTENT_DIR."/$FZ".ADMIN_CONTENT_STR;
                    $F=$FZ;
                }
            }

            if ((HaveFtp() or is_writable($Cfilename)) or !file_exists($Cfilename)) {
                //$filepointer=fopen($Cfilename,"w");
                // fwrite($filepointer,$CTEXT);

                // if ($NEWFILE) {
                    // chmod($Cfilename, 0666);
                // }

                // fclose($filepointer);
                AdminWriteFile($Cfilename, $CTEXT);

                if (($SP!= 1) and ($PUBLISH) and file_exists($Cdraftfilename)) {
                    unlink($Cdraftfilename);
                }

                if ($PUBLISH) {
                    LogUpdate(ADMIN_USERNAME,'Publish Page',$F);
                } else {
                    LogUpdate(ADMIN_USERNAME,'Save Draft',$F);
                }

                clearstatcache();
                $ContentDate = date("YmdHis",filemtime($Cfilename));

            } else {
                $ERROR_MSG = 'File is Not Writeable - Check Permissions!';
            }
        }
    }

    if ($TitleDate) {
        $Tfd = date('m\/d\/Y - h:ia', filemtime($Tfilename));
    }
    if ($ContentDate) {
        $Cfd = date('m\/d\/Y - h:ia', filemtime($Cfilename));
    }

    if (empty($TitleDate) and empty($ContentDate)) {
        $ERROR_MSG = 'File Not Found!';
        $F = '';
    }

    //------------ARCHIVE FILES--------------
    if ($ARCHIVEPAGE and $F) {

        $Tfd2       = date("YmdHis", filemtime($Tfilename));
        $AF         = str_replace('/', '@', $F);

        $Tfilename2 = ADMIN_ARCHIVE_DIR . "/$AF" . ADMIN_TITLE_STR . "_$Tfd2.php";

        if (!file_exists($Tfilename2)) {
            copy($Tfilename, $Tfilename2);
            chmod($Tfilename2, 0766);
            LogUpdate(ADMIN_USERNAME, 'Archive Title', $F);
            AddFlash("$F ($Tfd2) Title Archived");
        } else {
            AddFlash("$F ($Tfd2) Title Already Archived");
        }

        $Cfd2  = date('YmdHis', filemtime($Cfilename));

        $fname = $F;
        if (substr($F, 0, 3) == '../') {
            $fname = substr($F, 3);
        }
        $fname = str_replace($SITE_ROOT, '', $F);

        $AF    = str_replace('/', '@', $fname);

        if ($SP == '1') {
            $Cfilename2 = ADMIN_ARCHIVE_DIR . "/$AF"."_$Cfd2.php";
        } else {
            $Cfilename2 = ADMIN_ARCHIVE_DIR . "/$AF".ADMIN_CONTENT_STR."_$Cfd2.php";
        }

        if (!file_exists($Cfilename2)) {
            copy($Cfilename,$Cfilename2);
            chmod($Cfilename2, 0766);
            LogUpdate(ADMIN_USERNAME, 'Archive Content',$F);
            AddFlash("$F ($Cfd2) File Archived");
        } else {
            AddFlash("$F ($Cfd2) File Already Archived");
        }
    }

//-----------READ THE ARCHIVES-----------
    $ACfiles   = array();
    $ATfiles   = array();
    $titlename = strTo($FZ, '.') . ADMIN_TITLE_STR;

    if ($SP==1) {
        $contentname = str_replace('../','',$F);
    } else {
        $contentname = strTo($FZ, '.') . ADMIN_CONTENT_STR;
    }

    $Adir = GetDirectory(ADMIN_ARCHIVE_DIR, basename($F));

    foreach ($Adir as $file) {
        $file = str_replace('@', '/', $file);

        $archivedate = ExtractArchiveDate($file);

        $ArchiveFromFile = ExtractArchiveFile($file);

        if ($SP==1) {
            $ccheck = str_replace("$ROOT/", '', "$contentname");
            if ($ArchiveFromFile == $ccheck) {
                $ACfiles[] = $archivedate;
            }

        } else {
            $cdir = ($SITECONFIG['sitedir'])? strFrom($SITECONFIG['contentdir'], $SITECONFIG['sitedir']) : $SITECONFIG['contentdir'];
            $cdir = substr($cdir, 1);

            if ($ArchiveFromFile == "$cdir/$titlename") {
                $ATfiles[] = $archivedate;
            }
            if ($ArchiveFromFile == "$cdir/$contentname") {
                $ACfiles[] = $archivedate;
            }
        }


    }
    rsort($ACfiles);
    rsort($ATfiles);
}

//-----------READ THE DIRECTORY----------
if ($FILE_SET or !AdminSession('CONTENT_FILES', SESSION_FILES)) {
    $files = GetDirectory(ADMIN_CONTENT_DIR, ADMIN_CONTENT_STR);
    $files = SubTextBetweenArray('', ADMIN_CONTENT_STR, $files);
    //$files = array_map('removeextension', $files); 
    //$_SESSION['SITE_ADMIN'][SESSION_FILES]['CONTENT_FILES'] = $files;
} else {
    $files = AdminSession('CONTENT_FILES', SESSION_FILES);
}

//$files = str_replace('\\', '/', $files);  // fixes windows directory problems

//---------- clean variables from this file----------
unset($Adir);
unset($ArchiveFromFile);
