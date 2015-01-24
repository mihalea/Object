<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		
		<base href="//localhost/test/">
		
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
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
			header('Location: index.php?ref=groups');
		} ?>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
	</body>
</html>