<?php
require_once "/app/lib/Session.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(400);
	die('Solo se admiten peticiones POST');
}

$session = new Session();

if (!$session->isLoggedIn()) {
	http_response_code(400);
	die('No registrado');
}

$session->logout();
?>
