<?php
$A = Get('A');
$F = Get('F');
$OPT = Get('OPT');

if ($A == 'COPY') {
    $ADMIN->ModifyFileCopy($F, $OPT);
} elseif ($A == 'DELETE') {
    $ADMIN->ModifyFileDelete($F, $OPT);
} elseif ($A == 'RENAME') {
    $ADMIN->ModifyFileRename($F, $OPT);
} elseif ($A == 'RESIZE') {
    include "$LIB/form_helper.php";
    $ADMIN->ModifyFileResizeImage($F);
} else {
    AddError('Action Not Defined');
}

