<?
/*
  File Name     :   cdeclass.php
  Purpose       :   implements the CDE Library
  Author        :   Andre van Zuydam
  Copyright     :   Spiceware Software CC 2007 - 2008
                    published under the terms of the GNU General Public License.

  Created       :   19 April 2007

  Change Log    :

  19 April  2007        -- Created the library
                        -- Added connect for firebird & sqlite
                        -- Added query for firebird & sqlite
                        -- Added fetch_object for firebird & sqlite
                        -- Added fetch_array for firebird & sqlite
                        -- Added close for firebird & sqlite
                        -- Added execute for firebird

 26 April 2007          -- Fixed bug with sqlite exec
                        -- Added num_fields
                        -- Added num_rows
                        -- Added field_info

 15 May 2007            -- Added conversion at parsesql from FIRST 1 to LIMIT 1 - firebird SQL -> SQLlite functionality


01 August 2007

                        -- Adding the dbase connectivity for reading
                        -- num_rows
                        -- num_fields
                        -- fetch_object
                        -- fetch_array

02 August 2007
                        -- Adding mysql functionality


01 July 2008
			-- dbh is now public for use in applications	
			-- Oracle functionality has been added oci8
			-- import function added to import data from an existing connection - beta
			-- create_tables function added to create metadata from an existing connection - beta
			-- tran_date function added to translate any given date format to specified format
			-- Added pre_r which is same as echo print_r only with the pre tags around 

22 July 2008		
			-- Added the backup function
			-- Added the restore function


24 July 2008
      
      -- Finally got the inbuilt debugger working & manual is getting complete
      -- Added reset_query to reset the lastsql and lastqry variables
      -- Added first_row to fetch a single row as either an object or array
      -- Added next_row to fetch the next row of any query variable as either an object or array
      -- Changed fetch_object to have a case parameter to return either Uppercase or Lowercase
      -- Added fetch_row to library which returns 0 ... number of fields - this is now different from fetch array
      -- Fixed the problems with the cdeclasstest.php
      
      Notes: fetch_array & genid still need some work 

31 July 2008
    -- Added blob_update
    -- Added blob_read      

27 Aug 2008
	-- Added PGSQL Support
	
29 Aug 2008
 
  -- fetch added -> to fetch an object based on a single sql statement.
      
  P.S. if you're reading this
 
  1. can we consider adding row & array ?
  2. we need to work on blob updating .... 
  
04 Sep 2008
  -- fixed up replace_params
  -- added code for genid in sqllite  
  -- added blob_read - sqlite
  -- added blob_read - mysql   
  -- added public dateformat & datetimeformat - for use in returning and inputting date values
  
06 Sep 2008
	-- added PGSQL functionality to blob_read
	
08 Sep 2008
  -- fixed replace_params for single quote support	
  
*/

/**************************************************************************************************************************
Name: CDE_LIBRARY

The CDE Library is intended to help port php applications from one database to another.
PHP has many database connection methods and this is to make them work within one class.

**************************************************************************************************************************/
class CDE_Library
{
/**************************************************************************************************************************
PROTECTED VARIABLES
**************************************************************************************************************************/
     protected $version = 'Version 1.8';
     protected $lastquery;
/**************************************************************************************************************************
PUBLIC VARIABLES
**************************************************************************************************************************/
     public $dbh;
     public $dbpath;
     public $tmppath;
     public $username;
     public $password;
     public $lastsql;
     public $lastqry;
     public $currentrecord;
     public $dbtype;
     public $debug = false;
     public $devdebug = false; 
     public $error;
     public $errorno;
          
