<?php
include "site.config.php";

require_once("bl/login.cls.php");

$login = new login();
$row = $login->checkLogin("anirban@yahoo.com", "anirban");
if($row==false)
echo "false";
else
echo "true";

echo "<br>".$_SESSION['username'];
?>