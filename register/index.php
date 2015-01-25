<?php
require_once("../classes/Register.php");

$register = new Register();

if($register->errors)
foreach($register->errors as $error)
		echo $error;
?>
<html>
	
<head>
	<meta charset="utf-8">
	
	<?php
			$path = realpath($_SERVER["DOCUMENT_ROOT"] . "/test/") . "/";
			require_once($path . "config/site.php");
			echo '<base href="' . SITE_ROOT . '/">';
		?>
		
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="css/styles.css">
	
	<title>Register</title>

</head>

<body>
	<div class="container form-div">
		<form method="post" class="form-signin">
		
			<h2 class="form-signin-header">Register</h2>

			<label for="username" class="sr-only">Username</label>
			<input type="text" id="username" name="user" placeholder="Username" class="form-control top" pattern="[a-zA-Z0-9_]{6,64}" required />
			
			<label for="name" class="sr-only">Name</label>
			<input type="text" id="name" name="name" placeholder="Name" class="form-control top" pattern="[a-zA-Z0-9]{6,64}" required />
			
			<label for="email" class="sr-only">Email</label>
			<input type="text" id="email" name="email" placeholder="Email" class="form-control middle" pattern=".{6,64}" required />
			
			<label for="password" class="sr-only">Password</label>
			<input type="password" id="password" name="pass" placeholder="Password" class="form-control middle" pattern=".{6,64}" required />
			
			<label for="password_repeat" class="sr-only">Repeat password</label>
			<input type="password" id="password_repeat" name="repeat" placeholder="Confirm password" class="form-control bottom" pattern=".{6,64}" required />
			
			<input type="submit" name="register" value="Register" class="btn btn-lg btn-primary btn-block" />

		</form>
		
		<?php 
				if (isset($_GET["error"]))
					echo '<div class="alert alert-danger" style="position:relative; top:20px;"><p>Failed to register</p></div>';
			?>
		
	</div>
	
		<script src="js/jquery-1.11.2.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
<body>
</html>