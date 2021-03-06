<?php
	$path = realpath($_SERVER["DOCUMENT_ROOT"] . "/test/config/site.php");
	require_once($path);
	
	class EventManager
	{
		public $errors = array();
		public $eventAdded;
		
		public function __construct()
		{
			$url = SITE_ROOT . (isset($_POST["group"]) ? "groups/events.php" : "events");
			if(isset($_POST["event"])) {			
				if($this->addEvent() == true) {
					header('Location: ' . $url);
				}
				else {
					header('Location: ' . $url . '?error=add');
				}
			}
			elseif(isset($_POST["remove"])) {
				if($this->removeEvent() == true) {
					header('Location: ' . $url);
				}
				else {
					header('Location: ' . $url . '?error=rem');
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
				
				$stmt = $conn->prepare("INSERT INTO events (group_id, user_id, name, location, date, start, end, comment)
				VALUES (?, ?, ?, ?, ?, ?, ?, ?);");
				if(false === $stmt)
				die("Failed to prepare at addEvent: " . $conn->connect_error);
				
				$group_id = isset($_POST["group"]) ? $_SESSION["group_id"] : null;
				$user_id = $_SESSION["id"];
				$name = strip_tags($_POST["name"]);
				$location = !empty($_POST["location"]) ? strip_tags($_POST["location"]) : null;
				$date = date('Y-m-d H:i:s', strtotime($_POST["date"]));
				$start = !empty($_POST["start"]) ? date('H:i', strtotime($_POST["start"])) : null;
				$end = !empty($_POST["end"]) ? date('H:i', strtotime($_POST["end"])) : null;
				$comment = !empty($_POST["comment"]) ? strip_tags($_POST["comment"]) : null;
				
				if(!empty($_POST["group"]))
					$name = "WHY THE FUCK";
				
				$code = $stmt->bind_param("iissssss", $group_id, $user_id, $name, $location, $date, $start, $end, $comment);
				if(false === $code)
				die("Failed to bind params at addEvent");
				
				$ok = $stmt->execute();
				if(false === $ok)
				die("Failed to execute at addEvent");
				
				$stmt->close();
				
				if(!empty($group_id)) {
					$message = '<span class="text-info">Title: </span>' . $name.'<br />
								<span class="text-info">Date: </span>' . date('Y-m-d', strtotime($date)) .'<br />';
					$stmt = $conn->prepare("INSERT INTO posts (group_id, user_id, event_id, text, date)
						VALUES (?, ?, LAST_INSERT_ID(), '" . $message . "', NOW())");
					if(false === $stmt)
					die("Failed to prepare at addEvent: " . $conn->connect_error);
					
					$code = $stmt->bind_param("ii", $group_id, $user_id);
					if(false === $code)
					die("Failed to bind params at addEvent");
					
					$ok = $stmt->execute();
					if(false === $ok)
					die("Failed to execute at addEvent");
					
					$stmt->close();
				}
				
				$conn->close();
				
				return true;
			}
			
			return false;
		}
		
		public function removeEvent()
		{
			if(empty($_POST["event_id"])) {
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
				
				$isAdmin = hasGroupFlag('a');
				
				if($isAdmin)
					$stmt = $conn->prepare("DELETE FROM events WHERE event_id = ?;");
				elseif(hasGroupFlag('u'))
					$stmt = $conn->prepare("DELETE FROM events WHERE event_id = ? AND user_id = ?;");
				else
					die('No permissions');
				
				if(false === $stmt) {
					$this->errors[] = "Failed to prepare at removeEvent";
					
					return false;
				}
				$id = $_POST["event_id"];
				
				if($isAdmin)
					$code = $stmt->bind_param("i", $id);
				else
					$code = $stmt->bind_param("ii", $id, $_SESSION["id"]);
				
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
		
		public  static function getEvents($group = true, $days = 0, $limit = 0) {
			if(isset($_SESSION["id"]) AND !empty($_SESSION["id"]) AND 
			isset($_SESSION["group_id"]) AND !empty($_SESSION["group_id"])) {
				$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
				
				if($conn->connect_errno)
				die("Failed to connect to database");
				
				$query = "SELECT event_id, user_id, name, location, date, start, end, comment
				FROM events
				WHERE group_id = ?";
				if($days != 0)
					$query = $query . " AND date <= DATE_ADD(NOW(), INTERVAL " . $days . " DAY) AND date >= NOW()";
				
				$query = $query . " ORDER BY date ASC";
				if($limit != 0)
					$query = $query . " LIMIT " . $limit;
				
				$stmt = $conn->prepare($query);
				if(false === $stmt)
				die("Prepare failed " . $conn->errno);
				
				
				
				$ok = $stmt->bind_param("i", $_SESSION["group_id"]);
				if(false === $ok)
				die("bind_param failed");
				
				$ok = $stmt->execute();
				if(false === $ok)
				die("Execute failed");
				
				$ok = $stmt->bind_result($event_id, $user_id, $name, $location, $date, $start, $end, $comment);
				if(false === $ok)
				die("bind_result failed");
				
				
				$events = array();
				while($stmt->fetch())
				{
					if(!empty($start)) {
						$exp = explode(":", $start);
						$ep_start = strtotime($date)+ $exp[0]*60*60 + $exp[1]*60;
						$start = date("Y-m-d H:i", $ep_start);
					}
					
					if(!empty($end)) {
						$exp = explode(':', $end);
						$ep_end   = strtotime($date) + $exp[0]*60*60 + $exp[1]*60;				
						$end = date("Y-m-d H:i", $ep_end);
					}
					
					
					$events[] = array(
					'id' => $event_id,
					'user_id' => $user_id,
					'title' => $name,
					'location' => $location,
					'date' => $date,
					'start' => $start,
					'end' => $end,
					'comment' => $comment,
					'can_delete' => ($user_id == $_SESSION["id"] OR hasGroupFlag('a') ? true : false));
				}
				
				return $events;
			}
			
			return null;
		}
	}
?>