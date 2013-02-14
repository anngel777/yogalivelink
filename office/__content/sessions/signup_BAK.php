<?php
    $Ins = new General_ModuleInstructions;
    $Ins->AddInstructions('sessions/signup');
    
$RS = new General_Steps;
$Obj = new Sessions_Signup;



# SESSION SETUP
# =========================================
$Obj->sessions_id = (Get('sid')) ? Get('sid') : $Obj->sessions_id;



# CREATE THE PAGE CONTENT
# =========================================
$step = (Get('step')) ? Get('step') : 'start';
$RS->StepCurrentContent = $Obj->HandleStep($step);



# STEP SETUP
# =========================================
$CURSTEP    = $Obj->current_step;
$PREVSTEP   = $Obj->current_step - 1;
$NEXTSTEP   = $Obj->current_step + 1;

$RS->StepTableWidth             = '590px';
$RS->StepCurrent                = $CURSTEP;
$RS->StepPrevious               = $PREVSTEP;
$RS->StepNext                   = $NEXTSTEP;
$RS->StepTranslation            = 'Step';
$RS->StepArray                  = array(
    1 => 'Session Information',
    2 => 'Payment',
    3 => 'Confirmation',
    );



# OUTPUT THE STEP WITH CONTENT
# =========================================
echo $RS->CreateStepsContent();
$style = $RS->GetStyle();
AddStyle($style);