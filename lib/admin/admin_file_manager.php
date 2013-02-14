<?php
//==========================================================
//                    FILE MANAGER
//==========================================================

if ($COPY or $RENAME) {
    echo '<table class="upload" align="center"><tr><td>';
}


if ($COPY) {
print <<<COPYLABEL
  <form method="post" action="$ADMIN_FILE">
  <input type="hidden" name="OLDNAME" value="$COPY" />
  <table align="center" border="0" cellpadding="3" style="color:#fff; font-size:1.2em;">
  <tr>
    <td align="right">Copy from Filename:</td><td>$COPY</td>
  </tr>
  <tr>
    <td align="right">Copy to Filename:</td>
    <td><input type="text" name="NEWNAME" size="50" value="$COPY" /></td>
  </tr>
  <tr>
    <td></td>
    <td><input type="Submit" name="COPYFILE" value="Copy File" /></td>
  </tr>
  </table>
COPYLABEL;

    if ($DuplicateFile) {
        print '<p style="text-align:center; color:white;">File: <b>'.$NEWNAME.'</b> already exists.</p>';
    }

    print '</form>';
}



if ($RENAME) {
    print <<<RENAMELABEL
  <form method="post" action="$ADMIN_FILE">
  <input type="hidden" name="OLDNAME" value="$RENAME" />
  <table align="center" border="0" cellpadding="3" style="color:#fff; font-size:1.2em;">
  <tr>
    <td align="right">Old Filename:</td><td>$RENAME</td>
  </tr>
  <tr>
    <td align="right">New Filename:</td>
    <td><input type="text" name="NEWNAME" size="50" value=$RENAME /></td>
  </tr>
    <td></td>
    <td><input type="submit" name="RENAMEFILE" value="Rename File" /></td>
  </tr></table>
RENAMELABEL;

    if ($DuplicateFile) {
        print '<p style="text-align:center; color:white;">File: <b>'.$NEWNAME.'</b> already exists.</p>';
    }
    print '</form>';
}

if ($COPY or $RENAME) {
    echo '</td></tr></table>';
}




if ($FM=='1') {
    print '<table id="filemanager" cellpadding="5" border="0" align="center">';
    print '<tr>
    <td colspan="6" class="header" align="center">
      <span class="subheader">File Manager</span>
    </td>
</tr>';

    $count=0;

    foreach ($files as $fi) {
    //---------------output the info-------
        $filename = ADMIN_CONTENT_DIR."/$fi".ADMIN_CONTENT_STR;
        $titlename = ADMIN_CONTENT_DIR."/$fi".ADMIN_TITLE_STR;

        if (file_exists($titlename)) {
            $name = TextBetween('<name>','</name>',file_get_contents($titlename));
            $fileback = empty($name)? ' style="background-color:#ccc;"' : '';
        } else {
            $name = '(NO TITLE FILE)';
            $fileback = ' style="background-color:#ff7;"';
        }
        $updated = date("m\/d\/Y", filemtime($filename));
        $filesize = number_format(filesize($filename)).' Bytes';
        $count++;
        $link="$ADMIN_FILE?F=$fi";
        $deletelink = "$ADMIN_FILE?DELETE=$fi$SV".'FM=1';

        print <<<FMLABEL

  <tr>
  <!-- ===================FILE: $fi ===================== -->

  <td align="right">
  <span class="fileheader">$count.</span>
  </td>
  <td$fileback>
    <a class="fileheader" href="$link">$fi</a>
  </td>
  <td>
    <span class="updated">$updated<br />$filesize</span>
  </td>
  <td>
      <a class="titlebutton" href="$ADMIN_FILE?COPY=$fi">Copy</a>
  </td>
  <td>
      <a class="titlebutton" href="$ADMIN_FILE?RENAME=$fi">Rename</a>
  </td>
  <td>
     <a class="contentbutton" href="$deletelink"
        onclick="return confirm('Are you sure you want to delete [$fi]?')">Delete</a>
  </td>
  </tr>

FMLABEL;
    }
    echo '</table>';
}
?>