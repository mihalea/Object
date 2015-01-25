<?php
	function timeDifference($time) 
	{
		$timeNow = time();
		$timePost = strtotime($time);
		
		
		$time = $timeNow - $timePost;
		if($time<0) {
			$time = -$time;
			} else {
			$suffix = " ago";
		}
		$out = "";
		if($time < 60) {
			$out = $time . " seconds";
			} else {
			$time = $time / 60; #minutes
			if( $time < 60 ) {
				$time = round($time);
				
				if($time == 1)
				$out = $time . ' minute';
				else
				$out = $time . ' minutes';
				} else {
				$time = $time / 60; #hours
				if ( $time < 24 ) {
					$time = round($time);
					if($time == 1)
					$out = $time . ' hour';
					else
					$out = $time . ' hours';
					} else {
					$time = round($time / 24); #days
					if($time == 1)
					$out = $time . ' day';
					else
					$out = $time . ' days';
				}
			}
		}
		
		if(!empty($suffix))
		$out = $out . $suffix;
		
		return $out;
	}	
	
	function formatBytes($bytes, $precision = 2) { 
		$units = array('B', 'KB', 'MB', 'GB', 'TB'); 
		
		$bytes = max($bytes, 0); 
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
		$pow = min($pow, count($units) - 1); 
		
		// Uncomment one of the following alternatives
		// $bytes /= pow(1024, $pow);
		$bytes /= (1 << (10 * $pow)); 
		
		return round($bytes, $precision) . ' ' . $units[$pow]; 
	} 	
	
	function getAllUsers() {
		if(!empty($_SESSION["id"])) {
			$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
			
			$query = "SELECT user_id, name FROM members ORDER BY name ASC";
			$stmt = $conn->prepare($query);
			if(false === $stmt)
			die("Prepare failed");
			
			$ok = $stmt->execute();
			if(false === $ok)
			die("Execute failed");
			
			$ok = $stmt->bind_result($user_id, $name);
			if(false === $ok)
			die("bind_result failed");
			
			$members = array();
			while($stmt->fetch())
			{
				$members[] = array ( 'user_id' => $user_id,
				'name' => $name );
			}
			
			$stmt->close();
			$conn->close();
			
			return $members;
		}
	}	