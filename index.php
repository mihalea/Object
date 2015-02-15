<!DOCTYPE html>
<html lang="en">
	
	<head>
		<meta charset="utf-8">
		
		<?php $path = realpath($_SERVER["DOCUMENT_ROOT"] . "/test/") . "/"; require_once($path . "config/site.php"); echo '<base href="' . SITE_ROOT . '/">'; ?>	
		
		<link href="css/font-awesome.min.css" rel="stylesheet">
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="css/styles.css">
		<title> Object </title>
	</head>
	
	<body>
		<?php
			
			require_once("config/db.php");
			require_once("classes/Login.php");
			require_once("classes/Frontpage.php");
			require_once("classes/Helper.php");
			require_once("classes/GroupManager.php");
			
			$login = new Login();
			$group = new GroupManager();
			
			if($login->errors)
			foreach($login->errors as $error)
			echo $error;
			
			if ($login->isLogged()) { 
			include("views/navbar.php");?>
			
			<div class="container">
				<?php
					if(isset($_GET["error"]) AND $_GET["error"] == "post") {
						echo '<div class="alert alert-danger""><p>Error! Failed to post your message.</p></div>';
					}
					
					$posts = Frontpage::getPosts();
					
					if(is_array($posts) AND count($posts) > 0) {
						foreach($posts as $post) { ?>
						<div class="panel panel-default">
							<div class="panel-heading red-heading">
								<h5><span class="glyphicon glyphicon-user"></span>&nbsp;
									<?php
										$title = $post["name"];
										if(!empty($post["file_id"])) {
											$title = $title.' uploaded a file';
											echo '<div class="pull-right top-buttons">
											<a href="groups/materials.php?file_id=' . $post["file_id"] . '" class="btn btn-default fixed-width"><i class="fa fa-file"></i>&nbsp;View file</a>
											</div>';
											} elseif(!empty($post["event_id"])) {
											$title = $title.' created a new event';
											echo '<div class="pull-right top-buttons">
											<a href="groups/events.php?event_id=' . $post["event_id"] . '" class="btn btn-default fixed-width"><i class="fa fa-calendar"></i>&nbsp;View event</a>
											</div>';
										}
										echo $title . " on " . $post["group_name"];
										
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
										echo '<span class="toggleComment" value="' . $id . '" count="' . (empty($post["count"]) ? 0 : $post["count"]) . '" target="#comments_'. $id .'" data-toggle="collapse" aria-expanded="false" aria-controls="comment_' . $id . '">';
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
											<input type="hidden" name="group_id" value="<?=$post["group_id"]?>" />
											
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
				
				<div id="template" style="visibility: hidden;">
					<strong><span class="text-info">Name</span></strong>&nbsp;
					<span class="text"></span>
					<br />
					<small><span class="text-muted">Hours ago</span><small>
				</div>
			</div>
			
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
				
				<a href="register" id="register">Register</a>
				
				<?php 
					if (isset($_GET["error"]))
					echo '<div class="alert alert-danger" style="position:relative; top:20px;"><p>Failed to login</p></div>';
					elseif (isset($_GET["register"]))
					echo '<div class="alert alert-success" style="position:relative; top:20px;"><p>Successfully registered</p></div>';
				?>
			</div>
			
			
			
			
			
		<?php } ?>
		
		
		<script src="js/jquery-2.1.3.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script>
			$(document).ready(function() {			
			
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
						url: "<?=SITE_ROOT?>classes/ajax.php?comments&post_id=" + post_id + "&batch=" + batch
					})
					.done(function( data ) {
						$('#comment_' + post_id).collapse('show');
					
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
							
							if(batch == 0) {
							target.append(comm);
							}
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