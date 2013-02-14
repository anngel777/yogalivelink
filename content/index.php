<?php
/* ====================================================================================
        Created: January 1, 2011
     Created By: Richard Witherspoon
   Last Updated: June 19, 2012
Last Updated By: Richard Witherspoon

       Filename: index
    Description: Show the website homepage
    
    2012-07-19 -> Converted content for new look/feel
==================================================================================== */


$_GET['special'] = true;

/*
# ----- sliders NOT showing -----
<img class="image" src="/images/slider/slider_man.jpg" alt="" border="0"  />
<img class="image" src="/images/slider/slider_african_woman.jpg" alt="" border="0"  />
*/

$tagline = (Get('tagline')) ? Get('tagline') : "Yoga Therapy & Private Yoga";
$subtag = (Get('subtag')) ? Get('subtag') : "Build strength and vitality with<br /><b>live</b>, <b>private</b>, <b>one on one</b> yoga";
?>

<div>

    <table width="100%" border="0">
        <tr>
            <td rowspan="2">
                
                <div id="slideshow_wrapper" style="border:0px; ">
                <div id="box">
                    <!-- START of slider -->
                    <div class="slideshow" style="">
                        <img class="image" src="/images/slider/slider_student_teacher.jpg" alt="" border="0" />
                        <img class="image" src="/images/slider/slider_young_woman.jpg" alt="" border="0" />
                        <img class="image" src="/images/slider/slider_woman_baby.jpg" alt="" border="0" />
                        <img class="image" src="/images/slider/slider_old_woman.jpg" alt="" border="0" />
                    </div> 
                    <!-- END of slider -->
                </div>
                </div>
                
            </td>
            <td valign="top" align="left">
                
                <div style="padding-left:12px;">
                    <br /><br />
                    <div class="index_header" style="font-size:36px;"><?php echo $tagline; ?></div>
                    <div class="index_header_sub" style="font-size:18px; color:#494949;"><?php echo $subtag; ?></div>
                    <br /><br /><br /><br />
                    <center>
                    <div style="padding-bottom:10px;"><div class="buttonImg"><a href="/signup"><img src="/images/buttons/btn_get_started_off.png"></a></div></div>
                    <a class="link_arrow" style="font-size:12px;" href="/how_yll_works" >Online Yoga - how it works</a>
                    
                    
                    <div style="display:none; padding-bottom:10px;"><div class="buttonImg"><a href="/how_yll_works"><img src="/images/buttons/btn_how_works_off.png"></a></div></div>
                    
                    
                    <div style="display:none; padding-bottom:10px;"><a href="/how_yll_works" class="index_button_simple">How it Works</a></div>
                    <div style="display:none; padding-bottom:10px;"><a href="/pricing" class="index_button_simple">Pricing</a></div>
                    <div style="display:none; padding-bottom:0px;"><a href="/signup" class="index_button_simple_red">Get Started!</a></div>
                    </center>
                </div>  
                
            </td>
        </tr>
    </table>

    
    
    <a id="logo_overlay_trigger" href="#logo_overlay"></a>
    <div style="display:none;"><div id="logo_overlay">
        <center>
        <img alt="YogaLiveLink.com" src="/images/yoga_animated_logo.gif" border="0" />
        </center>
    </div></div>

</div>



<br /><br />


@@PAGE_CONTENT@@



<?php


$instructor_img_width   = 50; //120
$instructor_div_width   = $instructor_img_width + 20;
$yoga_img_width         = 50; //290
$instructor_font_size   = 11;

$content_bio_1 = <<<content_bio_1

    <div class="lower_header">Featured Yoga Instructor</div>
    <div class="lower_content">
        <div>
            <br />
            <div style="float:left; width:{$instructor_div_width}px;"><img src="http://www.yogalivelink.com/office/images/instructors/thumbnail_1305523918.jpg" width="{$instructor_img_width}" alt="Zamanta" border="0" /></div>
            <div style="float:left; width:75%; font-size:{$instructor_font_size}px;">
            
                <b style="font-size:20px;">Zamanta Archibold</b>
                <br /><br />
                <b>BACKGROUND</b><br />
                Zamanta Archibold is an internationally respected yoga instructor, owner of ZaYoga Retreats, and is a Black Belt in Karate.
                <br /><br />
                <b>EXPERIENCE</b><br />
                Licensed Yoga Alliance E-YT (Experienced Yoga Teacher)<br />
                Black Belt in Karate<br />
                Yoga4Vets Teacher<br />
                Connected Warriors Teacher<br />
                Hear Me Now Teacher
                <br /><br />
                <b>SPECIALTIES</b><br />
                Zamanta's creation "Personalize Your Yoga" is a fusion yoga that draws wisdom from Vinyasa, Ashtanga, and martial arts. Focused on the individual's needs and goals, it is flowing, energetic, and light-hearted.
                
            
            </div> 
            <div class="clear"></div>
        </div>
    </div>

