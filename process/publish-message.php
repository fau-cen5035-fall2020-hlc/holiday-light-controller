<?php
require(__DIR__ . '/../sessioninfo.php');

// Query to send in HTTPS request			
//$payload = json_encode(array('messages' => array('data' => base64_encode(json_encode(array('Lights' => array('on' => $_GET['on'], 'hue' => $_GET['hue'], 'effect' => $_GET['eft'], 'bri' => $_GET['bri'], 'sat' => $_GET['sat'], 'ct' => $_GET['ct'])), JSON_NUMERIC_CHECK)))));

//$payload = json_encode(array('messages' => array('data' => base64_encode('{"Lights":{"on":' . $_GET['on'] . ',"hue":' . $_GET['hue'] . ',"effect":"' . $_GET['eft'] . '","bri":' . $_GET['bri'] . ',"sat":' . $_GET['sat'] . ',"ct":' . $_GET['ct'] . '}}'))));

$payload = json_encode(array('messages' => array('data' => base64_encode('{"Lights":{"on":' . $_GET['on'] . ',"hue":' . $_GET['hue'] . ',"effect":"' . $_GET['eft'] . '","bri":' . $_GET['bri'] . ',"sat":' . $_GET['sat'] . '}}'))));


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

echo($response);

?>