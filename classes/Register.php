<?php

require_once("config/db.php");

class Register {

	private $connection = null;
	public $errors = array();
	public $messages = array();
	private $isRegistered = false;
	
	public function __construct()
	{
		if(isset($_POST["register"]))
			$this->register();
	}
	
	private function register()
	{
		if (empty($_POST["uname"]))
			$errors[] = "Empty username.";
		elseif (empty($_POST["email"]))
			$errros[] = "Empty email.";
		elseif (empty($_POST["pass"]) || empty($_POST["repeat"]))
			$errors[] = "Empty password.";
		elseif ($_POST["pass"] !== $_POST["repeat"])
			$errors[] = "Passwords do not match.";
		elseif (strlen($_POST["uname"]) < 6 || strlen($_POST["uname"]) > 64)
			$errors[] = "Username must be between 6 and 64 characters.";
		elseif (strlen($_POST["email"]) < 6 || strlen($_POST["email"]) > 64)
			$errors[] = "Email must be between 6 and 64 characters.";
		elseif (strlen($_POST["pass"]) < 6 || strlen($_POST["pass"] > 64))
			$errors[] = "Password must be between 6 and 64 characters.";
		elseif (!preg_match('/^[a-z\d]{2,64}$/i', $_POST["uname"]))
			$errors[] = "Username must contain only a-Z and numbers";
		elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))
			$errors[] = "Invalid email address";
		else {
			$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
			
			
			
			if($connection->connect_errno)
				die("Connection failed");
			
			$stmt = $connection->prepare("INSERT INTO members (username, email, password)
										  VALUES (?, ?, ?);");
			if(false === $stmt)
				die("prepare() failed");
				
				
			$username = $_POST["uname"];
			$email	  = $_POST["email"];
			$password = password_hash($_POST["pass"], PASSWORD_DEFAULT);
			
			$code = $stmt->bind_param("sss", $username, $email, $password);	
			if(false === $code)
				die("bind_param() failed");
			
			
			$isRegistered = $stmt->execute();
			if(false === $isRegistered)
				die("execute() failed");
				
			$stmt->close();
			$connection->close();
										  
		}
	}
		
	public function isRegistered()
	{
		return $isRegistered;
	}
		
}
