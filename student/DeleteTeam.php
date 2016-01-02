<?php

nclude ('../private/session.php');
require_once ("../private/utils.php");

if ($_SESSION['login_user_type'] != STUDENT) {
    header('Location: index.php'); // Redirecting To Login Page
}

?>