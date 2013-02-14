<?php
//==========================================================
//                  VIEW/CHANGE DOCUMENT FILES
//==========================================================
if ($DELETE_DOCUMENT) {
    unlink("$DOCUMENT_ROOT$DELETE_DOCUMENT");
    $VD=1;
}


if ($RENAMEFILE_DOCUMENT) {
    if (!(file_exists("$DOCUMENT_ROOT$NEWNAME_DOCUMENT"))) {
        $filename1  = "$DOCUMENT_ROOT$OLDNAME_DOCUMENT";
        $filename2  = str_replace(' ','_',"$DOCUMENT_ROOT$NEWNAME_DOCUMENT");
        rename($filename1,$filename2);
        LogUpdate(ADMIN_USERNAME,'Rename DOCUMENT File',"$OLDNAME_DOCUMENT - $NEWNAME_DOCUMENT");
        $VD=1;
    } else {
        $RENAME_DOCUMENT = $OLDNAME_DOCUMENT; $DuplicateFile=1;
    }
}


if ($RENAME_DOCUMENT) {
    print <<<RENAMEDOCUMENTLABEL
  <form method=post action="$ADMIN_FILE">
  <input type="hidden" name="OLDNAME_DOCUMENT" value="$RENAME_DOCUMENT">
  <table align="center" border="0" cellpadding="3" style="color:#fff; font-size:1.2em;">
  <tr>
    <td align="right">Old Filename:</td><td>$RENAME_DOCUMENT</td>
  </tr>
  <tr>
    <td align="right">New Filename:</td>
    <td><input type="text" name="NEWNAME_DOCUMENT" size="80" value="$RENAME_DOCUMENT" /></td>
  </tr>
    <td></td>
    <td><input type="Submit" name="RENAMEFILE_DOCUMENT" value="Rename File" /></td>
  </tr></table>
RENAMEDOCUMENTLABEL;

    if ($DuplicateFile) {
        print '<p style="text-align:center; color:white;">File: <b>'.$NEWNAME_DOCUMENT.'</b> already exists.</p>';
        print '</form>';
    }
}


//==========================================================
//                  VIEW DOCUMENT FILES
//==========================================================
if ($VD=='1') {
    print '<table cellpadding="5" bgcolor="black" align="center">';
    print '<tr><td colspan="3" class="header" align="center"><span class="subheader">Document Files</span></td></tr>';

    foreach ($DocLinkDirs as $dir) {
        $files = GetDirectory("$DOCUMENT_ROOT$dir",'');
        printqn("<tr><td colspan=`3` style=`font-size:1.2em; font-weight:bold; background-color:#ccc`>$dir</td></tr>");
        $count=0;
        foreach ($files as $fi) {
            $Lfilename="$dir/$fi";
            $filename="$DOCUMENT_ROOT$dir/$fi";
            $t=date("m\/d\/Y",filemtime($filename));
            $fsize = number_format(filesize($filename)/1024,1).' KB';
            $count++;
            print '<tr style="background-color:#fff;">';
            printqn("<td>$count. <a href=`$Lfilename` target=`_blank`><b>$dir/$fi</b></a></td>");
            printqn("<td style=`font-size: 8pt`>Version: $t &mdash; $fsize</td>");
            printqn("<td><a class=`titlebutton` href=`$ADMIN_FILE?RENAME_DOCUMENT=$dir/$fi`>Rename</a>&nbsp;
                <a class=`contentbutton` href=`$ADMIN_FILE?DELETE_DOCUMENT=$dir/$fi` onclick=`return confirm('Are you sure you want to delete [$dir/$fi]?')`>Delete</a>
                </td></tr>");
        }
    }
    print "</table>\n";

}
