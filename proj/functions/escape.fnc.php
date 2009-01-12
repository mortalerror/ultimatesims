<?php
/**
 * @author Anirban Bhattacherya
 * @email anirbanbhattacherya@gmail.com
 * @Project Student Information & Management System
 * @copyright 2009
 */


function escape($str, $tagsallow=''){
	$str = htmlspecialchars($str);
	
	
	$dict  = array(chr(225) => '�', chr(228) =>  '�', chr(232) => 'c', chr(239) => 'd', 
            chr(233) => '�', chr(236) => 'e', chr(237) => '�', chr(229) => 'l', chr(229) => 'l', 
            chr(242) => 'n', chr(244) => '�', chr(243) => '�', chr(154) => '�', chr(248) => 'r', 
            chr(250) => '�', chr(249) => 'u', chr(157) => 't', chr(253) => '�', chr(158) => '�',
            chr(193) => '�', chr(196) => '�', chr(200) => 'C', chr(207) => 'D', chr(201) => '�', 
            chr(204) => 'E', chr(205) => '�', chr(197) => 'L',    chr(188) => 'L', chr(210) => 'N', 
            chr(212) => '�', chr(211) => '�', chr(138) => '�', chr(216) => 'R', chr(218) => '�', 
            chr(217) => 'U', chr(141) => 'T', chr(221) => '�', chr(142) => '�', 
            chr(150) => '-');

	
	$str = strtr($str, $dict);

	$str = strip_tags($str, $tagsallow);
	
	$str = htmlentities($str, ENT_QUOTES);
	
	
	return $str;
	
}





?>