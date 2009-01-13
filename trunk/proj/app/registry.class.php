<?php

/**
 * @author Anirban Bhattacherya
 * @email anirbanbhattacherya@gmail.com
 * @Project Student Information & Management System
 * @copyright 2009
 * @file registry.class.php
 */


Class Registry {

 //variables as array
 private $vars = array();


 //Magic Method to Set the unknown variables
 public function __set($index, $value)
 {
	$this->vars[$index] = $value;
 }

 
 //Magic Method to get the variables
 public function __get($index)
 {
	return $this->vars[$index];
 }


}

?>
