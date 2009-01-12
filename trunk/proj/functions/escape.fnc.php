<?php
/**
 * @author Anirban Bhattacherya
 * @email anirbanbhattacherya@gmail.com
 * @Project Student Information & Management System
 * @copyright 2009
 */


function escape($str, $tagsallow=''){
	$str = htmlspecialchars($str);
	
	
	$dict  = array(chr(225) => 'á', chr(228) =>  'ä', chr(232) => 'c', chr(239) => 'd', 
            chr(233) => 'é', chr(236) => 'e', chr(237) => 'í', chr(229) => 'l', chr(229) => 'l', 
            chr(242) => 'n', chr(244) => 'ô', chr(243) => 'ó', chr(154) => 'š', chr(248) => 'r', 
            chr(250) => 'ú', chr(249) => 'u', chr(157) => 't', chr(253) => 'ý', chr(158) => 'ž',
            chr(193) => 'Á', chr(196) => 'Ä', chr(200) => 'C', chr(207) => 'D', chr(201) => 'É', 
            chr(204) => 'E', chr(205) => 'Í', chr(197) => 'L',    chr(188) => 'L', chr(210) => 'N', 
            chr(212) => 'Ô', chr(211) => 'Ó', chr(138) => 'Š', chr(216) => 'R', chr(218) => 'Ú', 
            chr(217) => 'U', chr(141) => 'T', chr(221) => 'Ý', chr(142) => 'Ž', 
            chr(150) => '-');

	
	$str = strtr($str, $dict);

	$str = strip_tags($str, $tagsallow);
	
	$str = htmlentities($str, ENT_QUOTES);
	
	
	return $str;
	
}





?>