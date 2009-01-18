<?php

/**
 * @author Anirban Bhattacherya
 * @email anirbanbhattacherya@gmail.com
 * @Project Student Information & Management System
 * @copyright 2009
 */
 
class showdate{
 
//date variable
 private $strdate;

//Day 
 public $day;

//Integer Month
 public $month;

//Month
 public $short_month;
 
//Month
 public $long_month;

//Year
 public $year;
 
//Short Year
 public $short_year;

//Time
 public $time; 
 
//Total Date
public $date;
 
//Construction
 function __construct($strdate) {
        $this->strdate = $strdate;
        //getdate();
 }
 
 
 
 //Get the date
 public function getdate($dttype = "short")
 {
 	$day = substr($this->strdate, 0, 2);
	$month = substr($this->strdate, 3, 2);
	$year = substr($this->strdate, 6, 4);
	$time = substr($this->strdate, 11,5);
	
	
	//Declare the Month Names
	$short_month_array = array(1=>"Jan", 2=>"Feb", 3=>"Mar", 4=>"Apr", 5=>"May", 6=>"Jun", 7=>"Jul", 8=>"Aug", 9=>"Sep", 10=>"Oct", 11=>"Nov", 12=>"Dec");
	$long_month_array = array(1=>"January", 2=>"February", 3=>"March", 4=>"April", 5=>"May", 6=>"June", 7=>"July", 8=>"August", 9=>"September", 10=>"October", 11=>"November", 12=>"December");
	
	//Set the day variables
	$this->day = $day;
	
	//Set the month Variables
	$this->month = $month;
	$this->short_month = $short_month_array[$month];
	$this->long_month = $long_month_array[$month];
	
	//Set the year variables
	$this->year = $year;
	$this->short_year = substr($year, 2, 2);
	
	if($dttype == "short")
	$this->date = $this->day." - ".$this->short_month." - ".$this->year;
	else
	$this->date = $this->day." ".$this->long_month.", ".$this->year;
	
	 
	$this->time = $time;
 	
 }


}
 
?>