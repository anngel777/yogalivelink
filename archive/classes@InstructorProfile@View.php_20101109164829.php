<?php
class InstructorProfile_View extends BaseClass
{
	# MOVE TO A SETTINGS FILE - THESE ARE ALL CSS
	#########################
    private $picture_width             	= 100;
	private $picture_height             = 120;
    private $picture_border             = 1; //border around picture
    
    public $window_width               = 400;
    private $window_padding             = 10;
    
    private $debug_border               = 0; //turn this on to show columns    
    
    private $col_left_width             = 0; //calculated later
    private $col_right_width            = 0; //calculated later
    private $col_gap_width              = 10; //CSS gap between columns
    
    
    private $img_no                     = '/office/images/buttons/cancel.png';
    private $img_yes                    = '/office/images/buttons/save.png';
    private $picture_dir                = '/office/';
    
    private $testing_wh_id              = 111; // if this WH_ID is sent in - it will generate data for a fake instructor
	private $profile 					= array();
	
	public $check_pending_status		= false; // if set to TRUE - will look to see if there is a pending or rejected version of this profile.
	public $status_pending				= false;
	public $status_rejected 			= false;
    
    public $ShowClassVersion            = true;
    
    public $SQL                     = '';
    
    public $reset_settings          = false;
    private $settings               = array();
    
    public function  __construct()
    {
        $this->SQL = Lib_Singleton::GetInstance('Lib_Pdo');
        
        $this->ClassInfo = array(
            'Created By'    => 'Richard WItherspoon',
            'Description'   => 'Instructor profile window',
            'Created'       => '2010-06-17',
            'Updated'       => '2010-06-17',
            'Revision'          => '1.00.00',
            'Revision Title'    => 'ALPHA'
        );
        
    } // -------------- END __construct --------------


    public function ProcessAjax()
    {
        $action = Get('action');
        switch ($action) {
            case 'get_profile':
                $this->InitializeProfileWindowFromAjaxProfile();
            break;
        }
    }
            
            
            
    public function InitializeProfileWindowFromAjaxProfile()
    {
        global $FormPrefix;
        
        $first_name = Post($FormPrefix.'first_name');
        $last_name  = Post($FormPrefix.'last_name');
        $picture    = Post($FormPrefix.'primary_pictures_id');
        $city       = Post($FormPrefix.'location_city');
        $state      = Post($FormPrefix.'location_state');
        $experience = Post($FormPrefix.'experience_years');
        $profile    = Post($FormPrefix.'profile');
        $type       = Post($FormPrefix.'yoga_types');
        
        $this->profile = array(
			'name'          => "{$first_name} {$last_name}",
            'picture'       => $picture,
            'location'      => "{$city}, {$state}",
            'experience'    => $experience,
            'description'   => $profile,
            'types'         => $type,
		);
        
        # CALCULATE STYLESHEET
        #$this->CalculateStyle();
        
        # OUTPUT THE STYLESHEET
        #$this->AddStyle(true);
        
        # OUTPUT ANY SCRIPTS
        #$this->AddScript();
        
        # CREATE THE PROFILE WINDOW
        $this->CreateProfileWindow();
    }
    
	
	
