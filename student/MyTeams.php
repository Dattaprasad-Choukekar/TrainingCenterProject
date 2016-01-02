<?php
include ('../private/session.php');
require_once ("../private/utils.php");

if ($_SESSION['login_user_type'] != STUDENT) {
    header('Location: index.php'); // Redirecting To Login Page
}


?>



<html lang="en">
<head>
<?php require '../base/head.html'; ?>
<title>My Teams</title>
</head>

<body>
<?php require '../base/top.html'; ?>


<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

<div class="row placeholders"></div>

<?php require '../base/top.html'; ?>

<h2 class="sub-header">List of my Teams</h2>
<div class="table-responsive">
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Team Id</th>
				<th>Name</th>
				<th>Summary</th>
				<th>Creation Date</th>
				<th>Project Name</th>
			</tr>
		</thead>

		<tbody id="teams_tbody">

		</tbody>
	</table>
</div>
 <script>
      $(document).ready(function () {
        // Get data from server when click on Reload button
       var responseBody={};
          $.ajax({
            // HTTP mthod
            type: "GET",
            url: "/TCP/WS/TeamResource.php?id=*&team_owner_id=<?= $_SESSION['login_user_id'] ?>",
            // return type
            dataType: "json",
            // error processing
            // xhr is the related XMLHttpRequest object
            error: function (xhr, string) {
              var msg = (xhr.status == 404)
                      ? "project   not found"
                      : "Error : " + xhr.status + " " + xhr.statusText;
				console.log(msg);
				console.log(string);
            },
            // success processing (when 200,201, 204 etc)
            success: function (data) {
				
				processResponse(data);
                
            }
          });
		
		
		function processResponse(responseBody) {
			
			for (var key in responseBody) {
				var rowData = responseBody[key];
                console.log(rowData);
				var rowDatahtml="<tr>";
				
				rowDatahtml= rowDatahtml + "<td>" + rowData["team_id"] + "</td>";
				rowDatahtml= rowDatahtml + "<td>" + rowData["name"] + "</td>";
				rowDatahtml= rowDatahtml +"<td>" + rowData["summary"] + "</td>";
				rowDatahtml= rowDatahtml + "<td>" + rowData["creation_date"] + "</td>";
				rowDatahtml= rowDatahtml + "<td name='project_id'>" + rowData["Project_id"] + "</td>";
	
				//rowDatahtml = rowDatahtml + "<td><a href='EditProject.php?project_id="+ rowData["id"]+ "'><span class='glyphicon glyphicon-edit' aria-hidden='true'></span></a></td>";
				rowDatahtml = rowDatahtml + "<td><a onclick='deleteTeam(this);' id='"+ rowData["team_id"]+"'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></a></td>";
				rowDatahtml = rowDatahtml + "</tr>";
				//console.log(rowDatahtml);
				$("#teams_tbody").append(rowDatahtml);
						
				
			}
			setProjectNameData();
		};
		
		
			function setProjectNameData() {				
				 $.ajax({
					// HTTP mthod
					type: "GET",
					url: "/TCP/WS/ProjectResource.php?id=*&class_id=<?=$_SESSION["login_user_class_id"]?>",
					// return type
					dataType: "json",					
					// error processing
					// xhr is the related XMLHttpRequest object
					error: function (xhr, string) {					
						console.log(msg);
						console.log(string);
					},
					// success processing (when 200,201, 204 etc)
					success: function (data) {						
						$('td[name*=project_id]').each(function(){
							$(this).text(data[$(this).text()]['title']);
						});
					//	console.log(data[1]['name']);
						//$('td[name*=class_id]').text(data[$(this).attr('id')]['name']);

					}
				  });
				  
				
				 
				
			};
            
            
           
      });
      
       function deleteTeam(myvar) {
                	 $.ajax({
					// HTTP mthod
					type: "DELETE",
					url: "/TCP/WS/TeamResource.php?id=" + myvar.getAttribute("id") + "&team_owner_id=<?=$_SESSION["login_user_id"]?>",
					// return type
					dataType: "json",					
					// error processing
					// xhr is the related XMLHttpRequest object
					error: function (xhr, string) {					
						console.log(msg);
						console.log(string);
					},
					// success processing (when 200,201, 204 etc)
					success: function (data) {	
					   window.location="/TCP/student/MyTeams.php";
					//	console.log(data[1]['name']);
						//$('td[name*=class_id]').text(data[$(this).attr('id')]['name']);

					}
				  });
				  
            }

    </script>


</div>
<?php require '../base/bottom.html'; ?>
</body>
</html>