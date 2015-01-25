<?php
	class Frontpage {
		public static function getPosts() {
			$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
			
			if($conn->connect_errno)
			die("Failed to connect to database");
		
			$query = "SELECT p.post_id, p.group_id, p.user_id, p.file_id, p.event_id, p.text, p.date, u.name, c.count
			FROM posts p
			INNER JOIN membership m ON p.group_id = m.group_id 
			INNER JOIN members u ON u.user_id = p.user_id
			LEFT JOIN (SELECT post_id, count(comment_id) as count FROM comments GROUP BY post_id) as c on p.post_id = c.post_id
			WHERE m.user_id = ?";
			$stmt = $conn->prepare($query);
			if(false === $stmt)
			die("Prepare failed");
			
			$ok = $stmt->bind_param("i", $_SESSION["id"]);
			if(false === $ok)
			die("bind_param failed");
			
			$ok = $stmt->execute();
			if(false === $ok)
			die("Execute failed");
			
			$ok = $stmt->bind_result($post_id, $group_id, $user_id, $file_id, $event_id, $text, $date, $name, $count);
			if(false === $ok)
			die("bind_result failed");
			
			$posts = array();
			while($stmt->fetch())
			{
				
				$posts[] = array(
				'post_id' => $post_id,
				'group_id' => $group_id,
				'user_id' => $user_id,
				'file_id' => $file_id,
				'event_id' => $event_id,
				'text' => $text,
				'date' => $date,
				'name' => $name,
				'count' => $count);
			}
			$stmt->close();
			
			return $posts;
		}
	}