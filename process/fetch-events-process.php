<?php
//require(__DIR__ . '/../sessioninfo.php');

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
					"name": "userID"
				},
				"op": "EQUAL",
				"value": {
					"stringValue": "' .$_SESSION['userID'] . '"
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
  print_r($error);
  array_push($_SESSION['messages'], $error);
	header('Location:' . $_SERVER['HTTP_REFERER']);
	die();
}

// If successfully fetched events, store events in $_SESSION['events']
if($response['http_code'] === 200){
  $_SESSION['events'] = array();

  $entities = json_decode($result, TRUE)['batch']['entityResults'];

  foreach ($entities as $key => $value) {
    $property = $value['entity']['properties'];
    array_push($_SESSION['events'],
      array('event' => $property['event']['stringValue'],
      'date' => $property['date']['stringValue'],
      'time' => $property['time']['stringValue'])
      //'color' => $property['color']['stringValue'])
    );
  };

}


?>
