<?php

// FILE: class.EventSetups.php

/*-------------------PLACE CHANGE NOTES HERE--------------------

2009-04-29: MVP-> Added Events_Id, Added select for event type

-------------------------------------------------------*/


class EventSetups extends BaseClass
{
    public $Record= '';

    public $Registration_Methods = array();

    public function  __construct($event_type='', $event_setups_id='')
    // event type = ICC/TST/IRD
    {
        global $ROOT;

        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => '',
            'Description' => 'Create and manage event_setups',
            'Created'     => '2009-01-14',
            'Updated'     => '2009-01-14'
        );

        $this->Add_Submit_Name  = 'EVENT_SETUPS_SUBMIT_ADD';
        $this->Edit_Submit_Name = 'EVENT_SETUPS_SUBMIT_EDIT';
        $this->Table  = 'event_setups';
        $this->Flash_Field = '';

        $this->Index_Name = 'event_setups_id';
        $this->Default_Sort = "$this->Index_Name DESC";

        if ($event_setups_id != '') {
            $this->Record = $this->GetEventSetupsRecord($event_setups_id);
            if ($this->Record) {
                $registration_methods = preg_replace('/\|+/', '|', $this->Record['registration_methods']);
                $registration_methods = preg_replace('[(^\|)|(\|$)]', '', $registration_methods);
                $this->Registration_Methods = explode('|', $registration_methods);
            }
        }

        $this->Field_Titles = array(
            'event_setups.id' => 'Id',
            'event_setups.event_type' => 'Event Type',
            'events.event_name' => 'Event',
            'events.events_id'  => 'Event ID',
            'event_setups.title' => 'Title',
            'event_setups.display_title' => 'Display Title',
            'event_setups.approval_status' => 'Approval Status',
            'event_setups.display_status' => 'Display Status',
            'event_setups.geo' => 'Geo',
            'event_setups.region' => 'Region',
            'event_setups.event_date_start' => 'Event Date Start',
            'event_setups.event_date_end' => 'Event Date End',
            'time_zones.time_zone'  => 'Time Zone',
            'event_setups.template' => 'Template',
            'event_setups.web_languages' => 'Website Languages',
            'event_setups.onsite_languages' => 'On-site Training Languages',
            'event_setups.location_name' => 'Location Name',
            'event_setups.location_address_1' => 'Location Address 1',
            'event_setups.location_address_2' => 'Location Address 2',
            'event_setups.location_city' => 'Location City',
            'event_setups.location_state' => 'Location State',
            'event_setups.location_country' => 'Location Country',
            'event_setups.location_postal_code' => 'Location Postal Code',
            'event_setups.hotel_required'  => 'Hotel Required',
            'event_setups.hotel_information'  => 'Hotel Information',
            'event_setups.transportation_information'  => 'Transportation Information',
            'event_setups.parking_details' => 'Parking Details',
            'event_setups.diet_options' => 'Diet Options',
            'event_setups.registration_types' => 'Registration Types',
            //'event_setups.registration_methods' => 'Registration Methods',
            'event_setups.agenda_items' => 'Agenda Items',
            'event_setups.courses' => 'Courses',

            'login_irc' => 'IRC Login',
            'login_public' => 'Public Login',
            'login_irc_whitelist_type' => 'IRC Whitelist Type',
            'login_public_whitelist_type' => 'Public Whitelist Type',
            'login_irc_level_types' => 'IRC Registration Levels',

            'file_items' => 'File Items',
            'other_items' => 'Other Items',

            'event_setups.created_by' => 'Created By',
            'event_setups.active' => 'Active',
            'event_setups.updated' => 'Updated',
            'event_setups.created' => 'Created'
        );



        $this->Default_Values = array(
            'event_type' => $event_type,
            'login_irc_whitelist_type'    => 'none',
            'login_public_whitelist_type' => 'none',
        );

        $this->Default_Fields = 'event_setups.id,event_setups.event_type,event_setups.title,approval_status,display_status,geo,region,event_date_start,events.event_name';

        $this->Joins = "LEFT JOIN `events` ON `events`.`events_id`=`event_setups`.`events_id`
        LEFT JOIN time_zones ON time_zones.time_zones_id=event_setups.time_zones_id";

