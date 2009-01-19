<?php
/**
* @author:  Anirban Bhattacherya
* @email: anirbanbhattacherya@gmail.com 
* @function getuser()
*/

function getuser()
{
	$val = $_SESSION['user_details'];
	$ret['name'] = $val['fname']." ".$val['lname'];
	
	return $ret;
}






?>