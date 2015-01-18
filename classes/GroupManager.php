<?php
	
	require_once("config/db.php");
	
	class GroupManager {
		
		public $errors = array();
		
		public function __construct()
		{
			$url = "groups.php?";
			if(isset($_GET["id"]) && !empty($_GET["id"]))
				$url = $url . "id=" . $_GET["id"];
		
			if(isset($_POST["newGroup"])) {
				if ($this->createGroup() == true) {
					header("Location: " . $url);
					} else {
					header("Location: " . $url . "&error=create");
				}
			} elseif (isset($_POST["newPost"])) {
				if($this->createPost() == true) {
					header("Location: " . $url);
				} else {
					#header("Location: " . $url . "&error=post");
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
				$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
				if($conn->connect_error) {
					$this->errors[] = "Connection failed!: " . $conn->connect_error;
					return false;
				}
				
				$stmt = $conn->prepare("INSERT INTO groups (name, user_id) VALUES(?, ?);");
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
				$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
				if($conn->connect_error) {
					$this->errors[] = "Connection failed!: " . $conn->connect_error;
					return false;
				}
				
				$stmt = $conn->prepare("INSERT INTO gposts (group_id, user_id, text, date) 
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
				
				return true;
				
			}
			
			return false;
		}
		
		public function getGroups()
		{
			$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
			
			if($conn->connect_errno)
			die("Connection failed");
			
			$user_id = $_SESSION["id"];
			
			$query = "SELECT groups.name, groups.group_id, membership.flag
			FROM groups
			INNER JOIN membership ON groups.group_id = membership.group_id
			WHERE membership.user_id = ?";
			
			$stmt = $conn->prepare($query);
			
			if(false === $stmt)
			die("prepare() failed");
			
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
					echo '<a href="groups.php?id=' . $group_id . '" class="list-group-item">';
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
		
		public function getID()
		{
			return $this->id;
		}
	}
	
	class Group {
		private $id;
		private $name;
		private $members = array();
		private $posts = array();
		
		public function __construct($id)
		{
			$this->id = $id;
			$this->getData();
		}
		
		private function getData()
		{
			if(empty($this->id))
			die("Empty id");
			else
			{
				$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
				
				if($conn->connect_errno)
				die("Failed to connect to database");
				
				$this->nameQuery($conn);
				$this->memberQuery($conn);
				$this->postQuery($conn);
			}
		}
		
		private function nameQuery($conn) {
			$query = "SELECT name
			FROM groups
			WHERE group_id = ?
			LIMIT 1;";
			$stmt = $conn->prepare($query);
			if(false === $stmt)
			die("Prepare failed");
			
			$ok = $stmt->bind_param("i", $this->id);
			if(false === $ok)
			die("bind_param failed");
			
			$ok = $stmt->execute();
			if(false === $ok)
			die("Execute failed");
			
			$ok = $stmt->bind_result($name);
			if(false === $ok)
			die("bind_result failed");
			
			$ok = $stmt->fetch();
			if(false === $ok)
			die("Execute failed");
			
			$this->name = $name;
		}
		
		private function memberQuery($conn) {
			$query = "SELECT membership.user_id, members.username
			FROM membership
			INNER JOIN members ON membership.user_id = members.user_id
			WHERE group_id = ?;";
			$stmt = $conn->prepare($query);
			if(false === $stmt)
			die("Prepare failed");
			
			$ok = $stmt->bind_param("i", $this->id);
			if(false === $ok)
			die("bind_param failed");
			
			$ok = $stmt->execute();
			if(false === $ok)
			die("Execute failed");
			
			$ok = $stmt->bind_result($user_id, $name);
			if(false === $ok)
			die("bind_result failed");
			
			
			while($stmt->fetch())
			{
				$this->members[] = new User($user_id, $name);
			}
		}
		
		private function postQuery($conn) {
			$query = "SELECT gposts.user_id, members.username, gposts.text, gposts.date
			FROM gposts
			INNER JOIN members ON gposts.user_id = members.user_id
			WHERE group_id = ?
			ORDER BY gposts.date DESC;";
			$stmt = $conn->prepare($query);
			if(false === $stmt)
				die("Prepare failed");
			
			$ok = $stmt->bind_param("i", $this->id);
			if(false === $ok)
				die("bind_param failed");
			
			$ok = $stmt->execute();
			if(false === $ok)
				die("Execute failed");
			
			$ok = $stmt->bind_result($user_id, $username, $text, $date);
			if(false === $ok)
				die("bind_result failed");
			
			while($stmt->fetch())
			{
				$this->posts[] = new Post(new User($user_id, $username), $text, $date);
			}
		}
		
		public function getRandomMembers($limit) {
			if($limit > count($this->members))
			$limit = count($this->members);
			$keys = array_rand($this->members, $limit);
			
			$random = array();
			
			if(is_array($keys) AND count($keys) > 0) {
				foreach($keys as $key)
				$random[] = $this->members[$key];
				} else {
				$random[] = $this->members[$keys];
			}
			
			return $random;
		}
		
		public function getID() { return $this->id; }
		public function getName() { return $this->name; }
		public function getPosts() { return $this->posts; }
		
		
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
					if($days == 1)
						return $time . ' day ago';
					else
						return $time . ' days ago';
				}
			}
		}
	}
	
?>