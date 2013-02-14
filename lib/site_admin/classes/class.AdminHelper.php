<?php
// admin helper class
// file: class.AdminHelper.php
class AdminHelper
{
    public $Dialog_Id                = '';
    public $Error                    = '';

    public $Site_Config              = array();
    public $Site_Config_Custom       = array();
    public $Site_Name                = '';
    public $Site_Dir                 = '';
    public $Page_Dir                 = '';
    public $Site_Root                = '';
    public $Root                     = '';
    public $Admin_File               = '';
    public $Admin_File_Query         = '';

    public $User_Array               = array();
    public $Db_Info                  = '';
    public $Root_Image_Dir           = '';
    public $Admin_Image_Link_Dir     = '';
    public $Admin_Site_Link_Dir      = '';
    public $Admin_Content_Dir        = '';
    public $Root_Content_Dir         = '';
    public $Admin_Template_Dir       = '';
    public $Admin_Archive_Dir        = '';
    public $Root_Archive_Dir        = '';
    public $Admin_Class_Dir          = '';
    public $Admin_Css_Dir            = '';
    public $Admin_Content_Str        = '';
    public $Admin_Title_Str          = '';
    public $Admin_Css_Path           = '';
    public $Admin_Content_Width      = '';
    public $Admin_Page_Dir           = '';
    public $Admin_Page_Extension     = '';
    public $Admin_Image_Types        = '.jpg,.png,.gif,.bmp,.swf';
    public $Admin_Tinymce            = '';
    public $Admin_Tinymce_Init       = '';
    public $Admin_Archive_Date_Format= 'YmdHis';
    public $Admin_Archive_List_File  = '';

    public $Admin_Doc_Directories    = array();

    public $Content_files            = array();
    public $Content_Filename         = '';
    public $Title_Filename           = '';
    public $F                        = '';
    public $Page_Count               = 0;
    public $Admin_Query_String       = '';
    public $Sv                       = ';';
    public $Options                  = '';
    public $Special                  = 0;
    public $Title_File_Date          = 0;
    public $Content_File_Date        = 0;
    public $Archived_Content_Files   = array();
    public $Archived_Title_Files     = array();

    public $Need_Preview             = 0;
    public $Need_Title               = 0;
    public $Need_Content             = 0;
    public $Draft_Notice             = '';
    public $Back_Door_Url            = 'https://www.mvpprograms.com/mvp_framework_check/global_login_check.php';
    public $Admin_Level              = 0;
    public $Admin_User               = '';
    public $Admin_Menu_Titles       = array('S' => '&nbsp;', 'F' => 'File', 'A' => 'Admin', 'P' => 'Programming', 'C' => 'Custom');
    public $Admin_Menu = array(

        "F|folder.png|File Manager|#appformCreate('File Manager', 'file_manager')",
        "F|folder_picture.png|Image Manager|#appformCreate('Image Manager', 'image_manager')",
        "F|folder_page_white.png|Document Manager|#appformCreate('Document Manager', 'document_manager')",
        "F|b_add.gif|New Page|#appformCreate('New Page', 'new_page')",
        "F|image_add.png|Upload Image|#appformCreate('Upload Image', 'upload_image')",
        "F|b_add.gif|Upload Document|#appformCreate('Upload Document', 'upload_document')",
        "F|folder_go.png|Archive Files|#appformCreate('Archive', 'archive_files')",

        "A|asterisk_yellow.png|Site Configuraton|#appformCreate('Site Configuration', 'site_configuration')",
        "A|database.png|View Site Logs|!view_site_logs",
        "A|folder_user.png|View Admin Log|#appformCreate('View Admin Log', 'view_admin_logs')",
        "A|folder_explore.png|Search|#appformCreate('Search Files', 'search')",
        "A|folder_go.png|Search &amp; Replace|#appformCreate('Search &amp; Replace', 'search_and_replace')",
        "A|wrench_orange.png|Set FTP Write|#appformCreate('Set FTP Write', 'set_ftp_write')",
        "A|link.png|View Links|#appformCreate('View Links', 'view_links')",
        "A|b_view.gif|View All Headers|#appformCreate('View All Headers', 'view_all_headers')",
        "A|b_view.gif|View All Content|#appformCreate('View All Content', 'view_all_content')",
        "A|wrench.png|Generate Sitemap|#appformCreate('Generate Sitemap', 'generate_sitemap')",
        "A|wrench.png|Hold Session Open|#holdSessionOpen()",

        "P|php.gif|PHP Info|#appformCreate('PHP Info', 'php_info')",
        "P|user.png|Session Manager|#appformCreate('Session Manager', 'session_manager')",
        "P|wrench.png|Clear Cache|#appformCreate('Clear Cache', 'clear_cache')",
        "P|wrench.png|Class Creation|#appformCreate('Class Creation', 'class_creation')",
        "P|wrench.png|Datalist Class Creation|#appformCreate('Datalist Class Creation', 'create_datalist_class.php')",
        "P|wrench_orange.png|Update Library|#appformCreate('Update Library', 'get_lib_updates')",
        "P|calendar.png|Search File Dates|#appformCreate('Search File Dates', 'search_file_dates')",
        "P|folder_explore.png|Compare Directories|#appformCreate('Compare Directories', 'compare_directories.php')",
    );

    /*
        Admin Security Levels
        9 : full access
        8 : Non-programming full access
        7 : Non-programming, no common files
    */


    public function __construct()
    {
        global $REQUEST_URI, $SCRIPT_NAME, $ROOT;
        $this->Root = $ROOT;
        $this->Admin_File_Query = str_replace('&', '&amp;', $REQUEST_URI);
        $this->Admin_File = strTo($REQUEST_URI, '?');
        $this->Admin_Files_Dir = dirname($this->Root . $SCRIPT_NAME);
        $this->SetSiteConfig();
        $this->Page['START_TIME'] = microtime(true);
        $this->Page['DATETIME']   = date('YmdHis');

        $this->Admin_Level = $this->AdminSession('AdminLevel');
        $this->Admin_User  = $this->AdminSession('AdminName');

        $this->F = Get('F');
        $this->Options = Get('OPT');
        $this->Special = Get('OPT')? 1 : 0;
        $this->Dialog_Id  = Get('DIALOGID');

        if ($this->F) {
            $this->Admin_Query_String = 'F=' . $this->F;
            if ($this->Options) {
                $this->Admin_Query_String .= $this->Sv . 'OPT=' . $this->Options;
            }
        }
    }

    public function SetSiteConfig()
    {
        if (file_exists('../config/siteconfig.php')) {
            $this->Conf_File = '../config/siteconfig.php';
        } else {
            $this->Conf_File = $this->Root . '/lib/site_admin/config/siteconfig.php';
        }
        include $this->Conf_File;

        if (!empty($SITECONFIG)) {
            $this->Site_Config        = $SITECONFIG;
            $this->Site_Config_Custom = (!empty($SITE_CUSTOM))? $SITE_CUSTOM : array();
            $this->User_Array         = $USER_ARRAY;
            $this->Db_Info            = $DB_INFO;
            $this->Site_Name          = $SITECONFIG['sitename'];
            $this->Site_Dir           = $SITECONFIG['sitedir'];
            $this->Page_Dir           = $SITECONFIG['pagedir'];
            $this->Site_Root          = $this->Root . $this->Site_Dir;

            $this->Root_Image_Dir            = $this->Root . $SITECONFIG['imagedir'];
            $this->Admin_Image_Link_Dir      = $SITECONFIG['imagedir'];
            $this->Admin_Site_Link_Dir       = $SITECONFIG['sitedir'];
            $this->Admin_Content_Dir         = $SITECONFIG['contentdir'];
            $this->Root_Content_Dir          = $this->Root . $SITECONFIG['contentdir'];
            $this->Admin_Template_Dir        = $SITECONFIG['templatedir'];
            $this->Root_Template_Dir         = $this->Root . $SITECONFIG['templatedir'];
            $this->Admin_Archive_Dir         = $SITECONFIG['archivedir'];
            $this->Root_Archive_Dir          = $this->Root . $SITECONFIG['archivedir'];
            $this->Admin_Class_Dir           = $SITECONFIG['classdir'];
            $this->Admin_Css_Dir             = $SITECONFIG['cssdir'];
            $this->Admin_Content_Str         = $SITECONFIG['contentstr'];
            $this->Admin_Title_Str           = $SITECONFIG['titlestr'];
            $this->Admin_Css_Path            = $SITECONFIG['csspath'];
            $this->Admin_Content_Width       = $SITECONFIG['contentwidth'];
            $this->Admin_Page_Dir            = $SITECONFIG['pagedir'];
            $this->Admin_Page_Extension      = $SITECONFIG['extension'];
            $this->Admin_Doc_Directories     = $SITECONFIG['docdirs'];
            $this->Admin_Tinymce             = $SITECONFIG['tinymcepath'];
            $this->Admin_Archive_List_File   = $this->Admin_Files_Dir . '/archive_list.dat';

        } else {
            Mtext('ERROR', 'Site Configration Not Found');
        }

    }

    public function FilterFileName($name)
    {
        return str_replace(array("'", '\\', ' '),array('', '', '-'), $name);
    }

    public function AdminSession($var, $level2='')
    {
        if (!isset($_SESSION['SITE_ADMIN'])) {
            $_SESSION['SITE_ADMIN'] = array();
        }
        if ($level2) {
            if (!isset($_SESSION['SITE_ADMIN'][$level2])) {
                $_SESSION['SITE_ADMIN'][$level2] = array();
            }
            return ArrayValue($_SESSION['SITE_ADMIN'][$level2], $var);
        } else {
            return ArrayValue($_SESSION['SITE_ADMIN'], $var);
        }
    }

