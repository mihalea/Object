<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		
		<base href="//localhost/test/">
		
		
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
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
			require_once("../classes/GroupManager.php");
			require_once("../classes/ScheduleManager.php");
			
			$login = new Login();
			$manager = new GroupManager();
			$schedule = new ScheduleManager();
			
			if(isset($_GET["id"]))
			$group = new Group($_GET["id"]);
			
			if ($login->isLogged()) { 
			include("../views/navbar.php"); ?>
			
			
			
			<div class="container">
				<div class="page-header">
					<h2>
						<span>Schedule for <?=$group->getName()?></span>
						
						<?php if ($group->getFlag() == 'a') { ?>
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
								Class details
							</div>
							<div class="panel-body">
								<form class="form-horizontal" method="POST">
									<div class="form-group">
										<label for="ctrlSubject" class="col-sm-2 control-label">Subject:</label>
										<div class="col-sm-10">
											<select class="form-control" id="ctrlSubject" required>
												<?php
													foreach($group->getSubjects() as $sub)
													echo '<option value="'. $sub["id"] .'">' . $sub["name"] . '</option>';
												?>
											</select>
										</div>
									</div>
									
									<div class="form-group">
										<label for="ctrlDay" class="col-sm-2 control-label">Day: </label>
										<div class="col-sm-10">
											<select class="form-control" id="ctrlDay" required>
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
										<label for="ctrlStart" class="col-sm-2 control-label">Start time: </label>
										<div class="col-sm-10">
											<div class="input-group date" id="pickStart">
												<input type="text" id="ctrlStart" name="time_start" class="form-control" data-date-format="HH:mm" required/>
												<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
											</div>
										</div>
									</div>
									
									<div class="form-group">
										<label for="ctrlEnd" class="col-sm-2 control-label">Start time: </label>
										<div class="col-sm-10">
											<div class="input-group date" id="pickEnd">
												<input type="text" id="ctrlEnd" name="time_end" class="form-control" data-date-format="HH:mm"required/>
												<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
											</div>
										</div>
									</div>	
									
									<input type="hidden" name="group_id" value="<?=$_GET["id"]?>"></input>
									<input type="hidden" name="group_id" value="<?=$group->getScheduleID()?>"></input>
									<input type="hidden" name="event_id" id="hiddenID"></input>
									<input type="hidden" name="subject_id" id="hiddenSubject"></input>
									<input type="hidden" name="day" id="hiddenDay"></input>
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
			
			
			<script src="js/schedule.js"></script> 
			<?php } else { 
				header('Location: index.php?ref=groups');
			} ?>
	</body>
</html>