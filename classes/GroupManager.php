<?php

require_once("config/db.php");

class GroupManager {

	public $id;
	public $group;

	public function __construct()
	{
		if(isset($_POST["name"]))
			$this->createGroup();
	}
	
	public function createGroup()
	{
		if(empty($_POST["name"]))
			die("Empty post var: name");
		elseif(!isset($_SESSION["id"]) OR empty($_SESSION["id"]))
			die("Empty or not set user id");
		else {
			$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
			if($conn->connect_errno)
				die("Connection failed!");
			
			$query = "INSERT INTO groups (name, user_id)
					  VALUES (?, ?);
					  SELECT LAST_INSERT_ID()";
			$stmt = $conn->prepare($query);
			if(false === $stmt)
				die("prepare() failed");
				
			$name = $_POST["name"];
			$user_id = $_SESSION["id"];
				
			$rc = $stmt->bind_param("si", $name, $user_id);
			if(false === $rc)
				die("bind_param() failed");
				
			$created = $stmt->execute();
			if(false === $created)
				die("execute() failed");
				
			$rc = $stmt->bind_result($group_id);
			if(false === $rc)
				die("bind_result() failed");
		
			$query = "INSERT INTO membership (user_id, group_id, flag)
					  VALUES (?, ?, ?);";
			$stmt = $conn->prepare($query);
			if(false === $stmt)
				die("prepare() failed");
				
			$flag = 'a';
			
			$rc = $stmt->bind_param("iis", $user_id, $group_od, $flag);
			if(false === $rc)
				die("bind_param() failed");
				
			$added = $stmt->execute();
			if(false === $added)
				die("execute() failed");
				
			$GLOBAL["ok"] = 1;
		}
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