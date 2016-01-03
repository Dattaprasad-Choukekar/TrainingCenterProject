<?php
include('private/session.php');
require_once("WS/DemoDB.php");

if($_SESSION['login_user_type']!=TRAINER){
header('Location: index.php'); // Redirecting To Login Page
}

 if (!isset($_GET["project_id"]) || !is_numeric($_GET["project_id"])) {
	header('Location: index.php'); 
 }

 


?>



<html lang="en">
<head>
<?php require 'base/head.html';?>
<title>Project Details</title>
</head>

<body>

<?php require 'base/top.html';?>


<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

<div class="row placeholders"><dl id='current_proj_details' class="dl-horizontal">

</dl></div>

<div class="table-responsive">


<h3 class="sub-header">List of Teams</h3>
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Team Id</th>
				<th>Name</th>
				<th>Summary</th>
				<th>Creation Date</th>
				<th>Owner</th>
			</tr>
		</thead>

		<tbody id="teams_tbody">

		</tbody>
	</table>
    <h3 class="sub-header">Remaining Students</h3>
	<table class="table table-striped">
		<thead>
			<tr>
                <th>Count</th>
				<th>Student Id</th>
				<th>Name</th>
			</tr>
		</thead>

		<tbody id="students_tbody">

		</tbody>
	</table>
</div>
<script> $(document).ready(function() {


	$.ajax({
		// HTTP mthod
		type: "GET",
		url: "/TCP/WS/ProjectResource.php?id=<?= $_GET['project_id'] ?>",
		// return type
		dataType: "json",
		// error processing
		// xhr is the related XMLHttpRequest object
		error: function(xhr, string) {
			var msg = (xhr.status == 404) ? "project   not found" : "Error : " + xhr.status + " " + xhr.statusText;
			console.log(msg);
			console.log(string);
		},
		// success processing (when 200,201, 204 etc)
		success: function(data) {
			$("#current_proj_details").append("<dt> Project:</dt>").append("<dd>"+ data['title'] + "</dd>");
            
    /*        .append("<dl>"+ data['id'] + "</dl>")
            .append("<dt> Title </dt>")
            .append("<dl>"+ data['title'] + "</dl>")
            .append("<dt> Subject </dt>")
            .append("<dl>"+ data['subject'] + "</dl>")
            .append("<dt> Creation Time </dt>")
            .append("<dl>"+ data['creation_datetime'] + "</dl>")
            .append("<dt> Deadline </dt>")
            .append("<dl>"+ data['deadline'] + "</dl>") */
		}
	});

	var responseBody = {};
    // get all the teams for the given project id    
	$.ajax({
		// HTTP mthod
		type: "GET",
		url: "/TCP/WS/TeamResource.php?id=*&project_id=<?= $_GET['project_id'] ?>",
		// return type
		dataType: "json",
		// error processing
		// xhr is the related XMLHttpRequest object
		error: function(xhr, string) {
			var msg = (xhr.status == 404) ? "project   not found" : "Error : " + xhr.status + " " + xhr.statusText;
			console.log(msg);
			console.log(string);
		},
		// success processing (when 200,201, 204 etc)
		success: function(data) {
			window.student_arr = new Array();
            console.log('Got all the teams data for given project id');            
			getstudentData(data);
		}
	});

	function getstudentData(team_data) {
	   // Get all the students and store them in an all_student_data object on window object.	   
		$.ajax({
			// HTTP mthod
			type: "GET",
			url: "/TCP/WS/StudentResource.php?id=*",
			// return type
			dataType: "json",
			// error processing
			// xhr is the related XMLHttpRequest object
			error: function(xhr, string) {
				console.log(msg);
				console.log(string);
			},
			// success processing (when 200,201, 204 etc)
			success: function(data) {
				window.all_student_data = data;
				processResponse(team_data);
			}
		});
	};

	function processResponse(responseBody) {
		for (var key in responseBody) {
			var rowData = responseBody[key];
			console.log(rowData);
			var rowDatahtml = "<tr>";
			rowDatahtml = rowDatahtml + "<td>" + rowData["team_id"] + "</td>";
			rowDatahtml = rowDatahtml + "<td>" + rowData["name"] + "</td>";
			rowDatahtml = rowDatahtml + "<td>" + rowData["summary"] + "</td>";
			rowDatahtml = rowDatahtml + "<td>" + rowData["creation_date"] + "</td>";
			rowDatahtml = rowDatahtml + "<td name='owner_id'>" + rowData["team_owner_id"] + "</td>";
			window.student_arr.push(parseInt(rowData["team_owner_id"]));
			//rowDatahtml = rowDatahtml + "<td><a href='EditProject.php?project_id="+ rowData["id"]+ "'><span class='glyphicon glyphicon-edit' aria-hidden='true'></span></a></td>";
			//rowDatahtml = rowDatahtml + "<td><a onclick='deleteTeam(this);' id='"+ rowData["team_id"]+"'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></a></td>";
			rowDatahtml = rowDatahtml + "</tr>";
			//rowDatahtml = rowDatahtml + "<td><ul><li>aaa</li><li>aaa</li><li>aaa</li><li>aaa</li><li>aaa</li><li>aaa</li><li>aaa</li><li>aaa</li><li>aaa</li><li>aaa</li><li>aaa</li><li>aaa</li><li>aaa</li><li>aaa</li><li>aaa</li><li>aaa</li><li>aaa</li><li>aaa</li><li>aaa</li><li>aaa</li></ul></td>";
			//console.log(rowDatahtml);
			processMembershipData(rowDatahtml, rowData);
			//$("#teams_tbody").append(rowDatahtml);
		}
		populateRemainingStudents();
        setStudentNameData();
	};

	function processMembershipData(rowDatahtml, rowData) {
	   // Populate members table	   
		if (rowData['members']) {
			rowDatahtml = rowDatahtml + "<tr><td/><td/><td>Members: </td><td><ul>";
			var memb_arr = JSON.parse(rowData['members']);
			for (var id in memb_arr) {
				window.student_arr.push(parseInt(memb_arr[id]));
				rowDatahtml = rowDatahtml + "<li>" + memb_arr[id] + ".  " + window.all_student_data[memb_arr[id]]['name'] + "</li>";
			}
			rowDatahtml = rowDatahtml + "</td></ul></tr>";
		}
		$("#teams_tbody").append(rowDatahtml);
	};

	function populateRemainingStudents() {
		var count = 0;
		for (student in window.all_student_data) {
			console.log(typeof student);
			if (window.student_arr.indexOf(parseInt(student)) <= -1) {
				var rowData = window.all_student_data[student];
				var rowDatahtml = "<tr>";
				rowDatahtml = rowDatahtml + "<td>" + ++count + "</td>";
				rowDatahtml = rowDatahtml + "<td>" + rowData["student_id"] + "</td>";
				rowDatahtml = rowDatahtml + "<td>" + rowData["name"] + "</td>";
				rowDatahtml = rowDatahtml + "</tr>";
				$("#students_tbody").append(rowDatahtml);
			}
		}
	};
    
    function setStudentNameData() {	
        $('td[name*=owner_id]').each(function(){
		$(this).text(window.all_student_data[$(this).text()]['name']);
      });
};
    
}); </script>


</div>
<?php require 'base/bottom.html'; ?>
</body>
</html>