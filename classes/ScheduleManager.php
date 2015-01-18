<?php
	$path = realpath($_SERVER["DOCUMENT_ROOT"] . "/test/config/site.php");
	require_once($path);
	
	class ScheduleManager
	{
		public $errors = array();
		
		public function __construct()
		{
			if(isset($_POST["get"]))
				$this->getEvents();
		}
		
		private function getEvents()
		{
			if(!isset($_POST["schedule_id"]) OR empty($_POST["schedule_id"])) {
				$this->errors[] = "Schedule id not set";
			} elseif (!isset($_POST["group_id"]) OR empty($_POST["group_id"])) {
				$this->errors[] = "Group id not set";
			} else {
				if(
			
				$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
					
				if($conn->connect_errno)
					die("Failed to connect to database");
				
				$query = "SELECT title, allDay, start, end, url
					WHERE schedule_id = ?";
				$stmt = $conn->prepare($query);
				if(false === $stmt)
					die("Prepare failed");
				
				$schedule_id = $_POST["id"];
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
			}
		}
	}
?>