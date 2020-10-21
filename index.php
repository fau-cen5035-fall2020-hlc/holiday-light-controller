<?php
switch (@parse_url($_SERVER['REQUEST_URI'])['path']) {
    case '/login.php':
        require 'login.php';
        break;
	case '/registration.php':
        require 'registration.php';
        break;
    default:
        http_response_code(404);
        exit('Not Found');
}
?>