<?php

/**
 * @author Anirban Bhattacherya
 * @email anirbanbhattacherya@gmail.com
 * @Project Student Information & Management System
 * @copyright 2009
 */


Class Template {

private $registry;

private $vars = array();

//Construct
function __construct($registry) {
	$this->registry = $registry;

}


//Magic Method
 public function __set($index, $value)
 {
        $this->vars[$index] = $value;
 }


function show($mod, $name) {
	$path = __SITE_PATH . '/views' . '/modules/' . $mod ."/". $name . '.php';

	if (file_exists($path) == false)
	{
		throw new Exception('Template not found in '. $path);
		return false;
	}

	// Load variables
	foreach ($this->vars as $key => $value)
	{
		$$key = $value;
	}

	include ($path);               
}


}

?>
