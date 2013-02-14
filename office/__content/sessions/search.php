<?php
$script_location = "/office/AJAX/sessions/search";
$WH_ID = 666;
$dialog_id = 0;


$OBJ    = new Sessions_Search();
$OBJ->wh_id = $WH_ID;

//$OBJ->CreateSessionsForInstructors('2010-11-08');

$img_no = $OBJ->img_no;
$img_yes = $OBJ->img_yes;



if ($AJAX) {
    $OBJ->AjaxHandle();
    //$OBJ->ProcessAjax();
} else {
    //$OBJ->ListTable();
    
    #$wh_id = Get('wh_id');
    #$date = Get('date');
    $OBJ->GetCourses();
    $OBJ->FormatCourses();
    $OBJ->CreateSchedule();
    $OBJ->AddStyle();
    
    $schedule = $OBJ->OutputSchedule(true);
    $instructors = $OBJ->GetInstructors(true);
    
    echo "
    <div style='width:950px;'>
        
        <div style='float:left;width:200px; border:0px solid red;'>
        
            <div class='sec_header'>STEP 1. - PICK DATE</div>
            <div class='sec_wrapper'>
                <div id='cal_date'></div>
            </div><br /><br />
            
            <div class='sec_header'>STEP 2. - CRITERIA</div>
            <div class='sec_wrapper'>
                <div id='elements'>
                    [] checkbox 1<br />
                    [] checkbox 2<br />
                    [] checkbox 3
                </div>
            </div><br /><br />
            
            <div class='sec_header'>STEP 3. - SEARCH</div>
            <div class='sec_wrapper'>
                <input type='button' class='btn_submit' name='btn_submit' value='SEARCH' /><br />
                <input type='button' class='btn_submit' name='btn_reset' value='RESET FORM' />
            </div>
        
        </div>
        
        <div style='float:left; width:300px; border:0px solid red;'>
            <div style='font-size:14px; color:#990000; font-weight:bold;'>SESSIONS ON THIS DAY</div>
            <div id='schedule_holder' style='height:400px; overflow-y:scroll; overflow-x:hidden; border:1px solid #990000;'>{$schedule}</div>
        </div>
        
        <div style='float:left; width:440px; border:0px solid red;'>
            <div style='font-size:14px; color:#990000; font-weight:bold;'>INSTRUCTORS</div>
            <div style='border:1px solid #990000;' class='picture_active'>
                
                <div id='instructors_active_selected_holder'; style='float:left;'>
                    <ul id='instructors_active_selected_target' class='image-grid'></ul>
                    <div class='backLink'></div>
                </div>
                
                <div style='float:left; wid__th:300px;'>
                    <ul id='instructors_active_target' class='image-grid'></ul>
                </div>
                
                <div style='clear:both;'></div>
                
            </div>
            <div style='border:0px solid #990000;' class='picture_inactive'>            
                <ul id='instructors_inactive_target' class='image-grid'>{$instructors}</ul>
            </div>
        </div>
        
        <div style='clear:both;'></div>
        
    </div>
    ";




# SCRIPT
# ======================================================================
$SCRIPT = <<<SCRIPT




$(document).ready(function() {

    // SUBMIT BUTTON FOR SAVING RECORDS HAS BEEN CLICKED
    // ===========================================================================
    $('.btn_submit').livequery('click', function(event) {
        //alert('save has been clicked');
        
        
        // SEND AN ARRAY OF ALL SELECTED OPTIONS
        var sendArray = new Array();
        
        tempArray['ci_id']                  = $(this).attr("recordID");
        tempArray['ci_status']              = $(this).attr("status");
        tempArray['ci_time_start']          = $(this).children('.course_info').children('.ci_time_start').html();
        tempArray['ci_time_end']            = $(this).children('.course_info').children('.ci_time_end').html();
        tempArray['ci_cancel_before_time']  = $(this).children('.course_info').children('.ci_cancel_before_time').val();
        
        sendArray[thisID] = tempArray;
        
        var date                = $('#cal_date').val();
        var serialize_array     = serialize(sendArray);
        serialize_array         = 'contentArray=' + serialize_array;
        var url                 = "{$script_location};action=SearchRecords;wh_id={$WH_ID};date=" + date;
    	
        
        $('#result_send').html(serialize_array);
        
        $('body').css('cursor','wait');
        $.ajax({
			type: "POST",
			url: url,
            data: serialize_array,
			dataType: "html",
			success: function(data) {
                $('#result_receive').html(data);
                $('body').css('cursor','auto');
                GetReservationsAjax();
			}
		});
        return false;
        $('body').css('cursor','auto');
    
    });
    
    
    // RESET BUTTON FOR SAVING RECORDS HAS BEEN CLICKED
    // ===========================================================================
    $('.btn_reset').livequery('click', function(event) {
        GetReservationsAjax();
    });
    
    
    
	$.ajaxSetup ({
		cache: false
	});
    
    $("#cal_date").change(function(){	
        GetReservationsAjax();
	});
    
    $("#cal_date").datepicker({
        dateFormat: 'yy-mm-dd',
        altField: '#display_date',
        altFormat: 'DD, MM d, yy',
        changeMonth: true,
        changeYear: true
    });
    
    
}); //END ON READY PORTION


function GetReservationsAjax() {
    // ==============================================================================
    // FUNCTION :: Called to get all sessions offered on a particular day
    // ==============================================================================
    
    var date            = $('#cal_date').val();
    //var loadUrl         = "{$script_location}.php?action=LoadExistingRecords&date=" + date + "&extraScript=" + extraScript;
    var loadUrl         = "{$script_location}.php?action=LoadExistingRecords&date=" + date;
    var ajax_load       = "<img src='/office/images/upload.gif' alt='loading...' />";
    $("#schedule_holder").html(ajax_load).load(loadUrl);
    $("#class_info").empty();
    
    //$('#instructors_active_selected_holder').css({width: '50px'});
}

function ShowCourseListing(divID, instructors_list) {
    // ==============================================================================
    // FUNCTION :: Called when clicking on a group of sessions - showing in calendar
    // ==============================================================================
    
    // 1. CLEAR ALL THE SPECIAL DIV CLASSES - DO AS A LOOP
    $('.zone_selected').each(function(index) {
        $(this).removeClass('zone_selected').addClass('zone_existing_data');
    });
    
    
    // 2. MODIFY THE CLASS OF THE SELECTED DIV
    $('#' + divID).addClass('zone_selected').removeClass('zone_existing_data');
    //$('#time_0000').addClass('zone_selected');
    
    
    // 3. CALL THE INSTRUCTOR LIST
    SetInstructorPictures(instructors_list, divID);
    
    
    //var courses         = $("#courses_"+divID).html();
    //var ajax_load       = "<img src='/office/images/upload.gif' alt='loading...' />";
    //$("#class_info").html(ajax_load).html(courses);
}

function GetCoursesForInstructor(instructor_id) {
    // ==============================================================================
    // FUNCTION :: Called when a single instructor's profile has been clicked
    // ==============================================================================

    var date            = $('#cal_date').val();
    var loadUrl         = "{$script_location}.php?action=LoadExistingRecordsForInstructor&instructor_id=" + instructor_id + "&date=" + date;
    var ajax_load       = "<img src='/office/images/upload.gif' alt='loading...' />";
    $("#schedule_holder").html(ajax_load).load(loadUrl, function() {
        SetInstructorActivePicture(instructor_id);
    });
    $("#class_info").empty();
}

function HandleClickingInstructorShowAll(divID, instructors_list) {
    // ==============================================================================
    // FUNCTION :: Called when looking at a particular instructor's sessions 
    //             and now wanting to go back and view all sessions on that time.
    // ==============================================================================
    
    var date            = $('#cal_date').val();
    var loadUrl         = "{$script_location}.php?action=LoadExistingRecords&date=" + date;
    var ajax_load       = "<img src='/office/images/upload.gif' alt='loading...' />";
    $("#schedule_holder").html(ajax_load).load(loadUrl, function() {
        ShowCourseListing(divID, instructors_list);
    });
    $("#class_info").empty();
    
    //$('#instructors_active_selected_holder').css({width: '50px'});
}

function SetInstructorPictures(instructors_list, divID) {
    // ==============================================================================
    // FUNCTION :: Create list of active instructors for the given time period
    // ==============================================================================
    
    var instructors = instructors_list.split("|");
    
    
    // 1. MOVE ALL IMAGES BACK TO THE INACTIVE LIST
    // =============================================================================
    $('#instructors_active_target li').each(function(index) {
        $(this).appendTo('#instructors_inactive_target');
    });
    
    $('#instructors_active_selected_target li').each(function(index) {
        $(this).appendTo('#instructors_inactive_target');
    });
    
    $('#instructors_active_target').html('');
    $('#instructors_active_selected_target').html('');
    
    
    // 2. MOVE ACTIVE IMAGES UP TO ACTIVE LIST
    // =============================================================================
    for (i=0; i<instructors.length; i++) {
        $("#picture_" + instructors[i] + "_li").appendTo('#instructors_active_target').show("slide", {direction: "up"}, "1000");
    }
    
    
    // 3. SETUP THE ACTIVE INSTRUCTOR BOX
    // =============================================================================
    var newData = "<a href='#' onclick=\"HandleClickingInstructorShowAll('"+divID+"', '"+instructors_list+"');\">VIEW ALL</a>";
    $('#instructors_active_selected_holder > .backLink').html('').append(newData);
    $('#instructors_active_selected_holder').hide();
    
}

function SetInstructorActivePicture(instructor_id) {
    // ==============================================================================
    // FUNCTION :: Show the currently selected instructor
    // ==============================================================================
    
    // MOVE PREVIOUSLY SELECTED INSTRUCTOR OUT OF LIST
    // =============================================================================
    $('#instructors_active_selected_target li').each(function(index) {
        $(this).appendTo('#instructors_active_target'); //.removeClass('picture_selected')
    });
    $('#instructors_active_selected_target').html('');
    
    
    $("#picture_" + instructor_id + "_li").appendTo('#instructors_active_selected_target').show("slide", {direction: "up"}, "1000");
    //$('#picture_'+instructor_id).addClass('picture_selected');
    
    
    $('#instructors_active_selected_holder').show(); //css({width: '100px'});
    
    
//    $('#instructors_active_target li .picture_holder').each(function(index) {
//        $(this).removeClass('picture_selected');
//    });
}

SCRIPT;
AddScript($SCRIPT);





# RESIZE THE CURRENT FRAME TO FIT CONTENTS
# ================================================
$script = <<<SCRIPT
    var dialogNumber = '';
    if (window.frameElement) {
        if (window.frameElement.id.substring(0, 13) == 'appformIframe') {
            dialogNumber = window.frameElement.id.replace('appformIframe', '');
        }
    }
    ResizeIframe();
SCRIPT;
AddScript($script);

}