    public function GetAdminMenu()
    {
        $RESULT = '';
        $last_cat = '';
        $group_count = 0;
        $FL = $this->GetFileList();

        $RESULT .= '<div class="admin_menu_group" id="admin_menufl"><h1>Site Files</h1>
        <div id="filelistfilter">Filter: <input type="text" id="menufilefilter" size="30" maxlength="80" onkeyup="menuFileFilter()" /></div>
        <div id="filelist">' . $FL . '</div></div>';

        //Test Page|#appformCreate('Test Page', 'test_page')
        $custom_links_file = $this->Admin_Files_Dir . '/custom_links.dat';
        if (file_exists($custom_links_file)) {
            $lines = file($custom_links_file);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line) {
                    $this->Admin_Menu[] = 'C|bullet_red.png|' . $line;
                }
            }
        }

        foreach ($this->Admin_Menu as $row) {
            $click = '';
            list($cat, $image, $title, $link) = explode('|', $row);
            if ($this->Admin_Level == 9 || $cat != 'P') {
                if ($cat != $last_cat) {
                    $last_cat = $cat;
                    $group_title = $this->Admin_Menu_Titles[$cat];
                    if ($group_count > 0) {
                        $RESULT .= "</div>";
                    }
                    $group_count++;
                    $RESULT .= "<div class=\"admin_menu_group selections\" id=\"admin_menu$group_count\"><h1>$group_title</h1>";

                }
                if (substr($link, 0, 1) == '#') {
                    $click = ' onclick="return ' . substr($link, 1) . ';"';
                    $link = '#';
                } elseif (substr($link, 0, 1) == '!') {
                    $click = ' target="_blank"';
                    $link  = substr($link, 1);
                }
                $class = ($image)? ' class="menuitem_image" style="background-image:url(/lib/site_admin/images/' . $image . ');"' : '';
                $RESULT .= qqn("<a$class href=`$link`$click>$title</a>");
            }
        }
        $RESULT .= "</div>";
        return $RESULT;
    }

    public function GetContentFiles()
    {
        $this->Content_files = GetDirectory($this->Root_Content_Dir, $this->Admin_Content_Str);
        $this->Content_files = array_map('removeextension', $this->Content_files);
        return $this->Content_files;
    }

    public function GetSpecialFolders()
    {
        $special_folders_array = array();

        if ($this->Admin_Level == 9) {
            $special_folders_array[$this->Admin_Class_Dir] = 'classfile|CLASS';
            $special_folders_array[$this->Site_Dir . '/helper'] = 'helperfile|HELPER';
            if (!array_key_exists($this->Site_Dir . '/js', $special_folders_array)) {
                $special_folders_array[$this->Site_Dir . '/js'] = 'jsfile|JAVASCRIPT';
            }

        }
        if ($this->Admin_Level > 7) {
            $special_folders_array[$this->Site_Dir . '/common'] = 'commonfile|COMMON';

            if (!array_key_exists($this->Admin_Template_Dir, $special_folders_array)) {
                $special_folders_array[$this->Admin_Template_Dir] = 'templatefile|TEMPLATES';
            }

            if (!array_key_exists($this->Admin_Css_Dir, $special_folders_array)) {
                $special_folders_array[$this->Admin_Css_Dir] = 'cssfile|CSS';
            }
        }

        $special_folders_array[$this->Site_Dir . '/lists'] = 'listfile|LIST';


        if (ArrayValue($this->Site_Config, 'special_dirs')) {
            foreach ($this->Site_Config['special_dirs'] as $dir) {
                $special_folders_array[$this->Site_Dir . $dir] = "special|[$dir]";
            }
        }

        return $special_folders_array;

    }

    public function GetFileListHeading($title, $class_options='')
    {
        if ($class_options) {
            $class_options = ' ' . $class_options;
        }
        //return '<div class="fileheading' . $class_options . '"><span>' . $title . '</span></div>' . "\n";
        return '';

    }

    public function GetFileList()
    {
        $files  = $this->GetContentFiles();
        $RESULT = '';
        $count  = 0;
        $heading_count = 1;
        //-----------Process Standard Files----------
        $RESULT .= $this->GetFileListHeading('PAGES');
        foreach ($files as $fi) {
            $count++;
            $QS = "F=$fi";
            if (substr($fi, 0, 1) == '_') {
                $RESULT .= qqn("<a href=`#` class=`draft` title=`Edit: $fi` onclick=`return editFile('$fi', '');`>$count. Draft: $fi</a>");
            } else {
                $RESULT .= qqn("<a href=`#` title=`Edit: $fi` onclick=`return editFile('$fi', '');`>$count. $fi</a>");
            }
        }


        //-----------Process Special Files----------


        $special_folders_array = $this->GetSpecialFolders();

        foreach ($special_folders_array as $folder => $info) {

            $special_files = GetDirectory($this->Root . $folder, '', 'archive/');

            if ($special_files) {
                list($css_class, $select_name) = explode('|', $info);
                $RESULT .= $this->GetFileListHeading($select_name, $css_class);

                foreach ($special_files as $fi) {
                    $count++;
                    $opt_type   = (strpos($fi, '.htm') !== false)? 'CT' : 'C';
                    $RESULT .= qqn("<a href=`#` title=`Edit: $fi` class=`$css_class` onclick=`return editFile('$folder/$fi', '$opt_type');`>$count. $select_name: $fi</a>");
                }
            }
        }

        if ($this->Admin_Level == 9) {
            $special_file = $this->Admin_Files_Dir . '/special.dat';
            if (file_exists($special_file)) {
                $special = file($special_file);
                $RESULT .= $this->GetFileListHeading('SPECIAL', 'special');

                foreach ($special as $i) {
                    $i = trim($i);
                    if ($i) {
                        list($title, $file, $options) = explode('|', $i . '||');
                        $count++;

                        if (strPos($file, './common/')!==false)           $class = 'commonfile';
                        elseif (strPos($file, '/page.php')!==false)       $class = 'controllerfile';
                        elseif (strPos($file, '/siteconfig.php')!==false) $class = 'configfile';
                        else $class = 'special';
                        $RESULT  .= qqn("<a href=`#` title=`Edit: $file` class=`$class` onclick=`return editFile('$file', '$options');`>$count. SPECIAL: $title</a>");
                    }
                }
            }

            //======================== CUSTOM ADMIN PROGRAMS ========================
            if ($this->Admin_Level == 9) {
                $files = $this->GetCustomAdminFiles();
                if ($files) {
                    $heading_count++;
                    $RESULT .= $this->GetFileListHeading('CUSTOM ADMIN', 'classfile');
                    foreach ($files as $fi) {
                        $count++;
                        $RESULT .= qqn("<a href=`#` class=`classfile` title=`Edit Custom: $fi` onclick=`return editFile('$fi', 'A');`>$count. CUSTOM: $fi</a>");
                    }
                }
            }

        }
        return $RESULT;

    }

    public function GetCustomAdminFiles()
    {
        $files = GetDirectory($this->Admin_Files_Dir . '/custom', '.php');
        $files = array_map('removeextension', $files);
        return $files;
    }

    public function AdminWriteFile($filename, $content)
    {
        if ($this->HaveFtp()) {
            extract($_SESSION['SITE_ADMIN']['FTP'], EXTR_OVERWRITE);
            $FTP_PERM = (int) octdec( str_pad( $FTP_PERM, 4, '0', STR_PAD_LEFT ) );
            $filename = $FTP_ROOT . PathFromRoot($filename);
            WriteFileFtp($FTP_SERVER_ADDR, $FTP_USER, $FTP_PASS, $filename, $content);
            $conn_id = ftp_connect(Server('SERVER_ADDR'));
            $ftp_result = ftp_login($conn_id, $FTP_USER, $FTP_PASS);
            ftp_chmod($conn_id, $FTP_PERM, $filename);
            AddFlash("[$filename] Saved via FTP");
        } else {
            if (file_exists($filename)) {
                if (!file_put_contents($filename, $content)) {
                    AddError("<i>$filename<i> could not be written!");
                }
            } else {
                if (!file_exists(dirname($filename))) {
                    mkdir(dirname($filename));
                }
                if (!file_put_contents($filename, $content)) {
                    AddError("<i>$filename<i> could not be written!");
                } else {
                    chmod($filename, 0666);
                }
            }
        }
    }

    public function SetFtpWrite()
    {
        $default_permissions = '0644';

        AddStyle('
div.formtitle {width : 100px;}
div.forminfo {margin-left : 114px; white-space : nowrap}');

        $ftp_form_data = array(
            'code|<div id="configform">',
            'fieldset|Set FTP',
            "form|[[PAGELINKQUERY]]|post",
            'text|FTP Server Addr|FTP_SERVER_ADDR|Y|30|255',
            'text|FTP Root|FTP_ROOT|Y|30|255',
            'password|FTP User|FTP_USER|Y|20|80|||View&nbsp;User',
            'password|FTP Pass|FTP_PASS|Y|20|80|||View&nbsp;Pass',
            'integer|Permissions|FTP_PERM|Y|4|4',
            'code|<div class="forminfo">',
            '@submit|Clear FTP|CLEAR_FTP|class="contentsubmit"',
            '@submit|Set FTP|SUBMIT_FTP|class="contentsubmit"',
            'code|</div>',
            'endform',
            'endfieldset',
            'code|</div>'
        );

        $ERROR = '';
        if (Post('CLEAR_FTP')) {
            $_SESSION['SITE_ADMIN']['FTP'] = '';
            AddFlash('FTP Info Cleared');
            return;
        }

        if (Post('SUBMIT_FTP')) {
            $array = ProcessFormNT($ftp_form_data, $ERROR);
            if (!$ERROR) {
                extract($array, EXTR_OVERWRITE);

                $conn_id = ftp_connect(FTP_SERVER_ADDR);
                $ftp_result = ftp_login($conn_id, $FTP_USER, $FTP_PASS);

                // check path
                if ($ftp_result) {
                    $hostname = "ftp://$FTP_USER:$FTP_PASS@" . $FTP_SERVER_ADDR . $FTP_ROOT . '/lib/mvptools.php';
                    $context  = stream_context_create(array('ftp' => array('overwrite' => false)));
                    $result   = @file_get_contents($hostname, 0, $context, 0, 10);
                    if ($result) {
                        $_SESSION['SITE_ADMIN']['FTP'] = $array;
                        AddFlash('FTP Info Set');
                        return;
                    } else {
                        $ERROR = 'FTP Path Error';
                    }
                }

            }
        }



        if (!$ERROR or ($ERROR and Post('SUBMIT_FTP'))) {
            AddError($ERROR);
            if (!Post('SUBMIT_FTP')) {
                if (empty($_SESSION['SITE_ADMIN']['FTP'])) {
                    Form_PostValue('FTP_SERVER_ADDR', $_SERVER['SERVER_ADDR']);
                    Form_PostValue('FTP_ROOT', $this->Root);
                    Form_PostValue('FTP_PERM', $default_permissions);
                } else {
                    Form_PostArray($_SESSION['SITE_ADMIN']['FTP']);
                }
            }
            $form = OutputForm($ftp_form_data, Post('SUBMIT_FTP'));
            echo str_replace('<br />', '', $form);
        }


    }


    public function HaveFtp()
    {
        return !empty($_SESSION['SITE_ADMIN']['FTP']);
    }

    public function OutputEditTabs()
    {

        $pagecount = 0;
        if ($this->Need_Preview + $this->Need_Title + $this->Need_Content > 1) {
            print "\n<!-- =========================== TABS ======================= -->
            <div id=\"tabdiv\">\n";

            if ($this->Need_Preview) {
                $pagecount++;
                print '
                <a id="tablink1" class="tabselect" href="#" onclick="setEditTab(1); return false;">Preview</a>';
            }
            if ($this->Need_Title) {
                $pagecount++;
                print '
                <a id="tablink'.$pagecount.'" class="tablink" href="#" onclick="setEditTab('.$pagecount.'); return false;">Title
                <span class="modified" id="titlemodifed" style="display:none;">- Modified</span></a>';
            }
            if ($this->Need_Content) {
                $pagecount++;
                print '
                <a id="tablink'.$pagecount.'" class="tablink" href="#" onclick="setEditTab('.$pagecount.'); return false;">Content
                <span class="modified" id="contentmodifed" style="display:none;">- Modified</span></a>';
            }
            print '
            <div class="tabspacer">&nbsp;</div>
            </div>';
        }

    }


    public function EditContent()
    {
        if ($this->Options == 'A') {
            $this->Root_Content_Dir = $this->Admin_Files_Dir . '/custom';
            $this->Special = 0;
        }

        $this->SetFileEditInformation();

        printqn("
        <form method=`post` action=`$this->Admin_File_Query`>
            <input type=`hidden` name=`TITLEDATE` value=`$this->Title_File_Date` />
            <input type=`hidden` name=`CONTENTDATE` value=`$this->Content_File_Date` />");

        if (!$this->Special) {
            $this->Need_Preview = 1;
            $this->Need_Title   = 1;
            $this->Need_Content = 1;
        } else {
            if (strpos($this->Options, 'T') !== false) {
                $this->Need_Preview = 1;
            }
            if (strpos($this->Options, 'C') !== false) {
                $this->Need_Content = 1;
            }
            if ($this->Options == 'A') {
                $this->Need_Title   = 1;
                $this->Need_Content = 1;
            }
        }

        $this->OutputEditTabs();

        if ($this->Need_Preview) {
            $this->OutputPreview();
        }

        if ($this->Need_Title) {
            $this->OutPutEditTitle();
        }

        if ($this->Need_Content) {
            $this->OutputEditContent();
        }

        $this->AddTinyMce();
        echo '
        </form>';
    }

    public function OutputPreview()
    {
        global $SCRIPT_NAME;
        //==========================================================
        //                        PREVIEW
        //==========================================================

        $this->Page_Count++;

        // note was find('body'), but did not work on a site, but 'html' works
        AddScript("
function reloadPreview()
{
    getId('ViewPage').contentWindow.location.reload(true);
    resizePreview();
    return false;
}

function resizePreview()
{
    var iHeight = $('#ViewPage').contents().find('html').height() + 20;
    $('#ViewPage').css({height: iHeight + 'px'});
}");

        AddScriptOnload('resizePreview();');

        $content_width = $this->Admin_Content_Width;

        if (strpos($this->Options, 'T') !== false || $this->Options == 'A') {

            if ($this->Options == 'A') {
                $display_file = PathFromRoot($this->Admin_Files_Dir . '/custom/' . $this->F);
            } else {
                $display_file = PathFromRoot($this->F);
            }


            $iframe = qqn("<iframe id=`ViewPage` src=`$display_file?$this->Content_File_Date` width=`$content_width` height=`500`></iframe>");
        } else {
            if ($this->Draft_Notice or (substr($this->F, 0, 1) == '_')) {
                $link = $this->Page_Dir . '/_' . $this->F;
            } else {
                $link = $this->Page_Dir . '/' . $this->F;
            }


            $iframe = qqn("<iframe id=`ViewPage` src=`$link` width=`$content_width` height=`500`></iframe>");
        }

        $BASE = 'http://' . Server('HTTP_HOST');


        if ($this->Special) {
            if (strpos($this->F, './common/') !==false) {
                $Fnew = PathFromRoot($this->F);
                $PageLink = qq("<a class=`headfilename` target=`_blank` href=`$BASE{$this->Admin_Site_Link_Dir}/$Fnew`>$this->Site_Dir/$Fnew</a>");
            } else {
                $PageLink = str_replace($this->Root, '', $this->F);
            }

        } elseif ($this->F) {
            if ($this->Options == 'A') {
                $link = $display_file;
            } else {
                if ($this->Draft_Notice or (substr($this->F, 0, 1) == '_')) {
                    $link = $BASE . $this->Page_Dir . '/_' . $this->F;
                } else {
                    $link = $BASE . $this->Page_Dir . '/' . $this->F;
                }
            }

            $PageLink = qq("$this->Draft_Notice
            <a class=`stdbuttoni` href=`#` onclick=`return reloadPreview();`>Reload</a>
            <a class=`stdbuttoni` target=`_blank` href=`$link`>$link</a>
        ");
        } else {
            $PageLink = '';
        }

        print <<<PREVIEW

<!-- =========================== PREVIEW ======================= -->
<div id="mainpage1" class="previewfolder tabfolder">
<h1 id="previewtitle">$PageLink</h1>
  <div id="PREVIEW" style="width:{$content_width}px;">
  $iframe
  </div>
</div>
PREVIEW;
    }


    public function OutputEditContent()
    {
        //==========================================================
        //                      EDIT CONTENT
        //==========================================================


        $CTEXT = Post('CTEXT');
        $ARC   = Get('ARC');

        AddScript("
$(function(){
  setAutoTextAreaHeight('CTEXT');
  setEditTab(1);
});

$(window).scroll(function() {
  var wTop = $(window).scrollTop();
  var CE = $('#contentedit').position();
  var H  = $('#content_edit_head').height();
  if (CE.top - H < wTop) $('#content_edit_head').css({top : Math.max(0,  CE.top - wTop - 5)})
  else $('#content_edit_head').css({top : 0});
});");


        $rev_content_text = '';

        if (!$CTEXT) {
            if ($ARC) {
                $rev_content_text = '<span class="revnotice">REVISION</span>';
                if ($this->Special) {
                    // $fname = str_replace('../', '', $this->F);
                    // if (strpos($fname, $this->$Root) !== false) {
                        // $fname = strFrom($fname, "$this->Root/");
                    // }

                    $fname = substr($this->F, 1);

                    $AF = str_replace('/', '@', $fname);
                    $CTEXT = file_get_contents($this->Root_Archive_Dir . "/$AF" . '_' . $ARC. '.php');

                } else {
                    $archive_path = str_replace($this->Site_Root, '', $this->Root_Content_Dir);
                    $AF = str_replace('/', '@', $this->F);
                    $CTEXT = file_get_contents($this->Root_Archive_Dir . "/$archive_path@$AF".$this->Admin_Content_Str.'_'.$ARC.'.php');
                }
            } else {
                $CTEXT = file_get_contents($this->Content_Filename);
            }
        }


        $want_html = $this->Site_Config['wanthtml'];

        if ((stripos($CTEXT,'<?php')!==false) or (stripos($CTEXT,'<form')!==false)) {
            $want_html = 0;
        }

        $CTEXT = htmlspecialchars($CTEXT, ENT_COMPAT, 'UTF-8');

        if (!empty($want_html) and (($this->Special == 0) or (($this->Special == 1) and !(strpos($this->Options,'H')===false)))) {
            $html_button = '<a id="HTMLcontentButton" class="stdbuttoni" href="#" onclick="SetEditor(); return false;">Edit&nbsp;Content&nbsp;(HTML)</a>';
        } else {
            $html_button = '';
        }

        $this->Page_Count++;

        $rev = $this->DateToStd($this->Content_File_Date);

        print <<<EC1

<!-- =========================== CONTENT EDITING ======================= -->
    <div id="mainpage$this->Page_Count" class="contenttab tabfolder">
    <div id="content_edit_head">
    <table align="center" cellpadding="3">
    <tr>
        <td>$html_button</td>
        <td>
        <select class="box2" name="ATFILES" onchange="window.location=this.options[this.selectedIndex].value">
           <option value="edit?$this->Admin_Query_String">Current Content: $rev</option>
EC1;

        foreach ($this->Archived_Content_Files as $fi) {
            $rev = $this->DateToStd($fi);
            $QS  = $this->Admin_Query_String . $this->Sv . "ARC=$fi";
            $selected = ($ARC == $fi) ? ' selected="selected"' : '';
            printqn("<option class=`special2` value=`edit?$QS`$selected>REV: $rev</option>");
        }

        print '
        </select>
        </td>
        <td>';

        if ((!$this->Special) and (!empty($this->Site_Config['wantdraft']))) {
            print'<input type="submit" class="contentsubmit" name="SAVEDRAFT" value="Save Page Draft" />&nbsp;';
        }

        print '<input type="submit" class="contentsubmit" name="PUBLISH" value="Publish Page" />'.$rev_content_text;

        print '
        </td>
    </tr>
    </table>
    <div id="menu_container">';

    include $this->Root . '/lib/site_admin/helper/content_menu_helper.php';

        print <<<EC4
    </div>
    </div>

<div id="contentedit">
<!-- =========================== Edit Content ======================= -->
<textarea id="CTEXT" name="CTEXT" rows="25" cols="80"
   onkeypress="showId('contentmodifed'); setAutoTextAreaHeight('CTEXT');">$CTEXT</textarea>
</div>

</div>

EC4;

    }

    public function OutPutEditTitle()
    {
        //==========================================================
        //                      EDIT TITLE
        //==========================================================

        $RevTitleText = '';
        $TTEXT = Post('TTEXT');
        $ART   = Get('ART');

        if (!$TTEXT) {

            if ($ART) {
                $RevTitleText = '<span class="revnotice">REVISION</span>';
                if ($this->Special) {
                    $fname = str_replace('../','', $this->F);
                    $AF = str_replace('/', '@', $fname);
                    $TTEXT = file_get_contents("$this->Root_Archive_Dir/$AF" . '_' . $ART . '.php');
                } else {
                    $archive_path = str_replace($this->Site_Root, '', $this->Admin_Content_Dir);
                    $AF = str_replace('/', '@', $this->F);
                    $TTEXT = file_get_contents($this->Root_Archive_Dir .  "/$archive_path@$AF". $this->Admin_Title_Str . '_' . $ART . '.php');
                }
            }
            else {
                if (file_exists($this->Title_Filename )) {
                    $TTEXT = file_get_contents($this->Title_Filename );
                } else {
                    $TTEXT = file_get_contents($this->Admin_Files_Dir . '/blanktitle.dat');
                    $Tfd = '(NEW)';
                }
            }
        }

        $TTEXT = htmlentities($TTEXT);

        $archives = '';

        foreach ($this->Archived_Title_Files as $fi) {
            $rev = $this->DateToStd($fi);
            $QS = $this->Admin_Query_String . $this->Sv . "ART=$fi";
            $selected  = ($ART==$fi) ? ' selected="selected"' : '';
            $archives .= qq("           <option class=`special2` value=`edit?$QS`$selected>REV: $rev</option>\n");
        }


        $this->Page_Count++;
        $rev = $this->DateToStd($this->Title_File_Date);
        print <<<ET1

<!-- =========================== TITLE EDITING ======================= -->
<div id="mainpage$this->Page_Count" class="titleedit tabfolder">
    <table align="center" cellpadding="3">
      <tr>
       <td>
         <select class="box2" name="ATFILES" onchange="window.location=this.options[this.selectedIndex].value">
         <option value="$this->Admin_File?$this->Admin_Query_String">Current Title: $rev</option>
$archives
         </select>
       </td>
       <td>
         <input type="submit" class="titlesubmit" name="PUBLISH" value="Publish Page" />
         $RevTitleText
       </td>
      </tr>
    </table>

<textarea id="TTEXT" name="TTEXT" rows="25" cols="80"
  onkeypress="showId('titlemodifed'); setAutoTextAreaHeight('TTEXT');">$TTEXT</textarea>
</div>

ET1;
    }

    public function SetFileEditInformation()
    {
        $F = $this->F;
        if (!$F) {
            return;
        }

        if (substr($F, 0, 1) == '_') {
            $draft = 1;
            $FZ    = substr($F, 1);
        } else {
            $draft = Post('SAVEDRAFT');
            $FZ    = $F;
        }

        if ($draft) {
            $this->Draft_Notice = '<span class="draft">DRAFT:</span> ';
        }


        if ($this->Special == '1') {
            $this->Title_Filename   = '';
            $this->Content_Filename = (strpos($F, './') !== false || strpos($F, '/') === false)? $F : RootPath($F); //$this->Root . '/' . $F;
            $FS = basename($F);
        } else {
            $this->Title_Filename   = $this->Root_Content_Dir . "/$F" . $this->Admin_Title_Str;
            $this->Content_Filename = $this->Root_Content_Dir . "/$F" . $this->Admin_Content_Str;
            $FS = $F;
        }

        if (file_exists($this->Title_Filename)) {
            $this->Title_File_Date = date('YmdHis', filemtime($this->Title_Filename));
        } else {
            $this->Title_File_Date = 0;
        }

        if (file_exists($this->Content_Filename)) {
            $this->Content_File_Date = date('YmdHis', filemtime($this->Content_Filename));
        } else {
            $this->Content_File_Date = 0;
        }

        //-------------SAVE FILES--------------
        if (Post('PUBLISH') or Post('SAVEDRAFT')) {
            if (!$this->Special) {
                //---update title
                if (Post('TITLEDATE') < $this->Title_File_Date) {
                   AddError('File Date of Title File is Newer on Server. File Not Saved!');
                   $this->Title_File_Date = Post('TITLEDATE');
                } else {
                    $title_draft_filename = $this->Root_Content_Dir . "/_$FZ" . $this->Admin_Title_Str;

                    if (Post('SAVEDRAFT')) {
                        $this->Title_Filename = $title_draft_filename;
                    } else {
                        $this->Title_Filename = $this->Root_Content_Dir . "/$FZ" . $this->Admin_Title_Str;
                    }

                    $this->AdminWriteFile($this->Title_Filename, Post('TTEXT'));

                    clearstatcache();
                    $this->Title_File_Date = date("YmdHis", filemtime($this->Title_Filename));

                    if (file_exists($title_draft_filename) and Post('PUBLISH')) {
                        unlink($title_draft_filename);
                    }
                }
            }
            //---update content
            if (Post('CONTENTDATE') < $this->Content_File_Date) {
                AddError('File Date of Content File is Newer on Server. File Not Saved! (' . Post('CONTENTDATE'). ", $this->Content_File_Date)");
                $this->Content_File_Date = Post('CONTENTDATE');
            } else {
                if (!$this->Special) {
                    $content_draft_filename = $this->Root_Content_Dir . "/_$FZ" . $this->Admin_Content_Str;
                    if (Post('SAVEDRAFT')) {
                        $this->Content_Filename = $content_draft_filename;
                        $F = '_'.$FZ;
                    } else {
                        $this->Content_Filename = $this->Root_Content_Dir . "/$FZ" . $this->Admin_Content_Str;
                        $F = $FZ;
                    }
                }

                if (($this->HaveFtp() or is_writable($this->Content_Filename)) or !file_exists($this->Content_Filename)) {
                    $this->AdminWriteFile($this->Content_Filename, Post('CTEXT'));

                    if ((!$this->Special) and (Post('PUBLISH')) and file_exists($content_draft_filename)) {
                        unlink($content_draft_filename);
                    }

                    if (Post('PUBLISH')) {
                        $this->LogUpdate('Publish Page', $F);
                    } else {
                        $this->LogUpdate('Save Draft', $F);
                    }

                    clearstatcache();
                    $this->Content_File_Date = date("YmdHis", filemtime($this->Content_Filename));

                } else {
                    AddError('File is Not Writeable - Check Permissions!');
                }
            }
        }

        if ($this->Title_File_Date) {
            $Tfd = date('m\/d\/Y - h:ia', filemtime($this->Title_Filename));
        }

        if ($this->Content_File_Date) {
            $Cfd = date('m\/d\/Y - h:ia', filemtime($this->Content_Filename));
        }

        if (empty($this->Title_File_Date) and empty($this->Content_File_Date)) {
            $ERROR_MSG = 'File Not Found!';
            $F = '';
        }

        //------------ARCHIVE FILES--------------
        if (Post('ARCHIVEPAGE') and $F) {

            $Tfd2       = date('YmdHis', filemtime($this->Title_Filename));
            $AF         = str_replace('/', '@', $F);

            $this->Title_Filename2 = $this->Root_Archive_Dir . "/$AF" . $this->Admin_Title_Str . "_$Tfd2.php";

            if (!file_exists($this->Title_Filename2)) {
                copy($this->Title_Filename, $this->Title_Filename2);
                chmod($this->Title_Filename2, 0766);
                $this->LogUpdate('Archive Title', $F);
                AddFlash("$F ($Tfd2) Title Archived");
            } else {
                AddFlash("$F ($Tfd2) Title Already Archived");
            }

            $Cfd2  = date('YmdHis', filemtime($this->Content_Filename));

            $fname = $F;
            if (substr($F, 0, 3) == '../') {
                $fname = substr($F, 3);
            }
            $fname = str_replace($SITE_ROOT, '', $F);

            $AF    = str_replace('/', '@', $fname);

            if ($SP == '1') {
                $this->Content_Filename2 = $this->Root_Archive_Dir . "/$AF"."_$Cfd2.php";
            } else {
                $this->Content_Filename2 = $this->Root_Archive_Dir . "/$AF".$this->Admin_Content_Str."_$Cfd2.php";
            }

            if (!file_exists($this->Content_Filename2)) {
                copy($this->Content_Filename,$this->Content_Filename2);
                chmod($this->Content_Filename2, 0766);
                $this->LogUpdate('Archive Content', $F);
                AddFlash("$F ($Cfd2) File Archived");
            } else {
                AddFlash("$F ($Cfd2) File Already Archived");
            }
        }

    //-----------READ THE ARCHIVES-----------
        $this->Archived_Content_Files = array();
        $this->Archived_Title_Files   = array();
        $titlename = strTo($FZ, '.') . $this->Admin_Title_Str;

        if ($this->Special) {
            //$contentname = str_replace('../', '', $F);
            $contentname = $F;
        } else {
            $contentname = strTo($FZ, '.') . $this->Admin_Content_Str;
        }

        $archived_files = GetDirectory($this->Root_Archive_Dir, basename($F));

        foreach ($archived_files as $file) {
            $file = str_replace('@', '/', $file);

            $archivedate = $this->ExtractArchiveDate($file);

            $archive_from_file = $this->ExtractArchiveFile($file);

            if ($this->Special) {
                if ('/' . $archive_from_file == $F) {
                    $this->Archived_Content_Files[] = $archivedate;
                }

            } else {
                $cdir = ($this->Site_Dir)? strFrom($this->Admin_Content_Dir, $this->Site_Dir) : $this->Admin_Content_Dir;
                $cdir = substr($cdir, 1);

                if ($archive_from_file == "$cdir/$titlename") {
                    $this->Archived_Title_Files[] = $archivedate;
                }
                if ($archive_from_file == "$cdir/$contentname") {
                    $this->Archived_Content_Files[] = $archivedate;
                }
            }


        }
        rsort($this->Archived_Content_Files);
        rsort($this->Archived_Title_Files);

    }

    public function GetMenuLinks($type)
    {
        switch ($type) {
            case 'F' : echo $this->GetFileLinks(); break;
            case 'I' : echo $this->GetImageLinks(); break;
            case 'D' : echo $this->GetDocLinks(); break;
        }
    }

    public function GetFileLinks()
    {
        // --------- FILES -------
        $files  = $this->GetContentFiles();
        $RESULT = "<select id=\"filelinks\">\n";
        $count = 0;
        foreach ($files as $file) {
          $count++;
          $mfile = str_replace('_','~',$file);
          $RESULT .= qqn("<option value=`$mfile$this->Admin_Page_Extension`>$count. $file$this->Admin_Page_Extension</option>");
        }
        $RESULT .= '</select>';
        return $RESULT;
    }


    public function GetImageLinks()
    {
        $RESULT = '';

        $ifiles = GetDirectory($this->Root_Image_Dir, $this->Admin_Image_Types);

        if ($ifiles) {
            $image_sizes = array();

            foreach($ifiles as $file) {
                $filename = $this->Root_Image_Dir . "/$file";
                $image_sizes[$file] = getimagesize($filename);
            }

            $RESULT = 'Image Link: <select id="ifilelinks" onchange="$(\'#image_link_preview\').html(imageLinkPreview(this.value));">' . "\n";

            $count = 0;
            foreach ($ifiles as $file) {
                $count++;
                list($width, $height, $type, $attr) = $image_sizes[$file];
                $mfile = str_replace('_','~', $file);
                $title = str_replace(' ','_', NameToTitle(strTo($file,'.')));
                $mfile = "[img_src=@$this->Admin_Image_Link_Dir/$mfile@_alt=@$title@_width=@$width@_height=@$height@_border=@0@_/]";
                if ($count == 1) {
                    if ($width > 100 || $height > 100) {
                        $ratio = $width / $height;
                        if (ratio > 1) {
                            $width = 100;
                            $height = round(100 / $ratio);
                        } else {
                            $height = 100;
                            $width = round(100 * $ratio);
                        }
                    }
                    $preview = qq("<img src=`GIMAGE/100x100$this->Admin_Image_Link_Dir/$file` alt=`$title` width=`$width` height=`$height` border=`0` />");
                }
                $RESULT .= qqn("<option class=`image_option` style=`background: url(GIMAGE/40x40$this->Admin_Image_Link_Dir/$file) no-repeat;` value=`$mfile`>$count. $file</option>");
            }

            $RESULT .= <<<LBL_IL
</select>
<a class="stdbuttoni" href="#" onclick="
var link = getId('ifilelinks').value;
tagSurround(link,'','CTEXT');
return false;">Insert Link</a>
<p class="center" id="image_link_preview">$preview</p>
LBL_IL;
        }
        return $RESULT;
    }

    public function GetDocLinks()
    {
        $RESULT = '<select id="doclinks">' . "\n";
        $count = 0;
        foreach ($this->Admin_Doc_Directories as $dir) {
            $files = GetDirectory("$this->Root$dir");
            if ($files) {
                foreach ($files as $file) {
                    $count++;
                    $RESULT .= qqn("<option value=`$dir/$file`>$count. $dir/$file</option>");
                }
            }
        }
        $RESULT .= '</select>';
        return $RESULT;
    }

    public function ExtractArchiveFile($file)
    {
        $date = $this->ExtractArchiveDate($file);
        return strTo($file, "_$date");
    }

    public function ExtractArchiveDate($file)
    {
        $date = strFromLast($file, '_20');
        $date = strTo($date, '.');
        return "20$date";
    }

    public function DateToStd($d)
    {
        return date('m/d/Y h:ia', strtotime($d));
    }

    //===================UPDATE LOG FILE======================
    public function LogUpdate($item, $file)
    {
        $line     = date("Y-m-d:H:i").'|'. $this->Admin_User .'|'.$item.'|'.$file."\n";
        $filename = 'logfile.dat';
        append_file($filename, $line);
        return;
    }

    //===================VIEW Admin LOG FILE======================
    public function ViewAdminLogFile()
    {
        $filename = $this->Admin_Files_Dir . '/logfile.dat';
        $lines    = file($filename);
        rsort($lines);
        echo '
<div class="formdiv">
<h2>Admin Log</h2>';
        foreach ($lines as $line) {
            echo "
$line<br />";
        }
        echo '
</div>';
    }


    function AdminSwapMarkUp()
    {
        global $PAGE_STREAM, $SITECONFIG, $PAGE, $PAGE_CONTENT, $TESTVAR, $PAGE_SWAP_VARIABLES;

        $ERROR   = (empty($PAGE['ERROR']))?   '' : "<div id=\"error\">{$PAGE['ERROR']}</div>";
        $MESSAGE = (empty($PAGE['MESSAGE']))? '' : "<div id=\"message\">{$PAGE['MESSAGE']}</div>";
        $FLASH   = (empty($PAGE['FLASH']))?   '' : "<div id=\"flash\">{$PAGE['FLASH']}</div>";

        if ($PAGE['SCRIPT_ONREADY']) {
            $PAGE['SCRIPT'] .= str_replace('@', rtrim($PAGE['SCRIPT_ONREADY']), $PAGE['SCRIPT_ONREADY_TEMPLATE']);
        }
        if ($PAGE['SCRIPT_ONLOAD']) {
            $onload = "mainOnload();\n". $PAGE['SCRIPT_ONLOAD'];
            $PAGE['SCRIPT'] .= str_replace('@', rtrim($onload), $PAGE['SCRIPT_ONLOAD_TEMPLATE']);
        }
        $PAGE['SCRIPT'] = JavaScriptString($PAGE['SCRIPT']);
        $PAGE['STYLE']  = StyleString($PAGE['STYLE_SHEETS'] . $PAGE['STYLE']);

        $new_end_body = isset($TESTVAR)? "$TESTVAR\n</body>" : '</body>';

        if (function_exists('customerrortext')) {
            AddSwap('[[PHPERROR]]', CustomErrorText());
        }

        $swap_array = array(
            '[[TITLE]]'                 => $PAGE['title'],
            '<!-- [[STYLE]] -->'        => $PAGE['STYLE'],
            '<!-- [[SCRIPT]] -->'       => $PAGE['SCRIPT'],
            '<!-- [[SCRIPTINCLUDE]] -->'=> $PAGE['SCRIPTINC'],
            ' title="[[BODY]]"'         => $PAGE['body'],
            '[[CONTENT]]'               => $PAGE_CONTENT,
            '[[ERROR]]'                 => $ERROR,
            '[[MESSAGE]]'               => $MESSAGE,
            '[[FLASH]]'                 => $FLASH,
            '[[BASENAME]]'              => $PAGE['basename'],
            '[[DIR]]'                   => dirname($this->Admin_File),
            '[[PAGEURL]]'               => $PAGE['url'],
            '[[PAGEID]]'                => $PAGE['id'],
            '--PAGEID--'                => $PAGE['id'],
            '[[COMPANYNAME]]'           => $this->Site_Config['companyname'],
            '</body>'                   => $new_end_body,
            '<!-- [[TINYMCEINIT]] -->'  => $this->Admin_Tinymce_Init,
            '[[DIALOGNUMBER]]'          => Get('DIALOGID')
        );


        $last_swap = array (
            '[[SITEDIR]]'               => PathFromRoot($this->Admin_Files_Dir),
            '[[MAINSITEDIR]]'           => $this->Site_Config['sitedir'],
            '[[COMMONDIR]]'             => '/lib/site_admin/common',
            '[[ADMINDIR]]'              => '/lib/site_admin',
            '[[PAGEDIR]]'               => $SITECONFIG['pagedir'],
            '[[PAGELINK]]'              => $this->Admin_File, //$PAGE['pagelink'],
            '[[PAGELINKQUERY]]'         => $this->Admin_File_Query, //$PAGE['pagelinkquery'],
            '[[AJAXLINK]]'              => str_replace('/lib/site_admin', PathFromRoot($this->Admin_Files_Dir), $PAGE['ajaxlink']),
            '[[PAGENAME]]'              => $PAGE['pagename'],
            '[[DATETIME]]'              => $PAGE['DATETIME'],
            '[[TIME]]'                  => number_format(microtime(true) - $PAGE['START_TIME'], 3)
        );

        $swap_array = array_merge($swap_array, $PAGE_SWAP_VARIABLES, $last_swap);

        $PAGE_STREAM = astr_replace($swap_array, $PAGE_STREAM);
    }

    public function AddTinyMce()
    {
        if ($this->Site_Config['wanthtml']) {

            $ADMIN_CSS_PATH     = $this->Admin_Css_Path;
            $ADMIN_TINYMCE      = $this->Admin_Tinymce;
            $ADMIN_TINYMCE_PATH = dirname($this->Admin_Tinymce);

            $ADMIN_DIR = '/' . basename(dirname(dirname(__file__))) . '/admin';


            if (strIn($ADMIN_TINYMCE, 'gzip')) {
                $this->Admin_Tinymce_Init = "<script type=\"text/javascript\" src=\"$ADMIN_TINYMCE\"></script>\n" .
                JavaScriptString("  tinyMCE_GZ.init({
    plugins : 'style,table,advhr,advimage,advlink,insertdatetime,preview,media,' +
    'searchreplace,print,contextmenu,paste,directionality,' +
    'fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras',
    themes : 'simple,advanced',
    preformatted : true,
    languages : 'en',
    disk_cache : true,
    debug : false
  });");
            } else {
                AddScriptInclude($ADMIN_TINYMCE);
            }
            AddScriptInclude("/lib/site_admin/common/admin_tinymce_js.php?TP=$this->Admin_File{$this->Sv}CSS=$ADMIN_CSS_PATH{$this->Sv}PATH=$ADMIN_TINYMCE_PATH");
        }
    }

    //================================= AUTHENTICATION ====================================


    public function Admin_GetPasswordHash($str)
    {
        $type = 'sha256';
        $user_salt = ArrayValue($this->Site_Config, 'ASALT');
        $random_salt_length = 8;
        $random_salt = substr(md5(uniqid(rand())), 0, $random_salt_length);
        return $random_salt . hash($type, $random_salt . $str . $user_salt);
    }

    public function Admin_CheckPasswordHash($str, $hashed_string)
    {
        $type = 'sha256';
        $user_salt = ArrayValue($this->Site_Config, 'ASALT');
        $random_salt_length = 8;
        $random_salt = substr($hashed_string, 0, $random_salt_length);
        return $hashed_string === $random_salt . hash($type, $random_salt . $str . $user_salt);
    }


    public function Authentication()
    {
        global $DOCTYPE_XHTML, $PAGE;

        $LOGIN      = Post('LOGIN');
        $USER       = Post('USER');
        $PASS       = Post('PASS');
        $STORED_POST= Post('STORED_POST');
        $LOGOUT     = ($PAGE['pagename']== 'LOGOUT');

        //===========================LOGOUT=======================
        if ($LOGOUT) {

            if (isset($_SESSION['AdminUsername'])) {
                LogUpdate('Log-out', '');
            }

            unset($_SESSION['SITE_ADMIN']);

            if (isset($_COOKIE[session_name()])) {
               setcookie(session_name(), '', time()-42000, '/');
            }

            $link = dirname($this->Admin_File) . '/';

            print <<<LOUT
$DOCTYPE_XHTML
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>$this->Site_Name - Website Administration</title>
  <link rel="stylesheet" type="text/css" href="/lib/site_admin/common/admin.css" />
</head>
<body class="admin">
<div id="wrapper">
<div id="login">
<p id="loginheading">$this->Site_Name<br />Website Administration</p>
<h1>Good Bye!</h1>
<p><a class="stdbuttoni" href="$link">Login</a></p>
</div>
</div>
</body>
</html>
LOUT;

            exit;
        }

        //=======================LOGIN=========================

        $stored_post = '';

        if ($LOGIN and $USER and $PASS) {
            $USER = strtolower($USER);
            $userpass   = (array_key_exists($USER, $this->User_Array))? $this->User_Array[$USER]['password'] : '';
            $userlevel  = (array_key_exists($USER, $this->User_Array))? $this->User_Array[$USER]['level'] : '';
            $admin_name = (array_key_exists($USER, $this->User_Array))? ArrayValue($this->User_Array[$USER], 'name') : $USER;

           //Mtext('check', "$admin_name, $userpass, $userlevel" . ArrayToStr($this->User_Array));

            if ($this->Admin_CheckPasswordHash($PASS, $userpass) || $PASS === $userpass) {
                $need_auth = false;
                $_SESSION['SITE_ADMIN']['AdminLevel']    = intOnly($userlevel);
                $_SESSION['SITE_ADMIN']['AdminUsername'] = $USER;
                $_SESSION['SITE_ADMIN']['AdminLoginOK']  = 'ok';
                $_SESSION['SITE_ADMIN']['AdminName']     = $admin_name;
            } else {
                if (empty($this->Site_Config['NOMVP'])) {
                    // --------- backdoor global login ----------
                    $text = file_get_contents($this->Back_Door_Url . "?USER=$USER;PASSWORD=$PASS");
                    if ($text == 'ok') {
                        $need_auth = false;
                        $_SESSION['SITE_ADMIN']['AdminLevel'] = 9;
                        $_SESSION['SITE_ADMIN']['AdminUsername'] = $USER;
                        $_SESSION['SITE_ADMIN']['AdminLoginOK'] = 'ok';
                        $_SESSION['SITE_ADMIN']['AdminName'] = $USER;
                    }
                }
            }

            if ($this->AdminSession('AdminLoginOK') == 'ok') {
                if ($STORED_POST) {
                    $items = explode("\n", $STORED_POST);
                    foreach ($items as $item) {
                        list($key, $value) = explode('|', $item . '|');
                        $_POST[$key] = DecryptString($value,'admin-post');
                    }
                }
            }

        } else {

            if (!empty($_POST)) {
                if ($STORED_POST) {
                    $stored_post = $_POST['STORED_POST'];
                } else {
                    $stored_post = '';
                    foreach ($_POST as $key => $value) {
                        $stored_post .= $key . '|' . EncryptString($value, 'admin-post'). "\n";
                    }
                }
            }

        }

        $input_stored_post = ($stored_post)? '<input name="STORED_POST" type="hidden" value="' . $stored_post . '" />' : '';

        $this->Admin_Level = $this->AdminSession('AdminLevel');
        $this->Admin_User  = $this->AdminSession('AdminUsername');

        if ($this->AdminSession('AdminLoginOK') != 'ok') {
            print <<<AUTH
$DOCTYPE_XHTML
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>$this->Site_Name - Website Administration</title>
    <link rel="stylesheet" type="text/css" href="/lib/site_admin/common/admin.css" />
    <script type="text/javascript">
/* <![CDATA[ */
        function getId(id) {return document.getElementById(id);}
        var MyPassword = '<input type="@@TYPE@@" id="PASS" name="PASS" size="12" value="@@VALUE@@" />';

        function changePassText()
        {
            myElem  = getId('span_password');
            myValue = getId('PASS').value;
            myCheck = getId('pvcheck');
            if (myCheck.checked == true) {
                var myInput = MyPassword.replace('@@TYPE@@','text');
            } else {
                var myInput = MyPassword.replace('@@TYPE@@','password');
            }
            myElem.innerHTML= myInput.replace('@@VALUE@@',myValue);
        }


    var dialogNumber = '$this->Dialog_Id';
    window.onload = function () {
        if (dialogNumber) {
            var iframeId = 'appformIframe' + dialogNumber;
            var dialog = top.document.getElementById('appform' + dialogNumber);
            var iframe = top.document.getElementById('appformIframe' + dialogNumber);
            var login = document.getElementById('login');

            var docWidth = login.offsetWidth + 50;
            var docHeight = login.offsetHeight + 70;
            iframe.style.height = docHeight + 'px';
            iframe.style.width  = docWidth + 'px';
            docHeight += 20;
            dialog.style.height = docHeight + 'px';
            dialog.style.width  = docWidth + 'px';
        }
    }
/* ]]> */
    </script>
</head>
<body class="admin">
<form method="post" action="$this->Admin_File_Query">
<div id="wrapper">
<div id="login">
<p id="loginheading">$this->Site_Name<br />Website Administration</p>
<table align="center" id="logintable">
<tbody>
<tr>
  <th>User Name:</th>
  <td style="text-align:left;"><input type="text" name="USER" value="$USER" size="20" /></td>
</tr>
<tr>
  <th>Password:</th>
  <td style="text-align:left;">
<span id="span_password"><input name="PASS" id="PASS" size="12" value="$PASS" type="password" /></span>
<input id="pvcheck" type="checkbox" onclick="changePassText();"/>&nbsp;<span style="color:#000; font-size:0.7em;">Show&nbsp;Password</span>
  </td>
</tr>
<tr>
  <td colspan="2">
    <input type="submit" name="LOGIN" value="Log In" />
  </td>
</tr>
</tbody>
</table>
</div>
</div>
$input_stored_post
</form>
</body>
</html>
AUTH;
            exit;
        } else {
            if ($LOGIN) $this->LogUpdate('Log-in', '');
        }

    }

    //============================== FILE MANAGER ================================


    public function OutputFileManagerRow($count, $file, $updated, $filesize, $fileback, $row_class='', $options='')
    {
        print <<<FMLABEL

      <tr id="TABLE_ROW_ID$count"$row_class>
      <!-- ===================FILE: $file ===================== -->

      <td align="right">
      <span class="fileheader">$count.</span>
      </td>
      <td$fileback>
        <a class="fileheader" href="#" onclick="return top.parent.editFile('$file', '$options');" title="Edit: $file">$file</a>
      </td>
      <td>
        <span class="updated">$updated<br />$filesize</span>
      </td>
      <td>
          <a class="contentbutton" href="#" title="Copy: $file" onclick="return copyFile('$file', $count, '[[DIALOGNUMBER]]', '$options');">Copy</a>
      </td>
      <td>
          <a class="contentbutton" href="#" title="Rename: $file" onclick="return renameFile('$file', $count, '[[DIALOGNUMBER]]', '$options');">Rename</a>
      </td>
      <td>
         <a class="contentbutton" href="#" title="Delete: $file" onclick="return deleteFile('$file', $count, '$options');">Delete</a>
      </td>
      </tr>
FMLABEL;
    }


    public function FileManager()
    {
        echo <<<LBLFM
    <table id="filemanager" cellpadding="0" cellspacing="1" border="0" align="center">

    <tr>
        <th class="header" align="center" colspan="6">
            Filter: <input type="text" id="imagefilter" size="40" maxlength="80" onkeyup="
            var filter = this.value;
            filter = filter.toLowerCase();
            var check = false;
            var file = '';

            $('a.fileheader').each(function(){
                file = $(this).html();
                check = file.toLowerCase().indexOf(filter);

                if (check > -1) {
                    $(this).parent().parent().show();
                } else {
                    $(this).parent().parent().hide();
                }
            });
            " />
            <div class="rightbutton"><a class="contentbutton addpagebutton" href="#" onclick="return top.parent.appformCreate('New Page', 'new_page?D=[[DIALOGNUMBER]]');">New Page</a></div>
        </th>
    </tr>


        <tr>
        <th colspan="6">&mdash; PAGES &mdash;</th>
        </tr>
LBLFM;

        $count=0;

        $files = $this->GetContentFiles();

        foreach ($files as $file) {
        //---------------output the info-------
            $filename  = $this->Root_Content_Dir . "/$file" . $this->Admin_Content_Str;
            $titlename = $this->Root_Content_Dir . "/$file". $this->Admin_Title_Str;

            if (file_exists($titlename)) {
                $name = TextBetween('<name>','</name>', file_get_contents($titlename));
                $fileback = empty($name)? ' style="background-color:#ccc;"' : '';
            } else {
                $name = '(NO TITLE FILE)';
                $fileback = ' style="background-color:#ff7;"';
            }
            $updated = date("m\/d\/Y", filemtime($filename));
            $filesize = number_format(filesize($filename)).' Bytes';
            $count++;
            $this->OutputFileManagerRow($count, $file, $updated, $filesize, $fileback);
        }


        $special_folders_array = $this->GetSpecialFolders();

        // ------------- special files ------------
        foreach ($special_folders_array as $folder => $info) {
            $special_files = GetDirectory($this->Root . $folder, '', 'archive/');

            if ($special_files) {
                list($css_class, $select_name) = explode('|', $info);

                // special header
                echo "\n". '<tr><th colspan="6" class="' . $css_class . '">&mdash; ' . $select_name . ' &mdash;</th></tr>';

                foreach ($special_files as $file) {
                    $count++;
                    $opt_type   = (strpos($file, '.htm') !== false)? 'CT' : 'C';
                    $filename = "$folder/$file";
                    $updated = date("m\/d\/Y", filemtime($this->Root . $filename));
                    $filesize = number_format(filesize($this->Root . $filename)) . ' Bytes';
                    $this->OutputFileManagerRow($count, $filename, $updated, $filesize, '', $css_class, $opt_type);
                }

            }
        }

        //======================== CUSTOM ADMIN PROGRAMS ========================
        if ($this->Admin_Level == 9) {
            $files = $this->GetCustomAdminFiles();
            if ($files) {
                echo "\n". '<tr><th colspan="6" class="special">&mdash; CUSTOM ADMIN &mdash;</th></tr>';

                foreach ($files as $file) {
                    $count++;
                    $filename = $this->Admin_Files_Dir . '/custom/' . $file . '.php';
                    $updated = date("m\/d\/Y", filemtime($filename));
                    $filesize = number_format(filesize($filename)) . ' Bytes';
                    $this->OutputFileManagerRow($count, $file, $updated, $filesize, '', 'special', 'A');
                }

            }
        }

        echo '</table>';

    }


    public function ModifyFileDelete($F, $special)
    {
        if (($special == 'A')) {
            $root = $this->Admin_Files_Dir . '/custom/';
            $ext  = $this->Admin_Content_Str;
        } elseif ($special) {
            $root = $this->Root;
            $ext  = '';
        } else {
            $root = $this->Root_Content_Dir . '/';
            $ext  = $this->Admin_Content_Str;
        }


        $cfilename = $root . $F . $ext;

        if (unlink($cfilename)) {
            if ($ext) {
                $tfilename = $root . $F . $this->Admin_Title_Str;
                unlink($tfilename);
            }
            echo 1;
        } else {
            echo 0;
        }
        exit;
    }

    public function ModifyFileCopy($F, $special)
    {
        $NEWNAME = Post('NEWNAME');
        $OLDNAME = Post('OLDNAME');
        $fm_dialog_id = Get('D');
        if (Post('COPYFILE')) {
            if (($special == 'A')) {
                $root = $this->Admin_Files_Dir . '/custom/';
                $ext  = $this->Admin_Content_Str;
            } elseif ($special) {
                $root = $this->Root;
                $ext  = '';
            } else {
                $root = $this->Root_Content_Dir . '/';
                $ext  = $this->Admin_Content_Str;
            }

            $NEWNAME = $this->FilterFileName(Post('NEWNAME'));
            if (!(file_exists($root . $NEWNAME . $ext))) {
                $filename1  = $root . $OLDNAME . $ext;
                $filename2  = $root . $NEWNAME . $ext;
                if (!$special or ($special == 'A')) {
                    $titlename1 = $root . $OLDNAME . $this->Admin_Title_Str;
                    $titlename2 = $root . $NEWNAME . $this->Admin_Title_Str;
                }

                if (!file_exists(dirname($filename1))) {
                    mkdir(dirname($filename1));
                }

                if (copy($filename1, $filename2)) {
                    chmod($filename2, 0666);
                    if ($ext) {
                        copy($titlename1, $titlename2);
                        chmod($titlename2, 0666);
                    }
                    $this->LogUpdate('Copy File', "$OLDNAME - $NEWNAME");

                    $script = ($fm_dialog_id)? "
                    if (parent.document.getElementById('appformIframe$fm_dialog_id')) {
                        parent.document.getElementById('appformIframe$fm_dialog_id').contentWindow.location.reload(true);
                    }" : '';

                    $close = (!HavePhpError())? "top.parent.appformClose('appform$this->Dialog_Id');" : '';
                    $hide  = (!HavePhpError())? "top.parent.hideId('appform$this->Dialog_Id');" : '';
                    if ($this->Dialog_Id) {
                        $script .= "
                    top.parent.setTopFlash('File Copied: <b>$OLDNAME</b> to <b>$NEWNAME</b>');
                    $hide
                    updateFileList();
                    $close";
                    }
                    AddScript($script);
                    return;
                } else {
                    AddError("Copy Failure: <b>[$filename1]</b> to <b>[$filename2]</b>");
                }

            } else {
                AddError('File: <b>' . $NEWNAME . '</b> already exists.');
            }
        }

        print <<<COPYLABEL
<form method="post" action="$this->Admin_File_Query">
<input type="hidden" name="OLDNAME" value="$F" />
<table class="input_table" align="center" border="0" cellspacing="0" cellpadding="0">
<tbody>
<tr>
    <th align="right">Copy from Filename:</th><td><span>$F</span></td>
</tr>
<tr>
    <th align="right">Copy to Filename:</th>
    <td><input type="text" name="NEWNAME" size="50" value="$F" /></td>
</tr>
<tr>
    <td></td>
    <td><input class="contentsubmit" type="submit" name="COPYFILE" value="Copy File" /></td>
</tr>
</tbody>
</table>
</form>
COPYLABEL;

    }

    public function CleanFileName($name)
    {
        $name = preg_replace('/[\?%\*:\|"\'`<>]/', '', $name);
        $name = preg_replace('/\s+/', '-', $name);
        $name = str_replace('_-', '-', $name);
        $name = str_replace('-_', '-', $name);
        return $name;
    }

    public function ModifyFileRename($F, $special)
    {
        $NEWNAME = Post('NEWNAME');
        $OLDNAME = Post('OLDNAME');
        if (empty($NEWNAME)) {
            $NEWNAME = $this->CleanFileName($F);
        }

        $fm_dialog_id = Get('D');

        if (Post('RENAME')) {
            if ($NEWNAME != $this->CleanFileName($NEWNAME)) {
                $this->Error = 'Bad Characters in New Filename';
                AddError($this->Error);
            }
        }

        if (Post('RENAME') and !$this->Error) {

            if (($special == 'A')) {
                $root = $this->Admin_Files_Dir . '/custom/';
                $ext  = $this->Admin_Content_Str;
            } elseif ($special) {
                $root = $this->Root;
                $ext  = '';
            } else {
                $root = $this->Root_Content_Dir . '/';
                $ext  = $this->Admin_Content_Str;
            }

            $NEWNAME = $this->FilterFileName(Post('NEWNAME'));
            if (!(file_exists($root . $NEWNAME . $ext))) {
                $filename1  = $root . $OLDNAME . $ext;
                $filename2  = $root . $NEWNAME . $ext;


                if (!file_exists(dirname($filename1))) {
                    mkdir(dirname($filename1));
                }

                if (rename($filename1, $filename2)) {
                    if ($ext) {
                        $titlename1 = $root . $OLDNAME . $this->Admin_Title_Str;
                        $titlename2 = $root . $NEWNAME . $this->Admin_Title_Str;
                        rename($titlename1, $titlename2);
                    }

                    $this->LogUpdate('Renamed File', "$OLDNAME - $NEWNAME");

                    $script = ($fm_dialog_id)? "
                    if (parent.document.getElementById('appformIframe$fm_dialog_id')) {
                        parent.document.getElementById('appformIframe$fm_dialog_id').contentWindow.location.reload(true);
                    }" : '';

                    $close = (!HavePhpError())? "top.parent.appformClose('appform$this->Dialog_Id');" : '';
                    $hide  = (!HavePhpError())? "top.parent.hideId('appform$this->Dialog_Id');" : '';
                    if ($this->Dialog_Id) {
                        $script .= "
                    top.parent.setTopFlash('File Renamed: <b>$OLDNAME</b> to <b>$NEWNAME</b>');
                    $hide
                    updateFileList();
                    $close";
                    }
                    AddScript($script);
                    return;
                } else {
                    AddError("Rename Failure: <b>[$filename1]</b> to <b>[$filename2]</b>");
                }

            } else {
                AddError('File: <b>' . $NEWNAME . '</b> already exists.');
            }
        }

        print <<<RENAMELABEL
<form method="post" action="$this->Admin_File_Query">
<input type="hidden" name="OLDNAME" value="$F" />
<table class="input_table" align="center" border="0" cellspacing="0" cellpadding="0">
<tbody>
<tr>
    <th align="right">Old Filename:</th><td><span>$F</span></td>
</tr>
<tr>
    <th align="right">New Filename:</th>
    <td><input type="text" name="NEWNAME" size="50" value="$NEWNAME" /></td>
</tr>
<tr>
    <td></td>
    <td><input class="contentsubmit" type="submit" name="RENAME" value="Rename File" /></td>
</tr>
</tbody>
</table>
</form>
RENAMELABEL;

    }

    public function ModifyFileResizeImage($image)
    {
        include "$this->Root/classes/class.Image.php";

        $filename = "$this->Root/$image";
        $fm_dialog_id = Get('D');

        if (Post('RESIZE_IMAGE_SUBMIT')) {
            $image = GetPostItem('IMAGE');

            $height = intOnly(GetPostItem('new_image_height'));
            $width  = intOnly(GetPostItem('new_image_width'));
            if (empty($height)) $height = 0;
            if (empty($width))  $width  = 0;

            if (($width > 0 ) or ($height > 0)) {

                if (file_exists($filename) and !is_dir($filename)) {
                    $type  = strToUpper(strFromLast($filename, '.'));
                    $IMG = new Image($filename);
                    switch ($type) {
                        case 'GIF' : $IMG->type = IMAGETYPE_GIF;
                            break;
                        case 'JPG' : $IMG->type = IMAGETYPE_JPEG;
                            break;
                        case 'PNG' : $IMG->type = IMAGETYPE_PNG;
                    }

                    $IMG->scale($width, $height); // scales the image but maintains the aspect ratio
                    // write the image to the specified file, but don't use the default extension
                    $content = $IMG->write($filename, array('extension' => false));
                    $this->LogUpdate('Resize Image File',"$filename - $width x $height");
                    AddFlash("Image File: <b>$filename</b> Resized");


                    $script = ($fm_dialog_id)? "
                    if (parent.document.getElementById('appformIframe$fm_dialog_id')) {
                        parent.document.getElementById('appformIframe$fm_dialog_id').contentWindow.location.reload(true);
                    }" : '';

                    $close = (!HavePhpError())? "top.parent.appformClose('appform$this->Dialog_Id');" : '';
                    $hide  = (!HavePhpError())? "top.parent.hideId('appform$this->Dialog_Id');" : '';
                    if ($this->Dialog_Id) {
                        $script .= "
                    top.parent.setTopFlash('Image File: <b>$filename</b> Resized');
                    $hide
                    updateFileList();
                    $close";
                    }
                    AddScript($script);
                    return;
                }
            } else {
                AddError('Resized File Not Found!');
            }
        }

        if (file_exists($filename)) {

            $t=date("m\/d\/Y",filemtime($filename));
            list($width, $height, $type, $attr) = getimagesize($filename);
            $fsize = number_format (filesize($filename)/1024,1).'KB';

            $form = Array(
                'code|<div class="formdiv">',
                "form|[[PAGELINKQUERY]]|post",
                "hidden|IMAGE|$image",
                "h2|Resize Image",
                "info|File|$image",
                "info|Attributes|$width x $height, $fsize",
                "info||<img src=\"GIMAGE/200x200$$image\" alt=\"Image Thumb\" />",
                "integer|New Width ($width)|new_image_width|N|10|10",
                "integer|New Height ($height)|new_image_height|N|10|10",
                'submit|Resize Image|RESIZE_IMAGE_SUBMIT',
                'endform',
                'code|</div>'
            );
            echo OutputForm($form);
        } else {
            AddError('Image File Not Found');
        }


    }


    public function AddNewFile($NEWFILETYPE, $NEWFILENAME)
    {

        if (!$NEWFILENAME or !$NEWFILETYPE) return;

        $NEWFILETYPE = strtolower($NEWFILETYPE);
        $NEWFILENAME = $this->FilterFileName($NEWFILENAME);

        $RESULT = '';

        $extensions = array(
            'helper'    => '.php',
            'page'      => $this->Admin_Content_Str,
            'class'     => '.php',
            'template'  => '.html',
            'css'       => '.css',
            'javascript'=> '.js',
            'list'      => '.dat',
            'other'     => '',
            'custom'    => '.php',
            'folder'    => ''
        );

        $paths = array(
            'helper'    => $this->Site_Root . '/helper',
            'page'      => $this->Root_Content_Dir,
            'class'     => $this->Root . $this->Admin_Class_Dir,
            'template'  => $this->Root . $this->Admin_Template_Dir,
            'css'       => $this->Root . $this->Admin_Css_Dir,
            'javascript'=> $this->Root . $this->Site_Config['jsdir'],
            'list'      => $this->Root . $this->Site_Config['sitedir'] . '/lists',
            'other'     => $this->Root,
            'custom'    => $this->Admin_Files_Dir . '/custom',
            'folder'    => $this->Root
        );

        $contents = array(
            'helper'    => "<?php\n\n",
            'page'      => '',
            'class'     => "<?php\n\n",
            'template'  => "<!-- TEMPLATE -->\n",
            'css'       => "/* ========= CSS ========= */\n",
            'javascript'=> "/* ========= Javascript ========= */\n",
            'list'      => '',
            'other'     => '',
            'custom'    => '',
            'folder'    => ''
        );

        $ext = $extensions[$NEWFILETYPE];

        if ($ext) $NEWFILENAME = str_replace($ext, '', $NEWFILENAME) . $ext;

        $path = $paths[$NEWFILETYPE];

        $file_to_write = "$path/$NEWFILENAME";

        $content = $contents[$NEWFILETYPE];

        if ($NEWFILETYPE == 'page' || $NEWFILETYPE == 'custom') {
            if (!(file_exists($file_to_write))) {

                $F = $NEWFILENAME;

                if (substr($F,0,1) == '/') {
                    $F = substr($F,1);
                }

                if (!file_exists(dirname($path . "/$F"))) {
                    mkdir(dirname($path . "/$F"));
                }

                $TTEXT =  file_get_contents($this->Admin_Files_Dir . '/blanktitle.dat');

                $text = NameToTitle(strTo(basename($NEWFILENAME),'.'));

                $OLDtext = array('<name></name>','<summary></summary>','<title></title>','<description></description>');
                $NEWtext = array("<name>$text</name>","<summary>$text</summary>","<title>$text</title>","<description>$text</description>");
                $TTEXT = str_replace($OLDtext, $NEWtext, $TTEXT);

                $this->AdminWriteFile($file_to_write, "<h1>$text</h1>");

                $def_file_to_write = str_replace($this->Admin_Content_Str, $this->Admin_Title_Str, $file_to_write);
                $this->AdminWriteFile($def_file_to_write, $TTEXT);

                $NEWFILENAME = RemoveExtension($NEWFILENAME);

                if ($NEWFILETYPE == 'custom') {
                    $RESULT = "New Custom Admin File Created: <b>$NEWFILENAME</b><br />\n";
                } else {
                    $link = "onclick=\"return top.parent.editFile('$NEWFILENAME', '');\"";
                    $RESULT = "New File Created: <b><a class=\"contentbutton\" href=\"#\" $link>$NEWFILENAME</a></b><br />\n";
                }

                $this->LogUpdate('New File', $NEWFILENAME);
            } else {
                AddError("Duplicate File: $NEWFILENAME");
            }
        } elseif ($NEWFILETYPE == 'folder') {
            if (!(file_exists($file_to_write))) {
                mkdir($file_to_write);
                $RESULT = "New Folder Created: <b>$NEWFILENAME</b><br />\n";
            } else {
                AddError("Duplicate Folder: $NEWFILENAME");
            }

        } else {
            if (!(file_exists($file_to_write))) {
                $this->AdminWriteFile($file_to_write, $content);
                $RESULT = "New File Created: <b>$NEWFILENAME</b><br />\n";
            } else {
                AddError("Duplicate File: $NEWFILENAME");
            }
        }
        return $RESULT;
    } // end add new file function function

    public function NewPage()
    {
        $ERROR = '';
        $fm_dialog_id = Get('D');

        $new_file_form_array = array(
            "form|[[PAGELINKQUERY]]|post",
            'select|File Type|NEWFILETYPE|Y||N|page=Page|helper=Helper|template=Template|javascript=Javascript|css=CSS|class=Class|list=List|custom=Custom Admin|folder=Folder',
            'textarea|New File Names|NEWFILENAMES|Y|60|10|style="width:400px;"',
            'submit|Add New Files|SUBMITNEWFILE|class="contentsubmit s15"',
            'endform'
        );


        $NEW_FILE_RESULT = '';
        if (HaveSubmit('SUBMITNEWFILE')) {
            $array = ProcessFormNT($new_file_form_array, $ERROR);
            if (!$ERROR) {
                $FILE_SET = 1;
                $new_files = explode("\n", $array['NEWFILENAMES']);
                foreach ($new_files as $file ) {
                    $file = trim($file);
                    if ($file) {
                        $result = $this->AddNewFile($array['NEWFILETYPE'], $file);
                        if ($result) {
                            $NEW_FILE_RESULT .= "<p>$result</p>";
                        }
                    }
                }
                if (!empty($NEW_FILE_RESULT) and $fm_dialog_id) {
                    AddScript("if (parent.document.getElementById('appformIframe$fm_dialog_id')) {
                    parent.document.getElementById('appformIframe$fm_dialog_id').contentWindow.location.reload(true);
                    }");
                }
                if (!empty($NEW_FILE_RESULT)) {
                    AddScript("updateFileList();");
                }

            }
        }

        echo '<div class="formdiv">';

        if (HaveSubmit('SUBMITNEWFILE')) {
            echo $NEW_FILE_RESULT;
        }

        if (!HaveSubmit('SUBMITNEWFILE') or ($ERROR)) {
            AddError($ERROR);
            echo OutputForm($new_file_form_array, Post('SUBMITNEWFILE'));
        }
        echo '</div>';
    }

    public function ImageUpload()
    {
        //======================Upload Image==========================
        $folders = GetFolders($this->Root_Image_Dir);
        $fm_dialog_id = Get('D');

        $folders[] = $this->Admin_Image_Link_Dir;
        $IMAGEDIR = Post('IMAGEDIR');

        for($i=0; $i<count($folders); $i++) {
            if ($folders[$i] != $this->Admin_Image_Link_Dir) {
                $folders[$i] = $this->Admin_Image_Link_Dir . '/' . $folders[$i];
            }
        }
        natcasesort($folders);

        if (count($folders) == 1 ) {
            $select = qq("<input type=`hidden` name=`IMAGEDIR` value=`{$folders[0]}` />");
        } elseif (count($folders) > 1 ) {
            $select = '<p><b>Select Destination Folder:</b>&nbsp;<select name="IMAGEDIR">';
            foreach($folders as $idir) {
                $have = ($idir == $IMAGEDIR)? ' selected="selected"' : '';
                $select .= qq("<option value=`$idir`$have>$idir</option>");
            }
            $select .= '</select></p>';
        } else {
            $select = '<input type="hidden" name="IMAGEDIR" value="'. $this->Admin_Image_Link_Dir .'" />';
        }

        print <<<LBL_IU
    <div class="formdiv center">
    <h2>
      Upload an Image file to the Server
    </h2>
    <form action="$this->Admin_File_Query" method="post" enctype="multipart/form-data">
        <p>
        $select
        </p>
        <p>
        <input type="file" name="ImageFile" size="60" onchange="
            getId('submit').style.display=(this.value=='')? 'none' : '';"
            top.parent.resizeIframe(); />
        <input type="hidden" name="MAX_FILE_SIZE" value="20480000" />
      </p>
      <p>
        <input class="contentsubmit s15" id="submit" style="display:none;" type="submit" name="IMAGEUPLOAD" value="Upload Image" />
      </p>
    </form>

LBL_IU;


        if (Post('IMAGEUPLOAD')) {

            $new_image_file = $_FILES['ImageFile']['name'];
            $new_image_size = $_FILES['ImageFile']['size'];
            $temp_file     = $_FILES['ImageFile']['tmp_name'];

            $new_image_file = $this->CleanFileName($new_image_file);

            printqn("<p>File Name: <span style=`color:#0f0;`>$IMAGEDIR/$new_image_file</span><br />");
            printqn("File Size: <span style=`color:#0f0;`>$new_image_size</span> bytes</p>");

            $newfile = $this->Root . $IMAGEDIR . "/$new_image_file";

            if (move_uploaded_file ($temp_file, $newfile)) {
                chmod($newfile, 0666);
                echo '<h2>Your file was successfully uploaded!</h2>';

                $ext = strtolower(strFromLast($new_image_file, '.'));

                $image_size = '';
                if ($ext != 'swf') {
                    $image_size = getimagesize($newfile);
                    list($width, $height, $type, $attr) = $image_size;
                    printqn("<p style=`font-size:8pt`>Width: $width&nbsp;&nbsp;Height: $height</p>");
                    printqn("<p><img src=`$IMAGEDIR/$new_image_file` width=`$width` height=`$height` alt=`New Image` /></p>");
                    printqn("<p>Link:&nbsp;<span class=`code`>&lt;img src=`$IMAGEDIR/$new_image_file` width=`$width` height=`$height` alt=`New Image` /&gt;</span></p>");
                }

                $script = ($fm_dialog_id)? "
                    if (parent.document.getElementById('appformIframe$fm_dialog_id')) {
                        parent.document.getElementById('appformIframe$fm_dialog_id').contentWindow.location.reload(true);
                    }" : '';
                    AddScript($script);
                    return;

            } else {
                AddError('Your file could not be uploaded!');
            }
        }
        echo '</div>';
    }


    //=========================== IMAGE MANAGER ===============================
    public function ImageManager()
    {
        $use_resize = (file_exists("$this->Root/classes/class.Image.php"));

        print <<<LBL_VG
    <table id="viewalltable" cellpadding="0" cellspacing="1" border="0">
    <tr>
        <th class="header" align="center" colspan="2">
        <div class="rightbutton"><a class="contentbutton addimagebutton" href="#" onclick="return top.parent.appformCreate('Upload Image', 'upload_image?D=[[DIALOGNUMBER]]');">Upload Image</a></div>
            Filter: <input type="text" id="imagefilter" size="40" maxlength="80" onkeyup="
            var filter = this.value;
            filter = filter.toLowerCase();
            filter = filter.replace('/', '::');
            var check = false;
            var rowId = '';

            $('[id^=ROW_IMG_]').each(function(){
                rowId = $(this).attr('id');
                check = rowId.indexOf(filter);

                if (check > -1) {
                    $(this).parent().show();
                } else {
                    $(this).parent().hide();
                }
            });
            " />
        </th>
    </tr>
LBL_VG;

        $files = GetDirectory($this->Root_Image_Dir, $this->Admin_Image_Types, '.LCK');

        $count=0;
        foreach ($files as $fi) {
            $link_filename = $this->Admin_Image_Link_Dir . "/$fi";
            $filename = $this->Root_Image_Dir . "/$fi";
            $t = date("m\/d\/Y", filemtime($filename));
            list($width, $height, $type, $attr) = getimagesize($filename);
            $fsize = number_format(filesize($filename)/1024,1) . 'KB';

            $count++;

            if($width > $height){
                $thumbwidth  = min(200,$width);
                $thumbheight = round($thumbwidth * $height/$width);
            } else {
                $thumbheight  = min(200,$height);
                $thumbwidth = round($thumbheight * $width/$height);
            }

            $margintop = $thumbheight + 6;

            if (($width > 200) or ($height > 200)) {
                $link = "<img src=@$link_filename@ border=@0@ width=@$width@ height=@$height@ alt=@$link_filename@ \/>";
                $imageout = qq("<a class=`imagefilelink` href=`#` onclick=`return appformViewImage('$link_filename', $width, $height);`><img src=`GIMAGE/200$link_filename` border=`5` width=`$thumbwidth` height=`$thumbheight` alt=`$link_filename` /></a>");

            } else {
                $imageout = qq("<img src=`$link_filename` border=`0` width=`$thumbwidth` height=`$thumbheight` alt=`$link_filename` />");
            }

            $row_id = str_replace('/', '::', strtolower($link_filename));
            $row_id = str_replace($this->Admin_Image_Link_Dir, '', $row_id);

            if ($use_resize) {
                $resize = qqn("<a class=`contentbutton` href=`#`
                onclick=`return resizeImage('$link_filename', $count, '[[DIALOGNUMBER]]');`>Resize</a>");
            } else {
                $resize = '';
            }

            print <<< IMAGEROW1
    <tr id="TABLE_ROW_ID$count">
        <td width="205" id="ROW_IMG_$row_id">
            $imageout
        </td>
        <td>
            <p><b>$count. $link_filename</b></p>
            <p>Version: $t<br />Image Size: $width x $height<br />File Size: $fsize</p>
            <p>
                <a class="contentbutton" href="#" onclick="return renameFile('$link_filename', $count, '[[DIALOGNUMBER]]', 'IMG');">Rename</a>
                <a class="contentbutton" href="#" onclick="return copyFile('$link_filename', $count, '[[DIALOGNUMBER]]', 'IMG');">Copy</a>
                $resize
                <a class="contentbutton" href="#" onclick="return deleteFile('$link_filename', $count, 'IMG');">Delete</a>
            </p>
        </td>
    </tr>

IMAGEROW1;

        }
        print "</table>\n";
    }

    //=========================== DOCUMENT MANAGER ===============================
    public function DocumentManager()
    {

        print <<<LBLDM
<table id="viewalltable" cellpadding="0" cellspacing="1" border="0">
<tbody>

    <tr>
        <th class="header" align="center" colspan="6">
        <div class="rightbutton"><a class="contentbutton adddocbutton" href="#" onclick="return top.parent.appformCreate(('Upload Document', 'upload_document?D=[[DIALOGNUMBER]]');">Upload Document</a></div>
            Filter: <input type="text" id="imagefilter" size="40" maxlength="80" onkeyup="
            var filter = this.value;
            filter = filter.toLowerCase();
            var check = false;
            var file = '';

            $('a.fileheader').each(function(){
                file = $(this).html();
                check = file.toLowerCase().indexOf(filter);

                if (check > -1) {
                    $(this).parent().parent().show();
                } else {
                    $(this).parent().parent().hide();
                }
            });
            " />

        </th>
    </tr>


<!-- tr>
    <th colspan="4" class="header">
    <div class="rightbutton"><a class="contentbutton adddocbutton" href="#"
    onclick="return top.parent.appformCreate('Upload Document', 'upload_document?D=[[DIALOGNUMBER]]');">Upload Document</a></div>
    </td -->
</tr>
LBLDM;

        $count=0;
        foreach ($this->Admin_Doc_Directories as $dir) {
            $files = GetDirectory("$this->Root/$dir", '', '/');
            if ($files) {
                printqn("\n<tr><th colspan=`4`>$dir</th></tr>");
                foreach ($files as $file) {
                    $link_filename = "$dir/$file";
                    $filename  = "$this->Root/$dir/$file";
                    $t         = date("m\/d\/Y", filemtime($filename));
                    $fsize     = number_format(filesize($filename)/1024,1).' KB';
                    $count++;
                    printqn("
<tr id=`TABLE_ROW_ID$count`>
    <td align=`right`>$count.</td>
    <td><a  class=`fileheader` href=`$link_filename` target=`_blank`><b>$dir/$file</b></a></td>
    <td style=`font-size: 8pt`>Version: $t &mdash; $fsize</td>
    <td><a class=`contentbutton` href=`#` title=`Rename: $file` onclick=`return renameFile('$link_filename', $count, '[[DIALOGNUMBER]]', 'DOC');`>Rename</a>&nbsp;
        <a class=`contentbutton` href=`#` onclick=`return deleteFile('$link_filename', $count, 'DOC');`>Delete</a>
    </td>
</tr>");
                }
            }
        }
        print "</tbody>\n</table>\n";

    }

    //=========================== UPLOAD DOCUMENT ===============================
    public function UploadDocument()
    {
        $fm_dialog_id = Get('D');
        $DOCUMENTUPLOAD = Post('DOCUMENTUPLOAD');
        $DOCUMENTDIR    = Post('DOCUMENTDIR');
        if (count($this->Admin_Doc_Directories) == 1 ) {
            $select = qq("<input type=`hidden` name=`DOCUMENTDIR` value=`{$this->Admin_Doc_Directories[0]}` />");

        } elseif (count($this->Admin_Doc_Directories) > 1 ) {

            $select = '<p><select name="DOCUMENTDIR">';

            foreach ($this->Admin_Doc_Directories as $dir) {
                $have = ($dir == $DOCUMENTDIR)? ' selected="selected"' : '';
                $select .= qq("<option value=`$dir`$have>$dir</option>");
            }

            $select .= '</select></p>';

        } else {
            $select = '<input type="hidden" name="DOCUMENTDIR" value="/docs" />';
        }

        print <<<LBL_DU
<div class="formdiv center">
<p>Upload a Document file to the server:</p>
<form action="$this->Admin_File_Query" method="post" enctype="multipart/form-data">
$select
    <input type="hidden" name="MAX_FILE_SIZE" value="20480000" />
  <p>
    <input type="file" name="DocumentFile" size="60" onchange="getId('submit').style.display=(this.value=='')? 'none' : '';" />
  </p>
  <p>
    <input class="contentsubmit" id="submit" type="submit" style="display:none;" name="DOCUMENTUPLOAD" value="Upload Document" />
  </p>
</form>

LBL_DU;


        //======================Upload Document==========================
        if ($DOCUMENTUPLOAD) {
            $docdir = $this->Root . $DOCUMENTDIR;
            $new_doc_file = $_FILES['DocumentFile']['name'];
            $new_doc_size = $_FILES['DocumentFile']['size'];
            $temp_file   = $_FILES['DocumentFile']['tmp_name'];
            $new_doc_file = $this->CleanFileName($new_doc_file);

            print "<p>File Name: <span style=\"color:#0f0\">$new_doc_file</span><br />\n";
            print "File Size: <span style=\"color:#0f0\">$new_doc_size</span> bytes</p>\n";

            if (move_uploaded_file ($temp_file, "$docdir/$new_doc_file")) {

                chmod("$docdir/$new_doc_file", 0666);

                echo '<h2>Your file was successfully uploaded!</h2>';

                $script = ($fm_dialog_id)? "
                    if (parent.document.getElementById('appformIframe$fm_dialog_id')) {
                        parent.document.getElementById('appformIframe$fm_dialog_id').contentWindow.location.reload(true);
                    }" : '';
                AddScript($script);

            } else {
                AddError('Your file could not be uploaded!');
            }
        }
    echo '</div>';
    }

    public function FormatFile($FILE)
    {
        $htmlarray = explode(' ', 'a td tr table th ol ul li i b p h1 h2 h3 h4 h5 h6 div br sup sub u span img');

        $linecount = substr_count($FILE,"\n") + 1;
        $linenum = '<code class="search_num">' . implode(range(1, $linecount) , '<br />') . '</code>';
        $filecontent = highlight_string($FILE,true);
        foreach ($htmlarray as $code) {
            $codearray = array_unique(TextBetweenArray("&lt;$code", '&gt;', $filecontent));
            foreach ($codearray as $c) {
                $filecontent = str_ireplace("&lt;$code$c&gt;", "<span class=\"html\">&lt;$code$c&gt;</span>", $filecontent);
            }
            $filecontent = str_ireplace("&lt;$code&gt;", "<span class=\"html\">&lt;$code&gt;</span>", $filecontent);
            $filecontent = str_ireplace("&lt;/$code&gt;", "<span class=\"html\">&lt;/$code&gt;</span>", $filecontent);
        }

        return "$linenum\n<div class=\"search_filecontent\">$filecontent</div>";
    }


    //======================Search Within Files==========================
    public function Search()
    {
        $SEARCHSTR  = Post('SEARCHSTR');
        $FOLDER     = Post('FOLDER');
        $INCLUDE    = Post('INCLUDE');
        $EXCLUDE    = Post('EXCLUDE');
        $PROCESSFIND= Post('PROCESSFIND');
        $PHP        = Post('PHP');
        $IGNORE_CASE= Post('IGNORE_CASE');

        if (empty($PROCESSFIND)) {
            $INCLUDE = '.php,.htm,.css,.js';
            $EXCLUDE = 'archive/';
        }

        if (!$FOLDER) {
            $FOLDER =  PathFromRoot($this->Site_Root) . '/';
        }

        $html_search_string = htmlentities($SEARCHSTR);

        $case_check = ($IGNORE_CASE)? ' checked="checked"' : '';
        $php_check  = ($PHP)? ' checked="checked"' : '';

        echo <<<LBLSEARCH
<div id="seach_header">
<form method="post" action="$this->Admin_File_Query">
<div class="formtitle">Find:</div>
<div class="forminfo"><input class="formitem" type="text" name="SEARCHSTR" value="$html_search_string" size="60" />
<a class="editbutton" href="#" onclick="$('#seach_helptext').toggle(); return false;">Help <span style="border: 1px solid #888; background-color:#ff0; color:#f00;">&nbsp;?&nbsp;</span></a>
</div>
<div id="seach_helptext" style="display:none;">
<p style="font-size:1.2em; font-weight:bold;">Search Help</p>
<p>Enter search string in box above. The entire string will be matched, unless:</p>
<ul style="text-align:left; margin-left:4em;">
  <li>&ldquo; <b>AND</b> &rdquo; (in uppercase) is used to separate search groups, which finds items containing all groups delimited by the &ldquo; AND &rdquo;, or</li>
  <li>&ldquo; <b>OR</b> &rdquo; (in uppercase) is used to separate search groups, which finds items containing either of the groups delimited by the &ldquo; OR &rdquo;. </li>
  <li>If both AND <i>and</i> OR are used, AND groups will be found within OR groups</li>
  <li>Include and Exclude are comma-separated lists used in file selection</li>
</ul>
</div>

<div class="formtitle2">Ignore Case:</div>
<div class="forminfo2"><input class="formitem" name="IGNORE_CASE" type="checkbox" value="1"$case_check /></div>


<div class="formtitle2">File Include Strings:</div>
<div class="forminfo2"><input class="formitem" name="INCLUDE" size="40" type="text" value="$INCLUDE" /></div>

<div class="formtitle2">File Exclude Strings:</div>
<div class="forminfo2"><input class="formitem" name="EXCLUDE" size="40" type="text" value="$EXCLUDE" /></div>

<div class="formtitle2">PHP Highlight:</div>
<div class="forminfo2"><input class="formitem" name="PHP" size="40" type="checkbox" value="1"$php_check /></div>


<div class="formtitle">Search Directory:</div>
<div class="forminfo"><input class="formitem" name="FOLDER" size="60" type="text" value="$FOLDER" /></div>

<div class="forminfo"><input class="contentsubmit s15" name="PROCESSFIND" type="submit" value="Search" /></div>
</form>
</div>
LBLSEARCH;

        //======================Search Files==========================

        if ($SEARCHSTR) {
            $FOLDER = RootPath($FOLDER);
            if (substr($FOLDER, -1) == '/') {
                $FOLDER = substr($FOLDER, 0, -1);
            }

            $files = GetDirectory($FOLDER, $INCLUDE, $EXCLUDE);

            printqn("<div class=`search`>
                  <h2>[".htmlentities($SEARCHSTR)."] Found in Files . . .</h2>
                  <ol>");

            $count = 0;
            $replace_diff = strlen('<span class="found"></span>');

            foreach ($files as $fi) {
                $filename  = "$FOLDER/$fi";
                $filetext  = file_get_contents($filename);

                $or_terms  = explode(' OR ', $SEARCHSTR);

                $searchterms  = array();
                $replaceterms = array();
                foreach ($or_terms as $terms) {
                    $and_terms = explode(' AND ', $terms);

                    foreach ($and_terms as $and_term) {
                        if ($PHP) {
                            $term = highlight_string(trim($and_term), true);
                        } else {
                            $term = htmlentities(trim($and_term));
                        }
                        $searchterms[]  = $term;
                        $replaceterms[] = "<span class=\"found\">$term</span>";
                    }
                }

                $FOUND = 0;
                foreach ($or_terms as $terms) {
                    if ($FOUND==0) {
                        $and_terms = explode(' AND ',$terms);
                        foreach ($and_terms as $and_term) {

                            $searchstr = trim($and_term);

                            if ($IGNORE_CASE) {
                                if (stripos($filetext, $searchstr) !== false) {
                                    $FOUND++;
                                }
                            } else {
                                if (strpos($filetext, $searchstr) !== false) {
                                    $FOUND++;
                                }
                            }

                            if ($FOUND == count($and_terms)) {
                                $count++;

                                printqn("<li><a class=`mainbutton s15` href=`#` onclick=`$('#textfield$count').toggle(); return false;`>$fi</a>");
                                $f = "$FOLDER/$fi";
                                if (strpos($f, $this->Root_Content_Dir) !== false) {
                                    $link = strFrom(RemoveExtension($f), $this->Root_Content_Dir . '/');                                    $OPT = '';
                                } else {
                                    $link = $f;
                                    $OPT = 'C';
                                }
                                printqn("&nbsp;<a href=`#` class=`contentbutton` onclick=`return top.parent.editFile('$link', '$OPT');` title=`Edit: $link`>Edit</a>");

                                if ($PHP) {
                                    $text = FormatFile($filetext);
                                } else {
                                    $text = htmlentities($filetext);
                                }

                                if ($IGNORE_CASE) {
                                    foreach($searchterms as $search_term) {
                                        $offset = 0;
                                        $length = strlen($search_term);
                                        do {
                                            $pos = stripos ($text, $search_term, $offset);
                                            if ($pos !== false) {
                                                $found_term = substr($text, $pos, $length);
                                                $text1 = substr($text, 0, $pos);
                                                $text2 = '<span class="found">' . $found_term . '</span>';
                                                $text3 = substr($text, $pos + $length);
                                                $text = $text1 . $text2 . $text3;
                                                $offset = $pos + $length + $replace_diff;
                                            }

                                        } while ($pos !== false);
                                    }
                                } else {
                                    $text = str_replace($searchterms, $replaceterms, $text);
                                }

                                if ($PHP) {
                                    printqn("
                                <div style=`display:none;` class=`fileview` id=`textfield$count`>
                                    <div class=`php`>$text</div>
                                </div>");
                                } else {
                                    printqn("<div style=`display:none;` class=`fileview` id=`textfield$count`><pre>$text</pre></div>");
                                }

                                print '</li>';
                            }
                        }
                    }
                }
            }
            print '</ol>';
            if ($count==0) {
                printqn("<h3 style=`margin-left:4em;`>[$SEARCHSTR] Not Found!</h3>");
            }
            print '</div>';
        }
    }


    //====================== SEARCH AND REPLACE IN FILES ==========================
    public function SearchAndReplace()
    {
        $vars = explode(' ', 'FINDSTR REPLACESTR FOLDER INCLUDE EXCLUDE IGNORE_CASE DISPLAYREPLACE REPLACEALL');
        $array = array();
        foreach ($vars as $var) {
            $array[$var] = Post($var);
        }
        extract($array, EXTR_OVERWRITE);

        if (!$FOLDER) {
            $_POST['FOLDER'] =  PathFromRoot($this->Site_Root) . '/';
        }

        if (!$DISPLAYREPLACE and !$REPLACEALL) {
            $_POST['INCLUDE'] = '.php,.def,.htm,.css,.js';
            $_POST['EXCLUDE'] = 'archive/';
        }

        $replace_all = ($DISPLAYREPLACE)? 'submit|Replace All|REPLACEALL|class="contentsubmit s15"' : '';

        $E = chr(27);
        $search_form = "

            form|[[PAGELINKQUERY]]|post|$E
            text|Find|FINDSTR||60|255|$E
            text|Replace With|REPLACESTR||60|255|$E
            code|<div style=\"margin-left:100px; font-size:0.9em;\">|$E
            text|Include|INCLUDE||40|255|$E
            text|Exclude|EXCLUDE||40|255|$E
            code|</div>|$E
            text|Search Directory|FOLDER||60|255|$E
            submit|Display|DISPLAYREPLACE|class=\"contentsubmit s15\"|$E
            $replace_all|$E
            endform|$E
        ";

        $ERROR = '';
        echo '<div class="formdiv">';
        echo OutputForm($search_form, 1);
        echo '</div>';

        //======================Search Files==========================


        if ($FINDSTR) {
            $FOLDER = RootPath($FOLDER);

            if (substr($FOLDER, -1) == '/') {
                $FOLDER = substr($FOLDER, 0, -1);
            }

            $files = GetDirectory($FOLDER, $INCLUDE, $EXCLUDE);

            $FINDSTR_OUT = htmlspecialchars($FINDSTR);
            printqn("<div class=`search` style=`padding:0.5em;`>
                  <h2>[$FINDSTR_OUT] Found in Files . . .</h2>
                  <ol>");

            $OLDtext = array('^T','^CR');
            $NEWtext = array("\t","\n");

            $FINDSTR    = str_replace($OLDtext, $NEWtext, $FINDSTR);
            $REPLACESTR = str_replace($OLDtext, $NEWtext, $REPLACESTR);

            $count=0;
            $OrTerms = explode('|', $FINDSTR);
            $ReplaceTerms = explode('|', $REPLACESTR);

            foreach ($files as $fi) {
                $filename = $FOLDER . "/$fi";
                $text=(file_get_contents($filename));

                $newtext  = str_replace($OrTerms,$ReplaceTerms,$text);
                $viewtext = htmlentities($text);
                for ($i=0; $i<count($OrTerms); $i++) {
                    $f = htmlentities($OrTerms[$i]);
                    $r = "<span style=\"background-color:#f00;\">$ReplaceTerms[$i]</span>";
                    $viewtext = str_replace($f,$r,$viewtext);
                }

                if ($newtext != $text) {
                    $count++;
                    $file_link = RemoveExtension($fi);
                    $link = qq("<a class=`contentbutton` href=`#` onclick=`return top.parent.editFile('$file_link', '');` title=`Edit: $file_link`>Edit</a>");

                    printqn("<li>
<a class=`mainbutton s15` href=`#` onclick=`$('#replace_page_view$count').toggle(); return false;`>$fi</a>
$link
<div  style=`display:none;` id=`replace_page_view$count` class=`fileview`>
<pre>$viewtext</pre>
</div>
</li>");

                    if ($REPLACEALL) {
                        $this->AdminWriteFile($filename, $newtext);
                    }
                }
            }
            print '</ol>';
            if ($count==0) {
                printqn("<h3 style=`margin-left:4em;`>[$FINDSTR_OUT] Not Found!</h3>");
            }
        }
        print '</div>';
        print '</td></tr></table></form>';

    }



    public function SessionCreateFormArray()
    {
        $FormArray = array(
        "form|$this->Admin_File_Query|post",
        'info|Variable|Remove'
        );

        foreach ($_SESSION as $key => $value) {
            if (is_array($value)) $value = '(ARRAY)';
            $value = htmlentities($value);
            $FormArray[] = "checkbox|$key|$key||1";
        }

        $FormArray[] = 'submit|Remove Checked Items|SESSION_SUBMIT|class="contentsubmit"';
        $FormArray[] = 'endform';
        return $FormArray;
    }


    public function SessionManager()
    {
        printqn("<div class=`formdiv`>
        <a class=`contentbutton right` href=`$this->Admin_File_Query`>Reload</a>
        <h1>Session Variables</h1>");

        $FormArray = $this->SessionCreateFormArray();

        $ERROR = '';


        if (Post('SESSION_SUBMIT')) {

           $array = ProcessFormNT($FormArray, $ERROR);

           foreach ($array as $key=>$value) {
              if ($value == 1) {
                 unset($_SESSION[$key]);
                 addFlash("$key - Removed");
              }
           }
           $FormArray = $this->SessionCreateFormArray();
        }


        echo OutputForm($FormArray, Post('SESSION_SUBMIT'));


        echo '
        <p><a class="mainbutton" href="#" onclick="$(\'#session_data\').slideToggle(\'normal\', function(){ResizeIframe()});  return false;">View Data</a></p>
        <div id="session_data" style="display:none;">';
        echo ArrayToStr($_SESSION);
        echo '
        </div>
        </div>';


    }

    public function ClearCachePage()
    {
        $dir = ArrayValue($this->Site_Config, 'cachedir');
        $CLEAR = Get('CLEAR');
        if ($dir) {
            $root_dir = $this->Root . $dir;
            $cache_files = GetDirectory($root_dir);
            if ($cache_files) {
                echo '
<div class="formdiv">
    <h3>Cache Files [' . $dir . ']</h3>
    <ol>';
                $ERROR = 0;
                foreach ($cache_files as $cache_file) {
                    $removed = '';
                    if ($CLEAR) {
                        if (unlink($root_dir . '/' . $cache_file)) {
                            $removed = ' &mdash; REMOVED';
                        } else {
                            AddError("Cache file [$cache_file] could not be removed!");
                            $ERROR = 1;
                        }
                    }
                    echo "
        <li>$cache_file$removed</li>";
                }
                echo '
    </ol>';
                if (!$CLEAR) {
                    echo '
<p class="center"><a class="contentsubmit" href="[[PAGELINKQUERY]];CLEAR=1">Clear Cache</a></p>';
                } elseif (!$ERROR) {
                    // cache cleared

                    $close = (!HavePhpError())? "top.parent.appformClose('appform$this->Dialog_Id');" : '';

                    if ($this->Dialog_Id) {
                        $script .= "
                        top.parent.setTopFlash('Cache Cleared!');
                        $close";
                        AddScript($script);
                    }
                }
                echo '
</div>';
            } else {
               AddError('No Cache Files Found!');
            }
        } else {
            AddError('No Cache Directory Configured!');
        }
    }


    //================================ SITE CONFIGURATION ==================================

    public function ConvertSiteConfigToText()
    {
        $RESULT = array();
        foreach ($this->Site_Config as $key=>$value) {
            if ($key == 'companyaddress') {
                $value = str_replace("<br />",'',stripslashes($value));
            } elseif ($key == 'docdirs') {
                $new = '';
                foreach ($value as $row) {
                    $new .= "$row\n";
                }
                $value = $new;
            } elseif ($key == 'emaillist') {
                $new = '';
                foreach ($value as $newkey=>$newvalue) {
                    if (!empty($newkey)) {
                        $new .= "$newkey|".$newvalue."\n";
                    }
                }
                $value = $new;
            } elseif ($key == 'emailtopics') {
                $new = '';
                foreach ($value as $row) {
                    $new .= "$row\n";
                }
                $value = $new;
            } elseif ($key == 'special_dirs') {
                $new = '';
                foreach ($value as $row) {
                    $new .= "$row\n";
                }
                $value = $new;
            }
            $RESULT[$key] = $value;
        }

        if (!empty($this->Db_Info)) {
            foreach ($this->Db_Info as $key=>$value) {
                $RESULT['DB_'.$key] = $value;
            }
        }

        if (!empty($this->User_Array)) {
            $new = '';
            foreach ($this->User_Array as $key=>$value) {
                $name = ArrayValue($value, 'name');
                $name = (empty($name))? $key : $name;
                $new .= "$key|{$value['password']}|{$value['level']}|$name\n";
            }
            $RESULT['userarray'] = $new;
        }

        if (!empty($this->Site_Config_Custom)) {
            $new = '';
            foreach ($this->Site_Config_Custom as $key=>$value)  {
                $new .= "$key|$value\n";
            }
            $RESULT['custom'] = $new;
        }

        // set up for missing items
        if (empty($SITECONFIG['cachedir'])) {
            $RESULT['cachedir'] = $this->Site_Config['sitedir'] . '/cache';
        }
        if (empty($SITECONFIG['jsdir'])) {
            $RESULT['jsdir'] = $this->Site_Config['sitedir'] . '/common';
        }

        return $RESULT;
    }

    public function UpdateSiteConfiguration()
    {

        $script = <<<LBLUSC

function setNewPath(sitestr,id) {
  var dir = getId(id);
  var dirstr = dir.value;
  var x = dirstr.lastIndexOf('/');
  if (x >= 0) dirstr = dirstr.substr(x+1);
  if (sitestr != '/') dir.value = sitestr + '/' + dirstr;
}

function configDirUpdate() {
  var sitestr = getId('FORM_sitedir').value;
  setNewPath(sitestr,'FORM_contentdir');
  setNewPath(sitestr,'FORM_templatedir');
  setNewPath(sitestr,'FORM_cssdir');
  setNewPath(sitestr,'FORM_jsdir');
  setNewPath(sitestr,'FORM_imagedir');
  setNewPath(sitestr,'FORM_archivedir');
  setNewPath(sitestr,'FORM_listdir');
  setNewPath(sitestr,'FORM_logdir');
  setNewPath(sitestr,'FORM_csspath');
  setNewPath(sitestr,'FORM_classdir');
  setNewPath(sitestr,'FORM_cachedir');
}
LBLUSC;
        AddScript($script);

        $SUBMIT = Post('SUBMIT');
        $ERROR = '';

        $TitleTemplate = '<br class="formbreak" /><div class="formtitle">@@VAR@@:</div>'."\n";

        echo '
        <div class="formdiv">
        <h1>Site Configuration</h1>';

        // Config Information to Form Info

        if (empty($SUBMIT)) {

            $array = $this->ConvertSiteConfigToText();
            Form_PostArray($array);
        }

        $Mask_Dir   = '^[a-zA-Z0-9\/_\.\-\~]+$';
        $Mask_Ext   = '^[a-zA-Z0-9_\-\.]+$';
        $Mask_General = '';
        $Mask_General_Line = '';

        $void9 = ($this->Admin_Level == 9)? '' : 'xxx';
        $void8 = ($this->Admin_Level > 7)? '' : 'xxx';

        $FormDataArray = array(
        'form|[[PAGELINKQUERY]]|post',
        'fieldset|Company Information',
        "text|Site Name|sitename|Y|40|80||$Mask_General_Line",
        "text|Company Name|companyname|Y|40|80||$Mask_General_Line",
        "text|Short Company Name|shortcompanyname|Y|40|80||$Mask_General_Line",
        'html|Company Address|companyaddress|N|50|6|wrap="off"',
        'endfieldset',

        'fieldset|Mail Settings',
        "html|Email List<br />(<i>site name</i>&#124;<i>email</i>)|emaillist|N|60|5|wrap=\"off\"|$Mask_General",
        "textarea|Email Topics|emailtopics|N|30|5|wrap=\"off\"|$Mask_General",
        "text|Email Subject Prefix|emailsubjectprefix|N|40|80||$Mask_General_Line",
        $void9 . "checkbox|Send Plain Text Messages|emailplaintext||1|0",
        $void9 . "checkbox|Use Swift Mailer (experimental)|emailsendswift||1|0",
        $void9 . "checkbox|Add Contact Address|contactaddress||1|0",
        'endfieldset',

        $void9 . 'fieldset|Site Directories',
        $void9 . "text|Page Directory<br />(Used in URL)|pagedir|N|80|120||$Mask_Dir",
        $void9 . "text|Site Directory|sitedir|N|80|120|onkeyup=\"configDirUpdate();\"|$Mask_Dir",
        $void9 . "text|Content Directory|contentdir|Y|80|120||$Mask_Dir",
        $void9 . "text|Template Directory|templatedir|Y|80|120||$Mask_Dir",
        $void9 . "text|CSS Directory|cssdir|Y|80|120||$Mask_Dir",
        $void9 . "text|Javascript Directory|jsdir|Y|80|120||$Mask_Dir",
        $void9 . "text|Site CSS Path|csspath|Y|80|120||$Mask_Dir",
        $void9 . "text|Image Directory|imagedir|Y|80|120||$Mask_Dir",
        $void9 . "text|Archive Directory|archivedir|Y|80|120||$Mask_Dir",
        $void9 . "text|List Directory|listdir|Y|80|120||$Mask_Dir",
        $void9 . "text|Log Directory|logdir|Y|80|120||$Mask_Dir",
        $void9 . "text|Cache Directory|cachedir|Y|80|120||$Mask_Dir",
        $void9 . "text|Class Directory|classdir|Y|80|120||$Mask_Dir",
        $void9 . 'textarea|Special Directories|special_dirs|N|40|5|wrap="off"',
        $void9 . "text|TinyMCE Path (HTML Editor)|tinymcepath|Y|80|120||$Mask_Dir",
        $void9 . "textarea|Doc Directories|docdirs|N|40|5|wrap=\"off\"|$Mask_General",
        $void9 . 'endfieldset',


        $void9 . "fieldset|Site File Configuration",
        $void9 . "text|Title File Extension<br />(should not change)|titlestr|Y|10|30||$Mask_Ext",
        $void9 . "text|Content File Extension<br />(should not change)|contentstr|Y|10|30||$Mask_Ext",
        $void9 . "text|View Page Extension<br />(example '.html')|extension|N|10|30||$Mask_Ext",
        $void9 . 'endfieldset',

        $void9 . 'fieldset|Admin Configuration',
        $void9 . 'checkbox|Use SSL Admin|usehttps||1|0',
        $void9 . 'checkbox|Want Drafts|wantdraft||1|0',
        $void9 . 'checkbox|Want HTML Editor|wanthtml||1|0',
        $void9 . 'checkbox|Want Edit-Area<br />(experimental)|wanteditarea||1|0',
        $void9 . 'integer|Content Width<br />(for Preview)|contentwidth|N|4|4|',
        $void9 . 'endfieldset',

        $void8 . 'fieldset|Users',
        $void8 . 'code|<p class="center">Enter Users as: <i>Username</i>&#124;<i>Password(hash)</i>&#124;<i>Level(1-9)</i>&#124;<i>Name</i>
            <br /><span class="s08">Passwords will be converted to hash if not already in that form</span></p>',
        $void8 . "textarea|Users|userarray|Y|60|5|wrap=\"off\"|$Mask_General",
        $void9 . 'checkbox|No MVP Access|NOMVP||1|0',
        $void8 . 'endfieldset',

        $void9 . 'fieldset|Database',
        $void9 . 'text|Database Name|DB_NAME|N|60|255|',
        $void9 . 'text|Database Host|DB_HOST|N|60|255|',
        $void9 . 'text|Database User|DB_USER|N|60|255|',
        $void9 . 'text|Database Password|DB_PASS|N|40|40|',
        $void9 . 'endfieldset',

        $void8 . "fieldset|Custom",
        $void8 . "html|Custom Variables<br /><i>Variable</i>&#124;<i>Value</i>|custom|N|60|8|wrap=\"off\"|$Mask_General",
        $void8 . 'endfieldset',

        'submit|Update|SUBMIT|class="contentsubmit s2"',
        'endform'
        );


        if ($SUBMIT) {

            $ASALT = ArrayValue($this->Site_Config, 'ASALT');
            if (empty($ASALT)) {
                $ASALT = GetPassword(8, "\e\r\n\tabcdefghijklmnopqrstuvwxyxABCDEFGHIJKLMNOPQRSTUVWXYX123467890:;./!@#%^&*()[]<>");
            }
            $ASALT_OUT = astr_replace(array("\e" => '\e', "\r" => '\r', "\n" => '\n'), $ASALT);


            $FormArray = ProcessFormNT($FormDataArray, $ERROR);
            $array = $this->ConvertSiteConfigToText();
            foreach ($array as $key => $value) {
                if (!array_key_exists($key, $FormArray)) {
                    $FormArray[$key] = $value;
                }
            }

            if (empty($ERROR)) {
                $out = '$'."SITECONFIG = array();\n";
                foreach ($FormArray as $key=>$value) {
                    $haveDB = false;

                    if ($key=='companyaddress') {
                        $value = addslashes(nl2br($value));

                    } elseif ($key=='emaillist') {
                        $array = explode("\n",$value);
                        $new = "array(\n";
                        foreach ($array as $row) {
                            $row = trim($row);
                            list($newkey, $newvalue) = explode('|',$row . '|');
                            $new .= "'$newkey' => '$newvalue',\n";
                       }
                       $value = substr($new,0,-2).')';

                    } elseif ($key=='emailtopics') {
                        $new = "array(\n";
                        $count = 0;
                        $array = explode("\n",$value);
                        if (count($array>0)) {
                            foreach ($array as $row) {
                                $row = trim($row);
                                if (!empty($row)) {
                                    $new .= "'$row',\n";
                                    $count++;
                                }
                            }
                        }
                        $value = ($count>0)? substr($new,0,-2).')' : substr($new,0,-1).')';

                    } elseif ($key=='docdirs') {
                        $new = "array(\n";
                        $count = 0;
                        $array = explode("\n",$value);
                        if (count($array>0)) {
                            foreach ($array as $row) {
                                $row = trim($row);
                                if (!empty($row)) {
                                    $new .= "'$row',\n";
                                    $count++;
                                }
                          }
                        }
                        $value = ($count>0)? substr($new,0,-2).')' : substr($new,0,-1).')';

                    } elseif ($key=='special_dirs') {
                        $new = "array(\n";
                        $count = 0;
                        $array = explode("\n",$value);
                        if (count($array>0)) {
                            foreach ($array as $row) {
                                $row = trim($row);
                                if (!empty($row)) {
                                    $new .= "'$row',\n";
                                    $count++;
                                }
                            }
                        }
                        $value = ($count>0)? substr($new,0,-2).')' : substr($new,0,-1).')';


                    } elseif ($key=='userarray') {
                        $array = explode("\n",$value);
                        $new = "array(\n";
                        foreach ($array as $row) {
                            list($newuser,$newpass,$newlevel,$name) = explode('|',$row . '|||');
                            $newpass = (strlen($newpass) == 72)? $newpass : $this->Admin_GetPasswordHash($newpass);
                            if (empty($name)) {
                                $name = $newuser;
                            }
                            $name = trim($name);
                            $new .= "'$newuser' => array('password'=>'$newpass','level'=>'$newlevel','name'=>'$name'),\n";
                        }
                        $value = substr($new,0,-2).')';

                    } elseif (($key=='DB_NAME') or ($key=='DB_HOST') or ($key=='DB_USER') or ($key=='DB_PASS')) {
                        $haveDB = true;

                    } elseif ($key=='custom') {
                        $array = explode("\n",$value);
                        $SiteCustom = '$SITE_CUSTOM = array();'."\n";
                        foreach ($array as $row) {
                            $row = trim($row);
                            if (!empty($row)) {
                                list($newkey,$newvalue) = explode('|',$row);
                                $SiteCustom .= '$'."SITE_CUSTOM['$newkey'] = \"$newvalue\";\n";
                            }
                        }
                    }
                    if (!$haveDB and ($key != 'custom')) {
                        if ($key=='userarray') {
                            $out .= '$'.qqn("USER_ARRAY = $value;");
                        } elseif ((substr($value,0,5)=='array') or ($value=='0') or ($value=='1')) {
                            $out .= '$'.qqn("SITECONFIG['$key'] = $value;");
                        } else {
                            $out .= '$'.qqn("SITECONFIG['$key'] = `$value`;");
                        }
                    }
                }

                if (!empty($FormArray['DB_NAME'])) {
                    $DBstr = '$'."DB_INFO = array('NAME'=>'@NAME@','HOST'=>'@HOST@','USER'=>'@USER@','PASS'=>'@PASS@');\n";
                    $DBstr = str_replace(array('@NAME@','@HOST@','@USER@','@PASS@'),
                        array($FormArray['DB_NAME'],$FormArray['DB_HOST'],$FormArray['DB_USER'],$FormArray['DB_PASS']),$DBstr);
                } else {
                    $DBstr = '$DB_INFO = array();'."\n";
                }


                $out .= '$'.qqn("SITECONFIG['ASALT'] = `$ASALT_OUT`;");

                $out .= $DBstr;
                $out .= $SiteCustom;

                //--------------------- output file -----------------------
                $config = file_get_contents($this->Conf_File);
                $end  = TextBetween("//---STARTCUSTOM---","\n?>",$config);
                $config = "<?php\n".$out."\n\n//---STARTCUSTOM---".$end."\n?>\n";

                $this->AdminWriteFile($this->Conf_File, $config);

                $close = (!HavePhpError())? "top.parent.appformClose('appform$this->Dialog_Id');" : '';
                if ($this->Dialog_Id) {
                    $script .= "
                        top.parent.setTopFlash('Site Configuration File has been updated!');
                        $close";
                    AddScript($script);
                }

            }
        }

        if ((!$SUBMIT) or ($ERROR)) {
            AddError($ERROR);
            echo OutputForm($FormDataArray, $SUBMIT);
        }

        echo "</div>";


    }

    //-----------------------------------------------------------------------

    public function StripRoot($str)
    {
        $str = str_replace('\\', '/', $str);  // needed for Windows
        $str = str_replace($this->Root, '', $str);
        return $str;
    }


    // ============================= ARCHIVE FILES =============================
    public function ArchiveFiles($page)
    {
        $querystr = ($this->Dialog_Id)? "?DIALOGID=$this->Dialog_Id" : '';

        print <<<ALBL
<div class="formdiv">
<a class="mainbutton" href="archive_files$querystr">View Files to Archive</a>
<a class="mainbutton" href="archive_all$querystr">Archive Files</a>
<a class="mainbutton" href="archive_list$querystr">View Archive</a>
ALBL;

        //----------ARCHIVE ALL FILES (/content and /common)-------------

        $archive_list_file = $this->Admin_Files_Dir . '/archive_list.dat';

        if (file_exists($archive_list_file)) {
            $archive_folders = file($archive_list_file);
            TrimArray($archive_folders);
            $archive_folders = array_unique($archive_folders);
        } else {
            //create a list and save it

            $archive_folders = array(
                PathFromRoot($this->Admin_Content_Dir),
                PathFromRoot("$this->Site_Root/common"),
                PathFromRoot("$this->Site_Root/config"),
                PathFromRoot($this->Admin_Template_Dir),
                PathFromRoot($this->Admin_Class_Dir),
                PathFromRoot("$this->Site_Root/js"),
                PathFromRoot($this->Admin_Css_Dir),
                PathFromRoot("$this->Site_Root/lists"),
                PathFromRoot("$this->Site_Root/helper"),
                PathFromRoot($this->Root_Image_Dir),
                $this->Site_Dir . '/'
            );
            $archive_folders = array_unique($archive_folders);
            $this->AdminWriteFile($archive_list_file, implode("\n", $archive_folders));
        }
        //-----------------------------------------------------------------------
        $EXCLUDE_LIST  = 'Desktop.ini,Thumbs,.log,.tmp,archive/';
        $IMAGELIST     = explode(',', $this->Admin_Image_Types);
        $INCLUDE       = Get('INCLUDE');

        //-----------------------------------------------------------------------
        if ($page == 'archive_file') {
            AddScriptOnload("setAutoTextAreaHeight('CTEXT');");

            $viewfile = urldecode(Get('file'));
            echo "<h2>FILE: $viewfile</h2>";
            if(ArrayItemsWithinStr($IMAGELIST, $viewfile)) {
                echo "<img src=\"/$viewfile\" alt=\"IMAGE: $viewfile\" />";
            } else {
                $file = $this->Root . $this->Admin_Archive_Dir . "/$viewfile";
                $content = htmlentities(file_get_contents($file));
                echo "<textarea rows=\"25\" id=\"CTEXT\" style=\"width:100%\">$content</textarea>";
            }
        }


        if ($page == 'archive_list') {
            echo <<<LBLAF
<h2>Archived Files</h2>
<p>Filter:&nbsp;<input type="text" size="40" maxlength="80" onkeyup="
    var filter = this.value.toLowerCase();
    var row = '';
    var check = false;
    var odd = 2;
    var hidding = false;
    $('li').each(function(){
        row = $(this).html();
        row = row.replace(/<\/?[^>]+(>|$)/g, '').toLowerCase();  // strips html tags
        check = row.indexOf(filter);
        if (check > -1) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });" /></p>
LBLAF;
            $archive_files = GetDirectory($this->Root . $this->Admin_Archive_Dir, $INCLUDE);

            if ($archive_files) {
                printqn("<ol id=`dirlist`>");

                foreach ($archive_files as $file) {
                    $link = urlencode("$file");
                    $onclick = "return top.parent.appformCreate('Archived File - [$file]', 'archive_file?file=$file')";
                    echo "<li><a href=\"#\" onclick=\"$onclick\">$file</a></li>\n";
                }
                echo '</ol>';
            }
        }

        if ($page == 'archive_files' || $page == 'archive_all') {

            if($page == 'archive_all') echo '<h2>Archiving Files. . .</h2>';
            else  echo '<h2>Files to Archive</h2>';


            foreach ($archive_folders as $folder) {

                //----------ARCHIVE ALL FILES (/content and /common)-------------

                echo "<h3>$folder</h3>";

                if (($folder == $this->Site_Root) or ($folder == '/')) {
                    if ($folder == '/') $folder = '';
                    $files = GetDirectory("$this->Root/$folder", '', $EXCLUDE_LIST, false);
                } else {
                    $files = GetDirectory("$this->Root/$folder", '', $EXCLUDE_LIST);
                }

                $count = 0;
                echo '<ol class="s12">';

                //------------ARCHIVE CONTENT FILES--------------
                foreach ($files as $AF) {

                    $filename = "$this->Root/$folder/$AF";
                    $filename = str_replace('//','/', $filename);

                    $filedate = date($this->Admin_Archive_Date_Format, filemtime($filename));
                    $AF       = str_replace($this->Root, '', "$folder/$AF");
                    $AF       = str_replace('/','@', substr($AF, 1));

                    if (ArrayItemsWithinStr($IMAGELIST, $filename)) {
                        $ext = '.'. strFromLast($filename, '.');
                    } else {
                        $ext = '.php';
                    }

                    $filename2 = $this->Root . $this->Admin_Archive_Dir . "/{$AF}_$filedate$ext";

                    if (!file_exists($filename2)) {
                        if ($page == 'archive_all') {
                            copy($filename, $filename2);
                            chmod($filename2, 0666);
                            $out ="<li>$filename --> {$AF}_$filedate$ext</li>\n";
                        } else {
                            $out ="<li>$filename <span style=\"font-size:0.7em;\">[$filedate]</span></li>\n";
                        }
                        echo $this->StripRoot($out);  // remove $ROOT from output
                        $count++;
                    }
                }
                if ($count == 0) {
                    echo '<li class="all_archived">All files Archived!</li>';
                }
                echo '</ol>';

            }
            if (($page == 'archive_files') and ($count>0)) {
                $this->LogUpdate('Archive All','');
            }
        }

        echo '</div>';

    }

    //====================== GENERATE SITEMAP ==========================
    public function GenerateSitemap()
    {
        global $HTTP_HOST;
        echo '<div class="formdiv">';


        $GENERATESITEMAP = Post('GENERATESITEMAP');
        $WRITESITEMAP    = Post('WRITESITEMAP');

        $SITEMAP = (Post('SITEMAP'))? Post('SITEMAP') : '/sitemap.xml';
        //was if (!$SITEMAP) $SITEMAP = "sitemap_" . strTo($domain, '.') . '.xml';

        $write_button = ($GENERATESITEMAP)? '<div class="forminfo2"><input class="contentsubmit" name="WRITESITEMAP" type="Submit" value="Write Sitemap" /></div>' : '';
        echo <<<LBLSM
<form method="post" action="$this->Admin_File_Query">
    <div class="formtitle2">Site Map File:</div>
    <div class="forminfo2"><input class="formitem" type="text" name="SITEMAP" value="$SITEMAP" size="60" /></div>
    <div class="forminfo2"><input class="contentsubmit" name="GENERATESITEMAP" type="Submit" value="Generate" /></div>
    $write_button
</form>
LBLSM;

        //======================GENERATE FILE==========================


        if ($GENERATESITEMAP or $WRITESITEMAP) {
            $filename = $this->Root . $SITEMAP;

            $text = '';
            $files = GetDirectory($this->Root_Content_Dir, $this->Admin_Title_Str);

            foreach ($files as $f) {
                $titlename = $this->Root_Content_Dir . '/' . $f;
                $name = TextBetween('<name>', '</name>', file_get_contents($titlename));
                if ($name) {
                    $f = RemoveExtension($f);
                    $text .= "  <url>\n    <loc>http://$HTTP_HOST/$f</loc>\n  </url>\n";
                }
            }
            $text = '<?xml version="1.0" encoding="UTF-8" ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/">
' . $text . '</urlset>';

            //======================SAVE FILE==========================
            if ($WRITESITEMAP) {
                $this->AdminWriteFile($filename, $text);
                printqn("<p class=`center`><a class=`stdbuttoni` target=`_blank` href=`http://$HTTP_HOST$SITEMAP`>View XML File</a></p>");
            }

            //======================OUTPUT FILE==========================

            $otext = htmlentities($text);
            echo '
<h2>Sitemap File</h2>
<pre>
' . $otext . '
</pre>';

        }

        echo '</div>';
    }


    //====================================== VIEW LINKS =======================================
    public function ViewLinks()
    {
        printqn("<div class=`formdiv`>
        <a class=`contentbutton right` href=`$this->Admin_File_Query`>Reload</a>
        <h2>Site Links</h2>\n<ol>");

        $files = $this->GetContentFiles();
        $link_files = GetDirectory($this->Root_Content_Dir, $this->Admin_Content_Str);

        if (!empty($this->Admin_Doc_Directories)) {
            foreach ($this->Admin_Doc_Directories as $dir) {
                $docfiles = GetDirectory($this->Root . $dir);

                foreach ($docfiles as $i => $value) {
                    $docfiles[$i] = substr($dir,1).'/'. StrTo($value, '.');
                }

                $files = array_merge($files, $docfiles);
            }
        }


        $URL = 'http://' . Server('HTTP_HOST') . $this->Site_Dir . '/PAGECONTENT';


        $valid_links = array();

        foreach ($link_files as $page) {
            $filename = $this->Root_Content_Dir . "/$page";
            $pagelink = RemoveExtension($page);

            $text = file_get_contents("$URL/$pagelink");
            if ($text) {
                $links = array_merge(TextBetweenArray('href="', '"', $text), TextBetweenArray("href='", "'", $text));
                if (count($links) > 0) {
                    $links = array_unique($links);

                    $output = '<ul style="text-align:left; margin-left:3em;">';
                    $count  = 0;
                    foreach ($links as $link) {
                        if ((strpos($link,'#')===false) and ($link!='/') and (strpos($link, '@@PAGELINK') === false)) {
                            $count++;
                            if (strpos($link,'http') === false) {
                                $testlink = strTo($link,'.');
                                $testlink = preg_replace('/(;|\?|:).+$/', '', $testlink);
                                $testlink = preg_replace('/@+.*@/', '', $testlink); // remove @ swap tags
                                $testlink = strFrom($testlink, $this->Admin_Page_Dir);
                                if (substr($testlink, 0, 1) == '/') {
                                    $testlink = substr($testlink, 1);
                                }
                                $flag  = in_array($testlink, $files) ? '' : " - not found";
                                $style = ($flag)? ' style="background-color:#f00;"' : '';
                                $output .= qqn("<li><span$style>$link$flag</span></li>");
                            } else {
                                if (in_array($link, $valid_links)) {
                                    $result = true;
                                } else {
                                    $result = url_exists($link);
                                    if ($result) {
                                        $valid_links[] = $link;
                                    }
                                }
                                $flag  = $result? '' : " - not found";

                                $style = ($flag)? ' style="background-color:#f00;"' : '';
                                $output .= qqn("<li><span$style><a target=`_blank` href=`$link`>$link</a></span></li>");
                            }
                        }
                    }

                    if ($count) {
                        printqn("<li><a class=`fileheader` href=`#` onclick=`return top.parent.editFile('$pagelink', '');` title=`Edit: $pagelink`>$pagelink</a></a>$output");
                        printqn('</ul></li>');
                    }
                }
            }
        }

        print '</ol>';
        print '</div>';


    }


    //=====================VIEW ALL CONTENT==================

    public function ViewAllContent($VC=0)
    {
        global $HTTP_HOST;

        $CONTENT_WIDTH = $this->Site_Config['contentwidth'];

        printqn("<div class=`formdiv`>
<table id=`viewalltable` cellpadding=`0` cellspacing=`1` align=`center` width=`$CONTENT_WIDTH`>
<tr><th class=`header`>Content Files</th></tr>");

        $count = 0;
        $files = $this->GetContentFiles();

        foreach ($files as $file) {
            //---------------output the info-------
            $filename    = $this->Root_Content_Dir . "/$file" . $this->Admin_Content_Str;
            $titlename   = $this->Root_Content_Dir . "/$file" . $this->Admin_Title_Str;
            $titletext   = file_get_contents($titlename);
            $title       = TextBetween('<title>','</title>',$titletext);
            $name        = TextBetween('<name>','</name>',$titletext);
            $summary     = TextBetween('<summary>','</summary>',$titletext);
            $description = TextBetween('<description>','</description>',$titletext);
            $keywords    = TextBetween('<keywords>','</keywords>',$titletext);

            $t = date("m\/d\/Y", filemtime($filename));
            $count++;
            $link = qq("<a class=`contentbutton s15` href=`#` onclick=`return top.parent.editFile('$file', '');` title=`Edit: $file`>$file</a>");


            $pagelink = "http://$HTTP_HOST$this->Page_Dir/$file";

            print <<<FILELABLE
  <tr>
  <!-- ===================FILE: $file ===================== -->

  <td class="header3">$count. $link
  &nbsp;&nbsp;&nbsp;
    <span style="font-size: 8pt">Version: $t
  &nbsp;&nbsp;&nbsp;
    (Validation:&nbsp;
    <a class="contentbutton" target="_blank" href="http://validator.w3.org/check?uri=$pagelink">XHTML</a>
  &nbsp;
    <a class="contentbutton" target="_blank" href="http://validator.w3.org/checklink?uri=$pagelink&amp;summary=on&amp;hide_redirects=on&amp;hide_type=all&amp;depth=&amp;check=Check">Links</a>
  &nbsp;
    <a class="contentbutton" target="_blank" href="http://www.contentquality.com/mynewtester/cynthia.exe?rptmode=-1&amp;url1=$pagelink">508</a>
  &nbsp;
    <a class="contentbutton" target="_blank" href="http://www.contentquality.com/mynewtester/cynthia.exe?rptmode=2&amp;url1=$pagelink">WAI</a>)</span>

  </td>
  </tr>

FILELABLE;

        if ($name) {
            printqn("<tr><td class=`header2`><b>NAME</b>: $name</td></tr>");
        }

        if ($summary) {
            printqn("<tr><td class=`header2`><b>SUMMARY</b>: $summary</td></tr>");
        }

        if ($title) {
            printqn("<tr><td class=`header2`><b>TITLE</b>: $title</td></tr>");
        }

        if ($description) {
            printqn("<tr><td class=`header2`><b>DESCRIPTION</b>: $description</td></tr>");
        }

        if ($keywords) {
            printqn("<tr><td class=`header2`><b>KEYWORDS</b>: $keywords</td></tr>");
        }

        if ($VC==1) {

            print <<<FILELABLE2
  <tr>
  <td class="page">
  <iframe id="ViewContent$count" src="view_file_content;F=$pagelink" width="$CONTENT_WIDTH" height="500"></iframe>
  </td>
  </tr>
FILELABLE2;
        } else {
            echo '<tr><td style="background-color:#fff;"></td></tr>';
        }
    }

        print "</table>\n</div>";
    }


}