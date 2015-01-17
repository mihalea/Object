<?php

require_once("config/db.php");

class EventManager
{
	public $errors = array();
	public $eventAdded;

	public function __construct()
	{
		if(isset($_POST["event"]))
			$this->addEvent();
		elseif(isset($_POST["eventID"]))
			$this->removeEvent();
	}
	
	public function addEvent()
	{	
		if(empty($_POST["name"]))
			$errors[] = "Empty name";
		elseif(empty($_POST["date"]))
			$errors[] = "Empty date";
		elseif(!preg_match('/\d{4}-\d{2}-\d{2}/', $_POST["date"]))
			$errors[] = "Invalid date";
		else 
		{
			$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
			
			
			if($conn->connect_error)
				die("Connection failed");
			
			if(!isset($_SESSION["id"]))
				die("User id not found");
				
			$name = $_POST["name"];
			$date = date('Y-m-d H:i:s', strtotime($_POST["date"]));
			$comment = $_POST["comment"];
			
			$stmt = $conn->prepare("INSERT INTO events (user_id, name, date, comment)
									VALUES (?, ?, ?, ?);");
									
			if(false === $stmt)
				die("prepare() failed");
									
			$code = $stmt->bind_param("isss", $_SESSION["id"], $name, $date, $comment);
			if(false === $code)
				die("bind_params() failed");
				
			$eventAdded = $stmt->execute();
			if($eventAdded)
				$GLOBALS["ok"] = 1;
			else
				$GLOBALS["ok"] = 0;
			
			$stmt->close();
			$conn->close();
		}
	}
	
	public function removeEvent()
	{
		if(empty($_POST["eventID"]))
			$errors="Empty event id";
		else
		{
			$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
			
			if($conn->connect_errno)
				die("Connection failed");
				
			if(!isset($_SESSION["id"]))
				die("User id not found");
				
			$id = $_POST["eventID"];
			
			$stmt = $conn->prepare("DELETE FROM events
									WHERE event_id = ?;");
									
			if(false === $stmt)
				die("prepare() failed");
				
			$code = $stmt->bind_param("i", $id);
			if(false === $code)
				die("bind_params() failed");
									
			$eventAdded = $stmt->execute();
			if($eventAdded)
				$errors[] = "execute() failed";
			
			$stmt->close();
			$conn->close();
		}
	}
	
	public function getEvents()
	{
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		
		if($conn->connect_errno)
			die("Connection failed");
			
		$author = $_SESSION["id"];
		
		$query = "SELECT name, date, comment, event_id
								FROM events
								WHERE user_id = ?";
		if(!isset($_GET["all"]))
			$query = $query . " AND date >= curdate()";
			
		$query = $query . " ORDER BY date";
		
		$stmt = $conn->prepare($query);
								
		if(false === $stmt)
			die("prepare() failed");
			
		$code = $stmt->bind_param("i", $author);
		if(false === $code)
			die("bind_param() failed");
			
		
		$rows = array();			
		$success = $stmt->execute();
		$result = $stmt->get_result();
		if(false === $success)
			die("execute() failed");
		else
		{
			$success = $stmt->bind_result($name, $date, $comment, $id);
			
			if(false === $success)
				die("bind_result() failed");
			
			echo '<table class="table" id="eventTable">
			<tr><th>Name</th> <th>Date</th> <th>Comment</th> <th style="width:50px"></th> </tr>';
			
			while($row = $result->fetch_array(MYSQLI_NUM))
			{
				echo "<tr>";
				
				$len = count($row);
				for ($i = 0 ; $i<$len-1 ; $i++)
					echo "<td>" . $row[$i] . "</td>";
					
				echo '<td class="removeCol"><span class="glyphicon glyphicon-remove" onclick="removeEvent(' . $row[$len-1] . ')"></span></td>';
					
				echo "</tr>";
			}
			
			echo '</table>';
		}
			
		$stmt->close();
		$conn->close();
	}
}
?>