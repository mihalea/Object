<?php
	
	require_once("config/db.php");
	
	class EventManager
	{
		public $errors = array();
		public $eventAdded;
		
		public function __construct()
		{
			if(isset($_POST["event"])) {			
				if($this->addEvent() == true) {
					header('Location: events.php');
				}
				else {
					header('Location: events.php?error=add');
				}
			}
			elseif(isset($_POST["eventID"])) {
				if($this->removeEvent() == true) {
					header('Location: events.php');
				}
				else {
					header('Location: events.php?error=rem');
				}
			}
		}
		
		public function addEvent()
		{	
			if(empty($_POST["name"])) {
				$this->errors[] = "Empty name";
			}
			elseif(empty($_POST["date"])) {
				$this->errors[] = "Empty date";
			}
			elseif(!preg_match('/\d{4}-\d{2}-\d{2}/', $_POST["date"])) {
				$this->errors[] = "Invalid date";
			}
			else 
			{
				$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
				
				
				if($conn->connect_error) {
					$this->errors[] = "Failed to connect to database at addEvent: " . $conn->connect_error;
					return false;
				}
				
				if(!isset($_SESSION["id"])) {
					$this->errors[] = "Failed to get session id";
					return false;
				}
				
				$name = $_POST["name"];
				$date = date('Y-m-d H:i:s', strtotime($_POST["date"]));
				$comment = $_POST["comment"];
				
				$stmt = $conn->prepare("INSERT INTO events (user_id, name, date, comment)
				VALUES (?, ?, ?, ?);");
				if(false === $stmt) {
					$this->errors[] = "Failed to prepare at addEvent: " . $conn->connect_error;
					return false;
				}
				
				$code = $stmt->bind_param("isss", $_SESSION["id"], $name, $date, $comment);
				if(false === $code) {
					$this->errors[] = "Failed to bind params at addEvent";
					return false;
				}
				
				$ok = $stmt->execute();
				if(false === $ok) {
					$this->errors[] = "Failed to execute at addEvent";
					return false;
				}
				
				$stmt->close();
				$conn->close();
				
				return true;
			}
			
			return false;
		}
		
		public function removeEvent()
		{
			if(empty($_POST["eventID"])) {
				$errors="Empty event id";
			}
			else
			{
				$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
				
				if($conn->connect_error) {
					$this->errors[] = "Failed to connect to database at removeEvent";
					return false;
				}
				
				if(!isset($_SESSION["id"])) {
					$this->errors[] = "Failed to get session id at removeEvent";
					return false;
				}
				
				$id = $_POST["eventID"];
				
				$stmt = $conn->prepare("DELETE FROM events
				WHERE event_id = ?;");
				
				if(false === $stmt) {
					$this->errors[] = "Failed to prepare at removeEvent";
					return false;
				}
				
				$code = $stmt->bind_param("i", $id);
				if(false === $code) {
					$this->errors[] = "Failed to bind parms at removeEvent";
					return false;
				}
				
				$eventAdded = $stmt->execute();
				if(false === $eventAdded) {
					$this->errors[] = "Failed to execute at removeEvent";
					return false;
				}
				
				$stmt->close();
				$conn->close();
				
				return true;
			}
			
			return false;
		}
		
		public function getEvents()
		{
			$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
			
			if($conn->connect_errno) {
				$this->errors[] = "Failed to connect to database at getEvents";
				return false;
			}
			
			$user_id = $_SESSION["id"];
			
			$query = "SELECT name, date, comment, event_id
			FROM events
			WHERE user_id = ?";
			if(!isset($_GET["all"])) {
				$query = $query . " AND date >= curdate()";
			}
			
			$query = $query . " ORDER BY date";
			
			$stmt = $conn->prepare($query);
			
			if(false === $stmt) {
				$this->errors[] = "Failed to prepare at getEvents";
				return false;
			}
			
			$ok = $stmt->bind_param("i", $user_id);
			if(false === $ok) {
				$this->errors[] = "Failed to prepare at getEvents";
				return false;
			}
			
				
			$ok = $stmt->execute();
			if(false === $ok) {
				$this->errors[] = "Failed to get results at getEvents";
				return false;
			}
			
			$ok = $stmt->bind_result($name, $date, $comment, $event_id);
			
			if(false === $ok) {
				$this->errors[] = "Failed to bind results at getEvents";
				return false;
			}

			$stmt->store_result();
			$rows = $stmt->num_rows;
			
			if($rows == 0) {
				echo '<div class="alert alert-info"><p>Heads up! There are no events to be shown.</p></div>';
			}
			else {
				echo '<table class="table" id="eventTable">
				<tr><th>Name</th> <th>Date</th> <th>Comment</th> <th style="width:50px"></th> </tr>';
				while($stmt->fetch())
				{
					echo "<tr>";
					
					echo "<td>" . $name . "</td>";
					echo "<td>" . $date . "</td>";
					echo "<td>" . $comment . "</td>";
					
					echo '<td class="removeCol"><span class="glyphicon glyphicon-remove" onclick="removeEvent(' . $event_id . ')"></span></td>';
					
					echo "</tr>";
				}
				
				echo '</table>';
			}
			
			
			$stmt->close();
			$conn->close();
			
			return true;
		}
	}
?>