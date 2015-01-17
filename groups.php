<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<link rel="stylesheet" href="css/styles.css">
		<title> Groups </title>
	</head>
	<body>
		<?php
			require_once("config/db.php");
			require_once("classes/Login.php");
			require_once("classes/GroupManager.php");
			
			$login = new Login();
			$manager = new GroupManager();
			
			if(isset($_GET["id"]))
				$group = new Group($_GET["id"]);
			
			if ($login->isLogged()) { 
			include("views/navbar.php"); 
			
				if(count($_GET) == 0) { ?>
				
				<div class="container">
					<div class="page-header">
						<h2> 
							<span>Your groups</span> 
							<button type="button" class="btn btn-primary pull-right" data-toggle="collapse" data-target="#createGroupCollapse" aria-expanded="false" aria-controls="createGroupCollapse"><span class="glyphicon glyphicon-plus-sign">&nbsp;</span>Create group</button>
						</h2>
						
						<div class="collapse" id="createGroupCollapse">
							<br />
						
							<form class="form-horizontal" id="createGroup" method="POST">
								<div class="form-group">
									<label for="inputName" class="col-sm-2 control-label">Name: </label>
									<div class="col-sm-10">
										<input type="text" id="inputName" name="name" pattern=".{3,32}" class="form-control" required/>
									</div>
								</div>
								
								<input type="submit" name="group" id="submit" class="btn btn-success pull-right"></input>
								<div class="clearfix"></div>
							</form>
						</div>
					</div>		

					<?php 
					if(isset($GLOBALS["ok"]))
					{
						if($GLOBALS["ok"] == 1) { ?>
						
						<div class="alert alert-success">
							<p> Group successfully created. </p>
						</div>
						
						<?php } else { ?>
					
						<div class="alert alert-error">
							<p> Failed to create group. </p>
						</div>
					
						<?php }
					} 
					
					$manager->getGroups();
					?>
				</div>
				
				<?php } elseif (isset($group)) { ?>
				
				<script type="text/javascript">
					document.title = "<?php echo $group->getName(); ?>"
				</script>
				
				<div class="container">
					<div class="row">
							<div class="page-header">
								<h1>
									<span><?php echo $group->getName(); ?></span>
									<button type="button" class="btn btn-primary pull-right" data-toggle="collapse" data-target="#newMessageCollapse" aria-expanded="false" aria-controls="newMessageCollapse"><span class="glyphicon glyphicon-pencil">&nbsp;</span>New post</button>
								</h1>
							</div>
					</div>
					<div class="row">
						<div class="col-xs-9">
							<div class="collapse" id="newMessageCollapse">						
								<form id="addEvent" method="POST">
									<div class="form-group">
										<label for="inputPost" class="control-label">Comment: </label>
										<textarea rows="4" id="inputPost" name="post" class="form-control"/></textarea>
									</div>
									
									<input type="submit" name="event" id="submit" class="btn btn-success pull-right"></input>
									
									<div class="clearfix"></div>
								</form>
								
								<hr />
							</div>
							
							<p> Main text </p>
						</div>
						<div class="col-xs-3">
							<p> Sidetext </p>
						</div>
					</div>
				</div>
		
		
		
		<?php } } else { ?>
		NOT LOGGED IN!
		<?php } ?>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
	</body>
</html>