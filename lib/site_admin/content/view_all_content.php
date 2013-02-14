<?php
if ($PAGE['original_name'] == 'view_file_content') {
    $F = Get('F');
    echo file_get_contents($F);
    exit;
}
$page = ($PAGE['original_name'] == 'view_all_content')? 1 : 0;
$ADMIN->ViewAllContent($page);