     public $dateformat = "d/m/Y";
     public $datetimeformat = "d/m/Y H:i:s";
     
/**************************************************************************************************************************
Name: GETVERSION

Function output a message in a different colour on the screen

Params: None
Returns: Returns the current library version

**************************************************************************************************************************/
     function getversion ()
     {
       return $this->version;
     }
/**************************************************************************************************************************
END: GETVERSION
**************************************************************************************************************************/

/**************************************************************************************************************************
Name: DEBUGMSG

Function output a message in a different colour on the screen

Params: message, color
Returns: Nothing

**************************************************************************************************************************/
     function debugmsg ($message, $color="green")
     {
       echo "<pre style=\"color : $color\">$message\n</pre>";
     }
/**************************************************************************************************************************
END: DEBUGMSG
**************************************************************************************************************************/

/**************************************************************************************************************************
Name: PRE_R

Function to return a string with <pre> tags and print_r functionality all in one

Params: string{variable or object}
Returns: html string with formatted result with pre tags

**************************************************************************************************************************/
     function pre_r ($string)
     {
       return "<pre>".print_r($string, 1)."</pre>";
     }
/**************************************************************************************************************************
END: PRE_R
**************************************************************************************************************************/
	
/**************************************************************************************************************************
Name: CDE_Errors()

Function to handle all the errors

Params: errno, errstr, errfile, errline as per default handler parameters
Returns: Error Message

**************************************************************************************************************************/
     function CDE_Errors($errno, $errstr, $errfile, $errline)
     {

       $backtrace = debug_backtrace();
       
       //Only report on serious errors
       
       
       if ($errno == E_ERROR||$errno == E_WARNING)
       {
         $this->errorno = rand(9999, 10000);
         $error = $backtrace [count($backtrace)-2]; 
         
        // echo $this->pre_r($error);
         
         if ($this->devdebug)
         {
           $clickhere = "<a href=\"#\" onclick=\"if (document.getElementById('error$this->errorno').style.display == 'block') {document.getElementById('error$this->errorno').style.display = 'none'; } else { document.getElementById('error$this->errorno').style.display = 'block'; } ;\"> [toggle debug] </a><div style=\"display:none;\" id=\"error$this->errorno\"><pre>".print_r($backtrace, 1)."</pre></div>";
         }
           else
         {
           $clickhere = '<b> enable $CDE->devdebug = true; to get more debug information </b>';
         }   
          
         $output  = "<pre style=\"color : red\"><b>An error occurred on line <span style=\"color:blue;\">{$error["line"]}</span> in <span style=\"color:blue;\">{$error["file"]}</span> calling {$error["class"]}->{$error["function"]}()</b>\n";
         $output .= "\n<span style=\"color:purple;\">$this->lastsql</span>\n\n<span style=\"color:black;\">$errstr</span>\n\n$clickhere</pre>";
          
         if ($this->debug)
         {
           $this->error = $output;
           echo $output;          
         }
           else
         {
           $this->error = $output;
           $posmessage = strpos ($errstr, ":");
           $errstr = substr ($errstr, $posmessage, strlen($errstr)-$posmessage);  
           
           $slimerror = "<pre style=\"color : black\"><b>error on line <span style=\"color:blue;\">{$error["line"]}</span> in <span style=\"color:blue;\">{$error["file"]}</span> <b> function {$error["function"]} $errstr </b>";
           //Diabling error message
		   echo $slimerror;   
         }
       } 
     }
/**************************************************************************************************************************
END: CDE_Errors()
**************************************************************************************************************************/

/**************************************************************************************************************************
Name: CDE_Library()

Init function for the class, sets the default handler to be an internal one.

Params: None
Returns: None

**************************************************************************************************************************/
     function CDE_Library()
     {
        set_error_handler               (array($this, 'CDE_Errors'));
        $this->error = ""; 
        $this->errorno = 0;     
     }  
	
/**************************************************************************************************************************
END: CDE_Library()
**************************************************************************************************************************/
	
/**************************************************************************************************************************
Name: CONNECT

The database handle for connecting which then stores the db handle in $dbh for use later

Params: None
Returns: Database Handle

**************************************************************************************************************************/
     function connect ()
     {
       $this->error = ""; //Set the errors to none
       if ($this->debug)
       {
         $this->debugmsg ("Connecting to ".$this->dbtype." database....", "blue");
       }
                    
       switch ($this->dbtype)
       {
         /* Firebird Functionality */
         case "firebird" :
           ini_set("ibase.dateformat", $this->dateformat);
           ini_set("ibase.timestampformat", $this->datetimeformat); 
     
           $this->dbh = ibase_connect ($this->dbpath, $this->username, $this->password);
         break;
         /* SQLite Functionality */
         case "sqlite" :
		       define("CDE_ASSOC", SQLITE_ASSOC);
           define("CDE_NUM", SQLITE_NUM);
           define("CDE_BOTH", SQLITE_BOTH);  
		      		      
           putenv("TMP=".$this->tmppath);
           $this->dbh = sqlite_open ($this->dbpath);
         break;
         /* DBASE Functionality */
         case "dbase" :
           $this->dbh = dbase_open ($this->dbpath, 2);
         break;
         /* MYSQL Functionality */
         case "mysql" :
		       //Set the constants
		   define("CDE_ASSOC", MYSQL_ASSOC);
           define("CDE_NUM", MYSQL_NUM);
           define("CDE_BOTH", MYSQL_BOTH);
		   
           $connection = explode(":", $this->dbpath);
           $this->dbh = mysql_connect ($connection[0], $this->username, $this->password);
           mysql_select_db ($connection[1]);
         break;
         /* Oracle Functionality */
         case "oracle" :
           define("CDE_ASSOC", OCI_ASSOC);
           define("CDE_NUM", OCI_NUM);
           define("CDE_BOTH", OCI_BOTH); 
           $this->dbh = oci_connect ($this->username, $this->password, $this->dbpath);
         break;
         /* MSSQL Functionality */
         case "mssql" :
           define("CDE_ASSOC", MSSQL_ASSOC);
           define("CDE_NUM", MSSQL_NUM);
           define("CDE_BOTH", MSSQL_BOTH); 
           $connection = explode(":", $this->dbpath);
           $this->dbh = mssql_connect ($connection[0], $this->username, $this->password);
           mssql_select_db ($connection[1]);
         break;		 
      	 /* PGSQL Functionality */
      	 case "pgsql":
           define("CDE_ASSOC", PGSQL_ASSOC);
           define("CDE_NUM", PGSQL_NUM);
           define("CDE_BOTH", PGSQL_BOTH); 
      	   $connection = explode(":", $this->dbpath);
      	 	 $this->dbh = pg_connect("host=".$connection[0]." dbname=".$connection[1]." user=".$this->username." password=".$this->password."");
      	 break;
       }
       
        //Define defaults for all databases that don't suport fetch_array
       define("CDE_ASSOC", 1);
       define("CDE_NUM", 2);
       define("CDE_BOTH", 3);
       define("CDE_INSERT", 0);
       define("CDE_UPDATE", 1); 
       define("CDE_DELETE", 2);
       
       if ($this->debug)
       {
         if ($this->dbh) { $this->debugmsg ("Connected to ".$this->dbtype." database....", "green"); }
         else {$this->debugmsg ("Failed to connect ".$this->dbtype." database....", "red"); exit;} //if we can't connect don't go on.
       }
     }
/**************************************************************************************************************************
END: CONNECT
**************************************************************************************************************************/

/**************************************************************************************************************************
Name: PCONNECT

The database handle for connecting which then stores the db handle in $dbh for use later, permanent

Params: None
Returns: Database Handle

**************************************************************************************************************************/
     function pconnect ()
     {
       $this->error = ""; //Set the errors to none
       if ($this->debug)
       {
         $this->debugmsg ("Connecting (PConnect) to ".$this->dbtype." database....", "blue");
       }
	     switch ($this->dbtype)
       {
         /* Firebird Functionality */
         case "firebird" :
           ini_set("ibase.dateformat", $this->dateformat);
           ini_set("ibase.timestampformat", $this->datetimeformat); 
           $this->dbh = ibase_pconnect ($this->dbpath, $this->username, $this->password);
         break;
         /* SQLite Functionality */
         case "sqlite" :
           define("CDE_ASSOC", SQLITE_ASSOC);
           define("CDE_NUM", SQLITE_NUM);
           define("CDE_BOTH", SQLITE_BOTH);  
           
           putenv("TMP=".$this->tmppath);
           $this->dbh = sqlite_open ($this->dbpath);
         break;
         /* DBASE Functionality */
         case "dbase" :
           $this->dbh = dbase_open ($this->dbpath, 2);
         break;
         /* MYSQL Functionality */
         case "mysql" :
           $connection = explode(":", $this->dbpath);
           $this->dbh = mysql_connect ($connection[0], $this->username, $this->password);
           mysql_select_db ($connection[1]);
         break;
         /* Oracle Functionality */
         case "oracle" :
           $this->dbh = oci_pconnect ($this->username, $this->password, $this->dbpath);
         break;
         /* MSSQL Functionality */
         case "mssql" :
           define("CDE_ASSOC", MSSQL_ASSOC, true);
           define("CDE_NUM", MSSQL_NUM, true);
           define("CDE_BOTH", MSSQL_BOTH, true); 
           $connection = explode(":", $this->dbpath);
           $this->dbh = mssql_connect ($connection[0], $this->username, $this->password);
           mssql_select_db ($connection[1]);
         break;
         /* PGSQL Functionality */
        case "pgsql":
        	$connection = explode(":", $this->dbpath);
        	$this->dbh = pg_connect("host=".$connection[0]." dbname=".$connection[1]." user=".$this->username." pass=".$this->password."");
        break;
       }
       
        //Define defaults for all databases that don't suport fetch_array
       define("CDE_ASSOC", 1);
       define("CDE_NUM", 2);
       define("CDE_BOTH", 3);
       define("CDE_INSERT", 0);
       define("CDE_UPDATE", 1); 
       define("CDE_DELETE", 2);
       
       if ($this->debug)
       {
         if ($this->dbh) { $this->debugmsg ("Connected (pconnect) to ".$this->dbtype." database....", "green"); }
         else {$this->debugmsg ("Failed to connect (pconnect) ".$this->dbtype." database....", "red"); exit;} //if we can't connect don't go on.
       }
     }
/**************************************************************************************************************************
END: PCONNECT
**************************************************************************************************************************/

/**************************************************************************************************************************
Name: CLOSE

The database handle for connecting which then stores the db handle in $dbh for use later, permanent

Params: None
Returns: Database Handle

**************************************************************************************************************************/
	   function close ()
     {
       $this->error = ""; //Set the errors to none
       if ($this->debug)
       {
         $this->debugmsg ("Closing connection to ".$this->dbtype." database....", "blue");
       }
       switch ($this->dbtype)
       {
         /* Firebird Functionality */
         case "firebird" :
           $result = ibase_close ($this->dbh);
         break;
         /* SQLite Functionality */
         case "sqlite" :
           $result = sqlite_close ($this->dbh);
         break;
         /* DBASE Functionality */
         case "dbase" :
           $result = dbase_close ($this->dbh);
         break;
         /* MYSQL Functionality */
         case "mysql" :
           $result = mysql_close ();
         break;
         /* Oracle Functionality */
         case "oracle" :
           $result = oci_close ($this->dbh);
         break;
         /* MSSQL Functionality */
         case "mssql" :
           $result = mssql_close ($this->dbh);
         break;
        	/* PGSQL Functionality */
        	case "pgsql":
        		$result = pg_close($this->dbh);
        	break;
         restore_exception_handler ();
       }
	     if ($this->debug)
       {
         if ($result) { $this->debugmsg ("Connection closed to ".$this->dbtype." database....", "blue"); }
         else $this->debugmsg ("Failed to close connection to ".$this->dbtype." database....", "red");
       }
       return $result;
     }
/**************************************************************************************************************************
END: CLOSE
**************************************************************************************************************************/

/**************************************************************************************************************************
Name: PARSESQL

Attempts to normalise and translate SQL from different languages to suite the current selected database type

Params: SQL String
Returns: Parsed SQL String

**************************************************************************************************************************/
     function parsesql ($sql)
     {
       $this->lastsql = $sql;
       $final = $sql;
       $thesql = strtoupper ($sql);
       //echo $thesql;
       /* FIRST = LIMIT */
       $pos = 0;
       if ($this->dbtype == "firebird")
       {
       }
         else
       if ($this->dbtype == "mssql")
       {
         //fix for blobs & dates -- make sure space before the words
         $sqlwords = array('/ blob/','/ date/');
         
         $repwords = array (' VarBinary null', ' datetime null');
         
         $final = preg_replace($sqlwords, $repwords, $final);
       }
         else  
       if ($this->dbtype == "sqlite" || $this->dbtype == "mysql")
       {
          
                 
        
         $pos = strpos ($thesql, "FIRST", $pos);
         if ($pos != 0)
         {
           //now to get the number and delete
           $start = $pos;
           //skip the first keyword
           $pos = $pos+5;
           while ($thesql[$pos] == " ")
           {
              $pos++;
           }
           //we have now found the number eg FIRST 100
           while ($thesql[$pos] != " ")
           {
              $pos++;
           }
           $end = $pos;
           //get the string FIRST 100
           $keyword = substr ($thesql, $start, $end-$start);
           //delete from sql statement
           $thesql = str_replace ($keyword, "", $thesql);
           //replace LIMIT by FIRST
           $keyword = str_replace ("FIRST", "LIMIT", $keyword);
           // add LIMIT to the end
           $thesql = $thesql." ".$keyword;
           $final = $thesql;
         }
         
         //Find Firebird date defaults
         
         $final = str_ireplace ("'now'", "'".date($this->dateformat)."'", $final);
         
       }
       return $final;
     }
/**************************************************************************************************************************
END: PARSESQL
**************************************************************************************************************************/

/**************************************************************************************************************************
Name: QUERY

Creates a query handle based on the passed SQL statement

Params: SQL String
Returns: Handle to Recordset

**************************************************************************************************************************/
     function query ($sql)
     {
       $this->error = ""; //Set the errors to none
       if ($this->debug)
       {
         $this->debugmsg ("Running query on ".$this->dbtype." database....", "blue");
         $this->debugmsg ($sql, "purple");
       }
       //Validate the sql statement and make adjustments
       $sql = $this->parsesql ($sql);
       switch ($this->dbtype)
       {
         /* Firebird Functionality */
         case "firebird" :
           $query = ibase_query ($this->dbh, $sql);
         break;
         /* SQLite Functionality */
         case "sqlite" :
           putenv("TMP=".$this->tmppath);
           $query = sqlite_query ($this->dbh, $sql);
         break;
         /* MYSQL Functionality */
         case "mysql" :
           //echo $sql;
           $query = mysql_query ($sql);
         break;
         /* Oracle Functionality */
         case "oracle" :
           $query = oci_parse ($this->dbh, $sql);
           oci_execute ($query);
         break;
         /* MSSQL Functionality */
         case "mssql" :
           $query = mssql_query ($sql, $this->dbh);
         break;
	       /* PGSQL Functionality */
         case "pgsql":
		       $query = pg_query($this->dbh,$sql);
         break;
       }
       
       if ($this->debug)
       {
         $this->debugmsg ("Query ran on ".$this->dbtype." database....", "green");
       }
       $this->lastquery = $query;
       return $query;
     }
/**************************************************************************************************************************
END: QUERY
**************************************************************************************************************************/

/**************************************************************************************************************************
Name: FETCH

Fetches a single row based on an sql string:

Basically the amalgamation of

$qry = $CDE->query ($sql);
$row = $CDE->fetch_object ($qry);

Might make the first_row & next_row redundant ? but performs similarly

Params: sql string
Returns: Object of Data

**************************************************************************************************************************/
    function fetch ($sql)
    {
       $qry = $this->query ($sql);
       return $this->fetch_object ($qry);
    }

/**************************************************************************************************************************
END: FETCH
**************************************************************************************************************************/


/**************************************************************************************************************************
Name: FETCH_OBJECT

Fetches a single row  into a data object from a query handle created by calling query.

Params: Query Handle, Case {0 = UpperCase, 1 = LowerCase}
Returns: Object of Data

**************************************************************************************************************************/
     function fetch_object ($query, $case=0)
     {
       $this->error = ""; //Set the errors to none
       if ($this->debug)
       {
         $this->debugmsg ("Fetching object on ".$this->dbtype." database....", "blue");
         $this->debugmsg ($query, "purple");
       }
       switch ($this->dbtype)
       {
         /* Firebird Functionality */
         case "firebird" :
           $query = ibase_fetch_object ($query);
         break;
         /* SQLite Functionality */
         case "sqlite" :
           $query = sqlite_fetch_object ($query);
         break;
         /*DBASE - this uses the record counter - currentrecord */
         case "dbase" :
           if ($this->currentrecord <= $this->num_rows($none))
           {
             $temp = dbase_get_record_with_names($this->dbh, $this->currentrecord);
             $this->currentrecord++;
             foreach ($temp as $name => $value)
             {
               $name = $name;
               $value = str_replace ("'", "''", $value);
               $query->$name = trim($value);
             }
           }
             else
           {
             $query = false;
           }
         break;
         /* MYSQL Functionality */
         case "mysql" :
           //echo $query;
           $query = mysql_fetch_object ($query);
         break;
         /* Oracle Functionality */
         case "oracle" :
           $query = oci_fetch_object ($query);
         break;
         /* MSSQL Functionality */
         case "mssql" :
           $query = mssql_fetch_object ($query);
         break;
      	/* PGSQL Functionality */
      	case "pgsql":
      	   $query = pg_fetch_object($query);
      	break;
       }
       
       //because of field name differences i choose to make all results uppercase as with firebird conventions as default
       //print_r ($query);
       if ($case == 0)
       { 
         if (is_object($query))
         {
           foreach ($query as $name => $value)
           {
             //Clean up the underscores and other characters
             
             $testname = str_replace ("_", "", $name);
             
             if (ctype_lower ($testname))
             {
               unset ($query->$name);
             }
             
             $name = strtoupper ($name);
             $query->$name = $value;
           }
         }
       }
         else
       {
         if (is_object($query))
         {
           foreach ($query as $name => $value)
           {
             //Clean up the underscores and other characters
             $testname = str_replace ("_", "", $name);
             
             if (ctype_upper ($testname))
             {
               unset ($query->$name);
             }

             $name = strtolower ($name);
             $query->$name = $value;
           }
         }
       }
              
       if ($this->debug)
       {
         $this->debugmsg ("Fetched object for ".$this->dbtype." database....", "green");
         $this->debugmsg ($this->pre_r($query), "purple");
       }
       
       return $query;
     }
/**************************************************************************************************************************
END: FETCH_OBJECT
**************************************************************************************************************************/

/**************************************************************************************************************************
Name: FETCH_ROW

Fetches a single row  into an  array object from a query handle created by calling query.

Params: Query Handle 
Returns: Array of Data starting at 0

Params: SQL String
Returns: Handle to Recordset

**************************************************************************************************************************/
	   function fetch_row ($query)
     {
       $this->error = ""; //Set the errors to none
       if ($this->debug)
       {
         $this->debugmsg ("Fetching row on ".$this->dbtype." database....", "blue");
         $this->debugmsg ($query, "purple");
       }
       switch ($this->dbtype)
       {
         /* Firebird Functionality */
         case "firebird" :
           $query = ibase_fetch_row ($query);
         break;
         /* SQLite Functionality */
         case "sqlite" :
           $query = sqlite_fetch_array ($query, SQLITE_NUM);
         break;
         /*DBASE - this uses the record counter - currentrecord */
         case "dbase" :
           if ($this->currentrecord <= $this->num_rows($none))
           {
             $query = dbase_get_record($this->dbh, $this->currentrecord);
             $this->currentrecord++;
           }
              else
           {
             $query = false;
           }
           if ($query)
           {
             foreach ($query as $name => $value)
             {
               $value = str_replace ("'", "''", $value);
               $query[$name] = trim($value);
             }
           }
         break;
         /* MYSQL Functionality */
         case "mysql" :
           $query = mysql_fetch_row ($query);
         break;
         /* Oracle Functionality */
         case "oracle" :
           $query = oci_fetch_row ($query);
         break;
         /* MSSQL Functionality */
         case "mssql" :
           $query = mssql_fetch_row ($query);
         break;
      	 /* PGSQL Functionality */
         case "pgsql":
      		 $query = pg_fetch_row($query);
      	 break;
       }
       if ($this->debug)
       {
         $this->debugmsg ("Fetched row on ".$this->dbtype." database....", "green");
         $this->debugmsg ($this->pre_r($query), "purple");
       }
       return $query;
     }
/**************************************************************************************************************************
END: FETCH_ROW
**************************************************************************************************************************/

/**************************************************************************************************************************
Name: FETCH_ARRAY

Fetches a single row  into an  array object from a query handle created by calling query, similar to fetch_row but with the extra parameter. Use the parameters below:

CDE_ASSOC -- names of fields
CDE_NUM -- field index from 0 ..
CDE_BOTH -- both names & numbers

Params: Query Handle 
Returns: Array of Data starting at 0

Params: SQL String
Returns: Handle to Recordset

**************************************************************************************************************************/
     function fetch_array ($query, $arraytype=CDE_NUM)
     {
       $this->error = ""; //Set the errors to none
       if ($this->debug)
       {
         $this->debugmsg ("Fetching array on ".$this->dbtype." database....", "blue");
         $this->debugmsg ($query, "purple");
       }
       switch ($this->dbtype)
       {
         /* Firebird Functionality */
         case "firebird" :
           if ($arraytype == 0)
           {
             $query = ibase_fetch_assoc ($query);
           }
             else
           if ($arraytype == 1) 
           {
             $query = ibase_fetch_row ($query);
           } 
             else
           {
             $query1 = ibase_fetch_assoc ($query);
             $query2 = ibase_fetch_row ($query);
             $query = array_merge($query1, $query2);
           }     
         break;
         /* SQLite Functionality */
         case "sqlite" :
           $query = sqlite_fetch_array ($query, $arraytype);
         break;
         /*DBASE - this uses the record counter - currentrecord */
         case "dbase" :
           if ($this->currentrecord <= $this->num_rows($none))
           {
             $query = dbase_get_record($this->dbh, $this->currentrecord);
             $this->currentrecord++;
           }
              else
           {
             $query = false;
           }
           if ($query)
           {
             foreach ($query as $name => $value)
             {
               $value = str_replace ("'", "''", $value);
               $query[$name] = trim($value);
             }
           }
         break;
         /* MYSQL Functionality */
         case "mysql" :
         	if($arraytype=="CDE_NUM")
         	$myarr = MYSQL_NUM;
         	elseif($arraytype=="CDE_ASSOC")
         	$myarr = MYSQL_ASSOC;
         	else
         	$myarr = MYSQL_BOTH;
         	
           $query = mysql_fetch_array ($query, $myarr);
         break;
         /* Oracle Functionality */
         case "oracle" :
           $query = oci_fetch_array ($query, $arraytype);
         break;
         /* MSSQL Functionality */
         case "mssql" :
           $query = mssql_fetch_array ($query, $arraytype);
         break;
         case "pgsql":
           $query = pg_fetch_array($query, $arraytype);
         break;
       }
       
       if ($this->debug)
       {
         $this->debugmsg ("Fetched array on ".$this->dbtype." database....", "green");
         $this->debugmsg ($this->pre_r($query), "purple");
       }
       
       return $query;
     }
/**************************************************************************************************************************
END: FETCH_ARRAY
**************************************************************************************************************************/

/**************************************************************************************************************************
Name: REPLACE_PARAMS

Replaces all the params specified by ? with the values provided in the input value parameter

Params: Array of Input Values, SQL String
Returns: SQL String

**************************************************************************************************************************/
    function replace_params ($inputvalues, $sql)
	  {
	    $lastplace = 1;  //Mustn't go back in the replace
	    for ($i = 1; $i < sizeof($inputvalues); $i++)
      {
        $tryme = $inputvalues[$i];
        $inputvalues[$i] = str_replace ("'", "''", $inputvalues[$i]); //some strings have single ' which make it break on replacing!
        $inputvalues[$i]  = "'".$inputvalues[$i]."'";
	   
		    $lastpos = 1;

    		while ($lastpos <> 0)
    		{
    		  $lastpos =  strpos ($sql, "?", $lastplace);
    		  if ($lastpos == "") break; //This checks that lastpos
          if ($sql[$lastpos-1] != "<" || $sql[$lastpos+1] != ">")
          {
            $sql = substr_replace($sql, $inputvalues[$i], $lastpos, 1);
            $lastplace = $lastpos+strlen($inputvalues[$i]);
          }  
    		  $lastpos = 0;
    		}
    		$count++;
    	}
	    return $sql; 		  
    }	
/**************************************************************************************************************************
END: REPLACE_PARAMS
**************************************************************************************************************************/

/**************************************************************************************************************************
Name: EXEC

Runs an sql query on the database which doesn't return a result

Params: SQL String, Parameters
Returns: Error Number (1 = Successful, 0 = Failed)

**************************************************************************************************************************/
     function exec ($sql)
     {
       $this->error = ""; //Set the errors to none
       $inputvalues = func_get_args();
	      
	     if ($this->debug)
       {
         $this->debugmsg ("Executing SQL on ".$this->dbtype." database....", "blue");
         $this->debugmsg ($sql, "purple");
       }
       
       //Validate the sql statement and make adjustments
       
       
       $sql = $this->parsesql ($sql);
       
       switch ($this->dbtype)
       {
         /* Firebird Functionality */
         case "firebird" :
           $query = ibase_prepare ($this->dbh, $sql);
    		   $params = array ();   
    		   $params[0] = $query;   
               //what if we have passed some parameters - firebird can do this
    		   for ($i = 1; $i < func_num_args(); $i++)
           {
    		     $params[$i] =func_get_arg($i);   
    	     }   
		   
           if (sizeof ($params) != 0)
           {
              $anerror = call_user_func_array ("ibase_execute", $params);
           }
             else
           {
              $anerror = ibase_execute ($query);
           }
         break;
         /* SQLite Functionality */
         case "sqlite" :
           //Replace ? with parameters if given before executing. this is not a big problem
           
            
           $sql = $this->replace_params ($inputvalues, $sql);
          
           $this->lastsql = $sql;
           $anerror = "";
           sqlite_exec ($this->dbh, $sql, $anerror);
         break;
          /* MYSQL Functionality */
         case "mysql" :
           //Replace ? with parameters if given before executing. however this is not a big problem
           $sql = $this->replace_params ($inputvalues, $sql);
           $this->lastsql = $sql;
           $anerror = "";
           mysql_query ($sql);
           $anerror = mysql_error($this->dbh);
         break;
         /* Oracle Functionality */
         case "oracle" :
           //Replace ? with parameters if given before executing.  this is not a big problem
           $sql = $this->replace_params ($inputvalues, $sql);
           $this->lastsql = $sql;
           $anerror = "";
           $query = oci_parse ($this->dbh, $sql);
           $anerror = oci_execute ($query);
         break;
         /* MSSQL Functionality */
         case "mssql" :
           //Replace ? with parameters if given before executing.  this is not a big problem
           $sql = $this->replace_params ($inputvalues, $sql);
           $this->lastsql = $sql;
                   
           mssql_query ($sql);
           $anerror = mssql_get_last_message();
         break;
        	/* PGSQL Functionality */	
        	case "pgsql":
        	   $params = array ();
                   for ($i = 1; $i < sizeof($inputvalues); $i++)
                   {
                      $tryme = $inputvalues[$i];
        
                    if (is_numeric($tryme))
                    {
                      $params[$count] = $inputvalues[$i];
                    }
                    else
                    {
                      $params[$count] = "'".$inputvalues[$i]."'";
                    }
        	    $query = pg_prepare($this->dbh, "", $sql);
         	    $anerror = pg_execute($this->dbh, "", $params);		
        	break;
         
       }
       
       if ($this->debug)
       {
         $this->debugmsg ("SQL executed on ".$this->dbtype." database.... returning $anerror", "green");
       }
       
       return $anerror;
     }
  }  
/**************************************************************************************************************************
END: EXEC
**************************************************************************************************************************/

/**************************************************************************************************************************
Name: BLOB_UPDATE

A BLOB field can be updated from either a file or string, it is important that you specify 
what the "where" clause is for the table or else you will overwrite your whole recordset with the same BLOB.
You will need to create the record first as this is merely an update function

Params: String / Filename, tablename, fieldname, where
Returns: Error Number (1 = Successful, 0 = Failed)

**************************************************************************************************************************/
     function blob_update ($content, $tablename, $fieldname, $where="0=1")
     {
       //Check for file
       if (file_exists($content))
       {
         $content = file_get_contents($content);
       }
              
       if ($this->debug)
       {
         $this->debugmsg ("Updating blob in $tablename ($fieldname) where $where on $this->dbpath ....", "blue");
       } 
             
       $sql = "update $tablename set $fieldname = ? where $where";
       
       switch ($this->dbtype)
       {
         /* Firebird Functionality */
         case "firebird" :
           $result = $this->exec ($sql, $content);
         break;
         /* SQLite Functionality */
         case "sqlite" :
           $result = $this->exec ($sql, $content);
         break;
          /* MYSQL Functionality */
         case "mysql" :
           $result = $this->exec ($sql, $content);
         break;
         /* Oracle Functionality */
         case "oracle" :
           $result = $this->exec ($sql, $content);
         break;
      	/* PGSQL Functionality */
      	case "pgsql":
      	  $result = $this->exec ($sql, $content);
      	break;
       }
       
       if ($this->debug)
       {
         $this->debugmsg ("Blob updated on ".$this->dbtype." database.... returning $result", "green");
       }
       
       return $result;
     }
/**************************************************************************************************************************
END: BLOB_UPDATE
**************************************************************************************************************************/

/**************************************************************************************************************************
Name: BLOB_READ

A BLOB field can be updated from either a file or string, it is important that you specify 
what the "where" clause is for the table or else you will overwrite your whole recordset with the same BLOB.
You will need to create the record first as this is merely an update function

Params: String / Filename, tablename, fieldname, where
Returns: Error Number (1 = Successful, 0 = Failed)

**************************************************************************************************************************/
     function blob_read ($tablename, $fieldname, $where="0=1")
     {
              
       if ($this->debug)
       {
         echo "<pre style=\"color : green\">Reading blob in $tablename ($fieldname) where $where on $this->dbpath <p style=\"color:purple;\">  $sql</p></pre>";
       }
       
       switch ($this->dbtype)
       {
         /* Firebird Functionality */
         case "firebird" :
           $sql = "select first 1 $fieldname from $tablename where $where";
           $this->reset_query();
           $object = $this->first_row ($sql);
           
           $fieldname = strtoupper($fieldname);
           $blobfield = $object->$fieldname;
           
           $blob_data = ibase_blob_info($this->dbh, $blobfield);
           $blob_hndl = ibase_blob_open($this->dbh, $blobfield);
           $content = ibase_blob_get($blob_hndl, $blob_data[0]);
         break;
         /* SQLite Functionality */
         case "sqlite" :
           $sql = "select first 1 $fieldname from $tablename where $where";
           $this->reset_query();
           $object = $this->first_row ($sql);
           
           $fieldname = strtoupper($fieldname);
           $content = $object->$fieldname;
         break;
          /* MYSQL Functionality */
         case "mysql" :
           $sql = "select first 1 $fieldname from $tablename where $where";
           $this->reset_query();
           $object = $this->first_row ($sql);
           
           $fieldname = strtoupper($fieldname);
           $content = $object->$fieldname;
         break;
         /* Oracle Functionality */
         case "oracle" :
           //implement
         break;
      	/* PGSQL Functionality */
      	 case "pgsql":
      		// testing required
		$sql = "select first 1 $fieldname from $tablename where $where";
		$this->reset_query();
		$object = $this->first_row($sql);

		$fieldname = strtoupper($fieldname);
		$content = $object->$fieldname;
      	break;
       }
       if ($this->debug)
       {
         echo "<pre style=\"color : blue\">Query Executed on $this->dbpath \n </pre>";
       }
       return $content;
     }
/**************************************************************************************************************************
END: BLOB_READ
**************************************************************************************************************************/

/**************************************************************************************************************************
Name: CREATE_TABLES

Runs an sql query on the database which doesn't return a result

Params: CDE Object, Tablename, [*, Field1, Field2] String, Delete Table, Prefix String for new table
Returns: Error Number (1 = Successful, 0 = Failed)

**************************************************************************************************************************/
     function create_tables ($IMP, $tablename="", $fields="*",$delete=false, $prefix="")
     {
        if ($this->debug)
        {
          echo "<pre style=\"color : green\">Create table ".$tablename." from ".$IMP->dbpath." to ".$this->dbpath."....\n</pre>";
        }

        if ($delete)
        {
          $this->exec ("drop table $tablename");
        }

        $sql = "select";

        if ($fields != "*")
        {
          $sql .= " !0!";
          $fields = explode (",", $fields);
          foreach ($fields as $id => $value)
          {
            $sql .= ",".$value;
          }
        }
          else
        {
          $sql .= " * ";
        }

        $sql .= " from $tablename";


        $sql = str_replace ("!0!,", "", $sql);

        $qryfrom = $IMP->query ($sql);

        $nooffields = $IMP->num_fields ($qryfrom);
        for ($i = 1; $i <= $nooffields; $i++)
        {
          $fieldinfo[$i] = $IMP->field_info($qryfrom, $i);
        }

        $fieldvalues = "!0!";
        $datatype    = "!0!";
        foreach ($fieldinfo as $id => $value)
        {

          $typeoffield = $value["type"];

          if ($this->debug)
          {
            echo "<pre style=\"color : green\">Field {$value["name"]} = {$value["type"]} : {$value["length"]}\n</pre>";
          }

          if ($typeoffield == "CHAR")
          {
            $typedeclaration = "CHAR(".$value["length"].") default ''";
          }
          elseif ($typeoffield == "VARCHAR2")
          {
            $typedeclaration = "VARCHAR(".$value["length"].") default ''";
          }
          elseif ($typeoffield == "DATE")
          {
            $typedeclaration = "DATE";
          }
          elseif ($typeoffield == "NUMBER")
          {
            if ($value["scale"] == 0)
            {
             $typedeclaration = "INTEGER default 0";
            }
              else
            {
              $typedeclaration = "NUMERIC (".$value["prec"].", ".$value["scale"].") default 0";
            }
          }


          $fieldvalues .= ",\n".strtolower ($value["name"])." ".$typedeclaration;
        }
        $fieldvalues = str_replace ("!0!,", "", $fieldvalues);

        $sqlcreatetable = "create table ".$prefix."$tablename ($fieldvalues)";

        $error = $this->exec ($sqlcreatetable);

        if ($this->debug)
        {
          echo "<pre style=\"color : green\">SQL for ".$tablename.": ".$sqlcreatetable."....\n</pre>";
        }

        $this->commit();

        if ($this->debug)
        {
          echo "<pre style=\"color : green\">Table ".$tablename." created from ".$IMP->dbpath." to ".$this->dbpath."....\n</pre>";
        }
     }

