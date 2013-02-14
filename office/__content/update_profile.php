<h1 class="pagehead">Update Your Profile</h1>
<?php
    $Ins = new General_ModuleInstructions;
    $Ins->AddInstructions('update_profile');

$Obj = new AdminUsers;
$Obj->UpdateProfile($USER->User_Id);


