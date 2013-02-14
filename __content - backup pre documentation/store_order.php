<?php
//if(empty($HTTPS)) header("Location: $HTTPS_URI");
require_once "$LIB/form_helper.php";
$SQL->SetTrace(true);


echo '<div id="ordercontent">';

$SO = new Store_YogaStoreOrder();
if (!$AJAX) {
    $SO->Ajax_Cart = false;
}
$SO->ProcessOrderPage();

echo '</div>';  // end ordercontent







  
    
    
  
  
      
      
