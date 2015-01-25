<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		
		<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
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
			require_once("../classes/EventManager.php");
			require_once("../classes/Helper.php");
			
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
								
								<input type="submit" name="newGroup"  value="Create" id="submit" class="btn btn-success pull-right top-buttons fixed-width"></input>
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
							} elseif(isset($_GET["error"]) AND $_GET["error"] == "comment") {
							echo '<div class="alert alert-danger""><p>Error! Failed to comment</p></div>';
						}
						
						$groups = GroupManager::getGroups();
						if(is_array($groups) AND count($groups) > 0) {
							echo '<div class="list-group">';
							foreach($groups as $g) {
								echo '<a href="groups/?setid=' . $g["group_id"] . '" class="list-group-item"><h4>' . $g["name"] . '</h4></a>';
							}
							echo '<div class="list-group">';
							} else {
							echo '<div class="alert alert-info""><p>Heads up! There are no groups available</p></div>';
						}
					?>
				</div>
				
				<?php } elseif (isset($_SESSION["group_id"]) AND hasGroupFlag('u')) {; ?>
				
				<script type="text/javascript">
					document.title = "<?= $_SESSION['group_name']?>";
				</script>
				
				<div class="container">
					
					<div class="page-header red-pageheader">
						<h2>
							<span><?= $_SESSION["group_name"] ?></span>
							<button type="button" class="btn btn-primary pull-right" data-toggle="collapse" data-target="#newMessageCollapse" aria-expanded="false" aria-controls="newMessageCollapse"><span class="glyphicon glyphicon-pencil">&nbsp;</span>New post</button>
						</h2>
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
								foreach($posts as $post) { ?>
								<div class="panel panel-default">
									<div class="panel-heading red-heading">
										<h5><span class="glyphicon glyphicon-user"></span>&nbsp;
											<?php
												echo $post["name"];
												if(!empty($post["file_id"])) {
													echo ' uploaded a file';
													echo '<div class="pull-right top-buttons">
													<a href="groups/materials.php?file_id=' . $post["file_id"] . '" class="btn btn-default fixed-width"><i class="fa fa-file"></i>&nbsp;View file</a>
													</div>';
													} elseif(!empty($post["event_id"])) {
													echo ' created a new event';
													echo '<div class="pull-right top-buttons">
													<a href="groups/events.php?event_id=' . $post["event_id"] . '" class="btn btn-default fixed-width"><i class="fa fa-calendar"></i>&nbsp;View event</a>
													</div>';
												}
												
											?>
											<!--<div class="pull-right">
												<span class="glyphicon glyphicon-remove"></span>
											</div>-->
										</h5>
									</div>
									
									<div class="panel-body">
										<?=$post["text"]?>
									</div>
									<div class="panel-footer">
										<small>
											<?php
												$id = $post["post_id"];
												echo '<span class="toggleComment" value="' . $id . '" count="' . (empty($post["count"]) ? 0 : $post["count"]) . '" target="#comments_'. $id .'" data-toggle="collapse" href="#comment_'. $id .'" aria-expanded="false" aria-controls="comment_' . $id . '">';
												if(empty($post["count"])) 
												{
													echo '<span class="text-info cursor-hand">New comment</span>';
												} 
												else 
												{
													echo '<span class="text-info cursor-hand">' . $post["count"] . ' comment' . ($post["count"] > 1 ? "s" : "") . '</span>';
												}
												echo '</span>';
											?>
											<div class="pull-right">
												<span class="glyphicon glyphicon-calendar"></span>&nbsp;
												<?=timeDifference($post["date"])?>
											</div>
										</small>
										
										
										<div class="collapse" id="comment_<?=$id?>">
										
											<div class="comment-block">
												<div id="comments_<?=$id?>">
												</div>
												<form method="post" class="form" style="margin-top:15px">
													<div class="form-group">
														<textarea rows="3" name="text" class="form-control autogrow"></textarea>
													</div>
													
													<input type="hidden" name="post_id" value="<?=$id?>" />
													<input type="submit" name="comment" value="Comment" class="btn btn-success pull-right" />
												</form>
											</div>
										</div>
										
										<div class="clearfix"></div>
									</div>
								</div>
								<?php }
							}
						?>
					</div>
					<div class="col-sm-3">
						
						<div class="list-group">
							<span class="list-group-item red-heading">Dashboard</span>
							<a href="groups/schedule.php" class="list-group-item">Schedule</a>
							<a href="groups/events.php" class="list-group-item">Events</a>
							<a href="groups/materials.php" class="list-group-item">Materials</a>
							<a href="#" class="list-group-item">Members</a>
						</div>
						
						<?php 
							$events = EventManager::getEvents(7, 10);
							if(is_array($events) AND count($events) > 0) { ?>
							<div class="list group">
								<span class="list-group-item red-heading">Upcoming events</span>										
								<?php
									foreach($events as $e) {
										echo '<a href="groups/events.php?event_id=' . $e["id"] . '" class="list-group-item"> '
										. substr($e["title"], 0, 15) , (strlen($e["title"]) > 15 ? "..." : "") . 
										'<span class="badge visible-lg-inline-block">' . timeDifference($e["date"]) . '</span></a>';
									}
								?>
							</div>
						<?php } ?>
					</div>
				</div>
				
				<div id="template" style="visibility: hidden;">
					<strong><span class="text-info">Name</span></strong>&nbsp;
					<span class="text"></span>
					<br />
					<span class="text-muted">Hours ago</span>
				</div>
			</div>
			
			
			
		<?php } } else { 
			header('Location: /test/index.php?ref=groups');
	 } ?>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
	<script src="js/jquery.autogrowtextarea.min.js"></script>	
	
	<script>
		$(document).ready(function() {
			$('.autogrow').autoGrow();
			
			$('.toggleComment').click( function() {
				var parent = $(this);
				var post_id = parent.attr("value");
				var target = $(document).find(parent.attr("target"));
				var count = parent.attr('count');
				var hasComments = parent.attr("hasComments");
				
				var attr = parent.attr("batch");
				if(typeof attr == typeof undefined || attr === false)
					parent.attr("batch", "0");
				else {
					if(attr == -1) {
						return;
					}
				
					var incr = parseInt(attr + 1);
					parent.attr("batch", incr);
				}
					
				var batch = parent.attr("batch");

				$.ajax({
					dataType: "json",
					url: "groups/ajax.php?comments&post_id=" + post_id + "&batch=" + batch,
					})
				.done(function( data ) {
					if(data.length == 0) {
						parent.attr("batch", "-1");
						parent.attr("style", "visibility: hidden;");
						return;
					}
				
					$.each(data, function (index, comment) {
						var comm = $('#template').clone();
						comm.attr('style', 'margin-bottom: 5px');
						comm.find('span.text-info').text(comment.name);
						comm.find('span.text').text(comment.text);
						comm.find('span.text-muted').text(comment.ago);
						
						if(batch == 0)
							target.append(comm);
						else
							target.prepend(comm);
					});

					//$(this).attr("hasComments", "true");
					parent.attr("hasComments", "true");
					parent.attr("href", "");
					if(count > 10) {
						parent.children('span').text("View previous comments");
					} else if (count < 10) {
						parent.attr("batch", "-1");
						parent.attr("style", "visibility: hidden;");
					}
				});
			});
			
		});
		
	</script>
</body>
</html>