     function tran_date ($input="", $format="d/m/Y")
     {
       if ($input != "")
       {
         $output = date_parse($input);
         if ($ouput["year"] > 2038) $ouput["year"] = $ouput["year"] - 100; //PHP limitation for dates less than 1970 translated to 2000+ dates 
         $output = date ($format, mktime ($output["hour"], $output["minute"], $output["second"], $output["month"], $output["day"], $output["year"]));
       }
         else
       {
         $output = "null";
       }    
       return $output;
     }
/**************************************************************************************************************************
END: TRAN_DATE
**************************************************************************************************************************/

/**************************************************************************************************************************
Name: IMPORT

Import a table from the same or different CDE engine

Params: Tablename as String, {Field1, Field2, ... | *}, Delete content from table {True / False}, Verbose = Output results, Prefix, Where 
Returns: Error Number (1 = Successful, 0 = Failed)

**************************************************************************************************************************/
     /* Function to import from one CDE database to another
        $CDE->import ($ORA, "breed", "field1, field2| *");
     */
function import ($IMP, $tablename="", $fields="*", $delete=false, $verbose=true, $prefix="", $where="", $tblprefix="")
     {
        if ($this->debug)
        {
          echo "<pre style=\"color : green\">Importing from ".$IMP->dbpath." to ".$this->dbpath."....\n</pre>";
        }
        $sql = "select";
        if ($fields != "*")
        {
          $sql .= " dummy";
          $fields = explode (",", $fields);
          foreach ($fields as $id => $value)
          {
            $sql .= ",".$value;
          }
        }
          else
        {
          $sql .= " * ";
        }
        
        if ($where != "")
        {
          $sql .= " from $tablename where $where";
        }
          else
        {    
          $sql .= " from $tablename";
        }  

        $sql = str_replace ("dummy,", "", $sql);
        $qryfrom = $IMP->query ($sql);
        $nooffields = $IMP->num_fields ($qryfrom);
        for ($i = 1; $i <= $nooffields; $i++)
        {
          $fieldinfo[$i] = $IMP->field_info($qryfrom, $i);
        }
        $fieldvalues = "dummy";
        foreach ($fieldinfo as $id => $value)
        {
          $fieldvalues .= ",".strtolower ($value["name"]);
        }
        $fieldvalues = str_replace ("dummy,", "", $fieldvalues);
        if ($delete)
        {
          if ($this->debug)
          {
            echo "<pre style=\"color : green\">Deleting from $prefix.$tablename.$tblprefix on $this->dbpath ...\n</pre>";
          }
          $this->exec ("delete from ".$prefix.$tablename.$tblprefix."");
        }
        $counter=0;
        $count = 0;
        while ($row = $IMP->fetch_array ($qryfrom))
        {
           $counter++;
           $inputs = "dummy";
           foreach ($row as $id => $value)
           {

              for ($i =0; $i < strlen ($value); $i++)
              {
                if ((ord($value[$i]) < 32 || ord($value[$i]) > 126)) $value[$i] = "#";
              }
 
             if ($fieldinfo[$id+1]["type"] == "DATE")
             {
               $value = $this->tran_date($value, "m/d/Y");




             }
             
             $value = str_replace("'", "''", $value);
             
             if ($fieldinfo[$id+1]["type"] == "NUMERIC"||$fieldinfo[$id+1]["type"] == "NUMBER"||$fieldinfo[$id+1]["type"] == "INTEGER")
             {
                if ($value == "") $value = 0;
                $inputs .= ",".$value;
             }
               else
             {
               $value = trim($value);              
               if ($value != "null") 
               {
                $inputs .= ",'".$value."'";
               } 
               else $inputs .= ",".$value; //avoid putting nulls into quotes
             }
           }
           $inputs = str_replace ("dummy,", "", $inputs);
           $sqlinsert = "insert into ".$prefix.$tablename.$tblprefix." ($fieldvalues) values ($inputs)";

           if ($this->debug)
           {
             echo "<pre style=\"color : blue\"> $sqlinsert \n </pre>";
           }
           $this->exec ($sqlinsert);
           if ($counter >10000)
           {
             $count += 10000;
             if ($verbose) echo date("h:i:s")." records imported ... $count \n";
             $this->commit();
             $counter=0;
           }
        }
        $this->commit();
        if ($this->debug)
        {
          echo "<pre style=\"color : green\">Finished importing from ".$IMP->dbpath." to ".$this->dbpath."....\n</pre>";
        }
     }
/**************************************************************************************************************************
END: IMPORT
**************************************************************************************************************************/


