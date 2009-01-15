<?php

/**
 * @author Anirban Bhattacherya
 * @email anirbanbhattacherya@gmail.com
 * @Project Student Information & Management System
 * @copyright 2009
 * @file base.class.php
 */



//Check login
	session_start();
	if(!$_SESSION['username'])
	header("Location:login.php");

 //error reporting
 error_reporting(E_ALL);

 //Load the Config 
 include 'site.config.php';
 
//Load the header
 include "header.php";
 
 //load the router
 $registry->router = new router($registry);

 //set the controller path
 $registry->router->setPath (__SITE_PATH . '/controller');

 //load up the template
 $registry->template = new template($registry);

 //load the controller
 $registry->router->loader();







 //Load the footer
include "footer.php";

?>
