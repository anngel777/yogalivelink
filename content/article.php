<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: article
    Description: Show the "learning center" content - content pulled from database
==================================================================================== */

// ---------- GET SPECIFIC ARTICLE FROM DATABASE ----------
$OBJ            = new Website_Articles();
$article_menu   = $OBJ->GetArticleMenu();
$article        = $OBJ->HandleArticle(Get('article_id'), Get('eq'));

$article_menu .= '
<br /><br />
<a href="/test_computer">
<img src="/images/test_equipment.jpg">
</a>
<br /><br />
';

// ---------- GET CONTENT FROM DATABASE AND SWAP INTO PAGE ----------
AddSwap('@@CONTENT_LEFT@@',$article_menu);
AddSwap('@@CONTENT_RIGHT@@',$article);
AddSwap('@@PAGE_HEADER_TITLE@@','learning center: articles, trends and tips');