     /* Returns back an array with the field names and types in */

     function field_names ($query)
     {
        $nooffields = $this->num_fields ($query);
        $fieldlist = "!0!";
 
        for ($i = 1; $i < $nooffields; $i++)
        {
          $fieldinfo[$i] = $this->field_info($query, $i);
          $fieldlist .= ",".$fieldinfo[$i]["name"]; 
        }
       
        $fieldlist = str_replace ("!0!,", "", $fieldlist);
        
        $fields["fieldinfo"] = $fieldinfo;
        $fields["fieldlist"] = $fieldlist;
  
        return $fields;
     }

/**************************************************************************************************************************
END: FIELD_NAMES
**************************************************************************************************************************/


     /* This creates a textual backup of your information and returns a zip file 

        Example of use:

        $tabledata["ledger"]["fields"] = "id, datum, dr, cr";
        $tabledata["ledger"]["where"] = "where companyid = 14";
        $tabledata["company"]["fields"] = "id, name ";
        $tabledata["company"]["where"] = "where id = 14";
 
       
        echo "<a href=\"".$CDE->backup($tables)."\" 
     */

     function backup ($tabledata, $filename="")
     {
        $fileout = "";
        foreach ($tabledata as $key => $data)
        {
           $sql = "select {$data["fields"]} from $key";
           
           if ($data["where"])
           {
             $sql .= " where ".$data["where"];
           }     
                      
           if ($this->debug) echo $this->debugmsg ($sql);
           
           $qry = $this->query ($sql);
         
           
           $fields = $this->field_names ($qry); 
           while ($row = $this->fetch_object($qry))
           {
              
             $fileline = "insert into $key ({$fields["fieldlist"]}) values (!0!";
             foreach ($fields["fieldinfo"] as $id => $value)
             {
               if ($this->debug)
               {
                 echo $this->debugmsg ($this->pre_r($value));  
               } 
               if ($value["type"] == "INTEGER")
               {
                 $fileline .= ",".$row->$value["name"]; 
               }
                 else
               if ($value["type"] == "DATE")
               {
                 $fileline .= ",'".$this->tran_date($row->$value["name"], "m/d/Y")."'";  
               }
                 else  
               if ($value["type"] == "TIMESTAMP")
               {
                 $fileline .= ",'".$this->tran_date($row->$value["name"], "m/d/Y h:i:s")."'";  
               } 
                 else
               {
                 $fileline .= ",'".$row->$value["name"]."'";  
               } 
                
             }   
             $fileline .=");\n";
             $fileline = str_replace ("!0!,", "", $fileline);
             if ($this->debug)
             {
               echo $this->debugmsg ($this->pre_r($fileline));  
             } 
               
             $fileout .= $fileline;            
               
           }     
        }     
        /* Output the gzip file */
       if ($this->debug)
       {
         echo $this->debugmsg ($this->pre_r($fileout));  
       } 
       if (function_exists ("gzopen"))
       {
         $rand = rand(1000000, 99999999);  
      
         if ($filename == "") $filename = $this->tmppath."$rand.gz";
         $zp = gzopen($filename, "w9");
         // write string to file
         gzwrite($zp, $fileout);
         // close file
         gzclose($zp);
         if ($this->debug)
         {
	   echo $this->debugmsg ("Created backup file $filename", "blue");  
         } 
       } 
         else
       {
         $filename = "none";  
         if ($this->debug)
         {
      	   echo $this->debugmsg ("Please make sure you have enable zlib module in PHP", "red");  
         } 
       } 
       return $filename; 
     }
/**************************************************************************************************************************
END: BACKUP
**************************************************************************************************************************/
      /* The restore function restores data as insert statements, so make sure when you do a restore you database is cleared */
     
