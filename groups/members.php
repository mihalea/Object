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
		
		<title> Members </title>
	</head>
	<body>
		<?php
			require_once("../config/db.php");
			require_once("../classes/Login.php");
			require_once("../classes/GroupManager.php");
			require_once("../classes/Helper.php");
			require_once("../classes/Permissions.php");
			
			$login = new Login();
			$group = new GroupManager();
			
			if ($login->isLogged()) { 
			include("../views/navbar.php"); ?>
			
			<div class="container">
				<div class="page-header red-pageheader">
					<h2>
					<span><a href="groups"><?=$_SESSION["group_name"]?></a> <i class="fa fa-angle-right"></i> </span> Members</span>
					<?php if(hasGroupFlag('a')) { ?>
						<div class="pull-right">
									<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#user_modal"><i class="fa fa-user-plus"></i>&nbsp;Add member</button>
						</div>
								<?php } ?>
				</h2>				
			</div>
			
			<div class="row">
				<div class="col-md-9">
					<div class="list-group">
						<?php
							$members = GroupManager::getMembers();
							
							foreach($members as $member) { ?>
							<div class="list-group-item">
								<?php echo $member["name"];
								if($member["flag"] == 'a')
									echo '<small><span class="text-info">&nbsp;[Admin]</span></small>'; 
								elseif($member["flag"] == 'o')
									echo '<small><span class="text-info">&nbsp;[Owner]</span></small>';
								?>
								<div class="pull-right">
									<div class="dropdown">
										<i class="fa fa-caret-down cursor-hand" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="toggle"></i>
										<ul class="dropdown-menu" role="menu" aria-labelledby="toggle">
											<?php
												if($member["flag"] != 'a')
													echo '<li class="drop-item"><a user_id="' . $member["user_id"] . '" class="cursor-hand kick">Kick</a></li>'
											?>
											
											<?php
												if($member["flag"] != 'a')
													echo '<li class="drop-item"><a user_id="' . $member["user_id"] . '" class="cursor-hand admin">Make admin</a></li>'
											?>
											
											<?php
												if($member["flag"] == 'a')
													echo '<li class="drop-item"><a user_id="' . $member["user_id"] . '" class="cursor-hand user">Remove admin</a></li>'
											?>
										</ul>
									</div>
								</div>
								<div class="clearfix"></div>
							</div>
							<?php }
						?>
					</div>
				</div>
				<div class="col-md-3">
					<?php include("../views/side_panel.php"); ?>
				</div>
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
		
		<?php } else {
			header('Location: /test/index.php?ref=members');
		} ?>
		<script src="js/jquery-2.1.3.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
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
			$('#btnAddUser').click(function() {
				$('#user_form').submit();
				});
			
			$('.kick').click(function() {
				var user_id = $(this).attr("user_id");
				$.post( 
					"groups/members.php" , { user_id: user_id, remUser: 'true' } 
				).done( function() {
					window.location.href = 'groups/members.php';
				});
			});
			
			$('.admin').click(function() {
				var user_id = $(this).attr("user_id");
				$.post( 
					"groups/members.php" , { user_id: user_id, makeAdmin: 'true' } 
				).done( function() {
					window.location.href = 'groups/members.php';
				});
			});
			
			$('.user').click(function() {
				var user_id = $(this).attr("user_id");
				$.post( 
					"groups/members.php" , { user_id: user_id, makeUser: 'true' } 
				).done( function() {
					window.location.href = 'groups/members.php';
				});
			});
		</script>
</body>
</html>