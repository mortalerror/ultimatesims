<?php

/**
 * @author Anirban Bhattacherya
 * @email anirbanbhattacherya@gmail.com
 * @Project Student Information & Management System
 * @copyright 2009
 * @file base.class.php
 */

if(__HEADER == 1)
{
	try
	{
	 /*** load the router ***/
	 $registry->router = new router($registry);
	
	 /*** set the controller path ***/
	 $registry->router->setPath (__SITE_PATH . '/controller');
	
	 /*** load up the template ***/
	 $registry->template = new template($registry);
	
	 /*** load the controller ***/
	 $registry->router->loader();
	}
	catch(Exception $e)
	{
		echo "Unauthorized Access";	
	}
}
else
{
	echo "<b>Unauthorized Access</b>";	
}
?>
