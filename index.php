<?php

if(parse_url($_SERVER['REQUEST_URI'])['path'] == '/') {
	header('Location:login.php');
}
else {
	require(ltrim(preg_replace('/\?.+/', '', $_SERVER['REQUEST_URI']), '/'));
}
?>