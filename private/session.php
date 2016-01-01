

<?php

define("TRAINER",     "TRAINER");
define("STUDENT",     "STUDENT");
session_start();// Starting Session
$user_check=$_SESSION['login_user'];
if(!isset($user_check)){
header('Location: login.php'); // Redirecting To Login Page
}







?>