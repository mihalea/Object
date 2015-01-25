<?php

require_once("../config/db.php");
$path = realpath($_SERVER["DOCUMENT_ROOT"] . "/test/") . "/"; require_once($path . "config/site.php");

class Register {

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
		if (empty($_POST["user"]))
			$this->errors[] = "Empty username.";
		elseif (empty($_POST["email"]))
			$this->errros[] = "Empty email.";
		elseif(empty($_POST["name"]))
			$this->errros[] = "Empty name.";
		elseif (empty($_POST["pass"]) || empty($_POST["repeat"]))
			$this->errors[] = "Empty password.";
		elseif ($_POST["pass"] !== $_POST["repeat"])
			$this->errors[] = "Passwords do not match.";
		elseif (strlen($_POST["user"]) < 6 || strlen($_POST["user"]) > 64)
			$this->errors[] = "Username must be between 6 and 64 characters.";
		elseif (strlen($_POST["email"]) < 6 || strlen($_POST["email"]) > 64)
			$this->errors[] = "Email must be between 6 and 64 characters.";
		elseif (strlen($_POST["pass"]) < 6 || strlen($_POST["pass"] > 64))
			$this->errors[] = "Password must be between 6 and 64 characters.";
		elseif (!preg_match('/^[a-z\d]{2,64}$/i', $_POST["user"]))
			$this->errors[] = "Username must contain only a-Z and numbers";
		elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))
			$this->errors[] = "Invalid email address";
		else {
			$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

			if($connection->connect_error)
				$this->errors[] = "Failed to connect to database at register: " . $connection->connect_error;
			else {
				$stmt = $connection->prepare("INSERT INTO members (username, email, name, password)
											  VALUES (?, ?, ?, ?);");
				if(false === $stmt)
					$this->errors[] = "Failed to prepare at register: " . $connection->connect_error;
				else {
					$username = $_POST["user"];
					$email	  = $_POST["email"];
					$name	  = $_POST["name"];
					$password = password_hash($_POST["pass"], PASSWORD_DEFAULT);
					
					if($_POST["pass"] != $_POST["repeat"])
						$this->messages[] = "Passwords do not match";
					else {
						$ok = $stmt->bind_param("ssss", $username, $email, $name, $password);	
						if(false === $ok)
							$this->errors[] = "Failed to bind params at register";
						else {
							$this->isRegistered = $stmt->execute();
						}
					}
				}
				
				$stmt->close();
				$connection->close();
			}
			
			$url = "Location:" . SITE_ROOT;
			if(false === $this->isRegistered) {
				header($url . "register?error");
			} else {
				header($url . "?register");
			}
										  
		}
		
		return false;
	}
		
	public function isRegistered()
	{
		return $isRegistered;
	}
		
}
