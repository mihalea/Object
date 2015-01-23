<?php
	require_once("../classes/Permissions.php");
	require_once("../config/db.php");
	
	class DownloadHandler
	{
		public function __construct() 
		{			
			if(!empty($_GET["file_id"])) {
				$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
				
				$query = "SELECT filename, original_name, group_id
				FROM files
				WHERE file_id = ?
				LIMIT 1;";
				$stmt = $conn->prepare($query);
				if(false === $stmt)
				die("Prepare failed");
				
				
				$ok = $stmt->bind_param("s", $_GET["file_id"]);
				if(false === $ok)
				die("bind_param failed");
				
				$ok = $stmt->execute();
				if(false === $ok)
				die("Execute failed");
				
				$ok = $stmt->bind_result($filename, $original_name, $group_id);
				if(false === $ok)
				die("Bind result failed");
				
				$stmt->fetch();
				$stmt->close();
				
				if(hasGroupFlag('u', $group_id) == false)
					die("You don't have access to this file.");
				
				$path = realpath('C:/files/' . $filename);
				
				if(false === is_file($path))
					die("No such file");
				
				// get the mime type
				$finfo = finfo_open();
				$mime = finfo_file($finfo, $path, FILEINFO_MIME_TYPE);
				
				// send it to the client

				header('Content-Disposition: attachment; filename=' . $original_name);
				header('Content-Type: '.$mime);
				readfile($path);
			}
		}
	}
?>