<script type="text/javascript">

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
</script>

<?php
if (ADMIN_LEVEL<9) return;

$configfile = '../config/siteconfig.php';
require_once "$LIB/form_helper.php";
include $configfile;


$SUBMIT = Post('SUBMIT');
$ErrorMsg = '';

$TitleTemplate = '<br class="formbreak" /><div class="formtitle">@@VAR@@:</div>'."\n";

echo '
<div id="configform">
<h1>Site Configuration</h1>';

// Config Information to Form Info

if (empty($SUBMIT)) {
    foreach ($SITECONFIG as $key=>$value) {
        if ($key == 'companyaddress') {
            $value = str_replace("<br />",'',$value);
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
        $_POST[$FormPrefix.$key] = $value;
    }

    if (!empty($DB_INFO)) {
        foreach ($DB_INFO as $key=>$value) $_POST[$FormPrefix.'DB_'.$key] = $value;
    }

    if (!empty($USER_ARRAY)) {
        $new = '';
        foreach ($USER_ARRAY as $key=>$value) {
            $name = ArrayValue($value, 'name');
            $name = (empty($name))? $key : $name;
            $new .= "$key|{$value['password']}|{$value['level']}|$name\n";
        }
        $_POST[$FormPrefix.'userarray'] = $new;
    }

    if (!empty($SITE_CUSTOM)) {
      $new = '';
      foreach ($SITE_CUSTOM as $key=>$value)  $new .= "$key|$value\n";
      $_POST[$FormPrefix.'custom'] = $new;
    }

    // set up for missing items
    if (empty($SITECONFIG['cachedir'])) {
        $_POST[$FormPrefix.'cachedir'] = $SITECONFIG['sitedir'] . '/cache';
    }
    if (empty($SITECONFIG['jsdir'])) {
        $_POST[$FormPrefix.'jsdir'] = $SITECONFIG['sitedir'] . '/common';
    }

}

$Mask_Dir   = '^[a-zA-Z0-9\/_\.\-\~]+$';
$Mask_Ext   = '^[a-zA-Z0-9_\-\.]+$';

$FormDataArray = array(
"form|$THIS_PAGE_QUERY|post",
"fieldset|Company Information",
"text|Site Name|sitename|Y|40|80||$Mask_General_Line",
"text|Company Name|companyname|Y|40|80||$Mask_General_Line",
"text|Short Company Name|shortcompanyname|Y|40|80||$Mask_General_Line",
"html|Company Address|companyaddress|N|50|6|wrap=\"off\"",
'endfieldset',

"fieldset|Mail Settings",
"html|Email List<br />(<i>site name</i>&#124;<i>email</i>)|emaillist|N|60|5|wrap=\"off\"|$Mask_General",
"textarea|Email Topics|emailtopics|N|30|5|wrap=\"off\"|$Mask_General",
"text|Email Subject Prefix|emailsubjectprefix|N|40|80||$Mask_General_Line",
"checkbox|Send Plain Text Messages|emailplaintext||1|0",
"checkbox|Use Swift Mailer (experimental)|emailsendswift||1|0",
"checkbox|Add Contact Address|contactaddress||1|0",
'endfieldset',

"fieldset|Site Directories",
"text|Page Directory<br />(Used in URL)|pagedir|N|80|120||$Mask_Dir",
"text|Site Directory|sitedir|N|80|120|onkeyup=\"configDirUpdate();\"|$Mask_Dir",
"text|Content Directory|contentdir|Y|80|120||$Mask_Dir",
"text|Template Directory|templatedir|Y|80|120||$Mask_Dir",
"text|CSS Directory|cssdir|Y|80|120||$Mask_Dir",
"text|Javascript Directory|jsdir|Y|80|120||$Mask_Dir",
"text|Site CSS Path|csspath|Y|80|120||$Mask_Dir",
"text|Image Directory|imagedir|Y|80|120||$Mask_Dir",
"text|Archive Directory|archivedir|Y|80|120||$Mask_Dir",
"text|List Directory|listdir|Y|80|120||$Mask_Dir",
"text|Log Directory|logdir|Y|80|120||$Mask_Dir",
"text|Cache Directory|cachedir|Y|80|120||$Mask_Dir",
"text|Class Directory|classdir|Y|80|120||$Mask_Dir",
"textarea|Special Directories|special_dirs|N|40|5|wrap=\"off\"",
"text|TinyMCE Path (HTML Editor)|tinymcepath|Y|80|120||$Mask_Dir",
"textarea|Doc Directories|docdirs|N|40|5|wrap=\"off\"|$Mask_General",
'endfieldset',


"fieldset|Site File Configuration",
"text|Title File Extension<br />(should not change)|titlestr|Y|10|30||$Mask_Ext",
"text|Content File Extension<br />(should not change)|contentstr|Y|10|30||$Mask_Ext",
"text|View Page Extension<br />(example '.html')|extension|N|10|30||$Mask_Ext",
'textarea|Combined Files<br />(target&#124;file1&#124;file2)|combined_files|N|60|5|wrap="off"',


'endfieldset',

"fieldset|Admin Configuration",
"checkbox|Use SSL Admin|usehttps||1|0",
"checkbox|Want Drafts|wantdraft||1|0",
"checkbox|Want HTML Editor|wanthtml||1|0",
"checkbox|Want Edit-Area<br />(experimental)|wanteditarea||1|0",
"text|Content Width<br />(for Preview)|contentwidth|N|4|4||$Mask_Integer",
'endfieldset',

"fieldset|Users",
'code|<p style="text-align:center;">Enter Users as: <i>Username</i>&#124;<i>Password(hash)</i>&#124;<i>Level(1-9)</i>&#124;<i>Name</i>
    <br /><span style="font-size:0.8em;">Passwords will be converted to hash if not already in that form</span></p>',
"textarea|Users|userarray|Y|60|5|wrap=\"off\"|$Mask_General",
'checkbox|No MVP Access|NOMVP||1|0',
'endfieldset',

"fieldset|Database",
"text|Database Name|DB_NAME|N|60|255||$Mask_Password",
"text|Database Host|DB_HOST|N|60|255||$Mask_Password",
"text|Database User|DB_USER|N|60|255||$Mask_Password",
"text|Database Password|DB_PASS|N|40|40||$Mask_Password",
'endfieldset',

"fieldset|Custom",
"html|Custom Variables<br /><i>Variable</i>&#124;<i>Value</i>|custom|N|60|8|wrap=\"off\"|$Mask_General",
'endfieldset',

"submit|Update|SUBMIT",
'endform'
);


if ($SUBMIT) {
    $ASALT = ArrayValue($SITECONFIG, 'ASALT');
    if (empty($ASALT)) {
        $ASALT = GetPassword(8, "\e\r\n\tabcdefghijklmnopqrstuvwxyxABCDEFGHIJKLMNOPQRSTUVWXYX123467890:;./!@#%^&*()[]<>");
    }
    $ASALT_OUT = astr_replace(array("\e" => '\e', "\r" => '\r', "\n" => '\n'), $ASALT);

    $FormArray = ProcessForm($FormDataArray,$table,'align="center" style="background-color:#888;"','align="right"','style="background-color:#fff;"',$ErrorMsg);
    if (empty($ErrorMsg)) {
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
                    $newpass = (strlen($newpass) == 72)? $newpass : Admin_GetPasswordHash($newpass);
                    if (empty($name)) {
                        $name = $newuser;
                    }
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
        $config = file_get_contents($configfile);
        $end  = TextBetween("//---STARTCUSTOM---","\n?>",$config);
        $config = "<?php\n".$out."\n\n//---STARTCUSTOM---".$end."\n?>\n";
        AdminWriteFile($configfile, $config);
        echo "<h2>Configuration file has been updated!</h2>";
    }
}

if ((!$SUBMIT) or ($ErrorMsg)) {
    WriteError($ErrorMsg);
    echo OutputForm($FormDataArray, $SUBMIT);
}

echo "</div>";
