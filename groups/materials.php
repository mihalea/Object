<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		
		<base href="//localhost/test/">	
		
		<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="css/styles.css">
		<link rel="stylesheet" href="css/jquery.fileupload.css">
		
		<title> Materials </title>
	</head>
	<body>
		<?php
			require_once("../config/db.php");
			require_once("../classes/Login.php");
			require_once("../classes/Permissions.php");
			require_once("../classes/GroupManager.php");
			
			$login = new Login();
			
			if ($login->isLogged() AND hasGroupFlag('u')) { 
			include("../views/navbar.php"); ?>
			
			
			<div class="container">
				<div class="page-header">
					<h2>
					<span><a href="groups"><?=$_SESSION["group_name"]?></a> <i class="fa fa-angle-right"></i> </span> Materials</span>
				</h2>
				
			</div>
			
			<div class="row">
				<div class="col-sm-8">
					<?php 
						if (isset($_GET["success"])) {
							echo '<div class="alert alert-success" style="position:relative; top:20px;"><p>Success! The material has been added to this group.</p></div>';
						}
						
					?>
				</div>
				<div class="col-sm-4">
					<div class="panel-group" role="tablist" id="accordion" aria-multiselectable="true">
						<div class="panel panel-default">
							<div class="panel-heading" id="sortHeading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion" href="#collapseSort" aria-expanded="true" aria-controls="collapseSort">
										Sort
									</a>
								</h4>
							</div>
							<div id="collapseSort" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="sortHeading">
								<div class="panel-body">
									<form class="form-horizontal" method="post" enctype="multipart/form-data">
										<div class="form-group">
											<label for='inSubjects' class="col-sm-3 control-label">Subject:</label>
											<div class="col-sm-9">
												<select class="form-control" id="inSubjects"  required>
													<?php
														foreach(GroupManager::getSubjects() as $sub)
														echo '<option value="'. $sub["id"] .'">' . $sub["name"] . '</option>';
													?>
												</select>
											</div>
										</div>
										
										<input type="submit" class="btn btn-success pull-right" value="Do sort!"></input>										
									</form>
								</div>
							</div>
						</div>
						<div class="panel panel-default">
							<div class="panel-heading" id="uploadHeading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion" href="#collapseUpload" aria-expanded="false" aria-controls="collapseUpload">
										Upload
									</a>
								</h4>
							</div>
							<div id="collapseUpload" class="panel-collapse collapse" role="tabpanel" aria-labelledby="uploadHeading">
								<div class="panel-body">
									<form class="form-horizontal" method="post" enctype="multipart/form-data">
										<div class="form-group">
											<label for='inTitle' class="col-sm-3 control-label">Title:</label>
											<div class="col-sm-9">
												<input type="text" id="inTitle" class="form-control" name="title" required></input>
											</div>
										</div>
										<div class="form-group">
											<label for='inSubjects' class="col-sm-3 control-label">Subject:</label>
											<div class="col-sm-9">
												<select class="form-control" id="inSubjects" name="subject"  required>
													<?php
														foreach(GroupManager::getSubjects() as $sub)
														echo '<option value="'. $sub["id"] .'">' . $sub["name"] . '</option>';
													?>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label for='inAuthor' class="col-sm-3 control-label">Author:</label>
											<div class="col-sm-9">
												<input type="text" id="inAuthor" class="form-control" name="author" required></input>
											</div>
										</div>
										<div class="form-group">
											<label for="inComment" class="col-sm-3 control-label">Comment: </label>
											<div class="col-sm-9">
												<textarea rows="3" id="inComment" name="comment" class="form-control" required></textarea>
											</div>
										</div>
										
										<span class="btn btn-primary fileinput-button">
											<i class="glyphicon glyphicon-plus"></i>
											<span>Select file</span>
											<input id="fileupload" type="file" name="files[]">
											
										</span>
										
									</form>
									
									<br />
									
									
								</div>
								<div id="progress" class="progress no-margin">
									<div class="progress-bar progress-bar-success"></div>
								</div>
							</div>
							
						</div>
					</div>
					
				</div>
			</div>
		</div>
		
		<?php } else {
			header('Location: index.php?ref=groups');
		} ?>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
		
		<script src="js/typeahead.bundle.min.js"></script>
		
		<script src="js/vendor/jquery.ui.widget.js"></script>
		<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
		<script src="js/jquery.iframe-transport.js"></script>
		<!-- The basic File Upload plugin -->
		<script src="js/jquery.fileupload.js"></script>
		<script>
			/*jslint unparam: true */
			/*global window, $ */
			$(function () {
				'use strict';
				// Change this to the location of your server-side upload handler:
				var url = 'transfer/';
				$('#fileupload').fileupload({
					url: url,
					dataType: 'json',
					done: function (e, data) {
						window.location.href="groups/materials.php?success";				
					},
					progressall: function (e, data) {
						var progress = parseInt(data.loaded / data.total * 100, 10);
						$('#progress .progress-bar').css(
						'width',
						progress + '%'
						);
					}
				}).prop('disabled', !$.support.fileInput)
				.parent().addClass($.support.fileInput ? undefined : 'disabled');
			});
		</script>
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
				$members = GroupManager::getMembers();
				
				echo "'" . $members[0]->name . "'";
				$len = count($members);
				for($i = 1 ; $i < $len ; $i++)
				echo ", '" . $members[$i]->name . "'";
			?>
			];
			
			$('#inAuthor').typeahead({
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
</body>
</html>