content_bio_1;


$content_bio_2 = <<<content_bio_2

    <div class="lower_header">Featured Yoga Therapy Instructor</div>
    <div class="lower_content">
        <div>
            <br />
            <div style="float:left; width:{$instructor_div_width}px;"><img src="http://www.yogalivelink.com/office/images/instructors/thumbnail_1305143953.jpg" width="{$instructor_img_width}" alt="Zamanta" border="0" /></div>
            <div style="float:left; width:75%; font-size:{$instructor_font_size}px;">
            
                <b style="font-size:20px;">EB Ferdig</b>
                <br /><br />
                <b>BACKGROUND</b><br />
                EB Ferdig is a yoga teacher, Certified Yoga Therapist and Integrated Movement Therapy (IMT) Practitioner. She is President of the Northwest Yoga Therapy Collaborative, and has worked with people from around the world for more than ten years
                <br /><br />
                <b>EXPERIENCE</b><br />
                President of the Northwest Yoga Therapy Collaborative (NWYTC)<br />
                Yoga Alliance RYT-500 certified yoga teacher<br />
                Certified Yoga Therapist<br />
                Certified Integrated Movement Therapy (IMT) practitioner
                <br /><br />
                <b>SPECIALTIES</b><br />
                Using Integrated Movement Therapy methods in her Yoga Therapy practice, EB specializes in relieving anxiety, depression, chronic pain, post-partum challenges, ADD/ADHD, and eating disorders. Her focus helps people increase their happiness and ease in life and discover opportunities for healing.

            
            </div> 
            <div class="clear"></div>
        </div>
    </div>

content_bio_2;



$what_is_private_yoga = <<<what_is_private_yoga
    <div class="lower_header">What Is Private Yoga?</div>
    <div class="lower_content">
    Private Yoga Sessions -- One on one yoga instruction designed just for you.<br />
    Private yoga sessions are the best way to create and develop a strong yoga workout, and the fastest way to advance in your yoga fitness goals. With YogaLiveLink you can now practice yoga one-on-one with a professional yoga instructor live through our innovative service at the time and place that works for your lifestyle. We link people like you with highly trained yoga instructors for private yoga sessions delivered via webcam.
     <br /><br />
    You receive professional, focused instruction in the comfort of your own home, office, or at a hotel room while you are traveling, without having to commute to a gym or studio. Plus, you select the time that works in your schedule.
    <br /><br />
    With YogaLiveLink you get a private yoga session at the time and place that work best for you.<br />
    Via webcam your instructor can see you and give you specific guidance, and you can see your instructor demonstrate yoga poses.
    </div>
what_is_private_yoga;


$what_is_yoga_therapy = <<<what_is_yoga_therapy
    <div class="lower_header">What Is Yoga Therapy?</div>
    <div class="lower_content">
    Yoga Therapy encompasses mental, emotional, spiritual and physical health. Its goal is to increase happiness, whereas other therapies, such as physical therapy, focus on strength and flexibility.
    <br /><br />
    The word 'yoga' means 'to connect.' All types of Yoga Therapy strive to help the practitioner connect with the inner self, with others, and with God or a higher power. Yoga Therapy professionals believe that through this deep connection the potential for healing is unlimited. 
    <br /><br />
    Yoga Therapy benefits people who have physical, mental, emotional, or spiritual challenges. Issues such as infertility, anxiety, depression, chronic pain, cancer, Parkinson's disease, stroke recovery, chronic pain, ADD/ADHD, sensory integration disorders, Autism Spectrum disorders, eating disorders, and even managing life after a major loss can all be addressed with Yoga Therapy. 
    <br /><br />
    Many people with significant challenges have trouble getting to doctor and other therapy appointments. Through YogaLiveLink you can now partake in Yoga Therapy from the comfort of your home at the time that works for you.
    <div style="display:none;">
        <br /><br />
        When you work with a Yoga Therapist online with YogaLiveLink, you work in partnership with your therapist with the powerful tools of yoga, such as focused breathing, philosophy, visualization, movement and relaxation practices, for your particular condition and symptoms. You and your Yoga Therapist work as a team to create a healing experience.
        <br /><br />
        Yoga Therapy has existed for centuries in India and it is gaining momentum in the U.S. as a recognized field, with scientific research documenting its benefits. The International Association of Yoga Therapists, a professional organization dedicated to supporting the field and providing a clearinghouse for research, is a great resource for more information about this amazing field.
    </div>
    </div>
what_is_yoga_therapy;



