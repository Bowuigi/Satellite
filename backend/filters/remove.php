<?php
require_once "/app/lib/Database.php";
require_once "/app/lib/Session.php";
require_once "/app/lib/request_data.php";

$db = new Database();
$session = new Session();

if (!$session->isLoggedIn()) {
	http_response_code(400);
	die('No registrado');
}

$data = request_data(function(RequestJSON $json) {
	return [
		'name' => $json['name'],
	];
});

if (!preg_match('/^[a-z0-9_\\.]{1,50}$/', $data['name'])) {
	http_response_code(400);
	die('Nombre inválido');
}

$filters = $db->statement(
	'select name from post_filters where name = :name and author = :author',
	[
		'author' => $session->get('username'),
		'name' => $data['name'],
	]
);

if (count($filters) === 0) {
	http_response_code(400);
	die('No existe un filtro con ese nombre');
}

$filters = $db->statement(
	'delete from post_filters where name = :name and author = :author',
	[
		'author' => $session->get('username'),
		'name' => $data['name'],
	]
);
?>
