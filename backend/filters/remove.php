<?php
require_once "/app/lib/Database.php";
require_once "/app/lib/Session.php";
require_once "/app/lib/request_data.php";
require_once "/app/lib/uuidv4.php";

$db = new Database();
$session = new Session();

if (!$session->isLoggedIn()) {
	http_response_code(400);
	die('No registrado');
}

$data = request_data(function(RequestJSON $json) {
	return [
		'id' => $json['id'],
	];
});

if (!is_uuid($data['id'])) {
	http_response_code(400);
	die('Nombre inválido');
}

$filters = $db->statement('select id from post_filters where id = ?', [$data['id']]);

if (count($filters) === 0) {
	http_response_code(400);
	die('No existe un filtro con ese nombre');
}

$db->statement(
	'delete from post_filters where id = ?',
	[$data['id']]
);
?>
