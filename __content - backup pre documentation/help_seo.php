<?php
$OBJ = new Profile_HelpCenter();
$OBJ->AddScript();
$OBJ->Use_Seo_Urls = true;

$content_right = $OBJ->Execute();
$content_left = $OBJ->ColumnLeft();


AddSwap('@@CONTENT_LEFT@@',$content_left);
AddSwap('@@CONTENT_RIGHT@@',$content_right);
AddSwap('@@PAGE_HEADER_TITLE@@','help center: get answers to your questions');