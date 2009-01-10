<?php

/*
Created by Anirban Bhattacherya (anirbanbhattacherya@gmail.com)
Student Information & Management System
Used cde cross database engine
*/


require ("cdeclass.php");
global $CDE;
$CDE = new CDE_Library ();

//Class for Database
class db {
		
		var $sql = '';
  		var $error = '';
  		
		//Connect the database
		function connectDB()
		{	
		//The db connection Info
		global $debug, $DatabaseType, $DatabaseServer,$DatabaseUsername,$DatabasePassword,$DatabaseName;
		global $CDE;
		
		$CDE->dbtype = $DatabaseType;
		$CDE->dbpath = $DatabaseServer.":".$DatabaseName;
		$CDE->username = $DatabaseUsername;
		$CDE->password = $DatabasePassword;
		
		$CDE->debug = $debug;
    	$CDE->tmppath = "../tmp/";
		
		$CDE->connect();
		$this->error = $CDE->error;
		}
		

		//Replace the SQL for possible SQL Injection attacks
		function replace_query($sql){
			//$sql = ereg_replace("([,\(=])[\r\n\t ]*''",'\\1NULL',$sql);
			return $sql;
		}
		
		
		//Execute any query
		function ExecuteQuery($sql)
		{
			$this->connectDB();
			$sql = $this->replace_query($sql);
			global $CDE;
			
			$CDE->exec($sql);
			$CDE->commit();
			$this->error = $CDE->error;
			$CDE->close();
		}
		
		//Return a Single Data
		function getData($sql)
		{
			$this->connectDB();
			$sql = $this->replace_query($sql);
			global $CDE;
			
			$qry = $CDE->query($sql);
			$row = $CDE->fetch_row($qry);
			$this->error = $CDE->error;
			$CDE->close();
			return $row[0];
		}
		
		
		//Return a Single Row
		function getDataRow($sql)
		{
			$this->connectDB();
			$sql = $this->replace_query($sql);
			global $CDE;
			
			$qry = $CDE->query($sql);
			$row = $CDE->fetch_row($qry);
			$this->error = $CDE->error;
			$CDE->close();
			return $row;
		}


		//Return a Number of data
		function getNumRows($sql)
		{
		$this->connectDB();
		$sql = $this->replace_query($sql);
		global $CDE;
		
		$qry = $CDE->query($sql);
		$row = $CDE->num_rows($qry);
		
		$this->error = $CDE->error;
		$CDE->close();
		return $row;
		}
		
		
		
		//Return a Complete Data Row
		function getDataRowAssoc($sql)
		{
		$this->connectDB();
		$sql = $this->replace_query($sql);
		global $CDE;
		
		$qry = $CDE->query($sql);
		$row = $CDE->fetch_object($qry, 0);
		
		$this->error = $CDE->error;
		$CDE->close();
		
		return $row;
		}
		
		
		//Return an Array as Set
		function getDataSet($sql, $ass=CDE_BOTH)
		{
		
		$this->connectDB();
		$sql = $this->replace_query($sql);
		global $CDE;
		
		$qry = $CDE->query($sql);
		while($row=$CDE->fetch_array($qry, $ass))
		{
			$newarray[]=$row ;
		}
		//return $newarray; ;
		
		$this->error = $CDE->error;
		$CDE->close();
			
		return $newarray;
		}

		
//Class End
}

?>