<?php

session_start(); // Starting Session

if(isset($_SESSION['login_user'])){
header("location: index.php");
}


$error=''; // Variable To Store Error Message
if (isset($_POST['submit'])) {
	if (empty($_POST['username']) || empty($_POST['password'])) {
	$error = "Username or Password is invalid";
	}
	else
	{
	// Define $username and $password
		$username=$_POST['username'];
		$password=$_POST['password'];
	// Establishing Connection with Server by passing server_name, user_id and password as a parameter


	if ($username=='Jack' && $password=='Jack') {
		$_SESSION['login_user']=$username;
		// Initializing Session
		header("location: index.php"); // Redirecting To Other Page
	}if ($username=='1' && $password=='1') {
		$_SESSION['login_user']=$username;
		// Initializing Session
		header("location: index.php"); // Redirecting To Other Page
	}  
	else {
		$error = "Username or Password is invalid";
	}

	}
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta name="generator"
    content="HTML Tidy for HTML5 (experimental) for Windows https://github.com/w3c/tidy-html5/tree/c63cc39" />
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="" />
    <meta name="author" content="" />
    <link rel="icon" href="images/favicon.ico" />
    <title>Base Template</title>
    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet" />
    <!-- Custom styles for this template -->
    <link href="css/dashboard.css" rel="stylesheet" />
    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="js/ie-emulation-modes-warning.js"></script>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
        <div style="css/signin.css">
		<?php 
		print $error;
		?>
          <form class="form-signin" method="post">
          <h2 class="form-signin-heading">Please sign in</h2>
          <label for="inputEmail" class="sr-only">Email address</label> 
          <input type="text" name="username" id="username" class="form-control" placeholder="Email address" required="" autofocus="" /> 
          <label for="inputPassword" class="sr-only">Password</label> 
          <input type="password" name="password" id="password" class="form-control" placeholder="Password" required="" />
          <div class="checkbox">
            <label>
            <input type="checkbox" value="remember-me" /> Remember me</label>
          </div>
          <button name="submit" class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button></form>
        </div>
      </div>
    </div>
  </div>
  <!-- Bootstrap core JavaScript
    ================================================== -->
  <!-- Placed at the end of the document so the pages load faster -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script> 
  <script src="js/bootstrap.min.js"></script> 
  <!-- Just to make our placeholder images work. Don't actually copy the next line! -->
   
  <script src="js/vendor/holder.min.js"></script> 
  <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
   
  <script src="js/ie10-viewport-bug-workaround.js"></script> 
  <script src="js/custom.js"></script></body>
</html>
