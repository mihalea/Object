<?php
	class ScheduleManager
	{
		public $errors = array();
		public $connection;
		
		public function __construct()
		{
			$this->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
			if($this->connection->connect_error)
			die($this->connection->connect_error);
			
			if(isset($_POST["editClass"])) {
				$head = 'Location: ' . SITE_ROOT . "groups/schedule.php";
				if(isset($_POST["group_id"]))
				$head = $head . "?id=" . $_POST["group_id"];
				
				if($this->editClass() == true) {
					header($head);
					} else {
					header($head . "?error");
				}
			}
			
		}
		
		private function editClass() {
			if(empty($_POST["group_id"])) {
				$this->errors[] = "Could not find group id";
				} elseif (empty($_POST["day"])) {
				$this->errors[] = "Could not find day";
				} elseif (empty($_POST["subject_id"])) {
				$this->errors[] = "Could not find subject id";
				} elseif (empty($_POST["time_start"])) {
				$this->errors[] = "Could not find start time";
				} elseif (empty($_POST["time_end"])) {
				$this->errors[] = "Could not find end time";
				} else {
				
				if(isset($_POST["event_id"]))
					return $this->updateEvent();
				else
					return $this->createEvent();
				
			}
		}
		
		private function updateEvent() {		
			$query = "UPDATE s_events 
			SET day = ?, subject_id = ?, time_start = ?, time_end = ?
			WHERE event_id = ?";
			$stmt = $this->conn->prepare($query);
			if(false === $stmt)
			die("prepare() failed");
			
			$code = $stmt->bind_param("iissi", $_POST["day"], $_POST["subject_id"],
				$_POST["time_start"], $_POST["time_end"], $_POST["event_id"]);
			if(false === $code)
				die("bind_param() failed");
			
			$ok = $stmt->execute();
			if(false === $ok)
				die("Execute failed");
			$stmt->close();
			
			return true;
		}
		
		private function createEvent() {			
			if(empty($SESSION["schedule_id"])) {
				$query = "INSERT INTO schedules (user_id) VALUES (?);";
				$stmt = $this->conn->prepare($query);
				if(false === $stmt)
					die("prepare() failed");
					
				$code = $stmt->bind_param("i", $_SESSION["id"]);
				if(false === $code)
					die("bind_param() failed");
					
				$ok = $stmt->execute();
				if(false === $ok)
					die("Execute failed");
				$stmt->close();
					
				$query = "UPDATE groups SET schedule_id = LAST_INSERT_ID() WHERE group_id = ?;";
				$stmt = $this->conn->prepare($query);
				if(false === $stmt)
					die("prepare() failed");
					
				$code = $stmt->bind_param("i", $this->id);
				if(false === $code)
					die("bind_param() failed");
					
				$ok = $stmt->execute();
				if(false === $ok)
					die("Execute failed");	
				$stmt->close();
				
				$query = "SELECT schedule_id FROM groups WHERE group_id = ?";
				$stmt = $this->conn->prepare($query);
				if(false === $stmt)
					die("prepare() failed");
					
				$code = $stmt->bind_param("i", $this->id);
				if(false === $code)
					die("bind_param() failed");
					
				$ok = $stmt->execute();
				if(false === $ok)
					die("Execute failed");
					
				$ok = $stmt->bind_result($schedule_id);
				if(false === $ok)
				die("bind_result failed");
				
				$stmt->fetch();
				$_SESSION["schedule_id"] = $schedule_id;
			}
				
			$query = "INSERT INTO events (schedule_id, subject_id, day, time_start, time_end)
				VALUES (?, ?, ?, ?, ?)";
			$stmt = $this->conn->prepare($query);
			if(false === $stmt)
			die("prepare() failed");
			
			$code = $stmt->bind_param("iiiss", $POST["schedule_id"], $_POST["subject_id"], $_POST["day"],
				$_POST["time_start"], $_POST["time_end"], $_POST["event_id"]);
			if(false === $code)
				die("bind_param() failed");
			
			$ok = $stmt->execute();
			if(false === $ok)
				die("Execute failed");
			$stmt->close();
			
			return true;
			}
		}
	}
?>