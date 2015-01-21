<?php
	$path = realpath($_SERVER["DOCUMENT_ROOT"] . "/test/config/site.php");
	require_once($path);
	
	require_once("../classes/Permissions.php");
	
	class GroupManager {
		
		public $errors = array();
		public $conn;
		
		public function __construct()
		{
			$url = "Location: " . SITE_ROOT . "groups?";
			
			if(isset($_SESSION["group_id"]) AND !empty($_SESSION["group_id"]) AND empty($_GET["setid"]))
				$url = $url . "id=" . $_SESSION["group_id"];
				
			$this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
			if($this->conn->connect_error) {
				die("Connection failed!: " . $conn->connect_error);
				return false;
			}
			
			if(isset($_POST["newGroup"])) {
				if ($this->createGroup() == true) {
					header($url);
					} else {
					header($url . "&error=create");
				}
			} elseif (isset($_POST["newPost"])) {
				if($this->createPost() == true) {
					header($url);
					} else {
					header($url . "&error=post");
				}
			} elseif (isset($_GET["setid"])) {
				if($this->setID() == true) {
					header($url);
					} else {
					header($url . "select&error=set");
				}
			}
		}
		
		public function createGroup()
		{
			if(!isset($_POST["name"]) OR empty($_POST["name"])) {
				$this->errors[] = "Empty post var: name";
				} elseif(!isset($_SESSION["id"]) OR empty($_SESSION["id"])) {
				$this->errors[] = "Empty or not set user id";
				} else {
				
				$stmt = $this->conn->prepare("INSERT INTO groups (name, user_id) VALUES(?, ?);");
				if(false === $stmt) {
					$this->errors[] = "Prepare failed " . $conn->error;
					return false;
				}
				
				$name = $_POST["name"];
				$user_id = $_SESSION["id"];
				
				$ok = $stmt->bind_param("si", $name, $user_id);
				if(false === $ok) {
					$this->errors[] = "bind_param() failed";
					return false;
				}
				
				$ok = $stmt->execute();
				if(false === $ok) {
					$this->errors[] = "execute() failed";
					return false;
				}
				$stmt->close();
				
				$stmt = $conn->prepare("INSERT INTO membership (user_id, group_id, flag)
				VALUES (?, LAST_INSERT_ID(), ?);");
				if(false === $stmt) {
					$this->errors[] = "Failed to connect to db: " . $conn->connect_error;
					return false;
				}
				
				$flag = "a";
				$ok = $stmt->bind_param("is", $user_id, $flag);
				if(false === $ok) {
					$this->errors[] = "Failed to prepare statement";
					return false;
				}
				
				$ok = $stmt->execute();
				if(false === $ok) {
					$this->errors[] = "Failed to execute";
					return false;
				}
				$stmt->close();
				
				return true;
				
			}
			
			return false;
		}
		
		public function createPost()
		{
			if(!isset($_POST["post"]) OR empty($_POST["post"])) {
				$this->errors[] = "Empty post var: text";
				} elseif(!isset($_GET["id"]) OR empty($_GET["id"])) {
				$this->errors[] = "Group not selected";
				} elseif(!isset($_SESSION["id"]) OR empty($_SESSION["id"])) {
				$this->errors[] = "Empty or not set user id";
				} else {
				
				$stmt = $this->conn->prepare("INSERT INTO g_posts (group_id, user_id, text, date) 
				VALUES(?, ?, ?, NOW());");
				if(false === $stmt) {
					$this->errors[] = "Prepare failed " . $conn->error;
					return false;
				}
				
				$group_id = $_GET["id"];
				$user_id = $_SESSION["id"];
				$text = $_POST["post"];
				
				$ok = $stmt->bind_param("iis", $group_id, $user_id, $text);
				if(false === $ok) {
					$this->errors[] = "bind_param() failed";
					return false;
				}
				
				$ok = $stmt->execute();
				if(false === $ok) {
					$this->errors[] = "execute() failed";
					return false;
				}
				$stmt->close();
				
				return true;
				
			}
			
			return false;
		}
		
		private function setID()
		{
			if(empty($_GET["setid"]))
				die("Empty id");
			else
			{		
				
				if(hasGroupFlag('u', $_GET["setid"]) == false)
					return false;
					
				$query = "SELECT name, schedule_id
				FROM groups
				WHERE group_id = ?
				LIMIT 1;";
				$stmt = $this->conn->prepare($query);
				if(false === $stmt)
				die("Prepare failed");
				
				$ok = $stmt->bind_param("i", $_GET["setid"]);
				if(false === $ok)
				die("bind_param failed");
				
				$ok = $stmt->execute();
				if(false === $ok)
				die("Execute failed");
				
				$ok = $stmt->bind_result($name, $schedule_id);
				if(false === $ok)
				die("bind_result failed");
				
				$ok = $stmt->fetch();
				if(false === $ok)
				die("Execute failed");
				
				$_SESSION["group_id"] = $_GET["setid"];
				$_SESSION["group_name"] = $name;
				$_SESSION["schedule_id"] = $schedule_id;
				
				return true;
			}
			
			return false;
		}
		
		public static function getPosts() {
			if(!empty($_SESSION["group_id"])) {
				$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
			
				$query = "SELECT g_posts.user_id, members.username, g_posts.text, g_posts.date
				FROM g_posts
				INNER JOIN members ON g_posts.user_id = members.user_id
				WHERE group_id = ?
				ORDER BY g_posts.date DESC;";
				$stmt = $conn->prepare($query);
				if(false === $stmt)
				die("Prepare failed");
				
				$ok = $stmt->bind_param("i", $_SESSION["group_id"]);
				if(false === $ok)
				die("bind_param failed");
				
				$ok = $stmt->execute();
				if(false === $ok)
				die("Execute failed");
				
				$ok = $stmt->bind_result($user_id, $username, $text, $date);
				if(false === $ok)
				die("bind_result failed");
				
				$posts = array();
				while($stmt->fetch())
				{
					$posts[] = new Post(new User($user_id, $username), $text, $date);
				}
				
				$stmt->close();
				$conn->close();
				
				return $posts;
			}
		}
		
		public static function getMembers() {
			if(!empty($_SESSION["group_id"])) {
				$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
			
				$query = "SELECT membership.user_id, members.name
				FROM membership
				INNER JOIN members ON membership.user_id = members.user_id
				WHERE group_id = ?;";
				$stmt = $conn->prepare($query);
				if(false === $stmt)
				die("Prepare failed");
				
				$ok = $stmt->bind_param("i", $_SESSION["group_id"]);
				if(false === $ok)
				die("bind_param failed");
				
				$ok = $stmt->execute();
				if(false === $ok)
				die("Execute failed");
				
				$ok = $stmt->bind_result($user_id, $name);
				if(false === $ok)
				die("bind_result failed");
				
				$members = array();
				while($stmt->fetch())
				{
					$members[] = new User($user_id, $name);
				}
				
				$stmt->close();
				$conn->close();
				
				return $members;
			}
		}
		
		public static function printGroups()
		{		
				if(!empty($_SESSION["id"])) {
				$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
			
				$query = "SELECT groups.name, groups.group_id, membership.flag
				FROM groups
				INNER JOIN membership ON groups.group_id = membership.group_id
				WHERE membership.user_id = ?";
				
				$stmt = $conn->prepare($query);
				
				if(false === $stmt)
				die("prepare() failed");
				
				$user_id = $_SESSION["id"];
				$code = $stmt->bind_param("i", $user_id);
				if(false === $code)
				die("bind_param() failed");
				
				
				$rows = array();			
				$success = $stmt->execute();
				if(false === $success)
				die("execute() failed");
				else
				{
					$success = $stmt->bind_result($name, $group_id, $flag);
					
					if(false === $success)
					die("bind_result() failed");
					
					echo '<div class="list-group">';
					while($stmt->fetch())
					{
						echo '<a href="groups?setid=' . $group_id . '" class="list-group-item">';
						echo $name;		
						
						if($flag == 'a')
						echo '<span class="glyphicon glyphicon-tasks pull-right"></span>';				
						echo '</a>';
						
					}
					echo '</div>';
				}
				
				$stmt->close();
				$conn->close();
			}
		}
		
		public static function getSubjects()
		{
			$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
			
			$query = "SELECT subject_id, name FROM subjects ORDER BY name";
			$stmt = $conn->prepare($query);
			if(false === $stmt)
			die("Prepare failed");
			
			$ok = $stmt->execute();
			if(false === $ok)
			die("Execute failed");
			
			$ok = $stmt->bind_result($subject_id, $name);
			if(false === $ok)
			die("bind_result failed");
			
			$subjects = array();
			while($stmt->fetch())
			{
				$subjects[] = array("id" => $subject_id, "name" => $name);
			}
			
			$stmt->close();
			$conn->close();
			
			return $subjects;
		}
	}
	
	class User {
		public $name;
		public $id;
		
		public function __construct($id, $name) {
			$this->name = $name;
			$this->id = $id;
		}
	}
	
	class Post {
		public $user;
		public $text;
		public $datetime;
		
		public function __construct($user, $text, $datetime) {
			$this->user = $user;
			$this->text = $text;
			$this->datetime = $datetime;
		}
		
		public function timeDifference() {
			$timeNow = time() + (2 * 60 * 60);
			$timePost = strtotime($this->datetime);
			
			
			$time = $timeNow - $timePost;
			
			$time = $time / 60; #minutes
			if( $time < 60 ) {
				$time = round($time);
				
				if($time == 1)
				return $time . ' minute ago';
				else
				return $time . ' minutes ago';
				} else {
				$time = $time / 60;
				if ( $time < 24 ) {
					$time = round($time);
					if($time == 1)
					return $time . ' hour ago';
					else
					return $time . ' hours ago';
					} else {
					$time = round($time / 24);
					if($time == 1)
					return $time . ' day ago';
					else
					return $time . ' days ago';
				}
			}
		}
	}
	
?>