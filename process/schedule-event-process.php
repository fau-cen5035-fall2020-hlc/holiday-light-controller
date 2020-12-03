<?php
require(__DIR__ . '/../sessioninfo.php');

// Query to send in HTTPS request
$payload = '{
	"mode": "NON_TRANSACTIONAL",
	"mutations": [
		{
			"insert": {
				"key": {
					"path": [
						{
							"kind": "calendar"
						}
					]
				},
				"properties": {
					"userID": {
						"stringValue": "'.$_SESSION['userID'].'"
					},
					"datetime": {
						"stringValue": "' . $_POST['datetime']. '"
					},
					"date":{
						"stringValue": "' . $_POST['selected-date']. '"
					},
					"time":{
						"stringValue": "' . $_POST['selected-time']. '"
					},
					"event": {
						"stringValue": "' . $_POST['event-name'] . '"
					},
					"recurrence": {
						"integerValue": "' . $_POST['recurrence'] . '"
					},
					"song": {
						"stringValue": "' . $_POST['song-input'] . '"
					},
					"url": {
						"stringValue": "' . $_POST['url-input'] . '"
					},
					"hue": {
						"doubleValue": "' . floatval($_POST['color1']) . '"
					},
					"hue2": {
						"doubleValue": "' . floatval($_POST['color2']) . '"
					},
					"hue3": {
						"doubleValue": "' . floatval($_POST['color3']) . '"
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
if(!isset($_SESSION['messages'])) $_SESSION['messages'] = array();
if(array_key_exists('error', json_decode($result, TRUE))){
	$error = array('danger', json_decode($result, TRUE)['error']['code'] . ': ' . json_decode($result, TRUE)['error']['message']);
	array_push($_SESSION['messages'], $error);
  //print_r($_SESSION['messages']);
	header('Location:' . $_SERVER['HTTP_REFERER']);
	die();
}

// IF successful insert
if($response['http_code'] === 200){
	//$_SESSION['userID'] = json_decode($result, TRUE)['mutationResults'][0]['key']['path'][0]['id'];
	$message = array('success', 'You have successfully created an event!');
	array_push($_SESSION['messages'], $message);
	header('Location:' . '../dashboard.php');
	die();
}

// All other failures - generic error
$error = array('danger', 'Unable to create an event at this time');
array_push($_SESSION['messages'], $error);
//print_r($errors);
header('Location:' . $_SERVER['HTTP_REFERER']);
die();
?>
