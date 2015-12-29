<?php
include('private/session.php');

if($_SESSION['login_user_type']!=TRAINER){
header('Location: index.php'); // Redirecting To Login Page
}
?>

<?php

$error=''; // Variable To Store Error Message
if (isset($_POST['submit'])) {
	if (empty($_POST['title']) || empty($_POST['subject'])  || empty($_POST['class_id'])  ) {
		$error = "Fields can not be empty";
	}
	else
	{

	}
}
?>

<html lang="en">
<head>
<?php require 'base/head.html';?>
<title>Create a new Project</title>
</head>

<body>
<?php require 'base/top.html';?>


<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
		<?php 
		print $error;
		?>

<h1 class="page-header">Create a new Project</h1>

<div class="row placeholders"></div>

<!-- <h2 class="sub-header">List of Identities</h2> -->
<form id="form" class="form-horizontal" role="form" action="" method="post">
	<div class="form-group">
		<label class="control-label col-sm-2" for="title">Title:</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" name="title" id="title"
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
		<label class="control-label col-sm-2" for="class_id">Class:</label>
		<div class="col-sm-10">
			<input type="number" class="form-control" name="class_id" id="class_id"
				placeholder="Enter Class id">
		</div>
	</div>
	
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button id="submit" type="submit" class="btn btn-default">Submit</button>
		</div>
	</div>
</form>


 <script>
      $(document).ready(function () {
        // Get data from server when click on Reload button
        $("#submit").click(function (event) {
		  event.preventDefault();
		  var body = $("#form").serialize();
		  body = body + "&owner_id=<?= $_SESSION['login_user_id'] ?>";
		  console.log(body);
          $.ajax({
            // HTTP mthod
            type: "POST",
            url: "/TCP/WS/ProjectResource.php?id=1",
            // return type
			data:body,
            // error processing
            // xhr is the related XMLHttpRequest object
            error: function (xhr, string) {
				//console.log(xhr.status );
				//console.log(xhr.statusText );
				//console.log(xhr.responseText );
				//var msg = (xhr.status == 404    ? "Person   not found": "Error : " + xhr.status + " " + xhr.statusText;
              //$("#message").html(msg);
            },
            // success processing (when 200,201, 204 etc)
            success: function (data) {
				window.location="/TCP/CreateProject.php";
              //$("#name").val(data.name);
              //$("#message").html("Person loaded")
            }
          });
		  
		  
		  
		 
        }
		
		
		);


      });

    </script>



</div>
<?php require 'base/bottom.html';?>
</body>
</html>