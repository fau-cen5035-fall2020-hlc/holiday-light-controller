<?php
// start session if not started already
if (session_status() != PHP_SESSION_ACTIVE) session_start();

// obtain access token if one has not been obtained or is expired
if(!isset($_SESSION['accessToken']) or !isset($_SESSION['tokenExpiration']) or $_SESSION['tokenExpiration'] <= time() + 60){
	
	// URL for HTTPS request
	$url = 'https://oauth2.googleapis.com/token';

	// Initialize cURL
	$ch = curl_init();

	// cURL headers
	$headers = array(
		"Content-Type: application/x-www-form-urlencoded",
		"Accept: application/json"
	);
	
	// JSON web token (JWT) header
	$jwtHeader = '{"alg":"RS256","typ":"JWT"}';
	
	// JSON web token (JWT) claim set
	$jwtClaimSet = '{"iss":"holiday-light-controller@appspot.gserviceaccount.com","scope":"https://www.googleapis.com/auth/datastore https://www.googleapis.com/auth/pubsub","aud":"https://oauth2.googleapis.com/token","exp":' . (time() + 3600) . ',"iat":' . time() . '}';
	
	// JSON web token (JWT) signature
	openssl_sign(
		rtrim(strtr(base64_encode($jwtHeader), '+/', '-_'), '=') . '.' . rtrim(strtr(base64_encode($jwtClaimSet), '+/', '-_'), '='),
		$jwtSig,
		json_decode(file_get_contents('holiday-light-controller-a05bad0a6f62.json'), true)['private_key'],
		'sha256WithRSAEncryption'
	);
	
	// JSON web token (JWT) assertion
	$jwtAssertion = rtrim(strtr(base64_encode($jwtHeader), '+/', '-_'), '=') . '.' . rtrim(strtr(base64_encode($jwtClaimSet), '+/', '-_'), '=') . '.' . rtrim(strtr(base64_encode($jwtSig), '+/', '-_'), '=');
	
	// Data to transfer
	$payload = 'grant_type=urn%3Aietf%3Aparams%3Aoauth%3Agrant-type%3Ajwt-bearer&assertion=' . $jwtAssertion;

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
	$response = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	// Close cURL
	curl_close($ch);

	// If successful, store access token and expiration in session variable
	if($response === 200){
		$_SESSION['accessToken'] = json_decode($result, TRUE)['access_token'];
		$_SESSION['tokenExpiration'] = json_decode($result, TRUE)['expires_in'] + time();
	}
}
?>