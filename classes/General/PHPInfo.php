<?php
class General_PHPInfo
{
    public function __construct() 
    {
        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Description' => 'Output phpinfo() to the screen',
        );
    } // -------------- END __construct --------------
    
    
    public function Execute()
    {
        echo phpinfo();
    }
    
}  // -------------- END CLASS --------------