<?php
require('sessioninfo.php');
// Query to send in HTTPS request			
$payload = '{
	"keys": [
		{
			"path": [
				{
					"kind": "user",
					"id": "' . $_SESSION['userID'] . '"
				}
			]
		}
	]
}';

// URL for HTTPS request
$url = 'https://datastore.googleapis.com/v1/projects/holiday-light-controller:lookup';

// Initialize cURL
$ch = curl_init();

// cURL headers
$headers = array(
	"Content-Type: application/json",
	"Content-Length: " . strlen($payload),
	"Authorization: Bearer " . $_SESSION['accessToken']
);

// cURL options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// Execute cURL and get result
$result = curl_exec($ch);

// Get HTTP response code and errors
$errors = curl_error($ch);
$response = curl_getinfo($ch);

// Close cURL
curl_close($ch);

if(array_key_exists('error', json_decode($result, TRUE))){
	$error = array('danger', json_decode($result, TRUE)['error']['code'] . ': ' . json_decode($result, TRUE)['error']['message']);
	array_push($_SESSION['messages'], $error);
}
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
		<h4 class="text-center">Dashboard</h4>
		<div class="container col-md-3">
			
			<!-- Display error messages, if any  -->
			<?php require('displaymessages.php'); ?>
			
			<h5>User information</h5>
			
			<table class="table table-bordered">
				<tbody>
					<tr>
					<th scope="row">First name</th>
					<td><?php print_r(json_decode($result, TRUE)['found'][0]['entity']['properties']['fname']['stringValue']); ?></td>
					</tr>
					<tr>
					<th scope="row">Last name</th>
					<td><?php print_r(json_decode($result, TRUE)['found'][0]['entity']['properties']['lname']['stringValue']); ?></td>
					</tr>
					<tr>
					<th scope="row">User name</th>
					<td><?php print_r(json_decode($result, TRUE)['found'][0]['entity']['properties']['username']['stringValue']); ?></td>
					</tr>
					<tr>
					<th scope="row">Email address</th>
					<td><?php print_r(json_decode($result, TRUE)['found'][0]['entity']['properties']['email']['stringValue']); ?></td>
					</tr>
				</tbody>
			</table>
			<a href="update-profile.php">Update profile</a><br />
			<a href="change-password.php">Change password</a><br />
			<a href="login.php">Log out</a>
		</div>
	</body>
</html>