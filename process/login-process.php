<?php
require(__DIR__ . '/../sessioninfo.php');

// Validation - user name required
if(strlen($_POST['userName']) == 0){
	$error = array('danger', 'User name is required.');
	array_push($_SESSION['messages'], $error);
	header('Location:' . $_SERVER['HTTP_REFERER']);
	die();
}

// Validation - password required
if(strlen($_POST['password']) == 0){
	$error = array('danger', 'Password is required.');
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

// IF username not found
if(!array_key_exists('entityResults', json_decode($result, TRUE)['batch'])){
	$error = array('danger', 'Invalid username/password');
	array_push($_SESSION['messages'], $error);
	header('Location:' . $_SERVER['HTTP_REFERER']);
	die();
}

// Verify password and log in
if(password_verify($_POST['password'], json_decode($result, TRUE)['batch']['entityResults'][0]['entity']['properties']['password_hash']['stringValue'])){
	$_SESSION['userID'] = json_decode($result, TRUE)['batch']['entityResults'][0]['entity']['key']['path'][0]['id'];
	$message = array('success', 'Successfuly logged in!');
	array_push($_SESSION['messages'], $message);
	header('Location:' . '../dashboard.php');
	die();
}

// Unable to verify password
$error = array('danger', 'Invalid username/password');
array_push($_SESSION['messages'], $error);
header('Location:' . '../login.php');
die();
?>