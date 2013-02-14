<?php
if (ADMIN_LEVEL<9) return;
require "$LIB/form_helper.php";

$default_permissions = '0644';


$ftp_form_data = array(
    'code|<div id="configform">',
    'fieldset|Set FTP',
    "form|$THIS_PAGE_QUERY|post",
    'text|FTP Root|FTP_ROOT|Y|30|255',
    'password|FTP User|FTP_USER|Y|20|80|||View User',
    'password|FTP Pass|FTP_PASS|Y|20|80|||View Pass',
    'integer|Permissions|FTP_PERM|Y|4|4',
    'code|<div class="forminfo">',
    '@submit|Clear FTP|CLEAR_FTP',
    '@submit|Set FTP|SUBMIT_FTP',
    'code|</div>',
    'endform',
    'endfieldset',
    'code|</div>'
);

$ERROR = '';
if (Post('CLEAR_FTP')) {
    $_SESSION['SITE_ADMIN']['FTP'] = '';
    AddFlash('FTP Info Cleared');
    return;
}

if (Post('SUBMIT_FTP')) {
    $array = ProcessFormNT($ftp_form_data, $ERROR);
    if (!$ERROR) {
        extract($array, EXTR_OVERWRITE);

        $conn_id = ftp_connect(Server('SERVER_ADDR'));
        $ftp_result = ftp_login($conn_id, $FTP_USER, $FTP_PASS);

        // check path
        if ($ftp_result) {
            $hostname = "ftp://$FTP_USER:$FTP_PASS@" . $_SERVER['SERVER_ADDR'] . $FTP_ROOT . '/lib/mvptools.php';
            $context  = stream_context_create(array('ftp' => array('overwrite' => false)));
            $result   = @file_get_contents($hostname, 0, $context, 0, 10);
            if ($result) {
                $_SESSION['SITE_ADMIN']['FTP'] = $array;
                AddFlash('FTP Info Set');
                return;
            } else {
                $ERROR = 'FTP Path Error';
            }
        }

    }
}



if (!$ERROR or ($ERROR and Post('SUBMIT_FTP'))) {
    WriteError($ERROR);
    if (!Post('SUBMIT_FTP')) {
        if (empty($_SESSION['SITE_ADMIN']['FTP'])) {
            Form_PostValue('FTP_ROOT', $ROOT);
            Form_PostValue('FTP_PERM', $default_permissions);
        } else {
            Form_PostArray($_SESSION['SITE_ADMIN']['FTP']);
        }
    }
    $form = OutputForm($ftp_form_data, Post('SUBMIT_FTP'));
    echo str_replace('<br />', '', $form);
}

