<?php
$loginerr = '';

session_start();
require_once("include/config.inc.php");
require_once("dal/db.cls.php");
require_once("bl/login.cls.php");

if($_POST['loginsubmit'])
{ 
		$login = new Login();
		$row = $login->checkLogin($_POST['txtusername'], $_POST['txtpassword']);
		if($row==false)
		$loginerr = $login->msg;
		else
		{
			header("Location:index.php");
		}
}

?>

<html><head>
<title>Please Login - Student Information and Management system</title>
<link rel="stylesheet" type="text/css" href="styles/login.css" />
</head><body>
<form id='login' name='login' action="<?php echo $PHP_SELF ?>" method="POST">

<div class="login_wrapper">
  <div class="topbar">
    <div class="logo"></div>
    <div class="desc">Student Information and Management System</div>
  </div>
  <div class="content">
    <div class="label">Username</div>
    <div class="field_separator">:</div>
    <div class="field">
      <input name="txtusername" type="text" />
    </div>
    <div class="clear"></div>
    <div class="label">Password</div>
    <div class="field_separator">:</div>
    <div class="field">
      <input name="txtpassword" type="password" />
    </div>
    <div class="clear"></div>
    <div class="label">&nbsp;</div>
    <div class="field_separator">&nbsp;</div>
    <div class="field">
      <input name="loginsubmit" type="submit" class="btn_medium" value="Login" />
    </div>
    <div class="clear"></div>
  </div>
  <div class="footer">
    <div class="footer_txt">Copyright &copy; 2009 Student Information and Management System. All Rights Reserved.</div>
  </div>
</div>


</form>
</body></html>