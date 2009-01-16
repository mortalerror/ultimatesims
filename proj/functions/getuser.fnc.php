<?php

function getuser($usertype, $uid)
{
	if(!$usertype || $uid == 0)
	$usr['name'] = "Unauthorized User";
	else
	{
		$types = array(1=>'student', 2=>'parent', 3=>'contact', 4=>'teacher', 5=>'staff', 6=>'admin', 7=>'admin', 8=>'admin', 9=>'admin');
		$sql = "select * from ".$types[$usertype]." where userid='".$uid."'";
		//$usr= $sql;
	}
	
	return $usr;
}

?>