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
<title>Edit Project</title>
</head>

<body>

<?php require 'base/top.html';?>


<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

<h1 class="page-header">Edit Project</h1>

<div class="row placeholders"></div>

<!-- <h2 class="sub-header">List of Identities</h2> -->
<form id="form" class="form-horizontal" role="form" action="" method="post">
	<div class="form-group">
		<label class="control-label col-sm-2" for="title">Title:</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" name="title" id="title" value=""
				placeholder="Enter title" required>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-2" for="subject">Subject:</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" name="subject" id="subject"
				placeholder="Enter subject" required>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-2" for="deadline">Deadline:</label>
		<div class="col-sm-10">
			<input type="date" class="form-control" name="deadline" id="deadline"
				placeholder="Enter Deadline">
		</div>
	</div>
	
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button id="submit" type="submit" class="btn btn-default">Submit</button>
		</div>
	</div>
</form>

</div>

 <script>
      $(document).ready(function () {
		  		 
          $.ajax({
            // HTTP mthod
            type: "GET",
            url: "/TCP/WS/ProjectResource.php?id=<?=$_GET["project_id"]?>",
            // return type
			body:"",
            // error processing
            // xhr is the related XMLHttpRequest object
            error: function (xhr, string) {
				
				console.log(xhr.status );
				console.log(xhr.statusText );
				console.log(xhr.responseText );
				window.location="/TCP/index.php";
				//var msg = (xhr.status == 404    ? "Person   not found": "Error : " + xhr.status + " " + xhr.statusText;
              //$("#message").html(msg);
            },
            // success processing (when 200,201, 204 etc)
            success: function (data) {
				
				if (data["id"]) {
					$("#title").val(data["title"]);
					$("#subject").val(data["subject"]);
					$("#deadline").val(data["deadline"].substring(0,10));					
					
				} else {
					window.location="/TCP/index.php";
				}
				
				
				$("#submit").click(function(){

					event.preventDefault();
		 
		  var body = $("#form").serialize();
		  body = body ;
		  console.log(body);
          $.ajax({
            // HTTP mthod
            type: "POST",
            url: "/TCP/WS/ProjectResource.php?id=<?= $_GET['project_id'] ?>",
            // return type
			data:body,
            // error processing
            // xhr is the related XMLHttpRequest object
            error: function (xhr, string) {
				console.log(xhr.status );
				console.log(xhr.statusText );
				console.log(xhr.responseText );
				//var msg = (xhr.status == 404    ? "Person   not found": "Error : " + xhr.status + " " + xhr.statusText;
              //$("#message").html(msg);
            },
            // success processing (when 200,201, 204 etc)
            success: function (data) {
				
				window.location="/TCP/ListProjects.php";

            }
          });
					
					}
				);
				
				
				
				
              //$("#name").val(data.name);
              //$("#message").html("Person loaded")
            }
          });
		  
	



      });

    </script>

<?php require 'base/bottom.html';?>
</body>
</html>