    public function InitializeProfileWindow($WH_ID='')
    {
        if ($WH_ID) {
		
			# LOAD THE INSTRUCTOR RECORD FROM DATABASE
			
            if ($WH_ID == $this->testing_wh_id) {
                $this->GetFakeInstructorProfile();
            } else {
                $this->GetInstructorProfile($WH_ID);
            }
			
			# FIND OUT IF THIS PROFILE IS PENDING APPROVAL
			if ($this->check_pending_status) {
				$this->GetPendingStatus();
			}
			
            # CALCULATE STYLESHEET
            $this->CalculateStyle();
            
			# OUTPUT THE STYLESHEET
			$this->AddStyle();
			
			# OUTPUT ANY SCRIPTS
			$this->AddScript();
			
			# CREATE THE PROFILE WINDOW
			$this->CreateProfileWindow();
            
        } else {
            echo "<br />[ERROR] :: CLASS -> InstructorProfile_View :: FUNCTION -> InitializeProfileWindow() :: ERROR -> No WH_ID sent into function";
        }
    }
    
    
    public function CalculateStyle()
    {
        $this->col_left_width       = $this->picture_width + (2 * $this->picture_border);
        $this->col_right_width      = $this->window_width - $this->col_left_width - $this->col_gap_width - (2 * $this->window_padding);
    }
    
    
	private function GetFakeInstructorProfile()
	{
		# FUNCTION :: USED FOR TESTING ONLY
		# Creates a fake instructor profile since database does not 
		# currently have instructor information in it.
		
		$record = array(
			'name'          => 'Selma Louise',
            'picture'       => 'instructor_53456.gif',
            'location'      => 'Boston, MA',
            'experience'    => '5',
            'description'   => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque bibendum metus vitae urna interdum faucibus. Proin euismod faucibus purus, dapibus luctus mi tincidunt sit amet. Mauris consectetur tincidunt molestie. Aliquam erat volutpat. Quisque nulla libero, tincidunt sed convallis at, tincidunt vitae diam. Nulla nibh arcu, viverra a consectetur sed, facilisis aliquam orci. Ut ullamcorper lectus eget odio ullamcorper mattis. Phasellus ac turpis leo, vel porttitor ipsum. Aliquam facilisis est vel tellus vulputate id faucibus risus gravida. Sed ut nisl id ante hendrerit aliquam.',
            'types'         => '',
		);
		
		$this->profile = $record;
	}
    
	private function GetInstructorProfile($WH_ID=0)
	{
		# FUNCTION :: LOADS INSTRUCTOR PROFILE FROM DATABASE
        $record = $this->SQL->GetRecord(array(
            'table' => 'instructor_profile',
            'keys'  => '*',
            'where' => "wh_id='$WH_ID' AND active=1",
        ));
        
        $this->profile = array(
			'name'          => "{$record['first_name']} {$record['last_name']}",
            'picture'       => $record['primary_pictures_id'], //'instructor_53456.gif',
            'location'      => "{$record['location_city']}, {$record['location_state']}",
            'experience'    => $record['experience_years'],
            'description'   => $record['profile'],
            'types'         => $record['yoga_types'],
		);
	}
	
	private function GetPendingStatus()
	{
		# FUNCTION :: CHECK TO SEE IF PROFILE IS PENDING APPROVAL
		
		$this->status_pending	= true;
		$this->status_rejected 	= false;

	}
	
