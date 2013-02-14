<?php
$client_id = 666;

    $Ins = new General_ModuleInstructions;
    $Ins->AddInstructions('dev_richard/blog_view');

$Obj = new DevRichard_BlogView();
$Obj->client_id = $client_id;
$Obj->LoadBlog();