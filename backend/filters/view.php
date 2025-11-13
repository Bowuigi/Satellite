<?php
require_once "/app/lib/Database.php";
require_once "/app/lib/Session.php";
require_once "/app/lib/request_data.php";
require_once "/app/lib/is_identifier.php";

$db = new Database();
$session = new Session();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
	http_response_code(400);
	die('Solo se admiten peticiones GET');
}

if (!isset($_GET['user']) || $_GET['user'] === '') {
	http_response_code(400);
	die('Falta el parámetro query "user"');
}

if (!isset($_GET['filter']) || $_GET['filter'] === '') {
	http_response_code(400);
	die('Falta el parámetro query "filter"');
}

$user = $_GET['user'];
$filter = $_GET['filter'];

if (!is_identifier($user)) {
	http_response_code(400);
	die('Nombre de usuario inválido');
}

if (!is_identifier($filter)) {
	http_response_code(400);
	die('Nombre de filtro inválido');
}

$filters = $db->statement(
	'select name, author, pf_condition, sort_by from post_filters where name = :name and author = :author',
	[
		'name' => $filter,
		'author' => $user,
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
