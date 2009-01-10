<?php
include "database.inc.php";
include "bl/login.cls.php";

$login = new login();
$row = $login->checkLogin("superadmin", "superadmin");
if($row==false)
echo "false";
else
echo "true";
?>