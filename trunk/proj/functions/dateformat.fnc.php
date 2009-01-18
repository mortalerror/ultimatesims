<?php

/**
 * @author Anirban Bhattacherya
 * @email anirbanbhattacherya@gmail.com
 * @Project Student Information & Management System
 * @copyright 2009
 */

//All date functions that the application understands are here

 //Set the date in proper way
 function setdate($day, $month, $year)
 {
 	//Set the day to leading 0
	if(strlen($day) == 1)
 	$day = "0".$day;
 	
 	//Set the month as integer
 	if(strlen($month) == 1)
 		$month = "0".$month;
 	elseif(strlen($month) == 3)
 	{
 		$short_month_array = array("JAN"=>"01", "FEB"=>"02", "MAR"=>"03", "APR"=>"04", "MAY"=>"05", "JUN"=>"06", "JUL"=>"07", "AUG"=>"08", "SEP"=>"09", "OCT"=>"10", "NOV"=>"11", "DEC"=>"12");
 		$month = $short_month_array[strtoupper($month)];
 	}
 	elseif(strlen($month) >3)
 	{
 		$long_month_array = array("JANUARY"=>"01", "FEBRUARY"=>"02", "MARCH"=>"03", "APRIL"=>"04", "MAY"=>"05", "JUNE"=>"06", "JULY"=>"07", "AUGUST"=>"08", "SEPTEMBER"=>"09", "OCTOBER"=>"10", "NOVEMBER"=>"11", "DECEMBER"=>"12");	
 	}
 	
 	if(strlen($year) == 2)
 	{
 		if($year >= 0 && $year <= date('y'))
		 	$year = "20".$year;
		else
			$year = "19".$year;
 	}
 	
 	return $day.".".$month.".".$year;
 	
 }
 
 
 function getcurrentdate()
 {
 	 $day = date('d');
	 $month = date('m');
	 $year = date('Y');	
	 
	 return $day.".".$month.".".$year;
 }
 
 function getcurrentdatetime()
 {
 	 $day = date('d');
	 $month = date('m');
	 $year = date('Y');	
	 $hour = date('G');
	 $min = date('i');
	 
	 return $day.".".$month.".".$year.".".$hour.":".$min;
 }

?>