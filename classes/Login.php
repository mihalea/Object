<?php
	$path = realpath($_SERVER["DOCUMENT_ROOT"] . "/test/config/site.php");
	require_once($path);
	
	class Login {
		
		public $errors = array();
		
		public function __construct()
		{
			session_start();
			
			if(isset($_POST["login"])) {
				if ( $this->login() == true )
					header("Location: " . SITE_ROOT);
				else
					header("Location: " . SITE_ROOT . "?error");
			}
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
					return false;
				}
				
				$stmt = $connection->prepare("SELECT user_id, username, name, email, password
				FROM members
				WHERE username = ? OR email = ?
				LIMIT 1;");
				if(false === $stmt)
				{
					$this->errors[] = "Failed to prepare statement at login: " . $connection->connect_error;
					return false;
				}
				
				
				$user = $_POST["u"];
				
				$ok = $stmt->bind_param("ss", $user, $user);
				if(false === $ok)
				{
					$this->errors[] = "Failed to bind params at login";
					return false;
				}
				
				$ok = $stmt->execute();
				if(false === $ok)
				{
					$this->errors[] = "Failed to execute at login";
					return false;
				}
				
				$ok = $stmt->bind_result($id, $username, $name, $email, $password);
				if(false === $ok)
				{
					$this->errors[] = "Failed to bind result at login";
					return false;
				}
				
				$ok = $stmt->fetch();
				if(false === $ok)
				{
					$this->errors[] = "Failed to fetch at login";
					return false;
				}
				
				$stmt->close();
				$connection->close();
				
				if(password_verify($_POST["p"], $password))
				{
					$_SESSION["user"] = $username;
					$_SESSION["email"] = $email;
					$_SESSION["name"] = $name;
					$_SESSION["id"] = $id;
					$_SESSION["isLogged"] = 1;
					return true;
				}
			}
			
			return false;
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