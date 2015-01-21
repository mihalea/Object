<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		
		<base href="//localhost/test/">
			
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
		<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
		<link rel="stylesheet" href="css/fullcalendar.min.css">
		<link rel="stylesheet" href="css/fullcalendar.print.css" media="print">
		<link rel="stylesheet" href="css/bootstrap-datetimepicker.min.css">
		<link rel="stylesheet" href="css/styles.css">
		
		<title> Schedule </title>
	</head>
	<body>
		<?php
			require_once("../config/db.php");
			require_once("../config/site.php");
			require_once("../classes/Login.php");
			require_once("../classes/Permissions.php");
			require_once("../classes/GroupManager.php");
			require_once("../classes/ScheduleManager.php");
			
			$login = new Login();
			$schedule = new ScheduleManager();
			
			if ($login->isLogged() AND hasGroupFlag('u')) { 
			include("../views/navbar.php"); ?>
			
			
			
			<div class="container">
				<div class="page-header">
					<h2>
						<span><a href="groups"><?=$_SESSION["group_name"]?></a> <i class="fa fa-angle-right"></i> Schedule</span>

						<?php if (hasGroupFlag('a')) { ?>
							<button type="button" class="btn btn-primary pull-right" id="btnAdd"><span class="glyphicon glyphicon-plus-sign">&nbsp;</span>Add class</button>
						<?php } ?>
						
					</h2>
				</div>
				
				<div class="row">
					<div class="col-md-8">
						<div id='schedule'></div>
					</div>
					<div class="col-md-4">
						<div class="panel panel-default" id="scheduleControl">
							<div class="panel-heading" id="panelTitle">
								<span id="panelText">Class details</span>
								<div class="pull-right">
									<?php if(hasGroupFlag('a')) echo '<small><a class="white-link" id="remLink">Delete</a></small>' ?>
								</div>
							</div>
							<div class="panel-body">
								<form class="form-horizontal" method="POST" id="schedForm">
									<div class="form-group">
										<label for="ctrlSubject" class="col-sm-2 control-label">Subject:</label>
										<div class="col-sm-10">
											<select class="form-control" id="ctrlSubject" name="subject_id"  style="background-color:#fff;" required>
												<?php
													foreach(GroupManager::getSubjects() as $sub)
													echo '<option value="'. $sub["id"] .'">' . $sub["name"] . '</option>';
												?>
											</select>
										</div>
									</div>
									
									<div class="form-group">
										<label for="ctrlDay" class="col-sm-2 control-label read-only">Day: </label>
										<div class="col-sm-10">
											<select class="form-control" id="ctrlDay" name="day" style="background-color:#fff;"  required>
												<option value="1">Monday</option>
												<option value="2">Tuesday</option>
												<option value="3">Wednesday</option>
												<option value="4">Thursday</option>
												<option value="5">Friday</option>
											</select>
										</div>
									</div>
									
									<div class="form-group">
										<label for="ctrlStart" class="col-sm-2 control-label">Start time: </label>
										<div class="col-sm-10">
											<div class="input-group date" id="pickStart">
												<input type="text" id="ctrlStart" name="time_start" class="form-control read-only" style="background-color:#fff;"  data-date-format="HH:mm" required/>
												<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
											</div>
										</div>
									</div>
									
									<div class="form-group">
										<label for="ctrlEnd" class="col-sm-2 control-label">Start time: </label>
										<div class="col-sm-10">
											<div class="input-group date" id="pickEnd">
												<input type="text" id="ctrlEnd" name="time_end" class="form-control read-only" style="background-color:#fff;"  data-date-format="HH:mm"required/>
												<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
											</div>
										</div>
									</div>	
									
									<input type="hidden" name="group_id" value="<?=$_SESSION["group_id"]?>"></input>
									<input type="hidden" name="schedule_id" value="<?=$_SESSION["schedule_id"]?>"></input>
									<input type="hidden" name="event_id" id="hiddenID"></input>
									<input type="submit" name="editClass" id="submit" class="btn btn-success pull-right"></input>
									<div class="clearfix"></div>
								</form>
							</div>
						</div>
					</div>
				</div>
				
			</div>
			
			
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
			<!-- Latest compiled and minified JavaScript -->
			<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
			<script src="js/moment.js"></script>
			<script src="js/fullcalendar.min.js"></script>
			<script src="js/bootstrap-datetimepicker.js"></script>			
			
			
			<script type="text/javascript">
				$(document).ready(function() {
					
					// page is now ready, initialize the calendar...
					
					var date = new Date(); //Getting the current date
					var dow = (date.getDay() + 6) % 7; //Getting 
					date.setDate(date.getDate() - dow);
					
					var d = date.getDate();
					var m = date.getMonth();
					var y = date.getYear();
					
					$('#schedule').fullCalendar({
						// put your options and callbacks here
						header: {
							left: '',
							center: 'title',
							right: ''
						},
						//editable: true,
						events: "<?=SITE_ROOT, "groups/json.php"?>",
						eventLimit: true, 
						defaultView: 'agendaWeek',
						eventColor: '#a6373f',
						firstDay: 1,
						scrollTime: '07:00:00',
						weekends: false,
						axisFormat: 'H:mm',
						columnFormat: 'dddd',
						header: '',
						//aspectRatio: 1.85,
						eventClick: function(callEvent, jsEvent, view) {
							if(createMode == true) {
								$("#panelText").text("Class details");
								$("#panelTitle").attr("style", "background-color: #a6373f");
								createMode = false;
							}
							$(".white-link").show();
							
							var start = moment(callEvent["start"]).format("H:mm");
							var end = moment(callEvent["end"]).format("H:mm");
							
							$("#ctrlSubject").val(callEvent["subject_id"]);
							$("#ctrlDay").val(callEvent["day"]);
							$("#ctrlStart").val(start);
							$("#ctrlEnd").val(end);
							$("#hiddenID").val(callEvent["id"]);							
						}
					})
					
				});
				
				<?php 
					if(hasGroupFlag('a') == false) {
						echo "$('#ctrlSubject').attr('disabled', 'disabled');";
						echo "$('#ctrlDay').attr('disabled', 'disabled');";
						echo "$('#ctrlStart').attr('disabled', 'disabled');";
						echo "$('#ctrlEnd').attr('disabled', 'disabled');";
						}
				?>
				
				<?php 
					if(hasGroupFlag('a') == false) {
						echo "$('#submit').hide()";
						}
				?>
				$(".white-link").hide();

				
				$(function () {
					$('#pickStart').datetimepicker({
						pickDate: false
					});
					$('#pickEnd').datetimepicker({
						pickDate: false
					});
				});
				
				var createMode = false;
				$("#btnAdd").click(function() {
					createMode = true;
					
					$("#panelText").text("Add new class");
					$("#panelTitle").attr("style", "background-color: #337ab7");
					$("#ctrlStart").val("08:00");
					$("#ctrlEnd").val("08:50");
					$("#hiddenID").val("-1");
					$(".white-link").hide();
				});
				
				$("#remLink").click(function() {
					$('#submit').attr("name", "remClass");
					$('#submit').click();
				});
			</script> 
			<?php } else { 
				header('Location: index.php?ref=groups');
			} ?>
	</body>
</html>