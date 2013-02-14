<?php
class General_CSSStyle
{
    public $Image_Template_Blog_Dates       = '/office/images/templates/blog/dates.png';
    
    public function __construct() 
    {
        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Description' => 'General CSS styles used throughout website - requiring calculations - for blog calendar dates',
        );
    } // -------------- END __construct --------------
    
    public function AddStyleBlog()
    {
        $style = "
            /* DATE SPRITE SETTINGS */
            /* ======================================== */
            .blog_postdate {
              background-color:#F4F3EB;
              position: relative;
              width: 60px;
              height: 60px;
z-index:1;
            }
            .blog_month, .blog_day, .blog_year {
              position: absolute;
              text-indent: -1000em;
              background-image: url({$this->Image_Template_Blog_Dates});
              background-repeat: no-repeat;
z-index:1;
            }
            .blog_month { top: 2px; left: 0; width: 32px; height: 24px;}
            .blog_day { top: 25px; left: 0; width: 32px; height: 25px;}
            .blog_year { top: 2px; left: 32px; width: 17px; height: 48px;}
            /*.blog_year { bottom: 0; right: 0; width: 17px; height: 48px;}*/


            .blog_m-01 {background-position:0 4px}
            .blog_m-02 {background-position:0 -28px}
            .blog_m-03 {background-position:0 -57px}
            .blog_m-04 {background-position:0 -90px}
            .blog_m-05 {background-position:0 -121px}
            .blog_m-06 {background-position:0 -155px}
            .blog_m-07 {background-position:0 -180px}
            .blog_m-08 {background-position:0 -216px}
            .blog_m-09 {background-position:0 -246px}
            .blog_m-10 {background-position:0 -273px}
            .blog_m-11 {background-position:0 -309px}
            .blog_m-12 {background-position:0 -340px}
            ";
            
            
        for ($d=0; $d<31; $d++) {
            $pos_left           = ($d < 16) ? '-50' : '-100';
            $pos_top_offset     = 31;
            $pos_top            = ($d < 16) ? -($d * $pos_top_offset) : -(($d-16) * $pos_top_offset);
            
            $day_str            = $d + 1;
            $day                = str_pad($day_str, 2, "0", STR_PAD_LEFT);
            
            $style .= ".blog_d-{$day} { background-position: {$pos_left}px {$pos_top}px;}
            ";
        }
        
        
        
        for ($y=0; $y<9; $y++) {
            $pos_left           = '-150';
            $pos_top_offset     = 50;
            
            $year_str           = $y + 6;
            $pos_top            = -($y * $pos_top_offset);
            $year               = '20' . str_pad($year_str, 2, "0", STR_PAD_LEFT);
            
            $style .= ".blog_y-{$year} { background-position: {$pos_left}px {$pos_top}px;}
            ";
        }
        
        AddStyle($style);
    }
    
} // end class