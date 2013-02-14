<?php
$client_id = 666;

$type = Get('type');
switch ($type) {
    case 'comments':
        $Obj = new DevRichard_BlogComments();
        //$Obj->client_id = $client_id;
        
            $Ins = new General_ModuleInstructions;
            $Ins->AddInstructions('dev_richard/blog_edit', 'comments');
        
        echo "
        <a href='/office/dev_richard/blog_edit;type=blog'>blog</a><br />
        <a href='/office/dev_richard/blog_edit;type=comments'>comments</a><br />
        ";
        
        if ($AJAX) {
            $Obj->ProcessAjax();
        } else {
            $Obj->ListTable();
        }
        
        
    break;
    case 'blog':
    default:
        $Obj = new DevRichard_BlogEdit();
        $Obj->client_id = $client_id;

            $Ins = new General_ModuleInstructions;
            $Ins->AddInstructions('dev_richard/blog_edit', 'blog');
            
        echo "
        <a href='/office/dev_richard/blog_edit;type=blog'>blog</a><br />
        <a href='/office/dev_richard/blog_edit;type=comments'>comments</a><br />
        ";

        $Obj->ListTable();
    break;
}