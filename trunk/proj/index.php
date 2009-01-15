<?php

/**
 * @author Anirban Bhattacherya
 * @email anirbanbhattacherya@gmail.com
 * @Project Student Information & Management System
 * @copyright 2009
 * @file base.class.php
 */

 /*** error reporting on ***/
 error_reporting(E_ALL);


 /*** include the init.php file ***/
 include 'site.config.php';

 /*** load the router ***/
 $registry->router = new router($registry);

 /*** set the controller path ***/
 $registry->router->setPath (__SITE_PATH . '/controller');

 /*** load up the template ***/
 $registry->template = new template($registry);

 /*** load the controller ***/
 $registry->router->loader();

?>
