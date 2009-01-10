<?php
/*
Created by Anirban Bhattacherya
email: anirbanbhattacherya@gmail.com 
Student Information & Management System
*/

require_once "dal/db.cls.php";
$db = new db();

//Login Class
class Login {
		
		// Custom Error Message for a field left blank 
		const ERROR_EMPTY_LOGIN = "Please fill in all fields!";
		
		// Custom Error Message for an invalid login
		const ERROR_VALIDATE_LOGIN = "Username or password doesn't match!";
		
		// Custom Error Message when a user has 5 or more invalid logins 
		const ERROR_BANNED_LOGIN = "Sorry, you are not authorized to access this page!";
		
		// The username of a member 
		private $username;
		
		// The password of a member 
		private $password;	
		
		//User Name
		private $name = '';
		
		//Authorization
		private $auth = 0;
		
		//User Type
		private $userid = 0;
		
		var $msg;

		// Return the username of a member
		public function getUsername() {
			return $this->username;
		}
	
		// Return the plain text password of a member 
		public function getPassword() {
			return $this->password;
		}
			
		// Return the encrypted password of a member 
		public function getEncryptedPassword() {
			return sha1($this->password);
		}	
		
		// Get a member's IP Address 
		public function getUserIP() {
			return getenv("REMOTE_ADDR");
		}
	
		// Validate an email is in the correct format e.g. someone@somewhere.com 
		public function validateEmail($email) {
			if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				return false;
			}
			return true;
		}
		
		
		// Verify the session login.
		public function sessionVerify() 
		{	
			if($_SESSION['userid']!=0){
			if($_SESSION['userid']!=1 || $_SESSION['userid'] != 2 || $_SESSION['userid'] != 3 ||$_SESSION['userid'] != 9 )
			{
				echo "ani";
				exit();
				if(validateEmail($this->username) == false)
				{
				clearall();
				return false;
				}	
			}
			
			session_regenerate_id();
			$_SESSION['auth'] = $this->auth;
			$_SESSION['username'] = $this->username;
			$_SESSION['name'] = $this->name;
			$_SESSION['userid'] = $this->userid;
			return true;
			}
		}
		
		// Checks if the Session data is correct before continuing
		public function verifyAccess() {
			if(isset($_SESSION['name']) && $_SESSION['auth'] != 0) {
				return true;
			}
			else
			return false;
		}
		
		
		//Login
		public function checkLogin($username, $password) {
			$this->username = $username;
			if(empty($username) || empty($password)) {
				//throw new Exception(Login::ERROR_EMPTY_LOGIN);
				$this->msg = "Please fill in all fields!";
			}	
			else {
			$sql = "SELECT * FROM users WHERE username = '".$username."' AND password = '".$password."'"; 											
			global $db;				
			$result = $db->getDataRow($sql);	
		
				if ($result) {
					$this->auth = $result[3];
					$this->username = $result[1];	
					//$this->name = $result['LNAME']." ".$result['FNAME'];
					$this->userid = $result[0];
					
					return $this->sessionVerify();
					//return true;
				}
				else {
					$ip = $this->getUserIP();			
					//ExecuteQuery("INSERT INTO hack_attempt(username, IP) VALUES('$ip')");
					//throw new Exception(Login::ERROR_VALIDATE_LOGIN);
					$this->msg = "Unauthorized Access";
				}
			}	
			$this->clearall();
			return false;
						
		}
	
		public function clearall()
		{
			unset($username);
			unset($password);
			unset($name);
			unset($userid);		
		}

	
//Class End
}

?>