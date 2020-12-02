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

print_r($result);

foreach(json_decode($result, TRUE)['batch']['entityResults'] as $key => $value){
	$id = $value['entity']['key']['path'][0]['id'];
	$userID = $value['entity']['properties']['userID']['stringValue'];
	$datetime = $value['entity']['properties']['datetime']['stringValue'];
	$event = $value['entity']['properties']['event']['stringValue'];
	$song = $value['entity']['properties']['song']['stringValue'];
	$hue1 = $value['entity']['properties']['hue']['doubleValue'];
	$hue2 = $value['entity']['properties']['hue2']['doubleValue'];
	$hue3 = $value['entity']['properties']['hue3']['doubleValue'];

	if(array_key_exists('recurrence', $value['entity']['properties'])){
		$recurrence = $value['entity']['properties']['recurrence']['integerValue'];
	}
	else{
		$recurrence = null;
	}

	// PUBLISH MESSAGE
	// Query to send in HTTPS request			
	$payload = json_encode(array('messages' => array('data' => base64_encode('{"Lights":{"on":true,"hue":' . $hue1 . ',"hue2":' . $hue2 . ',"hue3":' . $hue3 . ',"effect":"none","bri":100,"sat":254}}'))));

	// URL for HTTPS request
	$url = 'https://pubsub.googleapis.com/v1/projects/holiday-light-controller/topics/raspberry_pi:publish';

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
	$response = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

	// Close cURL
	curl_close($ch);

	// DELETE EXISTING RECORD
	// Query to send in HTTPS request
	$payload = '{
		"mode": "NON_TRANSACTIONAL",
		"mutations": [
			{
				"delete": {
					"path": [
						{
							"id": ' . $id . ',
							"kind": "calendar"
						}
					]
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

	// Check if recurrence is greater than 1
	if(!is_null($recurrence) and $recurrence > 1){
		$recurrence--;
		$datetime = strtotime('+1 years', $datetime);

		// INSERT NEW RECORD
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
								"stringValue": "' . $userID . '"
							},
							"datetime": {
								"stringValue": "' . $datetime . '"
							},
							"recurrence": {
								"integerValue": "' . $recurrence . '"
							},
							"event": {
								"stringValue": "' . $event . '"
							},
							"song": {
								"stringValue": "' . $song . '"
							},
							"hue": {
								"doubleValue": "' . $hue1 . '"
							},
							"hue2": {
								"doubleValue": "' . $hue2 . '"
							},
							"hue3": {
								"doubleValue": "' . $hue3 . '"
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
	}
}
?>