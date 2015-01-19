<?php
	session_start();
	require_once("../config/db.php");
	require_once("../classes/Permissions.php");
	
	if(isset($_SESSION["id"]) AND !empty($_SESSION["id"]) AND 
	isset($_SESSION["group_id"]) AND !empty($_SESSION["group_id"])) {
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		
		if($conn->connect_errno)
		die("Failed to connect to database");
		
		if(hasGroupFlag('u') == false)
			http_response_code(403);
		
		$query = "SELECT schedule_id
		FROM groups
		WHERE group_id = ?
		LIMIT 1;";
		$stmt = $conn->prepare($query);
		if(false === $stmt)
		die("Prepare failed");
		
		$ok = $stmt->bind_param("i", $_SESSION["group_id"]);
		if(false === $ok)
		die("bind_param failed");
		
		$ok = $stmt->execute();
		if(false === $ok)
		die("Execute failed");
		
		$ok = $stmt->bind_result($schedule_id);
		if(false === $ok)
		die("bind_result failed");
		
		$ok = $stmt->fetch();
		if(false === $ok)
		die("Execute failed");
		
		$stmt->close();
		
		$query = "SELECT s_events.event_id, subjects.name, s_events.subject_id, s_events.day, s_events.time_start, s_events.time_end
		FROM s_events
		INNER JOIN subjects on s_events.subject_id = subjects.subject_id
		WHERE schedule_id = ?;";
		$stmt = $conn->prepare($query);
		if(false === $stmt)
		die("Prepare failed " . $conn->errno);
		
		
		
		$ok = $stmt->bind_param("i", $schedule_id);
		if(false === $ok)
		die("bind_param failed");
		
		$ok = $stmt->execute();
		if(false === $ok)
		die("Execute failed");
		
		$ok = $stmt->bind_result($event_id, $title, $subject_id, $day, $timeStart, $timeEnd);
		if(false === $ok)
		die("bind_result failed");
		
		$timeNow = time();
		$dayOfWeek = date("N") - 1;
		$lastMonday = $timeNow - $dayOfWeek * 24 * 3600;
		$formatted = date("Y-m-d", $lastMonday);
		$lastMonday = strtotime($formatted);
		
		$events = array();
		while($stmt->fetch())
		{
			$parts = explode(":", $timeStart);
			$secondsStart = $parts[0]*3600 + $parts[1]*60;
			
			$parts = explode(":", $timeEnd);
			$secondsEnd = $parts[0]*3600 + $parts[1]*60;
			
			$events[] = array(
			'id' => $event_id,
			'subject_id' => $subject_id,
			'title' => $title,
			'day' => $day,
			'start' => date('Y-m-d H:i', $lastMonday + $secondsStart + ($day - 1) * 24 * 60 * 60),
			'end' => date('Y-m-d H:i', $lastMonday + $secondsEnd + ($day - 1) * 24 * 60 * 60));
		}
		
		echo json_encode($events);
	}
	
?>