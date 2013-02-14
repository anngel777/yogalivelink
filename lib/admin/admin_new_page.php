<?php
//================ Input New Page ================
if($NEW){

    echo '<table class="upload" align="center"><tbody><tr><td align="left">';


    if (HaveSubmit('SUBMITNEWFILE')) {
        echo $NEW_FILE_RESULT;
    }

    if (!HaveSubmit('SUBMITNEWFILE') or ($ERROR)) {
        WriteError($ERROR);
        echo OutputForm($NewFileFormArray, Post('SUBMITNEWFILE'));
    }
    echo '</td></tr></tbody></table>';

}
