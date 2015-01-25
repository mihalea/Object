<?php
	class Frontapge {
		public static class getPosts() {
			$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		
			$query = "SELECT flag
			FROM membership
			WHERE group_id = ? AND user_id = ?
			LIMIT 1;";
			$stmt = $conn->prepare($query);
			if(false === $stmt)
			die("Prepare failed");
			
			
			$ok = $stmt->bind_param("ii", $group_id, $_SESSION["id"]);
			if(false === $ok)
			die("bind_param failed");
			
			$ok = $stmt->execute();
			if(false === $ok)
			die("Execute failed");
		}
	}