<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<link rel="stylesheet" href="../css/styles.css">
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
		<?php } else { ?>
		NOT LOGGED IN!
		<?php } ?>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script src="../js/bootstrap.min.js"></script>
	</body>
</html>