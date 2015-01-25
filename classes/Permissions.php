<?php
	function hasGroupFlag($flag, $group_id = NULL)
	{
		if(empty($group_id))
			$group_id = $_SESSION["group_id"];
			
		
		if(!empty($_SESSION["id"])) {
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
			
			$stmt->store_result();			
			
			if($stmt->num_rows == 1) {
				$stmt->bind_result($dbFlag);
				$stmt->fetch();
				
				if($flag == 'u' AND ($dbFlag == 'a' OR $dbFlag == 'u' OR $dbFlag == 'o'))
					return true;
				elseif($flag == 'a' && ($dbFlag == 'a' OR $dbFlag == 'o'))
					return true;
				else
					return false;
			}
			else
				return false;
		}
		
		return false;
	}
?>