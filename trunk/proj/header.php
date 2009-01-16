<?php
if('__HEADER'==0)
{
	define('__HEADER',1);
	echo "<html><head>";
	//Title
	echo "<title>".__TITLE."</title>";
	
	//Adding javascripts
//Adding Functions
	$IgnoreFiles = Array('.DS_Store','CVS','.svn');
	
	if($handle = opendir($staticpath."/js"))
	{
		if(!is_array($IgnoreFiles))
			$IgnoreFiles=Array();

		while (false !== ($file = readdir($handle)))
		{
			// if filename isn't '.' '..' or in the Ignore list... load it.
			if($file != "." && $file != ".." && !in_array($file,$IgnoreFiles))
				echo "<script src='js/$file'></script>";
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