<?php
class InstructorProfile_View extends BaseClass
{
    public $Show_Query                  = false;    // TRUE = output the database queries ocurring on this page
    public $check_pending_status		= false;    // TRUE = will look to see if there is a pending or rejected version of this profile.
	public $Show_Profile_Status         = false;    // TRUE = Will show the pending status within the profile window itself - used for instructors
    public $ShowClassVersion            = false;    // TRUE = show vlass verion in ourput display - DEV ONLY
    
	// ---------- CSS SETTINGS ----------
	public $window_width                = 400;
    private $window_padding             = 10;
    private $debug_border               = 0;        // turn this on to show columns    
    private $col_gap_width              = 10;       // CSS gap between columns
    
    // ---------- NON-SETTABLE VARIABLES ----------
    public $SQL                         = '';
    public $WH_ID                       = 0;
    private $testing_wh_id              = 111;      // if this WH_ID is sent in - it will generate data for a fake instructor
	private $profile 					= array();
    public $yoga_type_list              = null;
    public $reset_settings              = false;
    private $settings                   = array();
    public $status_pending				= false;    // TRUE = set when in pending status
	public $status_rejected 			= false;    // TRUE = set when in rejected status
    private $img_no                     = null;
    private $img_yes                    = null;
    private $picture_dir                = null;
    private $picture_width             	= null;
	private $picture_height             = null;
    private $picture_border             = null;     // border around picture
    private $col_left_width             = 0;        // calculated later
    private $col_right_width            = 0;        // calculated later
    
    
    public function  __construct()
    {
        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Revision Title'    => 'ALPHA',
            'Description' => 'Load an instructors website profile - for display on website',
        );
        
        $this->SQL = Lib_Singleton::GetInstance('Lib_Pdo');
        
        $this->SetParameters(func_get_args());
        $this->WH_ID = $this->GetParameter(0);
        
        
        # INITIALIZE GLOBAL VARIABLES
        # ========================================================================
        $this->picture_width        = $GLOBALS['INSTRUCTOR_PICTURE_WIDTH_PROFILE'];
        $this->picture_height       = $GLOBALS['INSTRUCTOR_PICTURE_HEIGHT_PROFILE'];
        $this->picture_dir          = $GLOBALS['INSTRUCTOR_PICTURE_DIR'];
        $this->img_no               = $GLOBALS['ICO_NO'];
        $this->img_yes              = $GLOBALS['ICO_YES'];
        $this->yoga_type_list       = $GLOBALS['YOGA_TYPE_LIST'];
        
        
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
        
        # figure out the yoga types
        $yoga_type_parts    = explode('|', $GLOBALS['YOGA_TYPE_LIST']);
        $yoga_type_count    = count($yoga_type_parts);
        $type_list          = '';
        for ($i=0; $i<$yoga_type_count; $i++) {
            $value      = Post($FormPrefix.'yoga_types'.$i);
            $type_list .= "{$value},";
        }
        $type_list          = substr($type_list, 0, -1);
        $type               = $type_list;

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
        
        
        # OUTPUT ANY SCRIPTS
        #$this->AddScript();
        
