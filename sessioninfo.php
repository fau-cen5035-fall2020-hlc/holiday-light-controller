<?php
if(session_status() != PHP_SESSION_ACTIVE) session_start();
if(!isset($_SESSION['messages'])) $_SESSION['messages'] = array();

// Force user to log in before accessing site
if(!in_array(pathinfo($_SERVER['PHP_SELF'])['filename'], array('login', 'login-process', 'registration', 'registration-process', 'sso-process', 'read-recurring-process')) and !isset($_SESSION['userID'])){
	$message = array('info', 'You must log in to continue');
	array_push($_SESSION['messages'], $message);
	header('Location:login.php');
	die();
}

// Unset existing userID session variable when accessing login page
if(pathinfo($_SERVER['PHP_SELF'])['filename'] === 'login' and isset($_SESSION['userID'])){
	unset($_SESSION['userID']);
	$message = array('info', 'You have been successfully logged out');
	array_push($_SESSION['messages'], $message);
}

require('get-access-token.php');
?>