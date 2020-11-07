<?php 
require('sessioninfo.php');
?>
<html>
	<head>
		<!-- Metadata  -->
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
		<!-- Page Title  -->
		<title>Holiday Light Controller</title>
		
		<!-- Cascading Style Sheets -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
		
	</head>
	<body>
		<h4 class="text-center">Sign Up</h4>
		<div class="container col-md-3">
			
			<!-- Display error messages, if any  -->
			<?php require('displaymessages.php'); ?>
			
			<form action="process/registration-process.php" method="POST">
				<div class="form-group">
					<label for="userName">User name</label>
					<input type="text" name="userName" class="form-control" id="userName" autocomplete="off" />
				</div>
				<div class="form-group">
					<label for="email">Email address</label>
					<input type="email" name="email" class="form-control" id="email" autocomplete="off" />
				</div>
				<div class="form-group">
					<label for="firstName">First name</label>
					<input type="text" name="firstName" class="form-control" id="firstName" autocomplete="off" />
				</div>
				<div class="form-group">
					<label for="lastName">Last name</label>
					<input type="text" name="lastName" class="form-control" id="lastName" autocomplete="off" />
				</div>
				<div class="form-group">
					<label for="password">Password</label>
					<input type="password" name="password" class="form-control" id="password" autocomplete="off" />
				</div>
				<div class="form-group">
					<label for="confirmPassword">Confirm password</label>
					<input type="password" name="confirmPassword" class="form-control" id="confirmPassword" autocomplete="off" />
				</div>
				<button type="submit" class="btn btn-primary">Register</button>
			</form>
			<a href="login.php">Cancel</a>
		</div>
	</body>
</html>