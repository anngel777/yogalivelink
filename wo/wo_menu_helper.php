<?php
function wo_GenerateMenu($CATEGORY='', $module_table='admin_modules')
{
    global $USER, $SQL;

    if ($CATEGORY) {
        $js_prefix  = 'parent.';
        $MENU_ITEMS = '';
    } else {
        $js_prefix  = '';
        $MENU_ITEMS = '<a class="menu_icon" href="index"><img src="/wo/images/menu_icons/reset.gif" alt="Reset" /><br /><span>Reset</span></a>';
    }

    $MENU_TEMPLATE = '<a class="@CLASS@" href="#" onclick="' . $js_prefix . 'appformCreate(\'@TITLE@\', \'@PAGE@\',\'apps\'); return false;">
        <img src="/wo/images/menu_icons/@IMAGE@" alt="@TITLE@" /><br /><span>@TITLE@</span></a>';

    $modules_list = $SQL->GetArrayAll($module_table, "`title`,`is_folder`,`filename`,`image`", "active=1 AND category='$CATEGORY'", 'title');

    if ($modules_list) {

        foreach ($modules_list as $module) {

            $mod_title = $module['title'];
            $mod_name  = $module['filename'];
            $mod_image = $module['image'];
            $mod_folder = $module['is_folder'];
            $class = ($mod_folder)? 'menu_icon_folder' : 'menu_icon';

            if (in_array($mod_name, $USER->Module_Roles) or $USER->Super_User or ($mod_name == 'update_profile')) {
                $MENU_ITEMS .= str_replace(
                    array('@TITLE@','@PAGE@', '@IMAGE@', '@CLASS@'),
                    array($mod_title, $mod_name, $mod_image, $class),
                    $MENU_TEMPLATE
                );
            }
        }
    } else {
        $MENU_ITEMS = '<h1>No Modules Found!</h1>';
    }
    echo $MENU_ITEMS . '<br style="clear:both;" />';

}

function wo_GenerateGroupMenu($module_table='admin_modules')
{
    global $USER, $SQL;

    addScriptOnReady("
    $('.menu_icon_bar').each(function() {
        var id = $(this).attr('id');
        $('#' + id + ' span').toggleClass('updown_down');
        $(this).click(function (){
            $('#div_' + id).slideToggle('normal', function (){
                $('#' + id + ' span').toggleClass('updown_down');
            });
            return false;
        });
    });
    ");


    $MENU_ITEMS = "\n\n<!-- START GENERAL GROUP -->\n" . '<br />
    <a class="menu_icon_bar" href="#" id="menu_group_general">General<span class="updown updown_down">&nbsp;</span></a>' .
        "\n<div class=\"menu_group\" id=\"div_menu_group_general\">\n<table><tbody><tr><td>\n";

    $MENU_ITEMS .= '<a class="menu_icon" href="index"><img src="/wo/images/menu_icons/reset.gif" alt="Reset" /><br /><span>Reset</span></a>';

    $MENU_TEMPLATE = '<a class="@CLASS@" href="#" onclick="appformCreate(\'@TITLE@\', \'@PAGE@\',\'apps\'); return false;">
        <img src="/wo/images/menu_icons/@IMAGE@" alt="@TITLE@" /><br /><span>@TITLE@</span></a>';

    $modules_list = $SQL->GetArrayAll(array(
        'table' => $module_table,
        'keys'  => '`title`,`is_folder`,`filename`,`image`,`category`',
        'where' => 'active=1 AND is_folder=0',
        'order' => 'is_folder, category, title'
    ));

    $folder = ''; // starts with no categories;
    $start = true;
    if ($modules_list) {

        $MENU_ITEMS_GROUP = '';
        foreach ($modules_list as $module) {

            $mod_name  = $module['filename'];

            if (in_array($mod_name, $USER->Module_Roles) or $USER->Super_User or ($mod_name == 'update_profile')) {
                $mod_title = $module['title'];
                $mod_image = $module['image'];
                $mod_category = $module['category'];
                $class = 'menu_icon';


                if ($mod_category != $folder) {
                    $MENU_ITEMS .= "\n</td></tr></tbody></table>\n</div>\n<!-- END GROUP -->\n\n";
                    $folder = $mod_category;
                    $group_title = NameToTitle($mod_category);
                    $id = 'menu_group_' . preg_replace('/[^a-zA-Z]/', '', $mod_category);
                    $MENU_ITEMS .= "\n\n<!-- START GROUP -->\n" . '<br />
        <a class="menu_icon_bar" href="#" id="' . $id . '">' . $group_title . '<span class="updown">&nbsp;</span></a>' .
                    "\n<div class=\"menu_group\" style=\"display:none;\" id=\"div_$id\">\n<table><tbody><tr><td>\n";
                }

                $MENU_ITEMS .= str_replace(
                    array('@TITLE@','@PAGE@', '@IMAGE@', '@CLASS@'),
                    array($mod_title, $mod_name, $mod_image, $class),
                    $MENU_TEMPLATE
                );
            }
        }
        $MENU_ITEMS .= "\n</td></tr></tbody></table>\n</div>\n<!-- END GROUP -->\n\n";

    } else {
        $MENU_ITEMS = '<h1>No Modules Found!</h1>';
    }
    echo $MENU_ITEMS . '<br style="clear:both;" />';

}