<?php
if(SITE_PHP==0)
{
	//error_reporting(0);
	define("SITE_PHP",1);
	require_once("config.inc.php");
	require_once("dal/db.cls.php");
	
	$IgnoreFiles = Array('.DS_Store','CVS','.svn');
	
	if($handle = opendir("$staticpath/functions"))
	{
		if(!is_array($IgnoreFiles))
			$IgnoreFiles=Array();

		while (false !== ($file = readdir($handle)))
		{
			// if filename isn't '.' '..' or in the Ignore list... load it.
			if($file != "." && $file != ".." && !in_array($file,$IgnoreFiles))
				require_once("$staticpath/functions/$file");
		}
	}
	
}
?>