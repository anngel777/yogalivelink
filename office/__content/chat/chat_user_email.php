<?php

$code       = Get('code');
$id         = Get('id');
$email      = Get('email');

$OBJ    = new Chat_TouchpointChatsEmail();
$OBJ->EndChatUser           = true;
$OBJ->current_chat_id       = $id;
$OBJ->current_chat_code     = $code;
$OBJ->current_chat_email    = $email;
    
if ($AJAX) {
    $OBJ->ProcessAjax();
} else {
    $Ins = new General_ModuleInstructions;
    $Ins->AddInstructions('chat/chat_user_email');
    
    $OBJ->EmailChatToUser();
}


#AddDialogClassInfo($OBJ->ClassInfo); # WILL OUTPUT CLASS INFORMATION TO BOTTOM OF DIALOG SCREEN





## RESIZE THE CURRENT FRAME TO FIT CONTENTS
# ================================================
if (Get('resize')) {
    $script = "
    function resizeWinTo( idOfDiv ) {
      
      var oH    = getRefToDivMod( idOfDiv ); if( !oH ) { return false; }
      var oW    = oH.clip ? oH.clip.width : oH.offsetWidth;
      var oH    = oH.clip ? oH.clip.height : oH.offsetHeight; if( !oH ) { return false; }
      var x     = window; x.resizeTo( oW + 200, oH + 200 );
      var myW   = 0, myH = 0, d = x.document.documentElement, b = x.document.body;
      
      if( x.innerWidth ) { myW = x.innerWidth; myH = x.innerHeight; }
      else if( d && d.clientWidth ) { myW = d.clientWidth; myH = d.clientHeight; }
      else if( b && b.clientWidth ) { myW = b.clientWidth; myH = b.clientHeight; }
      if( window.opera && !document.childNodes ) { myW += 16; }
      
      var newWidth  = oW + ( ( oW + 200 ) - myW );
      var newHeight = oH + ( (oH + 200 ) - myH );
      
      //alert(newWidth + ' x ' + newHeight);
      
      x.resizeTo(newWidth, newHeight);
    }
    function getRefToDivMod( divID, oDoc ) {
      if( !oDoc ) { oDoc = document; }
      if( document.layers ) {
        if( oDoc.layers[divID] ) { return oDoc.layers[divID]; } else {
          for( var x = 0, y; !y && x < oDoc.layers.length; x++ ) {
            y = getRefToDivMod(divID,oDoc.layers[x].document); }
          return y; } }
      if( document.getElementById ) { return oDoc.getElementById(divID); }
      if( document.all ) { return oDoc.all[divID]; }
      return document[divID];
    }";
    AddScript($script);
    
    #$script = "resizeWinTo('dialog_body');";
    $script = "resizeWinTo('dialogcontainer');";
    #$script = "resizeWinTo('dialogcontent');";
    
    AddScriptOnReady($script);
} else {
    $script = "
    //alert('ResizeIframe');
        var dialogNumber = '';
        if (window.frameElement) {
            if (window.frameElement.id.substring(0, 13) == 'appformIframe') {
                dialogNumber = window.frameElement.id.replace('appformIframe', '');
            }
        }
        ResizeIframe();
    ";
    AddScript($script);
}