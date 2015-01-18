<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		
		<base href="//localhost/test/">
		
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="css/styles.css">
		<link rel="stylesheet" href="css/fullcalendar.min.css">
		<link rel="stylesheet" href="css/fullcalendar.print.css" media="print">
		<link rel="stylesheet" href="css/bootstrap-datetimepicker.min.css">
		
		
		
		
		<title> Schedule </title>
	</head>
	<body>
		<?php
			require_once("../config/db.php");
			require_once("../config/site.php");
			require_once("../classes/Login.php");
			require_once("../classes/GroupManager.php");
			
			$login = new Login();
			$manager = new GroupManager();
			
			if(isset($_GET["id"]))
			$group = new Group($_GET["id"]);
			
			if ($login->isLogged()) { 
			include("../views/navbar.php"); ?>
			
			
			
			<div class="container">
				<div class="page-header">
					<h2>
						<span>Schedule for <?=$group->getName()?></span>
						
						<?php if ($group->getFlag() == 'a') { ?>
							<button type="button" class="btn btn-primary pull-right" data-toggle="collapse" data-target="#addClassCollapse" aria-expanded="false" aria-controls="addClassCollapse"><span class="glyphicon glyphicon-plus-sign">&nbsp;</span>Add class</button>
						<?php } ?>
						
					</h2>
					
					<div class="collapse" id="addClassCollapse">
						<br />
						<form class="form-horizontal" method="POST">
							<div class="form-group">
								<label for="inputSubject" class="col-sm-2 control-label">Subject: </label>
								<div class="col-sm-10">
									<select class="form-control" id="inputSubject" required>
										<?php
											foreach($group->getSubjects() as $sub)
											echo '<option value="'. $sub["id"] .'">' . $sub["name"] . '</option>';
										?>
									</select>
								</div>
							</div>
							
							<div class="form-group">
								<label for="inputDay" class="col-sm-2 control-label">Subject: </label>
								<div class="col-sm-10">
									<select class="form-control" id="inputDay" required>
										<option value="1">Monday</option>
										<option value="2">Tuesday</option>
										<option value="3">Wednesday</option>
										<option value="4">Thursday</option>
										<option value="5">Friday</option>
										<option value="6">Saturday</option>
										<option value="7">Sunday</option>
									</select>
								</div>
							</div>
							
							<div class="form-group">
								<label for="startTime" class="col-sm-2 control-label">Start time: </label>
								<div class="col-sm-10">
									<div class="input-group date" id="startTimePick">
										<input type="text" id="startTime" name="startTime" class="form-control" data-date-format="HH:mm" required/>
										<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
									</div>
								</div>
							</div>
							
							<div class="form-group">
								<label for="endTime" class="col-sm-2 control-label">Start time: </label>
								<div class="col-sm-10">
									<div class="input-group date" id="endTimePick">
										<input type="text" id="endTime" name="endTime" class="form-control" data-date-format="HH:mm"required/>
										<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
									</div>
								</div>
							</div>	
							
							<input type="hidden" name="subject_id" id="hiddenSubject"></input>
							<input type="hidden" name="day" id="hiddenDay"></input>
							<input type="submit" name="addClass" id="submit" class="btn btn-success pull-right"></input>
							<div class="clearfix"></div>
						</form>
					</div>
				</div>
				
				<div id='schedule'></div>
				
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
						events: "<?=SITE_ROOT, "groups/getEvents.php?group_id=", $group->getID()?>",
						eventLimit: true, 
						defaultView: 'agendaWeek',
						eventColor: '#a6373f',
						firstDay: 1,
						aspectRatio: 1.85
					})
					
				});
				
				$(function () {
					$('#startTimePick').datetimepicker({
						pickDate: false
					});
					$('#endTimePick').datetimepicker({
						pickDate: false
					});
				});
				
				var id = $('#inputSubject').val(); 
				$('#hiddenSubject').attr("value", id);
				$('#inputSubject').change(function(){
				   var id = $('#inputSubject').val(); 
				   $('#hiddenSubject').attr("value", id);
				});
				
				var day = $('#inputDay').val(); 
				$('#hiddenDay').attr("value", day);
				$('#inputDay').change(function(){
				   var day = $('#inputDay').val(); 
				   $('#hiddenDay').attr("value", day);
				});
			</script> 
			<?php } else { 
				header('Location: index.php?ref=groups');
			} ?>
	</body>
</html>