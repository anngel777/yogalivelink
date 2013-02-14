<?php
if ($AJAX) {
    if (Get('LINKS')) {
        $ADMIN->GetMenuLinks(Get('LINKS'));
    }
    return;
}

if (!Get('DIALOGID')) {
   AddSwap('id="dialogcontainer"', 'id="dialogcontainerfull"');
   AddSwap('id="dialogcontent"', 'id="dialogcontentfull"');
   AddStyle('#contentedit {width : 100%;}');
}

$F = Get('F');
AddSwap('@@EDITFILENAME@@', basename($F));
if (!$F) {
    AddError('No File Defined!');
    return;
}

$ADMIN->EditContent($F);
