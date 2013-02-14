<?php
class DevRichard_BlogView extends BaseClass
{
    public $ShowArray               = false;
    private $table_blog             = 'website_blog';
    private $table_blog_id          = 'website_blog_id';
    public $client_id               = 0;
    
    
    public $image_template_blog_dates   = '';
    public $blog_picture_preview_width  = 100;
    public $blog_picture_preview_height = 75;


    public $OBJ_COMMENTS                = '';
    public $allow_user_comments         = true; // allow site users to add blog comments
    public $show_user_comments          = true; // show blog comments addeby users
    public $script_location_comments    = '/office/AJAX/dev_richard/blog_edit';
    
    public function  __construct()
    {
        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => '',
            'Description' => 'Create and manage touchpoint_chats',
            'Created'     => '2010-09-22',
            'Updated'     => '2010-09-22'
        );

        $this->Table                = 'website_blog';
        $this->Add_Submit_Name      = 'TOUCHPOINT_CHATS_SUBMIT_ADD';
        $this->Edit_Submit_Name     = 'TOUCHPOINT_CHATS_SUBMIT_EDIT';
        $this->Index_Name           = 'website_blog_id';
        $this->Flash_Field          = 'website_blog_id';
        $this->Default_Where        = '';  // additional search conditions
        $this->Default_Sort         = 'website_blog_id';  // field for default table sort
        $this->Default_Fields       = 'website_blog_id,client_wh_id,datetime,title,text,links_list,pictures_list,active,updated,created';
        $this->Unique_Fields        = '';

        $this->Field_Titles = array(
            'website_blog_id'   => 'Blog ID',
            'client_wh_id'      => 'Client WHID',
            'datetime'          => 'Date Time',
            'title'             => 'Title',
            'text'              => 'Text',
            'links_list'        => 'Links',
            'pictures_list'     => 'Pictures',
            'active'            => 'Active',
            'updated'           => 'Updated',
            'created'           => 'Created'
        );
        
