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

// IF user name already exists
if(array_key_exists('entityResults', json_decode($result, TRUE)['batch'])){
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

// IF email address already exists
if(array_key_exists('entityResults', json_decode($result, TRUE)['batch'])){
	$error = array('danger', 'The email address you entered is already in use');
	array_push($_SESSION['messages'], $error);
	header('Location:' . $_SERVER['HTTP_REFERER']);
	die();
}

// Register user
// Query to send in HTTPS request
$payload = '{
	"mode": "NON_TRANSACTIONAL",
	"mutations": [
		{
			"insert": {
				"key": {
					"path": [
						{
							"kind": "user"
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
						"stringValue": "' . password_hash($_POST['password'], PASSWORD_DEFAULT) . '",
						"excludeFromIndexes": true
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

// IF successful insert
if($response['http_code'] === 200){
	$_SESSION['userID'] = json_decode($result, TRUE)['mutationResults'][0]['key']['path'][0]['id'];
	$message = array('success', 'You have successfully registered');
	array_push($_SESSION['messages'], $message);
	header('Location:' . '../dashboard.php');
	die();
}

// All other failures - generic error
$error = array('danger', 'Unable to process registration at this time');
array_push($_SESSION['messages'], $error);
header('Location:' . $_SERVER['HTTP_REFERER']);
die();
?>