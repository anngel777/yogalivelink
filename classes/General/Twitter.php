<?php
class General_Twitter
{
    public function __construct() 
    {
        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Description' => 'Create a twitter share button',
        );
    } // -------------- END __construct --------------

    
    public function CreateButton($url='', $text='')
    {
        $text = ($text) ? $text : "Check out this awesome Yoga product!";
        $button = <<<BUTTON
            <a href="http://twitter.com/share" class="twitter-share-button" data-url="{$url}" data-text="{$text}" data-count="none">
                Tweet
            </a>
            <script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
BUTTON;
        return $button;
    }
    
}  // -------------- END CLASS --------------