<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		
		<?php
			$path = realpath($_SERVER["DOCUMENT_ROOT"] . "/test/") . "/";
			require_once($path . "config/site.php");
			echo '<base href="' . SITE_ROOT . '/">';
		?>
		
		<link href="css/font-awesome.min.css" rel="stylesheet">
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" href="css/styles.css">
		
		<title> Groups </title>
	</head>
	<body>
		<?php
			
			$path = realpath($_SERVER["DOCUMENT_ROOT"] . "/test/") . "/"; require_once($path . "config/site.php");
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
									<label for="inputName" class="col-md-2 control-label">Name: </label>
									<div class="col-md-10">
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
							<div class="pull-right">
								<?php if(hasGroupFlag('a')) { ?>
									<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#user_modal"><i class="fa fa-user-plus"></i>&nbsp;Add member</button>
								<?php } ?>
								<button type="button" class="btn btn-primary" data-toggle="collapse" data-target="#newMessageCollapse" aria-expanded="false" aria-controls="newMessageCollapse"><i class="fa fa-pencil"></i>&nbsp;New post</button>
							</div>
						</h2>
					</div>
					
					<div class="row">
						<div class="col-md-9">
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
					<div class="col-md-3">
						<?php include("../views/side_panel.php"); ?>
					</div>
				</div>
				
				<div id="template" style="visibility: hidden;">
					<strong><span class="text-info">Name</span></strong>&nbsp;
					<span class="text"></span>
					<br />
					<span class="text-muted">Hours ago</span>
				</div>
				
			</div>
			
			<div class="modal fade" id="user_modal" tabindex="-1" role="dialog" aria-labelledby="modal_label" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title" id="modal_label">Add new user</h4>
						</div>
						<div class="modal-body">
							<form method="post" class="form" id="user_form">
								<div class="form-group">
									<label class="control-label">Name</label>
									<input class="form-control" type="text" id="user" name="name" />
									<input type="hidden" name="newUser" />
								</div>
							</form>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default fixed-width" data-dismiss="modal">Close</button>
							<button type="button" class="btn btn-primary fixed-width" id="btnAddUser">Add</button>
						</div>
					</div>
				</div>
			</div>
			
			
			
			<?php } } else { 
			header('Location: /test/index.php?ref=groups');
		} ?>
		<script src="js/jquery-1.11.2.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/moment.js"></script>
		<script src="js/jquery.autogrowtextarea.min.js"></script>	
		<script src="js/bootstrap-datetimepicker.js"></script>		
		
		<script src="js/typeahead.bundle.min.js"></script>
		
		<script>
			var substringMatcher = function(strs) {
				return function findMatches(q, cb) {
					var matches, substrRegex;
					
					// an array that will be populated with substring matches
					matches = [];
					
					// regex used to determine if a string contains the substring `q`
					substrRegex = new RegExp(q, 'i');
					
					// iterate through the pool of strings and for any string that
					// contains the substring `q`, add it to the `matches` array
					$.each(strs, function(i, str) {
						if (substrRegex.test(str)) {
							// the typeahead jQuery plugin expects suggestions to a
							// JavaScript object, refer to typeahead docs for more info
							matches.push({ value: str });
						}
					});
					
					cb(matches);
				};
			}; 
			
			var substringMatcher = function(strs) {
				return function findMatches(q, cb) {
					var matches, substrRegex;
					
					// an array that will be populated with substring matches
					matches = [];
					
					// regex used to determine if a string contains the substring `q`
					substrRegex = new RegExp(q, 'i');
					
					// iterate through the pool of strings and for any string that
					// contains the substring `q`, add it to the `matches` array
					$.each(strs, function(i, str) {
						if (substrRegex.test(str)) {
							// the typeahead jQuery plugin expects suggestions to a
							// JavaScript object, refer to typeahead docs for more info
							matches.push({ value: str });
						}
					});
					
					cb(matches);
				};
			};
			var members = [<?php
				$members = getAllUsers();
				
				echo "'" . $members[0]["name"] . "'";
				$len = count($members);
				for($i = 1 ; $i < $len ; $i++)
				echo ", '" . $members[$i]["name"] . "'";
			?>
			];
			
			$('#user').typeahead({
				hint: true,
				highlight: true,
				minLength: 1
			},
			{
				name: 'members',
				displayKey: 'value',
				source: substringMatcher(members)
			}); 
		</script>
		
		<script>
			$(document).ready(function() {
				$('.autogrow').autoGrow();
				
				$('#btnAddUser').click(function() {
					$('#user_form').submit();
				});
				
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