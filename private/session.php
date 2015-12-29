

<?php

define("TRAINER",     "TRAINER");
define("STUDENT",     "STUDENT");
session_start();// Starting Session
$user_check=$_SESSION['login_user'];
if(!isset($user_check)){
header('Location: login.php'); // Redirecting To Login Page
}
if ($_SESSION['login_user']!=1) {
$_SESSION['login_user_id'] = 1;
$_SESSION['login_user_type'] = TRAINER;
} else {
	$_SESSION['login_user_id'] = 100;
	$_SESSION['login_user_type'] = STUDENT;
}
?>