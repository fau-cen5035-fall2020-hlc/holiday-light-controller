<?php
while(!empty($_SESSION['messages'])){
	$message = array_shift($_SESSION['messages']);
	echo('<div class="alert alert-' . $message[0] . '" role="alert">' . $message[1] . '</div>');
}
?>