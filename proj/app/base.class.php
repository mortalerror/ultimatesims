<?php

/**
 * @author Anirban Bhattacherya
 * @email anirbanbhattacherya@gmail.com
 * @Project Student Information & Management System
 * @copyright 2009
 * @file base.class.php
 */



Abstract Class base {

protected $registry;

//Construction Magic Method
function __construct($registry) {
	$this->registry = $registry;
}


//Abstract function - without body
abstract function index();

}

?>
