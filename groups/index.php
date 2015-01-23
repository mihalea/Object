<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="../css/styles.css">
		
		<base href="//localhost/test/">
		
		<title> Groups </title>
	</head>
	<body>
		<?php
			
			require_once("../config/site.php");
			require_once("../config/db.php");
			require_once("../classes/Login.php");
			require_once("../classes/GroupManager.php");
			
			$login = new Login();
			$manager = new GroupManager();
			
			if ($login->isLogged()) { 
			include("../views/navbar.php"); 
			
				if(isset($_GET["select"])) { ?>
				
				<div class="container">
					<div class="page-header red-pageheader">
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
								
								<input type="submit" name="newGroup" id="submit" class="btn btn-success pull-right"></input>
								<div class="clearfix"></div>
							</form>
						</div>
					</div>		

					<?php 
					if (isset($_GET["error"]) AND $_GET["error"] == "create") {
						echo '<div class="alert alert-danger""><p>Error! Failed to create new group</p></div>';
					} elseif (isset($_GET["error"]) AND $_GET["error"] == "noAccess") {
						echo '<div class="alert alert-danger""><p>Error! You don\'t have acces to that group.</p></div>';
					} elseif(isset($_GET["error"]) AND $_GET["error"] == "set") {
						echo '<div class="alert alert-danger""><p>Error! Failed to open that group</p></div>';
					}
	
					GroupManager::printGroups();
					?>
				</div>
				
				<?php } elseif (isset($_SESSION["group_id"]) AND hasGroupFlag('u')) {; ?>
				
				<script type="text/javascript">
					document.title = "<?= $_SESSION['group_name']?>";
				</script>
				
				<div class="container">
					<div class="row">
							<div class="page-header red-pageheader">
								<h2>
									<span><?= $_SESSION["group_name"] ?></span>
									<button type="button" class="btn btn-primary pull-right" data-toggle="collapse" data-target="#newMessageCollapse" aria-expanded="false" aria-controls="newMessageCollapse"><span class="glyphicon glyphicon-pencil">&nbsp;</span>New post</button>
								</h2>
							</div>
					</div>
					<div class="row">
						<div class="col-sm-9">
							<div class="collapse" id="newMessageCollapse">						
								<form id="addEvent" method="POST">
									<div class="form-group">
										<label for="inputPost" class="control-label">Post: </label>
										<textarea rows="4" id="inputPost" name="post" class="form-control"/></textarea>
									</div>
									
									<input type="submit" name="newPost" id="submit" class="btn btn-success pull-right"></input>
									
									<div class="clearfix"></div>
								</form>
								
								<hr />
							</div>
							
							<?php
								if(isset($_GET["error"]) AND $_GET["error"] == "post") {
									echo '<div class="alert alert-danger""><p>Error! Failed to post your message.</p></div>';
								}
							
								$posts = GroupManager::getPosts();
								
								if(is_array($posts) AND count($posts) > 0) {
									foreach($posts as $post) {
										echo '<div class="panel panel-default post"><div class="panel-body">';
										echo '<div class="pull-right"><span class="glyphicon glyphicon-remove"></span></div><br />';
										echo $post->text;
										echo '<hr />';
										echo '<div class="pull-right"><small>';
										echo '<span class="glyphicon glyphicon-user"></span>&nbsp;'
											. $post->user->name . '&nbsp;&nbsp;';
										echo '<span class="glyphicon glyphicon-calendar"></span>&nbsp;'
											. $post->getTimeAgo() . '</small></div>';
										echo '</div></div>';
									}
								}
							?>
						</div>
						<div class="col-sm-3">

							<div class="list-group">
								<span class="list-group-item red-heading">Dashboard</span>
								<a href="groups/schedule.php" class="list-group-item">Schedule</a>
								<a href="groups/materials.php" class="list-group-item">Materials</a>
								<a href="#" class="list-group-item">Members</a>
							</div>
						</div>
					</div>
				</div>
		
		
		
		<?php } } else { ?>
		NOT LOGGED IN!
		<?php } ?>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
	</body>
</html>