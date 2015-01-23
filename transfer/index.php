<?php
	/*
		* jQuery File Upload Plugin PHP Example 5.14
		* https://github.com/blueimp/jQuery-File-Upload
		*
		* Copyright 2010, Sebastian Tschan
		* https://blueimp.net
		*
		* Licensed under the MIT license:
		* http://www.opensource.org/licenses/MIT
	*/
	
	error_reporting(E_ALL | E_STRICT);
	require_once('UploadHandler.php');
	require_once('DownloadHandler.php');
	require_once('../classes/Permissions.php');
	require_once('../config/db.php');
	
	session_start();
	
	if(!empty($_SESSION["group_id"]) AND hasGroupFlag('u')) {
		if($_SERVER['REQUEST_METHOD'] == 'GET')
			$download_handler = new DownloadHandler();
		
		if($_SERVER['REQUEST_METHOD'] == 'DELETE') {
			$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
			
			$query = "SELECT user_id
			FROM files
			WHERE file_id=?
			LIMIT 1;";
			$stmt = $conn->prepare($query);
			if(false === $stmt)
			die("Prepare failed");
			
			
			$ok = $stmt->bind_param("i", $_GET["file_id"]);
			if(false === $ok)
			die("bind_param failed");
			
			$ok = $stmt->execute();
			if(false === $ok)
			die("Execute failed");
			
			$stmt->store_result();			
			
			if($stmt->num_rows == 1) {
				$stmt->bind_result($user_id);
				$stmt->fetch();
				
				if($user_id != $_SESSION["id"])
					die("You don't have permission to delete that file");;
			}
		}
		
		$upload_handler = new UploadHandler();
	}
	
