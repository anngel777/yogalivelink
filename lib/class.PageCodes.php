<?php
//file: /class.PageCodes.php
class PageCodes
{
    public $YouTube_Tag = 'YOUTUBE';
    public $YouTube_Template = '
<div class="video">
    <object width="425" height="344" type="application/x-shockwave-flash" data="http://www.youtube.com/v/@@&amp;hl=en&amp;fs=1">
    <param name="movie" value="http://www.youtube.com/v/@@&amp;hl=en&amp;fs=1" />
    </object>
</div>
';
    public $Mp3_Tag = 'MP3';
    public $Mp3_Id = 0;

    public $Mp3_Template = '
<object id="audioplayer@ID@" width="290" height="24" data="/media/player.swf" type="application/x-shockwave-flash">
<param name="FlashVars" value="playerID=@ID@&amp;soundFile=@FILE@" />
<param name="quality" value="high" />
<param name="menu" value="false" />
<param name="wmode" value="transparent" />
<param name="src" value="/media/player.swf" />
</object>
';

    public $Contact_Page_Link = '/contact';
    
    public $Download_Tag = 'DOWNLOAD';
    public $Download_Link = '/lib/download.php?';

    public function ProcessPageContent($content)
    {
        if (strpos($content, "[$this->YouTube_Tag:")) {
            $this->SwapYouTube($content);
        }
        if (strpos($content, "[$this->Mp3_Tag:")) {
            $this->SwapMp3($content);
        }
        if (strpos($content, "[EMAIL:")) {
            $this->SwapMailLinks($content);
        }
        if (strpos($content, "[$this->Download_Tag:")) {
            $this->SwapDownload($content);
        }
        return $content;
    }

    public function SwapMailLinks(&$content)
    {
        $array = TextBetweenArray('[EMAIL:', ']', $content);
        if ($array) {
            foreach ($array as $line) {

                $newline = str_replace(array('&', ';'), array('\&', '\;') , $line);

                list($name, $email, $company, $subject, $message) = explode('|', $newline . '||||');
                $eq = "name=$name;email=$email";


                if ($company) {
                    $eq .= ";company=$company";
                }
                if ($subject) {
                    $eq .= ";subject=$subject";
                }
                if ($message) {
                    $eq .= ";message=$message";
                }

                $link = $this->Contact_Page_Link . ':eq=' . EncryptQuery($eq);

                $content = str_replace("[EMAIL:$line]", $link, $content);
            }
        }
    }

    public function SwapYouTube(&$content)
    {
        $array = TextBetweenArray("[$this->YouTube_Tag:", ']', $content);
        if ($array) {
            foreach ($array as $key) {
                $video = str_replace('@@', trim($key), $this->YouTube_Template);
                $content = str_replace("[$this->YouTube_Tag:$key]", $video, $content);
            }
        }
    }

    public function SwapMp3(&$content)
    {
        $array = TextBetweenArray("[$this->Mp3_Tag:", ']', $content);
        if ($array) {
            foreach ($array as $file) {
                $this->Mp3_Id++;
                $audio = str_replace(array('@ID@', '@FILE@'), array($this->Mp3_Id, trim($file)), $this->Mp3_Template);
                $content = str_replace("[$this->Mp3_Tag:$file]", $audio, $content);
            }
        }
    }
    
    public function SwapDownload(&$content)
    {
        $array = TextBetweenArray("[$this->Download_Tag:", ']', $content);
        if ($array) {
            foreach ($array as $file) {
                $f = trim($file);
                $eq = EncryptQuery("f=$f");
                $link = $this->Download_Link . $eq;                
                $content = str_replace("[$this->Download_Tag:$file]", $link, $content);
            }
        }
    }

}