<div class="list-group">
	<span class="list-group-item red-heading">Dashboard</span>
	<a href="groups/schedule.php" class="list-group-item">Schedule</a>
	<a href="groups/events.php" class="list-group-item">Events</a>
	<a href="groups/materials.php" class="list-group-item">Materials</a>
	<a href="groups/members.php" class="list-group-item">Members</a>
</div>

<?php 
	require_once("../classes/EventManager.php");
	require_once("../classes/Helper.php");
	
	$events = EventManager::getEvents(7, 10);
	if(is_array($events) AND count($events) > 0) { ?>
	<div class="list group">
		<span class="list-group-item red-heading">Upcoming events</span>										
		<?php
			foreach($events as $e) {
				echo '<a href="groups/events.php?event_id=' . $e["id"] . '" class="list-group-item"> '
				. substr($e["title"], 0, 15) , (strlen($e["title"]) > 15 ? "..." : "") . 
				'<span class="badge visible-lg-inline-block">' . timeDifference($e["date"]) . '</span></a>';
			}
		?>
	</div>
<?php } ?>