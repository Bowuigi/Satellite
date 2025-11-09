<?php
require_once "/app/lib/Database.php";
require_once "/app/lib/Session.php";
require_once "/app/lib/request_data.php";
require_once "/app/lib/is_identifier.php";

$db = new Database();
$session = new Session();

if (!$session->isLoggedIn()) {
	http_response_code(400);
	die('No registrado');
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
	http_response_code(400);
	die('Solo se admiten peticiones GET');
}

if (!isset($_GET['name']) || $_GET['name'] === '') {
	http_response_code(400);
	die('Falta el parámetro query "name"');
}

$name = $_GET['name'];

if (!is_identifier($name)) {
	http_response_code(400);
	die('Nombre inválido');
}

$filters = $db->statement(
	'select name, author, pf_condition, sort_by from post_filters where name = :name and author = :author',
	[
		'name' => $name,
		'author' => $session->get('username'),
	]
);

if (count($filters) === 0) {
	http_response_code(400);
	die('No existe un filtro con ese nombre');
}

echo json_encode([
	'name' => $filters[0]['name'],
	'author' => $filters[0]['author'],
	'condition' => json_decode($filters[0]['pf_condition'], true),
	'sort_by' => $filters[0]['sort_by'],
]);
?>
