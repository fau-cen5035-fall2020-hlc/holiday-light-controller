<?php
require(__DIR__ . '/../sessioninfo.php');

// Validation - password minimum length
if(strlen($_POST['password']) < 8){
	$error = array('warning', 'Password must be at least 8 characters.');
	array_push($_SESSION['messages'], $error);
	header('Location:' . $_SERVER['HTTP_REFERER']);
	die();
}

// Validation - password match
if($_POST['password'] != $_POST['confirmPassword']){
	$error = array('warning', 'The passwords entered do not match.');
	array_push($_SESSION['messages'], $error);
	header('Location:' . $_SERVER['HTTP_REFERER']);
	die();
}

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

// Store existing user profile attributes to be included in update below
$fname = json_decode($result, TRUE)['found'][0]['entity']['properties']['fname']['stringValue'];
$lname = json_decode($result, TRUE)['found'][0]['entity']['properties']['lname']['stringValue'];
$email = json_decode($result, TRUE)['found'][0]['entity']['properties']['email']['stringValue'];
$username = json_decode($result, TRUE)['found'][0]['entity']['properties']['username']['stringValue'];

// Change password
// Query to send in HTTPS request
$payload = '{
	"mode": "NON_TRANSACTIONAL",
	"mutations": [
		{
			"update": {
				"key": {
					"path": [
						{
							"kind": "user",
							"id": ' . $_SESSION['userID'] . '
						}
					]
				},
				"properties": {
					"fname": {
						"stringValue": "' . $fname . '"
					},
					"lname": {
						"stringValue": "' . $lname . '"
					},
					"email": {
						"stringValue": "' . $email . '"
					},
					"username": {
						"stringValue": "' . $username . '"
					},
					"password_hash": {
						"stringValue": "' . password_hash($_POST['password'], PASSWORD_DEFAULT) . '"
					}
				}
			}
		}
	]
}';

// URL for HTTPS request
$url = 'https://datastore.googleapis.com/v1/projects/holiday-light-controller:commit';

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

// IF error response
if(array_key_exists('error', json_decode($result, TRUE))){
	$error = array('danger', json_decode($result, TRUE)['error']['code'] . ': ' . json_decode($result, TRUE)['error']['message']);
	array_push($_SESSION['messages'], $error);
	header('Location:' . $_SERVER['HTTP_REFERER']);
	die();
}

// IF successful update
if($response['http_code'] === 200){
	$message = array('success', 'Password changed successfully');
	array_push($_SESSION['messages'], $message);
	header('Location:' . '../dashboard.php');
	die();
}

// All other failures - generic error
$error = array('danger', 'Unable to change password at this time');
array_push($_SESSION['messages'], $error);
header('Location:' . $_SERVER['HTTP_REFERER']);
die();
?>