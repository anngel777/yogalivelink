<?php
class General_Facebook
{
    
    //public function  __construct()
    //{
    //    
    //} // -------------- END __construct --------------

    
    public function CreateButton($url)
    {
        $button = <<<BUTTON
            <script src='http://connect.facebook.net/en_US/all.js#xfbml=1'></script>
            <fb:like href='{$url}' action='recommend' font='segoe ui'></fb:like>
BUTTON;
        return $button;
    }
    
}  // -------------- END CLASS --------------

