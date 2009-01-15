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
		header("Location:index.php");
	
}

?>

<html><head></head><body>
<form id='login' name='login' action="<?php echo $PHP_SELF ?>" method="POST">
<table border="1" width="28%" id="table1">
	<tr>
		<td width="40%">Username</td>
		<td width="55%"><input type="text" name="txtusername" size="20" value="<?php echo $_REQUEST['txtusername']; ?>"/></td>
	</tr>
	<tr>
		<td width="40%">Password</td>
		<td width="55%"><input type="password" name="txtpassword" size="20" /></td>
	</tr>
	<tr>
		<td colspan="2"><label><?php echo $loginerr; ?></label>
		<p align="right"><input type="submit" value="Submit" name="loginsubmit" /></td>
	</tr>
</table>
</form>
</body></html>