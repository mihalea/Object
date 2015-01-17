<?php

require_once("config/db.php");

class GroupManager {

	public $id;
	public $group;
	public $errors = array();

	public function __construct()
	{
		if(isset($_POST["name"])) {
			if ($this->createGroup() == true) {
				header("Location: groups.php");
			} else {
				#header("Location: groups.php?error=create");
			}
		}
	}
	
	public function createGroup()
	{
		if(empty($_POST["name"]))
			$this->errors[] = "Empty post var: name";
		elseif(!isset($_SESSION["id"]) OR empty($_SESSION["id"]))
			$this->errors[] = "Empty or not set user id";
		else {
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
	
	public function getGroups()
	{
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		
		if($conn->connect_errno)
			die("Connection failed");
			
		$user_id = $_SESSION["id"];
		
		$query = "SELECT groups.name, groups.group_id, membership.flag
				FROM groups
				INNER JOIN membership on groups.group_id = membership.group_id
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
	}
	
	public function getName() { return $this->name; }
}

?>