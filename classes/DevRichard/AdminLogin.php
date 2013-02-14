<?php
class DevRichard_AdminLogin {
    
    # FUNCTION TURNS ON OR OFF THE SESSION VARIABLES TO TRICK SYSTEM INTO THINKING
    # USER HAS LOGGED INTO THE ADMIN+ PAGE. THIS WILL THEN SHOW ALL ERRORS ON THE SCREEN
    
    public function  __construct()
    {
        
    }
    
    public function SetAdmin($VAL_TF, $SHOW_RESULT=false)
    {
        if ($VAL_TF)
        {    
            $_SESSION['SITE_ADMIN'] = array(
                'AdminLevel'    => 9,
                'AdminUsername' => 'Richard Witherspoon',
                'AdminLoginOK'  => 'ok',
            );
            
            if ($SHOW_RESULT) echo "SESSION VARIABLES SET TO: $VAL_TF";
            
        } else {
            unset($_SESSION['SITE_ADMIN']);
        }

    }
    
}