        # CREATE THE PROFILE WINDOW
        $this->CreateProfileWindow();
    }
    

    public function Execute()
    {
        $this->InitializeProfileWindow($this->WH_ID);
    }
	
    public function InitializeProfileWindow($WH_ID='', $RETURN=false)
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
				$this->GetPendingStatus($WH_ID);
			}
			
            # CALCULATE STYLESHEET
            $this->CalculateStyle();
            
			
			
			# OUTPUT ANY SCRIPTS
			$this->AddScript();
			
			# CREATE THE PROFILE WINDOW
			if ($RETURN) {
                $output = $this->CreateProfileWindow(true);
                return $output;
            } else {
                $this->CreateProfileWindow();
            }
            
        } else {
            echo "<br />[ERROR] :: CLASS -> InstructorProfile_View :: FUNCTION -> InitializeProfileWindow() :: ERROR -> No WH_ID sent into function";
        }
    }
    
    
    public function CalculateStyle()
    {
        #$this->col_left_width       = $this->picture_width + (2 * $this->picture_border);
        #$this->col_right_width      = $this->window_width - $this->col_left_width - $this->col_gap_width - (2 * $this->window_padding);
    }
    
    
	private function GetFakeInstructorProfile()
	{
		# FUNCTION :: USED FOR TESTING ONLY
		# Creates a fake instructor profile since database does not 
		# currently have instructor information in it.
		
		$record = array(
			'name'          => 'Selma Louise',
            'picture'       => 'instructor_53456.gif',
            //'location'      => 'Boston, MA',
            //'experience'    => '5',
            'description'   => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque bibendum metus vitae urna interdum faucibus. Proin euismod faucibus purus, dapibus luctus mi tincidunt sit amet. Mauris consectetur tincidunt molestie. Aliquam erat volutpat. Quisque nulla libero, tincidunt sed convallis at, tincidunt vitae diam. Nulla nibh arcu, viverra a consectetur sed, facilisis aliquam orci. Ut ullamcorper lectus eget odio ullamcorper mattis. Phasellus ac turpis leo, vel porttitor ipsum. Aliquam facilisis est vel tellus vulputate id faucibus risus gravida. Sed ut nisl id ante hendrerit aliquam.',
            'types'         => '',
		);
		
		$this->profile = $record;
	}
    
	private function GetInstructorProfile($WH_ID=0)
	{
		# FUNCTION :: LOADS INSTRUCTOR PROFILE FROM DATABASE
        $record = $this->SQL->GetRecord(array(
            'table' => $GLOBALS['TABLE_instructor_profile'],
            'keys'  => '*',
            'where' => "wh_id='$WH_ID' AND active=1",
        ));
        if ($this->Show_Query) echo "<br /><br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        
        $this->profile = array(
			'name'          => "{$record['first_name']} {$record['last_name']}",
            'picture'       => $record['primary_pictures_id'], //'instructor_53456.gif',
            //'location'      => "{$record['location_city']}, {$record['location_state']}",
            //'experience'    => $record['experience_years'],
            'description'   => $record['profile'],
            'types'         => $record['yoga_types'],
		);
	}
	
	private function GetPendingStatus($WH_ID)
	{
		# FUNCTION :: CHECK TO SEE IF PROFILE IS PENDING APPROVAL
		
        $record = $this->SQL->GetRecord(array(
            'table' => $GLOBALS['TABLE_instructor_profile_pending'],
            'keys'  => '*',
            'where' => "wh_id='$WH_ID' AND active=1",
        ));
        if ($this->Show_Query) echo "<br /><br />LAST QUERY = " . $this->SQL->Db_Last_Query;
        
        if ($record) {
            $this->status_pending	= $record['status_pending'];
            $this->status_rejected 	= $record['status_rejected'];
        }
	}
	
    private function CreateProfileWindow($RETURN=false)
    {
        # DEFAULT VALUE SETTING
        $class_version  = '';
        $status         = '';
        $types          = '';
        $picture        = '';
        
        
        # GET CLASS VERSION
        if ($this->ShowClassVersion) {
            $class_version = "<div class='col' style='float:right; color:#fff; font-size:10px; font-weight:normal; font-style:italic;'>{$this->ClassInfo['Revision Title']} {$this->ClassInfo['Version']}&nbsp;</div>";
        }
        
        
        # GET PROFILE STATUS
		if ($this->check_pending_status) {
            if ($this->status_pending) {
                $status = "
                <div id='status_pending'>
                    <div class='col_l'>PENDING APPROVAL</div>
                    <div class='col_r'>{$class_version}</div>
                    <div class='clear'></div>
                    The profile shown below is what is currectly displayed on the website. If you have submitted different content it has not been approved or rejected by the adminsitrator yet. To see the content awaiting approval - click the 'edit my public profile' link.
                </div>";
            }
            
            if ($this->status_rejected) {
                $status = "
                <div id='status_pending'>
                    <div class='col_l'>REJECTED BY ADMINISTRATOR</div>
                    <div class='col_r'>{$class_version}</div>
                    <div class='clear'></div>
                    The profile shown below is what is currectly displayed on the website. If you have submitted different content - it has been rejected by the adminsitrator. To see the rejected content - click the 'edit my public profile' link.
                </div>";
            }
		}
		
        
        # ---------- GET YOGA TYPES ----------
        $full_list          = explode ('|', $this->yoga_type_list);
        $instructor_list    = explode (',', $this->profile['types']);
        
        $types = '';
        foreach ($full_list AS $flp) {
            if (in_array($flp, $instructor_list)) {
                $img    =  "<img src='{$this->img_yes}' alt='yes' border='0' />";
                $types .= "{$img} {$flp}<br />";
            }
        }
        
        
        $show_schedule_link = false;
        $schedule_link = ($show_schedule_link) ? "[VIEW SCHEDULE]" :'';
        $profile_status = ($this->Show_Profile_Status && $this->check_pending_status) ? $status : '';
        
        $content = <<<CONTENT
        
        <table id='i_profile_wrapper' cellpadding='0' cellspacing='10'>
        <tr>
            <td valign='top' colspan='3'>
                {$profile_status}
            </td>
        </tr>
        <tr>
            <td valign='top'>
                <div id='i_profile_pic_*****_ture'>
                <img src='{$this->picture_dir}{$this->profile['picture']}' width='{$this->picture_width}' height='{$this->picture_height}' alt='Profile Picture' border='1' />
                </div>
            </td>
            <td valign='top'>&nbsp;&nbsp;&nbsp;</td>
            <td valign='top'>
                <div id='i_profile_name'>{$this->profile['name']}</div>
                <br />
                <div id='i_profile_description'>{$this->profile['description']}</div>
                <br />
                <div id='i_profile_types'><b>TYPES OF YOGA</b><br />{$types}</div> 
            </td>
        </tr>
        </table>
        <br /><br />
CONTENT;
            if ($RETURN) {
                return $content;
            } else {
                echo $content;
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


        $script = "
            $.ajaxSetup ({
                cache: false
            });
        ";
        addScriptOnReady($script);
        
    }
    
    private function echoScript($script)
    {
        # FUNCTION :: Immediately dump a script to the page - used in AJAX Calls
        
        if ($script) {
            echo "<script language='text/javascript'>$script</script>";
        }
    }
    
}  // -------------- END CLASS --------------