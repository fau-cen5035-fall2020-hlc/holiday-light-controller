<?php
require(__DIR__ . '/../sessioninfo.php');
require_once(__DIR__ . '/../google-api-client/vendor/autoload.php');
 
// init configuration
$clientID = '467051867349-stitogbnu41lvhdi65ntpp3hbvbru0nb.apps.googleusercontent.com';
$clientSecret = 'sBjtGqIEOp8rVdS8cDQaN0h-';
$redirectUri = ('https://holiday-light-controller.ue.r.appspot.com/process/sso-process.php');
  
// create Client Request to access Google API
$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");
 
// authenticate code from Google OAuth Flow
if (isset($_GET['code'])) {
  $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
  $client->setAccessToken($token['access_token']);
  
  // get profile info
  $google_oauth = new Google_Service_Oauth2($client);
  $google_account_info = $google_oauth->userinfo->get();
  $email =  $google_account_info->email;
  $name =  $google_account_info->name;
 
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
            "stringValue": "' . $email . '"
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
    header('Location:' . '../login.php');
    die();
  }

  // IF email address not found
  if(!array_key_exists('entityResults', json_decode($result, TRUE)['batch'])){
    $error = array('danger', 'Email address not on file');
    array_push($_SESSION['messages'], $error);
    header('Location:' . '../login.php');
    die();
  }

  // Log in
  $_SESSION['userID'] = json_decode($result, TRUE)['batch']['entityResults'][0]['entity']['key']['path'][0]['id'];
  $message = array('success', 'Successfully logged in!');
  array_push($_SESSION['messages'], $message);
  header('Location:' . '../dashboard.php');
  die();
} 
?>