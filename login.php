<?php
require('sessioninfo.php');
require_once('google-api-client/vendor/autoload.php');

// init configuration
$clientID = '467051867349-stitogbnu41lvhdi65ntpp3hbvbru0nb.apps.googleusercontent.com';
$clientSecret = 'sBjtGqIEOp8rVdS8cDQaN0h-';
$redirectUri = ('https://holiday-light-controller.ue.r.appspot.com/process/sso-process.php');
  
// create Client Request to access Google API
$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");
?>
<html>
	<head>
		<!-- Metadata  -->
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
		<!-- Page Title  -->
		<title>Holiday Light Controller</title>
		
		<!-- Cascading Style Sheets -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">	
		
		<!-- Google fonts -->
		<link rel="preconnect" href="https://fonts.gstatic.com">
		<link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
	</head>
	<body>
		<h4 class="text-center">Login</h4>
		<div class="container col-md-3">
		
			<!-- Display error messages, if any  -->
			<?php require('displaymessages.php'); ?>
			
			<form action="process/login-process.php" method="POST">
				<div class="form-group">
					<label for="userName">User name</label>
					<input type="text" name="userName" class="form-control" id="userName" autocomplete="off" autofocus />
				</div>
				<div class="form-group">
					<label for="password">Password</label>
					<input type="password" name="password" class="form-control" id="password" autocomplete="off" />
				</div>
				<button type="submit" class="btn btn-primary">Log in</button>
				<br />
				<div class="g-signin2" data-onsuccess="onSignIn"></div>
			</form>
			<?php echo "<a href='".$client->createAuthUrl()."'>"; ?>
			<button type="button" class="btn btn-outline-dark">Log In with Google</button></a><br /><br />
			<a href="registration.php">Register</a>
		</div>
	</body>
</html>