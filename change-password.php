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
		<h4 class="text-center">Change Password</h4>
		<div class="container col-md-3">
			
			<!-- Display error messages, if any  -->
			<?php require('displaymessages.php'); ?>
			
			<form action="process/change-password-process.php" method="POST">
				<div class="form-group">
					<label for="password">New Password</label>
					<input type="password" name="password" class="form-control" id="password" autocomplete="off" />
				</div>
				<div class="form-group">
					<label for="confirmPassword">Confirm new password</label>
					<input type="password" name="confirmPassword" class="form-control" id="confirmPassword" autocomplete="off" />
				</div>
				<button type="submit" class="btn btn-primary">Save Password</button>
			</form>
			<a href="dashboard.php">Cancel</a>
		</div>
	</body>
</html>