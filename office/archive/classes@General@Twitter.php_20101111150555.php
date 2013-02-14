<?php
class General_Twitter
{
    
    
    //public function  __construct()
    //{
    //    
    //} // -------------- END __construct --------------

    
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