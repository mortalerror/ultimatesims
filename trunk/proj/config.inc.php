<?php
if(CONFIG_INC==0)
	{
		//Defining the application start
		define('CONFIG_INC',1);
		
		//Add the DB Connection Info
		include "database.inc.php";
		
		//Start the session once if not started
		if(!isset($SessionStart))
			$SessionStart = 1;
			
		//Declare the static path
		$staticpath = dirname(__FILE__);
				
	}
?>