     function restore ($filename)
     {
       $zp = gzopen($filename, "r");
       // output until end of the file and close it.
       $buffer = ""; 
       while (!gzeof($zp)) 
       {
         $buffer .= gzgetss($zp, 4096);
       }
       gzclose($zp);

       $explodesql = explode (";\n", $buffer); 
       //what are we going to do if the file gets really big ???
       
     
       foreach ($explodesql as $id => $sql)
       {
         if (trim($sql) != "")
         {
            if ($this->debug)
            {
      	      echo $this->debugmsg ("Executing $sql ..."); 
            }   
            $this->exec ($sql);
         } 
       }    
       $this->commit();
       if ($this->debug)
       {
         echo $this->debugmsg ("$filename successfully restored ...", "blue"); 
       }   

                 
     }

/**************************************************************************************************************************
END: RESTORE
**************************************************************************************************************************/

     function commit ()
     {
       if ($this->debug)
       {
         echo "<pre style=\"color : green\">Committing $this->dbpath </pre>";
       }

       switch ($this->dbtype)
       {
         /* Firebird Functionality */
         case "firebird" :
           return ibase_commit_ret ($this->dbh);
         break;

         /* SQLite Functionality */
         case "sqlite" :
           //doesn't support comitting
           return "Not Supported";
         break;
         /* Oracle Functionality */
         case "oracle" :
           return oci_commit($this->dbh);
         break;
       }
     }

/**************************************************************************************************************************
END: COMMIT
**************************************************************************************************************************/

/* for those who want the auto number from the database engine or generator for a table - it returns an integer */
     function genid ($tablename)
     {
       if ($this->debug)
       {
         echo "<pre style=\"color : green\">Getting a generator id from $this->dbpath <p style=\"color:purple;\"> $tablename </p></pre>";
       }

       switch ($this->dbtype)
       {
         /* Firebird Functionality */
         case "firebird" :
           //write some things here
           $id = ibase_gen_id (strtoupper("GEN_".$tablename."_ID"), 1, $this->dbh);
         break;

         /* SQLite Functionality */
         case "sqlite" :
           //See how to get an autonumber from a table
           
           $exist = $this->fetch("select name from sqlite_master where type='table' and upper(name) = upper('$tablename')");
           if ($exist->NAME == "")
           {
             $this->exec ("create table cde_gen (id integer not null, tablename varchar (100) not null, primary key (id, tablename))");  
           }
           
           $id = $this->fetch ("select max(id) as maxid from cde_gen where upper(tablename) = upper('$tablename')");
           
           if ($id->MAXID == "")
           {
             $newid = 0;
           }
             else
           {
             $newid = $id->MAXID;
           }  
           
           $newid++;
           
           $this->exec ("insert into cde_gen (id, tablename) values ($newid, upper('$tablename'))");
           $this->commit();
           
           $id = $newid;
         break;
         /* Oracle Functionality */
         case "oracle" :
           //See how to get an autonumber from a table
         break;

       }

       return $id;

       if ($this->debug)
       {
         echo "<pre style=\"color : blue\">Getting a generator $id \n </pre>";
       }

     }

/**************************************************************************************************************************
END: GENID
**************************************************************************************************************************/


/* I want the rows */
     function num_rows ($query)
     {
       if ($this->debug)
       {
         echo "<pre style=\"color : green\">Getting number of rows $this->dbpath <p style=\"color:purple;\"> $query </p></pre>";
       }
       $noofrows = 0;
       //Validate the sql statement and make adjustments
       switch ($this->dbtype)
       {
         /* Firebird Functionality */
         case "firebird" :
           //write some things here
           $icount = 0;
           while ($row = ibase_fetch_object ($query))
           {
             $icount++;
           }
           $noofrows = $icount;
         break;
         /* SQLite Functionality */
         case "sqlite" :
           putenv("TMP=".$this->tmppath);
           $noofrows = sqlite_num_rows ($query);
         break;
          /*DBASE functionality */
         case "dbase" :
           $noofrows = dbase_numrecords($this->dbh);
         break;
         /* MYSQL Functionality */
         case "mysql" :
           $noofrows = mysql_num_rows ($query);
         break;
         /* Oracle Functionality */
         case "oracle" :
           $noofrows = oci_num_rows($query);
         break;
      	/* PGSQL Functionality */
      	 case "pgsql":
      	 	$noofrows = pg_num_rows($query);
      	 break;
       }
       if ($this->debug)
       {
         echo "<pre style=\"color : blue\">Number of rows $noofrows \n </pre>";
       }
       return $noofrows;
     }
/**************************************************************************************************************************
END: NUM_ROWS
**************************************************************************************************************************/
     /* I want the fields */
     function num_fields ($query)
     {
       if ($this->debug)
       {
         echo "<pre style=\"color : green\">Getting number of fields $this->dbpath <p style=\"color:purple;\">  $query </p></pre>";
       }
       $nooffields = 0;
       //Validate the sql statement and make adjustments
       switch ($this->dbtype)
       {
         /* Firebird Functionality */
         case "firebird" :
           //write some things here
           $nooffields = ibase_num_fields ($query);
         break;
         /* SQLite Functionality */
         case "sqlite" :
           putenv("TMP=".$this->tmppath);
           $nooffields = sqlite_num_fields ($query);
         break;
         /*DBASE functionality */
         case "dbase" :
           $nooffields = dbase_numfields($this->dbh);
         break;
         /* MYSQL Functionality */
         case "mysql" :
           $nooffields = mysql_num_fields ($query);
         break;
         /* Oracle Functionality */
         case "oracle" :
           $nooffields = oci_num_fields($query);
         break;
      	/* PGSQL Functionality */
      	 case "pgsql":
      	 	$nooffields = pg_num_fields($query);
      	 break;
       }
       if ($this->debug)
       {
         echo "<pre style=\"color : blue\">Number of fields $nooffields \n </pre>";
       }
       return $nooffields;
     }
/**************************************************************************************************************************
END: NUM_FIELDS
**************************************************************************************************************************/
     /*
     $col_info = ibase_field_info($rs, $i);
       echo "name: ". $col_info['name']. "\n";
       echo "alias: ". $col_info['alias']. "\n";
       echo "relation: ". $col_info['relation']. "\n";
       echo "length: ". $col_info['length']. "\n";
       echo "type: ". $col_info['type']. "\n";
     */
     function field_info ($query, $count)
     {
       if ($this->debug)
       {
         echo "<pre style=\"color : green\">Getting column information $this->dbpath <p style=\"color:purple;\">  $query </p></pre>";
       }
       $nooffields = 0;
       //Validate the sql statement and make adjustments
       switch ($this->dbtype)
       {
         /* Firebird Functionality */
         case "firebird" :
           //write some things here
           $col_info = ibase_field_info ($query, $count);
         break;
         /* SQLite Functionality */
         case "sqlite" :
           putenv("TMP=".$this->tmppath);
           $name= sqlite_field_name ($query, $count);
           //echo $name;
           $col_info["alias"] = $name;
           $col_info["name"] = $name;
         break;
         /* Oracle Functionality */
         case "oracle" :
           $column_name  = oci_field_name($query, $count);
           $column_type  = oci_field_type($query, $count);
           $column_size  = oci_field_size($query, $count);
           $column_prec  = oci_field_precision ($query, $count);
           $column_scale  = oci_field_scale($query, $count);
           $col_info["name"] = $column_name;
           $col_info["alias"] = $column_name;
           $col_info["length"] = $column_size;
           $col_info["prec"] = $column_prec;
           $col_info["type"] = $column_type;
           $col_info["scale"] = $column_scale;
         break;
        	/* PGSQL Functionality */
        	case "pgsql":
        	 $col_info["name"] = pg_field_name($query,$count);
         	 $col_info["alias"] = NULL; // always set to NULL
        	 $col_info["relation"] = NULL; // always set to NULL
        	 $col_info["length"] = pg_field_size($query,$count);
        	 $col_info["type"] = pg_field_type($query,$count);
        	break;
       }
       if ($this->debug)
       {
         echo "<pre style=\"color : blue\">Column Info fetched for Column $count \n </pre>";
       }
       return $col_info;
     }

/**************************************************************************************************************************
END: FIELD_INFO
**************************************************************************************************************************/
    
