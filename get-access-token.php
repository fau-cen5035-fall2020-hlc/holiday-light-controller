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
		"-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDD/5yqncMqMjSi\nCuBoJzugR/AZZ/tI3onGn3gQm/0Eo1f/uBO0slwmaqaxS+QVU0qBocEtMWWsRdRV\nfRNrSypobs/J1qhiNhH4P1iZwbVzOzoC/Gsf4lpkuPrsz5lCtjJjLuyFaUE9jKVF\nrOhUU0mb91RL3K6cXRv/kCRsyH11D56SNRwDzF9PFW6aozKbI2P9uKaEUslqxolD\nX1aLePUxpnpro+/UjJJygFr0bqtFCV6imx/+Twva4Q2/B2bxHvfrM0aQAGmOQ6XC\nEYk9h1lFKMm2lX32LpYHlWdDzdBybomSqtN7C3yck73ZouMFeA7zQWgGZsW87S1n\nU93DMheFAgMBAAECggEAWkS1+3Boa90ul3jp7J4xHHs1TgYxWdjAXJ2idwfYqpOu\nWkSFsuG4hir1dc+NjvLK7FQ6u502BfctOWg/EtnOAMYUZhG6gG0Frq9CF6J69sTO\nzYRCtC8UUbJOQtXD8NEn+L0qSrF0ziuiDKp7YCbxzmbFSKEtPC+3zuasl/toqIoH\n9gIpTcRlPi1ZOcHWQS0TpjDUDjrkw35nvuXZr/hQoOtbNlYBlX4ELYFj6vwHA9RQ\nuncpbX2mREYDk95COu9kZwjoBSgWRD4n0ei5PBeIxeBXLGUExPhEFGRVHbAUsrC7\n628/SLL+6X53399ViFF/YPm7VrciOO9EC+z6y79XCwKBgQDroshlao+gklIzBH7P\nlq7iY4KZ2r7mET1warycwKP+PAsdSFBtSC6pyObAthwge5pLnHagU5TPymTcbwr3\nz8iEHOdfwlvnDlZjBe0QjnYnQcDeKI2WjCcLv5uGfrv0hNDZdzvy9QYp6gchCV6U\nn1LdXUYSsrg9cvDpbl8yXoR7gwKBgQDU7+Pd2N0MeThD00jPb4wxNFPtcz3ehUBz\njG/8FtE191U/G5bad5LdmBF5zqObnphyRLLPWDpLPzHBo1wznTztxibUI6o0qaBk\niYdtddCoKSW/VbIHIF4iqXEEVixOJDLuUB5V1dDx4s9MkmSQ9uO5QnscvB05cMnF\ndiM2vvMKVwKBgEMbEMcRGeY4xYAR/6tO1m9St5bpXQRYVI73Vs+tbQayQtgMCBuX\nOvLPbCfltQzrJn3yCTaPlwm2MhM07IK2gfxd3ua+iz26L4/z2Bem+q8jYrHiCrCd\nKWGHJ9udrBlu2auU8VW3whGdvHoEGRNqbEbukU4x55ZYbZONAz9s+33RAoGAV1YN\nH/DSuUr+yNLgCjS2S3gq9DlQJvSAeuQfCkZRiNDpJfkq5nAkzL6SbGNUt39VB4kl\nzeViA3rn7YQTTRgw5VVgl6IJLZBlONcvtLWd31sBIHkFi+a6tXzJ3f16LpKqURSL\nXKlWCd/9Jmrk1JBu1o1t8Vs2oVihaVwLFWk8RgECgYEAl6XTPFOOOKP1PXtRuVhN\nN1SIPkCUcNy0BSNvyORgSKELdkwuMMPdn0brg26v1wKPzhQwhBySKpVFysgJVf0e\nqronqKN0kygz7b2b/t7YyMNZPxfY7CN72rx/FQPDUY2T9ycvh57NbSV5YAYDrnxL\nmnHf/r6qbYX/OGDjOmEEoKo=\n-----END PRIVATE KEY-----\n",
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