    private function CreateProfileWindow()
    {
        # DEFAULT VALUE SETTING
        $class_version  = '';
        $status         = '';
        $types          = '';
        $picture        = '';
        
        
        # GET CLASS VERSION
        if ($this->ShowClassVersion) {
            $class_version = "<div class='col' style='float:right; color:#fff; font-size:10px; font-weight:normal; font-style:italic;'>{$this->ClassInfo['Revision Title']} {$this->ClassInfo['Revision']}&nbsp;</div>";
        }
        
        
        # GET PROFILE STATUS
		if ($this->check_pending_status) {
            if ($this->status_pending) {
                $status = "
                <div id='status_pending'>
                    <div class='col_l'>PENDING APPROVAL</div>
                    <div class='col_r'>[?] {$class_version}</div>
                    <div class='clear'></div>
                </div>";
            }
            
            if ($this->status_rejected) {
                $status = "
                <div id='status_pending'>
                    <div class='col_l'>REJECTED BY ADMINISTRATOR</div>
                    <div class='col_r'>[?] {$class_version}</div>
                    <div class='clear'></div>
                </div>";
            }
		}
		
        
        # GET YOGA TYPES
        //"checkboxlistset|Yoga Types|yoga_types|N||{$this->yoga_type_list}",
        //$types_list         = $this->yoga_type_list;
        //$instructor_types   = 'Bikram|Hatha|Vinyasa';
        
        $full_list          = explode ('|', $this->yoga_type_list);
        $instructor_list    = explode (',', $this->profile['types']);
        
        
        
        $types = '';
        foreach ($full_list AS $flp) {
        
            $img    = (in_array($flp, $instructor_list)) ? "<img src='{$this->img_yes}' alt='yes' border='0' />" : "<img src='{$this->img_no}' alt='yes' border='0' />";
            $types .= "{$img} {$flp}<br />";
        }
        
        
        
        
        //{$this->picture_dir}
        
        $content = <<<CONTENT
		<div id='i_profile_wrapper'>
            {$status}
            <div class='col_l' id='i_profile_col_left'>
                <div id='i_profile_picture'><img src='{$this->picture_dir}{$this->profile['picture']}' width='{$this->picture_width}' height='{$this->picture_height}' alt='Profile Picture' border='0' /></div>
                <div style='display:none;' id='i_profile_location'>{$this->profile['location']}</div>
                <div style='display:none;' id='i_profile_experience'>{$this->profile['experience']} yrs</div>
                <br /><br />
                <div id='i_profile_buttons'>[VIEW SCHEDULE]</div>
            </div>
            <div class='col_l' id='i_profile_col_gap'></div>
            <div class='col_r' id='i_profile_col_right'>
                <div id='i_profile_name'>{$this->profile['name']}</div>
                <br />
                <div id='i_profile_description'>{$this->profile['description']}</div>
                <br />
                <div id='i_profile_types'><b>TYPES OF YOGA</b><br />{$types}</div>
            </div>
        </div>
        <br /><br />
CONTENT;
            echo $content;
    }
  
    
   
    
    public function AddStyle($wrap=false)
    {
        $style = "
        .col {
            float:left;
            border:{$this->debug_border}px solid red;
        }
        .col_l {
			float:left;
            border:{$this->debug_border}px solid red;
		}
		.col_r {
			float:right;
            border:{$this->debug_border}px solid red;
		}
		.clear {
			clear:both;
		}
		
		#status_pending {
			color:#fff;
			background-color:orange;
			font-size: 13px;
			font-weight:bold;
			border:1px solid #000;
			padding:3px;
		}
		#status_rejected {
			color:#fff;
			background-color:#990000;
			font-size: 13px;
			font-weight:bold;
			border:1px solid #000;
			padding:3px;
		}
        
        #i_profile_wrapper {
            width:{$this->window_width}px;
            padding:{$this->window_padding}px;
            border:{$this->debug_border}px solid green;
        }        
        #i_profile_col_left {
            width:{$this->col_left_width}px;
        }
        #i_profile_col_right {
            width:{$this->col_right_width}px;
        }
        #i_profile_col_gap {
            width:{$this->col_gap_width}px;
        }
        
        #i_profile_picture {
            border:{$this->picture_border}px solid #000;
            background-color:#ccc;
            width:{$this->picture_width}px;
            height:{$this->picture_height}px;
        }
        #i_profile_location {}
		#i_profile_experience {}
        #i_profile_buttons {}
        
        #i_profile_name {
            font-size:14px;
            font-weight:bold;
            border-bottom:1px solid #ddd;
        }
        #i_profile_description {
            font-size:12px;
            font-weight:normal;
        }
        #i_profile_types {
            font-size:13px;
        }
        
        
        
        
        .tooltip {
            background-color:#000;
            border:1px solid #fff;
            padding:10px 15px;
            width:200px;
            display:none;
            color:#fff;
            text-align:left;
            font-size:12px;
            z-index:1000;

            /* outline radius for mozilla/firefox only */
            -moz-box-shadow:0 0 10px #000;
            -webkit-box-shadow:0 0 10px #000;
        }
        #tooltip {
            position:absolute;
            text-align:left;
            border:1px solid #333;
            background:#f7f5d1;
            padding:2px 5px;
            color:#333;
            display:none;
            z-index:100;
        }
        
        
        
        
        
        
        
        
        
        .buttons a, .buttons button{
            display:block;
            float:left;
            margin:0 7px 0 0;
            background-color:#f5f5f5;
            border:1px solid #dedede;
            border-top:1px solid #eee;
            border-left:1px solid #eee;

            font-family:'Lucida Grande', Tahoma, Arial, Verdana, sans-serif;
            font-size:12px;
            line-height:130%;
            text-decoration:none;
            font-weight:bold;
            color:#565656;
            cursor:pointer;
            padding:5px 10px 6px 7px; /* Links */
        }
        .buttons button{
            width:auto;
            overflow:visible;
            padding:4px 10px 3px 7px; /* IE6 */
        }
        .buttons button[type]{
            padding:5px 10px 5px 7px; /* Firefox */
            line-height:17px; /* Safari */
        }
        *:first-child+html button[type]{
            padding:4px 10px 3px 7px; /* IE7 */
        }
        .buttons button img, .buttons a img{
            margin:0 3px -3px 0 !important;
            padding:0;
            border:none;
            width:16px;
            height:16px;
        }

        /* STANDARD */

        button:hover, .buttons a:hover{
            background-color:#dff4ff;
            border:1px solid #c2e1ef;
            color:#336699;
        }
        .buttons a:active{
            background-color:#6299c5;
            border:1px solid #6299c5;
            color:#fff;
        }

        /* POSITIVE */

        button.positive, .buttons a.positive{
            background-color:#E6EFC2;
            border:1px solid #C6D880;
            color:#529214;
        }
        .buttons a.positive:hover, button.positive:hover{
            background-color:#fff;
            border:1px solid #C6D880;
            color:#529214;
        }
        .buttons a.positive:active{
            background-color:#529214;
            border:1px solid #529214;
            color:#fff;
        }

        /* NEGATIVE */

        .buttons a.negative, button.negative{
            background:#fbe3e4;
            border:1px solid #fbc2c4;
            color:#d12f19;
        }
        .buttons a.negative:hover, button.negative:hover{
            background:#fbe3e4;
            border:1px solid #fbc2c4;
            color:#d12f19;
        }
        .buttons a.negative:active{
            background-color:#d12f19;
            border:1px solid #d12f19;
            color:#fff;
        }

        /* REGULAR */

        button.regular, .buttons a.regular{
            color:#336699;
        }
        .buttons a.regular:hover, button.regular:hover{
            background-color:#dff4ff;
            border:1px solid #c2e1ef;
            color:#336699;
        }
        .buttons a.regular:active{
            background-color:#6299c5;
            border:1px solid #6299c5;
            color:#fff;
        }
        ";
        
        if ($wrap) {
            echo "<script type='text/css' media='screen'>$style</script>";
        } else {
            AddStyle($style);
        }
    }

    private function AddScript()
    {
        # SCRIPT
        # ======================================================================
        $script = <<<SCRIPT
        
            function AjaxCall(page, action, extra_vars) {
                var loadUrl         = "/office/AJAX/chat/" + page + "?action=" + action + "&" + extra_vars;
                var ajax_load       = "<img src='/images/loading.gif' alt='loading...' />";
                
                //alert(loadUrl);
                $("#ajax_status").html(ajax_load).load(loadUrl);
            }
            
SCRIPT;
        AddScript($script);


        $script = <<<SCRIPT
            $.ajaxSetup ({
                cache: false
            });
SCRIPT;
        addScriptOnReady($script);
        
    }
    
    private function echoScript($script)
    {
        if ($script) {
            echo "<script language='text/javascript'>$script</script>";
        }
    }
    
}  // -------------- END CLASS --------------