        $this->Autocomplete_Fields = array(
            'events_id'            => "events|events_id|event_name"
        );  // associative array: field => table|field|variable


        $this->Unique_Fields = '';
    } // ================================= end construct =====================================


    public function SetFormArrays() // overrides parent
    {

        $event_types = db_GetFieldValues('event_types', 'event_type', 'active=1', 'U');
        $event_list  = Form_ArrayToList($event_types);

        AddStyle('fieldset {margin:2em 0px;}');

        $language_list = "English|Portuguese|Spanish|Russian|Korean|Vietnamese|Chinese|Thai|Indonesian";

        $regions     = db_GetFieldValues($this->Table, 'region');
        $region_list = Form_ArrayToList($regions);
        //$regions = "|NAMO (no region)|LAR (no region)|APAC (no region)|PRC (no region)|CER (Germany and Liechtenstein)|Iberia (Portugal and Spain)|Israel|Italy (Italy and Switzerland)|Benelux (Belgium, The Netherlands)|France|Greece (Cyprus and Greece)|Nordics (Denmark, Norway, Sweden)|United Kingdom|CEE|META|RCIS";

        $diet_options = "None|Halal|Kosher|Vegan|Vegetarian|Low Cholesterol|No Dairy|No Wheat|Allergic To Peanuts|Gluten Free|No Seafood";

        $eq_events_id = EncryptQuery("ac_table=events&ac_key=events_id&ac_field=event_name");

#            "checkboxlistbar|Website Languages|web_languages|N||$language_list",
#            "checkboxlistbar|On-site Training Languages|onsite_languages|N||$language_list",

        $time_zone_array = db_GetAssocArray(array(
            'table' => 'time_zones',
            'key'   => 'time_zones_id',
            'value' => 'time_zone',
            'where' => 'active=1'
        ));

        $time_zone_list = Form_AssocArrayToList($time_zone_array);

        $base_array = array(

            "select|Event Type|event_type|Y||$event_list",
            "autocomplete|Event Series|events_id|Y|60|80||addAutoCompleteFunctionality|$this->Auto_Complete_Helper?eq=$eq_events_id",
            "text|Title|title|Y|60|255",
            "text|Display Title|display_title|Y|60|100",
            "select|Approval Status|approval_status|Y||Requested|Pending|Pre-Approved|Approved|Denied",
            "select|Display Status|display_status|Y||Not Showing|Showing",
            'select|Geo|geo|Y||APAC|EMEA|LAR|NAMO|PRC',
            "selecttext|Region|region|Y|40|80|$region_list",

            'fieldset|<b>DATE SETUP</b>',
            "dateYMD|Event Date Start|event_date_start|Y-M-D|Y|NOW|3",
            "dateYMD|Event Date End|event_date_end|Y-M-D|Y|NOW|3",
            "select|Time Zone|time_zones_id|Y||$time_zone_list",


            'code|<div style="color:blue;">
                EXAMPLE:
                <br/>&lt;M&gt; &lt;D&gt;, &lt;Y&gt; = August 15, 2009 [THIS IS THE DEFAULT IF LEFT BLANK]
                <br/>&lt;D&gt; de &lt;M&gt; de &lt;Y&gt; - 15 de August de 2009
                <br />&bull; For (listing) do NOT include year
                </div><br/>
                ',
            "html|Date Format (details)|date_format|N|1|1",
            "html|Date Format (listing)|date_format_listing|N|1|1",
            'endfieldset',

            'fieldset|<b>LOCATION INFORMATION</b>',
            "text|Location Name|location_name|N|60|120",
            "text|Location Address 1|location_address_1|N|60|120",
            "text|Location Address 2|location_address_2|N|60|120",
            "text|Location City|location_city|N|60|120",
            "intstate|Location State|location_state|N||location_country",
            "country|Location Country|location_country|N",
            "text|Location Postal Code|location_postal_code|N|20|20",
            "text|Location Map URL|location_map_url|N|60|5555",
            'endfieldset',

            "checkbox|Hotel Required|hotel_required||1|0",

            "html|Hotel Information|hotel_information|N|60|5",
            "html|Transportation Information|transportation_information|N|60|5",
            "html|Parking Details|parking_details|N|60|5",
            "checkboxlistbar|Diet Options|diet_options|N||$diet_options",
            //'checkboxlistbar|Registration Types|registration_types|N||Member|Associate|Premier|Public|Private',
            //'checkboxlistbarh|Registration Methods|registration_methods|N||IRC|Public|Whitelist',
            'code|<br /><br /><p>Enter Agenda Items, one per line as: <b><i>start-time</i>&#124;<i>end-time</i>&#124;<i>description</i></b></p>',
            'code|<div style="color:blue;">
                EXAMPLE:
                <br/>9:00am&#124;10:00am&#124;Registration
                <br/>10:00am&#124;11:00am&#124;Course 1<br/>
                </div>',
            "html|Agenda Items|agenda_items|N|80|10",
            'code|<br /><br /><p>Enter Courses, one per line as: <b><i>title</i>&#124;<i>description</i></b></p>',
            "html|Courses|courses|N|80|10",

            'code|<br /><br /><p>Enter files you want to have listed for download, one per line as: <b><i>Displayed Filename</i>&#124;<i>File Extension</i>&#124;<i>Description</i>&#124;<i>URL</i></b></p>',
            "textarea|Downloadable Files|file_items|N|80|10",

            'code|<br /><br /><p>Enter additional text you want to display on the page, one per line as: <b><i>Heading</i>&#124;<i>Information</i></b></p>',
            "textarea|Additional Details|other_items|N|80|10",

            //--------------------------------------

            "integer|# Attendees Allowed|attendee_total|N|4|10",
            "integer|# Attendees Per Company|attendee_company|N|4|10",
            "integer|Total Companies Allowed|total_companies_allowed|N|4|10",

            //--------------------------------------

            'fieldset|<b>IRC LOGIN</b>',
            "checkbox|IRC Login|login_irc||1|0",
            "radio|IRC Login Whitelist Type|login_irc_whitelist_type|N||none|whitelist by person|whitelist by company",
            "checkboxlistset|IRC Registration Levels|login_irc_level_types|N||registered|associate|premier",
            'endfieldset',

            //--------------------------------------

            'fieldset|<b>PUBLIC LOGIN</b>',
            "checkbox|Public Login|login_public||1|0",
            "radio|Public Login Whitelist Type|login_public_whitelist_type|N||none|whitelist by person",
            'endfieldset',

            //--------------------------------------

            'fieldset|<b>Translation Display Override Options</b>',
            "html|Event Listing|event_list_display_override|N|60|1",
            "html|Event Details 1st Line|event_details_override_1|N|60|1",
            "html|Event Details 2nd Line|event_details_override_2|N|60|1",
            "html|Confirmation Page Message|override_confirmation_page|N|60|1",
            'endfieldset',


            //--------------------------------------

            'fieldset|<b>Confirmation Email Customization</b>',
            "info|Email Options|If you do not check any of the 'Show' boxes the confirmation email will be blank.",
            "checkbox|Show Location Section|email_show_location||1|0",
            "checkbox|Show Agenda Section|email_show_agenda||1|0",
            "checkbox|Show Payment Section <br />(if applicable)|email_show_payment||1|0",
            "checkbox|Show Hotel Section <br />(if applicable)|email_show_hotel||1|0",

            "text|Subject Line|email_custom_subject|N|60|255",
            "html|Introduction|email_custom_intro|N|60|1",
            "html|Middle Section|email_custom_middle|N|60|1",
            "html|Links <br />(display&#124;URL)|email_custom_links|N|60|1",
            "html|Closing <br />(replaces signature)|email_custom_closing|N|60|1",
            'endfieldset',


            //--------------------------------------

            'fieldset|<b>Additional Customizations</b>',
            "checkbox|Hide From Sponsor Site (NAMO)|hide_from_sponsor_site||1|0",
            'endfieldset',




            "hidden|Created_By|$this->User_Name",
        );

        $this->Form_Data_Array_Add = array_merge(
            array(
                "form|$this->Action_Link|post|db_edit_form"
            ),
            $base_array,
            array(
                "submit|Add Record|$this->Add_Submit_Name",
                "endform"
            )
        );

        $this->Form_Data_Array_Edit = array_merge(
            array(
                "form|$this->Action_Link|post|db_edit_form"
            ),
            $base_array,
            array(
                "checkbox|Active|active||1|0",
                "submit|Update Record|$this->Edit_Submit_Name",
                "endform"
            )
        );

    }




    public function GetTemplateList($event_type)
    {

    //==================== TEMPLATES NOT NEEDED ====================//
        global $ROOT;
        if ($event_type) {
            $event_path = strtolower($event_type);
            $templates = GetDirectory("$ROOT/$event_path/templates");
            $template_list = '';
            foreach ($templates as $template) {
                $template_list .= "|$template";
            }
        } else {
            $template_list = '|No templates defined';
        }
        return $template_list;
    }

    public function EditRecord($id, $id_field='id')
    {
        global $FormPrefix;
        $id = IntOnly($id);

        echo $this->EditRecordText($id, $id_field);
    }

    public function ProcessRecordCell($field, &$value, &$td_options)
    {
        $value = preg_replace('/\|+/', '<br />', $value);
        $value = preg_replace('[(^<br />)|(<br />$)]', '', $value);
    }


    public function ProcessTableCell($field, &$value, &$td_options, $id='')
    {
        parent::ProcessTableCell($field, $value, $td_options, $id);

        $text_fields = array('hotel_information', 'transportation_information', 'parking_details', 'agenda_items');

        if (in_array($field, $text_fields)) {
            $value = TruncStr(strip_tags($value), 50);
        } elseif (($field == 'file_items') or ($field == 'other_items')) {
            $value = nl2br($value);
        }
    }

    public function GetEventList($EVENT_TYPE, $EVENTS_ID, $EVENT_GEO, $SHOW_STATE='Showing')
    {
        $SHOWING = ($SHOW_STATE=='Showing') ? "AND `display_status`='$SHOW_STATE'" : "";

        $date = date('Y-m-d');

        $query_info = array(
            'table' => $this->Table,
            'keys'  => '*',
            'where' => "event_type='$EVENT_TYPE' AND `events_id`='$EVENTS_ID'
                        AND geo='$EVENT_GEO' $SHOWING AND `approval_status`='Approved'
                        AND event_date_end > '$date' AND active=1",   // mvp added event_end_date 8/12/09
            'order' => 'event_date_start ASC'
        );

        return db_GetArrayAll($query_info);
    }


    public function GetEventSetupsRecord($event_setups_id)
    {
        $event_setups_id = intOnly($event_setups_id);
        return db_GetRecord($this->Table, '*', "event_setups_id=$event_setups_id");
    }

    public function GetEventsRecord($events_id)
    {
        $events_id = intOnly($events_id);
        return db_GetRecord(array(
            'table' => 'events',
            'keys'  => '*',
            'where' => "events_id=$events_id"
        ));
    }

    public function GetDietList($diet_options)
    {
        $diet_options = preg_replace('/\|+/', '|', $diet_options);
        $diet_options = preg_replace('[(^\|)|(\|$)]', '', $diet_options);
        return $diet_options;
    }

    public function GetRecordFieldValue($FIELD)
    {
        $value = ArrayValue($this->Record, $FIELD);
        return $value;
    }

    public function SetContactRestrictedList($event_setups_id)
    {
        $event_setups_id = intOnly($event_setups_id);
        $list = db_GetValue($this->Table,
               'contact_required_fields', "`event_setups_id`=$event_setups_id",
               "LEFT JOIN `events` on `events`.`events_id` = `$this->Table`.`events_id`");

        $RESULT = array();
        if($list) {
            $items = explode(',', $list);
            foreach ($items as $item) {
                $RESULT[$item] = 'Y';
            }
        }

        $_SESSION['CONTACT_ADDITIONAL_REQUIRED'] = $RESULT;
        return $RESULT;
    }

    protected function TriggerAfterInsert($db_last_insert_id)
    {
        $this->SQL->UpdateRecord($this->Table, "id=$db_last_insert_id", "$this->Index_Name=$db_last_insert_id");
    }

}