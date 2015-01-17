<?php
require_once("classes/Register.php");

$register = new Register();

if($register->messages)
	foreach($register->messages as $message)
		echo $message;
?>
<html>

<head>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/styles.css">
	<title>Register</title>
</head>

<body>
	<div class="container form-div">
		<form method="post" action="register.php" class="form-signin">
		
			<h2 class="form-signin-header">Register</h2>

			<label for="username" class="sr-only">Username</label>
			<input type="text" id="username" name="uname" placeholder="Username" class="form-control top" pattern="[a-zA-Z0-9]{6,64}" required />
			
			<label for="email" class="sr-only">Email</label>
			<input type="text" id="email" name="email" placeholder="Email" class="form-control middle" pattern=".{6,64}" required />
			
			<label for="password" class="sr-only">Password</label>
			<input type="password" id="password" name="pass" placeholder="Password" class="form-control middle" pattern=".{6,64}" required />
			
			<label for="password_repeat" class="sr-only">Repeat password</label>
			<input type="password" id="password_repeat" name="repeat" placeholder="Confirm password" class="form-control bottom" pattern=".{6,64}" required />
			
			<input type="submit" name="register" value="Register" class="btn btn-lg btn-primary btn-block" />

		</form>
	</div>
	
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
<body>
</html>