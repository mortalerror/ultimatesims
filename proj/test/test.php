<?php
include "../site.config.php";

//require_once("../bl/login.cls.php");

$login = new login();
$row = $login->checkLogin("superadmin", "superadmin");
if($row==false)
echo "false";
else
echo "true";

echo "<br>".$_SESSION['username'];

//echo test("anirban");
?>