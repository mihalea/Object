<?php

class Login {
	
	public $errors = array();

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
			
			if($connection->connect_error) {
				$this->errors[] = $connection->connect_error;
				return;
			}
				
			$stmt = $connection->prepare("SELECT user_id, username, email, password
										  FROM members
										  WHERE username = ? OR email = ?
										  LIMIT 1;");
			if(false === $stmt)
			{
				$this->errors[] = "Failed to prepare statement at login: " . $connection->connect_error;
				return;
			}
			
			
			$uname = $_POST["u"];
			
			$ok = $stmt->bind_param("ss", $uname, $uname);
			if(false === $ok)
			{
				$this->errors[] = "Failed to bind params at login";
				return;
			}
			
			$ok = $stmt->execute();
			if(false === $ok)
			{
				$this->errors[] = "Failed to execute at login";
				return;
			}
			
			$ok = $stmt->bind_result($id, $username, $email, $password);
			if(false === $ok)
			{
				$this->errors[] = "Failed to bind result at login";
				return;
			}
			
			$ok = $stmt->fetch();
			if(false === $ok)
			{
				$this->errors[] = "Failed to fetch at login";
				return;
			}
			
			$stmt->close();
			$connection->close();
			
			if(password_verify($_POST["p"], $password))
			{
				$_SESSION["uname"] = $username;
				$_SESSION["email"] = $email;
				$_SESSION["id"] = $id;
				$_SESSION["isLogged"] = 1;
				header('Location: index.php');
			}
			else
				header('Location: index.php?error');
		}
	}
	
	public function logout()
	{
		$_SESSION= array();
		session_destroy();
		header('Location: index.php');
	}
	
	public function isLogged()
	{
		if(isset($_SESSION["isLogged"]) AND $_SESSION["isLogged"] = 1)
			return true;
			
		return false;
	}
}