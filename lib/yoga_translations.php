<?php
    $Translations = array(

        # CLASS :: General Form Functions
        # ========================================================================
        'TXT_REGISTRATION_0029' => "SELECT",
        'FRM_PROCESSING'        => "Processing...",
        'FRM_YEAR'              => 'Year',
        'FRM_MONTH'             => 'Month',
        'FRM_DAY'               => 'Day',
        
        # CLASS :: Sessions_RatingsInstructor
        # ========================================================================
        'RATING_INSTRUCTOR_001' => "Please help us improve our service by answering the following questions:",
        'RATING_INSTRUCTOR_002' => "Poor",
        'RATING_INSTRUCTOR_003' => "Excellent",
        'RATING_INSTRUCTOR_004' => "User Skill",
        'RATING_INSTRUCTOR_005' => "User Knowledge",
        'RATING_INSTRUCTOR_006' => "Technical Ease",
        'RATING_INSTRUCTOR_007' => "Technical Quality Video",
        'RATING_INSTRUCTOR_008' => "Technical Quality Audio",
        'RATING_INSTRUCTOR_009' => "Notes",
        
        # CLASS :: Sessions_RatingsUser
        # ========================================================================
        'RATING_USER_001' => "It has been a pleasure to provide services to you. Please help us improve our service by answering the following questions:",
        'RATING_USER_002' => "Poor",
        'RATING_USER_003' => "Excellent",
        'RATING_USER_004' => "Instructor Skill",
        'RATING_USER_005' => "Instructor Knowledge",
        'RATING_USER_006' => "Technical Ease",
        'RATING_USER_007' => "Technical Quality Video",
        'RATING_USER_008' => "Technical Quality Audio",
        'RATING_USER_009' => "Would you recommend our service to your friends?",
        'RATING_USER_010' => "Notes",
        
        # CLASS :: Profile_CreateAccountCustomerAdministrator
        # ========================================================================
        'EMAIL_NOT_UNIQUE'              => 'Your email address has already been used with YogaLiveLink.com. Each user must have a unique email address. Please use a different email address or contact us at support@yogalivelink.com.',
        'UNKNOWN_REGISTRATION_ERROR'    => 'UNKNOWN REGISTRATION ERROR. Please contact support@yogalivelink.com',
        
        # CLASS :: Website_SignupCustomer
        # ========================================================================
        //'USER_REGISTRATION_INSTRUCTIONS_START'  => 'Registration instructions for CUSTOMERS go here.',
        'USER_REGISTRATION_SUCCESS'             => 'Your registration has been completed successfully! You will receive an email with your login details at the email address you provided. If you have not received this email within 10 minutes - please contact us at support@yogalivelink.com.',
        'USER_REGISTRATION_INSTRUCTIONS'        => 'Your email will be your login username. Once you have finished filling in Your Details, we will send you a confirmation email with your temporary password to login to the system.',

        # CLASS :: Website_SignupInstructor
        # ========================================================================
        'INSTRUCTOR_REGISTRATION_INSTRUCTIONS_START'  => "Welcome! We are very grateful for your interest in working with us as an Instructor. Please fill out the information below to create your initial Instructor account where you'll be able to get more information about our application process and rigorous audition. You'll receive an email with a temporary password for your first login to the system. Once you've logged in the first time, you'll be able to create your own password in your Instructor Profile page.",
        'INSTRUCTOR_REGISTRATION_SUCCESS'             => 'Your registration has been completed successfully! You will receive an email with your login details at the email address you provided. If you have not received this email within 10 minutes - please contact us at support@yogalivelink.com.',
        'INSTRUCTOR_REGISTRATION_INSTRUCTIONS'        => 'Your email will be your login username. Once you have finished filling in Your Details, we will send you a confirmation email with your temporary password to login to the system.',
        
        # CLASS :: Profile_CustomerProfileOverview
        # ========================================================================
        'CPO_001' => 'Below are the email lists you are currently subscribed to.',
        'CPO_002' => 'Edit Profile',
        'CPO_003' => 'CUSTOMER PROFILE',
        'CPO_004' => 'Click here to begin searching',
        'CPO_005' => 'SEARCH FOR SESSIONS',
        'CPO_006' => 'Edit Information',
        'CPO_007' => 'LOGOUT',
        'CPO_008' => 'Click Here to Logout',
        'CPO_009' => 'Edit Email Subscriptions',
        'CPO_010' => 'Edit Subscriptions',
        'CPO_011' => 'EMAIL SUBSCRIPTIONS',
        'CPO_012' => 'View Sessions',
        'CPO_013' => 'Your Booked Sessions',
        'CPO_014' => 'CUSTOMER PROFILE',
        'CPO_015' => '',
    );
    
    
    
    global $TRANSLATION;
    $TRANSLATION->AddWordsToTranlateArrayFake($Translations);