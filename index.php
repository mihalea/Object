<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/styles.css">
	<title> Object </title>
</head>

<body>
<?php

require_once("config/db.php");
require_once("classes/Login.php");

$login = new Login();

if($login->errors)
	foreach($login->errors as $error)
		echo $error;

if ($login->isLogged()) { 
include("views/navbar.php");?>

<?php } else { ?>
	
	
		<div class="container form-div">
			<form method="post" class="form-signin">
			
				<h2 class="form-signin-heading">Object <small> beta </small> </h2>
			
				<label for="username" class="sr-only">Username: </label>
				<input id="username" name="u" type="text"  class="form-control top" placeholder="Username" pattern="[a-zA-Z0-9]{6,64}" required />
				
				<label for="password" class="sr-only">Password: </label>
				<input id="password" name="p" type="password" class="form-control bottom" placeholder="Password" pattern=".{6,64}" required />
				
				
				<input type="submit" name="login" value="Log in" class="btn btn-lg btn-primary btn-block" />
			
			</form>
		
			<a href="register.php" id="register">Register</a>
			
			<?php 
				if (isset($_GET["error"]))
					echo '<div class="alert alert-danger" style="position:relative; top:20px;"><p>Failed to login</p></div>';
				elseif (isset($_GET["register"]))
					echo '<div class="alert alert-success" style="position:relative; top:20px;"><p>Successfully registered</p></div>';
				elseif(isset($_POST["p"]))
					echo password
			?>
		</div>
		
		
	
		
	
<?php } ?>


	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
</body>
</html>