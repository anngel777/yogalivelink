<?php

if (!empty($SITECONFIG['wanthtml']) and $need_content) {

    $ADMIN_CSS_PATH     = ADMIN_CSS_PATH;
    $ADMIN_TINYMCE      = ADMIN_TINYMCE;
    $ADMIN_TINYMCE_PATH = dirname(ADMIN_TINYMCE);

    $ADMIN_DIR = '/' . basename(dirname(dirname(__FILE__))) . '/admin';
    
    printqn("<script type=`text/javascript` src=`$ADMIN_TINYMCE`></script>");

    if (strIn(ADMIN_TINYMCE, 'gzip')) echo "
<script type=\"text/javascript\">
tinyMCE_GZ.init({
    plugins : 'style,table,advhr,advimage,advlink,insertdatetime,preview,media,'+ 
        'searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras',
    themes : 'simple,advanced',
    preformatted : true,
    languages : 'en',
    disk_cache : true,
    debug : false
});
</script>
";
    printqn("<script type=`text/javascript` src=`$ADMIN_DIR/admin_tinymce_js.php?TP=$THIS_PAGE{$SV}CSS=$ADMIN_CSS_PATH{$SV}PATH=$ADMIN_TINYMCE_PATH`></script>");
}

    

