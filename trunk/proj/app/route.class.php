<?php

/**
 * @author Anirban Bhattacherya
 * @email anirbanbhattacherya@gmail.com
 * @Project Student Information & Management System
 * @copyright 2009
 */

class router {

//registry variable
 private $registry;

//Controller path 
 private $path;

 private $args = array();

 public $file;

//Controller Declaration
 public $controller;

//Action Declaration
 public $action; 

//Module Declaration
 public $module; 

//Magic Method
 function __construct($registry) {
        $this->registry = $registry;
 }

 //Set the Controller path
 function setPath($path) {

	/*** check if path i sa directory ***/
	if (is_dir($path) == false)
	{
		throw new Exception ('Invalid controller path: `' . $path . '`');
	}
	/*** set the path ***/
 	$this->path = $path;
}


 //Load the controller
 public function loader()
 {
	/*** check the route ***/
	$this->getController();

	
	/*** if the file is not there diaf ***/
	if (is_readable($this->file) == false)
	{
		/*
		$this->file = $this->path.'/error404.php';
                $this->controller = 'error404';
        */
        		$this->file = __SITE_PATH.'/error.php';
                $this->controller = 'error';
	}

	/*** include the controller ***/
	include $this->file;

	/*** a new controller class instance ***/
	$class = $this->controller . 'Controller';
	$controller = new $class($this->registry);

	/*** check if the action is callable ***/
	if (is_callable(array($controller, $this->action)) == false)
	{
		$action = 'index';
	}
	else
	{
		$action = $this->action;
	}
	
	/*** run the action ***/
	$controller->$action();
 }


 
private function getController() {

	/*** get the route from the url ***/
	$route = (empty($_GET['mod'])) ? '' : $_GET['mod'];
	$action = (empty($_GET['act'])) ? '' : $_GET['act'];
	
	
	if(!$action)
	$this->action = 'index';
	else
	$this->action = $action;
	
		
	if (empty($route))
	{
		$route = 'index';
	}
	else
	{
		/*** get the parts of the route ***/
		$parts = explode('/', $route);
		$this->module = $parts[0];
		$this->controller = $parts[1];
		/*
		if(isset( $parts[1]))
		{
			$this->action = $parts[1];
		}
		*/
	}

	if(empty($this->module))
	$this->module = 'default';


	if (empty($this->controller))
	{
		$this->controller = 'index';
	}


	/*** Get action ***/
/*	if (empty($this->action))
	{
		$this->module = 'default';
		$this->action = 'index';
	}
*/
	/*** set the file path ***/
	$this->file = $this->path .'/'. $this->module ."/" . $this->controller . '.php';
}


}

?>