     /* reset_query the default sql + query to make sure you fetch rows from scratch for next_row
     */

     function reset_query ()
     {
       $this->lastsql = "";
       $this->lastqry = null;
       return true; 
     }

/**************************************************************************************************************************
END: RESET_QUERY
**************************************************************************************************************************/

/* Get the first row  of an sql statement either as an array or an object 
       $type : 0 = object, 1 = array
     */

     function first_row ($sql, $type=0)
     {
       if ($this->debug)
       {
         echo "<pre style=\"color : green\">Running  $sql as type $type....\n</pre>";
       }

       $this->lastqry = $this->query ($sql);
       
       if ($type == 0)
       {
         return $this->fetch_object($this->lastqry);
       }
         else
       {
         return $this->fetch_array($this->lastqry);
       }            

       if ($this->debug)
       {
         echo "<pre style=\"color : blue\">Fetched first row of $sql as type $type database....\n </pre>";
       }
     }
     
/**************************************************************************************************************************
END: FIRST_ROW
**************************************************************************************************************************/


/* Get the next row  of an sql statement either as an array or an object 
       $type : 0 = object, 1 = array
     */

     function next_row ($sql, $type=0)
     {
       if ($this->debug)
       {
         echo "<pre style=\"color : green\">Getting next row for  $sql as type $type....\n</pre>";
       }

       //check if the query was already created & test the sql statement - if it does not match - create a new query 
       if (!$this->lastqry || $this->lastsql != $sql)
       { 
         $this->lastqry = $this->query ($sql);
       }  
       
       if ($type == 0)
       {
         $return = $this->fetch_object($this->lastqry);
       }
         else
       {
         $return = $this->fetch_array($this->lastqry);
       }            
           
       if (!$return)
       {
         unset($this->lastqry);
       }    
         else
       {
         return $return;
       }  

       if ($this->debug)
       {
         echo "<pre style=\"color : blue\">Fetched next row of $sql as type $type database....\n </pre>";
       }
     }
    
/**************************************************************************************************************************
END: NEXT_ROW
**************************************************************************************************************************/

} 
/**************************************************************************************************************************
END CDE CLASS
**************************************************************************************************************************/

