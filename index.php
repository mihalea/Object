<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/styles.css">
	<title> Object </title>
</head>

<body>
<?php

require_once("config/db.php");
require_once("classes/Login.php");

$login = new Login();

if ($login->isLogged()) { 
include("views/navbar.php");?>

<?php } else { ?>
	
	
		<div class="container form-div">
			<form method="post" action="index.php" class="form-signin">
			
				<h2 class="form-signin-heading">Object <small> beta </small> </h2>
			
				<label for="username" class="sr-only">Username: </label>
				<input id="username" name="u" type="text"  class="form-control top" placeholder="Username" required />
				
				<label for="password" class="sr-only">Password: </label>
				<input id="password" name="p" type="password" class="form-control bottom" placeholder="Password" required />
				
				
				<input type="submit" name="login" value="Log in" class="btn btn-lg btn-primary btn-block" />
			
			</form>
		
			<a href="register.php" id="register">Register</a>
		</div>
	
		
	
<?php } ?>


	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
</body>
</html>