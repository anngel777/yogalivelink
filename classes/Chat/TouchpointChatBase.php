<?php
class Chat_TouchpointChatBase extends BaseClass
{
    # CHAT TOOL SETTINGS
    # ======================================================================
    public $TableChatSettings       = 'touchpoint_chat_settings';
    public $reset_settings          = false;
    public $settings                = array();
    
    # CHAT TABLES
    # ======================================================================
    public $TableChats              = 'touchpoint_chats';
    
    
    public function  __construct()
    {
        parent::__construct();

        $this->GetSettings();
        
        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Description' => 'Touchpoint Base Class - commonly used functions',
        );
        
    } // -------------- END __construct --------------

    public function GetSettings()
    {
        # FUNCTION :: Get chat configuration settings from the database
    
        if (Session('settings_touchpoint_chat') && (!$this->reset_settings)) {
            $this->settings = Session('settings_touchpoint_chat');
        } else {
            
            $records = $this->SQL->GetArrayAll(array(
                'table' => $this->TableChatSettings,
                'keys'  => 'setting_name,setting_value',
                'where' => 'active=1',
            ));
            
            foreach ($records as $record) 
            {
                $name   = $record['setting_name'];
                $value  = $record['setting_value'];
                $this->settings[$name] = $value;
            }
            
            $_SESSION['settings_touchpoint_chat'] = $this->settings;
        }
    }
    
   


}  // -------------- END CLASS --------------