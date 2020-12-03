<?php
require('sessioninfo.php');
// Query to send in HTTPS request
$payload = '{
	"keys": [
		{
			"path": [
				{
					"kind": "user",
					"id": "' . $_SESSION['userID'] . '"
				}
			]
		}
	]
}';

// URL for HTTPS request
$url = 'https://datastore.googleapis.com/v1/projects/holiday-light-controller:lookup';

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

if(array_key_exists('error', json_decode($result, TRUE))){
	$error = array('danger', json_decode($result, TRUE)['error']['code'] . ': ' . json_decode($result, TRUE)['error']['message']);
	array_push($_SESSION['messages'], $error);
}
?>
<?php
/* below is moved to account.php which is being included in line 111
<html>
<html>
	<head>
		<!-- Metadata  -->
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<!-- Page Title  -->
		<title>Holiday Light Controller</title>

		<!-- Cascading Style Sheets -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">

		<!-- jQuery -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

	</head>
	<body>
		<h4 class="text-center">Dashboard</h4>
		<div class="container">
			<!-- Display error messages, if any  -->
			<?php require('displaymessages.php'); ?>
			<div class="row">
				<div class="col-lg-6">
					<h5>User information</h5>

					<table class="table table-bordered">
						<tbody>
							<tr>
							<th scope="row">First name</th>
							<td><?php print_r(json_decode($result, TRUE)['found'][0]['entity']['properties']['fname']['stringValue']); ?></td>
							</tr>
							<tr>
							<th scope="row">Last name</th>
							<td><?php print_r(json_decode($result, TRUE)['found'][0]['entity']['properties']['lname']['stringValue']); ?></td>
							</tr>
							<tr>
							<th scope="row">User name</th>
							<td><?php print_r(json_decode($result, TRUE)['found'][0]['entity']['properties']['username']['stringValue']); ?></td>
							</tr>
							<tr>
							<th scope="row">Email address</th>
							<td><?php print_r(json_decode($result, TRUE)['found'][0]['entity']['properties']['email']['stringValue']); ?></td>
							</tr>
						</tbody>
					</table>
					<a href="update-profile.php">Update profile</a><br />
					<a href="change-password.php">Change password</a><br />
					<a href="login.php">Log out</a>
				</div>
				<div class="col-lg-6">
					<h5>Lighting Controls</h5>
					<!--- Hue Notes
					brightness : level = (int)        - int is from 0 to 100 (mapped to 255)
					hue        : level = ('hue:int')  - int is from 0 to 65535
					saturation : level = ('sat:int')  - int is from 0 to 255
					ct         : level = ('ct:int')   - int is from 153 to 500
					rgb        : level = ('rgb:hex')  - hex is from 000000 to ffffff
					transition : level = ('tr:int')   - int is from 0 to 3000 in tenths of seconds
					effect     : level = ('eft:colorloop|none') put bulb in colour loop
					--->

					<div class="custom-control custom-switch">
						<input type="checkbox" class="custom-control-input" name="on" id="on">
						<label class="custom-control-label" for="on">Lights Off/On</label>
					</div>
					<div class="form-group">
						<label for="bri">Brightness (0 to 100)</label> = <output>50</output>
						<input type="range" class="form-control-range" min="0" max="100" value="50" name="bri" id="bri" oninput="this.previousElementSibling.value = this.value">

					</div>
					<div class="form-group">
						<label for="hue">Hue (0 to 65535)</label> = <output>32768</output>
						<input type="range" class="form-control-range" min="0" max="65535" value="32768" name="hue" id="hue" oninput="this.previousElementSibling.value = this.value">
					</div>
					<div class="form-group">
						<label for="sat">Saturation (0 to 254)</label> = <output>127</output>
						<input type="range" class="form-control-range" min="0" max="254" value="127" name="sat" id="sat" oninput="this.previousElementSibling.value = this.value">
					</div>
					<div class="form-group">
						<label for="ct">Color temperature (153 to 500)</label> = <output>327</output>
						<input type="range" class="form-control-range" min="153" max="500" value="327" name="ct" id="ct" oninput="this.previousElementSibling.value = this.value">
					</div>
					<div class="input-group mb-3">
						<div class="input-group-prepend">
						<span class="input-group-text" id="basic-addon1">RGB</span>
					</div>
						<input type="text" class="form-control" name="rgb" id="rgb" placeholder="000000 to FFFFFF" maxlength="6" required>
					</div>
					<div class="custom-control custom-switch">
						<input type="checkbox" class="custom-control-input" name="eft" id="eft">
						<label class="custom-control-label" for="eft">Color loop</label>
					</div>
					<div class="form-group">
						<label for="tr">Transition seconds (0 to 300)</label> = <output>150</output>
						<!-- Note: value is in 10ths of second -->
						<input type="range" class="form-control-range" min="0" max="3000" value="1500" name="tr" id="tr" oninput="this.previousElementSibling.value = Math.round(this.value / 10, 1)">
					</div>
					<button type="button" id="button" class="btn btn-primary">Send command</button>
				</div>
			</div>
		</div>*/
		include('dashboard-components/head.html');
		?>

	<body id="page-top">
			<?php
				include('dashboard-components/index.html');
				include('dashboard-components/account.php');
				include('dashboard-components/light-switch.html');
				include('dashboard-components/schedule-event.php');
				//include('dashboard-components/demo.html');
				include('dashboard-components/scripts.html');
				?>
		<script>
		$("#button").click(function(data){
			if(document.getElementById('on').checked) {
				$on = true;
				$cast = null;
			} else {
				$on = false;
				$cast = false;

			}

			// if(document.getElementById('eft').checked) {
			// 	$eft = "colorloop";
			// } else {
			// 	$eft = "none";
			// }

			$.get("process/publish-message.php?on=" + $on + "&bri=" + bri + "&hue=" + hue + "&cast=" + $cast, function(response){
				//alert(response);
			});
			// $.get("process/publish-message.php?on=" + $on + "&bri=" + $("#bri").val() + "&hue=" + $("#hue").val() + "&sat=" + $("#sat").val() + "&ct=" + $("#ct").val() + "&rgb=" + $("#rgb").val() + "&eft=" + $eft + "&tr=" + $("#tr").val(), function(response){
			// 	alert(response);
			// });
		});
		</script>
	</body>
</html>
