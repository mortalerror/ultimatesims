<?php

/**
 * @author Anirban Bhattacherya
 * @email anirbanbhattacherya@gmail.com
 * @Project Student Information & Management System
 * @copyright 2009
 * @file base.class.php
 */

Class Registry {

private $vars = array();

//Magic Method
public function __set($index, $value)
 {
	$this->vars[$index] = $value;
 }

//Magic Method
 public function __get($index)
 {
	return $this->vars[$index];
 }


}

?>
