<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		
		<base href="//localhost/test/">	
		
		<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="css/bootstrap-datetimepicker.min.css">
		<link rel="stylesheet" href="css/jquery.fileupload.css">
		<link rel="stylesheet" href="css/styles.css">
		
		<style>
			.fixed-width {
				width:150px;
			}
		</style>
		
		<title> Materials </title>
	</head>
	<body>
		<?php
			require_once("../config/db.php");
			require_once("../config/site.php");
			require_once("../classes/Login.php");
			require_once("../classes/Permissions.php");
			require_once("../classes/GroupManager.php");
			require_once("../classes/Helper.php");
			
			$login = new Login();
			
			if ($login->isLogged() AND hasGroupFlag('u')) { 
			include("../views/navbar.php"); ?>
			
			
			<div class="container">
				<div class="page-header red-pageheader">
					<h2>
					<span><a href="groups"><?=$_SESSION["group_name"]?></a> <i class="fa fa-angle-right"></i> </span> Materials</span>
				</h2>
				
			</div>
			
			<div class="row">
				<div class="col-sm-8">
					<?php 
						if (isset($_GET["success"])) {
							echo '<div class="alert alert-success"><p>Success! The material has been added to this group.</p></div>';
						}

												
						$materials = GroupManager::getMaterials();
						if(is_array($materials) AND count($materials) > 0) {
							foreach($materials as $mat) { 
								
									
								
									if(!empty($_GET["subject"]) AND$_GET["subject"] != 0 AND $_GET["subject"] != $mat["subject_id"])
										continue;
									if(!empty($_GET["title"]) AND !(preg_match("/" . $_GET["title"] . "/i", $mat["title"]) OR preg_match("/" . $_GET["title"] . "/i", $mat["filename"])))
										continue;
									if(!empty($_GET["time_start"]) AND strtotime($_GET["time_start"]) >= strtotime($mat["date"]))
										continue;
									if(!empty($_GET["time_end"]) AND strtotime($_GET["time_end"]) <= strtotime($mat["date"]))
										continue;
									
								?>
							
								<div class="panel panel-default">
									<div class="panel-heading">
										<div class="row">
											<div class="col-sm-9">
												<h4 class="break-h"><?=$mat["title"]?></h4>
												<h5 class="break-h"><?=$mat["subject"]?></h5>
											</div>
											<div class="col-sm-3">
												<a href="transfer/?file_id=<?=$mat["file_id"]?>" class="btn btn-default center-block fixed-width"><i class="fa fa-link"></i>&nbsp;Download</a>
												<?php
												if($mat['uploader_id'] == $_SESSION['id'])
													echo '<button class="btn btn-default delete center-block fixed-width" style="margin-top: 5px;" value=' . $mat['file_id'] . '><i class="fa fa-remove"></i>&nbsp;Delete</button>'
												?>
											</div>
											
										</div>
										
									</div>
									<div class="panel-body">
										<?php if(!empty($mat["comment"])) {
											echo '<blockquote><p>' . $mat["comment"] . '</p></blockquote>';
										}?>
										
										<p><span class="text-info">Filename:</span> <?=$mat['filename']?></p>
										<p><span class="text-info">Author:</span> <?=$mat['author']?></p>
										<p><span class="text-info">Size:</span> <?=formatBytes($mat['size'], 2)?></p>
										
										<hr />
										<div class="pull-right">
											<small>
												<span class="glyphicon glyphicon-user"></span> <?=$mat["uploader"]?>
												&nbsp;&nbsp;
												<span class="glyphicon glyphicon-calendar"></span> <?=timeDifference($mat["date"])?>
											</small>
										</div>
										
									</div>
								</div>
								
							<?php }
						} else {
							echo '<div class="alert alert-info"><p>Heads up! There are not materials to show.</p></div>';
						}
					?>
				</div>
				<div class="col-sm-4">
					<div class="panel-group" role="tablist" id="accordion" aria-multiselectable="true">
						<div class="panel panel-default">
							<div class="panel-heading red-heading" id="sortHeading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion" href="#collapseSort" aria-expanded="true" aria-controls="collapseSort">
										Filter <i class="fa fa-caret-down"></i>
									</a>
								</h4>
							</div>
							<div id="collapseSort" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="sortHeading">
								<div class="panel-body">
									<form class="form-horizontal">
										<div class="form-group">
											<label for='sortTitle' class="col-sm-3 control-label">Title:</label>
											<div class="col-sm-9">
												<input type="text" id="sortTitle" class="form-control" name="title"></input>
											</div>
										</div>
										<div class="form-group">
											<label for='sortSubject' class="col-sm-3 control-label">Subject:</label>
											<div class="col-sm-9">
												<select class="form-control" id="sortSubject" name="subject"  required>
													<?php
														echo '<option value="0">None</option>';
														foreach(GroupManager::getSubjects() as $sub)
														echo '<option value="'. $sub["id"] .'">' . $sub["name"] . '</option>';
													?>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label for="sortStart" class="col-sm-3 control-label">Start time: </label>
											<div class="col-sm-9">
												<div class="input-group date" id="timeStart">
													<input type="text" id="sortStart" name="time_start" class="form-control"  data-date-format="YYYY-MM-DD"/>
													<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
												</div>
											</div>
										</div>
										<div class="form-group">
											<label for="sortEnd" class="col-sm-3 control-label">End time: </label>
											<div class="col-sm-9">
												<div class="input-group date" id="timeEnd">
													<input type="text" id="sortEnd" name="time_end" class="form-control"  data-date-format="YYYY-MM-DD"/>
													<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
												</div>
											</div>
										</div>
										
										<input type="submit" class="btn btn-success pull-right" name="filter" value="Do it!"></input>	
									</form>
									<button class="btn btn-primary pull-right" id="reset-btn" style="margin-right: 5px;">Reset</button>
								</div>
							</div>
						</div>
						<div class="panel panel-default">
							<div class="panel-heading red-heading" id="uploadHeading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion" href="#collapseUpload" aria-expanded="false" aria-controls="collapseUpload">
										Upload <i class="fa fa-caret-down"></i>
									</a>
								</h4>
							</div>
							<div id="collapseUpload" class="panel-collapse collapse" role="tabpanel" aria-labelledby="uploadHeading">
								<div class="panel-body">
									<form class="form-horizontal" method="POST" enctype="multipart/form-data">
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
												<textarea rows="3" id="inComment" name="comment" class="form-control"></textarea>
											</div>
										</div>
										
										<div class="form-group">
											<label for="inFile" class="col-sm-3 control-label">File: </label>
											<div class="col-sm-9">
												<span id="inFile" class="form-control">None</span>
											</div>
										</div>

																				
										<div class="pull-right">
											<div class="btn btn-primary fileinput-button">
												<i class="glyphicon glyphicon-plus"></i>
												<span>Select file</span>
												<input id="fileupload" type="file" name="files[]"/>
											</div>
											
											<button class="btn btn-success" id="upload" disabled="disabled" >Upload</button>
										</div>
										
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
		
		<script src="js/moment.js"></script>
		<script src="js/bootstrap-datetimepicker.js"></script>		
		
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
					autoUpload: false,
					acceptFileTypes: /(\.|\/)(zip|rar|7z|pdf)$/i,
					maxFileSize: 50 * 1024 * 1024,
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
				}).on('fileuploadadd', function (e, data) {
					$.each(data.files, function (index, file) {
						$('#inFile').text(file.name);
						$('#upload').removeAttr('disabled');
						$('#upload').click(function (){
							data.submit();
						});
					});
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
		<script>
			$("#clear-btn").hide();
			<?php
				if(!empty($_GET["title"])) {
					echo '$("#sortTitle").val("' . $_GET["title"] . '");';
					echo '$("#reset-btn").show();';
				}
				if(!empty($_GET["subject"])) {
					echo '$("#sortSubject").val("' . $_GET["subject"] . '");';
					echo '$("#reset-btn").show();';
				}
				if(!empty($_GET["time_start"])) {
					echo '$("#sortStart").val("' . $_GET["time_start"] . '");';
					echo '$("#reset-btn").show();';
				}
				if(!empty($_GET["time_end"])) {
					echo '$("#sortEnd").val("' . $_GET["time_end"] . '");';
					echo '$("#reset-btn").show();';
				}
			?>
			
			$('#reset-btn').click(function() {
				 window.location.href = 'groups/materials.php';
			});
			
			
			$('.delete').click(function() {
				$.ajax({
					url: 'transfer/?file_id=' + $(this).val(),
					type: 'DELETE',
					success: function(result) {
						window.location.href = 'groups/materials.php';
					}
				});
			});
			
			$('#timeStart').datetimepicker({
				pickTime: false
			});
			
			$('#timeEnd').datetimepicker({
				pickTime: false
			});
		</script>
</body>
</html>