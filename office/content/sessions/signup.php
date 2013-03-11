<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: 
Last Updated By: 

       Filename: signup.php
    Description: CLASS :: Sessions_Signup
==================================================================================== */

$Obj = new Sessions_Signup();
$Obj->sessions_id = (Get('sid')) ? Get('sid') : $Obj->sessions_id;

if($Obj->checkIntakeForm()):
    $step = (Get('step')) ? Get('step') : 'start';
    echo $Obj->HandleStep($step);
else : ?>
    <div style="width:600px">
        <h3>You must fill out fitness form before you can schedule a session</h3>
    
        <?php $q = EncryptQuery("class=Profile_FormStandardIntake;v1=;v2=$Obj->WH_ID"); ?>
        <br>
        <a href='/office/class_execute?eq=<?php echo $q?>;template=overlay;DIALOGID=1'>
            <span style="font-size:16px;">edit my yoga: fitness form</span>
        </a>
    </div>
<?php endif;

# RESIZE THE CURRENT FRAME TO FIT CONTENTS
# ================================================
$script = <<<SCRIPT
    var dialogNumber = '';
    if (window.frameElement) {
        if (window.frameElement.id.substring(0, 13) == 'appformIframe') {
            dialogNumber = window.frameElement.id.replace('appformIframe', '');
        }
    }
    ResizeIframe();
SCRIPT;
AddScript($script);