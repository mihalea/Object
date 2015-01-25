<?php
	$path = realpath($_SERVER["DOCUMENT_ROOT"] . "/test/") . "/";
	
	require_once($path . "config/site.php");
	require_once($path . "classes/Permissions.php");
	
	class GroupManager {
		
		public $errors = array();
		public $conn;
		
		public function __construct()
		{
			$url = "Location: " . SITE_ROOT . "groups";
				
			$this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
			if($this->conn->connect_error) {
				die("Connection failed!: " . $conn->connect_error);
				return false;
			}
			
			if(isset($_POST["newGroup"])) {
				if ($this->createGroup() == true) {
					header($url);
					} else {
					header($url . "?error=create");
				}
			} elseif (isset($_POST["newPost"])) {
				if($this->createPost() == true) {
					header($url);
					} else {
					header($url . "?error=post");
				}
			} elseif(isset($_POST["comment"])) {
				if($this->createComment() == true) {
					#header($url);
					} else {
					header($url . "?error=comment");
				}
			} elseif (isset($_GET["setid"])) {
				if($this->setID() == true) {
					header($url);
					} else {
					header($url . "?select&error=set");
				}
			} elseif (isset($_POST["newUser"])) {
				if($this->addUser() == true) {
					header($url . '/members.php');
					} else {
					header($url . "/members.php/?error=user");
				}
			} elseif (isset($_POST["remUser"])) {
				if($this->remUser() == true) {
					header($url);
					} else {
					header($url . "?error=delUser");
				}
			} elseif (isset($_POST["makeAdmin"])) {
				if($this->makeAdmin() == true) {
					header($url);
					} else {
					header($url . "?error=delUser");
				}
			} elseif (isset($_POST["makeUser"])) {
				if($this->makeUser() == true) {
					header($url);
					} else {
					header($url . "?error=delUser");
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
				
				$flag = "o";
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
			if(empty($_POST["post"])) {
				$this->errors[] = "Empty post var: text";
				} elseif(empty($_SESSION["group_id"])) {
				$this->errors[] = "Group not selected";
				} elseif(empty($_SESSION["id"])) {
				$this->errors[] = "Empty or not set user id";
				} else {
				
				$stmt = $this->conn->prepare("INSERT INTO posts (group_id, user_id, text, date) 
				VALUES(?, ?, ?, NOW());");
				if(false === $stmt) {
					$this->errors[] = "Prepare failed " . $conn->error;
					return false;
				}
				
				$raw = $_POST["post"];
				$stripped = strip_tags($raw);
				
				$group_id = $_SESSION["group_id"];
				$user_id = $_SESSION["id"];
				$text = $string;
				
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
		
		private function createComment() 
		{
			if(empty($_POST["text"])) {
				die("Empty text");
			} elseif (empty($_SESSION["id"])) {
				die("Empty user id");
			} elseif (empty($_SESSION["group_id"])) {
				die("Empty group id");
			} elseif (empty($_POST["post_id"])) {
				die("Empty post id");
			} else {
			
				$stmt = $this->conn->prepare("INSERT INTO comments (group_id, post_id, user_id, text, date) 
				VALUES(?, ?, ?, ?, NOW());");
				if(false === $stmt) {
					$this->errors[] = "Prepare failed " . $conn->error;
					return false;
				}
				
				$group_id = $_SESSION["group_id"];
				$post_id = $_POST["post_id"];
				$user_id = $_SESSION["id"];
				$comment = strip_tags($_POST["text"]);
				
				$ok = $stmt->bind_param("iiis", $group_id, $post_id, $user_id, $comment);
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
		
		private function addUser() {
			if(empty($_POST["name"])) {
				die("Empty name");
			} elseif(empty($_SESSION["group_id"])) {
				die("Empty name");
			} else {
				if(hasGroupFlag('a') == false)
					return false;
				
				$query = "SELECT user_id
				FROM members
				WHERE name = ?
				LIMIT 1;";
				$stmt = $this->conn->prepare($query);
				if(false === $stmt)
				die("Prepare failed");
				
				$ok = $stmt->bind_param("s", $_POST["name"]);
				if(false === $ok)
				die("bind_param failed");
				
				$ok = $stmt->execute();
				if(false === $ok)
				die("Execute failed");
				
				$ok = $stmt->bind_result($user_id);
				if(false === $ok)
				die("bind_result failed");
				
				$ok = $stmt->fetch();
				if(false === $ok)
				die("Execute failed");
				
				$stmt->close();
				
				if(!empty($user_id)) {
				
					$query = "INSERT INTO membership (user_id, group_id, flag) VALUES (?, ?, 'u')";
					$stmt = $this->conn->prepare($query);
					if(false === $stmt)
					die("Prepare failed");
					
					$ok = $stmt->bind_param("ii", $user_id, $_SESSION["group_id"]);
					if(false === $ok)
					die("bind_param failed");
					
					$ok = $stmt->execute();
					if(false === $ok)
					die("Execute failed");
					
					$stmt->close();
				}
				
				return true;
			}
		}
		
		private function remUser() {
			if(empty($_POST["user_id"])) {
				die("Empty name");
			} elseif(empty($_SESSION["group_id"])) {
				die("Empty name");
			} else {
				if(hasGroupFlag('a') == false)
					return false;
					
				$query = "SELECT flag FROM membership WHERE user_id = ? AND group_id = ?";
				$stmt = $this->conn->prepare($query);
				if(false === $stmt)
				die("Prepare failed");
				
				$ok = $stmt->bind_param("ii", $_POST["user_id"], $_SESSION["group_id"]);
				if(false === $ok)
				die("bind_param failed");
				
				$ok = $stmt->execute();
				if(false === $ok)
				die("Execute failed");
				
				$ok = $stmt->bind_result($flag);
				if(false === $ok)
				die("bind_result failed");
				
				$stmt->fetch();
				
				$stmt->close();	
				
				if($flag != 'a') {
					
					$query = "DELETE FROM membership WHERE user_id = ? AND group_id = ?";
					$stmt = $this->conn->prepare($query);
					if(false === $stmt)
					die("Prepare failed");
					
					$ok = $stmt->bind_param("ii", $_POST["user_id"], $_SESSION["group_id"]);
					if(false === $ok)
					die("bind_param failed");
					
					$ok = $stmt->execute();
					if(false === $ok)
					die("Execute failed");
					
					$stmt->close();
					return true;
				} else {
					return false;
				}
			}
		}
		
		private function makeAdmin() {
			if(empty($_POST["user_id"])) {
				die("Empty name");
			} elseif(empty($_SESSION["group_id"])) {
				die("Empty name");
			} else {
				if(hasGroupFlag('a') == false)
					return false;
					
				$query = "SELECT flag FROM membership WHERE user_id = ? AND group_id = ?";
				$stmt = $this->conn->prepare($query);
				if(false === $stmt)
				die("Prepare failed");
				
				$ok = $stmt->bind_param("ii", $_POST["user_id"], $_SESSION["group_id"]);
				if(false === $ok)
				die("bind_param failed");
				
				$ok = $stmt->execute();
				if(false === $ok)
				die("Execute failed");
				
				$ok = $stmt->bind_result($flag);
				if(false === $ok)
				die("bind_result failed");
				
				$stmt->fetch();
				
				$stmt->close();	
				
				if($flag != 'o') {
					
					$query = "UPDATE membership SET flag = 'a'  WHERE user_id = ? AND group_id = ?";
					$stmt = $this->conn->prepare($query);
					if(false === $stmt)
					die("Prepare failed");
					
					$ok = $stmt->bind_param("ii", $_POST["user_id"], $_SESSION["group_id"]);
					if(false === $ok)
					die("bind_param failed");
					
					$ok = $stmt->execute();
					if(false === $ok)
					die("Execute failed");
					
					$stmt->close();
					return true;
				} else {
					return false;
				}
			}
		}
		
		private function makeUser() {
			if(empty($_POST["user_id"])) {
				die("Empty name");
			} elseif(empty($_SESSION["group_id"])) {
				die("Empty name");
			} else {
				if(hasGroupFlag('a') == false)
					return false;
					
				$query = "SELECT flag FROM membership WHERE user_id = ? AND group_id = ?";
				$stmt = $this->conn->prepare($query);
				if(false === $stmt)
				die("Prepare failed");
				
				$ok = $stmt->bind_param("ii", $_POST["user_id"], $_SESSION["group_id"]);
				if(false === $ok)
				die("bind_param failed");
				
				$ok = $stmt->execute();
				if(false === $ok)
				die("Execute failed");
				
				$ok = $stmt->bind_result($flag);
				if(false === $ok)
				die("bind_result failed");
				
				$stmt->fetch();
				
				$stmt->close();	
				
				if($flag != 'o') {
					
					$query = "UPDATE membership SET flag = 'u'  WHERE user_id = ? AND group_id = ?";
					$stmt = $this->conn->prepare($query);
					if(false === $stmt)
					die("Prepare failed");
					
					$ok = $stmt->bind_param("ii", $_POST["user_id"], $_SESSION["group_id"]);
					if(false === $ok)
					die("bind_param failed");
					
					$ok = $stmt->execute();
					if(false === $ok)
					die("Execute failed");
					
					$stmt->close();
					return true;
				} else {
					return false;
				}
			}
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
		
		public static function getPosts($group_id = null) {
			if(!empty($_SESSION["group_id"])) {
				$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
			
				$query = "SELECT posts.post_id, posts.user_id, members.name, posts.text, posts.date, posts.file_id, posts.event_id, c.count
				FROM posts
				INNER JOIN members ON posts.user_id = members.user_id
				LEFT JOIN (SELECT post_id, count(comment_id) as count FROM comments GROUP BY post_id) as c on posts.post_id = c.post_id
				WHERE group_id = ?
				ORDER BY posts.date DESC;";
				$stmt = $conn->prepare($query);
				if(false === $stmt)
				die("Prepare failed");
				
				if(empty($group_id))
					$group_id = $_SESSION["group_id"];
				$ok = $stmt->bind_param("i", $group_id);
				if(false === $ok)
				die("bind_param failed");
				
				$ok = $stmt->execute();
				if(false === $ok)
				die("Execute failed");
				
				$ok = $stmt->bind_result($post_id, $user_id, $name, $text, $date, $file_id, $event_id, $count);
				if(false === $ok)
				die("bind_result failed");
				
				$posts = array();
				while($stmt->fetch())
				{
					$posts[] = array(
									'post_id' => $post_id,
									'user_id' => $user_id,
									'name' => $name,
									'text' => $text,
									'date' => $date,
									'file_id' => $file_id,
									'event_id' => $event_id,
									'count' => $count);
				}
				
				$stmt->close();
				$conn->close();
				
				return $posts;
			}
		}
		
		public static function getMembers() {
			if(!empty($_SESSION["group_id"])) {
				$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
			
				$query = "SELECT m.user_id, m.name, f.flag 
						FROM members m 
						INNER JOIN membership f ON m.user_id = f.user_id
						WHERE group_id = ? 
						ORDER BY name";
				$stmt = $conn->prepare($query);
				if(false === $stmt)
				die("Prepare failed");
				
				$ok = $stmt->bind_param("i", $_SESSION["group_id"]);
				if(false === $ok)
				die("bind_param failed");
				
				$ok = $stmt->execute();
				if(false === $ok)
				die("Execute failed");
				
				$ok = $stmt->bind_result($user_id, $name, $flag);
				if(false === $ok)
				die("bind_result failed");
				
				$members = array();
				while($stmt->fetch())
				{
					$members[] = array ( 'user_id' => $user_id,
										 'name' => $name,
										 'flag' => $flag);
				}
				
				$stmt->close();
				$conn->close();
				
				return $members;
			}
		}
		
		public static function getMaterials() {
			if(!empty($_SESSION["group_id"])) {
				$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
			
				$query = "SELECT f.file_id, f.original_name, f.size, f.title, f.comment, f.date, f.subject_id, m1.name, m2.name, s.name, f.user_id
				FROM files f
				INNER JOIN members m1 ON f.author_id = m1.user_id
				INNER JOIN members m2 ON f.user_id = m2.user_id
				INNER JOIN subjects s ON f.subject_id = s.subject_id
				WHERE group_id = ?
				ORDER BY f.date DESC;";
				$stmt = $conn->prepare($query);
				if(false === $stmt)
				die("Prepare failed");
				
				$ok = $stmt->bind_param("i", $_SESSION["group_id"]);
				if(false === $ok)
				die("bind_param failed");
				
				$ok = $stmt->execute();
				if(false === $ok)
				die("Execute failed");
				
				$ok = $stmt->bind_result($file_id, $filename, $size, $title, $comment, $date, $subject_id, $author, $uploader, $subject, $uploader_id);
				if(false === $ok)
				die("bind_result failed");
				
				$materials = array();
				while($stmt->fetch())
				{
					$materials[] = array(
					'file_id' => $file_id,
										'filename' => $filename,
										'size' => $size,
										'title' => $title,
										'comment' => $comment,
										'date' => $date,
										'subject_id' => $subject_id,
										'author' => $author,
										'uploader' => $uploader,
										'subject' => $subject,
										'uploader_id' => $uploader_id);
				}
				
				$stmt->close();
				$conn->close();
				
				return $materials;
			}
		}
		
		public static function getGroups()
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
							
				$groups = array();
				$success = $stmt->execute();
				if(false === $success)
				die("execute() failed");
				else
				{
					$success = $stmt->bind_result($name, $group_id, $flag);
					
					if(false === $success)
					die("bind_result() failed");
					
					
					
					while($stmt->fetch())
					{
						$groups[] = array( 'name' => $name,
										   'group_id' => $group_id,
										   'flag' => $flag );						
					}
				}
				
				$stmt->close();
				$conn->close();
				
				return $groups;
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
?>