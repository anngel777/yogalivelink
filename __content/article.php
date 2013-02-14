<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: article
    Description: Show the "learning center" content - content pulled from database
==================================================================================== */

/*
// ---------- OVERRIDE DEFAULT CSS STYLES ----------
AddStyle("
    .articles_holder {
        border:1px solid #000;
        padding:10px;
        width:200px;
    }

    .articles_holder a {
        text-decoration:none;
        font-size:12px;
    }

    .articles_holder_header {
        background-color:#ccc;
        font-size:14px;
        font-weight:bold;
    }
");
*/

// ---------- GET SPECIFIC ARTICLE FROM DATABASE ----------
$OBJ            = new Website_Articles();
$article_menu   = $OBJ->GetArticleMenu();
$article        = $OBJ->HandleArticle(Get('article_id'), Get('eq'));

// ---------- GET CONTENT FROM DATABASE AND SWAP INTO PAGE ----------
AddSwap('@@CONTENT_LEFT@@',$article_menu);
AddSwap('@@CONTENT_RIGHT@@',$article);
AddSwap('@@PAGE_HEADER_TITLE@@','learning center: articles, trends and tips');

