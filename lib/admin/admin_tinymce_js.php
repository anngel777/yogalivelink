<?php
    include '../mvptools.php';

    SetGet('TP CSS PATH');
    $TP = urldecode($TP);
    $TP = str_replace(' ', '+', $TP);

    print <<<LABELTINYMCE1
tinyMCE.init({
    mode : "none",
    button_tile_map : true,
    theme : "advanced",
    plugins : "style,table,advhr,advimage,advlink,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras",
    theme_advanced_buttons1_add_before : "preview,fullscreen",
    theme_advanced_buttons1_add : "fontselect,fontsizeselect",
    theme_advanced_buttons2_add : "separator,insertdate,inserttime,zoom,separator,forecolor,backcolor",
    theme_advanced_buttons2_add_before: "cut,copy,paste,pastetext,pasteword,separator,search,replace,separator",
    theme_advanced_buttons3_add_before : "tablecontrols,separator",
    theme_advanced_buttons3_add : "iespell,flash,advhr",
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
    theme_advanced_path_location : "bottom",
    plugin_insertdate_dateFormat : "%m/%d/%Y",
    plugin_insertdate_timeFormat : "%H:%M:%S",
    extended_valid_elements : "a[name|class|style|href|target|title|onclick],img[class|style|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|style|width|size|noshade],font[face|size|color|style],span[class|align|style]",
    theme_advanced_resizing : true,
    theme_advanced_resize_horizontal : false,
    preformatted : true,
    gecko_spellcheck : true,
    relative_urls : false,
    convert_urls : false,
    content_css : "$CSS",
    popups_css : "$PATH/themes/advanced/css/editor_popup.css",
    external_link_list_url : "$TP?TINYMCE_FILE_LINKS=1",
    external_image_list_url : "$TP?TINYMCE_IMAGE_LINKS=1"
});
LABELTINYMCE1;

exit;
