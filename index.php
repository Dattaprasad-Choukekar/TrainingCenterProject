<?php
include('private/session.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php require 'base/head.html';?>
<title>Index Page</title>
</head>

<body>
<?php require 'base/top.html';?>

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
<h1><?php
echo 'Welcome user ' . $_SESSION['login_user'] . $_SESSION['login_user_type'];?>
</h1>
</div>
<?php require 'base/bottom.html';?>
</body>
</html>