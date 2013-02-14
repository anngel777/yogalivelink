<?php
class DevRichard_BlogComments extends BaseClass
{
    public $table_blog_comments         = 'website_blog_comments';
    public $img_no                      = '/office/images/buttons/cancel.png';
    public $img_yes                     = '/office/images/buttons/save.png';

    public $script_location             = '/office/AJAX/dev_richard/blog_edit';
    
    public function  __construct()
    {
        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => '',
            'Description' => 'Create and manage website_blog_comments',
            'Created'     => '2010-11-02',
            'Updated'     => '2010-11-02'
        );

        $this->Table                = 'website_blog_comments';
        $this->Add_Submit_Name      = 'WEBSITE_BLOG_COMMENTS_SUBMIT_ADD';
        $this->Edit_Submit_Name     = 'WEBSITE_BLOG_COMMENTS_SUBMIT_EDIT';
        $this->Index_Name           = 'website_blog_comments_id';
        $this->Flash_Field          = 'website_blog_comments_id';
        $this->Default_Where        = '';  // additional search conditions
        $this->Default_Sort         = 'website_blog_comments_id';  // field for default table sort
        $this->Default_Fields       = 'client_wh_id,datetime,username,email_address,comment,approved,denied,website_blog_id';
        $this->Unique_Fields        = '';
        
