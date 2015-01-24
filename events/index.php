<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		
		<base href="//localhost/test/">
		
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="css/styles.css">
		<link rel="stylesheet" href="css/bootstrap-datetimepicker.min.css">
		
		
		
		<title> Events </title>
	</head>
	<body>
		<?php
			require_once("../config/db.php");
			require_once("../classes/Login.php");
			require_once("../classes/EventManager.php");
			
			$login = new Login();
			$manager = new EventManager();
			
			if($manager->errors)
			foreach($manager->errors as $error)
			echo $error;
			
			if ($login->isLogged()) { 
				
			include("../views/navbar.php"); ?>
			
			<div class="container">
				<div class="page-header">
					<h2> 
						<span><?php if(isset($_GET["all"])) echo "All"; else echo "Upcoming";?> events</span> 
						<button type="button" class="btn btn-primary pull-right" data-toggle="collapse" data-target="#addEventCollapse" aria-expanded="false" aria-controls="addEventCollapse"><span class="glyphicon glyphicon-plus-sign">&nbsp;</span>Add event</button>
					</h2>
					
					
					<small><a href=<?php if(!isset($_GET["all"])) echo "events.php?all"; else echo "events.php";?>>View <?php if(!isset($_GET["all"])) echo "all"; else echo "upcoming";?> events</a></small>
					
					<div class="collapse" id="addEventCollapse">
						<br />
						
					<form class="form-horizontal" id="addEvent" method="POST">
							<div class="form-group">
								<label for="inputName" class="col-sm-2 control-label">Name: </label>
								<div class="col-sm-10">
									<input type="text" id="inputName" name="name" class="form-control" required/>
								</div>
							</div>
							
							<div class="form-group">
								<label for="inputDate" class="col-sm-2 control-label">Date: </label>
								<div class="col-sm-10">
									<div class="input-group date" id="dateTimePicker">
										<input type="text" id="inputDate" name="date" data-date-format="YYYY-MM-DD"/ class="form-control" required/>
										<div class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></div>
									</div>
								</div>
							</div>						
							
							<div class="form-group">
								<label for="inputComment" class="col-sm-2 control-label">Comment: </label>
								<div class="col-sm-10">
								<textarea rows="3" id="inputComment" name="comment" class="form-control"></textarea>
							</div>
						</div>
						
						<input type="submit" name="event" id="submit" class="btn btn-success pull-right"></input>
						<div class="clearfix"></div>
						
						
						
					</form>
				</div>
			</div>
			
			<?php 
				if (isset($_GET["error"]) AND $_GET["error"] == "add") {
					echo '<div class="alert alert-danger" style="position:relative; top:20px;"><p>Error! Failed to add the event</p></div>';
					} elseif (isset($_GET["error"]) AND $_GET["error"] == "rem") {
					echo '<div class="alert alert-danger"><p>Error! Failed to remove the event</p></div>'; 
				}
				
			?>
			
			
			<?php
				#$manager->getEvents();
			?>			
			
			<form method="POST" name="removeForm">
				<input type="hidden" name="eventID" />
			</form>
			
			
		</div>
		
		<?php } else { ?>
		NOT LOGGED IN!
	<?php } ?>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
	<script src="js/moment.js"></script>
	<script src="js/bootstrap-datetimepicker.js"></script>
	
	<script type="text/javascript">
		$('#dateTimePicker').datetimepicker({
			pickTime: false
		});
	</script> 
	
	<script type="text/javascript">
		function removeEvent(eventID) {
			document.removeForm.eventID.value = eventID;
			document.removeForm.submit();
		}
	</script> 
</body>
</html>