        $this->OBJ_COMMENTS = new DevRichard_BlogComments();
    } // -------------- END __construct --------------
    
    
    public function SetFormArrays()
    {        
            $base_array = array(
                "code|<div style='padding:20px;'>",
                "form|$this->Action_Link|post|db_edit_form",
                'text|Touchpoint Chats Code|touchpoint_chats_code|N|6|6',
                'text|Wh Id|wh_id|N|11|11',
                'text|Shopping Order Id|shopping_order_id|N|11|11',
                'text|Category|category|N|45|45',
                'textarea|Chat|chat|N|60|4',
                'text|Line Count|line_count|N|11|11',
                'text|Locked|locked|N|4|4',
                "code|</div>",
            );

            if ($this->Action == 'ADD') {
                $base_array[] = "submit|Add Record|$this->Add_Submit_Name";
                $base_array[] = 'endform';
                $this->Form_Data_Array_Add = $base_array;
            } else {
                $base_array[] = 'checkbox|Active|active||1|0';
                $base_array[] = "submit|Update Record|$this->Edit_Submit_Name";
                $base_array[] = 'endform';
                $this->Form_Data_Array_Edit = $base_array;
            }
    }
    
    public function GetBlogEntry($id)
    {
        $record = $this->SQL->GetRecord(array(
            'table' => $this->table_blog,
            'keys'  => '*',
            'where' => "`{$this->table_blog_id}`={$id} AND `active`=1",
        ));
        
        $blog = "<div class='blog_body_wrapper'>";
        $blog .= $this->FormatBlogEntry($record);
        $blog .= "</div>";
        
        $this->AddStyleBlogOne();
        
        return $blog;
    }
    
    public function LoadBlog()
    {
        # LOAD ALL BLOGS FOR THE PARTICULAR CLIENT ID
        # ======================================================
        // `client_id`=$this->client_id AND
        $records = $this->SQL->GetArrayAll(array(
            'table' => $this->table_blog,
            'keys'  => '*',
            'where' => " `active`=1",
            'order_by' => 'datetime DESC',
        ));
        
        
        # GET THE LAYOUT TEMPLATE
        # ======================================================
        $template = $this->BlogTemplateOne();
        
        
        # FORMAT THE BLOG
        # ======================================================
        $blog = "<div class='blog_body_wrapper'>";
        foreach ($records as $record) 
        {
            $blog .= $this->FormatBlogEntry($record);
        }
        $blog .= "</div>";
        
        
        # OUTPUT THE STYLESHEET
        # ======================================================
        $this->AddStyleBlogOne();
        $this->AddScript();
        
        echo $blog;
    }
    
    public function FormatBlogEntry($record)
    {
        # INITIALIZE VARIABLES
        # ======================================================
        $pictures_list  = '';
        $links_list     = '';
        
        # SETUP LINKS
        if ($record['links_list']) {
            $search     = array("\r\n", "\n", "\r");
            $links      = str_replace($search, '|', $record['links_list']);
            $links      = explode('|', $links);
            foreach ($links as $link) {
                $links_list .= "<li><a href='{$link}' target='_blank'>{$link}</a></li>";
            }
            $links_list = "<ul>{$links_list}</ul>";
        }
        
        
        # SETUP PICTURES
        if ($record['pictures_list']) {
            $search     = array("\r\n", "\n", "\r");
            $pictures   = str_replace($search, '|', $record['pictures_list']);
            $pictures   = explode('|', $pictures);
            foreach ($pictures as $picture) {
                $pictures_list .= "
                <div class='blog_picture_wrapper'>
                <div class='blog_picture_preview'>
                    <img src='{$picture}' alt='' width='{$this->blog_picture_preview_width}' height='{$this->blog_picture_preview_height}' border='0' />
                </div>
                </div>
                ";
            }
            $pictures_list .= "<div style='clear:both;'></div>";
        }
        
        
        # SETUP TEXT
        $search     = array("\r\n", "\n", "\r");
        $text       = str_replace($search, '<br />', $record['text']);
        
        
        # SETUP DATE
        $date       = explode(' ', $record['datetime']);
        $date       = explode('-', $date[0]);
        $y          = $date[0];
        $m          = $date[1];
        $d          = $date[2];
        $m_display  = date("M", mktime(0, 0, 0, $m+1, 0, 0));
        
        
        # GET COMMENTS
        $comments       = ($this->show_user_comments) ? $this->OBJ_COMMENTS->GetCommentsApproved($record['website_blog_id']) : '';
        $comment_box    = ($this->allow_user_comments) ? $this->AddCommentBox($record['website_blog_id']) : '';
        
        # CREATE BLOG ENTRY
        $output = <<<OUTPUT
        <div class='blog_entry_wrapper'>
            
            <div style='float:left; width:75px;'>
                <div class="blog_postdate" title="{$m_display} {$d}, {$y}">
                    <div class="blog_month blog_m-{$m}">{$m_display}</div>
                    <div class="blog_day blog_d-{$d}">{$d}</div>
                    <div class="blog_year blog_y-{$y}">{$y}</div>
                </div>
            </div>

            <div style='float:left; width:500px;'>
                <div class="blog_content_wrapper">
                    <div class="blog_entry_title">{$record['title']}</div>
                    <div class="blog_entry_text">{$text}</div>
                    <div class="blog_entry_links">{$links_list}</div>
                    <div class="blog_entry_pictures">{$pictures_list}</div>
                </div>
                <div class="blog_comments_wrapper">
                    {$comments}
                    {$comment_box}
                </div>
            </div>
            
            <div style='clear:both;'></div>
            
        </div>
        <br /><br /><br />
OUTPUT;
        return $output;
    }
    
    public function AddCommentBox($website_blog_id)
    {
        $output = "
            <div class='blog_comment_collect_wrapper'>
                <div class='blog_comment_collect_header'>LEAVE A COMMENT</div>
                <div class='blog_comment_collect_form' id='comment_form_{$website_blog_id}'>
                        Blog ID <input type='text' size='30' class='comment_form_blog_id' value='{$website_blog_id}'><br />
                        Name <input type='text' size='30' class='comment_form_name'><br />
                        Email <input type='text' size='30' class='comment_form_email'> (required but private)<br />
                        Comment <textarea rows='5' cols='10' class='comment_form_comment'></textarea><br />
                        <input type='button' value='Add Comment' class='btn_comment_submit' parentID='comment_form_{$website_blog_id}' />
                    
                </div>
            </div>
            ";
        

        
        return $output;
    }
    
    public function BlogTemplateOne()
    {
        $blog_template = <<<TEMPLATE
        
        <div style='padding:10px; width:400px; height:400px;'>
            
            <div class="blog_postdate">
                <div class="blog_month blog_m-01">Jun</div>
                <div class="blog_day blog_d-17">30</div>
                <div class="blog_year blog_y-2014">2009</div>
            </div>
            <br /><br />
            <div class="blog_entry_wrapper">
                <div class="blog_entry_title"></div>
                <div class="blog_entry_text"></div>
                <div class="blog_entry_links"></div>
                <div class="blog_entry_pictures"></div>
            </div>
            <br /><br />
            <div class="blog_comments_wrapper">
                <div class="blog_comments_title"></div>
                <div class="blog_comments_text"></div>
            </div>
            
        </div>
        
TEMPLATE;

        $this->image_template_blog_dates = "/office/images/templates/blog/dates.png";

        
        

        return $blog_template;
    }
    
    public function AddStyleBlogOne()
    {
        $style = "
            /* DATE SPRITE SETTINGS */
            /* ======================================== */
            .blog_postdate {
              background-color:#F4F3EB;
              position: relative;
              width: 60px;
              height: 60px;
            }
            .blog_month, .blog_day, .blog_year {
              position: absolute;
              text-indent: -1000em;
              background-image: url({$this->image_template_blog_dates});
              background-repeat: no-repeat;
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

            
            $style .= "
            .blog_body_wrapper {
                background-color:#F4F3EB;
            }
            .blog_divider {
                padding-top:10px;
                border-bottom:1px solid #990000;
            }
            .blog_entry_wrapper {
                padding:10px;
                width:600px;
                border-top:1px solid #990000;
            }
            .blog_entry_title {
                font-size:18px;
                font-weight:bold;
                color: #990000;
                padding-bottom:10px;
            }
            .blog_entry_text {
                font-size:12px;
                font-weight:normal;
                color: #000;
            }
            .blog_entry_links {
                color: blue;
            }
            .blog_entry_pictures {
            
            }
            .blog_content_wrapper {
            
            }
            .blog_picture_wrapper {
                padding:5px;
                float:left;
            }
            .blog_picture_preview {
                border:1px solid #ccc;
                background-color:#fff;
                padding:3px;
                
            }
            
            .blog_comments_wrapper {
            
            }
            .blog_comments_title {
            
            }
            .blog_comments_text {
            
            }
            
            
            .blog_comment_collect_wrapper {
                border: 1px solid #bbb;
                background-color:#fff;
                padding:5px;
            }
            .blog_comment_collect_header {
                background-color:#f3f3f3;
                padding:5px;
                color:#000;
                font-size:14px;
                font-weight:bold;
            }
            .blog_comment_collect_form {
                padding:10px;
                background-color:#fff;
            }
            ";
            
            
        AddStyle($style);
    }

    public function AddScript()
    {
        AddScriptInclude('/jslib/jquery.livequery.js');
        $script = <<<SCRIPT
        
            // ADD COMMENT BUTTON FOR A SPECIFIC FORM HAS BEEN CLICKED
            // ===========================================================================
            $('.btn_comment_submit').livequery('click', function(event) {
                var parentID = "#" + $(this).attr("parentID");
                
                var ajax_load       = "<img src='/office/images/loader.gif' alt='loading...' />";
                //$(parentID).html(ajax_load);
                
                var sendArray                   = new Array();
                sendArray['website_blog_id']    = $(parentID + '> .comment_form_blog_id').val();
                sendArray['username']           = $(parentID + '> .comment_form_name').val();
                sendArray['email_address']      = $(parentID + '> .comment_form_email').val();
                sendArray['comment']            = $(parentID + '> .comment_form_comment').val();
                var serialize_array             = serialize(sendArray);
                
                var dataSend    = "data=" + serialize_array;
                var dataURL     = "{$this->script_location_comments};type=comments;action=addcomment"
                
                var checkForm = array_validate(sendArray);
                
                if (checkForm) {
                    $(parentID).append(ajax_load);
                    $.ajax({
                        type: "POST",
                        url: dataURL,
                        data: dataSend,
                        dataType: "html",
                        success: function(data) {
                            //alert(data);
                            if (data == '1') {
                                //$(parentID).fadeOut('slow');
                                $(parentID).html('COMMENT HAS BEEN SUBMITTED AND IS AWAITING APPROVAL TO DISPLAY ON WEBSITE');
                            } else {
                                alert('Failed to complete action');
                            }
                            
                            //$('body').css('cursor','auto');
                            //GetReservationsAjax();
                        }
                    });
                    return false;
                } else {
                    alert('Completely fill out all fields in form.');
                }
            });
        
SCRIPT;
        AddScriptOnReady($script);
        
        $script = <<<SCRIPT
            function array_validate(formData) {
                for (var index in formData) {
                    if (formData[index].length == 0) {
                        return false;
                    }
                }
                return true;
            }
SCRIPT;
        AddScript($script);
    }
    
    
    
}  // -------------- END CLASS --------------