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
		events: "<?=SITE_ROOT, "groups/getEvents.php?group_id=", $group->getID()?>",
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
				$("#panelTitle").text("Class details");
				$("#panelTitle").attr("style", "background-color: #a6373f");
			}
			
			var start = moment(callEvent["start"]).format("H:mm");
			var end = moment(callEvent["end"]).format("H:mm");
			
			$("#ctrlSubject").val(callEvent["subject_id"]);
			$("#ctrlDay").val(callEvent["day"]);
			$("#ctrlStart").val(start);
			$("#ctrlEnd").val(end);
			$("#hiddenID").val(callEvent["id"]);
			
			<?php 
			if($group->getFlag() != 'a') {
				echo "$('#ctrlSubject').attr('disabled', 'disabled')";
				echo "$('#ctrlDay').attr('disabled', 'disabled')";
				} else {
				echo "$('#submit').show();";
			}
			?>
			
		}
	})
	
});

$('#submit').hide();

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
	
	$("#panelTitle").text("Add new class");
	$("#panelTitle").attr("style", "background-color: #337ab7");
	$("#ctrlStart").val("08:00");
	$("#ctrlEnd").val("08:50");
	$("#hiddenID").val("-1");
});

$("#submit").click(function() {
	var subject = $('#inputSubject').val(); 
	$('#hiddenSubject').attr("value", subject);
	
	var day = $('#inputDay').val(); 
	$('#hiddenDay').attr("value", day);
});