$yoga_for_you = <<<yoga_for_you

    <div class="lower_header">Yoga for You</div>
    <div class="lower_content">
        <i>When is the last time you fully relaxed, let go of your worries and were fully present?</i>
        YogaLiveLink is a haven in the world. It is a place to connect one-on-one with a professional yoga instructor for an empowering guided yoga experience that is made just for <b>you</b>.
        <br /><br />
        With yoga you can regenerate your body and reclaim your center. Healing, empowering, transformational, and fun a one-hour online session of private yoga or Yoga Therapy is like taking a whole day off.
        <br /><br />
        Through YogaLiveLink you can build strength and vitality in the midst of the whirling, hectic pace of daily life.
        <br /><br />
        Our sessions take place through live web-video chat. Private Yoga and Yoga Therapy sessions are available throughout the day so you can build your body and bliss at the time and place that works in your lifestyle.
        <br /><br />
        Get to know our <a href="/instructors">Yoga Instructors and Yoga Therapists</a>. Start a new relationship with your body, mind, and spirit.
        <br /><br />
        <b>Features:</b>
        <ul style="padding-left:5px;">
        <li>No Contracts</li>
        <li>New client special * Just $45 to start</li>
        <li>Yoga sessions available throughout the day</li>
        <li>Live, web-video chat with yoga professionals</li>
        <li>Safe and secure</li>
        </ul>
    </div>

yoga_for_you;


$radio_interview = <<<radio_interview
    <center>
    <a href="http://getfitnow.cascadiaaudio.com/getfitnow209.mp3" target="_radioshow">
        <img class="image" src="/images/yoga---radio-show.jpg" alt="" border="0" width="200" /> 
        <div class="index_header" style="font-size:12px;">Radio interview with YogaLiveLink.com</div>
    </a>
    </center>
radio_interview;


$INDEX_LOWER_CONTENT = <<<INDEX_LOWER_CONTENT


    <table>
    <tr>
        <td valign="top">
            <div style="width: 600px;">
                
                {$yoga_for_you}
                <br /><br />
                <br /><br />
                {$what_is_private_yoga}
                <br /><br />
                <br /><br />
                {$what_is_yoga_therapy}
                <br /><br />
                <br /><br />
                
                
                

                
            </div>
        </td>
        
        <td>
            <div style="width:50px;">&nbsp;</div>
        </td>
    
        <td valign="top">
            <div style="width: 300px; background-color:#efefef; padding:10px;">
                {$content_bio_1}            
                <br /><br />
                <br /><br />
                {$content_bio_2}
                <br /><br />
                <br /><br />
            </div>
            <br /><br />
            <br /><br />
            <div style="background-color:#efefef; padding:10px;">
                {$radio_interview}
            </div>
        </td>
    </tr>
    </table>
    
INDEX_LOWER_CONTENT;




AddSwap('@@INDEX_LOWER_CONTENT@@', $INDEX_LOWER_CONTENT);


















/*
$OBJ = new Website_IndexBoxes();
$boxes = $OBJ->GetIndexBoxesAsTable();
addSwapCustom('@@BOXES@@',$boxes);
*/


AddStylesheet("/css/jquery.fancybox-1.3.4.css");
AddScriptInclude("/jslib/jquery.easing-1.3.pack.js");
AddScriptInclude("/jslib/jquery.fancybox-1.3.4.pack.js");


$script = <<<SCRIPT
    $(".fancyYouTube").fancybox({
        'autoScale'     : false,
        'title'         : '',
        'transitionIn'  : 'elastic',
        'transitionOut' : 'elastic',
        'width'         : 680,
        'height'        : 510,
        'type'          : 'iframe'
    });
        
    if (location.hash) {
        $(location.hash).click();
    }
SCRIPT;
AddScriptOnReady($script);






# SLIDESHOW FOR IMAGES
# ==================================================================
AddScriptInclude("/jslib/jquery.cycle.lite.min.js");

$script = <<<SCRIPT
    $('.slideshow').cycle({
        fx: 'fade'
    });
SCRIPT;
addScriptOnReady($script);

$style = <<<STYLE
    #slideshow_wrapper { padding:10px; border:1px solid #C0C455; width: 620px; }
    .slideshow { height: 290px; width: 620px; margin: auto }
    .slideshow .image { height: 290px; width: 620px; margin: auto }
    .slideshow img { padding: 0px; border: 1px solid #C0C455; background-color: #eee; }
STYLE;
AddStyle($style);



# COOKIE.js -> For Opening logo on first visit to website
# ==================================================================
if ($ENABLE_POPUP) {
AddScriptInclude("/jslib/jquery.cookie.js");
$script = <<<SCRIPT
    if(!$.cookie('visits')){
        $.cookie('visits',1, {expires: 30});
    }
    $.cookie('visits', (parseInt($.cookie('visits')) + 1), {expires: 1}); //will expire after 1 days
    var overlayContact = function(){
        if(parseInt($.cookie('visits')) < 3){
            $("#logo_overlay_trigger").trigger('click');
        }
    };
    setTimeout(overlayContact, 50); //run the script immediately
    
    $("#logo_overlay_trigger").fancybox({
        'autoScale'         : false,
        'autoDimensions'    : false,
        'centerOnScroll'    : true,
        'title'             : '',
        'width'             : 550,
        'height'            : 270
    });
SCRIPT;
addScriptOnReady($script);
}