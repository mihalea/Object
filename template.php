<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<?php
			$path = realpath($_SERVER["DOCUMENT_ROOT"] . "/test/") . "/";
			require_once($path . "config/site.php");
			echo '<base href="' . SITE_ROOT . '/">';
		?>
		
		
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" href="css/styles.css">
		
		<title> Template </title>
	</head>
	<body>
		<?php
			require_once("../config/db.php");
			require_once("../classes/Login.php");
			
			$login = new Login();
			
			if ($login->isLogged()) { 
			include("../views/navbar.php"); ?>
		LOGGED IN!
		<?php } else {
			header('Location: /test/index.php?ref=groups');
		} ?>
		<script src="js/jquery-2.1.3.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
	</body>
</html>