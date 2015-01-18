<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="../css/styles.css">
		<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.6/fullcalendar.min.css">
		<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.6/fullcalendar.print.css">
		
		<base href="//localhost/test/">
		
		<title> Schedule </title>
	</head>
	<body>
		<?php
			require_once("../config/db.php");
			require_once("../classes/Login.php");
			require_once("../classes/GroupManager.php");
			
			$login = new Login();
			
			if(isset($_GET["id"]))
				$group = new Group($_GET["id"]);
			
			if ($login->isLogged()) { 
			include("../views/navbar.php"); ?>
			
			<div id='schedule'></div>
			
			
			
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
			<!-- Latest compiled and minified JavaScript -->
			<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
			<script src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.6/fullcalendar.min.js"></script>
			
			<script type="text/javascript">
				$('#schedule').fullCalendar({
					header: {
						left: 'prev,next today',
						center: 'title',
						right: 'month,basicWeek,basicDay'
					},
					defaultDate: '2014-11-12',
					editable: true,
					eventLimit: true, // allow "more" link when too many events
					events: [
						{
							title: 'All Day Event',
							start: '2014-11-01'
						},
						{
							title: 'Long Event',
							start: '2014-11-07',
							end: '2014-11-10'
						}
					]
				});
			</script> 
		<?php } else { 
			header('Location: index.php?ref=groups');
		} ?>
	</body>
</html>