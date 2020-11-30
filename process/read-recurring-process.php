<?php
require(__DIR__ . '/../sessioninfo.php');

// Query to send in HTTPS request			
$payload = '{
	"query": {
		"kind": [
			{
				"name": "calendar"
			}
		],
		"filter": {
			"propertyFilter": {
				"property": {
					"name": "datetime"
				},
				"op": "LESS_THAN_OR_EQUAL",
				"value": {
					"stringValue": "' . time() . '"
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

//print_r(json_decode($result, TRUE)['batch']['entityResults']);

foreach(json_decode($result, TRUE)['batch']['entityResults'] as $key => $value){
	$userID = $value['entity']['properties']['userID']['stringValue'];
	$datetime = $value['entity']['properties']['datetime']['stringValue'];
	$recurrence = $value['entity']['properties']['recurrence']['integerValue'];
	$event = $value['entity']['properties']['event']['stringValue'];
	$song = $value['entity']['properties']['song']['stringValue'];
	$color1 = $value['entity']['properties']['color1']['doubleValue'];
	$color2 = $value['entity']['properties']['color2']['doubleValue'];
	$color3 = $value['entity']['properties']['color3']['doubleValue'];
	
	echo('User ID: ' . $userID . '<br />');
	echo('datetime: ' . $datetime . '<br />');
	echo('recurrence: ' . $recurrence . '<br />');
	echo('event: ' . $event . '<br />');
	echo('song: ' . $song . '<br />');
	echo('color1: ' . $color1 . '<br />');
	echo('color2: ' . $color2 . '<br />');
	echo('color3: ' . $color3 . '<br />');
	
	print_r($value['entity']['properties']);
	echo('<br /><br />');
}
?>