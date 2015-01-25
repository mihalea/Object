<?php
	session_start();
	require_once("../config/db.php");
	require_once("../classes/Permissions.php");
	require_once("../classes/Helper.php");
	require_once("../classes/EventManager.php");
	
	if(isset($_GET["schedule"])) 
	{
		getSchedule();
	} 
	elseif (isset($_GET["events"])) 
	{
		getEvents();
	}
	elseif (isset($_GET["comments"]))
	{
		getComments();
	}
	
	function getEvents()
	{		
		echo json_encode(EventManager::getEvents());
	}
	
	function getSchedule()
	{
		if(isset($_SESSION["id"]) AND !empty($_SESSION["id"]) AND 
		isset($_SESSION["group_id"]) AND !empty($_SESSION["group_id"])) {
			if(hasGroupFlag('u') == false)
			http_response_code(403);
	
			$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
			
			if($conn->connect_errno)
			die("Failed to connect to database");

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
			#$timeNow = '1421034707';
			$dayOfWeek = date("N") - 1;
			
			if($dayOfWeek < 5)
			$lastMonday = $timeNow - $dayOfWeek * 24 * 3600;
			else
			$lastMonday = $timeNow + ( 7 - $dayOfWeek) * 24 * 3600;
			
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
	}
	
	function getComments()
	{
		if(empty($_SESSION["id"]))
			die("Empty user id");
		elseif (empty($_SESSION["group_id"]))
			die("Empty group id");
		elseif (empty($_GET["post_id"]))
			die("Empty post id");
		elseif (!isset($_GET["batch"]))
			die("Empty batch");
		else {
			if(hasGroupFlag('u') == false)
			http_response_code(403);
	
	
			$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
			
			if($conn->connect_errno)
			die("Failed to connect to database");

			$query = "SELECT c.comment_id, m.name, c.text, c.date
			FROM comments c
			INNER JOIN members m ON c.user_id = m.user_id 
			WHERE c.post_id = ?
			ORDER BY c.date DESC";
			
			$query = $query . " LIMIT " . $_GET["batch"] * 10  . ", 10";
			$stmt = $conn->prepare($query);
			if(false === $stmt)
			die("Prepare failed");
			
			$ok = $stmt->bind_param("i", $_GET["post_id"]);
			if(false === $ok)
			die("bind_param failed");
			
			$ok = $stmt->execute();
			if(false === $ok)
			die("Execute failed");
			
			$ok = $stmt->bind_result($comment_id, $name, $text, $date);
			if(false === $ok)
			die("bind_result failed");
			
			
			$comments = array();
			while($stmt->fetch()) {
				$comments[] = array( 'comment_id' => $comment_id,
									'name' => $name,
									'text' => $text,
									'date' => $date,
									'ago' => timeDifference($date));
			}
			
			
			
			$stmt->close();
			
			echo json_encode(array_reverse($comments));
		}
	}
?>