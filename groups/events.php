<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		
		<base href="//localhost/test/">
		
		<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="css/fullcalendar.min.css">
		<link rel="stylesheet" href="css/fullcalendar.print.css" media="print">
		<link rel="stylesheet" href="css/bootstrap-datetimepicker.min.css">
		<link rel="stylesheet" href="css/styles.css">
		
		<title> Events </title>
	</head>
	<body>
		<?php
			require_once("../config/db.php");
			require_once("../classes/Login.php");
			require_once("../classes/EventManager.php");
			require_once("../classes/Permissions.php");
			
			$login = new Login();
			$event = new EventManager();
			
			if ($login->isLogged()) { 
			include("../views/navbar.php"); ?>
			
			<div class="container">
				<div class="page-header red-pageheader">
					<h2>
					<span><a href="groups"><?=$_SESSION["group_name"]?></a> <i class="fa fa-angle-right"></i> </span> Events</span>
				</h2>
			</div>
			
			<div class="row">
				<div class="col-sm-8">
					<div id='calendar'></div>
				</div>
				<div class="col-sm-4">
					<div class="panel-group" role="tablist" id="accordion" aria-multiselectable="true" style="position: relative; top: 50px;">
						<div class="panel panel-default">
							<div class="panel-heading red-heading" id="addHeading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion" href="#collapseAdd" aria-expanded="true" aria-controls="collapseAdd">
										Add event <i class="fa fa-caret-down"></i>
									</a>
								</h4>
							</div>
							<div id="collapseAdd" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="addHeading">
								<div class="panel-body">
									<form method="post" class="form-horizontal" id="form">
										<div class="form-group">
											<label for="name" class="col-sm-3 control-label">Name: </label>
											<div class="col-sm-9">
												<input type="text" id="name" name="name" pattern=".{3,128}" class="form-control" required/>
											</div>
										</div>
										
										<div class="form-group">
											<label for="location" class="col-sm-3 control-label">Location: </label>
											<div class="col-sm-9">
												<input type="text" id="location" name="location" pattern=".{3,64}" class="form-control"/>
											</div>
										</div>
										
										<div class="form-group">
											<label for="date" class="col-sm-3 control-label">Date: </label>
											<div class="col-sm-9">
												<div class="input-group date" id="pickDate">
													<input type="text" id="date" name="date" class="form-control" data-date-format="YYYY-MM-DD" required/>
													<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
												</div>
											</div>
										</div>
										
										<div class="form-group">
											<label for="start" class="col-sm-3 control-label">Start: </label>
											<div class="col-sm-9">
												<div class="input-group date" id="pickStart">
													<input type="text" id="start" name="start" class="form-control" data-date-format="HH:mm"/>
													<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
												</div>
											</div>
										</div>
										
										<div class="form-group">
											<label for="end" class="col-sm-3 control-label">End: </label>
											<div class="col-sm-9">
												<div class="input-group date" id="pickEnd">
													<input type="text" id="end" name="end" class="form-control" data-date-format="HH:mm"/>
													<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
												</div>
											</div>
										</div>
										
										<div class="form-group">
											<label for="comment" class="col-sm-3 control-label">Comment: </label>
											<div class="col-sm-9">
												<textarea rows="2" id="comment" name="comment" class="form-control"></textarea>
											</div>
										</div>
										
										<input type="hidden" name="group" />
										
										<input type="submit" name="event" value="Add" class="btn btn-success pull-right" style="width:100px;" />
									</form>
								</div>
							</div>
						</div>
						<div class="panel panel-default" id="panel_details">
							<div class="panel-heading red-heading" id="detailsHeading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion" href="#collapseDetails" aria-expanded="false" aria-controls="collapseDetails">
										Details <i class="fa fa-caret-down"></i>
										</a>
								</h4>
							</div>
							<div id="collapseDetails" class="panel-collapse collapse" role="tabpanel" aria-labelledby="detailsHeading">
								<div class="panel-body">
									<form method="post" class="form-horizontal" id="form">
										<div class="form-group">
											<label for="d_name" class="col-sm-3 control-label">Name: </label>
											<div class="col-sm-9">
												<input type="text" id="d_name" name="name" pattern=".{3,128}" class="form-control readonly" disabled="disabled"/>
											</div>
										</div>
										
										<div class="form-group">
											<label for="d_location" class="col-sm-3 control-label">Location: </label>
											<div class="col-sm-9">
												<input type="text" id="d_location" name="location" pattern=".{3,64}" class="form-control readonly" disabled="disabled"/>
											</div>
										</div>
										
										<div class="form-group">
											<label for="d_date" class="col-sm-3 control-label">Date: </label>
											<div class="col-sm-9">
												<div class="input-group date" id="pickDate">
													<input type="text" id="d_date" name="date" class="form-control readonly" data-date-format="YYYY-MM-DD" disabled="disabled"/>
													<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
												</div>
											</div>
										</div>
										
										<div class="form-group">
											<label for="d_start" class="col-sm-3 control-label">Start: </label>
											<div class="col-sm-9">
												<div class="input-group date" id="pickStart">
													<input type="text" id="d_start" name="start" class="form-control readonly" data-date-format="HH:mm" disabled="disabled"/>
													<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
												</div>
											</div>
										</div>
										
										<div class="form-group">
											<label for="d_end" class="col-sm-3 control-label">End: </label>
											<div class="col-sm-9">
												<div class="input-group date" id="pickEnd">
													<input type="text" id="d_end" name="end" class="form-control readonly" data-date-format="HH:mm" disabled="disabled"/>
													<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
												</div>
											</div>
										</div>
										
										<div class="form-group">
											<label for="d_comment" class="col-sm-3 control-label">Comment: </label>
											<div class="col-sm-9">
												<textarea rows="2" id="d_comment" name="comment" class="form-control readonly" disabled="disabled"></textarea>
											</div>
										</div>
									</form>
									<br />
									<span id="d_remove" class="btn btn-danger fixed-width center-block cursor-hand">Remove</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<form method="post" id="removeForm">
			<input type="hidden" id="hidden_id" name="event_id" />
			<input type="hidden" name="remove"/>
			<input type="hidden" name="group" />
		</form>
		
		<?php } else {
			header('Location: index.php?ref=groups');
		} ?>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
		<script src="js/moment.js"></script>
		<script src="js/fullcalendar.min.js"></script>
		<script src="js/bootstrap-datetimepicker.js"></script>	
		<script src="js/jquery.autogrowtextarea.min.js"></script>	
		
		
		<script type="text/javascript">
			$(document).ready(function() {
				
				// page is now ready, initialize the calendar...
				
				var date = new Date(); //Getting the current date
				var dow = (date.getDay() + 6) % 7; //Getting 
				date.setDate(date.getDate() - dow);
				
				var d = date.getDate();
				var m = date.getMonth();
				var y = date.getYear();
				
				$('#calendar').fullCalendar({
					// put your options and callbacks here
					header: {
						left: 'today',
						center: 'title',
						right: 'prev,next'
					},
					//editable: true,
					events: "<?=SITE_ROOT, "groups/ajax.php?events"?>",
					eventLimit: true, 
					//defaultView: 'agendaWeek',
					eventColor: '#a6373f',
					firstDay: 1,
					weekends: true,
					axisFormat: 'H:mm',
					columnFormat: 'dddd',
					//header: '',
					aspectRatio: 1.55,
					loading: function(isLoading, view) {
						<?php
						if(!empty($_GET["event_id"])) { ?>
						if(!isLoading) {
							var events = $("#calendar").fullCalendar('clientEvents', <?=$_GET["event_id"]?>);
							$.each(events, function (index, e) {
								setDetails(e);
							});
						}
					
					<?php } ?>
						
					},
					eventClick: function(callEvent, jsEvent, view) {				
						setDetails(callEvent);
					}
				})
			});
			
			$('#addHeading').click(function() {
				$('#panel_details').hide();
			});
			
			$('#d_remove').click(function() {
				$('#removeForm').submit();
			});
			
			$('#panel_details').hide();
			
			$('#pickDate').datetimepicker( { pickTime: false });
			$('#pickStart').datetimepicker( { pickDate: false });
			$('#pickEnd').datetimepicker( { pickDate: false });
			
			$('#comment').autoGrow();
			
			var setDetails = function (event) {
				$('#panel_details').show();
					$('#collapseAdd').collapse('hide');
					$('#collapseDetails').collapse('show');
					
					$('#d_name').val(event["title"]);
					$('#d_location').val(event["location"]);
					$('#d_date').val(event["date"]);
					$('#d_start').val(event["start"]);
					$('#d_end').val(event["end"]);
					$('#d_comment').val(event["comment"]);
					$('#hidden_id').val(event["id"]);
					
					if(event["can_delete"] == true)
						$('#d_remove').show();
					else
						$('#d_remove').hide();
			};

		</script> 
</body>
</html>