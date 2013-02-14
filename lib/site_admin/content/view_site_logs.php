<?php
$db_log_file = $ROOT . $ADMIN->Site_Config['logdir'] . '/sitelog.db';

if (file_exists($db_log_file)) {
    $SITECONFIG = $ADMIN->Site_Config;
    include "$LIB/site_admin/helper/admin_viewlogs_db.php";
} else {
    $SITECONFIG = $ADMIN->Site_Config;
    include "$LIB/site_admin/helper/admin_viewlogs.php";
}
exit;