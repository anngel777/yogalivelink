<h1 class="pagehead">Update Your Profile</h1>
<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: update_profile.php
    Description: CLASS :: AdminUsers
==================================================================================== */

    $Ins = new General_ModuleInstructions;
    $Ins->AddInstructions('update_profile');

$Obj = new AdminUsers();
$Obj->UpdateProfile($USER->User_Id);