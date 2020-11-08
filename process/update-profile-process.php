<?php
require(__DIR__ . '/../sessioninfo.php');

// Validation - user name required
if(strlen($_POST['userName']) == 0){
	$error = array('danger', 'User name is required.');
	array_push($_SESSION['messages'], $error);
	header('Location:' . $_SERVER['HTTP_REFERER']);
	die();
}

// Validation - email required
if(strlen($_POST['email']) == 0){
	$error = array('danger', 'Email address is required.');
	array_push($_SESSION['messages'], $error);
	header('Location:' . $_SERVER['HTTP_REFERER']);
	die();
}

// Validation - email format
if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
	$error = array('danger', 'Email address is not valid.');
	array_push($_SESSION['messages'], $error);
	header('Location:' . $_SERVER['HTTP_REFERER']);
	die();
}

// Validation - first name required
if(strlen($_POST['firstName']) == 0){
	$error = array('danger', 'First name is required.');
	array_push($_SESSION['messages'], $error);
	header('Location:' . $_SERVER['HTTP_REFERER']);
	die();
}

// Validation - last name required
if(strlen($_POST['lastName']) == 0){
	$error = array('danger', 'Last name is required.');
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

// Store existing password hash to be included in update below
$password_hash = json_decode($result, TRUE)['found'][0]['entity']['properties']['password_hash']['stringValue'];

// Query to send in HTTPS request			
$payload = '{
	"query": {
		"kind": [
			{
				"name": "user"
			}
		],
		"filter": {
			"propertyFilter": {
				"property": {
					"name": "username"
				},
				"op": "EQUAL",
				"value": {
					"stringValue": "' . $_POST['userName'] . '"
				}
			}
		}
	}
}';

// URL for HTTPS request
$url = 'https://datastore.googleapis.com/v1/projects/holiday-light-controller:runQuery';

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

// IF user name already in use by another user
if(array_key_exists('entityResults', json_decode($result, TRUE)['batch']) and json_decode($result, TRUE)['batch']['entityResults'][0]['entity']['key']['path'][0]['id'] != $_SESSION['userID']){
	$error = array('danger', 'The user name you entered is already in use');
	array_push($_SESSION['messages'], $error);
	header('Location:' . $_SERVER['HTTP_REFERER']);
	die();
}

// Query to send in HTTPS request			
$payload = '{
	"query": {
		"kind": [
			{
				"name": "user"
			}
		],
		"filter": {
			"propertyFilter": {
				"property": {
					"name": "email"
				},
				"op": "EQUAL",
				"value": {
					"stringValue": "' . $_POST['email'] . '"
				}
			}
		}
	}
}';

// URL for HTTPS request
$url = 'https://datastore.googleapis.com/v1/projects/holiday-light-controller:runQuery';

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

// IF email address already in use by another user
if(array_key_exists('entityResults', json_decode($result, TRUE)['batch']) and json_decode($result, TRUE)['batch']['entityResults'][0]['entity']['key']['path'][0]['id'] != $_SESSION['userID']){
	$error = array('danger', 'The email address you entered is already in use');
	array_push($_SESSION['messages'], $error);
	header('Location:' . $_SERVER['HTTP_REFERER']);
	die();
}

// Update profile
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
						"stringValue": "' . $_POST['firstName'] . '"
					},
					"lname": {
						"stringValue": "' . $_POST['lastName'] . '"
					},
					"email": {
						"stringValue": "' . $_POST['email'] . '"
					},
					"username": {
						"stringValue": "' . $_POST['userName'] . '"
					},
					"password_hash": {
						"stringValue": "' . $password_hash . '"
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
	$message = array('success', 'User profile successfully updated');
	array_push($_SESSION['messages'], $message);
	header('Location:' . '../dashboard.php');
	die();
}

// All other failures - generic error
$error = array('danger', 'Unable to update user profile at this time');
array_push($_SESSION['messages'], $error);
header('Location:' . $_SERVER['HTTP_REFERER']);
die();
?>