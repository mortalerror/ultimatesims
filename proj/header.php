<?php
if('__HEADER'==0)
{
	define('__HEADER',1);
	echo "<html><head>";
	//Title
	echo "<title>".__TITLE."</title>";
	
	//Adding javascripts
	$handle=opendir("js");
	while ($file = readdir($handle)) {
	$filelst = "$filelst,$file";
	}
	closedir($handle);
	$filelist = explode(",",$filelst);
	
	if(count($filelist)>3)
	{
	for ($count=1;$count<count($filelist);$count++) {
	$filename=$filelist[$count];
	if(($filename != ".") && ($filename != "..") && ($filename!=""))
	echo "<script src='js/".$filename."'></script>";
	}
	}
	
	echo "</head>";
	echo '<link rel="stylesheet" type="text/css" href="styles/styles.css">';
	echo "<body>";
	
	echo '<div class="wrapper">
  <div class="topbar">
    <div class="logo">
      <div class="desc">Student Information and Management System</div>
    </div>
    <div class="welcome">Welcome '.$_SESSION['username']. '| <a href="#">Logout</a><br />
      Your Last Login was 3rd September, 2008</div>
    <div class="clear"></div>
  </div>';
}
?>