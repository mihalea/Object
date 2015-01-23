<?php
	function timeDifference($time) 
	{
		date_default_timezone_set("Europe/Bucharest");
		$timeNow = time();
		$timePost = strtotime($time);
		
		
		$time = $timeNow - $timePost;
		
		$time = $time / 60; #minutes
		if( $time < 60 ) {
			$time = round($time);
			
			if($time == 1)
			return $time . ' minute ago';
			else
			return $time . ' minutes ago';
			} else {
			$time = $time / 60; #hours
			if ( $time < 24 ) {
				$time = round($time);
				if($time == 1)
				return $time . ' hour ago';
				else
				return $time . ' hours ago';
				} else {
				$time = round($time / 24); #days
				if($time == 1)
				return $time . ' day ago';
				else
				return $time . ' days ago';
			}
		}
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