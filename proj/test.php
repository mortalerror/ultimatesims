<?php
include "site.config.php";
include "showdate.cls.php";

$strdate = "25.12.2008:12.080808";
echo $strdate."<br>";

$date = new showdate($strdate);
$date->getdate("long");
echo $date->date;

echo "<br>".$date->time;

date_default_timezone_set('UTC');
echo "<br><br>".date("G:i");
echo "<br>".$_SERVER['REQUEST_TIME'] ;
?>