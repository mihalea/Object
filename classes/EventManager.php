<?php
	$path = realpath($_SERVER["DOCUMENT_ROOT"] . "/test/config/site.php");
	require_once($path);
	
	class EventManager
	{
		public $errors = array();
		public $eventAdded;
		
		public function __construct()
		{
			$url = SITE_ROOT . "events";
			if(isset($_POST["event"])) {			
				if($this->addEvent() == true) {
					header('Location: ' . $url);
				}
				else {
					header('Location: ' . $url . '?error=add');
				}
			}
			elseif(isset($_POST["eventID"])) {
				if($this->removeEvent() == true) {
					header('Location: ' . $url);
				}
				else {
					header('Location: ' . $url . 'error=rem');
				}
			}
		}
		
		public function addEvent()
		{	
			if(empty($_POST["name"])) {
				$this->errors[] = "Empty name";
			} elseif(empty($_POST["date"])) {
				$this->errors[] = "Empty date";
			} elseif(!preg_match('/\d{4}-\d{2}-\d{2}/', $_POST["date"])) {
				$this->errors[] = "Invalid date";
			} else {
				$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
				
				
				if($conn->connect_error)
					die("Failed to connect to database at addEvent: " . $conn->connect_error);

				
				if(!isset($_SESSION["id"]))
					die("Failed to get session id");

				
				$group_id = isset($_POST["group"]) ? $_SESSION["group_id"] : null;
				$user_id = $_SESSION["id"];
				$name = strip_tags($_POST["name"]);
				$location = isset($_POST["location"]) ? strip_tags($_POST["location"]) : null;
				$date = date('Y-m-d H:i:s', strtotime($_POST["date"]));
				$start = isset($_POST["start"]) ? date('H:i', strtotime($_POST["start"])) : null;
				$end = isset($_POST["end"]) ? date('H:i', strtotime($_POST["end"])) : null;
				$comment = isset($_POST["comment"]) ? strip_tags($_POST["comment"]) : null;
				
				$stmt = $conn->prepare("INSERT INTO events (group_id, user_id, name, location, date, start, end, comment)
				VALUES (?, ?, ?, ?, ?, ?, ?, ?);");
				if(false === $stmt)
					die("Failed to prepare at addEvent: " . $conn->connect_error);
				
				$code = $stmt->bind_param("iissssss", $group_id, $user_id, $name, $location, $date, $start, $end, $comment);
				if(false === $code)
					die("Failed to bind params at addEvent");
				
				$ok = $stmt->execute();
				if(false === $ok)
					die("Failed to execute at addEvent");
				
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
			
			if(empty($_POST["group"])) {
				$query = $query . " AND group_id IS NULL";
			}
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