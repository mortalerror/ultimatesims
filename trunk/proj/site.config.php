<?php

/*
Created by Anirban Bhattacherya
anirbanbhattacherya@gmail.com
Student Information & Management System
*/


if('SITE_PHP'==0)
{
	//error_reporting(0);
	define('SITE_PHP',1);
	require_once("include/config.inc.php");
	require_once("dal/db.cls.php");
	
	
	//Declare the static path
	$staticpath = dirname(__FILE__);
	
	//Define Site path
	$site_path = realpath(dirname(__FILE__));
 	define ('__SITE_PATH', $site_path);
	
	//Includes Models
	add_include_path( $staticpath . "/bl");
	add_include_path( $staticpath . "/app");
	add_include_path( $staticpath);
	
	include "base.class.php";
	include "registry.class.php";
	include "route.class.php";
	include "template.class.php";
	
	
	//Adding Functions
	$IgnoreFiles = Array('.DS_Store','CVS','.svn');
	
	if($handle = opendir($staticpath."/functions"))
	{
		if(!is_array($IgnoreFiles))
			$IgnoreFiles=Array();

		while (false !== ($file = readdir($handle)))
		{
			// if filename isn't '.' '..' or in the Ignore list... load it.
			if($file != "." && $file != ".." && !in_array($file,$IgnoreFiles))
				require_once("$staticpath/functions/$file");
		}
	}
	
	$registry = new registry;
}



//Dunction for Adding include Path
function add_include_path ($path)
{
    foreach (func_get_args() AS $path)
    {
        if (!file_exists($path) OR (file_exists($path) && filetype($path) !== 'dir'))
        {
            trigger_error("Include path '{$path}' not exists", E_USER_WARNING);
            continue;
        }
        
        $paths = explode(PATH_SEPARATOR, get_include_path());
        
        if (array_search($path, $paths) === false)
            array_push($paths, $path);
        
        set_include_path(implode(PATH_SEPARATOR, $paths));
    }
}


//Function for Removing Include Path 
function remove_include_path ($path)
{
    foreach (func_get_args() AS $path)
    {
        $paths = explode(PATH_SEPARATOR, get_include_path());
        
        if (($k = array_search($path, $paths)) !== false)
            unset($paths[$k]);
        else
            continue;
        
        if (!count($paths))
        {
            trigger_error("Include path '{$path}' can not be removed because it is the only", E_USER_NOTICE);
            continue;
        }
        
        set_include_path(implode(PATH_SEPARATOR, $paths));
    }
}




?>