        $this->Field_Titles = array(
            'website_blog_comments_id'  => 'Website Blog Comments Id',
            'website_blog_id'           => 'Website Blog Id',
            'client_wh_id'              => 'Client Wh Id',
            'datetime'                  => 'Datetime',
            'username'                  => 'Username',
            'email_address'             => 'Email Address',
            'comment'                   => 'Comment',
            'approved'                  => 'Approved',
            'denied'                    => 'Denied',
            'active'                    => 'Active',
            'updated'                   => 'Updated',
            'created'                   => 'Created'
        );

    } // -------------- END __construct --------------


    public function ProcessAjax()
    {
        $action = Get('action');
        $website_blog_comments_id = Get('id');
        
        switch($action) {
            case 'accept':
                $result = $this->CommentAccept($website_blog_comments_id);
                //$result = "ACCEPT";
                echo $result;
            break;
            case 'reject':
                $result = $this->CommentReject($website_blog_comments_id);
                //$result = "REJECT";
                echo $result;
            break;
            case 'addcomment':
                $data = Post('data');
                $result = $this->CommentAdd($data);
                //$result = "REJECT";
                echo $result;
            break;
        }
    }
    
    public function CommentAccept($website_blog_comments_id)
    {
        $result = $this->SQL->UpdateRecord(array(
            'table'         => $this->table_blog_comments,
            'key_values'    => "`approved`=1, `denied`=0",
            'where'         => "`website_blog_comments_id`={$website_blog_comments_id} AND active=1",
        ));
        return $result;
    }
    
    public function CommentReject($website_blog_comments_id)
    {
        $result = $this->SQL->UpdateRecord(array(
            'table'         => $this->table_blog_comments,
            'key_values'    => "`approved`=0, `denied`=1",
            'where'         => "`website_blog_comments_id`={$website_blog_comments_id} AND active=1",
        ));
        return $result;
    }
    
    public function CommentAdd($data)
    {
        $FormArray = unserialize($data);
        $FormArray['datetime'] = Date("Y-m-d H:i:s");
        
        $data   = $this->FormatDataForInsert($FormArray);
        $data   = explode('||', $data);
        $keys   = $data[0];
        $values = $data[1];
        
        $result = $this->SQL->AddRecord(array(
            'table'     => $this->table_blog_comments,
            'keys'      => $keys,
            'values'    => $values,
        ));
        //echo "<br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        
        return $result;
    }

    public function SetFormArrays()
    {
        $base_array = array(
            "form|$this->Action_Link|post|db_edit_form",
            'text|Client Wh Id|client_wh_id|N|11|11',
            'text|Website Blog Id|website_blog_id|N|11|11',
            'text|Datetime|datetime|N|60|255',
            'text|Username|username|N|60|255',
            'text|Email Address|email_address|N|60|255',
            'textarea|Comment|comment|N|60|4',
            'checkbox|Approved|approved||1|0',
            'checkbox|Denied|denied||1|0',
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

    
    public function GetCommentsPending($id)
    {
        $records = $this->SQL->GetArrayAll(array(
            'table' => $this->table_blog_comments,
            'keys'  => '*',
            'where' => "`website_blog_id`=$id AND `approved`=0 AND `denied`=0 AND `active`=1",
            //'where' => "`website_blog_id`=$id AND `active`=1",
            'order_by' => 'datetime DESC',
        ));
        
        
        # FORMAT THE COMMENTS
        # ======================================================
        $comment = "<br /><br /><br /><br /><br /><div class='blog_comments_wrapper'>";
        foreach ($records as $record) 
        {
            $comment .= "
                <div class='blog_comment' id='blog_comment_{$record['website_blog_comments_id']}'>
                    <div class='blog_comment_header'>
                        <div style='float:left;'><span class='blog_comment_username'>{$record['username']}</span> (<span class='blog_comment_email'>{$record['email_address']}</span>)</div>
                        <div style='float:right;'><span class='blog_comment_date'>{$record['datetime']}</span></div>
                        <div style='clear:both;'></div>
                    </div>
                    <div class='blog_comment_text'>
                    
                        <div style='float:left; width:80%;'>
                            {$record['comment']}
                        </div>
                        
                        <div style='float:right; width:15%;'>
                            <div><a href='#' onclick=\"CommentAction('reject', {$record['website_blog_comments_id']}); return false;\"><img src='{$this->img_no}' alt='Reject Comment' border='0' /> Reject</a></div>
                            <div><a href='#' onclick=\"CommentAction('accept', {$record['website_blog_comments_id']}); return false;\"><img src='{$this->img_yes}' alt='Accept Comment' border='0' /> Accept</a></div>
                        </div>
                        
                        <div style='clear:both;'></div>
                        
                    </div>
                </div>
                <br />
                ";
            
            //$comment .= $this->FormatBlogEntry($record);
        }
        $comment .= "</div>";
        
        
        $this->AddStyle();
        $this->AddScript();
        
        
        return $comment;
    }

    public function GetCommentsApproved($id)
    {
        $records = $this->SQL->GetArrayAll(array(
            'table' => $this->table_blog_comments,
            'keys'  => '*',
            'where' => "`website_blog_id`=$id AND `approved`=1 AND `denied`=0 AND `active`=1",
            'order_by' => 'datetime DESC',
        ));
        
        
        # FORMAT THE COMMENTS
        # ======================================================
        $comment = "<div class='blog_comments_wrapper'>";
        foreach ($records as $record) 
        {
            $comment .= "
                <div class='blog_comment' id='blog_comment_{$record['website_blog_comments_id']}'>
                    <div class='blog_comment_header'>
                        <div style='float:left;'><span class='blog_comment_username'>{$record['username']}</span> (<span class='blog_comment_email'>{$record['email_address']}</span>)</div>
                        <div style='float:right;'><span class='blog_comment_date'>{$record['datetime']}</span></div>
                        <div style='clear:both;'></div>
                    </div>
                    <div class='blog_comment_text'>
                        {$record['comment']}
                    </div>
                </div>
                <br />
                ";
        }
        $comment .= "</div>";
        
        
        $this->AddStyle();
        $this->AddScript();
        
        
        return $comment;
    }

    
    public function AddStyle()
    {
        $style = "
            .blog_comment {
                border:1px solid #ddd;
            }
            .blog_comment_header {
                background-color:#ccc;
                padding:3px;
            }
            .blog_comment_date {
                font-size:11px;
            }
            .blog_comment_username {
                font-size:12px;
                font-weight:bold;
            }
            .blog_comment_email {
                font-size:9px;
            }
            .blog_comment_text {
                background-color:#fff;
                padding:10px;
                font-size:12px;
            }
            .blog_comment_text a {
                text-decoration:none;
            }
        ";
        
        AddStyle($style);
    }

    
    public function AddScript()
    {
        $script = <<<SCRIPT
        function CommentAction(action, id) {
            
            var dataSend    = "type=comments&action=" + action + "&id=" + id;
            var divID       = "blog_comment_" + id;
            
            $.ajax({
                type: "GET",
                url: "{$this->script_location}.php",
                data: dataSend,
                /*dataType: "html",*/
                success: function(data) {
                    //alert(data);
                    if (data = '1') {
                        $('#' + divID).fadeOut('slow');
                    } else {
                        alert('Failed to complete action');
                    }
                    
                    //$('body').css('cursor','auto');
                    //GetReservationsAjax();
                }
            });
            return false;
        }
        
        
        function serialize( mixed_value ) {
            var _getType = function( inp ) {
                var type = typeof inp, match;
                var key;
                if (type == 'object' && !inp) {
                    return 'null';
                }
                if (type == "object") {
                    if (!inp.constructor) {
                        return 'object';
                    }
                    var cons = inp.constructor.toString();
                    match = cons.match(/(\w+)\(/);
                    if (match) {
                        cons = match[1].toLowerCase();
                    }
                    var types = ["boolean", "number", "string", "array"];
                    for (key in types) {
                        if (cons == types[key]) {
                            type = types[key];
                            break;
                        }
                    }
                }
                return type;
            };
            var type = _getType(mixed_value);
            var val, ktype = '';
            
            switch (type) {
                case "function": 
                    val = ""; 
                    break;
                case "undefined":
                    val = "N";
                    break;
                case "boolean":
                    val = "b:" + (mixed_value ? "1" : "0");
                    break;
                case "number":
                    val = (Math.round(mixed_value) == mixed_value ? "i" : "d") + ":" + mixed_value;
                    break;
                case "string":
                    val = "s:" + mixed_value.length + ":\"" + mixed_value + "\"";
                    break;
                case "array":
                case "object":
                    val = "a";
                    var count = 0;
                    var vals = "";
                    var okey;
                    var key;
                    for (key in mixed_value) {
                        ktype = _getType(mixed_value[key]);
                        if (ktype == "function") { 
                            continue; 
                        }
                        
                        okey = (key.match(/^[0-9]+$/) ? parseInt(key, 10) : key);
                        vals += serialize(okey) +
                                serialize(mixed_value[key]);
                        count++;
                    }
                    val += ":" + count + ":{" + vals + "}";
                    break;
            }
            if (type != "object" && type != "array") {
              val += ";";
          }
            return val;
        }
        
SCRIPT;
        AddScript($script);
    }
    
}  // -------------- END CLASS --------------