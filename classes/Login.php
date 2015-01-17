<?php

require_once("config/db.php");

class Login {
	
	private $connection = null;
	private $errors = array();

	public function __construct()
	{
		session_start();
		
		if(isset($_POST["login"]))
			$this->login();
		elseif(isset($_GET["logout"]))
			$this->logout();
	}
	
	public function login()
	{
		if(!isset($_POST["u"]))
			$this->errors[] = "Username field was empty";
		elseif(!isset($_POST["p"]))
			$this->errors[] = "Password field was empty";
		elseif(!empty($_POST["u"]) && !empty($_POST["p"]))
		{
			$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
			
			if($connection->connect_error) 
				die("Connection failed!");
				
			$stmt = $connection->prepare("SELECT user_id, username, email, password
										  FROM members
										  WHERE username = ? OR email = ?
										  LIMIT 1;");
			
			$uname = $_POST["u"];
			
			$stmt->bind_param("ii", $uname, $uname);
			$stmt->execute();
			
			$stmt->bind_result($id, $username, $email, $password);
			$stmt->fetch();
			
			if(password_verify($_POST["p"], $password))
			{
				$_SESSION["uname"] = $username;
				$_SESSION["email"] = $email;
				$_SESSION["id"] = $id;
				$_SESSION["isLogged"] = 1;
			}
			
			
			$stmt->close();
			$connection->close();
		}
	}
	
	public function logout()
	{
		$_SESSION= array();
		session_destroy();
	}
	
	public function isLogged()
	{
		if(isset($_SESSION["isLogged"]) AND $_SESSION["isLogged"] = 1)
			return true;
			
		return false;
	}
}